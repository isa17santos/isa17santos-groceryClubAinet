<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(20); 
        return view('catalog.index', compact('products'));
    }
}
