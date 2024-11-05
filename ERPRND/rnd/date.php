<?php
$date_arr = array('20-05-2001','02-01-2015','30-03-2012');


echo "Max Date: ". max($date_arr)."\n";
echo "Min Date: ". min($date_arr)."\n";


die;
    date_default_timezone_set('Asia/Dhaka');
    echo date('D, d M Y H:i:s',time()+5);
    exit();
?>