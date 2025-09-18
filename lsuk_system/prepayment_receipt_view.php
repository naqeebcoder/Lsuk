<?php session_start(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>View Prepayment Receipt</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/util.css">
</head>
<?php
include 'class.php';
include 'db.php';
$v_id = $_GET['v_id'];
$row_t = $acttObj->read_specific("id, voucher, invoice_no, attachment", "pre_payments", "id=" . $v_id);
$exp_r = "file_folder/pre_payments/" . $row_t['attachment'];
?>

<body>
	<div align="center" class="col-md-12"><br>
		<h1>
			Receipt for voucher: <?php echo $row_t['voucher']; ?> <br>
			Track# <?php echo $row_t['invoice_no']; ?>
		</h1><br />

		<?php
			if (file_exists($exp_r)) {
				// Get file extension in lowercase
				$extension = strtolower(pathinfo($exp_r, PATHINFO_EXTENSION));

				// Image extensions
				$imageExtensions = array("jpg", "jpeg", "png", "bmp", "webp");

				if (in_array($extension, $imageExtensions)) { ?>
					<div class="col-md-12">
						<a href="<?php echo $exp_r; ?>" target="_blank" title="Click to full view">
							<div class="col-md-6" style="background: url('<?php echo $exp_r; ?>'); background-size: cover; background-repeat: no-repeat; height: 80%; width: 75%;">
								<img src="<?php echo $exp_r; ?>" style="width: 100%;" class="img-thumbnail img-responsive">
							</div>
						</a>
					</div>
					<br><br><br>
				<?php 
				} else {
					// Document extensions
					$docExtensions = array("doc", "docx", "xls", "xlsx");

					if (in_array($extension, $docExtensions)) { ?>
						<iframe src="https://docs.google.com/viewer?url=<?php echo urlencode($exp_r); ?>&embedded=true" frameborder="2" width="100%" height="600"></iframe>
					<?php 
					} else { ?>
						<iframe src="<?php echo $exp_r; ?>" frameborder="2" width="100%" height="600"></iframe>
					<?php 
					}
				}
			} else {
				echo '<div class="alert alert-danger">
					<h3 class="m-t-0 m-b-0">No receipt found.</h3>
				</div>';
			}
			?>

	</div>
</body>

</html>