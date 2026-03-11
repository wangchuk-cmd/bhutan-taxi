<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bhutantaxi.bt',
            'phone_number' => '17000001',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Sample Driver User
        $driverUser = User::create([
            'name' => 'Dorji Tshering',
            'email' => 'driver@bhutantaxi.bt',
            'phone_number' => '17000002',
            'password' => Hash::make('driver123'),
            'role' => 'driver',
        ]);

        // Create Driver Profile
        Driver::create([
            'user_id' => $driverUser->id,
            'license_number' => 'DL-2024-001',
            'taxi_plate_number' => 'BP-1-A-1234',
            'vehicle_type' => 'Sedan',
            'verified' => true,
            'active' => true,
        ]);

        // Create Sample Passenger User
        User::create([
            'name' => 'Karma Wangmo',
            'email' => 'user@bhutantaxi.bt',
            'phone_number' => '17000003',
            'password' => Hash::make('user123'),
            'role' => 'passenger',
        ]);

        // Seed Routes (Bhutan Dzongkhags)
        $routes = [
            ['Thimphu', 'Paro', 54, '01:15:00'],
            ['Thimphu', 'Punakha', 77, '02:30:00'],
            ['Thimphu', 'Wangdue Phodrang', 71, '02:15:00'],
            ['Thimphu', 'Phuentsholing', 176, '04:30:00'],
            ['Paro', 'Phuentsholing', 166, '04:00:00'],
            ['Paro', 'Haa', 67, '02:00:00'],
            ['Punakha', 'Wangdue Phodrang', 21, '00:45:00'],
            ['Punakha', 'Trongsa', 129, '04:00:00'],
            ['Wangdue Phodrang', 'Trongsa', 108, '03:30:00'],
            ['Trongsa', 'Bumthang', 68, '02:30:00'],
            ['Bumthang', 'Mongar', 197, '07:00:00'],
            ['Mongar', 'Trashigang', 91, '03:30:00'],
            ['Thimphu', 'Tsirang', 147, '04:30:00'],
            ['Thimphu', 'Dagana', 198, '06:00:00'],
            ['Phuentsholing', 'Gelephu', 276, '07:00:00'],
            ['Trashigang', 'Samdrup Jongkhar', 180, '06:00:00'],
        ];

        foreach ($routes as $route) {
            Route::create([
                'origin_dzongkhag' => $route[0],
                'destination_dzongkhag' => $route[1],
                'distance_km' => $route[2],
                'estimated_time' => $route[3],
            ]);
        }

        // Seed Default Settings
        Setting::set('service_charge_percentage', '10', 'decimal', 'Service charge percentage for driver payouts');
        Setting::set('min_booking_hours', '2', 'integer', 'Minimum hours before departure for booking');
        Setting::set('max_seats_per_booking', '4', 'integer', 'Maximum seats per booking');
        Setting::set('booking_timeout_minutes', '10', 'integer', 'Minutes before unpaid booking expires');
        Setting::set('site_name', 'Bhutan Taxi', 'string', 'Site name');
        Setting::set('contact_email', 'support@bhutantaxi.bt', 'string', 'Contact email');
        Setting::set('contact_phone', '+975 17 100 100', 'string', 'Contact phone');

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@bhutantaxi.bt / admin123');
        $this->command->info('Driver: driver@bhutantaxi.bt / driver123');
        $this->command->info('User: user@bhutantaxi.bt / user123');
    }
}
