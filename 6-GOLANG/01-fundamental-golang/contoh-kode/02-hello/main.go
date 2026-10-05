// Setiap file Go diawali deklarasi package.
// "package main" = program yang bisa dijalankan (executable).
package main

// import: memanggil paket lain. "fmt" = format & cetak teks.
import "fmt"

// main() adalah titik masuk program. Go mulai menjalankan dari sini.
func main() {
	fmt.Println("Halo, Dunia!")
	fmt.Println("Selamat datang di kelas Golang")

	// Printf memakai "verb": %s teks, %d bilangan bulat, %f desimal, \n baris baru
	nama := "Budi"
	umur := 20
	fmt.Printf("Nama saya %s, umur %d tahun\n", nama, umur)
}
