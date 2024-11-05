<?php
/************************************************************************
|	Purpose			:	This Controller is for Home Dash Board Privilege
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	27.07.2015
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

if($action=="load_drop_down_item")
{
	$data=explode("__",$data);
	$home_arr=array();
	foreach($home_page_array[$data[0]] as $key=>$val)
	{
		foreach($val as $k=>$v)
		{
			$home_arr[$key]=$val['name'];
		}
	}
	
	//print_r($home_arr);
	echo create_drop_down( "cboItemId_".$data[1],150,$home_arr,"",1,"-- Select --","","","","","","","","","","cboItemId[]" );
}

if($action=='save_update_delete')
{
	//echo $total_row; die;
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		$rID = execute_query("delete from home_page_priviledge where user_id=$cboUserId",1);
		$id = return_next_id( "id", "home_page_priviledge", 1 ) ;
		$field_array = "id, module_id, item_id, user_id, sequence_no";
		for($i=1;$i<=$total_row;$i++)
		{
			$module_id		= "cboModuleId_".$i;
			$item_id		= "cboItemId_".$i;
			$sequence_no	= "txtSequinceNo_".$i;

			if($data_array!="") $data_array.=","; 	
			$data_array.="(".$id.",".$$module_id.",".$$item_id.",".$cboUserId.",".$$sequence_no.")"; 
			$id=$id+1;
		}
		$rID2 = sql_insert("home_page_priviledge",$field_array,$data_array,1);
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "0**".$rID;
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
	
	//update
	if($operation==1)
	{
		$con = connect();
		$rID = execute_query("delete from home_page_priviledge where user_id=$cboUserId",1);
		$id = return_next_id( "id", "home_page_priviledge", 1 ) ;
		$field_array = "id, module_id, item_id, user_id, sequence_no";
		for($i=1;$i<=$total_row;$i++)
		{
			$module_id		= "cboModuleId_".$i;
			$item_id		= "cboItemId_".$i;
			$sequence_no	= "txtSequinceNo_".$i;

			if($data_array!="") $data_array.=","; 	
			$data_array.="(".$id.",".$$module_id.",".$$item_id.",".$cboUserId.",".$$sequence_no.")"; 
			$id=$id+1;
		}
		$rID2 = sql_insert("home_page_priviledge",$field_array,$data_array,1);
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "0**".$rID;
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
	
	//delete
	if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID = execute_query("delete from home_page_priviledge where user_id=$cboUserId",1);		
		if($db_type==0)
		{
			if($rID)
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
		{
			if($rID)
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

if($action=='action_user_data')
{
	//echo "su..re";
	$nameArray=sql_select( "select id, module_id, item_id, user_id, sequence_no from home_page_priviledge where user_id=$data" );
	$num_row=count($nameArray);
	$i=1;
	if($num_row>0)
	{
		foreach($nameArray as $row) 
		{
			$home_arr=array();
			foreach($home_page_array[$row[csf('module_id')]] as $key=>$val)
			{
				foreach($val as $k=>$v)
				{
					$home_arr[$key]=$val[$k];
				}
			}
			?>
			<tr id="dashboard_<? echo $i; ?>">
				<td width="150">
					<?php echo create_drop_down( "cboModuleId_".$i,150,$home_page_module,"",1,"-- Select --",$row[csf('module_id')],"set_item( this.value,".$i." );","","","","","","","","cboModuleId[]" ); ?>
				</td>
				<td width="150" id="itemtd_<? echo $i; ?>">
					<?php echo create_drop_down( "cboItemId_".$i,150,$home_arr,"",1, "-- Select --",$row[csf('item_id')],"","","","","","","","","cboItemId[]" ); ?>
				</td>
				<td width="150">
                	<input type="text" id="txtSequinceNo_<? echo $i; ?>" name="txtSequinceNo[]" value="<?php echo $row[csf('sequence_no')]; ?>" class="text_boxes_numeric" style="width:138px; text-align:left;" />
                </td>
				<td width="140" align="center">
					<input type="button" id="dashboardincrease_<? echo $i; ?>" name="dashboardincrease[]" value="+" class="formbuttonplasminus" style="width:60px;" onClick="fnc_addRow(<? echo $i; ?>,'tbl_dashboard','dashboard_')" />
					<input type="button" id="dashboarddecrease_<? echo $i; ?>" name="dashboarddecrease[]" value="-" class="formbuttonplasminus" style="width:60px;" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_dashboard','dashboard_',<? echo $row[csf('id')]; ?>)" />
				</td>
			</tr>
			<?
			$i++;
		}
	}
	else 
	{
		?>
        <tr id="dashboard_<? echo $i; ?>">
            <td width="150">
            <?php echo create_drop_down( "cboModuleId_".$i,150,$home_page_module,"",1,"-- Select --","","set_item( this.value,".$i." );","","","","","","","","cboModuleId[]" ); ?>
            </td>
            <td width="150" id="itemtd_<? echo $i; ?>">
                <?php echo create_drop_down( "cboItemId_".$i,150,$blank_array,"",1,"-- Select --","","","","","","","","","","cboItemId[]" ); ?>
            </td>
            <td width="150">
            	<input type="text" id="txtSequinceNo_<? echo $i; ?>" name="txtSequinceNo[]" value="" class="text_boxes_numeric" style="width:138px; text-align:left;" />
            </td>
            <td width="140" align="center">
                <input type="button" id="dashboardincrease_<? echo $i; ?>" name="dashboardincrease[]" value="+" class="formbuttonplasminus" style="width:60px;" onClick="fnc_addRow(<? echo $i; ?>,'tbl_dashboard','dashboard_')" />
                <input type="button" id="dashboarddecrease_<? echo $i; ?>" name="dashboarddecrease[]" value="-" class="formbuttonplasminus" style="width:60px;" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_dashboard','dashboard_','')" />
            </td>
        </tr>
		<?
	}
	exit();
 }
?>