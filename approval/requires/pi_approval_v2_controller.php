<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
	
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$menu_id = $_SESSION['menu_id'];
$approva_status_arr=array(0=>"Un-Approved", 1=>"Approved", 3=>"Partial Approved");
$permissionSql = "SELECT approve_priv FROM user_priv_mst where user_id=$user_id AND main_menu_id = $menu_id";
$permissionCheck = sql_select( $permissionSql ); 
$approvePermission = $permissionCheck[0][csf('approve_priv')];
$piFor_array=array(1=>"BTB",2=>"Margin LC",3=>"Fund Buildup",4=>"TT/Pay Order",5=>"FTT",6=>"FDD");

//==========================================================================================
    $company_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
    $season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
    //$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
    //$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    //$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name");
    //$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group', 'id', 'item_name');
    //$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count', 'id', 'yarn_count');
    //$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$sys_id and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
    //$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
    //$user_lib_desg=return_library_array("SELECT id, designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,designation_lib from lib_designation", "id", "designation_lib");
    $item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME");
    $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');

$good_receive_data_source_arr = return_library_array( "select company_name, export_invoice_qty_source from variable_settings_commercial where variable_list=23 and status_active=1 and is_deleted=0",'company_name','export_invoice_qty_source');
if($good_receive_data_source_arr=="")
{
	$good_receive_data_source_arr=array();
}

if($action=="load_dropdown_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/pi_approval_v2_controller',this.value, 'load_dropdown_season', 'season_td' );load_drop_down( 'requires/pi_approval_v2_controller',this.value, 'load_dropdown_brand', 'brand_td' );" );  
    exit();
}


if ($action=="load_dropdown_season")
{
	echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}


if ($action=="load_dropdown_brand")
{
	echo create_drop_down( "cbo_brand_id", '100', "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=218 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="load_drop_down_buyer_new_user")
{
    $data=explode("_",$data);
    $log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1"); 
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

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 100,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]'  and c.status_active=1 and c.is_deleted=0 order by   c.supplier_name ",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type =9
	exit();
}    

if ($action=="load_supplier_dropdown_pi_new")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_pi_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	exit();
}



 
function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr']))); 
    
    $brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}



	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
       
        $rows['ITEM_ID']=($rows['ITEM_ID'] !='' ) ? $rows['ITEM_ID'] : $lib_item_cat_id_string;
		$rows['BUYER_ID']=($rows['BUYER_ID'] !='' ) ? $rows['BUYER_ID'] : $lib_buyer_id_string;

        if($rows['BRAND_ID']=='' || $rows['BRAND_ID']==0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	

	return $dataArr;
}

function getFinalUser($parameterArr=array()){

    $lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));

    $brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}   
    
    
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

        $rows['ITEM_ID']=($rows['ITEM_ID']!='')?$rows['ITEM_ID']:$lib_item_cat_id_string;
		$rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;
		//$rows['BRAND_ID']=($rows['BRAND_ID']!='')?$rows['BRAND_ID']:$lib_brand_id_string; 
        if($rows['BRAND_ID']=='' || $rows['BRAND_ID']==0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		} 


		
		$usersDataArr[$rows['USER_ID']]['ITEM_ID']=explode(',',$rows['ITEM_ID']);
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				in_array($bbtsRows['item'],$usersDataArr[$user_id]['ITEM_ID'])
				&& (in_array($bbtsRows['buyer'],$usersDataArr[$user_id]['BUYER_ID']) || $bbtsRows['buyer']==0)
				&& (in_array($bbtsRows['brand'],$usersDataArr[$user_id]['BRAND_ID']) || $bbtsRows['brand']==0)
			
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	//var_dump($finalSeq);
	//die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}



if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 

    $company_name = str_replace("'","",$cbo_company_name);
    $txt_date = str_replace("'","",$txt_date);      
    $txt_pi_no = str_replace("'","",$txt_pi_no);
    $txt_pi_sys_id_no = str_replace("'","",$txt_pi_sys_id_no);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $cbo_brand_id = str_replace("'","",$cbo_brand_id);
    $cbo_season_name = str_replace("'","",$cbo_season_name);
    $cbo_season_year = str_replace("'","",$cbo_season_year);
    $txt_style_ref = str_replace("'","",$txt_style_ref);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);   
    $approval_type = str_replace("'","",$cbo_approval_type);
    $cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;

    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");

    if ($txt_pi_no!="") $where_con = " and a.pi_number='$txt_pi_no'";	
    if ($txt_pi_sys_id_no!="") $where_con .= " and a.id='$txt_pi_sys_id_no'";	

	if($txt_date!="")
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if($cbo_get_upto==1){ $where_con.=" and a.pi_date>'".$txt_date."'";}
		else if($cbo_get_upto==2){$where_con.=" and a.pi_date<='".$txt_date."'";} 
		else if($cbo_get_upto==3){$where_con.=" and a.pi_date='".$txt_date."'";} 
		else{ $where_con .= " and a.pi_date='$txt_date'";}	
	}
    
    $year_field=" to_char(a.insert_date,'YYYY')";
   // $item_cateory_id_list = " listagg(b.item_category_id,',') within group (order by b.item_category_id)";	


	$electronicDataArr=getSequence(array('company_id'=>$company_name,'entry_form'=>27,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'lib_department_id_arr'=>0));

    //print_r($electronicDataArr);die;

    if($approval_type==0) // Un-Approve
	{
		//echo $electronicDataArr['user_by'][$app_user_id]['ITEM_ID'];die;
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['ITEM_ID']){
			$where_con .= " and b.ITEM_CATEGORY_ID in(".$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['ITEM_ID']=$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'];
		}
        if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}

        if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
			$where_con .= " and a.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
		}
		 
			
          $data_mas_sql="SELECT a.ID, a.IMPORTER_ID, a.ITEM_CATEGORY_ID,a.BUYER_ID,a.BRAND_ID from com_pi_master_details a, com_pi_item_details b 
            where a.id=b.pi_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved<>1 and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $where_con group by a.ID, a.IMPORTER_ID, a.ITEM_CATEGORY_ID,a.BUYER_ID,a.BRAND_ID";     
		     //echo $data_mas_sql;die;

			$tmp_sys_id_arr=array();
			$data_mas_sql_res=sql_select( $data_mas_sql );
			foreach ($data_mas_sql_res as $row)
			{ 
				for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
					if( (in_array($row['ITEM_CATEGORY_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['ITEM_ID']))) 
                        && (in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0)
                        && (in_array($row['BRAND_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID']==0)
                    
                    )
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
 
		 
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			
			if($tmp_sys_id_arr[$seq]){
			   if($sql!=''){$sql .=" UNION ALL ";}
               $sql .= "SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.export_pi_id,to_char(a.insert_date,'YYYY') as YEAR, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, $year_field as year,a.within_group,a.import_pi from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved<>1 and a.APPROVED_SEQU_BY=$seq and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sys_con group by a.id, a.item_category_id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.export_pi_id,to_char(a.insert_date,'YYYY'), a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, b.item_category_id, a.net_total_amount, $year_field,a.within_group,a.import_pi order by a.id desc";
                
			}
		}

        $sql = "select x.id, x.importer_id, x.source, x.supplier_id, x.pi_number, x.pi_date, x.export_pi_id, x.last_shipment_date, x.internal_file_no, x.approved, x.remarks, x.net_pi_amount, x.net_total_amount, x.is_apply_last_update, x.year,x.within_group,x.import_pi from($sql)x order by x.id desc";
	}
	else
	{
        $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.export_pi_id, to_char(a.insert_date,'YYYY') as YEAR, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, $year_field as year,a.within_group,a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, APPROVAL_MST c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.approved in (1,3)  and c.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}  $where_con GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.export_pi_id, to_char(a.insert_date,'YYYY'), a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update,  a.net_total_amount, $year_field,a.within_group,a.import_pi order by a.id desc";
    }
     //echo $sql;die;

    $nameArray = sql_select( $sql );
    $pi_id_arr=array();
    foreach ($nameArray as $row) {
        $pi_id_arr[$row['ID']]=$row['ID'];
    }
    $pi_Ids = implode(",", $pi_id_arr);

    if (count($pi_id_arr)){
		$flag=0;
		if($cbo_buyer_name){$whereCon.=" and e.buyer_name =$cbo_buyer_name ";$flag=1;}
		if($cbo_season_name>0){$whereCon.=" and e.SEASON_BUYER_WISE =$cbo_season_name";$flag=1;}
		if($cbo_season_year>0){$whereCon.=" and e.SEASON_YEAR =$cbo_season_year";$flag=1;}
		if($cbo_brand_id>0){$whereCon.=" and e.BRAND_ID=$cbo_brand_id ";$flag=1;}
		if($txt_internal_ref!=''){$whereCon.=" and f.FILE_NO ='$txt_internal_ref'";$flag=1;}
 
 	 
		///$pi_mst_id_cond
		$sql_buyer_marchent ="SELECT e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO, b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 1 as type
        from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
        where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and d.job_no=f.job_no_mst and e.company_name = $company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id 
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 2 as type
        from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
        where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and d.job_no=f.job_no_mst and c.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 3 as type
        from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
        where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 4 as type
        from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
        where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond  $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 5 as type 
        from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
        where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst  and c.job_no=f.job_no_mst and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id 
        union all
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 6 as type from com_pi_master_details a,com_pi_item_details b, wo_labtest_dtls c ,wo_po_details_master e,wo_po_break_down f where a.id=b.pi_id and a.id in($pi_Ids) $date_cond $pi_sys_no_cond $pi_no_cond  $category_idCond and b.item_category_id=31  and b.WORK_ORDER_ID=c.MST_ID and c.job_no =e.job_no and e.job_no=f.job_no_mst and c.job_no=f.job_no_mst $whereCon group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, f.id, e.job_no        
        ";
        //echo $sql_buyer_marchent;die;
        $sql_job=sql_select($sql_buyer_marchent); // and a.pi_id=6201

        $buyer_marchant_arr=array();
        $pi_id_arr=array();
        $all_job_arr=array();
        $order_id_arr=array();
        foreach($sql_job as $row)
        {
            
            if($buyer_marchant_arr[$row[csf("pi_id")]][$row[csf("buyer_name")]]=="")
            {
                $buyer_marchant_arr[$row[csf("pi_id")]][$row[csf("buyer_name")]]=$row[csf("buyer_name")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["buyer_name"].=$row[csf("buyer_name")].',';
            }
            if($buyer_marchant_arr[$row[csf("pi_id")]][$row[csf("dealing_marchant")]]=="")
            {
                $buyer_marchant_arr[$row[csf("pi_id")]][$row[csf("dealing_marchant")]]=$row[csf("dealing_marchant")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["dealing_marchant"].=$row[csf("dealing_marchant")].',';
            }
            
                $buyer_marchant_arr[$row[csf("pi_id")]]["STYLE_REF_NO"][$row[csf("job_no")]]=$row[csf("STYLE_REF_NO")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["job"][$row[csf("job_no")]]=$row[csf("job_no")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["SEASON_BUYER_WISE"][$row[csf("SEASON_BUYER_WISE")]]=$season_arr[$row[csf("SEASON_BUYER_WISE")]];
                $buyer_marchant_arr[$row[csf("pi_id")]]["SEASON_YEAR"][$row[csf("SEASON_YEAR")]]=$row[csf("SEASON_YEAR")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["BRAND_NAME"][$row[csf("BRAND_ID")]]=$brand_arr[$row[csf("BRAND_ID")]];
			 	$buyer_marchant_arr[$row[csf("pi_id")]]["FILE_NO"][$row[csf("FILE_NO")]]=$row[csf("FILE_NO")];
			
				$pi_id_arr[$row[csf("order_id")]]=$row[csf("pi_id")];         
				$piArr[$row[csf("pi_id")]]=$row[csf("pi_id")];         
                $order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
			    $all_job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			
        }
        unset($sql_job);
        // Buyer and dealing_marchant info END
        
  
        $order_id_cond=where_con_using_array($order_id_arr,0,'a.wo_po_break_down_id');

        //echo $order_id_cond;
        $sql_lcSc="select a.wo_po_break_down_id as order_id, max(b.last_shipment_date) as last_shipment_date, b.bank_file_no, 1 as type from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.status_active=1 
        $order_id_cond
        group by a.wo_po_break_down_id
        union all
        select a.wo_po_break_down_id as order_id, max(b.last_shipment_date) as last_shipment_date, b.bank_file_no, 2 as type from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 $order_id_cond
        group by a.wo_po_break_down_id";
        
        $sql_lcSc_res=sql_select($sql_lcSc);
        $lcsc_ship_date_arr=array();
        foreach ($sql_lcSc_res as $row) {
            $lcsc_ship_date_arr[$pi_id_arr[$row[csf("order_id")]]]['last_shipment_date']=$row[csf("last_shipment_date")];
            if ($row[csf("bank_file_no")] != ''){
                $lcsc_ship_date_arr[$pi_id_arr[$row[csf("order_id")]]]['bank_file_no'].=$row[csf("bank_file_no")].',';
            }        
        }
        unset($sql_lcSc_res);

        $sql_picategory=sql_select("select b.item_category_id, b.pi_id from com_pi_item_details b where 1=1 ".where_con_using_array(explode(',',$pi_Ids),0,'b.pi_id')."");
        $pi_category_array=array();
        foreach ($sql_picategory as $row) {
            $pi_category_array[$row[csf('pi_id')]]=$row[csf('item_category_id')];
        }
    } 
 	
    $precost_app_arr = return_library_array("select a.APPROVED,a.JOB_NO from WO_PRE_COST_MST a where a.IS_DELETED=0 ".where_con_using_array($all_job_arr,1,'a.job_no')."", 'JOB_NO', 'APPROVED');
	
    $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =183 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
 
    $width=1750;
	?>

    <script>
        function open_print_btn_popup(data){
            var title = 'Show Print Options';
            var page_link = 'requires/pi_approval_v2_controller.php?action=print_button_variable&print_data='+data;
            emailwindow=dhtmlmodal.open('ShowPrint', 'iframe', page_link, title, 'width=650px,height=100px,center=1,resize=1,scrolling=0','');
            emailwindow.onclose=function()
            {
                
            }
        }
    </script>
    <form name="piApproval_2" id="piApproval_2">
        <fieldset style="width:<? echo $width; ?>px; margin-top:10px">
            <legend>PI Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
                <thead>
                	<th width="25"></th>
                    <th width="30">SL</th>
                    <th width="60">System Id</th>
                    <th width="80">PI No</th>
                    <th width="60">Image/ File</th>
                    <th width="70">PI Date</th>
                    <th width="70">Year</th>
                    <th width="90">Item Category</th>
                    <th width="90">PI Amount</th>
                    <th width="100">PI Net Amount</th>
                    <th width="80">Source</th>
                    <th width="100">Supplier</th>
                    <th width="90">Buyer Name</th>
                    <th width="80">Brand</th>
                    <th width="120">Style Ref.</th>
                    <th width="100">Dealing Merchandiser</th>
                    <th width="80">Approval Status</th>
                    <th width="100">Remarks</th>
                    <th width="100">Refusing cause</th>
                    <th width="60">Cross Check</th>
                   
                </thead>
            </table>            
            <div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="pi_approve_unapprove_list_view" >
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                            $i = 1; $all_approval_id = '';
                            $j=0;
                           $img_val =  return_field_value("master_tble_id","common_photo_library","form_name='proforma_invoice'","master_tble_id");//master_tble_id='$value' and 
                            foreach ($nameArray as $row)
                            {
								if($flag==1 && $piArr[$row[csf('id')]]==''){continue;}
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																
								if($row[csf('approval_id')] == 0) $print_cond = 1;
								else
								{
									if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=="")
									{
										$duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=$row[csf('id')];
										$print_cond=1;
									}
									else
									{
										if($all_approval_id == "") $all_approval_id = $row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
										$print_cond=0;
									}
								}
                                
                                //===========================
                                $row[csf('item_category_id')]=$pi_category_array[$row[csf('id')]];									

                                if( $row[csf('item_category_id')] == 1) $entry_form = "165";
                                else if( $row[csf('item_category_id')] == "2" ||  $row[csf('item_category_id')] == "3" ||  $row[csf('item_category_id')] == "13" ||  $row[csf('item_category_id')] == "14")
                                {
                                    $entry_form = "166";
                                }
                                else if( $row[csf('item_category_id')] == 4) $entry_form = "167";
                                else if( $row[csf('item_category_id')] == "12") $entry_form = "168";
                                else if( $row[csf('item_category_id')] == "24") $entry_form = "169";
                                else if( $row[csf('item_category_id')] == "25") $entry_form = "170";
                                else if( $row[csf('item_category_id')] == "30") $entry_form = "197";
                                else if( $row[csf('item_category_id')] == "31") $entry_form = "171";
                                else if( $row[csf('item_category_id')] == "5" ||  $row[csf('item_category_id')] == "6" ||  $row[csf('item_category_id')] == "7" ||  $row[csf('item_category_id')] == "23")
                                {
                                    $entry_form = "227";
                                }
                                else $entry_form = "172";
                               // print_r($format_ids[$j]);exit;
                              

                                //=================================================

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<?=$row['ID']; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row['PI_NUMBER']; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('pi_number')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    </td> 
                                    <td width="30" align="center"><? echo $i;?></td>
                                    <td width="60" align="center"><p><a href="javascript:openPopup(<? echo $row[csf('id')];?>)"><? echo $row[csf('id')];?></a></p></td>
                                    <td width="80" align="center" ><p><a href="javascript:open_print_btn_popup('<?= $row[csf('importer_id')]."*".$row[csf('id')]."*". $entry_form."*".$row['ITEM_CATEGORY_ID']."*".$row['EXPORT_PI_ID'];?>')"> <?= $row['PI_NUMBER'];?></a></p> </td>
                                    <td width="60" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $row[csf('importer_id')]; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>
                                    <td width="70" align="center"><p><?=change_date_format($row[csf('pi_date')]); ?></p></td>
                                    <td width="70" align="center"><p><?=$row[csf('year')];?></p></td>
                                    <td width="90">
										<? 
                                        $item_ids="";
                                        $item_ids .= $item_category[$pi_category_array[$row[csf('id')]]].",";
                                        echo chop($item_ids,","); 
                                        ?>
                                    </td>
                                    <td width="90" align="right"><p><? echo $row[csf('net_pi_amount')]; ?></p></td>
                                    <td width="100" align="right"><p><? echo $row[csf('net_total_amount')]; ?></p></td>
                                    <td width="80" align="center" style="word-break:break-all"><? echo $source[$row[csf('source')]]; ?></td>
                                    <td width="100">
                                        <? 
                                            if($row[csf('import_pi')]==1 && $row[csf('within_group')]==1)
                                            {
                                                echo $company_arr[$row[csf('supplier_id')]];
                                            }
                                            else
                                            {
                                                echo $supplier_arr[$row[csf('supplier_id')]];
                                            }
                                        ?>
                                    </td>
                                    <td width="90">
                                        <?
                                        $buyer_id=chop($buyer_marchant_arr[$row[csf("id")]]['buyer_name'],',');
                                        $buyer_name=array_unique(explode(',', $buyer_id));
                                        $comma_separate_buyer="";
                                        foreach ($buyer_name as $key => $val) 
                                        {
                                            if ($comma_separate_buyer=="") $comma_separate_buyer.=$buyer_arr[$val];
                                            else $comma_separate_buyer.=','.$buyer_arr[$val];
                                        }
                                        echo $comma_separate_buyer; ?>
                                    </td>
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["BRAND_NAME"]);?></p></td>
                                    <td width="120"><p>
										<?
										foreach($buyer_marchant_arr[$row[csf("id")]]["STYLE_REF_NO"] as $jobNo=>$style){
											?>
											<a href="javascript:generate_report('<?=$jobNo;?>')"><?=$style;?></a>
                                            <?
										}
                                        ?>
                                    </p>
                                    </td>
                                    <td width="100" style="word-break:break-all;">
                                        <?
                                        $marchant_id=chop($buyer_marchant_arr[$row[csf("id")]]['dealing_marchant'],',');
                                        $dealing_marchant=array_unique(explode(',', $marchant_id));
                                        $comma_separate_marchant="";
                                        foreach ($dealing_marchant as $key => $val) 
                                        {
                                            if ($comma_separate_marchant=="") $comma_separate_marchant.=$dealing_merchant_arr[$val];
                                            else $comma_separate_marchant.=','.$dealing_merchant_arr[$val];
                                        }
                                        echo $comma_separate_marchant; ?>
                                    </td>
                                    <td width="80"><p><?= $approva_status_arr[$row[csf("approved")]];?></p></td>
                                    <td width="100" align="center"><p><?=$row[csf('remarks')];?></p></td>
                                    <td width="100" align="center">
                                        <input name="txt_unappv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_unappv_cause_<?=$i;?>" style="width:90px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?= $row['ID']; ?>,<?=$approval_type; ?>,<?=$i; ?>,'2',<?= $app_user_id;?>)">
                                      
                                    </td>                                                                    
                                    <td width="60" align="center"><a href="javascript:void();" onClick="fnc_pi_cross_check('<?=$row[csf('id')]; ?>','<?=$row[csf('importer_id')]; ?>');">View</a> </td>                                                                                                          
                                </tr>
                                <Input name='txt_appv_instra[]' class='text_boxes' placeholder='Please Browse' ID='txt_appv_instra_"<?=$i;?>"'  type='hidden'>
                                <?
                                $i++;
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID = sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							}
							
							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
							$isApp="";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="25" align="center" style=" <?=$isApp; ?>">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>,<?=$approvePermission; ?>)"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form> 
    <style>
		.yes{background-color:#090; color:#FFFFFF; font-weight:bold;}
		.no{background-color:#FF0000; color:#FFFFFF;font-weight:bold;}
	</style>        
    <?
	exit();	
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$con = connect();

    $company_name = str_replace("'","",$cbo_company_name);
    $txt_date = str_replace("'","",$txt_date);      
    $txt_pi_no = str_replace("'","",$txt_pi_no);
    $txt_pi_sys_id_no = str_replace("'","",$txt_pi_sys_id_no);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $cbo_brand_id = str_replace("'","",$cbo_brand_id);
    $cbo_season_name = str_replace("'","",$cbo_season_name);
    $cbo_season_year = str_replace("'","",$cbo_season_year);
    $txt_style_ref = str_replace("'","",$txt_style_ref);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);   
    $approval_type = str_replace("'","",$approval_type);
    $cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $booking_ids = str_replace("'","",$booking_ids);

    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;


   		
    $sql = "SELECT a.ID, a.IMPORTER_ID, a.ITEM_CATEGORY_ID,a.BUYER_ID,a.BRAND_ID from com_pi_master_details a  where a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1  and a.id in($booking_ids)";   
   // echo $sql;die;

    $sqlResult = sql_select( $sql );
    foreach ($sqlResult as $row)
    {
        $matchDataArr[$row['ID']]=array('buyer'=>$row['BUYER_ID'],'brand'=>$row['BRAND_ID'],'item'=>$row['ITEM_CATEGORY_ID'],'store'=>0,'department'=>0);
    } 
   
    $finalDataArr=getFinalUser(array('company_id'=>$company_name,'entry_form'=>27,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'lib_department_id_arr'=>0,'match_data'=>$matchDataArr));

    //print_r($finalDataArr);die;

		
    $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
    $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];


    if($approval_type == 0)
	{

		$target_app_id_arr = explode(",",$booking_ids);

        $approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history and entry_form=27 and mst_id in($booking_ids)", "mst_id", "approved_no"  );
        //print_r($approved_no_arr);die;
        

        $id = return_next_id( "id","approval_history", 1) ;
        $app_mst_id=return_next_id( "id","approval_mst", 1 ) ;
        $data_array="";$mst_data_array="";
		foreach($target_app_id_arr as $mst_id)
		{
			$approved_no=$approved_no_arr[$mst_id]+1;
            $approved_no_array[$mst_id] = $approved_no;
            //History......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",27,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."','".$user_ip."','".$app_instru."',".$user_id.",'".$pc_date_time."')"; 
			$id++;	
            //App mst...............................
            if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$app_mst_id.",27,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$app_mst_id++;
            
            //Mst data...........................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$app_user_id."")); 


		}



        if(count($approved_no_array)>0)
        {
            $approved_string="";
            foreach($approved_no_array as $key=>$value)
            {
                $approved_string.=" WHEN $key THEN $value";
            }
            
            $approved_string_mst = "CASE id ".str_replace("'",'',$approved_string)." END";
            $approved_string_dtls = "CASE pi_id ".str_replace("'",'',$approved_string)." END"; 
        
        

            $sql_insert_his_mst="insert into com_pi_master_details_history(id,approved_no, mst_id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, goods_rcv_status, import_pi,export_pi_id, within_group, ready_to_approved,is_apply_last_update, lc_group_no, entry_form,approval_user,requested_by,ref_closing_status,version, after_goods_source, upcharge_breakdown,beneficiary,t_inserted_by, t_insert_date, t_status_active, t_is_deleted)
            
            select '', $approved_string_mst as approved_no, id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by,approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,goods_rcv_status, import_pi,export_pi_id, within_group,ready_to_approved,is_apply_last_update, lc_group_no, entry_form,approval_user, requested_by, ref_closing_status,version, after_goods_source, upcharge_breakdown,beneficiary,".$_SESSION['logic_erp']['user_id']. ",'" . $pc_date_time ."',1,0 from com_pi_master_details where id in ($booking_ids)";

           
            $sql_insert_his_dtls="insert into com_pi_item_details_history(id, approved_no,dtls_id,pi_id,work_order_no, work_order_id, 
            work_order_dtls_id, determination_id, item_prod_id,item_group, color_id, item_color,size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2,yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width,weight, uom,quantity,rate, amount, net_pi_rate,net_pi_amount, service_type, brand_supplier,inserted_by, insert_date, updated_by,update_date, status_active, is_deleted,item_description, gmts_item_id, embell_name,embell_type, lot_no, yarn_color,color_range, booking_without_order, country_id,staple_length, bale, bale_kg,test_for, test_item_id, remarks,fabric_source, item_category_id, entry_form,wo_qty_dtls_id, order_id, order_source,after_goods_source, is_sales, hs_code,booking_no, body_part_id,t_inserted_by, t_insert_date, t_status_active, t_is_deleted)
            
            select '', $approved_string_dtls as approved_dls,id,pi_id,work_order_no, work_order_id, 
            work_order_dtls_id, determination_id, item_prod_id,item_group, color_id, item_color,size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2,yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width,weight, uom,quantity,rate, amount, net_pi_rate,net_pi_amount, service_type, brand_supplier,inserted_by, insert_date, updated_by,update_date, status_active, is_deleted,item_description, gmts_item_id, embell_name,embell_type, lot_no, yarn_color,color_range, booking_without_order, country_id,staple_length, bale, bale_kg,test_for, test_item_id, remarks,fabric_source, item_category_id, entry_form,wo_qty_dtls_id, order_id, order_source,after_goods_source, is_sales, hs_code,booking_no, body_part_id,".$_SESSION['logic_erp']['user_id']. ",'" . $pc_date_time ."',1,0 from com_pi_item_details where pi_id in ($booking_ids)";

        }


        $flag=1;

		if($flag==1) 
		{
			$mst_field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,inserted_by,insert_date,user_ip";
            $rID1=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
            if($rID1) $flag=1; else $flag=0; 
		}

        if($flag==1) 
        {
            $rID2=execute_query("UPDATE approval_history SET current_approval_status=0 WHERE entry_form=27 and mst_id in ($booking_ids)",1);
            if($rID2) $flag=1; else $flag=0; 
        }

        if($flag==1) 
        {
            $field_array = "id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
            $rID3=sql_insert("approval_history",$field_array,$data_array,0);
            if($rID3) $flag=1; else $flag=0; 
            
        }

		if($flag==1) 
		{
			$mst_field_array_up="approved*approved_sequ_by*APPROVED_DATE*APPROVED_BY"; 
            $rID4=execute_query(bulk_update_sql_statement( "com_pi_master_details", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
            if($rID4) $flag=1; else $flag=0; 
		}

       
            
        if($flag==1) 
		{
            $rID5=execute_query($sql_insert_his_mst,0); 
            if($rID5) $flag=1; else $flag=0; 
        }

        if($flag==1) 
		{
            $rID6=execute_query($sql_insert_his_dtls,0); 
            if($rID6) $flag=1; else $flag=0; 
        }
    

       // echo  "0**21,".$rID1.','.$rID2.','.$rID3.','.$sql_insert_his_mst.','.$rID5.','.$rID6;oci_rollback($con);die;

        if($flag){$appMSG = 19;}else{$appMSG = 21;}

    }
    elseif($approval_type == 1){

  
        $history_data_arr=return_library_array( "select id, id from approval_history where current_approval_status=1 and entry_form=27 and mst_id in ($booking_ids)",'id','id');
		$app_ids= implode(',',$history_data_arr);
		
		$flag=1;
		
		if($flag==1) 
		{
			$rID1=sql_multirow_update("com_pi_master_details","approved*ready_to_approved*APPROVED_SEQU_BY","0*0*0","id",$booking_ids,0); 
            if($rID1) $flag=1; else $flag=0; 
		}

 
		
		if($flag==1) 
		{
			$rID2=execute_query("delete from approval_mst  WHERE entry_form=27 and mst_id in ($booking_ids)",1); 
            if($rID2) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1) 
		{
			$rID3=execute_query("UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=27 and mst_id in ($booking_ids)",1);
            if($rID3) $flag=1; else $flag=0; 
		} 
			

		if($flag==1) 
		{
            $data=$app_user_id."*'".$pc_date_time."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID4=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
            if($rID4) $flag=1; else $flag=0; 
		} 

        //echo  "0**21,".$rID1.','.$rID2.','.$rID3.','.$rID4;oci_rollback($con);die;

       if($flag){$appMSG = 20;}else{$appMSG = 22;}

    }
    else if($approval_type == 5){
        $history_data_arr=return_library_array( "select id, id from approval_history where current_approval_status=1 and entry_form=27 and mst_id in ($booking_ids)",'id','id');
		$app_ids= implode(',',$history_data_arr);
		
		$flag=1;
		
		if($flag==1) 
		{
			$rID1=sql_multirow_update("com_pi_master_details","approved*ready_to_approved*APPROVED_SEQU_BY","2*0*0","id",$booking_ids,0); 
            if($rID1) $flag=1; else $flag=0; 
		}

 
		
		if($flag==1) 
		{
			$rID2=execute_query("delete from approval_mst  WHERE entry_form=27 and mst_id in ($booking_ids)",1); 
            if($rID2) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1) 
		{
			$rID3=execute_query("UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=27 and mst_id in ($booking_ids)",1);
            if($rID3) $flag=1; else $flag=0; 
		} 
			

        if($flag==1)
        {
            $query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$app_user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=27 and current_approval_status=1 and mst_id in ($booking_ids)";
            $rID4=execute_query($query,1);
            if($rID4) $flag=1; else $flag=0;
        }
        //echo  "0**21,".$rID1.','.$rID2.','.$rID3.','.$rID4;oci_rollback($con);die;
        if($flag){$appMSG = 50;}else{$appMSG = 0;}
    
    }

 

    if($flag==1)
    {
        oci_commit($con);
        echo "$appMSG**$booking_ids";
    }
    else
    {
        oci_rollback($con);
        echo "$appMSG**$booking_ids";
    }
    disconnect($con);
    die;


}






if($action=="get_print_button_data")
{
	
	$sql="select a.COSTING_DATE,a.APPROVED,a.JOB_NO,a.JOB_ID,a.COSTING_PER,b.BUYER_NAME,b.COMPANY_NAME,b.STYLE_REF_NO from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id=a.job_id and a.job_no='$data'";
    $sqlRes = sql_select($sql); 
    foreach($sqlRes as $row)
    {
		echo "action=preCostRpt2&zero_value=1&rate_amt=2&&txt_job_no='{$row[JOB_NO]}'&cbo_company_name='{$row[COMPANY_NAME]}'&cbo_buyer_name='{$row[BUYER_NAME]}'&txt_style_ref='{$row[STYLE_REF_NO]}'&txt_costing_date='{$row[COSTING_DATE]}'&txt_po_breack_down_id=''&cbo_costing_per='{$row[COSTING_PER]}'";

    }
	exit();	
}




if ($action=="appcause_popup")
{
    echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
    extract($_REQUEST);
    list($wo_id,$app_type,$app_cause,$approval_id,$app_from,$user_id)=explode('_',$data);

    if($app_cause=="")
    {
        $sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=27 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
       // echo $sql_cause; die;
        $nameArray_cause=sql_select($sql_cause);
        if(count($nameArray_cause)>0)
		{
			if($app_from==1)
			{
				foreach($nameArray_cause as $row)
				{
					$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
					$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
				}
			}
			else
			{
				foreach($nameArray_cause as $row)
				{
					$app_cause1=return_field_value("not_approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
					$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
				}
			}
        }
        else
        {
            $app_cause = '';
        }
    }
	//echo $app_cause.test;die;
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

                var data="action=save_update_delete_appv_cause&operation="+operation+"&user_id="+<?=$user_id;?>+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id*app_from',"../../");
                //alert (data);return;
                freeze_window(operation);
                http.open("POST","pi_approval_v2_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange=fnc_appv_entry_Reply_info;
            }
        }

        function fnc_appv_entry_Reply_info()
        {
            if(http.readyState == 4)
            {

                var reponse=trim(http.responseText).split('**');
                show_msg(reponse[0]);
                set_button_status(1, permission, 'fnc_appv_entry',1);
                release_freezing();
                fnc_close();

               // generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
                
            }
        }

        function fnc_close()
        {
            appv_cause= $("#appv_cause").val();

            document.getElementById('hidden_appv_cause').value=appv_cause;

            parent.emailwindow.hide();
        }

       /* function generate_worder_mail(woid,mail,appvtype,user)
        {
            var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
            //alert (data);return;
            freeze_window(6);
            http.open("POST","pi_approval_v2_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange=fnc_appv_mail_Reply_info;

        }*/

        /*function fnc_appv_mail_Reply_info()
        {
            if(http.readyState == 4)
            {
                var response=trim(http.responseText).split('**');
                /*if(response[0]==222)
                {
                    show_msg(reponse[0]);
                }*/
                //release_freezing();
            //}
        //}

    </script>
    <body>
        <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
            <fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                        <textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $app_cause; ?></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                        <input type="hidden" name="app_from" class="text_boxes" ID="app_from" value="<? echo $app_from; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
                        //print_r ($id_up_all);
                            if($app_cause!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
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
            $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id  and a.id!=$user_id  and b.is_deleted=0 order by SEQUENCE_NO";
                //echo $sql;die;
             $arr=array (2=>$designation_lib,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
            
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
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
		$app_from=str_replace("'","",$app_from);
		
		if($app_from==1)
		{
			$approval_cause_field="approval_cause";
		}
		else
		{
			$approval_cause_field="not_approval_cause";
		}
		
		//echo "10**".$appv_cause."==".$not_appv_cause;die;
		
        if ($operation==0 || $operation==1)  // Insert Here
        {
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }
			
            $approved_no_history=return_field_value("approved_no","approval_history","entry_form=27 and mst_id=$wo_id and approved_by=$user_id");
            //echo "10**$approved_no_history";die;
            $approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
            //echo "10**$approved_no_cause";die;

            if($approved_no_history=="" && $approved_no_cause=="")
            {
                //echo "insert"; die;
				
                $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
				//if($app_from==1)
                $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                $data_array="(".$id_mst.",".$page_id.",27,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo "10**insert into fabric_booking_approval_cause ($field_array) values $data_array";die;
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
            else if($approved_no_history=="" && $approved_no_cause!="")
            {
                $con = connect();
                if($db_type==0)
                {
                    mysql_query("BEGIN");
                }

                $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                $data_array="".$page_id."*27*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
                 // echo "10**insert into fabric_booking_approval_cause ($field_array) values $data_array";die;
                 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
                //  echo "10**1803==>".$rID;die;
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
                $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=27 and mst_id=$wo_id and approved_by=$user_id");
                $max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                if($max_appv_no_his!=$max_appv_no_cause)
                {
                    $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

                    $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                    $data_array="(".$id_mst.",".$page_id.",27,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

                    $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                    $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                    $data_array="".$page_id."*27*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
                $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=27 and mst_id=$wo_id and approved_by=$user_id");
                $max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                if($max_appv_no_his!=$max_appv_no_cause)
                {
                    $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

                    $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                    $data_array="(".$id_mst.",".$page_id.",27,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

                    $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=27 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                    $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                    $data_array="".$page_id."*27*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
		if($app_from==1)
		{
			$approval_cause_field="approval_cause";
		}

        if ($operation==0 || $operation==1)  // Insert Here
        {  
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }

            $unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=27 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

            //echo "10**$unapproved_cause_id";die;
            $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=27 and mst_id=$wo_id and approved_by=$user_id");
           
            if($unapproved_cause_id=="")
            {  
                $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
                $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                $data_array="(".$id_mst.",".$page_id.",27,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

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
                //echo "10**entry_form=27 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
                $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=27 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

                $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                $data_array="".$page_id."*27*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

                 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
                // echo $rID; die;

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


function auto_approved($dataArr=array()){
    global $pc_date_time;
    global $user_id;
    $sys_id_arr=explode(',',$dataArr[sys_id]);
    
    $queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
    $queryTextRes = sql_select($queryText);
    
    if($queryTextRes[0][ALLOW_PARTIAL]==1){
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
 

if($action=="check_booking_last_update")
{
	$last_update = return_field_value("is_apply_last_update","com_pi_master_details","id='".trim($data)."'");
	echo $last_update;
	exit();	
}




if($action=="get_user_pi_file")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
  
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='proforma_invoice' and master_tble_id='$id'";
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        if($img[FILE_TYPE]==1){
            echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;">
            <a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'">
                <img src="../../' . $img[csf("image_location")] . '" width="89px" height="97px">
            </a><br>' . $img[csf("real_file_name")] . '
          </p>';
		}
        else{
            echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;">
            <a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'">
                <img src="../../' . $img[csf("image_location")] . '" width="89px" height="97px">
            </a><br>' . $img[csf("real_file_name")] . '
          </p>'; 
        }
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
              ob_end_clean();
            header("Content-Type: {$mime}");
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

if ($action == "file_no_popup") 
{
    echo load_html_head_contents("File Details", "../../", 1, 1,'','','');
    extract($_REQUEST);
    // $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $lien_bank_arr=return_library_array( "select id, (bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name", "id", "bank_name"  );

    if($pi_id != "")
    {
        $sql="select b.LC_YEAR as LC_SC_YEAR, b.export_lc_no as LC_SC_NO, b.buyer_name as BUYER_NAME, b.lien_bank as LIEN_BANK from com_pi_master_details a, com_export_lc b where a.lc_sc_id=b.id and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$pi_id and a.importer_id=$company_id and a.lc_sc_id is not null
        union all
        select b.sc_year as LC_SC_YEAR, b.contract_no as LC_SC_NO, b.buyer_name as BUYER_NAME, b.lien_bank as LIEN_BANK from com_pi_master_details a, com_sales_contract b where a.lc_sc_id=b.id and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$pi_id and a.importer_id=$company_id and a.lc_sc_id is not null";
    }
    $sql_res=sql_select( $sql);
   

    ?>
    </head>
    <body>
        <div align="center" style="width:490px;" >
            <form name="crosscheckform" id="crosscheckform"  autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="450" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="50">Year</th>
                        <th width="120">Buyer</th>
                        <th width="120">Lien Bank</th>
                        <th>SC/LC No</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
                    foreach ($sql_res as $row) 
                    {
                        if($i%2==0) $bg_color = "#E9F3FF";
                        else $bg_color = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bg_color;?>" onClick="set_checkbox_value(<? echo $i;?>)" style="cursor:pointer">
                            <td align="center" width="30"><? echo $i; ?></td>
                            <td align="center" width="50"><p><? echo $row['LC_SC_YEAR']; ?></p></td>
                            <td width="120"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="120"><p><? echo $lien_bank_arr[$row['LIEN_BANK']]; ?></p></td>
                            <td><p><? echo $row['LC_SC_NO']; ?></p></td>            
                        </tr>
                        <?
                        $i++;
                    }
                    ?>                
                </table>           
            </form>
        </div>
    </body>
    </html>
    <?
    exit();
}

if ($action == "cross_check_popup") {
    echo load_html_head_contents("Cross Check Details", "../../", 1, 1,'','','');
    extract($_REQUEST);

    $pi_cross_check_array = array(1=>"Yarn Price checked", 2=>"Trims Price Checked", 3=>"Consumption Checked", 4=>"Pilling Test", 5=>"Shrinkage test", 6=>"High Risk Analysis test");
    
    if($pi_id != "")
    {
        $cross_check_items = return_field_value("cross_check_activity_ids","com_cross_check_activity","pi_id=$pi_id and status_active=1","cross_check_activity_ids");
        $id = return_field_value("id","com_cross_check_activity","pi_id=$pi_id and status_active=1","id");
    }
    //echo $cross_check_items;die;
    $cross_check_items = explode(",", chop($cross_check_items,","));
    //echo $id;die;

    ?>
    <script>
        var activity_id = "<? echo $id;?>";
            function set_cross_check_value(){
               
                parent.emailwindow.hide();
            }

    </script>
    </head>
    <body>
        <div align="center" style="width:300px;" >
            <form name="crosscheckform" id="crosscheckform"  autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="300" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Checked/<br/>Unchecked</th>
                        <th>Checked/Unchecked Item Details</th>
                    </tr>
                </thead>
                <tbody>
            <?
            $i=1;
            foreach ($pi_cross_check_array as $key => $value) {
                if($i%2==0) $bg_color = "#E9F3FF"; 
                    else $bg_color = "#FFFFFF";
            ?>		
                <tr bgcolor="<? echo $bg_color;?>" onClick="set_checkbox_value(<? echo $i;?>)" style="cursor:pointer">
                    <td align="center" width="30"><? echo $i;?></td>
                    <td align="center" width="70">
                    <input type="checkbox" name="cross_checked_item_<? echo $i;?>" id="cross_checked_item_<? echo $i;?>" class="cross_check_item" value="<? echo $key;?>" onClick="js_set_value(this);" readonly/> </td>
                    <td style="padding-left: 5px;"> <? echo $value; ?></td>
                </tr>		
            <?
            $i++;
            }

            ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td align="left" colspan="3">
                            <input type="button" name="close" onClick="set_cross_check_value()" class="formbutton" value="Close" style="width:100px" />
                            
                        </td>
                    </tr>
                </tfoot>
            </table>
            <script>
            <?
            foreach ($cross_check_items as $value) {
                ?>
                
                    var id = "#cross_checked_item_"+<? echo $value;?>;
                    $(id).attr("checked",true).val(<? echo $value;?>);
                    //$("#cross_checked_item_"+val).val(val);
               
                <?
            }
            ?>
             </script>
            </form>
        </div>
    </body>
    </html>
        <?
        exit();
}



if ($action == "all_job_by_pi_popup") {
    echo load_html_head_contents("Cross Check Details", "../../", 1, 1,'','','');
    extract($_REQUEST);

    ?>

    </head>
    <body>
        <div style="width:300px;" >
           <?
		   
	$sql= "SELECT a.id, e.JOB_NO
    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and a.id=$pi_id
    group by a.id, e.JOB_NO
    union all 
    select a.id, e.JOB_NO
    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 and a.id=$pi_id
    group by a.id, e.JOB_NO
    union all 
    select a.id, e.JOB_NO
    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) and a.id=$pi_id
    group by a.id, e.JOB_NO
	union all 
    select a.id, e.JOB_NO
    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) and a.id=$pi_id
    group by a.id, e.JOB_NO
	union all
    select a.id, e.JOB_NO
    from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 and a.id=$pi_id
    group by a.id, e.JOB_NO";
	
	$sqlBuyerData = sql_select($sql); $job_arr=array();
	foreach ($sqlBuyerData as $brow)
	{
		$job_arr[$brow[csf('JOB_NO')]]=$brow[csf('JOB_NO')];
	}
	unset($sqlBuyerData);
	echo implode(', ',$job_arr);  
		   ?> 
            
        </div>
    </body>
    </html>
        <?
        exit();
}



if($action=='pi_approval_mail'){

    include('../../auto_mail/setting/mail_setting.php');
    $userSql = "SELECT ID, USER_EMAIL, USER_NAME, USER_FULL_NAME FROM USER_PASSWD";
    $userSqlRes=sql_select($userSql);
    foreach($userSqlRes as $urserRow){
        $user_maill_arr[$urserRow['ID']] =$urserRow['USER_EMAIL']; 
        $user_name_arr[$urserRow['ID']] =$urserRow['USER_NAME']; 
        $user_full_name_arr[$urserRow['ID']] =$urserRow['USER_FULL_NAME']; 
    }

    $user_arr=return_library_array( "select id,user_name from user_passwd", "id","user_name"  );
 

    
    $lib_com_arr=return_library_array("select id,COMPANY_NAME from LIB_COMPANY","id","COMPANY_NAME");
    $supplier_arr = return_library_array("select id,SUPPLIER_NAME from LIB_SUPPLIER where status_active=1 and is_deleted=0","id","SUPPLIER_NAME");
   
    $piFor_array=array(1=>"BTB",2=>"Margin LC",3=>"Fund Buildup",4=>"TT/Pay Order",5=>"FTT",6=>"FDD");
    $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");

    list($sysId, $mailId, $type, $alter_user_id, $mail_body, $cause_arr, $alterUserID)=explode('__',$data);
    $sysId=str_replace('*',',',$sysId);
   
    $mail_body = $mail_body;
    $cause_arr = $cause_arr;

    // print_r(explode(",",$cause_arr));die;

    $app_user_id = ($alter_user_id != "")?$alter_user_id:$user_id;

    if($alterUserID != ""){
        $user_id = $alterUserID;
    }
    else{
        $user_id = $user_id;
    }
   // echo $app_user_id;die;

    $sql="  SELECT a.APPROVED,a.APPROVED_BY,a.NET_TOTAL_AMOUNT,a.IMPORTER_ID,A.ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY,a.INTERNAL_FILE_NO,a.LC_SC_NO,a.PAY_TERM,a.PI_FOR,a.REMARKS,a.PRIORITY_ID  FROM com_pi_master_details a,  com_pi_item_details b  WHERE a.id=b.pi_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND a.id IN ($sysId) GROUP BY a.APPROVED,a.APPROVED_BY,a.NET_TOTAL_AMOUNT,A.ID,a.IMPORTER_ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY,a.INTERNAL_FILE_NO,a.LC_SC_NO,a.PAY_TERM,a.PI_FOR,a.REMARKS,a.PRIORITY_ID ";
    $sql_dtls=sql_select($sql);
    
    // echo $sql;die;
    foreach($sql_dtls as $row){ 

        $toArr = array();
        if($mailId){$toArr[]=str_replace('*',',',$mailId);}

        $sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=63 and b.mail_user_setup_id=c.id and a.company_id={$row['IMPORTER_ID']}   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.MAIL_TEMPLATE<>1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $rows)
		{
			if($rows['EMAIL_ADDRESS']){$toArr[$rows['EMAIL_ADDRESS']]=$rows['EMAIL_ADDRESS']; }
		}

      
		$team_email_arr = return_library_array("
		select ID,TEAM_MEMBER_EMAIL from LIB_MKT_TEAM_MEMBER_INFO where team_id in(select a.id from LIB_MARKETING_TEAM a,LIB_MKT_TEAM_MEMBER_INFO b where a.id=b.team_id and b.USER_TAG_ID={$row['INSERTED_BY']} and TEAM_MEMBER_EMAIL is not null)  and STATUS_ACTIVE=1 and IS_DELETED=0 and TEAM_MEMBER_EMAIL is not null",'ID','TEAM_MEMBER_EMAIL');
 

		if(count($team_email_arr)){
			$team_email_str = implode(',',$team_email_arr);
			$toArr[$team_email_str]=$team_email_str;
		}

         
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","entry_form=27 and user_id=$app_user_id and company_id ={$row['IMPORTER_ID']} and is_deleted = 0");

        $elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL,b.ITEM_CATE_ID   from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.entry_form=27 and a.SEQUENCE_NO>$user_sequence_no and a.company_id={$row['IMPORTER_ID']} order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
        //echo $elcetronicSql;die;
    
        $elcetronicSqlRes=sql_select($elcetronicSql);
        foreach($elcetronicSqlRes as $rows){
            
            if($rows['ITEM_CATE_ID'] != ''){ 
                $ITEM_CATE_ID_ARR = explode(',',$rows['ITEM_CATE_ID']);
                foreach($ITEM_CATE_ID_ARR as $item_cat_id){  
                    if($rows['USER_EMAIL']!='' && $item_cat_id == $row['ITEM_CATEGORY_ID']){
                        $toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
                    } 
                }
                if($rows['BYPASS']==2){break;}
            }
            else{ 
                if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
                if($rows['BYPASS']==2){break;}
            }

        }
      
        if($row['APPROVED'] ==1){$statusMsg = 'Full Approved';}
        else if($row['APPROVED'] ==3){$statusMsg = 'Partial Approved';}
        else if($row['APPROVED'] == 0){$statusMsg = 'Unapproved';}
        else if($type ==5){$statusMsg = 'Denied';}
 
        ob_start();	
        ?>
        <b>Dear Concerned,</b>				
        <table rules="all" border="1">
            <tr>
                <td colspan="7"><b>This PI has been <?= $statusMsg; ?>, Please Proceed.</b></td>
                <td colspan="7" align="right"><b><?= $statusMsg; ?> by <?php echo $user_arr[$user_id] ;?></b></td>
            </tr>
            <tr bgcolor="#CCCCCC">
                <td>SL</td>
                <td>Company</td>
                <td>System ID</td>
                <td>Item Category</td>
                <td>PI Receive Date</td>
                <td>PI No</td>
                <td>PI Value</td>
                <td>Supplier</td>
                <td>Internal File No</td>
                <td>LC/SC</td>
                <td>Pay Term</td>
                <td>Pi For</td>
                <td>Priority</td>
                <td>Remarks</td>
            </tr>
            <?php 
            $i=1;
                if($user_maill_arr[$row['INSERTED_BY']]){$mailArr[$row['INSERTED_BY']]=$user_maill_arr[$row['INSERTED_BY']];}
                if($user_maill_arr[$app_user_id]){$mailArr[$app_user_id]=$user_maill_arr[$app_user_id];}
            ?>
            <tr>
                <td><?= $i;?></td>
                <td><?= $lib_com_arr[$row['IMPORTER_ID']];?></td>
                <td><?= $row['ID'];?></td>
                <td><?= $item_category[$row['ITEM_CATEGORY_ID']];?></td>
                <td><?= change_date_format($row['PI_DATE']);?></td>
                <td><?= $row['PI_NUMBER']?></td>
                <td><?= number_format($row['NET_TOTAL_AMOUNT'],4);?></td>
                <td><?= $supplier_arr[$row['SUPPLIER_ID']];?></td>
                <td><?= $row['INTERNAL_FILE_NO'];?></td>
                <td><?= $row['LC_SC_NO'];?></td>
                <td><?= $pay_term[$row['PAY_TERM']];?></td>
                <td><?= $piFor_array[$row['PI_FOR']];?></td>
                <td><?= $piFor_array[$row['PRIORITY_ID']];?></td>
                <td><?= $row['REMARKS'];?></td>
            </tr>
        </table>
        <br>
          
        <ul>
            <?php
            foreach(explode(",",$cause_arr) as $couse_data){?>
            <li><?= $couse_data;?></li>
            <?php
            }
            ?>

        </ul> 
        <p><?= $mail_body;?></p>

        <?php 
        $message=ob_get_contents();
        ob_clean();
  
        $sysId = str_replace(",","','",$sysId);
        $image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID ='".$row['ID']."'",'ID','IMAGE_LOCATION');
 
        $att_file_arr=array();
        foreach($image_arr as $file){
            $att_file_arr[] = '../../'.$file.'**'.$file;
        }

        $header=mailHeader();
        $to = implode(',',array_unique(explode(',',implode(',',$toArr))));
    
        echo $message."<br>".implode(',',$att_file_arr)."<br>".$to."<br><br>"; 
        $subject = "Pi Approval";
        if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
    } 
   
    exit();	
}
 
if($action=="print_button_variable")
{ 
    echo load_html_head_contents("Print Button Options", "../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_id, $sys_id, $entryForm, $cbo_item_category_id, $export_pi_id) = explode('*', $print_data);
  
    ?>

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        function fnc_pi_approval_mst( operation )
        {
            var pi_approval_mst_values = $("#pi_approval_mst_values").val();
            var approval_mst_value = pi_approval_mst_values.split("*");
            var company_id =  approval_mst_value[0];
            var sys_id = approval_mst_value[1];
            var entry_form = approval_mst_value[2];
            var cbo_item_category_id = approval_mst_value[3];
            var export_pi_id = approval_mst_value[4];
            var cbo_goods_rcv_status = 2;
            var cbo_pi_basis_id = 1;
            var is_approved = '';

            if(sys_id=="")
            {
                alert("Something went wrong");
                return;
            }
            // print
            if(operation==1)
            {
                if((cbo_item_category_id==74 || cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104)  && export_pi_id=="")
                {
                    alert("This Category Not Allow Without Export PI");return;
                }
                
                if((cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104 || cbo_item_category_id==31 || cbo_item_category_id==115) && cbo_goods_rcv_status==1)
                {
                    alert("After Goods Receive Status Not Allow For This Category");return;
                }

                var good_rece_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
                if(cbo_item_category_id==25 && cbo_goods_rcv_status==1 && good_rece_data_source_arr[cbo_importer_id]!=1)
                {
                    alert("After Goods Receive Status Only Allow For Varriable Setting After Good Receive Data Source always Work Order This Category.");return;
                }

                if(operation!=4)
                {
                    if(is_approved==1 || is_approved==3)
                    {
                        alert("PI is Approved. So Change Not Allowed");
                        return;
                    }
                }

                if(cbo_pi_basis_id==2 && cbo_goods_rcv_status==1)
                {
                    alert("Goods Rcv Status (After Goods Rcv) Not Allow For PI Basis (Independent)");
                    return;
                }
                   
                if( cbo_item_category_id == "1")
                {
                    entry_form = "165";
                }
                else if( cbo_item_category_id == "2" ||  cbo_item_category_id == "3" ||  cbo_item_category_id == "13" ||  cbo_item_category_id == "14")
                {
                    entry_form = "166";
                }
                else if( cbo_item_category_id == "4")
                {
                    entry_form = "167";
                }
                else if( cbo_item_category_id == "12")
                {
                    entry_form = "168";
                }
                else if( cbo_item_category_id == "24")
                {
                    entry_form = "169";
                }
                else if( cbo_item_category_id == "25" || cbo_item_category_id == "102" || cbo_item_category_id == "103")
                {
                    entry_form = "170";
                }
                else if( cbo_item_category_id == "30")
                {
                    entry_form = "197";
                }
                else if( cbo_item_category_id == "31")
                {
                    entry_form = "171";
                }
                else if( cbo_item_category_id == "5" ||  cbo_item_category_id == "6" ||  cbo_item_category_id == "7" ||  cbo_item_category_id == "23")
                {
                    entry_form = "227";
                }
                else
                {
                    entry_form = "172";
                } 
                print_report(company_id+'*'+sys_id+'*'+entry_form+'*'+cbo_item_category_id, "print", "../../commercial/import_details/requires/pi_print_urmi");
                return;
              
            }
            // print-2
            if(operation==2){
                if(cbo_item_category_id==3)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_wf", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Woven Fabrics Item Print Allowed.");
                    return;
                }
            }
            // print-3
            if(operation==3)
            {
                if(cbo_item_category_id==12)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_sf", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Services Fabrics Item Print Allowed.");
                    return;
                }
            }
            // PI-print
            if(operation==4){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_pi", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
            // Print-5
            if(operation==5){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_f", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
        } 
    </script>

    <?php
    $buttonHtml='';
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
    $buttonHtml.='<div align="center">';
        foreach($printButton as $id){
            if($id==86)$buttonHtml.='
            <input type="hidden" name="printBtn4" id="pi_approval_mst_values" value="'.$print_data.'"/>
            <input type="button" name="printBtn4" id="printBtn4" value="Print" onClick="fnc_pi_approval_mst(1)" style="width:100px" class="formbutton"/>';

            if($id==116)$buttonHtml.='<input type="button" name="printBtn2" id="printBtn2" value="Print 2" onClick="fnc_pi_approval_mst(2)" style="width:100px" class="formbutton">';
            if($id==85)$buttonHtml.='<input type="button" name="printBtn3" id="printBtn3" value="Print 3" onClick="fnc_pi_approval_mst(3)" style="width:100px" class="formbutton">';	
            if($id==751)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="PI-Print" onClick="fnc_pi_approval_mst(4)" style="width:100px" class="formbutton" />';	
            if($id==479)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="Acc." onClick="fnc_pi_approval_mst(5)" style="width:100px" class="formbutton" />';
        }
    $buttonHtml.='</div>';
    echo $buttonHtml;
    exit();
} 
?>