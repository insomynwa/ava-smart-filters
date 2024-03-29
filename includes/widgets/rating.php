<?php

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ava_Smart_Filters_Rating_Widget extends Ava_Smart_Filters_Base_Widget {

	public function get_name() {
		return 'ava-smart-filters-rating';
	}

	public function get_title() {
		return __( 'Rating Filter', 'ava-smart-filters' );
	}

	public function get_icon() {
		return 'ava-smart-filters-rating-filter';
	}

	public function get_help_url() {
		return ava_smart_filters()->widgets->prepare_help_url(
			'https://blockcroco.com/knowledge-base/articles/avasmartfilters-how-to-filter-woocommerce-products-by-rating/',
			$this->get_name()
		);
	}

	protected function _register_controls() {

		$css_scheme = apply_filters(
			'ava-smart-filters/widgets/rating/css-scheme',
			array(
				'filter-rating-stars'  => '.ava-rating-stars',
				'filter-rating-icon'   => '.ava-rating-star__icon',
				'filter'               => '.ava-filter',
				'filter-control'       => '.ava-rating__control',
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

		$this->add_control(
			'filter_id',
			array(
				'label'       => __( 'Select filter', 'ava-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
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
			'rating_icon',
			array(
				'label'   => esc_html__( 'Rating icon', 'ava-smart-filters' ),
				'type'    => Controls_Manager::ICON,
				'default' => 'fa fa-star',
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


		$this->rating_controls_section_filter_stars( $css_scheme );

		$this->rating_controls_section_filter_label( $css_scheme );

		$this->rating_controls_section_filter_apply_button( $css_scheme );

	}

	public function rating_controls_section_filter_stars( $css_scheme ) {

		$this->start_controls_section(
			'section_stars_style',
			array(
				'label'      => esc_html__( 'Stars', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'stars_size',
			array(
				'label'      => esc_html__( 'Size', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-rating-icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stars_gutter',
			array(
				'label'      => esc_html__( 'Gutter', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-rating-icon'] => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stars_color',
			array(
				'label' => esc_html__( 'Default Color', 'ava-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ava-rating-star__label'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'stars_selected_color',
			array(
				'label' => esc_html__( 'Selected Color', 'ava-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ava-rating input.is-checked ~ label'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .ava-rating:not(.is-checked) label:hover'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .ava-rating:not(.is-checked) label:hover ~ label'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'stars_selected_hover_color',
			array(
				'label' => esc_html__( 'Selected On Hover Color', 'ava-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ava-rating input.is-checked + label:hover'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .ava-rating input.is-checked ~ label:hover'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .ava-rating label:hover ~ input.is-checked ~ label'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .ava-rating input.is-checked ~ label:hover ~ label'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'stars_margin',
			array(
				'label'      => esc_html__( 'Margin', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-rating-stars'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'stars_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'ava-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-control'] => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	public function rating_controls_section_filter_label( $css_scheme ) {

		$this->start_controls_section(
			'section_label_style',
			array(
				'label'      => esc_html__( 'Label', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filters-label'],
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'label_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['filters-label'],
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'ava-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	public function rating_controls_section_filter_apply_button( $css_scheme ) {

		$this->start_controls_section(
			'section_filter_apply_button_style',
			array(
				'label'      => esc_html__( 'Button', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_apply_button_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
			)
		);

		$this->start_controls_tabs( 'filter_apply_button_style_tabs' );

		$this->start_controls_tab(
			'filter_apply_button_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_apply_button_normal_color',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_normal_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_apply_button_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'filter_apply_button_border_border!' => '',
				)
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'filter_apply_button_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'filter_apply_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filter_apply_button_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'ava-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'   => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'  => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-right',
					),
					'stretch'  => array(
						'title' => esc_html__( 'Stretch', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$base_class = $this->get_name();
		$settings   = $this->get_settings();

		if ( empty( $settings['filter_id'] ) ) {
			return;
		}

		printf( '<div class="%1$s ava-filter">', $base_class );

		$query_id    = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label  = ! empty( $settings['show_label'] ) ? $settings['show_label'] : false;
		$icon = ! empty( $settings['rating_icon'] ) ? $settings['rating_icon'] : 'fa fa-star';
		$format      = '<i class="ava-rating-icon %s"></i>';
		$rating_icon = sprintf( $format, $icon );
		$show_label  = filter_var( $show_label, FILTER_VALIDATE_BOOLEAN );

		if ( 'ajax' === $settings['apply_type'] && 'submit' === $settings['apply_on'] ) {
			$apply_type = 'ajax-reload';
		} else {
			$apply_type = $settings['apply_type'];
		}

		ava_smart_filters()->filter_types->render_filter_template( $this->get_widget_fiter_type(), array(
			'filter_id'        => $settings['filter_id'],
			'content_provider' => $settings['content_provider'],
			'query_id'         => $query_id,
			'apply_type'       => $apply_type,
			'button_text'      => $settings['apply_button_text'],
			'show_label'       => $show_label,
			'rating_icon'      => $rating_icon,
			'__widget_id'      => $this->get_id()
		) );

		echo '</div>';

		if ( 'yes' === $settings['apply_button'] ) {
			include ava_smart_filters()->get_template( 'common/apply-filters.php' );
		}

	}

}