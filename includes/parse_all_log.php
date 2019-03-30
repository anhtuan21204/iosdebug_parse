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
	while(!feof($f))
	{
		$line = fgets($f);
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
		if(in_array($first_14_line, $str_sum) !== false){
			$tmp = substr(strrchr($line, ' '), 1);
			$tmp = str_replace(',', '', $tmp);
			$tmp = floatval($tmp);
			$arr_products[$k]['sum'] = $tmp;
			$sum += $tmp;
		}

		$pos = checkPaymentMethod($const_thanhtoan, $line);
		if($pos !== false){
			$p = getPayment($line, $pos);
			if(is_array($p)){
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
	fclose($f);
}