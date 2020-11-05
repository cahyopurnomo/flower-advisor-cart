<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function shop(){
    	$products = Product::all();
    	// dd($products);
    	return view('shop')->withTitle('FLOWER ADVISOR | SHOP')->with(['products' => $products]);
    }

    public function cart(){
    	$cartCollection = \Cart::getContent();
		
    	$discount = 0;
    	$nominal = 0;
    	// dd($cartCollection);
    	// return view('cart')->withTitle('FLOWER ADVISOR | CART')->with(['cartCollection' => $cartCollection]);
    	return view('cart')->withTitle('FLOWER ADVISOR | CART')->with(['cartCollection' => $cartCollection, 'nominal' => $nominal, 'discount' => $discount]);
    }

    public function add(Request $request){
    	// dd($request->product_code);
    	\Cart::add(array(
    		'id'			=> $request->id,
    		'name'			=> $request->name,
    		'price'			=> $request->price,
    		'quantity'		=> $request->quantity,
    		'product_code'	=> $request->product_code,
    		'attributes'	=> array('image' => $request->img,'slug' => $request->slug)
    	));

    	return redirect()->route('cart.index')->with('success_msg', 'Item is Added to Cart');
    }

    public function remove(Request $request){
    	\Cart::remove($request->id);
    	return redirect()->route('cart.index')->with('success_msg', 'Item is removed');
    }

    public function update(Request $request){
    	\Cart::update($request->id, array('quantity' => array('relative' => false, 'value' => $request->quantity)) );
    	return redirect()->route('cart.index')->with('success_msg', 'Cart is Updated');
    }

    public function clear(){
    	\Cart::clear();
    	return redirect()->route('cart.index')->with('success_msg', 'Cart is cleared');
    }

    public function discount(Request $request){
    	$cartCollection = \Cart::getContent();
    	$subTotal = \Cart::getSubTotal();
    	$day = date('D', strtotime(date('Y-m-d')));
    	$discount = 0;
    	$nominal = 0;
    	$promo_code = $request->promo_code;

    	foreach($cartCollection as $item){
    		$product_code = $item->product_code;
    	}

    	if( $promo_code == 'FA111' ){
    		$nominal = 'percent';
    		$discount = 0.1;
    	}else if( $promo_code == 'FA222' && $product_code == 'FA4532' ){
			//if kode barang FA4532
			$nominal = 'fix';
    		$discount = 50000;
    	}else if( $promo_code == 'FA333' && $subTotal > 400000 ){
			// if total belanja > 400.000
			$nominal = 'percent';
    		$discount = 0.06;
    	}else if( $promo_code == 'FA444' && strtolower($day) == 'tue' ){
			// jika membeli hari selasa
			$nominal = 'percent';
    		$discount = 0.05;
    	}
		
    	// dd($discount);
    	// return redirect()->route('cart.index')->with(['success_msg', 'Cart is cleared', 'cartCollection' => $cartCollection, 'nominal' => $nominal, 'discount' => $discount]);
    	return view('cart')->withTitle('FLOWER ADVISOR | CART')->with(['success_msg', 'Cart is cleared','cartCollection' => $cartCollection, 'nominal' => $nominal, 'discount' => $discount]);
    }

}
