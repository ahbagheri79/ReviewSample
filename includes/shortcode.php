<?php
// Enqueue the script and localize it with nonce and AJAX URL
function enqueue_custom_plugin_assets() {
    wp_enqueue_script('custom-plugin-script', plugin_dir_url(__FILE__) . 'includes/script.js', array('jquery'), '', true);
    wp_localize_script('custom-plugin-script', 'custom_plugin_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('save_rating_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_plugin_assets');

// Shortcode for the five-star rating
function five_star_shortcode() {
    // You can use output buffering to capture the output of your script
    ob_start();
    ?>
    <div class="star-container" data-nonce="<?php echo wp_create_nonce('save_rating_nonce'); ?>" data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php
        for ($i = 0; $i < 5; $i++) {
            ?>
            <svg class="star-svg" data-index="<?php echo $i; ?>"  width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="star-path" d="M13.73 3.51001L15.49 7.03001C15.73 7.52002 16.37 7.99001 16.91 8.08001L20.1 8.61001C22.14 8.95001 22.62 10.43 21.15 11.89L18.67 14.37C18.25 14.79 18.02 15.6 18.15 16.18L18.86 19.25C19.42 21.68 18.13 22.62 15.98 21.35L12.99 19.58C12.45 19.26 11.56 19.26 11.01 19.58L8.02 21.35C5.88 22.62 4.58 21.67 5.14 19.25L5.85 16.18C5.98 15.6 5.75 14.79 5.33 14.37L2.85 11.89C1.39 10.43 1.86 8.95001 3.9 8.61001L7.09 8.08001C7.62 7.99001 8.26 7.52002 8.5 7.03001L10.26 3.51001C11.22 1.60001 12.78 1.60001 13.73 3.51001Z" stroke="#505050" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php
        }
        ?>
    </div>
    <?php
    // Get the post information for schema
    global $post;
    $post_id = $post->ID;
    $post_title = get_the_title($post_id);
    $post_permalink = get_permalink($post_id);

    // Get rating data from post meta
    $rating_value = get_post_meta($post_id, 'review_rate', true);
    $rating_count = ''; // You need to fetch this data if available

    // Render the JSON-LD schema
    ?>
    <script type="application/ld+json">
        {
          "@context": "https://schema.org/",
          "@type": "Review",
          "itemReviewed": {
            "@type": "Article",
            "name": "<?php echo esc_html($post_title); ?>",
        "url": "<?php echo esc_url($post_permalink); ?>"
      },
      "reviewRating": {
        "@type": "Rating",
        "ratingValue": "<?php echo esc_html($rating_value); ?>",
        "bestRating": "5",
        "ratingCount": "<?php echo esc_html($rating_count); ?>"
      }
    }
    </script>
    <?php

    // Return the buffered content
    return ob_get_clean();
}
add_shortcode('five_star_shortcode', 'five_star_shortcode');
