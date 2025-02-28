<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


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
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
            ],
        ];

        foreach ($users as $userData) {
            // Raw SQL to insert into the 'users' table
            DB::statement("INSERT INTO users (name, email, password, role, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())", [
                $userData['name'],
                $userData['email'],
                $userData['password'],
                $userData['role'],
            ]);

            // Get the last inserted user ID
            $userId = DB::getPdo()->lastInsertId();

            // Insert corresponding data into the learners or tutors table based on the role
            if ($userData['role'] === 'learner') {
                // Raw SQL for inserting into the 'learners' table
                DB::statement("INSERT INTO learners (user_id, full_name, contact_number, gender, address) 
                    VALUES (?, ?, ?, ?, ?)", [
                    $userId,
                    $userData['name'],
                    '1234567890',  // Example contact number
                    'male',        // Example gender
                    'Dhanmondi',  // Example address
                ]);
            } elseif ($userData['role'] === 'tutor') {
                // Raw SQL for inserting into the 'tutors' table
                DB::statement("INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)", [
                    $userId,
                    $userData['name'],
                    '1234567890',  // Example contact number
                    'female',      // Example gender
                    'Bachelor of Science', // Example qualification
                    '5 years',     // Example experience
                    '50000',       // Example preferred salary
                    
                    
                ]);
            }
        }
    }
}
