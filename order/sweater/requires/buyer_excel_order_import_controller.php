<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Location-", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "load_drop_down( 'requires/buyer_excel_order_import_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/buyer_excel_order_import_controller', this.value, 'load_drop_down_brand', 'brand_td'); " );   
	exit();	 
} 

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 60, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 60, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Team Member-", $selected, "" );
	exit();
}

/*if ($action=="load_drop_down_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 70, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Fac Merchent-", $selected, "" );
	exit();
}
*/
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
	$sql_result = sql_select("select variable_list, copy_quotation, publish_shipment_date from variable_order_tracking where company_name=$data and variable_list in (20,47) and status_active=1 and is_deleted=0 order by variable_list ASC");//and variable_list in (14,20,23,25,32,33,44,45,47,53)
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
		//else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
	}
	//echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source."_".$cost_control_source."_".$set_smv_id."_".$color_from_lib;
	echo $copy_quotation."_".$set_smv_id;
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value, 'create_item_smv_search_list_view', 'search_div', 'buyer_excel_order_import_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	$compnay_id=$location_name=$buyer_name=$style_owner=$product_dept=$currency_id=$season_matrix=$product_category=$team_leader=$dealing_marchant=$factory_marchant=$order_uom=$gmts_item_id=$inquiry_id=$packing=0; $set_smv=''; $is_disable=0;
	if($data!="")
	{
		$sql_job=sql_select("Select id, company_name, location_name, buyer_name, style_owner, product_dept, currency_id, season_buyer_wise, season_year, brand_id, product_category, team_leader, dealing_marchant, inquiry_id, order_uom, gmts_item_id, gauge, set_smv, packing from wo_po_details_master where job_no='$data' and status_active=1");
		//echo "Select company_name, location_name, buyer_name, style_owner, product_dept, currency_id, season_buyer_wise, product_category, team_leader, dealing_marchant, factory_marchant, order_uom, gmts_item_id, set_smv, packing from wo_po_details_master where job_no='$data' and status_active=1";
		
		echo "$('#cbo_company_id').val('".$sql_job[0][csf('company_name')]."');\n";
		echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('company_name')].", 'load_drop_down_location', 'location_td');\n";
		echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('company_name')].", 'load_drop_down_buyer', 'buyer_td');\n";
		echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('buyer_name')].", 'load_drop_down_season', 'season_td');\n";
		echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('buyer_name')].", 'load_drop_down_brand', 'brand_td');\n";
		
		echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('team_leader')].", 'load_drop_down_dealing_merchant', 'div_marchant');\n";
		//echo "load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('team_leader')].", 'load_drop_down_factory_merchant', 'div_marchant_factory');\n";
		
		echo "$('#cbo_location_id').val('".$sql_job[0][csf('location_name')]."');\n";
		echo "$('#cbo_buyer_id').val('".$sql_job[0][csf('buyer_name')]."');\n";
		echo "$('#cbo_style_owner_id').val('".$sql_job[0][csf('style_owner')]."');\n";
		echo "$('#cbo_product_department').val('".$sql_job[0][csf('product_dept')]."');\n";
		echo "$('#cbo_currercy_id').val('".$sql_job[0][csf('currency_id')]."');\n";
		
		echo "$('#cbo_season_id').val('".$sql_job[0][csf('season_buyer_wise')]."');\n";
		echo "$('#cbo_season_year').val('".$sql_job[0][csf('season_year')]."');\n";
		echo "$('#cbo_brand_id').val('".$sql_job[0][csf('brand_id')]."');\n";
		
		echo "$('#cbo_prod_catgory').val('".$sql_job[0][csf('product_category')]."');\n";
		echo "$('#cbo_team_leader').val('".$sql_job[0][csf('team_leader')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$sql_job[0][csf('dealing_marchant')]."');\n";
		
		$masterStyle="";
		if($sql_job[0][csf('inquiry_id')]>0)
		{
			$masterStyle=return_field_value("style_refernce", "wo_quotation_inquery", "id='".$sql_job[0][csf('inquiry_id')]."'");
		}
		
		echo "$('#txt_masterStyle').val('".$masterStyle."');\n";
		echo "$('#hid_inquiry_id').val('".$sql_job[0][csf('inquiry_id')]."');\n";
		echo "$('#cbo_order_uom').val('".$sql_job[0][csf('order_uom')]."');\n";
		echo "$('#cbo_gmtsItem_id').val('".$sql_job[0][csf('gmts_item_id')]."');\n";
		echo "$('#cbo_packing').val('".$sql_job[0][csf('packing')]."');\n";
		echo "$('#tot_smv_qty').val('".$sql_job[0][csf('set_smv')]."');\n";
		echo "$('#hid_job_id').val('".$sql_job[0][csf('id')]."');\n";
		echo "$('#cbo_gauge').val('".$sql_job[0][csf('gauge')]."');\n";
		
		if(count($sql_job)>0) 
		{
			echo "disable_enable_fields('cbo_company_id*cbo_location_id*txt_masterStyle*cbo_buyer_id*cbo_style_owner_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_order_uom*cbo_gmtsItem_id*cbo_packing*tot_smv_qty*cbo_gauge',1);\n";
		}
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	extract($_REQUEST);
	$flag=1;
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//table lock here 	 
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		//echo '0**'.$style_count; die;
		$code_arr=array(); $countryWiseCodeArr=array();
		$code_sql=sql_select("select id, ultimate_country_code, country_id from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code");
		foreach($code_sql as $row)
		{
			$code_arr[trim($row[csf('ultimate_country_code')])]['code']=$row[csf('country_id')];
			$code_arr[trim($row[csf('ultimate_country_code')])]['id']=$row[csf('id')];
			
			$countryWiseCodeArr[trim($row[csf('country_id')])][trim($row[csf('ultimate_country_code')])]['id']=$row[csf('id')];
		}
		unset($code_sql);
		
		$countryNameArr=return_library_array("select country_name, id from lib_country order by country_name","country_name","id");
		
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
		
		$buyer_format=str_replace("'","",$hid_buyer_format);
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', '', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_id and $date_cond=".date('Y',time())." order by id DESC", "job_no_prefix", "job_no_prefix_num" ));
		$id=return_next_id("id", "wo_po_details_master", 1);
		
		$field_job="id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, style_owner, location_name, style_ref_no, style_description, product_dept, currency_id, season_buyer_wise, season_year, brand_id, order_repeat_no, product_category, team_leader, dealing_marchant, inquiry_id, order_uom, gmts_item_id, set_smv, packing, set_break_down, total_set_qnty, gauge, is_excel, is_deleted, status_active, inserted_by, insert_date";
		
		$interid=return_next_id( "id", "wo_order_entry_internal_ref", 1) ;
		$field_internal="id, job_no, internal_ref, job_insert_date, insert_date";
		
		$idSet=return_next_id( "id", "wo_po_details_mas_set_details", 1) ;
		$field_set="id, job_id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		
		$field_po="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, unit_price, details_remarks, packing, matrix_type, t_year, t_month, grouping, sc_lc, inserted_by, insert_date, is_deleted, status_active";
		
		$idPo=return_next_id("id", "wo_po_break_down", 1);
		
		$field_array_up="order_quantity*order_rate*excess_cut_perc*order_total*plan_cut_qnty*updated_by*update_date";
		//$field_array_up="order_quantity";
		
		$field_colSiz="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, inserted_by, insert_date";//Confirm Qty
		$field_proj_colSiz="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, proj_qty, proj_amt, inserted_by, insert_date";//Projected Qty
		
		$idColSiz=return_next_id( "id", "wo_po_color_size_breakdown", 1);
		
		$set_breck_down='';
		$set_breck_down=str_replace("'","",$cbo_gmtsItem_id).'_'.'1'.'_'.str_replace("'","",$tot_smv_qty).'_'.str_replace("'","",$tot_smv_qty).'_'.'0'.'_'.'2'.'_'.str_replace("'","",$hid_ws_id);
		$add_job=0; $data_job=''; $add_set=0; $data_set=''; $all_job_style=''; $add_po=0; $data_po=""; $add_colSiz=0; $data_colSiz='';
		
		$stylePOStatusArr=array(); $selectedPreJobArr=array(); $jobStr=""; $sty=1;
		foreach($_SESSION['excel'] as $style_ref=>$order_data)
		{
			$styleRef = "styleRef_".$sty;
			
			$txtexcelStyle = "txtexcelStyle_".$sty;
			$txtprevJob = "txtprevJob_".$sty;
			if(str_replace("'","",$$txtprevJob)!="")
			{
				if($jobStr=="") $jobStr="'".str_replace("'","",$$txtprevJob)."'"; else $jobStr.=",'".str_replace("'","",$$txtprevJob)."'";
				$selectedPreJobArr[$$txtprevJob]=$$styleRef;
			}
			
			$st=1; $pn=1; $count=1;
			foreach($order_data as $order_no=>$color_size_data)
			{
				$poNo = "poNo_".$sty.'_'.$pn;
				$cboOrderStatus = "cboOrderStatus_".$sty.'_'.$pn;
				$stylePOStatusArr[$$styleRef][$$poNo]=$$cboOrderStatus;
				//echo $$styleRef.'__'.$$poNo.'__'.$$cboOrderStatus.'<br>';
				$pn++;
			}
			$sty++;
		}
		/*echo '<pre>';
		print_r($stylePOStatusArr); die;*/
		$updateJobArr=array(); $updatePoArr=array(); $updateColorSizeArr=array(); $job_style_order_arr=array();
		if($jobStr!="")
		{
			$sqlpocolsize="select a.id as jobid, a.job_no, a.style_ref_no, a.style_description, a.set_break_down, b.id as poid, b.is_confirmed, b.po_number, b.po_received_date, b.pub_shipment_date, b.shipment_date, b.factory_received_date, b.details_remarks, c.id as cid, c.country_id, c.code_id, c.ultimate_country_id, c.ul_country_code, c.country_ship_date, c.color_number_id, c.size_number_id, c.order_quantity, c.proj_qty, c.order_rate, c.excess_cut_perc, c.article_number, c.order_total, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no in ($jobStr)";
			//echo $sqlpocolsize;
			$sqlpocolsizedata=sql_select($sqlpocolsize); $a=array();
			foreach($sqlpocolsizedata as $row)
			{
				//echo $row[csf('poid')].'<br>';
				$prevStyleTag=$selectedPreJobArr[$row[csf('job_no')]];
				$updateJobArr[$prevStyleTag]['job']=$row[csf('job_no')];
				$updateJobArr[$prevStyleTag]['jobid']=$row[csf('jobid')];
				$updatePoArr[$prevStyleTag][$row[csf('po_number')]]=$row[csf('poid')];
				
				$updateColorSizeArr[$prevStyleTag][$row[csf('poid')]][$row[csf('country_id')]][$row[csf('code_id')]][change_date_format($row[csf('country_ship_date')], "d-M-y", "-",1)][$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('cid')];
				$job_style_order_arr[$row[csf('job_no')]][$row[csf('poid')]]=$row[csf('set_break_down')];
				if($row[csf('is_confirmed')]==2) //&& $row[csf('proj_qty')]>0
				{
					$projectedPoArr[$prevStyleTag]['poQty']+=$row[csf('proj_qty')];
					$projectedPoArr[$prevStyleTag]['cid'].=','.$row[csf('cid')];
					$cidwiseQtyArr[$row[csf('cid')]]=$row[csf('proj_qty')].'__'.$row[csf('excess_cut_perc')].'__'.$row[csf('order_rate')];
					//$proj[$style_ref]=0;
				}
			}
			unset($sqlpocolsizedata);
		}
		unset($sqlpocolsizedata);

		$str=time(); $ready_to_save=1; //echo "10**";
		$i=1; $sty=1; $st_name="";  $dataArrayUp=""; $counter=0;
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
						foreach($extra_data as $ex_val=>$experdata)
						{
							foreach($experdata as $exper=>$sizeqty)
							{
								$poRemarks='';
								$ex_data=explode('__',$ex_val);
								$style_des=$ex_data[0];
								$recDate=$ex_data[1];
								$shipDate=$ex_data[2];
								$countryRate=number_format($ex_data[3],2,'.','');
								$poRemarks=$ex_data[4];
								//$poRemarks='';
								if(trim($ex_data[5])=="") $ex_data[5]=$ex_data[6];
								$countryQty=$sizeqty;
								$cboCodename=$ex_data[5];
								
								if($buyer_format==1) 
								{
									$cboCountryCode=$ex_data[6];
									$cboDeliveryCountry=$code_arr[trim($cboCodename)]['code'];
									$cboCountryId=$code_arr[trim($cboCountryCode)]['code'];
									$codeId=$code_arr[trim($cboCodename)]['id'];
									$countryCode=$code_arr[trim($cboCountryCode)]['id'];
								}
								else if($buyer_format==2) 
								{
									$cboCountryCode=$ex_data[6];//Country Name
									$cboDeliveryCountry=$countryNameArr[trim($ex_data[6])];
									
									$cboCountryId=$countryNameArr[trim($cboCountryCode)]['code'];
									$codeId=$countryWiseCodeArr[$cboDeliveryCountry][trim($cboCodename)]['id'];
									$countryCode=$countryWiseCodeArr[$cboCountryId][trim($cboCountryCode)]['id'];
								}
								
								$countryAmt=number_format($countryQty*$countryRate,2,'.','');
								
								if($cboCountryId=="") $cboCountryId=0;
								if($codeId=="") $codeId=0;
								if($countryCode=="") $countryCode=0;								
								
								if($cboDeliveryCountry=="" || $cboDeliveryCountry==0) $ready_to_save=0;
								
								if($updateJobArr[$style_ref]['job']=="")
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
										$data_job_arr[$id]="(".$id.",100,'".$new_job_no_arr[$style_ref][0]."','".$new_job_no_arr[$style_ref][1]."','".$new_job_no_arr[$style_ref][2]."',".$cbo_company_id.",".$cbo_buyer_id.",".$cbo_style_owner_id.",".$cbo_location_id.",'".$style_ref."','".$style_des."',".$cbo_product_department.",".$cbo_currercy_id.",".$cbo_season_id.",".$cbo_season_year.",".$cbo_brand_id.",'".$repeat_no."',".$cbo_prod_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$hid_inquiry_id.",".$cbo_order_uom.",".$cbo_gmtsItem_id.",".$tot_smv_qty.",".$cbo_packing.",'".$set_breck_down."',1,".$cbo_gauge.",1,0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										$data_arrayInter[$interid]="(".$interid.",'".$new_job_no_arr[$style_ref][0]."',".$txt_masterStyle.",'".$pc_date_time."','".$pc_date_time."')";
										
										$job_id=$id;
										$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
										for($c=0; $c < count($set_breck_down_array);$c++)
										{
											$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
											
											if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
												echo "SMV**";
												check_table_status( $_SESSION['menu_id'],0);
												disconnect($con);
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
									$new_job_no_arr[$style_ref][0]=$updateJobArr[$style_ref]['job'];//str_replace("'","",$txt_job_no);
									$job_id=$updateJobArr[$style_ref]['jobid'];//str_replace("'","",$hid_job_id);
								}
								
								if($p==1) 
								{
									if($db_type==0)
									{
										$receive_date=change_date_format($recDate, "Y-m-d", "-",1);
										$shiment_date=change_date_format($shipDate, "Y-m-d", "-",1);
									}
									else
									{
										$receive_date=change_date_format($recDate, "d-M-y", "-",1);
										$shiment_date=change_date_format($shipDate, "d-M-y", "-",1);
									}
									if($updatePoArr[$style_ref][$order_no]=="")
									{
										$postatus=1;
										$postatus=$stylePOStatusArr[$style_ref][$order_no];
										$data_po_arr[$idPo]="(".$idPo.",'".$job_id."','".$new_job_no_arr[$style_ref][0]."','".$postatus."','".$order_no."','".$receive_date."','".$shiment_date."','".$shiment_date."','".$receive_date."',0,'".$poRemarks."',".$cbo_packing.",1,'".date("Y",strtotime(str_replace("'","",$shiment_date)))."','".date("m",strtotime(str_replace("'","",$shiment_date)))."',".$txt_masterStyle.",".$txt_sclc.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
										//$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$idPo]=$set_breck_down;
										$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$idPo.'_'.$postatus]=$set_breck_down;
										$npoid=$idPo;
										$idPo=$idPo+1;
										$p++;
									}
									else
									{
										//$npoid=$updatePoArr[$style_ref][$order_no];
										//$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$npoid]=$set_breck_down;
										$postatus=$stylePOStatusArr[$style_ref][$order_no];
										$npoid=$updatePoArr[$style_ref][$order_no];
										if($postatus==1 && $projectedPoArr[$style_ref][$order_no]['cid']!="")
										{
											$tmppoid[]=$npoid;
											$poDataArrayUp[$npoid]=explode("*",("'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
										}
										$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$npoid.'_'.$postatus]=$set_breck_down;
									}
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
								//
								//$color_id = return_id( $$colorName, $color_library, "lib_color", "id,color_name");  
								
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
								
								//$size_id = return_id( $$sizeName, $size_library, "lib_size", "id,size_name");
								
								if($db_type==0) $countryShipDate=change_date_format($shipDate, "Y-m-d", "-",1);
								else $countryShipDate=change_date_format($shipDate, "d-M-y", "-",1);
								
								if($add_colSiz!=0) $data_colSiz.=",";
								
								$article_number="";
								$article_number="no article";
								if($exper=="") $exper=0;
								
								if($exper==0) { $planCutQty=$countryQty; } else $planCutQty=($countryQty*($exper/100))+$countryQty;
								//echo $countryShipDate;
								//if($add_colSiz!=0) //$data_colSiz.=",";
								$poids=$updatePoArr[$style_ref][$order_no];
								$colSizeIdUp="";
								$colSizeIdUp=$updateColorSizeArr[$style_ref][$poids][$cboDeliveryCountry][$codeId][$countryShipDate][$color_id][$size_id];
								if($colSizeIdUp=="")
								{

									if($postatus==1)//Confirm Qty
									{
										$break_down[$idColSiz]="(".$idColSiz.",'".$job_id."',".$npoid.",'".$new_job_no_arr[$style_ref][0]."',0,0,0,".$cbo_gmtsItem_id.",'".$cboDeliveryCountry."','".$codeId."','".$cboCountryId."','".$countryCode."','".$countryShipDate."','".$color_id."','".$size_id."','".$countryQty."','".$countryRate."','".$exper."','".$article_number."','".$countryAmt."','".number_format($planCutQty,0,'.','')."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									}
									else if($postatus==2)//Projected Qty
									{
										$break_downproj[$idColSiz]="(".$idColSiz.",'".$job_id."',".$npoid.",'".$new_job_no_arr[$style_ref][0]."',0,0,0,".$cbo_gmtsItem_id.",'".$cboDeliveryCountry."','".$codeId."','".$cboCountryId."','".$countryCode."','".$countryShipDate."','".$color_id."','".$size_id."','".$countryQty."','".$countryRate."','".$exper."','".$article_number."','".$countryAmt."','".number_format($planCutQty,0,'.','')."','".$countryQty."','".$countryAmt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									}
									$idColSiz=$idColSiz+1;
									$add_colSiz++;
								}
								else
								{
									$colsizeidarr[]=$colSizeIdUp;
									$dataArrayUp[$colSizeIdUp]=explode("*",("'".$countryQty."'*'".$countryRate."'*'".$exper."'*'".$countryAmt."'*'".number_format($planCutQty,0,'.','')."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
									$counter++;									
								}
								if($postatus==1)
								{
									$balancingDataArr[$style_ref]+=$countryQty;
									$proj[$style_ref].=','.$colSizeIdUp;
								}
								$i++; $ctpn++;
							}
						}
					}
				}
				
				$add_po++;
				$pn++;
			}
			$id=$id+1;
			$interid=$interid+1;
			$add_job++;
			if($all_job_style=="") $all_job_style=$new_job_no_arr[$style_ref][0].'__'.$style_ref; else $all_job_style.=','.$new_job_no_arr[$style_ref][0].'__'.$style_ref;
			//update_color_size_sequence("'".$new_job_no_arr[$style_ref][0]."'");
			$sty++;
		}

		//Balancing Projected Qty with Confirm Qty
		$projDataArrayUp=array(); $projDataUp=0; $styleQty=0;
		if($jobStr!="")
		{
			$projFieldArrayUp="order_quantity*order_total*plan_cut_qnty*updated_by*update_date";
			foreach($projectedPoArr as $styleref=>$style_data)
			{
				$styleQty=$balancingDataArr[$styleref];
				//$projStyleQty=array_sum();
				if(($styleQty*1)>0)
				{
					$projcidArr=array_unique(array_filter(explode(",",$proj[$styleref])));
					$cidall=array_unique(array_filter(explode(",",$style_data['cid'])));
					foreach($cidall as $cbreakid)
					{
						if (!in_array($cbreakid, $projcidArr))
						{
							$excprojQty=explode("__",$cidwiseQtyArr[$cbreakid]);
							
							$cprojQty=$excprojQty[0];
							$cprojExPer=$excprojQty[1]*1;
							$cprojRate=$excprojQty[2];
							
							if($excprojQty[0]<=$styleQty && $styleQty>0)
							{
								$cprojQty=$cprojQty;
								$styleQty-=$excprojQty[0];
								$projcolsizeidarr[]=$cbreakid;
								$projDataArrayUp[$cbreakid]=explode("*",("'0'*'0'*'0'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							}
							else if($excprojQty[0]>$styleQty && $styleQty>0)
							{
								//echo $cbreakid.'='.$excprojQty[0].'='.$cprojQty.'='.$cbalqty.'='.$styleQty.'<br>';
								$cbalqty=$excprojQty[0]-$styleQty;
								$cprojQty=$cbalqty;
								$projcolsizeidarr[]=$cbreakid;
								$projAmt=($cbalqty*1)*$cprojRate;
								if($cprojExPer>0) $projPlanCut=($cbalqty*1)*$cprojExPer; else $projPlanCut=($cbalqty*1);
								$projDataArrayUp[$cbreakid]=explode("*",("'".($cprojQty*1)."'*'".($projAmt*1)."'*'".($projPlanCut*1)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
								$styleQty=0;
								//echo $cbreakid.'='.$excprojQty[0].'='.$cprojQty.'='.$cbalqty.'='.$styleQty.'<br>';
							}
							else
							{
								$cprojQty=$excprojQty[0];
								$styleQty=0;
							}
							
							$projDataUp=1;
						}
					}
				}
			}
		}

		//unset($_SESSION["excel"]);
		//if($ready_to_save==0) { check_table_status( $_SESSION['menu_id'],0); echo "5**Please check Country and Upload the file again."; disconnect($con); die; }

		$roll_back_msg="Data not save.";
		$time=time(); $flag=1; $rID=$rID0=$rID1=$rID2=$rID3=$rIDup=1;
		oci_commit($con);
		check_table_status( $_SESSION['menu_id'],0);
		$data_job=array_chunk($data_job_arr,1);
		foreach( $data_job as $jobRows)
		{
			$rID=sql_insert("wo_po_details_master",$field_job,implode(",",$jobRows),0);
			if($rID==1) $flag=1; //else $flag=0;
			else if($rID==0) 
			{
				//echo "10**INSERT INTO wo_po_details_master (".$field_job.") VALUES ".implode(",",$jobRows); die;
				$flag=0;
				oci_rollback($con); 
				echo "10**Job**".$roll_back_msg; disconnect($con); die;
			}
		}//die;
		//if($rID==1) $flag=1; else $flag=0;
		
		$data_interRef=array_chunk($data_arrayInter,1);
		foreach( $data_interRef as $interrefRows)
		{
			$rID0=sql_insert("wo_order_entry_internal_ref",$field_internal,implode(",",$interrefRows),0);
			if($rID0==1) $flag=1; //else $flag=0;
			else if($rID0==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**InternalRef**".$roll_back_msg; disconnect($con); die;
			}
		}
		
		$data_set=array_chunk($data_set_arr,1);
		foreach( $data_set as $setRows)
		{
			$rID1=sql_insert("wo_po_details_mas_set_details",$field_set,implode(",",$setRows),0);
			if($rID1==1) $flag=1; //else $flag=0;
			else if($rID1==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**ItemSet**".$roll_back_msg; disconnect($con); die;
			}
		}
		//if($rID1) $flag=1; else $flag=0;
		
		$po_data=array_chunk($data_po_arr,1);
		foreach( $po_data as $poRows)
		{
			//echo "10**INSERT INTO wo_po_break_down (".$field_po.") VALUES ".implode(",",$poRows); die;
			$rID2=sql_insert("wo_po_break_down",$field_po,implode(",",$poRows),0);
			if($rID2==1) $flag=1; //else $flag=0;
			else if($rID2==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**PO**".$roll_back_msg; disconnect($con); die;
			}
		}//die;
		//if($rID2) $flag=1; else $flag=0;
		//oci_commit($con); 
		$break_down_chnk=array_chunk($break_down,20);
		foreach( $break_down_chnk as $rows)
		{
			$rID3=sql_insert("wo_po_color_size_breakdown",$field_colSiz, implode(",",$rows),0);
			if($rID3==1) $flag=1; //else $flag=0;
			else if($rID3==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**CZ**".$roll_back_msg; disconnect($con); die;
			}
		}

		if($flag==1)
		{
			$breakDownChnkProj=array_chunk($break_downproj,20);
			foreach( $breakDownChnkProj as $rowp)
			{				
				$rIDp=sql_insert("wo_po_color_size_breakdown",$field_proj_colSiz, implode(",",$rowp),0);
				if($rIDp==1) $flag=1; //else $flag=0;
				else if($rIDp==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**CZProj**".$roll_back_msg; disconnect($con); die;
				}
			}
		}
		if($poDataArrayUp!="" && $flag==1)
		{
			$rIDconf=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$fieldArrayUpconf,$poDataArrayUp,$tmppoid ));
			if($rIDconf==1) $flag=1; //else $flag=0;
			else if($rIDconf==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**POConfirm**".$roll_back_msg; disconnect($con); die;
			}
		}
		if($dataArrayUp!="" && $flag==1)
		{
			$rIDup=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$dataArrayUp,$colsizeidarr ));
			if($rIDup==1 && $flag==1) $flag=1; 
			else if($rIDup==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**CZUP**".$roll_back_msg; disconnect($con); die;
			}
		}
		
		if($projDataUp==1 && $flag==1)
		{
			$rIDProjup=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$projFieldArrayUp,$projDataArrayUp,$projcolsizeidarr ));
			if($rIDProjup==1 && $flag==1) $flag=1; 
			else if($rIDProjup==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**CZPROJUP**".$roll_back_msg; disconnect($con); die;
			}
		}		
		//$job_style_order_arr[$new_job_no_arr[$style_ref][0]][$idPo]=$set_breck_down;
		$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		foreach ( $job_style_order_arr as $job_no=>$job_data )
		{
			update_color_size_sequence("'".$job_no."'",1);
			update_cost_sheet("'".$job_no."'");
			foreach($job_data as $podata=>$setData)
			{
				$expval=explode('_',$podata);
				$poId=$expval[0];
				$postatus=$expval[1];
				$exSetData=explode('__',$setData);
				//echo "'".$job_no."'".'=='.$poId.'=='.$setData;
				job_order_qty_update("'".$job_no."'",$poId,$exSetData,$postatus);
				
				$sam=1;				
				$sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$cbo_buyer_id order by sequ");
				$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 
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

function sql_multirow_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);


	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}

	//$arrRefFields=explode("*",$arrRefFields);
	//$arrRefValues=explode("*",$arrRefValues);
	$strQuery .= $arrRefFields." in (".$arrRefValues.")";
	 echo $strQuery;die;
    global $con;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!oci_error($stid))
		{

		$pc_time= add_time(date("H:i:s",time()),360);
		$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	    $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')";

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss);
		$_SESSION['last_query']="";
		oci_commit($con);
		return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
}

function bulk_update_sql_statement2($table, $id_column, $update_column, $data_values, $id_count) {
	$field_array = explode("*", $update_column);
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	$sql_up .= "UPDATE $table SET ";

	for ($len = 0; $len < count($field_array); $len++) {
		$sql_up .= " " . $field_array[$len] . " = CASE $id_column ";
		for ($id = 0; $id < count($id_count); $id++) {
			if (trim($data_values[$id_count[$id]][$len]) == "") {
				$sql_up .= " when " . $id_count[$id] . " then  '" . $data_values[$id_count[$id]][$len] . "'";
			} else {
				$sql_up .= " when " . $id_count[$id] . " then  " . $data_values[$id_count[$id]][$len] . "";
			}

		}
		if ($len != (count($field_array) - 1)) {
			$sql_up .= " END, ";
		} else {
			$sql_up .= " END ";
		}

	}
	$sql_up .= " where $id_column in (" . implode(",", $id_count) . ")";
	return $sql_up;
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
	/*echo "10**".$po_id.'--'.$job_no."<pre>";
	print_r($po_data_arr); die;*/
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
	//echo "10**". $field_array_po.'--'.$data_array_po.'--'.$po_id; die;
	$rID2=sql_update("wo_po_details_master",$field_array_job,$data_array_job,"job_no","".$job_no."",1);
	$rID3=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$po_id."",1);
	
	/*$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,
	sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$job_no' ");
	
	$jobQtyProjected=0; $jobPriceProjected=0; $jobAmtProjected=0; $jobQtyOriginal=0; $jobPriceOriginal=0; $jobAmtOriginal=0;
	$job_projected_price=0;
	$job_projected_price=$projected_data_array[0][csf('job_projected_total')]/$projected_data_array[0][csf('job_projected_qty')];
	
	$jobQtyProjected= number_format($projected_data_array[0][csf('job_projected_qty')]);
	$jobPriceProjected= number_format($job_projected_price,4);
	$jobAmtProjected= number_format($projected_data_array[0][csf('job_projected_total')],2);
	
	$jobQtyOriginal= number_format($projected_data_array[0][csf('projected_qty')]);
	$jobPriceOriginal= number_format($projected_data_array[0][csf('projected_rate')],4);
	$jobAmtOriginal= number_format($projected_data_array[0][csf('projected_amount')],2);*/
	
	//$value= $job_qty."**".$poavgprice."**".$job_amt."**".$jobQtyProjected."**".$jobPriceProjected."**".$jobAmtProjected."**".$jobQtyOriginal."**".$jobPriceOriginal."**".$jobAmtOriginal;
	//array(0=>$rID,1=>$po_data[csf('po_tot')],2=>$poavgprice,3=>$po_data[csf('po_tot_price')]);
	//return $value;
	//exit();
}

if($action=="inquiry_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="140">Buyer Name</th>
                <th width="100">Inquiry ID</th>
                <th width="60">Year</th>
                <th width="100">Style Ref.</th>
                <th width="100">Buyer Inquery No</th>
                <th width="70">Inquiry Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton" /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 140, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1); ?></td>
                <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                <td><? echo create_drop_down( "cbo_year", 60, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_inquiry_search_list_view', 'search_div', 'buyer_excel_order_import_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8"><input type="hidden" id="hidden_issue_number" value="" /></td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_inquiry_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
    if($company==0) { $company_name=""; echo "Please Select Company First."; die; } else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";

	$sql_cond='';
	$inquery_id_cond='';
	$request_no='';
	if($ex_data[7]==1)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
		if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'";
	}
	else if($ex_data[7]==4 || $ex_data[7]==0)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
	}
	else if($ex_data[7]==2)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' ";
	}
	else if($ex_data[7]==3)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' ";
	}
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$arr=array(0=>$buyer_arr,5=>$brandArr,6=>$season_buyer_wise_arr,8=>$row_status);
	 $sql = "select id, system_number_prefix_num, system_number, buyer_request, company_id, buyer_id, season_buyer_wise, season_year, brand_id, inquery_date, style_refernce, status_active, extract(year from insert_date) as year, gmts_item, gauge, product_dept from wo_quotation_inquery where is_deleted=0 and entry_form=457 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date $year_cond order by id DESC ";
	//echo $sql;
	echo create_list_view("list_view", "Buyer Name,Inquery NO,Year,Style Ref., Inquery Date,Brand,Season,Season Year, Status","120,60,50,120,70,80,80,50,100","800","260",0, $sql , "js_set_value", "style_refernce,id,buyer_id,season_buyer_wise,season_year,brand_id,gmts_item,gauge,product_dept", "", 1, "buyer_id,0,0,0,0,brand_id,season_buyer_wise,0,status_active", $arr, "buyer_id,system_number_prefix_num,year,style_refernce,inquery_date,brand_id,season_buyer_wise,season_year,status_active", "",'','0,0,0,0,3,0,0,0,0');
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="prevjob_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
	
		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="120">Buyer Name</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref </th>
                    <th width="80">Master Style/ Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_job">
					<?=create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "--Select Company--", $company,"load_drop_down( 'buyer_excel_order_import_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                </td>
        		<td id="buyer_pop_td"><?=create_drop_down( "cbo_buyer_id", 120, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'buyer_excel_order_import_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><?=load_month_buttons(1); ?></td>
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

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]' "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]' "; //else  $style_cond=""; 
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%' "; //else  $style_cond=""; 
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%' "; //else  $style_cond=""; 
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]' "; //else  $style_cond=""; 
	}
			
	$internal_ref = str_replace("'","",$data[10]);
	$file_no = str_replace("'","",$data[11]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[2]==0)
	{
		$arr=array(2=>$buyer_arr,9=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=100 and a.order_uom=1 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";// and b.is_confirmed=2
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=100 and a.order_uom=1  and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";//and b.is_confirmed=2
		}
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Gmts Nature,Master Style/ Internal Ref, File No,Lead time", "50,40,100,100,70,40,80,70,60,73,70,70,50","950","300",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "0,0,buyer_name,0,0,0,order_repeat_no,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,1,0,0,1,3,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$company_arr,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.garments_nature, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=100 and a.order_uom=1 and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=100 and a.order_uom=1 and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,110,100,100,60","900","200",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
}
?>