<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>La Montanya</title>
 
<cms:template clonable='1' > </cms:template>
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/js/jquery.min.js"; ?>"></script>
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/js/imgselect.js"; ?>"></script>
<script type="text/javascript" src="<?php echo TEMPLATE_PATH . "/js/imguploader.js"; ?>"></script>

	<style>
	/*
 * imgAreaSelect animated border style
 */

.imgareaselect-border1 {
	background: url(border-anim-v.gif) repeat-y left top;
}

.imgareaselect-border2 {
    background: url(border-anim-h.gif) repeat-x left top;
}

.imgareaselect-border3 {
    background: url(border-anim-v.gif) repeat-y right top;
}

.imgareaselect-border4 {
    background: url(border-anim-h.gif) repeat-x left bottom;
}

.imgareaselect-border1, .imgareaselect-border2,
.imgareaselect-border3, .imgareaselect-border4 {
    filter: alpha(opacity=50);
	opacity: 0.5;
}

.imgareaselect-handle {
    background-color: #fff;
	border: solid 1px #000;
    filter: alpha(opacity=50);
	opacity: 0.5;
}

.imgareaselect-outer {
	background-color: #000;
    filter: alpha(opacity=50);
	opacity: 0.5;
}

.imgareaselect-selection {
}
	</style>



</head>

<body>

<?php

$block = $results['block'];
print_r($block); // TODO delete
$blockId = $block->id;
if ($blockId == null) {
	echo 'block id is null';
	exit;
}



?>


<?php
	// In the case where its a newly created image block (without image content)
	// if ($block->type == "image" && $block->content == null) : 
	
	// We've already uploaded one image.
	 if ($block->type == "image" && $block->content != null) : 
?>
<p>An image already exists for this block. Uploading a new one will delete it</p>
<?php
	 endif 
	
	// TODO : Make it so that we delete the old image from the system!
?>

<!-- image preview area-->
<img id="uploadPreview" style="display:none;"/>
  
<!-- image uploading form -->
<form id="submitForm" action="<?php echo (HOST_URL . "/admin.php?action=uploadImage&blockId=" . $blockId) ?>" method="post" enctype="multipart/form-data">
  <input id="uploadImage" type="file" accept="image/jpeg" name="image" />
  <input type="submit" value="Upload">

  <!-- hidden inputs -->
  <input type="hidden" id="x" name="x" />
  <input type="hidden" id="y" name="y" />
  <input type="hidden" id="w" name="w" />
  <input type="hidden" id="h" name="h" />
</form>





<?php

/**
*** PHP for image cropper from http://www.w3bees.com/2013/08/image-upload-and-crop-with-jquery-and.html
**/

$valid_exts = array('jpeg', 'jpg', 'png', 'gif');
$max_file_size = 200 * 1024; #200kb
$nw = $nh = 200; # image with & height

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ( isset($_FILES['image']) ) {
    if (! $_FILES['image']['error'] && $_FILES['image']['size'] < $max_file_size) {
      # get file extension
      $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      # file type validity
      if (in_array($ext, $valid_exts)) {
      		$uniqueid = uniqid();
	  		
          $path = SERVER_ABSOLUTE . "/uploads/" .  $uniqueid . '.' . $ext;
          $size = getimagesize($_FILES['image']['tmp_name']);
          # grab data form post request
          $x = (int) $_POST['x'];
          $y = (int) $_POST['y'];
          $w = (int) $_POST['w'] ? $_POST['w'] : $size[0];
          $h = (int) $_POST['h'] ? $_POST['h'] : $size[1];
          # read image binary data
          $data = file_get_contents($_FILES['image']['tmp_name']);
          # create v image form binary data
          $vImg = imagecreatefromstring($data);
          $dstImg = imagecreatetruecolor($nw, $nh);
          # copy image
          imagecopyresampled($dstImg, $vImg, 0, 0, $x, $y, $nw, $nh, $w, $h);
          # save image
          imagejpeg($dstImg, $path);
          # Save full
          $full = move_uploaded_file($_FILES['image']['tmp_name'], SERVER_ABSOLUTE . "/uploads/" . $uniqueid . "-full." . $ext);
          # clean memory
          imagedestroy($dstImg);
          echo '<img src="' . HOST_URL . "/uploads/" . $uniqueid . "." . $ext . '" />';
          
          if ($block->content != null && $block->type == "image") {
	          // We'll try and safely delete the old image
	          
	          $oldfullimage = SERVER_ABSOLUTE . "/uploads/" . $block->content;
	          $oldthumbimage = SERVER_ABSOLUTE . "/uploads/" .  $block->content2;
	          
	          if (file_exists($oldfullimage)) {
		          unlink($oldfullimage);
	          } else {
		          echo "ERROR : Tried to delete old full image but file did not exist on server. " . $oldfullimage;
	          }
	          
	          if (file_exists($oldthumbimage)) {
	          	  unlink($oldthumbimage);
	          } else {
		          echo "ERROR : Tried to delete old thumb image but file did not exist on server. " . $oldthumbimage;
	          }
	          
	          
          }
          
          
          $block->content = $uniqueid . "-full." . $ext;
          $block->content2 = $uniqueid . "." . $ext;
          $block->update();
          
        } else {
          echo 'unknown problem!';
        } 
    } else {
      echo 'file is too small or large';
    }
  } else {
    echo 'file not set';
  }
} else {
  // echo 'bad request!'; // Should only happen when file is not yet uploaded.
}

?>

</body>
</html>

