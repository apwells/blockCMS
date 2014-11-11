<?php

/*
** Functions you can call on the template/layout pages!

The functions class gets a page object as param for its constructor. Thereafter, simply call $f->Function() in your template php files to easily call these functions.

*/ 

class Functions
{

	public $page = null;
	
	/*
	** Takes a Page object
	*/
	public function __construct($page) {
		$this->page = $page;
		//echo 'printing page';
		//print_r($this->page);
	}


	/*
	** Will output an HTML menu depending on what you send it in the options.
	*/
	
	static function printMenu( $data=array() ) {
	
		if (!isset($data['exclude'])) {
			//echo "no params. Printing whole menu"; //TODO : DELETE!
			
			
	    	$pages = Page::getList();
	    	$pages = $pages["results"];
	    	
	    	echo "<ul>";
	    	foreach($pages as $page){
				if ($page->parentPage == -1) {	// If no parent, create a new item at root level
				?>
					<li>
					<?php echo Functions::menuLink($page); ?>
					<?php
					
						//$hasChildren = false;
						foreach($pages as $childCheck) {
							if ($childCheck->parentPage == $page->id) { ?>
							<ul class="sublist"><?php
					
								foreach($pages as $subpage) {
									if ($subpage->parentPage == $page->id) {
										?>
										<li><?php echo Functions::menuLink($subpage); ?></li>
										<?php
									}
								}
						
						
							?></ul><?php break;
							}
						}
						
					?>
					</li>
				<?php
				}
	    	}
	    	echo "</ul>";
			
		}
	
	}
	
	private static function menuLink(Page $page) {
		return '<a href="index.php?action=viewPage&pageId=' .  $page->id . '">' . $page->title . '</a>';
	}
	
	public function printTitle() {
		echo $this->page->title;
	}
	
	function getTemplate() {
		// Returns the template dir
		//print_r($this->page);
		
		//echo $this->page->template;
		return  $this->page->template;
	}
	
	function printBlockIdFromPosition($n) {
		if ($this->page->block == null) {
			// Exit if we have no blocks at all.
			return;
		}
		foreach ($this->page->blockArray as $block) {
			if ($block->position == $n) {				
				echo $block->id;
			}
		}
		return;
	}
	
	function printBlockTypeFromPosition($n) {
		if ($this->page->block == null) {
			// Exit if we have no blocks at all.
			return;
		}
		foreach ($this->page->blockArray as $block) {
			if ($block->position == $n) {				
				echo $block->type;
			}
		}
		return;
	}
	
	// TODO
	function printSiteName() {
		echo "La Montanya";
	}
	
	function printBlock($n) {
		
		if ($this->page->block == null) {
			$this->displayCreateForm($n);
			return;
		}
		
		
		$found = false;
		
		foreach ($this->page->blockArray as $block) {
			if ($block->position == $n) {
				if ($block->type == "text") {
					/*
					**	Slightly unreadable code below. Really it just formats us a <p editable="true" id="block-1" class="blocktype-text">CONTENT</p>
					**	I didn't need to call "printBlockTypeFromPosition, but thought i'd leave it here incase we want to change it later.
					*/
					?>
					<p <?php if (isset( $_SESSION['username'] )) { echo 'contenteditable="true"'; } ?> id="block-<?php $this->printBlockIdFromPosition($n); ?>" class="blocktype-<?php $this->printBlockTypeFromPosition($n); ?>"><?php echo $block->content; ?></p>
					<?php
				} elseif ($block->type == "image") {
					$this->displayImage($block, $n);
				} elseif ($block->type == "video") {
					$this->displayVideo($block, $n);
				}
				
				
				$found = true;
				return;
			}
		}
		
		if (!$found) {
			$this->displayCreateForm($n);
		}
	
	}
	
	private function displayCreateForm($n) {
			if (isset( $_SESSION['username'] )) {
				// TODO : If we're logged in, present a form to create a new block
				?>
				<p>Empty block. Create below</p>
				<form class="block-create-form" action="javascript:void(0);" >
					 <!-- <input type="text" name="name" value="Block Name"> -->
					 <input type="hidden" name="position" value="<?php echo $n; ?>">
					 <select name="type">
						<option value="text">Text</option>
						<option value="image">Image</option>
						<option value="video">Video</option>
					</select>
					<input type="submit" value="Create">
				</form>
				<?php
			} else {
				// If we're not logged in, print nothing.
			}
			// echo "no block for position " . $n;
	}
	
	// Change this if you want to display images differently.
	private function displayImage(Block $block, $n) {
			echo '<div><a href="uploads/' . $block->content . '" class="fancybox" data-lightbox="' . $block->content . '">';
			echo '<img src="uploads/' .  $block->content2 . '" alt="'. $block->content . ' height="100%" width = "100%" />';
			echo '</a></div>';
	}
	
	
	private function displayVideo(Block $block, $n) {
		/*
		**	Here we want to display the youtube video in a fancybox. If we're logged in, just give the option to change the video ID.
		*/
		if (isset( $_SESSION['username'])) {
			// Logged in
			?>
			<p>Change the ID of the youtube video below</p>
			<input type="text" name="youtube-id" id="block-<?php $this->printBlockIdFromPosition($n); ?>" class="youtube-id" value="<?php echo $block->content; ?>">
			
			<?php
		} else {
			// Not logged in
			// Note that some templates will want a different height and width
			?>
				<a class="fancybox-media" href="http://www.youtube.com/watch?v=<?php echo $block->content; ?>">
					<img class="youtube-thumb" src="http://img.youtube.com/vi/<?php echo $block->content; ?>/0.jpg" alt="Youtube Thumbnail" />
				</a>
			<?php
		}
	}
	

}


?>
