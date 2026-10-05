package routes

import (
	"net/http"
	"strings"
	"time"

	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"

	"taskmanager/internal/config"
	"taskmanager/internal/handlers"
	"taskmanager/internal/middleware"
	"taskmanager/internal/utils"
)

// Setup membuat router Gin lengkap dengan CORS dan semua endpoint.
func Setup(cfg *config.Config, auth *handlers.AuthHandler, tasks *handlers.TaskHandler) *gin.Engine {
	if cfg.AppEnv == "production" {
		gin.SetMode(gin.ReleaseMode)
	}
	r := gin.Default()

	r.Use(cors.New(cors.Config{
		AllowOrigins:     strings.Split(cfg.CORSOrigin, ","),
		AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowHeaders:     []string{"Origin", "Content-Type", "Authorization"},
		AllowCredentials: true,
		MaxAge:           12 * time.Hour,
	}))

	api := r.Group("/api")
	{
		api.GET("/health", func(c *gin.Context) {
			utils.OK(c, http.StatusOK, "server berjalan", gin.H{"time": time.Now()})
		})

		authGroup := api.Group("/auth")
		{
			authGroup.POST("/register", auth.Register)
			authGroup.POST("/login", auth.Login)
			authGroup.GET("/me", middleware.Auth(cfg.JWTSecret), auth.Me)
		}

		taskGroup := api.Group("/tasks", middleware.Auth(cfg.JWTSecret))
		{
			taskGroup.GET("", tasks.List)
			taskGroup.POST("", tasks.Create)
			taskGroup.GET("/:id", tasks.Get)
			taskGroup.PUT("/:id", tasks.Update)
			taskGroup.DELETE("/:id", tasks.Delete)
		}
	}

	r.NoRoute(func(c *gin.Context) {
		utils.Fail(c, http.StatusNotFound, "endpoint tidak ditemukan")
	})
	return r
}
