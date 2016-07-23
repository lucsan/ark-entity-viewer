<?php
/**
 * Requires an entities.json file produced by either htmlParse or PasteParse.
 * Creates data.json containing the entities and vareous sort types.
 */
namespace ark;

$json = file_get_contents('entities.json');
$entities = json_decode($json);

$blueprints = makeAdminLines($entities->entities);

//print_r($entities); die();
$sortings = sortings($blueprints);

$json = json_encode(['blueprints'=> $blueprints, 'sorts' => $sortings]);
file_put_contents('data.json', $json);


function makeAdminLines($items)
{
  foreach ($items as $item) {
    $blueprint = $item->blueprint;
    // Makeing the paths.
    // Remove beginging of string.
    $blueprint = substr($blueprint, 10, strlen($blueprint));
    // Remove quotes.
    $blueprint = str_replace(['"', "'"], '', $blueprint);
    // Remove unwanted (common) path elements.
    $blueprint = str_replace(['/Game/', 'PrimalEarth/', 'CoreBlueprints/'], '', $blueprint);
    $item->bluePath = explode('/', $blueprint);

    // Create admincheat lines.
    $item->giveItem = 'admincheat giveitem ' . $item->blueprint . ' ' . $item->max . ' 100 0';

    if (is_numeric($item->id) && $item->id > -1) {
      $item->giveItemId = 'admincheat giveitemnum ' . $item->id . ' ' . $item->max . ' 100 0';
    }
  }
  return $items;
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
  $new = [];
  $words = explode(' ', $item->title);
  foreach ($words as $word) {
    $word = str_replace(['(', ')'], '', $word);
    $catkeys[$word] = '';
  }
  return $catkeys;
}

?>
