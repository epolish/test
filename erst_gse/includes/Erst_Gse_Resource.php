<?php

class Erst_Gse_Resource
{

	private $service;
	private $table_config;
	const MAX_ROW_COUNT = 9709551615;
	
	public function __construct()
	{
		$this->table_config = json_decode(get_option('erst_gse_table_settings'));
	}

	public function get_sheet_data($update = false)
	{
		global $wpdb;
		
		$sheets = [];
		$table_meta_old = $update ? json_decode(get_option('erst_gse_table_meta')) : null;
		
		foreach ($this->table_config as $key => $value) {
			$field_titles = [];
			
			if (!$table_meta_old) {
				foreach ($value->fields as $sub_key => $sub_value) {
					$field_titles[] = $sub_value;
				}
				
				$sheets[] =[
					'title' => $value->title,
					'data' 	=> array_merge([$field_titles], $wpdb->get_results(
						$this->get_select_sql($key, $value->fields), ARRAY_N
					))
				];
			} else {
				$sheets[] =[
					'title' => $value->title,
					'data' 	=> $wpdb->get_results(
						$this->get_select_sql($key, $value->fields, $table_meta_old->$key), ARRAY_N
					)
				];
			}
			
			$table_meta[$key] = $wpdb->get_results($this->get_rows_count_sql($key), ARRAY_N)[0][0];
		}
		
		update_option('erst_gse_table_meta', json_encode($table_meta));
		
		return $sheets;
	}
	
	public function get_select_sql($table_name, $fields, $start_from = null)
	{
		global $wpdb;
		
		$columns = '';
		$select = 'SELECT';
		$from = ' FROM '.$wpdb->prefix.$table_name;
		
		foreach ($fields as $key => $value) {
			$columns .= ', '.$key;
		}
		
		$columns[0] = ' ';
		$sql = $select.$columns.$from;
		
		if ($start_from) {
			$sql .= ' LIMIT '.$start_from.', '.self::MAX_ROW_COUNT;
		}
		
		return $sql;
	}
	
	public function get_rows_count_sql($table_name)
	{
		global $wpdb;

		return 'SELECT COUNT(*) FROM '.$wpdb->prefix.$table_name;
	}
	
}
