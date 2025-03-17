<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function beautyHealth()
    {
        return view('products.beauty-health');
    }

    public function homeCare()
    {
        return view('products.home-care');
    }

    public function babyKid()
    {
        return view('products.baby-kid');
    }

    // public function showCategory($slug)
    // {
    //     // Pastikan kategori ditemukan berdasarkan slug
    //     $category = Category::where('slug', $slug)->first();

    //     if (!$category) {
    //         abort(404, 'Kategori tidak ditemukan');
    //     }

    //     // Ambil produk berdasarkan category_id
    //     $products = Product::where('category_id', $category->id)->get();

    //     return view('categories.show', compact('category', 'products'));
    // }

    public function foodBeverage()
    {
        $category = Category::where('name', 'Food & Beverage')->first();
        $products = $category ? $category->products : collect(); // Jika tidak ada kategori, gunakan koleksi kosong
    
        return view('products.food-beverage', compact('category', 'products')); 
    }
    
}
