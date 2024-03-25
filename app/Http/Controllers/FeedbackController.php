<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Schema(
 *     schema="Feedback",
 *     title="Feedback",
 *     description="Feedback details",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="rating", type="integer", format="int32"),
 *     @OA\Property(property="product_id", type="integer", format="int64"),
 *     @OA\Property(property="parent_id", type="integer", format="int64"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */

class FeedbackController extends Controller
{
    //public function __construct()
    //{
        //$this->middleware('auth:api');
    //}

    /**
 * @OA\Post(
 *     path="/v1/feedback",
 *     summary="Create feedback for a product",
 *     tags={"Feedback"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Feedback details",
 *         @OA\JsonContent(
 *             required={"content", "product_id"},
 *             @OA\Property(property="content", type="string", example="Great product!"),
 *             @OA\Property(property="rating", type="integer", format="int32", minimum=1, maximum=5, example=5),
 *             @OA\Property(property="product_id", type="integer", format="int64", example=1),
 *             @OA\Property(property="parent_id", type="integer", format="int64", example=1),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Feedback created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Feedback")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Invalid or missing authentication token",
 *         @OA\JsonContent(
 *             example={"error": "Unauthorized"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity - Validation Error",
 *         @OA\JsonContent(
 *             example={"errors": {"content": {"The content field is required."}}}
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

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'product_id' => 'required|exists:products,id',
            'parent_id' => 'nullable|exists:feedback,id',
        ]);

        $user = Auth::user();

        // Check if it's a reply or a new feedback
        if ($request->filled('parent_id')) {
            // It's a reply, so create a new feedback entry with the parent_id
            $feedback = Feedback::create([
                'content' => $request->input('content'),
                //'rating' => $request->input('rating'),
                'user_id' => $user->id,
                'product_id' => $request->input('product_id'),
                'parent_id' => $request->input('parent_id'),
            ]);
        } else {
            // It's a new feedback entry
            $feedback = Feedback::create([
                'content' => $request->input('content'),
                'rating' => $request->input('rating'),
                'user_id' => $user->id,
                'product_id' => $request->input('product_id'),
            ]);

            // Update the product rating directly only if it's not a reply
            $this->updateProductRating($request->input('product_id'));
        }

        return response()->json($feedback, 201);
    }

    private function updateProductRating($productId)
    {
        $product = Product::find($productId);

        // Calculate the new average rating
        $averageRating = Feedback::where('product_id', $productId)->avg('rating');

        // Update the product rating field
        $product->update(['ratings' => $averageRating]);
    }


    /**
 * @OA\Get(
 *     path="/v1/feedback/{product_id}",
 *     summary="Fetch feedback for a specific product",
 *     tags={"Feedback"},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to fetch feedback for",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback fetched successfully",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Feedback")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No feedback found for the product",
 *         @OA\JsonContent(
 *             example={"message": "No feedback found for the product"}
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

    public function index($product_id)
    {
	    $feedback = Feedback::where('product_id', $product_id)
		    ->with(['user', 'product', 'parent'])
	      	    ->orderBy('created_at', 'desc')
		    ->get();

        return response()->json($feedback);
    }
}
