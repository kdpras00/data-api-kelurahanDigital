<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\HeadOfFamily;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if lurah user already exists
        if (!User::where('email', 'lurah@gmail.com')->exists()) {
            User::create([
                'name'     => 'Lurah',
                'email'    => 'lurah@gmail.com',
                'password' => bcrypt('password'),
            ])->assignRole('lurah');
        }

        // Get or create head of family user
        $user = User::firstOrCreate(
            ['email' => 'headoffamily@gmail.com'],
            [
                'name'     => 'Kepala Keluarga',
                'password' => bcrypt('password'),
            ]
        );
        
        // Assign role if not already assigned
        if (!$user->hasRole('head-of-family')) {
            $user->assignRole('head-of-family');
        }

        // Check if HeadOfFamily data already exists
        $headOfFamily = HeadOfFamily::firstOrCreate(
            ['user_id' => $user->id],
            [
                'identity_number'  => '3201234567890001',
                'gender'           => 'male',
                'date_of_birth'    => '1980-05-15',
                'phone_number'     => '081234567890',
                'occupation'       => 'Pegawai Swasta',
                'marital_status'   => 'married',
                'profile_picture'  => 'profile-pictures/default-avatar.jpg',
            ]
        );

        // Create wife (Istri) if not exists
        if (!User::where('email', 'siti.aminah@gmail.com')->exists()) {
            $wifeUser = User::create([
                'name'     => 'Siti Aminah',
                'email'    => 'siti.aminah@gmail.com',
                'password' => bcrypt('password'),
            ]);
            $wifeUser->assignRole('head-of-family');

            \App\Models\FamilyMember::firstOrCreate(
                ['user_id' => $wifeUser->id, 'head_of_family_id' => $headOfFamily->id],
                [
                    'profile_picture'   => 'profile-pictures/default-avatar.jpg',
                    'identity_number'   => '3201234567890002',
                    'gender'            => 'female',
                    'date_of_birth'     => '1985-08-20',
                    'phone_number'      => '081234567891',
                    'occupation'        => 'Ibu Rumah Tangga',
                    'marital_status'    => 'married',
                    'relation'          => 'wife',
                ]
            );
        }

        // Create first child (Anak Pertama) if not exists
        if (!User::where('email', 'ahmad.rizki@gmail.com')->exists()) {
            $child1User = User::create([
                'name'     => 'Ahmad Rizki',
                'email'    => 'ahmad.rizki@gmail.com',
                'password' => bcrypt('password'),
            ]);
            $child1User->assignRole('head-of-family');

            \App\Models\FamilyMember::firstOrCreate(
                ['user_id' => $child1User->id, 'head_of_family_id' => $headOfFamily->id],
                [
                    'profile_picture'   => 'profile-pictures/default-avatar.jpg',
                    'identity_number'   => '3201234567890003',
                    'gender'            => 'male',
                    'date_of_birth'     => '2010-03-15',
                    'phone_number'      => '081234567892',
                    'occupation'        => 'Pelajar',
                    'marital_status'    => 'single',
                    'relation'          => 'child',
                ]
            );
        }

        // Create second child (Anak Kedua) if not exists
        if (!User::where('email', 'fatimah.zahra@gmail.com')->exists()) {
            $child2User = User::create([
                'name'     => 'Fatimah Zahra',
                'email'    => 'fatimah.zahra@gmail.com',
                'password' => bcrypt('password'),
            ]);
            $child2User->assignRole('head-of-family');

            \App\Models\FamilyMember::firstOrCreate(
                ['user_id' => $child2User->id, 'head_of_family_id' => $headOfFamily->id],
                [
                    'profile_picture'   => 'profile-pictures/default-avatar.jpg',
                    'identity_number'   => '3201234567890004',
                    'gender'            => 'female',
                    'date_of_birth'     => '2015-11-08',
                    'phone_number'      => '081234567893',
                    'occupation'        => 'Pelajar',
                    'marital_status'    => 'single',
                    'relation'          => 'child',
                ]
            );
        }

        UserFactory::new ()->count(15)->create()->each(function ($user) {
            $user->assignRole('head-of-family');
        });
    }
}
