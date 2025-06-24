<?php

class Database
{
  private $host = 'db';
  private $db_name = 'testdb';
  private $username = 'user';
  private $password = 'userpass';
  private $charset = 'utf8mb4';
  private $pdo;

  public function __construct()
  {
    $this->connect();
  }

  private function connect()
  {
    try {
      $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];

      $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  public function getConnection()
  {
    return $this->pdo;
  }

  public function query($sql, $params = [])
  {
    try {
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  public function fetchOne($sql, $params = [])
  {
    $stmt = $this->query($sql, $params);
    return $stmt->fetch();
  }

  public function fetchAll($sql, $params = [])
  {
    $stmt = $this->query($sql, $params);
    return $stmt->fetchAll();
  }

  public function lastInsertId()
  {
    return $this->pdo->lastInsertId();
  }
}
