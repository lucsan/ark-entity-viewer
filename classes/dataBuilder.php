<?php
/**
 *
 */
namespace ark\tool;
class dataBuilder {
  private $results = [];
  private $entities = [];
  private $type = 'entity'; // or dino.
  private $cmds = [];
  private $jsondata = '';
  private $category = '';

  function __construct($type = 'entity')
  {
    if ($type == 'dino') {
      if (!file_exists('data/dinoData.json')) die ('Error: no dinoData.json file.');
      $json = file_get_contents('data/dinoData.json', true);
      if ($json == null) die ('Error: dinoData.json contains no data.');
      $data = json_decode($json);
      $this->entities = $data->dinos;
    } else {
      if (!file_exists('data/entityData.json')) die ('Error: no entityData.json file.');
      $json = file_get_contents('data/entityData.json', true);
      if ($json == null) die ('Error: entityData.json contains no data.');
      $data = json_decode($json);
      $this->entities = $data->entities;
    }
    $this->type = $type;
    $this->categories = $data->sorts->categories;
    $this->catKeys =  (array) $data->sorts->catkeys;
    $this->jsonData = $data;
  }

  public function commands ($cmds)
  {
    $this->cmds = $cmds;
    foreach ($cmds as $cmd => $qualifiers) {
      if ($cmd == 'list')  $cmd = 'mainList';  // Required for php 5. (Dosen't like function list ())
      $this->$cmd($qualifiers);
    }
    return $this->results;
  }

  public function categories ()
  {
    return $this->categories;
  }

  public function catKeys ()
  {
    if ($this->category != null) {
      $category = $this->category;
      if (isset($this->catKeys[$category])) return $this->catKeys[$category];
    }
    return [];
  }

  public function category ()
  {
    return $this->category;
  }

  public function catKey ()
  {

  }

  private function entity ($qualifiers)
  {
    //$this->results[] = $this->categories;
    //$this->results[] = $this->catKeys;
  }

  private function dino ($qualifiers)
  {
    //$this->results[] = $this->categories;
    //$this->results[] = $this->catKeys;
  }

  private function mainList ($qualifiers = null)
  {
    // Default - No qulifiers for list so return categories.
    if ($qualifiers == null) return;
    $this->results = $this->entities;
    foreach ($qualifiers as $key => $qualifyer) {
      if ($key === 0) $this->qualify($qualifyer);
      if ($key === 1) $this->qualify($qualifyer, 'title');
      $count++;
    }
    if ($this->category != null) {
\ark\pr($this->category,'chosen category');
    }
  }

  private function qualify ($qualifyer, $listKey = 'category')
  {
    $new = [];
    foreach ($this->results as $key => $entity) {
      $qualifyer = strtolower($qualifyer);
      $target = strtolower($entity->$listKey);
      if (stripos($target, $qualifyer) > -1) {
        if ($listKey == 'category') $this->category = $target;
        $new[] = $entity;
      }
    }
    $this->results = $new;
  }

  // show only title.
  function titleonly ()
  {
    $new = [];
    foreach($this->results as $result) {
      $obj = (object) [
        'title' => $result->title,
      ];
      $new[] = $obj;
    }
    $this->results = $new;
  }

  function info ()
  {
    $new = [];
    foreach($this->results as $result) {
      if ($this->type == 'dino') {
        $new[] = $this->dinoInfo($result);
      } else {
        $new[] = $this->entityInfo($result);
      }
    }
    $this->results = $new;
  }

  function entityInfo ($result)
  {
    $obj = (object) [
      'id' => $result->id,
      'category' => $result->category,
      'title' => $result->title,
      'giveItem' => $result->giveItem,
      'info' => $result->info,
    ];
    return $obj;
  }

  function dinoInfo ($result)
  {
    $obj = (object) [
      'id' => $result->id,
      'category' => $result->category,
      'title' => $result->title,
      'summon' => $result->summon,
      'spawn' => $result->spawn,
      'info' => $result->info,
    ];
    return $obj;
  }

}
?>
