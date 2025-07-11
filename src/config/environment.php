<?php

$env = realpath(__DIR__ . '/../.env');

if (file_exists($env) && is_readable($env)) {
  $lines = file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
      continue;
    }

    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);

    putenv("{$key}={$value}");
  }
}
