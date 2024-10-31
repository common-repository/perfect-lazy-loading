<?php

class Perfect_Lazy_Loading_Frontend
{
    private $settings;
    private $default_placeholder = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    const IMAGE_SIZE_NAME = 'perfect_lazy_loading_placeholder';
    const LAZY_LOAD_CLASS = 'perfect-lazy-loading-image';

    public function __construct()
    {
        $this->settings = get_option('Perfect_Lazy_Loading_Settings');

        if (is_admin() || $this->settings['lazy_loading_active'] !== '1') {
            return;
        }

        $this->register_placeholder_image_size();
        $this->load_dependencies();
        $this->add_filters();
        $this->script_settings();
    }

    private function register_placeholder_image_size()
    {
        add_action('after_setup_theme', function () {
            add_image_size(self::IMAGE_SIZE_NAME, 1, 1, true);
        });
    }

    private function load_dependencies()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('pll',
                plugin_dir_url(PERFECT_LAZY_LOADING_PLUGIN_FILE) . 'frontend/assets/js/script.js', []);
        });
    }

    private function add_filters()
    {
        add_filter('the_content', [$this, 'pll_replace_images'], 10, 1);
        add_filter('acf_the_content', [$this, 'pll_replace_images'], 10, 1);
        add_filter('wp_get_attachment_image_attributes', [$this, 'pll_attachment_attributes_filter'], 10, 3);

        if (class_exists('Vc_Manager')) {
            add_filter('vc_shortcode_output', [$this, 'wpb_page_builder_modifications'], 10, 4);
        }
    }

    public function pll_replace_images($content)
    {
        $expression = "#<img([^>]*) src=(\"(?:[^\"]+)\"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#";

        return preg_replace_callback($expression, [$this, 'pll_get_replaced_image'], $content, PHP_INT_MAX);
    }

    /**
     * @param array $atts
     * @param WP_Post $attachment
     * @param $size
     * @return array
     */
    public function pll_attachment_attributes_filter($atts, $attachment, $size)
    {
        unset($atts['srcset']);
        unset($atts['sizes']);

        if (isset($atts['class'])) {
            $atts['class'] .= ' '.self::LAZY_LOAD_CLASS;
        } else {
            $atts['class'] = self::LAZY_LOAD_CLASS;
        }

        $atts['src'] = $this->default_placeholder;

        $attachment_meta = wp_get_attachment_metadata($attachment->ID);

        if (isset($attachment_meta['sizes'][self::IMAGE_SIZE_NAME])) {
            $atts['src'] = wp_get_attachment_image_url($attachment->ID, self::IMAGE_SIZE_NAME);
        }


        if (isset($atts['data-src']) && is_array($atts['data-src'])) {
            $atts['data-src'] = $this->get_active_image_sizes($attachment->ID, $attachment->guid, $atts['data-src']);
        } else {
            $atts['data-src'] = $this->get_active_image_sizes($attachment->ID, $attachment->guid);
        }

        return $atts;
    }

    private function pll_get_replaced_image($image)
    {
        if (strpos($image[0], 'data-no-lazy-load') !== false) {
            return $image[0];
        }

        $placeholder     = $this->default_placeholder;
        $image_src       = preg_replace(['/(-\d+(?:x\d+)?)/', '/\"/'], '', $image[2]);
        $attachment_id   = false;

        if (preg_match('/(\sclass=\")([^\"]*)(\")/', $image[1], $class_matches)) {
            $image[1] = str_replace($class_matches[0], '', $image[1]);
        } elseif (preg_match('/(\sclass=\")([^\"]*)(\")/', $image[3], $class_matches)) {
            $image[3] = str_replace($class_matches[0], '', $image[3]);
        }

        if ( ! empty($class_matches)) {
            $classes   = explode(' ', $class_matches[2]);
            $classes[] = self::LAZY_LOAD_CLASS;

            $attachment_id = $this->get_attachment_id_by_url($image_src);

            $classes = ' class="' . implode(' ', $classes) . '"';

            if ($attachment_id) {
                $attachment_meta = wp_get_attachment_metadata($attachment_id);

                if (isset($attachment_meta['sizes'][self::IMAGE_SIZE_NAME])) {
                    $placeholder = wp_get_attachment_image_url($attachment_id, self::IMAGE_SIZE_NAME);
                }
            }

        } else {
            $classes = ' class="' . self::LAZY_LOAD_CLASS . '"';
        }

        if ($attachment_id) {
            $data_src = $this->get_active_image_sizes($attachment_id, $image_src);
        } else {
            $data_src = $image[2];
        }

        $lazy_image = sprintf("<img %s src=\"%s\" data-src=%s %s %s/>", $classes, $placeholder, $data_src, $image[1],
            $image[3]);
        $lazy_image = preg_replace('/(\ssrcset=\")([^\"]*)(\")/', '', $lazy_image);
        $lazy_image = preg_replace('/(\ssizes=\")([^\"]*)(\")/', '', $lazy_image);

        return $lazy_image;
    }

    private function get_attachment_id_by_url($image_url)
    {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));

        return count($attachment) ? $attachment[0] : null;
    }

    /**
     * @param int $attachment_id
     * @param string $image_src
     * @param array|bool $overwrite_sizes
     * @return array|string
     */
    private function get_active_image_sizes($attachment_id, $image_src, $overwrite_sizes = false)
    {
        $metadata          = wp_get_attachment_metadata($attachment_id);
        $current_post_type = get_post_type();
        $data_src = [];

        if (isset($metadata['sizes'])) {
            unset($metadata['sizes'][self::IMAGE_SIZE_NAME]);
        }

        if (isset($this->settings['section_' . $current_post_type]) && $this->settings['section_' . $current_post_type]['overwrite'] === '1') {
            $active_sizes = $this->settings['section_' . $current_post_type]['image_sizes'];
        } else {
            $active_sizes = $this->settings['image_sizes'];
        }

        if ( ! $overwrite_sizes) {
            foreach ($active_sizes as $active_size => $active) {
                if ($active === '0' && isset($metadata['sizes'])) {
                    unset($metadata['sizes'][$active_size]);
                }
            }
        } else {
            foreach ($metadata['sizes'] as $size_name => $size_data) {
                if ( ! in_array($size_name, $overwrite_sizes) && isset($metadata['sizes'])) {
                    unset($metadata['sizes'][$size_name]);
                }
            }
        }

        if (isset($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $data) {
                $data_src[$size] = [
                    'width'  => $data['width'],
                    'height' => $data['height'],
                    'src'    => wp_get_attachment_image_url($attachment_id, $size)
                ];
            }
        }

        if (empty($data_src)) {
            return $image_src;
        }

        if (count($data_src) === 1) {
            return array_values($data_src)[0]['src'];
        }

        uasort($data_src, function ($a, $b) {
            return $a['width'] - $b['width'];
        });

        return json_encode($data_src);
    }

    private function script_settings()
    {
        add_action('wp_head', function () {
            $settings_data     = [];
            $current_post_type = get_post_type();

            if (isset($this->settings['section_' . $current_post_type])
                && $this->settings['section_' . $current_post_type]['overwrite'] === '1'
                && $this->settings['section_' . $current_post_type]['image_size_threshold'] > 0) {
                $settings_data['image_size_threshold'] = $this->settings['section_' . $current_post_type]['image_size_threshold'];
            } elseif (isset($this->settings['image_size_threshold'])) {
                $settings_data['image_size_threshold'] = $this->settings['image_size_threshold'];
            }

            $settings_data['lazy_loading_class'] = self::LAZY_LOAD_CLASS;

            echo '<script> window.PLL_load_settings = ' . json_encode($settings_data) . '; </script>';
        });
    }

    /**
     * WP Bakery Pagebuilder Filters
     */
    public function wpb_page_builder_modifications($output, $object, $prepared_atts, $shortcode)
    {
        $function_name = 'filter_' . $shortcode;
        if (method_exists($this, $function_name)) {
            $this->$function_name($output);
        }

        return $output;
    }

    private function filter_vc_single_image(&$output)
    {
        $settings   = $this->settings['section_vc_single_image'];
        $expression = "#<a([^>]*) href=(\"(?:[^\"]+)\"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#";
        preg_match($expression, $output, $matches);
        $url = $matches[2];

        if ($settings['active'] === '1' && isset($settings['image_size'])) {
            $image_src     = preg_replace(['/(-\d+(?:x\d+)?)/', '/\"/'], '', $matches[2]);
            $attachment_id = $this->get_attachment_id_by_url($image_src);

            if ($target_url = wp_get_attachment_image_url($attachment_id, $settings['image_size'])) {
                $url = '"' . $target_url . '"';
            }
        }

        $output = str_replace($matches[0], "<a $matches[1] href=$url $matches[3] >", $output);
    }

}
