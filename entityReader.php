<?php

// admincheat giveitem
// GiveItem "Blueprint'/Game/PrimalEarth/CoreBlueprints/Weapons/PrimalItem_WeaponGun.PrimalItem_WeaponGun'" 1 1 0

$cmds = [];
$results = [];

$json = file_get_contents('entitys.json');

$data = json_decode($json);

$blueprints = $data->blueprints;

array_shift($argv);

if (count($argv) > 0) {
  $mark = '';
  foreach ($argv as $key => $arg) {
    if (stripos($arg, '-') > -1) {
      $mark = $arg;
      $cmds[$mark] = [];
    } else {
      $cmds[$mark][] = $arg;
    }
  }

  foreach ($cmds as $cmd => $values) {
    if ($cmd == '-cats') {
      print_r($data->sorts->categories);
    }

    if ($cmd == '-paths') {
      $results = presentPaths($data->sorts->path);
      //print_r($data->sorts->path);
    }

    if ($cmd == '-listcat') {
      $val = isset($values[0])? $values[0]: '';
      $results = listByCategory($val, $results);
    }
    if ($cmd == '-title') {
      $val = isset($values[0])? $values[0]: '';
      $results = listByTitle($val, $results);
      if ($val =='') {
        $titles = [];
        foreach ($results as $item) {
          $titles[] = $item->title;
        }
        $results = $titles;
      }
    }

    if ($cmd == '-titleGive') {
      $titles = [];
      foreach ($results as $item) {
        $titles[] = $item->title . ' ' . $item->giveItem;
      }
      $results = $titles;
    }

    if ($cmd == '-titleOnly') {
      $titles = [];
      foreach ($results as $item) {
        $titles[] = $item->title;
      }
      $results = $titles;
    }

  }

  if (count($results) > 0) {
    echo "RESULTS:" , PHP_EOL;
    print_r($results);
  }

}


function presentPaths ($data = null)
{
  return $data;
}

function presentResults($results)
{
  foreach ($results as $item) {

  }
}


function listByCategory ($find = null, $filtered = null)
{
 $items = makeList($filtered);
  $new = [];
  foreach ($items as $key => $item) {
    if (strtolower($item->category) == strtolower($find)) {
      $new[] = $item;
    }
  }
  return $new;
}

function listByTitle ($find = null, $filtered = [])
{
  $items = makeList($filtered);
  if ($find === '') return $items;
  $new = [];
  foreach ($items as $key => $item) {
    $mark = strtolower($item->title);
    $target = strtolower($find);
    if (stripos($mark, $target) > -1 ) {
      $new[] = $item;
    }
  }
  return $new;
}

function makeList ($filtered = [])
{
  global $blueprints;
  $items = $blueprints;
  if (count($filtered) > 0 ) $items = $filtered;
  return $items;
}

?>
