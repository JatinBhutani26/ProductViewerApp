<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="bg-green-50 text-green-800 border border-green-200 rounded p-3 mb-4">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-600">Total: {{ $products->total() }}</p>
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add Product
                </a>
            </div>

            @if ($products->count())
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-right">Price</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-left">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $p->id }}</td>
                                    <td class="px-4 py-2">{{ $p->name }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($p->price, 2) }}</td>
                                    <td class="px-4 py-2">
                                        {{ \Illuminate\Support\Str::limit($p->description, 80) }}
                                    </td>
                                    <td class="px-4 py-2">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-gray-600">No products yet. <a class="text-blue-600 underline" href="{{ route('products.create') }}">Create one</a>.</div>
            @endif
        </div>
    </div>
</x-app-layout>
