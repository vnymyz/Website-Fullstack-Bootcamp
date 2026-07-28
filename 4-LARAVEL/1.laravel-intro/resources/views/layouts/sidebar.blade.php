<aside x-data="{ open: false }" class="w-64 bg-white border-r border-gray-200 shrink-0">
    <div class="p-6 border-b border-gray-100">
        <a href="/dashboard" class="text-lg font-bold text-blue-600">Laravel Intro</a>
    </div>

    <nav class="p-4 space-y-1">
        <a href="/dashboard"
            class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
            Dashboard
        </a>

        <a href="/posts"
            class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->is('posts*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
            Posts
        </a>

        @if (auth()->user()->role === 'admin')
            <a href="/admin/users"
                class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->is('admin/users*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                User Management
            </a>
        @endif

        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
            Profile
        </a>
    </nav>

    <div class="p-4 mt-auto border-t border-gray-100">
        <div class="px-3 mb-2">
            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm text-red-600 hover:bg-red-50">
                Logout
            </button>
        </form>
    </div>
</aside>
