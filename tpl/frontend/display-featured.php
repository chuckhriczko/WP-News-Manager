<article id="news-manager-<?php echo $news_item->ID; ?>" class="news-manager-featured news-manager-<?php echo $news_item->post_name; ?>">
				<h3 class="news-manager-featured-title"><?php echo $news_item->post_title; ?></h3>
				<div class="news-manager-featured-content"><?php echo do_shortcode($news_item->post_content); ?></div>
</article>