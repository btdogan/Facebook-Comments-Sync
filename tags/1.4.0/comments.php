<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);


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
        if (!isset($post_meta['_disable_fbcs'])) {
            $post_meta['_disable_fbcs'] = "off";
        }

        if ($post_meta['_disable_fbcs'] != 'on') {

            if ($options['title'] != '') {
                if ($options['titleclass'] == '' && $options['titleid'] == '') {
                    $commenttitle = "<h3>";
                } else {
                    if ($options['titleid'] == '') {
                        $commenttitle = "<h3 class=\"" . $options['titleclass'] . "\">";
                    }
                    else if ($options['titleclass'] == ''){
                        $commenttitle = "<h3 id=\"" . $options['titleid'] . "\">";
                    }
                    else {
                        $commenttitle = "<h3 class=\"" . $options['titleclass'] . "\" id=\"" . $options['titleid'] . "\">";
                    }
                }
                $commenttitle .= $options['title'] . "</h3>";
            }

            ?>
            <div id='comments' class='comments-area'> <?php $commenttitle; ?>

            <div class="fb-comments" data-href="<?php echo get_permalink(); ?>" data-num-posts="<?php echo $options['num']; ?>" data-width="<?php echo $options['width']; ?>" data-colorscheme="<?php echo $options['scheme']; ?>" data-notify='true'></div>
<?php
            if ($options['btdogan'] != 'no') {
                if ($options['btdogan'] != 'off') {
                    if (empty($fbcommentssync['btdogan'])) {
                        ?>
                <p><a href="http://btdogan.com">Facebook Comments Sync</a></p> <?php
                    }
                }
            }
?>
            </div>
<?php
        }
    }
