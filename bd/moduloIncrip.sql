create database moduloIncrip;
use moduloIncrip;
create table rol (
id_rol int primary key,
nombre varchar(50) not null
);
create table usuario(
id_usuario int primary key,
nombre varchar(100) not null,
apellido varchar(100) not null,
email varchar(150)not null unique,
password_hash varchar(255) not null,
id_rol int not null,
activo boolean default true,
foreign key(id_rol) references rol(id_rol)
);
USE moduloIncrip;
INSERT INTO rol (id_rol, nombre) VALUES (1, 'Administrador'), (2, 'Preceptor');
USE moduloIncrip;

INSERT INTO usuario (id_usuario, nombre, apellido, email, password_hash, id_rol, activo) 
VALUES (1, 'Silvana', 'Castro', 'silvina67@gmail.com', '234567', 2, 1)
ON DUPLICATE KEY UPDATE email=email;
select * from usuario;
USE moduloIncrip;

UPDATE usuario 
SET password_hash = '234567' 
WHERE email = 'silvina67@gmail.com';
select* from inscripciones;
CREATE TABLE IF NOT EXISTS inscripciones (
    id_inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    -- Datos del Alumno/a
    alumno_apellido_nombre VARCHAR(150) NOT NULL,
    alumno_dni VARCHAR(20) NOT NULL,
    alumno_cuil VARCHAR(20),
    alumno_edad INT,
    alumno_fecha_nacimiento DATE NOT NULL,
    alumno_lugar_nacimiento VARCHAR(100),
    alumno_escuela_origen VARCHAR(150),
    alumno_foto VARCHAR(255),
    alumno_observaciones TEXT,

    -- Datos de la Madre / Tutora
    madre_apellido_nombre VARCHAR(150) NOT NULL,
    madre_dni VARCHAR(20) NOT NULL,
    madre_cuil VARCHAR(20),
    madre_fecha_nacimiento DATE,
    madre_lugar_nacimiento VARCHAR(100),
    madre_percebe_asignacion VARCHAR(10),
    madre_domicilio VARCHAR(200) NOT NULL,
    madre_telefono VARCHAR(50) NOT NULL,
    madre_estudios VARCHAR(50),
    madre_email VARCHAR(100),

    -- Datos del Padre / Tutor
    padre_apellido_nombre VARCHAR(150),
    padre_dni VARCHAR(20),
    padre_cuil VARCHAR(20),
    padre_fecha_nacimiento DATE,
    padre_lugar_nacimiento VARCHAR(100),
    padre_percebe_asignacion VARCHAR(10),
    padre_domicilio VARCHAR(200),
    padre_telefono VARCHAR(50),
    padre_estudios VARCHAR(50),
    padre_email VARCHAR(100),

    -- Acta de Aceptación y Autorización
    tutor_nombre_acta VARCHAR(150) NOT NULL,
    tutor_dni_acta VARCHAR(20) NOT NULL,
    correo_contacto_acta VARCHAR(100),
    autorizacion_imagen VARCHAR(20) NOT NULL,
    
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;