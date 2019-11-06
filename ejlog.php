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
<h1 class="text-center">EJLOG parse</h1>
<div class="container">
	<div class="row"> 
		<div class="col-xs-12">
			<form action="<?= dirname($_SERVER['PHP_SELF']) ?>/ejlog_upload.php" class="dropzone" id="my-awesome-dropzone"></form>
		</div>
	</div>
	<div class="row"> 
		<div class="col-xs-12">
			Support payment:
			<ul>
				<li>CASH</li>
				<li>CREDIT CARD</li>
				<li>POINT</li>
				<li>MOMO</li>
				<li>GOTIT</li>
			</ul>
		</div>
	</div>
</div>
<hr>
<div class="container">
	<div class="row"> 
		<div class="col-xs-12">
		<table class="table table-responsive" id="iosdebug-table">
		<tbody>	
			
		</tbody>
		</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	Dropzone.autoDiscover = false;
	$(document).ready(function(){
		var $table = $('#iosdebug-table tbody');
		var myDropzone = new Dropzone('.dropzone',{ 
			paramName: "iosdebug",
			acceptedFiles: '.dat',
			init: function() {
				this.on("success", function(file, responseText) {
				  	myDropzone.removeFile(file);
			  		$table.empty();
			  		$table.append(responseText);
				});
			}
		});

		$('body').on('change', '#cashiers',function(){
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
