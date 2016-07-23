<?php
/**
 * Main controller file for the ark admin application.
 */
namespace ark;
require 'config.php';
require 'classes/tools.php';
require 'classes/requestFilter.php';

use ark\tool\requestFilter;

$rf = new requestFilter();
print_r($rf->getCmds());

?>
