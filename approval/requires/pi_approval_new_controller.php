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
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );



/*if($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
    exit();
}

*/

if($action=="load_dropdown_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/pi_approval_new_controller',this.value, 'load_dropdown_season', 'season_td' );load_drop_down( 'requires/pi_approval_new_controller',this.value, 'load_dropdown_brand', 'brand_td' );" );  
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
    //  echo "SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$data[1]' AND valid = 1";die;
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

if($action=="report_generate" and $type==1)
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
	
	

    if ($txt_pi_no!="") $pi_no_cond = " and a.pi_number='$txt_pi_no'";	
    if ($txt_pi_sys_id_no!="") $pi_sys_no_cond = " and a.id='$txt_pi_sys_id_no'";	

	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0)  $txt_date=change_date_format($txt_date,"yyyy-mm-dd");
		else   			 $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.pi_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.pi_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.pi_date='".$txt_date."'";
		else $date_cond = '';
	}

	$approval_type = str_replace("'","",$cbo_approval_type);

	if($previous_approved==1 && $approval_type==1)
    {
        $previous_approved_type=1;
    }
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    if($txt_alter_user_id!="")
    {       
        $user_id=$txt_alter_user_id;
    }

	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");     
    $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq"); 
	//echo "select sequence_no form electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";die;
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pro Forma Invoice.</font>";
		die;
	}
	$category_array=array();
	$all_category = array_keys($item_category);
	$OtherUserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');

    $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    $user_crediatial_item_cat_cond = "";
    if($item_cate_id != "")
    {
        $category_array = explode(",",$item_cate_id);
        $user_crediatial_item_cat_cond = " and b.item_category_id in ($item_cate_id)";
        $user_crediatial_item_cat_cond2 = " and c.item_category_id in ($item_cate_id)";
    }
    else
    {
        $category_array = $all_category;
    } 
	
	$category_id=implode(",",$category_array);

	//echo "10**".$category_id; die;
	if($category_id!="") $category_idCond="and b.item_category_id in ($category_id)"; else $category_idCond="";

    if($db_type==0) 
	{
		$item_cateory_id_list = " group_concat(b.item_category_id)";
		$year_field=" YEAR(a.insert_date)"; 		
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY')";
		$item_cateory_id_list = " listagg(b.item_category_id,',') within group (order by b.item_category_id)";		
	}

    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');	
    

    if($previous_approved==1 && $approval_type==1)  //approval process with prevous approve start
    {
        $sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
        if($db_type == 0 )
        {
            $select_item_cat = "group_concat(c.item_category) as item_category_id ";
        }
        else
        {
            $select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
        }

        $sql = "SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.total_amount as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year, a.within_group, a.import_pi
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sequence_no_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP BY a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi
        ORDER BY c.id desc";
        // echo $sql; die();
    }
	elseif($approval_type==0) // unapproval process start
	{  
		if($user_sequence_no==$min_sequence_no) // First user
		{
           $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.total_amount as net_pi_amount, a.net_total_amount, a.is_apply_last_update, $year_field as year, a.within_group, a.import_pi
            from com_pi_master_details a, com_pi_item_details b 
            where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $pi_no_cond $category_idCond
            group by a.id, a.item_category_id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, b.item_category_id, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi 
            order by a.id desc";
            //echo $sql;die;
		}
		else // Next user
        {
            if($db_type==0)
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a ,user_passwd b "," a.user_id=b.id and b.item_cate_id='' and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2 and a.is_deleted = 0","seq");
			}
			else
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a, user_passwd b "," a.user_id=b.id and b.item_cate_id  is   null and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2  and a.is_deleted = 0","seq");
			}
		   
            if($sequence_no=="") // bypass if previous user Yes
            {
            	$seqSql="select a.sequence_no, a.bypass, b.item_cate_id from electronic_approval_setup a, user_passwd b where a.user_id=b.id and  a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.is_deleted=0 order by a.sequence_no desc";
            	//echo $seqSql;die;
				$seqData=sql_select($seqSql);

				$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
				foreach($seqData as $sRow)
				{
					if($sRow[csf('bypass')]==2)
					{
						$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
						if($sRow[csf('item_cate_id')]!="")
						{
							$buyerIds.=$sRow[csf('item_cate_id')].",";
							$buyer_id_arr=explode(",",$sRow[csf('item_cate_id')]);
							$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
							if(count($result)>0)
							{
								$query_string.=" (c.sequence_no=".$sRow[csf('sequence_no')]." and b.item_category_id in(".implode(",",$result).")) or ";
							}
							$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
						}
					}
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}

                //print_r($check_buyerIds_arr);die;
				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="")
				{
					$buyerIds_cond="";
					$seqCond="";
				}
				else
				{
					$buyerIds_cond=" and b.item_category_id not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				$pi_mst_id='';
				$pi_mst_id_sql="SELECT distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_no) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $seqCond $category_idCond
				union
				select distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $category_idCond";

				//echo $pi_mst_id_sql;die;
				$bResult=sql_select($pi_mst_id_sql);
				foreach($bResult as $bRow)
				{
					$pi_mst_id.=$bRow[csf('pi_mst_id')].",";
				}


				$pi_mst_id=chop($pi_mst_id,',');

				$pi_mst_id_app_sql=sql_select("SELECT a.id as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c
				where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no=$user_sequence_no and c.entry_form=27 and a.ready_to_approved=1 and c.current_approval_status=1 $category_idCond");

				foreach($pi_mst_id_app_sql as $inf)
				{
					if($pi_mst_id_app_byuser!="") $pi_mst_id_app_byuser.=",".$inf[csf('pi_mst_id')];
					else $pi_mst_id_app_byuser.=$inf[csf('pi_mst_id')];
				}

				$pi_mst_id_app_byuser=implode(",",array_unique(explode(",",$pi_mst_id_app_byuser)));

				$pi_mst_id_app_byuser=chop($pi_mst_id_app_byuser,',');
				$result=array_diff(explode(',',$pi_mst_id),explode(',',$pi_mst_id_app_byuser));
				$pi_mst_id=implode(",",$result);
				//echo $pre_cost_id;die;
				$pi_mst_id_cond="";

				if($pi_mst_id_app_byuser!="")
				{
					$pi_mst_id_app_byuser_arr=explode(",",$pi_mst_id_app_byuser);
					if(count($pi_mst_id_app_byuser_arr)>995)
					{
						$pi_mst_id_app_byuser_chunk_arr=array_chunk(explode(",",$pi_mst_id_app_byuser),995) ;
						foreach($pi_mst_id_app_byuser_chunk_arr as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$pi_mst_id_cond.=" and a.id not  in($chunk_arr_value)";
						}
					}
					else
					{
						$pi_mst_id_cond=" and a.id not in($pi_mst_id_app_byuser)";
					}
				}
				else $pi_mst_id_cond="";


               $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, a.total_amount as net_pi_amount, a.net_total_amount, a.is_apply_last_update, a.within_group, a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b 
                WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0) 
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.total_amount, a.net_total_amount, $year_field 
                ORDER by a.id desc";

                if($pi_mst_id!="")
				{
					$pi_mst_id_cond2="and ";
					$pi_mst_id_arr=explode(",",$pi_mst_id);
					if(count($pi_mst_id_arr)>995)
					{
						$pi_mst_id_cond2.=" ( ";
						$pi_mst_id_arr_chunk_arr=array_chunk(explode(",",$pi_mst_id),995) ;
						$slcunk=0;
						foreach($pi_mst_id_arr_chunk_arr as $chunk_arr)
						{
							if($slcunk>0) $pi_mst_id_cond2.=" or";
							$chunk_arr_value=implode(",",$chunk_arr);	
							$pi_mst_id_cond2.="  a.id  in($chunk_arr_value)";
							$slcunk++;	
						}
						$pi_mst_id_cond2.=" )";
					}
					else
					{
						$pi_mst_id_cond2.="  a.id  in($pi_mst_id)";	 
					}
					
                    $sql=" SELECT x.* from (SELECT a.id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.update_date,a.pi_for, a.last_shipment_date,$year_field as year, a.internal_file_no,a.approved, a.remarks, a.total_amount as net_pi_amount,a.net_total_amount,a.is_apply_last_update, a.within_group, a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $category_idCond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0,2) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi ";
                    $sql.=" union all
                    SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, a.total_amount as net_pi_amount, a.net_total_amount, a.is_apply_last_update, a.within_group, a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond2 and a.approved in(0,3) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi ) x
                    order by x.id";
				}
                // echo "**".$sql;die;
            }
            else // bypass No
            {
				$user_sequence_no=$user_sequence_no-1;

                if($db_type==0)
                {
                    $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no  and is_deleted=0");
                }
                else
                {
                    $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no", "electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
                }

                if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
                else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
               
                $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.total_amount as net_pi_amount, a.net_total_amount, a.within_group, a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b, approval_history c
                WHERE a.id=b.pi_id and a.id=c.mst_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(0,3) and c.current_approval_status=1 and b.status_active=1 and b.is_deleted=0 and b.amount>0 $date_cond $sequence_no_cond $pi_sys_no_cond  $pi_no_cond $user_crediatial_item_cat_cond $category_idCond AND c.entry_form = 27
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi 
                ORDER by a.id desc"; 
                //echo $sql;            
            }
		}
	}
	else // approval process start
    {
		$sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.total_amount as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year, a.within_group, a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and c.approved_by= $user_id $date_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date,a.update_date, a.pi_for, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.total_amount, a.net_total_amount, $year_field, a.within_group, a.import_pi
		ORDER by c.id desc";
        //echo $sql;
	} 
	//echo $sql;die;

    $nameArray = sql_select( $sql );
    foreach ($nameArray as $row) {
        $pi_ids.=$row[csf('id')].',';

        $pi_id_arr[$row[csf('id')]]=$row[csf('id')];
    }


    $pi_Ids = implode(",", array_unique(explode(",", rtrim($pi_ids,','))));


    $con = connect();
    execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=47");
    oci_commit($con);
    disconnect($con);
    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 47, 1, $pi_id_arr, $empty_arr);

  

    if ($pi_Ids != ''){
        
		$flag=0;
		if($cbo_buyer_name){$whereCon.=" and e.buyer_name =$cbo_buyer_name ";$flag=1;}
		if($cbo_season_name>0){$whereCon.=" and e.SEASON_BUYER_WISE =$cbo_season_name";$flag=1;}
		if($cbo_season_year>0){$whereCon.=" and e.SEASON_YEAR =$cbo_season_year";$flag=1;}
		if($cbo_brand_id>0){$whereCon.=" and e.BRAND_ID=$cbo_brand_id ";$flag=1;}
		if($txt_internal_ref!=''){$whereCon.=" and f.FILE_NO ='$txt_internal_ref'";$flag=1;}
 
 	 
		//$pi_mst_id_cond
		$sql_buyer_marchent ="SELECT e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO, b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 1 as type
        from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f,gbl_temp_engine g
        where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_id=e.id and e.id=f.job_id and d.job_id=f.job_id and e.company_name = $company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id 
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 2 as type
        from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f,gbl_temp_engine g
        where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_id=e.id and e.job_no=f.job_no_mst and d.job_no=f.job_no_mst and c.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 3 as type
        from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f,gbl_temp_engine g
        where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no and e.id=f.job_id and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25)  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all 
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 4 as type
        from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f,gbl_temp_engine g
        where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.id=f.job_id and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14)  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond  $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id
        union all
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 5 as type 
        from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f,gbl_temp_engine g
        where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.id=f.job_id and c.job_no=f.job_no_mst and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond   $category_idCond $whereCon
        group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id 
        union all
        select e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, e.job_no, f.id as order_id, 6 as type from com_pi_master_details a,com_pi_item_details b, wo_labtest_dtls c ,wo_po_details_master e,wo_po_break_down f,gbl_temp_engine g where a.id=b.pi_id  and a.id=g.ref_val and g.entry_form=47 and g.ref_from=1 and g.user_id=$user_id $date_cond $pi_sys_no_cond $pi_no_cond  $category_idCond and b.item_category_id=31  and b.WORK_ORDER_ID=c.MST_ID and c.job_no =e.job_no and e.job_no=f.job_no_mst and c.job_no=f.job_no_mst $whereCon group by e.STYLE_REF_NO,e.SEASON_BUYER_WISE,e.SEASON_YEAR,e.BRAND_ID,f.FILE_NO,b.pi_id, e.buyer_name, e.dealing_marchant, f.id, e.job_no        
        ";
        //echo $sql_buyer_marchent;die;
        $sql_job=sql_select($sql_buyer_marchent); // and a.pi_id=6201

        $buyer_marchant_arr=array();
        $pi_id_arr=array();
        $order_ids='';
        $tot_rows=0;
        foreach($sql_job as $row)
        {
            
			$tot_rows++;
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
            
                $buyer_marchant_arr[$row[csf("pi_id")]]["STYLE_REF_NO"][$row[csf("STYLE_REF_NO")]]=$row[csf("STYLE_REF_NO")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["job"][$row[csf("job_no")]]=$row[csf("job_no")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["SEASON_BUYER_WISE"][$row[csf("SEASON_BUYER_WISE")]]=$season_arr[$row[csf("SEASON_BUYER_WISE")]];
                $buyer_marchant_arr[$row[csf("pi_id")]]["SEASON_YEAR"][$row[csf("SEASON_YEAR")]]=$row[csf("SEASON_YEAR")];
                $buyer_marchant_arr[$row[csf("pi_id")]]["BRAND_NAME"][$row[csf("BRAND_ID")]]=$brand_arr[$row[csf("BRAND_ID")]];
			 	$buyer_marchant_arr[$row[csf("pi_id")]]["FILE_NO"][$row[csf("FILE_NO")]]=$row[csf("FILE_NO")];
			
				$pi_id_arr[$row[csf("order_id")]]=$row[csf("pi_id")];         
				
				$piArr[$row[csf("pi_id")]]=$row[csf("pi_id")];         

            if ($row[csf("order_id")] != '') $order_ids.=$row[csf("order_id")].',';
        }
        unset($sql_job);
        // Buyer and dealing_marchant info END
        //print_r($pi_id_arr);die;

        if ($order_ids != '')
        {
            $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
            $order_id_cond = '';

            if($db_type==2 && $tot_rows>1000)
            {
                $order_id_cond = ' and (';
                $orderNoArr = array_chunk($orderIds,999);
                foreach($orderNoArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
                }
                $order_id_cond = rtrim($order_id_cond,'or ');
                $order_id_cond .= ')';
            }
            else
            {
                $orderIds = implode(',', $orderIds);
                $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
            }
        }
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
    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=27  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".$pi_Ids.")");
 
    //echo "select * from fabric_booking_approval_cause where  entry_form=27  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".implode(',',$pi_id_arr).")";die();
	$unapproved_request_arr=array();
	$approval_case_arr=array();
	foreach($sql_unapproved as $rowu)
	{
        $approval_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu['NOT_APPROVAL_CAUSE'];
	}

    $sql_remarks=sql_select("select * from fabric_booking_approval_cause where  entry_form=27  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".$pi_Ids.")");
    // $sql="select * from fabric_booking_approval_cause where  entry_form=27  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".$pi_Ids.")";
   // echo $sql;die();
	$unapproved_request_arr=array();
	$remarks_case_arr=array();
	foreach($sql_remarks as $rowu)
	{
        $remarks_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu['APPROVAL_CAUSE'];
	}

    //     echo "<pre>";
    // print_r($remarks_case_arr); 
    //   echo "</pre>";//die();
   
    $fset=2300;
    $table1=2300; 
    $table2=2280;    
	?>
    <form name="piApproval_2" id="piApproval_2">
        <fieldset style="width:<? echo $fset; ?>px; margin-top:10px">
            <legend>PI Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table2; ?>" class="rpt_table" align="left">
                <thead>
                	<th width="40"></th>
                    <th width="30">SL</th>
                    <th width="60">System Id</th>
                    <th width="80">PI No</th>
                    <th width="80">Image/File</th>
                    <th width="100">Supplier</th>
                    <th width="100">Dealing Merchandiser</th>
                    <th width="80">Job No</th>
                    <th width="80">Style Ref</th>
                    <th width="90">Buyer Name</th>
                    <th width="80">Approval Status</th>
                    <th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="80">Season Year</th>
                    <th width="80">File no</th>
                    <th width="80">PI For</th>
                    <th width="80">Bank File no</th>
                    <th width="70">PI Date.</th>
                    <th width="60"> Submission Date of Approval</th>
                    <th width="70">Last Shipt date</th>
                    <th width="90">Item Category</th>
                    <th width="90">Amount</th>
                    <th width="100">Net Amount</th>
                    <th width="80">Source</th>
                    <th width="70">Year</th>
                    <th width="100">Last Shipt date LC/SC</th>
                    <th width="100">Remarks</th>
                    <th width="100">Refusing cause</th>
                    <th>Cross Check</th>
                </thead>
            </table>            
            <div style="width:<? echo $table1; ?>px; overflow-y:scroll; max-height:330px;" id="pi_approve_unapprove_list_view" >
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table2; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?  
                            $i = 1; $all_approval_id = '';
							
                           	$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='proforma_invoice'","master_tble_id");//master_tble_id='$value' and 
                            foreach ($nameArray as $row)
                            {
                                
								if($flag==1 && $piArr[$row[csf('id')]]==''){continue;}
								
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																
								$value = $row[csf('id')];
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
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('pi_number')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('pi_number')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    </td> 
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="60" align="center"><p><a href="javascript:openPopup(<? echo $row[csf('id')];?>)"><? echo $row[csf('id')];?></a></p></td>
                                    <td width="80" align="center" style="word-break:break-all;">
                                    <?php 
                                    //$row[csf('item_category_id')]=end(explode(',',$row[csf('item_category_id')]));
                                    $row[csf('item_category_id')]=$pi_category_array[$row[csf('id')]];
									
									if ($row[csf('item_category_id')] == 4)  $fireOnFuncName = 'print_pi';
                                    else $fireOnFuncName = 'print';

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
                                    ?>
                                    <a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$row[csf('id')].'*'.$entry_form.'*'.'PI Approval New';?>','<?php echo $fireOnFuncName; ?>', '../commercial/import_details/requires/pi_print_urmi')"><font color="blue"><b><? echo $row[csf('pi_number')]; ?></b></font></a>
                                    </td>
                                    <td width="80" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $row[csf('importer_id')]; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>
                                    <td width="100" style="word-break:break-all;">
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
                                    
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["job"]);?></p></td>
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["STYLE_REF_NO"]);?></p></td>
                                    
                                    <td width="90" style="word-break:break-all;">
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
                                    <td width="80"><p><?= $approva_status_arr[$row[csf("approved")]];?></p></td>
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["BRAND_NAME"]);?></p></td>
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["SEASON_BUYER_WISE"]);?></p></td>
                                    <td width="80" align="center"><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["SEASON_YEAR"]);?></td>
                                    <td width="80" align="center"><a href="javascript:void();" onClick="fnc_file_popup('<?=$row[csf('id')]; ?>','<?=$row[csf('importer_id')]; ?>');"><? echo $row[csf('internal_file_no')]; ?></a></td>
                                    <td width="80"><?= $piFor_array[$row[csf('pi_for')]]; ?></td>
                                    <td width="80" align="center"><? echo implode(',',array_unique(explode(',', rtrim($bank_file_arr[$row[csf('id')]]['bank_file_no'],',')))); ?></td>
                                    <td width="70" align="center"><p><?=change_date_format($row[csf('pi_date')]); ?></p></td>
                                    <td width="60" align="center"><p><?=date('Y-m-d g:i:s A', strtotime($row[csf('update_date')])); ?></p></td>
                                    <td width="70" align="center"><?=$row[csf('last_shipment_date')]; ?>&nbsp;</td>
                                    <td width="90" style="word-break:break-all">
	                                    	<? 
	                                    	$item_ids="";
	                                    	/*foreach (array_unique(explode(",",$row[csf('item_category_id')])) as $item_id) {
	                                    		$item_ids .= $item_category[$item_id].","; 
	                                    	}*/
	                                    	$item_ids .= $item_category[$pi_category_array[$row[csf('id')]]].",";
	                                    	echo chop($item_ids,","); 
	                                    	?>
                                    </td>
                                    <td width="90" align="right"><p><? echo $row[csf('net_pi_amount')]; ?>&nbsp;</p></td>
                                    <td width="100" align="right"><p><? echo $row[csf('net_total_amount')]; ?>&nbsp;</p></td>
                                    <td width="80" align="center" style="word-break:break-all"><? echo $source[$row[csf('source')]]; ?>&nbsp;</td>
                                    <td width="70" id="dealing_merchant_<?=$i;?>"><p><?=$row[csf('year')]; ?>&nbsp;</p></td>
                                    <td width="100" align="center" title="<?=$row[csf("id")];?>"><p><? echo change_date_format($lcsc_ship_date_arr[$row[csf("id")]]['last_shipment_date']); ?>&nbsp;</p></td>

                                    <? 
                                    $casues1=$remarks_case_arr[$value][0];
                                    
								    ?>
                                  
                                    <td width="100" align="center"><input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:90px" title="<?=$value."--".$approval_type?>" value="<? echo $casues1;?>"  maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i; ?>,'1',<?=$user_id;?>)">&nbsp;</td>
                                    <?    
                                   
                                    $casues=$approval_case_arr[$value][$approval_type];
									?>
                                    <td width="100" align="center"><input name="txt_unappv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_unappv_cause_<?=$i;?>" style="width:90px" value="<? echo $casues;?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i; ?>,'2',<?=$user_id;?>)">&nbsp;</td>  

                                   
					
                                    <td align="center"><a href="javascript:void();" onClick="fnc_pi_cross_check('<?=$row[csf('id')]; ?>','<?=$row[csf('importer_id')]; ?>');">View</a>                                    
                                    </td>                                                                      
                                </tr>
                                <Input name='txt_appv_instra[]' class='text_boxes' placeholder='Please Browse' ID='txt_appv_instra_"<?=$i;?>"'  type='hidden'>
                                <?
                                $i++;
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
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
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="40" align="center" style=" <?=$isApp; ?>">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>,<?=$approvePermission; ?>)"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
    <?
    $con = connect();
    execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=47");
    oci_commit($con);
    disconnect($con);
	exit();	
}


if($action=="report_generate" and $type==2)
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
	
	

    if ($txt_pi_no!="") $pi_no_cond = " and a.pi_number='$txt_pi_no'";	
    if ($txt_pi_sys_id_no!="") $pi_sys_no_cond = " and a.id='$txt_pi_sys_id_no'";	

	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0)  $txt_date=change_date_format($txt_date,"yyyy-mm-dd");
		else   			 $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.pi_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.pi_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.pi_date='".$txt_date."'";
		else $date_cond = '';
	}
    if ($txt_date!="") $p_cond = " and a.pi_date='$txt_date'";	

	$approval_type = str_replace("'","",$cbo_approval_type);

	if($previous_approved==1 && $approval_type==1)
    {
        $previous_approved_type=1;
    }
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    if($txt_alter_user_id!="")
    {       
        $user_id=$txt_alter_user_id;
    }

	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");     
    $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq"); 

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pro Forma Invoice.</font>";
		die;
	}
	$category_array=array();
	$all_category = array_keys($item_category);
	$OtherUserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');

    $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    $user_crediatial_item_cat_cond = "";
    if($item_cate_id != "")
    {
        $category_array = explode(",",$item_cate_id);
        $user_crediatial_item_cat_cond = " and b.item_category_id in ($item_cate_id)";
        $user_crediatial_item_cat_cond2 = " and c.item_category_id in ($item_cate_id)";
    }
    else
    {
        $category_array = $all_category;
    } 
	
	$category_id=implode(",",$category_array);

	if($category_id!="") $category_idCond="and b.item_category_id in ($category_id)"; else $category_idCond="";

    if($db_type==0) 
	{
		$item_cateory_id_list = " group_concat(b.item_category_id)";
		$year_field=" YEAR(a.insert_date)"; 		
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY')";
		$item_cateory_id_list = " listagg(b.item_category_id,',') within group (order by b.item_category_id)";		
	}

    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');	
    

    if($previous_approved==1 && $approval_type==1)  //approval process with prevous approve start
    {
        $sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
        if($db_type == 0 )
        {
            $select_item_cat = "group_concat(c.item_category) as item_category_id ";
        }
        else
        {
            $select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
        }

        $sql = "SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year,a.within_group,a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sequence_no_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP BY a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.net_total_amount, $year_field,a.within_group,a.import_pi
        ORDER BY c.id desc";
        // echo $sql; die();
    }
	elseif($approval_type==0) // unapproval process start
	{  
		if($user_sequence_no==$min_sequence_no) // First user
		{
           $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, $year_field as year,a.within_group,a.import_pi
            from com_pi_master_details a, com_pi_item_details b 
            where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $pi_no_cond $category_idCond
            group by a.id, a.item_category_id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, b.item_category_id, a.net_total_amount, $year_field,a.within_group,a.import_pi 
            order by a.id desc";
            //echo $sql;die;
		}
		else // Next user
        {
            if($db_type==0)
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a ,user_passwd b "," a.user_id=b.id and b.item_cate_id='' and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2 and a.is_deleted = 0","seq");
			}
			else
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a, user_passwd b "," a.user_id=b.id and b.item_cate_id  is   null and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2  and a.is_deleted = 0","seq");
			}
		   
            if($sequence_no=="") // bypass if previous user Yes
            {
            	$seqSql="select a.sequence_no, a.bypass, b.item_cate_id from electronic_approval_setup a, user_passwd b where a.user_id=b.id and  a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.is_deleted=0 order by a.sequence_no desc";
            	//echo $seqSql;die;
				$seqData=sql_select($seqSql);

				$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
				foreach($seqData as $sRow)
				{
					if($sRow[csf('bypass')]==2)
					{
						$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
						if($sRow[csf('item_cate_id')]!="")
						{
							$buyerIds.=$sRow[csf('item_cate_id')].",";
							$buyer_id_arr=explode(",",$sRow[csf('item_cate_id')]);
							$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
							if(count($result)>0)
							{
								$query_string.=" (c.sequence_no=".$sRow[csf('sequence_no')]." and b.item_category_id in(".implode(",",$result).")) or ";
							}
							$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
						}
					}
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}

                //print_r($check_buyerIds_arr);die;
				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="")
				{
					$buyerIds_cond="";
					$seqCond="";
				}
				else
				{
					$buyerIds_cond=" and b.item_category_id not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				$pi_mst_id='';
				$pi_mst_id_sql="select distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_no) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $seqCond $category_idCond
				union
				select distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $category_idCond";

				//echo $pi_mst_id_sql;die;
				$bResult=sql_select($pi_mst_id_sql);
				foreach($bResult as $bRow)
				{
					$pi_mst_id.=$bRow[csf('pi_mst_id')].",";
				}


				$pi_mst_id=chop($pi_mst_id,',');

				$pi_mst_id_app_sql=sql_select("select a.id as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c
				where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no=$user_sequence_no and c.entry_form=27 and a.ready_to_approved=1 and c.current_approval_status=1 $category_idCond");

				foreach($pi_mst_id_app_sql as $inf)
				{
					if($pi_mst_id_app_byuser!="") $pi_mst_id_app_byuser.=",".$inf[csf('pi_mst_id')];
					else $pi_mst_id_app_byuser.=$inf[csf('pi_mst_id')];
				}

				$pi_mst_id_app_byuser=implode(",",array_unique(explode(",",$pi_mst_id_app_byuser)));

				$pi_mst_id_app_byuser=chop($pi_mst_id_app_byuser,',');
				$result=array_diff(explode(',',$pi_mst_id),explode(',',$pi_mst_id_app_byuser));
				$pi_mst_id=implode(",",$result);
				//echo $pre_cost_id;die;
				$pi_mst_id_cond="";

				if($pi_mst_id_app_byuser!="")
				{
					$pi_mst_id_app_byuser_arr=explode(",",$pi_mst_id_app_byuser);
					if(count($pi_mst_id_app_byuser_arr)>995)
					{
						$pi_mst_id_app_byuser_chunk_arr=array_chunk(explode(",",$pi_mst_id_app_byuser),995) ;
						foreach($pi_mst_id_app_byuser_chunk_arr as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$pi_mst_id_cond.=" and a.id not  in($chunk_arr_value)";
						}
					}
					else
					{
						$pi_mst_id_cond=" and a.id not in($pi_mst_id_app_byuser)";
					}
				}
				else $pi_mst_id_cond="";


               $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update,a.within_group,a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b 
                WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0) 
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field ,a.within_group,a.import_pi
                ORDER by a.id desc";

                if($pi_mst_id!="")
				{
					$pi_mst_id_cond2="and ";
					$pi_mst_id_arr=explode(",",$pi_mst_id);
					if(count($pi_mst_id_arr)>995)
					{
						$pi_mst_id_cond2.=" ( ";
						$pi_mst_id_arr_chunk_arr=array_chunk(explode(",",$pi_mst_id),995) ;
						$slcunk=0;
						foreach($pi_mst_id_arr_chunk_arr as $chunk_arr)
						{
							if($slcunk>0) $pi_mst_id_cond2.=" or";
							$chunk_arr_value=implode(",",$chunk_arr);	
							$pi_mst_id_cond2.="  a.id  in($chunk_arr_value)";
							$slcunk++;	
						}
						$pi_mst_id_cond2.=" )";
					}
					else
					{
						$pi_mst_id_cond2.="  a.id  in($pi_mst_id)";	 
					}
					
                    $sql=" SELECT x.* from (SELECT a.id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,$year_field as year, a.internal_file_no,a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update,a.within_group,a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $category_idCond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0,2) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi ";
                    $sql.=" union all
                    SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update,a.within_group,a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $p_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond2 and a.approved in(1,3) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi ) x
                    order by x.id";
				}
                // echo "**".$sql;die;
            }
            else // bypass No
            {
				$user_sequence_no=$user_sequence_no-1;

                if($db_type==0)
                {
                    $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no  and is_deleted=0");
                }
                else
                {
                    $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no", "electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
                }

                if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
                else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
               
                $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount,a.within_group,a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b, approval_history c
                WHERE a.id=b.pi_id and a.id=c.mst_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(1,3) and c.current_approval_status=1 and b.status_active=1 and b.is_deleted=0 and b.amount>0 $date_cond $sequence_no_cond $pi_sys_no_cond  $pi_no_cond $user_crediatial_item_cat_cond $category_idCond AND c.entry_form = 27
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi 
                ORDER by a.id desc"; 
                //echo $sql;            
            }
		}
	}
	else // approval process start
    {
		$sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year,a.within_group,a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and c.approved_by= $user_id $date_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.net_total_amount, $year_field,a.within_group,a.import_pi
		ORDER by c.id desc";
        //echo $sql;
	} 
	//echo $sql;die;

    $nameArray = sql_select( $sql );
    foreach ($nameArray as $row) {
        $pi_ids.=$row[csf('id')].',';
    }
    $pi_Ids = implode(",", array_unique(explode(",", rtrim($pi_ids,','))));

    if ($pi_Ids != ''){
        
		$flag=0;
		if($cbo_buyer_name){$whereCon.=" and e.buyer_name =$cbo_buyer_name ";$flag=1;}
		if($cbo_season_name>0){$whereCon.=" and e.SEASON_BUYER_WISE =$cbo_season_name";$flag=1;}
		if($cbo_season_year>0){$whereCon.=" and e.SEASON_YEAR =$cbo_season_year";$flag=1;}
		if($cbo_brand_id>0){$whereCon.=" and e.BRAND_ID=$cbo_brand_id ";$flag=1;}
		if($txt_internal_ref!=''){$whereCon.=" and f.FILE_NO ='$txt_internal_ref'";$flag=1;}
 
 	 
		//$pi_mst_id_cond
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
        $pi_id_arr=array();$all_job_arr=array();
        $order_ids='';
        $tot_rows=0;
        foreach($sql_job as $row)
        {
            
			$tot_rows++;
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

            if ($row[csf("order_id")] != '') $order_ids.=$row[csf("order_id")].',';
			
			
			$all_job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			
        }
        unset($sql_job);
        // Buyer and dealing_marchant info END
        

        if ($order_ids != '')
        {
            $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
            $order_id_cond = '';

            if($db_type==2 && $tot_rows>1000)
            {
                $order_id_cond = ' and (';
                $orderNoArr = array_chunk($orderIds,999);
                foreach($orderNoArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
                }
                $order_id_cond = rtrim($order_id_cond,'or ');
                $order_id_cond .= ')';
            }
            else
            {
                $orderIds = implode(',', $orderIds);
                $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
            }
        }
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
	
	
	
	
 
    $width=1580;
	?>
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
                    <th width="90">Item Category</th>
                    <th width="90">PI Amount</th>
                    <th width="100">PI Net Amount</th>
                    <th width="80">Source</th>
                    <th width="100">Supplier</th>
                    <th width="90">Buyer Name</th>
                    <th width="80">Approval Status</th>
                    <th width="80">Brand</th>
                    <th width="100">Dealing Merchandiser</th>
                    <th width="80">Job No</th>
                    <th width="120">Style Ref.</th>
                    <th width="100">Refusing cause</th>
                    <th width="60">Cross Check</th>
                    <th>BOM</th>
                </thead>
            </table>            
            <div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="pi_approve_unapprove_list_view" >
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                            $i = 1; $all_approval_id = '';
                           $img_val =  return_field_value("master_tble_id","common_photo_library","form_name='proforma_invoice'","master_tble_id");//master_tble_id='$value' and 
                            foreach ($nameArray as $row)
                            {
                                
								if($flag==1 && $piArr[$row[csf('id')]]==''){continue;}
								
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																
								$value = $row[csf('id')];
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
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('pi_number')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('pi_number')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    </td> 
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="60" align="center"><p><a href="javascript:openPopup(<? echo $row[csf('id')];?>)"><? echo $row[csf('id')];?></a></p></td>
                                    <td width="80" align="center" style="word-break:break-all;">
                                    <?php 
                                    //$row[csf('item_category_id')]=end(explode(',',$row[csf('item_category_id')]));
                                    $row[csf('item_category_id')]=$pi_category_array[$row[csf('id')]];
									
									if ($row[csf('item_category_id')] == 4)  $fireOnFuncName = 'print_pi';
                                    else $fireOnFuncName = 'print';

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
                                    ?>
                                    <a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$row[csf('id')].'*'.$entry_form.'*'.'PI Approval New';?>','<?php echo $fireOnFuncName; ?>', '../commercial/import_details/requires/pi_print_urmi')"><font color="blue"><b><? echo $row[csf('pi_number')]; ?></b></font></a>
                                    </td>
                                    
                                    
                                    <td width="60" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $row[csf('importer_id')]; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>
                                    
                                    <td width="70" align="center"><p><?=change_date_format($row[csf('pi_date')]); ?></p></td>
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
                                    <td width="80"><p><?= $approva_status_arr[$row[csf("approved")]];?></p></td>
                                    <td width="80"><p><?=implode(',',$buyer_marchant_arr[$row[csf("id")]]["BRAND_NAME"]);?></p></td>
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
                                    
                                    <td width="80"><p>
									<? 
										foreach($buyer_marchant_arr[$row[csf("id")]]["job"] as $jobNo){
										?>
											<a href="javascript:generate_report('<?=$jobNo;?>')"><?=$jobNo;?></a>
                                         <?
										}
									?>
                                    </p></td>
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
                                    <td width="100" align="center"><input name="txt_unappv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_unappv_cause_<?=$i;?>" style="width:90px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i; ?>,'2')">&nbsp;</td>                                                                      
                                    <td width="60" align="center"><a href="javascript:void();" onClick="fnc_pi_cross_check('<?=$row[csf('id')]; ?>','<?=$row[csf('importer_id')]; ?>');">View</a>                                    
                                    <td align="center" valign="middle">
										<?
										$statusArr=array();
										foreach($buyer_marchant_arr[$row[csf("id")]]["STYLE_REF_NO"] as $jobNo=>$style){
											$yn = ($precost_app_arr[$jobNo]==1)?"<p class='yes'>Yes</p>":"<p class='no'>No</p>";
											$statusArr[$yn]=$yn;
										}
										echo implode(',',$statusArr);
                                        ?>
                                    
                                    </td>

                                                                                                          
                                </tr>
                                <Input name='txt_appv_instra[]' class='text_boxes' placeholder='Please Browse' ID='txt_appv_instra_"<?=$i;?>"'  type='hidden'>
                                <?
                                $i++;
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
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

if($action=="report_generate" and $type==3)
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
	
	

    if ($txt_pi_no!="") $pi_no_cond = " and a.pi_number='$txt_pi_no'";	
    if ($txt_pi_sys_id_no!="") $pi_sys_no_cond = " and a.id='$txt_pi_sys_id_no'";	

	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0)  $txt_date=change_date_format($txt_date,"yyyy-mm-dd");
		else   			 $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.pi_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.pi_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.pi_date='".$txt_date."'";
		else $date_cond = '';
	}
    if ($txt_date!="") $p_cond = " and a.pi_date='$txt_date'";	

	$approval_type = str_replace("'","",$cbo_approval_type);

	if($previous_approved==1 && $approval_type==1)
    {
        $previous_approved_type=1;
    }
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    if($txt_alter_user_id!="")
    {       
        $user_id=$txt_alter_user_id;
    }

	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");     
    $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq"); 

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pro Forma Invoice.</font>";
		die;
	}
	$category_array=array();
	$all_category = array_keys($item_category);
	$OtherUserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');

    $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    $user_crediatial_item_cat_cond = "";
    if($item_cate_id != "")
    {
        $category_array = explode(",",$item_cate_id);
        $user_crediatial_item_cat_cond = " and b.item_category_id in ($item_cate_id)";
        $user_crediatial_item_cat_cond2 = " and c.item_category_id in ($item_cate_id)";
    }
    else
    {
        $category_array = $all_category;
    } 
	
	$category_id=implode(",",$category_array);

	if($category_id!="") $category_idCond="and b.item_category_id in ($category_id)"; else $category_idCond="";

    if($db_type==0) 
	{
		$item_cateory_id_list = " group_concat(b.item_category_id)";
		$year_field=" YEAR(a.insert_date)"; 		
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY')";
		$item_cateory_id_list = " listagg(b.item_category_id,',') within group (order by b.item_category_id)";		
	}

    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');	
    

    if($previous_approved==1 && $approval_type==1)  //approval process with prevous approve start
    {
        $sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
        if($db_type == 0 )
        {
            $select_item_cat = "group_concat(c.item_category) as item_category_id ";
        }
        else
        {
            $select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
        }

        $sql = "SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year,a.within_group,a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sequence_no_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP BY a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.net_total_amount, $year_field,a.within_group,a.import_pi
        ORDER BY c.id desc";
        // echo $sql; die();
    }
	elseif($approval_type==0) // unapproval process start
	{  
		if($user_sequence_no==$min_sequence_no) // First user
		{
           $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, $year_field as year,a.within_group,a.import_pi
            from com_pi_master_details a, com_pi_item_details b 
            where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $user_crediatial_item_cat_cond $pi_sys_no_cond $pi_no_cond $category_idCond
            group by a.id, a.item_category_id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, b.item_category_id, a.net_total_amount, $year_field,a.within_group,a.import_pi 
            order by a.id desc";
            //echo $sql;die;
		}
		else // Next user
        {
            if($db_type==0)
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a ,user_passwd b "," a.user_id=b.id and b.item_cate_id='' and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2 and a.is_deleted = 0","seq");
			}
			else
			{
				$sequence_no=return_field_value("max(a.sequence_no) as seq","electronic_approval_setup a, user_passwd b "," a.user_id=b.id and b.item_cate_id  is   null and a.company_id=$company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.bypass=2  and a.is_deleted = 0","seq");
			}
		   
            if($sequence_no=="") // bypass if previous user Yes
            {
            	$seqSql="select a.sequence_no, a.bypass, b.item_cate_id from electronic_approval_setup a, user_passwd b where a.user_id=b.id and  a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no<$user_sequence_no and a.is_deleted=0 order by a.sequence_no desc";
            	//echo $seqSql;die;
				$seqData=sql_select($seqSql);

				$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
				foreach($seqData as $sRow)
				{
					if($sRow[csf('bypass')]==2)
					{
						$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
						if($sRow[csf('item_cate_id')]!="")
						{
							$buyerIds.=$sRow[csf('item_cate_id')].",";
							$buyer_id_arr=explode(",",$sRow[csf('item_cate_id')]);
							$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
							if(count($result)>0)
							{
								$query_string.=" (c.sequence_no=".$sRow[csf('sequence_no')]." and b.item_category_id in(".implode(",",$result).")) or ";
							}
							$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
						}
					}
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}

                //print_r($check_buyerIds_arr);die;
				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="")
				{
					$buyerIds_cond="";
					$seqCond="";
				}
				else
				{
					$buyerIds_cond=" and b.item_category_id not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				$pi_mst_id='';
				$pi_mst_id_sql="select distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_no) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $seqCond $category_idCond
				union
				select distinct (a.id) as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=27 and c.current_approval_status=1 $user_crediatial_item_cat_cond $category_idCond";

				//echo $pi_mst_id_sql;die;
				$bResult=sql_select($pi_mst_id_sql);
				foreach($bResult as $bRow)
				{
					$pi_mst_id.=$bRow[csf('pi_mst_id')].",";
				}


				$pi_mst_id=chop($pi_mst_id,',');

				$pi_mst_id_app_sql=sql_select("select a.id as pi_mst_id from com_pi_master_details a, com_pi_item_details b, approval_history c
				where a.id=b.pi_id and a.id=c.mst_id and a.importer_id=$company_name and c.sequence_no=$user_sequence_no and c.entry_form=27 and a.ready_to_approved=1 and c.current_approval_status=1 $category_idCond");

				foreach($pi_mst_id_app_sql as $inf)
				{
					if($pi_mst_id_app_byuser!="") $pi_mst_id_app_byuser.=",".$inf[csf('pi_mst_id')];
					else $pi_mst_id_app_byuser.=$inf[csf('pi_mst_id')];
				}

				$pi_mst_id_app_byuser=implode(",",array_unique(explode(",",$pi_mst_id_app_byuser)));

				$pi_mst_id_app_byuser=chop($pi_mst_id_app_byuser,',');
				$result=array_diff(explode(',',$pi_mst_id),explode(',',$pi_mst_id_app_byuser));
				$pi_mst_id=implode(",",$result);
				//echo $pre_cost_id;die;
				$pi_mst_id_cond="";

				if($pi_mst_id_app_byuser!="")
				{
					$pi_mst_id_app_byuser_arr=explode(",",$pi_mst_id_app_byuser);
					if(count($pi_mst_id_app_byuser_arr)>995)
					{
						$pi_mst_id_app_byuser_chunk_arr=array_chunk(explode(",",$pi_mst_id_app_byuser),995) ;
						foreach($pi_mst_id_app_byuser_chunk_arr as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$pi_mst_id_cond.=" and a.id not  in($chunk_arr_value)";
						}
					}
					else
					{
						$pi_mst_id_cond=" and a.id not in($pi_mst_id_app_byuser)";
					}
				}
				else $pi_mst_id_cond="";


               $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update,a.within_group,a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b 
                WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0) 
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field ,a.within_group,a.import_pi
                ORDER by a.id desc";

                if($pi_mst_id!="")
				{
					$pi_mst_id_cond2="and ";
					$pi_mst_id_arr=explode(",",$pi_mst_id);
					if(count($pi_mst_id_arr)>995)
					{
						$pi_mst_id_cond2.=" ( ";
						$pi_mst_id_arr_chunk_arr=array_chunk(explode(",",$pi_mst_id),995) ;
						$slcunk=0;
						foreach($pi_mst_id_arr_chunk_arr as $chunk_arr)
						{
							if($slcunk>0) $pi_mst_id_cond2.=" or";
							$chunk_arr_value=implode(",",$chunk_arr);	
							$pi_mst_id_cond2.="  a.id  in($chunk_arr_value)";
							$slcunk++;	
						}
						$pi_mst_id_cond2.=" )";
					}
					else
					{
						$pi_mst_id_cond2.="  a.id  in($pi_mst_id)";	 
					}
					
                    $sql=" SELECT x.* from (SELECT a.id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,$year_field as year, a.internal_file_no,a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount,a.net_total_amount,a.is_apply_last_update,a.within_group,a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $category_idCond $pi_sys_no_cond $category_idCond $pi_no_cond $pi_mst_id_cond $buyerIds_cond and a.approved in(0,2) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi ";
                    $sql.=" union all
                    SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update,a.within_group,a.import_pi
                    FROM com_pi_master_details a, com_pi_item_details b 
                    WHERE a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.amount>0 $date_cond $user_crediatial_item_cat_cond $p_cond $category_idCond $pi_sys_no_cond $pi_no_cond $pi_mst_id_cond2 and a.approved in(1,3) 
                    GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi ) x
                    order by x.id";
				}
                // echo "**".$sql;die;
            }
            else // bypass No
            {
				$user_sequence_no=$user_sequence_no-1;

                if($db_type==0)
                {
                    $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no  and is_deleted=0");
                }
                else
                {
                    $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no", "electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
                }

                if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
                else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
               
                $sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, $year_field as year, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount,a.within_group,a.import_pi
                FROM com_pi_master_details a, com_pi_item_details b, approval_history c
                WHERE a.id=b.pi_id and a.id=c.mst_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved in(1,3) and c.current_approval_status=1 and b.status_active=1 and b.is_deleted=0 and b.amount>0 $date_cond $sequence_no_cond $pi_sys_no_cond  $pi_no_cond $user_crediatial_item_cat_cond $category_idCond AND c.entry_form = 27
                GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, a.net_total_amount, $year_field,a.within_group,a.import_pi 
                ORDER by a.id desc"; 
                //echo $sql;            
            }
		}
	}
	else // approval process start
    {
		$sql="SELECT a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, sum(b.net_pi_amount) as net_pi_amount, a.net_total_amount, a.is_apply_last_update, c.id as approval_id, $year_field as year,a.within_group,a.import_pi 
        FROM com_pi_master_details a, com_pi_item_details b, approval_history c 
        WHERE a.id=b.pi_id and a.id=c.mst_id and c.entry_form=27 and a.importer_id=$company_name and a.supplier_id like $cbo_supplier_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved in (1,3) and c.approved_by= $user_id $date_cond $pi_sys_no_cond $pi_no_cond $category_idCond
        GROUP by a.id, a.importer_id, a.source, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.internal_file_no, a.approved, a.remarks, a.is_apply_last_update, c.id, a.net_total_amount, $year_field,a.within_group,a.import_pi
		ORDER by c.id desc";
        //echo $sql;
	} 
	//echo $sql;die;

    $nameArray = sql_select( $sql );
    foreach ($nameArray as $row) {
        $pi_ids.=$row[csf('id')].',';
    }
    $pi_Ids = implode(",", array_unique(explode(",", rtrim($pi_ids,','))));

    if ($pi_Ids != ''){
        
		$flag=0;
		if($cbo_buyer_name){$whereCon.=" and e.buyer_name =$cbo_buyer_name ";$flag=1;}
		if($cbo_season_name>0){$whereCon.=" and e.SEASON_BUYER_WISE =$cbo_season_name";$flag=1;}
		if($cbo_season_year>0){$whereCon.=" and e.SEASON_YEAR =$cbo_season_year";$flag=1;}
		if($cbo_brand_id>0){$whereCon.=" and e.BRAND_ID=$cbo_brand_id ";$flag=1;}
		if($txt_internal_ref!=''){$whereCon.=" and f.FILE_NO ='$txt_internal_ref'";$flag=1;}
 
 	 
		//$pi_mst_id_cond
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
        $pi_id_arr=array();$all_job_arr=array();
        $order_ids='';
        $tot_rows=0;
        foreach($sql_job as $row)
        {
            
			$tot_rows++;
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

            if ($row[csf("order_id")] != '') $order_ids.=$row[csf("order_id")].',';
			
			
			$all_job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			
        }
        unset($sql_job);
        // Buyer and dealing_marchant info END
        

        if ($order_ids != '')
        {
            $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
            $order_id_cond = '';

            if($db_type==2 && $tot_rows>1000)
            {
                $order_id_cond = ' and (';
                $orderNoArr = array_chunk($orderIds,999);
                foreach($orderNoArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
                }
                $order_id_cond = rtrim($order_id_cond,'or ');
                $order_id_cond .= ')';
            }
            else
            {
                $orderIds = implode(',', $orderIds);
                $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
            }
        }
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
	
	
	
	
 
    $width=1580;
	?>
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
                    <th width="90">Item Category</th>
                    <th width="90">PI Amount</th>
                    <th width="100">PI Net Amount</th>
                    <th width="80">Source</th>
                    <th width="100">Supplier</th>
                    <th width="90">Buyer Name</th>
                    
                    <th width="80">Brand</th>
                    <th width="100">Dealing Merchandiser</th>
                    <th width="80">Job No</th>
                    <th width="120">Style Ref.</th>
                    <th width="100">Refusing cause</th>
                    <th width="60">Cross Check</th>
                    <th>BOM</th>
                </thead>
            </table>            
            <div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="pi_approve_unapprove_list_view" >
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                            $i = 1; $all_approval_id = '';
                           $img_val =  return_field_value("master_tble_id","common_photo_library","form_name='proforma_invoice'","master_tble_id");//master_tble_id='$value' and 
                            foreach ($nameArray as $row)
                            {
                                
								if($flag==1 && $piArr[$row[csf('id')]]==''){continue;}
								
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																
								$value = $row[csf('id')];
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
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('pi_number')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('pi_number')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    </td> 
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="60" align="center"><p><a href="javascript:openPopup(<? echo $row[csf('id')];?>)"><? echo $row[csf('id')];?></a></p></td>
                                    <td width="80" align="center" style="word-break:break-all;">
                                    <?php 
                                    //$row[csf('item_category_id')]=end(explode(',',$row[csf('item_category_id')]));
                                    $row[csf('item_category_id')]=$pi_category_array[$row[csf('id')]];
									
									if ($row[csf('item_category_id')] == 4)  $fireOnFuncName = 'print_pi';
                                    else $fireOnFuncName = 'print';

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
                                    ?>
                                    <a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$row[csf('id')].'*'.$entry_form.'*'.'PI Approval New';?>','<?php echo $fireOnFuncName; ?>', '../commercial/import_details/requires/pi_print_urmi')"><font color="blue"><b><? echo $row[csf('pi_number')]; ?></b></font></a>
                                    </td>
                                    
                                    
                                    <td width="60" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $row[csf('importer_id')]; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>
                                    
                                    <td width="70" align="center"><p><?=change_date_format($row[csf('pi_date')]); ?></p></td>
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
                                    
                                    <td width="80"><p>
									<? 
										foreach($buyer_marchant_arr[$row[csf("id")]]["job"] as $jobNo){
										?>
											<a href="javascript:generate_report('<?=$jobNo;?>')"><?=$jobNo;?></a>
                                         <?
										}
									?>
                                    </p></td>
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
                                    <td width="100" align="center"><input name="txt_unappv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_unappv_cause_<?=$i;?>" style="width:90px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i; ?>,'2')">&nbsp;</td>                                                                      
                                    <td width="60" align="center"><a href="javascript:void();" onClick="fnc_pi_cross_check('<?=$row[csf('id')]; ?>','<?=$row[csf('importer_id')]; ?>');">View</a>                                    
                                    <td align="center" valign="middle">
										<?
										$statusArr=array();
										foreach($buyer_marchant_arr[$row[csf("id")]]["STYLE_REF_NO"] as $jobNo=>$style){
											$yn = ($precost_app_arr[$jobNo]==1)?"<p class='yes'>Yes</p>":"<p class='no'>No</p>";
											$statusArr[$yn]=$yn;
										}
										echo implode(',',$statusArr);
                                        ?>
                                    
                                    </td>

                                                                                                          
                                </tr>
                                <Input name='txt_appv_instra[]' class='text_boxes' placeholder='Please Browse' ID='txt_appv_instra_"<?=$i;?>"'  type='hidden'>
                                <?
                                $i++;
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
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

    $data_all=explode('_',$data);

    $wo_id=$data_all[0];
    $app_type=$data_all[1];
    $app_cause=$data_all[2];
    $approval_id=$data_all[3];
	$app_from=$data_all[4];
	$user_id=$data_all[5];

    if($app_cause=="")
    {
        $sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=27 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
        //echo $sql_cause; die;
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
                http.open("POST","pi_approval_new_controller.php",true);
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
                //alert(http.responseText);return;

                var reponse=trim(http.responseText).split('**');
                show_msg(reponse[0]);

                set_button_status(1, permission, 'fnc_appv_entry',1);
                release_freezing();

                generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
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
            http.open("POST","pi_approval_new_controller.php",true);
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
            $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');  
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
                //$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
                $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;
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
 


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response=''; 
	
	$sqlBuyer= "SELECT a.id, e.buyer_name
    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and a.id in ($booking_ids)
    group by a.id, e.buyer_name
    union all 
    select a.id, e.buyer_name
    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 and a.id in ($booking_ids)
    group by a.id, e.buyer_name
    union all 
    select a.id, e.buyer_name
    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) and a.id in ($booking_ids)
    group by a.id, e.buyer_name
	union all 
    select a.id, e.buyer_name
    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) and a.id in ($booking_ids)
    group by a.id, e.buyer_name
	union all
    select a.id, e.buyer_name
    from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 and a.id in ($booking_ids)
    group by a.id, e.buyer_name";
	//echo "10**".$sqlBuyer; die;
	
	$sqlBuyerData = sql_select($sqlBuyer); $buyer_arr=array();
	foreach ($sqlBuyerData as $brow)
	{
		$buyer_arr[$brow[csf('id')]]=$brow[csf('buyer_name')];
	}
	unset($sqlBuyerData);

	// $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $alter_uer = $_REQUEST['txt_alter_user_id'];
    if($_REQUEST['txt_alter_user_id']!="")  $user_id_approval=$_REQUEST['txt_alter_user_id']; else $user_id_approval=$user_id;

    $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0");
    $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
    
	if($approval_type == 0)
	{
		$response = $booking_ids;   
        $category_arr=return_library_array( "select id, item_category_id  from com_pi_master_details where id in ($response)", "id", "item_category_id");
		
		if($min_sequence_no != $user_sequence_no)
		{
			$sql = sql_select("select b.buyer_id as buyer_id,b.sequence_no from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.sequence_no order by b.sequence_no ASC");
			foreach ($sql as $key => $buyerID) {
				$allUserBuyersArr[$buyerID[csf('sequence_no')]] = $buyerID[csf('buyer_id')];
				$buyerIds.=$buyerID[csf('buyer_id')].",";
			}

			if(count($allUserBuyersArr)>0)
			{
				foreach ($allUserBuyersArr as $user_id => $buyer_string) {
					$user_buyer_arr = explode(',',$buyer_string);
					foreach ($user_buyer_arr as $buyer_id) {
						$all_buyer_by_seq[$buyer_id] = $user_id;
					}
				}
			}

			$sql = sql_select("select b.buyer_id as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no = $user_sequence_no and b.is_deleted=0 group by b.buyer_id"); $userBuyer=0;
			foreach ($sql as $key => $buyerID) {
				if($buyerID[csf('buyer_id')]!='')
				{
					$currUserBuyersArr[$user_sequence_no] = $buyerID[csf('buyer_id')];
				}
				else
				{
					$currUserBuyersArr[$user_sequence_no] = chop($buyerIds,',');;
				}
			}
			
			if(count($currUserBuyersArr)>0)
			{
				foreach ($currUserBuyersArr as $user_id => $buyer_string) {
					$user_buyer_arr = explode(',',$buyer_string);
					foreach ($user_buyer_arr as $buyer_id) {
						$curr_buyer_by_seq[$buyer_id] = $user_id;
					}
				}
			}
			else
			{
				$userBuyer=1;
			}
			foreach ($curr_buyer_by_seq as $buyer_id=>$sequence_id) {
				if (array_key_exists($buyer_id,$all_buyer_by_seq))
			    {
			    	$key_arr[$buyer_id] = $all_buyer_by_seq[$buyer_id];			    
			    }
			}
			
			foreach ($buyer_arr as $booking => $buyer) {
				if (array_key_exists($buyer,$key_arr))
			    {
			    	$match_seq[$buyer_id] = $key_arr[$buyer_id];			    
			    }
			}
			
			if(count($match_seq)>0 || $userBuyer==1)
			{
				$previous_user_seq = implode(',', $match_seq);
				$previous_user_app = sql_select("select id from approval_history where mst_id in ($booking_ids) and entry_form=27 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
				
				if(count($previous_user_app)==0)
				{
					$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=27 and sequence_no in ($previous_user_seq) and current_approval_status=1 group by id");
					//echo "25**".count($previous_user_app);die;
				}
				
				if(count($previous_user_app)==0)
				{
					echo "25**approved"; 
                    disconnect($con);
					die;
				}
			}				
		}
		
		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else {$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
		
        $partial_approval = "";
		$field_array = "id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
		$id = return_next_id( "id","approval_history", 1) ;
		
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=27 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, approved from com_pi_master_details where id in($booking_ids)","id","approved");
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
		$is_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 ","sequence_no");
		//echo "10** select a.sequence_no from electronic_approval_setup a where a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond";
		
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}

			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
		}
		else
		{
			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{

				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
			//print_r($credentialUserBuyersArr);die;
		}

        //echo "select id, approved from com_pi_master_details where id in($booking_ids)".$booking_nos; die;
		$approved_no_array = array();
		$booking_ids_all = explode(",",$booking_ids);
		$booking_nos_all = explode(",",$booking_nos);
		$app_instru_all = explode(",",$appv_instras);
		$book_nos = '';
		//echo "10**==";
		for($i=0;$i<count($booking_ids_all);$i++)
		{
			$val = $booking_ids_all[$i];
			$booking_id = $booking_ids_all[$i];
			$app_instru = $app_instru_all[$i];

			$approved_no = $max_approved_no_arr[$booking_id];
			$approved_status = $approved_status_arr[$booking_id];
			
			$buyer_id=$buyer_arr[$booking_id];
			
			
			if($approved_status==0)
			{
				$approved_no = $approved_no+1;
				$approved_no_array[$val] = $approved_no;
				if($book_nos=="") $book_nos = $val; else $book_nos.=",".$val;
			}

          
       
			if($is_not_last_user=="")
			{
				if($is_last_user!="")
				{
					if( in_array($buyer_id,$credentialUserBuyersArr) )
					{
						$partial_approval=3;
					}
					else{$partial_approval=1;}
				}
				else{$partial_approval=1;}
				
			}
			else
			{
				if(count($credentialUserBuyersArr)>0)
				{
					if(in_array($buyer_id,$credentialUserBuyersArr)  && $buyer_id!='')
					{
						$partial_approval=3;
					}
					else $partial_approval=1;
				}
				else{$partial_approval=3;}
			}
			
			
			
			//echo $partial_approval.'q'; die;
			
			
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",27,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;			
		}
		
		
		
		
			
//Item store lave start...............................................
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	
	$app_setup_sql= "select USER_ID, SEQUENCE_NO,BYPASS from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0";	
	$app_setup_sql_res=sql_select( $app_setup_sql );
	foreach ($app_setup_sql_res as $row)
	{
		$appDataArr['BYPASS'][$row[USER_ID]]=$row[BYPASS];
		$appDataArr['USER_WISE_SEQ'][$row[USER_ID]]=$row[SEQUENCE_NO];
		$appDataArr['SEQUENCE_WISE_USER'][$row[SEQUENCE_NO]]=$row[USER_ID];
		$appDataArr['USER_ID'][$row[USER_ID]]=$row[USER_ID];
	}
	
	
	$user_sql="select ID, STORE_LOCATION_ID,ITEM_CATE_ID from user_passwd where id in(".implode(',',$appDataArr['USER_ID']).")";	
	$user_sql_res=sql_select( $user_sql );
	foreach ($user_sql_res as $row)
	{
		$userDataArr[$row[ID]]['STORE_ID']=$row[STORE_LOCATION_ID];
		$userDataArr[$row[ID]]['ITEM_CATE_ID']=$row[ITEM_CATE_ID];
	}
	
	
	$nextAllUserStorIdArr=array();
	$nextAllUserItemIdArr=array();
	foreach($appDataArr['SEQUENCE_WISE_USER'] as $seqId=>$userId){
		if($seqId>$appDataArr['USER_WISE_SEQ'][$user_id_approval]){
			$nextAllUserStorIdArr[$userId]=$userDataArr[$userId]['STORE_ID'];
			$nextAllUserItemIdArr[$userId]=$userDataArr[$userId]['ITEM_CATE_ID'];
		}
	}
	
	
	

	if(count($nextAllUserStorIdArr)>0){$nextAllUserStorIdArr=array_unique(explode(',',implode(',',$nextAllUserStorIdArr)));}
	if(count($nextAllUserItemIdArr)>0){$nextAllUserItemIdArr=array_unique(explode(',',implode(',',$nextAllUserItemIdArr)));}
	
		
		
		$sql="select ID, IMPORTER_ID as COMPANY_ID, ITEM_CATEGORY_ID, READY_TO_APPROVED, APPROVED from  com_pi_master_details where id in ($booking_ids)";
		$sqlRes=sql_select( $sql );
		foreach ($sqlRes as $row)
		{
			$sotreIdArr[$row[ID]]=$row[STORE_NAME];
			$itemIdArr[$row[ID]]=$row[ITEM_CATEGORY_ID];
		}
 
		
/*		foreach($sotreIdArr as $sysId=>$storeId){
			if(!in_array($storeId,$nextAllUserStorIdArr) && count($nextAllUserStorIdArr)>0 || $is_not_last_user!=""){
				$approvalStatusBySysIdArr[$sysId]=3;
			}
			else{
				$approvalStatusBySysIdArr[$sysId]=1;
			}
		}
*/		
		
		
		foreach($itemIdArr as $sysId=>$itemId){
			if((in_array($itemId,$nextAllUserItemIdArr) && count($nextAllUserItemIdArr)>0) || $partial_approval==3){
				$approvalStatusBySysIdArr[$sysId]=3;
			}
			else{
				$approvalStatusBySysIdArr[$sysId]=1;
			}
		}
		
		//print_r($approvalStatusBySysIdArr);die;
		
		
		
		foreach($approvalStatusBySysIdArr as $sysId=>$statusVal){
			$data_array_up[$sysId] = explode(",",("'".$approvalStatusBySysIdArr[$sysId]."','".$user_id_approval."','".$pc_date_time."'"));
			$sys_id_up_array[]=$sysId;
			
		}
		
	
		 
		// echo "10**".$rID_up; die;
//...............................................Item store lave end;
			
		
		
	 //echo "10**";print_r($data_array_up);die;

		
		
		
		
		
		
		
		
		//echo count($approved_no_array); die;

		if(count($approved_no_array)>0)
		{
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst = "CASE id ".str_replace("'",'',$approved_string)." END";
			$approved_string_dtls = "CASE pi_id ".str_replace("'",'',$approved_string)." END";
			
			
				
				
            $sql_mst="select '', $approved_string_mst as approved_no, id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by,approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,goods_rcv_status, import_pi,export_pi_id, within_group,ready_to_approved,is_apply_last_update, lc_group_no, entry_form,approval_user, requested_by, ref_closing_status,version, after_goods_source, upcharge_breakdown,beneficiary from com_pi_master_details where id in ($book_nos)"; 
			
			
            $field_array_mst = "id,approved_no, mst_id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, goods_rcv_status, import_pi,export_pi_id, within_group, ready_to_approved,is_apply_last_update, lc_group_no, entry_form,approval_user,requested_by,ref_closing_status,version, after_goods_source, upcharge_breakdown,beneficiary,t_inserted_by, t_insert_date, t_status_active, t_is_deleted";
			
            $data_array_mst = "";
            $id=return_next_id( "id", "com_pi_master_details_history", 1 ) ;
            $serial=$from;
            $check_pi_arr = return_library_array("select mst_id, mst_id from  com_pi_master_details_history", "mst_id", "mst_id");
          	 foreach(sql_select($sql_mst) as $val)
            {
                $mst_id=$val[csf("id")];
                if(!$check_pi_arr[$mst_id])
                {
                    if ($data_array_mst != "") $data_array_mst .= ",";
					$approved_no=$val[csf("approved_no")];
					$mst_id=$val[csf("id")];
					$item_category_id=$val[csf("item_category_id")];
					$importer_id=$val[csf("importer_id")];
					$supplier_id=$val[csf("supplier_id")];
					$pi_number=$val[csf("pi_number")];
					$pi_date=$val[csf("pi_date")];
					$last_shipment_date=$val[csf("last_shipment_date")];
					$pi_validity_date=$val[csf("pi_validity_date")];
					$currency_id=$val[csf("currency_id")];
					$source=$val[csf("source")];
					$hs_code=$val[csf("hs_code")];
					$internal_file_no=$val[csf("internal_file_no")];
					$intendor_name=$val[csf("intendor_name")];
					$pi_basis_id=$val[csf("pi_basis_id")];
					$remarks=$val[csf("remarks")];
					$total_amount=$val[csf("total_amount")];
					$upcharge=$val[csf("upcharge")];
					$discount=$val[csf("discount")];
					$net_total_amount=$val[csf("net_total_amount")];
					$approved=$val[csf("approved")];
					$approved_by=$val[csf("approved_by")];
					$approved_date=$val[csf("approved_date")];
					$inserted_by=$val[csf("inserted_by")];
					$insert_date=$val[csf("insert_date")];
					$updated_by=$val[csf("updated_by")];
					$update_date=$val[csf("update_date")];
					$status_active=$val[csf("status_active")];
					$is_deleted=$val[csf("is_deleted")];
					$goods_rcv_status=$val[csf("goods_rcv_status")];
					$import_pi=$val[csf("import_pi")];
					$export_pi_id=$val[csf("export_pi_id")];
					$within_group=$val[csf("within_group")];
					$ready_to_approved=$val[csf("ready_to_approved")];
					$is_apply_last_update=$val[csf("is_apply_last_update")];
					$lc_group_no=$val[csf("lc_group_no")];
					$entry_form=$val[csf("entry_form")];
					$approval_user=$val[csf("approval_user")];
					$requested_by=$val[csf("requested_by")];
					$ref_closing_status=$val[csf("ref_closing_status")];
					$version=$val[csf("version")];
					$after_goods_source=$val[csf("after_goods_source")];
					$upcharge_breakdown=$val[csf("upcharge_breakdown")];
					$beneficiary=$val[csf("beneficiary")];
					



                    $data_array_mst .= "(".$id.",'".$approved_no."','".$mst_id."','".$item_category_id."','".$importer_id."','".$supplier_id."','".$pi_number."','".$pi_date."','".$last_shipment_date."','".$pi_validity_date."','".$currency_id."','".$source."','".$hs_code."','".$internal_file_no."','".$intendor_name."','".$pi_basis_id."','".$remarks."','".$total_amount."','".$upcharge."','".$discount."','".$net_total_amount."','".$approved."','".$approved_by."','".$approved_date."','".$inserted_by."','".$insert_date."','".$updated_by."','".$update_date."','".$status_active."','".$is_deleted."','".$goods_rcv_status."','".$import_pi."','".$export_pi_id."','".$within_group."','".$ready_to_approved."','".$is_apply_last_update."','".$lc_group_no."','".$entry_form."','".$approval_user."','".$requested_by."','".$ref_closing_status."','".$version."','".$after_goods_source."','".$upcharge_breakdown."','".$beneficiary."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                    $id++;

                }
                    
                
            }  
            $rID_mst=sql_insert("com_pi_master_details_history",$field_array_mst,$data_array_mst,0);


		
   
            $sql_dtls="select '', $approved_string_dtls as approved_dls,id,pi_id,work_order_no, work_order_id, 
        work_order_dtls_id, determination_id, item_prod_id,item_group, color_id, item_color,size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2,yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width,weight, uom,quantity,rate, amount, net_pi_rate,net_pi_amount, service_type, brand_supplier,inserted_by, insert_date, updated_by,update_date, status_active, is_deleted,item_description, gmts_item_id, embell_name,embell_type, lot_no, yarn_color,color_range, booking_without_order, country_id,staple_length, bale, bale_kg,test_for, test_item_id, remarks,fabric_source, item_category_id, entry_form,wo_qty_dtls_id, order_id, order_source,after_goods_source, is_sales, hs_code,booking_no, body_part_id from com_pi_item_details where pi_id in ($book_nos)";
			
            $field_array_dtls = "id, approved_no,dtls_id,pi_id,work_order_no, work_order_id, 
        work_order_dtls_id, determination_id, item_prod_id,item_group, color_id, item_color,size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2,yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width,weight, uom,quantity,rate, amount, net_pi_rate,net_pi_amount, service_type, brand_supplier,inserted_by, insert_date, updated_by,update_date, status_active, is_deleted,item_description, gmts_item_id, embell_name,embell_type, lot_no, yarn_color,color_range, booking_without_order, country_id,staple_length, bale, bale_kg,test_for, test_item_id, remarks,fabric_source, item_category_id, entry_form,wo_qty_dtls_id, order_id, order_source,after_goods_source, is_sales, hs_code,booking_no, body_part_id,t_inserted_by, t_insert_date, t_status_active, t_is_deleted";
   
    		$data_array_dtls = "";
            $dtlsid=return_next_id( "id", "com_pi_item_details_history", 1 ) ;
            $serial=$from;
            $check_pi_details_arr = return_library_array("select pi_id, pi_id from  com_pi_item_details_history", "pi_id", "pi_id");
          	foreach(sql_select($sql_dtls) as $val)
            {
                $pi_id=$val[csf("pi_id")];
                if(!$check_pi_details_arr[$pi_id])
                {
                    if ($data_array_dtls != "") $data_array_dtls .= ",";
					$approved_no=$val[csf("approved_dls")];
					$dtls_id=$val[csf("id")];
					$pi_id=$val[csf("pi_id")];
					$work_order_no=$val[csf("work_order_no")];
					$work_order_id=$val[csf("work_order_id")];
					$work_order_dtls_id=$val[csf("work_order_dtls_id")];
					$determination_id=$val[csf("determination_id")];
					$item_prod_id=$val[csf("item_prod_id")];
					$item_group=$val[csf("item_group")];
					$color_id=$val[csf("color_id")];
					$item_color=$val[csf("item_color")];
					$size_id=$val[csf("size_id")];
					$item_size=$val[csf("item_size")];
					$count_name=$val[csf("count_name")];
					$yarn_composition_item1=$val[csf("yarn_composition_item1")];
					$yarn_composition_percentage1=$val[csf("yarn_composition_percentage1")];
					$yarn_composition_item2=$val[csf("yarn_composition_item2")];
					$yarn_composition_percentage2=$val[csf("yarn_composition_percentage2")];
					$fabric_composition=$val[csf("fabric_composition")];
					$fabric_construction=$val[csf("fabric_construction")];
					$yarn_type=$val[csf("yarn_type")];
					$gsm=$val[csf("gsm")];
					$dia_width=$val[csf("dia_width")];
					$weight=$val[csf("weight")];
					$uom=$val[csf("uom")];
					$quantity=$val[csf("quantity")];
					$rate=$val[csf("rate")];
					$amount=$val[csf("amount")];
					$net_pi_rate=$val[csf("net_pi_rate")];
					$net_pi_amount=$val[csf("net_pi_amount")];
					$service_type=$val[csf("service_type")];
					$brand_supplier=$val[csf("brand_supplier")];
					$inserted_by=$val[csf("inserted_by")];
					$insert_date=$val[csf("insert_date")];
					$updated_by=$val[csf("updated_by")];
					$update_date=$val[csf("update_date")];
					$status_active=$val[csf("status_active")];
					$is_deleted=$val[csf("is_deleted")];
					$item_description=$val[csf("item_description")];
					$gmts_item_id=$val[csf("gmts_item_id")];
					$embell_name=$val[csf("embell_name")];
					$embell_type=$val[csf("embell_type")];
					$lot_no=$val[csf("lot_no")];
					$yarn_color=$val[csf("yarn_color")];
					$color_range=$val[csf("color_range")];
					$booking_without_order=$val[csf("booking_without_order")];
					$country_id=$val[csf("country_id")];
					$staple_length=$val[csf("staple_length")];
					$bale=$val[csf("bale")];
					$bale_kg=$val[csf("bale_kg")];
					$test_for=$val[csf("test_for")];
					$test_item_id=$val[csf("test_item_id")];
					$remarks=$val[csf("remarks")];
					$fabric_source=$val[csf("fabric_source")];
					$item_category_id=$val[csf("item_category_id")];
					$entry_form=$val[csf("entry_form")];
					$wo_qty_dtls_id=$val[csf("wo_qty_dtls_id")];
					$order_id=$val[csf("order_id")];
					$order_source=$val[csf("order_source")];
					$after_goods_source=$val[csf("after_goods_source")];
					$is_sales=$val[csf("is_sales")];
					$hs_code=$val[csf("hs_code")];
					$booking_no=$val[csf("booking_no")];
					$body_part_id=$val[csf("body_part_id")];

                    $data_array_dtls .= "(".$dtlsid.",'".$approved_no."','".$dtls_id."','".$pi_id."','".$work_order_no."','".$work_order_id."','".$work_order_dtls_id."','".$determination_id."','".$item_prod_id."','".$item_group."','".$color_id."','".$item_color."','".$size_id."','".$item_size."','".$count_name."','".$yarn_composition_item1."','".$yarn_composition_percentage1."','".$yarn_composition_item2."','".$yarn_composition_percentage2."','".$fabric_composition."','".$fabric_construction."','".$yarn_type."','".$gsm."','".$dia_width."','".$weight."','".$uom."','".$quantity."','".$rate."','".$amount."','".$net_pi_rate."','".$net_pi_amount."','".$service_type."','".$brand_supplier."','".$inserted_by."','".$insert_date."','".$updated_by."','".$update_date."','".$status_active."','".$is_deleted."','".$item_description."','".$gmts_item_id."','".$embell_name."','".$embell_type."','".$lot_no."','".$yarn_color."','".$color_range."','".$booking_without_order."','".$country_id."','".$staple_length."','".$bale."','".$bale_kg."','".$test_for."','".$test_item_id."','".$remarks."','".$fabric_source."','".$item_category_id."','".$entry_form."','".$wo_qty_dtls_id."','".$order_id."','".$order_source."','".$after_goods_source."','".$is_sales."','".$hs_code."','".$booking_no."','".$body_part_id."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                    $dtlsid++;

                }
            }  
            $rID_dtls=sql_insert("com_pi_item_details_history",$field_array_dtls,$data_array_dtls,0);
		}
		
        /*$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
        $rID=sql_multirow_update("com_pi_master_details","approved*approved_by*approved_date",$data,"id",$booking_ids,1);        
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_up="approved*approved_by*approved_date";
		$rID=execute_query(bulk_update_sql_statement( "com_pi_master_details", "id", $field_array_up, $data_array_up, $sys_id_up_array ));
        if($rID) $flag=1; else $flag=0;
		
		
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=27 and mst_id in ($booking_ids)";
        $rIDapp=execute_query($query,1);
        if($flag==1) 
        {
            if($rIDapp) $flag=1; else $flag=0; 
        }

		
		
		$rID2 = sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
       			
		if($flag==1) $msg='19'; else $msg='21';
		
		if($flag==1)
		{
			auto_approved(array(company_id=>$cbo_company_name,app_necessity_page_id=>18,mst_table=>'com_pi_master_details',sys_id=>$booking_ids,approval_by=>$user_id_approval));
		}
		
		
		
	}
	else if($approval_type == 1)
	{
		$booking_ids_all=explode(",",$booking_ids);

		$booking_ids=''; $app_ids='';

		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];

			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		
		$next_user_app = sql_select("select id from approval_history where mst_id in ($booking_ids) and entry_form=27 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "16**unapproved"; 
            disconnect($con);
			die;
		}
		
		$rID=sql_multirow_update("com_pi_master_details","approved*ready_to_approved","0*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		$query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=27 and mst_id in ($booking_ids)";
		$rID2 = execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		 
        $data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
        $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response = $booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=27  and mst_id in ($booking_ids) ";
		
		$nameArray=sql_select($sqlBookinghistory); $bookidArr=array(); $approval_id_arr=array();
		foreach ($nameArray as $row)
		{
			$bookidArr[$row[csf('mst_id')]]=$row[csf('mst_id')];
			$approval_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		
		$appBookNoId=implode(",",$bookidArr);
		$approval_ids=implode(",",$approval_id_arr);

		
		$rID=sql_multirow_update("com_pi_master_details","approved*ready_to_approved","0*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=27 and current_approval_status=1 and id in ($approval_ids)";
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		
		$response=$booking_ids;
		if($flag==1) $msg='50'; else $msg='51';
	}
	
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
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

            # silver line xlsx file download but not open use this two line
            // ob_end_clean();
            // header("Content-Type: {$mime}");
            
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
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
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











?>