<?php

class ET_Builder_Module_br_cart_notice extends ET_Builder_Module {

	public $slug       = 'et_pb_br_cart_notice';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => '',
		'author_uri' => '',
	);

	public function init() {
        $this->name             = __( 'Cart Notice', 'BeRocket_AJAX_domain' );
		$this->folder_name = 'et_pb_berocket_modules';
        
        $this->fields_defaults = array(
            'display_inline' => array('on'),
        );

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'   => array(
					'css'          => array(
						'main'      => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
						'important' => true,
					),
                    'hide_font_size' => true,
                    'hide_letter_spacing' => true,
                    'hide_line_height' => true,
                    'hide_text_shadow' => true,
				),
			),
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
							'border_styles' => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
						),
                        'defaults' => array(
                            'border_styles' => array(
                                'style' => 'none',
                            ),
                        ),
                        'important' => true,
					),
				),
			),
			'background'     => array(
				'css'      => array(
					'main'    => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
                    'important'=> true
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding'   => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
					'margin'    => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
					'important' => 'all',
				),
			),
			'box_shadow' => array(
                'default' => array(
                    'css' => array(
                        'main'      => "{$this->main_css_element} .berocket_cart_notice_shortcode > *",
                        'important' => 'all',
                    ),
                )
			),
			'link_options'  => false,
			'visibility'    => false,
			'text'          => false,
			'transform'     => false,
			'animation'     => false,
			'button'        => false,
			'filters'       => false,
			'max_width'     => false,
		);
	}

    function get_fields() {
        $fields = array(
            'display_inline' => array(
                'label'             => esc_html__( 'Notice example in builder', 'BeRocket_AJAX_domain' ),
                'type'              => 'yes_no_button',
                'options'           => array(
                    'off' => esc_html__( "No", 'et_builder' ),
                    'on'  => esc_html__( 'Yes', 'et_builder' ),
                )
            )
        );

        return $fields;
    }

    function render( $atts, $content = null, $function_name = '' ) {
        return do_shortcode('[br_cart_notices]');
    }
}

new ET_Builder_Module_br_cart_notice;
