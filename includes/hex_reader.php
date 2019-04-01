<?php
$filename = "./upload/EJLOG_20190322.DAT";
$bills = [];
$sum =0;
$payments = [];
try {
	if ( !file_exists($filename) ) {
    throw new Exception('File not found.');
  }	
	$my_file = fopen($filename, "rw"); 
	while (! feof ($my_file)) 
	{ 
		$bill = singleBill(fgets($my_file));
		if($bill === false){
			continue;
		}
		$bill = parseSingleBill($bill);
		if(count($bill) == 0){
			continue;
		}
		$bills[] = $bill;
	}	 
	  
	// file is closed using fclose() function 
	fclose($my_file); 
} catch (Exception $e) {
	echo $e;
}


function singleBill($line)
{
	if(strpos($line, "MANAGER") === false){
		return false;
	}
	$line = substr($line, strpos($line, "MANAGER"));
	$line = str_replace("+".chr(3)."z", '<br/>', $line);
	$line = str_replace(chr(4)."z", '<br/>', $line);
	$line = str_replace(chr(8)."h", '<br/>', $line);
	return $line.'<br/>'.'<br/>';
}


function parseSingleBill($bill){
	if(strpos($bill, 'CASHIER') === false){
		return [];
	}
	if(strpos($bill, 'Cong ty minhTra gop') !== false){
		$bill = substr($bill, strpos($bill, 'MANAGER', 5));
	}
	$lines = explode("<br/>", $bill);
	$b = ['product'=>[]];
	$prev = '';
	$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
	foreach ($lines as $line) {

		//get pos no
		if(preg_match('/POS:[0-9]{4}-[0-9]{4}/', $line)){
			$b['pos_number'] = substr($line, -9);
		}
		$l = count($b['product']);
		if(preg_match('/^[ 0-9]{14}/', $line)){
			$code = substr($line, 1, 13);
			if(is_numeric($code)){
				$b['product'][$l]['name'] = $prev;
				$b['product'][$l]['price'] = $line;
				
				$barcode = $generatorPNG->getBarcode($code, $generatorPNG::TYPE_CODE_128);	
				$b['product'][$l]['barcode_image'] = $barcode;	
			}
		}
// $b['product'][] = $line;
		$prev = $line;
	}
	return $b;
	
}