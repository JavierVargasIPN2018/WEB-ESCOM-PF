<?php

require_once 'config/database.php';

class Place
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  /**
   * Create a new place
   */
  public function create($name, $place_type_id, $short_code, $building, $floor_level, $position_x, $position_y, $capacity = null, $description = '', $is_accessible = true, $is_active = true)
  {
    $sql = "INSERT INTO places (name, place_type_id, short_code, building, floor_level, position_x, position_y, capacity, description, is_accessible, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [$name, $place_type_id, $short_code, $building, $floor_level, $position_x, $position_y, $capacity, $description, $is_accessible, $is_active];
    $this->db->query($sql, $params);

    return $this->db->lastInsertId();
  }

  /**
   * Find place by ID
   */
  public function findById($id)
  {
    $sql = "SELECT * FROM places WHERE id = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$id]);
  }

  /**
   * Get all places
   */
  public function getAllPlaces()
  {
    $sql = "SELECT 
              p.*, 
              pt.name AS type_name, 
              pt.icon AS type_icon, 
              pt.color AS type_color
            FROM places p
            JOIN place_types pt ON p.place_type_id = pt.id
            WHERE p.is_active = 1
            ORDER BY p.name ASC";

    $rows = $this->db->fetchAll($sql);

    foreach ($rows as &$row) {
      $row['type'] = [
        'name' => $row['type_name'],
        'icon' => $row['type_icon'],
        'color' => $row['type_color']
      ];
      unset($row['type_name'], $row['type_icon'], $row['type_color']);
    }

    return $rows;
  }

  /**
   * Find place by short code
   */
  public function findByShortCode($short_code)
  {
    $sql = "SELECT * FROM places WHERE short_code = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$short_code]);
  }

  /**
   *  Update place's
   */
  public function update($id, $data)
  {
    $fields = [];
    $params = [];

    foreach ($data as $key => $value) {
      $fields[] = "$key = ?";
      $params[] = $value;
    }

    $params[] = $id;
    $sql = "UPDATE places SET " . implode(", ", $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";

    return $this->db->query($sql, $params);
  }

  /**
   * Deactivate place
   */
  public function deactivate($id)
  {
    $sql = "UPDATE places SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Activate place
   */
  public function activate($id)
  {
    $sql = "UPDATE places SET is_active = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  /**
   * Find place by type
   */
  public function findByType($place_type_id)
  {
    $sql = "SELECT * FROM places WHERE place_type_id = ? AND is_active = 1 ORDER BY name ASC";
    return $this->db->fetchAll($sql, [$place_type_id]);
  }

  /**
   * Find place by building
   */
  public function findByBuilding($building)
  {
    $sql = "SELECT * FROM places WHERE building = ? AND is_active = 1 ORDER BY floor_level ASC, name ASC";
    return $this->db->fetchAll($sql, [$building]);
  }

  /**
   * Get place with type information
   */
  public function getPlaceWithType($placeId)
  {
    $sql = "SELECT 
                p.*,
                pt.name as type_name,
                pt.description as type_description,
                pt.icon as type_icon,
                pt.color as type_color
            FROM places p
            INNER JOIN place_types pt ON p.place_type_id = pt.id
            WHERE p.id = ? AND p.is_active = 1";

    return $this->db->fetchOne($sql, [$placeId]);
  }

  public function getRelatedPlaces($placeId, $limit = 3)
  {
    $place = $this->findById($placeId);
    if (!$place) {
      return [];
    }

    $typeId = $place['place_type_id'];

    // 2. Buscar lugares del mismo tipo, excluyendo el actual
    $sql = "SELECT 
          p.*, 
          pt.name AS type_name, 
          pt.icon AS type_icon, 
          pt.color AS type_color
      FROM places p
      JOIN place_types pt ON p.place_type_id = pt.id
      WHERE p.is_active = 1
        AND p.place_type_id = ?
        AND p.id != ?
      ORDER BY p.name ASC
      LIMIT ?
  ";

    $rows = $this->db->fetchAll($sql, [$typeId, $placeId, $limit]);

    foreach ($rows as &$row) {
      $row['type'] = [
        'name' => $row['type_name'],
        'icon' => $row['type_icon'],
        'color' => $row['type_color']
      ];
      unset($row['type_name'], $row['type_icon'], $row['type_color']);
    }

    return $rows;
  }

  /**
   * Search places
   */
  public function searchPlaces($q = '', $type = '')
  {
    $sql = "SELECT 
              p.*, 
              pt.name AS type_name, 
              pt.icon AS type_icon, 
              pt.color AS type_color
          FROM places p
          JOIN place_types pt ON p.place_type_id = pt.id
          WHERE p.is_active = 1";

    $params = [];

    if ($type > 0) {
      $sql .= " AND p.place_type_id = ?";
      $params[] = $type;
    }

    if (!empty($q)) {
      $sql .= " AND (
                p.name LIKE ?
                OR p.short_code LIKE ?
                OR p.building LIKE ?
                OR pt.name LIKE ?
              )";
      $searchPattern = "%$q%";
      $params = array_merge($params, array_fill(0, 4, $searchPattern));
    }

    $sql .= " ORDER BY p.name ASC";

    $rows = $this->db->fetchAll($sql, $params);

    // Formatear resultados
    foreach ($rows as &$row) {
      $row['type'] = [
        'name' => $row['type_name'],
        'icon' => $row['type_icon'],
        'color' => $row['type_color']
      ];
      unset($row['type_name'], $row['type_icon'], $row['type_color']);
    }

    return $rows;
  }


  /**
   * Count places
   */
  public function countPlaces()
  {
    $sql = "SELECT COUNT(id) as count FROM places";
    return $this->db->fetchOne($sql)["count"];
  }
}
