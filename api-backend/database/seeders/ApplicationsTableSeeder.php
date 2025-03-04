<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement("
            INSERT INTO applications (tution_id, learner_id, tutor_id, matched, status, created_at, updated_at) VALUES
            (6, 5, 5, 0, 'Applied', NOW(), NOW()),
            (3, 5, 5, 0, 'Shortlisted', NOW(), NOW()),
            (3, 4, 5, 0, 'Confirmed', NOW(), NOW()),
            (4, 5, 5, 0, 'Cancelled', NOW(), NOW()),
            (7, 5, 5, 0, 'Applied', NOW(), NOW()),
            (4, 5, 5, 0, 'Shortlisted', NOW(), NOW()),
            (7, 5, 5, 0, 'Confirmed', NOW(), NOW()),
            (8, 5, 5, 0, 'Cancelled', NOW(), NOW()),
            (10, 4, 5, 0, 'Applied', NOW(), NOW()),
            (10, 5, 5, 0, 'Shortlisted', NOW(), NOW())
        ");
    }
}
