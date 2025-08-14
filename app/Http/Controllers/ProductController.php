<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
	public function show(Product $product)
	{
		$user = Auth::user();
		$user_sale = User::where('id', $product->created_by)->first();
		$product->load(['images', 'category']);
		$product_images = ProductImage::where('product_id', $product->id)->get();
		$auction = Auction::where('product_id', $product->id)->first();
		return view('products.product-details', compact('product', 'product_images', 'auction', 'user_sale'));
	}
}