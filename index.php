<?php
 
require( "config.php" );
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
 
switch ( $action ) {
  case 'archive':
    archive();
    break;
  case 'viewPage':
    viewPage();
    break;
  default:
    homepage();
}
 
 
function archive() {
  $results = array();
  $data = Page::getList();
  $results['pages'] = $data['results'];
  $results['totalRows'] = $data['totalRows'];
  $results['pageTitle'] = "Page Archive | Widget News";
  require( TEMPLATE_PATH . "/archive.php" );
}
 
function viewPage() {
  if ( !isset($_GET["pageId"]) || !$_GET["pageId"] ) {
    homepage();
    return;
  }
 
  $results = array();
  $results['page'] = Page::getById( (int)$_GET["pageId"] );
  $results['pageTitle'] = $results['page']->title . " | Widget News";
  require( TEMPLATE_PATH . "/viewPage.php" );
}
 
function homepage() {
  $results = array();
  $results['page'] = Page::getHomepage();
  
  if (!isset($results['page'])) {
  	echo "NO HOMEPAGE!";
  	return;
  } else {
  }
  
  
  require( TEMPLATE_PATH . "/homepage.php" );
  
}
 
?>