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

	// flowing script for multy select data------------------------------------------------------------------------------start;
	  function js_set_value(id)
	  {
	 // alert(id)
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
	// avobe script for multy select data------------------------------------------------------------------------------end;
	</script>
	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
       	<?php
		$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;


		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0 and b.entry_form=43 order by b.sequence_no";

		//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?> 
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script> 
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type =9
	exit();
}



 
function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$buyer_brand_arr=$parameterArr['lib_brand_arr'];

	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;


	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}

		// $temp_brand_arr=array(0=>0);
		// foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
		// 	if(count($parameterArr['lib_brand_arr'][$buyer_id])){
		// 		$temp_brand_arr[]=implode(',',(array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
		// 	}
		// }
		// if($rows['BRAND_ID']==''){$rows['BRAND_ID']=implode(',',explode(',',implode(',',$temp_brand_arr)));}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));


	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
	 	
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		//if($rows['BRAND_ID']==''){$rows['BRAND_ID']=$lib_brand_arr;}

		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		//$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID'])
				//&& in_array($bbtsRows['brand_id'],$usersDataArr[$user_id]['BRAND_ID'])
				&&  $bbtsRows['buyer_id']>0
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	  
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}




$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');


$brand_sql ="select ID, BUYER_ID,BRAND_NAME from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
$brand_sql_rs=sql_select( $brand_sql );
foreach($brand_sql_rs as $row){
	$brand_arr[$row['ID']]=$row['BRAND_NAME'];
	$buyer_brand_arr[$row['BUYER_ID']][$row['ID']]=$row['BRAND_NAME'];
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_name);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_date=str_replace("'","",$txt_date);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);

	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	$app_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


	if($txt_wo_no !='' ){$where_con .= " and a.wo_number_prefix_num LIKE '%$txt_wo_no'";}
	if($cbo_supplier_id !=0 ){$where_con .= " and a.SUPPLIER_ID=$cbo_supplier_id";}
	if($txt_date!="")
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if(str_replace("'","",$cbo_get_upto)==1){$where_con .= " and a.wo_date>'".$txt_date."'";} 
		else if(str_replace("'","",$cbo_get_upto)==2){$where_con .= " and a.wo_date<='".$txt_date."'";} 
		else if(str_replace("'","",$cbo_get_upto)==3){$where_con .= " and a.wo_date='".$txt_date."'";} 
	}
	// echo $date_cond;die;

	$submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');


	$electronicDataArr=getSequence(array('company_id'=>$company_id,'entry_form'=>43,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$buyer_brand_arr,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));


	if($approval_type==0)
	{

		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= where_con_using_array(explode(',',$electronicDataArr['user_by'][$app_user_id]['BUYER_ID']),0,'b.BUYER_ID') ;
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}


		$data_mas_sql = "select a.ID, b.BUYER_ID from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.COMPANY_NAME=$company_id and b.item_category_id=1 and a.entry_form=234 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1  $where_con group by a.ID, b.BUYER_ID";
		   //echo $data_mas_sql;die;

			 
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				
				if($electronicDataArr['sequ_by'][$seq]['BUYER_ID']==''){$electronicDataArr['sequ_by'][$seq]['BUYER_ID']=0;}
				
				if(in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])))
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

		//print_r($tmp_sys_id_arr);die;
		//..........................................Match data;

		//ALTER TABLE PLATFORMERPV3.WO_NON_ORDER_INFO_MST ADD (APPROVED_SEQU_BY  NUMBER(11) DEFAULT 0);
 


		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= "SELECT A.ID, A.COMPANY_NAME, A.WO_NUMBER_PREFIX_NUM,a.WO_NUMBER, A.SUPPLIER_ID, A.WO_DATE, A.DELIVERY_DATE, A.IS_APPROVED, A.SOURCE, A.PAYTERM_ID, A.INSERTED_BY, A.UPDATED_BY, A.WO_BASIS_ID,B.BUYER_ID from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.company_name=$company_id and a.is_approved<>1 and a.APPROVED_SEQU_BY=$seq  and  a.id=b.mst_id and a.READY_TO_APPROVED=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $sys_con  group by a.id, a.company_name, a.wo_number_prefix_num,a.WO_NUMBER, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,b.buyer_id"	;
			}
		
		}

	}
	elseif($approval_type==1){
		$sql = "SELECT A.ID, A.COMPANY_NAME,b.BUYER_ID, A.WO_NUMBER_PREFIX_NUM,a.WO_NUMBER, A.SUPPLIER_ID, A.WO_DATE, A.DELIVERY_DATE, A.IS_APPROVED, A.SOURCE, A.PAYTERM_ID, A.INSERTED_BY, A.UPDATED_BY, A.WO_BASIS_ID,A.APPROVED_SEQU_BY, A.APPROVED_BY from wo_non_order_info_mst a, wo_non_order_info_dtls b,APPROVAL_MST c where
		a.id=b.mst_id and a.id=b.mst_id  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.READY_TO_APPROVED=1  and a.entry_form=234 and c.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and c.ENTRY_FORM=43 $where_con  group by a.id, a.company_name,b.buyer_id, a.wo_number_prefix_num,a.WO_NUMBER, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id,a.APPROVED_SEQU_BY, a.APPROVED_BY";
	}


	//echo $sql;


	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1150px; margin-top:10px">
        <legend>Yarn Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" align="left" >
                <thead>
                	<th width="35">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="70">Work Order No</th>
                    <th width="100">Image / File</th>
                    <th width="120">Supplier</th>
                    <th width="120">Buyer</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="100">WO Basis</th>
                    <th width="80">Source</th>
                    <th width="80">Pay Term</th>
                    <th width="150">Submitted By</th>
                    <th >Action</th>
                </thead>
            </table>
            <div style="width:1150px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                            $i=1;

							$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='Yarn_Purc_Ord_Sweater'","master_tble_id");
							
                            $nameArray=sql_select( $sql );
                            
                            foreach ($nameArray as $row)
                            {
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

								
								 
								$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =45 and is_deleted=0 and status_active=1");
                            	$format_idss=explode(",",$print_report_format);    
       							//echo $format_ids;
       							foreach ($format_idss as $key => $format_ids) 
       							{
	                                if($format_ids == 78){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row['ID']."*Yarn Purchase Order*0*3*".$row['IS_APPROVED']."*6&action=yarn_work_order_print' style='color:#000' target='_blank'>". $row['WO_NUMBER']."</a>";
	                                }elseif($format_ids == 84){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row['ID']."*Yarn Purchase Order*1*0*".$row['IS_APPROVED']."&action=print_to_html_report' style='color:#000' target='_blank'>". $row['WO_NUMBER']."</a>";
	                                }elseif($format_ids == 85){
	                                	//echo 'system';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row['ID']."*Yarn Purchase Order*2*0*".$row['IS_APPROVED']."&action=print_to_html_report2' style='color:#000' target='_blank'>". $row['WO_NUMBER']."</a>";
	                                }elseif($format_ids == 193){
	                                	//echo 'systemfalse';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row['ID']."*Yarn Purchase Order*4*0*".$row['IS_APPROVED']."&action=print_to_html_report4' style='color:#000' target='_blank'>". $row['WO_NUMBER']."</a>";
	                                }else{
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row['ID']."*Yarn Purchase Order*0*1*".$row['IS_APPROVED']."&action=yarn_work_order_print5' style='color:#000' target='_blank'>". $row['WO_NUMBER']."</a>";
	                            	}
                            	}
								if($row['UPDATED_BY']=="" || $row['UPDATED_BY']==0) $row['UPDATED_BY']=$row['INSERTED_BY'];
								
                            	?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
                                	<td width="35" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" />
                                        <input id="req_id_<?= $i;?>" name="req_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                    </td>
									<td width="30" align="center"><?= $i; ?></td>
									<td width="70"><?= $variable; ?></td>

									<td width="100" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $row[csf('COMPANY_NAME')]; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>

                                    <td width="120"><?= $supplier[$row['SUPPLIER_ID']]; ?></td>
                                    <td width="120"><?= $buyer_arr[$row['BUYER_ID']]; ?></td>
									<td width="70" align="center"><? if($row['WO_DATE']!="0000-00-00") echo change_date_format($row['WO_DATE']); ?></td>
									<td width="70" align="center"><? if($row['DELIVERY_DATE']!="0000-00-00") echo change_date_format($row['DELIVERY_DATE']); ?></td>
                                    
                                    <td width="100"><? echo $wo_basis[$row['WO_BASIS_ID']]; ?></td>
                                    <td width="80"><? echo $source[$row['SOURCE']]; ?></td>
                                    <td width="80"><? echo $pay_term[$row['PAYTERM_ID']]; ?></td>
                                    <td width="150"><? echo $submittedByArr[$row['UPDATED_BY']]; ?></td>

                                    <td align="center"><a href="##" onClick="openmypage_work_order('<?= $row['ID']; ?>','<?= $row['COMPANY_NAME']; ?>','<?= $row['WO_NUMBER']; ?>','<?= $row["BUYER_ID"]; ?>','<?= $row["WO_BASIS_ID"]; ?>','<?= $row["SUPPLIER_ID"]; ?>','Work Order Details')" >Work Or. Details</a></td>
								</tr> 
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" align="left">
				<tfoot>
                    <td width="35" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>)"/></td>
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

	$company_id=str_replace("'","",$cbo_company_name);
	$req_nos=str_replace("'","",$req_nos);
	$approval_type=str_replace("'","",$approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$app_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$con = connect();

	$sql_pi = sql_select("select a.PI_NUMBER, b.WORK_ORDER_NO from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company_id and a.item_category_id=1 and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id in($req_nos)");
	if (count($sql_pi)>0){
		$pi_number=$sql_pi[0]['PI_NUMBER'];
		$work_order_no=$sql_pi[0]['WORK_ORDER_NO'];
		echo "50**PI Found $pi_number againts of this Work Order $work_order_no";
		disconnect($con); die;
	}

	$sql = "select a.ID, b.BUYER_ID from wo_non_order_info_mst a,WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.COMPANY_NAME=$company_id and b.item_category_id=1 and a.entry_form=234 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.READY_TO_APPROVED=1  and a.id in($req_nos) group by a.ID, b.BUYER_ID";
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>0,'item'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$company_id,'entry_form'=>43,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'product_dept_arr'=>$product_dept,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
	$flag=1;



	if($approval_type==0) //Approve button
	{
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and APPROVED_BY=$app_user_id and entry_form=43 group by mst_id","mst_id","approved_no");

		$id=return_next_id( "id","approval_history", 1 );
		$app_mst_id=return_next_id( "id","approval_mst", 1 );
		$approved_no_array=array();

		$reqs_ids=explode(",",$req_nos);
		foreach($reqs_ids as $mst_id)
		{
			$approved_no=($max_approved_no_arr[$mst_id] == '') ? 1 : $max_approved_no_arr[$mst_id]+1;
			$approved_no_array[$mst_id]=$approved_no;
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;

			//History...................
			if($his_data_array == '') $his_data_array.=",";
			$his_data_array.="(".$id.",43,".$mst_id.",".$approved_no.",".$app_user_id.",'".$pc_date_time."',".$user_sequence_no.",1,".$user_id.",'".$pc_date_time."',".$approved.")";
			$id++;

			//App mst ..........................
			if($app_mst_data_array!=''){$app_mst_data_array.=",";}
			$app_mst_data_array.="(".$app_mst_id.",43,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$app_mst_id++;


			//Mst data.......................
			
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$app_user_id."")); 

		}
		
		
		
		if($flag==1) 
		{
			$app_mst_field_array="ID, ENTRY_FORM, MST_ID,  SEQUENCE_NO,APPROVED_BY, APPROVED_DATE,INSERTED_BY,INSERT_DATE,user_ip";
			$rID['APP_MST']=sql_insert("approval_mst",$app_mst_field_array,$app_mst_data_array,0);
			if($rID['APP_MST']) $flag=1; else $flag=0; 
		}


		
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_BY"; 
			$rID['MST']=execute_query(bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $reqs_ids ));
			if($rID['MST']) $flag=1; else $flag=0; 
		}
		
 
        
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=43 and mst_id in ($req_nos)";
			$rID['HIS_UPDATE']=execute_query($query,1);
			if($rID['HIS_UPDATE']) $flag=1; else $flag=0;
			
			
			$his_field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status, inserted_by, insert_date,approved";
			$rID['HIS_INSERT']=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID['HIS_INSERT']) $flag=1; else $flag=0;
			
		}


		$approved_string="";
		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}

		$approved_string_mst="CASE id ".$approved_string." END";
		$approved_string_dtls="CASE mst_id ".$approved_string." END";

		$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
		select
		'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";

		if($flag==1)
		{
			$rID['COPY_MST']=execute_query($sql_insert,0);
			if($rID['COPY_MST']) $flag=1; else $flag=0;
		}

		$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
		select
		'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";
		if($flag==1)
		{
			$rID['COPY_MST_DTLS']=execute_query($sql_insert_dtls,1);
			if($rID['COPY_MST_DTLS']) $flag=1; else $flag=0;
		}

      
		if($flag==1) $msg='19'; else $msg='21';

		


	}
	else if($approval_type==1) // Un-Approve button
	{
		
		$id=return_next_id( "id","approval_history", 1 );
		$reqs_ids=explode(",",$req_nos);
		foreach($reqs_ids as $mst_id)
		{
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($his_data_array == '') $his_data_array.=",";
			$his_data_array.="(".$id.",43,".$mst_id.",".$approved_no.",".$app_user_id.",'".$pc_date_time."',".$user_sequence_no.",0,0,".$user_id.",'".$pc_date_time."')";
			$id++;
		}


		
		
		if($flag==1) 
		{
			$rID['MST_UPDATE']=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY","0*0*0","id",$req_nos,0);
			if($rID['MST_UPDATE']) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=43 and mst_id in ($req_nos)";
			$rID['DELETE_APP_MST']=execute_query($query,1); 
			if($rID['DELETE_APP_MST']) $flag=1; else $flag=0; 
		}



		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=43 and mst_id in ($req_nos)";
			$rID['HIS_UPDATE']=execute_query($query,1);
			if($rID['HIS_UPDATE']) $flag=1; else $flag=0;
			
			$his_field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status,approved, inserted_by, insert_date";
			$rID['HIS_INSERT']=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID['HIS_INSERT']) $flag=1; else $flag=0;
			
		}


		if($flag==1) $msg='20'; else $msg='22';

	}
	else if($approval_type==5) // Deny button
	{
		
		$id=return_next_id( "id","approval_history", 1 );
		$reqs_ids=explode(",",$req_nos);
		foreach($reqs_ids as $mst_id)
		{
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($his_data_array == '') $his_data_array.=",";
			$his_data_array.="(".$id.",43,".$mst_id.",".$approved_no.",".$app_user_id.",'".$pc_date_time."',".$user_sequence_no.",0,2,".$user_id.",'".$pc_date_time."')";
			$id++;
		}
		
		if($flag==1) 
		{
			$rID['MST_UPDATE']=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","2*0","id",$req_nos,0);
			if($rID['MST_UPDATE']) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=43 and mst_id in ($req_nos)";
			$rID['DELETE_APP_MST']=execute_query($query,1); 
			if($rID['DELETE_APP_MST']) $flag=1; else $flag=0; 
		}


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=43 and mst_id in ($req_nos)";
			$rID['HIS_UPDATE']=execute_query($query,1);
			if($rID['HIS_UPDATE']) $flag=1; else $flag=0;
			
			$his_field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status,approved, inserted_by, insert_date";
			$rID['HIS_INSERT']=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID['HIS_INSERT']) $flag=1; else $flag=0;
			
		}


		if($flag==1) $msg='50'; else $msg='51';
	}

	// echo $flag.'**'.implode(',',$rID);oci_rollback($con);die;

	
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

if($action=="get_user_pi_file")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
  
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='Yarn_Purc_Ord_Sweater' and master_tble_id='$id'";

	//echo $img_sql;die;
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        //if($img[FILE_TYPE]==1){
			echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'"><img src="../../file_upload/blank_file.png" width="89px" height="97px"></a><br>'.$img[csf("real_file_name")].'</p>'; 
		//}
    }
}

if($action=="downloiadFile")
{
    if(isset($_REQUEST["file"]))
    {        
        $file = urldecode($_REQUEST["file"]); // Decode URL-encoded string   
        
       // echo $file;die;
		
		$filepath = "../../" . $file;    
        // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        }
    }
}

if($action=="wo_details")   //safa
{
	echo load_html_head_contents("Report Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$buyer_short_name_arr = return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');
	$color_arr = return_library_array("select id, color_name from  lib_color", "id", "color_name");
	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");
	$user_full_name_arr = return_library_array( "SELECT id, USER_FULL_NAME from USER_PASSWD","id","USER_FULL_NAME");


	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_library_address=return_library_array( "select id, city from lib_company", "id", "city"  );
	$supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$supplier_name_add=return_library_array( "select id, address_1 from  lib_supplier", "id", "address_1");
	$custom_desig=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
	// echo $wo_id.'__'.$company_id.'__'.$wo_no.'__'.$buyer_id.'__'.$wo_basis; die();
	
	$sql = "SELECT B.ID, B.REQUISITION_DTLS_ID, B.JOB_ID, B.JOB_NO, B.BUYER_ID, B.STYLE_NO, B.YARN_COUNT, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_TYPE, B.COLOR_NAME, B.REQ_QUANTITY, B.SUPPLIER_ORDER_QUANTITY, B.UOM, B.RATE, B.AMOUNT,d.ID as PRE_COST_ID,E.TOTAL_SET_QNTY AS RATIO, a.REMARKS, a.INSERTED_BY,a.READY_TO_APPROVED
	from WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B 
	left join wo_pre_cost_fabric_cost_dtls d ON B.JOB_NO=d.JOB_NO AND d.STATUS_ACTIVE=1 
	left join wo_po_details_master e ON B.JOB_NO=e.JOB_NO AND e.STATUS_ACTIVE=1 
	WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.ID=$wo_id AND A.ID=B.MST_ID 
	group by B.ID, B.REQUISITION_DTLS_ID, B.JOB_ID, B.JOB_NO, B.BUYER_ID, B.STYLE_NO, B.YARN_COUNT, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_TYPE, B.COLOR_NAME, B.REQ_QUANTITY, B.SUPPLIER_ORDER_QUANTITY, B.UOM, B.RATE, B.AMOUNT,d.ID,E.TOTAL_SET_QNTY, a.REMARKS, a.INSERTED_BY, a.READY_TO_APPROVED ORDER BY b.id";

	//echo $sql;
	$result=sql_select($sql); 
	$row_count=array();
	foreach($result as $row)  
	{
		$seq_group = $row['YARN_COMP_TYPE1ST']."*".$row['YARN_COUNT']."*".$row['COLOR_NAME'];

		$source_data[$row['JOB_ID']][$seq_group]['JOB_ID']  = $row['JOB_ID'];
		$source_data[$row['JOB_ID']][$seq_group]['JOB_NO']  = $row['JOB_NO'];
		$source_data[$row['JOB_ID']][$seq_group]['REQUISITION_DTLS_ID']  = $row['REQUISITION_DTLS_ID'];
		$source_data[$row['JOB_ID']][$seq_group]['BUYER_ID']  = $row['BUYER_ID'];
		$source_data[$row['JOB_ID']][$seq_group]['STYLE_NO']  = $row['STYLE_NO'];
		$source_data[$row['JOB_ID']][$seq_group]['YARN_COUNT']  = $row['YARN_COUNT'];
		$source_data[$row['JOB_ID']][$seq_group]['YARN_COMP_TYPE1ST']  = $row['YARN_COMP_TYPE1ST'];
		$source_data[$row['JOB_ID']][$seq_group]['YARN_COMP_PERCENT1ST']  = $row['YARN_COMP_PERCENT1ST'];
		$source_data[$row['JOB_ID']][$seq_group]['YARN_TYPE']  = $row['YARN_TYPE'];
		$source_data[$row['JOB_ID']][$seq_group]['COLOR_NAME']  = $row['COLOR_NAME'];
		$source_data[$row['JOB_ID']][$seq_group]['REQ_QUANTITY']  = $row['REQ_QUANTITY'];
		$source_data[$row['JOB_ID']][$seq_group]['SUPPLIER_ORDER_QUANTITY']  += $row['SUPPLIER_ORDER_QUANTITY'];
		$source_data[$row['JOB_ID']][$seq_group]['UOM']  = $row['UOM'];
		$source_data[$row['JOB_ID']][$seq_group]['RATE']  = $row['RATE'];
		$source_data[$row['JOB_ID']][$seq_group]['AMOUNT']  = $row['AMOUNT'];
		$source_data[$row['JOB_ID']][$seq_group]['REQ_QNTY']  = $row['REQ_QNTY'];
		$source_data[$row['JOB_ID']][$seq_group]['PRE_COST_ID']  = $row['PRE_COST_ID'];

		$source_data[$row['JOB_ID']][$seq_group]['RATIO']  = $row['RATIO'];
		$source_data[$row['JOB_ID']][$seq_group]['CONS_QNTY']  = $row['CONS_QNTY'];
		
		$job_id .= $row['JOB_ID'].","; 
		$job_no_dtls .= "'".$row['JOB_NO']."'".","; 
		$color .= $row['COLOR_NAME'].","; 
		$pre_cost_id .= $row['PRE_COST_ID'].","; 
	}

	$wo_pre=sql_select("SELECT JOB_NO, COUNT_ID, COPM_ONE_ID, COLOR, CONS_QNTY as  CONS_QNTY from  wo_pre_cost_fab_yarn_cost_dtls where  STATUS_ACTIVE=1");

	$wo_precost_arr=array();
	foreach($wo_pre as $row){
		$wo_precost_arr[$row["JOB_NO"]][$row["COUNT_ID"]][$row["COPM_ONE_ID"]][$row["COLOR"]]["CONS_QNTY"]+=$row["CONS_QNTY"];
	}

	foreach ($source_data as $job_no => $job_data) 
	{
		$row_no=0;
		foreach ($job_data as $ref_str => $row) 
		{
			$row_no++;
		}
		$job_td_span[$job_no] = $row_no;
	}


	$all_job_id = ltrim(implode(",", array_unique(explode(",", chop($job_id, ",")))), ',');
	$all_job_no = ltrim(implode(",", array_unique(explode(",", chop($job_no_dtls, ",")))), ',');
	$all_color = ltrim(implode(",", array_unique(explode(",", chop($color, ",")))), ',');
	$all_pre_cost_id = ltrim(implode(",", array_unique(explode(",", chop($pre_cost_id, ",")))), ',');

	$po_sql= "SELECT ID,JOB_ID, PO_QUANTITY,UNIT_PRICE  FROM WO_PO_BREAK_DOWN WHERE IS_DELETED=0 AND JOB_ID IN ($all_job_id) ORDER BY ID DESC"; 
	//echo $po_sql;
	$data_array=sql_select($po_sql);
	$poArr=array();
	foreach($data_array as $row)  
	{
		 $poArr[$row['JOB_ID']]['PO_QUANTITY'] += $row['PO_QUANTITY']; 
		 $poArr[$row['JOB_ID']]['UNIT_PRICE'] = $row['UNIT_PRICE']; 
		 //$poArr[$row['JOB_ID']]['COUNT'] ++; 
		 $poArr[$row['JOB_ID']]['COUNT'] += COUNT($row['ID']); 
	}

	//---***---Req qty start---

	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where job_no in ($all_job_no)", "job_no", "costing_per");
	$CmWoPrecostArr=return_library_array( "select job_no, CM_COST from wo_pre_cost_dtls where JOB_ID in ($all_job_id)", "job_no", "CM_COST");
	$fabric_uom_arr=array(); $fabric_gitem_arr=array();
	$yarnsql="select id,job_no, item_number_id, uom from wo_pre_cost_fabric_cost_dtls where job_no in ($all_job_no)";
	$yarnsqlRes=sql_select($yarnsql);
	foreach($yarnsqlRes as $yrow)
	{
		$fabric_uom_arr[$yrow[csf('job_no')]][$yrow[csf('id')]]=$yrow[csf('uom')];
		$fabric_gitem_arr[$yrow[csf('job_no')]][$yrow[csf('id')]]=$yrow[csf('item_number_id')];
	}
	unset($yarnsqlRes);
	
	$plan_qty_arr=array();
	$po_sql=sql_select("select job_no_mst, item_number_id, color_number_id, plan_cut_qnty as plan_cut from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and job_no_mst in ($all_job_no)");
	
	foreach($po_sql as $row)
	{
		$plan_qty_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut')];
	}
	unset($po_sql);
	//print_r($plan_qty_arr); die;

	$stripe_color_arr=array(); $strip_cons_arr=array();
	$sql_stripe=sql_select("select job_no, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement from wo_pre_stripe_color where status_active=1 and is_deleted=0 and job_no in ($all_job_no) group by job_no, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement" );

	foreach($sql_stripe as $row)
	{
		$stripe_color_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]].=$row[csf('color_number_id')].',';
		$strip_cons_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]].=$row[csf('color_number_id')].'_'.$row[csf('measurement')].',';
	}
	unset($sql_stripe);

	$SqlProd=sql_select("SELECT b.PROD_ID, b.CONS_QUANTITY, c.YARN_COMP_TYPE1ST, c.YARN_COUNT_ID, c.COLOR FROM  inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and c.id=b.PROD_ID and a.ISSUE_PURPOSE=80 and a.ENTRY_FORM=277 and a.ITEM_CATEGORY=1 and b.JOB_NO in($all_job_no) and a.status_active=1 and b.status_active=1 and c.status_active=1");

	$ProdArr=$IssueArrQty=array();
	foreach($SqlProd as $val){
		$IssueArrQty[$val['YARN_COMP_TYPE1ST']][$val['YARN_COUNT_ID']][$val['COLOR']]['CONS_QUANTITY']+=$val["CONS_QUANTITY"];
		$ProdArr[$val["PROD_ID"]]=$val["PROD_ID"];
	}
	$uniqueArray = array_values(array_unique($ProdArr));

	// echo implode(', ', $uniqueArray);

	$sql_transaction = sql_select("SELECT b.YARN_COMP_TYPE1ST, b.YARN_COUNT_ID, b.COLOR, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
	from inv_transaction a, product_details_master b where b.id=a.PROD_ID and a.job_no in($all_job_no)  and a.company_id=$company_id and a.prod_id in(".implode(', ', $uniqueArray).") and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382) group by b.YARN_COMP_TYPE1ST, b.YARN_COUNT_ID, b.COLOR");
    $TransArr=array();
	foreach($sql_transaction as $v){
		$TransArr[$v['YARN_COMP_TYPE1ST']][$v['YARN_COUNT_ID']][$v['COLOR']]['YARN_QNTY']=$v["YARN_QNTY"];
	}
	// print_r($TransArr);
	
	$contrast_color_arr=array();
	$sql_contrast=sql_select("select job_no, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no in ($all_job_no)");
	foreach($sql_contrast as $row)
	{
		$contrast_color_arr[$row[csf('job_no')]][$row[csf('contrast_color_id')]]=$row[csf('gmts_color_id')];
	}
	unset($sql_contrast);


	$pre_cost_sql=sql_select("SELECT STRIPE_COLOR, PRE_COST_FABRIC_COST_DTLS_ID, CONS, EXCESS_PER, MEASUREMENT FROM WO_PRE_STRIPE_COLOR WHERE PRE_COST_FABRIC_COST_DTLS_ID in ($all_pre_cost_id) AND IS_DELETED=0 AND STATUS_ACTIVE=1 ORDER BY ID");
	
	$consArr = array();
	foreach($pre_cost_sql as $row)  
	{
		 $consArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']][$row['STRIPE_COLOR']]['CONS'] = $row['CONS']; 
		 $consArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']][$row['STRIPE_COLOR']]['EXCESS_PER'] = $row['EXCESS_PER']; 
		 $consArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']][$row['STRIPE_COLOR']]['MEASUREMENT'] = $row['MEASUREMENT']; 
	}
	unset($pre_cost_sql);

	$sql_wo = sql_select("SELECT REQUISITION_DTLS_ID,JOB_NO,YARN_COUNT,COLOR_NAME,YARN_TYPE,YARN_COMP_TYPE1ST, SUM(SUPPLIER_ORDER_QUANTITY) AS SUPPLIER_ORDER_QUANTITY, SUM(AMOUNT) AS AMOUNT FROM  WO_NON_ORDER_INFO_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND MST_ID<>$wo_id  AND MST_ID<$wo_id  GROUP BY REQUISITION_DTLS_ID,JOB_NO,YARN_COUNT,COLOR_NAME,YARN_TYPE,YARN_COMP_TYPE1ST");
	$prev_wo_qnty_arr = array();
	foreach ($sql_wo as $row) {
		$prev_wo_qnty_arr[$row["JOB_NO"]][$row["YARN_COUNT"]][$row["COLOR_NAME"]][$row["YARN_TYPE"]][$row["YARN_COMP_TYPE1ST"]]["supplier_order_quantity"] += $row["SUPPLIER_ORDER_QUANTITY"];
	}
	unset($sql_wo);

	$sql_wo_no = sql_select("SELECT a.ID,a.WO_NUMBER from WO_NON_ORDER_INFO_MST A WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ID=$wo_id");
	
	// $sql_revise_date=sql_select("SELECT MST_ID, count(un_approved_by) as REVISE, max(un_approved_date) as LAST_REVISE_DATE FROM approval_history WHERE MST_ID=$wo_id and UN_APPROVED_BY>0 AND ENTRY_FORM = 43 group by MST_ID");
	$sql_revise_date=sql_select("SELECT MST_ID, count(APPROVED_NO) as REVISE, max(APPROVED_DATE) as LAST_REVISE_DATE FROM approval_history WHERE MST_ID=$wo_id and APPROVED_NO=0  AND ENTRY_FORM = 43 group by MST_ID");

	$SqlLastFirst=sql_select("SELECT MST_ID, APPROVED_BY, SEQUENCE_NO, CURRENT_APPROVAL_STATUS FROM approval_history WHERE MST_ID=$wo_id AND ENTRY_FORM = 43");

	$SqlLastFirst=sql_select("SELECT MST_ID, APPROVED_BY, SEQUENCE_NO FROM approval_mst WHERE MST_ID=$wo_id AND ENTRY_FORM = 43");

	$FirstApprovalArr=$LastApprovalArr=array();
	foreach($SqlLastFirst as $row){
		$FirstApprovalArr[$row["MST_ID"]][$row["SEQUENCE_NO"]]["FIRST_APP"]=$row["APPROVED_BY"];	
		$LastApprovalArr[$row["MST_ID"]][$row["SEQUENCE_NO"]]["LAST_APP"]=$row["APPROVED_BY"];
	}

	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<style>
		.size{
			font-size: 18px;
		}
	</style>
	<fieldset style="width:1600px; margin-left:5px">
		<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
		<br/>
		<div id="report_container" align="center" style="width:1600px">
			<div  align="center">
				<table style="float: center;" class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-size: 30px;" colspan="2"><b>Company: <? echo $company_library[$company_id]; ?></b></td>
					</tr>
					<tr>
						<td style="font-size: 17px;" colspan="2"><b>Address: </b><? echo $company_library_address[$company_id]; ?></td>
					</tr>
				</table>
			</div>
			<div style="padding-top: 20px;">
				<table style="float: left; margin-left:5px;" class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-size: 20px;"><b>Supplier: <? echo $supplier_name_library[$supplier]; ?></b></td>
					</tr>
					<tr>
						<td style="font-size: 20px;"><b>Address: </b> <? echo $supplier_name_add[$company_id]; ?></td>
					</tr>
				</table>
				<table style="float: right;" class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-size: 18px;">
							<div id="report_container" align="left">
								<b>WO No: <?echo $sql_wo_no[0]['WO_NUMBER'];?></b>
							</div>
						</td>
					</tr>
					<tr>
						<td style="font-size: 18px;"><b>No of Revise: <? echo $sql_revise_date[0]["REVISE"]; ?></b></td>
					</tr>
					<tr>
						<td style="font-size: 18px;"><b> Last Revise Date: </b> <? echo change_date_format($sql_revise_date[0]["LAST_REVISE_DATE"]);  ?></td>
					</tr>
				</table>
			</div>	
			<div align="center" style="width:1600px">
				<table style="float: left; margin-left:5px;"  width="1100">
				    <tr style="height: 20px;"></tr>
					<tr>
						<td style="font-size: 18px;word-break: break-all;" ><b> Remarks: <? echo $result[0]["REMARKS"]; ?></b></td>
					</tr>
					<tr style="height: 20px;"></tr>
				</table>
			</div>

            <table class="rpt_table" style="padding-top: 30px;" border="1" rules="all" width="1600" cellpadding="0" cellspacing="0">
             	<thead>
					 <tr>
					 <th width="50">SL No</th>
						<th width="80">Job Number</th>
						<th width="80">Buyer</th>
						<th width="80">Style <br> Number</th>
						<th width="30">GMT <br> FOB</th>
						<th width="30">CM</th>
						<th width="50">Order Rec <br>Qty Yet</th> 
						<th width="120">Yarn <br> Specification</th> 
						<th width="70">Yarn <br> Count</th> 
						<th width="80">Yarn <br> Colour</th> 
						<th width="80">Avg cons <br> in Lbs/Dzn</th> 
						<th width="50">Ex. %</th> 
						<th width="50">Total Cons <br> in Lbs/Dzn</th> 
						<th width="50">Req Qty <br> in Lbs</th> 
						<th width="50">"Cum. WO <br>Qty. (LBS)</th> 
						<th width="50">Balance Qty <br> in Lbs</th> 
						<th width="50">Left Over <br> Stock in Lbs</th> 
						<th width="50">Today W/O <br> Qty</th> 
						<th width="40">UOM</th> 
						<th width="50">Rate <br> in USD</th> 
						<th width="50">Value <br> in USD</th> 
					 </tr>
                </thead>
                <tbody> 
					<?
						$i=1;
						foreach ($source_data as $job_no => $job_data) 
						{
							$z=1;
							foreach ($job_data as $ref_str => $row) 
							{
								$rowspan= $job_td_span[$job_no];
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								$prev_wo_qnty = $prev_wo_qnty_arr[$row["JOB_NO"]][$row["YARN_COUNT"]][$row["COLOR_NAME"]][$row["YARN_TYPE"]][$row["YARN_COMP_TYPE1ST"]]["supplier_order_quantity"];
								$stripe_color=''; $contrast_color=''; $color_id=0; $is_stripe=0;
								$stripe_color=implode(",",array_filter(array_unique(explode(",",$stripe_color_arr[$row["JOB_NO"]][$row['PRE_COST_ID']][$row['COLOR_NAME']]))));
								$contrast_color=$contrast_color_arr[$row["JOB_NO"]][$row['COLOR_NAME']];
								if($stripe_color!="") { $color_id=$stripe_color; $is_stripe=1; } else if($contrast_color!="") $color_id=$contrast_color; else $color_id=$row['COLOR_NAME'];
								
								$dzn_qnty=0; $cons_qnty=0; $cons_balance_qnty=0;  $fabuom=0; $fgitem=0;
								if($costing_per_id_library[$row["JOB_NO"]]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$row["JOB_NO"]]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$row["JOB_NO"]]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$row["JOB_NO"]]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;
								$dzn_qnty=$dzn_qnty;
								$fabuom=$fabric_uom_arr[$row['JOB_NO']][$row['PRE_COST_ID']];
								$fgitem=$fabric_gitem_arr[$row['JOB_NO']][$row['PRE_COST_ID']];
								if($fabuom==12)
								{
									$plan_cut_qnty=0; $cons_qnty=0;
									$excolor_id=explode(",",$color_id);
									if($is_stripe==1)
									{
										$stripe_data=array_filter(array_unique(explode(",",$strip_cons_arr[$row["JOB_NO"]][$row['PRE_COST_ID']][$row['COLOR_NAME']])));
										//print_r($stripe_data);
										foreach($stripe_data as $stcolorcons)
										{
											$gmts_color=""; $strip_cons=0; $plan_cut_qnty=0;
											$ex_stcolorcons=explode("_",$stcolorcons);
											$gmts_color=$ex_stcolorcons[0]; 
											$strip_cons=$ex_stcolorcons[1];
											//$strip_cons=$row[csf('cons_qnty')];
											$plan_cut_qnty=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$gmts_color];
											
											$cons_qty=$plan_cut_qnty*($strip_cons/$dzn_qnty);
											//echo $stcolorcons.'='.$plan_cut_qnty.'='.$strip_cons.'='.$dzn_qnty.'='.$cons_qty.'<br>';
											$cons_qnty+=$cons_qty;
										}
									}
									else
									{
										foreach($excolor_id as $colorid)
										{
											$plan_cut_qnty+=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$colorid]*$row['RATIO'];
										}
										$cons_qnty=$plan_cut_qnty*($row['CONS_QNTY']/$dzn_qnty);
									}
									
									$cons_qnty=$cons_qnty*2.20462;
								}
								else
								{
									$cons_qnty=$wo_precost_arr[$row["JOB_NO"]][$row["YARN_COUNT"]][$row["YARN_COMP_TYPE1ST"]][$row["COLOR_NAME"]]["CONS_QNTY"];
									$plan_cut_qnty=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$color_id]*$row['RATIO'];
									$cons_qnty=$plan_cut_qnty*($cons_qnty/$dzn_qnty);
								}								
								if($basis == 7) $cons_qnty='';

								$LeftOverStockInLbs=$TransArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT']][$row['COLOR_NAME']]['YARN_QNTY']+$IssueArrQty[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT']][$row['COLOR_NAME']]['CONS_QUANTITY']
							
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
								<?if($z == 1)
									{?>
									<td rowspan="<?= $rowspan; ?>" class="size"  valign="middle"  align="center"><? echo $i; ?></td>
									<td rowspan="<?= $rowspan; ?>" class="size"  valign="middle"><? echo $row['JOB_NO']; ?></td>
									<td rowspan="<?= $rowspan; ?>" class="size"  valign="middle" ><? echo $buyer_short_name_arr[$row['BUYER_ID']]; ?></td>
									<td rowspan="<?= $rowspan; ?>" class="size" valign="middle"  align="center"><? echo $row['STYLE_NO']; ?></td>
									<td rowspan="<?= $rowspan; ?>" class="size" valign="middle"  align="center"><? echo 
									$poArr[$row['JOB_ID']]['UNIT_PRICE'];//$poArr[$row['JOB_ID']]['COUNT']; //$rowspan; ?>
									</td>
									<td rowspan="<?= $rowspan; ?>" class="size" valign="middle"  align="center"><? echo 
									$CmWoPrecostArr[$row["JOB_NO"]]; ?>
									</td>

									<td rowspan="<?= $rowspan; ?>" class="size" valign="middle"  align="right"><? echo number_format($poArr[$row['JOB_ID']]['PO_QUANTITY'],2) ?>&nbsp;</td>
									<?}?>
									<td class="size"><? echo $composition[$row['YARN_COMP_TYPE1ST']];?></td>
									<td class="size" align="center"><? echo $yarn_count_arr[$row['YARN_COUNT']];?></td>
									<td class="size"><? echo $color_arr[$row['COLOR_NAME']];?></td>
									<td class="size" align="right"> <?echo number_format(($consArr[$row['PRE_COST_ID']][$row['COLOR_NAME']]['CONS']*2.20462),4);?>&nbsp;</td>
									<td class="size"  align="center"> <?echo $consArr[$row['PRE_COST_ID']][$row['COLOR_NAME']]['EXCESS_PER'];?></td>
									<td class="size" align="right"> <?echo number_format(($consArr[$row['PRE_COST_ID']][$row['COLOR_NAME']]['MEASUREMENT']*2.20462),4);?>&nbsp;</td>
									<td class="size" align="right"><? echo number_format($cons_qnty,2);?></td>
									<td class="size" align="right"><? echo number_format($prev_wo_qnty,2); ?></td>
									<td class="size" align="right"><? $balance = $cons_qnty-$prev_wo_qnty; echo number_format($balance,2); ?></td>
									<td class="size" align="right"><?=$LeftOverStockInLbs?> </td>
									<td class="size" align="right"><? echo number_format($row['SUPPLIER_ORDER_QUANTITY'],2);?></td>
									<td class="size" align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
									<td class="size" align="right"><? echo number_format($row['RATE'],2); ?></td>
									<td class="size" align="right"><? echo number_format($row['AMOUNT'],2); ?></td>
								</tr>
								<?
								$total_req_qty +=$cons_qnty;
								$total_prev_wo_qnty +=$prev_wo_qnty;
								$total_balance +=$balance;
								$total_tdy_wo +=$row['SUPPLIER_ORDER_QUANTITY'];
								$total_amount +=$row['AMOUNT'];
								$totalLeftOver +=$LeftOverStockInLbs;

								$z++;
							}
							$i++;
						}
					?>

					<tr bgcolor="#dddddd">
						<td class="size" colspan="13" align="right"><b>Total: </b></td>
						<td class="size" align="right"><b><? echo number_format($total_req_qty,2)?></b></td>
						<td class="size" align="right"><b><? echo number_format($total_prev_wo_qnty,2)?></b></td>
						<td class="size" align="right"><b><? echo number_format($total_balance,2)?></b></td>
						<td class="size" align="right"><b> <? echo number_format($totalLeftOver,2)?></b></td>
						<td class="size" align="right"><b><? echo number_format($total_tdy_wo,2)?></b></td>
						<td class="size" align="right"></td>
						<td class="size" align="right"></td>
						<td class="size" align="right"><b><? echo number_format($total_amount,2)?></b></td>
					</tr>
                </tbody>   				
            </table>
			<br><br><br><br>
			<?
			$firstID=$FirstApprovalArr[$wo_id][1]["FIRST_APP"];
			$FirstuserId= sql_select("select id, DESIGNATION from user_passwd  where id=$firstID");
			$lastID=$LastApprovalArr[$wo_id][3]["LAST_APP"];
			$LastuserId= sql_select("select id, DESIGNATION from user_passwd  where id=$lastID");
			$preparedID=$result[0]["INSERTED_BY"];
			$preDesig= sql_select("select id, DESIGNATION from user_passwd  where id=$preparedID");
			?>
			<table style="float: left; margin-left:5px;" class="rpt_table"  width="1200" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-size: 20px;" colspan="7" align="center"><b> </b> <? echo $user_full_name_arr[$preparedID]; ?></td>
						<td style="font-size: 20px;" colspan="7" align="center"><? echo $user_full_name_arr[$firstID]; ?></td>
						<td style="font-size: 20px;" colspan="7" align="center"> <? echo $user_full_name_arr[$lastID]; ?></td>
					</tr>
					<tr>
						<td style="font-size: 20px;" colspan="7" align="center"><b> </b> <? echo $custom_desig[$preDesig[0]["DESIGNATION"]]; ?></td>
						<td style="font-size: 20px;" colspan="7" align="center"><?  echo $custom_desig[$FirstuserId[0]['DESIGNATION']];  ?></td>
						<td style="font-size: 20px;" colspan="7" align="center"> <? echo $custom_desig[$LastuserId[0]['DESIGNATION']]; ?></td>
					</tr>
					<tr>
						<td style="font-size: 20px;" colspan="7" align="center"><b>Pre-Paired By: </b> </td>
						<td style="font-size: 20px;" colspan="7" align="center"><b>1st Approval By: </b> </td>
						<td style="font-size: 20px;" colspan="7" align="center"><b>2nd Approval By: </b> </td>
					</tr>
			</table>
		</div>
    </fieldset>
	<?
    exit();
}

?>
