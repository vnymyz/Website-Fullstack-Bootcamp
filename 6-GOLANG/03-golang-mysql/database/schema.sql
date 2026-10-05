-- Database latihan materi 3
CREATE DATABASE IF NOT EXISTS belajar_go
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE belajar_go;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)    NOT NULL,
  price       DECIMAL(12,2)   NOT NULL,
  stock       INT             NOT NULL DEFAULT 0,
  description TEXT            NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS accounts;
CREATE TABLE accounts (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner   VARCHAR(100)    NOT NULL,
  balance DECIMAL(14,2)   NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT chk_balance CHECK (balance >= 0)
) ENGINE=InnoDB;
