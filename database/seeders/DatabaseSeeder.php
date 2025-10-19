<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Event;
use App\Models\GuestGroup;
use App\Models\Guest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@tamukami.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
            'is_active' => true,
        ]);

        echo "✅ Admin user created:\n";
        echo "   Username: admin\n";
        echo "   Password: password\n\n";

        // Create Demo User
        $user = User::create([
            'name' => 'Demo User',
            'username' => 'demo',
            'email' => 'demo@tamukami.com',
            'password' => Hash::make('demo123'),
            'phone' => '082345678901',
            'role' => 'user',
            'is_active' => true,
        ]);

        echo "✅ Demo user created:\n";
        echo "   Username: demo\n";
        echo "   Password: demo123\n\n";

        // Create Demo Event
        $event = Event::create([
            'user_id' => $admin->id,
            'name' => 'Demo Event',
            'type' => 'Demo Event',
            'date' => now()->addDays(7),
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'location' => 'Grand Ballroom Hotel XYZ',
            'description' => 'Event demo untuk testing aplikasi TamuKami',
            'is_active' => true,
        ]);

        echo "✅ Demo event created: {$event->name}\n\n";

        // Create Guest Groups
        $groups = [
            [
                'name' => 'Keluarga',
                'color' => '#ef4444',
                'description' => 'Anggota keluarga',
            ],
            [
                'name' => 'Teman Kerja',
                'color' => '#3b82f6',
                'description' => 'Rekan kerja',
            ],
            [
                'name' => 'Sahabat',
                'color' => '#10b981',
                'description' => 'Sahabat dekat',
            ],
            [
                'name' => 'Karyawan BBS',
                'color' => '#f59e0b',
                'description' => 'Tim internal',
            ],
        ];

        foreach ($groups as $groupData) {
            GuestGroup::create(array_merge($groupData, ['event_id' => $event->id]));
        }

        echo "✅ Guest groups created\n\n";

        // Create Sample Guests
        $sampleGuests = [
            [
                'name' => 'Yufu',
                'address' => 'Balam',
                'whatsapp' => '628971851234',
                'table_number' => null,
                'guests_count' => 2,
                'is_vip' => true,
                'group_id' => 1,
            ],
            [
                'name' => 'nama tamu',
                'address' => 'da',
                'whatsapp' => '628971851235',
                'table_number' => null,
                'guests_count' => 1,
                'is_vip' => false,
                'group_id' => 2,
            ],
            [
                'name' => 'Vi',
                'address' => '-',
                'whatsapp' => null,
                'table_number' => null,
                'guests_count' => 1,
                'is_vip' => false,
                'group_id' => 3,
            ],
            [
                'name' => 'hera',
                'address' => 'serang',
                'whatsapp' => '628971851236',
                'table_number' => null,
                'guests_count' => 1,
                'is_vip' => false,
                'group_id' => 1,
            ],
            [
                'name' => 'Irfan Gian Pratama',
                'address' => '-',
                'whatsapp' => '628971851237',
                'table_number' => null,
                'guests_count' => 1,
                'is_vip' => false,
                'group_id' => 2,
            ],
            [
                'name' => 'juli',
                'address' => 'batam',
                'whatsapp' => '628971851238',
                'table_number' => null,
                'guests_count' => 1,
                'is_vip' => false,
                'group_id' => 3,
            ],
        ];

        foreach ($sampleGuests as $guestData) {
            Guest::create(array_merge($guestData, [
                'event_id' => $event->id,
                'qr_code' => 'GUEST-' . uniqid(),
                'is_invited' => true,
            ]));
        }

        echo "✅ Sample guests created: " . count($sampleGuests) . " guests\n\n";

        echo "🎉 Database seeding completed successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "You can now login with:\n";
        echo "  Admin - username: admin, password: password\n";
        echo "  Demo  - username: demo, password: demo123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
