<?php 
include_once 'private_constant.php';
class ParseJournal 
{
	private $store_id;
	private $date;
	private $pos_number;

	function __construct($store_id, $pos_number, $date=null){
		$this->store_id = $store_id;
		$this->pos_number = $pos_number;
		$this->date = ($date === null)?date('Ymd'):$date;
	}	

	public function getBills(){
		$url = 'http://'.JOURNAL_IP.'/bin/f_header.asp?SCH_TYPE=12&STRNO='.$this->store_id.'&TXT_STR='.$this->store_id.'&OPT_STR='.$this->store_id.'&RYEAR='.date('Y', strtotime($this->date)).'&RMONTH='.date('m', strtotime($this->date)).'&RDAY='.date('d', strtotime($this->date)).'&RDATE='.$this->date.'&TRMNO='.$this->pos_number.'&TXT_TRM='.$this->pos_number.'&OPT_TRM=&SAIJI=&TNO_S=0000&TNO_E=0999&TSHU=&TXT_TSHU=&OPT_TSHU=ZERO&TNAME=&chkbox=000000000&QRYCNT=84';
		$html = '';		
		$html = @file_get_contents($url);
		libxml_use_internal_errors(true);

		if(strlen($html) == 0){
			echo 'Can\'t connect to journal server';
			return;
		}
		
		$dom = new DOMDocument();
		$dom->loadHTML($html);
		$str = array();
		$table = $dom->getElementsByTagName("table")->item(0);

		foreach($table->getElementsByTagName('tr') as $tr)
		{
		    $tds = $tr->getElementsByTagName('td');
		    foreach ($tds as $td) {
		    	$td_content = $dom->saveHTML($td);
		    	if(strpos($td_content, 'Đăng ký bình thường') !== false){
		    		$str[] = $this->parseRow($dom->saveHTML($tr));
			    }
		    }
		    // $tds = $dom->saveHTML($tr);
		    
		}
		return $str[0];
	}

	private function parseRow($tr)
	{
		$dom = new DOMDocument();
		$dom->loadHTML($tr);
		$return = array();
		foreach($dom->getElementsByTagName('td') as $k=>$td){
			if($k == 0 || $k == 7){ continue; }
			$return[] = $dom->saveHTML($td);
		}
		return $return;
	}
}