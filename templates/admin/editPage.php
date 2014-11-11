<?php

/**
*** editPage.php - The interface for the creation, deletion and editing of pages through the page view admin editor.
*** 	AJAX/post will interface with this using javascript in the page's template.
**/


// Will give a page, and the appropriate variables to change. (Pages themselves only have a few vars - template, isHomePage etc.)
function editPage() {
	
}

// Delete some given page. Not necessarily the page that the request was sent from
function deletePage() {
	
}


// Create a new page.
function createPage() {
	
}

// Recieves an array of page orderings from the POST. Could apply to top level pages, or some sub-level. Each level has its own ordering.
function orderPages() {
	
}

// Loads a GUI for creating a new page (to be shown in a fancybox in the CMS)
function createPageForm() {
	
}

?>