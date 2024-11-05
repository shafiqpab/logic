<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
extract($_REQUEST);

//---------------------------------------------------------------------

if($action=="conversion_rate_entry_from_data")
{
	$sql="select id,company_id,currency,conversion_rate,marketing_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{	
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "$('#cbo_company_id').val('".$row[csf("company_id")]."');\n";
		echo "$('#txt_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_conversion_rate').val('".$row[csf("conversion_rate")]."');\n";	
        echo "$('#txt_marketing_rate').val('".$row[csf("marketing_rate")]."');\n";
		echo "$('#txt_date').val('".change_date_format($row[csf("con_date")])."');\n";
		echo "set_button_status(1, permission, 'fn_conversion_rate_entry',1,1);";
	}
	exit();	
}

//--------------------------------------------------------------------------------------------
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$txt_date=str_replace("'","",$txt_date);
	if($db_type==0)
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-");
	}
	else
	{
		$txt_date=change_date_format($txt_date, "d-M-y", "-",1);
	}
	
	if( $operation==0 ) // Insert
	{
		$con = connect();
		if($db_type==0)	{
			mysql_query("BEGIN");}
		
		if(str_replace("'","",$update_id)=="")
		{
			
			
			$id=return_field_value("id","currency_conversion_rate","company_id=$cbo_company_id and currency=$txt_currency and con_date=$txt_date and status_active=1 and is_deleted=0");			

			if($id==''){
				$id= return_next_id("id","currency_conversion_rate",1);
				$field_array_mst="id,company_id,currency,conversion_rate,marketing_rate,con_date,inserted_by,insert_date,status_active,is_deleted";
				$data_array_mst="(".$id.",".$cbo_company_id.",".$txt_currency.",".$txt_conversion_rate.",".$txt_marketing_rate.",'".$txt_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo "insert into currency_conversion_rate($field_array_mst)values".$data_array_mst;die;
				$rID=sql_insert("currency_conversion_rate",$field_array_mst,$data_array_mst,1);
			}
			else
			{
				$field_array_up="company_id*currency*conversion_rate*marketing_rate*con_date*update_by*update_date";
				$data_array_up="".$cbo_company_id."*".$txt_currency."*".$txt_conversion_rate."*".$txt_marketing_rate."*'".$txt_date."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
				$rID=sql_update("currency_conversion_rate",$field_array_up,$data_array_up,"id",$id,1);
			}
		}
		
		if($db_type==0)
		{
			if( $rID)
			{
			mysql_query("COMMIT");  
			echo "0**_".$id;
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "10**_".$id;
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if( $rID)
				{
				oci_commit($con);  
				echo "0**_".$id;
				}
				else
				{
				oci_rollback($con);
				echo "10**_".$id;
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1) // Update 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$update_id=str_replace("'",'',$update_id);
		if($update_id!="")
		{
			$field_array_up="company_id*currency*conversion_rate*marketing_rate*con_date*update_by*update_date";
			$data_array_up="".$cbo_company_id."*".$txt_currency."*".$txt_conversion_rate."*".$txt_marketing_rate."*'".$txt_date."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		//echo "10**".$data_array_up; die;
		
		$rID=sql_update("currency_conversion_rate",$field_array_up,$data_array_up,"id",$update_id,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**_".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**_".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID)
				{
					oci_commit($con);  
					echo "1**_".str_replace("'",'',$update_id);
				}
				else
				{
					oci_rollback($con); 
					echo "10**_".str_replace("'",'',$update_id);
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==2) // Delete
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="update_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_update("currency_conversion_rate",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "2**_".str_replace("'",'',$update_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**_".str_replace("'",'',$update_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "2**_".str_replace("'",'',$update_id);
				}
				else
				{
					oci_rollback($con); 
					echo "10**_".str_replace("'",'',$update_id);
				}
			}
				disconnect($con);
				die;
	}

}



//--------------------------------------------------------------------------------------------

if($action=="load_list_view")
{ 
	list($currency_id,$com_id)=explode("_",$data);
	$con="";
	
	if($currency_id>0)$con=" and currency=$currency_id"; 
	if($com_id>0)$con.=" and company_id=$com_id";
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
<fieldset style="width:600px;">
<div style="width:590px;">
<table class="rpt_table" width="99%" border="1" cellspacing="0" cellpadding="0" rules="all">
    <thead>
        <th width="30">SL No</th>
        <th width="120">Company</th>
        <th width="80">Currency</th>
        <th width="80">Conversion Rate</th>
        <th width="80">Marketing Rate</th>
        <th>Date</th>
    </thead>
</table>
</div>
<div style="width:590px; overflow-y: scroll; max-height:200px;">
<table class="rpt_table" width="99%" border="1" id="mail_setup" cellspacing="0" cellpadding="0" rules="all">
    <tbody>
<?
$result=sql_select("select id, company_id, currency, conversion_rate, marketing_rate, con_date from currency_conversion_rate where status_active=1 and is_deleted=0 $con order by id DESC");
$sl=1;
foreach($result as $list_rows){
$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
 ?>    
    <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data(<? echo $list_rows[csf('id')]; ?>,'conversion_rate_entry_from_data', 'requires/currency_conversion_rate_controller')" style="cursor:pointer;">
        <td width="30" align="center"><? echo $sl; ?> </td>
        <td width="120"><? echo $comp[$list_rows[csf('company_id')]]; ?> </td>
        <td width="80"><? echo $currency[$list_rows[csf('currency')]]; ?> </td>
        <td width="80" align="right"><? echo $list_rows[csf('conversion_rate')]; ?></td>
        <td width="80" align="right"><? echo $list_rows[csf('marketing_rate')]; ?></td>
        <td align="center"><? echo change_date_format($list_rows[csf('con_date')]); ?> </td>
    </tr>
<? $sl++; } ?>    
</tbody>  
</table>
</div>    
   
</fieldset>	
<?
exit(); 
} 
?>