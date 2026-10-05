package middleware

import (
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"

	"taskmanager/internal/utils"
)

// ContextUserID adalah kunci untuk menyimpan ID user di gin.Context.
const ContextUserID = "userID"

// Auth memverifikasi header "Authorization: Bearer <token>".
func Auth(secret string) gin.HandlerFunc {
	return func(c *gin.Context) {
		header := c.GetHeader("Authorization")
		parts := strings.SplitN(header, " ", 2)
		if len(parts) != 2 || !strings.EqualFold(parts[0], "Bearer") {
			utils.Fail(c, http.StatusUnauthorized, "token tidak ditemukan")
			c.Abort()
			return
		}

		claims, err := utils.ParseToken(parts[1], secret)
		if err != nil {
			utils.Fail(c, http.StatusUnauthorized, "token tidak valid atau kedaluwarsa")
			c.Abort()
			return
		}

		c.Set(ContextUserID, claims.UserID)
		c.Next()
	}
}

// UserID mengambil ID user yang sudah diset oleh middleware Auth.
func UserID(c *gin.Context) int64 {
	return c.GetInt64(ContextUserID)
}
