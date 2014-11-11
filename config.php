<?php
$siteFolder = "/lamontanyacustom"; // CHANGE THIS! To wherever the CMS is located in relation to the domain.
ini_set( "display_errors", true );
error_reporting(E_ALL);
date_default_timezone_set( "Europe/London" );  // http://www.php.net/manual/en/timezones.php
define( "DB_DSN", "mysql:host=localhost;dbname=mydb" );
define( "DB_USERNAME", "root" );
define( "DB_PASSWORD", "root" );
define( "CLASS_PATH", "classes" );
define( "TEMPLATE_PATH", "templates" );
define( "HOMEPAGE_NUM_ARTICLES", 5 ); // DELETE
define( "ADMIN_USERNAME", "admin" );
define( "ADMIN_PASSWORD", "mypass" );
define( 'ROOT_DIR', dirname(__FILE__) );
define( 'SERVER_ABSOLUTE', $_SERVER['DOCUMENT_ROOT'] . $siteFolder );	
define( 'HOST_URL', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $siteFolder ) ;
require( CLASS_PATH . "/Page.php" );
require( CLASS_PATH . "/Block.php" );

function handleException( $exception ) {
  echo "Sorry, a problem occurred. Please try later.";
  error_log( $exception->getMessage() );
}
 
set_exception_handler( 'handleException' );
?>