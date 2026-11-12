-- migracion-tests.sql — tests privados (solo miembros o invitados por el terapeuta)
-- Ejecutar UNA vez en phpMyAdmin > SQL.
ALTER TABLE consultas ADD COLUMN tests_asignados VARCHAR(120) NULL;

CREATE TABLE IF NOT EXISTS resultados_tests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  consulta_id INT NULL,
  test VARCHAR(30) NOT NULL,
  resumen TEXT NULL,
  datos LONGTEXT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usuario_test (usuario_id, test)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
