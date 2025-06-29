<?php

require_once 'config/database.php';

class Connection
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function create($from_id, $to_id, $distance, $travel_time, $type = 'corridor', $is_bidirectional = true, $is_accessible = true, $is_active = true, $notes = null)
  {
    $sql = "INSERT INTO connections (from_place_id, to_place_id, distance_m, travel_time_minutes, connection_type, is_bidirectional, is_accessible, is_active, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [$from_id, $to_id, $distance, $travel_time, $type, $is_bidirectional, $is_accessible, $is_active, $notes];
    $this->db->query($sql, $params);

    return $this->db->lastInsertId();
  }


  public function getAllActive()
  {
    $sql = "SELECT c.*,p1.name AS from_name, p2.name AS to_name
            FROM connections c
            JOIN places p1 ON c.from_place_id = p1.id
            JOIN places p2 ON c.to_place_id = p2.id
            WHERE c.is_active = 1";

    return $this->db->fetchAll($sql);
  }

  /**
   * Obtener conexiones desde un lugar
   */
  public function getFromPlace($placeId)
  {
    $sql = "SELECT * FROM connections WHERE from_place_id = ? AND is_active = 1";
    return $this->db->fetchAll($sql, [$placeId]);
  }

  /**
   * Obtener conexiones bidireccionales desde o hacia un lugar
   */
  public function getBidirectionalConnections($placeId)
  {
    $sql = "SELECT * FROM connections
            WHERE is_active = 1
              AND (
                (from_place_id = ?)
                OR (is_bidirectional = 1 AND to_place_id = ?)
              )";
    return $this->db->fetchAll($sql, [$placeId, $placeId]);
  }

  public function deactivate($id)
  {
    $sql = "UPDATE connections SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    return $this->db->query($sql, [$id]);
  }

  public function find($from_id, $to_id)
  {
    $sql = "SELECT * FROM connections WHERE from_place_id = ? AND to_place_id = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$from_id, $to_id]);
  }
}
