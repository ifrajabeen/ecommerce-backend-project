<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Catgeory;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("created_at", "desc")->paginate(10);
        $dashboarddates = DB::select("SELECT 
    SUM(total) AS TotalAmount,
    SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
    SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
    SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount,
    COUNT(*) AS Total,
    SUM(IF(status = 'ordered', 1, 0)) AS TotalOrdered,
    SUM(IF(status = 'delivered', 1, 0)) AS TotalDelivered,
    SUM(IF(status = 'canceled', 1, 0)) AS TotalCanceled
FROM Orders;

         ");
        $monthlydates = DB::select("SELECT 
    M.id AS MonthNo, 
    M.name AS MonthName,
    IFNULL(D.TotalAmount, 0) AS TotalAmount,
    IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
    IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
    IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
FROM month_names M
LEFT JOIN (
    SELECT 
        DATE_FORMAT(created_at, '%b') AS MonthName,
        MONTH(created_at) AS MonthNo,
        SUM(total) AS TotalAmount,
        SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
        SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
        SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
    FROM Orders 
    WHERE YEAR(created_at) = YEAR(NOW()) 
    GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
    ORDER BY MONTH(created_at)
) D ON D.MonthNo = M.id;
");
        $AmountM = implode(',', collect($monthlydates)->pluck('TotalAmount')->toArray());
        $OrderedAmountM = implode(',', collect($monthlydates)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthlydates)->pluck('TotaldeliveredAmount')->toArray());
        $CanceledAmountM = implode(',', collect($monthlydates)->pluck('TotalcenceledAmount')->toArray());

        $TotalAmount = collect($monthlydates)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlydates)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlydates)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlydates)->sum('TotalCanceledAmount');
        return view('admin.index', compact('orders', 'dashboarddates', 'monthlydates','AmountM','OrderedAmountM','DeliveredAmountM','CanceledAmountM','TotalAmount','TotalOrderedAmount','TotalDeliveredAmount','TotalCanceledAmount'));
    }
    //brand function
    public function brands()
    {
        $brands = Brand::orderBy('id', 'asc')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function create_brand()
    {
        return view('admin.createbrand');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;

        $destination = public_path('uploads/brands');
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }
        $image->move($destination, $file_name);

        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return redirect()->route('admin.brands')->with('error', 'Brand not found!');
        }
        return view('admin.brandedit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = Brand::find($request->id);
        if (!$brand) {
            return redirect()->route('admin.brands')->with('error', 'Brand not found!');
        }

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $oldImage = public_path('uploads/brands/' . $brand->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = uniqid() . '.' . $file_extention;
            $destination = public_path('uploads/brands');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $image->move($destination, $file_name);

            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }
    public function delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete((public_path('uploads/brands') . '/' . $brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been Deleted successfully!');
    }

    //category function

    public function categories()
    {
        $Categories = Catgeory::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('Categories'));
    }

    public function create_category()
    {
        return view('admin.createcategory');
    }
    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category = new Catgeory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = time() . '.' . $file_extention;
            $destination = public_path('uploads/categoryimage');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image->move($destination, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been created successfully!');
    }
    public function category_edit($id)
    {
        $editcategory = Catgeory::find($id);
        if (!$editcategory) {
            return redirect()->route('admin.categoryedit')->with('error', 'Category not found!');
        }
        return view('admin.categoryedit', compact('editcategory'));
    }
    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $updatecategory = Catgeory::find($request->id);
        if (!$updatecategory) {
            return redirect()->route('admin.categories')->with('error', 'Category not found!');
        }

        $updatecategory->name = $request->name;
        $updatecategory->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/categoryimage/' . $updatecategory->image);
            if (File::exists($oldImagePath) && !empty($updatecategory->image)) {
                File::delete($oldImagePath);
            }
            $image = $request->file('image');
            $file_name = uniqid() . '.' . $image->extension();
            $destination = public_path('uploads/categoryimage');


            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }


            $image->move($destination, $file_name);
            $updatecategory->image = $file_name;
        }

        $updatecategory->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }
    public function category_delete($id)
    {
        $deletecategory = Catgeory::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $deletecategory->image)) {
            File::delete((public_path('uploads/brands') . '/' . $deletecategory->image));
        }
        $deletecategory->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been Deleted successfully!');
    }

    //product functions

    public function products()
    {
        $products = Product::orderBy('created_at')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function create_product()
    {
        $categories = Catgeory::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.createproduct', compact('categories', 'brands'));
    }


    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'SKU' => 'required|unique:products,SKU',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/productimage'), $imageName);
            $product->image = $imageName;
        }

        $gallery_images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $gImageName = $current_timestamp . '-' . ($index + 1) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/productimage/thumbnails'), $gImageName);
                $gallery_images[] = $gImageName;
            }
        }
        $product->images = implode(',', $gallery_images);

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product added successfully!');
    }

    public function edit_product($id)
    {
        $product = Product::find($id);
        $categories = Catgeory::select('id', 'name')->orderBY('name')->get();
        $brands = Brand::select('id', 'name')->orderBY('name')->get();
        return view('admin.productedit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request)
    {


        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,',
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'SKU' => 'required|unique:products,SKU,',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $product = Product::find($request->id);

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;


        if ($request->hasFile('image')) {

            if ($product->image && file_exists(public_path('uploads/productimage/' . $product->image))) {
                unlink(public_path('uploads/productimage/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/productimage'), $imageName);
            $product->image = $imageName;
        }


        if ($request->hasFile('images')) {

            if ($product->images) {
                foreach (explode(',', $product->images) as $oldImage) {
                    if (file_exists(public_path('uploads/productimage/thumbnails/' . $oldImage))) {
                        unlink(public_path('uploads/productimage/thumbnails/' . $oldImage));
                    }
                }
            }

            $gallery_images = [];
            foreach ($request->file('images') as $index => $file) {
                $gImageName = $current_timestamp . '-' . ($index + 1) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/productimage/thumbnails'), $gImageName);
                $gallery_images[] = $gImageName;
            }
            $product->images = implode(',', $gallery_images);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product updated successfully!');
    }

    public function deleteproduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product deleted successfully!');
    }

    //Coupon function
    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }
    public function coupon_add()
    {

        return view('admin.couponadd');
    }
    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupons = new Coupon();
        $coupons->code = $request->code;
        $coupons->type = $request->type;
        $coupons->value = $request->value;
        $coupons->cart_value = $request->cart_value;
        $coupons->expiry_date = $request->expiry_date;
        $coupons->save();
        return redirect()->route('admin.coupons')->with('status', 'Record has been addded successfully');
    }
    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon_edit', compact('coupon'));
    }
    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupons = Coupon::find($request->id);
        $coupons->code = $request->code;
        $coupons->type = $request->type;
        $coupons->value = $request->value;
        $coupons->cart_value = $request->cart_value;
        $coupons->expiry_date = $request->expiry_date;
        $coupons->save();
        return redirect()->route('admin.coupons')->with('status', 'Record has been updated successfully');
    }
    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'coupon has been deleted successfully');
    }

    // Order function
    public function orders()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.orders', compact('orders'));
    }
    public function order_items($order_id)
    {
        $order = Order::find($order_id);
        $orderitems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view("admin.order-details", compact('order', 'orderitems', 'transaction'));
    }
    public function order_status_update(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }
        $order->save();
        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }
        return back()->with("status", "Status Changed Successfully!");
    }
    // Contact Functions
    public function contacts(){
        $contacts = Contact::orderBy("created_at","DESC")->paginate(10);
        return view("admin.contacts", compact("contacts"));
    }
    public function contact_delete($id){
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with('status','Contact Deleted Successfully');
    }

    //Search 
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', '%' . $query . '%')->get();
    
        return response()->json($results);
    }

}
