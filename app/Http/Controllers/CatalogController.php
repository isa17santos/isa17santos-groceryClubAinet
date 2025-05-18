<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get(); 
        return view('catalog.index', compact('products'));
    }
}
