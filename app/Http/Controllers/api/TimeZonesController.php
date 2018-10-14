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
        $user_id = $request['user']->id;
        $timeZonesData = $query ? $this->getFilteredUsersTimeZones($user_id, $query) : $this->getUsersTimeZones($user_id);

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
    
    public function show($tz_id)
    {

    }

    public function update($tz_id, Request $request)
    {

    }

    public function destroy($tz_id)
    {

    }
}
