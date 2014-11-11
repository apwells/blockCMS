<?php 
	// Initialize our functions class.' TODO ! This doesn't work great here as its also called in the admin panel.
	require_once( CLASS_PATH . "/Functions.php" );
  	$f = new Functions($results['page']);
  	session_start();
  	$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";
  	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>La Montanya</title>


<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script> <!-- Draggable -->
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/js/jquery.fancybox.js?v=2.1.5"></script>
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/js/helpers/jquery.fancybox-media.js"></script>
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/js/jquery.getUrlParam.js"></script>
 
<link rel="stylesheet" href="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/css/reset.css" type="text/css" />
<link rel="stylesheet" href="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/css/main.css" type="text/css" />
<link rel="stylesheet" href="<?php echo TEMPLATE_PATH . "/" . $f->getTemplate(); ?>/css/jquery.fancybox.css?v=2.1.5" type="text/css" />

</head>
 