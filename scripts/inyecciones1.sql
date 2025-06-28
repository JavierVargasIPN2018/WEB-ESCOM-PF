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
