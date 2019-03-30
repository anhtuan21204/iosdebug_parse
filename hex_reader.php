<?php
$filename = "./upload/EJLOG_20190201.DAT";
try {
	$my_file = fopen($filename, "rw"); 
	$c = 1;
	while (! feof ($my_file)) 
	{ 
		$bill = singleBill(fgets($my_file));
		if($bill === false){
			continue;
		}
		echo "BILL ".$c.'<br/>';
		$c++;
		echo $bill; 
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

