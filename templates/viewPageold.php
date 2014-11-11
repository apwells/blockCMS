<?php include "templates/include/header.php" ?>
 
      <h1 style="width: 75%;"><?php echo htmlspecialchars( $results['page']->title )?></h1>
      <div style="width: 75%; font-style: italic;"><?php echo htmlspecialchars( $results['page']->summary )?></div>
      <div style="width: 75%;"><?php echo $results['page']->content?></div>
      <p class="pubDate">Published on <?php echo date('j F Y', $results['page']->publicationDate)?></p>
 
      <p><a href="./">Return to Homepage</a></p>
 
<?php include "templates/include/footer.php" ?>