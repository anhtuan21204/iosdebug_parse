<?php include_once('./php-barcode-generator/src/BarcodeGenerator.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorHTML.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorPNG.php'); ?>
<?php include_once('./includes/constant.php'); ?>
<?php include_once('./includes/functions.php'); ?>
<?php
if(count($_FILES) > 0){
	$uploaddir = getcwd().DIRECTORY_SEPARATOR.'upload';
	if (!file_exists($uploaddir)) {
	    mkdir($uploaddir, 0777, true);
	}
	$uploadfile = $uploaddir .DIRECTORY_SEPARATOR. time();

	if (move_uploaded_file($_FILES['iosdebug']['tmp_name'], $uploadfile)) {
		$arr = array();
		$file = $uploaddir.DIRECTORY_SEPARATOR.basename($uploadfile);
		$f = fopen($file, "r") or exit("Unable to open file!");
		// read file line by line until the end of file (feof)
		while(!feof($f))
		{

			$arr[] = fgets($f);
		}
		 
		fclose($f);
		$bills = splitDataToBill($arr);

		$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
		$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
	    echo createHTMLForReview($bills, $generatorPNG);
	} else {
		echo '<pre>';
	    echo "Possible file upload attack!\n";	
	    echo 'Here is some more debugging info:';
		print_r($_FILES);
		echo "</pre>";
	}
	return;
}
?>