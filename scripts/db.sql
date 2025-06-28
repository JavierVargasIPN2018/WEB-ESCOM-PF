-- ====================================================================
-- SISTEMA DE NAVEGACIÓN DE IPN - MariaDB
-- Versión: 1.0
-- Fecha: 2025-06-24
-- ====================================================================

-- Configuración inicial
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas existentes si existen (en orden correcto para evitar conflictos FK)
DROP TABLE IF EXISTS connections;
DROP TABLE IF EXISTS favorite_places;
DROP TABLE IF EXISTS place_types;
DROP TABLE IF EXISTS places;
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
-- TABLA: favorite_places
-- Propósito: Lugares favoritos marcados por los usuarios
-- ====================================================================
CREATE TABLE favorite_places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    place_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Claves foráneas
    CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_place FOREIGN KEY (place_id) REFERENCES places(id) ON DELETE CASCADE,
    
    -- Restricción para evitar duplicados
    CONSTRAINT uc_user_place UNIQUE (user_id, place_id),
    
    -- Índices
    INDEX idx_user (user_id),
    INDEX idx_place (place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- DATOS INICIALES
-- ====================================================================

-- Insertar tipos de lugares predefinidos
INSERT INTO place_types (id, name, description, icon, color) VALUES
(1,'Salón', 'Aulas y salones de clase', 'classroom', '#4A90E2'),
(2,'Laboratorio', 'Laboratorios de práctica y investigación', 'science', '#7ED321'),
(3,'Biblioteca', 'Espacios de estudio y consulta', 'library_books', '#F5A623'),
(4,'Auditorio', 'Espacios para eventos y conferencias', 'event', '#D0021B'),
(5,'Oficina', 'Oficinas administrativas y académicas', 'business', '#9013FE'),
(6,'Cafetería', 'Espacios de alimentación', 'restaurant', '#FF6D00'),
(7,'Baño', 'Servicios sanitarios', 'wc', '#607D8B'),
(8,'Pasillo', 'Pasillos y corredores', 'more_horiz', '#9E9E9E'),
(9,'Escalera', 'Escaleras y accesos verticales', 'stairs', '#795548'),
(10,'Ascensor', 'Elevadores', 'elevator', '#3F51B5'),
(11,'Entrada', 'Entradas y salidas del edificio', 'exit_to_app', '#4CAF50'),
(12,'Estacionamiento', 'Áreas de estacionamiento', 'local_parking', '#FF9800'),
(13,'Otro', 'Otros tipos de espacios', 'location_on', '#757575');


-- LUGARES DE CECYT9 
INSERT INTO places (id, name, place_type_id, image, floor_level, position_x, position_y, short_code, description) VALUES
-- Planta baja (floor_level = 0)
(1, 'Entrada principal', 11, 'uploads/entrada_principal.webp', 0, 360, 510, "P1", 'Es el acceso principal al CECyT 9 "Juan de Dios Bátiz". Desde este punto, los visitantes, alumnos y personal ingresan al plantel. También sirve como referencia para calcular distancias hacia otros puntos de interés dentro de la escuela.'),
(2, 'Jardineras centrales', 13, 'uploads/jardineras_centrales.webp', 0, 360, 400, 'P2', 'Espacio abierto compuesto por tres jardineras ubicadas en el corazón del plantel. Estas áreas verdes están pensadas para el descanso, convivencia entre alumnos y el embellecimiento del entorno escolar.'),
(3, 'Comedores', 6, 'uploads/comedores.webp', 0, 460, 520, 'P3', 'Zona equipada con mesas y bancas donde los alumnos pueden consumir sus alimentos. Es un área de uso común que promueve la alimentación ordenada y cómoda durante los recesos.'),
(4, 'Cafeteria', 6, 'uploads/cafeteria.webp', 0, 510, 535, 'P4', 'Establecimiento donde se ofrecen alimentos y bebidas a estudiantes, docentes y personal administrativo. Se encuentra cerca de los comedores y forma parte de los servicios básicos del plantel.'),
(5, 'Letras de batiz', 13, 'uploads/letras_batiz.webp', 0, 420, 400, 'P5', 'Conjunto escultórico de letras gigantes que forman la palabra "BATIZ". Es un punto emblemático y frecuentemente utilizado para tomarse fotografías o como punto de encuentro entre alumnos.'),
(6, 'Auditorio', 4, 'uploads/auditorio.webp', 0, 480, 400, 'P6', 'Espacio cerrado con capacidad para aproximadamente 300 personas. Utilizado para realizar conferencias, ceremonias, reuniones, proyecciones y otros eventos institucionales o culturales.'),
(7, 'Escaleras edificio A', 9, 'uploads/escaleras_edificio_a.webp', 0, 410, 450, 'P7', 'Escalinatas que permiten el acceso al área administrativa y a los salones del edificio A. Facilitan el tránsito vertical dentro de uno de los edificios principales del plantel.'),
(8, 'Baños auditorio', 7, 'uploads/banos_auditorio.webp', 0, 440, 440, 'P8', 'Servicios sanitarios ubicados en las inmediaciones del auditorio. Están disponibles para el público que asiste a eventos o transita por esa zona del plantel.'),
(9, 'Biblioteca', 3, 'uploads/biblioteca.webp', 0, 460, 480, 'P9', 'Instalación que ofrece un acervo académico físico y digital, así como una sala de cómputo. Está destinada al estudio, la consulta de materiales y el acceso al conocimiento por parte de la comunidad escolar.'),
(10, 'Papeleria edificio B', 13, 'uploads/papeleria_edificio_b.webp', 0, 330, 330, 'P10', 'Local que brinda servicios de impresión, copiado y venta de útiles escolares. Es frecuentado por estudiantes que requieren material de apoyo para sus actividades académicas.'),
(11, 'Salones provisionales', 1, 'uploads/salones_provisionales.webp', 0, 220, 230, 'P11', 'Cuatro aulas temporales instaladas para atender la alta demanda de espacios educativos. Su objetivo es facilitar la distribución de grupos y mantener la continuidad académica mientras se ajusta la infraestructura definitiva.'),
(12, 'Cancha de futbol sala', 13, 'uploads/cancha_futbol_sala.webp', 0, 140, 290, 'P12', 'Espacio deportivo techado destinado a la práctica de fútbol rápido, con medidas reglamentarias y superficie sintética.'),
(13, 'Canchas de basquet', 13, 'uploads/canchas_basquet.webp', 0, 230, 184, 'P13', 'Dos canchas al aire libre con superficie de concreto y aros reglamentarios para la práctica de baloncesto.'),
(14, 'Club de atletismo', 13, 'uploads/club_atletismo.webp', 0, 250, 80, 'P14', 'Área destinada al almacenamiento de materiales y equipamiento para la práctica de atletismo, así como espacio para reuniones del club deportivo.'),
(15, 'Prefectura', 5, 'uploads/prefectura.webp', 0, 430, 265, 'P15', 'Oficina encargada de mantener el orden y disciplina escolar, así como de gestionar permisos y reportes de los estudiantes.'),
(16, 'Escaleras primer piso A', 9, 'uploads/escaleras_primer_piso_a.webp', 0, 425, 195, 'P16', 'Escalinatas que conectan la planta baja con el primer nivel del edificio A, permitiendo el acceso a salones y áreas administrativas.'),
(17, 'Academia de matematicas', 5, 'uploads/academia_matematicas.webp', 0, 360, 290, 'P17', 'Salón destinado a reuniones y planeación académica del departamento de matemáticas, equipado con recursos didácticos.'),
(18, 'Academia de area social', 5, 'uploads/academia_social.webp', 0, 335, 295, 'P18', 'Espacio de trabajo para los profesores del área social, donde se realizan planeaciones y actividades académicas.'),
(19, 'Servicio medico', 5, 'uploads/servicio_medico.webp', 0, 300, 290, 'P19', 'Área equipada para brindar primeros auxilios y atención médica básica a estudiantes y personal del plantel.'),
(20, 'Salon de maquinas y herramientas', 1, 'uploads/salon_maquinas.webp', 0, 370, 140, 'P20', 'Taller equipado con maquinaria, tornos y herramientas para las prácticas de las carreras de mecatrónica y automatización.'),
(21, 'Papeleria ciberbatis', 13, 'uploads/papeleria_ciberbatis.webp', 0, 485, 300, 'P21', 'Establecimiento que ofrece servicios de papelería, fotocopiado e impresión, ubicado en el área de Ciberbatiz.'),
(22, 'Salon de usos multiples', 1, 'uploads/salon_multiusos.webp', 0, 530, 310, 'P22', 'Aula versátil equipada para diversas actividades académicas, eventos o reuniones especiales.'),
(23, 'Ciberbatis', 13, 'uploads/ciberbatis.webp', 0, 510, 270, 'P23', 'Espacio al aire libre con palapas y mesas para estudio individual o en grupo, con acceso a red inalámbrica.'),

-- Primer piso (floor_level = 1)
(24, 'Baño escaleras edificio B', 7, 'uploads/banos_edificio_b.webp', 1, 450, 265, 'P24', 'Servicios sanitarios ubicados cerca de las escaleras del edificio B, para uso de estudiantes y personal.'),
(25, 'Salones 100', 1, 'uploads/salones_100.webp', 1, 480, 230, 'P25', 'Conjunto de aulas estándar para clases teóricas, numeradas en la serie 100.'),
(26, 'Salones de dibujo tecnico', 1, 'uploads/salones_dibujo.webp', 1, 325, 225, 'P26', 'Aulas especializadas equipadas con mesas de dibujo técnico para las asignaturas de diseño y arquitectura.'),
(27, 'Laboratorio de biologia', 2, 'uploads/laboratorio_biologia.webp', 1, 370, 145, 'P27', 'Espacio equipado con microscopios, especímenes y materiales para prácticas de biología y ciencias afines.'),
(28, 'Salones samsung', 1, 'uploads/salones_samsung.webp', 1, 320, 135, 'P28', 'Laboratorio de tecnología equipado con dispositivos donados por Samsung para prácticas especializadas.'),
(29, 'Laboratorio de sistemas digitales', 2, 'uploads/laboratorio_sistemas.webp', 1, 450, 140, 'P29', 'Espacio con equipos de cómputo y herramientas para el desarrollo de proyectos de sistemas digitales.'),
(30, 'Cubiculos de sistemas digitales', 5, 'uploads/cubiculos_sistemas.webp', 1, 530, 150, 'P30', 'Oficinas para profesores del área de sistemas digitales, equipadas para preparación de clases y asesorías.'),
(31, 'Gestion escolar', 5, 'uploads/gestion_escolar.webp', 1, 420, 400, 'P31', 'Área de atención a estudiantes para trámites académicos, certificados y documentación escolar.'),
(32, 'Subdireccion', 5, 'uploads/subdireccion.webp', 1, 460, 400, 'P32', 'Oficina del subdirector del plantel, encargada de la gestión académica y administrativa secundaria.'),
(33, 'Direccion', 5, 'uploads/direccion.webp', 1, 490, 400, 'P33', 'Oficina principal del director del plantel, donde se toman las decisiones institucionales más importantes.'),
(34, 'Cubiculos administracion', 5, 'uploads/cubiculos_administracion.webp', 1, 415, 480, 'P34', 'Espacios de trabajo para el personal administrativo del plantel, ubicados cerca de las áreas directivas.'),
(35, 'Escaleras edificio B segundo piso', 9, 'uploads/escaleras_segundo_piso_b.webp', 1, 430, 195, 'P35', 'Escalinatas que conectan el primer piso con el segundo nivel del edificio B, dando acceso a más salones y laboratorios.'),

-- Segundo piso (floor_level = 2)
(36, 'Salones 200', 1, 'uploads/salones_200.webp', 2, 440, 230, 'P36', 'Conjunto de aulas estándar para clases teóricas en el segundo piso, numeradas en la serie 200.'),
(37, 'Laboratorio de programacion', 2, 'uploads/laboratorio_programacion.webp', 2, 430, 150, 'P37', 'Espacio equipado con computadoras y software especializado para la enseñanza de lenguajes de programación.'),
(38, 'Laboratorio de base de datos', 2, 'uploads/laboratorio_bases_datos.webp', 2, 475, 150, 'P38', 'Aula especializada en el manejo de sistemas gestores de bases de datos, con equipos y servidores para prácticas.'),
(39, 'UDI', 1, 'uploads/udi.webp', 2, 370, 140, 'P39', 'Sala de Usos Múltiples destinada a talleres, cursos especiales y actividades extracurriculares.'),
(40, 'Baño segundo piso edificio B', 7, 'uploads/banos_segundo_piso.webp', 2, 350, 190, 'P40', 'Servicios sanitarios ubicados en el segundo nivel del edificio B, para uso de estudiantes y profesores.'),
(41, 'Escaleras tercer piso', 9, 'uploads/escaleras_tercer_piso.webp', 2, 430, 195, 'P41', 'Escalinatas que conectan el segundo piso con el tercer nivel del edificio B, dando acceso a aulas superiores.'),

-- Tercer piso (floor_level = 3)
(42, 'Salones 300', 1, 'uploads/salones_300.webp', 3, 440, 230, 'P42', 'Conjunto de aulas estándar para clases teóricas en el tercer piso, numeradas en la serie 300.'),
(43, 'Laboratorio de fisica', 2, 'uploads/laboratorio_fisica.webp', 3, 370, 140, 'P43', 'Espacio equipado con instrumentos y dispositivos para la realización de experimentos y prácticas de física.'),
(44, 'Laboratorio de quimica', 2, 'uploads/laboratorio_quimica.webp', 3, 460, 150, 'P44', 'Área equipada con campanas de extracción, reactivos y materiales para experimentos y prácticas de química.'),
(45, 'Escaleras ultimo piso', 9, 'uploads/escaleras_ultimo_piso.webp', 2, 430, 195, 'P45', 'Escalinatas que conectan el segundo piso con el tercer nivel del edificio B, dando acceso a aulas superiores.');

-- INSERTAR CONEXIONES
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional) VALUES
-- Entrada (1) → Jardineras (2)
-- (1, 2, 130.00, 2.17, 'corridor', TRUE, TRUE, TRUE, NULL),
(1, 2, 100, 1.67, 'corridor', TRUE),
(1, 3, 120.00, 2.00, 'corridor', TRUE),
(3, 4, 50.00, 0.83, 'corridor', TRUE),
(2, 5, 90.00, 1.50, 'corridor', TRUE),
(5, 8, 40.00, 0.67, 'corridor', TRUE),
(5, 6, 60.00, 1.00, 'corridor', TRUE),
(5, 7, 35.00, 0.58, 'corridor', TRUE),
(5, 9, 70.00, 1.17, 'corridor', TRUE),
(2, 10, 85.00, 1.42, 'corridor', TRUE),
(10, 11, 120.00, 2.00, 'corridor', TRUE),
(11, 12, 90.00, 1.50, 'corridor', TRUE),
(11, 13, 80.00, 1.33, 'corridor', TRUE),
(13, 14, 120.00, 2.00, 'corridor', TRUE),
(2, 15, 100.00, 1.67, 'corridor', TRUE),
(15, 17, 60.00, 1.00, 'corridor', TRUE),
(17, 18, 35.00, 0.58, 'corridor', TRUE),
(18, 19, 40.00, 0.67, 'corridor', TRUE),
(15, 16, 50.00, 0.83, 'stairs', TRUE),
(16, 20, 80.00, 1.33, 'corridor', TRUE),
(15, 21, 90.00, 1.50, 'corridor', TRUE),
(21, 22, 50.00, 0.83, 'corridor', TRUE),
(22, 23, 40.00, 0.67, 'corridor', TRUE),
(16, 35, 50.00, 0.83, 'stairs', TRUE),
(35, 25, 60.00, 1.00, 'corridor', TRUE),
(25, 26, 90.00, 1.50, 'corridor', TRUE),
(35, 24, 40.00, 0.67, 'corridor', TRUE),
(35, 27, 75.00, 1.25, 'corridor', TRUE),
(27, 28, 40.00, 0.67, 'corridor', TRUE),
(35, 29, 50.00, 0.83, 'corridor', TRUE),
(29, 30, 60.00, 1.00, 'corridor', TRUE),
(35, 41, 50.00, 0.83, 'stairs', TRUE),
(41, 40, 40.00, 0.67, 'corridor', TRUE),
(41, 36, 60.00, 1.00, 'corridor', TRUE),
(41, 37, 80.00, 1.33, 'corridor', TRUE),
(37, 39, 40.00, 0.67, 'corridor', TRUE),
(37, 38, 35.00, 0.58, 'corridor', TRUE),
(41, 45, 45.00, 0.75, 'stairs', TRUE),
(45, 44, 40.00, 0.67, 'corridor', TRUE),
(45, 42, 50.00, 0.83, 'corridor', TRUE),
(7, 31, 70.00, 1.17, 'corridor', TRUE),
(31, 32, 30.00, 0.50, 'corridor', TRUE),
(32, 33, 30.00, 0.50, 'corridor', TRUE),
(31, 34, 60.00, 1.00, 'corridor', TRUE);

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
    (SELECT COUNT(*) FROM places) AS places,
    (SELECT COUNT(*) FROM connections) AS connections,
    (SELECT COUNT(*) FROM favorite_places) AS favorite_places,
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
