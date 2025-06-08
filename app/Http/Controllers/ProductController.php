<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category')) {
            if ($request->category === 'none') {
                // Produtos cuja categoria foi apagada (soft deleted)
                $query->whereHas('category', function ($q) {
                    $q->onlyTrashed(); // Só categorias soft deleted
                });
            } else {
                $query->where('category_id', $request->category);
            }
        }


        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->has('on_sale')) {
            $query->whereNotNull('discount')->whereNotNull('discount_min_qty');
        }

        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate(20)->appends($request->query());
        $categories = Category::all();

        return view('edit_catalog.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $product = null;
        return view('edit_catalog.create', compact('categories', 'product'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->whereNull('deleted_at') // ignora os soft-deleted
            ],
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


        // Verificar se há produto apagado com o mesmo nome
        $trashedProduct = Product::onlyTrashed()->where('name', $request->name)->first();

        if ($trashedProduct) {
            // Restaurar e atualizar os dados
            $trashedProduct->restore();
            $trashedProduct->update([
                'category_id' => $request->category_id,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
                'discount_min_qty' => $request->discount_min_qty,
                'discount' => $request->discount,
                'stock_lower_limit' => $request->stock_lower_limit,
                'stock_upper_limit' => $request->stock_upper_limit,
            ]);

            if ($request->hasFile('photo')) {
                $imageName = time() . '_' . $request->file('photo')->getClientOriginalName();
                $request->file('photo')->storeAs('products', $imageName, 'public');
                $trashedProduct->photo = $imageName;
                $trashedProduct->save();
            }

            return redirect()->route('products.index')->with('success', 'Soft deleted product restored and updated.');
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

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
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


