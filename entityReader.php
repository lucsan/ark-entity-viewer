<?php
namespace ark;

error_reporting(E_ALL);

$vector = null; // incomming vector ie: cli or webserver.
$blueprints = [];
$categories = [];
$cmds = [];
$results = [];

$json = file_get_contents('data.json');
$data = json_decode($json);

$blueprints = $data->blueprints;
$categories = $data->sorts->categories;
$catKeys = $data->sorts->catkeys;
$catWords = [];
$catName = '';

//print_r($blueprints);die();

if (isset($argv)) {
  $vector = 'cli';
  $cmds = cliCmds($argv);
  doCommands();
}

if (count($_GET) > 0) {
  $vector = 'wsv';
  $cmds = wsvCmds($_GET);
  if (isset($_GET['list'])) {
    $catName = $_GET['list'];
    $catWords = getCatWords($catKeys, $_GET['list']);
  }
  doCommands();
}

cmdResponse($results, 'Results');
cmdResponse(count($results), 'Result Count:');

function getCatWords ($catKeys, $cat)
{
  foreach ($catKeys as $key => $words) {
    if ($key == $cat) {
      return $words;
    }
  }
  return [];
}

function cliCmds ($argv)
{
  global $results;
  $cmds = [];
  array_shift($argv);
  if (count($argv) > 0) {
    $mark = '';
    foreach ($argv as $key => $arg) {
      if (stripos($arg, '-') > -1) {
        $mark = substr($arg, 1, strlen($arg));
        $cmds[$mark] = [];
      } else {
        // Note only the last param after a command is kept.
        $cmds[$mark] = $arg;
      }
    }
  }
  if (count($cmds) < 1) {
    // tool help
    $results[] = "Cli usage: -list -cats -path";
  }
  return $cmds;
}

function wsvCmds ($get)
{
  global $results;
  foreach ($get as $key => $arg) {
    $cmds[$key] = $arg;
  }
  return $cmds;
}

function doCommands ()
{
  global $cmds, $results, $data;
  foreach ($cmds as $cmd => $filter) {
    if ($cmd == 'cats') {
      $results = $data->sorts->categories;
      return;
    }

    if ($cmd == 'list') $results = filterByCategory($filter, $results);

    if ($cmd == 'title') $results = filterResults('title', $filter, $results);

    if ($cmd == 'info') $results = filterResults('info', $filter, $results);

    if ($cmd == 'details') $results = filterResults('details', $filter, $results);
  }

}

// title no filter = return result titles only
// title with filter = return results filtered by title
// info no filter

function filterByCategory ($filter = null, $results = [])
{
  $items = makeList($results);
  if ($filter == null) return $items;
  $new = [];
  foreach ($items as $key => $item) {
    $filter = strtolower($filter);
    $target = strtolower($item->category);
    if (stripos($target, $filter) > -1 ) {
      $new[] = $item;
    }
  }
  return $new;
}

function filterResults ($type = null, $filter = null, $results = [])
{
  $results = makeList($results);
  if ($type == null) return $results;

  if ($type == 'info' || $type == 'details') {
    $results = listInfo($type, $results);
  } else {
    if ($filter) {
      $results = filterByType($type, $filter, $results);
    } else {
      $results = listByType($type, $results);
    }
  }
  return $results;
}

function listByDetail ($results = [])
{
  $new = [];
  foreach ($results as $key => $item) {
    $obj = (object) [
      'title' => $item->title,
      'info' => '',
      'max' => $item->max,
      'blueprint' => $item->blueprint,
    ];
    $obj->info = $item->giveItemId != ''? $item->giveItemId: $item->giveItem;
    $new[] = $obj;
  }
  return $new;
}


function listInfo ($type= 'info', $results = [])
{
  $new = [];
  foreach ($results as $key => $item) {
    $obj = (object) [
      'title' => $item->title,
      'info' => ''
    ];
    if ($type == 'details') {
      $obj->max = $item->max;
      $obj->blueprint = $item->blueprint;
      $obj->giveItem = $item->giveItem;
    }
    $obj->info = $item->giveItemId != ''? $item->giveItemId: $item->giveItem;
    $new[] = $obj;
  }
  return $new;
}

function filterByType ($type = null, $filter = null, $results = [])
{
  $new = [];
  foreach ($results as $key => $item) {
    $filter = strtolower($filter);
    $target = strtolower($item->$type);
    if (stripos($target, $filter) > -1 ) {
      $new[] = $item;
    }
  }
  return $new;
}

function listByType ($type = null, $results = [])
{
  $new = [];
  foreach ($results as $key => $item) {
    $obj = (object) [
      'title' => $item->title,
      'info' => $item->$type
    ];
    if ($type == 'title') $obj->info = $item->giveItemId != ''? $item->giveItemId: $item->giveItem;
    $new[] = $obj;
  }
  return $new;
}

function makeList ($filtered = [])
{
  global $blueprints;
  if (count($filtered) > 0 ) return $filtered;
  return $blueprints;
}

function cmdResponse ($msg = '', $title = null) {
  global $vector;
  if ($vector != 'cli') return;
  if ($title) echo $title, PHP_EOL;
  if (is_array($msg)) {
    print_r($msg);
    echo PHP_EOL;
  }
}


?>
