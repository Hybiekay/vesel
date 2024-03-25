<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


 /**
 * @OA\Schema(
 *     schema="Order",
 *     required={"id", "user_id", "products"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product")),
 * )
 *   @OA\Schema(
 *     schema="Product",
 *     required={"id", "name", "price"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 * )
 *   @OA\Schema(
 *     schema="CartItem",
 *     required={"id", "name", "price"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *
 * )
 * /**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User details",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category details",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Category Name"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 * )
* @OA\Schema(
 *     schema="BusinessDetails",
 *     title="BusinessDetails",
 *     description="Business details",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="business_name", type="string", example="Business Name"),
 *     @OA\Property(property="business_email", type="string", format="email", example="business@example.com"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="business_address", type="string", example="123 Main St, City, Country"),
 *     @OA\Property(property="bank_name", type="string", example="Bank Name"),
 *     @OA\Property(property="account_number", type="string", example="1234567890"),
 *     @OA\Property(property="certificate", type="string", example="Certificate details"),
 *     @OA\Property(property="profile_picture", type="string", example="https://example.com/profile.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-25 12:00:00"),
 * )


 */







class OrderController extends Controller
{
    //

    /**
     * Get orders for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */


     /**
 * @OA\Get(
 *     path="/v1/user/orders",
 *     summary="Get current user's orders",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Order")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */


    public function getUserOrders()
    {
        try {
            $user = Auth::user();
	    $orders = Order::with('products')
		    ->where('user_id', $user->id)
		    ->get();

            return response()->json($orders);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Get details of a specific order by ID.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */


     /**
 * @OA\Get(
 *     path="/v1/user/orders/{orderId}",
 *     summary="Get a specific order for the current user",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="orderId",
 *         in="path",
 *         required=true,
 *         description="ID of the order to retrieve",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Order")
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=404, description="Order not found")
 * )
 */

    public function getOrderDetails($orderId)
    {
        try {
            $user = Auth::user();

            //$order = Order::with('products')->where('user_id', $user->id)->findOrFail($orderId);
	    $order = Order::with('products')
		    ->where('user_id', $user->id)
		    ->find($orderId);

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
	    }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTotalOrders()
    {
        $user = Auth::user();
        $totalOrders = Order::where('user_id', $user->id)->count();
        return response()->json(['totalOrders' => $totalOrders]);
    }

}
