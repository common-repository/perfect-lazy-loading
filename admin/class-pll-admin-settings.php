<?php

class Perfect_Lazy_Loading_Settings extends AdminPageFramework
{
    private $sizes;
    private $sizes_cb_labels = [];
    private $sizes_cb_defaults = [];

    public function setUp()
    {
        $this->setup_sizes_values();

        // Create the root menu
        $this->setRootMenuPage(
            PERFECT_LAZY_LOADING_PLUGIN_TITLE,
            plugin_dir_path(PERFECT_LAZY_LOADING_PLUGIN_FILE) . 'admin/assets/img/menu_icon.png'
        );

        // Add the sub menu item
        $this->addSubMenuItems(
            [
                'title'      => __('Settings', 'pll'),
                'menu_title' => __('Settings', 'pll'),
                'page_slug'  => 'perfect_lazy_loading_settings',
            ],
            [
                'title'      => __('Import / Export Settings', 'pll'),
                'menu_title' => __('Import / Export', 'pll'),
                'page_slug'  => 'perfect_lazy_loading_import_export_settings',
            ]
        );

        // Add in-page tabs
        $this->addInPageTabs(
            'perfect_lazy_loading_settings',
            [
                'tab_slug' => 'main',
                'title'    => __('General', 'pll')
            ],
            [
                'tab_slug' => 'post_types',
                'title'    => __('Post Types', 'pll')
            ]
        );

        if (class_exists('Vc_Manager')) {
            $this->addInPageTabs(
                [
                    'tab_slug' => 'vc',
                    'title'    => __('WP Bakery Page Builder', 'pll')
                ]
            );
        }
    }

    public function load_perfect_lazy_loading_import_export_settings()
    {
        $this->addSettingFields(
            [
                'field_id'    => 'import_settings',
                'type'        => 'import',
                'title'       => __('Import your Settings', 'pll'),
                'label' => __('Import settings', 'pll')
            ],
            [
                'field_id'    => 'export_settings',
                'type'        => 'export',
                'title'       => __('Export your Settings', 'pll'),
                'label' => __('Export settings', 'pll')
            ]
        );
    }

    public function load_perfect_lazy_loading_settings_main()
    {
        $this->addSettingFields(
            [
                'field_id'    => 'lazy_loading_active',
                'title'       => __('Activate lazy loading', 'pll'),
                'type'        => 'checkbox',
                'label'       => '',
                'default'     => false,
                'after_label' => '<br/>'
            ],
            [
                'field_id'    => 'image_sizes',
                'title'       => __('Image sizes to be used', 'pll'),
                'type'        => 'checkbox',
                'label'       => $this->sizes_cb_labels,
                'default'     => $this->sizes_cb_defaults,
                'after_label' => '<br/>'
            ],
            [
                'field_id'    => 'image_size_threshold',
                'title'       => __('Threshold', 'pll'),
                'type'        => 'number',
                'description' => __('Value (in%) by how much smaller the image size may be than the actual displayed size.', 'pll')
            ],
            [
                'field_id'    => 'submit_button',
                'type'        => 'submit',
                'placeholder' => __('Save settings', 'pll')
            ]
        );
    }

    public function load_perfect_lazy_loading_settings_post_types()
    {
        $post_types = Perfect_Lazy_Loading_Admin::get_relevant_post_types();

        foreach ($post_types as $post_type) {
            $post_type_object = get_post_type_object($post_type);

            $this->addSettingSections([
                'section_id'   => 'section_' . $post_type,
                'title'        => $post_type_object->labels->singular_name . ' (' . $post_type . ')',
                'repeatable'   => false,
                'sortable'     => false,
                'collapsible'  => true,
                'is_collapsed' => true
            ]);
            $this->addSettingFields(
                'section_' . $post_type,
                [
                    'field_id' => 'overwrite',
                    'title' => __('Overwrite settings', 'pll'),
                    'type' => 'checkbox',
                    'default' => false
                ],
                [
                    'field_id'    => 'image_sizes',
                    'title'       => __('Image sizes to be used', 'pll'),
                    'type'        => 'checkbox',
                    'label'       => $this->sizes_cb_labels,
                    'default'     => $this->sizes_cb_defaults,
                    'after_label' => '<br/>'
                ],
                [
                    'field_id'    => 'image_size_threshold',
                    'title'       => __('Threshold', 'pll'),
                    'type'        => 'number',
                    'description' => __('Value (in%) by how much smaller the image size may be than the actual displayed size.', 'pll')
                ],
                [
                    'field_id'    => 'submit_button',
                    'type'        => 'submit',
                    'placeholder' => __('Save settings', 'pll')
                ]
            );

        }
    }

    public function load_perfect_lazy_loading_settings_vc() {
        $this->addSettingSections([
            'section_id'   => 'section_vc_single_image',
            'title'        => __('Single Image', 'pll'),
            'repeatable'   => false,
            'sortable'     => false,
            'collapsible'  => true,
            'is_collapsed' => true
        ]);

        $this->addSettingFields(
            'section_vc_single_image',
            [
                'field_id' => 'active',
                'title' => __('Overwrite Lightbox Link', 'pll'),
                'type' => 'checkbox',
                'default' => false
            ],
            [
                'field_id'    => 'image_size',
                'title'       => __('Image size to be used', 'pll'),
                'type'        => 'radio',
                'label'       => $this->sizes_cb_labels,
                'default'     => $this->sizes_cb_defaults,
                'after_label' => '<br/>'
            ],
            [
                'field_id'    => 'submit_button',
                'type'        => 'submit',
                'placeholder' => __('Save settings', 'pll')
            ]
        );
    }

    private function setup_sizes_values() {
        $this->sizes             = Perfect_Lazy_Loading_Admin::get_image_sizes();

        foreach ($this->sizes as $name => $data) {
            if ($name === Perfect_Lazy_Loading_Frontend::IMAGE_SIZE_NAME) {
                continue;
            }
            $dimensions = '';
            $dimensions .= $data['width'] . 'px';
            $dimensions .= $data['height'] ? ' X ' . $data['height'] . 'px (' . __('width', 'pll') . ' x ' . __('height', 'pll') . ')' : ' (' . __('width', 'pll') . ')';
            $dimensions .= $data['crop'] ? ' <em>' . __('cropped', 'pll') . '</em>' : '';

            $this->sizes_cb_labels[$name]   = '<strong>' . $name . ':</strong> ' . $dimensions;
            $this->sizes_cb_defaults[$name] = false;
        }
    }
}