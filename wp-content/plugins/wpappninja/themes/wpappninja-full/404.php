<?php
if (isset($_GET['offlinewpappninja'])) {
	status_header(200);
} ?>
<?php get_header(); ?>
<div class="posts">
<div class="post main-post" style="text-align:center">
<div class="wpapp-post-content">
<?php
if (isset($_GET['offlinewpappninja'])) {

	$has_custom_offline_page = get_page_by_title("wpmobile_offline");

	if ($has_custom_offline_page != null) {
		echo get_post_field('post_content', $has_custom_offline_page->ID);
	} else {
		echo '<h1><i class="f7-icons" style="font-size: 60px;color: #b7b7b7;">close_round</i></h1>';
		_e('Sorry, this page is not available offline.', 'wpappninja');
		echo '<br/>';
		_e('Check your connection.', 'wpappninja');
	}

} else {
	echo '<h1><i class="f7-icons" style="font-size: 60px;color: #b7b7b7;">close_round</i></h1>';
	_e('Sorry, this page does not exist.', 'wpappninja');
} ?>
<br/><br/>
</div>
</div>
</div>
<?php get_footer(); ?>