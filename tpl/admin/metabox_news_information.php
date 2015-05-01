<label for="news-manager-featured"><input type="checkbox" id="news-manager-featured" name="news-manager-featured" value="1"<?php echo isset($data['news-manager-featured']) && $data['news-manager-featured'] ? ' checked' : ''; ?> /> Set As Featured?</label>
<div id="news-manager-featured-order-container" class="news-manager-<?php echo isset($data['news-manager-featured']) && $data['news-manager-featured'] ? 'visible' : 'hidden'; ?>">
				<label for="news-manager-featured-order">Featured Order</label>
				<input type="text" id="news-manager-featured-order" name="news-manager-featured-order" value="<?php echo isset($data['news-manager-featured-order']) ? $data['news-manager-featured-order'] : 0; ?>" />
</div>
<label for="news-manager-featured-end-date">Expiration Date</label>
<input type="text" class="news-manager-datepicker" id="news-manager-featured-end-date" name="news-manager-featured-end-date" <?php echo isset($data['news-manager-featured-end-date']) && !empty($data['news-manager-featured-end-date']) ? ' value="'.$data['news-manager-featured-end-date'].'"' : ''; ?> />
<label for="news-manager-author">Author</label>
<?php
if ($data['can-edit-users']){
				?>
				<select id="news-manager-author" name="news-manager-author">
								<?php
								foreach($data['users'] as $user){
												?><option value="<?php echo $user->ID; ?>"<?php echo $user->ID==$data['author']->ID ? ' selected="selected"' : '' ?>><?php echo $user->display_name; ?></option><?php
								}
								?>
				</select>
				<?php
} else {
				?>
				<input type="text" id="news-manager-author-name" name="news-manager-author-name" value="<?php echo $data['author']->display_name; ?>" disabled="disabled" />
				<input type="hidden" id="news-manager-author" name="news-manager-author" value="<?php echo $data['author']->ID; ?>" />
				<?php
}
?>