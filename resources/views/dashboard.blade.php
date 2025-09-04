<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 text-white">

        {{-- Guest view --}}
        @guest
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="mb-4">Welcome to ProductViewer. Please log in to continue.</p>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 border border-gray-500 text-white rounded hover:bg-gray-700">
                        Register
                    </a>
                </div>
            </div>
        @endguest

        {{-- Authenticated user view --}}
        @auth
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{ __("You're logged in!") }}
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-300">Total Products</div>
                    <div class="mt-2 text-3xl font-semibold">{{ \App\Models\Product::count() }}</div>
                </div>
                <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-300">Database</div>
                    <div class="mt-2 text-xl">{{ DB::getDatabaseName() }}</div>
                </div>
                <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-300">Quick Links</div>
                    <div class="mt-2 flex gap-2">
                        <a href="{{ route('products.index') }}"
                           class="px-3 py-1 border border-gray-500 rounded hover:bg-gray-700">View Products</a>
                        <a href="{{ route('products.create') }}"
                           class="px-3 py-1 border border-gray-500 rounded hover:bg-gray-700">New Product</a>
                    </div>
                </div>
            </div>

            {{-- Recent Products --}}
            <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Recent Products</h3>
                @php
                    $recent = \App\Models\Product::orderByDesc('id')->take(5)->get();
                @endphp

                @if ($recent->count())
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border border-gray-700 text-sm">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-right">Price</th>
                                    <th class="px-4 py-2 text-left">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent as $p)
                                    <tr class="border-t border-gray-700">
                                        <td class="px-4 py-2">{{ $p->id }}</td>
                                        <td class="px-4 py-2">{{ $p->name }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($p->price, 2) }}</td>
                                        <td class="px-4 py-2">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-300">
                        No products yet.
                        <a href="{{ route('products.create') }}" class="text-blue-400 hover:text-blue-300 underline">Create one</a>.
                    </p>
                @endif
            </div>
        @endauth

    </div>
</x-app-layout>
