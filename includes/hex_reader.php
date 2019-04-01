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
		if(isset($bill['discount'])){
			$sum += $bill['sum'] - $bill['discount'];
		}else{
			$sum += $bill['sum'];
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
	if(strpos($bill, '[ket thuc cashier]') !== false){
		return [];
	}	
	if(strpos($bill, '[ CLOSE     ]') !== false){
		return [];
	}

	$card = 0;
	if(strpos($bill, 'Cong ty minhTra gop') !== false){
		$bill = substr($bill, strpos($bill, 'MANAGER', 5));
		$card = 1;
	}
	$lines = explode("<br/>", $bill);
	$b = ['product'=>[], 'pay'=>0, 'payments'=>[]];
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

		if(preg_match('/^hv  :[0-9]{16}/', $line)){
			$b['cus'] = substr($line, 5, 16);
			if(is_numeric($b['cus'])){
				$barcode = $generatorPNG->getBarcode($b['cus'], $generatorPNG::TYPE_CODE_128);	
				$b['cus_img'] = $barcode;	
			}
		}

		if($card == 1 && strpos($line, 'So tien yeu cau') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['card'] = $tmp;
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Tien mat        ') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['cash'] = $tmp;
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Thanh toan diem') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['point'] = $tmp;
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Tong cong') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['sum'] = $tmp;
		}

		if(strpos($line, 'Giam gia     ') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['discount'] = $tmp;
		}

		$prev = $line;
	}
	return $b;
	
}