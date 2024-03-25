<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class AuthSocialiteController extends Controller
{
     /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

/**
 * @OA\Get(
 *     path="/v1/auth/google",
 *     summary="Redirect to Google for authentication",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to Google login page"
 *     )
 * )
 */

    public function redirectToGoogle()
    {
        //return Socialite::driver('google')->redirect();
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     /**
 * @OA\Get(
 *     path="/v1/auth/google/callback",
 *     summary="Handle Google authentication callback",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to the frontend with authentication token",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             example="Redirect to frontend URL with authentication token"
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error Message"
 *             )
 *         )
 *     )
 * )
 */
    public function handleGoogleCallback()
    {
      try {
            $socialiteUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in your database
            $user = User::where('email', $socialiteUser->email)->first();

            if ($user) {
                $token = $user->createToken('API Auth Token')->accessToke+n;
            } else {
                // If the user doesn't exist, create a new user
                $newUser = new User();
                $newUser->name = $socialiteUser->name;
                $newUser->email = $socialiteUser->email;
                $newUser->provider_id = $socialiteUser->id;
                $newUser->profile_picture = $socialiteUser->avatar;
                $newUser->password = bcrypt(Str::random(123654));
                $newUser->save();

                // Create a token for the new user
                $token = $newUser->createToken('AppName')->accessToken;
            }

	                // Use $newUser if it was created, or $user if it already existed
            $redirectUrl = 'http://localhost:3000/social-auth-redirect?token=' . $token . '&user=' . json_encode($user ?: $newUser);
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
