-- =========================================================
-- Migración: verificación de correo con link de confirmación
-- Ejecutar en phpMyAdmin → tu base de datos → pestaña SQL
-- =========================================================

ALTER TABLE usuarios ADD COLUMN email_verificado TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE usuarios ADD COLUMN token_verificacion VARCHAR(64) NULL;
