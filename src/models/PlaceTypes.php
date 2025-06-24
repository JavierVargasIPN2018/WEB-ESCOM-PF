<?php

require_once 'config/database.php';

class PlaceTypes
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  /**
   * Create a new place type
   */
  public function create($name, $description, $icon, $color)
  {


    $sql = "INSERT INTO place_types (name, description, icon, color) VALUES (?, ?, ?, ?)";
    $stmt = $this->db->query($sql, [$name, $description, $icon, $color]);

    return $this->db->lastInsertId();
  }

  /**
   * Find user by ID
   */
  public function findById($id)
  {
    $sql = "SELECT * FROM place_types WHERE id = ?";
    return $this->db->fetchOne($sql, [$id]);
  }

  /**
   * Find user by name
   */
  public function findByName($name)
  {
    $sql = "SELECT * FROM place_types WHERE name = ?";
    return $this->db->fetchOne($sql, [$name]);
  }

  /**
   * Get all places types
   */
  public function getAllPlaceTypes()
  {
    $sql = "SELECT id, name, description, icon, color, created_at FROM place_types ORDER BY created_at DESC";
    return $this->db->fetchAll($sql);
  }

  /**
   * Count places types
   */
  public function countPlaceTypes()
  {
    $sql = "SELECT COUNT(id) as count FROM place_types";
    return $this->db->fetchOne($sql)["count"];
  }

  /**
   * Update place types name
   */
  public function updateName($place_type_id, $name)
  {
    $sql = "UPDATE place_types SET name = ? WHERE id = ?";
    return $this->db->query($sql, [$name, $place_type_id]);
  }
}
