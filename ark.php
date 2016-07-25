<?php
/**
 * Main controller file for the ark admin application.
 */
namespace ark;

require 'config.php';
require 'classes/tools.php';
require 'classes/requestFilter.php';
require 'classes/dataBuilder.php';

use ark\tool\requestFilter;
use ark\tool\dataBuilder;

$data = [];
$type = '';
$rf = new requestFilter();
$vector = $rf->getVector(); // cli or wsv.
$cmds = $rf->getCmds(); // Return a list of commands.

//pr($cmds,'Commands');

if (isset($cmds['dino'])) {
  $type = 'dino';
  $builder = new dataBuilder('dino');
} else {
  $type = 'entity';
  $builder = new dataBuilder('entity');
}

$results = $builder->commands($cmds);
$categories = $builder->categories();
$catWords = $builder->catkeys();

pr($builder->categories(),'Categories');
//pr($catWords,'CatKeys');
//pr($results, 'Results');

?>
