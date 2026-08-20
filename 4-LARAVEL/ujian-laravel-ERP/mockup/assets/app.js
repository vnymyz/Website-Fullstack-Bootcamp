const page = location.pathname.split("/").pop() || "dashboard.html";
const salesModulePages = [
    "sales.html",
    "sales-form.html",
    "sales-detail.html",
    "delivery-order.html",
    "invoices.html",
    "invoice-detail.html",
    "sales-returns.html",
    "return-form.html",
    "receivables.html",
    "payment-form.html",
];
const pages = {
    "dashboard.html": [
        "Dashboard",
        "Ikhtisar operasional perusahaan hari ini.",
    ],
    "master-data.html": [
        "Master Data & Stok",
        "Kelola produk, customer, supplier, dan persediaan.",
    ],
    "purchases.html": [
        "Pembelian",
        "Pembelian, penerimaan barang, ongkir, dan hutang supplier.",
    ],
    "purchase-form.html": [
        "Buat Pembelian",
        "Tambahkan supplier dan barang yang akan dibeli.",
    ],
    "sales.html": [
        "Penjualan",
        "Quotation, PO, diskon, komisi, dan piutang customer.",
    ],
    "sales-detail.html": [
        "PO-2026-0045",
        "Detail order, invoice, dan progres pengiriman.",
    ],
    "delivery-order.html": [
        "Delivery Order",
        "Dokumen pengiriman parsial untuk customer.",
    ],
    "invoices.html": [
        "Invoice Penjualan",
        "Kelola invoice, pembayaran, dan piutang customer.",
    ],
    "invoice-detail.html": [
        "INV-2026-0038",
        "Detail dan dokumen invoice penjualan.",
    ],
    "sales-returns.html": [
        "Retur Penjualan",
        "Kelola pengembalian barang dan penyesuaian tagihan customer.",
    ],
    "return-form.html": [
        "Tambah Retur Penjualan",
        "Catat barang yang dikembalikan berdasarkan invoice atau Delivery Order.",
    ],
    "receivables.html": [
        "Piutang Dagang",
        "Pantau tagihan customer, jatuh tempo, dan riwayat pembayaran.",
    ],
    "payment-form.html": [
        "Catat Pembayaran",
        "Catat pembayaran customer untuk mengurangi saldo piutang.",
    ],
    "finance.html": [
        "Kas & Bank",
        "Pantau empat rekening operasional dan pengeluaran.",
    ],
    "reports.html": [
        "Laporan",
        "Analisis transaksi dan ringkasan keuangan periode custom.",
    ],
    "assets.html": [
        "Asset Management",
        "Aset tetap dan perhitungan depresiasi garis lurus.",
    ],
    "admin-users.html": [
        "Kelola Pengguna",
        "Role, wewenang, target penjualan, dan komisi.",
    ],
    "profile.html": [
        "Edit Profil",
        "Perbarui foto, username, email, dan password akun Anda.",
    ],
    "user-form.html": [
        "Tambah Pengguna",
        "Buat akun anggota baru dan atur wewenangnya.",
    ],
    "user-edit.html": [
        "Edit Pengguna",
        "Perbarui identitas, role, target, dan komisi anggota.",
    ],
    "sales-form.html": [
        "Tambah Penjualan",
        "Buat quotation atau sales order baru.",
    ],
    "finance-form.html": [
        "Tambah Pengeluaran",
        "Catat transaksi keluar dari kas atau rekening bank.",
    ],
    "asset-form.html": [
        "Tambah Aset",
        "Catat aset baru dan informasi depresiasinya.",
    ],
    "product-form.html": [
        "Tambah Produk",
        "Tambahkan produk baru ke master data dan persediaan.",
    ],
};
const iconPaths = {
    dashboard: "M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-16v4h6V4h-6Z",
    box: "m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 2.3L7 8l5 2.7L17 8l-5-2.7Zm-6 4.4v5.6l5 2.8v-5.7L6 9.7Zm7 8.4 5-2.8V9.7l-5 2.7v5.7Z",
    purchase: "M7 4V2h2v2h6V2h2v2h3v17H4V4h3Zm11 6H6v9h12v-9ZM6 8h12V6H6v2Z",
    sales: "M5 18 18 5h-7V3h10v10h-2V6.4L6.4 19H13v2H3V11h2v7Z",
    bank: "M12 2 3 7v2h18V7l-9-5Zm0 2.3L16.8 7H7.2L12 4.3ZM5 11v7H3v2h18v-2h-2v-7h-2v7h-3v-7h-2v7H9v-7H7v7H5v-7Z",
    report: "M5 3h14v18H5V3Zm2 2v14h10V5H7Zm2 2h6v2H9V7Zm0 4h6v2H9v-2Zm0 4h4v2H9v-2Z",
    asset: "m12 2 9 5-9 5-9-5 9-5Zm0 2.3L7.8 7 12 9.7 16.2 7 12 4.3ZM3 11l9 5 9-5v2.3l-9 5-9-5V11Zm0 5 9 5 9-5v2.3l-9 5-9-5V16Z",
    users: "M9 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm7.5 3a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7ZM2 21v-3c0-3 3.1-5 7-5s7 2 7 5v3H2Zm2-2h10v-1c0-1.5-2.1-3-5-3s-5 1.5-5 3v1Z",
    search: "m20.7 19.3-4.2-4.2a7 7 0 1 0-1.4 1.4l4.2 4.2 1.4-1.4ZM5 11a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z",
    bell: "M12 22a2.5 2.5 0 0 0 2.4-2H9.6a2.5 2.5 0 0 0 2.4 2Zm7-5v-6a7 7 0 0 0-6-6.9V2h-2v2.1A7 7 0 0 0 5 11v6l-2 2h18l-2-2Zm-12 0v-6a5 5 0 0 1 10 0v6H7Z",
    plus: "M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5Z",
};
const icon = (name) =>
    `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="${iconPaths[name]}"></path></svg>`;
const nav = [
    ["dashboard.html", "dashboard", "Dashboard"],
    ["master-data.html", "box", "Master Data & Stok"],
    ["purchases.html", "purchase", "Pembelian"],
    ["sales.html", "sales", "Penjualan"],
    ["finance.html", "bank", "Kas & Bank"],
    ["reports.html", "report", "Laporan"],
    ["assets.html", "asset", "Asset Management"],
    ["admin-users.html", "users", "Pengguna"],
    ["profile.html", "users", "Profil Saya"],
];
function sidebar() {
    return `<aside class="sidebar"><a class="brand" href="dashboard.html"><span class="brand-mark">N</span><span>NiagaERP</span></a><button class="sidebar-toggle" type="button" aria-label="Sembunyikan sidebar" aria-expanded="true">&lt;&lt;</button><a class="sidebar-home" href="index.html"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 9 8h-3v10h-5v-6h-2v6H6V11H3l9-8Zm0 2.7L8 9.2V19h1v-6h6v6h1V9.2l-4-3.5Z"/></svg><span>Halaman Utama</span></a><p class="nav-label">MENU UTAMA</p>${nav
        .slice(0, 5)
        .map(
            (n) =>
                `<a class="side-link ${n[0] === page || (n[0] === "sales.html" && salesModulePages.includes(page)) ? "active" : ""}" href="${n[0]}"><span class="side-icon">${icon(n[1])}</span><span>${n[2]}</span></a>`,
        )
        .join("")}<p class="nav-label">MANAJEMEN</p>${nav
        .slice(5)
        .map(
            (n) =>
                `<a class="side-link ${n[0] === page || (n[0] === "sales.html" && salesModulePages.includes(page)) ? "active" : ""}" href="${n[0]}"><span class="side-icon">${icon(n[1])}</span><span>${n[2]}</span></a>`,
        )
        .join(
            "",
        )}<div class="sidebar-bottom"><a class="sidebar-profile ${page === "profile.html" ? "active" : ""}" href="profile.html"><span class="sidebar-avatar-wrap"><span class="avatar avatar-small">AN</span><i class="online-indicator" title="Online" aria-label="Online"></i></span><span class="user-mini">Admin Niaga<small>admin@niaga.test</small></span></a><a class="logout" href="index.html" aria-label="Logout"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 2h2v10h-2V2Zm6.7 3.3A9 9 0 1 1 6.3 5.3l1.4 1.4a7 7 0 1 0 8.6 0l1.4-1.4Z"/></svg><span>Logout</span></a></div></aside>`;
}
const badge = (v, c = "blue") => `<span class="badge ${c}">${v}</span>`;
const table = (head, body) => {
    let row = 0;
    const numbered = body.replace(
        /<tr>/g,
        () => `<tr><td class="row-number">${++row}</td>`,
    );
    return `<div class="card table-wrap"><table class="data-table"><thead><tr><th class="row-number">No.</th>${head.map((x) => `<th>${x}</th>`).join("")}</tr></thead><tbody>${numbered}</tbody></table></div>`;
};
const stat = (a, b, c, warning = false) =>
    `<div class="card stat"><div class="stat-label">${a}</div><div class="stat-value">${b}</div><div class="trend ${warning ? "warn" : ""}">${c}</div></div>`;
function dashboard() {
    return `<div class="grid stats">${stat("Penjualan Bulan Ini", "Rp128,4 jt", "Naik 12% dari bulan lalu")}${stat("Piutang Dagang", "Rp34,8 jt", "12 invoice belum lunas", true)}${stat("Hutang Supplier", "Rp21,25 jt", "6 pembelian aktif", true)}${stat("Stok Menipis", "6 Produk", "Perlu restock", true)}</div><div class="grid two-col" style="margin-top:18px"><section class="card panel"><div class="panel-title"><h2>Aktivitas Terbaru</h2><a href="sales.html">Lihat semua</a></div><ul class="activity"><li><i class="dot"></i><span><b>DO-2026-0012</b> dikirim untuk PO-2026-0045 <small>10:15</small></span></li><li><i class="dot" style="background:#f59e0b"></i><span><b>PB-2026-0021</b> menunggu penerimaan barang <small>09:20</small></span></li><li><i class="dot" style="background:#10b981"></i><span>Pengeluaran ongkir dicatat dari <b>Bank BCA</b> <small>Kemarin</small></span></li></ul></section><section class="card panel"><div class="panel-title"><h2>Saldo Kas & Bank</h2><a href="finance.html">Detail</a></div><ul class="activity"><li><i class="dot"></i><span>Kas Kecil <small>Rp2.500.000</small></span></li><li><i class="dot"></i><span>Bank BCA <small>Rp48.900.000</small></span></li><li><i class="dot"></i><span>Bank Mandiri <small>Rp35.700.000</small></span></li><li><i class="dot"></i><span>Bank BRI <small>Rp12.150.000</small></span></li></ul></section></div><section class="card panel" style="margin-top:18px"><div class="panel-title"><h2>Akses Cepat</h2></div><div class="quick"><a href="purchase-form.html"><span class="quick-icon purchase">${icon("purchase")}</span><div><b>Pembelian Baru</b><span>Catat purchase order supplier</span></div></a><a href="sales-form.html"><span class="quick-icon sales">${icon("sales")}</span><div><b>Quotation / PO</b><span>Buat penawaran atau order</span></div></a><a href="finance-form.html"><span class="quick-icon bank">${icon("bank")}</span><div><b>Pengeluaran</b><span>Catat pengeluaran bank/kas</span></div></a><a href="asset-form.html"><span class="quick-icon asset">${icon("asset")}</span><div><b>Tambah Aset</b><span>Kelola aset tetap perusahaan</span></div></a></div></section>`;
}
function master() {
    return `<div class="grid stats">${stat("Total Produk", "86", "12 kategori")}${stat("Nilai Persediaan", "Rp95,2 jt", "Berdasarkan harga beli")}${stat("Stok Menipis", "6 SKU", "Butuh restock", true)}${stat("Kontak Aktif", "42", "28 customer - 14 supplier")}</div><section style="margin-top:18px">${table(["SKU", "Produk", "Satuan", "Harga Jual", "Stok", "Status", ""], `<tr><td>ATK-001</td><td><b>Kertas HVS A4 80gsm</b></td><td>Rim</td><td>Rp58.000</td><td>125</td><td>${badge("Aman", "green")}</td><td><a class="table-link" href="product-form.html">Edit</a></td></tr><tr><td>ATK-011</td><td><b>Tinta Printer Black</b></td><td>Pcs</td><td>Rp145.000</td><td>3</td><td>${badge("Menipis", "orange")}</td><td><a class="table-link" href="product-form.html">Edit</a></td></tr>`)}</section>`;
}
function productForm() {
    return `<form class="card panel form-card transaction-form" onsubmit="event.preventDefault();location.href='master-data.html'"><div class="form-card-head"><div><h2>Data Produk</h2><p>Lengkapi identitas, harga, dan batas minimum persediaan.</p></div>${badge("Produk Baru", "blue")}</div><div class="form-grid"><div class="field"><label>SKU / Kode Produk</label><input class="input" value="ATK-012" placeholder="Contoh: ATK-012" required></div><div class="field"><label>Nama Produk</label><input class="input" placeholder="Contoh: Pulpen Gel Hitam" required></div><div class="field"><label>Kategori</label><select class="select"><option>Alat Tulis Kantor</option><option>Kertas</option><option>Tinta dan Printer</option><option>Perlengkapan Gudang</option></select></div><div class="field"><label>Satuan</label><select class="select"><option>Pcs</option><option>Rim</option><option>Box</option><option>Pack</option><option>Unit</option></select></div><div class="field"><label>Harga Beli</label><div class="input-prefix"><span>Rp</span><input class="input" type="number" min="0" placeholder="0" required></div></div><div class="field"><label>Harga Jual</label><div class="input-prefix"><span>Rp</span><input class="input" type="number" min="0" placeholder="0" required></div></div><div class="field"><label>Stok Awal</label><input class="input" type="number" min="0" value="0" required></div><div class="field"><label>Stok Minimum</label><input class="input" type="number" min="0" value="5" required></div><div class="field"><label>Status Produk</label><select class="select"><option>Aktif</option><option>Nonaktif</option></select></div></div><div class="field form-wide"><label>Deskripsi Produk</label><textarea class="input" rows="4" placeholder="Tambahkan informasi produk jika diperlukan..."></textarea></div><div class="form-actions"><a class="btn btn-secondary" href="master-data.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Produk</button></div></form>`;
}
function purchases() {
    return `<div class="grid stats">${stat("Belum Diterima", "4", "Purchase aktif", true)}${stat("Ongkir Belum Dibayar", "Rp1.250.000", "2 transaksi", true)}${stat("Retur Pembelian", "1", "Bulan ini")}${stat("Total Hutang", "Rp21,25 jt", "6 supplier")}</div><section style="margin-top:18px">${table(["No. Pembelian", "Supplier", "Tanggal", "Total", "Status", "Aksi"], `<tr><td><b>PB-2026-0021</b></td><td>PT Sumber Makmur</td><td>18 Agu 2026</td><td>Rp8.450.000</td><td>${badge("Menunggu Barang", "orange")}</td><td><a class="table-link" href="purchase-form.html">Terima Barang</a></td></tr><tr><td><b>PB-2026-0020</b></td><td>CV Prima Niaga</td><td>15 Agu 2026</td><td>Rp4.200.000</td><td>${badge("Ongkir Belum Bayar", "blue")}</td><td><a class="table-link" href="#">Detail</a></td></tr>`)}</section>`;
}
function purchaseForm() {
    return `<form class="card panel form-card transaction-form" onsubmit="event.preventDefault();location.href='purchases.html'"><div class="form-card-head"><div><h2>Data Pembelian</h2><p>Pilih supplier dan tambahkan barang yang dibeli.</p></div>${badge("Draft", "slate")}</div><div class="form-grid"><div class="field"><label>Nomor Pembelian</label><input class="input" value="PB-2026-0022" required></div><div class="field"><label>Supplier</label><select class="select" required><option>PT Sumber Makmur</option><option>CV Prima Niaga</option></select></div><div class="field"><label>Tanggal Pembelian</label><input class="input" type="date" value="2026-08-20" required></div><div class="field"><label>Status Awal</label><select class="select"><option>Draft</option><option>Open</option></select></div><div class="field"><label>Biaya Ongkir</label><input class="input" type="number" value="0" min="0"></div><div class="field"><label>Status Ongkir</label><select class="select"><option>Belum Dibayar</option><option>Sudah Dibayar</option></select></div></div><div class="section-divider"><span>Item Barang</span></div>${table(["Produk", "Quantity", "Harga", "Subtotal", "Aksi"], `<tr><td><select class="select"><option>Kertas HVS A4 80gsm</option><option>Tinta Printer Black</option></select></td><td><input class="input" type="number" value="20" min="1"></td><td><input class="input" type="number" value="58000" min="0"></td><td>Rp1.160.000</td><td><button class="btn btn-small btn-danger" type="button" onclick="removeItemRow(this)">Delete</button></td></tr>`)}<button class="btn btn-soft add-row-button" type="button" onclick="addProductRow(this)">${icon("plus")}<span>Tambah Item</span></button><div class="transaction-total"><span>Total Pembelian</span><b>Rp1.160.000</b></div><div class="form-actions"><a class="btn btn-secondary" href="purchases.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Pembelian</button></div></form>`;
}
function sales() {
    return `${salesTabs("orders")}<div class="grid stats">${stat("PO Aktif", "12", "Order berjalan")}${stat("Pengiriman Parsial", "3", "Menunggu pengiriman", true)}${stat("Piutang Dagang", "Rp34,8 jt", "12 invoice belum lunas", true)}${stat("Komisi Sales", "Rp2,6 jt", "Periode Agustus")}</div><section style="margin-top:18px">${table(["No. Dokumen", "Customer", "Nilai", "Terkirim", "Status", "Aksi"], `<tr><td><b>PO-2026-0045</b></td><td>CV Maju Jaya</td><td>Rp12.500.000</td><td>60%</td><td>${badge("Partial", "blue")}</td><td><a class="table-link" href="sales-detail.html">Detail</a></td></tr><tr><td><b>QT-2026-0031</b></td><td>PT Cipta Abadi</td><td>Rp6.750.000</td><td>-</td><td>${badge("Quotation", "slate")}</td><td><a class="table-link" href="#">Konversi PO</a></td></tr>`)}</section>`;
}
function salesForm() {
    return `<form class="card panel form-card transaction-form" onsubmit="event.preventDefault();location.href='sales.html'"><div class="form-card-head"><div><h2>Data Penjualan Baru</h2><p>Buat quotation atau sales order untuk customer.</p></div>${badge("Draft", "slate")}</div><div class="form-grid"><div class="field"><label>Jenis Dokumen</label><select class="select"><option>Quotation</option><option>Sales Order / PO</option></select></div><div class="field"><label>Nomor Dokumen</label><input class="input" value="QT-2026-0032" required></div><div class="field"><label>Tanggal</label><input class="input" type="date" value="2026-08-20" required></div><div class="field"><label>Customer</label><select class="select"><option>CV Maju Jaya</option><option>PT Cipta Abadi</option></select></div><div class="field"><label>Sales</label><select class="select"><option>Budi Santoso</option></select></div><div class="field"><label>Tipe Diskon</label><select class="select"><option>Tanpa diskon</option><option>Persentase</option><option>Nominal</option></select></div></div><div class="section-divider"><span>Item Penjualan</span></div>${table(["Produk", "Quantity", "Harga Jual", "Diskon", "Subtotal", "Aksi"], `<tr><td><select class="select"><option>Kertas HVS A4 80gsm</option><option>Tinta Printer Black</option></select></td><td><input class="input" type="number" value="10" min="1"></td><td><input class="input" type="number" value="58000" min="0"></td><td><input class="input" type="number" value="0" min="0"></td><td>Rp580.000</td><td><button class="btn btn-small btn-danger" type="button" onclick="removeItemRow(this)">Delete</button></td></tr>`)}<button class="btn btn-soft add-row-button" type="button" onclick="addProductRow(this)">${icon("plus")}<span>Tambah Item</span></button><div class="transaction-total"><span>Total Penjualan</span><b>Rp580.000</b></div><div class="form-actions"><a class="btn btn-secondary" href="sales.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Penjualan</button></div></form>`;
}
function salesDetail() {
    return `${salesTabs("orders")}<div class="grid two-col"><section class="card panel"><div class="panel-title"><h2>Item Pesanan</h2>${badge("Partial", "blue")}</div>${table(["Produk", "Pesan", "Terkirim", "Sisa", "Harga"], `<tr><td>Kertas HVS A4 80gsm</td><td>100 rim</td><td>60 rim</td><td><b>40 rim</b></td><td>Rp58.000</td></tr><tr><td>Tinta Printer Black</td><td>20 pcs</td><td>12 pcs</td><td><b>8 pcs</b></td><td>Rp145.000</td></tr>`)}</section><section class="card panel"><h2 style="margin-top:0;font-size:15px">Ringkasan Invoice</h2><ul class="activity"><li><span>Subtotal <small>Rp12.900.000</small></span></li><li><span>Diskon 5% <small>-Rp645.000</small></span></li><li><span><b>Total</b> <small><b>Rp12.255.000</b></small></span></li><li><span>Sudah Dibayar <small>Rp5.000.000</small></span></li><li><span class="amount-negative">Sisa Piutang <small class="amount-negative">Rp7.255.000</small></span></li></ul><div class="document-actions"><a href="delivery-order.html" class="btn btn-primary">Buat Delivery Order</a><a href="invoice-detail.html" class="btn btn-soft">Buat Invoice</a><a href="invoices.html" class="btn btn-secondary">Daftar Invoice</a></div></section></div><section class="card panel" style="margin-top:18px"><div class="panel-title"><h2>Riwayat Pengiriman</h2><a href="delivery-order.html">Print DO</a></div><p class="muted" style="font-size:13px"><b style="color:var(--ink)">DO-2026-0011</b> - 15 Agustus 2026 - 60% pesanan telah dikirim.</p></section>`;
}
function delivery() {
    return `${salesTabs("delivery")}<section class="card panel" style="max-width:850px;margin:auto"><div class="panel-title"><h2>NIAGAERP <span class="muted" style="font-weight:400">/ DELIVERY ORDER</span></h2><button class="btn btn-secondary" onclick="window.print()">Print Delivery Order</button></div><div class="grid two-col" style="margin:25px 0"><div><b>Kepada</b><p class="muted">CV Maju Jaya<br>Jl. Industri Raya No. 5<br>Jakarta Barat</p></div><div><b>Referensi</b><p class="muted">DO-2026-0011<br>PO-2026-0045 - 15 Agu 2026</p></div></div>${table(["Produk", "Qty Dikirim", "Satuan"], `<tr><td>Kertas HVS A4 80gsm</td><td>60</td><td>rim</td></tr><tr><td>Tinta Printer Black</td><td>12</td><td>pcs</td></tr>`)}<div class="grid two-col" style="margin-top:100px;text-align:center"><div style="border-top:1px solid var(--line);padding-top:10px">Diterima oleh</div><div style="border-top:1px solid var(--line);padding-top:10px">Dikirim oleh</div></div></section>`;
}
function finance() {
    return `<div class="grid stats">${stat("Kas Kecil", "Rp2.500.000", "Saldo saat ini")}${stat("Bank BCA", "Rp48.900.000", "Saldo saat ini")}${stat("Bank Mandiri", "Rp35.700.000", "Saldo saat ini")}${stat("Bank BRI", "Rp12.150.000", "Saldo saat ini")}</div><section style="margin-top:18px">${table(["Tanggal", "Akun", "Kategori", "Keterangan", "Nominal", "Aksi"], `<tr><td>20 Agu 2026</td><td>Bank BCA</td><td>Pengiriman</td><td>Ongkir PB-2026-0020</td><td class="amount-negative">-Rp350.000</td><td><a class="btn btn-small btn-secondary" href="finance-form.html">Edit</a></td></tr><tr><td>19 Agu 2026</td><td>Kas Kecil</td><td>Operasional</td><td>ATK kantor</td><td class="amount-negative">-Rp125.000</td><td><a class="btn btn-small btn-secondary" href="finance-form.html">Edit</a></td></tr>`)}</section>`;
}
function financeForm() {
    return `<form class="card panel form-card transaction-form compact-form" onsubmit="event.preventDefault();location.href='finance.html'"><div class="form-card-head"><div><h2>Catat Pengeluaran Kas / Bank</h2><p>Pilih sumber dana dan lengkapi informasi pengeluaran.</p></div>${badge("Pengeluaran", "orange")}</div><div class="form-grid profile-fields"><div class="field"><label>Akun Kas / Bank</label><select class="select"><option>Kas Kecil</option><option>Bank BCA</option><option>Bank Mandiri</option><option>Bank BRI</option></select></div><div class="field"><label>Tanggal</label><input class="input" type="date" value="2026-08-20" required></div><div class="field"><label>Kategori</label><select class="select"><option>Operasional</option><option>Pengiriman</option><option>Utilitas</option><option>Perawatan Aset</option></select></div><div class="field"><label>Nominal</label><div class="input-prefix"><span>Rp</span><input class="input" type="number" min="1" placeholder="0" required></div></div></div><div class="field form-wide"><label>Keterangan</label><textarea class="input" rows="4" placeholder="Tuliskan tujuan pengeluaran..." required></textarea></div><div class="form-actions"><a class="btn btn-secondary" href="finance.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Pengeluaran</button></div></form>`;
}
function reports() {
    return `<section class="card panel"><div class="filters"><input class="input" type="date" value="2026-08-01"><input class="input" type="date" value="2026-08-20"><select class="select"><option>Ringkasan Keuangan</option></select><button class="btn btn-primary">Tampilkan Laporan</button></div></section><div class="grid three-col" style="margin-top:18px">${stat("Total Penjualan", "Rp128,4 jt", "Periode dipilih")}${stat("Total Pembelian", "Rp74,25 jt", "Periode dipilih")}${stat("Total Pengeluaran", "Rp9,85 jt", "Periode dipilih")}</div><section class="card panel" style="margin-top:18px"><div class="panel-title"><h2>Ringkasan Perpajakan Sederhana</h2><a href="#">Print</a></div><div class="grid three-col"><div><span class="muted">Penjualan Bersih</span><b class="stat-value" style="display:block">Rp121.600.000</b></div><div><span class="muted">Pembelian Bersih</span><b class="stat-value" style="display:block">Rp70.500.000</b></div><div><span class="muted">Estimasi Laba Kotor</span><b class="stat-value" style="display:block">Rp41.250.000</b></div></div></section>`;
}
function assets() {
    return `<div class="grid stats">${stat("Total Aset", "12", "Aset aktif")}${stat("Nilai Perolehan", "Rp143,5 jt", "Seluruh aset")}${stat("Akumulasi Depresiasi", "Rp28,2 jt", "Sampai Agustus")}${stat("Nilai Buku", "Rp115,3 jt", "Nilai saat ini")}</div><section style="margin-top:18px">${table(["Kode", "Nama Aset", "Harga Perolehan", "Masa Manfaat", "Depresiasi/Bulan", "Nilai Buku", "Aksi"], `<tr><td>AST-001</td><td><b>Laptop Operasional</b></td><td>Rp15.000.000</td><td>48 bulan</td><td>Rp281.250</td><td><b>Rp10.500.000</b></td><td><a class="btn btn-small btn-secondary" href="asset-form.html">Edit</a></td></tr><tr><td>AST-002</td><td><b>Rak Gudang Besi</b></td><td>Rp8.000.000</td><td>60 bulan</td><td>Rp125.000</td><td><b>Rp7.000.000</b></td><td><a class="btn btn-small btn-secondary" href="asset-form.html">Edit</a></td></tr>`)}</section>`;
}
function assetForm() {
    return `<form class="card panel form-card transaction-form" onsubmit="event.preventDefault();location.href='assets.html'"><div class="form-card-head"><div><h2>Data Aset Baru</h2><p>Masukkan nilai aset untuk menghitung depresiasi garis lurus.</p></div>${badge("Aset Baru", "blue")}</div><div class="form-grid"><div class="field"><label>Kode Aset</label><input class="input" value="AST-003" required></div><div class="field"><label>Nama Aset</label><input class="input" placeholder="Contoh: Printer Kantor" required></div><div class="field"><label>Tanggal Perolehan</label><input class="input" type="date" value="2026-08-20" required></div><div class="field"><label>Harga Perolehan</label><div class="input-prefix"><span>Rp</span><input class="input" type="number" min="1" placeholder="0" required></div></div><div class="field"><label>Nilai Residu</label><div class="input-prefix"><span>Rp</span><input class="input" type="number" min="0" value="0"></div></div><div class="field"><label>Masa Manfaat</label><div class="input-suffix"><input class="input" type="number" min="1" placeholder="48" required><span>Bulan</span></div></div><div class="field"><label>Kategori Aset</label><select class="select"><option>Peralatan Kantor</option><option>Kendaraan</option><option>Mesin</option><option>Bangunan</option></select></div><div class="field"><label>Metode Depresiasi</label><select class="select"><option>Garis Lurus</option></select></div><div class="field"><label>Lokasi Aset</label><input class="input" placeholder="Contoh: Kantor Pusat"></div></div><div class="calculation-preview"><span>Estimasi depresiasi per bulan</span><b>Otomatis setelah nilai diisi</b></div><div class="form-actions"><a class="btn btn-secondary" href="assets.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Aset</button></div></form>`;
}
function addProductRow(button) {
    const tableElement = button.closest("form").querySelector("table");
    const tbody = tableElement.tBodies[0];
    const number = tbody.rows.length + 1;
    const isSales = tableElement.tHead.rows[0].cells.length === 7;
    const discount = isSales
        ? '<td><input class="input" type="number" value="0" min="0"></td>'
        : "";
    tbody.insertAdjacentHTML(
        "beforeend",
        `<tr><td class="row-number">${number}</td><td><select class="select"><option>Pilih produk</option><option>Kertas HVS A4 80gsm</option><option>Tinta Printer Black</option></select></td><td><input class="input" type="number" value="1" min="1"></td><td><input class="input" type="number" value="0" min="0"></td>${discount}<td>Rp0</td><td><button class="btn btn-small btn-danger" type="button" onclick="removeItemRow(this)">Delete</button></td></tr>`,
    );
}
function removeItemRow(button) {
    const tbody = button.closest("tbody");
    button.closest("tr").remove();
    [...tbody.rows].forEach(
        (row, index) =>
            (row.querySelector(".row-number").textContent = index + 1),
    );
}
function users() {
    return `<section class="card panel role-access-guide"><div class="role-guide-head"><div><h2>Aturan Role dan Wewenang</h2><p>Setelah login, setiap pengguna hanya dapat membuka menu dan menjalankan tindakan yang sesuai dengan role-nya.</p></div>${badge("Authorization", "blue")}</div><div class="role-guide-grid"><article><span class="role-mark admin">AD</span><div><h3>Admin</h3><p>Akses penuh ke seluruh modul, pengguna, role, master data, transaksi, aset, dan laporan.</p></div></article><article><span class="role-mark sales">SL</span><div><h3>Sales</h3><p>Mengelola customer, quotation, PO penjualan, invoice, Delivery Order, retur, target, dan komisi miliknya.</p></div></article><article><span class="role-mark purchasing">PC</span><div><h3>Purchasing</h3><p>Mengelola supplier, purchase order, penerimaan barang, ongkir, retur pembelian, dan daftar hutang.</p></div></article><article><span class="role-mark finance">FN</span><div><h3>Finance</h3><p>Mengelola kas dan bank, pengeluaran, pembayaran, piutang, hutang, serta laporan keuangan dan pajak.</p></div></article><article><span class="role-mark warehouse">WH</span><div><h3>Warehouse</h3><p>Mengelola stok, penerimaan barang, pengiriman, Delivery Order, dan retur tanpa mengubah data keuangan.</p></div></article></div><div class="authorization-note"><b>Aturan penting:</b><span>Menyembunyikan menu saja tidak cukup. Route, Controller, Form Request, dan Policy tetap harus memeriksa role agar URL tidak dapat diakses secara paksa.</span></div></section><div class="grid three-col user-summary">${stat("Total Pengguna", "3", "Semua anggota")}${stat("Sales Aktif", "1", "Target sedang berjalan")}${stat("Role Tersedia", "5", "Hak akses terpisah")}</div><section style="margin-top:18px">${table(["Nama", "Role", "Target / Bulan", "Aturan Komisi", "Status", "Aksi"], `<tr><td><div class="user-cell"><span class="avatar avatar-small">AN</span><span><b>Admin Niaga</b> <span class="me-badge">Me</span><small>admin@niaga.test</small></span></div></td><td>${badge("Admin", "blue")}</td><td>-</td><td>-</td><td>${badge("Aktif", "green")}</td><td><div class="row-actions"><a class="btn btn-small btn-secondary" href="profile.html">Edit</a><button class="btn btn-small btn-disabled" disabled>Delete</button></div></td></tr><tr><td><div class="user-cell"><span class="avatar avatar-small avatar-alt">BS</span><span><b>Budi Santoso</b><small>budi@niaga.test</small></span></div></td><td>${badge("Sales", "blue")}</td><td>Rp50.000.000</td><td>2% total penjualan</td><td>${badge("Aktif", "green")}</td><td><div class="row-actions"><a class="btn btn-small btn-secondary" href="user-edit.html">Edit</a><button class="btn btn-small btn-danger" onclick="this.closest('tr').remove()">Delete</button></div></td></tr><tr><td><div class="user-cell"><span class="avatar avatar-small avatar-purple">SW</span><span><b>Sari Wulandari</b><small>sari@niaga.test</small></span></div></td><td>${badge("Purchasing", "slate")}</td><td>-</td><td>-</td><td>${badge("Aktif", "green")}</td><td><div class="row-actions"><a class="btn btn-small btn-secondary" href="user-edit.html">Edit</a><button class="btn btn-small btn-danger" onclick="this.closest('tr').remove()">Delete</button></div></td></tr>`)}</section>`;
}
function userForm(edit = false) {
    return `<form class="card panel form-card" onsubmit="event.preventDefault();location.href='admin-users.html'"><div class="form-card-head"><div><h2>${edit ? "Edit Data Pengguna" : "Data Pengguna Baru"}</h2><p>${edit ? "Perbarui informasi dan pengaturan akses anggota." : "Isi identitas dan tentukan hak akses anggota."}</p></div>${edit ? badge("Aktif", "green") : badge("Akun Baru", "blue")}</div><div class="form-grid"><div class="field"><label>Nama Lengkap</label><input class="input" value="${edit ? "Budi Santoso" : ""}" placeholder="Contoh: Budi Santoso" required></div><div class="field"><label>Username</label><input class="input" value="${edit ? "budi.santoso" : ""}" placeholder="username" required></div><div class="field"><label>Email</label><input class="input" type="email" value="${edit ? "budi@niaga.test" : ""}" placeholder="nama@perusahaan.com" required></div><div class="field"><label>Password ${edit ? '<span class="optional">(opsional)</span>' : ""}</label><input class="input" type="password" placeholder="${edit ? "Kosongkan jika tidak diubah" : "Minimal 8 karakter"}" ${edit ? "" : "required"}></div><div class="field"><label>Role</label><select class="select"><option ${edit ? "" : "selected"}>Pilih role</option><option>Admin</option><option ${edit ? "selected" : ""}>Sales</option><option>Purchasing</option><option>Finance</option><option>Warehouse</option></select></div><div class="field"><label>Status</label><select class="select"><option selected>Aktif</option><option>Nonaktif</option></select></div></div><div class="section-divider"><span>Target dan Komisi Sales</span></div><div class="form-grid"><div class="field"><label>Target Penjualan / Bulan</label><input class="input" type="number" value="${edit ? "50000000" : ""}" placeholder="0"></div><div class="field"><label>Tipe Komisi</label><select class="select"><option>Tanpa komisi</option><option ${edit ? "selected" : ""}>Persentase total penjualan</option><option>Nominal per barang</option></select></div><div class="field"><label>Nilai Komisi</label><input class="input" type="number" value="${edit ? "2" : ""}" placeholder="0"></div></div><div class="form-actions"><a class="btn btn-secondary" href="admin-users.html">Batal</a><button class="btn btn-primary" type="submit">${edit ? "Simpan Perubahan" : "Tambah Pengguna"}</button></div></form>`;
}
function profile() {
    return `<div class="profile-layout"><aside class="card panel profile-photo-card"><div class="profile-avatar">AN</div><h2>Admin Niaga</h2><p>Administrator</p><label class="btn btn-secondary file-button">Ganti Foto<input type="file" accept="image/*"></label><small>JPG atau PNG, maksimal 2 MB.</small></aside><form class="card panel form-card" onsubmit="event.preventDefault()"><div class="form-card-head"><div><h2>Informasi Profil</h2><p>Perbarui identitas dan keamanan akun Anda.</p></div><span class="me-badge">Me</span></div><div class="form-grid profile-fields"><div class="field"><label>Username</label><input class="input" value="admin.niaga" required></div><div class="field"><label>Email</label><input class="input" type="email" value="admin@niaga.test" required></div></div><div class="section-divider"><span>Ubah Password</span></div><div class="form-grid profile-fields"><div class="field"><label>Password Saat Ini</label><input class="input" type="password" placeholder="Masukkan password saat ini"></div><div class="field"><label>Password Baru</label><input class="input" type="password" placeholder="Minimal 8 karakter"></div><div class="field"><label>Konfirmasi Password Baru</label><input class="input" type="password" placeholder="Ulangi password baru"></div></div><div class="form-actions"><a class="btn btn-secondary" href="dashboard.html">Batal</a><button class="btn btn-primary" type="submit">Simpan Profil</button></div></form></div>`;
}
const content = {
    "dashboard.html": dashboard,
    "master-data.html": master,
    "product-form.html": productForm,
    "purchases.html": purchases,
    "purchase-form.html": purchaseForm,
    "sales.html": sales,
    "sales-form.html": salesForm,
    "sales-detail.html": salesDetail,
    "delivery-order.html": delivery,
    "invoices.html": invoices,
    "invoice-detail.html": invoiceDetail,
    "sales-returns.html": salesReturns,
    "return-form.html": returnForm,
    "receivables.html": receivables,
    "payment-form.html": paymentForm,
    "finance.html": finance,
    "finance-form.html": financeForm,
    "reports.html": reports,
    "assets.html": assets,
    "asset-form.html": assetForm,
    "admin-users.html": users,
    "profile.html": profile,
    "user-form.html": () => userForm(false),
    "user-edit.html": () => userForm(true),
};
const pageAction = () =>
    ({
        "master-data.html": `<a class="btn btn-primary" href="product-form.html">${icon("plus")}<span>Tambah Produk</span></a>`,
        "purchases.html": `<a class="btn btn-primary" href="purchase-form.html">${icon("plus")}<span>Tambah Pembelian</span></a>`,
        "sales.html": `<a class="btn btn-primary" href="sales-form.html">${icon("plus")}<span>Tambah Penjualan</span></a>`,
        "invoices.html": `<a class="btn btn-primary" href="invoice-detail.html">${icon("plus")}<span>Buat Invoice</span></a>`,
        "sales-returns.html": `<a class="btn btn-primary" href="return-form.html">${icon("plus")}<span>Tambah Retur</span></a>`,
        "receivables.html": `<a class="btn btn-primary" href="payment-form.html">${icon("plus")}<span>Catat Pembayaran</span></a>`,
        "finance.html": `<a class="btn btn-primary" href="finance-form.html">${icon("plus")}<span>Tambah Pengeluaran</span></a>`,
        "assets.html": `<a class="btn btn-primary" href="asset-form.html">${icon("plus")}<span>Tambah Aset</span></a>`,
        "admin-users.html": `<a class="btn btn-primary" href="user-form.html">${icon("plus")}<span>Tambah Pengguna</span></a>`,
    })[page] || "";
const meta = pages[page] || pages["dashboard.html"];
const centeredPageHeader =
    page.endsWith("-form.html") ||
    page === "user-edit.html" ||
    page === "profile.html";
document.body.innerHTML = `<div class="app-shell">${sidebar()}<div class="workspace"><header class="topbar"><div><div class="crumb">NiagaERP / ${meta[0]}</div></div><div class="topbar-right"><div class="live-clock"><span id="liveDate">Memuat tanggal...</span><div><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm-1 3h2v4.6l3.2 3.2-1.4 1.4-3.8-3.8V7Z"/></svg><b id="liveTime">00:00:00</b><small>WIB</small></div></div><div class="top-actions"><label class="top-search" aria-label="Pencarian global">${icon("search")}<input type="search" placeholder="Cari..."></label><button class="icon-button notification-button" aria-label="Notifikasi, lebih dari 100 belum dibaca">${icon("bell")}<span class="notification-badge">100+</span></button></div></div></header><main class="content"><div class="page-head ${centeredPageHeader ? "page-head-centered" : ""}"><div><p class="eyebrow">ERP Perusahaan Dagang</p><h1>${meta[0]}</h1><p>${meta[1]}</p></div>${pageAction()}</div>${(content[page] || dashboard)()}</main></div></div>`;

const appShell = document.querySelector?.(".app-shell");
const sidebarToggle = document.querySelector?.(".sidebar-toggle");
function setSidebarCollapsed(collapsed) {
    appShell?.classList.toggle("sidebar-collapsed", collapsed);
    if (sidebarToggle) {
        sidebarToggle.innerHTML = collapsed ? "&gt;&gt;" : "&lt;&lt;";
        sidebarToggle.setAttribute("aria-expanded", String(!collapsed));
        sidebarToggle.setAttribute(
            "aria-label",
            collapsed ? "Tampilkan sidebar" : "Sembunyikan sidebar",
        );
    }
}
sidebarToggle?.addEventListener("click", () => {
    setSidebarCollapsed(!appShell.classList.contains("sidebar-collapsed"));
});

function updateLiveClock() {
    const dateElement = document.getElementById?.("liveDate");
    const timeElement = document.getElementById?.("liveTime");
    if (!dateElement || !timeElement) return;
    const now = new Date();
    dateElement.textContent = new Intl.DateTimeFormat("id-ID", {
        weekday: "long",
        day: "2-digit",
        month: "long",
        year: "numeric",
        timeZone: "Asia/Jakarta",
    }).format(now);
    timeElement.textContent = new Intl.DateTimeFormat("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
        timeZone: "Asia/Jakarta",
    })
        .format(now)
        .replace(/\./g, ":");
}
updateLiveClock();
if (typeof setInterval === "function") setInterval(updateLiveClock, 1000);
function salesTabs(active) {
    return `<nav class='sales-tabs no-print' aria-label='Navigasi modul penjualan'><a class='${active === "orders" ? "active" : ""}' href='sales.html'>Quotation / PO</a><a class='${active === "delivery" ? "active" : ""}' href='delivery-order.html'>Delivery Order</a><a class='${active === "invoice" ? "active" : ""}' href='invoices.html'>Invoice</a><a class='${active === "return" ? "active" : ""}' href='sales-returns.html'>Retur</a><a class='${active === "receivable" ? "active" : ""}' href='receivables.html'>Piutang</a></nav>`;
}
function invoices() {
    return `${salesTabs("invoice")}<div class='grid stats'>${stat("Total Invoice", "28", "Periode Agustus")}${stat("Belum Lunas", "12", "Perlu ditagih", true)}${stat("Piutang Dagang", "Rp34,8 jt", "Outstanding customer", true)}${stat("Jatuh Tempo", "3 Invoice", "Dalam 7 hari", true)}</div><section id='piutang' style='margin-top:18px'>${table(["No. Invoice", "Referensi PO / DO", "Customer", "Jatuh Tempo", "Total", "Terbayar", "Sisa Piutang", "Status", "Aksi"], `<tr><td><b>INV-2026-0038</b></td><td>PO-2026-0045 / DO-0011</td><td>CV Maju Jaya</td><td>30 Agu 2026</td><td>Rp7.255.000</td><td>Rp5.000.000</td><td class='amount-negative'>Rp2.255.000</td><td>${badge("Sebagian", "orange")}</td><td><a class='table-link' href='invoice-detail.html'>Detail / Print</a></td></tr><tr><td><b>INV-2026-0037</b></td><td>PO-2026-0042 / Semua DO</td><td>PT Cipta Abadi</td><td>25 Agu 2026</td><td>Rp6.750.000</td><td>Rp0</td><td class='amount-negative'>Rp6.750.000</td><td>${badge("Belum Lunas", "red")}</td><td><a class='table-link' href='invoice-detail.html'>Detail / Print</a></td></tr><tr><td><b>INV-2026-0036</b></td><td>PO-2026-0040 / DO-0009</td><td>CV Sentosa</td><td>20 Agu 2026</td><td>Rp4.250.000</td><td>Rp4.250.000</td><td>Rp0</td><td>${badge("Lunas", "green")}</td><td><a class='table-link' href='invoice-detail.html'>Detail / Print</a></td></tr>`)}</section>`;
}
function invoiceDetail() {
    return `${salesTabs("invoice")}<section class='invoice-document card panel'><div class='invoice-toolbar no-print'><a class='btn btn-secondary' href='invoices.html'>Kembali</a><div><button class='btn btn-primary' onclick='window.print()'>Print Invoice</button><a class='btn btn-soft' href='payment-form.html'>Catat Pembayaran</a></div></div><header class='invoice-head'><div><span class='invoice-brand'>NIAGAERP</span><p>ERP Perusahaan Dagang</p></div><div class='invoice-title'><h2>INVOICE</h2>${badge("Sebagian Dibayar", "orange")}</div></header><div class='invoice-meta'><div><span>Ditagihkan kepada</span><h3>CV Maju Jaya</h3><p>Jl. Industri Raya No. 5<br>Jakarta Barat<br>finance@majujaya.co.id</p></div><dl><div><dt>No. Invoice</dt><dd>INV-2026-0038</dd></div><div><dt>Tanggal</dt><dd>20 Agustus 2026</dd></div><div><dt>Jatuh Tempo</dt><dd>30 Agustus 2026</dd></div><div><dt>Referensi</dt><dd>PO-2026-0045 / DO-2026-0011</dd></div></dl></div>${table(["Produk", "Qty", "Harga", "Diskon", "Subtotal"], `<tr><td><b>Kertas HVS A4 80gsm</b></td><td>60 rim</td><td>Rp58.000</td><td>5%</td><td>Rp3.306.000</td></tr><tr><td><b>Tinta Printer Black</b></td><td>12 pcs</td><td>Rp145.000</td><td>0%</td><td>Rp1.740.000</td></tr>`)}<div class='invoice-summary'><div><h3>Informasi Pembayaran</h3><p>Bank BCA · 1234567890<br>a.n. PT Niaga ERP Indonesia</p><small>Mohon cantumkan nomor invoice pada berita transfer.</small></div><dl><div><dt>Subtotal</dt><dd>Rp5.220.000</dd></div><div><dt>Diskon</dt><dd>-Rp174.000</dd></div><div><dt>Ongkir</dt><dd>Rp350.000</dd></div><div class='invoice-total'><dt>Total Invoice</dt><dd>Rp5.396.000</dd></div><div><dt>Sudah Dibayar</dt><dd>Rp3.000.000</dd></div><div class='invoice-due'><dt>Sisa Piutang</dt><dd>Rp2.396.000</dd></div></dl></div><footer class='invoice-note'><b>Terima kasih atas kepercayaan Anda.</b><span>Invoice dibuat berdasarkan pengiriman parsial DO-2026-0011.</span></footer></section>`;
}
function salesReturns(){return `${salesTabs('return')}<div class='grid stats'>${stat('Retur Bulan Ini','4','3 customer')}${stat('Menunggu Persetujuan','1','Perlu diperiksa',true)}${stat('Barang Kembali','26 Item','Layak jual dan rusak')}${stat('Nilai Penyesuaian','Rp3,45 jt','Potong piutang / refund')}</div><section style='margin-top:18px'>${table(['No. Retur','Referensi','Customer','Tanggal','Nilai','Alasan','Status','Aksi'],`<tr><td><b>RTJ-2026-0004</b></td><td>INV-0038 / DO-0011</td><td>CV Maju Jaya</td><td>20 Agu 2026</td><td>Rp580.000</td><td>Barang rusak</td><td>${badge('Menunggu','orange')}</td><td><a class='table-link' href='return-form.html'>Detail / Edit</a></td></tr><tr><td><b>RTJ-2026-0003</b></td><td>INV-0034 / DO-0008</td><td>PT Cipta Abadi</td><td>16 Agu 2026</td><td>Rp1.450.000</td><td>Salah produk</td><td>${badge('Barang Diterima','blue')}</td><td><a class='table-link' href='return-form.html'>Detail / Edit</a></td></tr><tr><td><b>RTJ-2026-0002</b></td><td>INV-0029 / DO-0006</td><td>CV Sentosa</td><td>8 Agu 2026</td><td>Rp1.420.000</td><td>Jumlah tidak sesuai</td><td>${badge('Selesai','green')}</td><td><a class='table-link' href='return-form.html'>Detail</a></td></tr>`)}</section>`}
function returnForm(){return `${salesTabs('return')}<form class='card panel form-card transaction-form' onsubmit="event.preventDefault();location.href='sales-returns.html'"><div class='form-card-head'><div><h2>Data Retur Penjualan</h2><p>Pilih dokumen asal agar barang dan nilai retur dapat ditelusuri.</p></div>${badge('Draft','slate')}</div><div class='form-grid'><div class='field'><label>Nomor Retur</label><input class='input' value='RTJ-2026-0005' required></div><div class='field'><label>Tanggal Retur</label><input class='input' type='date' value='2026-08-20' required></div><div class='field'><label>Invoice / Delivery Order Asal</label><select class='select' required><option>INV-2026-0038 / DO-2026-0011</option><option>INV-2026-0037 / DO-2026-0010</option></select></div><div class='field'><label>Customer</label><input class='input' value='CV Maju Jaya' readonly></div><div class='field'><label>Alasan Retur</label><select class='select'><option>Barang rusak</option><option>Salah produk</option><option>Jumlah tidak sesuai</option><option>Lainnya</option></select></div><div class='field'><label>Penyelesaian Keuangan</label><select class='select'><option>Kurangi piutang customer</option><option>Refund ke customer</option><option>Saldo kredit customer</option></select></div></div><div class='section-divider'><span>Barang yang Dikembalikan</span></div>${table(['Produk','Qty Dikirim','Qty Retur','Kondisi','Nilai Retur','Aksi'],`<tr><td>Kertas HVS A4 80gsm</td><td>60 rim</td><td><input class='input' type='number' min='1' max='60' value='10'></td><td><select class='select'><option>Rusak</option><option>Layak dijual kembali</option></select></td><td>Rp580.000</td><td><button class='btn btn-small btn-danger' type='button' onclick='removeItemRow(this)'>Delete</button></td></tr>`) }<div class='field form-wide'><label>Catatan Pemeriksaan</label><textarea class='input' rows='4' placeholder='Jelaskan kondisi barang dan hasil pemeriksaan gudang...'></textarea></div><div class='transaction-total'><span>Total Nilai Retur</span><b>Rp580.000</b></div><div class='form-actions'><a class='btn btn-secondary' href='sales-returns.html'>Batal</a><button class='btn btn-primary' type='submit'>Simpan Retur</button></div></form>`}
function receivables(){return `${salesTabs('receivable')}<div class='grid stats'>${stat('Total Piutang','Rp34,8 jt','12 invoice terbuka',true)}${stat('Belum Jatuh Tempo','Rp18,2 jt','6 invoice')}${stat('Terlambat 1–30 Hari','Rp10,1 jt','4 invoice',true)}${stat('Terlambat >30 Hari','Rp6,5 jt','2 invoice',true)}</div><section class='card panel receivable-guide' style='margin-top:18px'><div class='panel-title'><h2>Umur Piutang</h2><span class='muted'>Aging berdasarkan tanggal jatuh tempo</span></div><div class='aging-bar'><span style='--size:52%;--color:#3b82f6'>Belum jatuh tempo · 52%</span><span style='--size:29%;--color:#f59e0b'>1–30 hari · 29%</span><span style='--size:19%;--color:#ef4444'>&gt;30 hari · 19%</span></div></section><section style='margin-top:18px'>${table(['Customer','No. Invoice','Jatuh Tempo','Umur','Total','Terbayar','Sisa Piutang','Status','Aksi'],`<tr><td><b>CV Maju Jaya</b></td><td>INV-2026-0038</td><td>30 Agu 2026</td><td>Belum jatuh tempo</td><td>Rp5.396.000</td><td>Rp3.000.000</td><td class='amount-negative'>Rp2.396.000</td><td>${badge('Sebagian','orange')}</td><td><a class='table-link' href='payment-form.html'>Catat Bayar</a></td></tr><tr><td><b>PT Cipta Abadi</b></td><td>INV-2026-0037</td><td>15 Agu 2026</td><td>5 hari terlambat</td><td>Rp6.750.000</td><td>Rp0</td><td class='amount-negative'>Rp6.750.000</td><td>${badge('Jatuh Tempo','red')}</td><td><a class='table-link' href='payment-form.html'>Catat Bayar</a></td></tr><tr><td><b>CV Sentosa</b></td><td>INV-2026-0032</td><td>5 Jul 2026</td><td>46 hari terlambat</td><td>Rp4.250.000</td><td>Rp1.500.000</td><td class='amount-negative'>Rp2.750.000</td><td>${badge('Jatuh Tempo','red')}</td><td><a class='table-link' href='payment-form.html'>Catat Bayar</a></td></tr>`)}</section>`}
function paymentForm(){return `${salesTabs('receivable')}<form class='card panel form-card transaction-form compact-form' onsubmit="event.preventDefault();location.href='receivables.html'"><div class='form-card-head'><div><h2>Pembayaran Piutang Customer</h2><p>Pembayaran akan mengurangi sisa tagihan pada invoice terpilih.</p></div>${badge('Pembayaran Masuk','green')}</div><div class='form-grid'><div class='field'><label>Nomor Invoice</label><select class='select' required><option>INV-2026-0038</option><option>INV-2026-0037</option><option>INV-2026-0032</option></select></div><div class='field'><label>Customer</label><input class='input' value='CV Maju Jaya' readonly></div><div class='field'><label>Sisa Piutang</label><input class='input' value='Rp2.396.000' readonly></div><div class='field'><label>Tanggal Pembayaran</label><input class='input' type='date' value='2026-08-20' required></div><div class='field'><label>Nominal Pembayaran</label><div class='input-prefix'><span>Rp</span><input class='input' type='number' min='1' max='2396000' value='2396000' required></div></div><div class='field'><label>Masuk ke Akun</label><select class='select'><option>Bank BCA</option><option>Bank Mandiri</option><option>Bank BRI</option><option>Kas Kecil</option></select></div><div class='field'><label>Metode Pembayaran</label><select class='select'><option>Transfer Bank</option><option>Tunai</option><option>Giro</option></select></div><div class='field'><label>Nomor Referensi</label><input class='input' placeholder='Contoh: TRX-BCA-82910'></div><div class='field'><label>Bukti Pembayaran</label><input class='input' type='file' accept='image/*,.pdf'></div></div><div class='field form-wide'><label>Catatan</label><textarea class='input' rows='4' placeholder='Tambahkan keterangan pembayaran jika diperlukan...'></textarea></div><div class='form-actions'><a class='btn btn-secondary' href='receivables.html'>Batal</a><button class='btn btn-primary' type='submit'>Simpan Pembayaran</button></div></form>`}

