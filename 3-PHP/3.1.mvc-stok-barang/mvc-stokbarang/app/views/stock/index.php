<?php
$pageTitle = 'Stok Barang';
ob_start();
?>
<!-- Summary cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</p>
        <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($summary['total_items']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok</p>
        <p class="text-3xl font-bold text-primary-600 mt-1"><?= number_format($summary['total_stok'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Inventori</p>
        <p class="text-2xl font-bold text-green-600 mt-1">Rp <?= number_format($summary['total_nilai'] ?? 0, 0, ',', '.') ?></p>
    </div>
</div>

<!-- Toolbar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <form method="GET" action="<?= BASE_URL ?>/stock" class="flex gap-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
               placeholder="Cari nama / kode / kategori..."
               class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-primary-500">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Cari
        </button>
        <?php if ($search): ?>
            <a href="<?= BASE_URL ?>/stock" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">Reset</a>
        <?php endif; ?>
    </form>
    <a href="<?= BASE_URL ?>/stock/create"
       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Barang
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($stocks)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <?= $search ? "Tidak ada hasil untuk \"" . htmlspecialchars($search) . "\"" : 'Belum ada data stok barang.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($stocks as $i => $item): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400"><?= $i + 1 ?></td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                <?= htmlspecialchars($item['kode_barang']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($item['nama_barang']) ?></td>
                        <td class="px-4 py-3">
                            <?php if ($item['kategori']): ?>
                                <span class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                    <?= htmlspecialchars($item['kategori']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-300">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold <?= $item['stok'] <= 10 ? 'text-red-600' : 'text-gray-800' ?>">
                            <?= number_format($item['stok']) ?>
                            <?php if ($item['stok'] <= 10): ?>
                                <span class="ml-1 text-xs text-red-500">⚠</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($item['satuan']) ?></td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= BASE_URL ?>/stock/edit?id=<?= $item['id'] ?>"
                                   class="text-primary-600 hover:text-primary-800 font-medium text-xs px-3 py-1.5 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>/stock/delete"
                                      onsubmit="return confirm('Hapus barang \'<?= addslashes($item['nama_barang']) ?>\'?')">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
