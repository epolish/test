<?php

require_once 'Erst_Gse_Client.php';
require_once 'Erst_Gse_Resource.php';

class Erst_Gse_Service
{
	private $sheets;
	private $service;
	private $spreadsheet_id;
	
	public function __construct()
	{
		$this->service = new Google_Service_Sheets(
			(new Erst_Gse_Client())->get_client()
		);
		$this->spreadsheet_id = $this->get_spreadsheet_id_from_url(
			esc_attr(get_option('erst_gse_spreadsheet_url'))
		);
	}
	
	public function export()
	{
		$this->sheets = (new Erst_Gse_Resource())->get_sheet_data();
		$this->create_sheets();

		foreach ($this->sheets as $sheet) {
			$this->service->spreadsheets_values->update(
				$this->spreadsheet_id, $sheet['title'].'!A1',
				new Google_Service_Sheets_ValueRange(array(
				  'values' => $sheet['data']
				)),
				array('valueInputOption' => USER_ENTERED)
			);
		}
	}
	
	public function update()
	{
		$all_sheets_empty = true;
		$this->sheets = (new Erst_Gse_Resource())->get_sheet_data(true);

		foreach ($this->sheets as $sheet) {
			if (count($sheet['data']) > 0) {
				$all_sheets_empty = false;
				break;
			}
		}

		if (!$all_sheets_empty) {
			foreach ($this->sheets as $sheet) {
				$this->service->spreadsheets_values->append(
					$this->spreadsheet_id, $sheet['title'],
					new Google_Service_Sheets_ValueRange(array(
					  'values' => $sheet['data']
					)),
					array('valueInputOption' => USER_ENTERED)
				);
			}
		}
	}
	
	private function get_spreadsheet_id_from_url($url)
	{
		return explode('/', $url)[5];
	}

	private function create_sheets()
	{
		try {	
			foreach ($this->sheets as $sheet) {
				$this->service->spreadsheets->batchUpdate($this->spreadsheet_id, 
					new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
						'requests' => array(
							'addSheet' => array(
								'properties' => array(
									'title' => $sheet['title']
								)
							)
						)
					))
				);
			}
		} catch(Exception $ignore) {
			
		}
	}

}
