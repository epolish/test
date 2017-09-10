<?php

require_once PLUGIN_DIR.'/vendor/autoload.php';

class Erst_Gse_Client
{

	private $client;
	
	public function __construct()
	{
		$this->client = new Google_Client();
	}
	
	public function get_client()
	{
		return $this->check_connection()->client;
	}

	public function check_connection()
	{
		return $this->init_client()->auth_client();
	}
	
	public function set_token_from_code($code)
	{
		$this->init_client()->client->authenticate($code);
		update_option('erst_gse_oauth_token', $this->client->getAccessToken()['access_token']);
	}

	private function auth_client()
	{
		$oauth_code = esc_attr(get_option('erst_gse_oauth_token'));
		
		if ( $oauth_code ) {
			$this->client->setAccessToken($oauth_code);
		} else {
			header('Location: '.filter_var($this->client->createAuthUrl(), FILTER_SANITIZE_URL));
		}
		
		return $this;
	}

	private function init_client()
	{	
		$this->client->setAccessType('offline');
		$this->client->setIncludeGrantedScopes(true);
		$this->client->addScope(Google_Service_Sheets::SPREADSHEETS);
		$this->client->setRedirectUri(admin_url('admin.php?page='.PLUGIN_NAME.'_plugin'));
		$this->client->setClientId(esc_attr(get_option('erst_gse_client_id')));
		$this->client->setClientSecret(esc_attr(get_option('erst_gse_client_secret')));
		$this->client->setDeveloperKey(esc_attr(get_option('erst_gse_developer_key')));
		
		return $this;
	}

}
