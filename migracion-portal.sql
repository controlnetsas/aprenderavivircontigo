-- migracion-portal.sql — enlaces de evaluación temporales para el portal del terapeuta
-- Ejecutar UNA vez en phpMyAdmin > SQL (requisito: migracion-evaluacion.sql ya corrida).

ALTER TABLE enlaces_evaluacion
  ADD COLUMN nombre_destino VARCHAR(150) NULL,
  ADD COLUMN correo_destino VARCHAR(190) NULL,
  ADD COLUMN celular_destino VARCHAR(30) NULL,
  ADD COLUMN expira_en DATETIME NULL,
  ADD COLUMN usos_max INT NOT NULL DEFAULT 1,
  ADD COLUMN usos INT NOT NULL DEFAULT 0;

-- Los enlaces que ya estaban activos pasan a vencer en 7 días (los enlaces sin fecha dejan de existir).
UPDATE enlaces_evaluacion
   SET expira_en = DATE_ADD(NOW(), INTERVAL 7 DAY), usos_max = 50
 WHERE activo = 1 AND expira_en IS NULL;
