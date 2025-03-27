@php
         $cart = session('cart', []);
          $inCart = isset($cart[$product->id]);
@endphp
@extends('layouts.app')
@section('content')

<main class="pt-90">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
        <div class="row">
            <div class="col-lg-7">
                <div class="product-single__media" data-media-type="vertical-thumbnail">
                    <div class="product-single__image">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                              

                                <!-- Main Image -->
                                <div class="swiper-slide product-single__image-item">
                                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/productimage/' . $product->image) }}" width="674" height="674" alt="" />
                                    <a data-fancybox="gallery" href="{{ asset('uploads/productimage/' . $product->image) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <use href="#icon_zoom" />
                                        </svg>
                                    </a>
                                </div>

                                <!-- Additional Images -->
                                @foreach(explode(",", $product->images) as $gimg)
                                    <div class="swiper-slide product-single__image-item">
                                        <img loading="lazy" class="h-auto" src="{{ asset('uploads/productimage/thumbnails/' . trim($gimg)) }}" width="674" height="674" alt="" />
                                        <a data-fancybox="gallery" href="{{ asset('uploads/productimage/thumbnails/' . trim($gimg)) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_zoom" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Navigation Buttons -->
                            <div class="swiper-button-prev">
                                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_prev_sm" />
                                </svg>
                            </div>
                            <div class="swiper-button-next">
                                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_next_sm" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnails -->
                    <div class="product-single__thumbnail">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide product-single__image-item">
                                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/productimage/' . $product->image) }}" width="104" height="104" alt="" />
                                </div>
                                @foreach(explode(",", $product->images) as $gimg)
                                    <div class="swiper-slide product-single__image-item">
                                        <img loading="lazy" class="h-auto" src="{{ asset('uploads/productimage/thumbnails/' . trim($gimg)) }}" width="104" height="104" alt="" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details and Add to Cart Form -->
            <div class="col-lg-5">
                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
                    </div>
                    <div class="product-single__prev-next d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
                        <a href="#" class="text-uppercase fw-medium">
                            <svg width="10" height="10" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_prev_md" />
                            </svg>
                            <span class="menu-link menu-link_us-s">Prev</span>
                        </a>
                        <a href="#" class="text-uppercase fw-medium">
                            <span class="menu-link menu-link_us-s">Next</span>
                            <svg width="10" height="10" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_next_md" />
                            </svg>
                        </a>
                    </div>
                </div>
                <h1 class="product-single__name">{{ $product->name }}</h1>
                <div class="product-single__rating">
                    <div class="reviews-group d-flex">
                        <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                        </svg>
                        <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                        </svg>
                        <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                        </svg>
                        <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                        </svg>
                        <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                        </svg>
                    </div>
                    <span class="reviews-note text-lowercase text-secondary ms-1">8k+ reviews</span>
                </div>
                <div class="product-single__price">
                    <span class="current-price">
                        @if($product->sale_price)
                            <s>${{ $product->regular_price }}</s> ${{ $product->sale_price }}
                        @else
                            ${{ $product->regular_price }}
                        @endif
                    </span>
                </div>
                <div class="product-single__short-desc">
                    <p>{{ $product->short_description }}</p>
                </div>

                <!-- Add to Cart Form -->
                @if($inCart)
                <a href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Go to Cart</a>
            @else
                <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                  @csrf
                  <div class="product-single__addtocart">
                    <div class="qty-control position-relative">
                      <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
                      <div class="qty-control__reduce">-</div>
                      <div class="qty-control__increase">+</div>
                    </div>
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="image" value="{{ $product->image }}" />
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="quantity" value="{{ $product->quantity }}" />
                    <input type="hidden" name="price" value="{{ $product->sale_price ?: $product->regular_price }}">
                    <button type="submit" class="btn btn-primary btn-addtocart " data-aside="cartDrawer">Add to
                      Cart</button>
                  </div>
                </form>
                @endif
        
                <!-- Other Product Details -->
                <div class="product-single__addtolinks">

                    @if(session('wishlist') && array_key_exists($product->id, session('wishlist')))
    <!-- Remove from Wishlist Form -->
    <form method="POST" action="{{ route('wishlist.remove', ['id' => $product->id]) }}" class="d-inline">
        @csrf
        <button type="submit" class="menu-link menu-link_us-s add-to-wishlist bg-transparent border-0 p-0">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: red;">
                <use href="#icon_heart" />
            </svg>
            <span>Remove from Wishlist</span>
        </button>
    </form>
@else
    <!-- Add to Wishlist Form -->
    <form method="POST" action="{{ route('wishlist.add') }}" class="d-inline">
        @csrf
        <input type="hidden" name="id" value="{{ $product->id }}" />
        <input type="hidden" name="name" value="{{ $product->name }}" />
        <input type="hidden" name="price" value="{{ $product->sale_price ?: $product->regular_price }}" />
        <button type="submit" class="menu-link menu-link_us-s add-to-wishlist bg-transparent border-0 p-0">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: black;">
                <use href="#icon_heart" />
            </svg>
            <span>Add to Wishlist</span>
        </button>
    </form>
@endif
                    <share-button class="share-button">
                        <button class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
                            <svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_sharing" />
                            </svg>
                            <span>Share</span>
                        </button>
                        <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
                            <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
                            <div id="Article-share-template__main" class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                                <div class="field grow mr-4">
                                    <label class="field__label sr-only" for="url">Link</label>
                                    <input type="text" class="field__input w-full" id="url" value="https://example.com" placeholder="Link" onclick="this.select();" readonly="">
                                </div>
                                <button class="share-button__copy no-js-hidden">
                                    <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" viewBox="0 0 11 13">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z" fill="currentColor"></path>
                                    </svg>
                                    <span class="sr-only">Copy link</span>
                                </button>
                            </div>
                        </details>
                    </share-button>
                </div>
                <div class="product-single__meta-info">
                    <div class="meta-item">
                        <label>SKU:</label>
                        <span>{{ $product->SKU }}</span>
                    </div>
                    <div class="meta-item">
                        <label>Categories:</label>
                        <span>{{ $product->category->name }}</span>
                    </div>
                    <div class="meta-item">
                        <label>Tags:</label>
                        <span>N/A</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="products-carousel container">
      <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>

      <div id="related_products" class="position-relative">
        <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
          <div class="swiper-wrapper">
            @foreach($rproduct as $rproduct)
            <div class="swiper-slide product-card">
              <div class="pc__img-wrapper">
                <a href="{{route('shop.product.details',['product_slug'=>$rproduct->slug])}}">
                  <img loading="lazy" src="{{asset('uploads/productimage/' . $rproduct->image)}}" width="330" height="400"
                    alt="Cropped Faux leather Jacket" class="pc__img">

                       
                        @foreach(explode(",", $rproduct->images) as $gimg)
                  <img loading="lazy" src="{{asset('uploads/productimage/thumbnails/')}}/{{trim($gimg)}}" width="330" height="400"
                    alt="Cropped Faux leather Jacket" class="pc__img pc__img-second">
                    @endforeach
                </a>
            
            @if($inCart)
                <a href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Go to Cart</a>
            @else
            
                      <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                        @csrf
                        <div class="product-single__addtocart">
                            <div class="qty-control position-relative">
                                <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
                            </div>
                            <input type="hidden" name="id" value="{{ $rproduct->id }}">
                            <input type="hidden" name="name" value="{{ $rproduct->name }}">
                            <input type="hidden" name="price" value="{{ $rproduct->sale_price ?: $rproduct->regular_price }}">
                            <button
                            class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                            data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                        </div>
                    </form>
                    @endif
               
              </div>

              <div class="pc__info position-relative">
                <p class="pc__category">{{$rproduct->category->name}}</p>
                <h6 class="pc__title"><a href="{{route('shop.product.details',['product_slug'=>$rproduct->slug])}}">{{$rproduct->name}}</a></h6>
                <div class="product-card__price d-flex">
                  <span class="money price">@if($rproduct->sale_price) <s>${{$rproduct->regular_price}}</s>${{$rproduct->sale_price}}
                    @else
                    ${{$rproduct->regular_price}}
                    @endif</span>
                </div>

                <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                  title="Add To Wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                </button>
              </div>
            </div>
@endforeach
            
          </div><!-- /.swiper-wrapper -->
        </div><!-- /.swiper-container js-swiper-slider -->

        <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_prev_md" />
          </svg>
        </div><!-- /.products-carousel__prev -->
        <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_next_md" />
          </svg>
        </div><!-- /.products-carousel__next -->

        <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
        <!-- /.products-pagination -->
      </div><!-- /.position-relative -->

    </section><!-- /.products-carousel container -->
    
</main>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const swiper = new Swiper('.swiper-container', {
        loop: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});
@endpush