// ================================================
// LESSON 1: preventDefault()
// Default browser: submit form → reload halaman
// e.preventDefault() → stop reload, biar JS yang atur
// ================================================

const formDemo1 = document.querySelector("#form-demo1");

formDemo1.addEventListener("submit", function (e) {
  e.preventDefault(); // ← ini kuncinya, tanpa ini halaman reload

  const nilai = document.querySelector("#input-demo1").value;
  const hasil = document.querySelector("#hasil-demo1");

  hasil.textContent = "Kamu ketik: " + nilai + " (halaman TIDAK reload!)";
  hasil.className = "pesan-sukses";
});

// ================================================
// LESSON 2: event "submit" di form, bukan "click" di button
// Enak-nya: Enter di keyboard juga jalan otomatis
// ================================================

const formLogin = document.querySelector("#form-login");

formLogin.addEventListener("submit", function (e) {
  e.preventDefault(); // selalu preventDefault dulu di awal function submit

  const username = document.querySelector("#input-username2").value.trim();
  const password = document.querySelector("#input-password2").value;
  const pesan = document.querySelector("#pesan-login");

  if (username === "" || password === "") {
    pesan.textContent = "❌ Username dan password harus diisi!";
    pesan.className = "pesan-error";
    return;
  }

  pesan.textContent = "✅ Login berhasil! Halo, " + username + "!";
  pesan.className = "pesan-sukses";
});

// ================================================
// LESSON 3: fetch() dasar
// fetch(url) → kirim request ke server
// .then() → jalan kalau response sudah datang
// response.json() → ubah response jadi object JS yang bisa dipakai
// ================================================

const btnFakta = document.querySelector("#btn-fakta");
const hasilFakta = document.querySelector("#hasil-fakta");

btnFakta.addEventListener("click", function () {
  hasilFakta.textContent = "Loading...";
  hasilFakta.className = "pesan-loading";

  fetch("https://catfact.ninja/fact")
    .then((response) => response.json()) // ubah response mentah jadi object
    .then((data) => {
      // data = object hasil dari API, punya property "fact"
      hasilFakta.textContent = "🐱 " + data.fact;
      hasilFakta.className = "pesan-sukses";
    })
    .catch((error) => {
      // kalau internet mati / server down, ini yang jalan
      hasilFakta.textContent = "Gagal ambil data. Coba lagi.";
      hasilFakta.className = "pesan-error";
      console.log("Error:", error);
    });
});

// ================================================
// LESSON 4: fetch() pakai async/await + try/catch
// async/await = cara lain nulis fetch, lebih gampang dibaca dari atas ke bawah
// try/catch = tangkap error biar website tidak "diam" kalau gagal
// ================================================

const btnAdvice = document.querySelector("#btn-advice");
const hasilAdvice = document.querySelector("#hasil-advice");

async function ambilSaran() {
  hasilAdvice.textContent = "Loading...";
  hasilAdvice.className = "pesan-loading";

  try {
    // await = "tunggu dulu sampai selesai" sebelum lanjut baris berikutnya
    const response = await fetch("https://api.adviceslip.com/advice");
    const data = await response.json();

    hasilAdvice.textContent = "💡 " + data.slip.advice;
    hasilAdvice.className = "pesan-sukses";
  } catch (error) {
    hasilAdvice.textContent = "Gagal ambil saran. Coba lagi.";
    hasilAdvice.className = "pesan-error";
    console.log("Error:", error);
  }
}

btnAdvice.addEventListener("click", ambilSaran);
