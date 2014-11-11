<?php
//$f->getTemplate(); 

//$f->printMenu();
?>

<body id="index" class="home">

<?php if (isset( $_SESSION['username'] )) {
	
	echo 'Logged in. <a href="http://localhost:8888/lamontanyacustom/admin.php?action=logout">log out</a>';
	print_r($f->page);
}
?>

<div id="wrapper">
	<div id="sidebar">
		<div id="title">
			<h1><?php $f->printSiteName(); ?></h1>
		</div>
		<div id="menu">
			<?php $f->printMenu(); ?>
		</div>
		<div id="sidebar-footer">
			ENG/SPA
		</div>
	</div>

	<div id="page">
		<div id="page-title">
			<h2><?php $f->printSiteName(); ?> | <?php $f->printTitle(); ?></h2>
		</div>
		<div id="blocks" >
			<ul id="blocklist">
				<li id="block-0" class="ui-state-default block"><?php $f->printBlock(0); ?></li>
				<li id="block-1" class="ui-state-default block"><?php $f->printBlock(1); ?></li>
				<li id="block-2" class="ui-state-default block"><?php $f->printBlock(2); ?></li>
				<li id="block-3" class="ui-state-default block"><?php $f->printBlock(3); ?></li>
				<li id="block-4" class="ui-state-default block"><?php $f->printBlock(4); ?></li>
				<li id="block-5" class="ui-state-default block"><?php $f->printBlock(5); ?></li>
				<li id="block-6" class="ui-state-default block"><?php $f->printBlock(6); ?></li>
				<li id="block-7" class="ui-state-default block"><?php $f->printBlock(7); ?></li>
				<li id="block-8" class="ui-state-default block"><?php $f->printBlock(8); ?></li>
			</ul>
		</div>
	</div>
</div>
