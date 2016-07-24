<?php
/**
 * requestFilter represents a endpoint, cli or webserver.
 * It take parameters (eg: cli -list all, web ?list[]=all)
 * and parses them into a commands array
 * [list]
 * 	[0] -> all
 * 	[1] -> next one
 * 	Implement
 * 	$rf = new requestFilter();
 *  print_r($rf->getCmds());
 */
namespace ark\tool;

class requestFilter {
  private $vector = '';
  private $cmds = [];

  public function __construct()
  {
    global $argv;
    if (isset($argv)) {
      $this->vector = 'cli';
      $this->cmds = $this->cliCmds($argv);
    }

    if (count($_GET) > 0) {
      $this->vector = 'wsv';
      $this->cmds = $this->wsvCmds($_GET);
    }

    if (count($_POST) > 0) {
      $this->vector = 'wsv';
    }
  }

  public function getVector ()
  {
    return $this->vector;
  }

  public function getCmds ()
  {
    return $this->cmds;
  }

  private function cliCmds ($argv)
  {
    $cmds = [];
    array_shift($argv); // Remove the file name from the $argv stack.
    if (count($argv) > 0) {
      $mark = '';
      foreach ($argv as $key => $arg) {
        if (stripos($arg, '-') > -1) {
          $mark = str_replace('-', '', $arg); // Reomve the leading -.
          $cmds[$mark] = [];
        } else {
          $cmds[$mark][] = $arg;
        }
      }
    }
    return $cmds;
  }

  private function wsvCmds ($get)
  {
    foreach ($get as $key => $arg) {
      if ($arg == null) $arg = [];
      if (is_string($arg)) $arg = [$arg];
        $cmds[$key] = $arg;
    }
    return $cmds;
  }
}
?>
