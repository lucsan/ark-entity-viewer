<?php

$chunk = []; // Holds the main chunk of data;
$pre = [];  // pre list page text.
$entitys = [];  // list of entitys. (ie: dinos);
$blueprints = []; // Array of item object blueprint details.
$sortings = []; // Multi-array of things to sort blueprints by (based on blueprint path)

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

$blueprints = parseBlueprint($items);

$sortings = sortings($blueprints);

$json = json_encode(['blueprints'=> $blueprints, 'sorts' => $sortings]);
file_put_contents('entitys.json', $json);

/**
 * Runs Through blueprint array and create vareous sort possibilites (ie: categories).
 * @param  Array $array Some data (maybe blueprint)
 * @return Array        Multi-array of vareous sortings.
 */
function sortings ($array)
{
  $sorts = [];
  $sorts['categories'] = []; // Extracted blueprint categories.
  $sorts['path'] = []; // Extracted path elements.
  $sorts['catkeys'] = []; // Key words used in item titles by category.

  // Categories
  foreach ($array as $key => $item) {
    if (!isset($sorts['categories'][$item->category])) $sorts['categories'][$item->category] = 0;
    $sorts['categories'][$item->category] += 1;
    // Paths
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

  // catkeys
  if (!isset($sorts['catkeys'][$item->category])) {
    $sorts['catkeys'][$item->category] = [];
  }
  $catkeys = $sorts['catkeys'][$item->category];
  $sorts['catkeys'][$item->category] = extractCatkeys($catkeys, $item);

  }
  return $sorts;
}

function extractCatkeys ($catkeys, $item) {
  //print_r($item->title);
  $new = [];
  $words = explode(' ', $item->title);
  foreach ($words as $word) {
    $word = str_replace(['(', ')'], '', $word);
    $catkeys[$word] = '';
  }
  return $catkeys;
}


/**
 * Parses raw input data into item blueprint array data.
 * @param  [type] $array [description]
 * @return [type]        [description]
 */
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


class item {
  public $id = -1;
  public $title = '';
  public $max = 1;
  public $category = '';
  public $blueprint = '';
  public $bluePath = [];
  public $giveItem = '';
  public $giveItemId = '';

  public function __constuct ($id = null)
  {
    $this->id = $id;
  }

  public function setBlueprint ($blueprintString) {
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

    //echo $this->max, PHP_EOL;

    $this->giveItem = 'admincheat giveitem ' . $this->blueprint . ' ' . $this->max . ' 100 0';

    if ($this->id > -1) {
      $this->giveItemId = 'admincheat giveitemnum ' . $this->id . ' ' . $this->max . ' 100 0';
    }
    //$this->blueCategory = substr($blueprintString, 0, stripos($blueprintString, '/'));







    //echo $blueprintString, PHP_EOL;
  }


}


/*

[19] => Changing the 0 to a 1 in either of these examples will give you a blueprint of the item instead.
[20] => Note: Blueprint paths (or any UE4 asset path) are not case sensitive. The case shown below is how it appears in the directory structure as it was compiled.
[21] => For all eggs of breedable creatures (e.g. not for Titanboa) there is also a fertilized version available. Add »_Fertilized« two times like so:
[22] => "Blueprint'/Game/PrimalEarth/Test/PrimalItemConsumable_Egg_Stego.PrimalItemConsumable_Egg_Stego'"
[23] => "Blueprint'/Game/PrimalEarth/Test/PrimalItemConsumable_Egg_Stego_Fertilized.PrimalItemConsumable_Egg_Stego_Fertilized'"
[24] => Icon        Item ID Name    Category        Stack Size      Blueprint Path
[25] => Simple Pistol.png
[26] => 1   Simple Pistol   Weapons 1       "Blueprint'/Game/PrimalEarth/CoreBlueprints/Weapons/PrimalItem_WeaponGun.PrimalItem_WeaponGun'"
[27] => Assault Rifle.png


 */

?>
