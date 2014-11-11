<?php
 
/**
 * Class to handle articles
 */
 
class Page
{
 
  // Properties
 
  /**
  * @var int The article ID from the database
  */
  public $id = null;
 
  /**
  * @var int When the article was published
  */
  public $publicationDate = null;
 
  /**
  * @var string Full title of the article
  */
  public $title = null;
 

  public $template = null;
 

  public $parentPage = null;
  
  public $block = null;	// Is an INT id of our FIRST BLOCK
  
  public $blockArray = null; // Is locally created array of Block objects (not stored in DB)
  
  public $isHomepage = null;
 
 
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 
  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
    if ( isset( $data['publicationDate'] ) ) $this->publicationDate = (int) $data['publicationDate'];
    if ( isset( $data['title'] ) ) $this->title = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['title'] );
    if ( isset( $data['template'] ) ) $this->template = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['template'] );
    if ( isset( $data['parentPage'] ) ) $this->parentPage = $data['parentPage'];
    if ( isset( $data['block'] ) ) $this->block = $data['block'];
    if ( isset( $data['isHomepage'] ) ) $this->isHomepage = $data['isHomepage'];
    
    $this->buildBlockArray();

  }
  
  public function buildBlockArray() {
  	// If we have a block on this page, we start constructing the linked list of all blocks
    if (isset($this->block)) {
    	$this->blockArray = array();
    	
    	$newBlock = Block::getById($this->block);
    	array_push($this->blockArray, $newBlock);
    	while (true) {
    		if (isset($newBlock->nextBlock)) {
    			//$blockArray[] = Block::getById($newBlock->nextBlock);
    			$newBlock = Block::getById($newBlock->nextBlock);
    			array_push($this->blockArray, $newBlock);
    		} else {
    			break;
    		}
    	}
    }
  }
  
  
  // We should only access blocks through the Page! As this is the only place where we won't mess with other pages' blocks.
  // TODO : TEST THIS FUNCTION
  public function getBlockByPosition($pos) {
	  if (isset($this->block)) {
		  foreach ($this->blockArray as $block) {
			  if ($block->position == $pos) {
				  return $block;
			  }
		  }
	  }
	  return null;
  }
  
  
  // Sets the first block as given by the param
  
  public function setBlock($blockId) {
	$block = $blockId;
	$this->update();
  }
  
  public function makeHomePage($id) {
  
  // First make all others not homepage
  
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM pages WHERE isHomepage = 1";
    $st = $conn->prepare( $sql );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) {
    	$oldHomepage = new Page ($row);
    	$oldHomepage->isHomepage = false;
    	$oldHomepage->update();
    }
  
  
  // CAREFUL OF THE USE OF ID AS A PARAMETER IN THIS FUNCTION... TODO
  
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM pages WHERE id = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) {
    	$homepage = new Page ($row);
    	$homepage->isHomepage = true;
    	$homepage->update();
    }
  }
  
  public static function getHomepage() {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM pages WHERE isHomepage = 1";
    $st = $conn->prepare( $sql );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Page( $row );
  }
 
 
  /**
  * Sets the object's properties using the edit form post values in the supplied array
  *
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {
 
    // Store all the parameters
    $this->__construct( $params );
 

    $this->publicationDate = date("Y-m-d H:i:s");
  }
 
 
  /**
  * Returns an Article object matching the given article ID
  *
  * @param int The article ID
  * @return Article|false The article object, or false if the record was not found or there was a problem
  */
 
  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM pages WHERE id = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Page( $row );
  }
 
 
  /**
  * Returns all (or a range of) Article objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the articles (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Article objects; totalRows => Total number of articles
  */
 
  public static function getList( $numRows=1000000, $order="publicationDate DESC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM pages
            ORDER BY " . $order . " LIMIT :numRows";
 
    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();
 
    while ( $row = $st->fetch() ) {
      $article = new Page( $row );
      $list[] = $article;
    }
 
    // Now get the total number of articles that matched the criteria
    $sql = "SELECT FOUND_ROWS() AS totalRows";
    $totalRows = $conn->query( $sql )->fetch();
    $conn = null;
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }
 
 
  /**
  * Inserts the current Article object into the database, and sets its ID property.
  */
 
  public function insert() {
  
 
    // Does the Article object already have an ID?
    if ( !is_null( $this->id ) ) trigger_error ( "Page::insert(): Attempt to insert an Page object that already has its ID property set (to $this->id).", E_USER_ERROR );
 
	// Insert a new Block
	// should we query the template?? 

    // Insert the Article
    
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO pages ( publicationDate, title, template, block, parentPage ) values ( :publicationDate, :title, :template, :block, :parentPage )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":publicationDate", $this->publicationDate, PDO::PARAM_STR );
    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );
    $st->bindValue( ":template", $this->template, PDO::PARAM_STR );
    $st->bindValue( ":block", $this->block, PDO::PARAM_INT );
    $st->bindValue( ":parentPage", $this->parentPage, PDO::PARAM_INT );
    $st->execute();
    $this->id = $conn->lastInsertId();
    $conn = null;
  }
 
 
  /**
  * Updates the current Article object in the database.
  */
 
  public function update() {
 
    // Does the Article object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "Page::update(): Attempt to update an Page object that does not have its ID property set.", E_USER_ERROR );
    
    // Update the Article
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE pages SET title=:title, template=:template, block=:block, parentPage=:parentPage, isHomepage=:isHomepage WHERE id = :id";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );
    $st->bindValue( ":template", $this->template, PDO::PARAM_STR );
    $st->bindValue( ":block", $this->block, PDO::PARAM_INT );
    $st->bindValue( ":parentPage", $this->parentPage, PDO::PARAM_INT );
    
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->bindValue( ":isHomepage", $this->isHomepage, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }
 
 
  /**
  * Deletes the current Article object from the database.
  */
 
  public function delete() {
 
    // Does the Article object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "Page::delete(): Attempt to delete an Page object that does not have its ID property set.", E_USER_ERROR );
 
 	foreach($this->blockArray as $block) {
 		$block->delete();
 	}
  
    // Delete the Page
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM pages WHERE id = :id LIMIT 1" );
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }
 
}