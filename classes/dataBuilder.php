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
    $this->catKeys = $data->sorts->catkeys;
    $this->jsonData = $data;
    // \ark\pr($this->categories,'categories', 'ended');
  }

  public function commands ($cmds)
  {
    //\ark\pr($cmds, 'commands');
    $this->cmds = $cmds;
    foreach ($cmds as $cmd => $qualifiers) {
      if ($cmd == 'list')  $cmd = 'mainList';  // Required for php 5. (Dosen't like function list ())
        //\ark\pr($qualifiers, 'qualifs');
      // 3 Conditions -
      // 1 command is a filter
      // 2 command is to present
      // 3 command defaults to filter
      $this->$cmd($qualifiers);

    }
    return $this->results;
  //$test = function () {};
  }

  public function getCategories ()
  {
    return $this->categories;
  }

  public function getCatKeys()
  {
    return $this->catKeys;
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
    if ($qualifiers == null) {
      $this->results = $this->categories;
      return $this;
    }
    $count = 0;
    $this->results = $this->entities;
    foreach ($qualifiers as $qualifyer) {
      if ($count === 0) $this->qualify($qualifyer);
      if ($count === 1) $this->qualify($qualifyer, 'title');
      $count++;
    }

    if ($this->category != null) {
      //\ark\pr($this->catKeys,'','end');
      \ark\pr($this->category,'chosen category');
      $new = [];
      foreach ($this->catKeys as $key => $list) {
        $target = strtolower($key);
        if (stripos($target, $this->category) > -1) {
          $new[] = $list;
        }
      }
      $this->catKeys = $new;
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
      $new[] = $result->title;
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
    ];
    return $obj;
  }

  /**
   * Filters the entity list by command.
   * @return [type] [description]
   */
  private function filter ($cmd, $qualifiers)
  {

  }

  /**
   * Presents the remaing data (ie: removes unwanted entity attributes.)
   * @return [type] [description]
   */
  private function presenter ()
  {

  }
}
?>
