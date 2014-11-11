<?php 

// For now, this page can just be an empty version of the normal theme.

include "templates/include/header.php"; ?>
 
<?php 
print_r($f->getTemplate());
include ROOT_DIR . "/templates/" . $f->getTemplate() . "/page.php";
//$f->getTemplate();
//echo $page->template;

?>
 
<?php include "templates/include/footer.php"; ?>