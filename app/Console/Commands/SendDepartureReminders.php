<?php

namespace App\Console\Commands;

use App\Models\Trip;
use App\Models\Booking;
use App\Mail\PassengerDepartureReminder;
use App\Mail\DriverDepartureReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDepartureReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-departure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to passengers and drivers 1 hour before trip departure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for trips departing in 1 hour...');

        // Get current time
        $now = Carbon::now();
        
        // Calculate the time window (55-65 minutes from now to avoid missing any trips)
        $reminderTimeStart = $now->copy()->addMinutes(55);
        $reminderTimeEnd = $now->copy()->addMinutes(65);

        // Find trips departing in approximately 1 hour
        $upcomingTrips = Trip::with(['driver.user', 'bookings.passenger', 'route'])
            ->whereBetween('departure_datetime', [$reminderTimeStart, $reminderTimeEnd])
            ->where('status', 'active')
            ->get();

        if ($upcomingTrips->isEmpty()) {
            $this->info('No trips departing in 1 hour.');
            return 0;
        }

        $this->info("Found {$upcomingTrips->count()} trip(s) departing soon.");

        $passengerEmailsSent = 0;
        $driverEmailsSent = 0;
        $errors = 0;

        foreach ($upcomingTrips as $trip) {
            $this->line("Processing Trip #{$trip->id}: {$trip->origin_dzongkhag} → {$trip->destination_dzongkhag}");

            // Send reminder to driver
            if ($trip->driver && $trip->driver->user && $trip->driver->user->email) {
                try {
                    Mail::to($trip->driver->user->email)->send(new DriverDepartureReminder($trip));
                    $this->info("  ✓ Driver reminder sent to: {$trip->driver->user->email}");
                    $driverEmailsSent++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send driver reminder: {$e->getMessage()}");
                    Log::error("Failed to send driver departure reminder for trip {$trip->id}: " . $e->getMessage());
                    $errors++;
                }
            }

            // Send reminders to all confirmed passengers
            $confirmedBookings = $trip->bookings()
                ->with('passenger')
                ->where('status', 'active')
                ->where('payment_status', 'paid')
                ->get();

            foreach ($confirmedBookings as $booking) {
                if ($booking->passenger && $booking->passenger->email) {
                    try {
                        Mail::to($booking->passenger->email)->send(new PassengerDepartureReminder($booking));
                        $this->info("  ✓ Passenger reminder sent to: {$booking->passenger->email}");
                        $passengerEmailsSent++;
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to send passenger reminder: {$e->getMessage()}");
                        Log::error("Failed to send passenger departure reminder for booking {$booking->id}: " . $e->getMessage());
                        $errors++;
                    }
                } else {
                    $this->warn("  ⚠ Passenger email not found for booking #{$booking->id}");
                }
            }
        }

        $this->newLine();
        $this->info("=== Departure Reminders Summary ===");
        $this->info("Driver emails sent: {$driverEmailsSent}");
        $this->info("Passenger emails sent: {$passengerEmailsSent}");
        
        if ($errors > 0) {
            $this->error("Errors encountered: {$errors}");
        }

        return 0;
    }
}
