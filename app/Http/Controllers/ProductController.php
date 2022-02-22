<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Photo;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products =  Product::with('type','rate','photo')->get();
        foreach ($products as $key => $product) {
            $rate=0;
            foreach ($product->rate as $key => $value) {
                $rate += (int) $value->rate;
            }
            if (count($product->rate) > 0) {
                $product->start = round($rate/count($product->rate));
            }else{
                $product->start =  0;
            }

        }
        if ($products) {
            return response()->json([
                'state' =>true,
                'data' =>$products
            ]);
        }
        return response()->json($products);

    }

    public function bestRate()
    {
        $products =  Product::with('photo')->withCount('order','type','rate')->orderByDesc("rate_count")->get();
        foreach ($products as $key => $product) {
            $rate=0;
            foreach ($product->rate as $key => $value) {
                $rate += (int) $value->rate;
            }
            if (count($product->rate) > 0) {
                $product->start = round($rate/count($product->rate));
            }else{
                $product->start =  0;
            }
            $product->type;

        }
        return response()->json($products);
    }


    public function best(Request $request)
    {
        $status= $request->status;
        $products =  Product::with('photo')->withCount('order','type')->orderByDesc("order_count")->whereHas('order', function($q) use ($status){
            $q->where('status', $status);
        })->get();
        foreach ($products as $key => $product) {
            $rate=0;
            foreach ($product->rate as $key => $value) {
                $rate += (int) $value->rate;
            }

            if (count($product->rate) > 0) {
                $product->start = round($rate/count($product->rate));
            }else{
                $product->start =  0;
            }

        }
        return response()->json($products);
    }
     public function sellerAlltime()
    {

        $products= Product::has('order')->with('photo')->get();
        $orders =  Order::with('cart')->where('status', 'delivered')->get();
        foreach ($products as $key => $product) {
                    $product->count = 0;
                    $product->amount = 0;
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

    public function bestview()
    {
        $products =  Product::with('photo')->orderByDesc("view")->orderByDesc("view")->get();

        return response()->json($products);
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
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $product =  new Product;
        $product->libelle =  $request->libelle;
        $product->description =  $request->description;
        $product->slug = str_replace(" ","-",$request->libelle);
        $product->stock =  $request->stock;
        $product->price =  $request->price;
        $product->discount =  $request->discount;
        $product->categorie_id =  $request->categorie_id;
        $product->save();
        if (request()->hasFile('photo')) {
            $files = $request->file('photo');
            foreach ($files as $file) {
                $fileName= $file->getClientOriginalName();
                $path = $file->storeAs('Products', $fileName);
                $img =  new Photo();
                $img->path = $path;
                $img->product_id = $product->id;
                $img->save();
            }
        }
        if ($product) {
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $rate=0;
        $product->view +=1;
        $product->save();
       foreach ($product->rate as $key => $value) {
        $rate += (int) $value->rate;
       }
       if (count($product->rate) > 0) {
        $product->start = round($rate/count($product->rate));
       }else{
        $product->start = 0;
       }

       $product->type;
       $product->photo;
       if ($product) {
            return response()->json([
                'state' =>true,
                'data' =>$product
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->libelle =  $request->libelle;
        $product->description =  $request->description;
        $product->slug = str_replace(" ","-",$request->libelle);
        $product->stock =  $request->stock;
        $product->price =  $request->price;
        $product->discount =  $request->discount;
        $product->categorie_id =  $request->categorie_id;
        $product->save();
        if (request()->hasFile('photo')) {
            $files = $request->file('photo');
            foreach ($files as $file) {
                $fileName= $file->getClientOriginalName();
                $path = $file->storeAs('Products', $fileName);
                $img =   Photo::findOrFail($product->id);
                $img->path = $path;
                $img->save();
            }
        }
        if ($product) {
            return response()->json([
                'state' =>true
            ]);
        }else{
            return response()->json([
                'state' =>false
            ]);
        }
    }

    public function deleteFile(Request $request){
        if ($request->path) {
            File::delete($request->path);
            $tof = Photo::findOrFail($request->photo_id);
            $tof->delete();
        }
        return response()->json([
            'state'=> true,
        ]);
    }
    public function addFile(Request $request){
        $Product = Product::findOrFail($request->product_id);
        if ($Product) {
            if (request()->hasFile('photo')) {
                $files = $request->file('photo');
                foreach ($files as $file) {
                    $fileName= $file->getClientOriginalName();
                    $path = $file->storeAs('Products', $fileName);
                    $img =  new Photo();
                    $img->path = $path;
                    $img->product_id = $Product->id;
                    $img->save();
                }
            }
        }

        return response()->json([
            'state'=> true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        if ($product) {
            return response()->json([
                'state' =>true
            ]);
        }else{
            return response()->json([
                'state' =>false
            ]);
        }
    }
}
