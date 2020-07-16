<?php
    require('../dbconnection.php');
    $sql = "SELECT `driver_id`, `driver_name`, `driver_phone`, `driver_car_type`, `driver_password`, `car_label`, `car_color`, `car_model`, `driver_deal_type`, `addDate`, `last_online`, `month_debt`, `lat`, `lon`, `driver_order_status`, `driver_order_status_update`, `ord_id` FROM `drivers`";
	$re = mysql_query($sql) or die(mysql_error()."<br>".$sql) ;
	$cars = array();
	if(mysql_num_rows($re)) {
		while($car = mysql_fetch_assoc($re)) {
			$cars[] = array('car'=>$car);
		}
	}
	/* output in necessary format */
	header('Content-type: application/json');
	echo json_encode(array('cars'=>$cars));
?>