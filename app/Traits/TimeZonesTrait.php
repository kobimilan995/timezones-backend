<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TimeZonesTrait {

    public function storeTimeZone($data, $user) {

        $gmt_diff = $data['gmt_differance']?:0;
        return DB::insert("INSERT INTO time_zones (tz_name, tz_city, tz_gmt_diff, tz_user_id) values ('{$data['name']}', '{$data['city']}', {$gmt_diff}, {$user->id})");
    }

    public function getUsersTimeZones($user_id) {
        return DB::select("SELECT * 
        FROM time_zones 
        WHERE tz_user_id = {$user_id}");
    }

    public function getFilteredUsersTimeZones($user_id, $query) {
        return DB::select("SELECT *
        FROM time_zones
        WHERE tz_user_id = {$user_id} AND tz_name LIKE '%{$query}%'");
    }

}