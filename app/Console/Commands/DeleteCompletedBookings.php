<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteCompletedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:delete-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically delete bookings 12 hours after ride completion';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find bookings where the ride was completed 12+ hours ago
        $now = now();
        $twelveHoursAgo = $now->copy()->subHours(12);

        // Delete completed bookings (where departure time is more than 12 hours ago)
        $deletedCount = Booking::whereHas('trip', function ($query) use ($twelveHoursAgo) {
            $query->where('departure_datetime', '<=', $twelveHoursAgo);
        })->delete();

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} completed booking(s).");
            \Log::info("Automatically deleted {$deletedCount} completed booking(s).");
        }

        return 0;
    }
}
