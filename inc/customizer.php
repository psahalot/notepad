<?php
/**
 * Notepad Theme Customizer support
 *
 * @package WordPress
 * @subpackage Notepad
 * @since Notepad 1.0
 */

/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @since Notepad 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function notepad_customize_register($wp_customize) {

    /** ===============
     * Extends CONTROLS class to add textarea
     */
    class notepad_customize_textarea_control extends WP_Customize_Control {

        public $type = 'textarea';

        public function render_content() {
            ?>

            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <textarea rows="5" style="width:98%;" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
            </label>

            <?php
        }

    }

    // Displays a list of categories in dropdown
    class WP_Customize_Dropdown_Categories_Control extends WP_Customize_Control {

        public $type = 'dropdown-categories';

        public function render_content() {
            $dropdown = wp_dropdown_categories(
                    array(
                        'name' => '_customize-dropdown-categories-' . $this->id,
                        'echo' => 0,
                        'hide_empty' => false,
                        'show_option_none' => '&mdash; ' . __('Select', 'notepad') . ' &mdash;',
                        'hide_if_empty' => false,
                        'selected' => $this->value(),
                    )
            );

            $dropdown = str_replace('<select', '<select ' . $this->get_link(), $dropdown);

            printf(
                    '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>', $this->label, $dropdown
            );
        }

    }

    
    // Rename the label to "Display Site Title & Tagline" in order to make this option extra clear.
    $wp_customize->get_control('display_header_text')->label = __('Display Site Title &amp; Tagline', 'notepad');


    // Add new section for theme layout and color schemes
    $wp_customize->add_section('notepad_theme_layout_settings', array(
        'title' => __('Color Scheme', 'notepad'),
        'priority' => 30,
    ));

    
    // Add color scheme options
   
    $wp_customize->add_setting('notepad_color_scheme', array(
        'default' => 'blue',
    ));

    $wp_customize->add_control('notepad_color_scheme', array(
        'label' => 'Color Schemes',
        'section' => 'notepad_theme_layout_settings',
        'default' => 'red',
        'type' => 'radio',
        'choices' => array(
            'blue' => __('Blue', 'notepad'),
            'red' => __('Red', 'notepad'),
            'green' => __('Green', 'notepad'),
            'gray' => __('Gray', 'notepad'),
            'purple' => __('Purple', 'notepad'),
            'orange' => __('Orange', 'notepad'),
            'brown' => __('Brown', 'notepad'),
            'pink' => __('Pink', 'notepad'),
        ),
    ));

    
    
     // Add footer text section
    $wp_customize->add_section('notepad_footer', array(
        'title' => 'Footer Text', // The title of section
        'priority' => 70,
    ));

    $wp_customize->add_setting('notepad_footer_footer_text', array(
        'default' => '',
    ));
    $wp_customize->add_control(new notepad_customize_textarea_control($wp_customize, 'notepad_footer_footer_text', array(
        'section' => 'notepad_footer', // id of section to which the setting belongs
        'settings' => 'notepad_footer_footer_text',
    )));

    
    // Add custom CSS section
    $wp_customize->add_section('notepad_custom_css', array(
        'title' => 'Custom CSS', // The title of section
        'priority' => 80,
    ));
    
    $wp_customize->add_setting('notepad_custom_css');
    
    $wp_customize->add_control(new notepad_customize_textarea_control($wp_customize, 'notepad_custom_css', array(
        'section' => 'notepad_custom_css', // id of section to which the setting belongs
        'settings' => 'notepad_custom_css', 
    )));

   

    //remove default customizer sections
    $wp_customize->remove_section('background_image');
    $wp_customize->remove_section('colors');
}

add_action('customize_register', 'notepad_customize_register');


/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Notepad 1.0
 */
function notepad_customize_preview_js() {
    wp_enqueue_script('notepad_customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), '20131205', true);
}

add_action('customize_preview_init', 'notepad_customize_preview_js');


function notepad_header_output() {
    ?>
    <!--Customizer CSS--> 
    <style type="text/css">
    <?php echo esc_attr(get_theme_mod('notepad_custom_css')); ?>
    </style> 
    <!--/Customizer CSS-->
    <?php
}

// Output custom CSS to live site
add_action('wp_head', 'notepad_header_output');
