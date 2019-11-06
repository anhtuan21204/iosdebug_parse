<?php include_once('./php-barcode-generator/src/BarcodeGenerator.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorHTML.php'); ?>
<?php include_once('./php-barcode-generator/src/BarcodeGeneratorPNG.php'); ?>
<?php include_once('./includes/constant.php'); ?>
<?php include_once('./includes/functions.php'); ?>
<?php include_once('./includes/hex_reader.php'); ?>
<?php
if(count($_FILES) > 0){
	$uploaddir = getcwd().DIRECTORY_SEPARATOR.'upload';
	if (!file_exists($uploaddir)) {
	    mkdir($uploaddir, 0777, true);
	}
	$uploadfile = $uploaddir .DIRECTORY_SEPARATOR. time();

	if (move_uploaded_file($_FILES['iosdebug']['tmp_name'], $uploadfile)) {
		$arr = array();
		$filename = $uploaddir.DIRECTORY_SEPARATOR.basename($uploadfile);
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

		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}

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
	  
	  ?>
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
				<tr class="cash <?= $bill['cashier'] ?>">
					<td>
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
					</td>
				</tr>
			<?php endforeach; ?>		

<?php
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