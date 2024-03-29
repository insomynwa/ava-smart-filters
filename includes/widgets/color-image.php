<?php

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ava_Smart_Filters_Color_Image_Widget extends Ava_Smart_Filters_Base_Widget {

	public function get_name() {
		return 'ava-smart-filters-color-image';
	}

	public function get_title() {
		return __( 'Visual Filter', 'ava-smart-filters' );
	}

	public function get_icon() {
		return 'ava-smart-filters-color-image-filter';
	}

	public function get_help_url() {
		return ava_smart_filters()->widgets->prepare_help_url(
			'https://blockcroco.com/knowledge-base/articles/avasmartfilters-how-to-create-visual-filter-with-taxonomies-data-source/',
			$this->get_name()
		);
	}

	/**
	 * Returns image size array in slug => name format
	 *
	 * @return  array
	 */
	public function get_image_sizes() {

		global $_wp_additional_image_sizes;

		$sizes  = get_intermediate_image_sizes();
		$result = array();

		foreach ( $sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
			} else {
				$result[ $size ] = sprintf(
					'%1$s (%2$sx%3$s)',
					ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					$_wp_additional_image_sizes[ $size ]['width'],
					$_wp_additional_image_sizes[ $size ]['height']
				);
			}
		}

		return array_merge( array( 'full' => esc_html__( 'Full', 'ava-woo-builder' ), ), $result );
	}

	public function _register_controls() {

		$css_scheme = apply_filters(
			'ava-smart-filters/widgets/color-image/css-scheme',
			array(
				'item'         => '.ava-color-image-list__row',
				'label'        => '.ava-color-image-list__label',
				'decorator'    => '.ava-color-image-list__decorator',
				'list-item'    => '.ava-color-image-list__row',
				'list-wrapper' => '.ava-color-image-list-wrapper',
				'filter'               => '.ava-filter',
				'filters-label'        => '.ava-filter-label',
				'apply-filters'        => '.apply-filters',
				'apply-filters-button' => '.apply-filters__button',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'ava-smart-filters' ),
			)
		);

		$filter_control = $this->get_filter_control_settings();

		$this->add_control(
			'filter_id',
			array(
				'label'       => __( 'Select filter', 'ava-smart-filters' ),
				'label_block' => true,
				'type'        => $filter_control['type'],
				'multiple'    => $filter_control['multiple'],
				'default'     => '',
				'options'     => $this->get_widget_filters(),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'ava-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => ava_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'epro_posts_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>ava-smart-filters</b> into Query ID option of Posts widget you want to filter', 'ava-smart-filters' ),
				'condition' => array(
					'content_provider' => 'epro-posts',
				),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'ava-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'ava-smart-filters' ),
					'reload' => __( 'Page reload', 'ava-smart-filters' ),
				),
			)
		);

		$this->add_control(
			'apply_on',
			array(
				'label'     => __( 'Apply on', 'ava-smart-filters' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'value',
				'options'   => array(
					'value'  => __( 'Value change', 'ava-smart-filters' ),
					'submit' => __( 'Click on apply button', 'ava-smart-filters' ),
				),
				'condition' => array(
					'apply_type' => 'ajax',
				),
			)
		);

		$this->add_control(
			'apply_button',
			array(
				'label'        => esc_html__( 'Show apply button', 'ava-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'ava-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'ava-smart-filters' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'apply_button_text',
			array(
				'label'     => esc_html__( 'Apply button text', 'ava-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Apply filters', 'ava-smart-filters' ),
				'condition' => array(
					'apply_button' => 'yes'
				),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'Show filter label', 'ava-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'ava-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'ava-smart-filters' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'ava-smart-filters' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'ava-smart-filters' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_display_options',
			array(
				'label' => __( 'Filter Options', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'show_items_label',
			array(
				'label'        => esc_html__( 'Show items label', 'ava-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'ava-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'ava-smart-filters' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'filter_image_size',
			array(
				'type'      => 'select',
				'label'     => esc_html__( 'Image Size', 'ava-smart-filters' ),
				'default'   => 'full',
				'options'   => $this->get_image_sizes(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items_style',
			array(
				'label'      => esc_html__( 'Items', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'filters_position',
			array(
				'label'       => esc_html__( 'Filters Position', 'ava-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'inline-block'    => array(
						'title' => esc_html__( 'Line', 'ava-smart-filters' ),
						'icon'  => 'fa fa-ellipsis-h',
					),
					'block' => array(
						'title' => esc_html__( 'Column', 'ava-smart-filters' ),
						'icon'  => 'fa fa-bars',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['list-item']     => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_space_between_horizontal',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['list-item']     => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['list-wrapper']  => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				),
				'condition'  => array(
					'filters_position' => 'inline-block'
				)
			)
		);

		$this->add_responsive_control(
			'filters_space_between_vertical',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['list-item'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filters_position' => 'inline-block'
				)
			)
		);

		$this->add_responsive_control(
			'filters_list_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'ava-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['list-wrapper'] => 'text-align: {{VALUE}};',
				),
				'condition'  => array(
					'filters_position' => 'inline-block'
				)
			)
		);

		$this->add_responsive_control(
			'items_space_between',
			array(
				'label'      => esc_html__( 'Space Between', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ':not(:last-child)'         => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['item'] . ':not(:first-child)'        => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
				),
				'condition'  => array(
					'filters_position!' => 'inline-block'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_color_image_style',
			array(
				'label'      => esc_html__( 'Item', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'color_image_size',
			array(
				'label'      => esc_html__( 'Size', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['decorator'] . ' .ava-color-image-list__color' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['decorator'] . ' .ava-color-image-list__image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'color_image_style_tabs' );

		$this->start_controls_tab(
			'color_image_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'ava-smart-filters' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'color_image_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['decorator'] . ' > *',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'color_image_checked_styles',
			array(
				'label' => esc_html__( 'Checked', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'color_image_checked_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ava-color-image-list__input:checked +' . $css_scheme['decorator'] . ' > *' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'color_image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['decorator'] . ' > *' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_color_image_label_style',
			array(
				'label'      => esc_html__( 'Item Label', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'color_image_label_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['label'],
			)
		);

		$this->add_responsive_control(
			'color_image_label_offset',
			array(
				'label'      => esc_html__( 'Offset Left', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['label'] => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'color_image_label_style_tabs' );

		$this->start_controls_tab(
			'color_image_label_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'color_image_label_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'color_image_label_checked_styles',
			array(
				'label' => esc_html__( 'Checked', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'color_image_label_checked_color',
			array(
				'label'     => esc_html__( 'Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ava-color-image-list__input:checked ~' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->base_controls_section_filter_label( $css_scheme );

		$this->base_controls_section_filter_apply_button( $css_scheme );

		$this->base_controls_section_filter_group( $css_scheme );
	}

}