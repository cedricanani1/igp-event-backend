<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;
use App\Services\UserService as User;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRatingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRatingRequest $request)
    {
        $token = $request->header('Authorization');
        $user = $this->userToken($token);
        if (!$user) {
            return response()->json([
                'state' =>false,
                'message' =>'Veillez vous connecter',
            ]);
        }
        $rating = Rating::where('user_id',$user->id)->where('product_id',$request->product_id)->first();

        if ($rating) {
            $rating->rate =  $request->rate;
            $rating->object =  $request->object;
            $rating->message =  $request->message;
            $rating->product_id =  $request->product_id;
            $rating->save();
        }else {
            
            $types =  new Rating;
            $types->rate =  $request->rate;
            $types->object =  $request->object;
            $types->message =  $request->message;
            $types->product_id =  $request->product_id;
            $types->user_id =  $user->id;
            $types->save();
        }

        if (isset($types) || isset($rating)) {
            return response()->json([
                'state' =>true
            ]);
        }else{
            return response()->json([
                'state' =>false
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function edit(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRatingRequest  $request
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRatingRequest $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        //
    }
        private function userToken($token){

        if(User::get($token)->success == true) {
            return User::get($token)->user;
        }else{
            return null;
        }
    }
}
