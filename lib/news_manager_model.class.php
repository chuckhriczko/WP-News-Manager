<?php
/*******************************************************************************
 * Define our model class for data operations
 ******************************************************************************/
class News_Manager_Model{
				/*******************************************************************************
				 * Get the meta data for a post and format it accordingly
				 ******************************************************************************/
				public function get_post_meta($post, $defaults){
								//If a post ID is passed, get the post object
								$post = is_int($post) ? get_post($post) : $post;
								
								//Loop through the metadata and convert it to a single dimensional array
								foreach($defaults as $key=>$meta){
												$defaults[$key] = get_post_meta($post->ID, $key, true);
								}
								
								return $defaults;
				}
				
				/*******************************************************************************
				 * Get a list of the users in this site
				 ******************************************************************************/
				public function get_users(){
								return get_users(array(
												'orderby'      => 'nicename',
												'order'        => 'ASC'
								));
				}
				
				/*******************************************************************************
				 * Get all published articles that have a featured flag set
				 ******************************************************************************/
				public function get_featured_articles($count = -1){
								//Init the return array
								$data = array();
								
								//Get all articles that have been published
								$articles = get_posts(array(
												'posts_per_page'   => -1,
												'orderby'          => 'post_date',
												'order'            => 'ASC',
												'post_type'        => 'article',
												'post_status'      => 'publish',
												'suppress_filters' => true
								));
								
								//Loop through each article and make sure the meta featured is set
								foreach($articles as $article){
												if (get_post_meta($article->ID, 'news-manager-featured', true)==true) array_push($data, $article);
								}
								
								return $data;
				}
				
				/*******************************************************************************
				 * Get all published news items
				 ******************************************************************************/
				public function get_news($count = -1, $order = 'desc', $orderby = 'post_date'){
								//Init the return array
								$data = array();
								
								//Get all articles that have been published
								$articles = get_posts(array(
												'posts_per_page'   => $count,
												'orderby'          => $orderby,
												'order'            => $order,
												'post_type'        => 'news',
												'post_status'      => 'publish',
												'suppress_filters' => true
								));
								
								//Loop through each article and make sure the meta featured is set
								/*foreach($articles as $article){
												if (get_post_meta($article->ID, 'news-manager-featured', true)==true) array_push($data, $article);
								}*/
								
								//Loop through each article to make sure this post is not expired
								foreach($articles as $article){
												//Get the metadata for this article
												$end_date = get_post_meta($article->ID, 'news-manager-featured-end-date', true);
												
												//Make sure the end date is set
												if (!empty($end_date)){
																//Convert the date to a timestamp
																$end_date = strtotime($end_date);
																
																//Compare the date with today's date to see if it is expired
																if ($end_date>strtotime('now')){
																				//Add the date to the data array
																				array_push($data, $article);
																} else {
																				//Throw the post in the trash so it's not deleted but it does not show on the website
																				wp_update_post(array(
																								'ID'          => $article->ID,
																								'post_status' => 'trash'
																				));
																}
												} else {
																//There is no end date so we add the article to the data array
																array_push($data, $article);
												}
								}
								
								return $data;
				}
}
?>