<?
//var_dump($_SERVER);die;

function get_client_ip() {
		    $ipaddress = '';
		    if (getenv('HTTP_CLIENT_IP')){
		        $ipaddress = getenv('HTTP_CLIENT_IP');
		    }
		    else if(getenv('HTTP_X_FORWARDED_FOR')){
		        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		    }
		    else if(getenv('HTTP_X_FORWARDED')){
		        $ipaddress = getenv('HTTP_X_FORWARDED');
		    }
		    else if(getenv('HTTP_FORWARDED_FOR')){
		        $ipaddress = getenv('HTTP_FORWARDED_FOR');
		    }
		    else if(getenv('HTTP_FORWARDED')){
		       $ipaddress = getenv('HTTP_FORWARDED');
		    }
		    else if(getenv('REMOTE_ADDR')){
		        $ipaddress = getenv('REMOTE_ADDR');
		    }
		    else{
		        $ipaddress = 'UNKNOWN';
		    }
		    return $ipaddress;
		}
		
echo get_client_ip();die;	

function get_ip_mac($trace)
{
 	 ob_start();
	 system($trace.' -h 2'." yahoo.com");
	 $trace=ob_get_contents();
	 ob_clean();
	 $lines=explode("\n", $trace);
	 $lines=explode(" ", $lines[5]);
	 
	foreach($lines as $line)
	{
		if (strlen(trim($line))>5)
		{
			$proxy_address=$line;
		}
	}
	
	$ipAddress=$_SERVER['REMOTE_ADDR'];
	$macAddr="";
	
	#run the external command, break output into lines
	ob_start();
	 system('arp -a '.$ipAddress);
	 $arp=ob_get_contents();
	 ob_clean();
	$lines=explode("\n", $arp);
	
	#look for the output line describing our IP address
	foreach($lines as $line)
	{
	   $cols=preg_split('/\s+/', trim($line));
	   if ($cols[0]==$ipAddress)
	   {
		   $macAddr=$cols[1];
	   }
	}
	return trim($ipAddress)."__".strtoupper(trim($macAddr))."__".trim($proxy_address); 
  //echo strtoupper($macAddr)."--".$ipAddress."--".$proxy_address; 
}





$output = `uname -a`;
if (preg_match("#Linux#i",$output)){
	$proxy_ip=get_ip_mac("traceroute");
}
else{
	$proxy_ip=get_ip_mac("tracert");
}


echo $proxy_ip;die;


?>