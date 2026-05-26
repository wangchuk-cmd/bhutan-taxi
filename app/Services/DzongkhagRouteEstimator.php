<?php

namespace App\Services;

use App\Models\Route;

class DzongkhagRouteEstimator
{
    private const EARTH_RADIUS_KM = 6371;

    private const DZONGKHAG_COORDINATES = [
        'Bumthang' => [27.5498, 90.7529],
        'Chhukha' => [27.0048, 89.5131],
        'Dagana' => [27.0323, 89.8876],
        'Gasa' => [27.9037, 89.7302],
        'Haa' => [27.3875, 89.2793],
        'Lhuentse' => [27.6679, 91.1839],
        'Mongar' => [27.2758, 91.2390],
        'Paro' => [27.4286, 89.4167],
        'Pemagatshel' => [27.0379, 91.4049],
        'Punakha' => [27.5910, 89.8774],
        'Samdrup Jongkhar' => [26.8036, 91.5033],
        'Samtse' => [27.0500, 88.9000],
        'Sarpang' => [26.8617, 90.2675],
        'Thimphu' => [27.4728, 89.6390],
        'Trashigang' => [27.3332, 91.5530],
        'Trashiyangtse' => [27.6116, 91.4561],
        'Trongsa' => [27.5000, 90.5080],
        'Tsirang' => [27.0200, 90.1200],
        'Wangdue Phodrang' => [27.4861, 89.8995],
        'Zhemgang' => [27.2169, 90.6573],
        'Phuentsholing' => [26.8587, 89.3889],
        'Gelephu' => [26.8721, 90.4856],
    ];

    private const SOUTHERN_CORRIDOR = [
        'Phuentsholing',
        'Samtse',
        'Gelephu',
        'Sarpang',
        'Samdrup Jongkhar',
        'Dagana',
        'Tsirang',
        'Chhukha',
    ];

    public function estimate(string $origin, string $destination): array
    {
        $originPoint = self::DZONGKHAG_COORDINATES[$origin] ?? self::DZONGKHAG_COORDINATES['Thimphu'];
        $destinationPoint = self::DZONGKHAG_COORDINATES[$destination] ?? self::DZONGKHAG_COORDINATES['Thimphu'];

        $airDistanceKm = $this->haversineDistanceKm(
            $originPoint[0],
            $originPoint[1],
            $destinationPoint[0],
            $destinationPoint[1]
        );

        $roadMultiplier = $this->roadMultiplier($origin, $destination, $airDistanceKm, $originPoint, $destinationPoint);
        $roadDistanceKm = max(15, (int) round($airDistanceKm * $roadMultiplier));

        $averageSpeed = $this->averageSpeedKmh($origin, $destination, $roadDistanceKm);
        $baseMinutes = ($roadDistanceKm / max(1, $averageSpeed)) * 60;
        $totalMinutes = max(25, (int) round($baseMinutes + $this->timeBufferMinutes($roadDistanceKm, $origin, $destination)));

        return [
            'distance_km' => $roadDistanceKm,
            'estimated_time' => $this->minutesToTimeString($totalMinutes),
            'minutes' => $totalMinutes,
        ];
    }

    public function syncAllRoutes(array $dzongkhags, bool $onlyMissing = false): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        $count = count($dzongkhags);
        for ($firstIndex = 0; $firstIndex < $count; $firstIndex++) {
            for ($secondIndex = $firstIndex + 1; $secondIndex < $count; $secondIndex++) {
                $origin = $dzongkhags[$firstIndex];
                $destination = $dzongkhags[$secondIndex];

                $estimate = $this->estimate($origin, $destination);

                $query = Route::query()->betweenDzongkhags($origin, $destination);

                $existingRows = $query->get(['id']);
                if ($existingRows->isNotEmpty()) {
                    if ($onlyMissing) {
                        $skipped += $existingRows->count();
                        continue;
                    }

                    $query->update([
                        'distance_km' => $estimate['distance_km'],
                        'estimated_time' => $estimate['estimated_time'],
                    ]);
                    $updated += $existingRows->count();
                    continue;
                }

                Route::create([
                    'origin_dzongkhag' => $origin,
                    'destination_dzongkhag' => $destination,
                    'distance_km' => $estimate['distance_km'],
                    'estimated_time' => $estimate['estimated_time'],
                ]);
                $created++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    private function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return 2 * self::EARTH_RADIUS_KM * asin(min(1, sqrt($a)));
    }

    private function roadMultiplier(string $origin, string $destination, float $airDistanceKm, array $originPoint, array $destinationPoint): float
    {
        $multiplier = 1.45;

        if ($this->isSouthernCorridor($origin) || $this->isSouthernCorridor($destination)) {
            $multiplier -= 0.12;
        }

        if (!$this->isSouthernCorridor($origin) && !$this->isSouthernCorridor($destination)) {
            $multiplier += 0.10;
        }

        if ($airDistanceKm > 120) {
            $multiplier += 0.08;
        }

        if (abs($originPoint[1] - $destinationPoint[1]) > 1.1) {
            $multiplier += 0.07;
        }

        return max(1.25, min(1.80, $multiplier));
    }

    private function averageSpeedKmh(string $origin, string $destination, int $roadDistanceKm): int
    {
        $speed = 34;

        if ($this->isSouthernCorridor($origin) || $this->isSouthernCorridor($destination)) {
            $speed += 6;
        }

        if ($this->isSouthernCorridor($origin) && $this->isSouthernCorridor($destination)) {
            $speed += 4;
        }

        if (!$this->isSouthernCorridor($origin) && !$this->isSouthernCorridor($destination)) {
            $speed -= 4;
        }

        if ($roadDistanceKm > 180) {
            $speed -= 2;
        }

        return max(24, min(48, $speed));
    }

    private function timeBufferMinutes(int $roadDistanceKm, string $origin, string $destination): int
    {
        $buffer = 10;

        if ($roadDistanceKm > 80) {
            $buffer += 10;
        }

        if ($roadDistanceKm > 160) {
            $buffer += 15;
        }

        if ($roadDistanceKm > 260) {
            $buffer += 20;
        }

        if (!$this->isSouthernCorridor($origin) || !$this->isSouthernCorridor($destination)) {
            $buffer += 6;
        }

        if ($this->isSouthernCorridor($origin) && $this->isSouthernCorridor($destination)) {
            $buffer -= 4;
        }

        return max(6, $buffer);
    }

    private function isSouthernCorridor(string $name): bool
    {
        return in_array($name, self::SOUTHERN_CORRIDOR, true);
    }

    private function minutesToTimeString(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }
}
