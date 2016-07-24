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
$rf = new requestFilter();
$vector = $rf->getVector();
$cmds = $rf->getCmds();

pr($cmds,'Commands');

if (isset($cmds['dino'])) {
  $builder = new dataBuilder('dino');
} else {
  $builder = new dataBuilder('entity');
}

$results = $builder->commands($cmds);
$categories = $builder->getCategories();
$catWords = $builder->getCatkeys();
pr($builder->getCategories(),'Categories');
pr($builder->getCatkeys(),'CatKeys');


// if (isset($cmds['dinos'])) {
//
// }



pr($results, 'Results');

?>
