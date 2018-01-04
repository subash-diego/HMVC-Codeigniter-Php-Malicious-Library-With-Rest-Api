<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h4>welcome to checkout to payment page</h4>

	<?php 
	echo !empty($this->session->flashdata('error_msg'))?$this->session->flashdata('error_msg'):FALSE;
	echo  !empty($this->session->flashdata('success_msg'))?$this->session->flashdata('success_msg'):FALSE;
	?>
	<form method="post" class="" role="form" action="<?php echo HOMEURL.'paypal_1/create_payment_using_paypal'; ?>">
		<input type="hidden" name="item_name" title="item_name" value="subash chandar">
		<input type="hidden" name="item_number" title="item_number" value="12345">
		<input type="hidden" name="item_description" title="item_description" value="to buy cake">	
		<input type="hidden" name="item_tex" title="item_tex" value="1">
		<input type="hidden" name="item_price" title="item_price" value="10">
		<input type="hidden" name="details_tex" title="details_tex" value="5">
		<input type="hidden" name="details_subtotal" title="details_subtotal" value="15">

		<input type="submit" name="pay now" value="pay now">
	</form>

</body>
</html>