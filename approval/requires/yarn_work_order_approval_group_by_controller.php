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


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=2 order by b.sequence_no";
		 	// echo $sql;die;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
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
	exit();
}

//Group app start..............................................................

function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO,FABRIC_SOURCE FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
 	// echo $electronic_app_sql;die;
	$electronic_app_sql_result=sql_select($electronic_app_sql);
	$dataArr=array();
	foreach($electronic_app_sql_result as $rows){

		if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){$rows['BUYER_ID'] = $lib_buyer_id_string;}

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
	$lib_buyer_id_string = implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO,FABRIC_SOURCE FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
        if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){
            $rows['BUYER_ID'] = $lib_buyer_id_string;
        }
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		$usersDataArr[$rows['USER_ID']]['FABRIC_SOURCE']=explode(',',$rows['FABRIC_SOURCE']);
		
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
				(in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) || $bbtsRows['buyer_id'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}



$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$txt_wo_no = str_replace("'","",$txt_wo_no);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$txt_date = str_replace("'","",$txt_date);
	$app_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>2,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));

    $my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
    $my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
    $electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1]; 
	
    $my_previous_bypass_no_seq = 0;
    rsort($electronicDataArr['bypass_seq_arr'][2]);
    foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
        if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
    }

	if($cbo_supplier_id != 0){$where_con .= " and a.supplier_id like '%$cbo_supplier_id%'";}
	if($txt_wo_no != ''){$where_con .= " and a.wo_number_prefix_num LIKE '%$txt_wo_no'";}
	if($txt_date!="")
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if($cbo_get_upto==1) $where_con .= " and a.wo_date>'".$txt_date."'";
		else if($cbo_get_upto==2) $where_con .= " and a.wo_date<='".$txt_date."'";
		else if($cbo_get_upto==3) $where_con .= " and a.wo_date='".$txt_date."'";
	}

	

	if($my_seq == ''){echo "<u><h2 style='color:red;'>You have no approval permission.</h2></u>";die();}

	if($approval_type==0) // Un-Approve
	{  
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and b.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}

		$data_mast_sql = "SELECT a.ID, a.COMPANY_NAME, a.SUPPLIER_ID,a.APPROVED_GROUP_BY,a.APPROVED_SEQU_BY,b.BUYER_ID
        from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $where_con and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1 and a.entry_form=144";

		$tmp_sys_id_arr=array();$sys_data_arr=array();
		$data_mas_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mas_sql_res as $row)
		{ 
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					if($seq<$my_seq){
						if(
							(in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0) && ($row['APPROVED_GROUP_BY'] <= $group)
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
						}//if end;
					}//if end;
				}//foreach end;

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

		 //print_r($electronicDataArr['group_seq_arr'][$my_group]);die;


 		// print_r($tmp_sys_id_arr);die;
		 // print_r($group_stage);die;
		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql .= "SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,b.BUYER_ID
                    from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.APPROVED_SEQU_BY=$seq and a.APPROVED_GROUP_BY=$group $sys_con  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approved=1 and a.is_approved<>1 and a.entry_form=144 group by a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,b.BUYER_ID";
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
		
		$sql = "SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,b.BUYER_ID
        from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b,APPROVAL_MST c where a.id=b.mst_id and a.company_name=$cbo_company_name and a.APPROVED_GROUP_BY=c.GROUP_NO $where_con and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']} $where_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ready_to_approved=1 and a.is_approved<>0 and a.entry_form=144 and c.entry_form=2 group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,b.BUYER_ID"; 
    }

	 //echo $sql; 
	 
	
	 $submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');

	?>
    
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = (app_type==0)?'Not Appv. Cause':'Not Un-Appv. Cause';
			var page_link = 'requires/yarn_work_order_approval_group_by_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

	</script>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1030px; margin-top:10px">
        <legend>Yarn Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" align="left" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="70">Work Order No</th>
                    <th width="70">Buyer</th>
                    <th width="120">Supplier</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="100">WO Basis</th>
                    <th width="80">Source</th>
                    <th width="80">Pay Term</th>
                    <th width="80">Submitted By</th>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:1030px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								
								$value=$row[csf('id')];
								 
								$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=5 and report_id =45 and is_deleted=0 and status_active=1");
                            	$format_idss=explode(",",$print_report_format);    
       							//echo $format_ids;
       							foreach ($format_idss as $key => $format_ids) 
       							{
	                                if($format_ids == 78){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$cbo_company_name*".$row[csf('id')]."*Yarn Purchase Order*0*3*".$row[csf('is_approved')]."*6&action=yarn_work_order_print' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 84){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$cbo_company_name*".$row[csf('id')]."*Yarn Purchase Order*1*0*".$row[csf('is_approved')]."&action=print_to_html_report' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 85){
	                                	//echo 'system';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$cbo_company_name*".$row[csf('id')]."*Yarn Purchase Order*2*0*".$row[csf('is_approved')]."&action=print_to_html_report2' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 193){
	                                	//echo 'systemfalse';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$cbo_company_name*".$row[csf('id')]."*Yarn Purchase Order*4*0*".$row[csf('is_approved')]."&action=print_to_html_report4' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }else{
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$cbo_company_name*".$row[csf('id')]."*Yarn Purchase Order*0*1*".$row[csf('is_approved')]."&action=yarn_work_order_print5' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                            	}
                            	}
								if($row[csf('updated_by')]=="" || $row[csf('updated_by')]==0) $row[csf('updated_by')]=$row[csf('inserted_by')];
								
                            	?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                        
										<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                        
                                        
                                    </td>
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70"><?= $variable; ?></td>
									<td width="70"><?= $buyer_arr[$row['BUYER_ID']]; ?></td>
                                    <td width="120" style="word-break:break-all"><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
									<td width="70" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td width="70" align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
                                    
                                    <td width="100" style="word-break:break-all"><? echo $wo_basis[$row[csf('wo_basis_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $source[$row[csf('source')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $pay_term[$row[csf('payterm_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $submittedByArr[$row[csf('updated_by')]]; ?></td>
                                    <td align="center">
                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i;?>)"></td>
								</tr>
								<?
								$i++;
							}
							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="921" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	list($wo_id,$app_type,$app_cause,$approval_id)=explode('_',$data);

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=2 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
		}
	}

	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","yarn_work_order_approval_group_by_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing();
				 //alert(http.responseText);

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}

		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","yarn_work_order_approval_group_by_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;

		}

		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split('**');
				release_freezing();
			}
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="cause_1" id="cause_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('cause_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('cause_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	

	if($approval_type==0)
	{

   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "10**reza_".$approved_no_history.'_'.$approved_no_cause; die;
			

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				 //echo "10**".$data_array; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
					
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			
			

		}

		if ($operation==1)  // Update Here
		{

		}

	}//type=0
	if($approval_type==1)
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=2 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");


			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");

			if($unapproved_cause_id=="")
			{

				//echo "shajjad_".$unapproved_cause_id; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}

				disconnect($con);
				die;
			}
			else
			{

				//echo "10**entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
		}

	}//type=1
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	$msg=''; $flag=''; $response='';

	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$req_ids = str_replace("'","",$req_ids);
    $appv_causes = str_replace("'","",$appv_causes);
	$appv_causes_arr = explode('**',$appv_causes);
    $user_id_approval = ($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	$target_app_id_arr = explode(',',$req_ids);	


	$sql = "SELECT a.ID, a.READY_TO_APPROVED,a.IS_APPROVED,a.COMPANY_NAME, a.SUPPLIER_ID,a.APPROVED_GROUP_BY,a.APPROVED_SEQU_BY,b.BUYER_ID
	from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1 and a.id in($req_ids) and a.entry_form=144";

	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '25**Please select ready to approved yes for approved this booking';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>0,'supplier_id'=>0,'fb_source'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>2,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
 
	$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];

	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
   // print_r($user_sequence_no);die;
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_ids) and entry_form=2 group by mst_id","mst_id","approved_no");
 
	//print_r($target_app_id_arr);die;
	
 	if($approval_type==0)
	{ 

		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		

        foreach($target_app_id_arr as $key => $mst_id)
        {
		 
			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

			//print_r($finalDataArr['last_bypass_no_data_arr']);die;

			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
				$approved_no_array[$mst_id] = $approved_no;
			}

			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",2,'".$mst_id."','".$user_sequence_no."','".$user_group_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
		
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",2,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
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
			$usql = bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
			//echo $usql;oci_rollback($con);die;
			$rID2=execute_query($usql);
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=2 and mst_id in ($req_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		if(count($approved_no_array)>0 && $flag)
		{
			$approved_string="";

			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN TO_NUMBER($key) THEN '".$value."'";
			}

			$approved_string_mst="CASE id ".$approved_string." END";
		    $approved_string_dtls="CASE mst_id ".$approved_string." END";

			$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
			select
			'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_ids)";

           //echo $sql_insert;die;

			//-----------------------------------------Booking dtls-------------------------------------	
            $sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
			select
			'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_ids)";


				$rID5=execute_query($sql_insert,0);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=0;
				}
	
				$rID6=execute_query($sql_insert_dtls,1);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			
	
		}


		
		 //echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5.",".$rID6;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else if($approval_type==5)
	{              
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		$target_app_id_arr = explode(',',$req_ids);	
        foreach($target_app_id_arr as $key => $mst_id)
        {
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";

			$history_data_array.="(".$ahid.",2,".$mst_id.",'".$approved_no."','".$user_sequence_no."',0,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
			$ahid++;
        }

		

		$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'2*0*0*0',"id",$req_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=2 and mst_id in ($req_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=2 and mst_id in ($req_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, COMMENTS,approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}

		//echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5;oci_rollback($con);die;
		
		$response=$req_ids;
		if($flag==1) $msg='50'; else $msg='0';
		
	}
	else
	{              
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
        foreach($target_app_id_arr as $key => $mst_id)
        {
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",2,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$ahid++;
        }

		//echo $req_ids;die;

		$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'0*0*0*0',"id",$req_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=2 and mst_id in ($req_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=2 and mst_id in ($req_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		  //echo "22**".$rID1.",".$rID2.",".$rID3.",".$rID4.$query;oci_rollback($con);die;
		
		$response=$req_ids;
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
