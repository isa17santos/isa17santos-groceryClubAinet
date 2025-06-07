<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->paginate(20);
        return view('edit_catalog.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('edit_catalog.create', compact('categories'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'discount_min_qty' => 'nullable|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'stock_lower_limit' => 'required|integer|min:0',
            'stock_upper_limit' => 'required|integer|min:0',
        ]);

        dd($request->all());


        // Verificar se existe um produto com o mesmo nome (mesmo soft deleted)
        $existing = Product::withTrashed()->where('name', $request->name)->first();

        if ($existing) {
            // Se já existir, restaura (caso soft deleted) e atualiza
            $existing->restore();
            $existing->category_id = $request->category_id;
            $existing->price = $request->price;
            $existing->stock = $request->stock;
            $existing->description = $request->description;
            $existing->discount_min_qty = $request->discount_min_qty;
            $existing->discount = $request->discount;
            $existing->stock_lower_limit = $request->stock_lower_limit;
            $existing->stock_upper_limit = $request->stock_upper_limit;

            if ($request->hasFile('photo')) {
                $imageName = time() . '_' . $request->file('photo')->getClientOriginalName();
                $request->file('photo')->storeAs('products', $imageName, 'public');
                $existing->photo = $imageName;
            }

            $existing->save();
            return redirect()->route('products.index')->with('success', 'Product restored and updated successfully.');
        }

        // Se não existir, criar um novo produto normalmente
        $product = new Product();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->discount_min_qty = $request->discount_min_qty;
        $product->discount = $request->discount;
        $product->stock_lower_limit = $request->stock_lower_limit;
        $product->stock_upper_limit = $request->stock_upper_limit;

        if ($request->hasFile('photo')) {
            $imageName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->storeAs('products', $imageName, 'public');
            $product->photo = $imageName;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }



    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('edit_catalog.edit', compact('product', 'categories'));
    }

   

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'discount_min_qty' => 'nullable|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'stock_lower_limit' => 'required|integer|min:0',
            'stock_upper_limit' => 'required|integer|min:0',
        ]);

        // Regra personalizada: stock >= stock_lower_limit
        $validator->after(function ($validator) use ($request) {
            if ((int)$request->stock < (int)$request->stock_lower_limit) {
                $validator->errors()->add('stock', 'O stock atual não pode ser inferior ao stock mínimo.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza campos
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->discount_min_qty = $request->discount_min_qty;
        $product->discount = $request->discount;
        $product->stock_lower_limit = $request->stock_lower_limit;
        $product->stock_upper_limit = $request->stock_upper_limit;

        if ($request->hasFile('photo')) {
            $imageName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->storeAs('products', $imageName, 'public');
            $product->photo = $imageName;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }


    public function destroy(Product $product)
    {
        if ($product->orders()->exists()) {
            $product->delete();
        } else {
            $product->forceDelete();
        }
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}


