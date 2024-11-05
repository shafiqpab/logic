<?php
/************************************************************************
|	Purpose			:	This Controller is for Field Level Access
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	26.08.2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*************************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);

$fieldlevel_arr[1][1]['name']="User ID";
$fieldlevel_arr[1][1]['id']="txt_user_id";
$fieldlevel_arr[1][2]['name']="Password";
$fieldlevel_arr[1][2]['id']="txt_passwd";
$fieldlevel_arr[1][3]['name']="Confirm Password";
$fieldlevel_arr[1][3]['id']="txt_conf_passwd";
$fieldlevel_arr[1][4]['name']="Full User Name";
$fieldlevel_arr[1][4]['id']="txt_full_user_name";
$fieldlevel_arr[1][5]['name']="Desig";  
$fieldlevel_arr[1][5]['id']="cbo_designation"; 


if($action=="load_drop_down_item")
{
	$field_arr=array();
	foreach($fieldlevel_arr[$data] as $key=>$val)
	{
		foreach($val as $k=>$v)
		{
			$field_arr[$key]=$v;
		}
	}
	//print_r($field_arr);
	echo create_drop_down( "cbo_field_id",150,$field_arr,"","","","","","","","","","","","","cbo_field_id" );
}

if($action=='save_update_delete')
{
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		$id = return_next_id( "id", "field_level_access", 1 ) ;
		$field_array = "id, page_id, field_id, permission_id, user_id";
		$field_id=explode(",",str_replace("'","",$cbo_field_id));
		
		for($i=0; $i<count($field_id); $i++)
		{
			if($data_array!="") $data_array.=","; 	
			$data_array.="(".$id.",".$cbo_page_id.",".$field_id[$i].",".$cbo_permission_id.",".$cbo_user_id.")"; 
			$id=$id+1;
		}
		$rID2 = sql_insert("field_level_access",$field_array,$data_array,1);
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$cbo_user_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_user_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID2)
			{
				oci_commit($con);   
				echo "0**".$cbo_user_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_user_id;
			}
		}
		disconnect($con);
		die;		
	}
	
	//update
	if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="page_id*field_id*permission_id";
		$data_array="".$cbo_page_id."*".$cbo_field_id."*".$cbo_permission_id."";
		$rID=sql_update("field_level_access",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$cbo_user_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_user_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "1**".$cbo_user_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_user_id;
			}
		}
	   disconnect($con);
	   die;
	}
	
	//delete
	if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID = execute_query("delete from field_level_access where id=$update_id",1);		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$cbo_user_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_user_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$cbo_user_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_user_id;
			}
		}
		disconnect($con);
		die;	
	}
}

if($action=='action_user_data')
{
	//echo "su..re";
	$array=sql_select("select id, page_id, field_id, permission_id, user_id from field_level_access where user_id=".$data."");
	$i=1;
	if(count($array)>0)
	{
		?>
		<div>
			<fieldset style="width:500px;">
				<table width="500" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">        
					<thead> 
						<tr>
							<th width="50">SL No</th>
							<th width="190">Page Name</th>
							<th width="190">Field Level</th>
							<th>Permission</th>
						</tr>
					</thead>
				</table>
				<div style="max-height:200px; width:500px; overflow-y:scroll" id="">
					<table id="list_view" width="480" height="" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">
						<tbody>
							<?php 
							$sl=0;
							foreach($array as $row)
							{
								$sl++;
								$field_arr=array();
								foreach($fieldlevel_arr[$row[csf('page_id')]] as $key=>$val)
								{
									foreach($val as $k=>$v)
									{
										$field_arr[$key]=$v;
									}
								}

								?>
								<tr id="tr_<?php echo $sl; ?>" height="20" bgcolor="<?php echo ($sl%2==0)?"#E9F3FF":"#FFFFFF"; ?>" onclick="get_php_form_data('<?php echo $row[csf('id')]; ?>','load_php_data_to_form','requires/field_level_access_controller')" style="cursor:pointer;"> 
									<td width="50"><?php echo $sl; ?></td>
									<td width="190"><?php echo $entry_form[$row[csf('page_id')]]; ?></td>
									<td width="190"><?php echo $field_arr[$row[csf('field_id')]]; ?></td>
									<td><?php echo $yes_no[$row[csf('permission_id')]]; ?></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>
        <?php			
	}
	exit();
 }
 
//load_php_data_to_form
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, page_id, field_id, permission_id, user_id from field_level_access where id=".$data."" );
	foreach ($nameArray as $inf)
	{	
		echo "document.getElementById('cbo_page_id').value = '".($inf[csf("page_id")])."';\n";    
		echo "document.getElementById('cbo_field_id').value  = '".($inf[csf("field_id")])."';\n";
		echo "document.getElementById('cbo_permission_id').value = '".($inf[csf("permission_id")])."';\n";    
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_field_level_access',1);\n";
		echo "set_item('".($inf[csf("page_id")])."');\n";
		echo "set_multiselect('cbo_field_id','0','1','".($inf[csf("field_id")])."','__set_supplier_status__requires/field_level_access_controller');\n";
	}
}
?>