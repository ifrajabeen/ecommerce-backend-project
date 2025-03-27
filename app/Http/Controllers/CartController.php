<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Show cart items
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    // Add item to cart
    public function add(Request $request)
    {
        $cart = session()->get('cart', []);
        $product = Product::find($request->id);
    
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }
    
        $price = $product->sale_price ?: $product->regular_price;
    
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity ?? 1;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'image' => $product->image,
                'quantity' => $request->quantity ?? 1
            ];
        }
    
        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Item added to cart');
    }
    
    
    // Update cart item quantity
    public function increaseItemQuantity($id)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
            session()->put('cart', $cart);
        }
    
        return redirect()->back()->with('success', 'Item quantity increased');
    }
    
    public function reduceItemQuantity($id)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id]) && $cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity'] -= 1;
            session()->put('cart', $cart);
        } elseif (isset($cart[$id])) {
            unset($cart[$id]); 
            session()->put('cart', $cart);
        }
    
        return redirect()->back()->with('success', 'Item quantity reduced');
    }
    
    public function updateItemQuantity(Request $request)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
    
        return redirect()->back()->with('success', 'Cart updated successfully');
    }
    

    // Remove item from cart
    public function remove($id)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item removed from cart');
        }
    
        return redirect()->route('cart.index')->with('error', 'Item not found in cart');
    }
    // Clear entire cart
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('shop.index')->with('error', ' cart clear');
    }



    // implementation of coupon on cart

    public function apply_coupon_code(Request $request)
    {        
        $request->validate([
            'coupon_code' => 'required|string'
        ]);
    
        $coupon_code = $request->coupon_code;
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    
        if ($coupon_code) {
            $coupon = Coupon::where('code', $coupon_code)
                            ->where('expiry_date', '>=', Carbon::today())
                            ->where('cart_value', '<=', $subtotal)
                            ->first();
    
            if (!$coupon) {
                return back()->with('error', 'Invalid coupon code!');
            }
    
            session()->put('coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);
    
            $this->calculateDiscounts(); // Recalculate discounts with the applied coupon
    
            return back()->with('status', 'Coupon code has been applied!');
        }
    
        return back()->with('error', 'Invalid coupon code!');
    }
    
  public function calculateDiscounts()
{
    $discount = 0;
    $cart = session()->get('cart', []); // Get cart from session
    $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']); // Calculate subtotal

    if (session()->has('coupon')) {
        $coupon = session()->get('coupon');

        if ($coupon['type'] == 'fixed') {
            $discount = $coupon['value'];
        } else {
            $discount = ($subtotal * $coupon['value']) / 100;
        }

        $discount = min($discount, $subtotal); // Ensure discount does not exceed subtotal
    }

    $subtotalAfterDiscount = $subtotal - $discount;
    $taxRate = config('cart.tax', 10); // Default tax rate to 10% if not set
    $taxAfterDiscount = ($subtotalAfterDiscount * $taxRate) / 100;
    $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

    // Store values in session
    session()->put('discounts', [
        'discount' => number_format($discount, 2, '.', ''),
        'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
        'tax' => number_format($taxAfterDiscount, 2, '.', ''),
        'total' => number_format($totalAfterDiscount, 2, '.', '')
    ]);
}

public function remove_coupon_code()
{
    session()->forget('coupon');
    session()->forget('discounts');
    return back()->with('status','Coupon has been removed!');
}
 public function checkout(){ 
    if(!Auth::check()){
        return redirect()->route('login');
    }
    $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
    return view('checkout',compact('address'));
 }
 public function place_order(Request $request)
 {
     $user_id = Auth::user()->id;
     $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
 
     if (!$address) {
         $request->validate([
             'name' => 'required|max:100',
             'phone' => 'required|numeric|digits:11',
             'zip' => 'required|numeric|digits:6',
             'state' => 'required',
             'city' => 'required',
             'address' => 'required',
             'locality' => 'required',
             'landmark' => 'required'
         ]);
 
         $address = new Address();
         $address->user_id = $user_id;
         $address->name = $request->name;
         $address->phone = $request->phone;
         $address->state = $request->state;
         $address->zip = $request->zip;
         $address->country = 'pakistan';
         $address->city = $request->city;
         $address->address = $request->address;
         $address->locality = $request->locality;
         $address->landmark = $request->landmark;
         $address->isdefault = true;
         $address->save();
     }
 
     $this->setAmountForCheckout();
 
     $order = new Order();
     $order->user_id = $user_id;
     $order->subtotal = session()->get('checkout')['subtotal'];
     $order->discount = session()->get('checkout')['discount'];
     $order->tax = session()->get('checkout')['tax'];
     $order->total = session()->get('checkout')['total'];
     $order->name = $address->name;
     $order->phone = $address->phone;
     $order->locality = $address->locality;
     $order->address = $address->address;
     $order->city = $address->city;
     $order->state = $address->state;
     $order->country = $address->country;
     $order->landmark = $address->landmark;
     $order->zip = $address->zip;
     $order->payment_mode = $request->mode;
$order->save();
 
 
     $cart = session()->get('cart', []);
 
     foreach ($cart as $item) {
         $orderitem = new OrderItem();
         $orderitem->product_id = $item['id'];
         $orderitem->order_id = $order->id;
         $orderitem->price = $item['price'];
         $orderitem->quantity = $item['quantity'];
         $orderitem->save();
     }
     if ($request->has('mode')) {
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->order_id = $order->id;
        $transaction->mode = $request->mode;
        $transaction->status = "pending";
        $transaction->save();
    } else {
        return back()->with('error', 'Payment method is required.');
    }
    
     // Clear cart session
     session()->forget('cart');
     session()->forget('checkout');
     session()->forget('coupon');
     session()->forget('discounts');
     session()->put('order_id',$order->id);
     return redirect()->route('cart.confirmation');
 }
 
 public function setAmountForCheckout()
 {
     $cart = session()->get('cart', []);
     
     if (empty($cart)) {
         session()->forget('checkout');
         return;
     }
 
     $subtotal = 0;
     foreach ($cart as $item) {
         $subtotal += $item['price'] * $item['quantity'];
     }
 
     $discount = session()->get('discounts')['discount'] ?? 0;
     
     $tax = ($subtotal * 0.10);  
     $total = ($subtotal - $discount) + $tax;
 
     session()->put('checkout', [
         'discount' => $discount,
         'subtotal' => $subtotal,
         'tax' => $tax,
         'total' => $total
     ]);
 }
 
 public function confirmation()
{
    if(Session::has('order_id')){
        $order = Order::find(Session::get('order_id'));
        return view('order-confirmation',compact('order'));
    }
    return redirect()->route('cart.index');
}

 
}
