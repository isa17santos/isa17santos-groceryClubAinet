<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withTrashed()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->only('name', 'image'));
        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                // Apagar a imagem anterior
                Storage::disk('public')->delete('categories/' . $category->image);
            }

            // Guardar nova imagem
            $validated['image'] = basename($request->file('image')->store('categories', 'public'));
        }

        $category->update($validated);

        return redirect()
            ->route('categories.edit', $category)
            ->with('success', 'Categoria atualizada com sucesso.');
    }


    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            $category->delete(); // soft delete
        } else {
            $category->forceDelete();
        }
        return redirect()->route('categories.index')->with('success', 'Categoria eliminada com sucesso.');
    }

}
