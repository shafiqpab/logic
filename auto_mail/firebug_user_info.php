<?
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


date_default_timezone_set("Asia/Dhaka");
session_start();
$file = 'firebug_user_list.txt';
$current = file_get_contents($file);
$current .= 'IP:'.get_client_ip().',User:'.$_SESSION['logic_erp']['user_id'].',PageId:'.$_SESSION['menu_id'].',PageTitle:'.$_SESSION['page_title'].',DateTime:'.date("d-m-Y h:i:s a",time()).";\n";
file_put_contents($file, $current);
//unset($_SESSION['logic_erp']);












?>


