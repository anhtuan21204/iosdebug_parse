<?php
$fil = fopen ("./upload/EJLOG_20190322.dat", "r");
$quotes=array();
while ($linje = fgets($fil))
{
	$linje = preg_replace('/[\x00-\x1F\x7F]/', '', $linje);
	var_dump($linje);
$quotes[] = unpack('a*',$linje) ;
}
//var_dump($quotes);
	 
foreach ($quotes as $key => $value) {
//	var_dump(hex2bin($value[1]))	;
}
