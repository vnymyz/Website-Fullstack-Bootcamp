package main

import (
	"errors"
	"fmt"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
)

type DaftarRequest struct {
	Nama     string `json:"nama" binding:"required,min=3,max=50"`
	Email    string `json:"email" binding:"required,email"`
	Umur     int    `json:"umur" binding:"required,gte=17,lte=100"`
	Password string `json:"password" binding:"required,min=8"`
	Konfirm  string `json:"konfirmasi" binding:"required,eqfield=Password"`
	Role     string `json:"role" binding:"omitempty,oneof=siswa guru"`
	Website  string `json:"website" binding:"omitempty,url"`
}

// pesanError menerjemahkan error validator menjadi pesan yang ramah (Bahasa Indonesia).
func pesanError(err error) map[string]string {
	hasil := map[string]string{}
	var ve validator.ValidationErrors
	if errors.As(err, &ve) {
		for _, fe := range ve {
			var pesan string
			switch fe.Tag() {
			case "required":
				pesan = "wajib diisi"
			case "min":
				pesan = fmt.Sprintf("minimal %s karakter/nilai", fe.Param())
			case "max":
				pesan = fmt.Sprintf("maksimal %s karakter/nilai", fe.Param())
			case "email":
				pesan = "format email tidak valid"
			case "gte":
				pesan = fmt.Sprintf("minimal %s", fe.Param())
			case "lte":
				pesan = fmt.Sprintf("maksimal %s", fe.Param())
			case "eqfield":
				pesan = "harus sama dengan " + fe.Param()
			case "oneof":
				pesan = "harus salah satu dari: " + fe.Param()
			default:
				pesan = "tidak valid (" + fe.Tag() + ")"
			}
			hasil[fe.Field()] = pesan
		}
		return hasil
	}
	hasil["body"] = "format JSON tidak valid"
	return hasil
}

func main() {
	r := gin.Default()

	r.POST("/daftar", func(c *gin.Context) {
		var req DaftarRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"errors": pesanError(err)})
			return
		}
		c.JSON(http.StatusCreated, gin.H{"pesan": "pendaftaran berhasil", "nama": req.Nama})
	})

	r.Run(":8080")
}
