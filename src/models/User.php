<?php

require_once 'config/database.php';

class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  /**
   * Create a new user
   */
  public function create($firstname, $lastname, $email, $password, $student_id, $role = 'user')
  {
    // Check if email already exists
    if ($this->findByEmail($email)) {
      return false;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (firstname, lastname, email, password_hash, role, student_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->db->query($sql, [$firstname, $lastname, $email, $passwordHash, $role, $student_id]);

    return $this->db->lastInsertId();
  }


  /**
   * Find user by email
   */
  public function findByEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$email]);
  }

  /**
   * Find user by ID
   */
  public function findById($id)
  {
    $sql = "SELECT * FROM users WHERE id = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$id]);
  }

  /**
   * Find user by student_id
   */
  public function findByUsername($student_id)
  {
    $sql = "SELECT * FROM users WHERE student_id = ? AND is_active = 1";
    return $this->db->fetchOne($sql, [$student_id]);
  }

  /**
   * Verify user credentials
   */
  public function verifyCredentials($email, $password)
  {
    $user = $this->findByEmail($email);

    if ($user && password_verify($password, $user['password_hash'])) {
      return $user;
    }

    return false;
  }

  /**
   * Update user's last login
   */
  public function updateLastLogin($userId)
  {
    $sql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    return $this->db->query($sql, [$userId]);
  }

  /**
   * Get all users (admin only)
   */
  public function getAllUsers()
  {
    $sql = "SELECT id, username, email, role, is_active, created_at FROM users ORDER BY created_at DESC";
    return $this->db->fetchAll($sql);
  }

  /**
   * Update user role
   */
  public function updateRole($userId, $role)
  {
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    return $this->db->query($sql, [$role, $userId]);
  }

  /**
   * Deactivate user
   */
  public function deactivateUser($userId)
  {
    $sql = "UPDATE users SET is_active = 0 WHERE id = ?";
    return $this->db->query($sql, [$userId]);
  }

  /**
   * Activate user
   */
  public function activateUser($userId)
  {
    $sql = "UPDATE users SET is_active = 1 WHERE id = ?";
    return $this->db->query($sql, [$userId]);
  }

  /**
   * Change password
   */
  public function changePassword($userId, $newPassword)
  {
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
    return $this->db->query($sql, [$passwordHash, $userId]);
  }
}
