<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuitionRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tuitionRequests = [
            [1, 'Class 10', 'Math, Physics', 5000, 'National', '5 days/week', 'Dhanmondi'],
            [6, 'Class 8', 'English, Science', 4000, 'Cambridge', '3 days/week', 'Uttara'],
            [5, 'Class 6', 'Bangla, ICT', 3500, 'National', '4 days/week', 'Banani'],
            [6, 'Class 12', 'Accounting, Finance', 7000, 'Edexcel', '6 days/week', 'Gulshan'],
            [6, 'Class 9', 'Chemistry, Biology', 4500, 'National', '3 days/week', 'Mirpur'],
            [5, 'Class 7', 'Math, English', 3800, 'Cambridge', '5 days/week', 'Mohakhali'],
            [5, 'Class 5', 'General Science', 3200, 'National', '4 days/week', 'Rampura'],
            [5, 'Class 11', 'Economics, Business Studies', 6500, 'Edexcel', '6 days/week', 'Farmgate'],
            [5, 'Class 4', 'Bangla, Math', 3000, 'National', '3 days/week', 'Bashundhara'],
            [5, 'Class 3', 'English, Science', 2800, 'Cambridge', '2 days/week', 'Lalmatia'],
        ];

        foreach ($tuitionRequests as $request) {
            DB::statement("INSERT INTO tuition_requests (LearnerID, class, subjects, asked_salary, curriculum, days, location, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())", $request);
        }
    }
}
