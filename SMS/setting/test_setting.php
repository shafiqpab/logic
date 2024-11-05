 <?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('sms_setting.php');

echo sendSMS(array('01511100004,01552601805'),'Test sms');
	
	




?> 