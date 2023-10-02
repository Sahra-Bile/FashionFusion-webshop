<?php
/**
 * List/Grid widget
 */
class BeRocket_cart_notices_Widget extends WP_Widget 
{
    public static $defaults = array(
        'title'     => '',
        'display'   => 'all',
    );
	public function __construct() {
        parent::__construct("cart_notices_widget", "WooCommerce Cart Notices",
            array("description" => ""));
    }
    /**
     * WordPress widget
     */
    public function widget($args, $instance)
    {
        $BeRocket_cart_notices = BeRocket_cart_notices::getInstance();
        $instance = wp_parse_args( (array) $instance, self::$defaults );
        $instance['title'] = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
        $options = $BeRocket_cart_notices->get_option();
        set_query_var( 'title', apply_filters( 'cart_notices_widget_title', $instance['title'] ) );
        set_query_var( 'args', $args );
        $BeRocket_cart_notices = $BeRocket_cart_notices->getInstance();
        ob_start();
        $BeRocket_cart_notices->shortcode($instance);
        $shortcode = ob_get_clean();
        if( ! empty($shortcode) ) {
            echo $args['before_widget'];
            $BeRocket_cart_notices->br_get_template_part( apply_filters( 'cart_notices_widget_template', 'widget' ) );
            echo $shortcode;
            echo $args['after_widget'];
        }
	}
    /**
     * Update widget settings
     */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
    /**
     * Widget settings form
     */
	public function form($instance)
	{
        $instance = wp_parse_args( (array) $instance, self::$defaults );
		$title = strip_tags($instance['title']);
		?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
		<?php
	}
}
?>
