<?php

//ADD OPEN GRAPH META
function fbgraphinfo()
{
    $options = get_option('fbcommentssync');
    if (!empty($options['app_ID'])) {
        echo '<meta property="fb:app_id" content="' . $options['app_ID'] . '"/>';
    }
    if (!empty($options['moderators'])) {
        echo '<meta property="fb:admins" content="' . $options['moderators'] . '"/>';
    }
}

add_action('wp_head', 'fbgraphinfo');

//Comment Box
function fbcommentbox($content)
{
    $options = get_option('fbcommentssync');
    if (!isset($options['btdogan'])) {
        $options['btdogan'] = "off";
    }
    if (!isset($options['posts'])) {
        $options['posts'] = "off";
    }
    if (!isset($options['pages'])) {
        $options['pages'] = "off";
    }
    if (!isset($options['homepage'])) {
        $options['homepage'] = "off";
    }

    if ((is_single() && $options['posts'] == 'on') || (is_page() && $options['pages'] == 'on') || ((is_home() || is_front_page()) && $options['homepage'] == 'on')) {

        $custom_fields = get_post_custom();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field_key => $field_values) {
                foreach ($field_values as $key => $value)
                    $post_meta[$field_key] = $value; // builds array
            }
        }
        if (!isset($post_meta['_disable_fbc'])) {
            $post_meta['_disable_fbc'] = "off";
        }

        if ($post_meta['_disable_fbc'] != 'on') {

            if ($options['title'] != '') {
                if ($options['titleclass'] == '') {
                    $commenttitle = "<h3>";
                } else {
                    $commenttitle = "<h3 class=\"" . $options['titleclass'] . "\">";
                }
                $commenttitle .= $options['title'] . "</h3>";
            }

            $content .= $commenttitle;

            $content .= "<div class=\"fb-comments\" data-href=\"" . get_permalink() . "\" data-num-posts=\"" . $options['num'] . "\" data-width=\"" . $options['width'] . "\" data-colorscheme=\"" . $options['scheme'] . "\"></div>";

            if ($options['btdogan'] != 'no') {
                if ($options['btdogan'] != 'off') {
                    if (empty($fbcommentssync[btdogan])) {
                        $content .= '<p><a href="http://btdogan.com">Facebook Comments Sync</a></p>';
                    }
                }
            }
        }
    }
    return $content;
}

add_filter('the_content', 'fbcommentbox', 100);

function fbcsinit_top()
{
    $options = get_option('fbcommentssync');
    if (!isset($options['fbjs'])) {
        $options['fbjs'] = "";
    }
    if ($options['fbjs'] == 'on') {
        ?>
        <script>
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/<?php echo $options['language']; ?>/sdk.js#xfbml=1&version=v2.4&appID=<?php echo $options['app_ID']; ?>";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            jQuery(window).load(function ($) {
                FB.Event.subscribe('comment.create', comment_add);
                FB.Event.subscribe('comment.remove', comment_remove);
            });
        </script>

        <?php
    }
}

add_action('wp_head', 'fbcsinit_top', 100);


function fbcommentsajax()
{ ?>
    <script>

        var comment_add = function (response) {
            var cevap = response;

            jQuery.ajax({
                type: 'POST',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                data: {
                    'action': 'fbcs_ajaxCA', myData: cevap
                },
                success: function () {
                },
                error: function (exception) {
                    console.log('Exception:' + exception);
                }
            });
            return false;
        };

        var comment_remove = function (response) {
            var cevap = response;

            jQuery.ajax({
                type: 'POST',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                data: {
                    'action': 'fbcs_ajaxCR', myData: cevap
                },
                success: function () {
                    console.log('comment');
                },
                error: function (exception) {
                    console.log('Exception:' + exception);
                }
            });
            return false;
        };


    </script>

    <?php
}

add_action('wp_footer', 'fbcommentsajax', 100);
