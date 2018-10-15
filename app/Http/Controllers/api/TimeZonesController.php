<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\TimeZonesTrait;
use Illuminate\Support\Facades\Validator;

class TimeZonesController extends Controller
{
    use TimeZonesTrait;

    public function index(Request $request)
    {
        $query = $request->input('search_query');
        $user = $request['user'];
        if($user->role_name != 'Admin') {
            $timeZonesData = $query ? $this->getFilteredUsersTimeZones($user->id, $query) : $this->getUsersTimeZones($user->id);
        } else {
            $timeZonesData = $query ? $this->getFilteredAllTimeZones($query) : $this->getAllTimeZones();
        }

        return response()->json([
            'type' => 'success',
            'time_zones' => $timeZonesData
        ]);
    }

    public function store(Request $request)
    {   
        $validation = Validator::make($request->all(),[ 
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'gmt_differance' => 'required|numeric|max:12|min:-12'
        ]);
        if($validation->fails()) {
            return response()->json([
                'type' => 'error',
                'data' => $validation->errors()
            ], 400);
        }
        $user = $request['user'];

        $bool = $this->storeTimeZone($request->all(), $user);

        return response()->json([
            'type' => 'success',
            'completed' => $bool
        ]);
    }
    
    public function show($tz_id, Request $request)
    {
        $time_zone_data = $this->findById($tz_id);
        if(!$time_zone_data) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Time zone does not exist']]  
              ], 404);
        }

        $auth_user = $request['user'];

        if($auth_user->id != $time_zone_data[0]->tz_user_id && $auth_user->role_name != 'Admin') {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Forbidden!']]  
              ], 403);
        }

        return response()->json([
            'type' => 'success',
            'time_zone' => $time_zone_data[0]
         ]);

    }

    public function update($tz_id, Request $request)
    {
        $validation = Validator::make($request->all(),[ 
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'gmt_differance' => 'required|numeric|max:12|min:-12'
        ]);

        if($validation->fails()) {
            return response()->json([
                'type' => 'error',
                'data' => $validation->errors()
            ], 400);
        }
        $time_zone_data = $this->findById($tz_id);
        

        if(!$time_zone_data) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Specified time zone does not exist!']]  
            ], 400);
        }

        $auth_user = $request['user'];
        if($auth_user->id != $time_zone_data[0]->tz_user_id && $auth_user->role_name != 'Admin') {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Forbidden! You are not the owner of this time zone.']]  
              ], 403);
        }

        $bool = $this->updateById($tz_id, $request->all());
        return response()->json([
            'type' => 'success',
            'completed' => $bool
        ]);


    }

    public function destroy($tz_id, Request $request)
    {
        $time_zone_data = $this->findById($tz_id);
        $auth_user = $request['user'];
        if($auth_user->id != $time_zone_data[0]->tz_user_id && $auth_user->role_name != 'Admin') {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Forbidden! You are not the owner of this time zone.']]  
              ], 403);
        }


        $bool = $this->deleteById($tz_id);

        return response()->json([
            'type' => 'success',
            'bool' => $bool
        ]);
    }
}
