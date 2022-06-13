<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTrip;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TripController extends Controller
{
    /**
     * Create user trip
     */
    public function create(Request $request): object
    {
        //validation
        $input = $request->validate([
            'title' => ['required'],
            'origin_city_id' => [
                'required', 
                'numeric',
                'exists:cities,id',
            ],
            'destination_city_id' => [
                'required', 
                'numeric',
                'exists:cities,id',
                'different:origin_city_id'
            ],
            'date_start' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'date_end' => [
                'required',
                'date',
                'after:date_start'
            ],
            'trip_type' => [
                'required',
                'exists:trip_types,id'
            ],
            'description' => 'required'
        ]);

        //Save data
        $user_id = Auth::user()->id;
        $userTrip = app(UserTrip::class);
        $userTrip->user_id = $user_id;
        $userTrip->origin = $request->origin_city_id;
        $userTrip->destination = $request->destination_city_id;
        $userTrip->trip_type_id = $request->trip_type;
        $userTrip->start_trip = $request->date_start;
        $userTrip->end_trip = $request->date_end;
        $userTrip->title = $request->title;
        $userTrip->description = $request->description;
        
        $message = ['message' => 'Data trip failed to save'];

        if ($userTrip->save()) {
            $message = ['message' => 'Data trip saved'];
        }

        return response()->json($message, HttpResponse::HTTP_ACCEPTED);

    }
    
    /**
     * Update data trip
     */
    public function update($id, Request $request): object
    {
        //validation
        $input = $request->validate([
            'origin_city_id' => [
                'numeric',
                'exists:cities,id',
            ],
            'destination_city_id' => [
                'numeric',
                'exists:cities,id',
                'different:origin_city_id'
            ],
            'date_start' => [
                'date',
                'after_or_equal:today'
            ],
            'date_end' => [
                'date',
                'after:date_start'
            ],
            'trip_type' => [
                'exists:trip_types,id'
            ]
        ]);

        //Check for data author
        $user_id = Auth::user()->id;
        $userTrip = UserTrip::where('id', $id)->where('user_id', $user_id)->first();

        //Save data if exist
        if (!is_null($userTrip)) {
            $input_allowed = $request->only([
                'user_id',
                'origin',
                'destination',
                'trip_type_id',
                'start_trip',
                'end_trip',
                'title',
                'description'
            ]);
            foreach ($input_allowed as $input => $value){
                $userTrip->{$input} = $value;
            }
    
            $message = ['message' => 'Data trip failed to update'];
    
            if ($userTrip->save()) {
                $message = ['message' => 'Data trip updated'];
            }

        } else {
            $message = ['message' => 'Data not found'];
        }

        return response()->json($message, HttpResponse::HTTP_OK);
    }

    /**
     * Delete data trip
     */
    public function delete($id): object
    {
        $user_id = Auth::user()->id;
        $userTrip = UserTrip::where('id', $id)->where('user_id', $user_id)->first();

        $message = ['message' => 'Data trip not found'];

        if (!is_null($userTrip)) {
            $userTrip->delete();

            $message = ['message' => 'Data trip deleted'];
        }

        return response()->json($message, HttpResponse::HTTP_ACCEPTED);
    }

    public function get($id): object
    {
        $user_id = Auth::user()->id;
        $userTrip = UserTrip::where('id', $id)->where('user_id', $user_id)->first();
        $message = ['message' => 'Your Data trip not found'];

        if (!is_null($userTrip)) {
            return response()->json($userTrip, HttpResponse::HTTP_OK);
        }

        return response()->json($message, HttpResponse::HTTP_OK);
    }

    public function list(): object
    {
        $user_id = Auth::user()->id;
        $userTrip = UserTrip::where('user_id', $user_id)->get();
        $message = ['message' => 'Your Data trip not found'];

        if (!is_null($userTrip)) {
            return response()->json($userTrip, HttpResponse::HTTP_OK);
        }

        return response()->json($message, HttpResponse::HTTP_OK);
    }
}
