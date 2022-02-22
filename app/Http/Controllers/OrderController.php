<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\UserService as User;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        $user = $this->userToken($token);
        if (!$user) {
            return response()->json([
                'state' =>false,
                'message' =>'Veillez vous connecter',
            ]);
        }

        $products =  Order::with('cart')->orderBy('updated_at', 'DESC')->get();

        return response()->json($products);
    }

    public function ordersClient(Request $request)
    {
        $token = $request->header('Authorization');
        $user = $this->userToken($token);
        if (!$user) {
            return response()->json([
                'state' =>false,
                'message' =>'Veillez vous connecter',
            ]);
        }

        $orders =  Order::where('user_id',$user->id)->orderBy('updated_at', 'DESC')->get();


        return response()->json($orders);
    }
    
    public function sellerPeriode(Request $request)
    {
        $from= $request->from;
        $to= $request->to;
        $products= Product::has('order')->with('photo')->get();
        $orders =  Order::with('cart')->whereBetween(DB::raw("(STR_TO_DATE(orders.updated_at,'%Y-%m-%d'))"),[$from,$to])->where('status', 'delivered')->get();
        foreach ($products as $key => $product) {
            foreach ($orders as $key => $order) {
                foreach ($order->cart as $key => $cart) {
                   if ($product->id === $cart->product_id) {
                       $product->count += $cart->quantity;
                       $product->amount += $cart->quantity * $cart->price;
                   }else{
                    $product->count += 0;
                    $product->amount += 0;
                   }
                }
            }
        }
        return response()->json($products);
    }

    public function UserBestSeller()
    {
        $users = $this->UserSeller();
        $orders =  Order::with('cart')->get();
        foreach ($users as $key => $user) {
            $user->order_count = 0;

            $user->count_article = 0;
            $user->cumul_amount = 0;
            foreach ($orders as $key => $order) {
                if ($user->id === $order->user_id) {
                    $user->order_count += 1;

                    foreach ($order->cart as $key => $cart) {
                        if ($user->id === $cart->user_id) {
                            $user->count_article += $cart->quantity;
                            $user->cumul_amount += $cart->quantity * $cart->price;
                        }else{
                         $user->count_article += 0;
                         $user->count_article += 0;
                        }
                     }
                }else{
                    $user->order_count += 0;
                }

            }
        }
        return response()->json($users);
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
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $token = $request->header('Authorization');
        $user = $this->userToken($token);
        if (!$user) {
            return response()->json([
                'state' =>false,
                'message' =>'Veillez vous connecter',
            ]);
        }
        
        if (!$user->email_verified_at) {
            return response()->json([
                'state' =>false,
                'message' =>'Vous compte doit etre activé',
            ]);
        }

        if (count($request->cart)==0) {
            return response()->json([
                'state' =>false,
                'message' =>'Votre panier est vide',
            ]);
        }
        $order =  new Order();
        $order->order_number =  'ORD-'.strtoupper(Str::random(10));
        $order->user_id = $user->id;
        $order->total_amount =  $request->total_amount;
        $order->payment_status =  'unpaid';
        $order->status =  'new';
        $order->nom =  $request->nom;
        $order->prenoms =  $request->prenoms;
        $order->raison_social =  $request->raison_social;
        $order->email =  $request->email;
        $order->phone =  $request->phone;
        $order->shipping =  $request->shipping;
        $order->save();
        $amount = 0;
        foreach ($request->cart as $key =>  $item) {
            $cart =  new Cart();
            $product =  Product::find($item['product_id']);

            if ($product) {
                $cart->order_id =  $order->id;
                $cart->product_id =  $item['product_id'];
                $cart->quantity =  $item['quantity'];
                $cart->price =  $product->price;
                $cart->days =  $item['days'];
                $cart->to =  $item['to'];
                $cart->from =  $item['from'];
                $cart->location =  $item['location'];
                $cart->objects =  $item['objects'];
                $cart->participant =  $item['participant'];
                $cart->details =  $item['details'];
                $amount += $product->price;
                $cart->save();
            }else{
                return response()->json([
                    'state' =>false,
                    'message' =>'Produits inexistant',
                ]);
            }
        }
        $order->total_amount =  $amount;
        $order->save();
        $this->adminNotif('Gestionnaire Evenementiel','logistique-orders','Evenementiel');
        if ($order) {
            return response()->json([
                'state' =>true,
            ]);
        }else{
            return response()->json([
                'state' =>false,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,Order $order)
    {
        $token = $request->header('Authorization');
        $user = $this->userToken($token);
        if (!$user) {
            return response()->json([
                'state' =>false,
                'message' =>'Veillez vous connecter',
            ]);
        }

        foreach ($order->cart as $key => $value) {
            $value->product;
            $value->product->type;
            $value->product->photo;
        }
        return response()->json($order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->status =  $request->status;
        $order->save();

        if ($order) {
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
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
    private function adminNotif($role,$url,$module){

        if(User::getAdmin($role,$url,$module)->success == true) {
            return User::getAdmin($role,$url,$module)->user;
        }else{
            return null;
        }
    }
    private function UserSeller(){

        if(User::getUserSeller()->success == true) {
            return User::getUserSeller()->users;
        }else{
            return null;
        }
    }
}
