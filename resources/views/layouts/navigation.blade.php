<nav class="bg-gray-800 border-b border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-white">
            ProductViewer
        </a>

        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-200 hover:text-white">Dashboard</a>
            @auth
                <a href="{{ route('products.index') }}" class="text-gray-200 hover:text-white">Products</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-gray-200 hover:text-white">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-200 hover:text-white">Login</a>
                <a href="{{ route('register') }}" class="text-gray-200 hover:text-white">Register</a>
            @endauth
        </div>
    </div>
</nav>
