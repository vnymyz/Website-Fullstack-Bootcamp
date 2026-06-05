<!-- Reusable stock form partial. Expects: $old, $errors, $action, $submitLabel, $hiddenId (optional) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= BASE_URL . $action ?>">
        <?php if (!empty($hiddenId)): ?>
            <input type="hidden" name="id" value="<?= (int)$hiddenId ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Kode Barang -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Barang <span class="text-red-500">*</span></label>
                <input type="text" name="kode_barang" value="<?= htmlspecialchars($old['kode_barang'] ?? '') ?>"
                       class="w-full border <?= isset($errors['kode_barang']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono"
                       placeholder="BRG-001">
                <?php if (isset($errors['kode_barang'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= $errors['kode_barang'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Nama Barang -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" value="<?= htmlspecialchars($old['nama_barang'] ?? '') ?>"
                       class="w-full border <?= isset($errors['nama_barang']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="Nama barang / produksi">
                <?php if (isset($errors['nama_barang'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= $errors['nama_barang'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                <input type="text" name="kategori" value="<?= htmlspecialchars($old['kategori'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="Bahan Baku, Komponen, dst." list="kategoriList">
                <datalist id="kategoriList">
                    <option value="Bahan Baku">
                    <option value="Bahan Pendukung">
                    <option value="Komponen">
                    <option value="Produk Jadi">
                    <option value="Alat & Mesin">
                </datalist>
            </div>

            <!-- Satuan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" value="<?= htmlspecialchars($old['satuan'] ?? 'pcs') ?>"
                       class="w-full border <?= isset($errors['satuan']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="pcs / kg / liter / batang" list="satuanList">
                <datalist id="satuanList">
                    <option value="pcs"><option value="kg"><option value="gram">
                    <option value="liter"><option value="ml"><option value="meter">
                    <option value="cm"><option value="batang"><option value="lembar">
                    <option value="kaleng"><option value="kotak"><option value="set">
                </datalist>
                <?php if (isset($errors['satuan'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= $errors['satuan'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Stok -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Stok <span class="text-red-500">*</span></label>
                <input type="number" name="stok" value="<?= htmlspecialchars($old['stok'] ?? 0) ?>" min="0"
                       class="w-full border <?= isset($errors['stok']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <?php if (isset($errors['stok'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= $errors['stok'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Harga -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="harga" value="<?= htmlspecialchars($old['harga'] ?? 0) ?>" min="0" step="any"
                       class="w-full border <?= isset($errors['harga']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <?php if (isset($errors['harga'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= $errors['harga'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Keterangan -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                          placeholder="Deskripsi / catatan tambahan..."><?= htmlspecialchars($old['keterangan'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
            <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <?= htmlspecialchars($submitLabel) ?>
            </button>
            <a href="<?= BASE_URL ?>/stock"
               class="text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
