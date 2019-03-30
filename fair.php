<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>iOS Debug read file</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<link rel="stylesheet" href="./js/dropzone.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="./js/dropzone.js"></script>
	<style type="text/css">
		.page-break{page-break-before: always;}
	</style>
</head>
<body>
<?php 
// include_once './includes/ParseJournal.php';
// $journal = new ParseJournal('01013', '0006', '20170919');
// print_r($journal->getBills()) ;
?>
<h1 class="text-center">iOS debug parse</h1>
<div class="container">
	<div class="row"> 
		<div class="col-xs-12">
			<form action="./fair_upload.php" class="dropzone" id="my-awesome-dropzone"></form>
		</div>
	</div>
</div>
<hr>
<div class="container">
	<div class="row"> 
		<div class="col-xs-12">
			<table class="table table-responsive table-striped" id="iosdebug-table" style="width:100%; max-width:100%">
			<thead>
			<tr style="width:100%; max-width:100%">
				<th>Command</th>
				<th>Barcode</th>
			</tr>
			</thead>
			<tbody style="width:100%; max-width:100%">
			
			</tbody>
		</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	Dropzone.autoDiscover = false;
	jQuery(function ($) {
		var $table = $('#iosdebug-table tbody');
		var myDropzone = new Dropzone('.dropzone',{ 
			paramName: "iosdebug",
			acceptedFiles: '.log',
			init: function() {
				this.on("success", function(file, responseText) {
				  	myDropzone.removeFile(file);
			  		$table.empty();
			  		$table.append(responseText);
				});
			}
		});

	});
	
</script>
</body>
</html>
