<?php

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ava_Smart_Filters_Search_Widget extends Ava_Smart_Filters_Base_Widget {

	public function get_name() {
		return 'ava-smart-filters-search';
	}

	public function get_title() {
		return __( 'Search Filter', 'ava-smart-filters' );
	}

	public function get_icon() {
		return 'ava-smart-filters-search-filter';
	}

	public function get_help_url() {
		return ava_smart_filters()->widgets->prepare_help_url(
			'https://blockcroco.com/knowledge-base/articles/avasmartfilters-how-to-create-a-search-filter/',
			$this->get_name()
		);
	}

	protected function _register_controls() {

		$css_scheme = apply_filters(
			'ava-smart-filters/widgets/search/css-scheme',
			array(
				'filters-label'             => '.ava-filter-label',
				'filter-wrapper'            => '.ava-search-filter',
				'input'                     => '.ava-search-filter__input',
				'apply-filters-button'      => '.ava-search-filter__submit',
				'apply-filters-button-icon' => '.ava-search-filter__submit > i',
				'apply-filters-label'       => '.ava-filter-label',
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
				'label'   => __( 'Select filter', 'ava-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_widget_filters(),
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
			'apply_button_text',
			array(
				'label'   => esc_html__( 'Search button text', 'ava-smart-filters' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Apply filters', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'apply_button_icon',
			array(
				'label'   => esc_html__( 'Search button icon', 'ava-smart-filters' ),
				'type'    => Controls_Manager::ICON,
				'default' => '',
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
			'section_search_content_style',
			array(
				'label'      => __( 'Content', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'content_position',
			array(
				'label'   => esc_html__( 'Position', 'ava-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'line' => array(
						'title' => esc_html__( 'Line', 'ava-smart-filters' ),
						'icon'  => 'fa fa-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Columns', 'ava-smart-filters' ),
						'icon'  => 'fa fa-bars',
					),
				),
				'selectors_dictionary' => array(
					'line'      => 'display:flex; flex-direction:row;',
					'column'    => 'display:flex; flex-direction:column;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => '{{VALUE}}',
				),
				'prefix_class' => 'ava-smart-filter-content-position-',
			)
		);

		$this->add_responsive_control(
			'content_search_input_width',
			array(
				'label'      => esc_html__( 'Input Width', 'ava-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_line_horizontal_alignment',
			array(
				'label'   => esc_html__( 'Horizontal Alignment', 'ava-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => esc_html__( 'Justify', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'line'
				)
			)
		);

		$this->add_responsive_control(
			'content_line_vertical_alignment',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'ava-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'ava-smart-filters' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'ava-smart-filters' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'ava-smart-filters' ),
						'icon'  => 'eicon-v-align-bottom',
					),
					'stretch'   => array(
						'title' => esc_html__( 'Stretch', 'ava-smart-filters' ),
						'icon'  => 'eicon-v-align-stretch',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'line'
				)
			)
		);

		$this->add_responsive_control(
			'content_column_horizontal_alignment',
			array(
				'label'   => esc_html__( 'Horizontal Alignment', 'ava-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => esc_html__( 'Stretch', 'ava-smart-filters' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'column'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_input_style',
			array(
				'label'      => __( 'Input', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_input_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input'],
			)
		);

		$this->add_control(
			'search_input_color',
			array(
				'label'     => esc_html__( 'Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input']                             => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . '::placeholder'           => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . ':-ms-input-placeholder'  => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . '::-ms-input-placeholder' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'search_input_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'search_input_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['input'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'search_input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_input_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input'],
			)
		);

		$this->add_responsive_control(
			'search_input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before'
			)
		);

		$this->add_responsive_control(
			'search_input_margin',
			array(
				'label'      => esc_html__( 'Margin', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

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
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
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
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_icon_heading',
			array(
				'label'     => esc_html__( 'Icon', 'ava-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_position',
			array(
				'label'       => esc_html__( 'Position', 'ava-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-arrow-right',
					),
				),
				'toggle'      => true,
				'default'     => 'left',
				'condition'   => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'ava-smart-filters' ),
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
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button-icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_icon_offset',
			array(
				'label'      => esc_html__( 'Icon Offset', 'ava-smart-filters' ),
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
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .button-icon-position-right ' . $css_scheme['apply-filters-button-icon'] => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-position-left ' . $css_scheme['apply-filters-button-icon']  => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_normal_color',
			array(
				'label'     => esc_html__( 'Default Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button-icon'] => 'color: {{VALUE}}',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover > i' => 'color: {{VALUE}}',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->end_controls_section();

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

	protected function render() {

		$base_class = $this->get_name();
		$settings   = $this->get_settings();

		if ( empty( $settings['filter_id'] ) ) {
			return;
		}

		printf( '<div class="%1$s ava-filter">', $base_class );

		if ( 'reload' === $settings['apply_type'] ) {
			$apply_type = 'reload';
		} else {
			$apply_type = 'ajax-reload';
		}

		$format     = '<i class="%s"></i>';
		$icon       = $settings['apply_button_icon'] ? sprintf( $format, $settings['apply_button_icon'] ) : '';
		$query_id   = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label = ! empty( $settings['show_label'] ) ? $settings['show_label'] : false;
		$show_label = filter_var( $show_label, FILTER_VALIDATE_BOOLEAN );

		ava_smart_filters()->filter_types->render_filter_template( $this->get_widget_fiter_type(), array(
			'filter_id'            => $settings['filter_id'],
			'content_provider'     => $settings['content_provider'],
			'apply_type'           => $apply_type,
			'button_text'          => $settings['apply_button_text'],
			'button_icon'          => $icon,
			'query_id'             => $query_id,
			'button_icon_position' => $settings['filter_apply_button_icon_position'],
			'show_label'           => $show_label,
		) );

		echo '</div>';

	}

}
