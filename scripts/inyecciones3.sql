-- ====================================================================
-- SISTEMA DE NAVEGACIÓN DE IPN - MariaDB
-- Versión: 1.0
-- Fecha: 2025-06-24
-- ====================================================================

-- Configuración inicial
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas existentes si existen (en orden correcto para evitar conflictos FK)
DROP TABLE IF EXISTS connections;
DROP TABLE IF EXISTS places;
DROP TABLE IF EXISTS place_types;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- TABLA: place_types
-- Propósito: Catálogo de tipos de lugares del IPN
-- ====================================================================
CREATE TABLE place_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50), -- Para iconos en interfaces
    color VARCHAR(7), -- Color hexadecimal para visualización (#FFFFFF)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- TABLA: users
-- Propósito: Gestión de usuarios del sistema
-- ====================================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    student_id VARCHAR(50) UNIQUE,
    phone VARCHAR(20),
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para optimización
    INDEX idx_email (email),
    INDEX idx_student_id (student_id),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- TABLA: places
-- Propósito: Lugares físicos del IPN
-- ====================================================================
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    place_type_id INT NOT NULL,
    image VARCHAR(100),
    description TEXT,
    short_code VARCHAR(10), -- Código corto para referencias rápidas (ej: "A101", "LAB3")
    capacity INT, -- Capacidad de personas
    floor_level INT DEFAULT 0, -- Nivel de piso (0=planta baja, 1=primer piso, etc.)
    building VARCHAR(50), -- Edificio al que pertenece
    position_x FLOAT, -- Coordenada X para visualización espacial
    position_y FLOAT, -- Coordenada Y para visualización espacial
    is_accessible BOOLEAN DEFAULT TRUE, -- Accesible para personas con discapacidad
    is_active BOOLEAN DEFAULT TRUE, -- Si el lugar está disponible/activo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Claves foráneas
    CONSTRAINT fk_place_type FOREIGN KEY (place_type_id) REFERENCES place_types(id) ON DELETE RESTRICT,
    
    -- Índices para optimización
    INDEX idx_place_type (place_type_id),
    INDEX idx_building (building),
    INDEX idx_floor (floor_level),
    INDEX idx_short_code (short_code),
    INDEX idx_active (is_active),
    INDEX idx_spatial (position_x, position_y)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- TABLA: connections
-- Propósito: Conexiones entre lugares para navegación
-- ====================================================================
CREATE TABLE connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_place_id INT NOT NULL,
    to_place_id INT NOT NULL,
    distance_m FLOAT NOT NULL,
    travel_time_minutes FLOAT, -- Tiempo estimado de viaje
    connection_type ENUM('corridor', 'stairs', 'elevator', 'outdoor', 'bridge') DEFAULT 'corridor',
    is_bidirectional BOOLEAN DEFAULT TRUE, -- Si la conexión funciona en ambos sentidos
    is_accessible BOOLEAN DEFAULT TRUE, -- Accesible para personas con discapacidad
    is_active BOOLEAN DEFAULT TRUE, -- Si la conexión está disponible
    notes TEXT, -- Notas adicionales (ej: "Cerrado los fines de semana")
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Claves foráneas
    CONSTRAINT fk_from_place FOREIGN KEY (from_place_id) REFERENCES places(id) ON DELETE CASCADE,
    CONSTRAINT fk_to_place FOREIGN KEY (to_place_id) REFERENCES places(id) ON DELETE CASCADE,
    
    -- Restricciones
    CONSTRAINT uc_connection UNIQUE (from_place_id, to_place_id),
    CONSTRAINT chk_different_places CHECK (from_place_id != to_place_id),
    CONSTRAINT chk_positive_distance CHECK (distance_m > 0),
    
    -- Índices para optimización
    INDEX idx_from_place (from_place_id),
    INDEX idx_to_place (to_place_id),
    INDEX idx_bidirectional (is_bidirectional),
    INDEX idx_active (is_active),
    INDEX idx_connection_type (connection_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- DATOS INICIALES
-- ====================================================================

-- Insertar tipos de lugares predefinidos
INSERT INTO place_types (name, description, icon, color) VALUES
('Salón', 'Aulas y salones de clase', 'classroom', '#4A90E2'),
('Laboratorio', 'Laboratorios de práctica y investigación', 'science', '#7ED321'),
('Biblioteca', 'Espacios de estudio y consulta', 'library_books', '#F5A623'),
('Auditorio', 'Espacios para eventos y conferencias', 'event', '#D0021B'),
('Oficina', 'Oficinas administrativas y académicas', 'business', '#9013FE'),
('Cafetería', 'Espacios de alimentación', 'restaurant', '#FF6D00'),
('Baño', 'Servicios sanitarios', 'wc', '#607D8B'),
('Pasillo', 'Pasillos y corredores', 'more_horiz', '#9E9E9E'),
('Escalera', 'Escaleras y accesos verticales', 'stairs', '#795548'),
('Ascensor', 'Elevadores', 'elevator', '#3F51B5'),
('Entrada', 'Entradas y salidas del edificio', 'exit_to_app', '#4CAF50'),
('Estacionamiento', 'Áreas de estacionamiento', 'local_parking', '#FF9800'),
('Otro', 'Otros tipos de espacios', 'location_on', '#757575');


-- Verificar las conexiones creadas
SELECT 
    c.id,
    p1.name as from_place,
    p2.name as to_place,
    c.distance_m,
    c.connection_type,
    c.is_bidirectional,
    c.is_accessible
FROM connections c
JOIN places p1 ON c.from_place_id = p1.id
JOIN places p2 ON c.to_place_id = p2.id
ORDER BY c.from_place_id, c.distance_m;

-- ====================================================================
-- TRIGGERS PARA AUDITORÍA Y VALIDACIÓN
-- ====================================================================

-- Trigger para validar conexiones bidireccionales
DELIMITER //
CREATE TRIGGER tr_connection_bidirectional
    AFTER INSERT ON connections
    FOR EACH ROW
BEGIN
    -- Si la conexión es bidireccional, crear la conexión inversa si no existe
    IF NEW.is_bidirectional = TRUE THEN
        INSERT IGNORE INTO connections 
        (from_place_id, to_place_id, distance_m, travel_time_minutes, 
         connection_type, is_bidirectional, is_accessible, is_active, notes)
        VALUES 
        (NEW.to_place_id, NEW.from_place_id, NEW.distance_m, NEW.travel_time_minutes,
         NEW.connection_type, FALSE, NEW.is_accessible, NEW.is_active, 
         CONCAT('Auto-generada desde conexión ', NEW.id));
    END IF;
END //
DELIMITER ;

-- ====================================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ====================================================================

-- Índice compuesto para búsquedas espaciales
CREATE INDEX idx_places_spatial_active ON places (position_x, position_y, is_active);

-- Índice para búsquedas de conexiones activas
CREATE INDEX idx_connections_active_bidirectional ON connections (is_active, is_bidirectional);

-- ====================================================================
-- CONFIGURACIÓN FINAL
-- ====================================================================

-- Mostrar información del esquema creado
SELECT 
    'Base de datos creada exitosamente' AS status,
    (SELECT COUNT(*) FROM place_types) AS tipos_lugares,
    (SELECT COUNT(*) FROM users) AS usuarios,
    NOW() AS fecha_creacion;

-- Comentarios finales
/*
INSTRUCCIONES DE USO:

1. INSERTAR LUGARES:
   INSERT INTO places (name, place_type_id, short_code, building, floor_level, position_x, position_y)
   VALUES ('Aula 101', 1, 'A101', 'Edificio A', 1, 10.5, 20.3);

2. CREAR CONEXIONES:
   INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes)
   VALUES (1, 2, 25.5, 1.5);

3. BUSCAR LUGARES CERCANOS:
   CALL sp_get_nearby_places(10.0, 20.0, 50.0, 'Salón');

4. VER LUGARES CON TIPOS:
   SELECT * FROM v_places_with_type WHERE building = 'Edificio A';

5. VER CONEXIONES DETALLADAS:
   SELECT * FROM v_connections_detailed WHERE from_place_name LIKE '%Aula%';

NOTAS IMPORTANTES:
- Todas las tablas usan InnoDB para soporte de transacciones
- Se incluyen índices para optimizar consultas frecuentes
- Las conexiones bidireccionales se manejan automáticamente
- Se incluyen validaciones de integridad referencial
- El sistema soporta coordenadas espaciales para navegación
*/
 
INSERT INTO places (name, place_type_id, position_x, position_y, short_code, description, image) VALUES
("Entrada", 13, 0, 0, "P1", "Es el acceso principal al CECyT 9 \"Juan de Dios Bátiz\". Desde este punto, los visitantes, alumnos y personal ingresan al plantel. También sirve como referencia para calcular distancias hacia otros puntos de interés dentro de la escuela.", "uploads/entrada.webp"),
("Jardineras centrales", 13, 0, 0, "P2", "Espacio abierto compuesto por tres jardineras ubicadas en el corazón del plantel. Estas áreas verdes están pensadas para el descanso, convivencia entre alumnos y el embellecimiento del entorno escolar.", "uploads/jardineras_centrales.webp"),
("Comedores", 6, 0, 0, "P3", "Zona equipada con mesas y bancas donde los alumnos pueden consumir sus alimentos. Es un área de uso común que promueve la alimentación ordenada y cómoda durante los recesos.", "uploads/comedores.webp"),
("Cafeteria", 6, 0, 0, "P4", "Establecimiento donde se ofrecen alimentos y bebidas a estudiantes, docentes y personal administrativo. Se encuentra cerca de los comedores y forma parte de los servicios básicos del plantel.", "uploads/cafeteria.webp"),
("Letras de batiz", 13, 0, 0, "P5", "Conjunto escultórico de letras gigantes que forman la palabra \"BATIZ\". Es un punto emblemático y frecuentemente utilizado para tomarse fotografías o como punto de encuentro entre alumnos.", "uploads/letras_batiz.webp"),
("Auditorio", 4, 0, 0, "P6", "Espacio cerrado con capacidad para aproximadamente 300 personas. Utilizado para realizar conferencias, ceremonias, reuniones, proyecciones y otros eventos institucionales o culturales.", "uploads/auditorio.webp"),
("Escaleras edificio A", 9, 0, 0, "P7", "Escalinatas que permiten el acceso al área administrativa y a los salones del edificio A. Facilitan el tránsito vertical dentro de uno de los edificios principales del plantel.", "uploads/escaleras_edificio_a.webp"),
("Baños auditorio", 7, 0, 0, "P8", "Servicios sanitarios ubicados en las inmediaciones del auditorio. Están disponibles para el público que asiste a eventos o transita por esa zona del plantel.", "uploads/banos_auditorio.webp"),
("Biblioteca", 3, 0, 0, "P9", "Instalación que ofrece un acervo académico físico y digital, así como una sala de cómputo. Está destinada al estudio, la consulta de materiales y el acceso al conocimiento por parte de la comunidad escolar.", "uploads/biblioteca.webp"),
("Papeleria edificio B", 13, 0, 0, "P10", "Local que brinda servicios de impresión, copiado y venta de útiles escolares. Es frecuentado por estudiantes que requieren material de apoyo para sus actividades académicas.", "uploads/papeleria_edificio_b.webp"),
("Salones provisionales", 1, 0, 0, "P11", "Cuatro aulas temporales instaladas para atender la alta demanda de espacios educativos. Su objetivo es facilitar la distribución de grupos y mantener la continuidad académica mientras se ajusta la infraestructura definitiva.", "uploads/salones_provisionales.webp"),
("Cancha de futbol sala", 13, 0, 0, "P12", "Espacio deportivo techado destinado a la práctica de fútbol rápido, con medidas reglamentarias y superficie sintética.", "uploads/cancha_futbol_sala.webp"),
("Canchas de basquet", 13, 0, 0, "P13", "Dos canchas al aire libre con superficie de concreto y aros reglamentarios para la práctica de baloncesto.", "uploads/canchas_basquet.webp"),
("Club de atletismo", 13, 0, 0, "P14", "Área destinada al almacenamiento de materiales y equipamiento para la práctica de atletismo, así como espacio para reuniones del club deportivo.", "uploads/club_atletismo.webp"),
("Prefectura", 5, 0, 0, "P15", "Oficina encargada de mantener el orden y disciplina escolar, así como de gestionar permisos y reportes de los estudiantes.", "uploads/prefectura.webp"),
("Escaleras primer piso A", 9, 0, 0, "P16", "Escalinatas que conectan la planta baja con el primer nivel del edificio A, permitiendo el acceso a salones y áreas administrativas.", "uploads/escaleras_primer_piso_a.webp"),
("Academia de matematicas", 5, 0, 0, "P17", "Salón destinado a reuniones y planeación académica del departamento de matemáticas, equipado con recursos didácticos.", "uploads/academia_matematicas.webp"),
("Academia de area social", 5, 0, 0, "P18", "Espacio de trabajo para los profesores del área social, donde se realizan planeaciones y actividades académicas.", "uploads/academia_social.webp"),
("Servicio medico", 5, 0, 0, "P19", "Área equipada para brindar primeros auxilios y atención médica básica a estudiantes y personal del plantel.", "uploads/servicio_medico.webp"),
("Salon de maquinas y herramientas", 1, 0, 0, "P20", "Taller equipado con maquinaria, tornos y herramientas para las prácticas de las carreras de mecatrónica y automatización.", "uploads/salon_maquinas.webp"),
("Papeleria ciberbatis", 13, 0, 0, "P21", "Establecimiento que ofrece servicios de papelería, fotocopiado e impresión, ubicado en el área de Ciberbatiz.", "uploads/papeleria_ciberbatis.webp"),
("Salon de usos multiples", 1, 0, 0, "P22", "Aula versátil equipada para diversas actividades académicas, eventos o reuniones especiales.", "uploads/salon_multiusos.webp"),
("Ciberbatis", 13, 0, 0, "P23", "Espacio al aire libre con palapas y mesas para estudio individual o en grupo, con acceso a red inalámbrica.", "uploads/ciberbatis.webp"),
("Baño escaleras edificio B", 7, 0, 0, "P24", "Servicios sanitarios ubicados cerca de las escaleras del edificio B, para uso de estudiantes y personal.", "uploads/banos_edificio_b.webp"),
("Salones 100", 1, 0, 0, "P25", "Conjunto de aulas estándar para clases teóricas, numeradas en la serie 100.", "uploads/salones_100.webp"),
("Salones de dibujo tecnico", 1, 0, 0, "P26", "Aulas especializadas equipadas con mesas de dibujo técnico para las asignaturas de diseño y arquitectura.", "uploads/salones_dibujo.webp"),
("Laboratorio de biologia", 2, 0, 0, "P27", "Espacio equipado con microscopios, especímenes y materiales para prácticas de biología y ciencias afines.", "uploads/laboratorio_biologia.webp"),
("Salones samsung", 1, 0, 0, "P28", "Laboratorio de tecnología equipado con dispositivos donados por Samsung para prácticas especializadas.", "uploads/salones_samsung.webp"),
("Laboratorio de sistemas digitales", 2, 0, 0, "P29", "Espacio con equipos de cómputo y herramientas para el desarrollo de proyectos de sistemas digitales.", "uploads/laboratorio_sistemas.webp"),
("Cubiculos de sistemas digitales", 5, 0, 0, "P30", "Oficinas para profesores del área de sistemas digitales, equipadas para preparación de clases y asesorías.", "uploads/cubiculos_sistemas.webp");


INSERT INTO places (name, place_type_id, position_x, position_y, short_code, description, image) VALUES
("Gestion escolar", 5, 0, 0, "P31", "Área de atención a estudiantes para trámites académicos, certificados y documentación escolar.", "uploads/gestion_escolar.webp"),
("Subdireccion", 5, 0, 0, "P32", "Oficina del subdirector del plantel, encargada de la gestión académica y administrativa secundaria.", "uploads/subdireccion.webp"),
("Direccion", 5, 0, 0, "P33", "Oficina principal del director del plantel, donde se toman las decisiones institucionales más importantes.", "uploads/direccion.webp"),
("Cubiculos administracion", 5, 0, 0, "P34", "Espacios de trabajo para el personal administrativo del plantel, ubicados cerca de las áreas directivas.", "uploads/cubiculos_administracion.webp"),
("Escaleras edificio B segundo piso", 9, 0, 0, "P35", "Escalinatas que conectan el primer piso con el segundo nivel del edificio B, dando acceso a más salones y laboratorios.", "uploads/escaleras_segundo_piso_b.webp"),
("Salones 200", 1, 0, 0, "P36", "Conjunto de aulas estándar para clases teóricas en el segundo piso, numeradas en la serie 200.", "uploads/salones_200.webp"),
("Laboratorio de programacion", 2, 0, 0, "P37", "Espacio equipado con computadoras y software especializado para la enseñanza de lenguajes de programación.", "uploads/laboratorio_programacion.webp"),
("Laboratorio de base de datos", 2, 0, 0, "P38", "Aula especializada en el manejo de sistemas gestores de bases de datos, con equipos y servidores para prácticas.", "uploads/laboratorio_bases_datos.webp"),
("UDI", 1, 0, 0, "P39", "Sala de Usos Múltiples destinada a talleres, cursos especiales y actividades extracurriculares.", "uploads/udi.webp"),
("Baño segundo piso edificio B", 7, 0, 0, "P40", "Servicios sanitarios ubicados en el segundo nivel del edificio B, para uso de estudiantes y profesores.", "uploads/banos_segundo_piso.webp"),
("Escaleras tercer piso", 9, 0, 0, "P41", "Escalinatas que conectan el segundo piso con el tercer nivel del edificio B, dando acceso a aulas superiores.", "uploads/escaleras_tercer_piso.webp"),
("Salones 300", 1, 0, 0, "P42", "Conjunto de aulas estándar para clases teóricas en el tercer piso, numeradas en la serie 300.", "uploads/salones_300.webp"),
("Laboratorio de fisica", 2, 0, 0, "P43", "Espacio equipado con instrumentos y dispositivos para la realización de experimentos y prácticas de física.", "uploads/laboratorio_fisica.webp"),
("Laboratorio de quimica", 2, 0, 0, "P44", "Área equipada con campanas de extracción, reactivos y materiales para experimentos y prácticas de química.", "uploads/laboratorio_quimica.webp");

-- Planta baja
UPDATE places SET position_x = 360, position_y = 510 WHERE id = 1;
UPDATE places SET position_x = 360, position_y = 400 WHERE id = 2;
UPDATE places SET position_x = 460, position_y = 520 WHERE id = 3;
UPDATE places SET position_x = 510, position_y = 535 WHERE id = 4;
UPDATE places SET position_x = 420, position_y = 400 WHERE id = 5;
UPDATE places SET position_x = 480, position_y = 400 WHERE id = 6;
UPDATE places SET position_x = 410, position_y = 450 WHERE id = 7;
UPDATE places SET position_x = 440, position_y = 440 WHERE id = 8;
UPDATE places SET position_x = 460, position_y = 480 WHERE id = 9;
UPDATE places SET position_x = 330, position_y = 330 WHERE id = 10;
UPDATE places SET position_x = 220, position_y = 230 WHERE id = 11;
UPDATE places SET position_x = 140, position_y = 290 WHERE id = 12;
UPDATE places SET position_x = 230, position_y = 184 WHERE id = 13;
UPDATE places SET position_x = 250, position_y = 80 WHERE id = 14;
UPDATE places SET position_x = 430, position_y = 265 WHERE id = 15;
UPDATE places SET position_x = 425, position_y = 195 WHERE id = 16;
UPDATE places SET position_x = 360, position_y = 290 WHERE id = 17;
UPDATE places SET position_x = 335, position_y = 295 WHERE id = 18;
UPDATE places SET position_x = 300, position_y = 290 WHERE id = 19;
UPDATE places SET position_x = 370, position_y = 140 WHERE id = 20;
UPDATE places SET position_x = 485, position_y = 300 WHERE id = 21;
UPDATE places SET position_x = 530, position_y = 310 WHERE id = 22;
UPDATE places SET position_x = 510, position_y = 270 WHERE id = 23;

-- Primer piso
UPDATE places SET position_x = 450, position_y = 265 WHERE id = 24;
UPDATE places SET position_x = 480, position_y = 230 WHERE id = 25;
UPDATE places SET position_x = 325, position_y = 225 WHERE id = 26;
UPDATE places SET position_x = 370, position_y = 145 WHERE id = 27;
UPDATE places SET position_x = 320, position_y = 135 WHERE id = 28;
UPDATE places SET position_x = 450, position_y = 140 WHERE id = 29;
UPDATE places SET position_x = 530, position_y = 150 WHERE id = 30;
UPDATE places SET position_x = 420, position_y = 400 WHERE id = 31;
UPDATE places SET position_x = 460, position_y = 400 WHERE id = 32;
UPDATE places SET position_x = 490, position_y = 400 WHERE id = 33;
UPDATE places SET position_x = 415, position_y = 480 WHERE id = 34;
UPDATE places SET position_x = 430, position_y = 195 WHERE id = 35;

-- Segundo piso
UPDATE places SET position_x = 440, position_y = 230 WHERE id = 36;
UPDATE places SET position_x = 430, position_y = 150 WHERE id = 37;
UPDATE places SET position_x = 475, position_y = 150 WHERE id = 38;
UPDATE places SET position_x = 370, position_y = 140 WHERE id = 39;
UPDATE places SET position_x = 350, position_y = 190 WHERE id = 40;
UPDATE places SET position_x = 430, position_y = 195 WHERE id = 41;

-- Tercer piso
UPDATE places SET position_x = 440, position_y = 230 WHERE id = 42;
UPDATE places SET position_x = 370, position_y = 140 WHERE id = 43;
UPDATE places SET position_x = 460, position_y = 150 WHERE id = 44;

INSERT INTO connections (
    from_place_id, to_place_id, distance_m, travel_time_minutes,
    connection_type, is_bidirectional, is_accessible, is_active, notes
) VALUES
-- Entrada (1) → Jardineras (2)
(1, 2, 100.00, 1.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(1, 3, 120.00, 2.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(3, 4, 50.00, 0.83, 'corridor', TRUE, TRUE, TRUE, NULL),
(2, 5, 90.00, 1.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(5, 8, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(5, 6, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(5, 7, 35.00, 0.58, 'corridor', TRUE, TRUE, TRUE, NULL),
(5, 9, 70.00, 1.17, 'corridor', TRUE, TRUE, TRUE, NULL),
(2, 10, 85.00, 1.42, 'corridor', TRUE, TRUE, TRUE, NULL),
(10, 11, 120.00, 2.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(11, 12, 90.00, 1.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(11, 13, 80.00, 1.33, 'corridor', TRUE, TRUE, TRUE, NULL),
(13, 14, 120.00, 2.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(2, 15, 100.00, 1.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(15, 17, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(17, 18, 35.00, 0.58, 'corridor', TRUE, TRUE, TRUE, NULL),
(18, 19, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(15, 16, 50.00, 0.83, 'stairs', TRUE, TRUE, TRUE, NULL),
(16, 20, 80.00, 1.33, 'corridor', TRUE, TRUE, TRUE, NULL),
(15, 21, 90.00, 1.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(21, 22, 50.00, 0.83, 'corridor', TRUE, TRUE, TRUE, NULL),
(22, 23, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(16, 35, 50.00, 0.83, 'stairs', TRUE, TRUE, TRUE, NULL),
(35, 25, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(25, 26, 90.00, 1.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(35, 24, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(35, 27, 75.00, 1.25, 'corridor', TRUE, TRUE, TRUE, NULL),
(27, 28, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(35, 29, 50.00, 0.83, 'corridor', TRUE, TRUE, TRUE, NULL),
(29, 30, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(35, 41, 50.00, 0.83, 'stairs', TRUE, TRUE, TRUE, NULL),
(41, 40, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(41, 36, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL),
(41, 37, 80.00, 1.33, 'corridor', TRUE, TRUE, TRUE, NULL),
(37, 39, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(37, 38, 35.00, 0.58, 'corridor', TRUE, TRUE, TRUE, NULL),
(41, 45, 45.00, 0.75, 'stairs', TRUE, TRUE, TRUE, NULL),
(45, 44, 40.00, 0.67, 'corridor', TRUE, TRUE, TRUE, NULL),
(45, 42, 50.00, 0.83, 'corridor', TRUE, TRUE, TRUE, NULL),
(7, 31, 70.00, 1.17, 'corridor', TRUE, TRUE, TRUE, NULL),
(31, 32, 30.00, 0.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(32, 33, 30.00, 0.50, 'corridor', TRUE, TRUE, TRUE, NULL),
(31, 34, 60.00, 1.00, 'corridor', TRUE, TRUE, TRUE, NULL);
