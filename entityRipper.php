<?php
/**
 * Requires an entities.json file produced by either htmlParse or PasteParse.
 * Creates data.json containing the entities and vareous sort types.
 */
namespace ark;

$json = file_get_contents('data/entities.json');
$entities = json_decode($json);

$json = file_get_contents('data/dinos.json');
$dinos = json_decode($json);

$entities = makeAdminLines($entities->entities, 'entity');
$dinos = makeAdminLines($dinos->dinos, 'dino');

$sortings = sortings($entities);
//\ark\pr($entities,'', 'die');
$json = json_encode(['entities'=> $entities, 'sorts' => $sortings]);
file_put_contents('data/entityData.json', $json);

$sortings = sortings($dinos);

$json = json_encode(['dinos'=> $dinos, 'sorts' => $sortings]);
file_put_contents('data/dinoData.json', $json);


function makeAdminLines($items, $type)
{
  foreach ($items as $item) {
    $item = makeBlueprintPath($item);
    if ($type == 'entity') entityAdminLines($item);
    if ($type == 'dino') dinoAdminLines($item);
  }
  return $items;
}

function dinoAdminLines ($item)
{
  // Create admincheat lines.
  $item->summon = 'admincheat summon ' . $item->blueprint;
  // distance (aprox 2 foundations, y, z, level).
  $item->spawn = 'admincheat spawnDino ' .$item->blueprint . ' 500 0 0 20';

  $item->info = $item->summon;

  return $item;
}

function entityAdminLines ($item)
{
  // Create admincheat lines.
  $item->giveItem = 'admincheat giveitem ' . $item->blueprint . ' ' . $item->max . ' 100 0';
  $item->info = $item->giveItem;
  // If entity has numeric id make an admin line for that too.
  if (is_numeric($item->id) && $item->id > -1) {
    $item->giveItemId = 'admincheat giveitemnum ' . $item->id . ' ' . $item->max . ' 100 0';
    $item->info = $item->giveItemId;
  }

  return $item;
}

function makeBlueprintPath ($item)
{
    $blueprint = $item->blueprint;
    // Makeing the paths.
    // Remove beginging of string.
    $blueprint = substr($blueprint, 10, strlen($blueprint));
    // Remove quotes.
    $blueprint = str_replace(['"', "'"], '', $blueprint);
    // Remove unwanted (common) path elements.
    $blueprint = str_replace(['/Game/', 'PrimalEarth/', 'CoreBlueprints/'], '', $blueprint);
    $item->bluePath = explode('/', $blueprint);

    return $item;
}

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
    $category = strtolower($item->category);
    if (!isset($sorts['categories'][$category])) $sorts['categories'][$category] = 0;
    $sorts['categories'][$category] += 1;
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
  if (!isset($sorts['catkeys'][$category])) {
    $sorts['catkeys'][$category] = [];
  }
  $catkeys = $sorts['catkeys'][$category];
  $sorts['catkeys'][$category] = extractCatkeys($catkeys, $item);
  }
  return $sorts;
}

function extractCatkeys ($catkeys, $item) {
  $new = [];
  $words = explode(' ', $item->title);
  foreach ($words as $word) {
    $word = str_replace(['(', ')'], '', $word);
    $word = strtolower($word);
    if (isset($catKeys[$word])) {
      $catKeys[$word] += 1;
    } else {
      $catkeys[$word] = 1;
    }
  }
  return $catkeys;
}

?>
