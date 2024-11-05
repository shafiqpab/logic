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
			//if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no = '';
	$company_name = str_replace("'","",$cbo_company_name);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$txt_internal_ref = str_replace("'","",$txt_internal_ref);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	// if($cbo_buyer_id==0){$cbo_buyer_id="'%%'";}

	$approval_type = str_replace("'","",$cbo_approval_type);

    
    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>28,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
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
	if($txt_booking_no!='') 
	{		
		$where_id_cond.=" and a.booking_no_prefix_num ='".$txt_booking_no."'"; 
	}
	
	// if($txt_date!='') 
	// {		
	// 	$where_id_cond.=" and a.booking_date='$txt_date'"; 
	// }

	if($txt_date_from && $txt_date_to){
		$where_id_cond .= " and a.booking_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	}

	$buyer_id_cond = '';
	if($cbo_buyer_name != 0){
		$buyer_id_cond .= " and a.buyer_id =$cbo_buyer_name";
	}

	if ($txt_internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and c.grouping='" . trim($txt_internal_ref) . "' ";

	// echo $where_id_cond;die();
	
    if($approval_type==0) // Un-Approve
	{  
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_NAME in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		//  }
        $data_mast_sql ="SELECT a.ID,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.supplier_id as supplier_name, a.is_approved,'0' as approval_id,
        a.job_no as po_job_no, a.booking_date, a.delivery_date ,a.tagged_booking_no
        FROM wo_booking_mst a
        WHERE a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.IS_APPROVED<>1 order by a.booking_no_prefix_num";

        

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
				$sql.="SELECT a.ID,a.pay_mode,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.supplier_id as supplier_name, a.is_approved,'0' as approval_id,
                 a.booking_date, a.delivery_date ,a.tagged_booking_no,
				 c.grouping as internal_ref from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  where a.booking_no=b.booking_no  and b.po_break_down_id=c.id
                and  a.booking_type=3 and a.company_id = $company_name $internal_ref_cond  $buyer_id_cond $where_id_cond and  a.status_active=1 and a.is_deleted=0 and a.process=35 and a.APPROVED_SEQU_BY=$seq $sys_con and a.ready_to_approved = 1 and a.IS_APPROVED<>1 group by a.ID,a.pay_mode,a.booking_no_prefix_num ,a.booking_no,a.buyer_id , a.supplier_id, a.is_approved,'0',
                 a.booking_date, a.delivery_date ,a.tagged_booking_no,
				 c.grouping order by a.booking_no_prefix_num";


			}
		}
	}
	else
	{   $sql="SELECT a.ID,a.pay_mode,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.supplier_id as supplier_name, a.is_approved,'0' as approval_id, a.booking_date, a.delivery_date ,a.tagged_booking_no
        FROM wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,APPROVAL_MST d
        WHERE a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=3 and a.company_id = $company_name  $buyer_id_cond $internal_ref_cond $where_id_cond and  a.status_active=1 and a.is_deleted=0 and a.process=35 and  a.ready_to_approved = 1 and a.IS_APPROVED<>0  and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.entry_form=28 and d.mst_id=a.id group by a.ID,a.pay_mode,a.booking_no_prefix_num ,a.booking_no,a.buyer_id , a.supplier_id, a.is_approved,'0', a.booking_date, a.delivery_date ,a.tagged_booking_no, c.grouping order by a.booking_no_prefix_num";


    }
 //echo $sql;die();

 $nameArray = sql_select($sql);
	
	$bokings_arr = array();
	foreach ($nameArray as $row) {
		$bokings_arr[$row['ID']] = $row['ID'];
	
	}
	$bokings= implode(',', $bokings_arr);
     //print_r($bokings);
	 $booking_cond = where_con_using_array($bokings_arr, 0, 'a.id');

	 $booking_sql=sql_select("SELECT a.ID,a.booking_no,b.job_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id = $company_name $booking_cond and  a.status_active=1  and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 group by  a.ID,a.booking_no,b.job_no ");
 
	//   echo "SELECT a.ID,a.booking_no,b.job_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id = $company_name $booking_cond and  a.status_active=1  and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 group by  a.ID,a.booking_no,b.job_no ";die;

	foreach($booking_sql as $row)
	{
		$booking_arr[$row[csf("booking_no")]].=$row[csf("job_no")].",";
		
	}

	// $intref_sql=sql_select("SELECT a.ID,a.booking_no,b.job_no,c.grouping as internal_ref from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and a.company_id = $company_name $booking_cond and  a.status_active=1  and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 group by  a.ID,a.booking_no,b.job_no,c.grouping ");

	// // echo "SELECT a.ID,a.booking_no,b.job_no,c.grouping from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and a.company_id = $company_name $booking_cond and  a.status_active=1  and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 group by  a.ID,a.booking_no,b.job_no,c.grouping ";die;

	// foreach($intref_sql as $row)
	// {
	// 	$internal_arr[$row[csf("booking_no")]].=$row[csf("internal_ref")].",";
		
	// }

	// echo "<pre>";
	// print_r($internal_arr); 
	//   echo "</pre>";die();
	$fset=1005;
	$table1=1005;    

	// $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=12 and is_deleted=0 and status_active=1");
	// $format_ids=explode(",",$print_report_format_ids);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =".$company_name."   and module_id=2 and report_id=49 and is_deleted=0 and status_active=1");

	$report_ids=explode(",",$print_report_format);




    ?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px;">
        <legend>Work Order for AOP Approval</legend>
        <div style="width:<? echo $table1+25; ?>px; margin:0 auto;">
        	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
                    <th width="25"></th>                   
                    <th width="50">SL</th>                   
                    <th width="80">System No</th>
                    <th width="125"> Booking No </th>
                    <th width="125">Job No</th>
                    <th width="100">Internel Ref</th>
                    <th width="150">Buyer</th> 
                    <th width="150">Supplier</th>                   
                    <th width="100">Booking Date</th>                   
                    <th width="100">Delivery Date</th>                   
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
                                    <td width="50" align="center"><p><?php echo $i;?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $row[csf('system_no')]; ?>&nbsp;</p></td>
                                    <td width="125" align="center"><a href='##' style='color:#000' onClick="generate_order_report('<? echo $row[csf('booking_no')]; ?>',<? echo $company_name; ?>,'<? echo $row[csf('is_approved')]; ?>','<? echo $row[csf('entry_form')]; ?>','<? echo $report_ids[0]; ?>','<? echo $row[csf('tagged_booking_no')]; ?>','<? echo $action; ?>')"><font color="blue"><b><? echo $row[csf('booking_no')]; ?></b></font></a></td>                                    
                                    <td width="125" align="center"><?php echo implode(',',array_unique(explode(',',rtrim($booking_arr[$row[csf("booking_no")]], ',')))); ?></td>
									<td width="100" align="center"><?php echo implode(',',array_unique(explode(',',rtrim($internal_arr[$row[csf("booking_no")]], ',')))); ?></td>
                                    <td width="150"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?> &nbsp;</p></td>
									<?php
                                      $supp = ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) ? $company_arr[$row[csf("supplier_name")]] : $supplier_arr[$row[csf("supplier_name")]];
									?>
                                    <td width="150"><p><? echo $supp; ?>&nbsp;</p></td>				
                                    <td width="100"><p><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>				                                  
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
	
	$sql = "select a.ID,a.BUYER_ID,a.READY_TO_APPROVED  from wo_booking_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '21**Ready to approve Yes is mandatory';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_NAME'],'brand_id'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>28,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
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
			$data_array.="(".$id.",28,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",28,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
			$rID2=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=28 and mst_id in ($target_ids)";
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
		$max_approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=28 and mst_id in($target_ids)  and APPROVED=2 and APPROVED_BY=$user_id_approval group by mst_id", "mst_id", "approved_no"  );
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'2*0*0',"id",$target_ids,0); ; 
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=28 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;


			
			
			$target_app_id_arr = explode(',',$target_ids);	
			foreach($target_app_id_arr as $mst_id)
			{		
				$approved_no=$max_approved_no_arr[$mst_id]+1;
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",28,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
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

		$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
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
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=28 and valid=1 and a.id!=$user_id   and a.is_deleted=0 AND b.is_deleted = 0 group by a.id,a.user_name,a.department_id,a.user_full_name,a.designation";
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


if ($action=="show_work_order_aop_report")
{
	// var_dump($_REQUEST);die();
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_booking_mst","booking_no=$txt_booking_no","supplier_id");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=162"); 
	list($nameArray_approved_row)=$nameArray_approved;
	
	//==================================================================	


	?>
	<div style="width:1540px; margin: 0 auto" align="center">       
    										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_arr[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name";
                            foreach ($nameArray as $result)
                            { 
                            	?>
                                Plot No: <? echo $result[csf('plot_no')]; ?> 
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?> 
                                Block No: <? echo $result[csf('block_no')];?> 
                                City No: <? echo $result[csf('city')];?> 
                                Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                Province No: <?php echo $result[csf('province')];?> 
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
											
                            }

							$sup_addres=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$com_supplier_id");  	
							foreach ($sup_addres as $row)
                            { 
				 				if($row[csf('plot_no')]!='') $plot_no=$row[csf('plot_no')].',';
								if($row[csf('level_no')]!='') $level_no=$row[csf('level_no')].',';
								if($row[csf('road_no')]!='') $road_no=$row[csf('road_no')].',';
								if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
								if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
								if($row[csf('country_id')]!=0) $country_name=$country_arr[$row[csf('country_id')]].',';
								if($row[csf('block_no')]!='') $block_no=$row[csf('block_no')].',';
								if($row[csf('province')]!='') $province=$row[csf('province')].',';
								if($row[csf('city')]!='') $city=$row[csf('city')].',';
								if($row[csf('zip_code')]!='') $zip_code=$row[csf('zip_code')].',';
								if($row[csf('email')]!='') $email=$row[csf('email')].',';
								if($row[csf('website')]!='') $website=$row[csf('website')];
								
								$company_address=$plot_no.$level_no.$road_no.$country_name.$block_no.$province.$city.$zip_code.$email.$website;
							}
                                ?>   
                                         
                               </td> 
                            </tr>
                            <tr>
                             
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Work Order for AOP Approval";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                             
                             <td style="font-size:20px"> 
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>
								 
								  <?
								 }
							  	?>
                             </td>
                              
                            </tr>
                      </table>
                </td>
                      
            </tr>
       </table>
       <?
                
		$fabric_source='';
		$booking_info=sql_select( "select id,buyer_id,booking_no,booking_date,supplier_id,exchange_rate,currency_id,attention,delivery_date,fabric_source,entry_form from wo_booking_mst where booking_no=$txt_booking_no"); 

		foreach ($booking_info as $result)
		{
			$fabric_source=$result[csf('fabric_source')];
			
			$varcode_booking_no=$result[csf('booking_no')];
							
			?>
		   <table width="100%" style="border:1px solid black">                    	
		        <tr>
		            <td colspan="6" valign="top"></td>                             
		        </tr>                                                
		        <tr>
		            <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
		            <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
		            <td width="100" style="font-size:12px"><b>Booking Date</b></td>
		            <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
		            <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
		            <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
		           			
		        </tr>
		        <tr>
		            
		            <td width="100"><span style="font-size:12px"><b>Buyer Name</b></span></td>
		            <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
		            <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
		            <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
					{
						echo $company_library[$result[csf('supplier_id')]];
					}
					else
					{
						echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					?>    </td>
		            <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
		           	<td width="110">:&nbsp;
					<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
					{
						echo $company_address;
					}
					else
					{
						echo $supplier_address_arr[$result[csf('supplier_id')]];
					}
					?>
		            
		            </td> 
		        </tr>
		        
		        
		        <tr>
		            <td width="100" style="font-size:12px"><b>Currency</b></td>
		            <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
		         
		            <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
		            <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
		            <td  width="100" style="font-size:12px"><b>Attention</b></td>
		            <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
		           
		        </tr> 
		    </table>  
		    <?
		}
		?>
            
      <br/>  
      <? 
	  
	if($db_type==0)
	{
		$sql= sql_select("select a.booking_no, a.booking_type, a.booking_date, a.delivery_date, a.buyer_id, a.supplier_id, a.job_no, a.item_category, a.source, a.attention, a.process, b.dia_width, b.uom, a.pay_mode, a.exchange_rate, a.currency_id, b.wo_qnty, b.amount, b.pre_cost_remarks FROM wo_booking_mst a, wo_booking_dtls b  WHERE a.booking_no=$txt_booking_no and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id"); 
	}
	if($db_type==2)
	{
		$sql= sql_select("select a.booking_no, a.booking_type, a.booking_date, a.delivery_date, a.buyer_id, a.supplier_id, a.job_no, a.item_category, a.source, a.attention, a.process, b.dia_width, b.uom, a.pay_mode, a.exchange_rate, a.currency_id, b.wo_qnty, b.amount, b.pre_cost_remarks FROM wo_booking_mst a, wo_booking_dtls b  WHERE a.booking_no=$txt_booking_no and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id"); 

	}
	
	?>
	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<th width="50">Sl</th>
			<th width="150">Booking No</th>
			<th width="100">Booking Type</th>
			<th width="100">Booking Date</th>
			<th width="100">Delivery Date</th>
			<th width="130">Buyer</th>
			<th width="130">Supplier</th>
			<th width="100">Job No</th>
			<th width="100">Item Category</th>
			<th width="80">Source</th>
			<th width="50">Attention</th>
			<th width="50">Process</th>
			<th width="50">Dia/ Width</th>
			<th width="60">UOM</th>
			<th width="50">Pay Mood</th>
			<th width="80">Currency</th>
			<th width="50">Ex. Rate</th>
			<th width="60">Quantity</th>	
			<th width="80">Amount</th>
			<th width="100">Remarks</th>
		</thead>
	<?

	$toatl_quattity=0;
	$total_amount=0;

	$i=1;
	foreach ($sql as $row)
	{		
		?>
		<tr>
		<td width="50"><? echo $i; ?></td>
		<td width="150"><? echo $row[csf('booking_no')]; ?></td>
		<td width="100"><? echo $row[csf('booking_type')]; ?></td>
		<td width="100"><? echo $row[csf('booking_date')]; ?></td>
		<td width="100"><? echo $row[csf('delivery_date')]; ?></td>
		<td width="130"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
		<td width="130"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
		<td width="100"><? echo $row[csf('job_no')]; ?></td>
		<td width="100"><? echo $row[csf('item_category')]; ?></td>
		<td width="80" align="center"><? echo $row[csf('source')]; ?></td>
		
		<td width="50" align="center"><? echo $row[csf('attention')]; ?></td>
		<td width="50" align="center"><? echo $row[csf('process')]; ?></td>
		<td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
		<td width="60" align="center"><? echo $row[csf('uom')]; ?></td>
		<td width="50" align="center"><? echo $pay_mode[$row[csf('pay_mode')]]; ?></td>

		<td width="80" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
		<td width="50" align="right"><? echo number_format($row[csf('exchange_rate')],2);?></td>
		<td width="60" align="right"><? echo number_format($row[csf('wo_qnty')],2); ?></td>
		<td width="80" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
		<td width="100"><? echo $row[csf('pre_cost_remarks')]; ?></td>
		
		</tr>
		<?
		$i++;
		$toatl_quattity += $row[csf('wo_qnty')];
		$total_amount += $row[csf('amount')];
	}
	?>
		<tr>
			<th width="50" colspan="17" align="right">Total </th>
			
			<th width="60" align="right"><? echo number_format($toatl_quattity,2);?></th>
			<th width="80" align="right"><? echo number_format($total_amount,2); ?></th>
			<th width="100" align="right"><? ?></th>
		</tr>
	</table>
        
        <br/> 

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    <tr>
                        	<th width="30" colspan="4" align="center">Approval Status</th>
                            
                        </tr>
                    	<tr>
                        	<th width="30">Sl   <? $bookingId=$nameArray[0][csf('id')]?> </th>
                            <th width="250">Name/Designation</th>
                            <th width="150">Approval Date</th>
                            <th width="80">Approval No</th>
                             
                        </tr>
                    </thead>
                    <tbody>

                    <?

 					$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
 					$desg_arr=return_library_array( "select id, designation from user_passwd", "id", "designation"  );
 					$desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
 					$sel=sql_select("select a.approved_by, a.approved_date, a.id as app_no from approval_history a, electronic_approval_setup b where a.mst_id in(select id from wo_booking_mst where id=$bookingId) and a.entry_form=162 and a.approved_by=b.user_id and b.company_id=$cbo_company_name and b.is_deleted=0 group by a.approved_by, a.approved_date, a.id, b.sequence_no order by b.sequence_no asc");
					//echo "select a.approved_by, a.approved_date, a.id as app_no from approval_history a, electronic_approval_setup b where a.mst_id in(select id from wo_non_ord_samp_booking_mst where id=$bookingId) and a.entry_form=9 and a.approved_by=b.user_id and b.company_id=$cbo_company_name and b.is_deleted=0 group by a.approved_by, a.approved_date, a.approved_no, b.sequence_no order by b.sequence_no asc";
					$i=1;
					$approval_arr=array(); 
					$app_id_arr=array();
 					foreach ($sel as $rows) {
						$app_id_arr[$rows[csf('approved_by')]][$rows[csf('app_no')]]=$rows[csf('app_no')];
						$approval_arr[$rows[csf('approved_by')]]['date'].=$rows[csf('approved_date')].',';
					}
					
					foreach($approval_arr as $approved_by=>$val)
					{
						$app_date="";
						$exapp_date="";
						$exapp_date=array_filter(array_unique(explode(",",$val['date'])));
						foreach($exapp_date as $apdate)
						{
							if($app_date=="") $app_date=$apdate; else $app_date.=', '.$apdate;
						}
						$count_no=count($app_id_arr[$approved_by]);
						
						?>
						<tr id="settr_1" align="">
                            <td width="30"><? echo $i ?></td>
                            <td width="250"><? echo $user_arr[$approved_by]." /".$desg_name[$desg_arr[$approved_by]]; ?></td>
                            <td width="150"><? echo $app_date; ?></td>
                            <td width="80"><? echo $count_no; ?></td>
						</tr>
						<?
						$i++;
					}
					?>  
					
                </tbody>
                </table>
                </td>
                
            </tr>
        </table>
        
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					?>
                </tbody>
                </table>
                </td>
                
            </tr>
        </table>
         
       </div>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
	   exit();

}
?>