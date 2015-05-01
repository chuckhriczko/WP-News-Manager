var News_Manager_Admin = {}; //Init the primary admin object

(function($) {
				$(document).ready(function(){
								News_Manager_Admin.init_layout();
								News_Manager_Admin.init_datepicker();
								News_Manager_Admin.bind_events();
				});
				
				/***********************************************************************
				 * Move the post thumbnail box to under the editor
				 **********************************************************************/
				News_Manager_Admin.init_layout = function(){
								//Only execute this code if we are on our custom post type editor
								if ($('body').hasClass('post-type-news')){
												var $postbox = $('#postimagediv'),
																html = $postbox.wrap('<p/>').parent('p').html();
												
												//Remove the existing postbox
												$postbox.parent('p').empty().remove();
												
												//Append it next to the article manager box
												$(html).insertAfter('.postbox-container #news-manager');
												
												//Remove the extra menu items from the articles menu
												$('#menu-posts-article ul.wp-submenu li').each(function(index, value){
																if (index>2) $(this).empty().remove();
												});
								}
				}
				
				/***********************************************************************
				 * Initializes the datepicker
				 **********************************************************************/
				News_Manager_Admin.init_datepicker = function(){
								//Find all datepickers and initialize them
								$('.news-manager-datepicker').datepicker({ minDate: 0 });
				}
				
				/***********************************************************************
				 * Binds events to different elements
				 **********************************************************************/
				News_Manager_Admin.bind_events = function(){
								var $news_manager = $('#news-manager'),
												$featured_order = $news_manager.find('#news-manager-featured-order-container');
												
								//Set the click handler for the featured checkbox
								$news_manager.on('click', '#news-manager-featured', function(e){
												if ($(this).is(':checked')) $featured_order.show(); else $featured_order.hide();
								});
				}
}(jQuery));