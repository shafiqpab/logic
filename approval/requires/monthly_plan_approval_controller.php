<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
 	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

$locationSql = "select ID,COMPANY_ID,LOCATION_NAME from LIB_LOCATION where  IS_DELETED=0 and STATUS_ACTIVE=1";
$locationSqlRes = sql_select($locationSql);
foreach($locationSqlRes as $row){
	$lib_location_arr[$row['COMPANY_ID']][$row['ID']]= $row['LOCATION_NAME'];	
}

if($action == 'user_popup')
{
    echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
    ?>  
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
      	parent.emailwindow.hide();
      }
    </script>
    <form>
		<input type="hidden" id="selected_id" name="selected_id" /> 
		<?php
		$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');  
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=82 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
		//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. No", "100,120,130,120,50,","660","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}

if($action == 'load_drop_down_location'){
	echo create_drop_down( "cbo_location_id", 120, $lib_location_arr[$data],"", 1, "-- Location --", $selected, "" );  
	exit();
}


function getSequence($parameterArr=array()){
	$lib_location_str=implode(',',(array_keys($parameterArr['lib_location_arr'][$parameterArr['company_id']])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,LOCATION as LOCATION_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
 	//echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['LOCATION_ID']==''){$rows['LOCATION_ID']=$lib_location_str;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_location_str=implode(',',(array_keys($parameterArr['lib_location_arr'][$parameterArr['company_id']])));

	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,LOCATION as LOCATION_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

		if($rows['LOCATION_ID']==''){$rows['LOCATION_ID']=$lib_location_str;}

		$usersDataArr[$rows['USER_ID']]['LOCATION_ID']=explode(',',$rows['LOCATION_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['location_id'],$usersDataArr[$user_id]['LOCATION_ID']) &&  $bbtsRows['location_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_location_id = str_replace("'","",$cbo_location_id);
	$cbo_form_year = str_replace("'","",$cbo_form_year);
	$cbo_form_month = str_replace("'","",$cbo_form_month);
	$cbo_to_year = str_replace("'","",$cbo_to_year);
	$cbo_to_month = str_replace("'","",$cbo_to_month);
	$approval_type = str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	if($approval_type){$where_con .= " and a.APPROVE =".$approval_type.""; }
 	if($cbo_location_id){$where_con .= " and a.LOCATION_ID =".$cbo_location_id.""; }
	if($cbo_form_year && $cbo_to_year){
		 
		$where_con .= " and a.PLAN_YEAR BETWEEN '".$cbo_form_year."' AND '".$cbo_to_year."'";	
	}
	if($cbo_form_month && $cbo_to_month){
		 
		$where_con .= " and a.PLAN_MONTH BETWEEN '".$cbo_form_month."' AND '".$cbo_to_month."'";	
	}

	$electronicDataArr=getSequence(array('company_id'=>$company_name,'entry_form'=>82,'user_id'=>$user_id_approval,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_location_arr'=>$lib_location_arr));

	// echo $approval_type;die;

	// Un-Approve
	if($approval_type==0) 
	{
		$data_mas_sql = "SELECT a.ID, a.LOCATION_ID FROM PPL_SEWING_PLAN_BOARD_MONTH_MST a WHERE a.COMPANY_ID =$company_name AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.APPROVED <> 1 AND a.READY_TO_APPROVE = 1 $where_con";

		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				
				if($electronicDataArr['sequ_by'][$seq]['LOCATION_ID']==''){$electronicDataArr['sequ_by'][$seq]['LOCATION_ID'] = $electronicDataArr['user_by'][$user_id_approval]['LOCATION_ID'];}
				
				if( in_array($row['LOCATION_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['LOCATION_ID'])) )
				{
					if ($electronicDataArr['sequ_by'][$seq]['BYPASS'] == 1) {
						$tmp_sys_id_arr[$seq][$row['ID']] = $row['ID'];
					} else {
						$tmp_sys_id_arr[$seq][$row['ID']] = $row['ID'];
						break;
					}

				}
			}
		}

		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";} 
				$sql .= "SELECT a.ID,a.COMPANY_ID,a.PLAN_MONTH,a.PLAN_YEAR,a.LOCATION_ID,a.SAH,a.SAM,a.MP,a.AVG_SMV, a.PLAN_QNTY, a.TOTAL_DAYS, a.AVG_HOURS, a.AVG_EFFICIENCY, a.CM_VALUE,a.CM_COST, a.CM_VALUE,a.ORDER_VALUE FROM PPL_SEWING_PLAN_BOARD_MONTH_MST a where a.approved<>1 and a.APPROVED_SEQU_BY=$seq and  a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 $sys_con";
			}
		}
	}
	else
	{
		$sql="SELECT a.ID,a.COMPANY_ID,a.PLAN_MONTH,a.PLAN_YEAR,a.LOCATION_ID,a.SAH,a.SAM,a.MP,a.AVG_SMV,a.COMMENTS, a.PLAN_QNTY, a.TOTAL_DAYS, a.AVG_HOURS, a.AVG_EFFICIENCY, a.CM_VALUE,a.CM_COST, a.CM_VALUE,a.ORDER_VALUE FROM PPL_SEWING_PLAN_BOARD_MONTH_MST a, APPROVAL_MST b WHERE  a.id = b.mst_id AND a.COMPANY_ID =$company_name  AND a.is_deleted = 0 AND a.status_active = 1  and b.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=b.SEQUENCE_NO and b.ENTRY_FORM=82";	
		$sql = "SELECT a.ID,a.COMPANY_ID,a.PLAN_MONTH,a.PLAN_YEAR,a.LOCATION_ID,a.SAH,a.SAM,a.MP,a.AVG_SMV,a.COMMENTS, a.PLAN_QNTY, a.TOTAL_DAYS, a.AVG_HOURS, a.AVG_EFFICIENCY, a.CM_VALUE,a.CM_COST, a.CM_VALUE,a.ORDER_VALUE 
		FROM PPL_SEWING_PLAN_BOARD_MONTH_MST a, APPROVAL_MST b 
		WHERE a.id = b.mst_id
		AND a.is_deleted = 0 AND a.status_active = 1";
    }
	//echo $sql;die;

	$dataArray=sql_select($sql);
	// print_r($dataArray);die;
	$width=1250;
    ?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Monthly Plan Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<th width="25"></th>                   
                    <th width="50">Sl</th>
					<th width="80">Year</th>
					<th width="80">Month</th>
					<th width="80">SAH</th>
					<th width="80">SAM</th>    
					<th width="80">Plan Qty</th>
					<th width="80">Avg. SMV</th>
					<th width="80">Days</th>
					<th width="100">W. hours per day</th>
					<th width="100">Efficiency</th>
					<th width="80">Order Value</th>
					<th width="80">CM Cost</th>
					<th width="80">CM Value</th>
					<th width="130">Comments </th>
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:330px;" id="buyer_list_view">
                <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?		 
                        $i=1;

                        foreach ($dataArray as $row)
                        {                                                   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="25" align="center" valign="middle">
									<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
									<input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $row['ID']; ?>" />                                                  
									<input type="hidden" id="approval_id_<? echo $i;?>" name="approval_id[]"  value="<? echo $row[csf('approval_id')]; ?>" />
								</td> 
								<td width="50" align="center"><p><?= $i;?></p></td>
								<td width="80" align="center"><?= $row['PLAN_YEAR'];?></td>
								<td width="80" align="center"><?= $months[$row['PLAN_MONTH']]; ?></td> 
								<td width="80" align="right"><?= $row['SAH']; ?></td>
								<td width="80" align="right"><?= $row['SAM']; ?></td>               
								<td width="80" align="right"><?= $row['PLAN_QNTY']; ?></td>
								<td width="80" align="right"><?= $row['AVG_SMV']; ?></td>
								<td width="80" align="right"><?= $row['TOTAL_DAYS']; ?></td>
								<td width="100" align="right"><?= $row['AVG_HOURS']; ?></td>
								<td width="100" align="right"><?= $row['AVG_EFFICIENCY']; ?></td>
								<td width="80" align="right"><?= $row['ORDER_VALUE']; ?></td>
								<td width="80" align="right"><?= $row['CM_COST']; ?></td>
								<td width="80" align="right"><?= $row['CM_VALUE']; ?></td>
								<td width="130" align="right">
								    <input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" style="width:120px;" class="text_boxes" value="<? echo $row[csf("comments")]; ?>" placeholder="Single Click" onClick="fnc_comments(this.id, this.value)" readonly>
								</td>        
							</tr>
							<?
							$i++;
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="38" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <!-- <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="< ?= $approval_type; ?>"> -->
                    </td>
                    <td align="left">
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>)"/>
						<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
                    </td>
				</tfoot>
			</table>
            </div>
        </fieldset>
    </form>
	<?
	exit();	
}



if($action=="comments_popup")
{
	echo load_html_head_contents("Comments Info", "../../", 1, 1,'','','');
	extract($_REQUEST); 
	$comments_data = $comments_data;
?>
</head>
<body>
<div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_comments" id="txt_comments" class="text_area" style="width:385px; height:120px;"><?= $comments_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_location_id = str_replace("'","",$cbo_location_id);
	$cbo_form_year = str_replace("'","",$cbo_form_year);
	$cbo_form_month = str_replace("'","",$cbo_form_month);
	$cbo_to_year = str_replace("'","",$cbo_to_year);
	$cbo_to_month = str_replace("'","",$cbo_to_month);
	$approval_type = str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$target_ids=str_replace("'","",$target_id_str);
	$comment=str_replace("'","",$comments);

	 
	// target_id_arr
	//............................................................................
 
	$approved_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	
	$sql = "select a.ID, a.LOCATION_ID from PPL_SEWING_PLAN_BOARD_MONTH_MST a where a.COMPANY_ID=$company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  AND a.READY_TO_APPROVE = 1 and a.id in($target_ids)";

	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('location_id'=>$row['LOCATION_ID']);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$company_name,'entry_form'=>82,'lib_location_arr'=>$lib_location_arr,'match_data'=>$matchDataArr));

	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$approved_user_id];
	//$txt_comments = 'txt_comments_'.$target_ids;

	//print_r($approval_type);die;
 
	if($approval_type==5)
	{
		$rID1=sql_multirow_update("PPL_SEWING_PLAN_BOARD_MONTH_MST","approved*ready_to_approve*APPROVED_SEQU_BY",'2*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=82 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=82 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';
	} 
	else if($approval_type==0)
	{      
 		
		$max_approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=82 and mst_id in($target_ids)  and APPROVED=1 and APPROVED_BY=$approved_user_id group by mst_id", "mst_id", "approved_no"  );
		
		$id = return_next_id( "id","approval_mst", 1 );
		$ahid = return_next_id( "id","approval_history", 1 );
		
		$target_app_id_arr = explode(',',$target_ids);
        foreach($target_app_id_arr as $mst_id)
        {		
			
			// mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$approved_user_id.",'".$pc_date_time."'".",'".$comment."'")); 
			
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",82,".$mst_id.",".$user_sequence_no.",".$approved_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",82,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$approved_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
			$ahid++;

        }
	 
        $flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_BY*APPROVED_DATE*COMMENTS"; 
			$rID2=execute_query(bulk_update_sql_statement( "PPL_SEWING_PLAN_BOARD_MONTH_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=82 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		if($flag==1) $msg='19'; else $msg='21';

	}
	else
	{   
		$max_approved_no_arr=return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where entry_form=82 and mst_id in($target_ids)  and APPROVED=0 and APPROVED_BY=$approved_user_id group by mst_id", "mst_id", "approved_no");
		$ahid=return_next_id( "id","approval_history", 1 ) ;
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=82 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}
		$comment_arr = explode(',',$comment);
        foreach($comment_arr as $comts)
        {		 
			$comts = explode("_", $comts);
			$comts_data = $comts[0];
			//echo $comts_data;die;
			$mst_id =$comts[1];
			$query = "UPDATE PPL_SEWING_PLAN_BOARD_MONTH_MST  
					SET approved = 0, ready_to_approve=0 , APPROVED_SEQU_BY=0, COMMENTS='$comts_data'
					WHERE id = $mst_id";
			$rID1=execute_query($query,1);
        }
		//$rID1=sql_multirow_update("PPL_SEWING_PLAN_BOARD_MONTH_MST","approved*ready_to_approve*APPROVED_SEQU_BY*COMMENTS","0*0*0*'.$comment.'","id",$target_ids,0);
		
		if($rID1) $flag=1; else $flag=0;

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=82 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=82 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;

		}
		$response=$target_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}

	if($flag==1)
	{
		oci_commit($con);
		echo $msg."**".$response;
	}
	else
	{
		oci_rollback($con);
		echo $msg."**".$response;
	}
 
	disconnect($con);
	die;
}
?>
 
