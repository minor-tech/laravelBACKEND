<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\LoginNeedVerification;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        //validate the phone number
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        //find or create a user
        $user = user::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json(['message' => 'could not process a user with that phone number.'], 401);
        }

        //send the user a one time use code

        $user->notify(new LoginNeedVerification());

        //return a response
        return response()->json(['message' => 'Text message notification sent.']);
    }

    public function verify(Request $request)
    {
        //Validate the incoming request

        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        //find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        //is the code provided the one saved

        //if so return back an auth token
        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        //if not, return back a message
        return response()->json(['message' => 'Invalid verification code.', 401]);
    }
}
