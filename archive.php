<?php
get_header();
?>	
	<div class="container">
	<div id="primary" >
		<div id="content">
	
		<?php
		if(have_posts()) : 
			while(have_posts()) :
				?> 
				<hr><?php
				the_post();
				the_title();
    			the_content();
    			$audio_input = get_post_meta( $post->ID, 'audio_input', true );
				?>
				<p><?php echo $audio_input; ?> </p>
				<?php 
			endwhile; 
		endif; 
			?>
			<hr>
		</div><!--site-content -->
	</div><!--content-area -->
	</div><!-- container -->
<?php
get_sidebar();
get_footer();
?>


