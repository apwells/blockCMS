<?php include "templates/include/header.php" ?>
 
      <div id="adminHeader">
        <h2>Widget News Admin</h2>
        <p>You are logged in as <b><?php echo htmlspecialchars( $_SESSION['username']) ?></b>. <a href="admin.php?action=logout"?>Log out</a></p>
      </div>
 
      <h1><?php echo $results['pageTitle']?></h1>
 
      <form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
        <input type="hidden" name="pageId" value="<?php echo $results['page']->id ?>"/>
 
<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>
 
        <ul>
 
          <li>
            <label for="title">Page Title</label>
            <input type="text" name="title" id="title" placeholder="Name of the page" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['page']->title )?>" />
          </li>
          
          <li>
          	<label for="template">Template</label>
          	<select name="template">
            	<option value="default">Default</option>
            </select>
          </li>
          
          <li>
            <label for="parentPage">Parent</label>
            <select name="parentPage">
            <option value="-1">No Parent</option>
			<?php foreach($pages as $page) { ?>
					<option <?php if($results['page']->parentPage == $page->id ){ ?> selected="selected" <?php } ?>
					value="<?php echo $page->id ?>"><?php echo $page->title ?></option>
			<?php } ?>
			</select>
          </li>

        </ul>
 
        <div class="buttons">
          <input type="submit" name="saveChanges" value="Save Changes" />
          <input type="submit" formnovalidate name="cancel" value="Cancel" />
        </div>
 
      </form>
 
<?php if ( $results['page']->id ) { ?>
      <p><a href="admin.php?action=deletePage&amp;pageId=<?php echo $results['page']->id ?>" onclick="return confirm('Delete This Page?')">Delete This Page</a></p>
<?php } ?>
 
<?php include "templates/include/footer.php" ?>