jQuery(document).ready(function($) {
    $('.star-svg').click(function () {
        const rate = parseInt($(this).data("index"));
        const postId = $(this).closest('.star-container').data("post-id");
        const nonce = custom_plugin_ajax_object.nonce;
        const ajaxUrl = custom_plugin_ajax_object.ajax_url;

        // Check if the user has already rated this post
        if (!getCookie("rated_post_" + postId)) {
            // Send AJAX request to save the rating
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: {
                    action: 'save_rating',
                    nonce: nonce,
                    post_id: postId,
                    rate: rate
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data);
                        setCookie("rated_post_" + postId, "true", 90);
                    } else {
                        alert(response.data);
                    }
                },
                error: function() {
                    alert("Failed to save rating. Please try again later.");
                }
            });
        } else {
            alert("You have already rated this post.");
        }
    });

    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    function getCookie(name) {
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookies = decodedCookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length + 1);
            }
        }
        return "";
    }
});
