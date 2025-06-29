<?php
function renderView($viewPath, $data = [])
{
  extract($data);
  include 'components/layouts/header.php';
  include $viewPath;
  include 'components/layouts/footer.php';
}

function renderAttributes($attributes = [])
{
  $attrs = '';
  foreach ($attributes as $key => $value) {
    if ($value !== null && $value !== false) {
      $attrs .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }
  }
  return $attrs;
}

function mergeAttributes($defaults, $custom = [])
{
  return array_merge($defaults, $custom);
}

function dijkstra($graph, $start, $end)
{
  $distances = [];
  $previous = [];
  $nodes = new SplPriorityQueue();

  foreach ($graph as $node => $edges) {
    if ($node == $start) {
      $distances[$node] = 0;
      $nodes->insert($node, 0);
    } else {
      $distances[$node] = INF;
      $nodes->insert($node, -INF);
    }
    $previous[$node] = null;
  }

  while (!$nodes->isEmpty()) {
    $smallest = $nodes->extract();

    if ($smallest == $end) {
      $path = [];
      while ($previous[$smallest]) {
        $path[] = $smallest;
        $smallest = $previous[$smallest];
      }
      $path[] = $start;
      return array_reverse($path);
    }

    if (!isset($graph[$smallest])) {
      continue;
    }

    foreach ($graph[$smallest] as $neighbor => $cost) {
      $alt = $distances[$smallest] + $cost;
      if ($alt < $distances[$neighbor]) {
        $distances[$neighbor] = $alt;
        $previous[$neighbor] = $smallest;
        $nodes->insert($neighbor, -$alt);
      }
    }
  }

  return [];
}
