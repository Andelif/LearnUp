<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $primaryKey = 'AdminID';
    public $incrementing = true;

    public static function createAdmin($data)
    {
        DB::insert("INSERT INTO admins (user_id, full_name, address, contact_number, permission_req, match_made, task_assigned) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $data['user_id'],
            $data['full_name'],
            $data['address'],
            $data['contact_number'],
            $data['permission_req'],
            $data['match_made'],
            $data['task_assigned']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    public static function findByUserId($user_id)
    {
        return DB::select("SELECT * FROM admins WHERE user_id = ?", [$user_id])[0] ?? null;
    }
}