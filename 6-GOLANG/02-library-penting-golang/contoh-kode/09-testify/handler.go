package handler

import (
	"net/http"

	"github.com/gin-gonic/gin"
)

// Tambah adalah fungsi murni yang akan kita uji.
func Tambah(a, b int) int { return a + b }

type HitungRequest struct {
	A int `json:"a"`
	B int `json:"b" binding:"required"`
}

// SetupRouter dipisahkan dari main agar bisa dipakai di test.
func SetupRouter() *gin.Engine {
	gin.SetMode(gin.TestMode)
	r := gin.New()

	r.GET("/ping", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "pong"})
	})

	r.POST("/tambah", func(c *gin.Context) {
		var req HitungRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": "input tidak valid"})
			return
		}
		c.JSON(http.StatusOK, gin.H{"hasil": Tambah(req.A, req.B)})
	})
	return r
}
