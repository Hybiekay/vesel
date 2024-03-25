<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\BusinessDetails;

class BusinessDetailsController extends Controller
{


    /**
 * @OA\Get(
 *     path="/v1/business-details",
 *     summary="Get business details for the authenticated user",
 *     tags={"Business Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="businessDetails",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/BusinessDetails")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             example={"error": "Internal server error"}
 *         )
 *     )
 * )
 */

public function getBusinessDetails()
{
    try {
        $user = auth()->user();
        $businessDetails = $user->businessDetails;

        return response()->json(['businessDetails' => $businessDetails]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


/**
 * @OA\Post(
 *     path="/v1/business-details",
 *     summary="Store business details",
 *     tags={"Business Details"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"user_id", "business_name", "phone", "business_address", "bank_name", "account_number"},
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     description="The ID of the user associated with the business"
 *                 ),
 *                 @OA\Property(
 *                     property="business_name",
 *                     type="string",
 *                     description="The name of the business"
 *                 ),
 *                 @OA\Property(
 *                     property="business_email",
 *                     type="string",
 *                     format="email",
 *                     description="The email address of the business (optional)"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string",
 *                     description="The phone number of the business"
 *                 ),
 *                 @OA\Property(
 *                     property="business_address",
 *                     type="string",
 *                     description="The address of the business"
 *                 ),
 *                 @OA\Property(
 *                     property="bank_name",
 *                     type="string",
 *                     description="The name of the bank associated with the business"
 *                 ),
 *                 @OA\Property(
 *                     property="account_number",
 *                     type="string",
 *                     description="The bank account number of the business"
 *                 ),
 *                 @OA\Property(
 *                     property="certificate",
 *                     type="string",
 *                     description="Certificate information of the business (optional)"
 *                 ),
 *                 @OA\Property(
 *                     property="profile_picture",
 *                     type="string",
 *                     description="Profile picture URL of the business (optional)"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Business details created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Business details created successfully"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/BusinessDetails"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Error message"
 *             )
 *         )
 *     )
 * )
 */


    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'business_name' => 'required|string',
                'business_email' => 'unique:business_details,business_email|nullable|email',
                'phone' => 'required|string',
                'business_address' => 'required|string',
                'bank_name' => 'required|string',
                'account_number' => 'required|string',
                'certificate' => 'nullable|string',
                'profile_picture' => 'nullable|string',
            ]);

            $businessDetails = BusinessDetails::create($request->all());

            return response()->json(['message' => 'Business details created successfully', 'data' => $businessDetails]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
/**
 * @OA\Get(
 *     path="/v1/business-details/{id}",
 *     summary="Get business details by ID",
 *     tags={"Business Details"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the business details to retrieve",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/BusinessDetails"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Business details not found",
 *         @OA\JsonContent(
 *             example={"message": "Business details not found"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             example={"error": "Internal server error"}
 *         )
 *     )
 * )
 */

    public function show($id)
    {
        try {
            $businessDetails = BusinessDetails::where('user_id', $id)->first();

            if (!$businessDetails) {
                return response()->json(['message' => 'Business details not found'], 404);
            }

            return response()->json(['data' => $businessDetails]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


/*
public function update(Request $request)
{
    try {
        $user = auth()->user();
        $businessDetails = $user->businessDetails;

        // Check if businessDetails exists, if not, create a new entry
        if (!$businessDetails) {
            $businessDetails = new BusinessDetails();
            $businessDetails->user_id = $user->id;
        }

        $request->validate([
            'business_name' => 'sometimes|required|string',
            'business_email' => 'sometimes|unique:business_details,business_email,' . $businessDetails->id . '|nullable|email',
            'phone' => 'sometimes|required|string',
            'business_address' => 'sometimes|required|string',
            'bank_name' => 'sometimes|required|string',
            'account_number' => 'sometimes|required|string',
            'certificate' => 'nullable|mimes:jpeg,jpg,png,pdf,doc,docx',
            'profile_picture' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('certificate')) {
            $this->deleteFileIfExists($businessDetails->certificate);
            $certificate = $this->uploadFile($request->file('certificate'));
            $businessDetails->certificate = $certificate;
        }

        if ($request->hasFile('profile_picture')) {
            $this->deleteFileIfExists($businessDetails->profile_picture);
            $profilePicture = $this->uploadFile($request->file('profile_picture'));
            $businessDetails->profile_picture = $profilePicture;
        }

        // Update other fields
        $businessDetails->update($request->except(['certificate', 'profile_picture']));

        return response()->json(['message' => 'Business details updated successfully', 'data' => $businessDetails]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
 */

private function deleteFileIfExists($filePath)
{
    if ($filePath && file_exists(public_path($filePath))) {
        unlink(public_path($filePath));
    }
}

private function uploadFile($file)
{
    $extension = $file->getClientOriginalExtension();
    $imageName = Str::random(32) . "." . $extension;
    $file->move('uploads/', $imageName);

    return 'uploads/' . $imageName;
}

/**
 * @OA\Post(
 *     path="/v1/business-details/update",
 *     summary="Update business details for the authenticated user",
 *     tags={"Business Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Business details data to update",
 *         @OA\JsonContent(
 *             required={"business_name"},
 *             @OA\Property(property="business_name", type="string"),
 *             @OA\Property(property="business_email", type="string", format="email"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="business_address", type="string"),
 *             @OA\Property(property="bank_name", type="string"),
 *             @OA\Property(property="account_number", type="string"),
 *             @OA\Property(
 *                 property="certificate_status",
 *                 type="string",
 *                 enum={"new", "existing"},
 *                 example="new"
 *             ),
 *             @OA\Property(
 *                 property="profile_picture_status",
 *                 type="string",
 *                 enum={"new", "existing"},
 *                 example="new"
 *             ),
 *             @OA\Property(
 *                 property="certificate",
 *                 type="string",
 *                 format="binary",
 *                 description="New certificate file (only required if certificate_status is 'new')"
 *             ),
 *             @OA\Property(
 *                 property="profile_picture",
 *                 type="string",
 *                 format="binary",
 *                 description="New profile picture file (only required if profile_picture_status is 'new')"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Business details updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Business details updated successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/BusinessDetails")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             example={"error": "Internal server error"}
 *         )
 *     )
 * )
 */

public function update(Request $request)
{
    try {
        $user = auth()->user();
        $businessDetails = $user->businessDetails;

        $request->validate([
            'business_name' => 'nullable|required',
            'business_email' => 'unique:business_details,business_email,' . $businessDetails->id . '|nullable|email',
            'phone' => 'nullable|string',
            'business_address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
	    'cerfificate' => $request->input('certificate_status') === 'new'
                ? 'nullable|mimes:jpeg,jpg,png,pdf,doc,docx'
                : 'sometimes|string',
	    'profile_picture' => $request->input('profile_picture_status') === 'new'
                ? 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
                : 'sometimes|string',
        ]);


        if ($request->profile_picture_status === 'new' && $request->hasFile('profile_picture')) {
            // Delete existing profile picture if it exists
            if ($businessDetails->profile_picture) {
                File::delete(public_path('uploads/' . $businessDetails->profile_picture));
            }

            $profilePicture = $request->file('profile_picture');
            $profilePictureName = Str::random(32) . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicture->move(public_path('uploads/'), $profilePictureName);

            $businessDetails->profile_picture = $profilePictureName;
        }

        // Handle certificate
        if ($request->certificate_status === 'new' && $request->hasFile('certificate')) {
            // Delete existing certificate if it exists
            if ($businessDetails->certificate) {
                File::delete(public_path('uploads/' . $businessDetails->certificate));
            }

            $certificate = $request->file('certificate');
            $certificateName = Str::random(32) . '.' . $certificate->getClientOriginalExtension();
            $certificate->move(public_path('uploads/'), $certificateName);

            $businessDetails->certificate = $certificateName;
        }

        $businessDetails->update($request->except(['profile_picture', 'certificate']));

        return response()->json(['message' => 'Business details updated successfully', 'data' => $businessDetails]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


/**
 * @OA\Delete(
 *     path="/v1/business-details/{id}",
 *     summary="Delete business details by ID",
 *     tags={"Business Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the business details to delete",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Business details deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Business details deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Business details not found",
 *         @OA\JsonContent(
 *             example={"message": "Business details not found"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             example={"error": "Internal server error"}
 *         )
 *     )
 * )
 */

    public function destroy($id)
    {
        try {
            $businessDetails = BusinessDetails::findOrFail($id);

            $businessDetails->delete();

            return response()->json(['message' => 'Business details deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
