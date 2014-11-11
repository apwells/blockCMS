<?php

/*
** Class to handle Blocks
*/

class Block 
{

	public $id = null;
	public $name = null;
	public $type = null;
	public $content = null;
	public $content2 = null;
	public $nextBlock = null;
	public $position = null;


	public function __construct( $data = array() ) {
	
		if ( isset( $data['id'] )) $this->id = (int) $data['id'];
		if ( isset( $data['name'] )) $this->name = $data['name'];
		if ( isset( $data['type'] )) $this->type = $data['type'];
		if ( isset( $data['content'] )) $this->content = $data['content'];
		if ( isset( $data['content2'] )) $this->content2 = $data['content2'];
		if ( isset( $data['nextBlock'] )) $this->nextBlock = (int) $data['nextBlock'];
		if ( isset( $data['position'] )) $this->position = (int) $data['position'];
	
	}

	public static function getList( $numRows=1000000 ) {
	    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM blocks
            ORDER BY order DESC LIMIT :numRows";
	    $st = $conn->prepare( $sql );
	    $st->execute();
	    $list = array();
	 
	    while ( $row = $st->fetch() ) {
	      $block = new Block( $row );
	      $list[] = $block;
	    }
	 
	    // Now get the total number of articles that matched the criteria
	    $sql = "SELECT FOUND_ROWS() AS totalRows";
	    $totalRows = $conn->query( $sql )->fetch();
	    $conn = null;
	    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
	  }
		
	
	// We need to make an intertInto() function that takes the page or block number that we want to add a new block to.
	
	private function insert() {
	
		//If we already have an ID, then it already exists in the DB.
		if ( !is_null( $this->id )) trigger_error ( "Block::insert(): Attempt to insert an Block object that already has its ID property set (to $this->id).", 												E_USER_ERROR );
	
    	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    	$sql = "INSERT INTO blocks ( name, type, content, content2, nextBlock, position ) VALUES ( :name, :type, :content, :content2, :nextBlock, :position )";
    	$st = $conn->prepare ( $sql );
    	$st->bindValue( ":name", $this->name, PDO::PARAM_INT );
    	$st->bindValue( ":type", $this->type, PDO::PARAM_STR );
    	$st->bindValue( ":content", $this->content, PDO::PARAM_STR );
    	$st->bindValue( ":content2", $this->content2, PDO::PARAM_STR );
    	$st->bindValue( ":nextBlock", $this->nextBlock, PDO::PARAM_INT );
    	$st->bindValue( ":position", $this->position, PDO::PARAM_INT );
    	$st->execute();
    	$this->id = $conn->lastInsertId();
    	$conn = null;
	
	}
	
	// Takes a Page object and uses the page's BlockArray to determine where to insert this new block.
	public function insertInto(Page $page) {
	
		$this->insert();
		if (!is_null($page->block)) { // Presumably we should have a constructed blockArray too.
			$lastBlock = end($page->blockArray);
			reset($page->blockArray);
			$lastBlock->nextBlock = $this->id;
			$lastBlock->update();
		} else {
			$page->block = $this->id;
			$lastBlock = Block::getById($page->block);
		}
		
		$page->update();
	
	}
	
	// Takes a Page object as we'll need to remove this block from its blockList too. TODO
	// TODO : Create proper functions in Page for this stuff. We shouldn't be directly editing Page's vars
	public function deleteFrom(Page $page) {
	
		foreach ($page->blockArray as $block) {
			// special case where we remove the first block
			if ($page->block == $this->id) {
				echo "deleting first block in DeleteFrom()";
				$page->block = $this->nextBlock;
				if (is_null($this->nextBlock)) {
					$page->block = null;	
				}
				
				$page->buildBlockArray();
				$page->update();
			}
		
			// Only one if statement should ever run. If both run then there's a problem. Lookout for this!
			
			if ($block->nextBlock == $this->id) {
				$block->nextBlock = $this->nextBlock;
				$block->update();
				$page->buildBlockArray();
			}
		}
		
	$this->delete();
	
	}
	
	// DANGEROUS FUNCTION! Deletes without fixing linked list!
	public function delete() {
		if ( is_null( $this->id ) ) trigger_error ( "Block::delete(): Attempt to delete an Block object that does not have its ID property set.", E_USER_ERROR );
		
		$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    	$st = $conn->prepare ( "DELETE FROM blocks WHERE id = :id LIMIT 1" );
    	$st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    	$st->execute();
    	$conn = null;
	}
	
	public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM blocks WHERE id = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Block( $row );
    
  }
  
  
  
    /**
  * Updates the current Block object in the database. (copied from Page object)
  */
 
  public function update() {
 
    // Does the Article object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "Page::update(): Attempt to update an Block object that does not have its ID property set.", E_USER_ERROR );
    
    // Update the Article
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE blocks SET name=:name, type=:type, content=:content, content2=:content2, nextBlock=:nextBlock, position=:position WHERE id = :id";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":name", $this->name, PDO::PARAM_STR );
    $st->bindValue( ":type", $this->type, PDO::PARAM_STR );
    $st->bindValue( ":content", $this->content, PDO::PARAM_INT );
    $st->bindValue( ":content2", $this->content2, PDO::PARAM_INT );
    $st->bindValue( ":nextBlock", $this->nextBlock, PDO::PARAM_INT );
    $st->bindValue( ":position", $this->position, PDO::PARAM_INT );
    
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    
    $st->execute();
    $conn = null;
  }

}

?>