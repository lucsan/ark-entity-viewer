<?php
namespace ark;
$output = '';
$cu = curl_init('ark.gamepedia.com/Entity_IDs');
curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
//if ($output = curl_exec($cu) === false) die('error: ' . curl_error($cu));
$output = curl_exec($cu);
if ($output === false) die('error: ' . curl_error($cu));
curl_close($cu);
//echo $output;
//$output = file_get_contents('entityIdSource');
$array = explode('<table', $output);

//print_r($array); die();
$array = explode('</table>', $array[1]);

$rawEntity = $array[0];
$items = chunkChomp($rawEntity);


$json = json_encode(['entities'=> $items]);
file_put_contents('entities.json', $json);
//print_r($items);
//file_put_contents('entityIdChop', print_r($array[1], true));

function chunkChomp ($rawEntity)
{
  $items = [];
  $trChunks = explode('<tr>', $rawEntity);
  foreach ($trChunks as $key => $chunks) {
    $divChunks = explode('</div>', $chunks);
    if (!isset($divChunks[2])) continue;

    $tdChunks = explode('<td>', $divChunks[2]);
    //echo ' td cnt: ', count($tdChunks), PHP_EOL;
    $item = (object) [];
    $item->id = extractId($tdChunks[1]);
    $item->title = extractTitle($tdChunks[2]);
    $item->category = extractCategory($tdChunks[3]);
    $item->max = extractStack($tdChunks[4]);
    $item->blueprint = extractBlueprint($tdChunks[5]);
    $items[] = $item;
  }
  return $items;
}

function extractBlueprint ($chunk)
{

  $chunks = explode('"', $chunk);
  if (!isset($chunks[3])) return null;
  $data = '"' . trim($chunks[3]) . '"';
  return $data;
}

function extractStack ($chunk)
{
  $data = str_replace('</td>', '', $chunk);
  return trim($data);
}

function extractCategory ($chunk)
{

  $chunks = explode('"', $chunk);
  $array = explode(':', $chunks[3]);
  //echo ' ar cnt: ', count($array), PHP_EOL;
  if (count($array) === 1) {
    $array = explode('=', $chunks[1]);
    $array = explode('&', $array[1]);
    $array = explode(':', $array[0]);
  }
  return trim($array[1]);
}

function extractTitle ($chunk)
{
  $chunks = explode('"', $chunk);
  return trim($chunks[3]);
}

function extractId ($chunk)
{
  return trim(str_replace('</td>', '', $chunk));
}

?>
