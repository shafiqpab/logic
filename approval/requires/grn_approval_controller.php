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

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

//$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 


function getSequence($parameterArr=array()){
	$lib_store_arr=implode(',',(array_keys($parameterArr['lib_store_arr'])));
	
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['STORE_ID']=$rows['STORE_ID'];
	}
		
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	// echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($userDataArr[$rows['USER_ID']]['STORE_ID']==''){$rows['STORE_ID']=$lib_store_arr;}
		else{$rows['STORE_ID']=$userDataArr[$rows['USER_ID']]['STORE_ID'];}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_store_arr=implode(',',(array_keys($parameterArr['lib_store_arr'])));

	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['STORE_ID']=$rows['STORE_ID'];
	}
		
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

		if($userDataArr[$rows['USER_ID']]['STORE_ID']==''){$rows['STORE_ID']=$lib_store_arr;}
		else{$rows['STORE_ID']=$userDataArr[$rows['USER_ID']]['STORE_ID'];}

		$usersDataArr[$rows['USER_ID']]['STORE_ID']=explode(',',$rows['STORE_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}
	//print_r($usersDataArr);die;

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['STORE_ID'],$usersDataArr[$user_id]['STORE_ID']) || $bbtsRows['STORE_ID']==0){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
$store_arr[0]='';

if($action=="report_generate")
{
	$process = array( &$_POST );
   //print_r($process);
	extract(check_magic_quote_gpc( $process )); 
    
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category); 
	$txt_grn_no=str_replace("'","",$txt_grn_no);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_grn_date_from=str_replace("'","",$txt_grn_date_from);
	$txt_grn_date_to=str_replace("'","",$txt_grn_date_to);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);

	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


	$where_con='';
	if($cbo_store_name){$where_con .= " and a.STORE_ID =".$cbo_store_name.""; }
	if($txt_grn_no){$where_con .= " and a.RECV_NUMBER like('%".$txt_grn_no."')"; }
	if($txt_grn_date_from && $txt_grn_date_to){
		$where_con .= " and a.RECEIVE_DATE BETWEEN '".$txt_grn_date_from."' AND '".$txt_grn_date_to."'";	
	}


	//............................................................................
	
	
	$electronicDataArr=getSequence(array('company_id'=>$company_id,'page_id'=>$menu_id,'user_id'=>$user_id_approval,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>$store_arr));


		if($approval_type==0){	
			//Match data..................................
			if($electronicDataArr['user_by'][$user_id_approval]['STORE_ID']){
				$where_con .= " and a.STORE_ID in(".$electronicDataArr['user_by'][$user_id_approval]['STORE_ID'].",0)";
				$electronicDataArr['sequ_by'][0]['STORE_ID']=$electronicDataArr['user_by'][$user_id_approval]['STORE_ID'];
			}
			
				$data_mas_sql = "SELECT A.ID,a.STORE_ID, A.RECV_NUMBER, A.COMPANY_ID, A.RECEIVE_BASIS, A.PAY_MODE, A.SUPPLIER_ID, A.STORE_ID, A.SOURCE, A.CURRENCY_ID, A.CHALLAN_NO, A.RECEIVE_DATE, A.CHALLAN_DATE, A.LC_NO, A.EXCHANGE_RATE, A.BOOKING_ID, A.BOOKING_NO, A.BOOKING_WITHOUT_ORDER, A.RECEIVE_PURPOSE, A.LOAN_PARTY, A.REMARKS from INV_RECEIVE_MASTER a,  QUARANTINE_PARKING_DTLS b where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.IS_QC_PASS=1  $where_con  and a.company_id=$company_id";
				 //echo $data_mas_sql; 

				$tmp_sys_id_arr=array();
				$data_mas_sql_res=sql_select( $data_mas_sql );
				foreach ($data_mas_sql_res as $row)
				{ 
					for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
						if( in_array($row['STORE_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['STORE_ID'])))
						{
							if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
								$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
							}
							else{
								$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
								break;
							}
						}
					}
				}
			//..........................................Match data;
			//var_dump($tmp_sys_id_arr);die;
			
			
			
			$sql='';
			for($seq=0;$seq<=count($electronicDataArr[sequ_arr]); $seq++ ){
				$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
				
				if($tmp_sys_id_arr[$seq]){
					if($sql!=''){$sql .=" UNION ALL ";}
					$sql .= " SELECT A.ID,a.STORE_ID, A.RECV_NUMBER, A.COMPANY_ID, A.RECEIVE_BASIS, A.PAY_MODE, A.SUPPLIER_ID, A.STORE_ID, A.SOURCE, A.CURRENCY_ID, A.CHALLAN_NO, A.RECEIVE_DATE, A.CHALLAN_DATE, A.LC_NO, A.EXCHANGE_RATE, A.BOOKING_ID, A.BOOKING_NO, A.BOOKING_WITHOUT_ORDER, A.RECEIVE_PURPOSE, A.LOAN_PARTY, A.REMARKS from INV_RECEIVE_MASTER a,  QUARANTINE_PARKING_DTLS b where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.IS_QC_PASS=1  and a.APPROVED_SEQU_BY=$seq $sys_con  and a.is_approved<>1  and a.company_id=$company_id order by a.ID desc";
				}
			
			}
			
		}
		else
		{
			$sql = "SELECT A.ID,a.STORE_ID, A.RECV_NUMBER, A.COMPANY_ID, A.RECEIVE_BASIS, A.PAY_MODE, A.SUPPLIER_ID, A.STORE_ID, A.SOURCE, A.CURRENCY_ID, A.CHALLAN_NO, A.RECEIVE_DATE, A.CHALLAN_DATE, A.LC_NO, A.EXCHANGE_RATE, A.BOOKING_ID, A.BOOKING_NO, A.BOOKING_WITHOUT_ORDER, A.RECEIVE_PURPOSE, A.LOAN_PARTY, A.REMARKS from INV_RECEIVE_MASTER a,  QUARANTINE_PARKING_DTLS b,APPROVAL_MST c where a.id=b.mst_id and a.id=c.mst_id and a.APPROVED_SEQU_BY=c.SEQUENCE_NO  and a.entry_form=529  and a.entry_form=529 and c.entry_form=69 and b.ITEM_CATEGORY_ID=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.IS_QC_PASS=1  and a.APPROVED_SEQU_BY={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  $where_con  and a.company_id=$company_id order by a.ID desc";		
		}
	//	echo $sql;


		$nameArray=sql_select($sql);
		$sys_id_arr=array();$dataArr=array();
		foreach( $nameArray as $rows){
			$sys_id_arr[$rows['ID']]=$rows['ID'];
			$dataArr[$rows['ID']]=$rows;
		}
 


	$sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=69  and INSERTED_BY=$user_id_approval ".where_con_using_array($sys_id_arr,0,'MST_ID')." order by id asc";	
	$nameArray_cause=sql_select($sql_cause);
	$app_cause_arr=array();
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	}





	$width=1350;
    ?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Erosion Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<th width="25"></th>                   
                    <th width="50">SL</th>                   
                    <th width="125"> Store Name</th>
                    <th width="125">Trans. Date</th>
                    <th width="100">GRN No.</th> 
                    <th width="150">Challan No</th>                   
                    <th width="100">Supplier Name/Loan Party</th>                   
                    <th width="100">Basis</th>                   
                    <th width="150">WO/PI NO./LC No./Lot No.</th> 
                    <th width="80">Currency</th> 
                    <th width="80">GRN Value</th> 
                    <th width="80">Files</th> 
                    <th width="80">Images</th> 
					<th>Comments </th>
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:330px;" id="buyer_list_view">
                <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
					   <?		 
                        $i=1; $all_approval_id='';

						//print_r($nameArray);
                        foreach ($dataArr as $row)
                        { 
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
                       
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                                        <input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $row['ID']; ?>" />
                                   </td> 
                                    <td width="50" align="center"><?php echo $i;?></p></td>
                                    <td width="125" align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>                                    
                                    <td width="125" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>	
                                    <td width="100" align="center"><? echo $row[csf('recv_number')];?></td>
                                    <td width="150" align="center"><? echo $row[csf('challan_no')]; ?></td>				
                                    <td width="100" align="center"><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
                                    <td width="100" align="center"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                                    <td width="150" align="center"><? echo $row[csf('booking_no')]; ?></td>
                                    <td width="80" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                                    <td width="80" align="right"></td>
                                    <td width="80" align="right"><input type="button" class="image_uploader" style="width:70px" value="PDF" onClick="openImgFile ('<?=$row[csf('recv_number')];?>','yarn_grn_receive_file')"></td>
									 
									<td width="80" align="right"><input type="button" class="image_uploader" style="width:70px" value="image" onClick="openImgFile ('<?=$row[csf('recv_number')];?>', 'yarn_grn_receive_image')"></td>

									<td style="cursor:pointer;" id="comments_<?= $row['ID'];?>" onClick="openmypage_refusing_cause('requires/grn_approval_controller.php?action=refusing_cause_popup','Comments','<?= $row['ID'];?>');"><?=$app_cause_arr[$row['ID']];?></td>			

												                                  
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
                    <td width="25" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
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
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category); 
	$txt_grn_no=str_replace("'","",$txt_grn_no);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_grn_date_from=str_replace("'","",$txt_grn_date_from);
	$txt_grn_date_to=str_replace("'","",$txt_grn_date_to);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);

	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$target_ids = str_replace("'","",$target_ids);

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	//............................................................................
 
	$approved_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	//echo "1**".$approved_user_id;die;

	
	$sql = "select a.ID, a.STORE_ID from INV_RECEIVE_MASTER a where a.COMPANY_ID=$company_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
	//echo $sql;die;
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('STORE_ID'=>$row['STORE_ID']);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$company_id,'page_id'=>$menu_id,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>$store_arr,'product_dept_arr'=>0,'match_data'=>$matchDataArr));

	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$approved_user_id];


	  //print_r($sequ_no_arr_by_sys_id);die;

 
	if($approval_type==5)
	{

		$rID1=sql_multirow_update("erosion_entry","approved*ready_to_approve*APPROVED_SEQU_BY",'2*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=529 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
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
			$data_array.="(".$id.",69,".$mst_id.",".$user_sequence_no.",".$approved_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$approved_user_id]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",69,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$approved_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
		//echo "10**insert into approval_mst ($field_array)values.$data_array";die;
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "INV_RECEIVE_MASTER", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=69 and mst_id in ($target_ids)";
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
		// $next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=69 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		// if(count($next_user_app)>0)
		// {
		// 	echo "25**unapproved"; 
		// 	disconnect($con);
		// 	die;
		// }

		$rID1=sql_multirow_update("INV_RECEIVE_MASTER","is_approved*APPROVED_SEQU_BY",'0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=69 and un_approved_by != '".$user_id_approval."' and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=69 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=69 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		 // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
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

if($action=='yarn_grn_receive_image'){
 
	$file_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='".$id."' and FORM_NAME='yarn_grn_receive_image'", "ID", "IMAGE_LOCATION"  );
	foreach($file_arr as $file){
		echo "<img src='../../".$file."'/> ";
	}
}

if($action=='yarn_grn_receive_file'){
	$file_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='".$id."' and FORM_NAME='yarn_grn_receive_file'", "ID", "IMAGE_LOCATION"  );
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
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=69 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
               // echo $sql;die();
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
	$sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=69 and mst_id='$quo_id' and INSERTED_BY=$app_user_id order by id asc";	
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
			http.open("POST","grn_approval_controller.php",true);
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
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=529 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
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
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=69 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=69 and id=$idpre"; //die;
		
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
		$data_array = "(".$id.",69,".$quo_id.",'".$refusing_cause."',".$app_user_id.",'".$pc_date_time."')";
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

		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=69 group by mst_id","id");
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
	//http://localhost/platform-v3.5/approval/requires/erosion_approval_controller.php?data=20__reza@logicsoftbd.com__3&action=app_mail_notification

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	

	list($sys_id,$email,$alter_user_id,$company_name,$type)=explode('__',$data);
	$approved_user_id=($alter_user_id!='')?$alter_user_id:$user_id;

		
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

		$sql="select c.COMPANY_ID,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS,c.PRECAUTIONERY_FUTURE_PLANS,b.PUB_SHIPMENT_DATE,b.SHIPMENT_DATE, b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY, a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO,d.EMBEL_TYPE,e.SEW_SMV from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst e,erosion_entry c left join pro_garments_production_mst d on c.PO_BREAK_DOWN_ID=d.PO_BREAK_DOWN_ID   where a.job_no=b.job_no_mst  and a.job_no=e.job_no  and b.id=c.PO_BREAK_DOWN_ID and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.id in($sys_id)";
		$dataArr=sql_select($sql);

	
		$erosion_type=array(1=>"Discount Shipment",2=>"Sea-Air Shipment",3=>"Air Shipment");
		$buyer_library_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");$lib_teamleader=return_library_array( "select id,team_leader_name from lib_team_mst", "id", "team_leader_name");
		$lib_dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info ", "id", "team_member_name");
		$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row['PO_RECEIVED_DATE']))), date("Y-m-d",strtotime(change_date_format($row['SHIPMENT_DATE']))) );
		$booking_arr=return_library_array( "select BOOKING_NO from WO_BOOKING_DTLS where PO_BREAK_DOWN_ID={$row['PO_BREAK_DOWN_ID']} and BOOKING_TYPE=1 and STATUS_ACTIVE=1 and IS_DELETED=0", "BOOKING_NO", "BOOKING_NO");
	

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
				Please check below erosion request for your electronic approval <br>
				Click the below link to approve or reject with comments";
			}

			?>

		  </p>
	
		  <table border="1" rules="all">
		  <?
		//$grandtotal_amount=0;
				  
				foreach ($dataArr as $row) 
				{  
					$precost_data_arr=get_precost_data(array('company_id'=>$row[csf("COMPANY_ID")],'job_no'=>$row['JOB_NO'],'po_id'=>$row['PO_BREAK_DOWN_ID']));
			 
				?>
					<tr>
						<th>Company Name</th>
						<td><? echo $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
					</tr>
					<tr>
						<th>Buyer Name</th>
						<td><? echo $buyer_library_arr[$row[csf("BUYER_NAME")]]; ?></td>
					</tr>
					<tr>
						<th>Erosion Date</th>
						<td><? echo $row[csf('EROSION_DATE')];?></td>
					</tr>
					<tr>
						<th>Erosion No.</th>
						<td><? echo $row[csf('SHIP_APP_REQ_NO')];?></td>
					</tr>
					<tr>
						<th>Erosion Type</th>
						<td><? echo $erosion_type[$row[csf("EROSION_TYPE")]]; ?></td>
					</tr>
					<tr>
						<th>Erosion Value</th>
						<td><? echo $row[csf('EROSION_VALUE')];?></td>
					</tr>
					<tr>
						<th>Team Leader</th>
						<td><? echo $lib_teamleader[$row[csf("TEAM_LEADER")]]; ?></td>
					</tr>
					<tr>
						<th>Dealing Merchant</th>
						<td><? echo $lib_dealing_merchant[$row[csf("DEALING_MARCHANT")]]; ?></td>
					</tr>
					<tr>
						<th>Factory Merchant</th>
						<td><? echo $lib_dealing_merchant[$row[csf("FACTORY_MARCHANT")]]; ?></td>
					</tr>
					<tr>
						<th>Job No.</th>
						<td><? echo $row[csf('JOB_NO')];?></td>
					</tr>
					<tr>
						<th>Budget Profit</th>
						<td><?=number_format($precost_data_arr['TotalMargin'],2); ?></td>
					</tr>
					<tr>
						<th>Profit After Erosion</th>
						<td><?=number_format($precost_data_arr['TotalMargin']-$row[csf('EROSION_VALUE')],2); ?></td>
					</tr>
				<?
			}
	
	  ?>
	</table>

	
	<?
 

	$message=ob_get_contents();
	ob_clean();

	echo $message;die;
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and entry_form=529 and user_id=$approved_user_id and is_deleted=0");

	$mailToArr=array($email);
		
	$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.entry_form=529 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
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
 
