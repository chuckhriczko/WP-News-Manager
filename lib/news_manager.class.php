<?php
require_once(__DIR__.'/news_manager_model.class.php'); //Make sure we include our model for data operations

/*******************************************************************************
 * Define our initial class
 ******************************************************************************/
class News_Manager{
				//Instantiate our public variables
				public $model, $plugin_path, $plugin_uri, $debug = false;
				
				//Instantiate our protected variables
				protected static $instance = NULL;
				
				/*******************************************************************************
				 * Instantiate our constructor
				 ******************************************************************************/
				public function __construct(){
								//Call the init function
								$this->init();
				}
				
				/*******************************************************************************
				 * Allows our views to access our functions
				 ******************************************************************************/
				public static function get_instance(){
        //Create an instance of this object and return it
        return NULL === self::$instance and self::$instance = new self;
    }
				
				/*******************************************************************************
				 * Perform initialization functions
				 ******************************************************************************/
				public function init(){
								//Enable debugging if the flag is set to true
								if ($this->debug) $this->init_debugging();
								
								//Init paths
								$this->plugin_path = __DIR__.'/..';
								$this->plugin_uri = str_replace('/lib', '', plugin_dir_url(__FILE__));
								
								//Init our model
								$this->model = new News_Manager_Model();
								
								//Init our hooks
								$this->init_hooks();
								
								//Init our shortcodes
								$this->init_shortcodes();
				}
				
				/*******************************************************************************
				 * Initializes our hooks
				 ******************************************************************************/
				public function init_hooks(){
        //Set up our activation and deactivation hooks
								register_activation_hook($this->plugin_path, array(&$this, 'plugin_activation'));
								register_deactivation_hook($this->plugin_path, array(&$this, 'plugin_deactivation'));
								
								//Add custom post type
								add_action('init', array(&$this, 'register_custom_post_type'));
								
								//Customize the messages for our custom post type
								add_filter('post_updated_messages', array(&$this, 'news_updated_messages'));
								
								//Add custom columns to the post listing page for our custom post type
								add_filter('manage_news_posts_columns' , array(&$this, 'manage_news_posts_columns'));
								add_action('manage_pages_custom_column', array(&$this, 'manage_pages_custom_column'), 10, 2);
								
								//Include scripts and styles for the admin
								add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
								
								//Include scripts and styles for the frotnend
								add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
								
								//Add meta boxes to the custom post type editor screen
								add_action('add_meta_boxes', array(&$this, 'add_meta_box_news_information'));
								
								//Save the meta data when a post is saved
								add_action('save_post', array(&$this, 'save_post'));
								
								//Disable update checks for this plugin
								add_filter('auto_update_plugin', '__return_false');
								add_filter('site_transient_update_plugins', array(&$this, 'remove_update_nag'));
    }
				
				/*******************************************************************************
				 * Initializes debugging
				 ******************************************************************************/
				public function init_debugging(){
								//Enable errors
								ini_set('display_startup_errors',1);
								ini_set('display_errors',1);
								error_reporting(-1);
				}
				
				/*******************************************************************************
				 * Initializes our shortcodes
				 ******************************************************************************/
				public function init_shortcodes(){
								//Add shortcode for displaying the featured posts
								add_shortcode('news_manager_display_featured', array(&$this, 'news_manager_display_featured'));
								
								//Add shortcode for displaying all posts
								add_shortcode('news_manager_display_all', array(&$this, 'news_manager_display'));
								
								//Add shortcode for displaying recent posts
								add_shortcode('news_manager_display_recent', array(&$this, 'news_manager_display_recent'));
				}
				
				/*******************************************************************************
				 * Runs functions when the plugin is activated (not the same as initialized)
				 ******************************************************************************/
				public function plugin_activated(){
								//Flush rewrite rules to activate slugs, etc.
								flush_rewrite_rules();								
				}
				
				/*******************************************************************************
				 * Runs functions when the plugin is deactivated
				 ******************************************************************************/
				public function plugin_deactivated(){
								
				}
				
				/*******************************************************************************
				 * Adds custom post types to the WP DB
				 ******************************************************************************/
				public function register_custom_post_type(){
								//Register the primary post type
								register_post_type('news', array(
												'labels' => array(
																'name' 														=> 'News Item',
																'singular_name' 					=> 'News Item',
																'menu_name'          => 'News',
																'name_admin_bar'     => 'News',
																'add_new'            => 'Add New',
																'add_new_Item'       => 'Add New News Item',
																'new_Item'           => 'New News Item',
																'edit_Item'          => 'Edit News Item',
																'view_Item'          => 'View News Item',
																'all_Items'          => 'All News Items',
																'search_Items'       => 'Search News Item',
																'parent_Item_colon'  => 'Parent News Item:',
																'not_found'          => 'No news found.',
																'not_found_in_trash' => 'No news found in Trash.'
												),
												'public'												 => true,
												'menu_icon' 									=> $this->plugin_uri.'/assets/images/menu-icon.png',
												'publicly_queryable' => true,
												'show_ui'            => true,
												'show_in_menu'       => true,
												'query_var'          => true,
												'rewrite'            => array('slug' => 'news'),
												'capability_type'			 => 'post',
												'has_archive'        => false,
												'hierarchical'       => true,
												'supports'           => array('title', 'editor', 'thumbnail'),
												'menu_position'						=> 5
								));
								
								//Add the category taxonomy to the custom post type, allowing the user to select categories
								register_taxonomy_for_object_type('category', 'news');
				}
				
				/*******************************************************************************
				 * Customizes the messages associated with our custom post type
				 ******************************************************************************/
				public function news_updated_messages($messages){
								//Get the post information
								$post = get_post();
								$post_type = get_post_type($post);
								$post_type_object = get_post_type_object($post_type);
								
								if ($post_type=='news'){
												//Set the messages
												$messages['news'] = array(
																0  => '', // Unused. Messages start at index 1.
																1  => 'News item updated.',
																2  => 'Custom field updated.',
																3  => 'Custom field deleted.',
																4  => 'News item updated.',
																5  => isset($_GET['revision']) ? sprintf('News item restored to revision from %s', wp_post_revision_title((int) $_GET['revision'], false)) : false,
																6  => 'News item published.',
																7  => 'News item saved.',
																8  => 'News item submitted.',
																9  => sprintf('News scheduled for: <strong>%1$s</strong>.', date_i18n('M j, Y @ G:i', strtotime($post->post_date))),
																10 => 'News draft updated.'
												);
												
												//Set the links if the post type is public
												if ($post_type_object->publicly_queryable){
																$permalink = get_permalink($post->ID);
																
																$view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), 'View news');
																$messages[$post_type][1] .= $view_link;
																$messages[$post_type][6] .= $view_link;
																$messages[$post_type][9] .= $view_link;
																
																$preview_permalink = add_query_arg('preview', 'true', $permalink);
																$preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), 'Preview news');
																$messages[$post_type][8]  .= $preview_link;
																$messages[$post_type][10] .= $preview_link;
												}
								}
								
								return $messages;
				}
				
				/*******************************************************************************
				 * Registers scripts and styles to be placed in the admin header
				 ******************************************************************************/
				public function admin_enqueue_scripts(){
								//Set the script dependencies
								$deps = array('jquery');
								
								//Enqueue scripts
								wp_enqueue_script('jquery-ui-datepicker', $deps);
								wp_enqueue_script('news-manager-admin-script', $this->plugin_uri.'assets/js/admin.js', $deps);
								
								//Register the styles first
								wp_enqueue_style('jquery-ui-datepicker-smoothness', 'http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
								wp_enqueue_style('news-manager-admin-style', $this->plugin_uri.'assets/css/admin.css');
				}
				
				/*******************************************************************************
				 * Registers scripts and styles to be placed in the frontend header
				 ******************************************************************************/
				public function wp_enqueue_scripts(){
								//Set the script dependencies
								$deps = array('jquery');
								
								//Enqueue the styles after they're registered
								wp_enqueue_style('news-manager-frontend-style', $this->plugin_uri.'assets/css/frontend.css');
								
								//Enqueue the scripts after they're registered
								wp_enqueue_script('news-manager-frontend-script', $this->plugin_uri.'assets/js/frontend.js', $deps);
				}
				
				/*******************************************************************************
				 * Adds custom meta boxes to the custom post type editor screen
				 ******************************************************************************/
				public function add_meta_box_news_information(){
								add_meta_box('news-manager', 'News Item Information', array(&$this, 'display_meta_box_news_information'), 'news', 'normal', 'core');
				}
				
				/*******************************************************************************
				 * Displays the custom meta boxes
				 ******************************************************************************/
				public function display_meta_box_news_information($post_id){
								//Get the meta data for this news
								$data = $this->model->get_post_meta($post_id, array('news-manager-featured' => false, 'news-manager-featured-order' => 0, 'news-manager-featured-end-date' => strtotime('now')));
								
								//Get the author for this post
								$data['author'] = get_userdata(get_current_user_id());
								
								//Get user's capabilities
								$data['can-edit-users'] = isset($data['author']->allcaps['edit_pages']) && $data['author']->allcaps['edit_pages']==1 ? true : false;
								
								//Get a list of the users
								$data['users'] = $this->model->get_users();
								
								//Load and display the template
								include($this->plugin_path.'/tpl/admin/metabox_news_information.php'); //Include the template
				}
				
				/*******************************************************************************
				 * Saves the meta box data when a post is saved
				 ******************************************************************************/
				public function save_post($post_id, $post = ''){
								//If the post object is not set, get it from the provided post ID
								$post = empty($post) ? get_post($post_id) : $post;
								
								//If this is our custom post type
								if ($post->post_type=='news'){
												//Set the options for this post
												update_post_meta($post_id, 'news-manager-featured', isset($_POST['news-manager-featured']) ? true : false);
												update_post_meta($post_id, 'news-manager-featured-order', isset($_POST['news-manager-featured-order']) ? $_POST['news-manager-featured-order'] : 0);
												update_post_meta($post_id, 'news-manager-featured-end-date', isset($_POST['news-manager-featured-end-date']) ? $_POST['news-manager-featured-end-date'] : '');
												update_post_meta($post_id, 'news-manager-author', isset($_POST['news-manager-author']) ? $_POST['news-manager-author'] : 0);
								}
				}
				
				/*******************************************************************************
				 * Add custom columns to the post listing for our custom post type
				 ******************************************************************************/
				public function manage_news_posts_columns($columns){
								//Remove the date column from the existing array
								$date = $columns['date'];
								unset($columns['date']);
								unset($columns['categories']);
								
								//Add the new items to the array
								return array_merge($columns, array(
												'content' => 'News Content',
												'author' => 'Author',
												'thumbnail' => 'Thumbnail',
												'date' => $date,
												'featured' => 'Featured?'
								));
				}
				
				/*******************************************************************************
				 * Adds content to the columns added in the function above
				 ******************************************************************************/
				public function manage_pages_custom_column($column, $post_id){
								//Get the post object for this post ID
								$post = get_post($post_id);
								
								//Check which column is being rendered
								switch($column){
												case 'content':
																echo do_shortcode(strlen($post->post_content)>NEWS_MANAGER_ELLIPSIS_LENGTH ? substr($post->post_content, 0, NEWS_MANAGER_ELLIPSIS_LENGTH).'&hellip;' : $post->post_content);
																break;
												case 'author':
																echo get_the_author($post->post_author);
																break;
												case 'date':
																echo date('M j, Y', strtotime($post->post_date)).' &ndash; '.date('g:ia', strtotime($post->post_date));
																break;
												case 'featured':
																?><span class="<?php echo get_post_meta($post_id, 'news-manager-featured', true) ? 'dashicons dashicons-star-filled' : 'dashicons dashicons-star-empty'; ?>"></span><?php
																break;
												case 'featured_order':
																echo get_post_meta($post_id, 'news-manager-featured-order', true);
																break;
												case 'thumbnail':
																$thumbnail_html = get_the_post_thumbnail($post_id, 'small-square');
																echo empty($thumbnail_html) ? 'None' : $thumbnail_html;
																break;
								}
				}
				
				/*******************************************************************************
				 * Shortcode for returning only featured news
				 ******************************************************************************/
				public function news_manager_display_featured($atts = array()){
								//Check if the attributes array is actually boolean
								//If so this will act as an overloaded function.
								//Boolean means we are using this function as a template tag
								$echo = is_bool($atts) ? true : false;
								
								//Get the attributes for this shortcode
								extract(shortcode_atts(array(
												'count' => -1,
												'order' => 'desc',
												'orderby' => 'post_date'
								), $atts));
								
								//Get all posts that have the featured flag set to true
								$featured = $this->model->get_featured_news($count);
								
								//If the template exists in the theme directory, we use that one.
								//Otherwise, we use the one included with the plugin
								$featured_tpl = file_exists(get_template_directory().'/news-manager/display-featured.php') ? get_template_directory().'/news-manager/display-featured.php' : $this->plugin_path.'/tpl/frontend/display-featured.php';
								
								//Initialize the HTML return string
								$html = '<div class="news-manager-featured-container">';
								
								//Start output buffering so the include gets saved into a variable which
								//can then be returned below this block
								ob_start();
												
												//Loop through each featured news
												foreach($featured as $key=>$news){
																include($featured_tpl); //Include the template, which is a loop template
												}
								
								$html .= ob_get_contents();				
								ob_end_clean();
								
								$html .= '</div>';
								
								//If $atts was boolean then we are using this function
								//as a template tag so we echo the result. Otherwise
								//this is a shortcode so we return it
								if ($echo) echo $html; else return $html;
				}
				
				/*******************************************************************************
				 * Shortcode for returning all news
				 ******************************************************************************/
				public function news_manager_display($atts = array()){
								//Check if the attributes array is actually boolean
								//If so this will act as an overloaded function.
								//Boolean means we are using this function as a template tag
								$echo = is_bool($atts) ? true : false;
								
								//Get the attributes for this shortcode
								extract(shortcode_atts(array(
												'count' => -1,
												'order' => 'desc',
												'orderby' => 'post_date',
												'display_type' => 'all'
								), $atts));
								
								//Get all posts based on our display type
								switch($display_type){
												case 'recent':
																$news = $this->model->get_news($count, 'desc', 'post_date');
																break;
												case 'all':
												default:
																$news = $this->model->get_news($count, $order, $orderby);
								}
								
								//Start output buffering so the include gets saved into a variable which
								//can then be returned below this block
								ob_start();
								include(file_exists(get_template_directory().'/news-manager/display-news.php') ? get_template_directory().'/news-manager/display-news.php' : $this->plugin_path.'/tpl/frontend/display-news.php'); //Include the template
								$html = ob_get_contents();				
								ob_end_clean();
								
								//If $atts was boolean then we are using this function
								//as a template tag so we echo the result. Otherwise
								//this is a shortcode so we return it
								if ($echo) echo $html; else return $html;
				}
				
				/*******************************************************************************
				 * Shortcode for returning recent news items
				 ******************************************************************************/
				public function news_manager_display_recent($atts = array()){
								//Add the recent flag to the atts array
								$atts['display_type'] = 'recent';
								
								return $this->news_manager_display($atts);
				}
				
				/*******************************************************************************
				 * Disables update notification to prevent plugin repository from mistaking
				 * with plugin of the same name
				 ******************************************************************************/
				function remove_update_nag($value = '') {
								if (isset($value->response)) unset($value->response[$this->plugin_path.'/init.php']);
								return $value;
				}
}
?>