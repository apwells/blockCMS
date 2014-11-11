



</body>
</html>

<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
		
	});
	
<?php if (isset( $_SESSION['username'] )) : ?>


	$(document).ready(function() {
		
    $( "#blocklist" ).sortable();
    $( "#blocklist" ).disableSelection();
    $('#blocklist').sortable( "disable" );
    
    
    
    // Create the block admin mouseover menu
	$(".block").each(function() {

		$(this).prepend(
    				'<div class="block-admin-menu"> \
    					<ul> \
    					<li><a href="javascript:;" class="blockmenu-delete">DELETE</a></li> \
    					<li><a href="javascript:;" class="blockmenu-edit">EDIT</a></li> \
    					<li><a href="javascript:;" class="blockmenu-move">MOVE</a></li> \
    					</ul> \
    				</div>'
    				);
		
	});
    				
    $("#menu").append(
    				'<a href="javascript:;" id="admin-newpage">Create New Page</a>'
    				);
    
    
	    // Activate block menu
	$( ".block" ).mouseover(function( e ) {
	  $(this).find( ".block-admin-menu li" ).css( "visibility", "visible" );
	});	
	
	// Disable the block menu
	$( ".block" ).mouseleave(function( e ) {
	  $(this).find( ".block-admin-menu li" ).css( "visibility", "hidden" );
	});	
	
	// Detect clicking on "move" in the blockmenu
	$(".blockmenu-move").click(function(e ) {
		$("#blocklist").sortable("enable");
		// For some reason after dropping, the sortable is disabled again... So this actually works fine just like this.
	});
	
	// Detect clicking on "delete" in the blockmenu
	$(".blockmenu-delete").click(function(e ) {
		var parent = $(e.target).parent();	// The .block element
		// TODO : Send a deleteBlock request
		// Problem is that the parent block does not have the ID or anything. We need a deleteByPosition function in the Page object (which can be called via the editBlocks.php page. This would be the proper way of doing it.
	});
	
	// Send the block information to EditBlock and display result in fancybox
    $(".blockmenu-edit").click(function(e ) {
		var parent = $(e.target).parent().parent().parent().parent();	// The .block element
		var blockPos = parent.attr('id').split("-")[1]; // Gets the second (0 based) thing after the '-' (in this case the position)
		var pageId = $(document).getUrlParam("pageId");
		var url = "admin.php?action=editBlocks&pageId=" + pageId;
		
		console.log(blockPos);
		console.log(pageId, url);
		
/*
		$.post( url, { do: "editform", blockPos: blockPos})
		  .done(function( data ) {
		    console.log( "Data Loaded: " + data );
		  });
*/
		$.ajax({
			type : "POST",
			cache : false,
			url : url,
			data : {do: "editform", blockPos: blockPos},
			success : function(data) {
				$.fancybox(data, {
					type	: 'html',
					autoSize	: false,
					width	: '100%',
					height	: '100%',
					afterShow: function(){
							$('#submitForm').ajaxForm(function() { 
						        alert("Thank you for your comment!"); 
						    }); 
						}
				});
				
			}
			
		});

	});
    
    // TODO : Open editPage.php in a fancybox. Refresh the page after closing (is this possible?)
    $("#admin-newpage").click(function(e ) {
		
	});
    
});
	



	
// Save Draggable Order
$('#blocklist').sortable({
    axis: 'xy',
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        var pageId = $(document).getUrlParam("pageId");


		$.post( "admin.php?action=editBlocks&pageId=" + pageId, { do: "order", data: data})
		  .done(function( data ) {
		    console.log( "Data Loaded: " + data );
		  });
		  
		location.reload(true);

        // POST to server using $.post or $.ajax
/*
        $.ajax({
            data: data,
            type: 'POST',
            url: '/your/url/here'
        });
*/

		// console.log(data);
    }
});
	
$("body").click(function (e) {
var contenteditable = $(e.target).attr('contenteditable');
if (contenteditable == 'true') {	// This will target all contenteditables. We need to do an if statement below for different kinds.
    $(e.target).unbind('focusout').focusout(function() {
        var content = $(this).html();
        var id = e.target.id.split("-")[1];
        var pageId = $(document).getUrlParam("pageId");
        
        var blocktype = e.target.className
        console.log(id);
        /*
        	Here we'll send our AJAX request using the content and the name.
        	If no type is suppled, we won't change the type (this is still TODO at php side!)
        	
        */
		$.post( "admin.php?action=editBlocks&pageId=" + pageId, { do: "edit", blockId: id, type: "", content: $(this).html() })
		  .done(function( data ) {
		    console.log( "Data Loaded: " + data );
		  });
    });
};          
});

// Editing the youtube URL from web view
$(".youtube-id").focusout(function (e) {
    // alert("hello");
    var textbox = $(e.target);
    var id = e.target.id.split("-")[1];
    var pageId = $(document).getUrlParam("pageId");
    
		$.post( "admin.php?action=editBlocks&pageId=" + pageId, { do: "edit", blockId: id, type: "", content: $(this).val() })
		  .done(function( data ) {
		    // alert( "Data Loaded: " + data );
		  });

});

// Creating a new block from the web view
$(".block-create-form").submit(function(e) {
	var vals = $(e.target).serializeArray();
	console.dir(vals);
	var pageId = $(document).getUrlParam("pageId");
	
	// After serializing the array, it creates vals[0] (position) and vals[1] (type). Get by vals[x].value i think. Shame its not associative by default.
	
	
	
	$.post( "admin.php?action=editBlocks&pageId=" + pageId, { do: "create", type: vals[1].value, position: vals[0].value })
		  .done(function( data ) {
		    //alert( "Data Loaded: " + data );
		  });
	location.reload(true);
	
});



<?php endif; ?>


$(document).ready(function() {
	$('.fancybox-media').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',
		helpers : {
			media : {}
		}
	});


});



</script>