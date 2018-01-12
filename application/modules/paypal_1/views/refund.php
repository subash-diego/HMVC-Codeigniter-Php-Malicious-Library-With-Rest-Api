<!DOCTYPE html>
<html>
<head>
	<title>refund</title>
</head>
<body>

	<form action="<?php echo base_url('paypal_1/refund_amount')?>" method="post">
		amount: <input type="text" name="amount">
		txn id : <input type="text" name="id">
		<input type="submit" name="proceed refund">
	</form>

</body>
</html>