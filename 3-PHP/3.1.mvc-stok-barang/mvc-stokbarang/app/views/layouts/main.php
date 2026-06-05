<?php require_once __DIR__ . '/header.php'; ?>
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary-800 text-white flex flex-col flex-shrink-0">
        <div class="px-6 py-5 border-b border-primary-700">
            <h1 class="text-xl font-bold tracking-tight">StokBarang</h1>
            <p class="text-primary-300 text-xs mt-0.5">Admin Panel</p>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="<?= BASE_URL ?>/stock"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      <?= str_contains($_SERVER['REQUEST_URI'], '/stock') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                Stok Barang
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-primary-700">
            <p class="text-primary-300 text-xs mb-2">Login sebagai</p>
            <p class="text-white text-sm font-semibold"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
            <a href="<?= BASE_URL ?>/logout"
               class="mt-3 flex items-center gap-2 text-primary-300 hover:text-red-400 text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </a>
        </div>
    </aside>

    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-gray-800 font-semibold text-lg"><?= htmlspecialchars($pageTitle ?? '') ?></h2>
            <span class="text-sm text-gray-500"><?= date('d F Y') ?></span>
        </header>

        <!-- Flash messages -->
        <div class="px-8 pt-4">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>
        </div>

        <!-- Page body -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
</body>
</html>
