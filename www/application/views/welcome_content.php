<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

	<?php
		// pagination 
		echo $hotlinks;
	?>
<div id="container">
	<?php foreach($posts as $key => $post){ ?>
		<div id="box<?php echo $key+1; ?>" class="outer">
			<a href="#" class="close ui-icon ui-icon-close" style="float:right">Close</a>		
			<div class="pubDate"><?php echo $post->date; ?></div>
			<?php 
			$features = unserialize($post->content); 
			if(is_array($features)){
			foreach($features as $f){
			?>
				<div class="body"><?php echo stripslashes($f); ?></div>
			<?php }
			}else{
				// Probably homepage blurb, spit out welcome html
				echo $features;
			}?>				
		</div>
	<?php } ?>
</div>