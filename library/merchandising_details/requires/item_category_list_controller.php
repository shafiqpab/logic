<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="category_list_view")
{
	$category_sql="select id,actual_category_name, category_id, short_name from lib_item_category_list order by category_id"; 
	$item_category_result=sql_select($category_sql);
	?>
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
    <? 
    $i=1;
    foreach($item_category_result as $row)
    {  
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		$actual_category_name=$row[csf('actual_category_name')];
		$category_id=$row[csf('category_id')];
		$shortname=$row[csf('short_name')];
		if($shortname!="") $shortname=$shortname; else $shortname=$actual_category_name;
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $category_id;?>","load_item_category","requires/item_category_list_controller")' style="cursor:pointer" >
            <td width="50"><?php echo $category_id; ?></td>
            <td width="250"><p><?php echo $actual_category_name; ?></p></td>
            <td><p><?php echo $shortname; ?></p></td>
		</tr>
		<? $i++;  
    } 
    ?>
    </table>
    <?
    exit();
}

if($action =="load_item_category")
{
	$sql="select id, category_id, short_name, actual_category_name, status_active from lib_item_category_list where category_id='$data'"; 
	$nameArray=sql_select($sql);
	$update_id=$nameArray[0][csf("id")]; 
	$actualcategoryname=$nameArray[0][csf("actual_category_name")]; 
	$categoryid=$nameArray[0][csf("category_id")];
	$statusFlag=$nameArray[0][csf("status_active")];
	echo "document.getElementById('txt_Category_id').value = '".$actualcategoryname."';\n";
	echo "document.getElementById('hidden_Category_id').value = '".$categoryid."';\n";
	echo "document.getElementById('update_id').value = '".$update_id."';\n";
	echo "document.getElementById('txt_Category_short_name').value = '".$nameArray[0][csf("short_name")]."';\n";
	echo "document.getElementById('cbo_status').value = '".$statusFlag."';\n";
}

if ($action=="save_update_delete_category")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$update_id=str_replace("'","",$update_id);
	$hidden_Category_id=str_replace("'","",$hidden_Category_id);
	if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "20**select category_id from lib_item_category_list where status_active=1 and is_deleted=0 and category_id<>$hidden_Category_id and trim(short_name)='".trim(str_replace("'","",$txt_Category_short_name))."'";disconnect($con);die;
		$duplicate = is_duplicate_field("category_id", "lib_item_category_list", "status_active=1 and is_deleted=0 and category_id<>$hidden_Category_id and trim(short_name)='".trim(str_replace("'","",$txt_Category_short_name))."'");
		if($duplicate) 
		{
			echo "20**Duplicate Category Name is Not Allow.";
			disconnect($con);
			die;
		}
		
		$trans_check=return_field_value("id","inv_transaction","status_active=1 and is_deleted=0 and item_category=$hidden_Category_id","id");
		if($trans_check)
		{
			echo "20**Transaction Available, Update Not Allow.";
			disconnect($con);
			die;
		}
		//echo "20**test."; disconnect($con);die;
		if($update_id!="")	
		{	 
			$field_array_update="category_id*short_name*updated_by*update_date*status_active";
	  		$data_array_update="".$hidden_Category_id."*".$txt_Category_short_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			$rID=sql_update("lib_item_category_list",$field_array_update,$data_array_update,"id","".$update_id."",1);
			$id=$update_id;
		}
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".$rID."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}
?>