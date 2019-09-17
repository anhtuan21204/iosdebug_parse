<?php include_once('./php-barcode-generator/src/BarcodeGenerator.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorHTML.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorPNG.php'); ?>
<?php include_once('./includes/constant.php'); ?>
<?php include_once('./includes/functions.php'); ?>
<?php include_once('./includes/hex_reader.php'); ?>
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
		<table class="table table-responsive" id="iosdebug-table">
		<tbody>	
			<tr>
				<td style="font-size: 20px;"><strong> DA TIM THAY <?= count($bills); ?> BILL VOI TONG SO TIEN $<?= number_format($sum); ?> </strong></td>
			</tr>
		<?php foreach ($payments as $k=>$p): ?>
			<?php if($p == 0) continue; ?>
			<tr>
				<td style="font-size: 20px;"><strong> <?= $k; ?>: <?= number_format($p); ?> </strong></td>
			</tr>
		<?php endforeach;?>

			<tr>
				<td style="font-size: 20px;"><strong> GIAM GIA: <?= number_format($discount); ?> </strong></td>
			</tr>
			
			<?php if(count($cashiers) > 0): ?>
			<tr>
				<td>
				<h4>CHON THU NGAN:</h4>
				<select id="cashiers">
					<option value="0">-- Select --</option>
					<?php foreach ($cashiers as $k=>$c): ?>
					<option value="<?= $c ?>"><?= $c ?></option>
					<?php endforeach;?>
				</select>
				</td>
			</tr>
			<?php endif; ?>

			<?php foreach ($c_payments as $k=>$c): ?>
				<tr class="cash <?= $k ?>">
				<td>
				TIEN NOP THU NGAN : <?= $k ?>

				</td>
				</tr>
				<?php foreach ($c as $l=>$p): ?>
					<?php if($p == 0) continue; ?>
					<tr class="cash <?= $k ?>">
						<td style="font-size: 20px;"><strong> <?= $l; ?>: <?= number_format($p); ?> </strong></td>
					</tr>
				<?php endforeach;?>
			<?php endforeach;?>
		
		<?php foreach ($bills as $bill): ?>
			<tr class="cash <?= $bill['cashier'] ?>"><td>
				<table class="table table-responsive table-striped">
				<tbody>
				
					<tr <?php if(array_sum($bill['payments']) - ($bill['sum']-$bill['discount']) != 0): ?>style="background: #333<?php endif; ?>" >
						<td style="font-size: 20px;"><strong><?= $bill['pos_number'] ?></strong></td>
					</tr>

					<tr>
						<td><strong>Cashier <?= $bill['cashier'] ?></strong></td>
					</tr>	
					<tr>
						<td><strong>Tổng Bill: <?= number_format($bill['sum']) ?></strong></td>
					</tr>	
					<?php if($bill['discount']):?>
					<tr>
						<td><strong>Giảm Giá: <?= number_format($bill['discount']) ?></strong></td>
					</tr>	
					<?php endif;?>
					<?php foreach ($bill['payments'] as $p=>$payment): ?>
					<tr>
						<td><?= $p ?>:<?= number_format($payment) ?></td>
					</tr>	
					<?php endforeach; ?>	
					
					<?php foreach ($bill['product'] as $product): ?>
						<tr>
						<td><?= $product['name'] ?></td>
					</tr>
					<tr>
						<td><?= $product['price'] ?></td>
					</tr>
					<tr>
						<td><img src="data:image/png;base64, <?= base64_encode($product['barcode_image']) ?>"></td>
					</tr>	
					<?php endforeach; ?>	
					
					<?php
						if(isset($bill['cus'])):
					?>
					<tr>
						<td>MA KH: <?= $bill['cus'] ?></td>
					</tr>
					<tr>
						<td><img src="data:image/png;base64, <?= base64_encode($bill['cus_img']) ?>"></td>
					</tr>
					<?php endif; ?>
					<tr class="page-break">
						<td style="font-size: 10px;">
							<div class="page-break">...</div>
						</td>
					</tr>	
				</tbody>
				</table> 
			</td></tr>
		<?php endforeach; ?>		
		</tbody>
		</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#cashiers').change(function(){
			var cashier = $(this).val();
			if(cashier == 0){
				$('.cash').show();
			}else{
				$('.cash').hide();
				$('.'+cashier).show();
			}
		})
	})
</script>
</body>
</html>
