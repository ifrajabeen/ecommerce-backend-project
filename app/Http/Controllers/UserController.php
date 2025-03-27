<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
   public function index(){
    return view('user.index');
   } 

   public function account_orders(){
      $orders =Order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
      return view('user.orders',compact('orders'));
   }
   public function account_orders_details($order_id){
      $orders = Order::where('user_id',Auth::user()->id)->find($order_id);
      $orderitems  = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(10);
      $transaction = Transaction::where('order_id',$order_id)->first();
      return view('user.order-details',compact('orders','orderitems','transaction'));
   }
   public function account_cancel_order(Request $request)
{
    $order = Order::find($request->order_id);
    $order->status = "canceled";
    $order->canceled_date = Carbon::now();
    $order->save();
    return back()->with("status", "Order has been cancelled successfully!");
}

}
