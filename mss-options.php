<?php

class MoJSocialShareOptions
{
    private $post_type_options = [];

    public function __construct()
    {
        add_action('admin_menu', array($this, 'options_page'));
        add_action('admin_init', array($this, 'options_page_init'));
        add_action('admin_init', array($this, 'setup_fields'));
    }

    public function options_page()
    {
        add_options_page(
            'MoJ Social Share Options', // page_title
            'MoJ Social Share Options', // menu_title
            'administrator', // capability
            'moj-social-share-options', // menu_slug
            array($this, 'options_page_content') // function
        );
    }

    public function options_page_content()
    {
        ?>
        <div class="wrap">
            <h1>MoJ Social Share Options</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('moj-social-share-options-group');
                do_settings_sections('moj-social-share-options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function options_page_init()
    {
        add_settings_section(
            'moj_social_share_section', // id
            'Select one or more post types (or none) where you would like the social share Facebook and Twitter icons to appear. ', // title
            null, // callback
            'moj-social-share-options' // page
        );
    }

    public function list_post_types()
    {
        // create list of content types
        $all_post_types = get_post_types(['public' => true]);

        if (is_array($all_post_types)) {
            foreach ($all_post_types as $key => $post_type) {
                $this->post_type_options[] =
                    [
                        'uid' => 'moj-field-' . $key,
                        'label' => ucwords(str_replace(['-', '_'], ' ', $post_type)),
                        'section' => 'moj_social_share_section',
                        'type' => 'checkbox',
                        'options' => false,
                        'placeholder' => '',
                        'helper' => '',
                        'supplemental' => '',
                        'default' => ''
                    ];
            }
            //echo '<pre>' . print_r($this->post_type_options, true) . '</pre>';
        }

        return $this->post_type_options;
    }

    public function setup_fields()
    {
        $fields = $this->list_post_types();

        foreach ($fields as $field) {
            add_settings_field(
                $field['uid'],
                $field['label'],
                array($this, 'field_callback'),
                'moj-social-share-options',
                $field['section'], $field
            );

            register_setting('moj-social-share-options-group', $field['uid']);
        }
    }

    public function field_callback($arguments)
    {
        $value = get_option($arguments['uid']); // Get the current value, if there is one

        // Check which type of field we want
        switch ($arguments['type']) {
            case 'checkbox': // If it is a checkbox field
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="1" %4$s />', $arguments['uid'],
                    $arguments['type'], $arguments['placeholder'], checked(1,
                        $value, false));
                break;
        }

        // If there is help text
        if ($helper = $arguments['helper']) {
            printf('<span class="helper"> %s</span>', $helper); // Show it
        }

        // If there is supplemental text
        if ($supplemental = $arguments['supplemental']) {
            printf('<p class="description">%s</p>', $supplemental); // Show it
        }
    }
}
