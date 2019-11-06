<?php
// $filename = "./upload/ejlog.DAT";
$filename = "./upload/2052_20190919.DAT";
$bills = [];
$sum = 0;
$discount = 0;
$payments = ['card'=>0, 'cash'=>0, 'point'=>0, 'momo'=>0, 'gotit'=>0, 'return_cash'=>0, 'return_card'=>0];
$payment_methods = ['card', 'cash', 'point', 'momo', 'gotit', 'return_card', 'return_cash'];
$cashiers = [];
$c_payments;

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
		if(!isset($bill['sum'])){
			var_dump($bill['pos_number']);
		}
		$sum = $sum + ($bill['sum'] - $bill['discount']);
		$discount += $bill['discount'];

		if(in_array($bill['cashier'], $cashiers)){

		}else{
			$cashiers[] = $bill['cashier'];
			$c_payments[$bill['cashier']] = $payments;
		}

		foreach ($bill['payments'] as $p=>$payment) {
			$payments[$p] += $payment;
			$c_payments[$bill['cashier']][$p] += $payment;
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
	if(strpos($bill, '[ket thuc cashier]') !== false || strpos($bill, '[Tienchuanbi]') !== false){
		return [];
	}	
	if(strpos($bill, '[ CLOSE     ]') !== false){
		return [];
	}
	if(strpos($bill, 'dthu gd noi bo ') !== false){
		return [];
	}
	if(strpos($bill, '***Hoa don bao luu giao dich***') !== false){
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
			$date = substr($line, strpos($line, '20'), 16);
			$date = new DateTime($date);
			$datetime2 = new DateTime('2019-09-19 13:55');

			if($date < $datetime2){
				return [];
			}

			// $b['pos_number'] = $line;
			$b['pos_number'] = substr($line, -9).' - '.$date->format('H:i');
		}

		$l = count($b['product']);
		if(preg_match('/^[ 0-9]{14}/', $line) || preg_match('/^L[ 0-9]{14}/', $line)){
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
			if($tmp < 0){
				$b['payments']['return_card'] = $tmp;
			}else{
				$b['payments']['card'] = $tmp;
			}
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Tien mat        ') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			if($tmp < 0){
				var_dump($b['pos_number']);
				var_dump($tmp);
				$b['payments']['return_cash'] = $tmp;
			}else{
				$b['payments']['cash'] = $tmp;
			}
			
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Thanh toan diem') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['point'] = $tmp;
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'MOMO ') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['momo'] = $tmp;
			$b['pay'] += $tmp;
		}

		if(strpos($line, 'Got It ') !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$b['payments']['gotit'] = $tmp;
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
			$b['discount'] = abs($tmp);
		}

		if(strpos($line, ' CASHIER ') !== false){
			$tmp = substr($line, 16);
			$tmp = trim($tmp);
			$b['cashier'] = str_replace(' ', '_', $tmp);
		}

		$prev = $line;
	}
	if(!isset($b['discount'])){
		$b['discount'] = 0;
	}
	if(!isset($b['sum'])){
		return [];
	}
	return $b;
	
}
