-- ====================================================================
-- SISTEMA DE NAVEGACIÓN DE CAMPUS - MariaDB
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
-- Propósito: Catálogo de tipos de lugares del campus
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
-- Propósito: Lugares físicos del campus
-- ====================================================================
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    place_type_id INT NOT NULL,
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

-- Insertar 10 lugares de ejemplo en el campus
INSERT INTO places (name, place_type_id, short_code, building, floor_level, position_x, position_y, capacity, description)
VALUES
('Aula 101', 1, 'A101', 'Edificio A', 1, 10.5, 20.3, 35, 'Salón de clases para materias generales'),
('Laboratorio de Física', 2, 'LAB1', 'Edificio B', 2, 15.2, 18.7, 20, 'Laboratorio equipado para prácticas de física'),
('Biblioteca Central', 3, 'BIB', 'Edificio C', 0, 12.0, 25.5, 100, 'Espacio de estudio y consulta de libros'),
('Auditorio Principal', 4, 'AUD1', 'Edificio D', 0, 5.5, 30.0, 250, 'Auditorio para conferencias y eventos'),
('Oficina de Dirección', 5, 'DIR', 'Edificio A', 1, 11.0, 21.0, 5, 'Oficina del director académico'),
('Cafetería Estudiantil', 6, 'CAF1', 'Edificio E', 0, 8.0, 28.5, 80, 'Área de alimentos con menú variado'),
('Baño Planta Baja A', 7, 'BANO1', 'Edificio A', 0, 10.0, 20.0, NULL, 'Servicios sanitarios planta baja'),
('Pasillo Norte A', 8, 'PAS1', 'Edificio A', 1, 10.8, 20.5, NULL, 'Pasillo que conecta aulas del ala norte'),
('Escalera Principal B', 9, 'ESC1', 'Edificio B', 0, 15.0, 18.5, NULL, 'Escalera principal de acceso a pisos superiores'),
('Ascensor Edificio C', 10, 'ELEV1', 'Edificio C', 0, 12.5, 25.8, NULL, 'Ascensor accesible entre todos los pisos');

-- Insertar conexiones de ejemplo entre los lugares
-- Estas conexiones crearán un mapa lógico del campus

-- Conexiones del Aula 101 (ID: 1)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(1, 8, 5.0, 0.5, 'corridor', TRUE, TRUE),     -- Aula 101 → Pasillo Norte A
(1, 5, 8.0, 1.0, 'corridor', TRUE, TRUE),     -- Aula 101 → Oficina de Dirección
(1, 7, 25.0, 2.0, 'stairs', TRUE, FALSE);     -- Aula 101 → Baño Planta Baja A

-- Conexiones del Laboratorio de Física (ID: 2)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(2, 9, 12.0, 1.5, 'corridor', TRUE, TRUE),    -- Lab Física → Escalera Principal B
(2, 1, 35.0, 4.0, 'corridor', TRUE, TRUE);    -- Lab Física → Aula 101

-- Conexiones de la Biblioteca Central (ID: 3)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(3, 10, 8.0, 1.0, 'corridor', TRUE, TRUE),    -- Biblioteca → Ascensor Edificio C
(3, 6, 45.0, 5.0, 'outdoor', TRUE, TRUE),     -- Biblioteca → Cafetería
(3, 4, 55.0, 6.0, 'outdoor', TRUE, TRUE);     -- Biblioteca → Auditorio

-- Conexiones del Auditorio Principal (ID: 4)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(4, 6, 30.0, 3.5, 'outdoor', TRUE, TRUE),     -- Auditorio → Cafetería
(4, 3, 55.0, 6.0, 'outdoor', TRUE, TRUE);     -- Auditorio → Biblioteca

-- Conexiones de la Oficina de Dirección (ID: 5)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(5, 1, 8.0, 1.0, 'corridor', TRUE, TRUE),     -- Oficina Dirección → Aula 101
(5, 8, 6.0, 0.8, 'corridor', TRUE, TRUE);     -- Oficina Dirección → Pasillo Norte A

-- Conexiones de la Cafetería (ID: 6)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(6, 4, 30.0, 3.5, 'outdoor', TRUE, TRUE),     -- Cafetería → Auditorio
(6, 3, 45.0, 5.0, 'outdoor', TRUE, TRUE);     -- Cafetería → Biblioteca

-- Conexiones del Baño Planta Baja A (ID: 7)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(7, 8, 15.0, 1.8, 'stairs', TRUE, FALSE),     -- Baño → Pasillo Norte A
(7, 1, 25.0, 2.0, 'stairs', TRUE, FALSE);     -- Baño → Aula 101

-- Conexiones del Pasillo Norte A (ID: 8)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(8, 1, 5.0, 0.5, 'corridor', TRUE, TRUE),     -- Pasillo → Aula 101
(8, 5, 6.0, 0.8, 'corridor', TRUE, TRUE),     -- Pasillo → Oficina Dirección
(8, 7, 15.0, 1.8, 'stairs', TRUE, FALSE);     -- Pasillo → Baño

-- Conexiones de la Escalera Principal B (ID: 9)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(9, 2, 12.0, 1.5, 'stairs', TRUE, FALSE),     -- Escalera → Lab Física
(9, 10, 50.0, 5.5, 'outdoor', TRUE, TRUE);    -- Escalera → Ascensor (conexión entre edificios)

-- Conexiones del Ascensor Edificio C (ID: 10)
INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible) VALUES
(10, 3, 8.0, 1.0, 'elevator', TRUE, TRUE),    -- Ascensor → Biblioteca
(10, 9, 50.0, 5.5, 'outdoor', TRUE, TRUE);    -- Ascensor → Escalera (conexión entre edificios)

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
