<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')->paginate(10);
        return view('products', compact('products')); // resources/views/products.blade.php
    }

    public function create()
    {
        return view('products-create'); // resources/views/products-create.blade.php
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'price'       => ['required','numeric','min:0'],
            'description' => ['nullable','string','max:2000'],
        ]);

        DB::transaction(fn () => Product::create($data));

        return redirect()->route('products.index')->with('status', 'Product created successfully!');
    }
}
