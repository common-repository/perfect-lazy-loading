<?php

class Perfect_Lazy_Loading_Admin
{
    public function __construct()
    {
        $this->load_dependencies();
    }

    private function load_dependencies()
    {
        require_once(plugin_dir_path(PERFECT_LAZY_LOADING_PLUGIN_FILE) . 'lib/apf/admin-page-framework.php');
        require_once(plugin_dir_path(PERFECT_LAZY_LOADING_PLUGIN_FILE) . 'admin/class-pll-admin-settings.php');

        new Perfect_Lazy_Loading_Settings();
    }

    public static function get_image_sizes()
    {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                $sizes[$_size]['width']  = get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = get_option("{$_size}_size_h");
                $sizes[$_size]['crop']   = (bool)get_option("{$_size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array(
                    'width'  => $_wp_additional_image_sizes[$_size]['width'],
                    'height' => $_wp_additional_image_sizes[$_size]['height'],
                    'crop'   => $_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        return $sizes;
    }

    public static function get_relevant_post_types(){
        $exclude_posttypes = [
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'vc4_templates',
            'vc_grid_item',
            'acf-field-group',
            'acf-field'
        ];

        $post_types = get_post_types();

        foreach ($exclude_posttypes as $name ) {
            unset($post_types[$name]);
        }

        return $post_types;
    }
}