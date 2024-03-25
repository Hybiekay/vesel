<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UserAlert;
//use App\Notifications\PasswordChanged;




/**
 *   @OA\Info(
 *     title="Vensle API ",
 *     version="1.0.0",
 *     description="Description of your API",
 *     @OA\Contact(
 *         email="hybiekay2@gmail.com"
 *     )
 * )
 * @OA\Post(
 *     path="/v1/register",
 *     summary="Register a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "email", "password", "password_confirmation", "phone_number", "address"},
 *                 @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                 @OA\Property(property="password", type="string", example="password"),
 *                 @OA\Property(property="password_confirmation", type="string", example="password"),
 *                 @OA\Property(property="phone_number", type="string", example="1234567890"),
 *                 @OA\Property(property="address", type="string", example="123 Street, City"),
 *                 @OA\Property(property="business_name", type="string", maxLength=255, nullable=true, example="Acme Inc. "),
 *                 @OA\Property(property="business_email", type="string", format="email", nullable=true, example="info@acme.com"),
 *                 @OA\Property(property="phone", type="nullable", nullable=true, example=null),
 *                 @OA\Property(property="business_address", type="string", nullable=true, example="456 Business Street, Business City"),
 *                 @OA\Property(property="certificate", type="string", nullable=true, example="Certificate of Business"),
 *                 @OA\Property(property="bank_name", type="string", nullable=true, example="Bank of Business"),
 *                 @OA\Property(property="account_number", type="string", nullable=true, example="1234567890"),
 *                 @OA\Property(property="profile_picture", type="string", nullable=true, example="https://example.com/profile.jpg")
 *             )
 *         )
 *     ),
 *     @OA\Response(response="200", description="Successful registration"),
 *     @OA\Response(response="400", description="Invalid input")
 * )
 */


class UserAuthController extends Controller

{







	/**
	* Register user
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\JsonResponse
	*/
	public function register(Request $request)
	{
		try {
		    $validated_data = $request->validate([
			'name' => 'required|max:255',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed',
			'phone_number' => 'required',
			'address' => 'required',
			'business_name' => 'nullable|max:255',
		    ]);

		    $validated_data['password'] = bcrypt($request->password);

		    $user = User::create($validated_data);

            // Add business details for the user
            $businessDetailsData = $request->only([
                'business_name',
                'business_email',
                'phone',
                'business_address',
                'certificate',
                'bank_name',
                'account_number',
                'profile_picture',
            ]);

            $user->businessDetails()->create($businessDetailsData);



		    $token = $user->createToken('API Auth Token')->accessToken;

		    return response()->json(['user' => $user, 'token' => $token], 200);
		} catch (\Illuminate\Validation\ValidationException $e) {
		    return response()->json(['errors' => $e->errors()], 422);
		} catch (\Exception $e) {
		    return response()->json(['error' => $e->getMessage()], 500);
		}
	}

        /**
        * Login user
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\JsonResponse
        */

        /**
 * @OA\Post(
 *     path="/v1/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example="3600")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

	public function login(Request $request)
	{
		try {
		    $validated_data = $request->validate([
			'email' => 'email|required',
			'password' => 'required'
		    ]);

		    if (!auth()->attempt($validated_data)) {
			return response()->json(['message' => 'Incorrect Details. Please try again'], 401);
		    }

		    $token = auth()->user()->createToken('API Auth Token')->accessToken;

		    return response()->json(['user' => auth()->user(), 'token' => $token], 200);
		} catch (\Illuminate\Validation\ValidationException $e) {
		    return response()->json(['errors' => $e->errors()], 422);
		} catch (\Exception $e) {
		    return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	//TODO try catch
        /**
        * Update user details
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\JsonResponse
	*/
/**
 * @OA\Post(
 *     path="/v1/update-profile",
 *     summary="Update user",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(property="name", type="string", nullable=true, example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", nullable=true, example="john@example.com"),
 *                 @OA\Property(property="phone_number", type="string", nullable=true, example="1234567890"),
 *                 @OA\Property(property="address", type="string", nullable=true, example="123 Street, City"),
 *                 @OA\Property(property="profile_picture", type="file", nullable=true, example=null)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */


public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'sometimes|string',
        'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
        'phone_number' => 'sometimes|string',
        'address' => 'sometimes|string',
	'profile_picture' => $request->input('imageStatus') === 'new'
            ? 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
            : 'sometimes|string',
    ]);


    $user = auth()->user();

    // Update user details
    $user->update($request->only(['name', 'email', 'phone_number', 'address']));

    // Handle profile picture update


    if ($request->imageStatus === 'new' && $request->hasFile('profile_picture')) {
        // Check if the user already has a profile picture
        if ($user->profile_picture) {
            // If a profile picture exists, delete the old image
            if (file_exists(public_path('uploads/' . $user->profile_picture))) {
                unlink(public_path('uploads/' . $user->profile_picture));
            }
        }

        // Handle file upload
        $extension = $request->file('profile_picture')->getClientOriginalExtension();
        $imageName = Str::random(32) . "." . $extension;
        $request->file('profile_picture')->move('uploads/', $imageName);

        // Update user's profile picture
        $user->update(['profile_picture' => $imageName]);
    }


    //TODO: call update once

    return response(['user' => $user, 'message' => 'Profile updated successfully'], 200);
}


	/*public function updateProfile(Request $request)
	{
		$request->validate([
		    'name' => 'sometimes|string',
		    'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
		]);

		$user = auth()->user();

		$user->update($request->only(['name', 'email']));

		// Return a success response
		return response(['user' => $user, 'message' => 'Profile updated successfully'], 200);
	}*/


        //TODO try catch
        /**
        * Change reset password
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\JsonResponse
        */

/**
 * @OA\Get(
 *     path="/user",
 *     summary="Get current user details",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="phone_number", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="profile_picture", type="string"),
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
	public function resetPassword(Request $request)
	{
		$request->validate([
		    'email' => 'required|email',
		    'new_password' => 'required',
		    'new_password_confirmation' => 'required|same:new_password',
		]);

		$user = auth()->user();

		//Check email
		/*if (!Hash::check($request->old_password, $user->password)) {
		    return response(['error' => 'Old password is incorrect'], 401);
		}*/

		$user->update(['password' => bcrypt($request->new_password)]);
		UserAlert::create([
		    'user_id' => $user->id,
		    'title' => 'Password Changed',
		    'message' => 'Your password was successfully changed.',
		]);

		return response(['message' => 'Password reset successfully'], 200);
	}


        //TODO try catch
        /**
        * Change user password
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\JsonResponse
        */
/**
 * @OA\Post(
 *     path="/v1/update-passwords",
 *     summary="Update user's password",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"old_password", "new_password", "new_password_confirmation"},
 *                 @OA\Property(property="old_password", type="string"),
 *                 @OA\Property(property="new_password", type="string"),
 *                 @OA\Property(property="new_password_confirmation", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password updated successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */


	public function updatePassword(Request $request)
	{
		$request->validate([
		    'old_password' => 'required',
		    'new_password' => 'required',
		    'new_password_confirmation' => 'required|same:new_password',
		]);

		$user = auth()->user();

		if (!Hash::check($request->old_password, $user->password)) {
		    return response(['error' => 'Old password is incorrect'], 401);
		}

		$user->update(['password' => bcrypt($request->new_password)]);
	/*
	 * TODO ?
       // Revoke the current user's access token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        // Create a new access token for the user
        $newToken = $user->createToken('token-name')->plainTextToken;
	*/

		// Send a notification about the password change
		//$user->notify(new PasswordChanged(), ['database']);

	        // Create a UserAlert for the password change
		UserAlert::create([
		    'user_id' => $user->id,
		    'title' => 'Password Changed',
		    'message' => 'Your password was successfully changed.',
		]);

		return response(['message' => 'Password updated successfully'], 200);
	}

        //TODO try catch
        /**
        * Change user password
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\JsonResponse
        */
/**
 * @OA\Post(
 *     path="/v1/update-profile-picture'",
 *     summary="Update user's profile picture",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"profile_picture"},
 *                 @OA\Property(property="profile_picture", type="string", format="binary", description="The image file to upload")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile picture updated successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */



	public function updateProfilePicture(Request $request)
	{
	    $request->validate([
		'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
	    ]);

	    $user = auth()->user();

	    // Check if the user already has a profile picture
	    if ($user->profile_picture) {
		// If a profile picture exists, delete the old image
		if (file_exists(public_path('uploads/' . $user->profile_picture))) {
		    unlink(public_path('uploads/' . $user->profile_picture));
		}
	    }

	    // Handle file upload
	    $extension = $request->file('profile_picture')->getClientOriginalExtension();
	    $imageName = Str::random(32) . "." . $extension;
	    $request->file('profile_picture')->move('uploads/', $imageName);

	    // Update user's profile picture
	    $user->update(['profile_picture' => $imageName]);

	    return response(['user' => $user, 'message' => 'Profile picture updated successfully'], 200);
	}


	/**
	 * Get user by ID with business details
	 *
	 * @param  int  $userId
	 * @return JsonResponse
	 */


     /**
 * @OA\Get(
 *     path="/v1/user/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the user to retrieve",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="phone_number", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="profile_picture", type="string"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */

	public function getUserById($userId)
	{
	    try {
		$userWithBusinessDetails = User::with('businessDetails')->find($userId);

		if (!$userWithBusinessDetails) {
		    return response()->json(['message' => 'User not found'], 404);
		}

		return response()->json(['user' => $userWithBusinessDetails], 200);
	    } catch (\Exception $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	    }
	}



}
