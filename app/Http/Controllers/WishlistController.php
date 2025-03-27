<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = session('wishlist', []);
        return view('wishlist', compact('wishlistItems'));
    }
    public function add_to_wishlist(Request $request)
    {
        $product = [
            'id' => $request->input('id'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'image' => $request->input('image'),
        ];

        $wishlist = session('wishlist', []);

        if (!array_key_exists($product['id'], $wishlist)) {
            $wishlist[$product['id']] = $product;
            session(['wishlist' => $wishlist]);
        }

        return redirect()->back()->with('success', 'Product added to wishlist!');
    }
    public function remove_from_wishlist($id = null)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$id])) {
            unset($wishlist[$id]);
            session()->put('wishlist', $wishlist);
        }

        return redirect()->back()->with('success', 'Product removed from wishlist!');
    }
    public function clear()
    {
        session()->forget('wishlist');
        return redirect()->back()->with('success', 'Wishlist cleared!');
    }
    public function move_to_cart($id)
    {

        $wishlist = session('wishlist', []);
        if (isset($wishlist[$id])) {
            $item = $wishlist[$id];
            unset($wishlist[$id]);
            session(['wishlist' => $wishlist]);
            $cart = session('cart', []);
            $cart[$id] = [
                'id' => $id,
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'quantity' => 1,
            ];
            session(['cart' => $cart]);

            return redirect()->back()->with('success', 'Product moved to cart!');
        }

        return redirect()->back()->with('error', 'Product not found in wishlist!');
    }
}
