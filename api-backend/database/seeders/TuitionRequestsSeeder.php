<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuitionRequestsSeeder extends Seeder
{
    public function run()
    {
        // Get actual Learner IDs from the database
        $learners = DB::table('learners')->pluck('LearnerID')->toArray();

        if (count($learners) < 2) {
            // Ensure there are at least two learners before seeding tuition requests
            return;
        }

        DB::table('tuition_requests')->insert([
            [
                'LearnerID' => $learners[0],  // First learner
                'class' => 'Class 6',
                'subjects' => 'Math, English',
                'asked_salary' => 4000,
                'curriculum' => 'National',
                'days' => '3 days/week',
                'location' => 'Dhaka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'LearnerID' => $learners[1], // Second learner
                'class' => 'Class 8',
                'subjects' => 'Physics, Chemistry',
                'asked_salary' => 5000,
                'curriculum' => 'English',
                'days' => '4 days/week',
                'location' => 'Chattogram',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
