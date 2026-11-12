-- migracion-tests-eval.sql — el terapeuta elige qué tests envía en cada evaluación
-- Ejecutar UNA vez en phpMyAdmin > SQL (requisito: migracion-portal.sql ya corrida).

ALTER TABLE enlaces_evaluacion ADD COLUMN tests VARCHAR(100) NOT NULL DEFAULT 'genealogia,eneatipo,herida';
ALTER TABLE evaluaciones       ADD COLUMN tests VARCHAR(100) NOT NULL DEFAULT 'genealogia,eneatipo,herida';
