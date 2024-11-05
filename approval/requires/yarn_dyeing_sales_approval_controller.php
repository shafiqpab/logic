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

if ($action=="load_supplier_dropdown")
{

    $data=explode("_",$data);
    if($data[1]==3 || $data[1]==5){
        echo create_drop_down( "cbo_supplier_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );
    }else{
        echo create_drop_down( "cbo_supplier_id", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(5,8) and a.status_active=1 and a.is_deleted=0 and c.tag_company ='$data[0]' group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/yarn_dyeing_sales_approval_controller');",0 );
    }
    
}

 

function getSequence($parameterArr=array()){
	$lib_supplier_arr=implode(',',(array_keys($parameterArr['lib_supplier_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,SUPPLIER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	// echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['SUPPLIER_ID']==''){$rows['SUPPLIER_ID']=$lib_supplier_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_supplier_arr=implode(',',(array_keys($parameterArr['lib_supplier_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,SUPPLIER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['SUPPLIER_ID']==''){$rows['SUPPLIER_ID']=$lib_supplier_arr;}
		$usersDataArr[$rows['USER_ID']]['SUPPLIER_ID']=explode(',',$rows['SUPPLIER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			//if( in_array($bbtsRows['SUPPLIER_ID'],$usersDataArr[$user_id]['SUPPLIER_ID']) &&  $bbtsRows['SUPPLIER_ID']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}




if($action=="report_generate")
{ 
	$process = array( &$_POST );
   // print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$txt_work_order = str_replace("'","",$txt_work_order);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;



	if($cbo_supplier_id){$where_con .= " and a.SUPPLIER_ID =".$cbo_supplier_id.""; }
	if($txt_date_from && $txt_date_to){
		$where_con .= " and a.booking_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	}
	

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//............................................................................
	
	


	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_id,'page_id'=>$menu_id,'user_id'=>$user_id_approval,'lib_supplier_arr'=>0,'lib_brand_arr'=>0));
    
	
	//var_dump($electronicDataArr);die;
	
	//print_r($file_arr);
 
	 
	
	if($approval_type==0) // Un-Approve
	{  
		//Match data..................................
		// if($electronicDataArr['user_by'][$user_id_approval]['SUPPLIER_ID']){
		// 	$where_con .= " and a.SUPPLIER_ID in(".$electronicDataArr['user_by'][$user_id_approval]['SUPPLIER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['SUPPLIER_ID']=$electronicDataArr['user_by'][$user_id_approval]['SUPPLIER_ID'];
		// }
 
		$data_mast_sql = "select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,a.currency, a.ecchange_rate, a.pay_mode, a.source, a.ready_to_approved from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_APPROVED<>1 and a.ready_to_approved=1 and a.COMPANY_ID=$cbo_company_id $where_con";
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
				$sql .= "select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,a.currency, a.ecchange_rate,a.entry_form, a.pay_mode, a.source, a.ready_to_approved,b.job_no,b.job_no_id,c.within_group from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b LEFT JOIN fabric_sales_order_mst c ON b.job_no=c.job_no where a.id=b.mst_id  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_APPROVED<>1 and a.ready_to_approved=1 and a.entry_form=135 and a.APPROVED_SEQU_BY=$seq $sys_con group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,a.currency, a.ecchange_rate,a.entry_form, a.pay_mode, a.source, a.ready_to_approved,b.job_no,b.job_no_id,c.within_group";
			}
		}
	}
	else
	{

		$sql = "select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,a.currency, a.ecchange_rate,a.entry_form, a.pay_mode, a.source, a.ready_to_approved,b.job_no,b.job_no_id,c.within_group from wo_yarn_dyeing_mst a, APPROVAL_MST d,wo_yarn_dyeing_dtls b LEFT JOIN fabric_sales_order_mst c ON b.job_no=c.job_no where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_APPROVED<>0 and a.ready_to_approved=1  and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and a.entry_form=135  and d.mst_id=a.id group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,a.currency, a.ecchange_rate,a.entry_form, a.pay_mode, a.source, a.ready_to_approved,b.job_no,b.job_no_id,c.within_group ";
    }
	//echo $sql;die();
	$precostArr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
    $supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
	// echo $sql;die();

	 $nameArray=sql_select( $sql );
	 $sys_id_arr=array();
	 foreach ($nameArray as $row)
	 {
		$sys_id_arr[$row['ID']]=$row['ID'];
	 }

	//
 
	 $sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=76 ".where_con_using_array($sys_id_arr,0,'mst_id')."  and INSERTED_BY=$user_id_approval order by id asc";
	// echo $sql_cause;	
	 $nameArray_cause=sql_select($sql_cause);
	 $app_cause_arr=array();
	 foreach($nameArray_cause as $row)
	 {
		 $app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	 }

	$width=950;

    ?> 


    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Erosion Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<th width="25"></th>                   
                    <th width="35">SL</th>                   
                    <th width="150"> WO No.</th>
                    <th width="100">Supplier</th>
                    <th width="100">Work Order Date</th>                   
                    <th width="100">Delivery Date</th>                   
                    <th width="100">Source</th>                   
                    <th width="100">Pay Mode</th>                   
					
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
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]" onClick="check_last_update(<?= $i;?>);" />
                                        <input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?= $row['ID']; ?>" />                                                  
                                   </td> 
                                    <td width="35" align="center"><?= $i?></td>
                                    <td width="150" align="center"><a href="javascript:fn_generate_print(<? echo $row['ID']; ?>,<? echo $row['COMPANY_ID']; ?>,'<? echo $row['JOB_NO']; ?>',<? echo $row['JOB_NO_ID']; ?>,'<? echo $row['YDW_NO']; ?>',<? echo $row['WITHIN_GROUP']; ?>)"><? echo $row[csf('ydw_no')]; ?></a></td>                                    
                                    <td width="100"><?echo $supplier[$row[csf('SUPPLIER_ID')]];?></td>
                                    <td width="100"><? if($row[csf('BOOKING_DATE')]!="0000-00-00") echo change_date_format($row[csf('BOOKING_DATE')]); ?></td>				
                                    <td width="100" align="right"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
                                    <td width="100" align="right"><? echo $source[$row[csf('source')]]; ?></td>
									 <td width="100" align="right"><? echo $pay_mode[$row[csf('pay_mode')]]; ?></td>
	                                  
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
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$txt_work_order = str_replace("'","",$txt_work_order);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


	//............................................................................
    
	
	$sql = "select a.ID  from wo_yarn_dyeing_mst a where a.COMPANY_ID=$cbo_company_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('SUPPLIER_ID'=>0,'brand_id'=>0,'item'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_id,'page_id'=>$menu_id,'lib_supplier_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
 

	if($approval_type==5)
	{

		$rID1=sql_multirow_update("wo_yarn_dyeing_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=76 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=76 and mst_id in ($target_ids)";
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
			$data_array.="(".$id.",76,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",76,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_yarn_dyeing_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=76 and mst_id in ($target_ids)";
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
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=76 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("wo_yarn_dyeing_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=76 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=76 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=76 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		 //echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
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
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_id and b.entry_form=76 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
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

 
if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Comments Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	$app_user_id = ($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	$sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=76 and mst_id='$quo_id' and INSERTED_BY=$app_user_id order by id asc";	
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
		if(refusing_cause == '')
		{
			document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			alert("Please save Comments first or empty");
			return;
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
			http.open("POST","yarn_dyeing_sales_approval_controller.php",true);
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
				parent.emailwindow.hide();
			}
			else if(response[0]==1)
			{
				alert("Data update successfully");
				parent.emailwindow.hide();
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
         <?
		$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=15 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
		$sqlHisRes=sql_select($sqlHis);
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
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			 </table>
		 </div>
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
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=76 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=76 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=76 and id=$idpre"; //die;
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",76,".$quo_id.",'".$refusing_cause."',".$app_user_id.",'".$pc_date_time."')";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
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

		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=76 group by mst_id","id");
		$field_array="refusing_reason*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$app_user_id."*'".$pc_date_time."'";
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$idpre,0);
		
		if($rID==1) $flag=1; else $flag=0;
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**$refusing_cause";
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


if($action=='app_mail_notification'){
	//http://localhost/platform-v3.5/approval/requires/yarn_dyeing_sales_approval_controller.php?data=20__reza@logicsoftbd.com__3&action=app_mail_notification

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	

	list($sys_id,$email,$alter_user_id,$company_name,$type)=explode('__',$data);
	$approved_user_id=($alter_user_id!='')?$alter_user_id:$user_id;

	$precostArr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );


		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$sql = "select a.ID,a.SYS_NUMBER,a.COMPANY_ID,a.WO_DATE,c.JOB_NO_MST,c.PO_NUMBER,b.JOB_ID,b.PO_ID,b.SUPPLIER_ID,b.AMOUNT from wo_yarn_dyeing_mst a,garments_service_wo_dtls b,WO_PO_BREAK_DOWN c where a.id=b.mst_id and b.po_id=c.id and b.job_id=c.job_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.id in($sys_id)";
		$dataArr=sql_select($sql);

	 

		ob_start();
	?>
	

 
		<table border="1" rules="all">
			<tr>
				<th>To</th>
				<td>value</td>
			</tr>
			<tr>
				<th>From</th>
				<td>..</td>
			</tr>
			<tr>
				<th>Subject</th>
				<td>...</td>
			</tr>
		</table>
		  <br>
		  <p>
			<?
			if($type==5){
				echo "Dear Sir, <br>
				Your request has been rejected.  <br>
				Check the below comments and take action for approval";
			}
			else{
				echo "Dear Sir, <br>
				Please check below Garments Service Workorder Approval Request for your electronic approval <br>
				Click the below link to approve or reject with comments";
			}

			?>

		  </p>
	
		  <table border="1" rules="all">
		  <?
		//$grandtotal_amount=0;
				  
			foreach ($dataArr as $row) 
			{  

			?>
				<tr>
					<th>Company Name</th>
					<td><? echo $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
				</tr>
				<tr>
					<th>Buyer Name</th>
					<td><? echo $buyer_library_arr[$row[csf("SUPPLIER_ID")]]; ?></td>
				</tr>
				<tr>
					<th>Work order Date</th>
					<td><? echo $row[csf('WO_DATE')];?></td>
				</tr>
				<tr>
					<th>Workorder No.</th>
					<td><? echo $row[csf('SYS_NUMBER')];?></td>
				</tr>
				<tr>
					<th>Reason for Subcontract</th>
					<td></td>
				</tr>
				<tr>
					<th>Subcontract Value</th>
					<td><? echo $row[csf("AMOUNT")]; ?></td>
				</tr>
				<tr>
					<th>Job No.</th>
					<td><? echo $row[csf("JOB_NO_MST")]; ?></td>
				</tr>
				<tr>
					<th>Job No.</th>
					<td><?=$precostArr[$row[csf('JOB_NO_MST')]];?></td>
				</tr>
				
			<?
			}
	
	  ?>
	</table>

	
 <?
 

	$message=ob_get_contents();
	ob_clean();

	echo $message;die;
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and entry_form=76 and user_id=$approved_user_id and is_deleted=0");

	$mailToArr=array($email);
		
	$elcetronicSql = "SELECT a.SUPPLIER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.entry_form=76 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		// echo $elcetronicSql;die;
	
	$elcetronicSqlRes=sql_select($elcetronicSql);
	foreach($elcetronicSqlRes as $rows){
			if($rows['USER_EMAIL']){$mailToArr[]=$rows['USER_EMAIL'];}
			if($rows['BYPASS']==2){break;}
	}

	$to=implode(',',$mailToArr);
	$subject = "Erosion Approved Notification";				
	$header=mailHeader();
	
	if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);


}




?>
 
