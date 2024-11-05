<?php
/********************************* Comments *************************
*	Purpose			: 	This Controller is for Dynamic Letter
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Nuruzzaman 
*	Creation date 	: 	05-10-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
*********************************************************************/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
include('../../../includes/array_function.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "11**$operation"; die;
	
	$txt_letter_body=str_replace("****", "?", $txt_letter_body);
	$txt_letter_body=str_replace("**", "&", $txt_letter_body);
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "letter_type", "dynamic_letter", "letter_type=$cbo_letter_type and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//id, letter_type, letter_body, status_active, is_deleted, inserted_by, insert_date, updated_by, update_date
			//cbo_letter_type,txt_letter_body
			$id=return_next_id( "id", "dynamic_letter", 1 );
			$field_array="id,letter_type,letter_body,inserted_by,insert_date";
			$data_array="(".$id.",".$cbo_letter_type.",'".$txt_letter_body."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$flag=1;
			$rID=sql_insert("dynamic_letter",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			//=================================================================================
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{  
					 oci_commit($con);  
					echo "0**".$rID."**".$id;
				}
				else
				{ 
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	if ($operation==1)  // Update Here
	{
		if (is_duplicate_field( "letter_type", "dynamic_letter", "letter_type=$cbo_letter_type and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="letter_type*letter_body*updated_by*update_date";
			$txt_letter_body=str_replace("'","",$txt_letter_body);
			$tot_string=strlen($txt_letter_body);
			$count_loop=ceil($tot_string/3900);
			$letter_body_data='';$count=0; $interval=3900;
			for($i=1;$i<=$count_loop; $i++)
			{
				$letter_body_data.="to_clob('".substr($txt_letter_body, $count, $interval)."') ||";
				$count+=3900;
			}
			$letter_body_data=chop($letter_body_data,"||");
			$data_array="".$cbo_letter_type."*".$letter_body_data."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$flag==1;
			$rID=sql_update("dynamic_letter",$field_array,$data_array,"id","".$update_id."",1);
			//echo "10**".$rID;oci_rollback($con);disconnect($con);die;
			if($rID) $flag=1; else $flag=0;
			//=======================================================================================================
		
			if($db_type==0)
			{
				if($flag==1 )
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			  if($flag==1 )
				{
					oci_commit($con);  
					echo "1**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_update("dynamic_letter",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
	
		if($db_type==2 || $db_type==1 )
		{	if($rID )
			{
				oci_commit($con);  
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}


function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
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

	return $strQuery;die;
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

?>