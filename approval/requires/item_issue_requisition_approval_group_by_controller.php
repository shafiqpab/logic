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

if($action=='user_popup')
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
			$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;

			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and a.id!=$user_id  and b.is_deleted=0 and b.entry_form=26 order by sequence_no";
            //echo $sql;
		
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq,Group", "100,120,150,150,50,50,","670","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO,GROUP_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
			?>
	        
	</form>
	<?
}


function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_brand_id_string=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));
	$lib_store_id_string=implode(',',(array_keys($parameterArr['lib_store_arr']))); 
	$product_dept_id_string=implode(',',(array_keys($parameterArr['product_dept_arr'])));
	
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID,IS_DATA_LEVEL_SECURED,STORE_LOCATION_ID as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]=$rows;
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_CATE_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		$rows['STORE_ID']=$userDataArr[$rows['USER_ID']]['STORE_ID'];
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_id_string;}
		if($rows['ITEM_CATE_ID']==''){$rows['ITEM_CATE_ID']=$lib_item_cat_id_string;}
		if($rows['STORE_ID']==''){$rows['STORE_ID']=$lib_store_id_string;}
		if($rows['DEPARTMENT']==''){$rows['DEPARTMENT']=$product_dept_id_string;}
		
        $dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];
	}
	
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$lib_item_cat_arr=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));
	$lib_store_arr=implode(',',(array_keys($parameterArr['lib_store_arr'])));
	$product_dept_arr=implode(',',(array_keys($parameterArr['product_dept_arr'])));
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['STORE_ID']=$rows['STORE_ID'];
	}
 
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		$userDataArr[$rows['USER_ID']]['BUYER_ID'] = $rows['BUYER_ID'];
		$userDataArr[$rows['USER_ID']]['BRAND_ID'] = $rows['BRAND_ID'];
		$userDataArr[$rows['USER_ID']]['DEPARTMENT'] = $rows['DEPARTMENT'];
		$userDataArr[$rows['USER_ID']]['ITEM_ID'] = $rows['ITEM_ID'];
		
		
		
		if($userDataArr[$rows['USER_ID']]['BUYER_ID']==''){
			$userDataArr[$rows['USER_ID']]['BUYER_ID']=$lib_buyer_arr;
		}
		if($userDataArr[$rows['USER_ID']]['ITEM_ID']==''){
			$userDataArr[$rows['USER_ID']]['ITEM_ID']=$lib_item_cat_arr;
		}
		if($userDataArr[$rows['USER_ID']]['STORE_ID']==''){
			$userDataArr[$rows['USER_ID']]['STORE_ID']=$lib_store_arr;
		}
		if($userDataArr[$rows['USER_ID']]['DEPARTMENT']==''){
			$userDataArr[$rows['USER_ID']]['DEPARTMENT']=$product_dept_arr;
		}
		
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['ITEM_ID']=explode(',',$userDataArr[$rows['USER_ID']]['ITEM_ID']);
		$usersDataArr[$rows['USER_ID']]['STORE_ID']=explode(',',$userDataArr[$rows['USER_ID']]['STORE_ID']);
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$userDataArr[$rows['USER_ID']]['DEPARTMENT']);
		

		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	
	 //print_r($parameterArr['match_data']);die;
 


	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				in_array($bbtsRows['store_id'],$usersDataArr[$user_id]['STORE_ID'])
				&& (in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT'])  || $bbtsRows['department']==0)
				&& $bbtsRows['store_id']>0
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}



$lib_department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
$lib_store_arr = return_library_array("select id, STORE_NAME from LIB_STORE_LOCATION where IS_DELETED=0 and STATUS_ACTIVE=1 and COMPANY_ID=$cbo_company_name", "id", "STORE_NAME");



if($action=="report_generate")
{   	
    ?>
		<script>
			function openmypage_reqdetails(requ_id,requ_no)
			{
				var data=requ_id+"**"+requ_no;
				var title = 'Requisition Details Info';
				var page_link = 'requires/item_issue_requisition_approval_group_by_controller.php?data='+data+'&action=reqdetails_popup';
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','');
				emailwindow.onclose=function()
				{
					/*var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
					$('#txt_appv_cause_'+i).val(appv_cause.value);*/
				}
			}
		</script>
	<?

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category_id);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_requsition_no=str_replace("'","",$txt_requsition_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$previous_approved=str_replace("'","",$previous_approved);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	

	
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
		$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
		$where_condition = " and a.indent_date between '$txt_date_from' and '$txt_date_to'";
	}

	if ($cbo_req_year != 0) $where_condition.= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
	if(trim($txt_requsition_no) != ""){ $where_condition .= " and a.ITEMISSUE_REQ_SYS_ID like ('%$txt_requsition_no') ";}
	if ($cbo_item_category != 0) $where_condition.= " and c.ITEM_CATEGORY_ID=$cbo_item_category";
	
	
	$electronicDataArr = getSequence(array('company_id'=>$company_name,'entry_form'=>26,'user_id'=>$user_id_approval,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'product_dept_arr'=>$lib_department_arr,'lib_item_cat_arr'=>$item_category,'lib_store_arr'=>$lib_store_arr));

    $my_seq = $electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$user_id_approval]['GROUP_NO'];
    $my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
    $electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

    $my_previous_bypass_no_seq = 0;
    rsort($electronicDataArr['bypass_seq_arr'][2]);
    foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
        if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
    }
	
 //var_dump($electronicDataArr[seq_store_id][5]);die;
	
	
	if($approval_type==0)// Un-Approve
	{
		//Match data..................................
		if($electronicDataArr['user_by'][$user_id_approval]['STORE_ID']){
			$where_condition .= " and a.STORE_ID in(".$electronicDataArr['user_by'][$user_id_approval]['STORE_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['STORE_ID']=$electronicDataArr['user_by'][$user_id_approval]['STORE_ID'];
		}

		if($electronicDataArr['user_by'][$user_id_approval]['DEPARTMENT']){
			$where_condition .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$user_id_approval]['DEPARTMENT'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT']=$electronicDataArr['user_by'][$user_id_approval]['DEPARTMENT'];
		}

		$data_mast_sql = "SELECT a.ID, a.DEPARTMENT_ID, a.STORE_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY, c.ITEM_CATEGORY_ID from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_name and a.ready_to_approved=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_approved<>1 and a.READY_TO_APPROVED=1 $where_condition  group by a.ID, a.DEPARTMENT_ID, a.STORE_ID, c.ITEM_CATEGORY_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY ";
	 	 //echo $data_mast_sql;die;

        $tmp_sys_id_arr=array();$sys_data_arr=array();
		$data_mas_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mas_sql_res as $row)
		{ 	// echo $my_group.'='.$row['APPROVED_GROUP_BY'];die;
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){

				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					if($seq<$my_seq){ 
						if(
							(in_array($row['DEPARTMENT_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['DEPARTMENT'])) || $row['DEPARTMENT']==0)  && (in_array($row['STORE_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['STORE_ID'])) || $row['STORE_ID']==0)  && ($row['APPROVED_GROUP_BY'] <= $group)
							)
						{ 
						
							if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
							}
							else{
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
								if(in_array($my_previous_bypass_no_seq,$electronicDataArr['group_seq_arr'][$my_group]) && $row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq){
									unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								}
								break 2; break; 
							}
							
							//This condition user for only 1 parson approve from group if all user can pass yes. but those are mendatory if found can pass no. 

							if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq ) ){ 
								unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								break; 
							}

							$group_stage_arr[$row['ID']][$group] = $group;
						}
					}
				}
			 
			}//group loof;
			foreach($group_stage_arr as $sys_id => $gidArr){ 
				foreach($gidArr as $gid => $gid){	
					if(count($group_stage_arr[$sys_id])>1 && array_key_first($group_stage_arr[$sys_id]) != $gid ){
						unset($tmp_sys_id_arr[$gid]);
					}
				}
			}
		 }
	 	//..........................................Match data;	

		//print_r($tmp_sys_id_arr);die;


        $sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){

				//print_r($electronicDataArr['group_seq_arr'][$group]);die;
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
                    $sql .= "SELECT a.ID, a.DEPARTMENT_ID,a.ITEMISSUE_REQ_SYS_ID, a.INDENT_DATE, a.REQUIRED_DATE, a.ITEMISSUE_REQ_SYS_ID, a.COMPANY_ID, a.INSERTED_BY, a.STORE_ID, a.IS_APPROVED,listagg(c.item_category_id,',') within group (order by c.item_category_id) as ITEM_CATEGORY_ID
                    from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
                    where a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_name and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.READY_TO_APPROVED=1 and a.IS_APPROVED<>1  and a.APPROVED_SEQU_BY=$seq and a.APPROVED_GROUP_BY=$group $sys_con group by a.id,a.DEPARTMENT_ID, a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved";
				}
		
			}
		}

	}
	else{
		$sql = "select a.ID, a.DEPARTMENT_ID,a.ITEMISSUE_REQ_SYS_ID, a.INDENT_DATE, a.REQUIRED_DATE, a.ITEMISSUE_REQ_SYS_ID, a.COMPANY_ID, a.INSERTED_BY, a.STORE_ID, a.IS_APPROVED,listagg(c.item_category_id,',') within group (order by c.item_category_id) as ITEM_CATEGORY_ID from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c,APPROVAL_MST d where a.id=b.mst_id and b.product_id=c.id  and d.mst_id=a.id   and a.company_id=$company_name  and a.ready_to_approved=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.APPROVED_GROUP_BY=d.GROUP_NO and d.GROUP_NO={$electronicDataArr['user_by'][$user_id_approval]['GROUP_NO']} and d.ENTRY_FORM=26 $where_condition group by a.id,a.DEPARTMENT_ID, a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved  order by a.id, a.itemissue_req_prefix_num";
	}
	
 	 //echo $sql;die;
    
	$nameArray=sql_select( $sql );
	$sys_id_arr=array();
	foreach ($nameArray as $row)
	{
	   $sys_id_arr[$row['ID']]=$row['ID'];
	}

	// $sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=26 ".where_con_using_array($sys_id_arr,0,'mst_id')." order by id asc";
    // //echo $sql_cause;	
	// $nameArray_cause=sql_select($sql_cause);
	// $app_cause_arr=array();
	// foreach($nameArray_cause as $row)
	// {
	// 	$app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	// }

    	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =156 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);

		
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$sql_user_res=sql_select("select id, user_full_name, department_id from user_passwd");
	foreach ($sql_user_res as $row) {
		$user_arr['user_full_name'][$row[csf('id')]]=$row[csf('user_full_name')];
		$user_arr['department_id'][$row[csf('id')]]=$department_arr[$row[csf('department_id')]];
	}
	
 	?>
      <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1130px; margin-top:10px">
        <legend>Item Issue Requisition Approval Group By</legend>	
        <div align="center" style="width:100%;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130px" class="rpt_table" align="left">
                <thead>
                	<th width="30"></th>
                    <th width="35">SL</th>
                    <th width="100">Requisition No</th>
                    <th width="160">Item Category</th>
                    <th width="120">Department</th>
                    <th width="120">Insert User</th>
                    <th width="100">Indent Date</th>
                    <th width="120">Req Date</th>
					<th width="140">Not Appv. Cause </th>
					<th >&nbsp;</th>
                </thead>
            </table>  

            <div style="width:1130px; overflow-y:scroll; overflow-x: hidden; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130px" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
						 //echo $sql;
                            $i=1; $j=0; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								$value='';
								if($approval_type==0){ $value=$row[csf('id')];}
								else{$value=$row[csf('id')]."**".$row[csf('approval_id')];}

								$variable='';
								if($format_ids[$j]==78) // Print 
                                {
                                	$type=1;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row['ITEMISSUE_REQ_SYS_ID']." <a/>";

                                }
                               	elseif($format_ids[$j]==66) // Print 2 
                                {
                                    $type=2;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row['ITEMISSUE_REQ_SYS_ID']." <a/>";
                                }
                                elseif($format_ids[$j]==85) // Print 3
                                {	
                                	$type=3;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row['ITEMISSUE_REQ_SYS_ID']." <a/>";
                                }
								else {
									$variable=$row['ITEMISSUE_REQ_SYS_ID'];
								}
								
								$temp_item_name_arr=array();
								foreach(explode(',',$row['ITEM_CATEGORY_ID']) as $catid){
								
									$temp_item_name_arr[$catid]=$item_category[$catid];
								}
								
								
								?>
									<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
										<td width="30" align="center" valign="middle">
											<input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]"/>
											<input id="req_id_<?= $i;?>" name="req_id[]" type="hidden" style="width:30px;" value="<?= $value; ?>" />
                                        	<input id="requisition_id_<?= $i;?>" name="requisition_id[]" type="hidden" value="<?= $row[csf('id')]; ?>" />
										</td> 
                                         <td width="35" align="center"><?= $i; ?></td>
                                        <td width="100" align="center"><?= $variable; ?></td>
										<td width="160"><p><?= implode(',',$temp_item_name_arr);?></p></td>
										<td width="120"><?= $department_arr[$row['DEPARTMENT_ID']]; ?></td>
                                        
                                        <td width="120"><?= $user_arr['user_full_name'][$row[csf('inserted_by')]]; ?></td>
									
										<td width="100" align="center"><? if($row[csf('indent_date')]!="0000-00-00") echo change_date_format($row[csf('indent_date')]); ?></td>
							
										<td width="120" align="center"><? if($row[csf('required_date')]!="0000-00-00") echo change_date_format($row[csf('required_date')]); ?></td>

										<td width="100" style="cursor:pointer;">
											<input name="comments_[]" class="text_boxes" readonly placeholder="Please Browse" id="comments_<?= $row['ID'];?>" onClick="openmypage_refusing_cause('requires/item_issue_requisition_approval_group_by_controller.php?action=refusing_cause_popup','Comments','<?= $row['ID'];?>');" value="">
										</td>	

										<td   align="center"><input type="button" class="formbutton" id="reqdtls_<? echo $i;?>" style="width:100px" value="Req. Details" onClick="openmypage_reqdetails(<? echo $row[ID]; ?>, '<? echo $row['ITEMISSUE_REQ_SYS_ID'];?>')"/></td>
									</tr>
									<?
									$i++;
								}
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							
                        ?>
                    </tbody>
                </table>
            </div>

            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1130px" class="rpt_table" align="left">
				<tfoot>
                    <td width="30" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><?= $all_approval_id; ?></font>
                    </td>
					<td align="left">
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>

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

if ($action=="approve")
{
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$approval_type = str_replace("'","",$approval_type);
	$requisition_ids = str_replace("'","",$requisition_ids);
	$comments = str_replace("'","",$comments);
	$requisition_id_arr = explode(',',$requisition_ids);
	$comments_arr = explode('**',$comments);
	$cbo_company_name = str_replace("'","",$cbo_company_name);

	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


	$sql="SELECT a.ID,a.STORE_ID, a.DEPARTMENT_ID,c.ITEM_CATEGORY_ID,a.IS_APPROVED
			from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_name and a.ready_to_approved=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_approved<>1 and a.READY_TO_APPROVED=1 and a.id in($requisition_ids) group by a.ID,a.STORE_ID, a.DEPARTMENT_ID,c.ITEM_CATEGORY_ID";
		//	echo $sql;die;
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('store_id'=>$row['STORE_ID'],'item_id'=>$row['ITEM_CATEGORY_ID'],'department'=>$row['DEPARTMENT_ID']);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	//print_r($matchDataArr);die;
	
	//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'entry_form'=>26,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_category,'lib_store_arr'=>$lib_store_arr,'product_dept_arr'=>$lib_department_arr,'match_data'=>$matchDataArr));

	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=7 group by mst_id","mst_id","approved_no");
	
	//---------------------
	
	if($approval_type==0)
	{
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$hmst_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($requisition_id_arr as $key => $mst_id)
		{		
			
			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;
			
			
			$approved_no = $max_approved_no_arr[$mst_id];
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
				$approved_no_array[$mst_id]=$approved_no;
			}
			
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",26,".$mst_id.",".$user_sequence_no.",".$user_group_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$id=$id+1;
			
			

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$hmst_id.",26,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$comments_arr[$key]."','".$approved."')";
			$hmst_id=$hmst_id+1;
			
			//Mst data.......................
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$user_id_approval.",".$user_group_no."")); 
		}

		
		$flag=1;

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip,APPROVED";
			$rID=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; 
		}
		
		

		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_DATE*APPROVED_BY*APPROVED_GROUP_BY"; 
			$rID1=execute_query(bulk_update_sql_statement( "inv_item_issue_requisition_mst", "id", $field_array_up, $data_array_up, $requisition_id_arr ));
			if($rID1) $flag=1; else $flag=0; 
		}
	
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($requisition_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=10;
		}
		

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,COMMENTS,APPROVED";
		$rID3=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID3) $flag=1; else $flag=0;
		}
		
		//----------------------------------------
		
		 //echo "21**$flag,".$rID.",".$rID1.",".$rID2.",".$rID3;oci_rollback($con); die;

		if($flag==1) $msg=19; else $msg=21;
	 
		
	}
	else if($approval_type==5)
	{ //echo 1;
		
		$hmst_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($requisition_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$hmst_id.",26,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$comments_arr[$key]."','2')";
			$hmst_id=$hmst_id+1;
		}
		
		
		$flag = 1;
		if($flag==1)
		{
			$rID1=sql_multirow_update("inv_item_issue_requisition_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'2*0*0*0',"id",$requisition_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,COMMENTS,APPROVED";
			$rID2=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($requisition_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=10;
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=26 and mst_id in ($requisition_ids)";
			$rID4=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}

		 //echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$requisition_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else{
		
		$hmst_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($requisition_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$hmst_id.",26,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$comments_arr[$key]."','0')";
			$hmst_id=$hmst_id+1;
		}
		
		
		$flag = 1;
		if($flag==1)
		{
			$rID1=sql_multirow_update("inv_item_issue_requisition_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'0*0*0*0',"id",$requisition_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,COMMENTS,APPROVED";
			$rID2=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($requisition_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=10;
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=26 and mst_id in ($requisition_ids)";
			$rID4=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}

		 //echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$requisition_ids;
	
		if($flag==1) $msg=20; else $msg=22;	
	}


	
	if($flag==1)
	{
		oci_commit($con); 
		echo $msg."**".$requisition_ids;
	}
	else
	{
		oci_rollback($con); 
		echo $msg."**".$requisition_ids;
	}
	disconnect($con);
	die;
	
}

if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Comments Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	$app_user_id = ($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	$sql_cause="SELECT MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=26 and mst_id='$quo_id' and INSERTED_BY=$app_user_id order by id asc";	
	$nameArray_cause=sql_select($sql_cause);
	$app_cause_arr=array();
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	}
	$btn_status=(count($app_cause_arr)==0)?0:1;
 
	?>
    <script>
 	var permission='<?=$permission; ?>';

	function set_values( cause )
	{
		var refusing_cause = document.getElementById('txt_refusing_cause').value;
		// alert(refusing_cause); return;
		if(refusing_cause == '')
		{
			// alert(refusing_cause); return;
			// document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			parent.emailwindow.hide();
			// alert("Please save Comments first or empty");
			// return;
		}
	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Comments')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id+"&app_user_id="+<?=$app_user_id;?>;
			http.open("POST","item_issue_requisition_approval_group_by_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				alert("Data saved successfully");
				// parent.emailwindow.hide();
			}
			else if(response[0]==1)
			{
				alert("Data update successfully");
				// parent.emailwindow.hide();
			}
			else
			{
				alert("Data not saved");
				return;
			}
		}
	}

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:470px;">
		<legend>Comments</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Comments</td>
					<td >
						<textarea name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;"><?=$app_cause_arr[$quo_id];?></textarea>
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<?= $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
						 
							echo load_submit_buttons( $permission, "fnc_cause_info", $btn_status,0 ,"reset_form('causeinfo_1','','')",1);
						 
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center"></td>
				</tr>
		   </table>
			</form>
		</fieldset>


		<!-- <div>
			<?
			//$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=26 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
			//$sqlHisRes=sql_select($sqlHis);
			?>
			<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th>Refusing History</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
				<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
				<?
				//$i=1;
				//foreach($sqlHisRes as $hrow)
				//{
					//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
						<td width="30"><?=$i; ?></td>
						<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
					</tr>
					<?
					//$i++;
				//}
				?>
				</table>
			</div>
		</div> -->


	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}
if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	$flag=1;
	
	// if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=26 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	// {
	// 	//
	// }
	// else
	// {
	// 	$con = connect();
	// 	if($db_type==0)
	// 	{
	// 		mysql_query("BEGIN");
	// 	}
	// 	//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
	// 	$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=26 group by mst_id","id");
	// 	$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
	// 			select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=26 and id=$idpre"; //die;
		
	// 	if(count($sqlHis)>0)
	// 	{
	// 		$rID3=execute_query($sqlHis,0);
	// 		if($flag==1)
	// 		{
	// 			if($rID3==1) $flag=1; else $flag=0;
	// 		}
	// 	}
	// }
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",26,".$quo_id.",'".$refusing_cause."',".$app_user_id.",'".$pc_date_time."')";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause**$quo_id";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();

		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=26 group by mst_id","id");
		$field_array="refusing_reason*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$app_user_id."*'".$pc_date_time."'";
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$idpre,0);
		
		if($rID==1) $flag=1; else $flag=0;
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**$refusing_cause**$quo_id";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="reqdetails_popup")
{ 
	echo load_html_head_contents("Requ. Details","../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$ex_data=explode("**",$data);
	$item_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');

	$sql="SELECT a.id, b.id as dtls_id, b.req_qty,b.current_stock, b.product_id, c.item_category_id, c.item_account, c.item_description, c.item_size, c.item_group_id, c.sub_group_name, c.order_uom as unit_of_measure
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c 
	where a.id=b.mst_id and b.product_id=c.id  and a.id=$ex_data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	// echo $sql;die();
	$sql_res=sql_select($sql);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function js_set_value()
		{
			parent.emailwindow.hide(); 
		}

		function fn_requisition_details_qtyupdate(operation)
		{
			var tot_row=$('#tbl_details tbody tr').length;
			var data_all="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtqty_'+i,'req_qty')==false)
				{
					return;
				}
				data_all = data_all+get_submitted_data_string('txtqty_'+i+'*req_dtls_id_'+i,"../");				
			}

			var data="action=save_update_delete_requ_qty&tot_row="+tot_row+data_all;
			//alert (data);//return;
			freeze_window(operation);
			http.open("POST","item_issue_requisition_approval_group_by_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fn_requisition_details_qtyupdate_reponse;					
		}

		function fn_requisition_details_qtyupdate_reponse()
		{
			if(http.readyState == 4)
			{				
				var reponse=http.responseText.split('**');
				if (reponse[0]==1) alert("Data is updated Successfully");
				else alert("Data is not updated Successfully");
				release_freezing();
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../",$permission,1); ?>
    	<fieldset style="width:970px; margin-top:10px;">
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
        	<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" align="center" id="tbl_details">
                <thead>
                    <tr>
                    	<th width="100">Item Account</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Item Group</th>
	                    <th width="100">Item Sub. Group</th>
	                    <th width="150">Item Description</th>
	                    <th width="100">Item Size</th>
	                    <th width="60">Order UOM</th>
	                    <th class="must_entry_caption" title="Must Entry Field." width="80"> <font color="blue">Quantity</font></th>
	                   
	                    <th width="50">Stock</th>
                	</tr>
            	</thead>               
                <tbody>
                	<?
                	$i=1;
                	$bgcolor="#E9F3FF";
                	foreach ($sql_res as $row) 
                	{                		
	                	?>
	                    <tr bgcolor="<?= $bgcolor; ?>">
	                    	 <input type="hidden" name="req_dtls_id[]" id="req_dtls_id_<?= $i; ?>" value="<?= $row[csf('dtls_id')]; ?>">
	                        <td><?= $row[csf('item_account')]; ?></td>
	                        <td><?= $item_category[$row[csf('item_category_id')]]; ?></td>
	                        <td><?=  $item_arr[$row[csf("item_group_id")]]; ?></td>
							
	                        <td><?= $row[csf('sub_group_name')]; ?></td>
	                        <td><?= $row[csf('item_description')]; ?></td>
	                        <td><?= $row[csf('item_size')]; ?></td>
	                        <td><?= $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
	                        <td align="right"><input type="text" name="txtqty[]" id="txtqty_<?= $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('req_qty')]; ?>" /></td>
							
							<td align="right"><?= number_format($row[csf('current_stock')],2); ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                }
	                ?>    
                </tbody>
            </table>

                <table width="100%">
                	<tr>
                        <td colspan="22" height="20" valign="middle" align="center" class="button_container"> 
                            <input type="button" class="formbutton" id="updateqtyid" name="updateqtyid" value="Update" onClick="fn_requisition_details_qtyupdate(1)" style="width:80px" />                           
                        </td>    
                    </tr>
                </table>
        </form>
	    </fieldset>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
}

if ($action=="save_update_delete_requ_qty")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	for ($i=1; $i<=$tot_row; $i++)
    {
		$req_dtls_id = "req_dtls_id_".$i;
		if(str_replace("'","",$$req_dtls_id)) $all_req_dtls_ids[str_replace("'","",$$req_dtls_id)]=str_replace("'","",$$req_dtls_id);
	}
	
	// if(count($all_req_dtls_ids)>0)
	// {
	// 	$requisition_arr=return_library_array( "select id, rate from inv_itemissue_requisition_dtls where id in(".implode(",",$all_req_dtls_ids).") ", 'id', 'rate' );
	// }
	//echo "10**<pre>";print_r($requisition_arr);oci_rollback($con);disconnect($con);die;
	$field_array_up = "req_qty*updated_by*update_date";
	for ($i=1; $i<=$tot_row; $i++)
    {
		$txtqty = "txtqty_".$i;
		$req_dtls_id = "req_dtls_id_".$i;
		//$amount=str_replace("'",'',$$txtqty)*$requisition_arr[str_replace("'",'',$$req_dtls_id)];
		$updateID_array[] = str_replace("'",'',$$req_dtls_id);
		$data_array_up[str_replace("'",'',$$req_dtls_id)] = explode("*",("".$$txtqty."*".$user_id."*'".$pc_date_time."'"));	
	}

	// echo bulk_update_sql_statement("inv_itemissue_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array);
	$dtlsrID=execute_query(bulk_update_sql_statement("inv_itemissue_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);

	if($db_type==0)
	{
		if($dtlsrID)
		{
			mysql_query("COMMIT");
			echo "1**";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**";
		}
	}
	if($db_type==2 || $db_type==1)
	{
	    if($dtlsrID)
		{
			oci_commit($con);
			echo "1**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
	}
	disconnect($con);
	die;
}

if($action=='app_mail_notification'){
	//http://localhost/platform-v3.5/approval/requires/erosion_approval_controller.php?data=20__reza@logicsoftbd.com__3&action=app_mail_notification

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	

	list($sys_id,$email,$alter_user_id,$company_name,$type)=explode('__',$data);
	$approved_user_id=($alter_user_id!='')?$alter_user_id:$user_id;

		
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");

		$sql = "select a.ID, a.DEPARTMENT_ID,a.ITEMISSUE_REQ_SYS_ID, a.INDENT_DATE, a.REQUIRED_DATE, a.ITEMISSUE_REQ_SYS_ID, a.COMPANY_ID, a.INSERTED_BY, a.STORE_ID,d.REFUSING_REASON, a.IS_APPROVED,listagg(c.item_category_id,',') within group (order by c.item_category_id) as ITEM_CATEGORY_ID from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, refusing_cause_history d where 
		a.id=b.mst_id and a.id =d.mst_id and b.product_id=c.id   and a.company_id=$company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and   a.id in($sys_id) group by a.id,a.DEPARTMENT_ID, a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,d.REFUSING_REASON,a.is_approved  order by a.id, a.itemissue_req_prefix_num";

		//echo $sql;die();
 

		$dataArr=sql_select($sql);
		

		//echo $sql;die;
	

		
		ob_start();
	?>
	

			<p>
				<?
				if($type==5){
					echo "Dear Sir, <br>
					Your request has been rejected.  <br>
					Check the below comments and take action for approval";
				}
				else{
					echo "Dear Sir, <br>
					Please check below erosion request for your electronic approval <br>
					Click the below link to approve or reject with comments";
				}

				?>

			</p>
		  <table border="1" rules="all">

			<tbody>


		  <?

				foreach ($dataArr as $row) 
				{  
					//$precost_data_arr=get_precost_data(array('company_id'=>$row[csf("COMPANY_ID")],'job_no'=>$row['JOB_NO'],'po_id'=>$row['PO_BREAK_DOWN_ID']));
					$temp_item_name_arr=array();
					foreach(explode(',',$row[ITEM_CATEGORY_ID]) as $catid){
								
					$temp_item_name_arr[$catid]=$item_category[$catid];
						}
			     
				?>
					<tr>
						<td>Company Name</td>
						<td><? echo $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
					</tr>
					<tr>
						<td>Requisition No</td>
						<td><? echo $row[csf('ITEMISSUE_REQ_SYS_ID')];?></td>
					</tr>
					<tr>
						<td>Item Category</td>
						<td><p><?= implode(',',$temp_item_name_arr);?></p></td>
					</tr>
					<tr>
						<td>Department</td>
						<td><?= $department_arr[$row[DEPARTMENT_ID]]; ?></td>
					</tr>
					<tr>
						<td>Indent Date</td>
						<td align="center"><? if($row[csf('indent_date')]!="0000-00-00") echo change_date_format($row[csf('indent_date')]); ?></td>
					</tr>
					<tr>
						<td>Req Date</td>
						<td  align="center"><? if($row[csf('required_date')]!="0000-00-00") echo change_date_format($row[csf('required_date')]); ?></td>
					</tr>
					<tr>
						<td>Comments</td>
						<td><? echo $row[csf('REFUSING_REASON')];?></td>
					</tr>
				
				<?
			}
	
	  ?>
			</tbody>
	</table>

	
	<?
 

	$message=ob_get_contents();
	ob_clean();
 
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and entry_form=26 and user_id=$approved_user_id and is_deleted=0");

	$mailToArr=array();
	if($email!=''){$mailToArr[]=$email;}

	$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.entry_form=26 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		 //echo $elcetronicSql;die;
	
	$elcetronicSqlRes=sql_select($elcetronicSql);
	foreach($elcetronicSqlRes as $rows){
			if($rows['USER_EMAIL']){$mailToArr[]=$rows['USER_EMAIL'];}
			if($rows['BYPASS']==2){break;}
	}

	$to=implode(',',$mailToArr);
	$subject = "Item Req Approved Notification";				
	$header=mailHeader();
	

	if($_REQUEST['isview']==1){
		echo $to.$message;
	 }
	 else{
	   if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
	 }


}


?>