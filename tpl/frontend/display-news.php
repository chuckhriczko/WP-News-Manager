<div class="news-manager-container">
				<?php foreach($news as $key=>$news_item){ ?>
								<div class="news-container long">
												<h2 class="entry-title">
																<img height="24" width="32" alt="<?php bloginfo('name'); ?> News" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/news.png" />
																<span>
																				<?php
																								if (!is_single()){
																												?><a href="<?php the_permalink(); ?>" title="<?php get_the_title($news_item->ID); ?>"><?php echo do_shortcode(get_the_title($news_item->ID)); ?></a><?php
																								} else {
																												echo do_shortcode(the_title());
																								}
																				?>
																</span>
												</h2>
												<div class="news-content">
																<?php
																				$content = do_shortcode(get_the_content('Read More&hellip;'));
																				echo empty($news_item->post_excerpt) ? strlen($content)>NEWS_MANAGER_ELLIPSIS_LENGTH ? substr($content, 0, NEWS_MANAGER_ELLIPSIS_LENGTH).'&hellip;' : $content : $news_item->post_excerpt.'&hellip;';
																?>
																<a class="read-more-link" href="<?php echo get_permalink(); ?>" title="Read More...">Read More&hellip;</a>
												</div>
								</div>
				<?php } ?>
</div>