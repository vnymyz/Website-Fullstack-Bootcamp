package main

import (
	"log"

	"taskmanager/internal/config"
	"taskmanager/internal/database"
	"taskmanager/internal/handlers"
	"taskmanager/internal/repository"
	"taskmanager/internal/routes"
)

func main() {
	cfg, err := config.Load()
	if err != nil {
		log.Fatalf("config: %v", err)
	}

	db, err := database.NewMySQL(cfg.DSN())
	if err != nil {
		log.Fatalf("database: %v", err)
	}
	defer db.Close()
	log.Println("terhubung ke MySQL")

	userRepo := repository.NewUserRepository(db)
	taskRepo := repository.NewTaskRepository(db)

	authHandler := handlers.NewAuthHandler(userRepo, cfg.JWTSecret, cfg.JWTExpire)
	taskHandler := handlers.NewTaskHandler(taskRepo)

	router := routes.Setup(cfg, authHandler, taskHandler)

	log.Printf("server berjalan di http://localhost:%s", cfg.AppPort)
	if err := router.Run(":" + cfg.AppPort); err != nil {
		log.Fatalf("server: %v", err)
	}
}
