<?php include_once('./php-barcode-generator/src/BarcodeGenerator.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorHTML.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorPNG.php'); ?>
<?php include_once('./includes/constant.php'); ?>
<?php include_once('./includes/functions.php'); ?>
<?php include_once('./includes/parse_all_log.php'); ?>
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
        *:before, *:after {
            box-sizing: border-box;
        }
        *, *:before, *:after {
            background: transparent !important;
            color: #000 !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }
        @media print {
            html, body {
                height: auto;
                margin-bottom: 0 !important;
            }
            table {
                width: 100%;
                max-width: 100%;
                margin: 1em 0;
            }
            table > thead > tr > th, table > tbody > tr > th, table > thead > tr > td, table > tbody > tr > td, table > tfoot > tr > td {
                font-size: 1em;
                padding: 0.5em 0.5em;
                line-height: 1.42857143;
                vertical-align: top;

            }
            table > thead > tr > td{
                border-top: 1px solid #ebebeb;
            }
            table > thead > tr > th {
                vertical-align: bottom;
                border-bottom: 2px solid #ebebeb;
            }
            table > tfoot > tr:first-child {
                vertical-align: bottom;
                border-top: 2px solid #ebebeb;
            }
            table > tbody > tr.border{
                border-bottom: 1px solid rgba(30, 28, 218, 0.49);
            }
            table tr.page-break{page-break-after: always;display: block;}
            .page-break{page-break-after: always;display: block;}
        }
        @page {
            margin: 0;
        }
        @media all {
		 .page-break  { display: none; }
		}
    </style>
</head>
<body>
<h1 class="text-center">Lost bill recover</h1>
<div class="container">
	<div class="row"> 
		<div class="col-xs-12">
		<table class="table table-responsive table-striped" id="iosdebug-table">
		<tbody>	
			<tr>
				<td style="font-size: 20px;"><strong> Tong cong co <?= $bill_count; ?> bill voi $<?= number_format($sum); ?> </strong></td>
			</tr>
		<?php foreach ($payments as $k=>$p): ?>
			<tr>
				<td style="font-size: 20px;"><strong> <?= $k; ?>: <?= number_format($p); ?> </strong></td>
			</tr>
		<?php endforeach;?>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td>Tính lại từ bill sau đây:</td>
			</tr>
		<?php 
			foreach ($arr_products as $products): 
				if(!isset( $products['pos_number'])) continue;
		?>
			<tr>
				<td><strong>BILL no: <?= $products['pos_number'] ?> </strong></td>
			</tr>
		<?php if(!isset($products['cashier'])): ?>
			<tr>
				<td><strong>BILL BẢO LƯU</strong></td>
			</tr>
		<?php
		 continue; endif; ?>
			<tr>
				<td><strong>CASHIER <?= $products['cashier'] ?> </strong></td>
			</tr>
		<?php if(isset($products['lines'])): ?>
			<tr>
				<td><strong> Bill name: <?= $products['file_name'] ?> </strong></td>
			</tr>
			<tr>
				<td><strong> Bill total: <?= $products['sum'] ?> </strong></td>
			</tr>

			<?php foreach ($products['pay'] as $pay): ?>
			<tr>
				<td><strong> <?= $pay[0] ?>: <?= $pay[1] ?> </strong></td>
			</tr>
			<?php endforeach; ?>

			<?php foreach ($products['lines'] as $product): ?>
			<tr>
				<td><?= $product['product_name'] ?></td>
			</tr>
			<tr>
				<td><?= $product['quantity'] ?></td>
			</tr>
			<tr>
				<td><?= $product['code'] ?></td>
			</tr>
			<tr>
				<td><img src="data:image/png;base64, <?= base64_encode($product['barcode_image']) ?>"></td>
			</tr>	
			<?php endforeach; ?>	
			<tr class="page-break">
				<td style="font-size: 10px;">
					<div class="page-break">...</div>
				</td>
			</tr>		
		<?php endif; ?>
		<?php endforeach; ?>		
		</tbody>
		</table>
		</div>
	</div>
</div>
</body>
</html>
