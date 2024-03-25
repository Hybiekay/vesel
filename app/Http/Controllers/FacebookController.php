<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class FacebookController extends Controller
{

    /**
 * @OA\Get(
 *     path="/v1/auth/facebook",
 *     summary="Authenticate via Facebook",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to Facebook authentication page"
 *     )
 * )
 */

    public function facebookpage()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }


    /**
 * @OA\Get(
 *     path="/v1/auth/facebook/callback",
 *     summary="Handle Facebook authentication callback",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to the frontend with the authentication token"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */

    public function facebookredirect()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            $finduser = User::where('provider_id', $user->id)->first();

            if ($finduser) {
                $token = $finduser->createToken('API Auth Token')->accessToken;
                $redirectUrl = 'http://localhost:3000/social-auth-redirect?token=' . $token . '&user=' . json_encode($finduser);
                return redirect($redirectUrl);
            } else {
                $newUser = User::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'provider_id' => $user->id,
                    'profile_picture' => $user->avatar,
                    'password' => encrypt('123456dummy')
                ]);
                $token = $newUser->createToken('API Auth Token')->accessToken;

                $redirectUrl = 'http://localhost:3000/social-auth-redirect?token=' . $token . '&user=' . json_encode($newUser);
                return redirect($redirectUrl);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
