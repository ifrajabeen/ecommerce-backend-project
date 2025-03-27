@extends('layouts.app')

@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Wishlist</h2>             
        <div class="shopping-cart">
            @if(count($wishlistItems) > 0)
            <div class="cart-table__wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>                           
                            <th>Action</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wishlistItems as $id => $wishlistItem)
                        <tr>
                            <td>
                                <div class="shopping-cart__product-item">
                                    <img loading="lazy" src="{{ asset('uploads/productimage/' . $wishlistItem['image']) }}" width="120" height="120" alt="" />
                                </div>
                            </td>
                            <td>
                                <div class="shopping-cart__product-item__detail">
                                    <h4>{{ $wishlistItem['name'] }}</h4>
                                </div>
                            </td>
                            <td>
                                <span class="shopping-cart__product-price">${{ $wishlistItem['price'] }}</span>
                            </td>      
                            <td>
                                <div class="del-action">
                                    <form method="POST" action="{{ route('wishlist.move_to_cart', ['id' => $id]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Move to Cart</button>
                                    </form>
                                    <form method="POST" action="{{ route('wishlist.remove', ['id' => $id]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </div>                               
                            </td>
                        </tr>   
                        @endforeach
                    </tbody>
                </table>      
                <div class="cart-table-footer">                    
                    <form method="POST" action="{{ route('wishlist.clear') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light">CLEAR WISHLIST</button>
                    </form>
                </div>          
            </div>   
            @else
                <div class="row">
                    <div class="col-md-12">
                        <p>No item found in your wishlist</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-info">Wishlist Now</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>

@endsection