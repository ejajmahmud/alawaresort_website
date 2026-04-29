( function ( wp, config ) {
	const { createElement: h, render, useEffect, useMemo, useState } = wp.element;
	const {
		Button,
		Card,
		CardBody,
		CardHeader,
		CheckboxControl,
		ExternalLink,
		Notice,
		SelectControl,
		Spinner,
		TextControl,
		TextareaControl,
		ToggleControl,
	} = wp.components;
	const apiFetch = wp.apiFetch;

	apiFetch.use( apiFetch.createNonceMiddleware( config.nonce ) );

	const pageMap = {
		'alawa-hps': 'dashboard',
		'alawa-hps-settings': 'settings',
		'alawa-hps-mapping': 'mapping',
		'alawa-hps-inventory': 'inventory',
		'alawa-hps-reconciliation': 'reconciliation',
		'alawa-hps-retries': 'retries',
		'alawa-hps-logs': 'logs',
	};

	function api( path, options = {} ) {
		return apiFetch( {
			path: '/alawa/v1/admin' + path,
			...options,
		} );
	}

	function classNames() {
		return Array.from( arguments ).filter( Boolean ).join( ' ' );
	}

	function Shell() {
		const [ view, setView ] = useState( pageMap[ config.page ] || 'dashboard' );
		const [ overview, setOverview ] = useState( null );
		const [ notifications, setNotifications ] = useState( [] );
		const [ drawerOpen, setDrawerOpen ] = useState( false );
		const [ notice, setNotice ] = useState( null );
		const lastSeenKey = 'alawa-hps-notifications-seen-at';

		const loadOverview = () => api( '/overview' ).then( setOverview ).catch( ( error ) => setNotice( { status: 'error', message: error.message } ) );
		const loadNotifications = () => api( '/notifications' ).then( ( data ) => setNotifications( data.items || [] ) ).catch( () => null );

		useEffect( () => {
			loadOverview();
			loadNotifications();
			const interval = window.setInterval( () => {
				loadOverview();
				loadNotifications();
			}, 60000 );
			return () => window.clearInterval( interval );
		}, [] );

		const unreadCount = useMemo( () => {
			const lastSeen = window.localStorage.getItem( lastSeenKey ) || '';
			return notifications.filter( ( item ) => String( item.created_at || '' ) > lastSeen ).length;
		}, [ notifications ] );

		const toggleDrawer = () => {
			const nextOpen = ! drawerOpen;
			setDrawerOpen( nextOpen );
			if ( nextOpen ) {
				const newest = notifications[ 0 ]?.created_at;
				if ( newest ) {
					window.localStorage.setItem( lastSeenKey, String( newest ) );
				}
			}
		};

		const nav = [
			[ 'dashboard', 'Dashboard' ],
			[ 'settings', 'Settings' ],
			[ 'mapping', 'Room Mapping' ],
			[ 'inventory', 'Inventory' ],
			[ 'reconciliation', 'Reconciliation' ],
			[ 'retries', 'Retry Queue' ],
			[ 'logs', 'Logs' ],
		];

		return h(
			'div',
			{ className: 'alawa-hps' },
			h(
				'header',
				{ className: 'alawa-hps__hero' },
				h(
					'div',
					null,
					h( 'p', { className: 'alawa-hps__eyebrow' }, 'Alawa Resort Operations' ),
					h( 'h1', null, 'HostPlatform Sync' ),
					h( 'p', null, 'Inventory, webhooks, and WooCommerce reservation control in one workspace.' )
				),
				h(
					'div',
					{ className: 'alawa-hps__hero-status' },
					h( StatusPill, { label: overview?.enabled === 'yes' ? 'Enabled' : 'Disabled', tone: overview?.enabled === 'yes' ? 'good' : 'bad' } ),
					h( StatusPill, { label: ( overview?.activeMode || 'production' ) === 'staging' ? 'Staging Mode' : 'Production Mode', tone: ( overview?.activeMode || 'production' ) === 'staging' ? 'warn' : 'good' } ),
					h( StatusPill, { label: overview?.cronScheduled ? 'Cron Active' : 'Cron Missing', tone: overview?.cronScheduled ? 'good' : 'warn' } ),
					h(
						Button,
						{ className: 'alawa-hps__drawer-toggle', variant: 'secondary', onClick: toggleDrawer },
						'Notifications',
						unreadCount ? h( 'span', { className: 'alawa-hps__drawer-badge' }, unreadCount ) : null
					)
				)
			),
			notice && h( Notice, { status: notice.status || 'info', onRemove: () => setNotice( null ) }, notice.message || notice ),
			h(
				'div',
				{ className: 'alawa-hps__layout' },
				h(
					'nav',
					{ className: 'alawa-hps__nav' },
					nav.map( ( item ) =>
						h(
							'a',
							{
								key: item[ 0 ],
								className: classNames( 'alawa-hps__nav-item', view === item[ 0 ] && 'is-active' ),
								href: ( config.pageUrls && config.pageUrls[ item[ 0 ] ] ) || '#',
								onClick: ( event ) => {
									const target = ( config.pageUrls && config.pageUrls[ item[ 0 ] ] ) || '';
									if ( ! target ) {
										event.preventDefault();
										setView( item[ 0 ] );
									}
								},
							},
							item[ 1 ]
						)
					)
				),
				h(
					'main',
					{ className: 'alawa-hps__main' },
					view === 'dashboard' && h( Dashboard, { overview, refresh: loadOverview, setNotice } ),
					view === 'settings' && h( Settings, { refresh: loadOverview, setNotice } ),
					view === 'mapping' && h( Mapping, { refresh: loadOverview, setNotice } ),
					view === 'inventory' && h( Inventory ),
					view === 'reconciliation' && h( Reconciliation, { setNotice } ),
					view === 'retries' && h( RetryQueue, { refreshOverview: loadOverview, setNotice } ),
					view === 'logs' && h( Logs, { setNotice } )
				),
				h( NotificationDrawer, { open: drawerOpen, items: notifications, onClose: () => setDrawerOpen( false ), onRefresh: loadNotifications } )
			)
		);
	}

	function StatusPill( { label, tone } ) {
		return h( 'span', { className: classNames( 'alawa-hps-pill', 'is-' + tone ) }, label );
	}

	function Metric( { label, value, detail, href, onClick } ) {
		const body = h( CardBody, null, h( 'span', null, label ), h( 'strong', null, value ?? '...' ), detail && h( 'em', null, detail ) );
		return h(
			Card,
			{ className: classNames( 'alawa-hps-metric', href && 'is-clickable' ) },
			href
				? h(
					'a',
					{
						href,
						className: 'alawa-hps-metric__link',
						onClick: ( event ) => {
							if ( ! href && onClick ) {
								event.preventDefault();
								onClick();
							}
						},
					},
					body
				)
				: body
		);
	}

	function Dashboard( { overview, refresh, setNotice } ) {
		const [ busy, setBusy ] = useState( '' );
		const [ health, setHealth ] = useState( null );
		const loadHealth = () => api( '/health' ).then( setHealth ).catch( () => null );
		useEffect( () => {
			loadHealth();
		}, [] );
		const run = ( action ) => {
			setBusy( action );
			const endpoint = action === 'test' ? '/test-connection' : '/run-sync';
			api( endpoint, { method: 'POST' } )
				.then( ( data ) => {
					refresh();
					loadHealth();
					if ( action === 'test' ) {
						setNotice( { status: 'success', message: data?.message || 'Connection test completed.' } );
					} else {
						setNotice( { status: 'success', message: 'Sync complete. ' + String( data?.products ?? 0 ) + ' product(s) refreshed.' } );
					}
				} )
				.catch( ( error ) => setNotice( { status: 'error', message: error.message } ) )
				.finally( () => setBusy( '' ) );
		};

		return h(
			'div',
			null,
			h(
				'section',
				{ className: 'alawa-hps-grid alawa-hps-grid--metrics' },
				h( Metric, { label: 'Active Mode', value: overview?.activeMode === 'staging' ? 'Staging' : 'Production', detail: overview?.inventoryProfile?.isLive ? 'Live inventory profile' : 'External inventory profile', href: ( config.pageUrls && config.pageUrls.settings ) || '#' } ),
				h( Metric, { label: 'Rental Products', value: overview?.productCount, href: ( config.pageUrls && config.pageUrls.mapping ) || '#' } ),
				h( Metric, { label: 'Mapped Products', value: overview?.mappedCount, href: ( config.pageUrls && config.pageUrls.mapping ) || '#' } ),
				h( Metric, { label: 'Inventory Rows', value: overview?.inventoryCount, href: ( config.pageUrls && config.pageUrls.inventory ) || '#' } ),
				h( Metric, { label: 'Webhook Events', value: overview?.webhookCount, href: ( config.pageUrls && config.pageUrls.logs ) || '#' } ),
				h( Metric, { label: 'Warnings / Errors', value: overview?.issueCount, href: ( config.pageUrls && config.pageUrls.logs ) || '#' } ),
				h( Metric, { label: 'Retry Queue', value: overview?.retryCount, href: ( config.pageUrls && config.pageUrls.retries ) || '#' } )
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, 'Operations' ) ),
				h(
					CardBody,
					null,
					h( 'div', { className: 'alawa-hps-actions' },
						h( Button, { variant: 'primary', isBusy: busy === 'sync', disabled: !! busy, onClick: () => run( 'sync' ) }, 'Run Full Inventory Sync' ),
						h( Button, { variant: 'secondary', isBusy: busy === 'test', disabled: !! busy, onClick: () => run( 'test' ) }, 'Test API Connection' ),
						h( Button, { variant: 'tertiary', onClick: () => { refresh(); loadHealth(); } }, 'Refresh Dashboard' )
					),
					h( 'div', { className: 'alawa-hps-copybox' },
						h( 'label', null, 'Webhook URL' ),
						h( 'code', null, overview?.webhookUrl || 'Loading...' )
					),
					h( 'div', { className: 'alawa-hps-dashboard-meta' },
						h( 'p', { className: 'alawa-hps-muted' }, 'Active mode: ', h( 'strong', null, overview?.activeMode === 'staging' ? 'Staging' : 'Production' ) ),
						h( 'p', { className: 'alawa-hps-muted' }, 'Inventory profile: ', h( 'strong', null, summarizeProfile( overview?.inventoryProfile ) ) ),
						h( 'p', { className: 'alawa-hps-muted' }, 'Reservation profile: ', h( 'strong', null, summarizeProfile( overview?.reservationProfile ) ) ),
						h( 'p', { className: 'alawa-hps-muted' }, 'Use this URL in HostPlatform for New Reservation, Cancel Reservation, Update Reservation (All), and Delete Reservation.' ),
						h( 'p', { className: 'alawa-hps-muted' }, 'Last full sync: ', h( 'strong', null, overview?.lastFullSync || 'Not yet run' ) ),
						h( 'p', { className: 'alawa-hps-muted' }, 'Next cron run: ', h( 'strong', null, overview?.nextCronRun || 'Not scheduled' ) )
					)
				)
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, 'Cron Health' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-health' },
					h( HealthItem, { label: 'Last cron run', value: overview?.lastCronRun || 'Not yet recorded' } ),
					h( HealthItem, { label: 'Cron status', value: overview?.lastCronStatus || 'Unknown' } ),
					h( HealthItem, { label: 'Next cron run', value: overview?.nextCronRun || 'Not scheduled' } ),
					h( HealthItem, { label: 'Last full sync', value: overview?.lastFullSync || 'Not yet run' } ),
					h( HealthItem, { label: 'Last retry pass', value: overview?.lastRetryRun || 'Not yet run' } ),
					h( HealthItem, { label: 'Freshest cache row', value: health?.cache?.freshestInventory || 'Unknown' } ),
					h( HealthItem, { label: 'Pending retries', value: String( health?.retries?.pending ?? '0' ) } ),
					h( HealthItem, { label: 'Failed retries', value: String( health?.retries?.failed ?? '0' ) } )
				)
			)
		);
	}

	function HealthItem( { label, value } ) {
		return h(
			'div',
			{ className: 'alawa-hps-health__item' },
			h( 'span', null, label ),
			h( 'strong', null, value )
		);
	}

	function summarizeProfile( profile ) {
		if ( ! profile ) {
			return 'Not configured';
		}

		const mode = profile.mode === 'staging' ? 'Staging' : 'Production';
		const path = profile.apiNamespace || '/external/v1';
		const property = profile.propertyId ? 'property ' + profile.propertyId : 'no property';
		return mode + ' | ' + path + ' | ' + property;
	}

	function NotificationDrawer( { open, items, onClose, onRefresh } ) {
		const groups = useMemo( () => {
			return ( items || [] ).reduce( ( acc, item ) => {
				const key = String( item.created_at || '' ).slice( 0, 10 ) || 'Unknown date';
				if ( ! acc[ key ] ) {
					acc[ key ] = [];
				}
				acc[ key ].push( item );
				return acc;
			}, {} );
		}, [ items ] );

		return h(
			'aside',
			{ className: classNames( 'alawa-hps-drawer', open && 'is-open' ) },
			h(
				'div',
				{ className: 'alawa-hps-drawer__header' },
				h( 'div', null, h( 'h2', null, 'Notification Drawer' ), h( 'p', null, 'Webhook arrivals, sync activity, and admin connection events.' ) ),
				h(
					'div',
					{ className: 'alawa-hps-actions' },
					h( Button, { variant: 'secondary', onClick: onRefresh }, 'Refresh' ),
					h( Button, { variant: 'tertiary', onClick: onClose }, 'Close' )
				)
			),
			h(
				'div',
				{ className: 'alawa-hps-drawer__body' },
				! items?.length && h( 'p', { className: 'alawa-hps-muted' }, 'No notifications yet.' ),
				Object.keys( groups ).map( ( dateKey ) =>
					h(
						'section',
						{ key: dateKey, className: 'alawa-hps-drawer__group' },
						h( 'h3', null, dateKey ),
						groups[ dateKey ].map( ( item ) => h( NotificationRow, { key: item.id, item } ) )
					)
				)
			)
		);
	}

	function NotificationRow( { item } ) {
		return h(
			'div',
			{ className: classNames( 'alawa-hps-note', 'is-' + ( item.level || 'info' ) ) },
			h(
				'div',
				{ className: 'alawa-hps-note__head' },
				h( 'strong', null, item.title || 'Update' ),
				h( 'span', null, item.created_at )
			),
			h( 'p', null, item.message || '' ),
			item.meta?.note ? h( 'small', null, item.meta.note ) : null
		);
	}

	function Settings( { refresh, setNotice } ) {
		const [ settings, setSettings ] = useState( null );
		const [ saving, setSaving ] = useState( false );
		const [ saved, setSaved ] = useState( false );

		useEffect( () => {
			api( '/settings' ).then( setSettings ).catch( ( error ) => setNotice( error.message ) );
		}, [] );

		if ( ! settings ) {
			return h( Loading );
		}

		const update = ( key, value ) => setSettings( { ...settings, [ key ]: value } );
		const profileFields = [
			[ 'inventory', 'Inventory / Availability' ],
			[ 'reservation', 'Reservation Push' ],
		];
		const modes = [
			[ 'staging', 'Staging' ],
			[ 'production', 'Production' ],
		];
		const activeMode = settings.active_mode || 'production';
		const activeEntry = modes.find( ( [ modeKey ] ) => modeKey === activeMode ) || modes[ 1 ];
		const inactiveEntry = modes.find( ( [ modeKey ] ) => modeKey !== activeMode ) || modes[ 0 ];
		const renderProfileSections = ( modeKey ) => profileFields.map( ( [ purposeKey, purposeLabel ] ) => {
			const prefix = modeKey + '_' + purposeKey + '_';
			const tokenFlag = 'has_' + prefix + 'access_token';
			return h(
				'section',
				{ key: prefix, className: 'alawa-hps-profile-block' },
				h( 'h3', { className: 'alawa-hps-profile-block__title' }, purposeLabel ),
				h(
					'div',
					{ className: 'alawa-hps-form' },
					h( TextControl, { label: 'Base URL', value: settings[ prefix + 'base_url' ] || '', onChange: ( value ) => update( prefix + 'base_url', value ), placeholder: modeKey === 'staging' ? 'https://nebulapi-stg.hostastay.com' : 'https://nebulapi-asg.hostplatform.com' } ),
					h( SelectControl, { label: 'Authentication mode', value: settings[ prefix + 'auth_mode' ] || 'access_token', options: [
						{ label: 'access-token header', value: 'access_token' },
						{ label: 'Authorization Bearer', value: 'bearer' },
						{ label: 'Authorization JWT', value: 'jwt' },
					], onChange: ( value ) => update( prefix + 'auth_mode', value ) } ),
					h( TextControl, { label: 'API prefix', value: settings[ prefix + 'api_namespace' ] || '/external/v1', onChange: ( value ) => update( prefix + 'api_namespace', value ), placeholder: purposeKey === 'inventory' ? '/v1 or /external/v1' : '/external/v1 or /v1' } ),
					h( TextControl, { label: settings[ tokenFlag ] ? 'Access token (saved, paste to replace)' : 'Access token', type: 'password', value: settings[ prefix + 'access_token' ] || '', onChange: ( value ) => update( prefix + 'access_token', value ) } ),
					h( TextControl, { label: 'Property ID', value: settings[ prefix + 'property_id' ] || '', onChange: ( value ) => update( prefix + 'property_id', value ), placeholder: modeKey === 'staging' ? 'i12p1' : '6912a0a80e1df0038cc9a5b5 or i91p1' } )
				)
			);
		} );
		const save = () => {
			setSaving( true );
			setSaved( false );
			api( '/settings', { method: 'POST', data: settings } )
				.then( ( data ) => {
					setSettings( data );
					setSaved( true );
					refresh();
				} )
				.catch( ( error ) => setNotice( error.message ) )
				.finally( () => setSaving( false ) );
		};

		return h(
			'div',
			{ className: 'alawa-hps-stack' },
			saved && h( Notice, { status: 'success', onRemove: () => setSaved( false ) }, 'Settings saved.' ),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, 'Mode' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-form' },
					h( ToggleControl, { label: 'Integration enabled', checked: settings.enabled === 'yes', onChange: ( value ) => update( 'enabled', value ? 'yes' : 'no' ) } ),
					h( SelectControl, { label: 'Active mode', value: settings.active_mode || 'production', options: [
						{ label: 'Staging', value: 'staging' },
						{ label: 'Production', value: 'production' },
					], onChange: ( value ) => update( 'active_mode', value ) } ),
					h( Notice, { status: 'info', isDismissible: false }, 'The selected mode is the primary configuration used by sync, checkout checks, and reservation push.' ),
					h( TextControl, { label: 'WordPress reservation source', value: settings.default_source || '', onChange: ( value ) => update( 'default_source', value ) } )
				)
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, activeEntry[ 1 ] + ' Configuration' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-stack' },
					...renderProfileSections( activeEntry[ 0 ] )
				)
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, inactiveEntry[ 1 ] + ' Backup Configuration' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-stack' },
					h( 'p', { className: 'alawa-hps-muted' }, 'Keep this ready so you can switch environments without re-entering everything.' ),
					...renderProfileSections( inactiveEntry[ 0 ] )
				)
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, 'Webhook' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-form' },
					h( TextControl, { label: 'Webhook URL secret', value: settings.webhook_secret || '', onChange: ( value ) => update( 'webhook_secret', value ) } ),
					h( TextControl, { label: settings.has_webhook_hmac_secret ? 'Webhook HMAC secret (saved, paste to replace)' : 'Webhook HMAC secret', type: 'password', value: settings.webhook_hmac_secret || '', onChange: ( value ) => update( 'webhook_hmac_secret', value ) } ),
					h( 'div', { className: 'alawa-hps-copybox' }, h( 'label', null, 'HostPlatform URL' ), h( 'code', null, settings.webhook_url ) )
				)
			),
			h(
				Card,
				null,
				h( CardHeader, null, h( 'h2', null, 'Sync Policy' ) ),
				h(
					CardBody,
					{ className: 'alawa-hps-form alawa-hps-form--two' },
					h( TextControl, { label: 'Sync days back', type: 'number', min: 0, value: settings.sync_days_back, onChange: ( value ) => update( 'sync_days_back', Number( value ) ) } ),
					h( TextControl, { label: 'Sync days forward', type: 'number', min: 1, value: settings.sync_days_forward, onChange: ( value ) => update( 'sync_days_forward', Number( value ) ) } ),
					h( ToggleControl, { label: 'Scheduled sync', checked: settings.cron_enabled === 'yes', onChange: ( value ) => update( 'cron_enabled', value ? 'yes' : 'no' ) } ),
					h( SelectControl, { label: 'Schedule', value: settings.cron_schedule, options: [
						{ label: 'Every 5 minutes', value: 'alawa_hps_5min' },
						{ label: 'Every 15 minutes', value: 'alawa_hps_15min' },
						{ label: 'Hourly', value: 'hourly' },
						{ label: 'Twice daily', value: 'twicedaily' },
						{ label: 'Daily', value: 'daily' },
					], onChange: ( value ) => update( 'cron_schedule', value ) } ),
					h( CheckboxControl, { label: 'Live HostPlatform check during checkout', checked: settings.checkout_live_check === 'yes', onChange: ( value ) => update( 'checkout_live_check', value ? 'yes' : 'no' ) } ),
					h( CheckboxControl, { label: 'Use cache fallback if API is temporarily unavailable', checked: settings.cache_fallback === 'yes', onChange: ( value ) => update( 'cache_fallback', value ? 'yes' : 'no' ) } ),
					h( CheckboxControl, { label: 'Generate placeholder guest email if missing', checked: settings.create_guest_email === 'yes', onChange: ( value ) => update( 'create_guest_email', value ? 'yes' : 'no' ) } ),
					h( TextControl, { label: 'Log retention days', type: 'number', min: 1, value: settings.log_retention_days, onChange: ( value ) => update( 'log_retention_days', Number( value ) ) } )
				)
			),
			h( 'div', { className: 'alawa-hps-sticky-actions' }, h( Button, { variant: 'primary', isBusy: saving, disabled: saving, onClick: save }, 'Save Settings' ) )
		);
	}

	function Mapping( { refresh, setNotice } ) {
		const [ rows, setRows ] = useState( null );
		const [ saving, setSaving ] = useState( false );

		const load = () => api( '/mapping' ).then( ( data ) => setRows( data.items || [] ) ).catch( ( error ) => setNotice( error.message ) );
		useEffect( load, [] );

		if ( ! rows ) {
			return h( Loading );
		}

		const update = ( index, key, value ) => {
			const next = rows.slice();
			next[ index ] = { ...next[ index ], [ key ]: value };
			setRows( next );
		};
		const save = () => {
			setSaving( true );
			api( '/mapping', { method: 'POST', data: { items: rows } } )
				.then( ( data ) => {
					setRows( data.items || [] );
					refresh();
				} )
				.catch( ( error ) => setNotice( error.message ) )
				.finally( () => setSaving( false ) );
		};

		return h(
			Card,
			null,
			h( CardHeader, null, h( 'h2', null, 'Room Mapping' ), h( Button, { variant: 'primary', isBusy: saving, disabled: saving, onClick: save }, 'Save Mapping' ) ),
			h(
				CardBody,
				null,
				h(
					'div',
					{ className: 'alawa-hps-table-wrap' },
					h(
						'table',
						{ className: 'alawa-hps-table' },
						h( 'thead', null, h( 'tr', null, [ 'Product', 'Type', 'room_id', 'unit_id', 'Unit pool', 'Push', 'Last Cache' ].map( ( label ) => h( 'th', { key: label }, label ) ) ) ),
						h(
							'tbody',
							null,
							rows.map( ( row, index ) =>
								h( 'tr', { key: row.id },
									h( 'td', null, h( 'strong', null, row.title ), h( 'br' ), h( ExternalLink, { href: row.editUrl }, '#' + row.id ) ),
									h( 'td', null, h( SelectControl, { hideLabelFromVision: true, label: 'Listing type', value: row.listing_type, options: [ { label: 'room', value: 'room' }, { label: 'unit', value: 'unit' }, { label: 'unit pool', value: 'unit_pool' } ], onChange: ( value ) => update( index, 'listing_type', value ) } ) ),
									h( 'td', null, h( TextControl, { hideLabelFromVision: true, label: 'room_id', value: row.room_id || '', onChange: ( value ) => update( index, 'room_id', value ) } ) ),
									h( 'td', null, h( TextControl, { hideLabelFromVision: true, label: 'unit_id', value: row.unit_id || '', onChange: ( value ) => update( index, 'unit_id', value ) } ) ),
									h( 'td', null, h( TextareaControl, { hideLabelFromVision: true, label: 'Unit pool', rows: 4, value: row.unit_pool || '', onChange: ( value ) => update( index, 'unit_pool', value ) } ), row.unit_count ? h( 'small', null, row.unit_count + ' units' ) : null ),
									h( 'td', null, h( ToggleControl, { label: '', checked: row.push_enabled !== 'no', onChange: ( value ) => update( index, 'push_enabled', value ? 'yes' : 'no' ) } ) ),
									h( 'td', null, row.last_available !== '' ? h( 'span', null, row.last_available, ' available' ) : h( 'span', { className: 'alawa-hps-muted' }, 'No cache' ), row.last_sync && h( 'small', null, row.last_sync ) )
								)
							)
						)
					)
				)
			)
		);
	}

	function Inventory() {
		const [ rows, setRows ] = useState( null );
		useEffect( () => {
			api( '/inventory' ).then( ( data ) => setRows( data.items || [] ) );
		}, [] );
		if ( ! rows ) {
			return h( Loading );
		}
		return h( DataTable, { title: 'Cached Inventory', rows, columns: [
			[ 'inventory_date', 'Date' ],
			[ 'product_title', 'Product' ],
			[ 'listing_id', 'Host ID' ],
			[ 'available', 'Available' ],
			[ 'occupied', 'Occupied' ],
			[ 'booked', 'Booked' ],
			[ 'blocked', 'Blocked' ],
			[ 'synced_at', 'Synced' ],
		] } );
	}

	function Reconciliation( { setNotice } ) {
		const [ rows, setRows ] = useState( null );
		useEffect( () => {
			api( '/reconciliation' ).then( ( data ) => setRows( data.items || [] ) ).catch( ( error ) => setNotice( { status: 'error', message: error.message } ) );
		}, [] );
		if ( ! rows ) {
			return h( Loading );
		}

		return h(
			Card,
			null,
			h( CardHeader, null, h( 'h2', null, 'Reservation Reconciliation' ), h( 'span', { className: 'alawa-hps-muted' }, rows.length + ' rows' ) ),
			h(
				CardBody,
				null,
				h(
					'div',
					{ className: 'alawa-hps-table-wrap' },
					h(
						'table',
						{ className: 'alawa-hps-table' },
						h( 'thead', null, h( 'tr', null, [ 'Order', 'Product', 'Stay', 'WP Status', 'HostPlatform Code', 'Queue', 'Mapping' ].map( ( label ) => h( 'th', { key: label }, label ) ) ) ),
						h(
							'tbody',
							null,
							rows.map( ( row ) =>
								h(
									'tr',
									{ key: row.order_id + '-' + row.item_id },
									h( 'td', null, h( 'strong', null, '#' + row.order_number ), h( 'small', null, row.order_date ) ),
									h( 'td', null, row.product_title, h( 'small', null, 'Item #' + row.item_id ) ),
									h( 'td', null, [ row.check_in, row.check_out ].filter( Boolean ).join( ' -> ' ), h( 'small', null, 'Qty ' + row.quantity ) ),
									h( 'td', null, h( 'span', { className: classNames( 'alawa-hps-badge', 'is-' + ( row.sync_state === 'synced' ? 'good' : row.sync_state === 'blocked' ? 'bad' : row.sync_state === 'retry' ? 'warn' : 'warn' ) ) }, row.sync_label || row.sync_state ), h( 'small', null, row.order_status ), row.sync_detail ? h( 'small', null, row.sync_detail ) : null ),
									h( 'td', null, row.hostplatform_code || 'No reservation code yet' ),
									h( 'td', null, row.retry_status ? h( 'span', { className: classNames( 'alawa-hps-badge', 'is-' + ( row.retry_status === 'completed' ? 'good' : row.retry_status === 'failed' ? 'bad' : 'warn' ) ) }, row.retry_status ) : 'No queue item', row.retry_error ? h( 'small', null, row.retry_error ) : row.retry_next ? h( 'small', null, 'Next: ' + row.retry_next ) : null ),
									h( 'td', null, row.listing_type + ' / ' + row.listing_id, h( 'small', null, row.push_enabled === 'yes' ? 'Push enabled' : 'Push disabled' ) )
								)
							)
						)
					)
				)
			)
		);
	}

	function RetryQueue( { refreshOverview, setNotice } ) {
		const [ rows, setRows ] = useState( null );
		const [ busy, setBusy ] = useState( '' );
		const load = () => api( '/retries' ).then( ( data ) => setRows( data.items || [] ) ).catch( ( error ) => setNotice( { status: 'error', message: error.message } ) );
		useEffect( load, [] );
		if ( ! rows ) {
			return h( Loading );
		}

		const processAll = () => {
			setBusy( 'all' );
			api( '/process-retries', { method: 'POST' } )
				.then( ( data ) => {
					setNotice( { status: 'success', message: 'Processed ' + String( data.processed || 0 ) + ' retry item(s).' } );
					load();
					refreshOverview();
				} )
				.catch( ( error ) => setNotice( { status: 'error', message: error.message } ) )
				.finally( () => setBusy( '' ) );
		};

		const retryOne = ( id ) => {
			setBusy( String( id ) );
			api( '/retry/' + id, { method: 'POST' } )
				.then( () => {
					setNotice( { status: 'success', message: 'Retry completed for queue item #' + id + '.' } );
					load();
					refreshOverview();
				} )
				.catch( ( error ) => setNotice( { status: 'error', message: error.message } ) )
				.finally( () => setBusy( '' ) );
		};

		return h(
			Card,
			null,
			h( CardHeader, null, h( 'h2', null, 'Retry Queue' ), h( Button, { variant: 'primary', isBusy: busy === 'all', disabled: !! busy, onClick: processAll }, 'Process Due Retries' ) ),
			h(
				CardBody,
				null,
				h(
					'div',
					{ className: 'alawa-hps-table-wrap' },
					h(
						'table',
						{ className: 'alawa-hps-table' },
						h( 'thead', null, h( 'tr', null, [ 'Status', 'Order', 'Product', 'Attempts', 'Next Retry', 'Last Error', 'Action' ].map( ( label ) => h( 'th', { key: label }, label ) ) ) ),
						h(
							'tbody',
							null,
							rows.map( ( row ) =>
								h(
									'tr',
									{ key: row.id },
									h( 'td', null, h( 'span', { className: classNames( 'alawa-hps-badge', 'is-' + ( row.status === 'completed' ? 'good' : row.status === 'failed' ? 'bad' : 'warn' ) ) }, row.status ) ),
									h( 'td', null, row.order_id ? h( ExternalLink, { href: row.order_url }, '#' + row.order_id ) : 'N/A', h( 'small', null, 'Item #' + row.order_item_id ) ),
									h( 'td', null, row.product_title || 'Unknown', h( 'small', null, row.listing_type + ' / ' + row.listing_id ) ),
									h( 'td', null, String( row.attempts || 0 ) ),
									h( 'td', null, row.next_retry_at || 'N/A', row.last_attempt_at ? h( 'small', null, 'Last: ' + row.last_attempt_at ) : null ),
									h( 'td', null, row.last_error || '-', row.completed_at ? h( 'small', null, 'Done: ' + row.completed_at ) : null ),
									h( 'td', null, h( Button, { variant: 'secondary', isBusy: busy === String( row.id ), disabled: !! busy || row.status === 'completed', onClick: () => retryOne( row.id ) }, 'Retry now' ) )
								)
							)
						)
					)
				)
			)
		);
	}

	function Logs( { setNotice } ) {
		const [ rows, setRows ] = useState( null );
		const [ busy, setBusy ] = useState( false );
		const load = () => api( '/logs' ).then( ( data ) => setRows( data.items || [] ) ).catch( ( error ) => setNotice( { status: 'error', message: error.message } ) );
		useEffect( load, [] );
		if ( ! rows ) {
			return h( Loading );
		}
		const clear = () => {
			if ( ! window.confirm( 'Clear integration logs?' ) ) return;
			setBusy( true );
			api( '/clear-logs', { method: 'POST' } )
				.then( () => {
					setNotice( { status: 'success', message: 'Logs cleared.' } );
					load();
				} )
				.catch( ( error ) => setNotice( { status: 'error', message: error.message } ) )
				.finally( () => setBusy( false ) );
		};
		const copyLogs = async () => {
			try {
				const text = rows.map( ( row ) => {
					let output = [ row.created_at, String( row.level || '' ).toUpperCase(), row.source, row.message ].filter( Boolean ).join( ' | ' );
					if ( row.context ) {
						output += '\n' + row.context;
					}
					return output;
				} ).join( '\n\n' );
				await navigator.clipboard.writeText( text );
				setNotice( { status: 'success', message: 'Logs copied to clipboard.' } );
			} catch ( error ) {
				setNotice( { status: 'error', message: 'Could not copy logs.' } );
			}
		};
		return h(
			Card,
			null,
			h(
				CardHeader,
				null,
				h( 'h2', null, 'Logs' ),
				h(
					'div',
					{ className: 'alawa-hps-actions' },
					h( Button, { variant: 'secondary', onClick: copyLogs }, 'Copy Logs' ),
					h( Button, { variant: 'secondary', isBusy: busy, disabled: busy, onClick: clear }, 'Clear Logs' )
				)
			),
			h( CardBody, null, rows.map( ( row ) => h( LogRow, { key: row.id, row } ) ) )
		);
	}

	function LogRow( { row } ) {
		const [ open, setOpen ] = useState( false );
		return h(
			'div',
			{ className: classNames( 'alawa-hps-log', 'is-' + row.level ) },
			h( 'button', { type: 'button', onClick: () => setOpen( ! open ) },
				h( 'span', null, row.created_at ),
				h( 'strong', null, row.level.toUpperCase() ),
				h( 'em', null, row.source ),
				h( 'span', null, row.message )
			),
			open && row.context && h( TextareaControl, { label: 'Context', value: row.context, readOnly: true, rows: 8 } )
		);
	}

	function DataTable( { title, rows, columns } ) {
		return h(
			Card,
			null,
			h( CardHeader, null, h( 'h2', null, title ), h( 'span', { className: 'alawa-hps-muted' }, rows.length + ' rows' ) ),
			h( CardBody, null,
				h( 'div', { className: 'alawa-hps-table-wrap' },
					h( 'table', { className: 'alawa-hps-table' },
						h( 'thead', null, h( 'tr', null, columns.map( ( col ) => h( 'th', { key: col[ 0 ] }, col[ 1 ] ) ) ) ),
						h( 'tbody', null, rows.map( ( row, index ) => h( 'tr', { key: row.id || index }, columns.map( ( col ) => h( 'td', { key: col[ 0 ] }, String( row[ col[ 0 ] ] ?? '' ) ) ) ) ) )
					)
				)
			)
		);
	}

	function Loading() {
		return h( 'div', { className: 'alawa-hps-loading' }, h( Spinner ), h( 'span', null, 'Loading workspace...' ) );
	}

	const root = document.getElementById( 'alawa-hps-admin-root' );
	if ( root ) {
		render( h( Shell ), root );
	}
} )( window.wp, window.AlawaHPS || {} );
