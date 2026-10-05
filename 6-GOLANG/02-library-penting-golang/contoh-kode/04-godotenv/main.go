package main

import (
	"fmt"
	"log"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	AppName string
	Port    string
	Debug   bool
	MaxUser int
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}

func Load() (*Config, error) {
	// Membaca file .env di folder kerja saat ini. Jika tidak ada, tidak masalah:
	// di server biasanya env diatur langsung oleh sistem.
	if err := godotenv.Load(); err != nil {
		log.Println("file .env tidak ditemukan, memakai environment sistem")
	}

	debug, err := strconv.ParseBool(getEnv("DEBUG", "false"))
	if err != nil {
		return nil, fmt.Errorf("DEBUG tidak valid: %w", err)
	}
	maxUser, err := strconv.Atoi(getEnv("MAX_USER", "10"))
	if err != nil {
		return nil, fmt.Errorf("MAX_USER tidak valid: %w", err)
	}

	return &Config{
		AppName: getEnv("APP_NAME", "Aplikasi"),
		Port:    getEnv("APP_PORT", "8080"),
		Debug:   debug,
		MaxUser: maxUser,
	}, nil
}

func main() {
	cfg, err := Load()
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("%+v\n", *cfg)
}
