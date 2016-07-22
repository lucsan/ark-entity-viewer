<?php
include 'entityReader.php';

//echo '<pre>';
//echo '<pre>', print_r($catWords), '</pre>';
$resultCount = count($results);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <style>
      body {font-family: Tahoma;}
      .categories {background-color: #eeeeee; font-size: large;}
      .catwords { background-color: #eeffee; }
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
      echo '<a class="category ',$category,'" href="entityView.php?list=',$category,'&title">',$category,'</a> (', $count, ') | ';
    }
  ?>
</div>
<br />
<div class="catwords">
  <?php
    if (count($catWords) > 0) {
      foreach ($catWords as $word => $null) {
        echo '<a class="catword ',$word,'" href="entityView.php?list=',$catName,'&title=',$word,'&info" >',$word,'</a> | ';
      }
    }
   ?>
</div>

<br />

<div>
  <table>
  <?php

    foreach ($results as $key => $result) {
      echo '
      <tr><td><a href="entityView.php?title=',$result->title,'&info">',$result->title,'</a></td>
      <td>',$result->info,'</td></tr>';
    }
    ?>

  </table>
</div>

<pre>

  <?php //print_r($results);?>

</pre>
  </body>
</html>
