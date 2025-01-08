<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $product = new Product();

        // Gelen verileri alalım
        $product->name = $request->name;
        $product->description = $request->description;

        // Resim işleme
        if ($request->image != "") {
            try {
                // Base64 verisini çözümle ve uzantısını çıkar
                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $ex = explode('/', $sub)[1]; // 'jpeg', 'png' vs.

                // Resmin ismini oluştur
                $name = time() . "." . $ex;

                // Base64 encoded resimi işleyip boyutlandırma
                $img = Image::make($request->image)->resize(200, 200);

                // Yükleme klasörünün yolunu belirleyelim
                $upload_path = public_path() . "/upload/";

                // Yükleme klasörü yoksa oluştur
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true); // Klasörü oluşturma
                }

                // Resmi kaydedelim
                $img->save($upload_path . $name);

                // Ürün modeline resmi kaydedelim
                $product->image = $name;
            } catch (\Exception $e) {
                // Resim işleme hatalarını loglayalım
                \Log::error("Resim işleme hatası: " . $e->getMessage());
                return response()->json(['error' => 'Resim işleme hatası'], 500);
            }
        } else {
            // Resim yoksa varsayılan bir resim atayalım
            $product->image = "no-image.jpg";
        }

        // Diğer verileri modelimize ekleyelim
        $product->type = $request->type;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        try {
            // Ürünü kaydedelim
            $product->save();
            return response()->json(['message' => 'Ürün başarıyla kaydedildi', 'product' => $product], 201);
        } catch (\Exception $e) {
            // Veritabanı hatalarını loglayalım
            \Log::error("Veritabanı hatası: " . $e->getMessage());
            return response()->json(['error' => 'Ürün kaydedilemedi'], 500);
        }
    }
}
