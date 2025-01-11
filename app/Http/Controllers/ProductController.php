<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|string|max:50',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable'
            ]);

            // Resim işleme
            if ($request->has('image') && !empty($request->image)) {
                if (strpos($request->image, 'base64') !== false) {
                    $image = $request->image;
                    $image_parts = explode(";base64,", $image);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    
                    $fileName = uniqid() . '.' . $image_type;
                    $uploadPath = 'upload/' . $fileName;
                    
                    file_put_contents($uploadPath, $image_base64);
                    
                    $validated['image'] = $fileName;
                }
            }

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla eklendi',
                'data' => $product
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Ürün ekleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|string|max:50',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable'
            ]);

            $product = Product::findOrFail($id);

            // Resim işleme
            if ($request->has('image') && !empty($request->image)) {
                if (strpos($request->image, 'base64') !== false) {
                    $image = $request->image;
                    $image_parts = explode(";base64,", $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = uniqid() . '.png';
                    
                    // Eski resmi sil
                    if ($product->image && file_exists('upload/' . $product->image)) {
                        unlink('upload/' . $product->image);
                    }
                    
                    // Yeni resmi kaydet
                    file_put_contents('upload/' . $fileName, $image_base64);
                    $validated['image'] = $fileName;
                }
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla güncellendi',
                'data' => $product
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
