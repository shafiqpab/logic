<?
header('Content-type:text/html; charset=utf-8');
session_start();

function connect( $server='localhost', $user='root', $passwd='asro@123', $db_name='ffl_erp' ) 
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
	//mysql_query("START TRANSACTION");
	return $con;
}

function disconnect($con) 
{
	$discdb = mysql_close($con);
	if(!$discdb)
	{
		trigger_error("Problem disconnecting database");
	}	
}

/* *******************************************  USAGE OF FUNCTION
	$nameArray=sql_select("SELECT id,country FROM  country order by  country");
	while($result = mysql_fetch_array($nameArray))
	 {
		echo $result[1];
	}
******************************************* */

function sql_select($strQuery, $is_single_row, $new_conn, $un_buffered)
{ 
    //$strQry=return_global_query($strQuery); 
	//return $strQry;die;
	//$con = connect();
	
	if ( $new_conn!="" )
	{
		$new_conn=explode("*",$new_conn);
		if($new_conn[2]=='') $new_conn[2]='';
		$con = connect($new_conn[0], $new_conn[1], $new_conn[2], $new_conn[3]);
		$db_name=$new_conn[3];
		//echo $db_name;
		if ( $un_buffered=="" ) $result_select = mysql_db_query( $db_name, $strQuery ) or die(mysql_error());
		else $result_select = mysql_unbuffered_query( $strQuery );
	}
	else
	{
		$con = connect();
		if ( $un_buffered=="" ) $result_select = mysql_query( $strQuery ) or die(mysql_error());
		else $result_select = mysql_unbuffered_query( $strQuery );
	}
	
	$rows = array();
	while( $row = mysql_fetch_array( $result_select ))
	{
		if($is_single_row==1) 
		{
			$rows[] = $row;
			return $rows;
			disconnect($con);
			die;
		}
		else
			$rows[] = $row;
	}
	return $rows;
	disconnect($con);
	die;
}

function sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
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
	if( strpos($strQuery, "WHERE")==false)  return "0";
	//return $strQuery; die;
  // echo $strQuery; die;
	$result=mysql_query($strQuery); 
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		//$pc_time= add_time(date("H:i:s",time()),360);  
		//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
		
		//$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')"; 

		//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		 
		//$result111=mysql_query($strQuery); 
		$_SESSION['last_query']="";
	}
	//return $strQuery; die;
	
		return $result;
	die;
}

function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
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
	 
   //return $strQuery; die;
	$result=mysql_query($strQuery); 
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		//$pc_time= add_time(date("H:i:s",time()),360);  
		//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
		
		//$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')"; 

		//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		 
		//$result111=mysql_query($strQuery); 
		$_SESSION['last_query']="";
	}
	//return $strQuery; die;
		return $result;
	die;
}

function sql_insert($strTable,$arrNames,$arrValues, $commit )
{
	global $con ;
	
	$strQuery= "INSERT INTO ".$strTable." (".$arrNames.") VALUES ".$arrValues.""; 
	 //return $strQuery; die;
	 
//	mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	
	$result=mysql_query($strQuery); 
	
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	
	if ($commit==1)
	{
		//$pc_time= add_time(date("H:i:s",time()),360);  
		//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
		
		//$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 

		///mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		 
		//$result111=mysql_query($strQuery); 
		$_SESSION['last_query']="";
	}
	  
		return $result;
	die;
}

function sql_delete($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
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
   //return $strQuery; die;
	$result=mysql_query($strQuery); 
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		////$/pc_time= add_time(date("H:i:s",time()),360);  
		//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
		
		//$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','2')"; 

		//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		 
		//$result111=mysql_query($strQuery); 
		$_SESSION['last_query']="";
	}
		return $result;
	die;
}

function execute_query( $strQuery, $commit )
{
	//echo $strQuery;die;
	//return $strQuery;die;
	global $con ;
	$result=mysql_query($strQuery); 
	return $result;
}
?>