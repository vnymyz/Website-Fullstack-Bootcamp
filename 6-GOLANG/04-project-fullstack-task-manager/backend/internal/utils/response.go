package utils

import "github.com/gin-gonic/gin"

// Response adalah format JSON standar untuk semua endpoint.
type Response struct {
	Success bool   `json:"success"`
	Message string `json:"message"`
	Data    any    `json:"data,omitempty"`
}

// OK mengirim response sukses.
func OK(c *gin.Context, status int, message string, data any) {
	c.JSON(status, Response{Success: true, Message: message, Data: data})
}

// Fail mengirim response gagal.
func Fail(c *gin.Context, status int, message string) {
	c.JSON(status, Response{Success: false, Message: message})
}
