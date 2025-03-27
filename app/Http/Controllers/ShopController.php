<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Catgeory;

class ShopController extends Controller
{
   public function index(Request $request)
   {
       $size = $request->query('size') ?? 12;
       $or_column = "id";
       $or_order = "desc";
       $order = $request->query('order') ?? -1;
       $f_brands = $request->query('brands');
   
       // Determine sorting column and direction
       switch ($order) {
           case 1:
               $or_column = "created_at";
               $or_order = "desc";
               break;
           case 2:
               $or_column = "created_at";
               $or_order = "asc";
               break;
           case 3:
               $or_column = "regular_price";
               $or_order = "asc";
               break;
           case 4:
               $or_column = "regular_price";
               $or_order = "desc";
               break;
           default:
               $or_column = "id";
               $or_order = "desc";
       }
   

       $brands = Brand::orderBy("name", "ASC")->get();
       $categories = Catgeory::orderBy("name", "ASC")->get();
       $products = Product::when($f_brands, function ($query, $f_brands) {
           return $query->whereIn('brand_id', explode(',', $f_brands));
       })->orderBy($or_column, $or_order)->paginate($size);

       return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands','categories'));
   }
   public function product_details($product_slug)
   {
      $product = Product::where('slug', $product_slug)->first();
      $rproduct = Product::where('slug', '<>', $product_slug)->limit(8)->get();
      return view('details', compact('product', 'rproduct'));
   }
}
