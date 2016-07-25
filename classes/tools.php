<?php
namespace ark;
/**
 * General tools not specific to this project, but useful.
 */

$vector = null;

if (!isset($env)) $env = 'dev';
$env = 'live';

switch ($env) {
  case 'live': break;
  default:
  ini_set('display_errors','On');
  error_reporting(E_ALL);
  break;
}

function pr ($data, $title = null, $kill = false)
{
  global $vector, $env;
  if ($env == 'live') return;
  switch ($vector) {
    case 'cli': prc($data, $title, $kill); break;
    case 'wsv': prh($data, $title, $kill); break;
  }
}


function prc ($data, $title = null, $kill = false)
{
  if ($title != null) echo $title, PHP_EOL;
  if (is_object($data) || is_array($data)) {
    print_r($data);
  } else {
    echo $data;
  }
  echo PHP_EOL;
  if ($kill) {
    die($kill);
  }
}

function prh ($data, $title = null, $kill = false)
{
  if ($title != null) echo "<div>$title</div>", PHP_EOL;
  echo '<pre>';
  if (is_object($data) || is_array($data)) {
    print_r($data);
  } else {
    echo $data;
  }
  echo '</pre>', PHP_EOL;
  if ($kill) {
    die($kill);
  }
}


?>
