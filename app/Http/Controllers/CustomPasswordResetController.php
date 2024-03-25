<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CustomPasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomPasswordResetController extends Controller
{


    /**
 * @OA\Post(
 *     path="/v1/forgot-password",
 *     summary="Forgot password",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email"},
 *                 @OA\Property(property="email", type="string", format="email")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset link sent successfully"
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }

        $token = Str::random(60);

        CustomPasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        return response()->json(['message' => 'Password reset token generated successfully', 'token' => $token]);
    }
        /**
 * @OA\Post(
 *     path="/v1/reset-password",
 *     summary="Reset user's password",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "new_password", "new_password_confirmation"},
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="token", type="string"),
 *                 @OA\Property(property="password", type="string"),
 *                 @OA\Property(property="password_confirmation", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully"
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $passwordReset = CustomPasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$passwordReset) {
            return response()->json(['error' => 'Invalid email or token'], 422);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the user's password
        $user->update(['password' => bcrypt($request->password)]);

        // Delete the password reset record
        $passwordReset->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}

