<?php

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ava_Smart_Filters_Pagination_Widget extends Widget_Base {

	public function get_name() {
		return 'ava-smart-filters-pagination';
	}

	public function get_title() {
		return __( 'Pagination', 'ava-smart-filters' );
	}

	public function get_icon() {
		return 'ava-smart-filters-pagination';
	}

	public function get_help_url() {
		return ava_smart_filters()->widgets->prepare_help_url(
			'https://blockcroco.com/knowledge-base/articles/avasmartfilters-how-to-use-ajax-pagination/',
			$this->get_name()
		);
	}

	public function get_categories() {
		return array( ava_smart_filters()->widgets->get_category() );
	}

	protected function _register_controls() {

		$css_scheme = apply_filters(
			'ava-smart-filters/widgets/pagination/css-scheme',
			array(
				'pagination'              => '.ava-filters-pagination',
				'pagination-item'         => '.ava-filters-pagination__item',
				'pagination-link'         => '.ava-filters-pagination__link',
				'pagination-link-current' => '.ava-filters-pagination__link.ava-filters-pagination__link-current',
				'pagination-dots'         => '.ava-filters-pagination__dots',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'Pagination for:', 'ava-smart-filters' ),
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
			'section_controls',
			array(
				'label' => __( 'Controls', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'enable_prev_next',
			array(
				'label'        => esc_html__( 'Enable Prev/Next buttons', 'ava-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'ava-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'ava-smart-filters' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_text',
			array(
				'label'   => esc_html__( 'Prev Text', 'ava-smart-filters' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Prev', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'next_text',
			array(
				'label'   => esc_html__( 'Next Text', 'ava-smart-filters' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Next', 'ava-smart-filters' ),
			)
		);

		$this->add_control(
			'pages_center_offset',
			array(
				'label'   => esc_html__( 'Items center offset', 'ava-smart-filters' ),
				'description'   => esc_html__( 'Set number of items to either side of current page, not including current page.Set 0 to show all items.', 'ava-smart-filters' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 50,
				'step'    => 1,
			)
		);

		$this->add_control(
			'pages_end_offset',
			array(
				'label'   => esc_html__( 'Items edge offset', 'ava-smart-filters' ),
				'description'   => esc_html__( 'Set number of items on either the start and the end list edges.', 'ava-smart-filters' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 50,
				'step'    => 1,
			)
		);

		$this->end_controls_section();

		$this->controls_section_pagination( $css_scheme );

	}

	protected function controls_section_pagination( $css_scheme ) {

		$this->start_controls_section(
			'pagination_style',
			array(
				'label'      => esc_html__( 'Pagination', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);
		$this->add_control(
			'pagination_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);
		$this->add_control(
			'pagination_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pagination_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);
		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => esc_html__( 'Padding', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['pagination'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'pagination_margin',
			array(
				'label'      => esc_html__( 'Margin', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pagination_items_style',
			array(
				'label'      => esc_html__( 'Items', 'ava-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_items_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);
		$this->start_controls_tabs( 'tabs_pagination_items_style' );
		$this->start_controls_tab(
			'pagination_items_normal',
			array(
				'label' => esc_html__( 'Normal', 'ava-smart-filters' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_hover',
			array(
				'label' => esc_html__( 'Hover', 'ava-smart-filters' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_active',
			array(
				'label' => esc_html__( 'Current', 'ava-smart-filters' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_dots',
			array(
				'label' => esc_html__( 'Dots', 'ava-smart-filters' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_dots',
			array(
				'label'     => esc_html__( 'Background Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_dots',
			array(
				'label'     => esc_html__( 'Text Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_dots_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'ava-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'pagination_items_padding',
			array(
				'label'      => esc_html__( 'Padding', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'pagination_items_horizontal_gap',
			array(
				'label'       => esc_html__( 'Horizontal Gap Between Items', 'ava-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 4,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] . '+' . $css_scheme['pagination-item'] => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_vertical_gap',
			array(
				'label'       => esc_html__( 'Vertical Gap Between Items', 'ava-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 4,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_items_border',
				'label'       => esc_html__( 'Border', 'ava-smart-filters' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);
		$this->add_responsive_control(
			'pagination_items_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'ava-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'pagination_items_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'ava-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'ava-smart-filters' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 *
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	/**
	 * Render pagination sample
	 *
	 * @return [type] [description]
	 */
	public function render_pagination_sample( $controls ) {

		$provider   = 'test';
		$apply_type = 'ajax';
		$query_id   = 'default';
		$props      = array(
			'found_posts'   => 30,
			'max_num_pages' => 10,
			'page'          => 4,
		);

		include ava_smart_filters()->get_template( 'common/pagination.php' );

	}

	protected function render() {

		$base_class       = $this->get_name();
		$settings         = $this->get_settings();
		$content_provider = $settings['content_provider'];
		$apply_type       = $settings['apply_type'];
		$query_id         = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$controls_enabled = isset( $settings['enable_prev_next'] ) ? $settings['enable_prev_next'] : '';

		if ( 'yes' === $controls_enabled ) {

			$controls = array(
				'nav'  => true,
				'prev' => $settings['prev_text'],
				'next' => $settings['next_text'],
			);

		} else {
			$controls['nav'] = false;
		}

		$controls['pages_mid_size']  = ! empty( $settings['pages_center_offset'] ) ? absint( $settings['pages_center_offset'] ) : 0;
		$controls['pages_end_size']  =  ! empty( $settings['pages_end_offset'] ) ? absint( $settings['pages_end_offset'] ) : 0;

		printf(
			'<div
				class="%1$s"
				data-apply-provider="%2$s"
				data-content-provider="%2$s"
				data-query-id="%3$s"
				data-controls="%4$s"
				data-apply-type="%5$s"
			>',
			$base_class,
			$content_provider,
			$query_id,
			htmlspecialchars( json_encode( $controls ) ),
			$apply_type
		);

		$plugin = Plugin::instance();

		if ( $plugin->editor->is_edit_mode() ) {
			$this->render_pagination_sample( $controls );
		} else {

			echo ava_smart_filters()->render->render_pagination(
				$content_provider, $apply_type, $query_id, $controls
			);
		}

		echo '</div>';

	}

}
