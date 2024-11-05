<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.others.php');
include('../../includes/class4/class.commercials.php');
include('../../includes/class4/class.commisions.php');


if($_SESSION['app_notification'] == 1){
	include('../../includes/class4/Notifications.php');
	$notification = new Notifications();
}

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id = $_SESSION['menu_id'];

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$userbrand_idCond="";

if ($brand_id !='') {
    $userbrand_idCond = " and id in ( $brand_id)";
}

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.insert_date,'YYYY')";

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/pre_costing_approval_group_by_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down('requires/pre_costing_approval_group_by_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 80, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]'");
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


if ($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}

//Group app start..............................................................

function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));
	//$lib_store_id_string=implode(',',(array_keys($parameterArr['lib_store_arr']))); 
	//$product_dept_id_string=implode(',',(array_keys($parameterArr['product_dept_arr'])));

	//$brand_arr=return_library_array( "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0 $where_con", "id", "brand_name"  );

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
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
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	    //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		$userDataArr[$rows['USER_ID']]['BUYER_ID']=$rows['BUYER_ID'];
		$userDataArr[$rows['USER_ID']]['BRAND_ID']=$rows['BRAND_ID'];
		$userDataArr[$rows['USER_ID']]['DEPARTMENT']=$rows['DEPARTMENT'];
		

		if($userDataArr[$rows['USER_ID']]['BUYER_ID']=='' || $userDataArr[$rows['USER_ID']]['BUYER_ID'] == 0){
			$userDataArr[$rows['USER_ID']]['BUYER_ID']=$lib_buyer_arr;
		}
		

		if($userDataArr[$rows['USER_ID']]['BRAND_ID']=='' || $userDataArr[$rows['USER_ID']]['BRAND_ID'] == 0){
			$tempBrandArr = array();
			foreach(explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$userDataArr[$rows['USER_ID']]['BRAND_ID']=implode(',',$tempBrandArr);
		}
		 

		// if($userDataArr[$rows['USER_ID']]['DEPARTMENT']==''){
		// 	$userDataArr[$rows['USER_ID']]['DEPARTMENT']=$product_dept_arr;
		// }
		
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BRAND_ID']);
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$userDataArr[$rows['USER_ID']]['DEPARTMENT']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	
	}

 
	
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer'],$usersDataArr[$user_id]['BUYER_ID']) && $bbtsRows['buyer']>0)
				&& (in_array($bbtsRows['brand'],$usersDataArr[$user_id]['BRAND_ID']) || $bbtsRows['brand']==0)
			 
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}
	

	  //var_dump($buyer_wise_my_previous_group_arr);die;
	 //var_dump($usersDataArr[526]['DEPARTMENT']);die;
	
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
//$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

 /*
 Note:
 */

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 
	$job_no = str_replace("'","",$txt_job_no);
	$file_no = str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$job_year = str_replace("'","",$cbo_year);
	$txt_styleref = str_replace("'","",$txt_styleref);
	$company_id = str_replace("'","",$cbo_company_name);
	$cbo_style_owner_id = str_replace("'","",$cbo_style_owner_id);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$cbo_brand = str_replace("'","",$cbo_brand);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$cbo_season_id = str_replace("'","",$cbo_season_id);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$cbo_get_upto = str_replace("'","",$cbo_get_upto);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	//............................................................................
	
	$app_user_id = ($txt_alter_user_id != '')?$txt_alter_user_id:$user_id;
 
	$buyer_arr[0]=0;
	$electronicDataArr = getSequence(array('company_id'=>$company_id,'entry_form'=>77,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
	 
	$my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
	$my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
	$my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
	$electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

	$my_previous_bypass_no_seq = 0;
	rsort($electronicDataArr['bypass_seq_arr'][2]);
	foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
		if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
	}
	if($my_seq == ''){echo "<u><h2 style='color:red;'>You have no approval permission.</h2></u>";die();}

	
	 
	if($job_no){
		$where_con = " and a.JOB_NO like('%$job_no')";
	}
	if($file_no){$where_con .=" and c.file_no like('%$file_no')";}
	if($txt_styleref){
		$where_con .=" and b.style_ref_no like('%$txt_styleref')";
	}
	if($cbo_season_id){
		$where_con .=" and b.SEASON_BUYER_WISE=$cbo_season_id";
	}
	if($cbo_buyer_name){
		$where_con .=" and b.buyer_name =$cbo_buyer_name";
	}
	if($cbo_brand){
		$where_con .=" and b.brand_id =$cbo_brand";
	}
	 
	//if($internal_ref){$where_con .=" and c.grouping =$internal_ref";}
	if($internal_ref != ""){$internal_ref_con.= " and c.grouping like '%".trim($internal_ref)."%' ";}
	if($job_year){
		$where_con .=" and to_char(b.insert_date,'YYYY') =$job_year";
	}
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$where_con .= " and a.costing_date between '$txt_date_from' and '$txt_date_to'";
	}

	if($cbo_style_owner_id){
		$where_con .=" and a.STYLE_OWNER=$cbo_style_owner_id";
	} 
	//echo $where_con;die;	
	
	$internalRefCond="rtrim(xmlagg(xmlelement(e,c.grouping,',').extract('//text()') order by c.grouping).GetClobVal(),',')"; 
	$fileNoCond="rtrim(xmlagg(xmlelement(e,c.file_no,',').extract('//text()') order by c.file_no).GetClobVal(),',')"; 
	
 
	
	if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
		$where_con .= " and b.BUYER_NAME in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
		$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
	}
	if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
		$where_con .= " and b.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
		$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
	}


	if($approval_type==2) // Un-Approve
	{

		$variable_sql = "select CM_STD_PER,CM_STD_VALUE,MARGIN_STD_PER,MARGIN_STD_VALUE from VARIABLE_APPROVAL_SETTINGS where COMPANY_NAME=$company_id and VARIABLE_LIST=1 and IS_REQUIRED=1";
		// echo $variable_sql;die;
		$variable_sql_res = sql_select( $variable_sql );
		extract($variable_sql_res[0]);
 
		
		//Match data..................................

			$data_mas_sql = " select a.ID,a.APPROVED_BY,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY,b.BRAND_ID,b.BUYER_NAME,b.JOB_NO,c.CM_COST,c.CM_COST_PERCENT,c.MARGIN_PCS_SET,c.MARGIN_PCS_SET_PERCENT,a.CONFIRM_APPROVAL from wo_pre_cost_mst a,wo_po_details_master b,WO_PRE_COST_DTLS c where b.id=a.job_id and b.id=c.job_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and a.APPROVED<>1 and a.READY_TO_APPROVED=1 and b.COMPANY_NAME=$company_id $where_con";
			 //echo $data_mas_sql; die;

		 // var_dump($electronicDataArr['group_seq_arr'][0]);
		  	
			$tmp_sys_id_arr=array();$sys_data_arr=array();
			$data_mas_sql_res=sql_select( $data_mas_sql );
			foreach ($data_mas_sql_res as $row)
			{ 	//echo $my_previous_bypass_no_seq.'='.$row['APPROVED_GROUP_BY'];
				$group_stage_arr = array();
				for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
					
					krsort($electronicDataArr['group_seq_arr'][$group]);
					foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
						
						if($seq<$my_seq){
							if(
								(in_array($row['BUYER_NAME'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_NAME']==0) && (in_array($row['BRAND_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID']==0) 
								&& ($row['APPROVED_GROUP_BY']<=$group)
								)
							{ 

								// print_r($electronicDataArr['group_seq_arr'][$my_group]);
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
								if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq )  ){ 
									unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
									break; 
								}

								$group_stage_arr[$row['ID']][$group] = $group;
							}
						}
					}
					
				}//group loof;

				foreach($group_stage_arr as $sys_id => $gidArr){ 
					foreach($gidArr as $gid => $gid){	
						if(count($group_stage_arr[$sys_id])>1 && array_key_first($group_stage_arr[$sys_id]) != $gid ){
							unset($tmp_sys_id_arr[$gid]);
						}
					}
				}


				//Below part for only precost BOM Confarmation.....................
				if(count($variable_sql_res) == 1){
					$confirm_flag = array();
					if( $CM_STD_VALUE > 0){$confirm_flag['CM_COST'] = $CM_STD_VALUE;}
					if($CM_STD_PER > 0){$confirm_flag['CM_COST_PERCENT'] = $CM_STD_PER;}
					if($MARGIN_STD_VALUE > 0){$confirm_flag['MARGIN_PCS_SET'] = $MARGIN_STD_VALUE;}
					if($MARGIN_STD_PER > 0){$confirm_flag['MARGIN_PCS_SET_PERCENT'] = $MARGIN_STD_PER;}
					
					foreach($confirm_flag as $coloum => $col_val){ 
						if($row['CONFIRM_APPROVAL'] == 1){}
						else if($row['CONFIRM_APPROVAL'] != 1 && $row[$coloum] < $col_val){
							unset($tmp_sys_id_arr[0]);
						}	
					}
				}
				//...............................end;



			}
		//..........................................Match data;
 
		 
		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
		
					$sql .= "select a.COSTING_PER,a.ID,a.sew_effi_percent,a.sew_smv,a.exchange_rate, b.QUOTATION_ID, b.JOB_NO_PREFIX_NUM, to_char(a.insert_date,'YYYY') as YEAR, b.id as JOB_ID, a.JOB_NO, b.BUYER_NAME,b.BRAND_ID, b.SEASON_BUYER_WISE as SEASON, b.SEASON_YEAR, b.STYLE_REF_NO, a.COSTING_DATE, a.APPROVED, a.INSERTED_BY, min(c.shipment_date) as MINSHIP_DATE, max(c.shipment_date) as MAXSHIP_DATE, b.JOB_QUANTITY, (b.job_quantity*b.total_set_qnty) as JOB_QTY_PCS,(b.job_quantity * b.avg_unit_price)    AS total_value ,b.avg_unit_price, b.TOTAL_PRICE,$internalRefCond as internalRef, $fileNoCond as fileNo from wo_pre_cost_mst a, wo_po_details_master b, wo_po_break_down c where b.company_name=$company_id and a.approved<>1 and a.APPROVED_SEQU_BY = $seq and a.APPROVED_GROUP_BY=$group $sys_con and  a.job_id=b.id and b.id=c.job_id and a.ready_to_approved=1 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $where_con $internal_ref_con group by a.costing_per,b.id, b.quotation_id, b.job_no_prefix_num, to_char(a.insert_date,'YYYY'), a.id, a.job_no, b.buyer_name,b.brand_id, b.SEASON_BUYER_WISE,a.sew_effi_percent,b.avg_unit_price, b.season_year, b.style_ref_no,a.sew_smv, a.costing_date, a.approved, b.inserted_by, b.job_quantity, b.total_set_qnty, b.total_price,a.exchange_rate, a.INSERTED_BY"	;	
			
				}
		
			}
		}
		//echo $sql;die;

	}
	else
	{
		$sql = "select a.COSTING_PER,a.ID, b.QUOTATION_ID,a.sew_effi_percent,a.sew_smv,a.exchange_rate, b.JOB_NO_PREFIX_NUM, to_char(a.insert_date,'YYYY') as YEAR, b.id as JOB_ID, a.JOB_NO, b.BUYER_NAME,b.BRAND_ID, b.SEASON_BUYER_WISE as SEASON, b.SEASON_YEAR, b.STYLE_REF_NO, a.COSTING_DATE, a.APPROVED, a.INSERTED_BY, min(c.shipment_date) as MINSHIP_DATE, max(c.shipment_date) as MAXSHIP_DATE, b.JOB_QUANTITY,b.avg_unit_price, (b.job_quantity*b.total_set_qnty) as JOB_QTY_PCS,(b.job_quantity * b.avg_unit_price)    AS total_value , b.TOTAL_PRICE,$internalRefCond as internalRef, $fileNoCond as fileNo from wo_pre_cost_mst a, wo_po_details_master b, wo_po_break_down c,APPROVAL_MST d 
		 where a.job_id=b.id and b.id=c.job_id and d.mst_id=a.id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and a.READY_TO_APPROVED=1  and a.APPROVED_GROUP_BY=d.GROUP_NO and d.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']} and d.ENTRY_FORM=77 and a.APPROVED<>0 $where_con $internal_ref_con 
		  group by a.costing_per,b.id, b.quotation_id, b.job_no_prefix_num, to_char(a.insert_date,'YYYY'), a.id, a.job_no, b.buyer_name,b.brand_id, b.SEASON_BUYER_WISE, b.season_year,a.exchange_rate,b.avg_unit_price, b.style_ref_no, a.costing_date, a.approved, b.inserted_by, b.job_quantity,a.sew_smv,a.sew_effi_percent, b.total_set_qnty, b.total_price, a.INSERTED_BY";
	}
	 //echo $sql;die;

	 // echo $my_previous_bypass_no_seq;

 	$nameArray=sql_select( $sql );
	$jobFobValue_arr=array(); $jobIds_arr=array(0);
	foreach ($nameArray as $row)
	{
		$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
		$jobIds_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		$job_no_array[$row[csf('job_no')]]=$row[csf('job_no')];
		$job_qnty_Arr[$row[csf('job_no')]]['job_quantity']+=$row[csf('job_quantity')];
		$job_aver_Arr[$row[csf('job_no')]]['avg_unit_price']=$row[csf('avg_unit_price')];
		$job_value_Arr[$row[csf('job_no')]]['total_value']+=$row[csf('total_value')];
		$order_values =$job_value_Arr[$row[csf('job_no')]]['total_value'];
		$poQty=$job_qnty_Arr[$row[csf('job_no')]]['job_quantity'];
		$pre_costing_date=change_date_format($row[csf('costing_date')],'','',1);
		$sew_effi_Arr[$row[csf('job_no')]]['sew_effi_percent']=$row[csf('sew_effi_percent')];
		$exchange_rate_Arr[$row[csf('job_no')]]['exchange_rate']=$row[csf('exchange_rate')];
		$sew_value_Arr[$row[csf('job_no')]]['sew_smv']=$row[csf('sew_smv')];  
		$pre_costing_date_arr[$row[csf('job_no')]]['costing_date']=change_date_format($row[csf('costing_date')],'','',1);  
	}

	// 	echo "<pre>";
	// 	print_r($sew_value_Arr); 
	//    echo "</pre>";die(); 
	$jobIds=implode(',',$jobIds_arr);

	$jobId_cond = where_con_using_array($jobIds_arr,0,'job_id');

	$bomDtls_arr=array();
	$bomDtlssql=sql_select( "select job_no, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, margin_pcs_set_percent from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond");

	foreach ($bomDtlssql as $row)
	{
		$bomDtls_arr[$row[csf('job_no')]]['trimper']=$row[csf('trims_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['ms']=$row[csf('fabric_cost_percent')]+$row[csf('trims_cost_percent')]+$row[csf('embel_cost_percent')]+$row[csf('wash_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_pcs_set_percent')];
		$fabric_cost_percentArr[$row[csf('job_no')]]=$row[csf('fabric_cost_percent')];
	}
	unset($bomDtlssql);

	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	
	if($jobIds!=''){
		$condition->jobid_in("$jobIds");
	}
	if(str_replace("'","",$txt_file_no)!='')
	{
		$condition->file_no("=$txt_file_no"); 
	}
	if(str_replace("'","",$txt_internal_ref)!='')
	{
		$condition->grouping("=$txt_internal_ref"); 
	}

	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_id and status_active=1 and is_deleted=0 order by id");

	foreach($sql_std_para as $row )
	{
		$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$date_all=add_date(str_replace("'","",$applying_period_date),$j);
			$newdate =change_date_format($date_all,'','',1);
			$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
			$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
			$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
		}
	}
	
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_amount_job_uom=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$wash= new wash($condition);
	$wash_data_array=$wash->getAmountArray_by_jobAndEmbtype();
	$emblishment= new emblishment($condition);
	$emblishment_data_array=$emblishment->getAmountArray_by_jobAndEmbname();
	$conversion= new conversion($condition);
	$yarn= new yarn($condition);
	//echo $yarn->getQuery();die;
	$trim= new trims($condition);
	$yarn_data_array=$yarn->getJobWiseYarnAmountArray();
	
	$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$conv_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
     $other= new other($condition);
//echo $other->getQuery();die;
$commercial= new commercial($condition);

$commision= new commision($condition);

$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
/* echo '<pre>';
print_r($fabric_costing_arr);die; */
$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();

$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
$trims_costing_arr=$trim->getAmountArray_by_job();
$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();

$commercial_costing_arr=$commercial->getAmountArray_by_job();
$commission_costing_arr=$commision->getAmountArray_by_job();

$other_costing_arr=$other->getAmountArray_by_job();

$calCMarr=array();$cm=array();$margin=array();$cpmCalarr=array();

						foreach($job_no_array as $job_no ){
							
						$ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
						$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
						$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
						$yarn_costing=$yarn_costing_arr[$job_no];
						$conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);

						$cpmCal=($financial_para[$pre_costing_date_arr[$job_no]['costing_date']][cost_per_minute]/$exchange_rate_Arr[$job_no]['exchange_rate'])/($sew_effi_Arr[$job_no]['sew_effi_percent']/100);

					   //echo $cpmCal;die;

						// echo $financial_para[$pre_costing_date_arr[$job_no]['costing_date']][cost_per_minute].'=='.$otherCost.'=='.$exchange_rate_Arr[$job_no]['exchange_rate'].'=='.$sew_effi_Arr[$job_no]['sew_effi_percent'];die;
						
						$fabricCost=$fab_purchase_knit+$fab_purchase_woven+$yarn_costing+$conversion_cost;
						$totMaterialCost=$fabricCost+$trims_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no]+$emblishment_costing_arr[$job_no];

						$otherCost=$commercial_costing_arr[$job_no]+$other_costing_arr[$job_no]['currier_pre_cost']+$commission_costing_arr[$job_no]+$other_costing_arr[$job_no]['lab_test']+$other_costing_arr[$job_no]['inspection']+$other_costing_arr[$job_no]['common_oh']+$other_costing_arr[$job_no]['deffdlc_cost']+$other_costing_arr[$job_no]['freight'];

						//echo $otherCost;die;
						//echo $totMaterialCost.'=='.$otherCost;die;
						$breakevencm=$cpmCal*$sew_value_Arr[$job_no]['sew_smv']*$job_qnty_Arr[$job_no]['job_quantity'];
						//print_r($breakevencm);
						$tot_qnty=$totMaterialCost+$otherCost;
						//print_r($job_value_Arr);
						$calCM=$job_value_Arr[$job_no]['total_value']-($totMaterialCost+$otherCost);
                        
						//echo $calCM;die;
						//$cmPcs=$calCM/$poQty;
						//$calCM=$order_values-$tot_qnty;
						
						$cmPcs=$calCM/$job_qnty_Arr[$job_no]['job_quantity'];
                         //echo $cmPcs;die;
						$totalMargin=$calCM-$breakevencm;
						//echo $totalMargin;
						//  echo $calCM."/".$job_qnty_Arr[$job_no]['job_quantity']."==".$calCM/$job_qnty_Arr[$job_no]['job_quantity']."<br>";
					     $marginPcs=$totalMargin/$poQty;

						 $calCMarr[$job_no]=$calCM /$job_qnty_Arr[$job_no]['job_quantity'];

						 $cm[$job_no] =($calCM / $job_value_Arr[$job_no]['total_value'])*100;

						 $cpmCalarr[$job_no] =$cpmCal;

						 
						
						 $margin[$job_no]=($totalMargin / $job_value_Arr[$job_no]['total_value'])*100;
						 //$margin[$job_no]=$breakevencm;

						 $epmarr[$job_no]=$cmPcs/$sew_value_Arr[$job_no]['sew_smv'];
						
                       
						 }
						

	unset($data_arr_fabric);
	

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id =43 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	 //echo $row_id.'d';

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	$width=2050;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$width+20;?>px; margin-top:10px">
        <legend>Pre-Costing Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" align="left" >
                <thead>
                    <th width="40"></th>
                    <th width="30">SL</th>
                    <th width="50">Job No</th>
                    <th width="70">Master Style/Internal Ref.</th>
                    <th width="110">Buyer</th>
                    <th width="40">Year</th>
					<th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="50">Season Year</th>
                    <th width="130">Style Ref.</th>
                    <th width="70">Costing Date</th>
                    <th width="70">Ship Start</th>
                    <th width="70">Ship End</th>
                    <th width="70">Job Qty(Pcs)</th>
                    <th width="60">Avg. Rate</th>
                    <th width="80">Total Value</th>
                    <th width="60" title="(Woven Finish/Total Price)*100">Fabric %</th>
                    <th width="60">Trims %</th>
                    <th width="60">Embel. Cost %</th>
                    <th width="60">Gmts.Wash%</th>
                    <th width="60">CM %</th>
                    <th width="60">Margin %</th>
                    <th width="100" title="As Per RG Formula">EPM</th>
                    <th width="140">Unapproved Request</th>
                    <th width="65">Insert By</th>
                    <th width="100">Approved Date</th>
					<th width="120">Refusing Cause</th>
                </thead>
            </table>
            <div style="width:<?=$width+20;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                        $i=1; //die;
						$aop_cost_arr=array(35,36,37,40);

					
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$value=$row[csf('id')];
							if($row[csf('approval_id')]==0) $print_cond=1;
							else
							{
								if($duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
								{
									$duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('approval_id')];
									$print_cond=1;
								}
								else
								{
									if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
									$print_cond=0;
								}
							}
							

							if($row_id2==23){$type=1;/*Summary;*/}
							else if($row_id2==24){$type=2;}
							else if($row_id2==25){$type=3;/*Budget Report2;*/}
							else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
							else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
							else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
							else if($row_id2==182){$type=7;/*Budget Report 3;*/}

							

							$function2="generat_print_report($type,$cbo_company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
							if($print_cond==1)
							{
								if($row_id==51) $action='preCostRpt2';
								else if($row_id==307)$action='basic_cost';
								else if($row_id==311)$action='bom_epm_woven';
								else if($row_id==313)$action='mkt_source_cost';
								else if($row_id==158) $action='preCostRptWoven';
								else if($row_id==159)$action='bomRptWoven';
								else if($row_id==192)$action='checkListRpt';
								else if($row_id==761) $action='bom_pcs_woven';
								else if($row_id==381) $action='mo_sheet_2';
								else if($row_id==403) $action='mo_sheet_3';
								else if($row_id==25) $action='budgetsheet2';
								else if($row_id==221) $action='fabric_cost_detail';

						 
			
								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."','','".$row[csf('costing_per')]."');"; 
								$jobavgRate=0; $int_ref = ""; $file_numbers = "";
								$jobavgRate=$row[csf('total_price')]/$row[csf('job_quantity')];
								if($db_type==2) $row[csf('internalRef')]= $row[csf('internalRef')]->load();
								
								$int_ref=implode(",",array_unique(explode(",",chop($row[csf('internalRef')],","))));
								$finishPercent=$trimPercent=$fabpurchase_per=$aopamt=$yarn_dyeingAmt=$yarn_dyeingPer=$msper=$aopPer=$cmper=$marginper=0;
								$trimPercent=$bomDtls_arr[$row[csf('job_no')]]['trimper'];
								
								//$finishPercent=(array_sum($fabric_amount_job_uom['woven']['finish'][$row[csf('job_no')]])/$row[csf('total_price')])*100;

								$finishPercent= $fabric_cost_percentArr[$row[csf('job_no')]];

								//print_r($fabric_amount_job_uom['knit']['grey']);
								
								$washPercent=(array_sum($wash_data_array[$row[csf('job_no')]])/$row[csf('total_price')])*100;
								$emblishmentPercent=(array_sum($emblishment_data_array[$row[csf('job_no')]])/$row[csf('total_price')])*100;
								
								foreach($aop_cost_arr as $aop_process_id)
								{
									$aopamt+=array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
								}
								$aopPer=($aopamt/$row[csf('total_price')])*100;
								
								$msper=$bomDtls_arr[$row[csf('job_no')]]['ms'];
								$cmper=$bomDtls_arr[$row[csf('job_no')]]['cm'];
								$marginper=$bomDtls_arr[$row[csf('job_no')]]['margin'];
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                                	<td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?=$i;?>" />
                                        <input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
                                        <input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('job_no')]; ?>" />
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                    </td>
									<td width="30" align="center"><?=$i; ?></td>
									<td width="50"><a href='##' onclick="<?=$function; ?>"><?=$row[csf('job_no_prefix_num')]; ?></a></td>
                                    <td width="70"><?=$int_ref; ?></td>
                                    <td width="110"><p><?=$buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                                    <td width="40" align="center"><?=$row[csf('year')]; ?></td>
									<td width="80"><?=$brandArr[$row[csf('brand_id')]]; ?></td>
                            	    <td width="80"><?=$seasonArr[$row[csf('season')]]; ?></td>
                              		<td width="50"><?=$row[csf('season_year')]; ?></td>
                                    <td width="125"><?=$row[csf('style_ref_no')]; ?></td>
                                    <td width="70" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?></td>
                                    <td align="center" width="70"><? if($row[csf('minship_date')]!="0000-00-00") echo change_date_format($row[csf('minship_date')]); ?></td>
                                    <td align="center" width="70"><? if($row[csf('maxship_date')]!="0000-00-00") echo change_date_format($row[csf('maxship_date')]); ?></td>
                                    <td width="70" align="right"><?=number_format($row[csf('job_qty_pcs')]); ?></td>
                                    <td width="60" align="right"><?=number_format($jobavgRate,4); ?></td>
                                    <td width="80" align="right"><?=number_format($row[csf('total_price')],2); ?></td>
                                    
                                    <td width="60" align="right"><?=number_format($finishPercent,2); ?></td>
                                    <td width="60" align="right"><?=number_format($trimPercent,2); ?></td>
                                    <td width="60" align="right"><?=number_format($emblishmentPercent,2); ?></td>
                                    <td width="60" align="right"><?=number_format($washPercent,2); ?></td>
                                    <td width="60" align="right" id="tdCm_<?=$i;?>"><?=number_format($cmper,2); ?></td>
                                    <td width="60" align="right"><?=number_format($marginper,2); ?></td>
									<td width="100" align="right" ><?=number_format($epmarr[$row[csf('job_no')]],3); ?></td>
                                    
                                    <td width="140"><? if($approval_type==1) echo $unapproved_request_arr[$value]; ?> </td>
                                    <td width="65"><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?></td>
                                    <td align="center" width="100"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?></td>
															<?
										$mst_id=$row[csf('id')];
										$refusing_reason_arr =sql_select("SELECT id,refusing_reason from refusing_cause_history where  mst_id='$mst_id' order by id desc ");							  
									//	 print_r($refusing_reason_arr);
										?>

										<td width="120"> <input style="width:100px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/pre_costing_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<?  echo $refusing_reason_arr[0][csf('refusing_reason')]; //$row[csf('refusing_cause')];?>"/></td>
								</tr>
								<?
								$i++;
							}

							if($all_approval_id!="")
							{
								$con = connect();
								$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
								//echo $rID."**";
								if($db_type==2 || $db_type==1 )
								{
									if($rID==1)
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
							}
							$denyBtn=""; $denyBtnMsg=""; $btnmsg=""; 
							if($approval_type==2) 
							{
								$denyBtn=""; 							
								$denyBtnMsg="Deny";
							
							}
							else 
							{
								$denyBtn=" display:none";							
								$denyBtnMsg="";
							
							}
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$width;?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="40" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
					&nbsp;&nbsp;&nbsp;<input type="button" value="<?=$denyBtnMsg; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				
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

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$approval_type = str_replace("'","",$approval_type);
	$booking_nos = str_replace("'","",$booking_nos);
	$booking_ids = str_replace("'","",$booking_ids);
	$approval_ids = str_replace("'","",$approval_ids);
	$refuse_causes = str_replace("'","",$refuse_causes);
	$user_id_approval = ($txt_alter_user_id)?$txt_alter_user_id:$user_id;

	$booking_nos_arr = explode(',',$booking_nos);	
	$target_app_id_arr = explode(',',$booking_ids);
	$refuse_cause_arr = explode(',',$refuse_causes);

	//echo $booking_nos;die;
	
	
	$sql="select A.ID,a.APPROVED,a.READY_TO_APPROVED,a.JOB_NO,b.BUYER_NAME,b.BRAND_ID from wo_pre_cost_mst a, wo_po_details_master b where a.job_id=b.id and a.id in($booking_ids)";
	//echo $sql;die;
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '25**Please select ready to approved yes for approved this Job';exit();}
		$matchDataArr[$row['ID']]=array('buyer'=>$row['BUYER_NAME'],'brand'=>$row['BRAND_ID'],'item'=>0,'store'=>0);
		$approved_status_arr[$row['ID']] = $row['APPROVED'];
		$approved_job_arr[$row['ID']] = $row['JOB_NO'];
	}

	
	$entry_form = 77;
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'entry_form'=>$entry_form,'lib_buyer_arr'=>$buyer_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'product_dept_arr'=>$product_dept,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);
	
	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=77 group by mst_id","mst_id","approved_no");
	
	
	if($approval_type==2){

		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;

        foreach($target_app_id_arr as $key => $mst_id)
        {		
			
			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

			$approved_no=$max_approved_no_arr[$mst_id]*1;
			
			$approved_status=$approved_status_arr[$mst_id]*1;
			if($approved_status==2 || $approved_status==0)
			{	
				$approved_no=$max_approved_no_arr[$mst_id]+1;

				$approved_no_array[$booking_nos_arr[$key]] = $approved_no;
				$target_job_no_arr[$booking_nos_arr[$key]] = $booking_nos_arr[$key];
			}

			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",".$entry_form.",".$mst_id.",".$user_sequence_no.",".$user_group_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$id=$id+1;
			

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",".$entry_form.",".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.",'".$refuse_cause_arr[$key]."')";
			$ahid++;
			
			//mst data.......................
			$mst_data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",".$user_id_approval.",'".$pc_date_time."'")); 

			if($approved == 1)
			{
				$is_mst_final_seq[$mst_id] = $mst_id;
			}

        }
	

        $flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,USER_IP,APPROVED";
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
 
		
		if($flag==1) 
		{
			$field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_up, $mst_data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}
	
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID15=execute_query($query,1);
			if($rID15) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID16=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID16) $flag=1; else $flag=0;
		}

 
		
		
		//$job_nos  = "'".str_replace(',',"','",$booking_nos)."'";
		//$target_job_no_arr = explode(',',$booking_nos);	

		$job_nos  = "'".implode("','",$target_job_no_arr)."'";
		if(count($target_job_no_arr)>0)
		{
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN TO_NCHAR('$key') THEN '".$value."'";
			}
			$approved_string_mst="CASE job_no ".$approved_string." END";
			$approved_string_dtls="CASE job_no ".$approved_string." END";

			
			$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place,
			machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent,
			cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active,
			is_deleted)
					select
					'', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per,
			remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent,
			efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
			from wo_pre_cost_mst where job_no in ($job_nos)";
			 // echo $sql_insert;die;


			$sql_precost_dtls="insert into wo_pre_cost_dtls_histry(id,approved_no,pre_cost_dtls_id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
			commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
			currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
			margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
			cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select
					'', $approved_string_dtls, id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
			commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
			currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
			margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
			cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_dtls  where  job_no in ($job_nos)";
			//echo $sql_precost_dtls;die;


			//------------------wo_pre_cost_fabric_cost_dtls_h-------------------------------------------------
			$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id,approved_no,pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type)
				select
				'', $approved_string_dtls, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_no in ($job_nos)";
			//echo $sql_precost_fabric_cost_dtls;die;

			//--------------------wo_pre_cost_fab_yarn_cst_dtl_h--------------------------------------------------------
			$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
			inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_no in ($job_nos)";
				//echo $sql_precost_fab_yarn_cst;die;

			//----------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
			$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
			is_deleted from wo_pre_cost_comarci_cost_dtls where  job_no in ($job_nos)";
				//echo $sql_precost_fcomarc_cost_dtls;die;


			//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
			$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
			commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_no in ($job_nos)";
			//	echo $sql_precost_commis_cost_dtls;die;

			//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
			$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
			emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,emb_name,
			emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_no in ($job_nos)";
				//echo $sql_precost_commis_cost_dtls;die;

			//---------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------

			$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_no in ($job_nos)";
				//echo $sql_precost_fab_yarnbkdown_his;die;

			//------------------------------wo_pre_cost_sum_dtls_histroy-----------------------------------------------

			$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_no in ($job_nos)";
				//echo $sql_precost_fab_sum_dtls;die;
				//-----------------------------wo_pre_cost_trim_cost_dtls_his------------------------------	-------------

			$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_no in ($job_nos)";
				//echo $sql_precost_trim_cost_dtls;die;


			//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------

			$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
				select
				'', $approved_string_dtls, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_no in ($job_nos)";
			//---------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------

			$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_no in ($job_nos)";


			if($flag==1)
			{
				$rID3=execute_query($sql_precost_trim_cost_dtls,1);
				if($rID3) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID4=execute_query($sql_precost_trim_co_cons_dtl,1);
				if($rID4) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID5=execute_query($sql_precost_fab_con_cst_dtls,1);
				if($rID5) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID6=execute_query($sql_insert,0);
				if($rID6) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID7=execute_query($sql_precost_dtls,1);
				if($rID7) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID8=execute_query($sql_precost_fabric_cost_dtls,1);
				if($rID8) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID9=execute_query($sql_precost_fab_yarn_cst,1);
				if($rID9) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID10=execute_query($sql_precost_fcomarc_cost_dtls,1);
				if($rID10) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID11=execute_query($sql_precost_commis_cost_dtls,1);
				if($rID11) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID12=execute_query($sql_precost_embe_cost_dtls,1);
				if($rID12) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID13=execute_query($sql_precost_fab_yarnbkdown_his,1);
				if($rID13) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID14=execute_query($sql_precost_fab_sum_dtls,1);
				if($rID14) $flag=1; else $flag=0;
			}
		}


		//App Notification..............................................
		if($flag == 1 && $_SESSION['app_notification'] == 1)
		{
			$query="UPDATE APPROVAL_NOTIFICATION_ENGINE SET IS_APPROVED=1 WHERE ENTRY_FORM=77 and REF_ID in ($booking_ids)";
			$rID6=execute_query($query,1);
			if($rID6) $flag=1; else $flag=0; 

			if($flag == 1)
			{
				
				foreach($target_app_id_arr as $mst_id)
				{
					$brand_arr=return_library_array( "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0 and id=".$matchDataArr[$mst_id]['brand']."", "id", "brand_name"  );

					$desc = 'Company :'.$company_arr[$cbo_company_name].', Job :'.$approved_job_arr[$mst_id].', Buyer :'.$buyer_arr[$matchDataArr[$mst_id]['buyer']].', Brand :'.$brand_arr[$matchDataArr[$mst_id]['brand']];
					
					$noti_data_arr = array(
						'ID' => $mst_id,
						'DATE' => '',
						'DELIVERY_DATE' => '',
						'COMPANY' => $cbo_company_name,
						'BUYER' => $matchDataArr[$mst_id]['buyer'],
						'SYS_NUMBER' => $approved_job_arr[$mst_id],
						'SYS_DEF' => '',
						'DESC' => $desc,
						'MENU_ID' => return_field_value("page_id as menu_id","ELECTRONIC_APPROVAL_SETUP","entry_form=77","menu_id")
					);
					
					$approval_parameter = array('BUYER_ID' => $matchDataArr[$mst_id]['buyer'],'BRAND_ID' => $matchDataArr[$mst_id]['brand'],'approval_desc' => $desc,'approval_data' => $noti_data_arr,'title' =>'Pending Approval :: Precost Approval');


					$not_res = $notification->notificationEngine($mst_id,$cbo_company_name,77,$approval_parameter,$user_id_approval);
					if($flag == 1)
					{
						$appr_data = array("USER_ID"=>$user_id_approval,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $cbo_company_name,"NOTIFICATION_TYPE"=>0,'approval_desc'=>$desc,'approval_data'=>$noti_data_arr);
						$notification->pushAll($mst_id,77,$appr_data);
					}
				}
			}
			
			if(count($is_mst_final_seq) > 0)
            {
                $query = "DELETE APPROVAL_NOTIFICATION_ENGINE  WHERE entry_form = 77  and ref_id in (".implode(",",$is_mst_final_seq).") and user_id not in ( $user_id_approval ) "; 
                $rID7 = execute_query($query);
                if($rID7 == 1 && $flag == 1) $flag=1; else $flag=0; 
            }
		}
		//..............................................end;
        
		 //echo "10**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5.",".$rID6.",".$rID7.",".$rID8.",".$rID9.",".$rID10.",".$rID11.",".$rID12.",".$rID13.",".$rID14.",".$rID15.",".$rID16;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';
	}
	else if($approval_type==1)
	{

		$ahid=return_next_id( "id","approval_history", 1 ) ;

        foreach($target_app_id_arr as $key => $mst_id)
        {		
			$approved_no=$max_approved_no_arr[$mst_id]*1;

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",".$entry_form.",".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$refuse_cause_arr[$key]."')";
			$ahid++;
        }	
		
		

		$rID1=sql_multirow_update("wo_pre_cost_mst","approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'0*0*0*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}

	 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}

 		
		  //echo "10**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5;oci_rollback($con);die;

		if($flag == 1 && $_SESSION['app_notification'] == 1)
		{
			$query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=77 AND REF_ID IN ($booking_ids) ";
			$rID7=execute_query($query,1);
			if($rID7) $flag=1; else $flag=0; 
			if($flag == 1)
			{
				$reqs_ids=explode(",",$booking_ids);
				$appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '',"NOTIFICATION_TYPE"=>0);
				foreach($reqs_ids as $ref_id)
				{
					$notification->pushAll($ref_id,77,$appr_data);
				}
			}
		}
		
		$response=$booking_ids;
		if($flag==1) $msg='20'; else $msg='22';
	   
	}
	else if($approval_type==5)
	{
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;

        foreach($target_app_id_arr as $key => $mst_id)
        {		
			$approved_no=$max_approved_no_arr[$mst_id]*1;

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",".$entry_form.",".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2,'".$refuse_cause_arr[$key]."')";
			$ahid++;
        }
		

		$rID1=sql_multirow_update("wo_pre_cost_mst","approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'2*0*0*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		   // echo "10**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5;oci_rollback($con);die;

		if($flag == 1 && $_SESSION['app_notification'] == 1)
		{
			$query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=77 AND REF_ID IN ($booking_ids) ";
			$rID6=execute_query($query,1);
			if($rID6) $flag=1; else $flag=0; 
			if($flag == 1)
			{
				$reqs_ids=explode(",",$booking_ids);
				$appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '',"NOTIFICATION_TYPE"=>0);
				foreach($reqs_ids as $ref_id)
				{
					$notification->pushAll($ref_id,77,$appr_data);
				}
			}
		}

		$response=$booking_ids;
		if($flag==1) $msg='50'; else $msg='51';
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

if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	
	$sql_cause="select refusing_reason from refusing_cause_history where entry_form=15 and mst_id='$quo_id'";	
		
	$nameArray_cause=sql_select($sql_cause);
	$app_cause='';
	foreach($nameArray_cause as $row)
	{
		$app_cause.=$row[csf("refusing_reason")].",";
	}
	$app_cause=chop($app_cause,",");
	//print_r($app_cause);
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
			alert("Please save refusing cause first or empty");
			return;
		}
	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Refusing Cause')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id;
			http.open("POST","pre_costing_approval_controller.php",true);
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
				//document.getElementById('txt_refusing_cause').value =response[1];
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
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Refusing Cause</td>
					<td >
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?=$cause;?>" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
						if(!empty($app_cause))
						{
							echo load_submit_buttons( $permission, "fnc_cause_info", 1,0 ,"reset_form('causeinfo_1','','')",1);
						}
						else
						{
							echo load_submit_buttons($permission, "fnc_cause_info", 0,0,"reset_form('causeinfo_1','','','','','');",1);
						}
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>
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


if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";

                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    <!--<td align="center"><? echo $row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
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
		 	$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id and b.entry_form=77 order by b.sequence_no";
			//echo $sql;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>

	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}

if($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}

if ( $action=="app_mail_notification" )
{

	require('../../mailer/class.phpmailer.php');
	require('../../auto_mail/setting/mail_setting.php');

	list($sysId,$mailId,$txt_alter_user,$type)=explode('__',$data);
	$sysId=str_replace('*',',',$sysId);

	$txt_alter_user=str_replace("'","",$txt_alter_user);
	$user_id=($txt_alter_user!='')?$txt_alter_user:$user_id;

	$user_maill_arr = return_library_array( "select id,USER_EMAIL from  user_passwd where USER_EMAIL is not null",'id','USER_EMAIL');



	$sql="select a.ID,a.APPROVED,a.INSERTED_BY,b.JOB_NO,b.STYLE_REF_NO,b.COMPANY_NAME,b.BUYER_NAME from wo_pre_cost_mst a,wo_po_details_master b where a.JOB_NO=b.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($sysId)";  
	
	$sql_dtls=sql_select($sql);
	$dataArr=array();$insertUserMailToArr=array();
	foreach($sql_dtls as $rows){
		$dataArr['company'][$rows['COMPANY_NAME']]=$rows['COMPANY_NAME'];
		$dataArr['buyer_arr'][$rows['BUYER_NAME']]=$rows['BUYER_NAME'];
		$dataArr['data'][$rows['COMPANY_NAME']][$rows['ID']]=$rows;

		if($rows['APPROVED'] == 1 || $type == 5 || $type == 1){
			$insertUserMailToArr[$rows['INSERTED_BY']]=$user_maill_arr[$rows['INSERTED_BY']];
		}
		if($rows['APPROVED'] == 1){
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Approved following reference.<br />";
			$subjectForinsertBy="Pre-cost approved";
		}
		else if($type == 5){
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Deny following reference.<br />";
			$subjectForinsertBy="Pre-cost deny";
		}
		else if($type == 1){
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Unapproved following reference.<br />";
			$subjectForinsertBy="Pre-cost unapproved";
		}



	}
	

	foreach($dataArr['company'] as $company_name){
		
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
		
		
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where a.IS_DELETED=0 and a.entry_form = 15 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		 //echo $elcetronicSql;die;
		 $mailToArr=array();
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
			if($rows['BUYER_ID']!=''){
				foreach(explode(',',$rows['BUYER_ID']) as $bi){
					if($rows[USER_EMAIL]!='' && in_array($bi,$dataArr['buyer_arr']) ){$mailToArr[]=$rows[USER_EMAIL];}
				}
				if($rows['BYPASS']==2){break;}
			}
			else{
				if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
				if($rows['BYPASS']==2){break;}
			}
		}


			
		ob_start();	
			?>
			<table rules="all" border="1">
				<tr bgcolor="#CCCCCC">
					<td>SL</td>
					<td>Company</td>
					<td>Job No</td>
					<td>Style Ref</td>
					<td>Buyer</td>
				</tr>
				<?php 
				$i=1;
				foreach($dataArr[data][$company_name] as $row){ 
					
				?>
				<tr>
					<td><?=$i;?></td>
					<td><?=$company_arr[$company_name]?></td>
					<td><?=$row[JOB_NO]?></td>
					<td><?=$row[STYLE_REF_NO]?></td>
					<td><?=$buyer_arr[$row[BUYER_NAME]]?></td>
				</tr>
				<?php } ?>
			</table>
			<?	
				
				$message=ob_get_contents();
				ob_clean();


				$to=implode(',',$mailToArr);
				$insertUserMail = implode(',',$insertUserMailToArr);

				//echo $to;die;

				if($type == 2){
					$header=mailHeader();
					$greetingMsg = "Dear Concerned,	<br />Job Is Approved following reference.<br />";
					$subject="Pre-costing approval WVN";
					$body = $greetingMsg . $message;
					if($to!="") echo sendMailMailer( $to, $subject, $body, $from_mail);
				}

				if($insertUserMail != ''){
					$body = $greetingMsgForinsertBy . $message;
					echo sendMailMailer( $to, $subjectForinsertBy, $body, $from_mail);
				}

				

	}
	exit();
}


?>
