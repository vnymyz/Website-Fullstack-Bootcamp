package main

import "fmt"

func prima(n int) bool {
	if n < 2 {
		return false
	}
	for i := 2; i*i <= n; i++ {
		if n%i == 0 {
			return false
		}
	}
	return true
}

func main() {
	var hasil []int
	for i := 1; i < 100; i++ {
		if prima(i) {
			hasil = append(hasil, i)
		}
	}
	fmt.Println(hasil)
	fmt.Println("jumlah:", len(hasil))
}
