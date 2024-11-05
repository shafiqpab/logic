<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

 
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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=53 order by b.sequence_no";
		 	 //echo $sql;die;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>

	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type =9
	exit();
}


function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
 

	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
  	//echo $electronic_app_sql;die;
	$electronic_app_sql_result=sql_select($electronic_app_sql);
	$dataArr=array();
	foreach($electronic_app_sql_result as $rows){

		if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){$rows['BUYER_ID'] = $lib_buyer_id_string;}
		if($rows['BRAND_ID']=='' || $rows['BRAND_ID']==0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}


		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];

		//$dataArr['bypass_by_group_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];

	}
	//print_r($buyer_wise_my_previous_group_arr);die;
	return $dataArr;
}


function getFinalUser($parameterArr=array()){
	$lib_buyer_id_string = implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
        if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){
            $rows['BUYER_ID'] = $lib_buyer_id_string;
        }

		if($rows['BRAND_ID']=='' || $rows['BRAND_ID'] == 0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}

		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}

	 //print_r($userDataArr[446]['BUYER_ID']);die;
	// print_r($buyer_wise_brand_id_arr[22]);die;
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) && $bbtsRows['buyer_id']>0)
				&& (in_array($bbtsRows['brand_id'],$usersDataArr[$user_id]['BRAND_ID']) || $bbtsRows['brand_id']==0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}

	 //print_r($finalSeq[332]);die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$txt_wo_no = str_replace("'","",$txt_wo_no);
	$approval_type = str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
    $txt_date = str_replace("'","",$txt_date);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $app_user_id = ($txt_alter_user_id!="") ? $txt_alter_user_id : $user_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 
		
	if($cbo_supplier_id>0) $where_con = " and a.supplier_id=$cbo_supplier_id";
	if($txt_wo_no>0) $where_con = " and a.WO_NUMBER LIKE '%$txt_wo_no' ";
	if($txt_date!="")
	{
		$txt_date = change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if($cbo_get_upto == 1) $where_con .= " and a.wo_date>'".$txt_date."'";
		else if($cbo_get_upto == 2) $where_con .= " and a.wo_date<='".$txt_date."'";
		else if($cbo_get_upto == 3) $where_con .= " and a.wo_date='".$txt_date."'";
	}


    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>53,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$buyer_brand_arr,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
      // print_r( $electronicDataArr);

    $my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
    $my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
    $electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

    $my_previous_bypass_no_seq = 0;
    rsort($electronicDataArr['bypass_seq_arr'][2]);
    foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
        if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
    }



    if($approval_type==0) // Un-Approve
	{  
		//Match data..................................

		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and b.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
	
		if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
			$where_con .= " and c.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
		}
		
		$data_mast_sql = "select a.ID, b.BUYER_ID,c.BRAND_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b,WO_PO_DETAILS_MASTER c where a.id = b.mst_id and b.job_id=c.id and a.entry_form=284 and a.COMPANY_NAME=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0  and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1  $where_con group by a.ID, b.BUYER_ID,c.BRAND_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY";
     	 //echo $data_mast_sql;die;
		
		// var_dump($electronicDataArr['group_seq_arr'][2]);die;
 

		$tmp_sys_id_arr=array();$sys_data_arr=array();
		$data_mas_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mas_sql_res as $row)
		{ 	//echo $my_previous_bypass_no_seq.'='.$row['APPROVED_GROUP_BY'];
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					
				
					if($seq<$my_seq){
						if(
							( in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0 || $row['BUYER_ID']=='') 
							  && (in_array($row['BRAND_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID']==0 || $row['BRAND_ID']=='') 
							  && ($row['APPROVED_GROUP_BY'] <= $group)
							)
						{
							//echo $electronicDataArr['sequ_by'][$seq]['BYPASS'].'='.$seq.',';
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

							//Do not delete comment code ..................................
							// if( ($group_stage > 2) || $electronicDataArr['sequ_by'][$seq]['BYPASS']==2){
							// 	break 2; break; 
							// }
							//........................................end;
							$group_stage_arr[$row['ID']][$group] = $group;
							
						}
						

					}
					 
				}

				 

				// echo $group_stage.',';
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
		 //print_r($my_group);die;

		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
                    $sql .= "SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.APPROVED_SEQU_BY=$seq and a.APPROVED_GROUP_BY=$group $sys_con and a.entry_form=284 and a.is_approved<>1 and a.status_active=1 and a.is_deleted=0 and b.item_category_id=1 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id, a.booking_type";
				}
		
			}
		}
	}
	else
	{   
       
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and b.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
	
		if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
			$where_con .= " and d.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
		} 
	 
	 	$sql = "SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE from wo_non_order_info_mst a, wo_non_order_info_dtls b,APPROVAL_MST c,WO_PO_DETAILS_MASTER d
        where a.id=b.mst_id and a.id=c.mst_id and b.mst_id = c.mst_id and d.id=b.job_id and a.company_name=$cbo_company_name and a.APPROVED_GROUP_BY=c.GROUP_NO and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']} $sys_con $where_con and a.entry_form=284 and a.is_approved<>0 and a.status_active=1 and a.is_deleted=0 and b.item_category_id=1 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and c.entry_form=53 group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id, a.booking_type";
    }

 	//echo $sql;die;
	
	$submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$nonOrder_booking_type = array(1 => "Aditional", 2=>"Compensative");
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:920px; margin-top:10px">
        <legend>Sample Or Additional Yarn WO Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="40">SL</th>
                    <th width="70">Work Order No</th>
                    <th width="140">Supplier</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="100">WO Basis</th>
                    <th width="100">Booking Type</th>
                    <th width="80">Source</th>
                    <th width="80">Pay Term</th>
                    <th>Submitted By</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($row['UPDATED_BY']=="" || $row['UPDATED_BY']==0) $row['UPDATED_BY']=$row['INSERTED_BY'];							
                            	?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row['ID']; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row['WO_NUMBER_PREFIX_NUM']); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                    </td>
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="70" align="center" style="color:#000; text-decoration: underline; cursor: pointer;" onclick="print_button_link('<? echo $company_name; ?>','<? echo $row['ID']; ?>');"><? echo $row['WO_NUMBER_PREFIX_NUM']; ?></td>
                                    <td width="140" style="word-break:break-all"><? echo $supplier[$row['SUPPLIER_ID']]; ?></td>
									<td width="70" align="center"><? if($row['WO_DATE']!="0000-00-00") echo change_date_format($row['WO_DATE']); ?></td>
									<td width="70" align="center"><? if($row['DELIVERY_DATE']!="0000-00-00") echo change_date_format($row['DELIVERY_DATE']); ?></td>
                                    
                                    <td width="100" style="word-break:break-all"><? echo $sample_wo_basis[$row['WO_BASIS_ID']]; ?></td>
                                    <? 
                                    if ($row['BOOKING_TYPE']==1)
                                    {	
                                    	?>
                                    	<td width="100" style="word-break:break-all; background-color: yellow;"><? echo $nonOrder_booking_type[$row['BOOKING_TYPE']]; ?></td>
                                    	<?
                                    }
                                    else
                                    {	
                                    	?>
                                    	<td width="100" style="word-break:break-all"><? echo $nonOrder_booking_type[$row['BOOKING_TYPE']]; ?></td>
                                    	<?
                                    }
                                    ?>
                                    <td width="80" style="word-break:break-all"><? echo $source[$row['SOURCE']]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $pay_term[$row['PAYTERM_ID']]; ?></td>
                                    <td style="word-break:break-all"><? echo $submittedByArr[$row['UPDATED_BY']]; ?></td>
								</tr>
								<?
								$i++;
							}
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" align="left" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left">
						<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
						&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
					</td>
				</tfoot>
			</table>
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

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$req_ids = str_replace("'","",$req_ids);
	$approval_type = str_replace("'","",$approval_type);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
    $target_app_id_arr = explode(',',$req_ids);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

    $sql = "select a.ID, b.BUYER_ID,c.BRAND_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY,a.READY_TO_APPROVED from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b,WO_PO_DETAILS_MASTER c where a.id = b.mst_id and b.job_id=c.id and a.entry_form=284 and a.COMPANY_NAME=$cbo_company_name  and a.id in($req_ids) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0  and a.READY_TO_APPROVED=1 group by a.ID, b.BUYER_ID,c.BRAND_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY";

	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '25**Please select ready to approved yes for approved this booking';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>$row['BRAND_ID'],'supplier_id'=>0,'store'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>53,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_ids) and entry_form=53 group by mst_id","mst_id","approved_no");
 





	if($approval_type==0) //Approve button
	{

        $id = return_next_id( "id","approval_mst", 1 ) ;
        $ahid = return_next_id( "id","approval_history", 1 ) ;	

        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $key => $mst_id)
        {
            
            $approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

            //print_r($finalDataArr['last_bypass_no_data_arr']);die;
            $approved_no = $max_approved_no_arr[$mst_id]*1;
            if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
                $approved_no = $max_approved_no_arr[$mst_id]+1;
                $approved_no_array[$mst_id]=$approved_no;
            }



            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",53,'".$mst_id."','".$user_sequence_no."','".$user_group_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
            
        
            if($history_data_array!="") $history_data_array.=",";
            $history_data_array.="(".$ahid.",53,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
            $ahid++;
            
            //mst data.......................
            $data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",'".$pc_date_time."',".$user_id_approval."")); 
        }    
        

        
        $flag=1;
        if($flag==1) 
        {  
            $field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1) $flag=1; else $flag=0; 
        }
            
        if($flag==1) 
        {
            $field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_DATE*APPROVED_BY"; 
            $rID2=execute_query(bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
            if($rID2) $flag=1; else $flag=0; 
        }

        if($flag==1)
        {
            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=53 and mst_id in ($req_ids)";
            $rID3=execute_query($query,1);
            if($rID3) $flag=1; else $flag=0;
        }
            
        if($flag==1)
        {
            $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
            $rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
            if($rID4) $flag=1; else $flag=0;
        }



        if(count($approved_no_array)){
            $approved_string="";
            foreach($approved_no_array as $key=>$value)
            {
                $approved_string.=" WHEN $key THEN $value";
            }

            $approved_string_mst="CASE id ".$approved_string." END";
            $approved_string_dtls="CASE mst_id ".$approved_string." END";

            $sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
            select
            '', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_ids)";


            $sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
            select
            '', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_ids)";

            
            if($flag==1)
            {
                $rID5 = execute_query($sql_insert,0);
                if($rID5) $flag=1; else $flag=0;
            }
            
            if($flag==1)
            {
                $rID6 = execute_query($sql_insert_dtls,1);
                if($rID6) $flag=1; else $flag=0;
            }
        }
  

		if($flag==1) $msg='19'; else $msg='21';

	}
	else if($approval_type==5)  // Denay
	{
        $ahid = return_next_id( "id","approval_history", 1 ) ;	

        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $key => $mst_id)
        {
            $approved_no = $max_approved_no_arr[$mst_id]*1;
 
            if($history_data_array!="") $history_data_array.=",";
            $history_data_array.="(".$ahid.",53,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
            $ahid++;
        } 
        
        $flag=1;
		if($flag==1)
		{
			$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'2*0*0*0',"id",$req_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=53 and mst_id in ($req_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=53 and mst_id in ($req_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		$response=$reqs_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	else  // Un-Approve button
	{
        $ahid = return_next_id( "id","approval_history", 1 ) ;	

        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $key => $mst_id)
        {
            $approved_no = $max_approved_no_arr[$mst_id]*1;
 
            if($history_data_array!="") $history_data_array.=",";
            $history_data_array.="(".$ahid.",53,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
            $ahid++;
        } 
        
        $flag=1;
		if($flag==1)
		{
			$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'0*0*0*0',"id",$req_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=53 and mst_id in ($req_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=53 and mst_id in ($req_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		$response=$reqs_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}

	//echo "22**".$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4;die;

	
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
