<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;



class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }


public function store(Request $request)
{
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Verificar se existe uma categoria soft deleted com o mesmo nome
        $existing = Category::withTrashed()->where('name', $request->name)->first();

        if ($existing) {
            // Restaurar a categoria eliminada
            $existing->restore();

            // Se houver uma imagem nova, guardar
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('categories', $imageName, 'public');
                $existing->image = $imageName;
            }

            $existing->save();

            return redirect()->route('categories.index')->with('success', 'Category restored and updated successfully.');
        }

        // Criar nova categoria (sem duplicados na base de dados)
        $category = new Category();
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('categories', $imageName, 'public');
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }



    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category->name = $request->name;

        if ($request->hasFile('image')) {
            // Gerar um nome único
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();

            // Guardar a imagem no storage/app/public/categories
            $path = $request->file('image')->storeAs('categories', $imageName, 'public');

            if ($path) {
                $category->image = $imageName;
            } else {
                return back()->with('error', 'Failed to upload the image.');
            }
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }




    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            $category->delete(); // soft delete
        } else {
            $category->forceDelete();
        }
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

}



