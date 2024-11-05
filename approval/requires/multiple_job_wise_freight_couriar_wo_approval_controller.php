<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();

}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,SUPPLIER_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,SUPPLIER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no = '';
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$txt_wo_no = str_replace("'","",$txt_wo_no);

	$txt_date=str_replace("'","",$txt_date);
    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	// if($cbo_buyer_id==0){$cbo_buyer_id="'%%'";}

	$approval_type = str_replace("'","",$cbo_approval_type);

    
    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>84,'user_id'=>$user_id_approval,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
   //print_r($buyer_arr);
    
   
	
	if($db_type==0) 
	{		
		$year_field="YEAR(a.insert_date) as year"; 
        $orderBy_cond="IFNULL";
	}
	else if($db_type==2) 
	{		
		$year_field="to_char(a.insert_date,'YYYY') as year";
        $orderBy_cond="NVL";
	}
	else 
	{	
        $year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}

	$where_id_cond = '';
	if($txt_wo_no!='') 
	{		
		$where_id_cond.=" and a.labtest_no ='".$txt_wo_no."'"; 
	}
	
	if($txt_date!='') 
	{		
		$where_id_cond.=" and a.wo_date='$txt_date'"; 
	}

	// if($txt_date_from ){
	// 	$where_id_cond .= " and a.booking_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	// }

	$buyer_id_cond = '';
	if($cbo_buyer_name != 0){
		$buyer_id_cond .= " and a.buyer_id =$cbo_buyer_name";
	}

	// if ($txt_internal_ref == "") $internal_ref_cond = "";
	// else $internal_ref_cond = " and c.grouping='" . trim($txt_internal_ref) . "' ";

	// echo $where_id_cond;die();
	
    if($approval_type==0) // Un-Approve
	{  
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_NAME in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		//  }
        $data_mast_sql =" select a.ID,a.labtest_no, a.company_id, a.buyer_id, a.supplier_id from wo_labtest_mst a where  a.entry_form=689  and a.company_id = $cbo_company_name and  a.status_active=1 and a.is_deleted=0 and a.ready_to_approved = 1 and a.IS_APPROVED<>1";

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
	 
	
		// 	  echo "<pre>";
		// 	  print_r($tmp_sys_id_arr);die;
		//    echo "</pre>";die();
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				//$approved_user_cond=" and c.approved_by='$user_id'";
				$sql.="SELECT a.ID,a.labtest_no, a.company_id, a.buyer_id, a.supplier_id, a.wo_date, a.currency, a.tenor, a.is_approved from wo_labtest_mst a where  a.entry_form=689  and a.company_id = $cbo_company_name and  a.status_active=1 and a.is_deleted=0 and a.ready_to_approved = 1 and  a.APPROVED_SEQU_BY=$seq $sys_con and a.IS_APPROVED<>1 $buyer_id_cond $where_id_cond";
   
			}
		}
	}
	 else
	  {   $sql= "SELECT a.ID,a.labtest_no, a.company_id, a.buyer_id, a.supplier_id, a.wo_date, a.currency, a.tenor, a.is_approved from wo_labtest_mst a,APPROVAL_MST b where  a.entry_form=689  and a.company_id = $cbo_company_name and  a.status_active=1 and a.is_deleted=0 and a.ready_to_approved = 1  and a.IS_APPROVED<>0 and b.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=b.SEQUENCE_NO and b.mst_id=a.id $buyer_id_cond $where_id_cond";


    }
    //echo $sql;die();

 $nameArray=sql_select( $sql );
	$sys_id_arr=array();
	foreach ($nameArray as $row)
	{
	   $sys_id_arr[$row['ID']]=$row['ID'];
	}

   

	$sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=84 ".where_con_using_array($sys_id_arr,0,'mst_id')." order by id asc";
    //echo $sql_cause;	
	$nameArray_cause=sql_select($sql_cause);
	$app_cause_arr=array();
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	}



	$fset=1110;
	$table1=1110;    

	// $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=12 and is_deleted=0 and status_active=1");
	// $format_ids=explode(",",$print_report_format_ids);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =".$company_name."   and module_id=2 and report_id=49 and is_deleted=0 and status_active=1");

	$report_ids=explode(",",$print_report_format);




    ?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px;">
        <legend>Work Order for AOP Approval</legend>
        <div align='center' style="width:<? echo $table1+25;?>px; margin:0 auto;">
        	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
                    <th width="25"></th>                   
                    <th width="50">SL</th>                   
                    <th width="200">Wo No</th>
                    <th width="300">Buyer</th> 
                    <th width="250">Supplier</th>                   
                    <th width="200">Wo Date</th> 
					<th width="140">Not Appv. Cause </th>                  
                                     
                </thead>
            </table>            
            <div style="min-width:<? echo $table1+20; ?>px; float:left; overflow-y:auto; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?		 
                        $i=1; $all_approval_id='';

						
                        $nameArray=sql_select( $sql );
                        foreach ($nameArray as $row)
                        {                                                   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

							/// echo $report_ids[0]."_________";
							

							if($report_ids[0]==163){ $action="show_trim_booking_report1";}
							else if($report_ids[0]==164){ $action="show_trim_booking_report2";}
							else if($report_ids[0]==16){ $action="show_trim_booking_report3";}
							else if($report_ids[0]==177){ $action="show_trim_booking_report4";}
							else if($report_ids[0]==176){ $action="show_trim_booking_report6";}
							else if($report_ids[0]==288){ $action="show_trim_booking_report5";}
							else{	$action="show_work_order_aop_report";}


									
							
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $row[csf('id')]; ?>" />                                                  
                                        <input type="hidden" id="approval_id_<? echo $i;?>" name="approval_id[]"  value="<? echo $row[csf('approval_id')]; ?>" />
                                   </td> 
                                    <td width="50" align="center"><p><?=$i ;?></p></td>
                                    <td width="200"><p><? echo $row[csf('labtest_no')]; ?></p></td>
                                   
                                    <td width="300"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?> &nbsp;</p></td>
							
                                    <td width="250"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?> &nbsp;</p></td>				
                                    <td width="200"><p><? echo change_date_format($row[csf('wo_date')]); ?>&nbsp;</p></td>
									<td width="100" style="cursor:pointer;"><input name="comments_[]" class="text_boxes" readonly placeholder="Please Browse" id="comments_<?= $row['ID'];?>" onClick="openmypage_refusing_cause('requires/multiple_job_wise_freight_couriar_wo_approval_controller.php?action=refusing_cause_popup','Comments','<?= $row['ID'];?>');" value="<?=$app_cause_arr[$row['ID']];?>"></td>	
                                				                                  
                                </tr>
                                <?
                                $i++;

                            if($all_approval_id!="")
                            {
                                $con = connect();
                                $rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
                                disconnect($con);
                            }
                        }                           
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1+25; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="25" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td align="left">
                        &nbsp;<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
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
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	
	
	$msg=''; $flag=''; $response='';
	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	

	//............................................................................
	
	$sql = "select a.ID,a.company_id,a.BUYER_ID from wo_labtest_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>84,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
  //print_r($user_sequence_no) ;die;

	
 	if($approval_type==0)
	{ 
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$target_ids);	
        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $mst_id)
        {
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",84,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",84,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$user_id_approval."")); 
        }
	 
 

        $flag=1;
		if($flag==1) 
		{  
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			//echo "10**insert into approval_mst($field_array) values $data_array";die;
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		   
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_DATE*APPROVED_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_labtest_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=84 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "24444**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}

   else if($approval_type==5)
	{
		$max_approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=84 and mst_id in($target_ids)  and APPROVED=2 and APPROVED_BY=$user_id_approval group by mst_id", "mst_id", "approved_no"  );
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$rID1=sql_multirow_update("wo_labtest_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'2*0*0',"id",$target_ids,0); ; 
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=84 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;


			
			
			$target_app_id_arr = explode(',',$target_ids);	
			foreach($target_app_id_arr as $mst_id)
			{		
				$approved_no=$max_approved_no_arr[$mst_id]+1;
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",84,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
				$ahid++;
			}		
			
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}

		// if($flag==1)
		// {
		// 	$query="UPDATE refusing_cause_history SET CURR_APP_STATUS=0  WHERE entry_form=66 and CURR_APP_STATUS=1 and mst_id in ($target_ids)";
		// 	$rID4=execute_query($query,1);
		// 	if($rID4) $flag=1; else $flag=0;
		// }
		

		// echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else
	{            
		
		
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=17 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("wo_labtest_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=28 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=28 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by=".$user_id_approval.", un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=28 and current_approval_status=1 and mst_id in ($target_ids)";

			
			$rID4=execute_query($query,1);
			//echo $rID4;
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

 if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Comments Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	$app_user_id = ($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	$sql_cause="SELECT MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=84 and mst_id='$quo_id' and INSERTED_BY=$app_user_id order by id asc";	
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
			http.open("POST","multiple_job_wise_freight_couriar_wo_approval_controller.php",true);
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
			//$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=56 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
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
	

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",84,".$quo_id.",'".$refusing_cause."',".$app_user_id.",'".$pc_date_time."')";
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

		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=84 group by mst_id","id");
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
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=84 and valid=1 and a.id!=$user_id   and a.is_deleted=0 AND b.is_deleted = 0 group by a.id,a.user_name,a.department_id,a.user_full_name,a.designation,b.sequence_no order by b.sequence_no";
                //echo $sql;die;
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}



?>