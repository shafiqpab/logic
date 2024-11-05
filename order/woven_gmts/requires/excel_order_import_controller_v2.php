<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
$user_id=$_SESSION['logic_erp']['user_id'];
//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id";

$location_id = $userCredential[0][csf('location_id')];
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$location_credential_cond=""; $userbrand_idCond="";
if($userbrand_id==0) $userbrand_id="";

if ($location_id) {
    $location_credential_cond = " and id in($location_id)";
}

if ($userbrand_id!='') {
    $userbrand_idCond = " and id in ( $userbrand_id)";
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Location-", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_brand', 'brand_td');" );   
	exit();	 
} 

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 60, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	//echo "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 60, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Team Member-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Fac Merchent-", $selected, "" );
	exit();
}

if($action=="load_drop_down_code")
{
	$ex_data=explode('__',$data);
	
	$code_arr=return_library_array("select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$ex_data[0]' and status_active=1 and is_deleted=0 order by ultimate_country_code","id","ultimate_country_code");
	echo create_drop_down( "cboCodeId_$ex_data[1]", 95,$code_arr, "", 1, "-Code-", $selected,"fnc_country_data_load( 2, '".$ex_data[1]."',this.value)" );
	exit();
}

if($action=="load_drop_down_countryCode")
{
	$ex_data=explode('__',$data);
	
	$code_arr=return_library_array("select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$ex_data[0]' and status_active=1 and is_deleted=0 order by ultimate_country_code","id","ultimate_country_code");
	echo create_drop_down( "cboCountryCode_$ex_data[1]", 95,$code_arr, "", 1, "-Country Code-", $selected,"fnc_country_data_load( 4, '".$ex_data[1]."',this.value)" );
	exit();
}

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, copy_quotation, publish_shipment_date, cost_control_source from variable_order_tracking where company_name=$data and variable_list in (20,47,53) and status_active=1 and is_deleted=0 order by variable_list ASC");//and variable_list in (14,20,23,25,32,33,44,45,47,53)
	$tna_integrated=0; $copy_quotation=0; $set_smv_id=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; $cost_control_source=0; $color_from_lib=0;
 	foreach($sql_result as $result)
	{
		//if($result[csf('variable_list')]==14) $tna_integrated=$result[csf('tna_integrated')];
		if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		/*else if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
		else if($result[csf('variable_list')]==25) $publish_shipment_date=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==32) $po_update_period=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==45) $excut_source=$result[csf('excut_source')];*/
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
	}
	//echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source."_".$cost_control_source."_".$set_smv_id."_".$color_from_lib;
	echo $copy_quotation."_".$set_smv_id."_".$cost_control_source;
 	exit();
}

if($action=="open_smv_list")
{
	echo load_html_head_contents("WS SMV Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$item_id=$item_id;
	$style_id=$txt_style_ref;
	$set_smv_id=$set_smv_id;
	$cbo_buyer_name=$cbo_buyer_name;
	$cbo_company_name=$cbo_company_name;
	
	//echo $cbo_company_name;
	?>
	<script type="text/javascript">
      function js_set_value(id)
      { 	//alert(id);
		  document.getElementById('selected_smv').value=id;
		  parent.emailwindow.hide();
      }
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                    <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                &nbsp;</th>
            </thead>
            <tr>
                <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value, 'create_item_smv_search_list_view', 'search_div', 'excel_order_import_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
        </table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=="create_item_smv_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	
	$ex_style=explode("***",$style);
	$styleRefNo="";
	foreach($ex_style as $styleref)
	{
		if($styleRefNo=="") $styleRefNo="'".$styleref."'"; else $styleRefNo.=",'".$styleref."'";
	}

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref in ($styleRefNo)";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?
	/*$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where 0=0 $gmts_item_con2  order by a.id Desc";
	$result = sql_select($sewing_sql);
	foreach($result as $row)
	{
		$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
		$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
	}*/
	// print_r($code_smv_arr);b.lib_sewing_id
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.approved=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $gmts_item_con $style_con $buyer_id_con
	order by a.id DESC";
	//echo $sql;

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
		$smv_dtls_arr[$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr[$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('extention_no')]]['system_no'].=$row[csf('system_no')].',';
		//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$smv_dtls_arr[$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		//echo $row[csf('operator_smv')].'<br>'.$row[csf('helper_smv')].'<br>';

		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_sewing_arr[8]);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="160">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $ext_no=>$arrdata)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
			$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

			$finish_smv=$cut_smv=$sewing_smv=0;

			$sys_id=rtrim($arrdata['id'],',');
			$ids=array_filter(array_unique(explode(",",$sys_id)));
			//print_r($ids);
			$id_str=""; $k=0;
			foreach($ids as $idstr)
			{
				if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;

				foreach($lib_sewing_ids as $lsid)
				{
					$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
					$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
					$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
				}
				$k++;
			}

			$system_no=rtrim($arrdata['system_no'],',');
			$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));

			$finish_smv=$finish_smv/$k;
			$cut_smv=$cut_smv/$k;
			$sewing_smv=$sewing_smv/$k;

			$data=$sewing_smv."_".$id_str;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i;//.'='.$k ?></td>
                <td width="120" style="word-break:break-all"><? echo $system_no; ?></td>
                <td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
                <td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
			</tr>
			<?
			$i++;
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="populate_job_data_form")
{
	$compnay_id=$location_name=$buyer_name=$style_owner=$product_dept=$currency_id=$season_matrix=$product_category=$team_leader=$dealing_marchant=$factory_marchant=$order_uom=$gmts_item_id=$packing=0; $set_smv=''; $is_disable=0;
	if($data!="")
	{
		$sql_job=sql_select("Select company_name, location_name, buyer_name, style_owner, product_dept, currency_id, season_buyer_wise, product_category, team_leader, dealing_marchant, factory_marchant, order_uom, gmts_item_id, set_smv, packing from wo_po_details_master where job_no='$data' and status_active=1");
		//echo "Select company_name, location_name, buyer_name, style_owner, product_dept, currency_id, season_buyer_wise, product_category, team_leader, dealing_marchant, factory_marchant, order_uom, gmts_item_id, set_smv, packing from wo_po_details_master where job_no='$data' and status_active=1";
		
		echo "$('#cbo_company_id').val('".$sql_job[0][csf('company_name')]."');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('company_name')].", 'load_drop_down_location', 'location_td');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('company_name')].", 'load_drop_down_buyer', 'buyer_td');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('buyer_name')].", 'load_drop_down_season', 'season_td');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('buyer_name')].", 'load_drop_down_brand', 'brand_td');\n";
		
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('team_leader')].", 'load_drop_down_dealing_merchant', 'div_marchant');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', ".$sql_job[0][csf('team_leader')].", 'load_drop_down_factory_merchant', 'div_marchant_factory');\n";
		
		echo "$('#cbo_location_id').val('".$sql_job[0][csf('location_name')]."');\n";
		echo "$('#cbo_buyer_id').val('".$sql_job[0][csf('buyer_name')]."');\n";
		echo "$('#cbo_style_owner_id').val('".$sql_job[0][csf('style_owner')]."');\n";
		echo "$('#cbo_product_department').val('".$sql_job[0][csf('product_dept')]."');\n";
		echo "$('#cbo_currercy_id').val('".$sql_job[0][csf('currency_id')]."');\n";
		
		echo "$('#cbo_season_id').val('".$sql_job[0][csf('season_buyer_wise')]."');\n";
		echo "$('#cbo_prod_catgory').val('".$sql_job[0][csf('product_category')]."');\n";
		echo "$('#cbo_team_leader').val('".$sql_job[0][csf('team_leader')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$sql_job[0][csf('dealing_marchant')]."');\n";
		
		echo "$('#cbo_factory_merchant').val('".$sql_job[0][csf('factory_marchant')]."');\n";
		echo "$('#cbo_order_uom').val('".$sql_job[0][csf('order_uom')]."');\n";
		echo "$('#cbo_gmtsItem_id').val('".$sql_job[0][csf('gmts_item_id')]."');\n";
		echo "$('#cbo_packing').val('".$sql_job[0][csf('packing')]."');\n";
		echo "$('#tot_smv_qty').val('".$sql_job[0][csf('set_smv')]."');\n";
		
		if(count($sql_job)>0) 
		{
			echo "disable_enable_fields('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_style_owner_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_order_uom*cbo_gmtsItem_id*cbo_packing*tot_smv_qty',1);\n";
		}
		
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$flag=1;
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//table lock here 	 
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		//echo '0**'.$style_count; die;
		/*$code_arr=array();
		$code_sql=sql_select("select id, ultimate_country_code, country_id from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code");
		foreach($code_sql as $row)
		{
			$code_arr[trim($row[csf('ultimate_country_code')])]['code']=$row[csf('country_id')];
			$code_arr[trim($row[csf('ultimate_country_code')])]['id']=$row[csf('id')];
		}
		unset($code_sql);*/
		
		$country_arr=return_library_array("select id, country_name from lib_country order by country_name","country_name","id");
		
		$prev_po_arr=array();
		if(str_replace("'","",$txt_job_no)!="")
		{
			$sql_po=sql_select("select po_number from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0");
			foreach($sql_po as $row)
			{
				$prev_po_arr[$row[csf('po_number')]]=$row[csf('po_number')];
			}
		}
		unset($sql_po);
		/*if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_buyer_wise=$cbo_season_id";
		if(str_replace("'","",$txt_repeat_no)=="")
		{
			$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=$cbo_company_id and buyer_name=$cbo_buyer_id and style_ref_no=$txt_style_ref $season_cond");
			
			if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
			else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
		}
		else
		{
			$repeat_no=str_replace("'","",$txt_repeat_no);
		}*/
		
		$stylePOStatusArr=array(); $selectedPreJobArr=array(); $jobStr=""; $sty=1;
		foreach($_SESSION['excel'] as $style_ref=>$order_data)
		{
			$styleRef = "styleRef_".$sty;
			
			$st=1; $pn=1; $count=1; 
			foreach($order_data as $order_no=>$color_size_data)
			{
				$poNo = "poNo_".$sty.'_'.$pn;
				$cboOrderStatus = "cboOrderStatus_".$sty.'_'.$pn;
				$txtpoRemarks = "txtpoRemarks_".$sty.'_'.$pn;
				$stylePOStatusArr[$$styleRef][$$poNo]['postatus']=$$cboOrderStatus;
				$stylePOStatusArr[$$styleRef][$$poNo]['poremarks']=$$txtpoRemarks;
				$pn++;
			}
			$sty++;
		}
		
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', '', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_id and $date_cond=".date('Y',time())." order by id DESC", "job_no_prefix", "job_no_prefix_num" ));
		$id=return_next_id("id", "wo_po_details_master", 1);
		$job_id=$id;
		
		$field_job="id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, style_owner, location_name, style_ref_no, style_description, quotation_id, product_dept, currency_id, season_year, season_buyer_wise, brand_id, order_repeat_no, product_category, team_leader, dealing_marchant, factory_marchant, order_uom, gmts_item_id, set_smv, packing, ship_mode, set_break_down, total_set_qnty, is_excel, is_deleted, status_active, inserted_by, insert_date";
		
		$idSet=return_next_id( "id", "wo_po_details_mas_set_details", 1) ;
		$field_set="id, job_id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		
		$field_po="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, unit_price, details_remarks, packing, matrix_type, t_year, t_month, file_no, inserted_by, insert_date, is_deleted, status_active";
		
		$idPo=return_next_id("id", "wo_po_break_down", 1);
		
		$field_colSiz="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, inserted_by, insert_date";
		
		$idColSiz=return_next_id( "id", "wo_po_color_size_breakdown", 1);
		
		$set_breck_down='';
		$set_breck_down=str_replace("'","",$cbo_gmtsItem_id).'_'.'1'.'_'.str_replace("'","",$tot_smv_qty).'_'.str_replace("'","",$tot_smv_qty).'_'.'0'.'_'.'2'.'_'.str_replace("'","",$hid_ws_id);
		$add_job=0; $data_job=''; $add_set=0; $data_set=''; $all_job_style=''; $add_po=0; $data_po=""; $add_colSiz=0; $data_colSiz='';
		
		//echo $idPo."=";
		//echo "10**";
		$str=time(); $ready_to_save=1;
		$i=1; $sty=1; $st_name=""; $job_style_order_arr=array();
		foreach($_SESSION['excel'] as $style_ref=>$order_data)
		{
			$st=1; $pn=1; $count=1;
			foreach($order_data as $order_no=>$color_size_data)
			{
				$p=1; $ctpn=1; 
				foreach($color_size_data as $color_val=>$size_data)
				{
					$s=1;
					foreach($size_data as $size_val=>$extra_data)
					{
						foreach($extra_data as $ex_val=>$sizeqty)
						{
							$poRemarks='';
							$ex_data=explode('__',$ex_val);
							
							$pubshipdate=$shipdate=$countryName=$po_avg_rate=0; $country_qty=0; $country_amt=0; $countryShipDate=""; $planCutQty=0;
							
							$style_des=$ex_data[0];
							$recDate=$ex_data[7];//date("d-m-Y");
							$pubshipdate=$ex_data[1];
							$shipDate=$ex_data[2];
							$countryName=$ex_data[3];
							$countryShipDate=$ex_data[4];
							$countryRate=number_format($ex_data[5],4,'.','');
							$exPer=$ex_data[6]*1;
							
							//$poRemarks='';
							//if(trim($ex_data[5])=="") $ex_data[5]=$ex_data[6];
							$countryQty=$sizeqty;
							
							if($exPer=="") $exPer=0;
							if($exPer==0) $planCutQty=$countryQty; else $planCutQty=($countryQty*($exPer/100))+$countryQty;
							$planCutQty=number_format($planCutQty,0,'.','');
							
							$cboCodename=0;
							$cboCountryCode=0;
							$countryAmt=number_format($countryQty*$countryRate,4,'.','');
							
							$cboDeliveryCountry=$country_arr[trim($countryName)];//"cboDeliveryCountry_".$i.'_'.$j.'_'.$k;
							$cboCountryId=0;//"cboCountryId_".$i.'_'.$j.'_'.$k;
							$codeId=0;
							$countryCode=0;
							//echo $cboCodename.'=='.$cboDeliveryCountry.'<br>';
							if($cboDeliveryCountry=="" || $cboDeliveryCountry==0) $ready_to_save=0;
							
							if(str_replace("'","",$txt_job_no)=="")
							{
								if($st==1) 
								{ 
									if($z==1)
									{
										$new_job_no;
										$new_job_no_arr[$style_ref]=$new_job_no;
										$z++;
									}
									else
									{
										$new_job[0]=$new_job_no[1].(str_pad($new_job_no[2]+$y,5,0,STR_PAD_LEFT));
										$new_job[1]=$new_job_no[1];
										$new_job[2]=$new_job_no[2]+$y;
										$new_job_no_arr[$style_ref]=$new_job;
										$y++;
									}
									
									if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_buyer_wise=$cbo_season_id";
									
									$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=".$cbo_company_id." and buyer_name=".$cbo_buyer_id." and style_ref_no='".$style_ref."' $season_cond");
				
									if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
									else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
									
									if($add_job!=0) $data_job.=",";
									
									
									$data_job_arr[$id]="(".$id.",3,'".$new_job_no_arr[$style_ref][0]."','".$new_job_no_arr[$style_ref][1]."','".$new_job_no_arr[$style_ref][2]."',".$cbo_company_id.",".$cbo_buyer_id.",".$cbo_style_owner_id.",".$cbo_location_id.",'".$style_ref."','".$style_des."',".$txt_quotation_id.",".$cbo_product_department.",".$cbo_currercy_id.",".$cbo_season_year.",".$cbo_season_id.",".$cbo_brand_id.",'".$repeat_no."',".$cbo_prod_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_factory_merchant.",".$cbo_order_uom.",".$cbo_gmtsItem_id.",".$tot_smv_qty.",".$cbo_packing.",".$cbo_ship_mode.",'".$set_breck_down."',1,1,0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									$job_id=$id;
									$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
									for($c=0; $c < count($set_breck_down_array);$c++)
									{
										$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
										
										if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
											echo "SMV**";
											die;
										}
										
										if ($add_set!=0) $data_set .=",";
										$data_set_arr[$idSet] ="(".$idSet.",'".$job_id."','".$new_job_no_arr[$style_ref][0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[6]."')";
										$add_set++;
										$idSet=$idSet+1;
									}
									$st++;
									
									
								} 
							}
							else
							{
								$new_job_no_arr[$style_ref][0]=str_replace("'","",$txt_job_no);
							}
							
							if($p==1) 
							{
								if($db_type==0)
								{
									$receive_date=change_date_format($recDate, "Y-m-d", "-",1);
									$pubshiment_date=change_date_format($pubshipdate, "Y-m-d", "-",1);
									$shiment_date=change_date_format($shipDate, "Y-m-d", "-",1);
								}
								else
								{
									$receive_date=change_date_format($recDate, "d-M-y", "-",1);
									$pubshiment_date=change_date_format($pubshipdate, "d-M-y", "-",1);
									$shiment_date=change_date_format($shipDate, "d-M-y", "-",1);
								}
								
								$msg_dup="Duplicate Data Found, Please check again.";
								if($prev_po_arr[$order_no]!="")
								{
									check_table_status( $_SESSION['menu_id'],0);
									echo "11**".$msg_dup."**".$order_no; die;
								}
								
								$postatus=1; $poremarks="";
								$postatus=$stylePOStatusArr[$style_ref][$order_no]['postatus'];
								$poremarks=$stylePOStatusArr[$style_ref][$order_no]['poremarks'];
								
								$data_po_arr[$idPo]="(".$idPo.",'".$job_id."','".$new_job_no_arr[$style_ref][0]."','".$postatus."','".$order_no."','".$receive_date."','".$pubshiment_date."','".$shiment_date."','".$receive_date."',0,'".$poremarks."',".$cbo_packing.",1,'".date("Y",strtotime(str_replace("'","",$shiment_date)))."','".date("m",strtotime(str_replace("'","",$shiment_date)))."',".$txt_sclc.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
								$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$idPo]=$set_breck_down;
								$p++; 
							} 
							
							if(str_replace("'","",$color_val)!="")
							{ 
								if (!in_array(str_replace("'","",$color_val),$new_array_color))
								{
									$color_id = return_id( str_replace("'","",$color_val), $color_library, "lib_color", "id,color_name","401");  
									$new_array_color[$color_id]=str_replace("'","",$color_val);
								}
								else $color_id =  array_search(str_replace("'","",$color_val), $new_array_color); 
							}
							else $color_id=0;
							
							if(str_replace("'","",$size_val)!="")
							{
								if (!in_array(str_replace("'","",$size_val),$new_array_size))
								{
								  $size_id = return_id( str_replace("'","",$size_val), $size_library, "lib_size", "id,size_name","401");  
								  $new_array_size[$size_id]=str_replace("'","",$size_val);
								}
								else $size_id =  array_search(str_replace("'","",$size_val), $new_array_size); 
							}
							else $size_id=0;
							
							if($db_type==0) $countryShipDate=change_date_format($countryShipDate, "Y-m-d", "-",1);
							else $countryShipDate=change_date_format($countryShipDate, "d-M-y", "-",1);
							
							if($add_colSiz!=0) $data_colSiz.=",";
							
							$article_number="";
							$article_number="no article";
							
							$break_down[$idColSiz]="(".$idColSiz.",'".$job_id."',".$idPo.",'".$new_job_no_arr[$style_ref][0]."',0,0,0,".$cbo_gmtsItem_id.",'".$cboDeliveryCountry."','".$codeId."','".$cboCountryId."','".$countryCode."','".$countryShipDate."','".$color_id."','".$size_id."','".$countryQty."','".$countryRate."','".$exPer."','".$article_number."','".$countryAmt."','".$planCutQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
							$idColSiz=$idColSiz+1;
							$add_colSiz++;
							$i++; $ctpn++;
						}
					}
				}
				$idPo=$idPo+1;
				$add_po++;
				$pn++;
			}
			$id=$id+1;
			$job_id=$id;
			$add_job++;
			if($all_job_style=="") $all_job_style=$new_job_no_arr[$style_ref][0].'__'.$style_ref; else $all_job_style.=','.$new_job_no_arr[$style_ref][0].'__'.$style_ref;
			//update_color_size_sequence("'".$new_job_no_arr[$style_ref][0]."'");
			$sty++;
		}
		//die;
		unset($_SESSION["excel"]);
		if($ready_to_save==0) { check_table_status( $_SESSION['menu_id'],0); echo "5**Please check Country and Upload the file again."; disconnect($con);die; }
		
		//print_r($break_down);die;
		//echo "10**INSERT INTO wo_po_break_down (".$field_po.") VALUES ".$data_po_arr; die;
		
		//print_r($new_array_color); die;
		 //echo $data_po; die;
		//echo "10**INSERT INTO wo_po_details_master (".$field_set.") VALUES ".$data_set; die;
		/*oci_commit($con);
		
		$break_down_chnk=array_chunk($break_down,900);
		foreach( $break_down_chnk as $rows)
		{
			echo $rID3 =sql_insert("wo_po_color_size_breakdown",$field_colSiz, implode(",",$rows),0);
			//if($rID3)  $flag=1; else $flag=0;
			die;
		}
		 
		oci_commit($con);  
		echo "TEST"; 
		die;*/
		$roll_back_msg="Data not save.";
		$time=time();
		oci_commit($con);
		$data_job=array_chunk($data_job_arr,1);
		foreach( $data_job as $jobRows)
		{
			//echo "10**INSERT INTO wo_po_details_master (".$field_job.") VALUES ".implode(",",$jobRows); die;
			$rID.=sql_insert("wo_po_details_master",$field_job,implode(",",$jobRows),0);
			if($rID==1) $flag=1; //else $flag=0;
			else if($rID==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**"; disconnect($con);die;
			}
		}//die;
		//if($rID==1) $flag=1; else $flag=0;
		
		$data_set=array_chunk($data_set_arr,1);
		foreach( $data_set as $setRows)
		{
			$rID1.=sql_insert("wo_po_details_mas_set_details",$field_set,implode(",",$setRows),0);
			if($rID1==1) $flag=1; //else $flag=0;
			else if($rID1==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg;disconnect($con); die;
			}
		}
		//if($rID1) $flag=1; else $flag=0;
		
		$po_data=array_chunk($data_po_arr,1);
		foreach( $po_data as $poRows)
		{
			//echo "10**INSERT INTO wo_po_break_down (".$field_po.") VALUES ".implode(",",$poRows); 
			$rID2.=sql_insert("wo_po_break_down",$field_po,implode(",",$poRows),0);
			if($rID2==1) $flag=1; //else $flag=0;
			else if($rID2==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; disconnect($con);die;
			}
		}//die;
		//if($rID2) $flag=1; else $flag=0;
		//oci_commit($con); 
		$break_down_chnk=array_chunk($break_down,20);
		foreach( $break_down_chnk as $rows)
		{
			$rID3.=sql_insert("wo_po_color_size_breakdown",$field_colSiz, implode(",",$rows),0);
			if($rID3==1) $flag=1; //else $flag=0;
			else if($rID3==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; disconnect($con);die;
			}
		}
		//die;
		//if($rID3)  $flag=1; else $flag=0;
		 
		//oci_commit($con);  
		//unset($_SESSION['excel']);
		
		//$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$idPo]=$set_breck_down;
		$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		foreach ( $job_style_order_arr as $job_no=>$job_data )
		{
			update_color_size_sequence("'".$job_no."'",1);
			update_cost_sheet("'".$job_no."'");
			foreach($job_data as $poId=>$setData)
			{
				$exSetData=explode('__',$setData);
				//echo "'".$job_no."'".'=='.$poId.'=='.$setData;
				job_order_qty_update("'".$job_no."'",$poId,$exSetData,1);
				
				$sam=1;
				
				//$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
				$sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$cbo_buyer_id order by sequ");
				$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 
				//echo "select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id";	 die;	
				$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$job_no' and a.id=b.po_break_down_id and b.po_break_down_id='$poId' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
				//print_r($data_array_sample);
				foreach($sample_tag as $sample_tag_row)
				{
					foreach ( $data_array_sample as $row_sam1 )
					{
						$dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst='$job_no' and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0 and (entry_form_id is null or entry_form_id=0)");
						list($idsm)=$dup_data;
						if( $idsm[csf('id')] =='')
						{
							if ($sam!=1) $data_array_sm .=",";
							$data_array_sm .="(".$id_sm.",".$update_id.",".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
							$id_sm=$id_sm+1;
							$sam=$sam+1;
						}
					}
				}
				
				if($data_array_sm !='')
				{
					$rIDsm=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
					oci_commit($con); 
					//if($rIDsm) $flag=1; else $flag=0;
				}
				//============================================================================================
				$lap=1;
				
				// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
				$field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted"; 		
				$data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$job_no' and a.id=b.po_break_down_id and  b.po_break_down_id='$poId' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
				foreach ( $data_array_lapdip as $row_lap1 )
				{
					$dup_lap=sql_select("select id from wo_po_lapdip_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_lap1[csf('po_id')]." and color_name_id=".$row_lap1[csf('color_number_id')]."  and status_active=1 and is_deleted=0");
					list($idlap)=$dup_lap;
					if( $idlap[csf('id')] =='')
					{
						if ($lap!=1) $data_array_lap .=",";
						$data_array_lap .="(".$id_lap.",".$update_id.",".$row_lap1[csf('po_id')].",".$row_lap1[csf('color_number_id')].",1,0)";
						$id_lap=$id_lap+1;
						$lap=$lap+1;
					}
				}
				if($data_array_lap !='')
				{
					$rIDlab=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
					oci_commit($con); 
					//if($rIDlab) $flag=1; else $flag=0;
				}
			}
		}
		
		//unset ($_SESSION['excel']);
		//release lock table
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$all_job_style);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";//.str_replace("'",'',$all_job_style)
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$all_job_style);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";//.str_replace("'",'',$all_job_style)
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Quotation Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="70">Quotation ID</th>
                <th width="100">Style Ref.</th>
                <th width="180">Delv. Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_id">
                <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down('order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
            </td>
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+'<? echo $txt_job_no; ?>', 'create_quotation_id_list_view', 'search_div', 'excel_order_import_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_quotation_id_list_view")
{
	//echo $data;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";// else { echo "Please Select Buyer First."; die; }
	//echo $data[1];
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}

	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($data[7]!='') $inset_date=return_field_value("insert_date", "wo_po_details_master", "job_no='$data[7]'");
	else $inset_date=$pc_date_time;

	$ex_insert_date=explode(" ",$inset_date);
	if($db_type==0) $date_change=change_date_format($ex_insert_date[0],"yyyy-mm-dd");
	else if($db_type==2) $date_change=change_date_format($ex_insert_date[0], "", "",1);
	
	$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
	$app_nessity=2; $validate_page=0; $allow_partial=2;
	foreach($sql as $row){
		$app_nessity=$row[csf('approval_need')];
		$validate_page=$row[csf('validate_page')];
		$allow_partial=$row[csf('allow_partial')];
	}
	
	$quotAppCond="";
	if($validate_page==1)
	{
		if($app_nessity==1)
		{
			 if($allow_partial==1) $quotAppCond=" and a.approved in (1,3)";
			 else $quotAppCond=" and a.approved=1";
		}
	}

	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select a.id, a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.pord_dept, a.offer_qnty, a.est_ship_date from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and b.confirm_date is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in (select quotation_id from wo_po_details_master where status_active=1 and is_deleted=0 and quotation_id!=0) $company $buyer_cond $style_cond $quotation_id_cond $quotAppCond $company order by a.id DESC";
	//echo $sql; die;

	echo create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "60,120,100,100,140,100,80","850","260",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_quotation")
{
	$ex_data=explode("_",$data);
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, b.price_with_commn_pcs,a.dealing_merchant from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id='$ex_data[0]'");
	foreach ($data_array as $row)
	{
		$team_id=return_field_value("team_id", "lib_mkt_team_member_info", "id='".$row[csf("dealing_merchant")]."'");
		
		//echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		//echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n";
		//echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "disable_enable_fields('cbo_company_name',1);\n";
		echo "document.getElementById('cbo_buyer_id').value = '".$row[csf("buyer_id")]."';\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$team_id."', 'load_drop_down_dealing_merchant', 'div_marchant');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$team_id."', 'load_drop_down_factory_merchant', 'div_marchant_factory');\n";
		
		echo "document.getElementById('cbo_team_leader').value = '".$team_id."';\n";
		
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_merchant")]."';\n";
		//echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		//echo "document.getElementById('txt_style_description').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("pord_dept")]."';\n";
		//echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		//echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";
		//echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";
		//echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		//echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		//echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		//echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";

		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		//echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		//echo "document.getElementById('txt_quotation_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "location_select();\n";
	}
	exit();
}

if ($action=="qc_id_popup")
{
  	echo load_html_head_contents("Quick Costing Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="90">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="70">Cost Sheet No</th>
                <th width="100">Style Ref.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
			<td>
			<? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in (3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $cbo_company_name, "",1); 
			?> 
			</td>
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_qc_id_list_view', 'search_div', 'excel_order_import_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('excel_order_import_controller_v2', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td');
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_qc_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";//else { echo "Please Select Buyer First."; die; }
	//echo $data[0];
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}

	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$arr=array (0=>$company_arr,5=>$buyer_arr,8=>$pord_dept);

	$sql= "select a.id, a.company_id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, a.revise_no, a.option_id, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $style_cond $quotation_id_cond $company order by a.id DESC";
	//echo $sql;
	echo create_list_view("list_view", "Company Name, QC ID, Cost Sheet No, Rev., Opt., Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "70,50,70,40,40,100,100,100,80,80","850","280",0, $sql , "js_set_value", "id", "", 1, "company_id,0,0,0,0,buyer_id,0,0,department_id,0,0", $arr , "company_id,id,cost_sheet_no,revise_no,option_id,buyer_id,confirm_style,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,0,0,0,2,3') ;
	
	exit();
}

if($action=="populate_data_from_search_popup_qc")
{
	$data_array=sql_select("select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.style_ref, a.style_des, a.prod_dept, a.offer_qty, a.delivery_date, a.team_leader, a.dealing_marchant, b.confirm_style, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
	//echo "select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.style_ref, a.style_des, a.prod_dept, a.offer_qty, a.delivery_date, a.team_leader, a.dealing_marchant, b.confirm_style, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'";
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_id').value = '".$row[csf("buyer_id")]."';\n";
		//echo "document.getElementById('txt_style_ref').value = '".$row[csf("confirm_style")]."';\n";
		//echo "document.getElementById('txt_style_description').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("prod_dept")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$row[csf("team_leader")]."', 'load_drop_down_dealing_merchant', 'div_marchant');\n";
		echo "load_drop_down( 'requires/excel_order_import_controller_v2', '".$row[csf("team_leader")]."', 'load_drop_down_factory_merchant', 'div_marchant_factory');\n";
		
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_id")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("qc_no")]."';\n";
		//echo "document.getElementById('txt_avg_price').value = '".$row[csf("confirm_fob")]."';\n";
		//echo "document.getElementById('txt_quotation_price').value = '".$row[csf("confirm_fob")]."';\n";
		exit();
	}
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

function update_color_size_sequence2($txt_job_no)
{
	$sql_data=sql_select("select min(id) as id, color_number_id,min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 group by color_number_id order by id ");
	$color_order=1;
	foreach ($sql_data as $row)
	{
		$rID=execute_query( "update wo_po_color_size_breakdown set color_order=".$color_order." where  color_number_id =".$row[csf('color_number_id')]." and job_no_mst=$txt_job_no",0);
		$color_order++;
	}
	$sql_data1=sql_select("select min(id) as id, size_number_id,min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 group by size_number_id order by id");
	$size_order=1;
	foreach ($sql_data1 as $row1)
	{
		$rID=execute_query( "update wo_po_color_size_breakdown set size_order=".$size_order." where  size_number_id =".$row1[csf('size_number_id')]." and job_no_mst=$txt_job_no",0);
		$size_order++;	
	}
}

function job_order_qty_update($job_no,$po_id,$set_data,$order_status)
{
	$po_data_arr=array(); $job_data_arr=array(); $item_set_arr=array(); $item_ratio=0;
	//print_r($set_data);
	foreach($set_data as $exSet)
	{
		$exItemRatio=explode('_',$exSet);
		//$item_ratio_arr[$exItemRatio[0]]=$exItemRatio[1];
		$item_ratio+=$exItemRatio[1];
	}
	//echo "select po_break_down_id, item_number_id, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active=1 group by po_break_down_id, item_number_id";
	$data_array_se=sql_select("select po_break_down_id, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active=1 group by po_break_down_id");
	foreach($data_array_se as $row)
	{
		//$item_ratio=0; 
		$item_qty=0; $item_amt=0; $item_planCut=0;
		//$item_ratio=$item_ratio_arr[$row[csf('item_number_id')]];
		$item_qty=$row[csf('po_tot')]/$item_ratio;
		$item_amt=$row[csf('po_tot_price')];//*$item_ratio;
		$item_planCut=$row[csf('plan_cut')]/$item_ratio;
		$po_data_arr[$row[csf('po_break_down_id')]]['qty']+=$item_qty;
		$po_data_arr[$row[csf('po_break_down_id')]]['amt']+=$item_amt;
		$po_data_arr[$row[csf('po_break_down_id')]]['plan']+=$item_planCut;
		$job_data_arr['qty']+=$item_qty;
		$job_data_arr['amt']+=$item_amt;
	}
	//echo $item_ratio; die;
	//list($po_data)=$data_array_se;
	$set_qnty=str_replace("'","",$set_qnty);
	
	$job_qty=$job_data_arr['qty'];
	$job_amt=$job_data_arr['amt'];
	$poavgprice=number_format($job_amt/$job_qty,4);
	//echo $job_qty_set.'='.$job_amt_set.'='.$job_price; die;
	$field_array_job="job_quantity*avg_unit_price*total_price";
	$data_array_job="".$job_qty."*".$poavgprice."*".$job_amt."";
	//echo $field_array_job."****".$data_array_job;
	$po_qty=$po_data_arr[str_replace("'","",$po_id)]['qty'];
	$poavgprice_po=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4);
	
	$po_ex_per=number_format((($po_data_arr[str_replace("'","",$po_id)]['plan']-$po_qty)/$po_qty)*100,2);
	
	if(str_replace("'","",$cbo_order_status)==2)
	{
		$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut*original_po_qty*original_avg_price*doc_sheet_qty";
		$data_array_po="".$po_qty."*'".$poavgprice_po."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*'".$po_qty."'*'".$poavgprice_po."'*".$po_qty."";
	}
	else
	{
		$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut*doc_sheet_qty";
		$data_array_po="".$po_qty."*'".$poavgprice_po."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*".$po_qty."";
	}
	//echo $data_array_job."*".$data_array_po;
	$rID2=sql_update("wo_po_details_master",$field_array_job,$data_array_job,"job_no","".$job_no."",1);
	$rID3=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$po_id."",1);
	//exit();
}
?>