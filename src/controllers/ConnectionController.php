<?php

require_once 'models/Connection.php';

class ConnectionController
{
  private $connectionModel;
  private $connections;

  public function __construct()
  {
    $this->connectionModel = new Connection();
  }


  public function getAllConnections()
  {
    return $this->connectionModel->getAllActive();
  }

  public function getPath($placeId)
  {
    $graph = [];
    $connections = $this->connectionModel->getAllActive();

    foreach ($connections as $conn) {
      $from = $conn['from_place_id'];
      $to = $conn['to_place_id'];
      $dist = floatval($conn['distance_m']);

      $graph[$from][$to] = $dist;
      if ($conn['is_bidirectional']) {
        $graph[$to][$from] = $dist;
      }
    }

    $path = dijkstra($graph, 1, $placeId);
    $this->connections = $connections;

    return $path;
  }

  public function getConnectionsForPath($path)
  {
    $results = [];
    $allConnections = $this->connections ?? $this->getAllConnections();

    for ($i = 0; $i < count($path) - 1; $i++) {
      $from = $path[$i];
      $to = $path[$i + 1];

      $found = null;

      foreach ($allConnections as $conn) {
        if (
          ($conn['from_place_id'] == $from && $conn['to_place_id'] == $to) ||
          ($conn['is_bidirectional'] && $conn['from_place_id'] == $to && $conn['to_place_id'] == $from)
        ) {
          $found = $conn;
          break;
        }
      }

      if ($found) {
        $results[] = $found;
      } else {
        throw new Exception("No se encontró conexión entre $from y $to");
      }
    }

    return $results;
  }
}
