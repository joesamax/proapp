<?php

/**
* Render comments tree
*
* @param WP_Comment $comment
*/
function ml_render_iphone_comment( $comment, $reply = false, $reply_on_screen = false, $first_reply_shown = false ) {
	// Do not show anonymous and very short comments
	if ( empty( $comment->comment_author ) && empty( $comment->comment_author_email ) && ( 3 >= strlen( $comment->comment_content ) ) ) {
		return;
	}
	$show_avatars = get_option( 'show_avatars' );
?>
	<ons-list-item class="comment ml_comment comment_id_<?php echo esc_attr( $comment->comment_ID ); ?>">
		<?php
		if ( $show_avatars ) {
			$uid_or_email = $comment->user_id != 0 ? $comment->user_id : $comment->comment_author_email;
			$link         = Mobiloud::ml_get_avatar_url( $uid_or_email, 50 );

			echo '<img src="' . esc_attr( $link ) . '" class="avatar avatar-50 photo">';
		}
		?>
		<div class="comment_body">
			<?php echo '<strong>' . esc_html( $comment->comment_author ) . '</strong> <p>' . nl2br( convert_smilies( $comment->comment_content ) ) . '</p>'; ?>
			<div
				class="comment_meta"><?php echo esc_html( human_time_diff( strtotime( $comment->comment_date_gmt ), time() ) ); ?></div>
		</div>
		<?php
		$children = $comment->get_children(
			array(
				'status'       => 'approve',
				'order'        => 'ASC',
				'hierarchical' => 'threaded',
			)
		);

		$count_text = '';
		if ( $children ) {
			$count_text = sprintf( _n( '%s Comment', '%s Comments', count( $children ) ), number_format_i18n( count( $children ) ) );
		}
		$linkText = $count_text . ' ' . __( 'Reply' );

		$replyUrl = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/comments?post_id=' . $comment->comment_post_ID . '&comment=' . $comment->comment_ID;
		$onclick = "nativeFunctions.handleLink( '" . $replyUrl . "', '" . esc_js( __( 'Reply to this comment' ) ) . "', 'native' )";
		if ( ! $reply ) {
		?><a class="ml-reply-link" onclick="<?php echo esc_attr( $onclick ); ?>"><?php echo esc_html( $linkText ); ?></a><?php

			if ( ! $first_reply_shown ) {
				$child = array_values( $children );
				ml_render_iphone_comment( $child[0], false, false, true );
				if ( $reply_on_screen ) {
					echo '<a class="ml-reply-link" onclick=\'' . esc_js( 'replyNow(' . $child[0]->comment_ID . ')' ) . '\'>' . __( 'Reply' ) . '</a>';
				}
			}
		} else {
			echo '<a class="ml-reply-link" onclick=\'' . esc_js( 'replyNow(' . $comment->comment_ID . ')' ) . '\'>' . __( 'Reply' ) . '</a>';
			foreach ( $children as $child ) {
				ml_render_iphone_comment( $child, true, true );
				// echo '<a class="ml-reply-link" onclick=\'' . esc_js( 'replyNow(' . $child->comment_ID . ')' ) . '\'>' . __( 'Reply' ) . '</a>';
			}
		}

		?>
	</ons-list-item>
<?php
}
