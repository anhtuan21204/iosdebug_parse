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
			$commands[] = $command;
			if( isCashierLogined($bill) ) {
				$bill = [0=>['signin'=>'Sign In']] + $bill;
				$bills[] = $bill;		
				$bill = array();		
			}elseif( isCashierLogOff($bill) ){
				$bill[] = ['signoff'=>'Sign Off'];
				$bills[] = $bill;		
				$bill = array();	
			}elseif( isCashKeyPressed($command) ) {
				$temp = 0;
				$commands = array();
				$commands[] = $command;
			}elseif(checkEndBillCash($commands) || checkEndBillCard($commands)){
				$bills[] = $bill;		
				$bill = array();	
				$commands = array();
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
	
	function isCashierLogined($commands) {
		$last = count($commands) - 1;
		if($last >= 1){
			if($commands[$last-1]['command'] == ENTER_KEY && strlen($commands[$last-1]['content']) == 9 && $commands[$last]['command'] == ENTER_KEY && strlen($commands[$last]['content']) == 4){ // thu ngan dang nhap
				return true;
			}
		}elseif($last == 0){
			if($commands[$last]['command'] == ENTER_KEY && strlen($commands[$last]['content']) == 4){ // thu ngan dang nhap
				return true;
			}
		}
		
		return false;
	}


	function isCashierLogOff($commands) {
		$last = count($commands) - 1;
		if($commands[$last]['command'] == SIGNOFF && strlen($commands[$last]['content']) == 0){ // thu ngan sign off
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

	function checkEndBillCash($commands=array()){
		if(count($commands) == 3){
			if($commands[0] == CASH_KEY && $commands[1] == ENTER_KEY && $commands[2] == ENTER_KEY){
				return true;
			}
		}
		return false;
	}

	function checkEndBillCard($commands=array()){
		if(count($commands) == 4){
			if($commands[0] == CARD_KEY && $commands[1] == ENTER_KEY && $commands[2] == ENTER_KEY && $commands[3] == ENTER_KEY){
				return true;
			}
		}
		return false;
	}

	function createHTMLForReview($bills, $generatorPNG) {
		foreach ($bills as $key => $bill) :			
			
			foreach ($bill as $k => $value) :	
				if($k == 0){
					echo '<tr><td colspan="5">Bill no: '.$key.'</td></tr>';
				}

				if( isset($value['signin']) ){
					echo '<tr><td colspan="5" style="text-align:center;font-size:20px"> --- '.$value['signin'].' --- </td></tr>';
					continue;
				}

				if( isset($value['signoff']) ){
					echo '<tr><td colspan="5" style="text-align:center;font-size:20px"> --- '.$value['signoff'].' --- </td></tr>';
					continue;
				}

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
					<td><?php if(is_numeric($value['content']) && strlen($value['content']) == 16){ echo 'Thẻ hội viên: '; } ?><?= $value['content']; ?></td>
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