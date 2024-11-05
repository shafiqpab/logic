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
        echo create_drop_down( "cbo_supplier_id", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(5,8) and a.status_active=1 and a.is_deleted=0 and c.tag_company ='$data[0]' group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/dyes_chemical_issue_approval_controller');",0 );
    }
    
}

 

function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
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
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
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


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{  
	$process = array( &$_POST );
    //print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	
	$txt_issue_no = str_replace("'","",$txt_issue_no);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$cbo_purpose = str_replace("'","",$cbo_purpose);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;



	//if($txt_issue_no){$where_con .= " and a.issue_number =".$txt_issue_no.""; }
	if($txt_issue_no){$where_con .= " and a.issue_number like('%".$txt_issue_no."')"; }
	if($cbo_purpose){$where_con .= " and a.issue_purpose =".$cbo_purpose.""; }

	if($txt_date_from && $txt_date_to){
		$where_con .= " and a.issue_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	}
	

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
	$division=return_library_array("select id, division_name from lib_division ",'id','division_name');
	$department=return_library_array("select id, department_name from lib_department ",'id','department_name');
	$section=return_library_array("select id, section_name from lib_section ",'id','section_name');
	//............................................................................
	
	


    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_id,'ENTRY_FORM'=>81,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
    
	
	//var_dump($electronicDataArr);die;
	
	//print_r($user_id_approval);
 
	 
	
	if($approval_type==0) // Un-Approve
	{  
		//Match data..................................
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		// }
        

		$data_mast_sql = "select a.ID, b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.id,
        a.is_posted_account 
        from inv_issue_master a left join dyes_chem_issue_requ_mst b on b.id = a.req_no
        where a.company_id=$cbo_company_id  and a.entry_form=5  and a.status_active=1 and a.is_deleted=0 and a.READY_TO_APPROVE=1 and a.IS_APPROVED<>1 ";
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
				$sql .= "select a.ID,a.item_category,a.loan_party,a.knit_dye_company,a.challan_no,a.issue_purpose,a.knit_dye_source,a.supplier_id,a.division_id,a.department_id,a.section_id, b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.batch_no, a.id,
                a.is_posted_account 
                from inv_issue_master a left join dyes_chem_issue_requ_mst b on b.id = a.req_no
                where a.company_id=$cbo_company_id $where_con and a.entry_form=5 and a.status_active=1 and a.is_deleted=0 and a.READY_TO_APPROVE=1 and a.IS_APPROVED<>1   and a.APPROVED_SEQU_BY=$seq $sys_con";
			}
		}
	}
	else
	{     

		$sql = "select a.ID,a.item_category,a.loan_party,a.knit_dye_company,a.challan_no,a.issue_purpose,a.knit_dye_source,a.supplier_id,a.division_id,a.department_id,a.section_id, b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.batch_no, a.id,
		a.is_posted_account
        from APPROVAL_MST c, inv_issue_master a left join dyes_chem_issue_requ_mst b on b.id = a.req_no
        where a.company_id=$cbo_company_id $where_con and a.status_active=1 and a.is_deleted=0 and a.READY_TO_APPROVE=1 and a.IS_APPROVED<>0 and  c.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and c.entry_form=81 and c.mst_id=a.id ";

    }
	//echo $sql;die();
	$precostArr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
    $supplier=return_library_array("select id, company_name from lib_company ",'id','company_name');
    $supplier_loan=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
	// echo $sql;die();

	 $nameArray=sql_select( $sql );
	 $sys_id_arr=array();
	 foreach ($nameArray as $row)
	 {
		$sys_id_arr[$row['ID']]=$row['ID'];
	 }

	//

	$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit 
	from inv_issue_master a, dyes_chem_issue_dtls b 
	where a.id=b.mst_id and a.entry_form=5  and a.status_active=1 and b.status_active=1");


	//echo $sql;die();
							
							foreach($total_issue_sql as $row)
							{
								$total_issue_arr[$row[csf("product_id")]]+=$row[csf("req_qny_edit")];
								$prev_issue_arr[$row[csf("issue_number")]]+=$row[csf("req_qny_edit")];
							}

							// echo "<pre>";
							// print_r($prev_issue_arr); 
							//   echo "</pre>";die();

		$total_amount_sql=sql_select("select a.issue_number, b.SUB_PROCESS, b.PRODUCT_ID, c.BATCH_LOT, c.ID AS TRANS_ID, c.CONS_QUANTITY ,c.cons_amount
		from INV_ISSUE_MASTER a, DYES_CHEM_ISSUE_DTLS b, INV_TRANSACTION c 
		where a.id=b.mst_id and b.trans_id = c.id and a.id=c.mst_id and  a.entry_form=5 $where_con and a.status_active=1 and b.status_active=1 and c.status_active=1");


		foreach($total_amount_sql as $row)
							{
								//$total_issue_arr[$row[csf("product_id")]]+=$row[csf("req_qny_edit")];
								$total_amount_arr[$row[csf("issue_number")]]+=$row[csf("cons_amount")];
							}

							// echo "<pre>";
							// print_r($total_amount_arr); 
							//   echo "</pre>";die();

									

    //echo $amount;die();

	//$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";

	$item_cat_sql=sql_select("select a.issue_number,b.item_category, b.product_id, b.req_qny_edit as req_qny_edit 
	from inv_issue_master a, dyes_chem_issue_dtls b 
	where a.id=b.mst_id and a.entry_form=5 $where_con and a.status_active=1 and b.status_active=1 GROUP BY a.issue_number,b.product_id,b.item_category,b.req_qny_edit");

	// echo "select a.issue_number,b.item_category, b.product_id, b.req_qny_edit as req_qny_edit 
	// from inv_issue_master a, dyes_chem_issue_dtls b 
	// where a.id=b.mst_id and a.entry_form=5 $where_con  and a.status_active=1 and b.status_active=1 GROUP BY a.issue_number,b.product_id,b.item_category,b.req_qny_edit";die();


	//echo $item_cat_sql;die();
							
		foreach($item_cat_sql as $row)
		{
			//$pro_item_arr[$row[csf("product_id")]]=$item_category[$row[csf("item_category_id")]];
			$issue_item_arr[$row[csf("issue_number")]].=$item_category[$row[csf("item_category")]].",";
		}

		// echo "<pre>";
		// print_r($issue_item_arr); 
		//   echo "</pre>";die();
			
	
	
 
	 $sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=81 ".where_con_using_array($sys_id_arr,0,'mst_id')."  and INSERTED_BY=$user_id_approval order by id asc";
	// echo $sql_cause;	
	 $nameArray_cause=sql_select($sql_cause);
	 $app_cause_arr=array();
	 foreach($nameArray_cause as $row)
	 {
		 $app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	 }

	$width=1450;

    ?> 


    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Erosion Approval</legend>
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<th width="25"></th>                   
                    <th width="35">SL</th>                   
                    <th width="150">Issue ID</th>
                    <th width="100">Issue Date</th>               
                    <th width="100">Item Category</th>                   
                    <!-- <th width="100">Sub Process</th>                    -->
                    <th width="100">Challan No</th>                   
                    <th width="100">Issue Purpose</th>                   
                    <th width="100">Dyeing Source</th>                   
                    <th width="100">Loan/Sales Party/Supp</th>                   
                    <th width="100">Division</th>                   
                    <th width="100">Department</th>                   
                    <th width="100">Section</th>                   
                    <th width="100">Issue Qnty</th>                   
                    <th width="100">Amount</th>                   
                   
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
                                    <td width="150" align="center"><a href="javascript:fn_generate_print(<? echo $row['ID']; ?>,<? echo $row['COMPANY_ID']?>)"><? echo $row[csf('issue_number')]; ?></a></td>                                    
									<td width="100" align="center"><? if($row[csf('issue_date')]!="0000-00-00") echo change_date_format($row[csf('issue_date')]); ?></td>
                                    <td width="100" align="center"><p><?php echo implode(',',array_unique(explode(',',rtrim($issue_item_arr[$row[csf("issue_number")]], ',')))); ?></p></td>				
                                  

									<td width="100" align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
									<td width="100" align="center"><p><? echo $general_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
									<td width="100" align="center"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?></p></td>
									<td width="100"><? if($row[csf('issue_purpose')]==5){
									       {echo $supplier_loan[$row[csf('loan_party')]];}
									     }
									       else { echo $supplier[$row[csf('knit_dye_company')]];}
									?></td>	
									<td width="100" align="center"><p><? echo $division[$row[csf('division_id')]]; ?></p></td>
									<td width="100" align="center"><p><? echo $department[$row[csf('department_id')]]; ?></p></td>
									<td width="100" align="center"><p><? echo $section[$row[csf('section_id')]]; ?></p></td>
									<td width="100" align="right"><p><?echo number_format($prev_issue_arr[$row[csf("issue_number")]],2);?></p></td>
									<td width="100" align="right"><p><?=number_format($total_amount_arr[$row[csf("issue_number")]],4);?></p></td>

									                              
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
	
	$txt_issue_no = str_replace("'","",$txt_issue_no);
	$cbo_purpose = str_replace("'","",$cbo_purpose);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;





	//echo "10**".'zdhgdsfgsgf';die;

	//............................................................................
	
	$sql = "select a.ID  from inv_issue_master a where a.COMPANY_ID=$cbo_company_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
    //echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
        //if($row['READY_TO_APPROVE'] != 1){echo '21**Ready to approve yes is mandatory';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>0,'brand_id'=>0,'supplier_id'=>0,'store'=>0);
	}
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_id,'ENTRY_FORM'=>81,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
 

	if($approval_type==5)
	{

		$rID1=sql_multirow_update("inv_issue_master","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=81 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=81 and mst_id in ($target_ids)";
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
			$data_array.="(".$id.",81,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",81,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
			$rID2=execute_query(bulk_update_sql_statement( "inv_issue_master", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=81 and mst_id in ($target_ids)";
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
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=81 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("inv_issue_master","is_approved*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=81 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=81 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=81 and current_approval_status=1 and mst_id in ($target_ids)";
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
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_id and b.entry_form=81 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;die();
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
 
