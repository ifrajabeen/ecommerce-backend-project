<?php
use App\Http\Controllers\HomeController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;




Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
//shop route
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/productdetails/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
//cart route
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/increase/{id}', [CartController::class, 'increaseItemQuantity'])->name('cart.increase');
Route::get('/cart/decrease/{id}', [CartController::class, 'reduceItemQuantity'])->name('cart.decrease');
Route::post('/cart/update-quantity', [CartController::class, 'updateItemQuantity'])->name('cart.updateQuantity');

Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
//wishlist route
Route::get('/all/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::post('/wishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::post('/wishlist/remove/{id}',[WishlistController::class,'remove_from_wishlist'])->name('wishlist.remove');
Route::post('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::post('/wishlist/move-to-cart/{id}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move_to_cart');

//apply coupon
Route::post('/cart/apply-coupon',[CartController::class,'apply_coupon_code'])->name('cart.coupon.apply');
Route::get('/cart/remove-coupon',[CartController::class,'remove_coupon_code'])->name('cart.coupon.remove');

//checkout
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-order',[CartController::class,'place_order'])->name('cart.place.order');
Route::get('/order-confirmation',[CartController::class,'confirmation'])->name('cart.confirmation');

//contact route
Route::get('/contact-us',[HomeController::class,'contact'])->name('home.contact');
Route::post('/contant/store',[HomeController::class,'contact_store'])->name('home.contact.store');

Route::get('/search', [HomeController::class, 'search'])->name('home.search');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'account_orders'])->name('user.account.orders');
    Route::get('/account-order-details/{order_id}', [UserController::class, 'account_orders_details'])->name('user.account.order.details');
    Route::put('/account-order/cancel-order',[UserController::class,'account_cancel_order'])->name('user.account_cancel_order');
});

Route::middleware(['auth',AuthAdmin::class])->group(function() {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    //Brand Routes
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/createbrands', [AdminController::class, 'create_brand'])->name('admin.createbrand');
    Route::post('/admin/brand/storebrands', [AdminController::class, 'brand_store'])->name('admin.storebrand');
    Route::get('/admin/brand/editbrands/{id}', [AdminController::class, 'brand_edit'])->name('admin.brandedit');
    Route::post('/admin/brand/updatebrands', [AdminController::class, 'brand_update'])->name('admin.updatebrand');
    Route::delete('/admin/brand/deletebrands/{id}', [AdminController::class, 'delete'])->name('admin.deletebrand');

    // Category Routes
    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/createcategory',[AdminController::class, 'create_category'])->name('admin.createcategory');
    Route::post('/admin/storecategory',[AdminController::class, 'category_store'])->name('admin.storecategory');
    Route::get('/admin/category/editcategory/{id}', [AdminController::class, 'category_edit'])->name('admin.categoryedit');
    Route::post('/admin/category/updatecategory', [AdminController::class, 'category_update'])->name('admin.updatecategory');
    Route::delete('/admin/category/daletecategory/{id}', [AdminController::class, 'category_delete'])->name('admin.categorydelete');

    //products route
    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/createproduct',[AdminController::class, 'create_product'])->name('admin.createproduct');
    Route::post('/admin/storeproduct',[AdminController::class, 'product_store'])->name('admin.productstore');
    Route::get('/admin/product/editproduct/{id}', [AdminController::class, 'edit_product'])->name('admin.productedit');
    Route::post('/admin/updateproduct', [AdminController::class, 'update'])->name('admin.updateproduct');
    Route::delete('admin/productdelete/{id}',[AdminController::class, 'deleteproduct'])->name('admin.productdelete');

//Coupons Route

    Route::get('admin/coupons',[AdminController::class,'coupons'])->name('admin.coupons');
    Route::get('admin.coupon/add',[AdminController::class,'coupon_add'])->name('admin.coupon.add');
    Route::post('admin.coupon/store',[AdminController::class,'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
    Route::post('/admin/updatecoupon', [AdminController::class, 'coupon_update'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/delete/{id}', [AdminController::class,'coupon_delete'])->name('admin.coupon.delete');

    //orders routes
    Route::get('/admin/orders',[AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/items/{order_id}',[AdminController::class,'order_items'])->name('admin.order.items');
    Route::put('admin/order/statusupdate', [AdminController::class,'order_status_update'])->name('admin.order.status.update');


    Route::get('/admin/contact',[AdminController::class,'contacts'])->name('admin.contacts');
    Route::delete('/admin/delete/contact/{id}', [AdminController::class,'contact_delete'])->name('admin.contact.delete');
  

});
