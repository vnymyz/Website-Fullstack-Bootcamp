package main

import (
	"log"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
)

type Catatan struct {
	ID    int    `json:"id"`
	Judul string `json:"judul"`
	Isi   string `json:"isi"`
}

type CatatanRequest struct {
	Judul string `json:"judul" binding:"required,min=3,max=100"`
	Isi   string `json:"isi" binding:"required"`
}

var (
	catatan = []Catatan{{1, "Belanja", "Beli susu"}}
	nextID  = 2
)

// Middleware: mencatat waktu eksekusi tiap request.
func Logger() gin.HandlerFunc {
	return func(c *gin.Context) {
		mulai := time.Now()
		c.Next() // jalankan handler berikutnya
		log.Printf("%s %s -> %d (%v)", c.Request.Method, c.Request.URL.Path, c.Writer.Status(), time.Since(mulai))
	}
}

// Middleware: contoh proteksi sederhana dengan API key di header.
func ApiKey(key string) gin.HandlerFunc {
	return func(c *gin.Context) {
		if c.GetHeader("X-API-Key") != key {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "API key salah"})
			return
		}
		c.Next()
	}
}

func main() {
	r := gin.New() // tanpa middleware bawaan
	r.Use(gin.Recovery(), Logger())

	r.GET("/ping", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "pong"})
	})

	// Path param dan query string
	r.GET("/salam/:nama", func(c *gin.Context) {
		nama := c.Param("nama")
		bahasa := c.DefaultQuery("bahasa", "id") // /salam/Budi?bahasa=en
		if bahasa == "en" {
			c.JSON(http.StatusOK, gin.H{"pesan": "Hello, " + nama})
			return
		}
		c.JSON(http.StatusOK, gin.H{"pesan": "Halo, " + nama})
	})

	// Group route
	api := r.Group("/api/v1")
	{
		api.GET("/catatan", func(c *gin.Context) {
			c.JSON(http.StatusOK, catatan)
		})

		api.GET("/catatan/:id", func(c *gin.Context) {
			id, err := strconv.Atoi(c.Param("id"))
			if err != nil {
				c.JSON(http.StatusBadRequest, gin.H{"error": "id harus angka"})
				return
			}
			for _, n := range catatan {
				if n.ID == id {
					c.JSON(http.StatusOK, n)
					return
				}
			}
			c.JSON(http.StatusNotFound, gin.H{"error": "tidak ditemukan"})
		})

		// Group dengan middleware: hanya yang punya API key
		aman := api.Group("", ApiKey("rahasia123"))
		aman.POST("/catatan", func(c *gin.Context) {
			var req CatatanRequest
			if err := c.ShouldBindJSON(&req); err != nil {
				c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
				return
			}
			n := Catatan{ID: nextID, Judul: req.Judul, Isi: req.Isi}
			nextID++
			catatan = append(catatan, n)
			c.JSON(http.StatusCreated, n)
		})
	}

	log.Fatal(r.Run(":8080"))
}
