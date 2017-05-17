<?php
/*
   Plugin Name: sv-first-plugin
   Plugin URI: 
   Description: Starskie Villanueva first plugin
   Version: 0.1
   Author: Starskie Villanueva
   Author URI: 
   License: GPL2
*/

// using plugin class for cleaner and more organize to use
class sv_first_plugin
{
	// start with construct and assign call backs 
	public function __construct() 
	{
		// call back for custom post types 
		add_action( 'init', array($this, 'custom_post_type_callback' ) );
					
		// call back for meta box
		add_action( 'add_meta_boxes', array($this, 'post_meta_boxes' ));
		
		// call back for saving post
		add_action( 'save_post', array( $this, 'save_sample_field' ) );
		
		// filter template include for viewing template
		add_filter( 'template_include',  array( $this, 'view_template' ));
		
		// filter post join to include meta data
		add_filter('posts_join', array( $this, 'search_join' ));
		
		//filter for modify the search query 
		add_filter( 'posts_where', array( $this, 'search_modify' ));
		
		//filter for post distinct to add in SQL query
		add_filter( 'posts_distinct', array( $this, 'search_distinct' ));
		
		//callback action for css
		add_action( 'wp_enqueue_scripts', array( $this, 'style_css' ) );
		
		
	}
	// css function
	public function style_css() 
	{
		wp_register_style( 'samplestyle', plugins_url( '/style.css', __FILE__ ));
		wp_enqueue_style( 'samplestyle' );
	}

	
	// distinct keyword to the SQL query in order to prevent returning duplicates.	
	function search_distinct( $where ) 
	{
    	global $wpdb;

    	if ( is_search() ) 
    	{
        	return "DISTINCT";
    	}

    	return $where;
	}
	
	//  modify the wordpress search query to include custom fields.
	function search_modify( $where ) 
	{
    	global $wpdb;
   
    	if ( is_search() ) 
    	{
        	$where = preg_replace(
            	"/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            	"(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    	}

    	return $where;
	}
	
	// to include the custom fields data in our search
	public function search_join( $join ) 
	{
   	 global $wpdb;

    	if ( is_search() ) 
    	{    
    	    $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    	}
    
    	return $join;
    }
		
  	//filtering template	
	public function view_template( $template )
	{	
    	if( is_archive( 'podcast' ) ) 
    	{
        	$template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/archive.php';
		}
 
		if( is_singular( 'podcast' ) ) 
		{
       	 $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/single-post.php';
		}
 
    	return $template;
	}
	
 	// callback action for custom post my type
	public function custom_post_type_callback() 
	{
    	register_post_type( 'podcast',
        	array(
            'labels' => array(
                'name' => 'Podcast',
                'singular_name' => 'Podcast Review',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Podcast Review',
                'edit' => 'Edit',
                'edit_item' => 'Edit Podcast Review',
                'new_item' => 'New Podcast Review',
                'view' => 'View',
                'view_item' => 'View Podcast Review',
                'search_items' => 'Search Podcast Reviews',
                'not_found' => 'No Podcast Reviews found',
                'not_found_in_trash' => 'No Podcast Reviews found in Trash',
                'parent' => 'Parent Podcast Review'
           		), 
           		'publicly_queryable' => true,
            	'public' => true,
            	'menu_position' => 15,
            	'menu_icon' => 'dashicons-microphone',
            	'has_archive' => true
        	));
	}
	//callback action for metabox
 	public function post_meta_boxes() 
 		{
			add_meta_box
			( 
		    	'post-class-meta-box',
		    	__( "Meta box", 'podcast' ),
		    	array( $this, 'post_class_meta_box' ),
		    	''
		    );
		}
		  	
	// for displaying meta box
	public function post_class_meta_box( $post ) 
	{ 
		if ( ! empty ( $post ) ) 
		{
			$audio_input = get_post_meta( $post->ID, 'audio_input', true );
			$episode_input = get_post_meta( $post->ID, 'episode_input', true );
		}
		?>
    		<p>
     		<label for="audio_input">Audio</label>
     		<br/>
     		<input type="text" id="audio_input" name="audio_input" value="<?php echo $audio_input; ?>" />
    		</p>
  			<p>
    		<label for="episode_input">Episode Notes</label>
    		<br/>
     		<textarea id="episode_input" name="episode_input" cols="100" rows="3"><?php echo $episode_input; ?></textarea>	
    		<?php
	  }
	  
	// callback for saving post meta data
	public function save_sample_field( $post_id ) 
	{
		
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		{
			return;
		}

		$slug = 'podcast';
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) 
		{
			return;
		}
		
		if ( isset( $_POST['audio_input']  ) ) 
		{
			update_post_meta( $post_id, 'audio_input',  esc_html( $_POST['audio_input'] ) );
		}
		
		if ( isset( $_POST['episode_input']  ) ) 
		{
			update_post_meta( $post_id, 'episode_input',  esc_html( $_POST['episode_input'] ) );
		}
	}	
	
		  
}	
$sv_first_plugin = new sv_first_plugin();
?>