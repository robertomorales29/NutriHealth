CREATE DATABASE sitio_salud;
USE sitio_salud;

CREATE TABLE personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE,
    sexo ENUM('Masculino', 'Femenino', 'Otro'),
    telefono VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    direccion TEXT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('paciente', 'nutriologo', 'entrenador') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE datos_salud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    peso DECIMAL(5,2),
    estatura DECIMAL(4,2),
    edad INT,
    enfermedades TEXT,
    alergias TEXT,
    medicamentos TEXT,
    objetivo_salud VARCHAR(150),
    nivel_actividad ENUM('Bajo', 'Moderado', 'Alto'),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (paciente_id) REFERENCES personas(id)
);

CREATE TABLE historial_salud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    peso DECIMAL(5,2),
    porcentaje_grasa DECIMAL(5,2),
    masa_muscular DECIMAL(5,2),
    cintura DECIMAL(5,2),
    cadera DECIMAL(5,2),
    observaciones TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (paciente_id) REFERENCES personas(id)
);

CREATE TABLE pacientes_nutriologos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    nutriologo_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,

    FOREIGN KEY (paciente_id) REFERENCES personas(id),
    FOREIGN KEY (nutriologo_id) REFERENCES personas(id)
);

CREATE TABLE pacientes_entrenadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    entrenador_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,

    FOREIGN KEY (paciente_id) REFERENCES personas(id),
    FOREIGN KEY (entrenador_id) REFERENCES personas(id)
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    especialista_id INT NOT NULL,
    tipo_especialista ENUM('nutriologo', 'entrenador') NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    motivo VARCHAR(200),
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'finalizada') DEFAULT 'pendiente',
    observaciones TEXT,

    FOREIGN KEY (paciente_id) REFERENCES personas(id),
    FOREIGN KEY (especialista_id) REFERENCES personas(id)
);

CREATE TABLE preferencias_comida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    comida_favorita VARCHAR(100),
    comida_no_gusta VARCHAR(100),
    restricciones TEXT,
    tipo_dieta_preferida VARCHAR(100),
    observaciones TEXT,

    FOREIGN KEY (paciente_id) REFERENCES personas(id)
);

CREATE TABLE platillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    calorias INT,
    proteinas DECIMAL(5,2),
    carbohidratos DECIMAL(5,2),
    grasas DECIMAL(5,2),
    tipo_comida ENUM('desayuno', 'comida', 'cena', 'colacion') NOT NULL,
    creado_por INT,

    FOREIGN KEY (creado_por) REFERENCES personas(id)
);

CREATE TABLE dietas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    indicaciones TEXT,
    nutriologo_id INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (nutriologo_id) REFERENCES personas(id)
);

CREATE TABLE dieta_platillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dieta_id INT NOT NULL,
    platillo_id INT NOT NULL,
    dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo'),
    hora_comida ENUM('desayuno', 'comida', 'cena', 'colacion'),
    porcion VARCHAR(100),

    FOREIGN KEY (dieta_id) REFERENCES dietas(id),
    FOREIGN KEY (platillo_id) REFERENCES platillos(id)
);

CREATE TABLE dietas_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    dieta_id INT NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('activa', 'finalizada', 'cancelada') DEFAULT 'activa',

    FOREIGN KEY (paciente_id) REFERENCES personas(id),
    FOREIGN KEY (dieta_id) REFERENCES dietas(id)
);

CREATE TABLE rutinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    entrenador_id INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (entrenador_id) REFERENCES personas(id)
);

CREATE TABLE rutina_ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT NOT NULL,
    dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
    nombre_ejercicio VARCHAR(100) NOT NULL,
    descripcion TEXT,
    series INT,
    repeticiones INT,
    duracion_minutos INT,
    descanso_segundos INT,
    link_video VARCHAR(255),

    FOREIGN KEY (rutina_id) REFERENCES rutinas(id)
);

CREATE TABLE rutinas_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    rutina_id INT NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('activa', 'finalizada', 'cancelada') DEFAULT 'activa',

    FOREIGN KEY (paciente_id) REFERENCES personas(id),
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id)
);

