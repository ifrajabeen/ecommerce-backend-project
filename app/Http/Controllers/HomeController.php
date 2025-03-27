<?php

namespace App\Http\Controllers;

use App\Models\Catgeory;
use App\Models\Product;
use App\Models\Contact;
use Illuminate\Http\Request;
// use App\Http\Controllers;
class HomeController extends Controller
{
  

 
    public function index()
    {
        $categories = Catgeory::orderBy("name")->get();
        $sproducts = Product::whereNotNull("sale_price")->where('sale_price','<>','')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where("featured",1)->get()->take(8);
        return view('index',compact('categories','sproducts','fproducts'));
    }

    public function contact(){
        return  view('contact');
    }
    public function contact_store(Request $request){
        $request->validate([
            'name'=> 'required|max:100',
            'email'=> 'required',
            'phone'=> 'required|numeric|digits:11',
            'comment'=> 'required',
        ]);
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success','Your message has been sent successfully');
    }
    //Searching
    public function search(Request $request)
{
    $query = $request->input('query');
    $results = Product::where('name', 'LIKE', '%' . $query . '%')->get();

    return response()->json($results);
}
}
