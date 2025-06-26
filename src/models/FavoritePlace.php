<?php

require_once 'config/database.php';

class FavoritePlace
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  /**
   * Create a new favorite place
   */
  public function create($userId, $placeId)
  {
    $sql = "INSERT INTO favorite_places (user_id, place_id) VALUES (?, ?)";

    $this->db->query($sql, [$userId, $placeId]);

    return $this->db->lastInsertId();
  }

  /**
   * Get a user's favorite places
   */
  public function getFavoritePlaces($userId)
  {
    $sql = "SELECT * FROM favorite_places WHERE user_id = ?";
    return $this->db->fetchAll($sql, [$userId]);
  }

  /**
   * Find a user's favorite place by Place ID
   */
  public function findByPlaceId($userId, $placeId)
  {
    $sql = "SELECT id FROM favorite_places WHERE user_id = ? AND place_id = ?";
    return $this->db->fetchOne($sql, [$userId, $placeId]);
  }

  /**
   * Search a user's favorite places (with optional filters)
   */
  public function searchFavorites($userId, $searchName = '', $placeType = '')
  {
    $sql = "
      SELECT 
        fp.id AS favorite_id,
        p.id AS place_id,
        p.name AS place_name,
        p.description AS place_description,
        pt.name AS type_name,
        pt.color,
        pt.icon,
        p.short_code,
        p.image,
        p.building,
        p.floor_level,
        p.position_x,
        p.position_y
      FROM favorite_places fp
      JOIN places p ON fp.place_id = p.id
      JOIN place_types pt ON p.place_type_id = pt.id
      WHERE fp.user_id = ?
    ";

    $params = [$userId];

    if (!empty($searchName)) {
      $sql .= " AND p.name LIKE ?";
      $params[] = '%' . $searchName . '%';
    }

    if (!empty($placeType)) {
      $sql .= " AND pt.name LIKE ?";
      $params[] = '%' . $placeType . '%';
    }

    $sql .= " ORDER BY p.name ASC";

    return $this->db->fetchAll($sql, $params);
  }

  /**
   * Delete a user's favorite place
   */
  public function delete($userId, $placeId)
  {
    $sql = "DELETE FROM favorite_places WHERE user_id = ? AND place_id = ?";
    return $this->db->query($sql, [$userId, $placeId]);
  }

  /**
   * Count a user's favorite place
   */
  public function countFavoritePlace($userId)
  {
    $sql = "SELECT COUNT(id) as count FROM favorite_places WHERE user_id = ?";
    return $this->db->fetchOne($sql, [$userId])["count"];
  }
}
