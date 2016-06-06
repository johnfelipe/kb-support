<?php
/**
 * HTML elements
 *
 * A helper class for outputting common HTML elements.
 *
 * @package     KBS
 * @subpackage  Classes/HTML
 * @copyright   Copyright (c) 2016, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * KBS_HTML_Elements Class
 *
 * @since	1.0
 */
class KBS_HTML_Elements {

	/**
	 * Renders an HTML Dropdown of all the Ticket Post Statuses
	 *
	 * @access	public
	 * @since	1.0
	 * @param	str		$name		Name attribute of the dropdown
	 * @param	str		$selected	Status to select automatically
	 * @return	str		$output		Status dropdown
	 */
	public function ticket_status_dropdown( $name = 'post_status', $selected = 0 ) {
		$ticket_statuses = kbs_get_post_statuses( 'labels' );
		$options    = array();
		
		foreach ( $ticket_statuses as $ticket_status ) {
			$options[ $ticket_status->name ] = esc_html( $ticket_status->label );
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => '',
			'show_option_none' => false
		) );

		return $output;
	} // ticket_status_dropdown

	/**
	 * Renders an HTML Dropdown of all the Ticket Categories
	 *
	 * @access	public
	 * @since	1.0
	 * @param	str		$name		Name attribute of the dropdown
	 * @param	int		$selected	Category to select automatically
	 * @return	str		$output		Category dropdown
	 */
	public function ticket_category_dropdown( $name = 'kbs_ticket_categories', $selected = 0 ) {
		$categories = get_terms( 'ticket_category', apply_filters( 'kbs_ticket_category_dropdown', array() ) );
		$options    = array();

		foreach ( $categories as $category ) {
			$options[ absint( $category->term_id ) ] = esc_html( $category->name );
		}

		$category_labels = kbs_get_taxonomy_labels( 'ticket_category' );
		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => sprintf( _x( 'All %s', 'plural: Example: "All Categories"', 'kb-support' ), $category_labels['name'] ),
			'show_option_none' => false
		) );

		return $output;
	} // ticket_category_dropdown
	
	/**
	 * Renders an HTML Dropdown of all the KB Categories
	 *
	 * @access	public
	 * @since	1.0
	 * @param	str		$name		Name attribute of the dropdown
	 * @param	int		$selected	Category to select automatically
	 * @return	str		$output		Category dropdown
	 */
	public function kb_category_dropdown( $name = 'kbs_kb_categories', $selected = 0 ) {
		$categories = get_terms( 'kb_category', apply_filters( 'kbs_kb_category_dropdown', array() ) );
		$options    = array();

		foreach ( $categories as $category ) {
			$options[ absint( $category->term_id ) ] = esc_html( $category->name );
		}

		$category_labels = kbs_get_taxonomy_labels( 'kb_category' );
		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => sprintf( _x( 'All %s', 'plural: Example: "All Categories"', 'kb-support' ), $category_labels['name'] ),
			'show_option_none' => false
		) );

		return $output;
	} // kb_category_dropdown

	/**
	 * Renders an HTML Dropdown of years
	 *
	 * @access	public
	 * @since	1.0
	 * @param	str		$name			Name attribute of the dropdown
	 * @param	int		$selected		Year to select automatically
	 * @param	int		$years_before	Number of years before the current year the dropdown should start with
	 * @param	int		$years_after	Number of years after the current year the dropdown should finish at
	 * @return	str		$output			Year dropdown
	 */
	public function year_dropdown( $name = 'year', $selected = 0, $years_before = 5, $years_after = 0 ) {
		$current     = date( 'Y' );
		$start_year  = $current - absint( $years_before );
		$end_year    = $current + absint( $years_after );
		$selected    = empty( $selected ) ? date( 'Y' ) : $selected;
		$options     = array();

		while ( $start_year <= $end_year ) {
			$options[ absint( $start_year ) ] = $start_year;
			$start_year++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	} // year_dropdown

	/**
	 * Renders an HTML Dropdown of months
	 *
	 * @access	public
	 * @since	1.0
	 * @param	str		$name		Name attribute of the dropdown
	 * @param	int		$selected	Month to select automatically
	 * @return	str		$output		Month dropdown
	 */
	public function month_dropdown( $name = 'month', $selected = 0 ) {
		$month   = 1;
		$options = array();
		$selected = empty( $selected ) ? date( 'n' ) : $selected;

		while ( $month <= 12 ) {
			$options[ absint( $month ) ] = kbs_month_num_to_name( $month );
			$month++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	} // month_dropdown

	/**
	 * Renders an HTML Dropdown
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args
	 *
	 * @return	str
	 */
	public function select( $args = array() ) {
		$defaults = array(
			'options'          => array(),
			'name'             => null,
			'class'            => '',
			'id'               => '',
			'selected'         => 0,
			'chosen'           => false,
			'placeholder'      => null,
			'multiple'         => false,
			'show_option_all'  => _x( 'All', 'all dropdown items', 'kb-support' ),
			'show_option_none' => _x( 'None', 'no dropdown items', 'kb-support' ),
			'data'             => array(),
		);

		$args = wp_parse_args( $args, $defaults );
		
		$args['id'] = ! empty( $args['id'] ) ? $args['id'] : $args['name'];

		$data_elements = '';
		foreach ( $args['data'] as $key => $value ) {
			$data_elements .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		if( $args['multiple'] ) {
			$multiple = ' MULTIPLE';
		} else {
			$multiple = '';
		}

		if( $args['chosen'] ) {
			$args['class'] .= ' kbs-select-chosen';
		}

		if( $args['placeholder'] ) {
			$placeholder = $args['placeholder'];
		} else {
			$placeholder = '';
		}

		$class  = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$output = '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( kbs_sanitize_key( str_replace( '-', '_', $args['id'] ) ) ) . '" class="kbs-select ' . $class . '"' . $multiple . ' data-placeholder="' . $placeholder . '"'. $data_elements . '>' . "\r\n";

		if ( $args['show_option_all'] ) {
			if( $args['multiple'] ) {
				$selected = selected( true, in_array( 0, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>' . "\r\n";
		}

		if ( ! empty( $args['options'] ) ) {

			if ( $args['show_option_none'] ) {
				if( $args['multiple'] ) {
					$selected = selected( true, in_array( -1, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], -1, false );
				}
				$output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>' . "\r\n";
			}

			foreach( $args['options'] as $key => $option ) {

				if( $args['multiple'] && is_array( $args['selected'] ) ) {
					$selected = selected( true, in_array( $key, $args['selected'], true ), false );
				} else {
					$selected = selected( $args['selected'], $key, false );
				}

				$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>' . "\r\n";
			}
		}

		$output .= '</select>' . "\r\n";

		return $output;
	} // select

	/**
	 * Renders an HTML Checkbox
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args
	 *
	 * @return	string
	 */
	public function checkbox( $args = array() ) {
		$defaults = array(
			'name'     => null,
			'current'  => null,
			'class'    => 'kbs-checkbox',
			'options'  => array(
				'disabled' => false,
				'readonly' => false
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$options = '';
		if ( ! empty( $args['options']['disabled'] ) ) {
			$options .= ' disabled="disabled"';
		} elseif ( ! empty( $args['options']['readonly'] ) ) {
			$options .= ' readonly';
		}

		$output = '<input type="checkbox"' . $options . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" ' . checked( 1, $args['current'], false ) . ' />';

		return $output;
	} // checkbox
	
	/**
	 * Renders an HTML Checkbox List
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args
	 *
	 * @return	string
	 */
	public function checkbox_list( $args = array() ) {
		$defaults = array(
			'name'      => null,
			'class'     => 'kbs-checkbox',
			'label_pos' => 'before',
			'options'   => array()
		);

		$args = wp_parse_args( $args, $defaults );

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		$label_pos = isset( $args['label_pos'] ) ? $args['label_pos'] : 'before';

		$output = '';
		
		if ( ! empty( $args['options'] ) )	{

			$i = 0;

			foreach( $args['options'] as $key => $value )	{

				if ( $label_pos == 'before' )	{
					$output .= $value . '&nbsp';
				}

				$output .= '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '[]" id="' . esc_attr( $args['name'] ) . '-' . $key . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" value="' . $key . '" />';

				if ( $label_pos == 'after' )	{
					$output .= '&nbsp' . $value;
				}

				if ( $i < count( $args['options'] ) )	{
					$output .= '<br />';
				}

				$i++;

			}
			
		}

		return $output;
	} // checkbox_list
	
	/**
	 * Renders HTML Radio Buttons
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args
	 *
	 * @return	string
	 */
	public function radio( $args = array() ) {
		$defaults = array(
			'name'     => null,
			'current'  => null,
			'class'    => 'kbs-radio',
			'label_pos' => 'before',
			'options'  => array()
		);

		$args = wp_parse_args( $args, $defaults );

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		$output = '';
		
		if ( ! empty( $args['options'] ) )	{

			$i = 0;

			foreach( $args['options'] as $key => $value )	{

				if ( $label_pos == 'before' )	{
					$output .= $value . '&nbsp';
				}

				$output = '<input type="radio" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '-' . $key . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" />';

				if ( $label_pos == 'after' )	{
					$output .= '&nbsp' . $value;
				}

				if ( $i < count( $args['options'] ) )	{
					$output .= '<br />';
				}

				$i++;

			}
			
		}

		return $output;
	} // radio

	/**
	 * Renders an HTML Text field
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args	Arguments for the text field
	 * @return	str		Text field
	 */
	public function text( $args = array() ) {

		$defaults = array(
			'id'           => '',
			'name'         => isset( $name )  ? $name  : 'text',
			'value'        => isset( $value ) ? $value : null,
			'label'        => isset( $label ) ? $label : null,
			'desc'         => isset( $desc )  ? $desc  : null,
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => '',
			'data'         => false
		);

		$args = wp_parse_args( $args, $defaults );
		
		$args['id'] = ! empty( $args['id'] ) ? $args['id'] : $args['name'];

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . kbs_sanitize_key( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}

		$output = '<span id="kbs-' . kbs_sanitize_key( $args['name'] ) . '-wrap">';

			$output .= '<label class="kbs-label" for="' . kbs_sanitize_key( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';

			if ( ! empty( $args['desc'] ) ) {
				$output .= '<span class="kbs-description">' . esc_html( $args['desc'] ) . '</span>';
			}

			$output .= '<input type="text" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] )  . '" autocomplete="' . esc_attr( $args['autocomplete'] )  . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $class . '" ' . $data . '' . $disabled . '/>';

		$output .= '</span>';

		return $output;
	} // text

	/**
	 * Renders a date picker
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args	Arguments for the text field
	 * @return	str		Datepicker field
	 */
	public function date_field( $args = array() ) {

		if( empty( $args['class'] ) ) {
			$args['class'] = 'kbs_datepicker';
		} elseif( ! strpos( $args['class'], 'kbs_datepicker' ) ) {
			$args['class'] .= ' kbs_datepicker';
		}

		return $this->text( $args );
	} // date_field

	/**
	 * Renders an HTML textarea
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args	Arguments for the textarea
	 * @return	srt		textarea
	 */
	public function textarea( $args = array() ) {
		$defaults = array(
			'name'        => 'textarea',
			'value'       => null,
			'label'       => null,
			'placeholder' => null,
			'desc'        => null,
			'class'       => 'large-text',
			'disabled'    => false
		);

		$args = wp_parse_args( $args, $defaults );

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';

		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		
		$placeholder = '';
		if( $args['placeholder'] ) {
			$placeholder = ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}

		$output = '<span id="kbs-' . kbs_sanitize_key( $args['name'] ) . '-wrap">';

			$output .= '<label class="kbs-label" for="' . kbs_sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';

			$output .= '<textarea name="' . esc_attr( $args['name'] ) . '" id="' . kbs_sanitize_key( $args['name'] ) . '" class="' . $class . '"' . $disabled . $placeholder . '>' . esc_attr( $args['value'] ) . '</textarea>';

			if ( ! empty( $args['desc'] ) ) {
				$output .= '<span class="kbs-description">' . esc_html( $args['desc'] ) . '</span>';
			}

		$output .= '</span>';

		return $output;
	} // textarea
	
	/**
	 * Renders an HTML Number field
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args	Arguments for the text field
	 * @return	str		Text field
	 */
	public function number( $args = array() ) {

		$defaults = array(
			'id'           => '',
			'name'         => isset( $name )  ? $name  : 'text',
			'value'        => isset( $value ) ? $value : null,
			'label'        => isset( $label ) ? $label : null,
			'desc'         => isset( $desc )  ? $desc  : null,
			'placeholder'  => '',
			'class'        => 'small-text',
			'min'          => '',
			'max'          => '',
			'disabled'     => false,
			'autocomplete' => '',
			'data'         => false
		);

		$args = wp_parse_args( $args, $defaults );
		
		$args['id'] = ! empty( $args['id'] ) ? $args['id'] : $args['name'];

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . kbs_sanitize_key( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}
		
		$min = ! empty( $args['min'] ) ? ' min="' . $args['min'] . '"' : '';
		$max = ! empty( $args['max'] ) ? ' max="' . $args['max'] . '"' : '';
		
		if ( $max > 5 )	{
			$max = 5;
		}

		$output = '<span id="kbs-' . kbs_sanitize_key( $args['name'] ) . '-wrap">';

			$output .= '<label class="kbs-label" for="' . kbs_sanitize_key( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';

			if ( ! empty( $args['desc'] ) ) {
				$output .= '<span class="kbs-description">' . esc_html( $args['desc'] ) . '</span>';
			}

			$output .= '<input type="number" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] )  . '" autocomplete="' . esc_attr( $args['autocomplete'] )  . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $class . '" ' . $data . '' . $min . '' . $max . '' . $disabled . '/>';

		$output .= '</span>';

		return $output;
	} // number
	
	/**
	 * Renders an HTML Hidden field
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args	Arguments for the text field
	 * @return	str		Hidden field
	 */
	public function hidden( $args = array() ) {

		$defaults = array(
			'id'           => '',
			'name'         => isset( $name )  ? $name  : 'hidden',
			'value'        => isset( $value ) ? $value : null
		);

		$args = wp_parse_args( $args, $defaults );
		
		$args['id'] = ! empty( $args['id'] ) ? $args['id'] : $args['name'];

		$output = '<input type="hidden" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] )  . '" value="' . esc_attr( $args['value'] ) . '" />';

		return $output;
	} // hidden

	/**
	 * Renders an ajax user search field
	 *
	 * @since	1.0
	 *
	 * @param	arr		$args
	 * @return	str		Text field with ajax search
	 */
	public function ajax_user_search( $args = array() ) {

		$defaults = array(
			'name'        => 'user_id',
			'value'       => null,
			'placeholder' => __( 'Enter username', 'kb-support' ),
			'label'       => null,
			'desc'        => null,
			'class'       => '',
			'disabled'    => false,
			'autocomplete'=> 'off',
			'data'        => false
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] = 'kbs-ajax-user-search ' . $args['class'];

		$output  = '<span class="kbs_user_search_wrap">';
			$output .= $this->text( $args );
			$output .= '<span class="kbs_user_search_results hidden"><a class="kbs-ajax-user-cancel" title="' . __( 'Cancel', 'kb-support' ) . '" aria-label="' . __( 'Cancel', 'kb-support' ) . '" href="#">x</a><span></span></span>';
		$output .= '</span>';

		return $output;
	} // ajax_user_search

} // KBS_HTML_Elements