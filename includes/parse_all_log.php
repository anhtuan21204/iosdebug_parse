<?php
$uploaddir = getcwd().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'lost';
if (!file_exists($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}
$files = array_diff(scandir($uploaddir), array('.', '..'));
$max_char_in_line = 46;
$begin_product_line = '[NOR] '; // bat dau mot dong san pham
// $begin_line = '[NOR]L'; // bill khong len, bi off
$str_sum = ['[NOR]Tong cong', '[DHE]Tong cong'];
$const_thanhtoan = ['[NOR]Thanh toan diem', '[NOR]Tien mat', '[NOR]CREDITCARD', '[NOR]Giam gia'];
$arr_products = [];
$sum = 0; $bill_count = 0; $payments = [];
foreach ($files as $k=>$file_name) {
	$file = $uploaddir.DIRECTORY_SEPARATOR.$file_name;
	$f = fopen($file, "r") or exit("Unable to open file!");
	// read file line by line until the end of file (feof)
	$prev_line = '';
	$bill_count++;
	$arr_products[$k]['file_name'] = $file_name;
	$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
	$card = 0;
	$skip_bill = 0;
	while(!feof($f))
	{
		$line = fgets($f);
		if($skip_bill == 1){
			continue;
		}
		// Bill bao luu - bo qua
		if(strpos($line, '[DHE]  So bao luu') !== false){
			$skip_bill = 1;
		}
		// Bill cho khach ky ten - Bo qua
		if(strpos($line, '[NOR] [ SIGN ] ') !== false){
			$skip_bill = 1;
		}
		$line_start = substr($line, 0, 6);
		if(strpos($line, $begin_product_line) !== false){
			$code = substr($line, 6, 13);
			if(is_numeric($code)){
				$barcode = $generatorPNG->getBarcode($code, $generatorPNG::TYPE_CODE_128);		
				$arr_products[$k]['lines'][] = [
					'product_name'=>$prev_line,
					'quantity'=>$line,
					'code'=>$code,
					'barcode_image'=>$barcode
				];
			}
		}	
		$prev_line = $line;	

		$first_14_line = substr($line, 0, 14);

		if(preg_match('/POS:[0-9]{4}-[0-9]{4}/', $line)){
			$date = substr($line, strpos($line, '20'), 16);
			$date = new DateTime($date);
			$datetime2 = new DateTime('2019-01-01 08:00');

			if($date < $datetime2){
				return [];
			}

			$arr_products[$k]['pos_number'] = substr($line, -11).' - '.$date->format('H:i');
		}

		if(strpos($line, '[LOG]CASHIER') !== false){
			$arr_products[$k]['cashier'] = substr($line, strpos($line, ':'));

		}

		if(in_array($first_14_line, $str_sum) !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$arr_products[$k]['sum'] = $tmp;
			$sum += $tmp;
		}

		if($card == 1 && strpos($line, '[NOR]So tien yeu cau') !== false){
			$p[0] = '[NOR]CREDITCARD';
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$p[1] = $tmp;

			$arr_products[$k]['pay'][] = $p;
			if(isset($payments[$p[0]])){
				$payments[$p[0]] += floatval($tmp);
			}else{				
				$payments[$p[0]] = floatval($tmp);
			}
		}
		$pos = checkPaymentMethod($const_thanhtoan, $line);
		if($pos !== false){
			$p = getPayment($line, $pos);
			if(is_array($p)){
				if($p[0] == '[NOR]CREDITCARD'){
					$card = 1;
				}else{
					$arr_products[$k]['pay'][] = $p;
					$i = str_replace(',', '', $p[1]);
					if(isset($payments[$p[0]])){
						$payments[$p[0]] += floatval($i);
					}else{				
						$payments[$p[0]] = floatval($i);
					}
				}
			}
		}
	}
	fclose($f);
}