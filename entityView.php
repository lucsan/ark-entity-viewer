<?php
include 'ark.php';

//echo '<pre>';
//print_r($results);
//echo '<pre>', print_r($catWords), '</pre>';
$resultCount = count($results);



?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Entity Viewer</title>
    <style>
      body {font-family: Tahoma;}
      .categories {background-color: #eeeeee; font-size: large;}
      .catwords { background-color: #eeffee; }
      .details {background-color: #eeeeff; }
    </style>
  </head>
  <body>
<div>
<h4>Entity Viewer</h4>
    Entity count: <?=$resultCount;?>
</div>

<div class="categories">
  <?php
    foreach ($categories as $category => $count) {
      echo '<a class="category ',$category,'" href="?',$type,'&list[]=',$category,'">',$category,'</a> (', $count, ') | ';
    }
  ?>
</div>
<br />
<div class="catwords">
  <?php
    if (count($catWords) > 0) {
      foreach ($catWords as $word => $null) {
        echo '<a class="catword ',$word,'" href="?',$type,'&list[]=',$cmds['list'][0],'&list[]=',$word,'" >',$word,'</a> | ';
      }
    }
   ?>
</div>

<br />

<div>
  <table>
  <?php

    foreach ($results as $key => $result) {
      if (isset($_GET['info'])) {
        echo '
        <tr><td><a href="?',$type,'&list[]=',$result->category,'&list[]=',$result->title,'&info">',$result->title,'</a></td>
        </tr>';
        echo '<div class="details" >';
        foreach($result as $name => $value) {
          echo '<tr><td>',$name,'<td>',$value,'</td></tr>';
        }
        echo '</div>';
      } else {
        echo '
        <tr><td><a href="?',$type,'&list[]=',$result->category,'&list[]=',$result->title,'&info">',$result->title,'</a></td>
        <td>',$result->info,'</td></tr>';
      }



    }
    ?>

  </table>
</div>

<pre>

  <?php //print_r($results);?>

</pre>
  </body>
</html>
