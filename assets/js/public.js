/**
 * Extend default number object with format function
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.avaFormat = function( n, x, s, c ) {
	var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
		num = this.toFixed(Math.max(0, ~~n));
	return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

( function( $ ) {

	"use strict";

	var AvaSmartFilterSettings = window.AvaSmartFilterSettings || false;
	var xhr = null;

	var AvaSmartFilters = {

		currentQuery: null,
		page: false,
		controls: false,
		resetFilters: false,

		init: function() {

			var self = AvaSmartFilters;

			$( document )
				.on( 'click.AvaSmartFilters', '.apply-filters__button[data-apply-type="reload"]', self.applyFilters )
				.on( 'click.AvaSmartFilters', 'button[data-apply-type="ajax-reload"]', self.applyAjaxFilters )
				.on( 'click.AvaSmartFilters', 'button[data-apply-type="ajax"]', self.applyAjaxFilters )
				.on( 'click.AvaSmartFilters', '.ava-active-filter', self.removeFilter )
				.on( 'click.AvaSmartFilters', '.ava-remove-all-filters__button', self.removeAllFilters )
				.on( 'change.AvaSmartFilters', 'input[data-apply-type="ajax"]', self.applyAjaxFilters )
				.on( 'change.AvaSmartFilters', 'select[data-apply-type="ajax"]', self.applyAjaxFilters )
				.on( 'keypress.AvaSmartFilters', 'input.ava-search-filter__input', self.applySearchFilterOnEnter )

				.on( 'click.AvaSmartFilters', '.ava-filters-pagination__link', self.applyPagination )

				.on( 'ava-filter-add-rating-vars', self.processRating )
				.on( 'ava-filter-add-checkboxes-vars', self.processCheckbox )
				.on( 'ava-filter-add-color-image-vars', self.processCheckbox )
				.on( 'ava-filter-add-check-range-vars', self.processCheckbox )
				.on( 'ava-filter-add-range-vars', self.processRange )
				.on( 'ava-filter-add-date-range-vars', self.processRange )
				.on( 'ava-filter-add-select-vars', self.processSelect )
				.on( 'ava-filter-add-search-vars', self.processSearch )
				.on( 'ava-filter-add-radio-vars', self.processRadio )

				.on( 'ava-filter-remove-checkboxes-vars', self.removeCheckbox )
				.on( 'ava-filter-remove-color-image-vars', self.removeCheckbox )
				.on( 'ava-filter-remove-check-range-vars', self.removeCheckbox )
				.on( 'ava-filter-remove-range-vars', self.removeRange )
				.on( 'ava-filter-remove-date-range-vars', self.removeDateRange )
				.on( 'ava-filter-remove-select-vars', self.removeSelect )
				.on( 'ava-filter-remove-search-vars', self.removeSearch )
				.on( 'ava-filter-remove-radio-vars', self.removeCheckbox )
				.on( 'ava-filter-remove-rating-vars', self.removeCheckbox )

				.on( 'click.AvaSmartFilters', 'input.ava-rating-star__input', self.unselectRating)

				.on( 'ava-filter-load', self.applyLoader )
				.on( 'ava-filter-loaded', self.removeLoader )

				.on( 'ava-engine-request-calendar', self.addFiltersToCalendarRequest );

			$( window ).on( 'elementor/frontend/init', function() {

				if ( false !== AvaSmartFilterSettings && AvaSmartFilterSettings.refresh_controls ) {

					$.each( AvaSmartFilterSettings.refresh_provider, function( provider, instances ) {
						$.each( instances, function( index, queryID ) {
							setTimeout( function() {
								self.refreshControls( provider, queryID );
							} );
						});
					});
				}

			} );

		},

		addFiltersToCalendarRequest: function( event ) {
			window.AvaEngine.currentRequest.query    = AvaSmartFilters.getQuery( 'object', 'ava-engine-calendar' );
			window.AvaEngine.currentRequest.provider = 'ava-engine-calendar';
		},

		providerSelector: function( providerWrap, queryID ) {

			var delimiter = '';

			if ( providerWrap.inDepth ) {
				delimiter = ' ';
			}

			return providerWrap.idPrefix + queryID + delimiter + providerWrap.selector;

		},

		applyLoader: function ( event, $scope, AvaSmartFilters, provider, query, queryID ) {

			var providerWrap = AvaSmartFilterSettings.selectors[ provider ],
				$provider    = null;

			if ( ! queryID ) {
				queryID = 'default';
			}

			if ( 'default' === queryID ) {
				$provider = $( providerWrap.selector );
			} else {
				$provider = $( AvaSmartFilters.providerSelector( providerWrap, queryID ) );
			}

			$provider.addClass( 'ava-filters-loading' );
			$( 'div[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"], button[data-apply-provider="' + provider + '"][data-query-id="' + queryID + '"]' ).addClass( 'ava-filters-loading' );

		},

		removeLoader: function ( event, $scope, AvaSmartFilters, provider, query, queryID ) {

			var providerWrap = AvaSmartFilterSettings.selectors[ provider ],
				$provider    = null;

			if ( ! queryID ) {
				queryID = 'default';
			}

			if ( 'default' === queryID ) {
				$provider = $( providerWrap.selector );
			} else {
				$provider = $( AvaSmartFilters.providerSelector( providerWrap, queryID ) );
			}

			$provider.removeClass( 'ava-filters-loading' );
			$( 'div[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"], button[data-apply-provider="' + provider + '"][data-query-id="' + queryID + '"]' ).removeClass( 'ava-filters-loading' );

			if( $scope.hasClass('ava-filters-pagination__link') && $scope.data('apply-type') ){
				$('html, body').stop().animate({ scrollTop: $provider.offset().top }, 500);
			}

		},

		removeFilter: function() {

			var $filter    = $( this ),
				$filters   = $filter.closest( '.ava-active-filters' ),
				data       = $filter.data( 'filter' ),
				provider   = $filters.data( 'apply-provider' ),
				reloadType = $filters.data( 'apply-type' ),
				queryID = $filters.data( 'query-id' );

			$( document ).trigger(
				'ava-filter-remove-' + data.type + '-vars',
				[$filter, data, AvaSmartFilters, provider]
			);

			if ( 'rating' === data.type ) {
				AvaSmartFilters.resetRating( provider, queryID );
			}

			if ( 'ajax' !== reloadType ) {
				AvaSmartFilters.requestFilter( provider );
			}

		},

		removeAllFilters: function() {

			var $scope        = $( this ),
				provider      = $scope.data( 'apply-provider' ),
				reloadType    = $scope.data( 'apply-type' ),
				queryID       = $scope.data( 'query-id' ),
				$currentQuery = AvaSmartFilters.currentQuery;

			if ( !queryID ) {
				queryID = 'default';
			}

			if ( 'reload' === reloadType ) {
				document.location.search = AvaSmartFilters.addQueryArg(
					'ava-smart-filters',
					provider + '/' + queryID,
					null,
					'append'
				);
			} else {
				AvaSmartFilters.resetFilters = true;

				$( '.ava-filter div[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"]' ).each( function() {

					var $this          = $( this ),
						queryType      = $this.data( 'query-type' ),
						filterId       = $this.data( 'filter-id' ),
						queryVar       = $this.data( 'query-var' ),
						filterType     = $this.data( 'smart-filter' ),
						key            = '_' + queryType + '_' + queryVar;

					key = AvaSmartFilters.addQueryVarSuffix( key, $this );

					if ( 'undefined' !== typeof $currentQuery[key] ){

						var data = {
							'id' : filterId,
							'type' : filterType,
							'value' : $currentQuery[key],
							'queryVar' : queryVar
						};

						$( document ).trigger(
							'ava-filter-remove-' + filterType + '-vars',
							[ $this, data, AvaSmartFilters, provider ]
						);

						if ( 'rating' === filterType ) {
							AvaSmartFilters.resetRating( provider, queryID );
						}

					}

				} );

				AvaSmartFilters.resetFilters = false;

				AvaSmartFilters.ajaxFilters( $scope );
			}

		},

		removeRange: function( event, $scope, data, AvaSmartFilters, provider ) {

			var filterID  = data.id,
				$filter   = AvaSmartFilters.getFilterElement( filterID ),
				$slider   = $filter.find( '.ava-range__slider' ),
				$input    = $filter.find( '.ava-range__input' ),
				$min      = $filter.find( '.ava-range__values-min' ),
				$max      = $filter.find( '.ava-range__values-max' ),
				min       = $slider.data( 'min' ),
				max       = $slider.data( 'max' ),
				applyType = $input.data( 'apply-type' );

			$slider.slider( 'values', [ min, max ] );

			$min.text( min );
			$max.text( max );

			$input.val( min + ':' + max );

			if( AvaSmartFilters.resetFilters ){
				return;
			}

			if ( 'ajax' === applyType ) {
				$input.trigger( 'change.AvaSmartFilters' );
			} else if ( 'ajax-reload' === applyType ) {
				AvaSmartFilters.ajaxFilters( $input );
			}

		},

		removeDateRange: function( event, $scope, data, AvaSmartFilters, provider ) {

			var filterID = data.id,
				$filter  = AvaSmartFilters.getFilterElement( filterID ),
				$from    = $filter.find( '.ava-date-range__from' ),
				$to      = $filter.find( '.ava-date-range__to' ),
				$input   = $filter.find( '.ava-date-range__input' ),
				$submit  = $filter.find( '.ava-date-range__submit' );

			$from.val( '' );
			$to.val( '' );
			$input.val( '' );

			if( AvaSmartFilters.resetFilters ){
				return;
			}

			$submit.trigger( 'click.AvaSmartFilters' );
		},

		removeCheckbox: function( event, $scope, data, AvaSmartFilters, provider ) {

			var filterID  = data.id,
				$last     = null,
				$filter   = AvaSmartFilters.getFilterElement( filterID ),
				applyType = null;

			$filter.find( 'input:checked' ).each( function() {
				var $this = $( this );
				$this.removeAttr( 'checked' );
				$last = $this;
			});

			if( AvaSmartFilters.resetFilters ){
				return;
			}

			if ( $last ) {

				applyType = $last.data( 'apply-type' );

				if ( 'ajax' === applyType ) {
					$last.trigger( 'change.AvaSmartFilters' );
				} else if ( 'ajax-reload' === applyType ) {
					AvaSmartFilters.ajaxFilters( $last );
				}
			}

		},

		removeSelect: function( event, $scope, data, AvaSmartFilters, provider ) {

			var filterID  = data.id,
				$select   = AvaSmartFilters.getFilterElement( filterID, 'div[data-filter-id="' + filterID + '"] select' ),
				applyType = $select.data( 'apply-type' );

			$select.find( 'option:selected' ).removeAttr( 'selected' );

			if( AvaSmartFilters.resetFilters ){
				return;
			}

			if ( 'ajax' === applyType ) {
				$select.trigger( 'change.AvaSmartFilters' );
			} else if ( 'ajax-reload' === applyType ) {
				AvaSmartFilters.ajaxFilters( $select );
			}

		},

		removeSearch: function( event, $scope, data, AvaSmartFilters, provider ) {

			var filterID = data.id,
				$filter  = AvaSmartFilters.getFilterElement( filterID );

			$filter.find( 'input' ).val( '' );

			if( AvaSmartFilters.resetFilters ){
				return;
			}

			$filter.find( '.ava-search-filter__submit' ).trigger( 'click.AvaSmartFilters' );

		},

		unselectRating : function (){

			var $this = $(this);

			if ( $this.hasClass('is-checked') ){

				$this.attr('checked', false);
				$this.removeClass('is-checked');

				var applyType = $this.data( 'apply-type' );

				if ( 'ajax' === applyType ) {
					$this.trigger( 'change.AvaSmartFilters' );
				}

			} else {
				$this.siblings().removeClass('is-checked');
				$this.addClass('is-checked');
			}

		},

		resetRating: function( provider, queryID ) {

			var $rating = $( 'input.ava-rating-star__input[data-apply-provider="' + provider + '"][data-query-id="' + queryID + '"]' );

			$rating.each( function() {
				if ( $(this).hasClass('is-checked') ){
					$(this).removeClass('is-checked');
				}
			} );

		},

		getFilterElement: function( filterID, selector ) {

			if ( ! selector ) {
				selector = 'div[data-filter-id="' + filterID + '"]';
			}

			var $el = $( selector );

			if ( ! $el.length ) {

				if ( window.elementorProFrontend && window.elementorFrontend ) {

					$.each( window.elementorFrontend.documentsManager.documents, function( index, elementorDocument ) {
						if ( 'popup' === elementorDocument.$element.data( 'elementor-type' ) ) {

							var $popupEl = elementorDocument.$element.find( selector );

							if ( $popupEl.length ) {
								$el = $popupEl;
							}
						}
					});

				}
			}

			return $el;

		},

		addQueryVarSuffix: function( queryVar, $scope ) {

			var queryVarSuffix = $scope.data( 'query-var-suffix' );

			if ( queryVarSuffix ) {
				queryVar = queryVar + '|' + queryVarSuffix;
			}

			return queryVar;
		},

		processSearch: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			var val = $scope.find( 'input[type="search"]' ).val();

			if ( ! val ) {
				return;
			}

			if ( 'url' === queryType ) {

				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					queryVar,
					val,
					AvaSmartFilters.currentQuery,
					'replace'
				);

			} else {
				AvaSmartFilters.currentQuery[ queryVar ] = val;
			}

		},

		processRadio: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			var val = $scope.find( 'input:checked' ).val();

			if ( ! val ) {
				return;
			}

			if ( 'url' === queryType ) {

				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					queryVar,
					val,
					AvaSmartFilters.currentQuery,
					'replace'
				);

			} else {
				AvaSmartFilters.currentQuery[ queryVar ] = val;
			}

		},

		processRating: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			var val = $scope.find( 'input:checked' ).addClass('is-checked').val();

			if ( ! val ) {
				return;
			}

			if ( 'url' === queryType ) {

				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					queryVar,
					val,
					AvaSmartFilters.currentQuery,
					'replace'
				);

			} else {
				AvaSmartFilters.currentQuery[ queryVar ] = val;
			}

		},

		processSelect: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			var val = $scope.find( 'option:selected' ).val();

			if ( ! val ) {
				return;
			}

			if ( 'url' === queryType ) {

				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					queryVar,
					val,
					AvaSmartFilters.currentQuery,
					'replace'
				);

			} else {

				if ( AvaSmartFilters.currentQuery[ queryVar ] ) {
					AvaSmartFilters.currentQuery[ queryVar + '|multi_select' ] = [ AvaSmartFilters.currentQuery[ queryVar ] ];
					AvaSmartFilters.currentQuery[ queryVar + '|multi_select' ].push( val );
					delete AvaSmartFilters.currentQuery[ queryVar ];
				} else {
					AvaSmartFilters.currentQuery[ queryVar ] = val;
				}

			}

		},

		processRange: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			var val     = $scope.find( 'input[type="hidden"]' ).val(),
				$slider = $scope.find( '.ava-range__slider' ),
				values  = val.split( ':' );

			if ( ! values[0] && ! values[1] ) {
				return;
			}

			// Prevent of adding slider defaults
			if ( $slider.length ) {

				var min = $slider.data( 'min' ),
					max = $slider.data( 'max' );

				if ( values[0] && values[0] == min && values[1] && values[1] == max ) {
					return;
				}

			}

			if ( ! val ) {
				return;
			}

			if ( 'url' === queryType ) {

				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					queryVar,
					val,
					AvaSmartFilters.currentQuery,
					'replace'
				);

			} else {
				AvaSmartFilters.currentQuery[ queryVar ] = val;
			}

		},

		processCheckbox: function( event, $scope, queryVar, AvaSmartFilters, queryType ) {

			queryVar = AvaSmartFilters.addQueryVarSuffix( queryVar, $scope );

			if ( 'url' === queryType ) {
				queryVar = queryVar + '[]';
			}

			$scope.find( 'input:checked' ).each( function() {

				if ( 'url' === queryType ) {

					AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
						queryVar,
						$( this ).val(),
						AvaSmartFilters.currentQuery,
						'append'
					);

				} else {

					if ( AvaSmartFilters.currentQuery[ queryVar ] ) {
						AvaSmartFilters.currentQuery[ queryVar ].push( $( this ).val() );
					} else {
						AvaSmartFilters.currentQuery[ queryVar ] = [ $( this ).val() ];
					}
				}

			} );

		},

		applyPagination: function() {

			var $this      = $( this ),
				reloadType = $this.data( 'apply-type' ),
				queryID = $this.data( 'query-id' ),
				provider   = $this.data( 'apply-provider' );

			AvaSmartFilters.page     = $this.data( 'page' );
			AvaSmartFilters.controls = $this.closest( '.ava-smart-filters-pagination' ).data( 'controls' );

			if ( 'ajax' === reloadType ) {
				AvaSmartFilters.ajaxFilters( $this );
			} else {
				AvaSmartFilters.requestFilter( provider, queryID );
			}

		},

		applySearchFilterOnEnter: function( e ) {

			if ( 'keypress' === e.type && 13 === e.keyCode){
				var $this    = $( this ),
					provider = $this.data( 'apply-provider' ),
					applyType = $this.data( 'apply-type' ),
					queryID  = $this.data( 'query-id' );

				if ( 'ajax-reload' === applyType ){
					AvaSmartFilters.ajaxFilters( $this );
				} else {
					AvaSmartFilters.requestFilter( provider, queryID );
				}
			}

		},

		applyAjaxFilters: function() {

			AvaSmartFilters.ajaxFilters( $( this ) );

		},

		refreshControls: function( provider, queryID ) {

			var query  = AvaSmartFilters.getQuery( 'object', provider ),
				props  = null,
				paginationType = 'ajax',
				$pager = $( '.ava-smart-filters-pagination[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"]' );

			if ( xhr ) {
				xhr.abort();
			}

			if ( $pager.length ) {
				AvaSmartFilters.controls = $pager.data( 'controls' );
				paginationType = $pager.data( 'apply-type' );
			}

			if ( AvaSmartFilterSettings.props && AvaSmartFilterSettings.props[ provider ] && AvaSmartFilterSettings.props[ provider ][ queryID ] ) {
				props = AvaSmartFilterSettings.props[ provider ][ queryID ];
			}

			var action = 'ajax' === paginationType ? 'ava_smart_filters_refresh_controls' : 'ava_smart_filters_refresh_controls_reload';

			xhr = AvaSmartFilters.ajaxRequest(
				false,
				action,
				provider,
				query,
				props,
				queryID
			);

		},

		ajaxFilters: function( $scope ) {

			var provider = $scope.data( 'apply-provider' ),
				queryID  = $scope.data( 'query-id' ),
				props    = null,
				$pager   = $( '.ava-smart-filters-pagination[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"]' ),
				query    = {};

			if ( ! queryID ) {
				queryID = 'default';
			}

			query = AvaSmartFilters.getQuery( 'object', provider, queryID );

			if ( xhr ) {
				xhr.abort();
			}

			if ( $pager.length ) {
				AvaSmartFilters.controls = $pager.data( 'controls' );
			}

			$( document ).trigger(
				'ava-filter-load',
				[ $scope, AvaSmartFilters, provider, query, queryID ]
			);

			if ( AvaSmartFilterSettings.props && AvaSmartFilterSettings.props[ provider ] && AvaSmartFilterSettings.props[ provider ][ queryID ] ) {
				props = AvaSmartFilterSettings.props[ provider ][ queryID ];
			}

			xhr = AvaSmartFilters.ajaxRequest(
				$scope,
				'ava_smart_filters',
				provider,
				query,
				props,
				queryID
			);

		},

		ajaxRequest: function( $scope, action, provider, query, props, queryID ) {

			if ( ! queryID ) {
				queryID = 'default';
			}

			var defaults, settings, filters, controls;

			if ( AvaSmartFilterSettings.queries[ provider ] ) {
				defaults = AvaSmartFilterSettings.queries[ provider ][ queryID ];
			} else {
				defaults = {};
			}

			if ( AvaSmartFilterSettings.settings[ provider ] ) {
				settings = AvaSmartFilterSettings.settings[ provider ][ queryID ];
			} else {
				settings = {};
			}

			if ( AvaSmartFilterSettings.filters[ provider ] ) {
				filters = AvaSmartFilterSettings.filters[ provider ][ queryID ];
			} else {
				filters = {};
			}

			controls                 = AvaSmartFilters.controls;
			AvaSmartFilters.controls = false;

			$.ajax({
				url: AvaSmartFilterSettings.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: action,
					provider: provider + '/' + queryID,
					query: query,
					defaults: defaults,
					settings: settings,
					filters: filters,
					paged: AvaSmartFilters.page,
					props: props,
					controls: controls,
				},
			}).done( function( response ) {
				if ( 'ava_smart_filters' === action ) {

					AvaSmartFilters.renderResult( response, provider, queryID );

					$( document ).trigger(
						'ava-filter-loaded',
						[ $scope, AvaSmartFilters, provider, query, queryID ]
					);

				} else {
					AvaSmartFilters.renderActiveFilters( response.activeFilters, provider, queryID );
					AvaSmartFilters.renderPagination( response.pagination, provider, queryID );
				}

				AvaSmartFilters.page = false;

			});

		},

		renderResult: function( result, provider, queryID ) {

			if ( ! queryID ) {
				queryID = 'default';
			}

			var providerWrap = AvaSmartFilterSettings.selectors[ provider ],
				$scope       = null;

			if ( 'default' === queryID ) {
				$scope = $( providerWrap.selector );
			} else {
				$scope = $( AvaSmartFilters.providerSelector( providerWrap, queryID ) );
			}

			if ( 'insert' === providerWrap.action ) {
				$scope.html( result.content );
			} else {
				$scope.replaceWith( result.content );
			}

			AvaSmartFilters.triggerElementorWidgets( $scope, provider );

			$( document ).trigger(
				'ava-filter-content-rendered',
				[ $scope, AvaSmartFilters, provider, queryID ]
			);

			AvaSmartFilters.renderActiveFilters( result.activeFilters, provider, queryID );
			AvaSmartFilters.renderPagination( result.pagination, provider, queryID );

		},

		triggerElementorWidgets : function( $scope, provider ) {

			switch ( provider ) {

				case 'ava-engine':

				window.elementorFrontend.hooks.doAction(
					'frontend/element_ready/ava-listing-grid.default',
					$scope,
					$
				);

				break;

			}

			$scope.find( 'div[data-element_type]' ).each( function() {
				var $this       = $( this ),
					elementType = $this.data( 'element_type' );

				if( 'widget' === elementType ){
					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
				}
				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );

			});

		},

		renderActiveFilters: function( html, provider, queryID ) {

			if ( ! queryID ) {
				queryID = 'default';
			}

			var $activeFiltersWrap = $( 'div.ava-active-filters[data-apply-provider="' + provider + '"][data-query-id="' + queryID + '"]' );

			if ( $activeFiltersWrap.length ) {
				$activeFiltersWrap.html( html );
				$activeFiltersWrap.find( '.ava-active-filters__title' ).html(
					$activeFiltersWrap.data( 'filters-label' )
				);
			}

		},

		renderPagination: function( html, provider, queryID ) {

			if ( ! queryID ) {
				queryID = 'default';
			}

			var $paginationWrap = $( 'div.ava-smart-filters-pagination[data-apply-provider="' + provider + '"][data-query-id="' + queryID + '"]' );

			if ( $paginationWrap.length ) {
				$paginationWrap.html( html );
			}

		},

		applyFilters: function() {
			var $this    = $( this ),
				provider = $this.data( 'apply-provider' ),
				queryID  = $this.data( 'query-id' );

			AvaSmartFilters.requestFilter( provider, queryID );

		},

		requestFilter: function( provider, queryID ) {

			var query = AvaSmartFilters.getQuery( 'url', provider, queryID );

			if ( AvaSmartFilters.page ) {
				query = AvaSmartFilters.addQueryArg( 'ava_paged', AvaSmartFilters.page, query, 'append' );
			}

			document.location.search = query;

		},

		getQuery: function( type, provider, queryID ) {

			var query = null;

			if ( ! queryID ) {
				queryID = 'default';
			}

			AvaSmartFilters.currentQuery = null;

			if ( 'url' === type ) {
				AvaSmartFilters.currentQuery = AvaSmartFilters.addQueryArg(
					'ava-smart-filters',
					provider + '/' + queryID,
					null,
					'append'
				);
			} else {
				AvaSmartFilters.currentQuery = {};
			}

			$( 'div[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"]' ).each( function() {

				var $this          = $( this ),
					queryType      = $this.data( 'query-type' ),
					queryVar       = $this.data( 'query-var' ),
					filterType     = $this.data( 'smart-filter' ),
					key            = '_' + queryType + '_' + queryVar;

				$( document ).trigger(
					'ava-filter-add-' + filterType + '-vars',
					[ $this, key, AvaSmartFilters, type ]
				);

			} );

			if ( window.elementorProFrontend && window.elementorFrontend ) {

				$.each( window.elementorFrontend.documentsManager.documents, function( index, elementorDocument ) {
					if ( 'popup' === elementorDocument.$element.data( 'elementor-type' ) ) {
						elementorDocument.$element.find( 'div[data-content-provider="' + provider + '"][data-query-id="' + queryID + '"]' ).each( function() {
							var $this          = $( this ),
								queryType      = $this.data( 'query-type' ),
								queryVar       = $this.data( 'query-var' ),
								filterType     = $this.data( 'smart-filter' ),
								key            = '_' + queryType + '_' + queryVar;

							$( document ).trigger(
								'ava-filter-add-' + filterType + '-vars',
								[ $this, key, AvaSmartFilters, type ]
							);
						} );
					}
				});

			}

			query = AvaSmartFilters.currentQuery;

			return query;
		},

		addQueryArg: function( key, value, query, action ) {

			key   = encodeURI( key );
			value = encodeURI( value );

			if ( ! query ) {
				query = '';
			}

			var kvp = query.split( '&' );

			if ( 'append' === action ) {

				kvp[ kvp.length ] = [ key, value ].join( '=' );

			} else {

				var i = kvp.length;
				var x;

				while ( i-- ) {
					x = kvp[ i ].split( '=' );

					if ( x[0] == key ) {
						x[1]     = value;
						kvp[ i ] = x.join( '=' );

						break;
					}
				}

				if ( i < 0 ) {
					kvp[ kvp.length ] = [ key, value ].join( '=' );
				}

			}

			return kvp.join( '&' );
		}
	};

	var JSFEProCompat = {

		archivePostsClass: '.elementor-widget-archive-posts',
		defaultPostsClass: '.elementor-widget-posts',
		postsSettings: {},
		skin: 'archive_classic',

		init: function() {
			$( document ).on( 'ava-filter-content-rendered', function( event, $scope, AvaSmartFilters, provider ) {

				if ( 'epro-archive' === provider || 'epro-posts' === provider ) {

					var postsSelector = JSFEProCompat.defaultPostsClass,
						$archive = null,
						widgetName = 'posts',
						hasMasonry = false;

					if ( 'epro-archive' === provider ) {
						postsSelector = JSFEProCompat.archivePostsClass;
						widgetName = 'archive-posts';
					}

					$archive = $( postsSelector );

					JSFEProCompat.fitImages( $archive );

					JSFEProCompat.postsSettings = $archive.data( 'settings' );
					JSFEProCompat.skin          = $archive.data( 'element_type' );
					JSFEProCompat.skin          = JSFEProCompat.skin.split( widgetName + '.' );
					JSFEProCompat.skin          = JSFEProCompat.skin[1];

					hasMasonry = JSFEProCompat.postsSettings[ JSFEProCompat.skin + '_masonry' ];

					if ( 'yes' === hasMasonry ) {
						setTimeout( JSFEProCompat.initMasonry( $archive ) );
					}

				}

			} );
		},

		initMasonry: function( $archive ) {

			var $container = $archive.find( '.elementor-posts-container' ),
				$posts     = $container.find( '.elementor-post' ),
				settings   = JSFEProCompat.postsSettings,
				colsCount  = 1,
				hasMasonry = true;

			$posts.css({
				marginTop: '',
				transitionDuration: ''
			});

			var currentDeviceMode = window.elementorFrontend.getCurrentDeviceMode();

			switch ( currentDeviceMode ) {
				case 'mobile':
					colsCount = settings[ JSFEProCompat.skin + '_columns_mobile' ];
					break;
				case 'tablet':
					colsCount = settings[ JSFEProCompat.skin + '_columns_tablet' ];
					break;
				default:
					colsCount = settings[ JSFEProCompat.skin + '_columns' ];
			}

			hasMasonry = colsCount >= 2;

			$container.toggleClass( 'elementor-posts-masonry', hasMasonry );

			if ( ! hasMasonry ) {
				$container.height('');
				return;
			}

			var verticalSpaceBetween = settings[ JSFEProCompat.skin + '_row_gap' ]['size'];

			if ( ! verticalSpaceBetween ) {
				verticalSpaceBetween = settings[ JSFEProCompat.skin + '_item_gap' ]['size'];
			}

			var masonry = new elementorModules.utils.Masonry({
				container: $container,
				items: $posts.filter( ':visible' ),
				columnsCount: colsCount,
				verticalSpaceBetween: verticalSpaceBetween
			});

			masonry.run();
		},

		fitImage: function( $post ) {
			var $imageParent = $post.find( '.elementor-post__thumbnail' ),
				$image       = $imageParent.find( 'img' ),
				image        = $image[0];

			if ( ! image ) {
				return;
			}

			var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
				imageRatio       = image.naturalHeight / image.naturalWidth;

			$imageParent.toggleClass( 'elementor-fit-height', imageRatio < imageParentRatio );
		},

		fitImages: function( $scope ) {
			var $element  = $scope,
				itemRatio = getComputedStyle( $element[0], ':after' ).content;

			$element.find( '.elementor-posts-container' ).toggleClass( 'elementor-has-item-ratio', !!itemRatio.match(/\d/) );

			$element.find( '.elementor-post' ).each( function () {
				var $post = $(this),
					$image = $post.find( '.elementor-post__thumbnail img' );

				JSFEProCompat.fitImage($post);

				$image.on( 'load', function () {
					JSFEProCompat.fitImage( $post );
				});
			} );
		},
	};

	JSFEProCompat.init();

	var AvaSmartFiltersUI = {

		init: function() {

			var widgets = {
				'ava-smart-filters-range.default' : AvaSmartFiltersUI.range,
				'ava-smart-filters-date-range.default' : AvaSmartFiltersUI.dateRange
			};

			$.each( widgets, function( widget, callback ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});
		},

		range: function( $scope ) {

			var $slider = $scope.find( '.ava-range__slider' ),
				$input  = $scope.find( '.ava-range__input' ),
				$min    = $scope.find( '.ava-range__values-min' ),
				$max    = $scope.find( '.ava-range__values-max' ),
				format  = $slider.data( 'format' ),
				slider;

			if ( ! format ) {
				format = {
					'thousands_sep' : '',
					'decimal_sep' : '',
					'decimal_num' : 0,
				};
			}

			slider = $slider.slider({
				range: true,
				min: $slider.data( 'min' ),
				max: $slider.data( 'max' ),
				step: $slider.data( 'step' ),
				values: $slider.data( 'defaults' ),
				slide: function( event, ui ) {
					$input.val( ui.values[ 0 ] + ':' + ui.values[ 1 ] );

					$min.html( ui.values[ 0 ].avaFormat(
						format.decimal_num,
						3,
						format.thousands_sep,
						format.decimal_sep
					) );

					$max.html( ui.values[ 1 ].avaFormat(
						format.decimal_num,
						3,
						format.thousands_sep,
						format.decimal_sep
					) );

				},
				stop: function( event, ui ) {
					$input.trigger( 'change' );
				},
			});

		},

		dateRange: function( $scope ) {
			var $id = $scope.data('id'),
				$from  = $scope.find( '.ava-date-range__from' ),
				$to    = $scope.find( '.ava-date-range__to' ),
				$input = $scope.find( '.ava-date-range__input' ),
				from,
				$texts = AvaSmartFilterSettings.datePickerData,
				to;

			from = $from.datepicker({
				defaultDate: '+1w',
				closeText: $texts.closeText,
				prevText: $texts.prevText,
				nextText: $texts.nextText,
				currentText: $texts.currentText,
				monthNames: $texts.monthNames,
				monthNamesShort: $texts.monthNamesShort,
				dayNames: $texts.dayNames,
				dayNamesShort: $texts.dayNamesShort,
				dayNamesMin: $texts.dayNamesMin,
				weekHeader: $texts.weekHeader,
				beforeShow: function (textbox, instance) {
					var $calendar = instance.dpDiv;
					$calendar.addClass('ava-smart-filters-datepicker-' + $id );
				},
				onClose: function (textbox, instance){
					var $calendar = instance.dpDiv;
					$calendar.removeClass('ava-smart-filters-datepicker-' + $id );
				}
			}).on( 'change', function() {
				to.datepicker( 'option', 'minDate', AvaSmartFiltersUI.getDate( this ) );
				$input.val( $from.val() + ':' + $to.val() );
			});

			to = $to.datepicker({
				defaultDate: '+1w',
				closeText: $texts.closeText,
				prevText: $texts.prevText,
				nextText: $texts.nextText,
				currentText: $texts.currentText,
				monthNames: $texts.monthNames,
				monthNamesShort: $texts.monthNamesShort,
				dayNames: $texts.dayNames,
				dayNamesShort: $texts.dayNamesShort,
				dayNamesMin: $texts.dayNamesMin,
				weekHeader: $texts.weekHeader,
				beforeShow: function (textbox, instance) {
					var $calendar = instance.dpDiv;
					$calendar.addClass('ava-smart-filters-datepicker-' + $id );
				},
				onClose: function (textbox, instance){
					var $calendar = instance.dpDiv;
					$calendar.removeClass('ava-smart-filters-datepicker-' + $id );
				}
			}).on( 'change', function() {
				from.datepicker( 'option', 'maxDate', AvaSmartFiltersUI.getDate( this ) );
				$input.val( $from.val() + ':' + $to.val() );
			});
		},

		getDate: function( element ) {

			var dateFormat = 'mm/dd/yy',
				date;

			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch ( error ) {
				date = null;
			}

			return date;
		}

	};

	AvaSmartFilters.init();

	$( window ).on( 'elementor/frontend/init', AvaSmartFiltersUI.init );

	window.AvaSmartFilters = AvaSmartFilters;

}( jQuery ) );
