<?php
function splitDataToBill($arr){
		$bills = array();
		$temp = 3;
		foreach ($arr as $key => $value) {
			$time = mb_substr($value, 0, TIME_LENGTH);
			$device = removeExtraSpace(mb_substr($value, TIME_LENGTH, READER_DEVICE_LENGTH));
			$command = removeExtraSpace(mb_substr($value, TIME_LENGTH+READER_DEVICE_LENGTH, COMMAND_READ_LENGTH));
			$content_length = removeExtraSpace(mb_substr($value, TIME_LENGTH+READER_DEVICE_LENGTH+COMMAND_READ_LENGTH, CONTENT_READ_LENGTH));
			$content_length = intval($content_length);
			$content = '';
			if( $content_length > 0){
				$content = substr(removeLineBreak($value), -$content_length);
			}

			$bill[] = array(
				'time' => $time,
				'device' => $device,
				'command' => $command,
				'content' => $content
			);
			if( isCashierLogined($command, $content) ) {
				$bills[] = $bill;		
				$bill = array();		
			}elseif( isCashKeyPressed($command) ) {
				$temp = 0;
			}elseif( $temp == 2 ) {
				$bills[] = $bill;		
				$bill = array();		
			}
			$temp++;
		}
		return $bills;
	}

	function removeExtraSpace($str) {
		return ltrim(rtrim(preg_replace('/\s+/', ' ', $str)));
	}

	function removeLineBreak($str) {
		return preg_replace( "/\r|\n/", "", $str );
	}
	
	function isCashierLogined($command, $content) {
		if($command == ENTER_KEY && strlen($content) == 4){ // thu ngan dang nhap
			return true;
		}
		return false;
	}

	function isCashKeyPressed($command) {
		if($command == CASH_KEY || $command == CARD_KEY || $command == GIFT_KEY) {
			return true;
		}
		return false;
	}

	function createHTMLForReview($bills, $generatorPNG) {
		foreach ($bills as $key => $bill) :
			echo '<tr><td colspan="5">Bill no: '.$key.'</td></tr>';
			foreach ($bill as $k => $value) :
				$barcode = '';
				if( strpos($value['content'], '=>') > 0 ){
					$barcode = substr($value['content'], -( strlen($value['content']) - strpos($value['content'], '>') - 1 ));
					if(!is_numeric($barcode)) {
						$barcode = '';
					}
				}elseif(is_numeric($value['content']) && strlen($value['content']) >= 12) {
					$barcode = $value['content'];
				}
			?>			
				<tr>
					<td><?= $value['time']; ?></td>
					<td><?= $value['device']; ?></td>
					<td><?= $value['command']; ?></td>
					<td><?= $value['content']; ?></td>
					<td>
					<?php 
						if(strlen($barcode) > 0 ){
							echo '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($barcode, $generatorPNG::TYPE_CODE_128)) . '">';
						}
					?>						
					</td>
				</tr>
			<?php
			endforeach;
		endforeach;
	}
?>