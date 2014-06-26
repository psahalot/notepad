<?php
/**
 * Solo Theme Customizer support
 *
 * @package WordPress
 * @subpackage Solo
 * @since Solo 1.0
 */

/**
 * Add postMessage support for site title, description and 
 * reorganize other elements for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function solo_customize_organizer($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    // reorganize background settings in customizer
    $wp_customize->get_control( 'background_color'  )->section   = 'background_image';
    $wp_customize->get_section( 'background_image'  )->title     = __('Background Settings','solo');
    $wp_customize->get_section( 'background_image' )->description = __('Please note that background color and image settings work only for Boxed Layout','solo'); 
    
    
    // Rename the label to "Display Site Title & Tagline" in order to make this option extra clear.
    $wp_customize->get_control('display_header_text')->label = __('Display Site Title &amp; Tagline', 'solo');
    
    // reorganize header settings in cusotmizer
    $wp_customize->get_control( 'header_textcolor'  )->section   = 'header_image';
    $wp_customize->get_control( 'display_header_text' )->section = 'header_image'; 
    $wp_customize->get_section( 'header_image'  )->title     = __('Header Settings','solo');
    
    $wp_customize->get_section( 'header_image'  )->priority     = 30;
    $wp_customize->get_section( 'background_image' )->priority  = 30; 
}

add_action('customize_register', 'solo_customize_organizer', 12);


/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @since Solo 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function solo_customize_register($wp_customize) {

    /** ===============
     * Extends CONTROLS class to add textarea
     */
    class solo_customize_textarea_control extends WP_Customize_Control {

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

    // Add new section for theme layout and color schemes
    $wp_customize->add_section('solo_theme_layout_settings', array(
        'title' => __('Color Scheme', 'solo'),
        'priority' => 30,
    ));

      // Add setting for primary color
    $wp_customize->add_setting('solo_theme_primary_color', array(
        'default' => '#EF7A7A', 
        'sanitize_callback' => 'solo_sanitize_hex_color',
        'sanitize_js_callback' => 'solo_sanitize_escaping',
    ));
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solo_theme_primary_color',
        array(
            'label' => 'Primary Color',
            'section' => 'solo_theme_layout_settings',
            'settings' => 'solo_theme_primary_color',
        )
    ));

    // Add setting for link color
    $wp_customize->add_setting('solo_theme_link_color', array(
        'default' => '#FFF', 
        'sanitize_callback' => 'solo_sanitize_hex_color',
        'sanitize_js_callback' => 'solo_sanitize_escaping',
    ));
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solo_theme_link_color',
        array(
            'label' => 'Link Color',
            'section' => 'solo_theme_layout_settings',
            'settings' => 'solo_theme_link_color',
        )
    ));
    
    // Add setting for link hover color
    $wp_customize->add_setting('solo_theme_linkhover_color', array(
        'default' => '#333', 
        'sanitize_callback' => 'solo_sanitize_hex_color',
        'sanitize_js_callback' => 'solo_sanitize_escaping',
    ));
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solo_theme_linkhover_color',
        array(
            'label' => 'Link Hover Color',
            'section' => 'solo_theme_layout_settings',
            'settings' => 'solo_theme_linkhover_color',
        )
    ));

    // Add footer text section
    $wp_customize->add_section('solo_footer', array(
        'title' => 'Footer Text', // The title of section
        'priority' => 75,
    ));

    $wp_customize->add_setting('solo_footer_footer_text', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'sanitize_js_callback' => 'solo_sanitize_escaping',
    ));
    
    $wp_customize->add_control(new solo_customize_textarea_control($wp_customize, 'solo_footer_footer_text', array(
        'section' => 'solo_footer', // id of section to which the setting belongs
        'settings' => 'solo_footer_footer_text',
    )));
    
    // Add custom CSS section 
    $wp_customize->add_section(
        'solo_custom_css_section', array(
        'title' => __('Custom CSS', 'smartshop'),
        'priority' => 80,
    ));

    $wp_customize->add_setting(
        'solo_custom_css', array(
        'default' => '',
        'sanitize_callback' => 'solo_sanitize_custom_css',
        'sanitize_js_callback' => 'solo_sanitize_escaping',
    ));

    $wp_customize->add_control(
        new solo_customize_textarea_control(
        $wp_customize, 'solo_custom_css', array(
        'label' => __('Add your custom css here and design live! (for advanced users)', 'smartshop'),
        'section' => 'solo_custom_css_section',
        'settings' => 'solo_custom_css'
    )));
}

add_action('customize_register', 'solo_customize_register');


/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Solo 1.0
 */
function solo_customize_preview_js() {
    wp_enqueue_script('solo_customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), '20131205', true);
}

add_action('customize_preview_init', 'solo_customize_preview_js');

/* 
 * Sanitize Hex Color for 
 * Primary and Secondary Color options
 * 
 * @since Solo 1.4
 */
function solo_sanitize_hex_color( $color ) {
    if ( $unhashed = sanitize_hex_color_no_hash( $color ) ) {
        return '#' . $unhashed;
    }
    return $color;
}

/* 
 * Sanitize Custom CSS 
 * 
 * @since Solo 1.4
 */

function solo_sanitize_custom_css( $input) {
    $input = wp_kses_stripslashes( $input);
    return $input;
}	

/*
 * Escaping for input values
 * 
 * @since Solo 1.4
 */
function solo_sanitize_escaping( $input) {
    $input = esc_attr( $input);
    return $input;
}


/**
 * Change theme colors based on theme options from customizer.
 *
 * @since Solo 1.0
 */
function solo_color_style() {
	$primary_color = get_theme_mod('solo_theme_primary_color');
        $link_color = get_theme_mod('solo_theme_link_color');
        $linkhover_color = get_theme_mod('solo_theme_linkhover_color'); 

	// If no custom options for text are set, let's bail
	if ( ($primary_color == '#ef7a7a' || $primary_color == '#EF7A7A') && ($link_color == '#FFF' || $link_color == '#fff' || $link_color == '#ffffff' || $link_color == '#FFFFFF') && ($linkhover_color == '#333' || $linkhover_color == '#333333') ) {
            return;
        }
        else {
	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="solo-colorscheme-css">

                #footercontainer,
                .pagination .page-numbers:hover,
                li span.current,
                li a:hover.page-numbers,
                button:hover,
                input:hover[type="button"],
                input:hover[type="reset"],
                input[type="submit"],
                .button:hover,
                .entry-content .button:hover,
                .main-navigation ul ul,
                .more-link,
                div.gform_wrapper .gform_footer input[type="submit"]
                {
                    background: <?php echo $primary_color; ?> ;
                }

                ::selection,
                ::-webkit-selection,
                ::-moz-selection,
                .widget_search #searchsubmit
                {
                    background:<?php echo $primary_color; ?> ;
                    color:<?php echo $link_color; ?> ;
                }
                
                .more-link:hover,
                .entry-header .entry-title a:hover,
                .header-meta a:hover,
                .widget_search #searchsubmit:hover,
                div.gform_wrapper .gform_footer input[type="submit"]:hover
                {
                   color:<?php echo $linkhover_color; ?> ; 
                }
                
                .site-title a,
                .sidebar a,
                .entry-header .entry-title a,
                .entry-header .entry-title,
                .entry-header h1 a:visited,
                .main-navigation ul a:hover,
                .main-navigation ul ul a:hover,
                .gform_wrapper .gfield_required,
                .gform_wrapper h3.gform_title,
                label .required,
                span.required{
                    color:<?php echo $primary_color; ?> ;
                }
                
                .main-navigation ul ul a,
                .more-link,
                .main-navigation ul a{
                    color:<?php echo $link_color; ?> ;
                }

	</style>
        <style type="text/css" id="solo-custom-css">
            <?php echo trim( get_theme_mod( 'solo_custom_css' ) ); ?>
        </style>
	<?php
        }
}
add_action('wp_head','solo_color_style');