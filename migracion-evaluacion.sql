-- migracion-evaluacion.sql — evaluación inicial por enlace + recuperación de contraseña
-- Ejecutar UNA vez en phpMyAdmin > SQL. REQUISITO: haber corrido migracion-tests.sql.

CREATE TABLE IF NOT EXISTS enlaces_evaluacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  terapeuta_usuario_id INT NOT NULL,
  token CHAR(64) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS evaluaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  terapeuta_usuario_id INT NOT NULL,
  enlace_id INT NULL,
  edad TINYINT UNSIGNED NULL,
  sexo VARCHAR(12) NULL,
  sintomas TEXT NULL,
  situacion_sentimental VARCHAR(190) NULL,
  hijos TEXT NULL,
  hijos_no_nacidos TEXT NULL,
  estado_salud TEXT NULL,
  trabajo_actual VARCHAR(190) NULL,
  profesion VARCHAR(190) NULL,
  conflictos_emocionales VARCHAR(255) NULL,
  conflicto_descripcion TEXT NULL,
  heridas_infancia VARCHAR(120) NULL,
  ficha_completa TINYINT(1) NOT NULL DEFAULT 0,
  estado VARCHAR(20) NOT NULL DEFAULT 'registrada',
  informe MEDIUMTEXT NULL,
  consentimiento_en DATETIME NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completada_en DATETIME NULL,
  UNIQUE KEY uq_usuario (usuario_id),
  INDEX idx_terapeuta (terapeuta_usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE usuarios ADD COLUMN token_reset VARCHAR(64) NULL;
ALTER TABLE usuarios ADD COLUMN token_reset_expira DATETIME NULL;
