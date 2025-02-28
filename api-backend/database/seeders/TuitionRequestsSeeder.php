<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;



class TuitionRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("
        INSERT INTO tuition_requests (LearnerID, class, subjects, asked_salary, curriculum, days, location, created_at, updated_at) VALUES
        (1, 'Class 6', 'Math, English', 4000, 'National', '3 days/week', 'Dhaka', NOW(), NOW()),
        (4, 'Class 8', 'Physics, Chemistry', 5000, 'English', '4 days/week', 'Chattogram', NOW(), NOW()),
        (1, 'Class 10', 'Biology, Math', 5500, 'Bangla Medium', '5 days/week', 'Rajshahi', NOW(), NOW()),
        (4, 'Class 5', 'Science, Bangla', 3500, 'English Medium', '3 days/week', 'Khulna', NOW(), NOW()),
        (1, 'Class 7', 'ICT, Math', 4500, 'National', '4 days/week', 'Sylhet', NOW(), NOW())
    ");
    }
}
