<?php

$chunk = []; // Holds the main chunk of data;
$pre = [];  // pre list page text.
$entitys = [];  // list of entitys. (ie: dinos);
$blueprints = []; // Array of item object blueprint details.
$sortings = []; // Multi-array of things to sort blueprints by (based on blueprint path)

$preSegmentor = 'Icon	Item ID	Name	Category	Stack Size	Blueprint Path';
$ItemSegmentor = 'To spawn an entity :';


$data = file_get_contents('entityIds');
$array = explode(PHP_EOL, $data);



$array = segmentor($array, $preSegmentor);
$pre = $array['pre'];
$chunk = $array['chunk'];


$array = segmentor($chunk, $ItemSegmentor);
$items = $array['pre'];
$chunk = $array['chunk'];

$items = removeIcons($items);



//print_r($items);

$blueprints = parseBlueprint($items);

$sortings = sortings($blueprints);

//print_r($blueprints);

$json = json_encode(['blueprints'=> $blueprints, 'sorts' => $sortings]);
file_put_contents('entitys.json', $json);

//getIds($items);
//

function sortings ($array)
{
  $sorts = [];
  $sorts['categories'] = [];
  $sorts['path'] = [];

  foreach ($array as $key => $item) {
    if (!isset($sorts['categories'][$item->category])) $sorts['categories'][$item->category] = 0;
    $sorts['categories'][$item->category] += 1;

    if (!isset($sorts['path'][$item->bluePath[0]])) $sorts['path'][$item->bluePath[0]]['count'] = 0;

    $sorts['path'][$item->bluePath[0]]['count']  += 1;

     if (count($item->bluePath) > 2) {
       if (!isset($sorts['path'][$item->bluePath[0]][$item->bluePath[1]])) {
         $sorts['path'][$item->bluePath[0]][$item->bluePath[1]]['count'] = 0;
       }
       $sorts['path'][$item->bluePath[0]][$item->bluePath[1]]['count'] += 1;
    }

    if (count($item->bluePath) > 3) {
      if (!isset($sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]])) {
        $sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]]['count'] = 0;
      }
      $sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]]['count'] += 1;
   }

   if (count($item->bluePath) > 4) {
     if (!isset($sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]][$item->bluePath[3]])) {
       $sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]][$item->bluePath[3]]['count'] = 0;
     }
     $sorts['path'][$item->bluePath[0]][$item->bluePath[1]][$item->bluePath[2]][$item->bluePath[3]]['count'] += 1;
  }

  }
  return $sorts;
}


function orderByBluePath ($array)
{
  $new = [];
  foreach ($array as $key => $item) {
    $new[$item->bluePath[0]][] = $item;
  }

  print_r($new);
}


function parseBlueprint ($array)
{
  $items = [];
  foreach ($array as $key => $item) {
    $data = explode("\t", $item);
    if (count($data) < 5) continue;
    $inst = new item();
    if (is_numeric($data[0])) $inst->id = $data[0];
    $inst->title = $data[1];
    $inst->category = $data[2];
    $inst->max = $data[3];
    $inst->setBlueprint($data[4]);

    $items[] = $inst;
  }
  return $items;
}


function getIds ($array)
{
  $hasId =[];
  $noId = [];
  foreach ($array as $key => $item) {
    $data = explode("\t", $item);
print_r($data);
    echo  PHP_EOL;




    if (is_numeric($data[0]) - 0) {
      $hasId[] = $item;
    }else {
      $noId[] = $item;
    }
  }
  return ['has' => $hasId, 'not' => $noId];
}





function removeIcons ($array)
{
  $new = [];
  foreach ($array as $key => $item) {
    if (stripos($item, '.png') > -1 ) continue;
    $new[] = $item;
  }
  return $new;
}


function segmentor ($array, $Segmentor)
{
  $pre = [];
  $main = [];
  $found = false;
  foreach ($array as $key => $item) {
    if ($item == $Segmentor && $found == false) {
      $found = true;
      return ['chunk' => $array, 'pre' => $pre];
    }
    if (!$found) {
      $pre[] = $item;
      unset($array[$key]);
    }
  }
}


class item {
  public $id = -1;
  public $title = '';
  public $max = 1;
  public $category = '';
  public $blueprint = '';
  public $bluePath = [];
  public $giveItem = '';

  public function __constuct ($id = null)
  {
    $this->id = $id;
  }

  public function setBlueprint (String $blueprintString) {
    $blueprintString = trim($blueprintString);
    $this->blueprint = $blueprintString;
// Check for non-standard blueprint path.
//if (stripos($blueprintString, "Blueprint '/Game/PrimalEarth/") == -1) echo $blueprintString;

  // Remove beginging of string.
    $blueprintString = substr($blueprintString, 10, strlen($blueprintString));
    // Remove quotes.
    $blueprintString = str_replace(['"', "'"], '', $blueprintString);

    $blueprintString = str_replace(['/Game/', 'PrimalEarth/', 'CoreBlueprints/'], '', $blueprintString);

    $this->bluePath = explode('/', $blueprintString);

    echo $this->max, PHP_EOL;

    $this->giveItem = 'admincheat giveitem ' . $this->blueprint . ' ' . $this->max . ' 100 0';

    //$this->blueCategory = substr($blueprintString, 0, stripos($blueprintString, '/'));







    //echo $blueprintString, PHP_EOL;
  }


}



?>
