
-- Base de datos: sitio_salud

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE sitio_salud;

-- Limpiar datos previos para poder ejecutar el script varias veces
DELETE FROM rutinas_pacientes;
DELETE FROM rutina_ejercicios;
DELETE FROM rutinas;
DELETE FROM dietas_pacientes;
DELETE FROM dietas;
DELETE FROM citas;
DELETE FROM pacientes_entrenadores;
DELETE FROM pacientes_nutriologos;
DELETE FROM datos_salud_actuales;
DELETE FROM historial_salud;
DELETE FROM datos_salud;
DELETE FROM personas;

ALTER TABLE personas AUTO_INCREMENT = 1;
ALTER TABLE datos_salud AUTO_INCREMENT = 1;
ALTER TABLE historial_salud AUTO_INCREMENT = 1;
ALTER TABLE datos_salud_actuales AUTO_INCREMENT = 1;
ALTER TABLE pacientes_nutriologos AUTO_INCREMENT = 1;
ALTER TABLE pacientes_entrenadores AUTO_INCREMENT = 1;
ALTER TABLE citas AUTO_INCREMENT = 1;
ALTER TABLE dietas AUTO_INCREMENT = 1;
ALTER TABLE dietas_pacientes AUTO_INCREMENT = 1;
ALTER TABLE rutinas AUTO_INCREMENT = 1;
ALTER TABLE rutina_ejercicios AUTO_INCREMENT = 1;
ALTER TABLE rutinas_pacientes AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

START TRANSACTION;

-- =========================================================
-- 1. Personas: pacientes, nutriólogos y entrenadores
-- =========================================================
INSERT INTO personas
(id, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, telefono, email, direccion, usuario, password_hash, rol, activo, fecha_registro)
VALUES
(1, 'Roberto', 'Martínez', 'López', '2001-05-14', 'Masculino', '5512345678', 'roberto.paciente@nutrihealth.com', 'Av. Central 120, Ecatepec, Estado de México', 'roberto_paciente', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'paciente', TRUE, '2026-05-10 09:00:00'),
(2, 'Mariana', 'Gómez', 'Torres', '1998-09-20', 'Femenino', '5522223344', 'mariana.paciente@nutrihealth.com', 'Calle Norte 45, Ciudad de México', 'mariana_paciente', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'paciente', TRUE, '2026-05-11 10:30:00'),
(3, 'Luis', 'Hernández', 'Vega', '1995-03-08', 'Masculino', '5533334455', 'luis.paciente@nutrihealth.com', 'Calle Reforma 77, Nezahualcóyotl, Estado de México', 'luis_paciente', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'paciente', TRUE, '2026-05-12 12:15:00'),
(4, 'Valeria', 'Sánchez', 'Ruiz', '2003-01-30', 'Femenino', '5544445566', 'valeria.paciente@nutrihealth.com', 'Calle Sur 88, Tlalnepantla, Estado de México', 'valeria_paciente', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'paciente', TRUE, '2026-05-13 14:20:00'),
(5, 'Sergio', 'Ramírez', 'Cruz', '1989-11-03', 'Masculino', '5555556677', 'sergio.paciente@nutrihealth.com', 'Av. Jardines 91, Gustavo A. Madero, Ciudad de México', 'sergio_paciente', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'paciente', TRUE, '2026-05-14 16:45:00'),

(10, 'Ana', 'Pérez', 'Nava', '1987-02-15', 'Femenino', '5566667788', 'ana.nutriologa@nutrihealth.com', 'Consultorio NutriHealth Norte', 'ana_nutriologa', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'nutriologo', TRUE, '2026-05-01 08:00:00'),
(11, 'Carlos', 'Mendoza', 'Pineda', '1984-07-22', 'Masculino', '5577778899', 'carlos.nutriologo@nutrihealth.com', 'Consultorio NutriHealth Centro', 'carlos_nutriologo', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'nutriologo', TRUE, '2026-05-01 08:30:00'),

(20, 'Diego', 'Castillo', 'Morales', '1990-04-18', 'Masculino', '5588889900', 'diego.entrenador@nutrihealth.com', 'Gimnasio NutriHealth Norte', 'diego_entrenador', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'entrenador', TRUE, '2026-05-02 09:00:00'),
(21, 'Laura', 'Flores', 'Aguilar', '1992-12-09', 'Femenino', '5599990011', 'laura.entrenadora@nutrihealth.com', 'Gimnasio NutriHealth Centro', 'laura_entrenadora', '$2y$12$UHbsZISQljnMgafrAJ6fFuFUMAo6R54TeHw.auuQJ35Ekxrk/xcUq', 'entrenador', TRUE, '2026-05-02 09:30:00');

-- =========================================================
-- 2. Datos de salud iniciales de pacientes
--    datos_salud.paciente_id -> personas.id
-- =========================================================
INSERT INTO datos_salud
(id, paciente_id, peso, estatura, edad, enfermedades, alergias, medicamentos, objetivo_salud, nivel_actividad, fecha_registro)
VALUES
(1, 1, 78.40, 1.74, 25, 'Ninguna enfermedad crónica reportada', 'Ninguna', 'Ninguno', 'Bajar grasa corporal y mejorar condición física', 'Moderado', '2026-05-10 09:20:00'),
(2, 2, 64.20, 1.62, 27, 'Gastritis ocasional', 'Lácteos enteros', 'Omeprazol ocasional', 'Mejorar digestión y mantener peso saludable', 'Bajo', '2026-05-11 10:50:00'),
(3, 3, 91.00, 1.80, 31, 'Hipertensión controlada', 'Ninguna', 'Losartán', 'Reducir peso y presión arterial', 'Bajo', '2026-05-12 12:35:00'),
(4, 4, 55.30, 1.60, 23, 'Ninguna enfermedad crónica reportada', 'Cacahuate', 'Ninguno', 'Ganar masa muscular de forma saludable', 'Alto', '2026-05-13 14:40:00'),
(5, 5, 84.70, 1.69, 36, 'Colesterol elevado', 'Mariscos', 'Atorvastatina', 'Mejorar composición corporal y reducir colesterol', 'Moderado', '2026-05-14 17:05:00');

-- =========================================================
-- 3. Historial de salud / progreso
--    historial_salud.paciente_id -> personas.id
-- =========================================================
INSERT INTO historial_salud
(id, paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, observaciones, fecha_registro)
VALUES
(1, 1, 80.10, 26.40, 34.20, 91.00, 99.00, 'Registro inicial. Se recomienda déficit calórico moderado y caminata diaria.', '2026-05-10 09:30:00'),
(2, 1, 79.00, 25.70, 34.60, 89.50, 98.00, 'Primer avance: mejor adherencia a dieta y ligera reducción de cintura.', '2026-05-24 09:30:00'),
(3, 1, 78.40, 25.10, 34.90, 88.70, 97.80, 'Segundo avance: buen progreso, mantener consumo de proteína.', '2026-06-06 09:30:00'),

(4, 2, 64.20, 30.00, 25.80, 76.00, 96.00, 'Registro inicial. Se sugiere regular horarios de comida.', '2026-05-11 11:00:00'),
(5, 2, 63.80, 29.40, 26.00, 75.30, 95.50, 'Avance positivo. Menos molestias digestivas.', '2026-05-25 11:00:00'),

(6, 3, 93.00, 31.50, 37.90, 104.00, 108.00, 'Registro inicial. Controlar sodio y actividad de bajo impacto.', '2026-05-12 12:45:00'),
(7, 3, 91.00, 30.30, 38.20, 101.50, 106.50, 'Avance favorable. Reporta mejor presión arterial.', '2026-06-02 12:45:00'),

(8, 4, 54.60, 21.00, 24.40, 66.00, 91.00, 'Registro inicial. Objetivo principal: hipertrofia y alimentación suficiente.', '2026-05-13 15:00:00'),
(9, 4, 55.30, 20.80, 25.10, 66.30, 91.50, 'Aumento de masa muscular sin incremento relevante de grasa.', '2026-06-03 15:00:00'),

(10, 5, 86.00, 28.70, 35.00, 98.00, 102.00, 'Registro inicial. Reducir grasas saturadas y aumentar cardio.', '2026-05-14 17:20:00'),
(11, 5, 84.70, 27.90, 35.50, 96.80, 101.30, 'Avance adecuado. Mejor cumplimiento de entrenamiento.', '2026-06-04 17:20:00');

-- =========================================================
-- 4. Datos de salud actuales (último avance + objetivo/actividad)
--    datos_salud_actuales.paciente_id -> personas.id
-- =========================================================
INSERT INTO datos_salud_actuales
(id, paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, objetivo_salud, nivel_actividad, fecha_actualizacion)
VALUES
(1, 1, 78.40, 25.10, 34.90, 88.70, 97.80, 'Bajar grasa corporal y mejorar condición física', 'Moderado', '2026-06-06 09:30:00'),
(2, 2, 63.80, 29.40, 26.00, 75.30, 95.50, 'Mejorar digestión y mantener peso saludable', 'Bajo', '2026-05-25 11:00:00'),
(3, 3, 91.00, 30.30, 38.20, 101.50, 106.50, 'Reducir peso y presión arterial', 'Bajo', '2026-06-02 12:45:00'),
(4, 4, 55.30, 20.80, 25.10, 66.30, 91.50, 'Ganar masa muscular de forma saludable', 'Alto', '2026-06-03 15:00:00'),
(5, 5, 84.70, 27.90, 35.50, 96.80, 101.30, 'Mejorar composición corporal y reducir colesterol', 'Moderado', '2026-06-04 17:20:00');

-- =========================================================
-- 5. Asignación de pacientes con nutriólogos
--    paciente_id y nutriologo_id -> personas.id
-- =========================================================
INSERT INTO pacientes_nutriologos
(id, paciente_id, nutriologo_id, fecha_asignacion, activo)
VALUES
(1, 1, 10, '2026-05-10 10:00:00', TRUE),
(2, 2, 10, '2026-05-11 11:30:00', TRUE),
(3, 3, 11, '2026-05-12 13:00:00', TRUE),
(4, 4, 10, '2026-05-13 15:30:00', TRUE),
(5, 5, 11, '2026-05-14 17:45:00', TRUE);

-- =========================================================
-- 5. Asignación de pacientes con entrenadores
--    paciente_id y entrenador_id -> personas.id
-- =========================================================
INSERT INTO pacientes_entrenadores
(id, paciente_id, entrenador_id, fecha_asignacion, activo)
VALUES
(1, 1, 20, '2026-05-10 10:10:00', TRUE),
(2, 2, 21, '2026-05-11 11:40:00', TRUE),
(3, 3, 20, '2026-05-12 13:10:00', TRUE),
(4, 4, 21, '2026-05-13 15:40:00', TRUE),
(5, 5, 20, '2026-05-14 17:55:00', TRUE);

-- =========================================================
-- 6. Citas
--    paciente_id y especialista_id -> personas.id
-- =========================================================
INSERT INTO citas
(id, paciente_id, especialista_id, tipo_especialista, fecha, hora, motivo, estado, observaciones)
VALUES
(1, 1, 10, 'nutriologo', '2026-06-09', '09:00:00', 'Revisión de dieta y progreso de peso', 'confirmada', 'Cita de seguimiento nutricional de 1 hora.'),
(2, 2, 10, 'nutriologo', '2026-06-09', '10:00:00', 'Ajuste por gastritis y preferencias alimentarias', 'confirmada', 'Evitar lácteos enteros.'),
(3, 3, 11, 'nutriologo', '2026-06-10', '11:00:00', 'Plan alimenticio bajo en sodio', 'pendiente', 'Paciente con hipertensión controlada.'),
(4, 4, 10, 'nutriologo', '2026-06-11', '12:00:00', 'Dieta para aumento de masa muscular', 'confirmada', 'Revisión de calorías y proteína.'),
(5, 5, 11, 'nutriologo', '2026-06-12', '13:00:00', 'Seguimiento de colesterol', 'confirmada', 'Revisar adherencia al plan.'),

(6, 1, 20, 'entrenador', '2026-06-09', '11:00:00', 'Entrenamiento inicial de fuerza y cardio', 'confirmada', 'Bloque de 1 hora.'),
(7, 2, 21, 'entrenador', '2026-06-10', '09:00:00', 'Rutina de bajo impacto', 'confirmada', 'Considerar nivel de actividad bajo.'),
(8, 3, 20, 'entrenador', '2026-06-10', '12:00:00', 'Cardio controlado y movilidad', 'pendiente', 'Evitar intensidad alta por hipertensión.'),
(9, 4, 21, 'entrenador', '2026-06-11', '16:00:00', 'Rutina de hipertrofia', 'confirmada', 'Trabajo de tren inferior y superior.'),
(10, 5, 20, 'entrenador', '2026-06-12', '15:00:00', 'Cardio y fuerza metabólica', 'confirmada', 'Objetivo: reducción de grasa.'),

-- Cita cancelada: no debe bloquear disponibilidad porque el sistema ignora estado cancelada
(11, 1, 10, 'nutriologo', '2026-06-13', '09:00:00', 'Cita cancelada de prueba', 'cancelada', 'Debe quedar visible como antecedente, pero sin bloquear horario.');





-- =========================================================
-- 9. Dietas creadas por nutriólogos
--    dietas.nutriologo_id -> personas.id
-- =========================================================
INSERT INTO dietas
(id, nombre, descripcion, indicaciones, nutriologo_id, fecha_creacion)
VALUES
(1, 'Plan reducción de grasa moderado', 'Dieta hipocalórica con alto aporte de proteína y carbohidratos controlados.', 'Lunes: desayuno con 2 huevos revueltos con espinaca y 1 tortilla de maíz; comida con 120 g de pechuga de pollo a la plancha, 1/2 taza de arroz integral y ensalada verde con limón; cena con atún en agua con verduras cocidas. Martes: desayuno con yogurt natural bajo en grasa, 1/3 taza de avena y frutos rojos; comida con 120 g de pescado al horno, calabacitas al vapor y 1 papa chica cocida; cena con ensalada de pollo deshebrado y aguacate. Miércoles: desayuno con omelette de claras con champiñones; comida con 120 g de carne magra asada, nopales y 1/2 taza de frijoles; cena con sopa de verduras y queso panela. Jueves: desayuno con licuado de proteína con agua y 1 plátano chico; comida con pollo en fajitas preparado con poco aceite y verduras; cena con tostadas horneadas con atún y lechuga. Viernes: desayuno con avena cocida en agua con canela; comida con pescado empapelado con verduras y 1/2 taza de quinoa; cena con ensalada de huevo cocido. Sábado: desayuno con 2 huevos cocidos y fruta; comida con pechuga de pollo en salsa casera sin crema, verduras y arroz integral; cena ligera con yogurt natural y nueces. Domingo: comida libre moderada, cuidando porciones, evitando frituras y bebidas azucaradas; mantener hidratación de 2 litros de agua al día.', 10, '2026-05-15 09:00:00'),

(2, 'Plan digestivo ligero', 'Plan con alimentos suaves, bajo en irritantes y comidas pequeñas.', 'Lunes: desayuno con avena cocida en agua y manzana cocida; comida con 100 g de pollo hervido o a la plancha, arroz blanco y zanahoria cocida; cena con sopa de verduras sin picante. Martes: desayuno con pan tostado integral y queso panela; comida con pescado al vapor, calabaza cocida y papa cocida; cena con crema de verduras sin lácteos pesados. Miércoles: desayuno con yogurt natural sin azúcar y papaya; comida con pechuga de pollo deshebrada, arroz y chayote hervido; cena con huevo cocido y verduras suaves. Jueves: desayuno con plátano chico y avena; comida con filete de pescado al horno con limón, verduras cocidas y 1 tortilla; cena con caldo de pollo bajo en grasa. Viernes: desayuno con arroz con leche ligero preparado con leche descremada; comida con pollo cocido, puré de papa natural y zanahoria; cena con gelatina sin azúcar y yogurt natural. Sábado: desayuno con pan tostado y té sin cafeína; comida con carne magra cocida en trozos pequeños, arroz y verduras blandas; cena con sopa de fideo ligera. Domingo: mantener comidas pequeñas cada 3 o 4 horas, evitar irritantes como chile, café, alcohol, frituras y alimentos muy condimentados; beber agua simple en sorbos durante el día.', 10, '2026-05-16 10:00:00'),

(3, 'Plan bajo en sodio', 'Dieta orientada a control de presión arterial y pérdida de peso gradual.', 'Lunes: desayuno con avena natural con fruta y sin azúcar añadida; comida con 120 g de pollo a la plancha sazonado con limón, ajo y hierbas, acompañado de ensalada y arroz integral; cena con verduras al vapor y queso fresco bajo en sodio. Martes: desayuno con claras de huevo con espinaca y jitomate; comida con pescado al horno con especias naturales, papa cocida y ensalada; cena con tostadas horneadas sin sal con aguacate y pollo. Miércoles: desayuno con yogurt natural y fruta; comida con carne magra asada sin sal añadida, nopales y frijoles de olla bajos en sal; cena con sopa de verduras casera. Jueves: desayuno con licuado de fruta con leche descremada; comida con pechuga de pollo en salsa de jitomate natural sin consomé, verduras y quinoa; cena con ensalada de atún bajo en sodio. Viernes: desayuno con pan integral bajo en sodio y huevo cocido; comida con pescado empapelado con calabaza, zanahoria y champiñones; cena con crema de verduras natural. Sábado: desayuno con fruta fresca y nueces sin sal; comida con pollo asado con hierbas, arroz integral y ensalada; cena con verduras salteadas con mínimo aceite. Domingo: evitar embutidos, enlatados, sopas instantáneas, consomés, botanas saladas y salsas comerciales; usar limón, vinagre, ajo, cebolla, orégano y pimienta para dar sabor sin añadir sal.', 11, '2026-05-17 11:00:00'),

(4, 'Plan volumen limpio', 'Plan para ganancia muscular con superávit calórico controlado.', 'Lunes: desayuno con 3 huevos, 2 tortillas y 1 taza de avena con plátano; comida con 150 g de pollo a la plancha, 1 taza de arroz integral, verduras y aguacate; cena con atún, papa cocida y ensalada. Martes: desayuno con yogurt griego, avena, frutos rojos y nueces; comida con 150 g de carne magra, pasta integral y verduras; cena con omelette de huevo con queso panela y pan integral. Miércoles: desayuno con licuado de leche descremada, proteína, plátano y crema de cacahuate; comida con pescado al horno, camote y ensalada; cena con pollo deshebrado, frijoles y tortillas. Jueves: desayuno con hot cakes de avena preparados con huevo y plátano; comida con pechuga de pollo en fajitas, arroz y verduras; cena con yogurt griego y frutos secos. Viernes: desayuno con huevos revueltos, pan integral y fruta; comida con carne asada, quinoa y ensalada; cena con sándwich integral de pollo. Sábado: desayuno con avena cocida con leche, nueces y miel moderada; comida con pasta integral con pollo y verduras; cena con pescado y papa cocida. Domingo: incluir una comida más alta en calorías pero controlada, priorizando proteína magra, carbohidratos complejos y grasas saludables; consumir una colación postentrenamiento con proteína y carbohidrato.', 10, '2026-05-18 12:00:00'),

(5, 'Plan cardiosaludable', 'Dieta alta en fibra, grasas saludables y baja en grasas saturadas.', 'Lunes: desayuno con avena, manzana y semillas de chía; comida con pescado a la plancha, ensalada verde, 1/2 taza de arroz integral y aguacate; cena con sopa de verduras y tostadas horneadas. Martes: desayuno con pan integral, aguacate y huevo cocido; comida con pollo al horno, verduras al vapor y quinoa; cena con ensalada de garbanzos con jitomate, pepino y limón. Miércoles: desayuno con yogurt natural bajo en grasa, fruta y nueces; comida con salmón o pescado azul, camote y ensalada; cena con verduras salteadas y queso panela. Jueves: desayuno con licuado de fruta con avena y leche descremada; comida con pechuga de pollo, frijoles de olla y ensalada; cena con atún en agua con verduras. Viernes: desayuno con avena cocida con canela; comida con carne magra en porción de 100 g, nopales y arroz integral; cena con crema de verduras sin crema. Sábado: desayuno con fruta fresca y almendras; comida con pescado empapelado con verduras y aceite de oliva en cantidad moderada; cena con ensalada de lentejas. Domingo: evitar mantequilla, frituras, carnes procesadas, exceso de azúcar y grasas saturadas; preferir aceite de oliva, aguacate, nueces sin sal, cereales integrales y leguminosas, cuidando porciones y manteniendo buena hidratación.', 11, '2026-05-19 13:00:00');


-- =========================================================
-- 11. Dietas asignadas a pacientes
--     paciente_id -> personas.id, dieta_id -> dietas.id
-- =========================================================
INSERT INTO dietas_pacientes
(id, paciente_id, dieta_id, fecha_inicio, fecha_fin, estado)
VALUES
(1, 1, 1, '2026-05-15', '2026-07-15', 'activa'),
(2, 2, 2, '2026-05-16', '2026-07-16', 'activa'),
(3, 3, 3, '2026-05-17', '2026-07-17', 'activa'),
(4, 4, 4, '2026-05-18', '2026-07-18', 'activa'),
(5, 5, 5, '2026-05-19', '2026-07-19', 'activa'),
(6, 1, 5, '2026-03-01', '2026-04-30', 'finalizada');

-- =========================================================
-- 12. Rutinas creadas por entrenadores
--     rutinas.entrenador_id -> personas.id
-- =========================================================
INSERT INTO rutinas
(id, nombre, descripcion, entrenador_id, fecha_creacion)
VALUES
(1, 'Rutina fuerza inicial', 'Rutina para adaptación muscular con ejercicios básicos de fuerza.', 20, '2026-05-15 15:00:00'),
(2, 'Rutina bajo impacto', 'Entrenamiento ligero para mejorar movilidad y resistencia sin impacto alto.', 21, '2026-05-16 16:00:00'),
(3, 'Rutina cardio controlado', 'Cardio moderado con monitoreo de esfuerzo y movilidad.', 20, '2026-05-17 17:00:00'),
(4, 'Rutina hipertrofia principiante', 'Rutina para aumento de masa muscular con enfoque técnico.', 21, '2026-05-18 18:00:00'),
(5, 'Rutina metabólica progresiva', 'Circuito de fuerza y cardio para reducción de grasa corporal.', 20, '2026-05-19 19:00:00');

-- =========================================================
-- 13. Ejercicios de rutinas
--     rutina_ejercicios.rutina_id -> rutinas.id
-- =========================================================
INSERT INTO rutina_ejercicios
(id, rutina_id, dia_semana, nombre_ejercicio, descripcion, series, repeticiones, duracion_minutos, descanso_segundos, link_video)
VALUES
-- Rutina 1
(1, 1, 'lunes', 'Sentadilla con peso corporal', 'Bajar controlado y mantener espalda neutra.', 4, 12, NULL, 60, 'https://www.youtube.com/results?search_query=sentadilla+peso+corporal'),
(2, 1, 'lunes', 'Press de pecho con mancuernas', 'Movimiento controlado, sin bloquear codos.', 4, 10, NULL, 75, 'https://www.youtube.com/results?search_query=press+pecho+mancuernas'),
(3, 1, 'miercoles', 'Remo con mancuerna', 'Jalar con espalda, no con cuello.', 4, 12, NULL, 75, 'https://www.youtube.com/results?search_query=remo+mancuerna'),
(4, 1, 'viernes', 'Caminata rápida', 'Mantener ritmo constante.', NULL, NULL, 30, 0, 'https://www.youtube.com/results?search_query=caminata+rapida+ejercicio'),

-- Rutina 2
(5, 2, 'lunes', 'Movilidad articular', 'Movilidad de hombros, cadera y tobillos.', 3, 15, NULL, 30, 'https://www.youtube.com/results?search_query=movilidad+articular'),
(6, 2, 'miercoles', 'Bicicleta estática suave', 'Ritmo ligero, respiración cómoda.', NULL, NULL, 25, 0, 'https://www.youtube.com/results?search_query=bicicleta+estatica+suave'),
(7, 2, 'viernes', 'Puente de glúteo', 'Contraer glúteo arriba y bajar lento.', 3, 15, NULL, 45, 'https://www.youtube.com/results?search_query=puente+gluteo'),

-- Rutina 3
(8, 3, 'martes', 'Caminadora inclinada suave', 'No exceder esfuerzo moderado.', NULL, NULL, 20, 0, 'https://www.youtube.com/results?search_query=caminadora+inclinada'),
(9, 3, 'jueves', 'Peso muerto rumano ligero', 'Mantener espalda recta.', 3, 12, NULL, 75, 'https://www.youtube.com/results?search_query=peso+muerto+rumano+ligero'),
(10, 3, 'sabado', 'Elíptica moderada', 'Ritmo constante, sin impacto alto.', NULL, NULL, 30, 0, 'https://www.youtube.com/results?search_query=eliptica+moderada'),

-- Rutina 4
(11, 4, 'lunes', 'Prensa de pierna', 'Rango cómodo y controlado.', 4, 10, NULL, 90, 'https://www.youtube.com/results?search_query=prensa+de+pierna'),
(12, 4, 'miercoles', 'Jalón al pecho', 'Llevar barra hacia la parte alta del pecho.', 4, 10, NULL, 90, 'https://www.youtube.com/results?search_query=jalon+al+pecho'),
(13, 4, 'viernes', 'Hip thrust', 'Elevar cadera y contraer glúteos.', 4, 12, NULL, 90, 'https://www.youtube.com/results?search_query=hip+thrust'),

-- Rutina 5
(14, 5, 'martes', 'Circuito de sentadilla y remo', 'Alternar ejercicios con técnica controlada.', 4, 15, NULL, 45, 'https://www.youtube.com/results?search_query=circuito+sentadilla+remo'),
(15, 5, 'jueves', 'Escaladora', 'Mantener ritmo medio.', NULL, NULL, 20, 0, 'https://www.youtube.com/results?search_query=escaladora+gimnasio'),
(16, 5, 'sabado', 'Plancha abdominal', 'Mantener abdomen activo y espalda recta.', 4, NULL, 1, 45, 'https://www.youtube.com/results?search_query=plancha+abdominal');

-- =========================================================
-- 14. Rutinas asignadas a pacientes
--     paciente_id -> personas.id, rutina_id -> rutinas.id
-- =========================================================
INSERT INTO rutinas_pacientes
(id, paciente_id, rutina_id, fecha_inicio, fecha_fin, estado)
VALUES
(1, 1, 1, '2026-05-15', '2026-07-15', 'activa'),
(2, 2, 2, '2026-05-16', '2026-07-16', 'activa'),
(3, 3, 3, '2026-05-17', '2026-07-17', 'activa'),
(4, 4, 4, '2026-05-18', '2026-07-18', 'activa'),
(5, 5, 5, '2026-05-19', '2026-07-19', 'activa'),
(6, 1, 3, '2026-03-01', '2026-04-30', 'finalizada');

COMMIT;
