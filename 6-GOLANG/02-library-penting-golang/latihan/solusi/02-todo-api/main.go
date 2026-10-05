package main

import (
	"log"
	"net/http"
	"strconv"
	"sync"

	"github.com/gin-gonic/gin"
)

type Todo struct {
	ID      int    `json:"id"`
	Judul   string `json:"judul"`
	Selesai bool   `json:"selesai"`
}

type TodoRequest struct {
	Judul   string `json:"judul" binding:"required,min=3,max=100"`
	Selesai bool   `json:"selesai"`
}

type Store struct {
	mu     sync.Mutex
	items  map[int]Todo
	nextID int
}

func NewStore() *Store {
	return &Store{items: map[int]Todo{}, nextID: 1}
}

func SetupRouter(s *Store) *gin.Engine {
	gin.SetMode(gin.TestMode)
	r := gin.New()
	r.Use(gin.Recovery())

	r.GET("/todos", func(c *gin.Context) {
		s.mu.Lock()
		defer s.mu.Unlock()
		out := make([]Todo, 0, len(s.items))
		for id := 1; id < s.nextID; id++ {
			if t, ok := s.items[id]; ok {
				out = append(out, t)
			}
		}
		c.JSON(http.StatusOK, out)
	})

	r.POST("/todos", func(c *gin.Context) {
		var req TodoRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
			return
		}
		s.mu.Lock()
		defer s.mu.Unlock()
		t := Todo{ID: s.nextID, Judul: req.Judul, Selesai: req.Selesai}
		s.items[t.ID] = t
		s.nextID++
		c.JSON(http.StatusCreated, t)
	})

	r.PUT("/todos/:id", func(c *gin.Context) {
		id, err := strconv.Atoi(c.Param("id"))
		if err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": "id harus angka"})
			return
		}
		var req TodoRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
			return
		}
		s.mu.Lock()
		defer s.mu.Unlock()
		if _, ok := s.items[id]; !ok {
			c.JSON(http.StatusNotFound, gin.H{"error": "todo tidak ditemukan"})
			return
		}
		t := Todo{ID: id, Judul: req.Judul, Selesai: req.Selesai}
		s.items[id] = t
		c.JSON(http.StatusOK, t)
	})

	r.DELETE("/todos/:id", func(c *gin.Context) {
		id, err := strconv.Atoi(c.Param("id"))
		if err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": "id harus angka"})
			return
		}
		s.mu.Lock()
		defer s.mu.Unlock()
		if _, ok := s.items[id]; !ok {
			c.JSON(http.StatusNotFound, gin.H{"error": "todo tidak ditemukan"})
			return
		}
		delete(s.items, id)
		c.Status(http.StatusNoContent)
	})

	return r
}

func main() {
	log.Fatal(SetupRouter(NewStore()).Run(":8080"))
}
