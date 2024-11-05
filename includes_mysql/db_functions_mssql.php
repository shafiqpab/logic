<?
header('Content-type:text/html; charset=utf-8');
session_start();
function connect() 
{
	//$link = mssql_connect("DEV_SERVER", 'sa', 'sa');
	 $con = odbc_connect("logic_erp", "", "");
	if(!$con)
	{
		trigger_error("Problem connecting to server");
	}	
	odbc_autocommit($con, FALSE);
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
	$discdb =odbc_close($con);
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

function sql_select($strQuery)
{
	///$strQry=return_global_query($strQuery); 
	//return $strQry; die;
	$con = connect();
	$result = odbc_exec($con, $strQuery);
	$rows = array();
	 while($summ=odbc_fetch_array($result))
	  
		$rows[] = $summ;
	  
	return $rows;
	disconnect($con);
	die;
	
	
	$con = connect();
	
	$result_select = mysql_query($strQuery) or die(mysql_error());
	$rows = array();
	while($row = mysql_fetch_array($result_select))
		$rows[] = $row;
	 
	return $rows;
	disconnect($con);
	die;
	
}

function sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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
	 
	odbc_exec($con, $strQuery);
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!odbc_error())
		{
			$pc_time= add_time(date("H:i:s",time()),360);  
			$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			$resultss=odbc_exec($con, $strQuery);
			$_SESSION['last_query']="";
			odbc_commit($con); 
			return "0";
		}
		else
		{
			odbc_rollback($con);
			return "10";
		}
	}
	else
		return 0;
	die;
}

function sql_insert($strTable, $arrNames, $arrValues, $commit)
{
	global $con ;
	 
	$strQuery="INSERT INTO ".$strTable." (".$arrNames.") VALUES ".$arrValues.""; 
	 //return  $strQuery; die;
	$result=odbc_exec($con, $strQuery);
	
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	
	if ($commit==1)
	{
		if (!odbc_error())
		{
			
			$pc_time= add_time(date("H:i:s",time()),360);  
			$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
			 
			$resultss=odbc_exec($con, $strQuery);
			odbc_commit($con);
			$_SESSION['last_query']="";
			return 0; die;
			
		}
		else
		{
			odbc_rollback($con);
			return 10;
		}
	}
	die;
}


 
function sql_delete($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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
	 
	odbc_exec($con, $strQuery);
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!odbc_error())
		{
			
			$pc_time= add_time(date("H:i:s",time()),360);  
			$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			
			$resultss=odbc_exec($con, $strQuery);
			odbc_commit($con); 
			$_SESSION['last_query']="";
			return "0";
		}
		else
		{
			odbc_rollback($con);
			return "10";
		}
	}
	else
		return 0;
	die;
 }
 
   
?>







 