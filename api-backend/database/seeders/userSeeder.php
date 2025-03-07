<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'learner',
                'gender' => 'male',
                'contact_number' => '0123456789',
                'address' => 'Dhanmondi'
            ],
           
            [
                'name' => 'Emma Watson',
                'email' => 'emma@example.com',
                'password' => Hash::make('password'),
                'role' => 'learner',
                'gender' => 'female',
                'contact_number' => '01722222222',
                'address' => 'Uttara'
            ],
            [
                'name' => 'Noah Khan',
                'email' => 'noah@example.com',
                'password' => Hash::make('password'),
                'role' => 'learner',
                'gender' => 'male',
                'contact_number' => '01733333333',
                'address' => 'Banani'
            ],
            [
                'name' => 'Sophia Rahman',
                'email' => 'sophia@example.com',
                'password' => Hash::make('password'),
                'role' => 'learner',
                'gender' => 'female',
                'contact_number' => '01744444444',
                'address' => 'Gulshan'
            ],

            // Tutors
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'gender' => 'female',
                'contact_number' => '01811111111',
                'qualification' => 'Bachelor of Science',
                'experience' => '5 years',
                'preferred_salary' => '50000'
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'gender' => 'female',
                'contact_number' => '01822222222',
                'qualification' => 'Master of Arts',
                'experience' => '3 years',
                'preferred_salary' => '45000'
            ],
            [
                'name' => 'Robert Brown',
                'email' => 'robert@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'gender' => 'male',
                'contact_number' => '01833333333',
                'qualification' => 'Ph.D. in Mathematics',
                'experience' => '10 years',
                'preferred_salary' => '70000'
            ],
            [
                'name' => 'Mia Hasan',
                'email' => 'mia@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'gender' => 'female',
                'contact_number' => '01844444444',
                'qualification' => 'M.Sc. in Physics',
                'experience' => '7 years',
                'preferred_salary' => '60000'
            ],
            [
                'name' => 'Hridita Alam',
                'email' => 'hriditaalam1@gmail.com',
                'password' => Hash::make('123123123'),
                'role' => 'learner',
                'gender' => 'female',
                'contact_number' => '01744444444',
                'address' => 'Rampura'
            ],
            [
                'name' => 'user12',
                'email' => 'user12@gmail.com',
                'password' => Hash::make('123123123'),
                'role' => 'learner',
                'gender' => 'male',
                'contact_number' => '01744444444',
                'address' => 'Gulshan'
            ],
            [
                'name' => 'User22',
                'email' => 'user22@gmail.com',
                'password' => Hash::make('123123123'),
                'role' => 'tutor',
                'gender' => 'male',
                'contact_number' => '01833333333',
                'qualification' => 'BSc in CSE',
                'experience' => '4 years',
                'preferred_salary' => '7000'
            ]

        ];

        foreach ($users as $userData) {
            // Insert into users table using raw SQL
            DB::statement("INSERT INTO users (name, email, password, role, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())", [
                $userData['name'],
                $userData['email'],
                $userData['password'],
                $userData['role'],
            ]);

            // Retrieve the last inserted user ID
            $userId = DB::getPdo()->lastInsertId();

            // Insert into the respective table based on role
            if ($userData['role'] === 'learner') {
                DB::statement("INSERT INTO learners (user_id, full_name, contact_number, gender, address, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())", [
                    $userId,
                    $userData['name'],
                    $userData['contact_number'],
                    $userData['gender'],
                    $userData['address']
                ]);
            } elseif ($userData['role'] === 'tutor') {
                DB::statement("INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())", [
                    $userId,
                    $userData['name'],
                    $userData['contact_number'],
                    $userData['gender'],
                    $userData['qualification'],
                    $userData['experience'],
                    $userData['preferred_salary']
                ]);
            }
        }
    }
}
