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

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "");
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
	}	
	exit();	 
} 

 

function getSequence($parameterArr=array()){
	$lib_party_arr=implode(',',(array_keys($parameterArr['lib_party_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,PARTY_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	// echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['PARTY_ID']==''){$rows['PARTY_ID']=$lib_party_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_party_arr=implode(',',(array_keys($parameterArr['lib_party_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,PARTY_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['PARTY_ID']==''){$rows['PARTY_ID']=$lib_party_arr;}
		$usersDataArr[$rows['USER_ID']]['PARTY_ID']=explode(',',$rows['PARTY_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			//if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}




if($action=="report_generate")
{  
	$process = array( &$_POST );
    //print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	if($cbo_party_name){$where_con .= " and a.PARTY_ID =".$cbo_party_name.""; }
	if($cbo_party_name){$where_con .= " and a.SUBCON_JOB =".$txt_job_id.""; }

	if($txt_date_from && $txt_date_to){
		$where_con .= " and a.receive_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	}
	

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//............................................................................
	
	


	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_id,'page_id'=>$menu_id,'user_id'=>$user_id_approval,'lib_party_arr'=>0,'lib_brand_arr'=>0));
    
	
	//var_dump($electronicDataArr);die;
	
	//print_r($file_arr);
 
	 
	
	if($approval_type==0) // Un-Approve
	{  
		//Match data..................................
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		// }
 
		$data_mast_sql = "SELECT a.id as ID,a.company_id, a.subcon_job as JOB_NO, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.entry_form=238 and a.subcon_job=b.job_no_mst  and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.APPROVED<>1  and a.COMPANY_ID=$cbo_company_id $where_con";
		 //echo $data_mast_sql;die;
    


		$tmp_sys_id_arr=array();
		$data_mast_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mast_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
					$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
				}
				else{
					$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					break;
				}
			}
		}
		//..........................................Match data;		
	
		//print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= "SELECT a.id as ID,a.COMPANY_ID, a.SUBCON_JOB as JOB_NO, a.WITHIN_GROUP as WITHIN_GROUP, a.PARTY_ID as PARTY_ID, a.ORDER_NO as ORDER_NO, a.CURRENCY_ID as CURRENCY_ID, a.RECEIVE_DATE as RECEIVE_DATE, a.REC_START_DATE as REC_START_DATE, a.TEAM_LEADER as TEAM_LEADER, a.TEAM_MEMBER as TEAM_MEMBER,sum(b.amount) as AMOUNT
				from subcon_ord_mst a, subcon_ord_dtls b
				where a.ENTRY_FORM=238 and a.SUBCON_JOB=b.JOB_NO_MST  and a.READY_TO_APPROVED=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.APPROVED<>1   and a.APPROVED_SEQU_BY=$seq $sys_con and a.COMPANY_ID=$cbo_company_id $where_con group by  a.id,a.company_id, a.subcon_job, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member  ";
				
			}
		}
	}
	else
	{

		$sql = "SELECT a.id as ID,a.COMPANY_ID, a.SUBCON_JOB as JOB_NO, a.WITHIN_GROUP as WITHIN_GROUP, a.PARTY_ID as PARTY_ID, a.ORDER_NO as ORDER_NO, a.CURRENCY_ID as CURRENCY_ID, a.RECEIVE_DATE as RECEIVE_DATE, a.REC_START_DATE as REC_START_DATE, a.TEAM_LEADER as TEAM_LEADER, a.TEAM_MEMBER as TEAM_MEMBER,sum(b.amount) as AMOUNT
		from subcon_ord_mst a, subcon_ord_dtls b,APPROVAL_MST c
		where  a.SUBCON_JOB=b.JOB_NO_MST  and a.READY_TO_APPROVED=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.APPROVED<>0   and c.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and c.entry_form=72 and c.mst_id=a.id and a.COMPANY_ID=$cbo_company_id $where_con group by  a.id,a.company_id, a.subcon_job, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member  ";

    }
	//echo $sql;die();
	$precostArr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
    $supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
	$company_library=return_library_array("select id, company_name from lib_company","id","company_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$team_leader_library=return_library_array("select id, team_leader_name from lib_marketing_team","id","team_leader_name");
	$team_member_library=return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	// echo $sql;die();

	 $nameArray=sql_select( $sql );
	 $sys_id_arr=array();
	 foreach ($nameArray as $row)
	 {
		$sys_id_arr[$row['ID']]=$row['ID'];
	 }

	//
 
	 $sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=72 ".where_con_using_array($sys_id_arr,0,'mst_id')."  and INSERTED_BY=$user_id_approval order by id asc";
	// echo $sql_cause;	
	 $nameArray_cause=sql_select($sql_cause);
	 $app_cause_arr=array();
	 foreach($nameArray_cause as $row)
	 {
		 $app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	 }

	$width=1030;

    ?> 


    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Sub Contact Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<th width="40"></th>                   
                    <th width="70">Within Group</th>
                    <th width="120">Party Name</th>
                    <th width="120">Job No</th>
                    <th width="100">WO No</th>
                    <th width="60">Currency</th>                    
                    <th width="70">Value</th>
                    <th width="70">Order Rcv.Date</th>
                    <th width="70">Target Delv. Date</th>
                    <th width="120">Team Leader</th>
                    <th >Team Member</th>                  
                   
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:330px;">
                <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?		 
                        $i=1; $all_approval_id='';
						//print_r($nameArray);
                        foreach ($nameArray as $row)
                        {                                                   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					
                                ?>
                                <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
                                    <td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]" onClick="check_last_update(<?= $i;?>);" />
                                        <input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?= $row['ID']; ?>" />                                                  
                                   </td> 
								     
									 <td width="70" align="center"><? echo $yes_no[$row['WITHIN_GROUP']]; ?></td>
                                    <td width="120" class="wrd_brk">
										<? 
											if($row['WITHIN_GROUP']==1)
											{
												echo $company_library[$row['PARTY_ID']];
											}
											else
											{
												echo $buyer_library[$row['PARTY_ID']];
											}
										?>
									</td>
                                   <td width="120" align="center"><a href="javascript:fn_generate_print(<? echo $row['ID']; ?>,<? echo $row['COMPANY_ID']?>,'<? echo $row['JOB_NO'] ?>')"><? echo $row[csf('JOB_NO')]; ?></a></td>  
                                    <td width="100" class="wrd_brk"><? echo $row['ORDER_NO']; ?></td>
                                    <td width="60" align="center"><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
									<td width="70" align="right"><? echo number_format($row['AMOUNT'],4);?>&nbsp;</td>
									<td width="70" align="center"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
									<td width="70" align="center"><? echo change_date_format($row['REC_START_DATE']); ?></td>
									<td width="120" class="wrd_brk"><? echo $team_leader_library[$row['TEAM_LEADER']]; ?></td>
									<td class="wrd_brk"><? echo $team_member_library[$row['TEAM_MEMBER']]; ?></td>
                            
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
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="3" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	//echo "10**".'zdhgdsfgsgf';die;
	//............................................................................
	
	$sql = "select a.ID  from subcon_ord_mst a where a.COMPANY_ID=$cbo_company_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('buyer_id'=>0,'brand_id'=>0,'PARTY_ID'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_id,'page_id'=>$menu_id,'lib_party_arr'=>$lib_party_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
 

	if($approval_type==5)
	{

		$rID1=sql_multirow_update("subcon_ord_mst","approved*ready_to_approved*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=72 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=72 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		 // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else if($approval_type==0)
	{      
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$target_ids);	
        foreach($target_app_id_arr as $mst_id)
        {		
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",72,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",72,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
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
			$field_array_up="APPROVED*APPROVED_SEQU_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "subcon_ord_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=72 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{              
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=72 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("subcon_ord_mst","approved*ready_to_approved*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=72 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=72 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=72 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		 //echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
		
		$response=$target_ids;
		if($flag==1) $msg='20'; else $msg='22';
		
	}
	

	if($db_type==2 || $db_type==1 )
	{
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
	}
	disconnect($con);
	die;
	
}

if($action=='garments_service_work_order'){
	$file_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='".$id."' and FORM_NAME='garments_service_work_order'", "ID", "IMAGE_LOCATION"  );
	foreach($file_arr as $file){
		echo "<a target='_blank' href='../../".$file."'>Download</a> ";
	}
}

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
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_id and b.entry_form=72 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. No", "100,120,130,120,50,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}



?>
 
