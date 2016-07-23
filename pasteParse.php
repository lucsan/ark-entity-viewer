<?php

//$chunk = []; // Holds the main chunk of data;
//$pre = [];  // pre list page text.
//$entitys = [];  // list of entitys. (ie: dinos);
//$blueprints = []; // Array of item object blueprint details.
//$sortings = []; // Multi-array of things to sort blueprints by (based on blueprint path)

$preSegmentor = 'Simple Pistol.png';
$ItemSegmentor = 'To spawn an entity :';


$data = file_get_contents('entityIds');
$array = explode(PHP_EOL, $data);

$array = segmentor($array, $preSegmentor);
$pre = $array['pre'];
$chunk = $array['chunk'];

//print_r($pre);die();
$array = segmentor($chunk, $ItemSegmentor);
$items = $array['pre'];
$chunk = $array['chunk'];

$items = removeIcons($items);

$items = chunkChomp($items);

//print_r($items);

$json = json_encode(['entities'=> $items]);
file_put_contents('entities.json', $json);

function chunkChomp ($array)
{
  $items = [];
  foreach ($array as $key => $chunk) {
    $data = explode("\t", $chunk);
    if (count($data) < 5) continue;
    $item = (object) [];
    $item->id = trim($data[0]);
    $item->title = $data[1];
    $item->category = $data[2];
    $item->max = $data[3];
    $item->blueprint = $data[4];
    $items[] = $item;
  }
  return $items;
}

function removeIcons ($array)
{
  if (count($array) < 1) return $array;
  $new = [];
  foreach ($array as $key => $item) {
    if (stripos($item, '.png') > -1 ) continue;
    $new[] = $item;
  }
  return $new;
}

function segmentor ($array, $segmentor)
{
  if(count($array) < 1) {
    echo 'empty array at segmentor ', $segmentor, PHP_EOL;
    return $array;
  }

  $pre = [];
  $main = [];
  $found = false;
  foreach ($array as $key => $item) {
    if (trim($item) == $segmentor && $found == false) {
      $found = true;
      return ['chunk' => $array, 'pre' => $pre];
    }
    if (!$found) {
      $pre[] = $item;
      unset($array[$key]);
    }
  }
  if (!$found) echo 'error in segmentor ', $segmentor;
}


?>
