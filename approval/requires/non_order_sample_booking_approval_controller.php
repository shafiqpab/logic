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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );
$lib_store_arr=return_library_array( "select id, STORE_NAME from LIB_STORE_LOCATION", "id", "STORE_NAME"  );
$department_arr=return_library_array( "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','DEPARTMENT_NAME');
$company_fullName=return_library_array( "select id, company_name from lib_company",'id','company_name');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

function getSequence($parameterArr=array())
{
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 
	//User data.....................	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		$rows['ITEM_ID']=$userDataArr[$rows['USER_ID']]['ITEM_ID'];
		
		$rows['ITEM_ID']=($rows['ITEM_ID']!='')?$rows['ITEM_ID']:$lib_item_cat_id_string;
		$rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;

		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	return $dataArr;
}
//echo '<pre>';print_r($usersDataArr);
function getFinalUser($parameterArr=array())
{
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));


	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

		$rows['BUYER_ID']=($rows['BUYER_ID'] != '') ? $rows['BUYER_ID'] : $lib_buyer_id_string;
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];	
	}

	
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){		
		foreach($userSeqDataArr as $user_id=>$seq){
			if((in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) || $bbtsRows['buyer_id']==0)) {
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1"); 
	//print_r($log_sql);die;
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	

	<script>
	// flowing script for multy select data-------start;
	function js_set_value(id)
	{ 
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	}
	// avobe script for multy select data--------end;
	</script>

	<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
 		<?
		$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
		//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>        
	</form>
	<script language="javascript" type="text/javascript">
  		setFilterGrid("tbl_style_ref");
	</script>
	<?
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$cbo_year=str_replace("'","",$cbo_year);	

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_name  = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id=($alter_user_id!='') ? $alter_user_id:$user_id;
	
	$year_field="";
	$year_field="to_char(a.insert_date,'YYYY') as year";

	$searchCon="";
	if ($cbo_year>0) $searchCon.=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_year";
	if ($cbo_buyer_name>0) $searchCon.=" and a.buyer_id=$cbo_buyer_name";
	if ($txt_booking_no!='') $searchCon .=" and a.BOOKING_NO like('%$txt_booking_no')";
	
	$electronicDataArr=getSequence(array('company_id'=>$company_name,'page_id'=>$menu_id,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));
	//echo '<pre>';print_r($electronicDataArr);
	$where_con="";
	if($approval_type==0) // Un-Approve
	{		
		//Match data..........................
		// if($electronicDataArr['user_by'][$app_user_id]['ITEM_ID']){
		// 	$where_con .= " and a.ITEM_CATEGORY in(".$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'].",0)";			
		// 	$electronicDataArr['sequ_by'][0]['ITEM_ID']=$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'];
		// }

		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";			
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}

		$data_mas_sql = "select a.id as ID, a.buyer_id as BUYER_ID, a.item_category as ITEM_CATEGORY from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form_id in(90,610,140,439) and a.item_category in(2,3,13) and a.is_approved<>1 and a.ready_to_approved=1 and a.company_id=$company_name $where_con $searchCon";
		// echo $data_mas_sql; die;		
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );

		//echo $app_user_id;
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

		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ )
		{
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			
			if($tmp_sys_id_arr[$seq])
			{
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql.="SELECT a.id as ID,c.id as SAMP_ID, a.booking_no_prefix_num as PREFIX_NUM, a.booking_no as BOOKING_NO, a.item_category as ITEM_CATEGORY, a.entry_form_id as ENTRY_FORM, c.entry_form_id as SAMP_ENTRY_FORM, a.fabric_source as FABRIC_SOURCE, a.company_id as COMPANY_ID, a.booking_type as BOOKING_TYPE, a.is_short as IS_SHORT, a.buyer_id as BUYER_ID, a.supplier_id as SUPPLIER_ID, a.pay_mode as PAY_MODE, a.delivery_date as DELIVERY_DATE, a.booking_date as BOOKING_DATE, a.job_no as JOB_NO, a.po_break_down_id as PO_BREAK_DOWN_ID, a.is_approved as IS_APPROVED, a.insert_date   FROM   wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join sample_development_mst c on  c.id=b.style_id  where a.company_id=$company_name and  a.item_category in(2,3,13) and a.ready_to_approved=1 and a.approved_sequ_by=$seq and a.is_approved<>1 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id in(90,610,140,439) $sys_con $searchCon group by a.id,c.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.entry_form_id, c.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, a.insert_date";		
			}
		}		
		$sql = "select * from ($sql) order by insert_date desc";
	}
	else
	{
		$sql="SELECT a.id as ID,c.id as SAMP_ID, a.booking_no_prefix_num as PREFIX_NUM,
	a.booking_no as BOOKING_NO, a.item_category as ITEM_CATEGORY, a.entry_form_id as ENTRY_FORM,c.entry_form_id as SAMP_ENTRY_FORM, a.fabric_source as FABRIC_SOURCE, a.company_id as COMPANY_ID, a.booking_type as BOOKING_TYPE, a.is_short as IS_SHORT, a.buyer_id as BUYER_ID, a.supplier_id as SUPPLIER_ID, a.pay_mode as PAY_MODE, a.delivery_date as DELIVERY_DATE, a.booking_date as BOOKING_DATE, a.job_no as JOB_NO, a.po_break_down_id as PO_BREAK_DOWN_ID, a.is_approved as IS_APPROVED, a.insert_date from  approval_mst d,wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join sample_development_mst c on  c.id=b.style_id where a.id=d.mst_id and a.company_id=$company_name and a.item_category in(2,3,13) and a.ready_to_approved=1 and d.sequence_no={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.approved_sequ_by=d.sequence_no and a.status_active=1 and a.is_deleted=0 and a.entry_form_id in(90,610,140,439) and d.entry_form=9 $searchCon group by a.id,c.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.entry_form_id,c.entry_form_id, 
	a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.pay_mode, 
	a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, a.insert_date order by a.insert_date desc";

    }
//echo $sql;
	$nameArray=sql_select( $sql );
	$booking_data_arr = array();
	foreach ($nameArray as $row)
	{ 
		$booking_data_arr[$row['ID']] = $row['ID'];
	}
	
 	// echo $sql;die;
	
	$print_report_format_ids_non = return_field_value("format_id","lib_report_template","template_name='".$company_name."' and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
	$format_ids_sample_non_knit=explode(",",$print_report_format_ids_non);
	$format_ids_row_knit=$format_ids_sample_non_knit[0];
	//echo '<pre>';print_r($format_ids_non);

	$sample_req_non_print_report_format_arr = return_field_value("format_id","lib_report_template","template_name='".$company_name."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	$format_ids_sample_non_req=explode(",",$sample_req_non_print_report_format_arr);
	$format_ids_row_req=$format_ids_sample_non_req[0];


	 $delivery_date_data=sql_select("select a.id, c.delivery_date from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,sample_development_fabric_acc c where a.id=b.booking_mst_id and b.style_id=c.sample_mst_id and a.company_id=$company_name ".where_con_using_array($booking_data_arr,0,'a.id')." and c.delivery_date  is not null group by a.id, c.delivery_date");

	 foreach($delivery_date_data as $val){
		$delivery_date_arr[$val[csf('id')]]=$val[csf('delivery_date')];
	 }
	 
	 

	$sample_req_with_booking_print_report_format_arr = return_field_value("format_id","lib_report_template","template_name='".$company_name."' and module_id=2 and report_id=142 and is_deleted=0 and status_active=1");
	$format_ids_sample_req_with_booking=explode(",",$sample_req_with_booking_print_report_format_arr);
	//print_r($format_ids_sample_req_with_booking);

	$sample_req_with_booking_format_ids_row=$format_ids_sample_req_with_booking[0];
	
	//echo $sam_format_ids_row.'SDS';
		
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:840px; margin-top:10px">
        <legend>Sample Booking (Without Order) Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="130">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:820px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="802" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <? 
						$i=1;
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF";									
							else $bgcolor="#FFFFFF";									
							
							$value=$row['ID'];
							if($row['BOOKING_TYPE']==4) $booking_type="Sample";
							//echo $row['SAMP_ENTRY_FORM'].'<br>';die;
							if ($row['SAMP_ENTRY_FORM']==203){
								$report_action_arr = array(109=>"sample_requisition_print",110=>"sample_requisition_print1",36=>"show_fabric_booking_report3",37=>"show_fabric_booking_report4",64=>"show_fabric_booking_report5",72=>"show_fabric_booking_report6",174=>"show_fabric_booking_report7",220=>"show_fabric_booking_report8");
								$variable="<a href='#' onClick=\"generate_worder_report_samp('".$row['COMPANY_ID']."','".$row['SAMP_ID']."','".$row['BOOKING_NO']."','".$row['SAMP_ENTRY_FORM']."','".$sample_req_with_booking_format_ids_row."','".$report_action_arr[$sample_req_with_booking_format_ids_row]."','".$i."')\"> ".$row['BOOKING_NO']." <a/>";
							}else{
								
								if ($row['ENTRY_FORM']==90)
								{
									$report_action_arr = array(34=>"show_fabric_booking_report",35=>"show_fabric_booking_report2",36=>"show_fabric_booking_report3",37=>"show_fabric_booking_report4",64=>"show_fabric_booking_report5",72=>"show_fabric_booking_report6",174=>"show_fabric_booking_report7",220=>"show_fabric_booking_report8");
									$variable="<a href='#' onClick=\"generate_worder_report('".$row['BOOKING_NO']."','".$row['COMPANY_ID']."','".$row['IS_APPROVED']."','".$row['ENTRY_FORM']."','".$format_ids_row_knit."','".$report_action_arr[$format_ids_row_knit]."','".$i."')\"> ".$row['BOOKING_NO']." <a/>";
								}
								else if ($row['ENTRY_FORM']==140){
									if($format_ids_row_req==10)
									{ 
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['BOOKING_NO']."','".$row['COMPANY_ID']."','".$row['IS_APPROVED']."','".$row['ENTRY_FORM']."','".$format_ids_row_req."','show_fabric_booking_report','".$i."')\"> ".$row['BOOKING_NO']." <a/>";
									}
									else if($format_ids_row_req==17)
									{ 
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['BOOKING_NO']."','".$row['COMPANY_ID']."','".$row['IS_APPROVED']."','".$row['ENTRY_FORM']."','".$format_ids_row_req."','show_fabric_booking_report_barnali','".$i."')\"> ".$row['BOOKING_NO']." <a/>";
									}
									else if($format_ids_row_req==61)
									{ 
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['BOOKING_NO']."','".$row['COMPANY_ID']."','".$row['IS_APPROVED']."','".$row['ENTRY_FORM']."','".$format_ids_row_req."','show_fabric_booking_report_micro','".$i."')\"> ".$row['BOOKING_NO']." <a/>";
									}else{
										$variable=$row['BOOKING_NO'];
									}	
														
								}
								else{$variable=$row['BOOKING_NO'];}

							}
							
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="50" align="center" valign="middle">
									<input type="checkbox" name="tbl[]" id="tbl_<? echo $i;?>"  onClick="check_booking_approved(<? echo $i;?>);"/>
									<input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?=$row['ID']; ?>" />
								</td>   
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="130"><p><? echo $variable; ?></p></td>
								<td width="80" align="center"><p><? echo $booking_type; ?></p></td>
								<td width="100" align="center"><? if($row['BOOKING_DATE']!="0000-00-00") echo change_date_format($row['BOOKING_DATE']); ?>&nbsp;</td>
								<td width="125"><p><? echo $buyer_arr[$row['BUYER_ID']]; ?>&nbsp;</p></td>
								<td width="160"><p><?									
									if($row['PAY_MODE']==3) echo $company_fullName[$row['SUPPLIER_ID']];
									else if($row['PAY_MODE']==5) echo $company_fullName[$row['SUPPLIER_ID']];
									else if($row['PAY_MODE']==1) echo $supplier_arr[$row['SUPPLIER_ID']];
									else if($row['PAY_MODE']==2) echo $supplier_arr[$row['SUPPLIER_ID']];
								?>&nbsp;</p></td>
								<td align="center" title="<?=$row['ID'];?>"><? if($delivery_date_arr[$row['ID']]!="0000-00-00") echo change_date_format($delivery_date_arr[$row['ID']]); ?>&nbsp;</td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="820" class="rpt_table">
				<tfoot>
                    <td width="50" align="center"><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>,<?= $user_id; ?>)"/>
				
					<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				
				</td>

					
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

function auto_approved($dataArr=array()){
	global $pc_date_time;
	global $user_id;
	$sys_id_arr=explode(',',$dataArr['sys_id']);
	
	$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
	$queryTextRes = sql_select($queryText);
	
	if($queryTextRes[0]['ALLOW_PARTIAL']==1){
		$con = connect();
	
		$query="UPDATE $dataArr[mst_table] SET IS_APPROVED=1,approved_by=$dataArr[approval_by],approved_date='$pc_date_time' WHERE id in ($dataArr[sys_id])";
		$rID1=execute_query($query,1);
		//echo $query;die;
		
		if($rID1==1){ oci_commit($con);}
		else{oci_rollback($con);}
		
		
		disconnect($con);
		//return $query;
	}
	//return $ALLOW_PARTIAL;
}

 	

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$con = connect();


	$company_name = str_replace("'","",$cbo_company_name);
	$approval_type = str_replace("'","",$approval_type);
	$target_ids = str_replace("'","",$target_ids);
	$target_app_id_arr = explode(',',$target_ids);	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	
	$msg=''; $flag=''; $response='';

	
	if($approval_type==0)
	{
		$sql = " select a.id as ID, a.buyer_id as BUYER_ID, a.item_category as ITEM_CATEGORY from wo_non_ord_samp_booking_mst a where a.id in($target_ids)";
		
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand'=>0,'item'=>$row['ITEM_CATEGORY'],'store'=>0,'department'=>0);
		}
		
		//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);
		
		$finalDataArr=getFinalUser(array('company_id'=>$company_name,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0,'match_data'=>$matchDataArr));

			
		$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];

		$mst_field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, inserted_by, insert_date, user_ip";
		$mst_field_array_up="is_approved*approved_sequ_by"; 
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$mst_data_array="";
		foreach($target_app_id_arr as $mst_id)
		{		
			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",9,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')";
			$id=$id+1;
			  //print_r($finalDataArr['final_seq'][$mst_id]);die;
			//mst data....................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no."")); 
		}

		//-------------------History
		$reqs_ids=explode(",",$target_ids);

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing";
		
        $id=return_next_id( "id","approval_history", 1 ) ;
        
        $approved_no_array=array();
		$data_array='';
		$i=0;
		foreach($reqs_ids as $val)
        {
            $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=9","approved_no");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";
             
            $data_array.="(".$id.",9,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
            
            $approved_no_array[$val]=$approved_no;
                
            $id=$id+1;
            $i++;
        }
		//echo $data_array;die;

		if(count($approved_no_array)>0)
		{
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
			}
			
			$approved_string_mst="CASE booking_no ".$approved_string." END";
			$approved_string_dtls="CASE booking_no ".$approved_string." END";
			
			$sql_insert="insert into wo_nonord_samboo_msthtry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,revised_date) 
			select	
			'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'".date('d-M-Y',time())."' from wo_non_ord_samp_booking_mst where id in ($target_ids)";
					
			$sql_insert_dtls="insert into wo_nonor_sambo_dtl_hstry(id, approved_no, booking_dtls_id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,revised_date) 
			select	
			'', $approved_string_dtls, id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'".date('d-M-Y',time())."' from wo_non_ord_samp_booking_dtls where booking_mst_id in ($target_ids)";
		}

		$flag=1;
		//echo "insert into approval_mst ($mst_field_array) values $mst_data_array";die;
		$rID=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}
		
		$rID1=execute_query(bulk_update_sql_statement( "wo_non_ord_samp_booking_mst", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
		if($flag==1) 
		{
			if($rID1) $flag=1; else $flag=0;
		}
	   
	    $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=9 and mst_id in ($target_ids)"; //die;
        $rID2=execute_query($query,1);
        if($flag==1) 
        {
            if($rID2) $flag=1; else $flag=0; 
        }
		

		//echo "insert into approval_history $field_array values($data_array)";die;
		$rID3=sql_insert("approval_history",$field_array,$data_array,0);
        if($flag==1) 
        {
            if($rID3) $flag=1; else $flag=0; 
            
        }
        $rID4=execute_query($sql_insert,0);
        if($flag==1) 
        {
            if($rID4) $flag=1; else $flag=0; 
            
        }       
        $rID5=execute_query($sql_insert_dtls,1);
        if($flag==1) 
        {
            if($rID5) $flag=1; else $flag=0;            
        } 
		$rID6=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$target_ids,1);
		if($flag==1)
		{
			if($rID6) $flag=1; else $flag=0;
		}
		//echo $rID.','.$rID2.','.$rID3.','.$rID4.','.$rID5.','.$flag;oci_rollback($con);die;
		$response=$target_ids;
		if($flag==1) $msg='19'; else $msg='21';
		
	}

	else if($approval_type==5)
	{ 
		$rID1=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved*ready_to_approved*approved_sequ_by",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$app_user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=9 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=9 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else
	{
		$history_data_arr=return_library_array( "select id, id from approval_history where current_approval_status=1 and entry_form=9 and mst_id in ($target_ids)",'id','id');
		$app_ids= implode(',',$history_data_arr);
		
		$flag=1;
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved*ready_to_approved*approved_sequ_by","0*0*0","id",$target_ids,0); 
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0;
		}

		$query="delete from approval_mst WHERE entry_form=9 and mst_id in ($target_ids)";
		$rID2=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		//-----------------------History
		$rID3=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved*ready_to_approved",'0*2',"id",$target_ids,0);
		if($rID3) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0,is_signing=0 WHERE entry_form=9 and mst_id in ($target_ids)";
		$rID4=execute_query($query,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0;
		} 
		
		$data=$user_id."*'".$pc_date_time."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID5=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0;
		}
		$rID6=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$target_ids,1);
		if($flag==1)
		{
			if($rID6) $flag=1; else $flag=0;
		}
			
		
		$response=$target_ids;
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


if($action=="check_sales_order_approved")
{
	$last_update=return_field_value("is_approved","fabric_sales_order_mst","sales_booking_no='".trim($data)."'");
	echo $last_update;
	exit();	
}

if($action=="get_requisition_no_from_booking")
{
	$sql="SELECT distinct a.id
  	FROM sample_development_mst a, wo_non_ord_samp_booking_mst b ,wo_non_ord_samp_booking_dtls c
 	WHERE     a.status_active = 1
       AND a.is_deleted = 0
       AND b.status_active = 1
       AND b.is_deleted = 0
       and  c.status_active = 1
       AND c.is_deleted = 0
       and b.booking_no=c.booking_no
       and a.id=c.style_id
       AND b.id = $data ";
    $res=sql_select($sql);
    if(count($res))
    {
    	echo $res[0][csf('id')];
    }
    exit();

	
}
?>


