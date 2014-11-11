<?php

if (!isset( $_SESSION['username'] )) {
	echo "editBlock: NOT LOGGED IN!";
	exit;
}

$page = $results['page'];
$blockArray = $page->block;


if (isset($_POST['delete'])) {
	deleteBlock($page, $_POST['delete']);
}

if (isset($_POST['do'])) {
	switch ($_POST['do']) {
		case 'create':
			createBlock($page);
			break;
		case 'order':
			orderBlocks($page);
			break;
		case 'edit':
			editBlock($page);
			break;
		case 'editform':
			editBlockForm($page);
			break;
		default:
		
	}
}

$page = $results['page'];
$blockArray = $page->block;

// displayBlocks($page, $results);



function orderBlocks(Page $page) {
	
	//print_r($_POST);
	
	$orderArray = array();
	parse_str($_POST['data'], $orderArray);
	
	print_r($orderArray);
	$blockArray = array();
	
	// First load all the blocks
	for ($id=0; $id < 9; $id++) {
		//print_r($orderArray['block'][$id]);
		$blockArray[$id] = $page->getBlockByPosition($orderArray['block'][$id]);
/*
		if (!is_null($block)) {
			print_r("BLOCK-- " . $block->position . "-- BLOCK");
			// $block->position = $orderArray['block'][$id];
			print("block " . $id . " moved to " . $orderArray['block'][$id]);
			// $block->update();
		}
*/
	}
	
	// Then separately update them. If we did this all in one loop, we'd end up moving 4-5 then 5 back to for etc.
	for ($id=0; $id < 9; $id++) {
		if (!is_null($blockArray[$id])) {
			$block = $blockArray[$id];
			// $blockArray[$id]->position = $id;
			print_r("block " . $block->position . " is at pos " . $id);
			$block->position = $id;
			$block->update();
		}
	}
}




	function createBlock(Page $page) {
	
			$pos = (int) $_POST['position'];
			$type = $_POST['type'];
			
			if ($type != "text" && $type != "image" && $type != "video") {
				echo "EditBlock error: Type was neither text, image or video! Rejecting block creation.";
				return;
			}
	
			if (!is_int($pos)) {
				echo "EditBlock error: position was not an INT! Input must be sanitized. Rejecting block creation.";
				return;
			}
	
			$data = array();
			$data['name'] = "New Block";
			$data['type'] = $type;
			if ($type == "text") { $data['content'] = "new text block"; }
			$data['position'] = $pos;
			$newBlock = new Block($data);
			$newBlock->insertInto($page);
			print_r($newBlock);
			
			return $page;
	}
	
	// Called via AJAX from main page.
	function editBlock(Page $page) {
	
		
		
		$blockId = (int)$_POST['blockId'];
		$type = $_POST['type'];
		$content = $_POST['content'];
		
		echo "Editing Block " . $blockId . " type " . $type . " , " . $content;
		
		$block = Block::getById($blockId);
		if ($block == null) {echo "editing null block!"; return;}
		
		// if ($type == "text") {
		$block->content = $content;
		$block->update();
		echo "block updated with content. " . $content;
		// }
	}
	
	
	function displayCreateButtion($results, $position, $page) {
		
?>

      <form action="admin.php?action=<?php echo $results['formAction']?>&pageId=<?php echo $page->id?>" method="post">
        <input type="hidden" name="do" value="create"/>
        <input type="hidden" name="position" value='<?php echo $position; ?>'/>
        <select name="type">
		<option value="text" selected>Text</option>
		<option value="image">Image</option>
		<option value="video">Video</option>
		</select>
 
<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>
 
        <div class="buttons">
          <input type="submit" name="newBlock" value="Create New Block" />
        </div>
 
      </form>


<?php 
		
	}
	
	function displayBlocks(Page $page, $results) {
	
	$maxBlocks = 9;
	
	if (is_null($page->blockArray)) {
		echo "no blocks yet";
		
		for ($i=0; $i < $maxBlocks; $i++) {
			echo "block " . ($i+1);
			displayCreateButtion($results, $i, $page);
		}
		
		return;
	}
	
	
	// New way of listing blocks. Instead of iterating through the array, we look for the correct position.
	for ($i = 0; $i < $maxBlocks; $i++ ) {
		$exists = false;	
	
			foreach ($page->blockArray as $block) {
			
				if ($block->position != $i) {
					// break;
				} else {
					$exists = true;
			
					echo "BLOCK <br />";
					if ($block->type == "image") {
						echo '<a href="admin.php?action=uploadImage&blockId=' . $block->id . '" >Upload/Change Image</a>'; 
						echo '<br />';
					}
					print_r($block);
					?>
					
					<form action="admin.php?action=<?php echo $results['formAction']?>&pageId=<?php echo $page->id?>" method="post">
					<input type="hidden" name="delete" value="<?php echo $block->id; ?>"/>
					<?php if ( isset( $results['errorMessage'] ) ) { ?>
					        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
					<?php } ?>
					<div class="buttons">
					<input type="submit" name="newBlock" value="Delete Block" />
					</div>
					</form> 
					
					<?php
					echo "<br />";
					// break;
				}
			}
			
			if (!$exists) {
				echo "no block for position " . ($i+1) . "<br />";
				displayCreateButtion($results, $i, $page);
			}
			
		}
	}
	
	function deleteBlock($page, $blockId) {
		$block = Block::getById($blockId);
		$block->deleteFrom($page);
	}
	
	
	function editBlockForm(Page $page) {
		print_r($_POST);
	
		$block = null;
		

		
		if (isset($_POST['blockId'])) {
			// Block ID is set, go ahead
			$blockId = (int) $_POST['blockId'];
			$block = Block::getById($blockId);
			
		} elseif (isset($_POST["blockPos"])) {
			// BlockPosition is supplied.
			$blockPos = (int) $_POST["blockPos"];
			$block = $page->getBlockByPosition($blockPos);
			
		} else {
			echo "editBlock : Insufficient information to edit block. Quitting";
			return;
		}
		
		if ($block->type == "image") {
			editImageForm($page, $block);
		} elseif ($block->type == "text") {
			editTextForm($page. $block);
		} elseif ($block->type == "video") {
			editVideoForm($page. $block);
		}

	}
	
	function editImageForm(Page $page, Block $block) {
		$results['block'] = $block;
		include("uploadImage.php");
	}
	
	function editTextForm(Page $page, Block $block) {
		echo "edit text";
	}
	
	function editVideoForm(Page $page, Block $block) {
		echo "edit video";
	}
?>