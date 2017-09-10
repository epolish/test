<?php
/*
Plugin Name: Google Spreadsheet Export for Studioschastie.com
Plugin URI: https://studioschastie.com.ua/
Description: Adds admin section for managing google spreadsheet export
Version: 1.0
Author: NUCLEO Inc
Author URI: http://design.nucleo.com.ua/
*/

define( 'PLUGIN_NAME', 'erst_gse' );
define( 'ADMIN_TITLE', __( 'Google Spreadsheet Export' ) );
define( 'PLUGIN_DIR', dirname( __FILE__ ).DIRECTORY_SEPARATOR  ); 

function erst_gse_template() {
	require_once PLUGIN_DIR.'html/form.php';

	wp_enqueue_style( 'jsoneditor', plugins_url( '/css/vendor/jsoneditor.min.css', __FILE__ ) );
	wp_enqueue_script( 'jsoneditor', plugins_url( '/js/vendor/jsoneditor.min.js', __FILE__ ) );	
};

function plugin_setup_menu() {
	add_menu_page(
		ADMIN_TITLE,
		ADMIN_TITLE,
		'manage_options',
		PLUGIN_NAME.'_plugin',
		PLUGIN_NAME.'_template'
	);
};

add_action( 'admin_menu', 'plugin_setup_menu' );

function erst_gse_init() {
	require_once PLUGIN_DIR.'includes/Erst_Gse_Service.php';
	
	delete_transient( 'error' );
	delete_transient( 'success' );
	$client = new Erst_Gse_Client();

	try {
		if ( isset( $_GET['code'] ) ) {
			$client->set_token_from_code( 
				esc_attr( $_GET['code'] ) 
			);
			( new Erst_Gse_Service() )->export();
			set_transient( 'success', __( 'Completed successfully' ) );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
			$client_id = $_POST[ 'client_id' ];
			$client_secret = $_POST[ 'client_secret' ];
			$developer_key = $_POST[ 'developer_key' ];
			$spreadsheet_url = $_POST[ 'spreadsheet_url' ];
			$cron_time = $_POST[ 'cron_time' ];
			$table_settings = $_POST[ 'table_settings' ];
			
			if ( 
				isset( $client_id ) && isset( $client_secret ) && isset( $developer_key )
				&& isset( $spreadsheet_url ) && isset( $cron_time ) && isset( $table_settings )
			) {
				delete_option( 'erst_gse_oauth_token' );
				update_option( 'erst_gse_client_id', esc_attr( $client_id ) );
				update_option( 'erst_gse_client_secret', esc_attr( $client_secret ) );
				update_option( 'erst_gse_developer_key', esc_attr( $developer_key ) );
				update_option( 'erst_gse_spreadsheet_url', esc_attr( $spreadsheet_url ) );
				update_option( 'erst_gse_cron_time', esc_attr( $cron_time ) );
				update_option( 'erst_gse_table_settings', stripslashes( $table_settings ) );
				
				#*/5 * * * * curl http://example.com/check/
				/*
				$output = shell_exec('crontab -l');
				file_put_contents('/tmp/crontab.txt', $output.'* * * * * NEW_CRON'.PHP_EOL);
				echo exec('crontab /tmp/crontab.txt');
				*/
				
				$client->check_connection();

				set_transient( 'success', __( 'Completed successfully' ) );
			} else {
				set_transient( 'error', __( 'Не все поля заполнены' ) );
			}
		}		
	} catch ( Google_Service_Exception $ex ) {
		set_transient( 'error', $ex->getCode() );
	} catch ( Exception $ex ) {
		set_transient( 'error', $ex->getMessage() );
	}

	add_rewrite_rule(
		'^'.PLUGIN_NAME.'_cron/(\w)?',
		'index.php?'.PLUGIN_NAME.'_cron_action=$matches[1]',
		'top'
	  );
};

add_action( 'init', PLUGIN_NAME.'_init' );

function erst_gse_query_vars( $vars ) {
	$vars[] = PLUGIN_NAME.'_cron_action';

	return $vars;
}

add_filter( 'query_vars', PLUGIN_NAME.'_query_vars' );

function erst_gse_requests ( $wp ) { 

	$valid_actions = array('r');

	if ( !empty( $wp->query_vars[PLUGIN_NAME.'_cron_action'] ) &&
		in_array( $wp->query_vars[PLUGIN_NAME.'_cron_action'], $valid_actions )
	) {
		require_once PLUGIN_DIR.'includes/Erst_Gse_Service.php';
		
		try {	
			( new Erst_Gse_Service() )->update();
		} catch ( Google_Service_Exception $ex ) {
			$error_message = $ex->getCode();
		} catch ( Exception $ex ) {
			$error_message = $ex->getMessage();
		}
	}
}

add_action( 'parse_request', PLUGIN_NAME.'_requests' );
