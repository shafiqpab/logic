<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
extract($_REQUEST);


if($action=="tna_task_list_view")
{
	if($data==0){echo "<td colspan='4' align='center'>Please Select Company</td>";die();}

	$sql="select TASK_NAME, TASK_SHORT_NAME,TASK_TYPE from LIB_TNA_TASK where STATUS_ACTIVE=1 and IS_DELETED=0";
	$resultArr = sql_select( $sql );
	foreach( $resultArr as $row ) 
	{
		$task_short_name_arr[$row['TASK_NAME']]=$row['TASK_SHORT_NAME'];
		$task_type_arr[$row['TASK_NAME']][$row['TASK_TYPE']]=$template_type_arr[$row['TASK_TYPE']];
	}//echo $data;

	$sql="select id,company_id,task_id,is_plan_manual,is_actual_manual, plan_user_id, actcual_user_id from tna_manual_permission where company_id=$data";
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$manual_status_arr[$row[csf('task_id')]]['plan']=$row[csf('is_plan_manual')];
		$manual_status_arr[$row[csf('task_id')]]['actual']=$row[csf('is_actual_manual')];
		$manual_status_arr[$row[csf('task_id')]]['plan_user_id']=$row[csf('plan_user_id')];
		$manual_status_arr[$row[csf('task_id')]]['actcual_user_id']=$row[csf('actcual_user_id')];
	}//echo $data;
// 	print_r($manual_status_arr);
// exit;
	$sql_new="select TNA_TASK_ID from TNA_TASK_TEMPLATE_DETAILS where STATUS_ACTIVE=1 and IS_DELETED=0
	union all select TASK_ID as TNA_TASK_ID from TNA_TASK_ENTRY_PERCENTAGE where STATUS_ACTIVE=1 and IS_DELETED=0";
	$results = sql_select( $sql_new );
	foreach( $results as $row ) 
	{
		$taskArr[$row['TNA_TASK_ID']]=$tna_task_name[$row['TNA_TASK_ID']];
	}//echo $data;
		
	$i=1;
	foreach($taskArr as $task_id=>$task_name){
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	$plan_status=($manual_status_arr[$task_id]['plan'])?$manual_status_arr[$task_id]['plan']:2;
	$actual_status=($manual_status_arr[$task_id]['actual'])?$manual_status_arr[$task_id]['actual']:2;
	$plan_user = ($manual_status_arr[$task_id]['plan_user_id'])?$manual_status_arr[$task_id]['plan_user_id']:'';
	$actcual_user = ($manual_status_arr[$task_id]['actcual_user_id'])?$manual_status_arr[$task_id]['actcual_user_id']:'';
    
	$plan_users = explode(",", $plan_user);
	$actcual_users = explode(",", $actcual_user);

	$plan_user_name = '';
	foreach($plan_users as $user_id){
		$user = sql_select("select USER_NAME,ID from user_passwd where ID=$user_id and valid=1");
		$plan_user_name .= $user[0]['USER_NAME'].',';
	}
	$actcual_user_name = '';
	foreach($actcual_users as $user_id){
		$user = sql_select("select USER_NAME,ID from user_passwd where ID=$user_id and valid=1");
		$actcual_user_name .= $user[0]['USER_NAME'].',';
	}
	 
	?> 
	<script>
		
	</script>
	<tr bgcolor="<? echo $bgcolor; ?>">
		<td align="center"><? echo $i;?></td>
		<td><? echo implode(',',$task_type_arr[$task_id]);?></td>
		<td title="Task ID: <? echo $task_id;?>">
		<input type="hidden" id="txt_tna_task_id_<? echo $task_id;?>" value="<? echo $task_id;?>"><? echo $task_name;?></td>
		<td><? echo $task_short_name_arr[$task_id];?></td>
		<td align="center">
			<? echo create_drop_down("cbo_plan_".$task_id, 45, array(1=>'Yes',2=>'No'), "", 0, "", $plan_status, "YesNo(this.value, $task_id, 'txt_plan_user_id_')"); ?>
		</td>
		<td align="center">
		    <input type="text" placeholder="Browse" <? if($plan_status==2) echo 'disabled'; ?> id="txt_plan_user_id_<?= $task_id; ?>" name="txt_plan_user_id" readonly class="text_boxes" style="width:60px" onDblClick="ManualUserPopUp(1, <?= $task_id?>, 'selected_plan_user_id_', 'txt_plan_user_id_')" value="<?= rtrim($plan_user_name, ',');?>"/>
			<input type="hidden" id="selected_plan_user_id_<?= $task_id; ?>" value="<?= $plan_user;?>"/>
		</td>
		<td align="center">
			<? echo create_drop_down( "cbo_actual_".$task_id, 45, array(1=>'Yes',2=>'No'),"", 0, "", $actual_status, "YesNo(this.value, $task_id, 'txt_actcual_user_id_')"); ?>
		</td>
		<td align="center">
		    <input type="text" placeholder="Browse" <? if($plan_status==2) echo 'disabled'; ?> id="txt_actcual_user_id_<?= $task_id; ?>" name="txt_actual_user_id" readonly class="text_boxes" style="width:60px" onDblClick="ManualUserPopUp(2, <?= $task_id?>, 'selected_actual_user_id_', 'txt_actcual_user_id_')" value="<?= rtrim($actcual_user_name, ',');?>"/>
			<input type="hidden" id="selected_actual_user_id_<?= $task_id; ?>" value="<?= $actcual_user;?>"/>
		</td>
	</tr>
	<? 
	$i++;
	} 
	?>			
    <td colspan="4" align="center" class="button_container">
        <? 
		$save_update=($result[0][csf('is_plan_manual')]=='')?0:1;
        echo load_submit_buttons($_SESSION['page_permission'], "fnc_tna_manual_permission", $save_update,0 ,"reset_form('tnamanualpermission_1','','')",1);
        ?> 
    </td>
<?
	die;	 
}



if($action=='openpopup_user_manual_permission'){
	extract($_REQUEST);
	echo load_html_head_contents("User Select", "../../", 1, 1, $unicode, '', '');
	?>
    <script>
	   var userId ='<?= $user_ids;?>';
	   var userName ='<?= $user_names;?>';

	    function check_all_data(user_id, user_name) 
		{
			allUserArr = user_id.split(',');
			allUserName = user_name.split(',');
			allUserArr.forEach((user_id, key) => {
				js_set_value(user_id, allUserName[key]);
			});
	    }

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var selected_id = new Array();
		var selected_name = new Array();
		function js_set_value(user_id, user_name) {
 
			user_id = user_id*1;
			toggle( document.getElementById( 'tr_' + user_id ), '#E9F3FF' );

			if( jQuery.inArray(user_id, selected_id ) == -1 ) {
				selected_id.push(user_id);
				selected_name.push(user_name);
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == user_id) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var idArr = Array();var nameArr = Array();
			for( var i = 0; i < selected_id.length; i++ ) {
				idArr.push(selected_id[i]);
				nameArr.push(selected_name[i]);
			}

			idStr = idArr.join(',');
			nameStr = nameArr.join(',');
 
			$('#txt_selected_user_id').val(idStr);
			$('#txt_selected_user_name').val(nameStr);
		}
	</script>
	</head>
	    <body>
		<?
		$sql_users = sql_select( "select USER_NAME,ID from user_passwd where valid=1 order by user_name ASC");
		?>
			<div align="center" style="width:100%;">
				<input type="hidden" id="txt_selected_user_id" name="txt_selected_user_id" value=""/>
				<input type="hidden" id="txt_selected_user_name" name="txt_selected_user_name" value=""/>
				<table width="220" cellspacing="0" class="rpt_table" border="0" rules="all"  id="tbl_list_search" >
					<thead>
						<th width="40">User ID</th>
						<th>User</th>
					</thead>
				</table>
				<div style="width:220px; max-height:220px; overflow-y:scroll;">
					<table cellspacing="0" width="200" class="rpt_table" border="0" rules="all" id="item_table2" align="left">
						<tbody>
						<?
						$i=1;
						$user_id_arr = array();
						$user_name_arr = array();
						foreach($sql_users as $key=>$user){
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$user_id_arr[$user['ID']] = $user['ID'];
							$user_name_arr[$user['USER_NAME']] = $user['USER_NAME'];
						?>
							<tr bgcolor="<?= $bgcolor;?>" onClick="js_set_value(<?= $user['ID'];?>,'<?= $user['USER_NAME'];?>')" id="tr_<?= $user['ID'];?>">
								<td width="40"><?= $user['ID']; ?></td>
								<td><?= $user['USER_NAME'] ?></td>
							</tr>
						<?
						$i++;
						}
						?>
						</tbody>
					</table>
				</div><br>
				<table cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td valign="bottom">
							<div style="width:100%">
								<div style="width:55%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?= implode(',',$user_id_arr);?>', '<?= implode(',',$user_name_arr);?>')"/> Check / Uncheck All
								</div>
								<div style="width:17%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
        </body>
	<script>
	if(userId !=''){
		check_all_data(userId, userName);
	}
	setFilterGrid('item_table2',-1);
	</script>
	</html>
	<?
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company=str_replace("'",'',$cbo_company);
	$data_string_arr=explode('*',$data_string);
	
	if ($operation==0)  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 
		$id=return_next_id( "id", "tna_manual_permission", 1 );
		$field_array="id, company_id, task_id, is_plan_manual, is_actual_manual, plan_user_id, actcual_user_id";
		$data_array=array();

		foreach($data_string_arr as $data_string){
			list($task_id,$plan_status,$plan_user_id,$actual_status, $actcual_user_id)=explode('_',$data_string);
			$data_array[]="(".$id.",".$cbo_company.",".$task_id.",".$plan_status.",".$actual_status.",'".$plan_user_id."','".$actcual_user_id."')";
			$id++;
		}

		$rID=sql_insert("tna_manual_permission",$field_array,implode(',',$data_array),1);
		
		if($rID)
		{
			oci_commit($con);
			echo "0**".str_replace("'", '', $id);
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		
		disconnect($con);
		die;
	}
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "tna_manual_permission", 1 ) ;
		$field_array="id, company_id,task_id,is_plan_manual, is_actual_manual, plan_user_id, actcual_user_id";
		$data_array=array();
		foreach($data_string_arr as $data_string){
			list($task_id,$plan_status,$plan_user_id,$actual_status, $actcual_user_id)=explode('_',$data_string);
			$data_array[]="(".$id.",".$cbo_company.",".$task_id.",".$plan_status.",".$actual_status.",'".$plan_user_id."','".$actcual_user_id."')";
			$id++;
		}
 
		$rID2=execute_query("DELETE FROM tna_manual_permission WHERE company_id=$cbo_company");
		$rID=sql_insert("tna_manual_permission",$field_array,implode(',',$data_array),1);
		 
		if($rID && $rID2)
		{
				oci_commit($con);
				echo "1**".str_replace("'", '', $id);
		}
		else
		{
				oci_rollback($con);
				echo "10**";
		}
		
		disconnect($con);
		die;
	}
	
	
	
	
}












?>

