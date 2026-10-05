package main

import (
	"net/http"
	"time"

	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
)

func main() {
	r := gin.Default()

	r.Use(cors.New(cors.Config{
		// Hanya origin ini yang diizinkan. Hindari "*" di produksi bila memakai credentials.
		AllowOrigins:     []string{"http://localhost:5173", "http://localhost:3000"},
		AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowHeaders:     []string{"Origin", "Content-Type", "Authorization"},
		ExposeHeaders:    []string{"Content-Length"},
		AllowCredentials: true,
		MaxAge:           12 * time.Hour, // browser menyimpan hasil preflight
	}))

	r.GET("/data", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "Data dari backend Go"})
	})

	r.Run(":8080")
}
