<?php 
	$user_url =  $user_page_url.'?user='.$value->user_nicename;
?>
	<div class="cell-user-grid media">
		<a class="pull-left" href="<?php echo $user_url ?>">
			<?php echo get_avatar($value->user_email, 64); ?>
		</a>
		<div class="media-body">
			<h4 class="media-heading"><a href="<?php echo $user_url ?>"><?php echo $value->display_name ?></a></h4>
		</div>
	</div>