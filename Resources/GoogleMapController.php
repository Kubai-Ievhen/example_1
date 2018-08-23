<?php

namespace App\Resources;

use App\Models\Geodata\State;
use Illuminate\Http\Request;

class GoogleMapController
{
    /**
     * @param Request $request
     * @return array
     */
    public static function getLatLonCity(Request $request){
        $key = SystemParametersSingleton::getParam('google_maps_key');
        $city_name = $request->get('name');
        $state = State::where('id', $request->get('state_id'))->with('country')->first()->toArray();
        $param = [
            'key'=> $key,
            'address'=> ($state['country']['name']??'').','.($state['name']??'').','.($city_name??''),
        ];

        $data = file_get_contents(env('GOOGLE_MAP_API_URL').'?'.http_build_query($param));
        $data = json_decode($data);

        if ($data->status == "OK") {
            $data = $data->results[0]->geometry->location;
        }

       return ['lat' => $data->lat ?? '0.0','lon' => $data->lng ?? '0.0'];
    }
}
