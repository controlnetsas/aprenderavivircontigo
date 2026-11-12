-- migracion-seguimiento.sql — historial de informes y notas de seguimiento por consultante
-- Ejecutar UNA vez en phpMyAdmin > SQL (después de migracion-evaluacion.sql).
CREATE TABLE IF NOT EXISTS informes_evaluacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  evaluacion_id INT NOT NULL,
  usuario_id INT NOT NULL,
  informe MEDIUMTEXT NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notas_seguimiento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  terapeuta_usuario_id INT NOT NULL,
  nota TEXT NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
