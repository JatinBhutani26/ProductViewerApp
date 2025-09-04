<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = null;
        $recent = collect();

        if (Auth::check()) {
            $stats = [
                'products_total' => Product::count(),
                'db_name'        => DB::getDatabaseName(),
            ];
            $recent = Product::orderByDesc('id')->take(5)->get();
        }

        return view('dashboard', compact('stats', 'recent'));
    }
}
