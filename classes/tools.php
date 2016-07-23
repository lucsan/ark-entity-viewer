<?php
namespace tools;
/**
 * General tools not specific to this project, but useful.
 */

if (!isset($env)) $env = 'dev';

switch ($env) {
  case 'live': break;
  default:
  ini_set('display_errors','On');
  error_reporting(E_ALL);
  break;
}

?>
