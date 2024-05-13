<?php
// AJAX handler to save the rating
add_action('wp_ajax_save_rating', 'save_rating');
add_action('wp_ajax_nopriv_save_rating', 'save_rating'); // Allow non-logged in users to rate

function save_rating() {
    // Check nonce for security
    check_ajax_referer('save_rating_nonce', 'nonce');

    // Sanitize and validate data
    $post_id = absint($_POST['post_id']);
    $rate = intval($_POST['rate']);

    // Verify that the user is allowed to rate
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( 'You are not allowed to rate this post.' );
    }

    // Validate post ID and rating
    if ( !$post_id || !$rate || $rate < 1 || $rate > 5 ) {
        wp_send_json_error( 'Invalid data.' );
    }

    // Check if the user has already rated this post
    if ( isset($_COOKIE['rated_post_' . $post_id]) ) {
        wp_send_json_error( 'You have already rated this post.' );
    }

    // Save the rating as post meta
    update_post_meta($post_id, 'review_rate', $rate);

    // Set cookie to prevent multiple ratings
    setcookie( 'rated_post_' . $post_id, 'true', time() + 86400 * 90, COOKIEPATH, COOKIE_DOMAIN, false, true );

    // Send success response
    wp_send_json_success( 'Rating saved successfully.' );

    // Required to terminate immediately and return a proper response
    wp_die();
}
