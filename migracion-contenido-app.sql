-- =========================================================
-- Migración: nuevos tipos de contenido para la app (PWA)
-- meditaciones, respiración, recetas y rutinas de alimentación
-- Ejecutar en phpMyAdmin → tu base de datos → pestaña SQL
-- =========================================================

ALTER TABLE contenido_valor
  MODIFY COLUMN tipo ENUM('taller','libro','descuento','meditacion','respiracion','receta','rutina') NOT NULL;

-- Opcional: duración en minutos, útil para meditaciones y rutinas de audio/video.
ALTER TABLE contenido_valor ADD COLUMN duracion_min INT NULL;
