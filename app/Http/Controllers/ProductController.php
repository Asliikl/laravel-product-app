<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{

    public function index(){
        $products = Product::query();
        $products = $products->latest()->get();

        return response()->json([
            'products' => $products
        ], 200);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' =>'required',
            'description' =>'required'
        ]);
    
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
    
            if (!empty($request->image)) {
                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $ex = explode('/', $sub)[1];
                $name = time() . "." . $ex;
    
                $img = Image::make($request->image)->resize(200, 200);
                $upload_path = public_path() . "/upload/";
                $img->save($upload_path . $name);
                $product->image = $name;
            } else {
                $product->image = "no-image.jpg";
            }
    
            $product->type = $request->type;
            $product->quantity = $request->quantity;
            $product->price = $request->price;
            $product->save();
    
            return response()->json(['message' => 'Product created successfully!', 'product' => $product]);
    
    }
}
