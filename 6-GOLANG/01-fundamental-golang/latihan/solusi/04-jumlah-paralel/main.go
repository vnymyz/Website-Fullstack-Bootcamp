package main

import (
	"fmt"
	"sync"
)

// JumlahParalel membagi slice menjadi beberapa bagian dan menjumlahkannya di goroutine terpisah.
func JumlahParalel(data []int, bagian int) int {
	if bagian < 1 {
		bagian = 1
	}
	ukuran := (len(data) + bagian - 1) / bagian
	hasil := make(chan int, bagian)

	var wg sync.WaitGroup
	for awal := 0; awal < len(data); awal += ukuran {
		akhir := min(awal+ukuran, len(data))
		wg.Add(1)
		go func(potongan []int) {
			defer wg.Done()
			total := 0
			for _, n := range potongan {
				total += n
			}
			hasil <- total
		}(data[awal:akhir])
	}

	wg.Wait()
	close(hasil)

	total := 0
	for h := range hasil {
		total += h
	}
	return total
}

func main() {
	data := make([]int, 1000)
	for i := range data {
		data[i] = i + 1
	}
	fmt.Println("Jumlah 1..1000 =", JumlahParalel(data, 4)) // 500500
}
