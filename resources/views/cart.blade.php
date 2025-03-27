@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Cart</h2>
      <div class="checkout-steps">
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Shipping and Checkout</span>
            <em>Checkout Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div>
      @if (session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif
  
  @if (session('error'))
      <div class="alert alert-danger">
          {{ session('error') }}
      </div>
  @endif
   
  @if (session('status'))
      <div class="alert alert-success">
          {{ session('status') }}
      </div>
  @endif
      <div class="shopping-cart">
        @if(session()->has('cart') && count(session('cart')) > 0)
        <div class="cart-table__wrapper">
            <table class="cart-table w-100" style="table-layout: auto;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th></th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('cart') as $id => $item)
                    <tr class="align-middle text-center">
                        <td class="px-3 py-2">
                            <div class="shopping-cart__product-item">
                                <img loading="lazy" src="{{ asset('uploads/productimage/' . $item['image']) }}" 
                                     width="100" height="100" class="rounded" alt="Product Image" />
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="shopping-cart__product-item__detail">
                                <h5 class="mb-0">{{ $item['name'] }}</h5>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <span class="shopping-cart__product-price fw-bold">
                                ${{ number_format($item['price'], 2) }}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <div class="qty-control d-flex align-items-center justify-content-center">
                                <a href="{{ route('cart.decrease', $id) }}" 
                                   class="btn btn-sm btn-outline-secondary px-2 py-1">−</a>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                                       class="qty-control__number text-center mx-2 update-quantity" 
                                       data-id="{{ $id }}" style="width: 50px; height: 30px;">
                                <a href="{{ route('cart.increase', $id) }}" 
                                   class="btn btn-sm btn-outline-secondary px-2 py-1">+</a>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <span class="shopping-cart__subtotal fw-bold">
                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('cart.remove', $id) }}" class="btn btn-sm btn-danger">
                                Remove
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="cart-table-footer">
               
                <form action="{{ route('cart.coupon.apply') }}" class="position-relative bg-body" method="POST">
                    @csrf
                    <input class="form-control" 
                           type="text" 
                           name="coupon_code" 
                           placeholder="Coupon Code" 
                           value="{{ Session::has('coupon') ? Session::get('coupon')['code'] : '' }}">
                    <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" 
                           type="submit"
                           value="{{ Session::has('coupon') ? 'UPDATE COUPON' : 'APPLY COUPON' }}">
                </form>
                @if(Session::has('coupon'))
                     <a href="{{ route('cart.coupon.remove') }}" class="btn btn-danger mt-2">Remove Coupon</a>
                @endif

                
                <a href="{{ route('cart.clear') }}" class="btn btn-light">Clear CART</a>
                <a href="{{ route('shop.index') }}" class="btn btn-light">Countiune shopping</a>
              </div>
        </div>
    
        @if(session()->has('cart') && count(session('cart')) > 0)
        @php
            $vatRate = 0.10; 
            $subtotal = collect(session('cart'))->sum(fn($item) => $item['price'] * $item['quantity']);
            $vat = $subtotal * $vatRate;
            $total = $subtotal + $vat;
        @endphp
    @else
        @php
            $subtotal = 0;
            $vat = 0;
            $total = 0;
        @endphp
    @endif
    <div class="shopping-cart__totals-wrapper">
        <div class="sticky-content">
            <div class="shopping-cart__totals">
                <h3>Cart Totals</h3>
                @php
                    $cart = session()->get('cart', []); 
                    $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']); 
                    $vatRate = config('cart.tax', 10); 
                    $vat = ($subtotal * $vatRate) / 100;
                    $total = $subtotal + $vat;
                @endphp
                
                @if(Session::has('discounts'))
                    <table class="cart-totals">
                        <tbody>
                            <tr>
                                <th>Subtotal</th>
                                <td>${{ number_format($subtotal, 2) }}</td>
                            </tr> 
                            <tr>
                                <th>Discount ({{ Session::get('coupon')['code'] }})</th>
                                <td>- ${{ Session::get('discounts')['discount'] }}</td>
                            </tr> 
                            <tr>
                                <th>Subtotal After Discount</th>
                                <td>${{ Session::get('discounts')['subtotal'] }}</td>
                            </tr>    
                            <tr>
                                <th>SHIPPING</th>
                                <td class="text-right">Free</td>
                            </tr>                            
                            <tr>
                                <th>VAT</th>
                                <td>${{ Session::get('discounts')['tax'] }}</td>
                            </tr>
                            <tr class="cart-total">
                                <th>Total</th>
                                <td>${{ Session::get('discounts')['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="cart-totals">
                        <tbody>
                            <tr>
                                <th>Subtotal</th>
                                <td>${{ number_format($subtotal, 2) }}</td>
                            </tr>   
                            <tr>
                                <th>SHIPPING</th>
                                <td class="text-right">Free</td>
                            </tr>                             
                            <tr>
                                <th>VAT</th>
                                <td>${{ number_format($vat, 2) }}</td>
                            </tr>
                            <tr class="cart-total">
                                <th>Total</th>
                                <td>${{ number_format($total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="mobile_fixed-btn_wrapper">
                <div class="button-wrapper container">
                    <a href="{{route('cart.checkout')}}" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
                </div>
            </div>
        </div>
    </div>
    
            
    @else
        <div class="row">
            <div class="col-md-12 text-center pt-5 bp-5">
                <p>No item found in your cart</p>
                <a href="{{ route('shop.index') }}" class="btn btn-info">Shop Now</a>
            </div>
        </div>
    @endif
    
      </div>
    </section>
  </main>
@endsection
@push('scripts')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.update-quantity').forEach(input => {
            input.addEventListener('change', function() {
                let productId = this.getAttribute('data-id');
                let quantity = this.value;
    
                fetch("{{ route('cart.updateQuantity') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id: productId, quantity: quantity })
                }).then(response => response.json()).then(data => {
                    window.location.reload(); // Reload page to reflect changes
                }).catch(error => console.log(error));
            });
        });
    });
    </script>
     
@endpush
    