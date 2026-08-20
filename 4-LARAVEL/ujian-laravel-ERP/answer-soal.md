# Kunci Jawaban — Ujian ERP Perusahaan Dagang

> Khusus pengajar. Jangan dibagikan bersama soal siswa.

1. MVC: route mengarahkan request ke Controller, Controller memanggil Model/relasi lalu mengirim data ke View. Contoh: SaleController menyimpan PO melalui Sale dan SaleItem lalu redirect ke halaman detail.
2. Route statis memiliki path tetap; dinamis memiliki parameter. Statis harus sebelum dinamis agar `create` tidak dibaca sebagai ID/nomor transaksi.
3. `@extends` memakai layout, `@yield` adalah slot pada layout, dan `@section` mengisi slot tersebut.
4. Blade hanya presentasi. Total dan stok adalah business rule yang harus berada pada Controller/service/Model agar aman dan testable.
5. `$fillable` adalah whitelist mass assignment. `role`/status tidak boleh dapat diisi sembarang request karena bisa menaikkan hak akses atau memalsukan status transaksi.
6. Migration memberi version control struktur DB. Foreign key menjaga SaleItem selalu terkait Sale yang valid.
7. PurchaseItem `belongsTo` Purchase/Product; Purchase `hasMany` PurchaseItem.
8. Authentication menjawab siapa user (login). Authorization menjawab apa yang boleh dilakukan (finance boleh expense, sales tidak boleh kelola user).
9. Middleware menyaring request sebelum Controller. Alias role dipasang pada route Asset Management untuk menolak role tidak sah dengan 403.
10. User masih dapat memanggil URL/API langsung. Proteksi wajib ada di middleware, policy, dan/atau Form Request.
11. Route model binding membuat Laravel mencari Model otomatis. `{sale}` memakai id default; `{sale:number}` memakai nomor unik.
12. Pengiriman membuat beberapa perubahan yang harus sukses semua: DO, item DO, stok, dan status PO. Transaction mencegah data setengah tersimpan ketika error.
13. `when()` menambahkan kondisi hanya bila input search ada. GET cocok untuk membaca/filter karena query bisa dibagikan dan tidak mengubah data.
14. Pagination membatasi data per halaman sehingga lebih ringan. `withQueryString()` mempertahankan filter ketika pindah halaman.
15. Form Request memisahkan validasi/otorisasi, mengurangi duplikasi, dan membuat Controller fokus pada alur bisnis.
16. Policy adalah aturan terpusat per Model. Misal hanya pembuat/manager tertentu boleh membatalkan Sale yang belum selesai.
17. Web route biasanya memakai session dan return HTML; API route biasanya return JSON untuk client lain.
18. Login API memvalidasi user lalu membuat token. Client mengirim `Authorization: Bearer <token>` pada route `auth:sanctum`.
19. `RefreshDatabase` membuat database test bersih tiap test. SQLite memory cepat dan tidak mencampur/menghapus MySQL development.
20. Factory adalah template data dummy; Seeder menjalankan pembuatan data. Transaksi membutuhkan master data yang sudah ada agar foreign key valid.
