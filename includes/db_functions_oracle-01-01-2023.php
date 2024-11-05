<?
function connect()
{ 
	$con = oci_pconnect('PLATFORMERPV3', 'PLATFORMERPV3', '//192.168.11.242:1521/logicdb');
	if(!$con)
	{
		trigger_error("Problem connecting to server");
	}
	// oci_commit($con);
	/*$db = mssql_select_db('logic_erp', $con);
	if(!$db)
	{
		trigger_error("Problem selecting database");
	}	*/

	return $con;
}

function disconnect($con)
{
	//$discdb = mssql_close($con);
	$discdb =oci_close($con);
	if(!$discdb)
	{
		trigger_error("Problem disconnecting database");
	}
}


//trims_erp connect...................................;
//$server='localhost', $user='trims', $passwd='trims#2015', $db_name='trims'
function trims_erpDBConnect( $server='localhost', $user='root', $passwd='', $db_name='logic_erp_trims' )
{
	$con = mysql_connect( $server, $user, $passwd );
	if(!$con)
	{
		trigger_error("Problem connecting to server");
	}
	$DB =  mysql_select_db($db_name, $con);
	if(!$DB)
	{
		trigger_error("Problem selecting database");
	}
	return $con;
}


function trims_erpDBClose($con)
{
	$discdb = mysql_close($con);
	if(!$discdb)
	{
		trigger_error("Problem disconnecting database");
	}
}

/*
function sql_select($strQuery, $is_single_row, $new_conn, $un_buffered, $connection)
{
	if($connection==""){
		$con_select = connect();
	}else{
		$con_select = $connection;
	}
	//echo  $strQuery;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;
			disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}*/

function sql_select($strQuery, $is_single_row="", $new_conn="", $un_buffered="", $connection="")
{
	if ( $new_conn!="" )
	{
		$new_conn=explode("*",$new_conn);
		$con_select = oci_connect($new_conn[1], $new_conn[2], $new_conn[0]);
	}
	else
	{
		if($connection==""){
			$con_select = connect();
		}else{
			$con_select = $connection;
		}
	}
	//echo  $strQuery;die;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;
			if($connection=="") disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	if($connection=="")  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}

//need to check
function sql_select_cls( $strQuery,$is_single_row,$un_buffered)
{
	$con_select = connect();
	//echo  $strQuery;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	return $result;
	/*$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;

			disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }*/
	  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}



function sql_insert( $strTable, $arrNames, $arrValues, $commit="", $contain_lob="" )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}

	   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	 //return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0";
		}
		return "1";

	}
  	//return  $strQuery; die;
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);


	if (!oci_error($exestd))
	{
		user_activities($exestd);
		
		/*		$pc_time= add_time(date("H:i:s",time()),360);
				$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
				$pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
		
				$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
				$resultss=oci_parse($con, $strQuery);
				oci_execute($resultss,OCI_NO_AUTO_COMMIT);
				$_SESSION['last_query']="";
		*/	

	}
	
	//echo $strQuery;die;
	

	if ($exestd)
		return "1";
	else
		return "0";
	die;

}

function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit="")
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);


	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}

	//$arrRefFields=explode("*",$arrRefFields);
	//$arrRefValues=explode("*",$arrRefValues);
	$strQuery .= $arrRefFields." in (".$arrRefValues.")";
	// echo $strQuery;die;
    global $con;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!oci_error($stid))
		{

		$pc_time= add_time(date("H:i:s",time()),360);
		$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	    $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')";

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss);
		$_SESSION['last_query']="";
		oci_commit($con);
		return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
}

function sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

		//return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

function sql_delete($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit="")//please check the function
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
			global $con;
	 //echo $strQuery;
	 $stid =  oci_parse($con, $strQuery);
	 $exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT );
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error( $stid))
		{
			oci_commit($con);
			return "2";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;


}



function execute_query( $strQuery, $commit="" )
{
	global $con ;
	$result =  oci_parse($con, $strQuery);
	$exestd=oci_execute($result,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;

	if ( $commit==1 )
	{
		if (!oci_error($result))
		{
			oci_commit($con);
			return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return "1";
}


function user_activities($exestd)
{
	global $con ;
	if (!oci_error($exestd))
	{
		date_default_timezone_set("Asia/Dhaka");
		$pc_time= date("H:i:s",time());
		$pc_date_time = date("d-M-Y h:i:s a",time());
		$pc_date = date("d-M-Y",time());

		//get_ip_mac($trace)
		
	/*	ob_start(); // Turn on output buffering
		system('ipconfig/all'); //Execute external program to display output
		$mycom=ob_get_contents(); // Capture the output into a variable
		ob_clean(); // Clean (erase) the output buffer
		$findme = "Physical";
		$pmac = strpos($mycom, $findme); // Find the position of Physical text
		$mac=substr($mycom,($pmac+36),17); // Get Physical Address*/
		
		
		$mac='UNKNOWN';
		foreach(explode("\n",str_replace(' ','',trim(`getmac`,"\n"))) as $i)
		if(strpos($i,'Tcpip')>-1){$mac=substr($i,0,17);break;}
	 
		
		
		$strQuery= "INSERT INTO activities_history ( mac,session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$mac."','".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss,OCI_NO_AUTO_COMMIT);
		$_SESSION['last_query']="";
		
		//echo $strQuery;
	}

}




?>
