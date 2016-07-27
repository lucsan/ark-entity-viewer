<?php

/**
 * rcon console
 */


namespace ark\tool;

include 'classes/rcon.php';
use ark\tool\rcon;

// Pman, 76561198021991420


$env = isset($_ENV['ENV'])? $_ENV['ENV']: 'dev';


if (isset($argv[1])) {
  $rcon = new rcon('94.250.222.67', 7777, 'd1n054ur5');
  if ($argv[1] == '-lp' || $argv[1] == '-listplayers') lp();
  if ($argv[1] == '-gc') gc();
  if ($argv[1] == '-sc' || $argv[1] == 'serverchat') sc($argv);
} else {
  echo 'Usage:- -lp, -gc, -sc message';
}


// $cmds = [];
// if (isset($argv)) {
//   $cmds = cliCmds($argv);
//   foreach ($cmds as $cmd => $qualifyers) {
//     $cmd($qualifyers);
//   }
// }
// print_r($cmds);

//lp();

//$data = constructPayload(12342,2,'serverchat "Hello Peeps."'); // works
//$data = constructPayload(12342,2,'broadcast "Hello Peeps."'); // works
//$data = constructPayload(12342,2,'getchat'); // works



//pm($rcon->send('listplayers'));

//print $response['msg'] . PHP_EOL;

//$response = $rcon->send('serverchatto "76561197970106710" hello there bonkers');
//print_r($response);
//
//pm($rcon->send('serverchat Hello peeps.'));

//pm($rcon->send('getchat'));
//
/**
 * [sc description]
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function sc ($args)
{
  global $rcon;
  if (!isset($args[2])) die ('Requires Message.');
  $cmd = 'serverchat ' . trim($args[2]);
  pm($rcon->send($cmd));
}

/**
 * [listplayers description]
 * @return [type] [description]
 */
function listplayers ()
{
  lp();
}

function gc ()
{
  global $rcon;
  pm($rcon->send('getchat'));
}

function lp ()
{
  global $rcon;
  pm($rcon->send('listplayers'));
}

function pm ($response)
{
  echo $response['msg'], PHP_EOL;
}

function cliCmds ($argv)
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

?>
