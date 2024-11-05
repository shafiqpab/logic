<?
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

$user_id=$_SESSION['logic_erp']['user_id'];
$data_level_secured=$_SESSION['logic_erp']["data_level_secured"];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";
if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($action=="get_company_config"){
	$action($data);
}

function get_company_config($data)
{
	global $location_credential_cond;
	$loc="select id, location_name from lib_location where company_id='$data' and is_deleted=0  and status_active=1 $location_credential_cond order by location_name";
	
	$cbo_location_name= create_drop_down( "cbo_location_name", 150, $loc,"id,location_name", 1, "-Select Location-", $index, "" ); 
	
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "fnc_get_buyer_config(this.value);" ); 
	
	$cbo_agent= create_drop_down( "cbo_agent", 150, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b,lib_buyer_party_type c where b.buyer_id=c.buyer_id and  a.status_active =1 and a.is_deleted=0 and c.party_type in(20,21) and b.buyer_id=a.id and b.tag_company='$data' group by  a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	
	echo "document.getElementById('location_td').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library, cm_cost_method, user_id from variable_order_tracking where company_name='$data' and variable_list in (14,20,23,25,32,33,44,45,47,53,93,96) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$tna_integrated=0; $copy_quotation=0; $set_smv_id=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; $cost_control_source=0; $color_from_lib=0; $next_process_ship_date=0; $act_po_data=2; $po_update_user_id=0; $po_control_booking=2;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
	}
	echo "document.getElementById('hidd_variable_data').value = '".$color_from_lib."';\n";
}

if($action=="check_requisition_acknowledg")
{
	$sql="SELECT a.requisition_date, a.requisition_number FROM sample_development_mst a, sample_requisition_acknowledge b
		  WHERE  a.is_approved IN (0, 1) AND a.requisition_number = '$data' AND a.is_acknowledge = 1 AND a.req_ready_to_approved = 1 and entry_form_id=449  AND a.status_active = 1 AND a.is_deleted = 0 AND a.id = b.sample_mst_id"; 
	//echo $sql."**";
	echo count(sql_select($sql));
	exit();
}

if($action=="get_buyer_config"){
	$action($data);
}

function get_buyer_config($data)
{
	global $userbrand_idCond;
	//echo $data;
	$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=70; else $width=150;
	$cbo_season_id=create_drop_down( "cbo_season_name", 150, "select a.id,a.season_name from LIB_BUYER_SEASON a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$data_arr[1]' and b.company_name='$data_arr[0]' and b.season_mandatory=1 and b.variable_list=44 order by a.season_name","id,season_name", 1, "--- Select Season ---", "", "" );
	
	$cbo_brand_id=create_drop_down( "cbo_brand_id", 150, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0  order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	
	$cbo_samplebuyer=create_drop_down( "cboSampleName_1", 100, "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id='$data_arr[1]' and b.business_nature=3 and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by a.id, a.sample_name, b.sequ order by b.sequ","id,sample_name", 1, "-- Select Buyer --", $selected, "" );
	
	echo "document.getElementById('season_td').innerHTML = '".$cbo_season_id."';\n";
	echo "document.getElementById('brand_td').innerHTML = '".$cbo_brand_id."';\n";
	echo "document.getElementById('sample_td').innerHTML = '".$cbo_samplebuyer."';\n";
}

if ($action=="load_drop_down_brand")
{
	list($buyer,$width)=explode('_',$data);
	$width=($width)?$width:150;
	
	echo create_drop_down( "cbo_brand_id", $width, "select brand.id, brand.brand_name from lib_buyer_brand brand where brand.buyer_id='$buyer' and brand.status_active =1 $brand_cond and brand.is_deleted=0 order by brand.brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

$color_arr=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
$size_arr=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name" );

if($action=="populate_data_to_req_qty")
{
	$data=explode("___",$data);
	if($data[4]==1)
	{
		$color_name=explode('***', trim($data[3]));
		$col_id="";
		foreach($color_name as $vals)
		{
			$vals=trim($vals);
			if($col_id=="")
			{
				$col_sql="select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$data[2]')";
				$col_arr=sql_select($col_sql);
				$col_id=$col_arr[0][csf("id")];
			}
			else
			{
				$col_arr=sql_select("select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$data[2]')");
				$col_id.=','.$col_arr[0][csf("id")];
			}
		}
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=449 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 and sample_color in($col_id) ");
	}
	else
	{
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=449 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
	}
	echo trim($value);
	exit();
}
if($action=="duplicate_style_check")
{
	$data=explode("_",$data);
	$value='';
	if($data[1]==2){
		$value=return_field_value("id","sample_development_mst","entry_form_id=449 and quotation_id=$data[0] and sample_stage_id=2 and status_active=1 and is_deleted=0");
	}
	echo trim($value);
	exit();
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="sample_wise_item_data")
{
	$data=explode("**",$data);
	$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id=449 and sample_name=$data[1]  and sample_mst_id=$data[0] and status_active=1 and is_deleted=0  ");
	echo trim($value);
	exit();
}

if($action=="load_data_to_uom")
{
   	$value=return_field_value("trim_uom","lib_item_group","id=$data and status_active=1 and is_deleted=0");
	echo $value;
	exit();
}

if($action=="load_data_to_colorRF")
{
    $data=explode("_", $data);
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

    $sql_color=sql_select("select distinct(sample_color) as sample_color from sample_development_dtls where entry_form_id=449 and sample_mst_id=$data[2] and sample_name=$data[0] and gmts_item_id=$data[1] and is_deleted=0  and status_active=1");
	foreach($sql_color as $row)
	{
		$sample_color_arr[$row[csf("sample_color")]]=$color_library[$row[csf("sample_color")]];
	}
	if(count($sql_color)>0)
	{
		echo "1_".implode("***",$sample_color_arr)."_".$sql[0][csf("sample_color")]."_";
	}
	exit();
}

if($action=="auto_sd_color_generation")
{
	$data=explode("***",$data);
	$sql=sql_select("select sample_color from sample_development_dtls where entry_form_id=449 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
	$color="";
	$i=1;
	foreach($sql as $row)
	{
		$color .=$i.'_'.'BLUE'.'_'.$row[csf("sample_color")].'_'.''."-----";
		$i++;
	}
	echo chop($color,"-----");
	exit();
}

if($action=="check_data_in_fab_acc_for_sample_dtls")
{
	$data=explode("**",$data);
	$value1=return_field_value("count(id)","sample_development_fabric_acc","form_type=1 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0");
	$SNdata1=return_field_value("wm_concat(sample_name_ra)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0 GROUP BY sample_mst_id");
	$GIdata1=return_field_value("wm_concat(gmts_item_id_ra)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0 GROUP BY sample_mst_id");
	$value2=return_field_value("count(id)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0");
	echo $value1."****".$value2."****".$SNdata1."****".$GIdata1;
	exit();
}

if($action=="check_data_in_fab_acc")
{
 	$value1=return_field_value("count(id)","sample_development_fabric_acc","form_type=1 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	$value2=return_field_value("count(id)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	$value3=return_field_value("count(id)","sample_development_fabric_acc","form_type=3 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	echo $value1."****".$value2."****".$value3;
	exit();
}

if($action=="load_drop_down_emb_type")
{
	$data=explode('_',$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cboReType_".$data[1], 120,$emblishment_print_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboReType_".$data[1], 120,$emblishment_embroy_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboReType_".$data[1], 120,$emblishment_wash_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboReType_".$data[1], 120,$emblishment_spwork_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==5)
	{
		echo create_drop_down( "cboReType_".$data[1], 120,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	exit();
}

if ($action=="load_drop_down_required_fabric_gmts_item")
{
	$data=explode("_", trim($data));
 	$sql=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=449 and sample_mst_id='$data[0]'");

	if($data[1]==1)
	{
		$gmts="";
		foreach ($sql as $row)
		{
			$gmts.=$row[csf("gmts_item_id")].",";
		}
		$gmts=chop($gmts,",");
		
		if(count($sql)>1)
		{
			echo create_drop_down( "cboRfGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
		else
		{
			echo create_drop_down( "cboRfGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
    }
	else if($data[1]==2)
	{
		$gmts="";
		foreach ($sql as $row)
		{
			$gmts.=$row[csf("gmts_item_id")].",";
		}
		$gmts=chop($gmts,",");
		
		if(count($sql)>1)
		{
			echo create_drop_down( "cboRaGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
		else
		{
			echo create_drop_down( "cboRaGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
	}
	else if($data[1]==3)
	{
		$gmts="";
		foreach ($sql as $row)
		{
			$gmts.=$row[csf("gmts_item_id")].",";
		}
		$gmts=chop($gmts,",");
		
		if(count($sql)>1)
		{
			echo create_drop_down( "cboWaGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
		else
		{
			echo create_drop_down( "cboWaGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
	}
	else if($data[1]==4)
	{
		$gmts="";
		foreach ($sql as $row)
		{
			$gmts.=$row[csf("gmts_item_id")].",";
		}
		$gmts=chop($gmts,",");
		
		if(count($sql)>1)
		{
			echo create_drop_down( "cboPrGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
		else
		{
			echo create_drop_down( "cboPrGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
	}
	else if($data[1]==5)
	{
		$gmts="";
		foreach ($sql as $row)
		{
			$gmts.=$row[csf("gmts_item_id")].",";
		}
		$gmts=chop($gmts,",");
		
		if(count($sql)>1)
		{
			echo create_drop_down( "cboReGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
		else
		{
			echo create_drop_down( "cboReGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		}
	}
	exit();
}

if ($action=="load_drop_down_required_fabric_sample_name")
{
	$data=explode("_", $data);
	$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=449 and b.sample_mst_id='$data[0]' group by a.id,a.sample_name,b.id order by b.id";
	$samp_array=array();
	$samp_result=sql_select($sql);
	if(count($samp_result)>0)
	{
		foreach($samp_result as $keys=>$vals)
		{
			$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
		}
	}
	if($data[1]==1)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboRfSampleName_1", 95, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,1);");
		}
		else
		{
			echo create_drop_down( "cboRfSampleName_1", 95, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}

	else if($data[1]==2)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboRaSampleName_1", 100, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,2);");
		}
		else
		{
			echo create_drop_down( "cboRaSampleName_1", 100, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	else if($data[1]==3)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboWaSampleName_1", 140, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,3);");
		}
		else
		{
			echo create_drop_down( "cboWaSampleName_1", 140, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	else if($data[1]==4)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboPrSampleName_1", 140, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,3);");
		}
		else
		{
			echo create_drop_down( "cboPrSampleName_1", 140, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	else if($data[1]==5)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboReSampleName_1", 100, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,3);");
		}
		else
		{
			echo create_drop_down( "cboReSampleName_1", 100, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($sampleStage==1)
	{
		$checkboxcaption="Fabric From BOM Only";
	}
	else
	{
		$checkboxcaption="Fabric From Inquiry Only";
	}
	//echo $RfFabricNature.'DS';
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('hid_libDes').value=trim(data);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		
		function fn_check_fabric_inq(){
			if($('#cbx_fabric_from_inq').is(':checked')){
				$('#cbx_fabric_from_inq').val(1);
			}
			else{
				$('#cbx_fabric_from_inq').val(0);
			}
		}
		</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="2" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        <th colspan="2"><input id="cbx_fabric_from_inq" type="checkbox" value="2" onClick="fn_check_fabric_inq();"> <?=$checkboxcaption; ?><!--ISD-22-21156--></th>
                    </tr>
                    <tr>
                    	<th>RD No</th>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"><input type="hidden" id="hid_libDes" name="hid_libDes" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><input type="text" style="width:80px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td><input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td>
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$RfFabricNature; ?>'+'**'+'<?=$libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('txt_rdno').value+'**'+document.getElementById('cbx_fabric_from_inq').value+'**'+'<?=$quotation_id.'**'.$sampleStage ;?>', 'fabric_description_popup_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<?=$libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($RfFabricNature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno,$fabric_from_inq,$quotation_id,$sampleStage)=explode('**',$data);
	
	if($fabric_from_inq==1){
		if($sampleStage==1)
			$where_con = "and a.id in ( select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where job_id = $quotation_id and status_active=1 and is_deleted=0)";
		else
			$where_con = "and a.id in(select b.CONSTRACTION from WO_QUOTATION_INQUERY a, WO_QUOTATION_INQUERY_FAB_DTLS b where a.id=b.mst_id and a.id = $quotation_id)";
	}
	
	// echo $fabric_nature.'DDD';
	if($RfFabricNature) $fabric_natureCond="and a.fab_nature_id=$RfFabricNature";else $fabric_natureCond="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}
	?>
	</head>
	<body>
		<?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
			//echo $sql_q;
			$data_array=sql_select($sql_q);
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					$compo_per="";
					if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
					if(array_key_exists($row[csf('mst_id')],$composition_arr))
					{
						$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
					}
				}
			}
			unset($data_array);
		?>
		<table class="rpt_table" width="850px" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
			<thead>
				<tr>
					<th width="25">SL</th>
					<th width="75">Fab Nature</th>
					<th width="60">RD No</th>
					<th width="80">Fabric Ref</th>
					<th width="60">Type</th>
					<th width="100">Construction</th>
					<th width="80">Design</th>
					<th width="50">Weight</th>
					<th width="50">Weight Type</th>
					<th width="50">Color Range</th>
					<th width="50">Full Width</th>
					<th width="50">Cutable Width</th>
					<th>Composition</th>
				</tr>
		   </thead>
	   </table>
	   <div style="max-height:230px; width:850px; overflow-y:scroll">
		   <table id="list_view" class="rpt_table" width="830px" height="" cellspacing="0" cellpadding="0" border="1" rules="all" >
				<tbody>
			<?
				
				$sql_data=sql_select("select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width from lib_yarn_count_determina_mst a where a.is_deleted=0  $search_con $where_con $fabric_natureCond order by a.id ASC"); //and a.entry_form=426 As Per 12964 Issue Id
				 
				$i=1;
				foreach($sql_data as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('rd_no')]."_".$row[csf('fabric_ref')]."_".$row[csf('type')]."_".$row[csf('construction')]."_".$row[csf('design')]."_".$row[csf('gsm_weight')]."_".$row[csf('weight_type')]."_".$row[csf('color_range_id')]."_".$composition_arr[$row[csf('id')]]."_".$row[csf('full_width')]."_".$row[csf('cutable_width')]; ?>')">
							<td width="25" align="center"><?=$i; ?></td>
							<td width="75" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
							<td width="60" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
							<td width="80" style="word-break:break-all"><?=$row[csf('fabric_ref')]; ?></td>
							<td width="60" style="word-break:break-all"><?=$row[csf('type')]; ?></td>
							<td width="100" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
							<td width="80" style="word-break:break-all"><?=$row[csf('design')]; ?></td>
							<td width="50" style="word-break:break-all"><?=$row[csf('gsm_weight')]; ?></td>
							<td width="50" style="word-break:break-all"><?=$fabric_weight_type[$row[csf('weight_type')]]; ?></td>
							<td width="50" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
							<td width="50" style="word-break:break-all"><?=$row[csf('full_width')]; ?></td>
							<td width="50" style="word-break:break-all"><?=$row[csf('cutable_width')]; ?></td>
							<td style="word-break:break-all"><?=$composition_arr[$row[csf('id')]]; ?></td>
						</tr>
					<?
					$i++;
				}
			?>
				</tbody>
			</table>
		</div>
	</body>
	</html>
	<?
	exit();
}

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per;
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$compo_per;
			}

			if($yarn_description!="")
			{
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
			}
			else
			{
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
			}
		}
	}
	echo $fab_description."**".$yarn_description;

}
if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
 }
if ($action=="color_popup_rf")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
		?>

    <script>
		var permission='<? echo $permission; ?>';

		function add_break_down_tr( i )
		{
			var row_num=$('#col_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#col_tbl tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
              'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
              'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
              'value': function(_, value) { return value }
            });

				}).end().appendTo("#col_tbl");

				$("#col_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);
				$("#txtSL_"+i).val(i);
				$('#txtContrast_'+i).removeAttr("ondblclick").attr("ondblclick","copy_gmts_color_to_fab("+i+");");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");

				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
 			}
		}
		function fn_deleteRows(rowNo)
		{
			var numRow=$('#col_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#col_tbl tbody tr:last').remove();
			}
			else
			{
			 //code
			}
		}


	function fn_deleteRow(rowNo)
    {
		  var k=rowNo-1;

		  $("table#col_tbl tbody tr:eq("+k+")").remove();
		   var numRow = $('#col_tbl tbody tr').length;

			for(i = rowNo;i <= numRow;i++)
			{
				//$('#txtSL_'+(i-1)).val(i);
				$("#col_tbl tr:eq("+i+")").find("input,select").each(function() {
					$('#txtSL_'+(i-1)).val(i);
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
				$("#col_tbl tr:eq("+i+")").removeAttr('id').attr('id','row_'+i);
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deleteRow("+i+");");
				$('#txtContrast_'+i).removeAttr("ondblclick").attr("ondblclick","copy_gmts_color_to_fab("+i+");");


				});
			}
			for(i=1;i<=numRow;i++)
			{
				$('#txtSL_'+(i)).val(i);
			}
    }

	function fnc_close( )
	{
		var rowCount = $('#col_tbl tbody tr').length;
		//alert( rowCount );return;
		var breck_down_data="";
		var display_col="";
		var total_qnty=0;
		var total_loss=0;
		var total_grey=0;
		for(var i=1; i<=rowCount; i++)
		{
			if(breck_down_data=="")
			{
				breck_down_data+=($('#txtSL_'+i).val()*1)+'_'+$('#txtColor_'+i).val()+'_'+($('#hiddenColorId_'+i).val())*1+'_'+$('#txtContrast_'+i).val()+'_'+$('#txtQnty_'+i).val()*1+'_'+$('#txtProcessLoss_'+i).val()*1+'_'+$('#txtGreyQnty_'+i).val()*1;
				  display_col +=$('#txtColor_'+i).val() ;
			}
			else
			{
				breck_down_data+="-----"+($('#txtSL_'+i).val()*1)+'_'+$('#txtColor_'+i).val()+'_'+($('#hiddenColorId_'+i).val())*1+'_'+$('#txtContrast_'+i).val()+'_'+$('#txtQnty_'+i).val()*1+'_'+$('#txtProcessLoss_'+i).val()*1+'_'+$('#txtGreyQnty_'+i).val()*1;;
				  display_col +='***'+$('#txtColor_'+i).val() ;
			}
			total_qnty+=$('#txtQnty_'+i).val()*1;
			total_loss+=$('#txtProcessLoss_'+i).val()*1;
			total_grey+=$('#txtGreyQnty_'+i).val()*1;
		}
		var loss_per= ((total_grey-total_qnty)/total_qnty)*100;
		document.getElementById('txtRfColorAllData').value=breck_down_data;
		document.getElementById('displayAllcol').value=display_col;
		document.getElementById('total_qnty_kg').value=total_qnty;
		document.getElementById('total_loss').value=loss_per;
		document.getElementById('total_grey').value=total_grey;
		parent.emailwindow.hide();
	}
	
	function copy_gmts_color_to_fab(id)
	{
		var gmts_color=$("#txtColor_"+id).val();
		$("#txtContrast_"+id).val(gmts_color);

	}

	function calculate_requirement(i)
     {
      	var cbo_company_name= '<? echo $company;?>';

     	var cbo_fabric_natu= 2;
      	var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'sample_requisition_with_booking_controller');
      	//alert(process_loss_method_id);
     	var txt_finish_qnty=(document.getElementById('txtQnty_'+i).value)*1;
     	var processloss=(document.getElementById('txtProcessLoss_'+i).value)*1;
     	var WastageQty='';

     	if(process_loss_method_id==1)
     	{
     		WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
     	}
     	else if(process_loss_method_id==2)
     	{
     		var devided_val = 1-(processloss/100);
     		var WastageQty=parseFloat(txt_finish_qnty/devided_val);
     	}
     	else
     	{
     		WastageQty=0;
     	}
     	WastageQty= number_format_common( WastageQty, 5, 0) ;
     	document.getElementById('txtGreyQnty_'+i).value= WastageQty;
     	//document.getElementById('txtAmount_'+i).value=number_format_common((document.getElementById('txtRate_'+i).value)*1*WastageQty,5,0);
     }

     function copy_all_field(id,value,type)
     {
      	var check_val=$('#checkboxId').is(':checked');
     	if(check_val==true)
     	{
     		var rows = $('#col_tbl tbody tr').length*1;
     		var id=id.split("_");
     		var position=id[1]*1;
     		var i;
     		for(i=position+1;i<=rows;i++)
     		{
     			if(type=='1')
     			{
     				$("#txtQnty_"+i).val(value);
     				calculate_requirement(i);
     			}
     			else if(type==2)
     			{
     				$("#txtProcessLoss_"+i).val(value);
     				calculate_requirement(i);
     			}
     			else if(type==3)
     			{
     				$("#txtContrast_"+i).val(value);
     			}
     		}
     	}
     }
    </script>
    <body>
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:520px;">
            <table align="center" cellspacing="0" width="520" class="rpt_table" border="1" rules="all" id="col_tbl" >
            	<thead>
            	<tr>
            		<td colspan="7" align="center">Copy<input type="checkbox" name="checkboxId" id="checkboxId" value="1"></td>
            	</tr>
            		<tr>
            			<th width="30">SL</th>
            			<th width="70">Gmts Color</th>
            			<th width="100">Fab. Col/Contrast</th>
            			<th width="40">Fin. Qty.</th>
            			<th width="50">Process Loss%</th>
            			<th width="50">Final Qty</th>
            			<th><input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<?=$mainId; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<?=$dtlId; ?>" style="width:30px" />
            			</th>
            		</tr>
            	</thead>
                <tbody>

                	<?
				 $sql_col="select id,sample_color from sample_development_dtls where entry_form_id=449 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0  and status_active=1 order by id ASC";
					$sql_result =sql_select($sql_col);
					foreach ($sql_result as $row)
					{
						$sample_new_color_arr[$row[csf('sample_color')]]=$row[csf('sample_color')];
					}
					//print_r($sample_new_color_arr);
				$sql_rf_col="select c.id,c.color_id,c.qnty,c.contrast,c.color_id,c.fabric_color,c.grey_fab_qnty,c.process_loss_percent from sample_development_fabric_acc b,sample_development_rf_color c where  b.id=c.dtls_id and b.sample_mst_id=$mainId and b.sample_name=$sampleName and b.gmts_item_id=$garmentItem and c.dtls_id=$dtlId and b.is_deleted=0  and b.status_active=1 and c.is_deleted=0 and c.grey_fab_qnty>0  and c.status_active=1 and b.form_type=1 order by c.id ASC";
				$sql_color_result =sql_select($sql_rf_col);
				if($data!='')
				{
					$data=$data;
					$type=2;
				}
				else
				{
					$data=$sql_color_result;//From Rf Color table
					$type=1;
				}
				//echo $type.'dd'.$data;
                	if($data)
                	{
						if($type==2)
						{
							$data_all=explode('-----',$data);
							$count_tr=count($data_all);
						}
						else
						{
							$count_tr=count($sql_color_result);
							$data_all=$data;
						}
                		if($count_tr>0)
                		{
                			$i=1;
                			foreach ($data_all as $size_data)
                			{
							/*$txtSL=0;
							$txtColor='';
							$hiddenColorId=0;
							$txtContrast=''; */
							if($type==2)
							{
							$ex_size_data=explode('_',$size_data);
							$txtSL=$ex_size_data[0];
							$txtColor=$ex_size_data[1];
							$hiddenColorId=$ex_size_data[2];
							$txtContrast=$ex_size_data[3];
							$txtQnty=$ex_size_data[4];
							$txtProcessLoss=$ex_size_data[5];
							$txtGreyQnty=$ex_size_data[6];
							}
							else
							{
							//$ex_size_data=explode('_',$size_data);
							$txtSL=$ex_size_data[0];
							$txtColor=$color_arr[$size_data[csf('color_id')]];
							$hiddenColorId=$size_data[csf('color_id')];
							$txtContrast=$size_data[csf('contrast')];
							$txtQnty=$size_data[csf('qnty')];
							$txtProcessLoss=$size_data[csf('process_loss_percent')];
							$txtGreyQnty=$size_data[csf('grey_fab_qnty')];
							}
							 $current_ColorId.=$hiddenColorId.',';
							?>
							<tr id="row_<? echo $i; ?>">

								<td><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td>
									<input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" style="width:70px" value="<? echo $txtColor; ?>" disabled  />
									<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $hiddenColorId ?>">

								</td>

								<td><input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:100px" onChange="copy_all_field(this.id,this.value,'3');"
									value="<? echo $txtContrast;?>"  ondblclick="copy_gmts_color_to_fab(<? echo $i; ?>);" /></td>


									<td><input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" style="width:40px" onBlur="calculate_requirement(<? echo $i; ?>);"  onchange="copy_all_field(this.id,this.value,'1');"  value="<? echo $txtQnty;?>"   /></td>

									<td><input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:50px" onChange="copy_all_field(this.id,this.value,'2');" onBlur="calculate_requirement(<? echo $i; ?>);"  value="<? echo $txtProcessLoss;?>"   /></td>

									<td><input readonly name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:50px"  value="<? echo $txtGreyQnty;?>"   /></td>

									<td align="center">
										<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
								$i++;
							}
							$current_ColorId=rtrim($current_ColorId,',');
							$current_ColorIds=array_unique(explode(",",$current_ColorId));
							foreach($sample_new_color_arr as $color_id)//For New Color add From Sample
							{
								if(!in_array($color_id,$current_ColorIds))
								{
									?>
                                    <tr id="row_<? echo $i; ?>">

								<td><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td>
									<input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" style="width:70px" value="<? echo $color_arr[$color_id]; ?>" disabled  />
									<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $color_id ?>">

								</td>

								<td><input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:100px" onChange="copy_all_field(this.id,this.value,'3');"
									value="<? //echo $txtContrast;?>"  ondblclick="copy_gmts_color_to_fab(<? echo $i; ?>);" /></td>


									<td><input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" style="width:40px" onBlur="calculate_requirement(<? echo $i; ?>);"  onchange="copy_all_field(this.id,this.value,'1');"  value="<? //echo $txtQnty;?>"   /></td>

									<td><input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:50px" onChange="copy_all_field(this.id,this.value,'2');" onBlur="calculate_requirement(<? echo $i; ?>);"  value="<? //echo $txtProcessLoss;?>"   /></td>

									<td><input readonly name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:50px"  value="<? //echo $txtGreyQnty;?>"   /></td>

									<td align="center">
										<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
								$i++;

								}
							}
						}
					}
					else
					{
						$sql_col="select id,sample_color from sample_development_dtls where entry_form_id=449 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0  and status_active=1 order by id ASC";
						$sql_result =sql_select($sql_col);
						$i=1;
						foreach($sql_result as $row)
						{
							$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

							?>

							<tr id="row_<? echo $i; ?>">
								<td width="30" align="center" ><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td width="70" align="center" ><input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" value="<? echo $color_library[$row[csf('sample_color')]];  ?>" style="width:70px" disabled />
									<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $row[csf('sample_color')];  ?>">

								</td>

								<td width="100" align="center" ><Input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'3');" value="" onDblClick="copy_gmts_color_to_fab(<? echo $i; ?>);"/></td>


								<td width="40" align="center" ><Input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" onBlur="calculate_requirement(<? echo $i; ?>);" onChange="copy_all_field(this.id,this.value,'1');" style="width:70px" value="" /></td>
								<td width="50" align="center" ><Input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'2');" value="" onBlur="calculate_requirement(<? echo $i; ?>);" /></td>
								<td width="50" align="center" ><Input name="txtGreyQnty_<? echo $i; ?>" readonly class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:70px" value="" /></td>

								<td align="center">
									<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</tbody>
            </table>
            <table align="center" cellspacing="0" width="520" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td colspan="7" align="center">
                        <input type="hidden" name="txtRfColorAllData" id="txtRfColorAllData" class="text_boxes"  value="" >
                        <input type="hidden" name="displayAllcol" id="displayAllcol">
                        <input type="hidden" name="total_qnty_kg" id="total_qnty_kg">
                        <input type="hidden" name="total_loss" id="total_loss">
                        <input type="hidden" name="total_grey" id="total_grey">
                     </td>
                </tr>
                <tr>
                    <td align="center" colspan="7" align="center" class="button_container">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="sample_requisition_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
 ?>
 <style>
#mstDiv {
    margin:0px auto;
    width:1130px;

}
#mstDiv @media print {

   thead {display: table-header-group;}

}

 @media print{
		html>body table.rpt_table {
		margin-left:12px;
  		}

	}
</style>
	<div id="mstDiv">
     <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
     <tr>
     	<td rowspan="4" valign="top" width="300"><img width="150" height="80" src="../../<? echo $company_img[0][csf("image_location")]; ?>"</td>
     	<td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
            <td width="200">
            <?

             $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
             $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
             $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
              ?>
             </td>
     </tr>
        <tr>
            <td colspan="5">
				<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					//echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
					echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
					echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
					echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
					echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
					echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
					echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
					echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
					echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
					echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
					  $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date from sample_development_mst where  id='$data[1]' and entry_form_id=449 and  is_deleted=0  and status_active=1";
 					  $dataArray=sql_select($sql);
 					  $barcode_no=$dataArray[0][csf('requisition_number')];
 					  if($dataArray[0][csf("sample_stage_id")]==1)
 					  {
 					  	 $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
 					  }

 					   $sqls="SELECT style_desc,supplier_id,revised_no,buyer_req_no,source,team_leader,dealing_marchant from wo_non_ord_samp_booking_mst where  booking_no='$data[2]'  and  is_deleted=0  and status_active=1";
 					  $dataArray_book=sql_select($sqls);
					// $style_desc= $dataArray_book[0][csf('style_desc')];
                ?>
            </td>

        </tr>
        <tr>
            <td colspan="3" style="font-size:medium"><strong> <b>Sample Program Without Order</b></strong></td>
             <td colspan="2" id="" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>

        </tr>


        </table>

        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
        	<tr>
        		<td colspan="4" align="left"><strong>System No. &nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> </strong></td>
        		<td ><strong>Revise:</strong></td>
        		<td ><? echo $dataArray_book[0][csf('revised_no')];?></td>
        		<td colspan="2"></td>
        	</tr>

        	<tr>
        	<td width="100"><strong>Booking No: </strong></td>
        		<td width="130" align="left"><? echo $data[2];?></td>
        		<td width="120"  align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
        		<td width="110">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
        		<td width="110"   align="left"><strong>Sample Sub Date:</strong></td>
        		<td width="100" ><? echo change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
        		<td width="110"   align="left"><strong>Style Desc:</strong></td>
        		<td   ><? echo $dataArray_book[0][csf('style_desc')];?></td>
        	</tr>
        	<tr>
        		<td width="100"><strong>Buyer Name: </strong></td>
        		<td width="130" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
        		<td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season:</strong></td>
        		<td width="110">&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>
        		<td width="110"><strong>BH Merchant:</strong></td>
        		<td width="100"><? echo $dataArray[0][csf('bh_merchant')];?></td>
        		<td width="110"><strong>Remarks/Desc:</strong></td>
        		<td   style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>

        	</tr>
        	<tr>
        		<td width="100"   align="left"><strong>Buyer Ref:</strong></td>
        		<td width="130" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
        		<td width="120"  >&nbsp;&nbsp;<strong>Product Dept:</strong></td>
        		<td width="110" ><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
        		<td width="110"  ><strong>Supplier</strong></td>
        		<td width="100" ><? echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
        		<td width="110"><strong>Est. Ship Date</strong></td>
        		<td ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>

        	</tr>
            <tr>
        		<td width="100"><strong>Team Leader</strong></td>
        		<td width="130" ><? echo $team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
        		<td width="120"  >&nbsp;&nbsp;<strong>Dealing Merchant:</strong></td>
        		<td width="110" ><? echo $dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
        		<td width="110"  ><strong>Sample Stage</strong></td>
        		<td width="100" ><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
        		<td width="110">&nbsp;</td>
        		<td >&nbsp;</td>

        	</tr>
        </table>

        <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;margin-left: 20px;" >
         <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            <table align="left" cellspacing="0" border="0" width="90%" >

        	</table>
        </td>
        </tr>



         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
        	<?
			 $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id='$data[1]' and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";

			foreach(sql_select($sql_sample_dtls) as $key=>$value)
			{
				if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
				{
					$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
				}
				else
				{
					if(!in_array($value[csf("article_no")], $sample_wise_article_no))
					{
						$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
					}

				}
				
				//$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];

			}
			/*$sql_book=sql_select("select dtls_id from wo_non_ord_samp_booking_dtls where style_id='$data[1]' and status_active=1");
			$dtls_id="";
			foreach($sql_book as $row)
			{
				$dtls_id.=$row[csf("dtls_id")].',';
			}
			$dtls_ids=rtrim($dtls_id,',');
			$dtls_ids=implode(",",array_unique(explode(",",$dtls_ids)));
			if($dtls_ids) $dtls_id_cond="and a.id in($dtls_ids) ";else $dtls_id_cond="and a.id in(0)";*/

		  $sql_fab="SELECT a.sample_name,a.gmts_item_id,b.color_id,b.contrast,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,b.process_loss_percent,c.grey_fabric as grey_fab_qnty  from sample_development_fabric_acc a,sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and  a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.style_id=a.sample_mst_id and c.style_id=b.mst_id and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";
			 $sql_fab_arr=array();
			 foreach(sql_select($sql_fab) as $vals)
			 {
				$article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
				$article_no=implode(",",array_unique(explode(",",$article_no)));
				$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["process_loss_percent"]=$vals[csf("process_loss_percent")];

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["fabric_source"] =$vals[csf("fabric_source")];

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["uom_id"] =$vals[csf("uom_id")];
				$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["dia"] =$vals[csf("dia")];

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["width_dia_id"] =$vals[csf("width_dia_id")];

			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["remarks"] =$vals[csf("remarks_ra")];
			 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["color_type_id"] =$vals[csf("color_type_id")];
			 }
			 $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

		  foreach($sql_fab_arr as $article_no=>$article_data) 
          {
			$article_no_span=0;
			foreach($article_data as $sample_type_id=>$sampleType_data) 
        	{
			$sample_type_span=0;
			foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
        	{
				$sample_span=0;
        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
        		{
        			
					//echo $gmts_color_id.'d';

        			foreach($body_part_data as $fab_id=>$fab_desc_data)
        			{
        				//$kk=0;
        				foreach($fab_desc_data as $colorType=>$colorType_data)
        				{

        					foreach($colorType_data as $gsm_id=>$gsm_data)
        					{
        						foreach($gsm_data as $dia_id=>$dia_data)
        						{
								   foreach($dia_data as $dia_type_id=>$diatype_data)
        						   {

        							foreach($diatype_data as $contrast_id=>$value)
        							{
        								$sample_span++;$sample_type_span++;$article_no_span++;
        								//$kk++;

        							}
										$article_wise_span[$article_no]=$article_no_span;
										$sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
										$sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
								  }
        						}

        					}


        				}

        				//$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;

        			}
        		//	$sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;

        		  }
				 }

        		}
			}
        	//echo "<pre>";
        	//print_r($sample_item_wise_color_span);die;
			// echo "<pre>"; print_r($sample_wise_article_no);die;

			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
				<tr>
					<th colspan="19">Required Fabric</th>
				</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="90">ALT / [C/W]</th>
						<th width="110">Sample Type</th>
						<th width="80">Gmt Color</th>
						<th width="80">Fab. Deli Date</th>
						<th width="120">Body Part</th>
						<th width="200">Fabric Desc & Composition</th>
						<th width="80">Color Type</th>
						<th width="80">Fab.Color</th>
						<th width="40">Item Size</th>
						<th width="55">GSM</th>
						<th width="55">Dia</th>
						<th width="60">Width/Dia</th>
						<th width="40">UOM</th>
						<th width="60">Grey Qnty</th>
						<th width="40">P. Loss</th>
						<th width="80">Fin Fab Qnty</th>
						<th width="80">Fabric Source</th>
						<th width="80">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$p=1;
					$total_finish=0;
					$total_grey=0;
					$total_process=0;
		 foreach($sql_fab_arr as $article_no=>$article_data) 
         {
			$aa=0;
			foreach($article_data as $sample_type_id=>$sampleType_data) 
        	{
			$nn=0;
			foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
        	{
				$cc=0;
        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
        		{
        			
					//echo $gmts_color_id.'d';

        			foreach($body_part_data as $fab_id=>$fab_desc_data)
        			{
        				//$kk=0;
        				foreach($fab_desc_data as $colorType=>$colorType_data)
        				{

        					foreach($colorType_data as $gsm_id=>$gsm_data)
        					{
        						foreach($gsm_data as $dia_id=>$dia_data)
        						{

        							foreach($dia_data as $dia_type=>$diatype_data)
        							{
										foreach($diatype_data as $contrast_id=>$value)
        							    {

														 
													?>
													<tr>


															
															<?
														if($aa==0)
														{
															?>
                                                            <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                            <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
                                                            <?
														}
														if($nn==0)
														{
															?>
															
															<td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
															
															<?
															
														}
														if($cc==0)
														{
														 ?>
                                                         <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
                                                          <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
                                                         <?
                                                        } ?>

														
														 <td width="120"  align="center"><? echo $body_part[$body_part_id];?></td>
														 <td  align="center"><? echo $fab_id;?></td>
														 <td  align="center"> <? echo $color_type[$colorType]; ?></td>
														 <td  align="center"><? echo $contrast_id; ?></td>
														 <td  align="center"><? echo $value["item_size"]; ?></td>
														 <td  align="center"><? echo $gsm_id; ?></td>
														 <td  align="center"><? echo $value["dia"]; ?></td>
														 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
														 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>

														 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
														 <td align="right"><? echo $value["process_loss_percent"];?></td>
														 <td align="right"><? echo $value["qnty"];?></td>

														 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
														 <td  align="center"><? echo $value["remarks"];?></td>

													</tr>


													<?
													$nn++;$cc++;$aa++;
		        									//$i++;
													$total_finish +=$value["qnty"];
													$total_grey +=$value["grey_fab_qnty"];
													$total_process +=$value["process_loss_percent"];
												}
											}
										}
									}
								}
							}
						  }
						}
					}
		 		}

					?>

					<tr>
						<th colspan="14" align="right"><b>Total</b></th>
						<th width="80" align="right"><? echo number_format($total_grey,2);?></th>
						<th width="40" align="right">&nbsp;</th>
						<th width="60" align="right"><? echo $total_finish;?></th>
						<th width="80" colspan="2"> </th>

					</tr>

				</tbody>



			</table>
			<br/>



<?

			$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
                      $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$data[1]' order by id asc";
					    $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id='$data[1]' order by a.id asc";
					 $size_type_arr=array(1=>"bh_qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
					 $color_size_arr=array();
					  foreach(sql_select($sql_qry_color) as $vals)
					 {
							if($vals[csf("bh_qty")]>0)
							{
							$color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
							$bh_qty=$vals[csf("bh_qty")];
							$color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
							}
							if($vals[csf("self_qty")]>0)
							{
							$color_size_arr[2][$vals[csf("size_id")]]='self qty';
							$color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
							}
							if($vals[csf("test_qty")]>0)
							{
							$color_size_arr[3][$vals[csf("size_id")]]='test qty';
							$color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
							}
							if($vals[csf("plan_qty")]>0)
							{
							$color_size_arr[4][$vals[csf("size_id")]]='plan qty';
							//$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
							$color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];

							}
							if($vals[csf("dyeing_qty")]>0)
							{
							$color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
							$color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];

							}

						}
						$tot_row=count($color_size_arr);
						$result=sql_select($sql_qry);

?>


            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
                        </tr>
                        <tr>
								<th width="30" rowspan="2" align="center">Sl</th>
								<th width="100" rowspan="2" align="center">Sample Name</th>
								<th width="120" rowspan="2" align="center">Garment Item</th>

								<th width="55" rowspan="2" align="center">ALT / [C/W]</th>
								<th width="70" rowspan="2" align="center">Color</th>
                                <?
								$tot_row_td=0;
                                foreach($color_size_arr as $type_id=>$val)
								{ ?>
									<th width="45" align="center" colspan="<? echo count($val);?>"> <?
                                 		  echo  $size_type_arr[$type_id];
									?></th>
                                    <?

								}
								?>
								<th rowspan="2" width="55" align="center">Total</th>
								<th rowspan="2" width="55" align="center">Submn Qty</th>
								<th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
								<th rowspan="2"  width="70" align="center">Remarks</th>
                         </tr>
                         <tr>
                         	<?
                            foreach($color_size_arr as $type_id=>$data_size)
							{
								foreach($data_size as $size_id=>$data_val)
								{
								$tot_row_td++;
								?>
									<th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
									<?
								}
                         	}

                         	?>
                         </tr>

            	</thead>
                    <tbody>

                        <?

 						$i=1;$k=0;
 						$gr_tot_sum=0;
 						$gr_sub_sum=0;
						foreach($result as $row)
						{
							$dtls_ids=$row[csf('id')];
							 //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
							$sub_sum=$sub_sum+$row[csf('submission_qty')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>

                            <td   align="left"><? echo $row[csf('article_no')];?></td>
                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>


                            <?
                            $total_sizes_qty=0;
                            $total_sizes_qty_subm=0;
                          	foreach($color_size_arr as $type_id=>$data_size)
							{
								foreach($data_size as $size_id=>$data_val)
								{
								$size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                            	?>
                            	<td align="right"><? echo $size_qty; ?></td>
                            	<?
									if($type_id==1)
									{
									$total_sizes_qty_subm+=$size_qty;
									}
									$total_sizes_qty+=$size_qty;
								}
                            }
                            ?>
                            <td align="right"><? echo $total_sizes_qty;?></td>
                            <td align="right"><? echo $total_sizes_qty_subm;?></td>
                            <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                            <td   align="left"><? echo $row[csf('comments')];?> </td>
                            <?
                            $gr_tot_sum+=$total_sizes_qty;
 							$gr_sub_sum+=$total_sizes_qty_subm;
                        }
						?>
                        </tr>
							<tr>
									<td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
 									<td   align="right"><b><? echo $gr_tot_sum;?> </b></td>
 									<td  align="right"><b><? echo $gr_sub_sum;?> </b></td>
									<td colspan="2"></td>
							</tr>
                    </tbody>
                    <tfoot>
                     </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">

             </td>
        </tr>

        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="10" align="center"><strong>Required Accessories</td>
                        </tr>
                        <tr>
								<th width="30" align="center">Sl</th>
								<th width="100" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="100" align="center">Trims Group</th>
								<th width="100" align="center">Description</th>
								<th width="100" align="center">Supplier</th>
								<th width="100" align="center">Brand/Supp.Ref</th>
 								<th width="30" align="center">UOM</th>
								<th width="30" align="center">Req/Dzn </th>
								<th width="30" align="center">Req/Qty </th>
								<th width="80" align="center">Acc.Sour. </th>
								<th width="100" align="center">Acc Delivery Date </th>
								<th width="80" align="center">Remarks </th>
                         </tr>
            	</thead>
                    <tbody>


                        <?
					   $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

						$resultA=sql_select($sql_qryA);
 						$i=1;$k=0;
 						$req_dzn_ra=0;
 						$req_qty_ra=0;
						foreach($resultA as $rowA)
						{
							$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
							$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                            <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                            <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                            <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                            <td  align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                             <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                            <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                            <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                            <td  align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                            <td  align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>

                            <?
                        }

						?>




                        </tr>

                          <tr>
									<td colspan="8" align="center"><b>Total </b></td>
									<!-- <td align="right"><b><? echo number_format($req_dzn_ra,2);?> </b></td> -->
  									<td align="right"  ><b><? echo number_format($req_qty_ra,2);?> </b></td>
  									<td>&nbsp;</td>

 							</tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

         <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="6" align="center"><strong>Required Emebellishment</td>
                        </tr>
                        <tr>
                        	<th width="30" align="center">Sl</th>
                        	<th width="100" align="center">Sample Name</th>
                        	<th width="110" align="center">Garment Item</th>
                        	<th width="110" align="center">Body Part</th>
                        	<th width="100" align="center">Supplier</th>
                        	<th width="60" align="center">Name</th>
                        	<th width="70" align="center">Type</th>
                        	<th width="100" align="center">Emb.Del.Date</th>
                        	<th width="70" align="center">Remarks</th>

                         </tr>
            	</thead>
                    <tbody>


                        <?
                        $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

						$result=sql_select($sql_qry);
 						$k=0;
 						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						foreach($result as $row)
						{

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                            <td  align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                            <td  align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                            <td  align="left">
                            <?
                            if($row[csf('name_re')]==1)
                            {
                          	  echo $emblishment_print_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==2)
                            {
                          	  echo $emblishment_embroy_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==3)
                            {
                          	  echo $emblishment_wash_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==4)
                            {
                          	  echo $emblishment_spwork_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==5)
                            {
                          	  echo $emblishment_gmts_type[$row[csf('type_re')]];
                          	}
                            ?>

                            </td>
                            <td  align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                              <?
                        }

						?>




                        </tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>

               <br>
               	<table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Yarn Required Summary </b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th width="40">Sl</th>
                        	<th>Yarn Desc.</th>
                             <th>Req. Qty</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=440", "booking_no", "supplier_id"  );
				//	echo  "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140";
					$tot_req_qty=0;//sample_development_mst
					$data_array=sql_select("select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.mst_id='$data[1]' and a.form_type=1 group by b.booking_no, b.determin_id, b.count_id, b.copm_one_id, b.percent_one, b.type_id, b.cons_qnty");
					
					//echo "select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1  and b.mst_id='$data[1]' and a.form_type=1";
				
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $yarn_des; ?> </td>
                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$row[csf("cons_qnty")];
						}
					}

					?>
                    <tr>
						<th  colspan="2" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
            
                <br>
                 <br>

               	<table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=440 and booking_no='$data[2]'");
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{

							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}

					?>
                </tbody>
            </table>
             </br>


             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="810" class="rpt_table" >
                	<tr>
                    	<td colspan="6">
							<?
                              //echo signature_table(134, $data[0], "810px");
							  echo signature_table(134, $data[0], "810px",$cbo_template_id);
                            ?>
                        </td>

                    </tr>

                </table>
            </td>
        </tr>
        </table>

    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>

function fnc_generate_Barcodes( valuess, img_id )
{
	var value = valuess;//$("#barcodeValue").val();
	var btype = 'code39';//$("input[name=btype]:checked").val();
	var renderer ='bmp';// $("input[name=renderer]:checked").val();
	var settings = {
	  output:renderer,
	  bgColor: '#FFFFFF',
	  color: '#000000',
	  barWidth: 1,
	  barHeight: 60,
	  moduleSize:5,
	  posX: 10,
	  posY: 20,
	  addQuietZone: 1
	};
	$("#"+img_id).html('11');
	 value = {code:value, rect: false};
	$("#"+img_id).show().barcode(value, btype, settings);
}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>

 <?
 exit();
}

if($action=="sample_requisition_print1")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);die;

	$cbo_template_id=$data[3];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0 ", "id", "size_name");
	$color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0 ", "id", "color_name");
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
	$page_path=$data[4];
	if($page_path==0 && $page_path!='')
	{
		$page_path="../";
	}
	else
	{
		$page_path="../";
		// $page_path="../../";
		
	}
 ?>
	<!-- <style>
		#mstDiv {
			margin:0px auto;
			width:1130px;
		}
		#mstDiv @media print {
			thead {display: table-header-group;}
		}
		@media print{
			html>body table.rpt_table {
				margin-left:12px;
			}
		}
    </style> -->
	<div id="mstDiv">
        <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;" >
            <tr>
                <td align="left" rowspan="4" valign="top" width="300"><img width="150" height="80" src="<? echo base_url($company_img[0][csf("image_location")]); ?>" ></td>
                <td align="center" colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></br>
				<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo ($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo ($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id from sample_development_mst where  id='$data[1]' and entry_form_id=449 and is_deleted=0 and status_active=1";
                    $dataArray=sql_select($sql);
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$quotation_id=$dataArray[0][csf("quotation_id")];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date");
                    }
					if($dataArray[0][csf("sample_stage_id")]==2)
                    {
                       $bodywashcolor=return_field_value("color","wo_quotation_inquery","id='$quotation_id'");
                    }
                    $sqls="SELECT style_desc, supplier_id, revised_no,team_leader, buyer_req_no, source, booking_date, attention from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and is_deleted=0 and status_active=1";
                    $dataArray_book=sql_select($sqls);
                    ?>
			
			
			</td>
                <td align="center" width="200">
					<?
                    $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                    $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                    $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="3" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Program Without Order</u></strong></td>
                <td colspan="2" width="250"><b>Approved By :<?=$approved_by ?></b> </br><b>Approved Date :<?=$approved_date ?></b> </td>
            </tr>
        </table>

        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;" >
        	<tr>
                <td width="130"><strong>System No.: </strong></td>
                <td width="130"><strong><?=$dataArray[0][csf("requisition_number")];?></strong></td>
                <td width="130"><strong>Booking Date:</strong></td>
                <td width="130"><?=$dataArray_book[0][csf('booking_date')];?></td>
                <td width="130"><strong>Sample Stage:</strong></td>
                <td width="130"><?=$sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                <td width="130"><strong>Revise:</strong></td>
                <td><?=$dataArray_book[0][csf('revised_no')];?></td>
            </tr>
            <tr>
                <td><strong>W/O No: </strong></td>
                <td><?=$data[2];?></td>
                <td><strong>Style Ref:</strong></td>
                <td><?=$dataArray[0][csf('style_ref_no')];?></td>
                <td><strong>Style Desc.:</strong></td>
                <td><?=$dataArray_book[0][csf('style_desc')];?></td>
                <td><strong>Sample Sub Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Buyer Name: </strong></td>
                <td><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td><strong>Season:</strong></td>
                <td><?=$season_arr[$dataArray[0][csf('season_buyer_wise')]];?></td>
                <td><strong>Season Year: </strong></td>
                <td><?=$dataArray[0][csf('season_year')];?></td>
                <td><strong>Brand:</strong></td>
                <td><?=$brandArr[$dataArray[0][csf('brand_id')]];?></td>
            </tr>
            <tr>
            	<td><strong>BH Merchant:</strong></td>
                <td><?=$dataArray[0][csf('bh_merchant')];?></td>
                <td><strong>Attention:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;" ><?=$dataArray_book[0][csf('attention')];?></td>
                <td><strong>Buyer Ref:</strong></td>
                <td><?=$dataArray[0][csf('buyer_ref')];?></td>
                <td><strong>Product Dept:</strong></td>
                <td><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
            </tr>
            <tr>
            	<td><strong>Supplier:</strong></td>
                <td><?=$supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
                <td><strong>Est. Ship Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
                <td><strong>Team Leader:</strong></td>
                <td><?=$team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
                <td title="Booking "><strong>Dealing Merchant:</strong></td>
                <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
            </tr>
            <tr>
            	<td><strong>Body/Wash Color:</strong></td>
                <td><?=$bodywashcolor; ?></td>
                <td><strong>Remarks/Desc:</strong></td>
                <td colspan="3" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('remarks')];?></td>
				<td><strong>Requisition Date:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('requisition_date')];?></td>
            </tr>
        </table>
        <br>
		
        <?
        $sample_names = array();

        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge,measurement_chart, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$data[1]' order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge,a.measurement_chart, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id='$data[1]' order by a.id asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Wash Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='Self Qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='Test Qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='Plan Qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Wash Qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
       
        ?>
        <div style="width: 1570px">
        	<div style="width: 1170px; float: left">
        		<table align="left" cellspacing="0" border="1" width="1170" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
		                </tr>
		                <tr>
		                    <th width="30" rowspan="2">SL</th>
		                    <th width="100" rowspan="2">Sample Name</th>
		                    <th width="120" rowspan="2">Garment Item</th>
		                    <th width="70" rowspan="2">Sample Delv.  Date</th>
		                    <th width="70" rowspan="2">Color</th>
		                        <?
		                        $tot_row_td=0;
		                        foreach($color_size_arr as $type_id=>$val)
		                        {
		                            ?>
		                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
		                            <?
		                        }
		                        ?>
		                    <th rowspan="2" width="55">Total</th>
		                    <th rowspan="2" width="55">Submission Qty</th>
		                    <th rowspan="2" width="70">Buyer Submission Date</th>
							<th rowspan="2" width="70" >M-Chart No</th>
							<th rowspan="2">Remarks</th>
		                </tr>
		                <tr>
		                    <?
		                    foreach($color_size_arr as $type_id=>$data_size)
		                    {
		                        foreach($data_size as $size_id=>$data_val)
		                        {
		                            $tot_row_td++;
		                            ?>
		                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
		                            <?
		                        }
		                    }
		                    ?>
		                </tr>
		            </thead>
		            <tbody>
		                <?
		                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
		                foreach($result as $row)
		                {
		                    $dtls_ids=$row[csf('id')];
		                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
		                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
		                    $k++;
		                    $sample_names[$sample_library[$row[csf('sample_name')]]]=$sample_library[$row[csf('sample_name')]];
		                    ?>
		                    <tr>
		                        <td align="center"><?=$k;?></td>
		                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
		                        <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
		                        <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
		                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
		                        <?
		                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
		                        foreach($color_size_arr as $type_id=>$data_size)
		                        {
		                            foreach($data_size as $size_id=>$data_val)
		                            {
		                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
		                                ?>
		                                <td align="right"><?=$size_qty;?></td>
		                                <?
		                                if($type_id==1)
		                                {
		                                $total_sizes_qty_subm+=$size_qty;
		                                }
		                                $total_sizes_qty+=$size_qty;
		                            }
		                        }
		                        ?>
		                        <td align="right"><?=$total_sizes_qty;?></td>
		                        <td align="right"><?=$total_sizes_qty_subm;?></td>
		                        <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
								<td align="center"><?=$row[csf('measurement_chart')];?> </td>
		                        <td align="left"><?=$row[csf('comments')];?> </td>
		                    </tr>
		                    <?
		                    $gr_tot_sum+=$total_sizes_qty;
		                    $gr_sub_sum+=$total_sizes_qty_subm;
		                }
		                ?>
		                <tr>
		                    <td colspan="<?=5 + $tot_row_td;?>" align="right"><b>Total</b></td>
		                    <td align="right"><b><?=$gr_tot_sum;?> </b></td>
		                    <td align="right"><b><?=$gr_sub_sum;?> </b></td>
		                    <td colspan="2">&nbsp;</td>
							<td colspan="2">&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
		
        	</div>
        	<div style="width: 400px; float: left">
        		<? 
        			$image_arr = sql_select("select image_location,form_name  from common_photo_library  where master_tble_id='$data[1]' and form_name in ('samplereqbackimage_1','samplereqfrontimage_1') and is_deleted=0 and file_type=1");
        			foreach ($image_arr as $row) {
        				if($row[csf('form_name')] == 'samplereqfrontimage_1')
        				{
        					$samplereqfrontimage = $row[csf('image_location')];
        				}
        				if($row[csf('form_name')] == 'samplereqbackimage_1')
        				{
        					$samplereqbackimage = $row[csf('image_location')];
        				}
        			}
        		?>
        		<table align="left" cellspacing="0" border="1" width="340" class="rpt_table" rules="all">
        		<tr>
        			<td width="170" align="center"><b>Front Image</b></td>
        			<td width="170" align="center"><b>Back Image</b></td>
        		</tr>
        		<tr>
        			<td><img width="170" height="200" src="<? echo base_url($samplereqfrontimage); ?>"></td>
        			<td><img width="170" height="200" src="<? echo base_url($samplereqbackimage); ?>"></td>

        		</tr>
        		</table>
        	</div>
        </div>

		<?
		// $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id,a.body_part_type_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id  and b.grey_fab_qnty=c.grey_fabric and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";

        $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id,a.body_part_type_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.form_type=1 and b.qnty>0 and a.status_active=1 and a.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";
		// and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
         //echo $sql_fab; 
        $sql_fab_arr=array(); $determination_id='';
        foreach(sql_select($sql_fab) as $vals)
        {
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
			
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["exdata"]=change_date_format($vals[csf("delivery_date")]).'__'.$vals[csf("fabric_source")].'__'.$vals[csf("uom_id")].'__'.$vals[csf("width_dia_id")].'__'.$vals[csf("remarks_ra")].'__'.$vals[csf("color_type_id")].'__'.$vals[csf("weight_type")].'__'.$vals[csf("cuttable_width")].'__'.$vals[csf("determination_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
			if($determination_id=="") $determination_id=$vals[csf("determination_id")]; else $determination_id.=','.$vals[csf("determination_id")];
        }
        $sample_item_wise_span=array();
		
		$sqlRd="select id, fabric_ref, rd_no from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($determination_id)";
		$sqlRdData=sql_select($sqlRd); $rdRefArr=array();
		foreach($sqlRdData as $row)
		{
			$rdRefArr[$row[csf("id")]]['ref']=$row[csf("fabric_ref")];
			$rdRefArr[$row[csf("id")]]['rd_no']=$row[csf("rd_no")];
		}
		
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
      /*echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1555"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="20">Required Fabric</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Sample Type</th>
                    <th width="80">Fab. Deli Date</th>
                    <th width="40">Fabric Source</th>
                    <th width="120">Body Part</th>
					<th width="100">Body Part Yype</th>
                    <th width="80">RD No</th>
                    <th width="80">Ref. No</th>
                    <th width="200">Fabric Desc & Composition</th>
                    <th width="80">Color Type</th>
                    <th width="80">Gmt. Color</th>
                    <th width="80">Fab. Color</th>
                    <th width="55">Fabric Weight</th>
                    <th width="55">F.Weight Type</th>
                    <th width="60">Full Width</th>
                    
                    <th width="55">Cuttable Width</th>
                    <th width="55">Width Type</th>
                    
                    <th width="40">UOM</th>
                    <th width="60">Fin Fabric Qty</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?
                $p=1; $total_finish=0; 
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
												$exData=explode("__",$value["exdata"]);
												$delivery_date=$fabricSource=$uom_id=$width_dia_id=$remarks_ra=$color_type_id=$weight_type=$cuttable_width=$determination_id='';
												
												$delivery_date=$exData[0];
												$fabricSource=$exData[1];
												$uom_id=$exData[2];
												$width_dia_id=$exData[3];
												$remarks_ra=$exData[4];
												$color_type_id=$exData[5];
												$weight_type=$exData[6];
												$cuttable_width=$exData[7];
												$determination_id=$exData[8];
												
                                                ?>
                                                <tr>
                                                    <td align="center" style="word-wrap: break-word;word-break: break-all;"><?=$p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                        
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    ?>
                                                    <td align="center"><?=$delivery_date; ?> </td>
                                                    <td style="word-break:break-all"><?=$fabric_source[$fabricSource]; ?></td>
                                                    <td style="word-break:break-all"><?=$body_part[$body_part_id]; ?></td>
													<td style="word-break:break-all"><?=$body_part_type[$body_part_type_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['rd_no']; ?></td>
                                                    <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['ref']; ?></td>
                                                    <td style="word-break:break-all"><?=$fab_id;?></td>
                                                    
                                                    <td style="word-break:break-all"><?=$color_type[$colorType]; ?></td>
                                                    <td style="word-break:break-all"><?=$color_library[$gmts_color_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$contrast_id; ?></td>
                                                    <td style="word-break:break-all"><?=$gsm_id; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_weight_type[$weight_type]; ?></td>
                                                    <td style="word-break:break-all"><?=$dia_id; ?></td>
                                                    <td style="word-break:break-all"><?=$cuttable_width; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_typee[$width_dia_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$unit_of_measurement[$uom_id]; ?></td>
                                                    <td align="right"><?=number_format($value["qnty"], 2); ?></td>
                                                    <td style="word-break:break-all"><?=$remarks_ra; ?></td>
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="17" align="right"><b>Total</b></th>
                    <th align="right"><?=number_format($total_finish, 2); ?></th>
                    <th>&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <br/>
		      
		        <table align="left" cellspacing="0" border="1" width="1455" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <td colspan="10" align="center"><strong>Required Accessories</td>
		                </tr>
		                <tr>
		                    <th width="30">Sl</th>
		                    <th width="100">Sample Name</th>
		                    <th width="120">Garment Item</th>
		                    <th width="100">Trims Group</th>
		                    <th width="100">Description</th>
		                    <th width="100">Supplier</th>
		                    <th width="100">Brand/Supp.Ref</th>
		                    <th width="30">UOM</th>
		                    <th width="30">Req/Dzn</th>
		                    <th width="30">Req/Qty</th>
		                    <th width="80">Acc. Source</th>
		                    <th width="100">Acc Delivery Date</th>
		                    <th width="150">Remarks </th>
		                </tr>
		            </thead>
		            <tbody>
						<?
		                $sql_qryA="SELECT id, sample_mst_id, sample_name_ra, gmts_item_id_ra, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, delivery_date, supplier_id, nominated_supp_multi, fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

		                $resultA=sql_select($sql_qryA);
		                $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
		                foreach($resultA as $rowA)
		                {
							$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
							$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
							
							$nominated_supp_str="";
							 $exnominated_supp=explode(",",$rowA[csf('nominated_supp_multi')]);
							 foreach($exnominated_supp as $supp)
							 {
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_library[$supp]; else $nominated_supp_str.=','.$supplier_library[$supp];
							 }
							$k++;
							?>
							<tr>
		                        <td align="center"><? echo $k;?></td>
		                        <td style="word-break:break-all"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $rowA[csf('description_ra')];?></td>
		                        <td style="word-break:break-all"><?=$nominated_supp_str;?></td>
		                        <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
		                        <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
		                        <td align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
		                        <td align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
		                        <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
		                        <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
		                        <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
							</tr>
							<?
		                }
		                ?>
		                <tr>
		                    <td colspan="9" align="center"><b>Total </b></td>
		                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
		                    <td>&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
        
      	  <br>
        
			<?
			$sqlEmbl="SELECT name_re from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 group by name_re order by name_re DESC";
			$sqlEmblData=sql_select($sqlEmbl);
			
			foreach($sqlEmblData as $erow)
			{
				$embNameId=$erow[csf('name_re')];
			?>
			  <br>&nbsp;
			<table align="left" cellspacing="0" border="1" width="1455" class="rpt_table" rules="all">
				<thead>
					<tr>
						<td colspan="8" align="center" width="100"><strong>Required <?=$emblishment_name_array[$erow[csf('name_re')]]; ?></td>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Sample Name</th>
						<th width="110">Garment Item</th>
						<th width="110">Body Part</th>
						<th width="110">Body Part Type</th>
						<th width="100">Supplier</th>
						<th width="70"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Type</th>
						<th width="100"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Del.Date</th>
						<th width="150">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$sql_qry="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id,body_part_type_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0 and name_re='$embNameId' and status_active=1 order by id asc";
					//echo $sql_qry;die;

					$result=sql_select($sql_qry); $k=0;
				// $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
					foreach($result as $row)
					{
						$k++;
						?>
						<tr>
							<td align="center"><? echo $k;?></td>
							<td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
							<td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
							<td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
							<td align="left"><? echo $body_part_type[$row[csf('body_part_type_id')]];?></td>
							<td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
							<td align="left">
								<?
								if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
								?>
							</td>
							<td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
							<td align="left"><? echo $row[csf('remarks_re')];?></td>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
			
			<br>
        <?
		}
		?>
          
               	
        <table style="margin-top:10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=440 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
        </br>
         <div style="clear:both;"></div>
         <table style="margin-top:10px;" class="rpt_table" width="680" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th width="140">Sample Type</th>
                    <th width="80">Pattern Date</th>
                    <th width="80">Cutting Date</th>
                    <th width="80">Sewing Date</th>
                    <th width="80">Wash Send Date</th>
                    <th width="80">Wash Receive Date</th>
                    <th >Finishing Date</th>
                </tr>
            </thead>
            <tbody>
				<?
				$sql_smaple_plan = "select sample_plan from sample_requisition_acknowledge where entry_form = 54 and sample_mst_id ='$data[1]' and status_active=1 and acknowledge_status=1";
				// echo $sql_smaple_plan;
                $data_array_sample_plan=sql_select($sql_smaple_plan);

				foreach($data_array_sample_plan as $row){
					$data_sample=explode("----",$row[csf('sample_plan')]);
				}				
                if(count($data_sample)>0)
                {
					$l=1;
					foreach( $data_sample as $key=>$row )
					{
						$sample_plan = explode("**",$row);
                        // $finishing_date_exp = explode('-',$sample_plan[6]);
						// $finishing_date = $finishing_date_exp[0].'-'.$finishing_date_exp[1].'-'.$finishing_date_exp[2];
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $sample_library[$sample_plan[0]] ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[1])) echo change_date_format($sample_plan[1]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[2])) echo change_date_format($sample_plan[2]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[3])) echo change_date_format($sample_plan[3]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[4])) echo change_date_format($sample_plan[4]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[5])) echo change_date_format($sample_plan[5]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[6])) echo change_date_format($sample_plan[6]); ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
        </br>
        <? echo signature_table(207, $data[0], "930px",$cbo_template_id); ?>
    </div>
    <script type="text/javascript" src="<?php echo $page_path; ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $page_path; ?>js/jquerybarcode.js"></script>
    <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}
if($action=="sample_requisition_print3") //print button-3 md mamun ahmed sagor 20-06-2022
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier", "id", "address_1");
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0 ", "id", "size_name");
	$color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0 ", "id", "color_name");
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
 ?>
	<!-- <style>
		#mstDiv {
			margin:0px auto;
			width:1130px;
		}
		#mstDiv @media print {
			thead {display: table-header-group;}
		}
		@media print{
			html>body table.rpt_table {
				margin-left:12px;
			}
		}
    </style> -->
	<div id="mstDiv">
        <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;" >
            <tr>
                <td align="left" rowspan="4" valign="top" width="300"><img width="150" height="80" src="../../<? echo $company_img[0][csf("image_location")]; ?>" ></td>
                <td align="center" colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></br>
				<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo ($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo ($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id from sample_development_mst where  id='$data[1]' and entry_form_id=449 and is_deleted=0 and status_active=1";
                    $dataArray=sql_select($sql);
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$quotation_id=$dataArray[0][csf("quotation_id")];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date");
                    }
					if($dataArray[0][csf("sample_stage_id")]==2)
                    {
                       $bodywashcolor=return_field_value("color","wo_quotation_inquery","id='$quotation_id'");
                    }
                    $sqls="SELECT style_desc, supplier_id, revised_no,team_leader, buyer_req_no, source, booking_date, attention,exchange_rate,currency_id from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and is_deleted=0 and status_active=1";
                    $dataArray_book=sql_select($sqls);
                    ?>
			
			
			</td>
                <td align="center" width="200">
					<?
                    $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                    $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                    $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="5" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Fabric Booking</u></strong></td>
               
            </tr>
        </table>

        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;" >
        	<tr>
                <td width="130"><strong>Booking No: </strong></td>
                <td width="130"><strong><?=$data[2];?></strong></td>
                <td width="130"><strong>Booking Date:</strong></td>
                <td width="130"><?=$dataArray_book[0][csf('booking_date')];?></td>
                <td width="130"><strong>Fab. Delivery Date</strong></td>
                <td width="130"><?=$dataArray_book[0][csf('booking_date')];?></td>
            </tr>
            <tr>
				<td><strong>Buyer Name: </strong></td>
                <td><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
				<td><strong>Supplier:</strong></td>
                <td><?=$supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
                <td><strong>Supplier Address:</strong></td>
                <td><?=$supplier_address_arr[$dataArray_book[0][csf('supplier_id')]];?></td>   
            </tr>
			<tr>
            	<td><strong>Currency:</strong></td>
                <td><?=$currency[$dataArray_book[0][csf('currency_id')]];?></td>
				<td><strong>Conversion Rate</strong></td>
                <td><?=$dataArray_book[0][csf('exchange_rate')];?></td>
                <td><strong>Attention:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;" ><?=$dataArray_book[0][csf('attention')];?></td>
                
               
            </tr>
            <tr>
               
                <td><strong>Season:</strong></td>
                <td><?=$season_arr[$dataArray[0][csf('season_buyer_wise')]];?></td>
				<td><strong>Buyer Req. No </strong></td>
                <td><?=$dataArray_book[0][csf('buyer_req_no')];?></td>
				<td title="Booking "><strong>Dealing Merchant:</strong></td>
                <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
              
            </tr>
           
         
           
        </table>
        <br>
		
        <?

        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$data[1]' order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id='$data[1]' order by a.id asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Wash Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='Self Qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='Test Qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='Plan Qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Wash Qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
        ?>
        

		<?
         $sql_fab="SELECT b.id as dtlsid,a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color,a.rate,a.amount from sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.body_part=a.body_part_id and c.lib_yarn_count_deter_id=a.determination_id and c.GMTS_COLOR=b.color_id  and a.sample_mst_id=c.style_id and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";
		 //and a.dia=c.dia 
		// and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
       //  echo $sql_fab; die;
        $sql_fab_arr=array(); $determination_id='';
        foreach(sql_select($sql_fab) as $vals)
        {
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["rate"]=$vals[csf("rate")];
			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["amount"]+=$vals[csf("amount")];
			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["process_loss_percent"]=$vals[csf("process_loss_percent")];

			
			 $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["gmts_item_id"]=$vals[csf("gmts_item_id")];
			
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["exdata"]=change_date_format($vals[csf("delivery_date")]).'__'.$vals[csf("fabric_source")].'__'.$vals[csf("uom_id")].'__'.$vals[csf("width_dia_id")].'__'.$vals[csf("remarks_ra")].'__'.$vals[csf("color_type_id")].'__'.$vals[csf("weight_type")].'__'.$vals[csf("cuttable_width")].'__'.$vals[csf("determination_id")];
			if($colorDtlsChkArr[$vals[csf("dtlsid")]]=='')
			{
				$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
			$colorDtlsChkArr[$vals[csf("dtlsid")]]=$vals[csf("dtlsid")];
			}
			

			if($determination_id=="") $determination_id=$vals[csf("determination_id")]; else $determination_id.=','.$vals[csf("determination_id")];
        }
        $sample_item_wise_span=array();
		
		$sqlRd="select id, fabric_ref, rd_no from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($determination_id)";
		$sqlRdData=sql_select($sqlRd); $rdRefArr=array();
		foreach($sqlRdData as $row)
		{
			$rdRefArr[$row[csf("id")]]['ref']=$row[csf("fabric_ref")];
			$rdRefArr[$row[csf("id")]]['rd_no']=$row[csf("rd_no")];
		}
		
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
 /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1395"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Sample Type</th>
					<th width="100">Garment Item</th>
					<th width="120">Body Part</th>
                   
                    <th width="80">GSM/ Ounch</th>
                  
                  
                    <th width="200">Fabric Desc & Composition</th>
                    <th width="80">Color Type</th>
                    <th width="80">Gmt. Color</th>
                    <th width="80">Fab. Color</th>
                    <th width="55">Fabric Weight</th>
                    <th width="55">F.Weight Type</th>
                    <th width="60">Full Width</th>                    
                    <th width="55">Process loss percent</th>
                    <th width="55">Width Type</th>                    
                    <th width="40">UOM</th>
                    <th width="60">Fin Fabric Qty</th>
                    <th width="60">Rate</th>
					<th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?
                $p=1; $total_finish=0; 
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
												$exData=explode("__",$value["exdata"]);
												$delivery_date=$fabricSource=$uom_id=$width_dia_id=$remarks_ra=$color_type_id=$weight_type=$cuttable_width=$determination_id='';
												
												$delivery_date=$exData[0];
												$fabricSource=$exData[1];
												$uom_id=$exData[2];
												$width_dia_id=$exData[3];
												$remarks_ra=$exData[4];
												$color_type_id=$exData[5];
												$weight_type=$exData[6];
												$cuttable_width=$exData[7];
												$determination_id=$exData[8];
												
                                                ?>
                                                <tr>
                                                    <td align="center" style="word-wrap: break-word;word-break: break-all;"><?=$p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
														<td rowspan="<?=$rowspan;?>" align="center"><?=$garments_item[$value["gmts_item_id"]];?></td>
                                                        
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    ?>
												    <td style="word-break:break-all"><?=$body_part[$body_part_id]; ?></td>
                                                  
                                                    <td style="word-break:break-all"><?=$fabric_source[$fabricSource]; ?></td>
                                                
                                                   
                                                    <td style="word-break:break-all"><?=$fab_id;?></td>                                                    
                                                    <td style="word-break:break-all"><?=$color_type[$colorType]; ?></td>
                                                    <td style="word-break:break-all"><?=$color_library[$gmts_color_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$contrast_id; ?></td>
                                                    <td style="word-break:break-all"><?=$gsm_id; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_weight_type[$weight_type]; ?></td>
                                                    <td style="word-break:break-all"><?=$dia_id; ?></td>
													<td style="word-break:break-all"><?=$value["process_loss_percent"]; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_typee[$width_dia_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$unit_of_measurement[$uom_id]; ?></td>
                                                    <td align="right"><?=number_format($value["qnty"], 2); ?></td>
                                                    <td style="word-break:break-all" align="right"><?=$value["rate"]; ?></td>
													<td style="word-break:break-all" align="right"><?=$value["qnty"]*$value["rate"]; ?></td>
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
												$total_amount +=$value["qnty"]*$value["rate"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="15" align="right"><b>Total</b></th>
                    <th align="right"><?=number_format($total_finish, 2); ?></th>
                    <th>&nbsp;</th>
					<th align="right"><?=number_format($total_amount, 2); ?></th>
                </tr>
            </tbody>
        </table>
        <br/>
		      
		      
        
      	
          
               	
        <table style="margin-top:10px;" class="rpt_table" width="600"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=440 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
		<table style="margin-top:10px;" class="rpt_table" width="600"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="4">Approval Status</th>
                    
                </tr>
            </thead>
            <tbody>
						<tr>
                            <th>&nbsp; Sl</th>
                            <th style="word-break:break-all">&nbsp;Name </th>
							<th>&nbsp;Approval Date </th>
                            <th style="word-break:break-all">&nbsp;Approval No </th>
						</tr>
						<tr>
                            <td>&nbsp; </td>
                            <td style="word-break:break-all">&nbsp; </td>
							<td>&nbsp; </td>
                            <td style="word-break:break-all">&nbsp; </td>
						</tr>
            </tbody>
        </table>
        </br>
        <? echo signature_table(207, $data[0], "930px",$cbo_template_id); ?>
    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}
if ($action=="sizeinfo_popup")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
		?>

    <script>
		var permission='<? echo $permission; ?>';
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name order by size_name", "size_name" ), 0, -1); ?> ];

		function total_submission_qty(inc)
		{
			// this function will calculate the sum of the BH Qty,Plan Qty,Dyeing,Test,Self Qty in Sizeinfo popup related with Sample Details Module for every row
			var tot_row=$('#size_tbl tbody tr').length;
			 var total="";
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);
			}
		}

		function calculate_total_qnty_by_type()
		{
			var tot_row=$('#size_tbl tbody tr').length;
			var total_bhqnty=""; var total_plqnty=""; var total_dyqnty=""; var total_testqnty=""; var total_selfqnty=""; var total_all_qnty=""; var total='';
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);

				var bh_qty=$("#txtbhqty_"+i).val()*1;
				total_bhqnty=total_bhqnty*1+$("#txtbhqty_"+i).val()*1;

				var pl_qty=$("#txtplqty_"+i).val()*1;
				total_plqnty=total_plqnty*1+$("#txtplqty_"+i).val()*1;

				var dy_qty=$("#txtdyqty_"+i).val()*1;
				total_dyqnty=total_dyqnty*1+$("#txtdyqty_"+i).val()*1;

				var test_qty=$("#txttestqty_"+i).val()*1;
				total_testqnty=total_testqnty*1+$("#txttestqty_"+i).val()*1;

				var self_qty=$("#txtselfqty_"+i).val()*1;
				total_selfqnty=total_selfqnty*1+$("#txtselfqty_"+i).val()*1;

				var total_qty=$("#txttotalqty_"+i).val()*1;
				total_all_qnty=total_all_qnty*1+$("#txttotalqty_"+i).val()*1;
			}
			document.getElementById('txt_total_bh_qty').value=total_bhqnty;
			document.getElementById('txt_total_pl_qty').value=total_plqnty;
			document.getElementById('txt_total_dy_qty').value=total_dyqnty;
			document.getElementById('txt_total_test_qty').value=total_testqnty;
			document.getElementById('txt_total_self_qty').value=total_selfqnty;
			document.getElementById('txt_total_all_qty').value=total_all_qnty;
		}


		function add_break_down_tr( i )
		{
			var row_num=$('#size_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#size_tbl tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return '' }
				});

				}).end().appendTo("#size_tbl");

				$("#size_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);

				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				add_auto_complete(i);
				set_all_onclick();
			}
		}

		function fn_deleteRow(rowNo)
		{
			var numRow=$('#size_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#size_tbl tbody tr:last').remove();
			}
			else
			{
				$("#txtsizename_"+rowNo).val('');
				$("#txtgmtpcs_"+rowNo).val('');
				$("#txtgmtbhqty_"+rowNo).val('');
				$("#sizeupid_"+rowNo).val('');
			}
		}

		function add_auto_complete(i)
		{
			$(document).ready(function(e)
			 {
					$("#txtsizename_"+i).autocomplete({
					 source: str_size
				  });
			 });
		}

		function fnc_close( )
		{
			var rowCount = $('#size_tbl tr').length-1;
			//alert( rowCount );return;
			var breck_down_data="";
			for(var i=1; i<=rowCount; i++)
			{
				if(breck_down_data=="")
				{
					breck_down_data+=$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
				else
				{
					breck_down_data+="__"+$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
			}
			//alert (breck_down_data);
			document.getElementById('hidden_size_data').value=breck_down_data;
			document.getElementById('hidden_total_self_and_all_data').value=document.getElementById('txt_total_self_qty').value+'___'+document.getElementById('txt_total_all_qty').value;
			parent.emailwindow.hide();
		}
    </script>

    <body onLoad="add_auto_complete(1);" >
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:680px;">
            <table align="center" cellspacing="0" width="680" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                    <th width="110">Size</th>
                    <th width="70">BH Qty</th>
                    <th width="70">Plan</th>
                    <th width="70">Wash</th>
                    <th width="70">Test</th>
                    <th width="70">Self</th>
                    <th width="70">Total</th>
                    <th>
                        <Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<?=$txt_style_id; ?>" style="width:30px" />
                        <Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<?=$update_id_dtl; ?>" style="width:30px" />
                    </th>
                </thead>
                <tbody>

                <?
					$data_all=explode('__',$data);
					$count_tr=count($data_all);
					if($count_tr>0)
					{
						$i=1;
						foreach ($data_all as $size_data)
						{
							$size_name=''; $bh_qty=0; $pl_qty=0; $dy_qty=0; $test_qty=0; $self_qty=0; $totalqty=0;
							$ex_size_data=explode('_',$size_data);
							$size_name=$ex_size_data[0];
							$bh_qty=$ex_size_data[1];
							$pl_qty=$ex_size_data[2];
							$dy_qty=$ex_size_data[3];
							$test_qty=$ex_size_data[4];
							$self_qty=$ex_size_data[5];
							$totalqty=$ex_size_data[6];
						?>
							<tr id="row_<? echo $i; ?>" >
								<td><input name="txtsizename[]" class="text_boxes" ID="txtsizename_<? echo $i; ?>" value="<? echo $size_name; ?>" style="width:100px" autofocus/><input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $i; ?>" value="" style="width:30px" ></td>

								 <td><input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $bh_qty; ?>" /></td>

								<td><input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $pl_qty; ?>" /></td>

								<td><input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $dy_qty; ?>" /></td>

							   <td><input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_qty; ?>" /></td>

							   <td><input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $self_qty; ?>"/></td>

							   <td><input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_<? echo $i; ?>" style="width:70px"  readonly value="<? echo $totalqty; ?>" /></td>
								<td align="center">
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
						<?
						$i++;
						}
					}
					else
					{
						?>
						<tr id="row_1">
							<td width="110" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_1" value="" style="width:100px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_1" value="" style="width:30px"></td>

							 <td width="70" align="center" ><Input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

                           <td width="70" align="center" ><Input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>
                           <td width="70" align="center" ><Input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>
                           <td width="70" align="center" ><Input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_1" style="width:70px"  readonly /></td>
							<td align="center">
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
							</td>
						</tr>
					<?
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="680" class="rpt_table" border="1" rules="all" id="" >
				<tr>
					<td width="110">&nbsp;</td>
					<td width="70" align="center"><Input name="txt_total_bh_qty" class="text_boxes_numeric" ID="txt_total_bh_qty" style="width:70px" value="<? echo $total_bhqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_pl_qty" class="text_boxes_numeric" ID="txt_total_pl_qty" style="width:70px" value="<? echo $total_plqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_dy_qty" class="text_boxes_numeric" ID="txt_total_dy_qty" style="width:70px" value="<? echo $total_dyqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_test_qty" class="text_boxes_numeric" ID="txt_total_test_qty" style="width:70px" value="<? echo $total_testqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_self_qty" class="text_boxes_numeric" ID="txt_total_self_qty" style="width:70px" value="<? echo $total_selfqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_all_qty" class="text_boxes_numeric" ID="txt_total_all_qty" style="width:70px" value="<? echo $total_all_qty; ?>" readonly /></td>
					 <td>&nbsp;</td>
				</tr>
                <tr>
                    <td colspan="8" align="center" class="">
                        <input type="hidden" name="hidden_size_data" id="hidden_size_data" class="text_boxes /">
                        <input type="hidden" name="hidden_total_self_and_all_data" id="hidden_total_self_and_all_data" class="text_boxes /">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script>calculate_total_qnty_by_type(); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="sizeinfo_popup_mouseover")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
		?>

    <script>
		var permission='<? echo $permission; ?>';
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name order by size_name", "size_name" ), 0, -1); ?> ];

		function total_submission_qty(inc)
		{
			// this function will calculate the sum of the BH Qty,Plan Qty,Dyeing,Test,Self Qty in Sizeinfo popup related with Sample Details Module for every row
			var tot_row=$('#size_tbl tbody tr').length;
			 var total="";
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);
			}
		}

		function calculate_total_qnty_by_type()
		{
			var tot_row=$('#size_tbl tbody tr').length;
			var total_bhqnty=""; var total_plqnty=""; var total_dyqnty=""; var total_testqnty=""; var total_selfqnty=""; var total_all_qnty=""; var total='';
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);

				var bh_qty=$("#txtbhqty_"+i).val()*1;
				total_bhqnty=total_bhqnty*1+$("#txtbhqty_"+i).val()*1;

				var pl_qty=$("#txtplqty_"+i).val()*1;
				total_plqnty=total_plqnty*1+$("#txtplqty_"+i).val()*1;

				var dy_qty=$("#txtdyqty_"+i).val()*1;
				total_dyqnty=total_dyqnty*1+$("#txtdyqty_"+i).val()*1;

				var test_qty=$("#txttestqty_"+i).val()*1;
				total_testqnty=total_testqnty*1+$("#txttestqty_"+i).val()*1;

				var self_qty=$("#txtselfqty_"+i).val()*1;
				total_selfqnty=total_selfqnty*1+$("#txtselfqty_"+i).val()*1;

				var total_qty=$("#txttotalqty_"+i).val()*1;
				total_all_qnty=total_all_qnty*1+$("#txttotalqty_"+i).val()*1;
			}
			document.getElementById('txt_total_bh_qty').value=total_bhqnty;
			document.getElementById('txt_total_pl_qty').value=total_plqnty;
			document.getElementById('txt_total_dy_qty').value=total_dyqnty;
			document.getElementById('txt_total_test_qty').value=total_testqnty;
			document.getElementById('txt_total_self_qty').value=total_selfqnty;
			document.getElementById('txt_total_all_qty').value=total_all_qnty;
		}


		function add_break_down_tr( i )
		{
			var row_num=$('#size_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#size_tbl tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return '' }
				});

				}).end().appendTo("#size_tbl");

				$("#size_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);

				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				add_auto_complete(i);
				set_all_onclick();
			}
		}

		function fn_deleteRow(rowNo)
		{
			var numRow=$('#size_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#size_tbl tbody tr:last').remove();
			}
			else
			{
				$("#txtsizename_"+rowNo).val('');
				$("#txtgmtpcs_"+rowNo).val('');
				$("#txtgmtbhqty_"+rowNo).val('');
				$("#sizeupid_"+rowNo).val('');
			}
		}

		function add_auto_complete(i)
		{
			$(document).ready(function(e)
			 {
					$("#txtsizename_"+i).autocomplete({
					 source: str_size
				  });
			 });
		}

		function fnc_close( )
		{
			var rowCount = $('#size_tbl tr').length-1;
			//alert( rowCount );return;
			var breck_down_data="";
			for(var i=1; i<=rowCount; i++)
			{
				if(breck_down_data=="")
				{
					breck_down_data+=$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
				else
				{
					breck_down_data+="__"+$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
			}
			//alert (breck_down_data);
			document.getElementById('hidden_size_data').value=breck_down_data;
			document.getElementById('hidden_total_self_and_all_data').value=document.getElementById('txt_total_self_qty').value+'___'+document.getElementById('txt_total_all_qty').value;
			parent.emailwindow.hide();
		}
    </script>

    <body onLoad="add_auto_complete(1);" >
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:700px;">
            <table align="center" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                    <th width="110" >Size</th>
                    <th width="70" >BH Qty</th>
                    <th width="70" >Plan</th>
                    <th width="70" >Wash</th>
                    <th width="70" >Test</th>
                    <th width="70" >Self</th>
                    <th width="70" >Total</th>


                    <th><Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $txt_style_id; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $update_id_dtl; ?>" style="width:30px" />
                    <!--<Input type="hidden" name="samp_color_id" class="text_boxes" ID="samp_color_id" value="<? //echo $txt_sample_color; ?>" style="width:30px" />-->
                    </th>
                </thead>
                <tbody>

                <?
					$data_all=explode('__',$data);
					$count_tr=count($data_all);
					if($count_tr>0)
					{
						$i=1;
						foreach ($data_all as $size_data)
						{
							$size_name=''; $bh_qty=0; $pl_qty=0; $dy_qty=0; $test_qty=0; $self_qty=0; $totalqty=0;
							$ex_size_data=explode('_',$size_data);
							$size_name=$ex_size_data[0];
							$bh_qty=$ex_size_data[1];
							$pl_qty=$ex_size_data[2];
							$dy_qty=$ex_size_data[3];
							$test_qty=$ex_size_data[4];
							$self_qty=$ex_size_data[5];
							$totalqty=$ex_size_data[6];
						?>
							<tr id="row_<?=$i; ?>" > <!--disabled=""--disable stop by ISD-22-15787-->
								<td><input name="txtsizename[]" class="text_boxes" ID="txtsizename_<?=$i; ?>" value="<? echo $size_name; ?>" style="width:100px" autofocus/><input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $i; ?>" value="" style="width:30px" ></td>

								 <td><input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $bh_qty; ?>" /></td>

								<td><input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $pl_qty; ?>" /></td>

								<td><input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $dy_qty; ?>" /></td>

							   <td><input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_qty; ?>" /></td>

							   <td><input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $self_qty; ?>" /></td>

							   <td><input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_<? echo $i; ?>" style="width:70px" readonly value="<? echo $totalqty; ?>"  /></td>
								<td align="center">
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
						<?
						$i++;
						}
					}
					else
					{
						?>
						<tr id="row_1">
							<td width="110" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_1" value="" style="width:100px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_1" value="" style="width:30px"></td>

							 <td width="70" align="center" ><Input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

                           <td width="70" align="center" ><Input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>
                           <td width="70" align="center" ><Input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>
                           <td width="70" align="center" ><Input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_1" style="width:70px"  readonly /></td>
							<td align="center">
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
							</td>
						</tr>
					<?
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="" >
				<tr>
					<td width="110">&nbsp;</td>
					<td width="70" align="center"><Input name="txt_total_bh_qty" class="text_boxes_numeric" ID="txt_total_bh_qty" style="width:70px" value="<? echo $total_bhqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_pl_qty" class="text_boxes_numeric" ID="txt_total_pl_qty" style="width:70px" value="<? echo $total_plqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_dy_qty" class="text_boxes_numeric" ID="txt_total_dy_qty" style="width:70px" value="<? echo $total_dyqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_test_qty" class="text_boxes_numeric" ID="txt_total_test_qty" style="width:70px" value="<? echo $total_testqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_self_qty" class="text_boxes_numeric" ID="txt_total_self_qty" style="width:70px" value="<? echo $total_selfqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_all_qty" class="text_boxes_numeric" ID="txt_total_all_qty" style="width:70px" value="<? echo $total_all_qty; ?>" readonly /></td>
					 <td>&nbsp;</td>
				</tr>
                <tr>
                    <td colspan="8" align="center" class="">
                        <input type="hidden" name="hidden_size_data" id="hidden_size_data" class="text_boxes /">
                        <input type="hidden" name="hidden_total_self_and_all_data" id="hidden_total_self_and_all_data" class="text_boxes /">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script>calculate_total_qnty_by_type(); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="load_data_to_sizeinfo")
{
	$exdata=explode("__",$data);
	$qry_size="select id, mst_id, dtls_id, size_id, size_qty,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty from sample_development_size where dtls_id='$exdata[0]";
	$qry_result=sql_select($qry_size);
	if(count($qry_result)<1)
	{
		foreach ($qry_result as $row)
		{
			if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
			if($size_id=="") $size_id=$size_arr[$row[csf("size_id")]]; else $size_id.="*".$size_arr[$row[csf("size_id")]];
			if($size_qty=="") $size_qty=$row[csf("size_qty")]; else $size_qty.="*".$row[csf("size_qty")];
			if($bh_qty=="") $bh_qty=$row[csf("bh_qty")]; else $bh_qty.="*".$row[csf("bh_qty")];
			if($pl_qty=="") $pl_qty=$row[csf("plan_qty")]; else $pl_qty.="*".$row[csf("plan_qty")];
			if($dy_qty=="") $dy_qty=$row[csf("dyeing_qty")]; else $dy_qty.="*".$row[csf("dyeing_qty")];
			if($test_qty=="") $test_qty=$row[csf("test_qty")]; else $test_qty.="*".$row[csf("test_qty")];
			if($self_qty=="") $self_qty=$row[csf("self_qty")]; else $self_qty.="*".$row[csf("self_qty")];
			if($total_qty=="") $total_qty=$row[csf("total_qty")]; else $total_qty.="*".$row[csf("total_qty")];
		}
	}
	else
	{
		if($exdata[1]!="")
		{
			$qry_result="select a.id, a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_id'$exdata[1]' group by a.id, a.size_name order by a.size_name ASC";
			$qry_result=sql_select($qry_size);
			foreach ($qry_result as $row)
			{
				if($size_id=="") $size_id=$row[csf("size_name")]; else $size_id.="*".$row[csf("size_name")];
			}
		}
	}
	echo "document.getElementById('hidden_size_id').value 	 				= '".$size_id."';\n";
	echo "document.getElementById('hidden_bhqty').value 	 					= '".$bh_qty."';\n";
	echo "document.getElementById('hidden_plnqnty').value 	 					= '".$pl_qty."';\n";
	echo "document.getElementById('hidden_dyqnty').value 	 					= '".$dy_qty."';\n";
	echo "document.getElementById('hidden_testqnty').value 	 					= '".$test_qty."';\n";
	echo "document.getElementById('hidden_selfqnty').value 	 					= '".$self_qty."';\n";
	echo "document.getElementById('hidden_totalqnty').value 	 					= '".$total_qty."';\n";
	echo "document.getElementById('hidden_tbl_size_id').value 	 			= '".$id."';\n";
	exit();
}


if ($action=="load_drop_down_location")
{
	$sql="select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1 $location_credential_cond  order by location_name";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
	}

	exit();
}

if ($action=="load_drop_down_garment_item_for_after_order")
{
 	 $dt=explode(",",$data);
 	 if(count($dt)>1)
 	 {
		echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 1, "-- Select Item --", $selected, "",0,$data );
	 }
	else
	{

		 echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$data );
	}
}

if ($action=="load_drop_down_garment_item_for_not_after_order")
{

	echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_trims_group_from_budget_for_after_order")
{
 $sql="select a.item_name,a.id from lib_item_group a,wo_pre_cost_trim_cost_dtls b where a.item_category=4 and  a.is_deleted=0  and a.status_active=1 and b.trim_group=a.id group by a.item_name,a.id";
echo create_drop_down( "cboRaTrimsGroup_1", 100, $sql,"id,item_name", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_fabric_nature_for_after_order")
{
 	 $dt=explode(",",$data);
 	 if(count($dt)>1)
		echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "-- Select Fabric Nature --", $selected, "",0,$data );
	else
		 echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "-- Select Fabric Nature --", $selected, "",0,$data );
}

if ($action=="load_drop_down_fabric_nature_for_not_after_order")
{

	echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value);" );
}

if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and a.status_active=1 and a.is_deleted=0 and a.business_nature=3 group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Sample --", $selected, "" );
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_60', 'load_drop_down_brand', 'brand_td');load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_60', 'load_drop_down_season_buyer', 'season_td');" );
}

if ($action=="load_drop_down_buyer_style")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_70', 'load_drop_down_brand', 'brand_td');load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_70', 'load_drop_down_season_buyer', 'season_td');" );
}

if ($action=="load_drop_down_buyer_inq")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_70', 'load_drop_down_brand', 'brand_td');load_drop_down( 'sample_requisition_with_booking_controller', this.value+'_70', 'load_drop_down_season_buyer', 'season_td')" );
}

if ($action=="load_drop_down_season_buyer")
{
	//$datas=explode('_',$data);
	//echo create_drop_down( "cbo_season_name", 158, "select a.id,a.season_name from LIB_BUYER_SEASON a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	//echo "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'";
	list($buyer,$width)=explode('_',$data);
	$width=($width)?$width:150;

	
	$sql="select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$buyer'";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_season_name", $width, $sql,'id,season_name', 0, '--- Select Season ---', 1, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", $width, $sql,'id,season_name', 1, '--- Select Season ---', 0, ""  );
	}

}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 150, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b,lib_buyer_party_type c where b.buyer_id=c.buyer_id and  a.status_active =1 and a.is_deleted=0 and c.party_type in(20,21) and b.buyer_id=a.id and b.tag_company='$data' group by  a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="save_update_delete_mst")
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
		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;
		$app_id=return_next_id("id", "ready_to_approved_his", 1);

		if($db_type==0) $yearCond="YEAR(insert_date)"; else if($db_type==2) $yearCond="to_char(insert_date,'YYYY')";

		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix, requisition_number_prefix_num from sample_development_mst where entry_form_id=449 and company_id=$cbo_company_name and $yearCond=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));

		$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season_buyer_wise, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, is_copy, req_ready_to_approved, material_delivery_date, season_year, brand_id";
		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,449,0,".$cbo_ready_to_approved.",".$txt_material_dlvry_date.",".$cbo_season_year.",".$cbo_brand_id.")";
		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
		$app_field_array="id,mst_id,ready_to_approved,entry_form,updated_by,update_date";
		$app_data_array ="(".$app_id.",".$id_mst.",".$cbo_ready_to_approved.",449,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID1=sql_insert("ready_to_approved_his",$app_field_array,$app_data_array,0);


		//echo $rID; die;

		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$id_mst;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id_mst;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id_mst;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id_mst;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$ready_to_app_data=sql_select("SELECT req_ready_to_approved from sample_development_mst where id=$update_id and status_active=1 and is_deleted=0");
		$ready_to_app=0;
		foreach($ready_to_app_data as $row){
			$ready_to_app=$row[csf('req_ready_to_approved')];
		}

		$field_array="sample_stage_id*requisition_date*style_ref_no*buyer_name*season_buyer_wise*product_dept*dealing_marchant*agent_name*buyer_ref*bh_merchant*estimated_shipdate*remarks*updated_by*update_date*req_ready_to_approved*material_delivery_date*quotation_id*season_year*brand_id";
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_sample_stage."*".$txt_requisition_date."*".$txt_style_name."*".$cbo_buyer_name."*".$cbo_season_name."*".$cbo_product_department."*".$cbo_dealing_merchant."*".$cbo_agent."*".$txt_buyer_ref."*".$txt_bhmerchant."*".$txt_est_ship_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$txt_material_dlvry_date."*".$txt_quotation_id."*".$cbo_season_year."*".$cbo_brand_id."";

		$rID=sql_update("sample_development_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID1=1;
		if($ready_to_app!=str_replace("'","",$cbo_ready_to_approved)){
			$app_id=return_next_id("id", "ready_to_approved_his", 1);
			$app_field_array="id,mst_id,ready_to_approved,entry_form,updated_by,update_date";
			$app_data_array ="(".$app_id.",".$update_id.",".$cbo_ready_to_approved.",449,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID1=sql_insert("ready_to_approved_his",$app_field_array,$app_data_array,0);
		}


		if($db_type==0)
		{
			if($rID && $rID1 )
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				//echo "1**".str_replace("'","",$update_id);
				echo "1**".str_replace("'","",$txt_requisition_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_development_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID2=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID3=sql_delete("sample_development_size",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID4=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID5=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if($action=="style_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
	?>
	<script>
		$(document).ready(function(e) {
			$("#txt_search_common").focus();
		});
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			//alert(document.getElementById('selected_job').value);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
                <table  width="1200" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <th colspan="11"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </thead>
                    <thead>
                        <th width="140">Company Name</th>
                        <th width="100">Job NO</th>
                        <th width="100">Order NO</th>
                        <th width="130">Buyer Name</th>
                        <th width="70">Brand</th>
                        <th width="70">Season</th>
                        <th width="70">Season Year</th>
                        <th width="70">Style ID</th>
                        <th width="100" >Style Name</th>
                        <th width="200">Est. Ship Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                    </thead>
                    <tr class="general">
                        <td>
                            <input type="hidden" id="selected_job">
                            <? 
							echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_credential_cond  order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_style', 'buyer_td_st' );",$isDis ); ?>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" /></td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" /></td>
                        <td id="buyer_td_st"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                        <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "-- all --" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_name", 70, $blank_array,'', 1, "-- All --" ); ?></td>
                        <td><? echo create_drop_down( "cbo_season_year", 70, $year,'', 1, "-- All --" ); ?></td>
                        
                        
                        <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td>
                        <td> <input type="text" style="width:100px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>
                        <td>
                            <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('cbo_season_year').value , 'create_style_id_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
			</form>
            <div id="search_div"></div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="inquiry_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($company>0) $isDis=1; else $isDis=0;
	?>
	<script>
		function js_set_value(mrr)
		{
			var style_id =return_global_ajax_value( mrr+'_2', 'duplicate_style_check', '', 'sample_requisition_with_booking_controller') ;
			if(style_id !=''){
				alert("Requisition found against this style");
				return;
			}
			$("#txt_inquiry_id").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="710" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th colspan="11"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
			   </thead>
				<thead>
					<tr>
					   <th width="150">Company Name</th>
						<th width="150">Buyer Name</th>
                        <th width="70">Brand</th>
                        <th width="70">Season</th>
                        <th width="70">Season Year</th>
						<th width="100">Inquiry ID</th>
						<th width="80">Year</th>
						<th width="150">Style Ref.</th>
						<th width="100">Buyer Inquiry No</th>
						<th width="100">Inquiry Date </th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_credential_cond  order by company_name","id,company_name", 1, "-- Select Company --",$company, "load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_inq', 'buyer_td_inq' );",$isDis); ?></td>
                        <td id="buyer_td_inq"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                        <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "-- all --" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_name", 70, $blank_array,'', 1, "-- All --" ); ?></td>
                        <td><? echo create_drop_down( "cbo_season_year", 70, $year,'', 1, "-- All --" ); ?></td>                        
                        
						<td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
						<td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
						<td><input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
						<td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="Date" /></td>
						<td>
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value
+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('cbo_season_year').value, 'create_inquiry_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
							<input type="hidden" id="txt_inquiry_id" value="" />
						</td>
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
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
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

	if($ex_data[7]==4 || $ex_data[7]==0)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
		}

	if($ex_data[7]==2)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' ";
		}

	if($ex_data[7]==3)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' ";
		}


	
		if($ex_data[8]!=0){$request_no.=" and BRAND_ID =".$ex_data[8];}
		if($ex_data[9]!=0){$request_no.=" and SEASON_BUYER_WISE =".$ex_data[9];}
		if($ex_data[10]!=0){$request_no.=" and SEASON_YEAR =".$ex_data[10];}

		
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$brand_arr = return_library_array( "select id, brand_name from lib_buyer_brand where status_active =1 and is_deleted=0 order by brand_name ASC",'id','brand_name');
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");


	if($_SESSION['logic_erp']["single_user"]==1 && $_SESSION['logic_erp']["brand_id"]){
		$where_con.=" and BRAND_ID in(".$_SESSION['logic_erp']["brand_id"].")";
	}

	if($_SESSION['logic_erp']["single_user"]==1 && $_SESSION['logic_erp']["buyer_id"]){
		$where_con.=" and buyer_id in(".$_SESSION['logic_erp']["buyer_id"].")";
	}
	 

	$arr=array(0=>$company_arr,1=>$buyer_arr,7=>$season_buyer_wise_arr,8=>$brand_arr);


	 $sql = "select brand_id,season_year,system_number_prefix_num, system_number, buyer_request, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, status_active, extract(year from insert_date) as year, id, color from wo_quotation_inquery where is_deleted=0 $where_con $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date  order by id Desc ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Ref.,Inquery Date,Season,Brand,Season Year, Body/Wash Color","120,120,60,50,70,120,70,80,70,50,100","1000","260",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_id,0,0,0,0,0,season_buyer_wise,brand_id,0,0", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,inquery_date,season_buyer_wise,brand_id,season_year,color", "",'','0') ;

	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="populate_data_from_inquiry_search")
{
$sql = sql_select("select  id, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, department_name, remarks, dealing_marchant, gmts_item, est_ship_date, color, season_year, brand_id, STYLE_DESCRIPTION, color,team_leader from wo_quotation_inquery where id='$data' order by id");
	foreach($sql as $row)
	{
		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/sample_requisition_with_booking_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td'); load_drop_down( 'requires/sample_requisition_with_booking_controller','".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td'); load_drop_down( 'requires/sample_requisition_with_booking_controller','".$row[csf("buyer_id")]."', 'load_drop_down_sample_for_buyer', 'sample_td'); load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("gmts_item")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1')\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_team_leader_book').value = ".$row[csf("team_leader")].";\n";
		echo "fnc_marchd_chk(".$row[csf("team_leader")].")\n";
		//echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_name').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("STYLE_DESCRIPTION")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_bodywashcolor').value = '".$row[csf("color")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant_book').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
	}
	exit();
}

if($action=="create_style_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and a.company_name='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and a.id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and a.style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]' "; else $style_cond="";
		}


	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and a.'".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and a.'".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	if ($data[7]!="") $job=" and a.job_no_prefix_num like '%$data[7]'"; else $job="";
	if ($data[8]!="") $order=" and b.po_number like '%$data[8]'"; else $order="";

	

	if($data[9]!=0){$style_cond.=" and a.BRAND_ID =".$data[9];}
	if($data[10]!=0){$style_cond.=" and a.SEASON_BUYER_WISE =".$data[10];}
	if($data[11]!=0){$style_cond.=" and a.SEASON_YEAR =".$data[11];}
	
	//echo $style_cond;die;
	
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active =1 and is_deleted=0 order by brand_name ASC",'id','brand_name');
	$season_arr=return_library_array( "select id, SEASON_NAME from LIB_BUYER_SEASON where status_active =1 and is_deleted=0 order by SEASON_NAME ASC",'id','SEASON_NAME');
	



	if($_SESSION['logic_erp']["single_user"]==1 && $_SESSION['logic_erp']["brand_id"]){
		$where_con.=" and BRAND_ID in(".$_SESSION['logic_erp']["brand_id"].")";
	}

	if($_SESSION['logic_erp']["single_user"]==1 && $_SESSION['logic_erp']["buyer_id"]){
		$where_con.=" and buyer_name in(".$_SESSION['logic_erp']["buyer_id"].")";
	}

	
	$arr=array (3=>$buyer_arr,4=>$brand_arr,5=>$season_arr,8=>$product_dept,9=>$team_leader,10=>$dealing_marchant);
	$sql="";

	if($db_type==0)
	{
		$sql= "SELECT a.id,a.job_no_prefix_num,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year,a.company_name,a.buyer_name,a.style_ref_no,a.product_dept,a.team_leader,a.dealing_marchant,a.BRAND_ID,a.SEASON_BUYER_WISE,a.SEASON_YEAR,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) $where_con and b.is_deleted=0 $company $buyer $style_id_cond $style_cond $job $order  order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "SELECT a.id,a.job_no_prefix_num,to_char(a.insert_date,'YYYY') as year,a.company_name,a.buyer_name,a.style_ref_no,a.product_dept,a.team_leader,a.dealing_marchant,a.BRAND_ID,a.SEASON_BUYER_WISE,a.SEASON_YEAR,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) $where_con and b.is_deleted=0 $company $buyer $style_id_cond $style_cond $job $order order by a.id DESC";
	}
	 //echo $sql; 
	echo create_list_view("list_view", "Year,Job No,PO Number,Buyer Name,Brand,Season,Season Year,Style Name,Product Department,Team Leader,Dealing Merchant", "60,80,120,140,70,70,70,100,90,90,90","1170","240",0, $sql , "js_set_value", "id", "", 1, "0,0,0,buyer_name,brand_id,season_buyer_wise,0,0,product_dept,team_leader,dealing_marchant,0", $arr , "year,job_no_prefix_num,po_number,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,product_dept,team_leader,dealing_marchant", "",'','0,0,0,0,0,0,0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_search_popup")
{
	$res = sql_select("select * from wo_po_details_master where id=$data");

 	foreach($res as $result)
	{
		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("gmts_item_id")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("garments_nature")]."', 'load_drop_down_fabric_nature_for_after_order', 'rf_fabric_nature_1');\n";
		//load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("item_number_id")]."', 'load_drop_down_trims_group_for_after_order', 'ra_trims_group_1');

		echo "$('#txt_quotation_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_quotation_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_name')]."');\n";
		get_company_config($result[csf('company_name')]);
		echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		get_buyer_config($result[csf('company_name')].'*'.$result[csf('buyer_name')].'*1');
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_style_desc').val('".$result[csf('style_description')]."');\n";
		echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season_buyer_wise')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season_buyer_wise')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
		echo "$('#txt_bodywashcolor').val('".$color_arr[$result[csf('body_wash_color')]]."');\n";
  	}
 	exit();
}

if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{ 
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="1080" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th colspan="13"><?=create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            </thead>
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="60">Brand</th>
                <th width="60">Season</th>
                <th width="60">Season Year</th>
                <th width="70">Requisition No</th>
                <th width="70">Booking No</th>
                <th width="70">Style ID</th>
                <th width="80">Style Name</th>
                <th width="90">Sample Stage</th>
                <th width="130" colspan="2">Requisition date</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_job">
                    <?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond  order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );",$isDis ); ?> </td>
                <td id="buyer_td_req"><?=create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 60, $blank_array,'', 1, "-- all --" ); ?></td>
                <td id="season_td"><?=create_drop_down( "cbo_season_name", 60, $blank_array,'', 1, "-- All --" ); ?></td>
                <td><?=create_drop_down( "cbo_season_year", 60, $year,'', 1, "-- All --" ); ?></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"/></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_booking_num" id="txt_booking_num"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  /></td>
                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                <td><?=create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "1,2,3","" ); ?></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value+'_'+document.getElementById('txt_booking_num').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('cbo_season_year').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td align="center" colspan="13" valign="middle"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>load_drop_down( 'sample_requisition_with_booking_controller', document.getElementById('cbo_company_mst').value, 'load_drop_down_buyer_req', 'buyer_td_req' );</script>
</html>
<?
exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
	   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
	   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==2)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==3)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and sample_stage_id= '$data[8]' "; else  $stage_id="";
	if($data[8]!=1)
	{
		if ($data[9]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num='$data[9]') "; else  $book_cond="";
	}
	

	if($data[10]!=0){$style_cond.=" and BRAND_ID =".$data[10];}
	if($data[11]!=0){$style_cond.=" and SEASON_BUYER_WISE =".$data[11];}
	if($data[12]!=0){$style_cond.=" and SEASON_YEAR =".$data[12];}
	 //echo $style_cond;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$libColorArr=return_library_array( "select id,color_name from lib_color", "id","color_name");

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active =1 and is_deleted=0 order by brand_name ASC",'id','brand_name');
	$season_arr=return_library_array( "select id, SEASON_NAME from LIB_BUYER_SEASON where status_active =1 and is_deleted=0 order by SEASON_NAME ASC",'id','SEASON_NAME');
	$req_booking=sql_select( "select b.style_id, b.booking_no, a.body_color_id from wo_non_ord_samp_booking_dtls b, wo_non_ord_samp_booking_mst a where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1");
	foreach($req_booking as $brow)
	{
		$req_wise_booking[$brow[csf("style_id")]]=$brow[csf("booking_no")];
		$bwashColorArr[$brow[csf("style_id")]]=$libColorArr[$brow[csf("body_color_id")]];
	}
	unset($req_booking);
	
	//$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');

	if($data[8]==1){
		$req_wise_booking=return_library_array( "select style_id, booking_no from wo_booking_dtls where status_active=1 and entry_form_id=440 order by booking_no ASC",'style_id','booking_no');
	}else{
		$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1 and entry_form_id=440",'style_id','booking_no');
	}
	$where_con="";
	/*if($_SESSION['logic_erp']['single_user']==1){
		$where_con=" and INSERTED_BY=".$_SESSION['logic_erp']['user_id'];
		echo "<b style='color:red;'>Note: As per user credential you are only eligible to view the data those are enter by using your ID.</b>";
	}*/ //off by kausar pls contract with me
	
	if($data_level_secured==1)//Limit Access user // ===Issue Id =another-27809 and 21156 (2022 yr)======
	{
		$sqlTeam=sql_select("select a.id,a.team_id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and a.user_tag_id='$user_id' and a.status_active =1 and a.is_deleted=0");
		foreach($sqlTeam as $row){
			$TeamIdArr[$row[csf('team_id')]]=$row[csf('team_id')];
		}
		$TeamIdArrId=implode(",",$TeamIdArr);
		$sqlTeamMember=sql_select("select a.id,a.team_id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and b.id in(".$TeamIdArrId.") and a.status_active =1 and a.is_deleted=0");
			foreach($sqlTeamMember as $row){
			$mktTeamIdArr[$row[csf('id')]]=$row[csf('id')];
		}
		
		$mktTeamId=implode(",",$mktTeamIdArr);
		
		$mktTeamAccess="";
		if(count($mktTeamIdArr)>0) $mktTeamAccess=" and dealing_marchant in($mktTeamId)";//Dont hide Issue id ISD-20-31821
	}
	else //All Acces user 
	{
		$mktTeamAccess="";	
	}

	$arr=array (2=>$brand_arr,3=>$season_arr,5=>$buyer_arr,7=>$bwashColorArr,8=>$product_dept,9=>$dealing_marchant,10=>$sample_stage,11=>$req_wise_booking);
	 
	$sql="";
	if($db_type==0)
	{
		$sql= "SELECT BRAND_ID, SEASON_BUYER_WISE, SEASON_YEAR, id, requisition_number_prefix_num, SUBSTRING_INDEX(insert_date, '-', 1) as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, sample_stage_id, quotation_id from sample_development_mst where entry_form_id=449 and status_active=1 and is_deleted=0 $where_con $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num $stage_id $book_cond $mktTeamAccess order by id DESC";
	}
	else if($db_type==2)
	{
		$sql= "SELECT BRAND_ID,SEASON_BUYER_WISE,SEASON_YEAR,id, requisition_number_prefix_num, to_char(insert_date,'YYYY') as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, sample_stage_id, quotation_id,requisition_date   from sample_development_mst where entry_form_id=449 and  status_active=1 and is_deleted=0 $where_con $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num $stage_id $book_cond $mktTeamAccess order by id DESC";
	}

	//echo $sql;die;
		
	echo  create_list_view("list_view", "Year,Req. No,Brand,Season,Season Year,Buyer Name,Style Name,Body/ Wash Color,Product Department,Dealing Merchant,Sample Stage,Booking No,inquiry ID,Requisition Date", "60,60,70,70,60,120,100,90,90,90,100,100,60,100","1300","240",0, $sql , "js_set_value", "id", "", 1, "0,0,BRAND_ID,SEASON_BUYER_WISE,0,buyer_name,0,id,product_dept,dealing_marchant,sample_stage_id,id,0,0", $arr , "year,requisition_number_prefix_num,BRAND_ID,SEASON_BUYER_WISE,SEASON_YEAR,buyer_name,style_ref_no,id,product_dept,dealing_marchant,sample_stage_id,id,quotation_id,requisition_date", "",'','0,0,0,0,0,0,0,0,0,0,0,0,0,3') ;

	exit();
}

if($action == "yarn_dtls_popup") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample yarn dtls info","../../../", 1, 1, $unicode);
	/*$data = explode('**', $data);
	$yarnbreackdown = $data[0];
	$dtls_id = $data[1];
	$yarncountid = $data[2];
	$oldyarncountid = $data[3];
	$yarn_dtls = array();*/
	if($req_id != '')
	{
		$yarn_dtls = sql_select("SELECT id,samp_fab_dtls_id,determin_id, mst_id, count_id,copm_one_id, cons_ratio, type_id, cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and mst_id =".$req_id."");
	}

	?>
    <script>
	var permission='<? echo $permission;?>';
	function fnc_yarn_dtls( operation ){

		//alert(operation);
		var delete_cause="";
		if(operation==2){
			//release_freezing();
			alert('Not allowed');
				return;
		}

		var row_num=$('#tbl_yarn_cost tr').length;
		//release_freezing();

		var data_all="";
		for (var i=1; i<=row_num; i++){ //determinid_
			data_all=data_all+get_submitted_data_string('hiddenreqid*cbocount_'+i+'*yarndtlsid_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'+i+'*determinid_'+i+'*sampfabdtldid_'+i,"../../../",i);
		}
		var data="action=save_update_delete_yarn_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
//alert(data);
		//return;
		http.open("POST","sample_requisition_with_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_dtls_reponse;
	}

	function fnc_yarn_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				alert("Update is successfully");
				//reset_form('','form_data_con','','');
				//release_freezing();
				parent.emailwindow.hide();
				//show_msg(trim(reponse[0]));
			 }
			// release_freezing();
		}
	}

	function fnc_close()
	{
		parent.emailwindow.hide();
	}
	</script>
 <body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:780px;" id="yarn_dtls_1">
    <label><b>Yarn Details</b> </label>
     <input style="width:125px;" type="hidden" class="text_boxes"  name="hiddenreqid" id="hiddenreqid" value="<? echo trim($req_id);  ?>" />
		<table width="780" cellspacing="0" class="rpt_table" border="0" rules="all">
			<thead>
		    	<tr>
		        	<th width="60">Count</th>
		            <th width="100" class="must_entry_caption">Comp.</th>
		            <th width="50" class="must_entry_caption">%</th>
		            <th width="110">Type</th>
		            <th width="75" class="must_entry_caption">Cons Qnty</th>
		            </th>
		        </tr>
		    </thead>
		    <tbody id="tbl_yarn_cost" >
	<?
	$i=1;

		foreach ($yarn_dtls as $yarnData) {
		?>
		<tr id="yarncost_<? echo $i; ?>" align="center">
                <td>
               <? echo create_drop_down( "cbocount_".$i, 100, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $yarnData[csf('count_id')],""); ?>
                <input type="hidden" name="yarndtlsid_<? echo $i?>" id="yarndtlsid_<? echo $i?>" value="<? echo $yarnData[csf('id')]; ?>">
                 <input type="hidden" name="sampfabdtldid_<? echo $i?>" id="sampfabdtldid_<? echo $i?>" value="<? echo $yarnData[csf('samp_fab_dtls_id')]; ?>">
                 <input type="hidden" name="consratio_<? echo $i?>" id="consratio_<? echo $i?>" value="<? echo $yarnData[csf('cons_ratio')]; ?>">
                  <input type="hidden" name="determinid_<? echo $i?>" id="determinid_<? echo $i?>" value="<? echo $yarnData[csf('determin_id')]; ?>">
                </td>
                <td><? echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $yarnData[csf('copm_one_id')], "",1,"" ); ?></td>
               <td><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $yarnData[csf('cons_ratio')];  ?>" readonly/>
                </td>

                <td><? echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $yarnData[csf('type_id')], "",$disabled,"" ); ?></td>
                <td>
                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $yarnData[csf('cons_qnty')] ?>"   />
                </td>
            </tr>
		<?
		$i++;
		}

	?>
			</tbody>
             <tr>
              	<td align="center" colspan="5">&nbsp;</td>
            </tr>
            <tr>
              	<td align="center" colspan="5">
					<?
                    echo load_submit_buttons( $permission, "fnc_yarn_dtls",1,0,"reset_form('yarn_dtls_1','','')",1);
                    ?>
            	</td>
            </tr>
            <tr>
              	<td align="center" colspan="5">
					 <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            	</td>
            </tr>
	</table>
	</fieldset>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
    </body>

	<?
	exit();
}

if ($action=="save_update_delete_yarn_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1)  // Update Here
	{
			$con = connect();
 			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//hidden_req_id*cbocount_'+i+'*yarn_dtls_id_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'


	$field_yarn_dtls_up="samp_fab_dtls_id*determin_id*count_id*copm_one_id*percent_one*type_id*cons_qnty*updated_by*update_date";

			$m=0;$yarn_data_array_dtls="";
			for ($i=1;$i<=$total_row;$i++) //Yarn Start here
		    {
				$hidden_req_id="hiddenreqid";
				$samp_fab_dtls_id="sampfabdtldid_".$i;
				$determin_id="determinid_".$i;
				$yarn_dtls_id="yarndtlsid_".$i;
				$percent_one="percentone_".$i;
				$consqnty="consqnty_".$i;
				$count_id="cbocount_".$i;
				$copm_one_id="cbocompone_".$i;
				$determinid="determinid_".$i;
				$cbotype="cbotype_".$i;
				//if ($i!=1) $libyarncountdeterminationid .=",";
					//if ($m!=0) $yarn_data_array_dtls .=",";

				if (str_replace("'",'',$$yarn_dtls_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$yarn_dtls_id);

					$yarn_data_dtls_up[str_replace("'",'',$$yarn_dtls_id)] =explode("*",("".$$samp_fab_dtls_id."*".$$determinid."*".$$count_id."*".$$copm_one_id."*".$$percent_one."*".$$cbotype."*".$$consqnty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

					//$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$hidden_req_id.",".$id_dtls.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				}
				 //foreach end
			}//For End
			//print_r($yarn_data_dtls_up);
		//echo "10**";die;



 			$flag=1;
 			if(count($yarn_data_dtls_up))
			{
				$rIDup=execute_query(bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr ));
				//echo "10**".bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr );die;
				if($rIDup) $flag=1; else $flag=0;
			}

			//echo "10**".$rIDs.'='.$rID1.'='.$rID_size_dlt.'='.$flag;die;



			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**";die;
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if($action=="populate_data_from_requisition_search_popup")
{
	// echo $data;die;
	$res = sql_select("select id, company_id, location_id, buyer_name, style_ref_no, product_dept, agent_name, dealing_marchant, bh_merchant, buyer_ref, estimated_shipdate, remarks, requisition_number, sample_stage_id, requisition_date, material_delivery_date, quotation_id, season_buyer_wise, season_year, brand_id, is_approved, req_ready_to_approved from sample_development_mst where id=$data and entry_form_id=449 and is_deleted=0 and status_active=1");
	//$sql = "select id, company_id, location_id, buyer_name, style_ref_no, product_dept, agent_name, dealing_marchant, bh_merchant, buyer_ref, estimated_shipdate, remarks, requisition_number, sample_stage_id, requisition_date, material_delivery_date, quotation_id, season_buyer_wise, season_year, brand_id, is_approved, /req_ready_to_approved from sample_development_mst where id=$data and entry_form_id=449 and is_deleted=0 and status_active=1";
	//echo $sql;die; cbo_supplier_name

	$sample_st=$res[0][csf("sample_stage_id")];
	$quotation_info=$res[0][csf("quotation_id")];
	if($sample_st==1)
	{
		$job_arr=array();
		$job_sql="select id, company_name, buyer_name, style_ref_no, product_dept, location_name, agent_name, dealing_marchant, bh_merchant, season_matrix, season_buyer_wise,gmts_item_id,garments_nature from wo_po_details_master where is_deleted=0 and status_active=1";
		$job_sql_res=sql_select($job_sql);
		foreach($job_sql_res as $jrow)
		{
			$season_id=0;
			if($jrow[csf("season_matrix")]!=0) $season_id=$jrow[csf("season_matrix")];
			else $season_id=$jrow[csf("season_buyer_wise")];

			$job_arr[$jrow[csf("id")]]['company']=$jrow[csf("company_name")];

			$job_arr[$jrow[csf("id")]]['buyer']=$jrow[csf("buyer_name")];
			$job_arr[$jrow[csf("id")]]['style']=$jrow[csf("style_ref_no")];
			$job_arr[$jrow[csf("id")]]['dept']=$jrow[csf("product_dept")];
			$job_arr[$jrow[csf("id")]]['loaction']=$jrow[csf("location_name")];
			$job_arr[$jrow[csf("id")]]['agent']=$jrow[csf("agent_name")];
			$job_arr[$jrow[csf("id")]]['dmarchant']=$jrow[csf("dealing_marchant")];
			$job_arr[$jrow[csf("id")]]['bh']=$jrow[csf("bh_merchant")];
			$job_arr[$jrow[csf("id")]]['gmts']=$jrow[csf("gmts_item_id")];
			$job_arr[$jrow[csf("id")]]['gmtsnature']=$jrow[csf("garments_nature")];
			$job_arr[$jrow[csf("id")]]['season']=$season_id;
		}
	 	unset($job_sql_res);
	}

	if($sample_st==2 && $quotation_info)
	{
		$inq_arr=array();
		$inq_sql="select id, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, department_name, remarks, dealing_marchant, gmts_item, est_ship_date, color, season from wo_quotation_inquery where is_deleted=0 and status_active=1";
		$inq_sql_res=sql_select($inq_sql);
		foreach($inq_sql_res as $Inqrow)
		{
			$inq_arr[$Inqrow[csf("id")]]['company']=$Inqrow[csf("company_id")];
			$inq_arr[$Inqrow[csf("id")]]['buyer']=$Inqrow[csf("buyer_id")];
			$inq_arr[$Inqrow[csf("id")]]['style']=$Inqrow[csf("style_refernce")];
			//$inq_arr[$Inqrow[csf("id")]]['dept']=$Inqrow[csf("department_name")];
			$inq_arr[$Inqrow[csf("id")]]['dmarchant']=$Inqrow[csf("dealing_marchant")];
			$inq_arr[$Inqrow[csf("id")]]['gmts']=$Inqrow[csf("gmts_item")];
			$inq_arr[$Inqrow[csf("id")]]['season']=$Inqrow[csf("season")];
			$inq_arr[$Inqrow[csf("id")]]['est']=$Inqrow[csf("est_ship_date")];
			$inq_arr[$Inqrow[csf("id")]]['remarks']=$Inqrow[csf("remarks")];
		}
		unset($inq_sql_res);
	}
	if($sample_st!=1)
	{
		/* $is_booking = sql_select("SELECT booking_no from wo_non_ord_samp_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=140 group by booking_no"); */
		$is_booking = sql_select("SELECT a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved,a.supplier_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.booking_no=b.booking_no and  b.style_id=$data and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form_id=440 group by a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved,a.supplier_id");
	}
	else if($sample_st==1)
	{
		/* $is_booking = sql_select("SELECT booking_no from wo_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=440 group by booking_no"); */
		$is_booking = sql_select("SELECT a.booking_no, a.is_approved, a.fabric_source, a.currency_id, a.pay_mode, a.booking_date, a.team_leader, a.dealing_marchant, a.remarks, a.ready_to_approved,a.supplier_id,b.booking_mst_id from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where  b.style_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=440 and b.booking_type=4 and b.is_short=2 group by a.is_approved, a.booking_no, a.fabric_source, a.currency_id, a.pay_mode, a.booking_date, a.team_leader, a.dealing_marchant, a.remarks, a.ready_to_approved,a.supplier_id,b.booking_mst_id order by b.booking_mst_id ASC");
	}
	// print_r($is_booking);die;

	 //clearstatcache(); txt_booking_no

 	foreach($res as $result)
	{
		//echo "load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td'); load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."', 'load_drop_down_brand', 'brand_td'); load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');\n";
		
		get_company_config($result[csf('company_id')]);
		
		get_buyer_config($result[csf('company_id')].'*'.$result[csf('buyer_name')].'*1');

 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#txt_requisition_date').val('".change_date_format($result[csf('requisition_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_material_dlvry_date').val('".change_date_format($result[csf('material_delivery_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$result[csf('req_ready_to_approved')]."');\n";

		if($result[csf('sample_stage_id')]==1)
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$job_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$job_arr[$result[csf("quotation_id")]]['loaction']."');\n";
			echo "$('#cbo_buyer_name').val('".$job_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			//echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
			echo "$('#txt_style_name').val('".$job_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$job_arr[$result[csf("quotation_id")]]['dept']."');\n";
			echo "$('#cbo_agent').val('".$job_arr[$result[csf("quotation_id")]]['agent']."');\n";
			echo "$('#cbo_dealing_merchant').val('".$job_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#txt_bhmerchant').val('".$job_arr[$result[csf("quotation_id")]]['bh']."');\n";
			echo "$('#cbo_season_name').val('".$job_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
			echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$job_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";
		}
		else if($result[csf('sample_stage_id')]==2 && ($result[csf('quotation_id')]))
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$inq_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			echo "$('#txt_style_name').val('".$inq_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_dealing_merchant').val('".$inq_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "$('#txt_est_ship_date').val('".$inq_arr[$result[csf("quotation_id")]]['est']."');\n";
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
			echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$inq_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";
			echo "$('#txt_style_name').removeAttr('readonly','');\n";
		}
 		else
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
			echo "$('#txt_style_name').removeAttr('readonly','');\n";

		}
		echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_buyer_ref').val('".$result[csf('buyer_ref')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season_buyer_wise')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1);\n";
		echo "$('#approvedMsg').html('');\n";
 		//echo "$('#sample_dtls').removeProp('disabled')".";\n";
		echo "$('#required_fab_dtls').removeProp('disabled')".";\n";
		echo "$('#required_accessories_dtls').removeProp('disabled')".";\n";
		echo "$('#required_embellishment_dtls').removeProp('disabled')".";\n";
		echo "$('#sample_dtls').removeProp('disabled')".";\n";
		/* if(count($is_booking)>0)
		{
			echo "$('#approvedMsg').html('Booking found aganist this Requisition!!');\n";
			echo "$('#txt_booking_no').val('".$is_booking[0][csf('booking_no')]."');\n";
		} */

		if(count($is_booking)>0)
		{
			$is_approved=$is_booking[0][csf('is_approved')];
			echo "$('#approvedMsg').html('Booking found aganist this Requisition!!');\n";			
			if($sample_st==2 || $sample_st==3){
				echo "$('#txt_booking_no').val('".$is_booking[0][csf('booking_no')]."');\n";
				$remarks=return_field_value("remarks", "wo_non_ord_samp_booking_mst", "booking_no='".$is_booking[0][csf('booking_no')]."' and is_deleted=0  and status_active=1");
				echo "$('#txt_booking_remarks').val('".$remarks."');\n";
				echo "$('#cbo_supplier_name').val('".$is_booking[0][csf('supplier_id')]."');\n";
				echo "$('#cbo_currency').val('".$is_booking[0][csf('currency_id')]."');\n";
				echo "$('#cbo_fabric_source').val('".$is_booking[0][csf('fabric_source')]."');\n";
				echo "$('#cbo_pay_mode').val('".$is_booking[0][csf('pay_mode')]."');\n";
				echo "$('#cbo_team_leader_book').val('".$is_booking[0][csf('team_leader')]."');\n";
				echo "$('#cbo_dealing_merchant_book').val('".$is_booking[0][csf('dealing_marchant')]."');\n";
				echo "$('#cbo_ready_to_approved_book').val('".$is_booking[0][csf('ready_to_approved')]."');\n";
			//	echo "$('#txt_booking_remarks').val('".$is_booking[0][csf('remarks')]."');\n";supplier_id
				
				if($is_approved==1 || $is_approved==3)
				{
					echo "$('#booking_approvedMsg').html('This Booking is Approved');\n";		
					echo "$('#txt_style_desc_book').attr('disabled','true')".";\n";
					echo "$('#cbo_currency').attr('disabled','true')".";\n";
					echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
					echo "$('#cbo_sources').attr('disabled','true')".";\n";
					echo "$('#cbo_pay_mode').attr('disabled','true')".";\n";
					echo "$('#cbo_supplier_name').attr('disabled','true')".";\n";
					echo "$('#cbo_dealing_merchant_book').attr('disabled','true')".";\n";
					echo "$('#txt_buyer_req_no').attr('disabled','true')".";\n";
					echo "$('#cbo_ready_to_approved_book').attr('disabled','true')".";\n";
					echo "$('#txt_revise_no').attr('disabled','true')".";\n";
					echo "$('#txt_attention').attr('disabled','true')".";\n";
					echo "$('#txt_booking_remarks').attr('disabled','true')".";\n";
				}
			}
			if($sample_st==1){ //booking_approvedMsg
				foreach($is_booking as $row){
					echo "$('#txt_booking_no').val('".$row[csf('booking_no')]."');\n";
					echo "$('#cbo_currency').val('".$row[csf('currency_id')]."');\n";
					echo "$('#cbo_fabric_source').val('".$row[csf('fabric_source')]."');\n";
					echo "$('#cbo_pay_mode').val('".$row[csf('pay_mode')]."');\n";
					echo "$('#cbo_team_leader_book').val('".$row[csf('team_leader')]."');\n";
					echo "$('#cbo_dealing_merchant_book').val('".$row[csf('dealing_marchant')]."');\n";
					echo "$('#cbo_ready_to_approved_book').val('".$row[csf('ready_to_approved')]."');\n";
					echo "$('#txt_booking_remarks').val('".$row[csf('remarks')]."');\n";
					$is_approved=$row[csf('is_approved')];
					if($is_approved==1 || $is_approved==3)
					{
						echo "$('#booking_approvedMsg').html('Booking Approved found aganist this Requisition!!');\n";		
						echo "$('#txt_style_desc_book').attr('disabled','true')".";\n";
						echo "$('#cbo_currency').attr('disabled','true')".";\n";
						echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
						echo "$('#cbo_sources').attr('disabled','true')".";\n";
						echo "$('#cbo_pay_mode').attr('disabled','true')".";\n";
						echo "$('#cbo_supplier_name').attr('disabled','true')".";\n";
						echo "$('#cbo_dealing_merchant_book').attr('disabled','true')".";\n";
						echo "$('#txt_buyer_req_no').attr('disabled','true')".";\n";
						echo "$('#cbo_ready_to_approved_book').attr('disabled','true')".";\n";
						echo "$('#txt_revise_no').attr('disabled','true')".";\n";
						echo "$('#txt_attention').attr('disabled','true')".";\n";
						echo "$('#txt_booking_remarks').attr('disabled','true')".";\n";
					}
				}
			}
		}
 		if($result[csf('is_approved')]==1  )
		{	
			if($result[csf('is_approved')]==1)
			{
				 echo "$('#approvedMsg').html('This Requisition is Approved by Authority..!!');\n";
			}

  			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1,1);\n";
 			echo "$('#save1').removeClass('formbutton').addClass('formbutton_disabled');\n";
 			echo "$('#save1').removeAttr('onclick','fnc_sample_requisition_mst_info(0)');\n";
			echo "$('#cbo_sample_stage').attr('disabled','true')".";\n";
			echo "$('#txt_requisition_date').attr('disabled','true')".";\n";
			echo "$('#txt_style_name').attr('disabled','true')".";\n";
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_location_name').attr('disabled','true')".";\n";
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
			echo "$('#cbo_season_name').attr('disabled','true')".";\n";
			echo "$('#cbo_product_department').attr('disabled','true')".";\n";
			echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
			echo "$('#cbo_agent').attr('disabled','true')".";\n";
			echo "$('#txt_buyer_ref').attr('disabled','true')".";\n";
			echo "$('#txt_bhmerchant').attr('disabled','true')".";\n";
			echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
			echo "$('#txt_remarks').attr('disabled','true')".";\n";
			echo "$('#cbo_ready_to_approved').attr('disabled','true')".";\n";
 			echo "$('#required_fab_dtls').prop('disabled','true')".";\n";
			echo "$('#sample_dtls').prop('disabled','true')".";\n";
			echo "$('#required_accessories_dtls').prop('disabled','true')".";\n";
			echo "$('#required_embellishment_dtls').prop('disabled','true')".";\n";
  		}

		if($result[csf('is_approved')]!=1)
		{
 			echo "$('#cbo_sample_stage').removeAttr('disabled','')".";\n";
			echo "$('#txt_requisition_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_style_name').removeAttr('disabled','')".";\n";
 			echo "$('#cbo_season_name').removeAttr('disabled','')".";\n";
 			echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
 			echo "$('#txt_buyer_ref').removeAttr('disabled','')".";\n";
			echo "$('#txt_bhmerchant').removeAttr('disabled','')".";\n";
			echo "$('#txt_est_ship_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_remarks').removeAttr('disabled','')".";\n";
			echo "$('#cbo_ready_to_approved').removeAttr('disabled','')".";\n";
		}
  	}
 	exit();
}

if($action=="all_remarks_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Remarks Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function fnc_close( )
		{
			 var remarks_text_area=document.getElementById('remarks_text_area').value;
 			document.getElementById('txt_remarks').value=remarks_text_area;
			parent.emailwindow.hide();
		}
    </script>
    <div>
	    <form>
	    <table>
	    	<tr>
	    		<td><strong>Remarks</strong></td>
	    		<td><textarea id="remarks_text_area" style="border:1px solid grey;border-radius: 3px;"  rows="8" cols="50"><? echo $remarks;?></textarea></td>
	    	</tr>
	    	<tr>
	    		<td></td>
	    		<td align="center">
		    		<input type="hidden" id="txt_remarks" value="">
		    	 	<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
	    	 	</td>
	    	</tr>
	    </table>

	    </form>
	</div>

    <?

	exit();
}

if($action=="color_popup_bk")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function js_set_value( mst_id )
		{
			document.getElementById('txt_color_name').value=mst_id;
			//document.getElementById('txt_color_id').value=color_id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="txt_color_name">
    <input type="hidden" id="txt_color_id">
    <?
	$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	$job_arr=return_library_array( "select id,job_no from wo_po_details_master", "id","job_no" );
	$arr=array(1=>$lib_color_arr);
	if($style_db_id!='')
	{
		 $sql= "select b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='".$job_arr[$style_db_id]."' group by b.color_name";

		echo  create_list_view("list_view", "Color Name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0,0", $arr , "color_name","requires/sample_requisition_with_booking_controller", 'setFilterGrid("list_view",-1);' );
	}
	else
	{
		$sql= "select  color_name from lib_color where status_active=1 and is_deleted=0";

		echo  create_list_view("list_view", "color_name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name","requires/sample_requisition_with_booking_controller", 'setFilterGrid("list_view",-1);' );
	}
	exit();
}
if($action=="color_popup")
{
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		var selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'color_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
					document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
					if( jQuery.inArray( $('#txtcolordata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txtcolordata_' + i).val());
					}
				}
				var colordata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    colordata += selected_name[i] + '__';
                }
                colordata = colordata.substr( 0, colordata.length - 2 );
                $('#color_data').val( colordata );
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txtcolordata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
				var colordata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    colordata += selected_name[i] + '__';
                }
                colordata = colordata.substr( 0, colordata.length - 2 );
                $('#color_data').val( colordata );

			}

		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		function js_set_value( str ) {
			var tbl_row_count = document.getElementById( 'color_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if($("#search"+str).css("display") !='none'){
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txtcolordata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txtcolordata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txtcolordata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var colordata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				colordata += selected_name[i] + '__';
			}
			if(selected_name.length == tbl_row_count){
                document.getElementById("check_all").checked = true;
            }
            else{
                document.getElementById("check_all").checked = false;
            }
			colordata = colordata.substr( 0, colordata.length - 2 );

			$('#color_data').val( colordata );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="color_data" name="color_data"/>
        <? 
		$sql_tgroup=sql_select( "select id, item_name, order_uom,trim_uom,trim_type from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name"); 
		$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
		$sql_tag_buyer=sql_select("select a.color_name, a.id FROM lib_color a, lib_color_tag_buyer c where a.id=c.color_id and c.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0");
		foreach($sql_tag_buyer as $row)
		{
			$ColorIdArr[$row[csf('id')]]=$row[csf('id')];
		}
		$colorIdCond=implode(",",$ColorIdArr);
		$idCond=where_con_using_array($ColorIdArr,0,"b.id"); 
		if($style_db_id!='' && $sampleStage==1)
		{
			$color_sql= sql_select("SELECT b.id as color_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=$style_db_id $idCond group by b.color_name, b.id");
			//echo "SELECT b.id as color_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=$style_db_id $idCond group by b.color_name, b.id";
		}
		else
		{
			$idCond=where_con_using_array($ColorIdArr,0,"id"); 
			$color_sql= sql_select("SELECT id as color_id, color_name from lib_color where status_active=1 and is_deleted=0 $idCond");
		}
		?>
        <table width="250" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
            	<th>Color Name</th>
            </thead>
        </table>
        <div style="width:250px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
        <table width="230" cellspacing="0" class="rpt_table" border="0" rules="all" id="color_table">
            <tbody>
				<?
                $i=1;
                foreach($color_sql as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('color_id')].'***'.$row[csf('color_name')];
					?>
					<tr style="cursor: pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $row[csf('color_name')]; ?>
                        	<input type="hidden" name="txtcolordata_<? echo $i; ?>" id="txtcolordata_<? echo $i; ?>" value="<? echo $str; ?>"/>
                        </td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
         </div>
        <table width="250" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('color_table',-1);</script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete_sample_details")
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
 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
 			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,measurement_chart,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";

			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboSampleName="cboSampleName_".$i;
				$cboGarmentItem="cboGarmentItem_".$i;
				$txtSmv="txtSmv_".$i;
				$txtArticle="txtArticle_".$i;
				$txtColor="txtColor_".$i;
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtSubmissionQty="txtSubmissionQty_".$i;
				$txtDelvStartDate="txtDelvStartDate_".$i;
				$txtDelvEndDate="txtDelvEndDate_".$i;
				$txtBuyerSubDate="txtBuyerSubDate_".$i;
				$txtRemarks="txtRemarks_".$i;
				$txtChargeUnit="txtChargeUnit_".$i;
				$txtMchart="txtMchart_".$i;
				$cboCurrency="cboCurrency_".$i;
				$txtAllData="txtAllData_".$i;
				//$updateIdDtls="updateidsampledtl_".$i;

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","440");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;


				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtBuyerSubDate.",".$$txtRemarks.",".$$txtChargeUnit.",".$$txtMchart.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,449,".$$txtAllData.",0,0,0,0,0,0)";


				$countsize=0; $ex_data="";

				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
				/*for($i=1;$i<=$countsize; $i++)
				{*/
				foreach($ex_data as $size_data)
				{
					$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;
					$ex_size_data=explode("_",$size_data);
					$size_name=$ex_size_data[0];
					$bhqty=$ex_size_data[1];
					$plqty=$ex_size_data[2];
					$dyqty=$ex_size_data[3];
					$testqty=$ex_size_data[4];
					$selfqty=$ex_size_data[5];
					$totalqty=$ex_size_data[6];

					if($size_name!="")
					{
						if (!in_array($size_name,$new_array_size))
						{
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","440");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_size[$size_id]=str_replace("'","",$size_name);
						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else $size_id=0;


					if($i==1) $add_comma=""; else $add_comma=",";
				//	$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}
				$id_dtls=$id_dtls+1;
				//echo "insert into sample_development_size (".$field_array_size.") Values ".$data_array_size."";die;

		    }

 			//echo "5**"."INSERT INTO sample_development_size(".$field_array_size.")VALUES ".$data_array_size; die;
			$rID_1=sql_insert("sample_development_dtls",$field_array,$data_array,1);
			$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);

			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$update_id)."**1";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID_1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$update_id)."**1";

				}
			else{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;

	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1);

			$field_array_up="sample_name*gmts_item_id*smv*article_no*sample_color*sample_prod_qty*submission_qty*delv_start_date*delv_end_date*sent_to_buyer_date*comments*sample_charge*measurement_chart*sample_curency*updated_by*update_date*size_data";

			$field_array= "id, sample_mst_id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date,sent_to_buyer_date,comments,sample_charge,measurement_chart, sample_curency, inserted_by, insert_date, status_active, is_deleted, entry_form_id, size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

			$add_comma=0; $data_array=""; //echo "10**";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboSampleName="cboSampleName_".$i;
				$cboGarmentItem="cboGarmentItem_".$i;
				$txtSmv="txtSmv_".$i;
				$txtArticle="txtArticle_".$i;
				$txtColor="txtColor_".$i;
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtSubmissionQty="txtSubmissionQty_".$i;
				$txtDelvStartDate="txtDelvStartDate_".$i;
				$txtDelvEndDate="txtDelvEndDate_".$i;
				$txtChargeUnit="txtChargeUnit_".$i;
				$txtMchart="txtMchart_".$i;
				$cboCurrency="cboCurrency_".$i;
				$updateIdDtls="updateidsampledtl_".$i;
				$txtAllData="txtAllData_".$i;
				$txtBuyerSubDate="txtBuyerSubDate_".$i;
				$txtRemarks="txtRemarks_".$i;
				$hiddenColorid="hiddenColorid_".$i;

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","440");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;
				
				//echo str_replace("'",'',$$updateIdDtls);
				$prev_ids="SELECT id,fab_status_id,gmts_item_id,sample_name,sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$update_id";
				$dtlsUpdate_id_array=array();$color_delete_arr=array();
				foreach(sql_select($prev_ids) as $key_id=>$key_val)
				{
					$dtlsUpdate_id_array[]=$key_val[csf('id')];
					$color_delete_arr[$key_val[csf('id')]]['fab_id']=$key_val[csf('fab_status_id')];
					$color_delete_arr[$key_val[csf('id')]]['sample_color']=$key_val[csf('sample_color')];
				
				}

				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboSampleName."*".$$cboGarmentItem."*".$$txtSmv."*".$$txtArticle."*'".$color_id."'*".$$txtSampleProdQty."*".$$txtSubmissionQty."*".$$txtDelvStartDate."*".$$txtDelvEndDate."*".$$txtBuyerSubDate."*".$$txtRemarks."*".$$txtChargeUnit."*".$$txtMchart."*".$$cboCurrency."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$txtAllData.""));

					$countsize=0; $ex_data="";
					$ex_data=explode("__",str_replace("'","",$$txtAllData));
					$countsize=count($ex_data);

					$data_array_size.='';
					foreach($ex_data as $size_data)
					{
						$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;
						$ex_size_data=explode("_",$size_data);
						$size_name=$ex_size_data[0];
						$bhqty=$ex_size_data[1];
						$plqty=$ex_size_data[2];
						$dyqty=$ex_size_data[3];
						$testqty=$ex_size_data[4];
						$selfqty=$ex_size_data[5];
						$totalqty=$ex_size_data[6];

						if($size_name!="")
						{
							if (!in_array($size_name,$new_array_size))
							{
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","440");
								//echo $$txtColorName.'='.$color_id.'<br>';
								$new_array_size[$size_id]=str_replace("'","",$size_name);

							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else $size_id=0;

						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$$updateIdDtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}

					$fab_id_color=$color_delete_arr[str_replace("'",'',$$updateIdDtls)]['fab_id'];
					$hiddenColorid=str_replace("'","",$$hiddenColorid);
					$sample_color_id=$color_delete_arr[str_replace("'",'',$$updateIdDtls)]['sample_color'];
					if($color_id!=$sample_color_id)
					{
						$update_color_delete=execute_query("UPDATE sample_development_rf_color set status_active=0,is_deleted=1 where mst_id=$update_id and dtls_id=".$fab_id_color." and color_id=".$hiddenColorid."",1);
						if($update_color_delete) $flag=1; else $flag=0;
					}
				}
			 	else
				{
						if ($add_comma!=0) $data_array .=",";
						//id, sample_mst_id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date,sent_to_buyer_date,comments,sample_charge,measurement_chart, sample_curency, inserted_by, insert_date, status_active, is_deleted, entry_form_id, size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id
						$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtBuyerSubDate.",".$$txtRemarks.",".$$txtChargeUnit.",".$$txtMchart.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,449,".$$txtAllData.",0,0,0,0,0,0)";

					$countsize=0; $ex_data="";
					$ex_data=explode("__",str_replace("'","",$$txtAllData));
					$countsize=count($ex_data);

					$data_array_size.='';
					/*for($i=1;$i<=$countsize; $i++)
					{*/
					foreach($ex_data as $size_data)
					{
						$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;
						$ex_size_data=explode("_",$size_data);
						$size_name=$ex_size_data[0];
						$bhqty=$ex_size_data[1];
						$plqty=$ex_size_data[2];
						$dyqty=$ex_size_data[3];
						$testqty=$ex_size_data[4];
						$selfqty=$ex_size_data[5];
						$totalqty=$ex_size_data[6];

						if($size_name!="")
						{
							if (!in_array($size_name,$new_array_size))
							{
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","440");
								//echo $$txtColorName.'='.$color_id.'<br>';
								$new_array_size[$size_id]=str_replace("'","",$size_name);
							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else $size_id=0;


						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}
						$id_dtls=$id_dtls+1;
						$add_comma++;
				}




		    } //For Loop End
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=array_diff($dtlsUpdate_id_array,$id_arr);
			}
			else
			{
				$distance_delete_id=$dtlsUpdate_id_array;
			}



			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			if(implode(',',$distance_delete_id)!="")
			{

				foreach($distance_delete_id as $id_val)
				{
					$delDtls=sql_update("sample_development_dtls",$field_array_del,$data_array_del,"id","".$id_val."",1);
					if($delDtls) $flag=1; else $flag=0;

					$fab_id=$color_delete_arr[$id_val]['fab_id'];
					$sample_color=$color_delete_arr[$id_val]['sample_color'];

					if($flag==1)
					{
					$update_color_delete=execute_query("UPDATE sample_development_rf_color set status_active=0,is_deleted=1 where mst_id=$update_id and dtls_id=".$fab_id." and color_id=".$hiddenColorid."",1);
					if($update_color_delete) $flag=1; else $flag=0;
					}
				}
			}

			//echo "10**XX";die;


			$flag=1;			
			if($data_array!="")
			{
				//echo "10**Insert Into sample_development_dtls ($field_array) values $data_array"; die;
				//echo "10**insert into sample_development_dtls (".$field_array.") Values ".$data_array;die;
				$rID_dtls=sql_insert("sample_development_dtls",$field_array,$data_array,0);
				$rID_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				if($rID_dtls && $rID_size) $flag=1; else $flag=0;
			}
			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "delete from sample_development_size where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				$rID1=execute_query(bulk_update_sql_statement("sample_development_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}


			if($txtDeltedIdSd!="" || $txtDeltedIdSd!=0)
			{

				//$fields="is_deleted";
				//$delDtls=sql_multirow_update("sample_development_dtls",$fields,"1","id",$txtDeltedIdSd,0);
 			 }


			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$update_id)."**1";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$update_id)."**1";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		$next_process=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id=$update_id and status_active=1 and is_deleted=0");
		if(count($next_process)>0)
		{
			echo "321**";
			die;
		}

		if( $is_approved==1)
		{
			echo "323**";
			die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id*entry_form_id","".$update_id."*449",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_size set status_active=0,is_deleted=1 where mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}


if ($action=="save_update_delete_required_fabric")
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

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$field_array= "id, sample_mst_id, sample_name, gmts_item_id, process_loss_percent, grey_fab_qnty, delivery_date, fabric_source, remarks_ra, fin_fab_qnty, body_part_id,body_part_type_id, fabric_nature_id, fabric_description, gsm, dia, color_data, color_type_id, width_dia_id, uom_id, required_qty,rate,amount, inserted_by, insert_date, status_active, is_deleted, form_type, determination_id, weight_type, cuttable_width";


		$field_array_col="id, mst_id, dtls_id, color_id, contrast, fabric_color, qnty, process_loss_percent, grey_fab_qnty, inserted_by, insert_date, status_active, is_deleted";
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
		$yarn_deter_id="";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboRfSampleName="cboRfSampleName_".$i;
			$cboRfGarmentItem="cboRfGarmentItem_".$i;//cboRfBodyPartType_1
			$cboRfBodyPart="cboRfBodyPart_".$i;
			$cboRfBodyPartType="cboRfBodyPartType_".$i;
			$cboRfFabricNature="cboRfFabricNature_".$i;
			$txtRfFabricDescription="txtRfFabricDescription_".$i;
			$txtRfGsm="txtRfGsm_".$i;
			$cboweighttype="cboweighttype_".$i;
			$txtRfDia="txtRfDia_".$i;
			$txtcuttablewidth="txtcuttablewidth_".$i;
			$txtRfColor="txtRfColor_".$i;
			$cboRfColorType="cboRfColorType_".$i;
			$cboRfWidthDia="cboRfWidthDia_".$i;
			$cboRfUom="cboRfUom_".$i;
			$txtRfReqQty="txtRfReqQty_".$i;
			$txtRfColorAllData="txtRfColorAllData_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;

			$txtProcessLoss="txtProcessLoss_".$i;
			$txtGrayFabric="txtGrayFabric_".$i;
			$txtRate="txtRate_".$i;
			$txtAmount="txtAmount_".$i;
			$fabricDelvDate="fabricDelvDate_".$i;
			$cboRfFabricSource="cboRfFabricSource_".$i;
			$txtRfRemarks="txtRfRemarks_".$i;

			$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';
			
			$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
			$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
			$fab_greyQty_arr[$libDeterId]+=str_replace("'",'',$$txtGrayFabric);
			$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);

			$ex_data="";
			$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
			$new_rf_color_all_data="";
			foreach($ex_data as $color_data)
			{
				$ex_size_data=explode("_",$color_data);
				$contrast=$ex_size_data[3];
				if(str_replace("'","",$contrast)!="")
				{
					if (!in_array(str_replace("'","",$contrast),$new_array_color))
					{
						$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","440");
						$new_array_color[$fab_color_id]=str_replace("'","",$contrast);
					}
					else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
				}
				else $fab_color_id=0;
				
				if($new_rf_color_all_data=="")
				{
					$new_rf_color_all_data.=$color_data."_".$fab_color_id;
				}
				else
				{
					$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
				}
			}

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$txtProcessLoss.",".$$txtGrayFabric.",".$$fabricDelvDate.",".$$cboRfFabricSource.",".$$txtRfRemarks.",".$$txtRfReqQty.",".$$cboRfBodyPart.",".$$cboRfBodyPartType.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqQty.",".$$txtRate.",".$$txtAmount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1, ".$$libyarncountdeterminationid.", ".$$cboweighttype.", ".$$txtcuttablewidth.")";

			$data_array_col.='';

			$add_comm="";
			$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
			foreach($ex_data as $color_data)
			{
				$ex_size_data=explode("_",$color_data);
				$colorName=$ex_size_data[1];
				$colorId=$ex_size_data[2];
				$contrast=$ex_size_data[3];
				$qnty=$ex_size_data[4];
				$txtProcessLoss=$ex_size_data[5];
				$txtGrayFabric=$ex_size_data[6];
				$fab_color_id=$ex_size_data[7];


				 if($data_array_col!="") $data_array_col.=",";
				$data_array_col.="(".$idColorTbl.",".$update_id.",".$id_dtls.",'".$colorId."','".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$idColorTbl = $idColorTbl + 1;

				if($qnty>0)
				{
				$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
				}
			}
				$id_dtls=$id_dtls+1;
		}
		$yarn_deter_ids=rtrim($yarn_deter_id,',');//id_dtls
		$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.id in($yarn_deter_ids)");
		$determin_arr="";
		foreach($select_deter as $row)
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]; else $compo_per="";
			$determin_arr.=$row[csf('id')].'**'.$compo_per.'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
		}
		$determin_data=rtrim($determin_arr,'##');
		$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
		$m=0; $yarn_data_array_dtls="";
		
		$ex_data=explode("##",$determin_data);
		foreach($ex_data as $deter_data)
		{
			if ($m!=0) $yarn_data_array_dtls .=",";
			$ex_dtl_data=explode("**",$deter_data);
			$deter_mst_id=$ex_dtl_data[0];
			$percent=$ex_dtl_data[1];
			$copmposition_id=$ex_dtl_data[2];
			$count_id=$ex_dtl_data[3];
			$type_id=$ex_dtl_data[4];
			
			$fab_nature=$fab_nature_arr[$deter_mst_id];
			$fab_greyQty=$fab_greyQty_arr[$deter_mst_id];
			$fab_gsm=$fab_gsm_arr[$deter_mst_id];
			
			if(str_replace("'",'',$fab_nature)==2)
			{
				$yanr_cons=str_replace("'",'',$fab_greyQty);
			}
			if(str_replace("'",'',$fab_nature)==3)
			{
				$yanr_cons=str_replace("'",'',$fab_gsm);
			}
			
			$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$yarn_id_dtls=$yarn_id_dtls+1;
			$m++;
		} //foreach end
			

		$yarn_field_array="id, mst_id,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,inserted_by, insert_date";
		$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
		// echo "10**insert into sample_development_fabric_acc (".$field_array.") Values ".$data_array;die;
		
		if($yarn_data_array_dtls!="")
		{
			//echo "10**insert into sample_development_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
			$rID_2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);
		}
		
	//	echo "10**=".$rID_1.'--'.$rIDs.'--'.$rID_2;; die;

		if($db_type==0)
		{
			if($rID_1 && $rIDs){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_1 && $rIDs && $rID_2)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**2";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$prev_ids="SELECT id from sample_development_fabric_acc where status_active=1 and is_deleted=0 and sample_mst_id=$update_id and form_type=1";
		$prev_ids_array=array();
		foreach(sql_select($prev_ids) as $key_id=>$key_val)
		{
			$prev_ids_array[$key_val[csf("id")]]=$key_val[csf("id")];
		}

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);

		$field_array_up="sample_name*gmts_item_id*process_loss_percent*grey_fab_qnty*delivery_date*fabric_source*remarks_ra*fin_fab_qnty*body_part_id*body_part_type_id*fabric_nature_id*fabric_description*gsm*dia*color_data*color_type_id*width_dia_id*uom_id*required_qty*rate*amount*updated_by*update_date*determination_id*weight_type*cuttable_width";

		$field_array= "id, sample_mst_id, sample_name, gmts_item_id, process_loss_percent, grey_fab_qnty, delivery_date, fabric_source, remarks_ra, fin_fab_qnty, body_part_id, body_part_type_id, fabric_nature_id, fabric_description, gsm, dia, color_data, color_type_id, width_dia_id, uom_id, required_qty,rate,amount, inserted_by, insert_date, status_active, is_deleted, form_type, determination_id, weight_type, cuttable_width";
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
		$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";

		$add_comma=0; $data_array=""; //echo "10**";
		$yarn_deter_id="";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboRfSampleName="cboRfSampleName_".$i;
			$cboRfGarmentItem="cboRfGarmentItem_".$i;
			$cboRfBodyPart="cboRfBodyPart_".$i;
			$cboRfBodyPartType="cboRfBodyPartType_".$i;
			$cboRfFabricNature="cboRfFabricNature_".$i;
			$txtRfFabricDescription="txtRfFabricDescription_".$i;
			$txtRfGsm="txtRfGsm_".$i;
			$cboweighttype="cboweighttype_".$i;
			$txtRfDia="txtRfDia_".$i;
			$txtcuttablewidth="txtcuttablewidth_".$i;
			$txtRfColor="txtRfColor_".$i;
			$cboRfColorType="cboRfColorType_".$i;
			$cboRfWidthDia="cboRfWidthDia_".$i;
			$cboRfUom="cboRfUom_".$i;
			$txtRfReqQty="txtRfReqQty_".$i;
			$updateidRequiredDtlf="updateidRequiredDtl_".$i;
			$txtRfColorAllData="txtRfColorAllData_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			$txtProcessLoss="txtProcessLoss_".$i;
			$txtGrayFabric="txtGrayFabric_".$i;
			$txtRate="txtRate_".$i;
			$txtAmount="txtAmount_".$i;
			$fabricDelvDate="fabricDelvDate_".$i;
			$cboRfFabricSource="cboRfFabricSource_".$i;
			$txtRfRemarks="txtRfRemarks_".$i;
			$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';


			unset($prev_ids_array[str_replace("'",'',$$updateidRequiredDtlf)]);

			if (str_replace("'",'',$$updateidRequiredDtlf)!="")
			{
				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
				$new_rf_color_all_data="";
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$contrast=$ex_size_data[3];
					if(str_replace("'","",$contrast)!="")
					{
						if (!in_array(str_replace("'","",$contrast),$new_array_color))
						{
							$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","440");
							$new_array_color[$fab_color_id]=str_replace("'","",$contrast);

						}
						else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
					}
					else $fab_color_id=0;
					
					if($new_rf_color_all_data=="")
					{
						$new_rf_color_all_data.=$color_data."_".$fab_color_id;
					}
					else
					{
						$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
					}
				}

				$id_arr[]=str_replace("'",'',$$updateidRequiredDtlf);

				$data_array_up[str_replace("'",'',$$updateidRequiredDtlf)] =explode("*",("".$$cboRfSampleName."*".$$cboRfGarmentItem."*".$$txtProcessLoss."*
				".$$txtGrayFabric."*".$$fabricDelvDate."*".$$cboRfFabricSource."*".$$txtRfRemarks."*".$$txtRfReqQty."*".$$cboRfBodyPart."*".$$cboRfBodyPartType."*".$$cboRfFabricNature."*".$$txtRfFabricDescription."*".$$txtRfGsm."*".$$txtRfDia."*'".$new_rf_color_all_data."'*".$$cboRfColorType."*".$$cboRfWidthDia."*".$$cboRfUom."*".$$txtRfReqQty."*".$$txtRate."*".$$txtAmount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$libyarncountdeterminationid."*".$$cboweighttype."*".$$txtcuttablewidth.""));

				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
				$cc=0;
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$colorName=$ex_size_data[1];
					$colorId=$ex_size_data[2];
					if($colorId=="") $colorId=0;else $colorId=$colorId;
					$contrast=$ex_size_data[3];
					$qnty=0;
					$qnty=$ex_size_data[4];
					$txtProcessLoss=$ex_size_data[5];
					$txtGrayFabric=$ex_size_data[6];
					$fab_col_id=$ex_size_data[7] ;

					$updateidRequiredDtlfID=str_replace("'",'',$$updateidRequiredDtlf);

					if($cc!=0) { $data_array_col .=",";}

					$data_array_col.="(".$idColorTbl.",".$update_id.",".$updateidRequiredDtlfID.",".$colorId.",'".$contrast."','".$fab_col_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;
					$cc++;

					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id and fab_status_id=".$updateidRequiredDtlfID."  and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);

					if($qnty>0)
					{
						$rId_rf_status_ac=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$updateidRequiredDtlfID." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
					}
				}
			}
			else
			{
				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
				$new_rf_color_all_data="";
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$contrast=$ex_size_data[3];
					if(str_replace("'","",$contrast)!="")
					{
						if (!in_array(str_replace("'","",$contrast),$new_array_color))
						{
							$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","440");
							$new_array_color[$fab_color_id]=str_replace("'","",$contrast);

						}
						else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
					}
					else $fab_color_id=0;
					
					if($new_rf_color_all_data=="")
					{
						$new_rf_color_all_data.=$color_data."_".$fab_color_id;
					}
					else
					{
						$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
					}
				}

				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$txtProcessLoss.",".$$txtGrayFabric.",".$$fabricDelvDate.",".$$cboRfFabricSource.",".$$txtRfRemarks.",".$$txtRfReqQty.",".$$cboRfBodyPart.",".$$cboRfBodyPartType.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqQty.",".$$txtRate.",".$$txtAmount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1,".$$libyarncountdeterminationid.",".$$cboweighttype.",".$$txtcuttablewidth.")";

				$ex_datas=explode("-----",str_replace("'","",$new_rf_color_all_data));
				$data_array_cols.='';
				foreach($ex_datas as $color_datas)
				{
					$ex_size_data=explode("_",$color_datas);
					$colorName=$ex_size_data[1];
					$colorId=$ex_size_data[2];
					if($colorId=="") $colorId=0;else $colorId=$colorId;
					$contrast=$ex_size_data[3];
					$qnty=$ex_size_data[4];
					$txtProcessLoss=$ex_size_data[5];
					$txtGrayFabric=$ex_size_data[6];
					$fab_color_id=$ex_size_data[7];

					if($data_array_cols)   $data_array_cols.=",";
					$data_array_cols.="(".$idColorTbl.",".$update_id.",".$id_dtls.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;

					if($qnty>0)
					{
					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1 where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."  and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
					}
				}
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		}
		$yarn_deter_ids=rtrim($yarn_deter_id,',');
		$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.id in($yarn_deter_ids)");
		$determin_arr="";
		foreach($select_deter as $row)
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]; else $compo_per="";
			$determin_arr.=$row[csf('id')].'**'.$compo_per.'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'**'.$row[csf('dtls_id')].'##';
		}
	
		$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
		$m=0;$yarn_data_array_dtls="";
		for ($i=1;$i<=$total_row;$i++) //Yarn Start here
		{
			$cboRfFabricSource="cboRfFabricSource_".$i;
			$txtRfGsm="txtRfGsm_".$i;
			$txtRfReqDzn="txtRfReqDzn_".$i;
			$txtRfReqQty="txtRfReqQty_".$i;
			$required_fab_id="updateidRequiredDtl_".$i;
			$txtGrayFabric="txtGrayFabric_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			$cboRfFabricNature="cboRfFabricNature_".$i;
			//if ($i!=1) $libyarncountdeterminationid .=",";
			$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
			$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
			$fab_greyQty_arr[$libDeterId]+=str_replace("'",'',$$txtGrayFabric);
			$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);
			//$fab_nature_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
		}//For End
		$determin_datas=rtrim($determin_arr,'##');
		$ex_data=array_unique(explode("##",$determin_datas));
		foreach($ex_data as $deter_data)
		{
			if ($m!=0) $yarn_data_array_dtls .=",";
			$ex_dtl_data=explode("**",$deter_data);
			$deter_mst_id=$ex_dtl_data[0];
			$percent=$ex_dtl_data[1];
			$copmposition_id=$ex_dtl_data[2];
			$count_id=$ex_dtl_data[3];
			$type_id=$ex_dtl_data[4];
			$deter_dtls_id=$ex_dtl_data[5];
			
			$fab_nature=$fab_nature_arr[$deter_mst_id];
			$fab_greyQty=$fab_greyQty_arr[$deter_mst_id];
			$fab_gsm=$fab_gsm_arr[$deter_mst_id];
			
			
			if(str_replace("'",'',$fab_nature)==2)
			{
				$yanr_cons=str_replace("'",'',$fab_greyQty);
			}
			if(str_replace("'",'',$fab_nature)==3)
			{
				$yanr_cons=str_replace("'",'',$fab_gsm);
			}
			$booking_no=str_replace("'",'',$txt_booking_no);
			if($booking_no!="")
			{
			$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."','".$txt_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else{
				$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			$yarn_id_dtls=$yarn_id_dtls+1;
			$m++;
			//}
		} //foreach end

		$flag=1;
		if(count($data_array_up))
		{
			$rID_size_dlt=execute_query( "delete from sample_development_rf_color where mst_id=$update_id",0);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));

			
			if($rID1)
			{
				$del_ids=implode(",",$prev_ids_array );
				if($del_ids)
				{
					execute_query( "delete from sample_development_fabric_acc where id  in($del_ids)",0);
					execute_query( "update sample_development_dtls set fabric_status=0,fab_status_id=0 where fab_status_id  in($del_ids)",0);
				}
			}
			if($rIDs && $rID1) $flag=1; else $flag=0;
		}
		if($booking_no!="")
		{
			$yarn_field_array="id, mst_id,determin_id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,booking_no, inserted_by, insert_date";
		}
		else
		{
			$yarn_field_array="id, mst_id,determin_id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
		}
		if($flag==1)
		{
			$yarn_delete=execute_query( "delete from sample_development_yarn_dtls where mst_id  in($update_id)",0);
			if($yarn_delete) $flag=1; else $flag=0;
		}

		if($yarn_data_array_dtls!="")
		 {
			//print_r($ex_data);
			//echo "10**insert into sample_development_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
			if($flag==1)
			{
				$rID2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);
			}
			if($rID2) $flag=1; else $flag=0;
		 }

		//echo "10**".$rIDs.'='.$rID1.'='.$rID_size_dlt.'='.$rID2.'='.$yarn_delete.'='.$flag;die;
		if($data_array!="")
		{
			//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array;
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_cols,1);
			if($rID && $rIDs) $flag=1; else $flag=0;
		}

		if($txtDeltedIdRf!="" || $txtDeltedIdRf!=0)
		{
			$fields="is_deleted";
			$fields_sd="fabric_status";
			$delrfDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","fab_status_id",$txtDeltedIdRf,0);
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRf,0);
		 }

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**2";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$non_ord_booking=return_field_value("id","wo_non_ord_samp_booking_dtls","style_id=$update_id and entry_form_id=440 and status_active=1 and is_deleted=0");
		$ord_booking=return_field_value("id","wo_booking_dtls","style_id=$update_id and entry_form_id=139 and status_active=1 and is_deleted=0");
		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			die;
		}
		if($non_ord_booking*1 >0 || $ord_booking*1 >0)
		{
			echo "321**";
			die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*1",0);
		$rID1=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_accessories")
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

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc",1);
		$field_array= "id, sample_mst_id, sample_name_ra, gmts_item_id_ra, nominated_supp_multi, delivery_date, fabric_source, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, inserted_by, insert_date, status_active, is_deleted, form_type";
		
		$idsuppdtls=return_next_id( "id", "sample_development_supplier_dtls",1);
		$fieldSuppArr= "id, mst_id, rf_id, type, supplier_id, inserted_by, insert_date, status_active, is_deleted";
		$dataSuppArr=""; $q=1;
		for ($i=1; $i<=$total_row; $i++)
		{
			$cboRaSampleName="cboRaSampleName_".$i;
			$cboRaGarmentItem="cboRaGarmentItem_".$i;
			$cboRaTrimsGroup="cboRaTrimsGroup_".$i;
			$txtRaDescription="txtRaDescription_".$i;
			$txtRaBrandSupp="txtRaBrandSupp_".$i;
			$cboRaUom="cboRaUom_".$i;
			$txtRaReqDzn="txtRaReqDzn_".$i;
			$txtRaReqQty="txtRaReqQty_".$i;
			$txtRaRemarks="txtRaRemarks_".$i;
			$hidnominasupplierid="hidnominasupplierid_".$i;
			$accDate="accDate_".$i;
			$cboRaFabricSource="cboRaFabricSource_".$i;

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$hidnominasupplierid.",".$$accDate.",".$$cboRaFabricSource.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",".$$txtRaRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
			
			//Nominated Supp $idsup
			$hidnominasupplier=str_replace("'",'',$$hidnominasupplierid);
			$hidnominasupplierarr=explode(",",$hidnominasupplier);
			foreach($hidnominasupplierarr as $key=>$supplierdata)
			{
				$supplierdataarr=explode(",",$supplierdata);
				foreach($supplierdataarr as $sid){
					if($sid>0)
					{
						if ($q!=1) $dataSuppArr .=",";
						$dataSuppArr.="(".$idsuppdtls.",".$update_id.",'".$id_dtls."',2,'".$sid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idsuppdtls++; $q++;
					}
				}				
			}
			//echo "10**".$dataSuppArr; die;
			
			$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
			$id_dtls=$id_dtls+1;
		}
		//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
		$flag=1;
		$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		
		if($dataSuppArr!="" && $flag==1)
		{
			$rID_in2=sql_insert("sample_development_supplier_dtls",$fieldSuppArr,$dataSuppArr,1);
			if($rID_in2==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID.'='.$rID_in2.'='.$flag; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**3";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**3";

			}
		else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);
		$field_array_up="sample_name_ra*gmts_item_id_ra*nominated_supp_multi*delivery_date*fabric_source*trims_group_ra*description_ra*brand_ref_ra*uom_id_ra*req_dzn_ra*req_qty_ra*remarks_ra*updated_by*update_date";
		$field_array= "id, sample_mst_id, sample_name_ra, gmts_item_id_ra, nominated_supp_multi, delivery_date, fabric_source, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, inserted_by, insert_date, status_active, is_deleted, form_type";
		
		$idsuppdtls=return_next_id( "id", "sample_development_supplier_dtls",1);
		$fieldSuppArr= "id, mst_id, rf_id, type, supplier_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=""; $dataSuppArr=""; $q=1;//echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboRaSampleName="cboRaSampleName_".$i;
			$cboRaGarmentItem="cboRaGarmentItem_".$i;
			$cboRaTrimsGroup="cboRaTrimsGroup_".$i;
			$txtRaDescription="txtRaDescription_".$i;
			$txtRaBrandSupp="txtRaBrandSupp_".$i;
			$cboRaUom="cboRaUom_".$i;
			$txtRaReqDzn="txtRaReqDzn_".$i;
			$txtRaReqQty="txtRaReqQty_".$i;
			$txtRaRemarks="txtRaRemarks_".$i;
			$updateIdAccDtls="updateidAccessoriesDtl_".$i;
			$hidnominasupplierid="hidnominasupplierid_".$i;
			$accDate="accDate_".$i;
			$cboRaFabricSource="cboRaFabricSource_".$i;

			if (str_replace("'",'',$$updateIdAccDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateIdAccDtls);
				
				$accrowid=str_replace("'",'',$$updateIdAccDtls);

				$data_array_up[str_replace("'",'',$$updateIdAccDtls)] =explode("*",("".$$cboRaSampleName."*".$$cboRaGarmentItem."*".$$hidnominasupplierid."*".$$accDate."*".$$cboRaFabricSource."*".$$cboRaTrimsGroup."*".$$txtRaDescription."*".$$txtRaBrandSupp."*".$$cboRaUom."*".$$txtRaReqDzn."*".$$txtRaReqQty."*".$$txtRaRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=0 where sample_mst_id=$update_id and acc_status_id=".$$updateIdAccDtls."",0);
				$rId_acc_status_ac=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$$updateIdAccDtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
			}
			else
			{
				$accrowid=$id_dtls;
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$hidnominasupplierid.",".$$accDate.",".$$cboRaFabricSource.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",".$$txtRaRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
				$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
			
			//Nominated Supp $idsup
			$hidnominasupplier=str_replace("'",'',$$hidnominasupplierid);
			$hidnominasupplierarr=explode(",",$hidnominasupplier);
			foreach($hidnominasupplierarr as $key=>$supplierdata)
			{
				$rID_de1=execute_query( "delete from sample_development_supplier_dtls where mst_id =".$update_id." and type=2",0);
				$supplierdataarr=explode(",",$supplierdata);
				foreach($supplierdataarr as $sid){
					if($sid>0)
					{
						if ($q!=1) $dataSuppArr .=",";
						$dataSuppArr.="(".$idsuppdtls.",".$update_id.",'".$accrowid."',2,'".$sid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idsuppdtls++; $q++;
					}
				}				
			}
			//echo "10**".$dataSuppArr; die;
		}
		//echo "5**"."INSERT INTO sample_development_supplier_dtls (".$fieldSuppArr." VALUES ".$dataSuppArr; die;
		$flag=$rID=$rID1=$delSampleDtls=$del=$rID_in2=1;
		if($data_array!="")
		{
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}

		if($data_array_up!="" && $flag==1)
		{
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1) $flag=1; else $flag=0;
		}

		if($txtDeltedIdRa!="" || $txtDeltedIdRa!=0)
		{
			$fields="is_deleted";
			$fields_sd="acc_status";
			$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","acc_status_id",$txtDeltedIdRa,0);
			if($delSampleDtls==1 && $flag==1) $flag=1; else $flag=0;
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRa,0);
			if($del==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($dataSuppArr!="" && $flag==1)
		{
			$rID_in2=sql_insert("sample_development_supplier_dtls",$fieldSuppArr,$dataSuppArr,1);
			if($rID_in2==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID.'='.$rID1.'='.$delSampleDtls.'='.$del.'='.$rID_in2.'='.$flag; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**3";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		elseif($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**3";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*2",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set acc_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_wash")
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

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;	$data_array_size="";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboWaSampleName_".$i;
			$cboReGarmentItem="cboWaGarmentItem_".$i;
			$cboReName="cboWaName_".$i;
			$cboReType="cboReType_".$i;
			$cboReRemarks="txtWaRemarks_".$i;

			$cboReSupplierName="cboWaSupplierName_".$i;
			$cboReBodyPart="cboWaBodyPart_".$i;
			$deliveryDate="deliveryWaDate_".$i;
			
			$txtReQty="txtWaQty_".$i;
			$txtReRate="txtWaRate_".$i;
			$txtReAmount="txtWaAmount_".$i;
			$txtcolorBreakdown="txtWacolorBreakdown_".$i;
			//$updateIdDtls="updateidRequiredWaDtls_".$i;
			// fab_status_id,acc_status_id,embellishment_status_id
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";

			//	CONS break down===============================================================================================
		if(str_replace("'",'',$$txtcolorBreakdown) !=''){
		
			//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  wo_booking_dtls_id =".$$txtbookingid."",0);
			$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
			for($c=0;$c < count($consbreckdown_array);$c++){
				$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
				//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
				if ($c!=0) $data_array_size .=",";
				$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id1=$id1+1;
				$add_comma++;
				//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
			}
		}
		//CONS break down end===============================================================================================
			
			$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			$id_dtls=$id_dtls+1;

		}
		//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
		$flag=1;
		$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		if($rID_1) $flag=1;else $flag=0;
		
		if($data_array_size !=""){
			if($flag==1)
			{
			 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
			 if($rID2) $flag=1;else $flag=0;
			}
		}
		//echo "10**".$rID_1.'='.$rID2.'='.$flag;die;
		

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**4";

			}
		else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);

		$field_array_up="sample_name_re*gmts_item_id_re*name_re*type_re*color_size_breakdown*fin_fab_qnty*rate*amount*remarks_re*updated_by*update_date*body_part_id*supplier_id*delivery_date";
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_size_up= "mst_id*dtls_id*sample_size_dtls_id*item_id*color_id*size_id*qnty*rate*amount*updated_by*update_date*status_active*is_deleted";
		
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1) ;
		//echo "10**kausar".$total_row; die;
		$add_comma=0;$add_comma2=0;$add_comma3=0; $data_array=""; //echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboWaSampleName_".$i;
			$cboReGarmentItem="cboWaGarmentItem_".$i;
			$cboReName="cboWaName_".$i;
			$cboReType="cboReType_".$i;
			$cboReRemarks="txtWaRemarks_".$i;
			$updateIdDtls="updateidRequiredWaDtls_".$i;
			$cboReSupplierName="cboWaSupplierName_".$i;
			$cboReBodyPart="cboWaBodyPart_".$i;
			$deliveryDate="deliveryWaDate_".$i;
			$txtReQty="txtWaQty_".$i;
			$txtReRate="txtWaRate_".$i;
			$txtReAmount="txtWaAmount_".$i;
			$txtcolorBreakdown="txtWacolorBreakdown_".$i;
			
			if (str_replace("'",'',$$updateIdDtls)!="")
			{
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown) !='')
				{
					 $rID_de1=execute_query( "delete from sample_develop_embl_color_size where  dtls_id =".$$updateIdDtls."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						// if(str_replace("'",'',$consbreckdownarr[9]!=""))
						// {
						// $size_mst_update_arr[]=str_replace("'",'',$consbreckdownarr[9]);
						// $data_array_size_up[str_replace("'",'',$consbreckdownarr[9])] =explode("*",("".$update_id."*".$$updateIdDtls."*".$consbreckdownarr[7]."*".$consbreckdownarr[0]."*".$consbreckdownarr[1]."*".$consbreckdownarr[2]."*".$consbreckdownarr[3]."*".$consbreckdownarr[4]."*".$consbreckdownarr[5]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
						
						// }
						// else
						// {
							$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
							for($c=0;$c < count($consbreckdown_array);$c++){
								$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
								//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
								if ($c!=0) $data_array_size .=",";
								$data_array_size .="(".$id1.",".$update_id.",".$$updateIdDtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
								$id1=$id1+1;
								$add_comma3++;
								//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
							//}
						}
					}
				}
			//CONS break down end===============================================================================================
			
				$id_arr[]=str_replace("'",'',$$updateIdDtls);

				$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboReSampleName."*".$$cboReGarmentItem."*".$$cboReName."*".$$cboReType."*".$$txtcolorBreakdown."*".$$txtReQty."*".$$txtReRate."*".$$txtReAmount."*".$$cboReRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$cboReBodyPart."*".$$cboReSupplierName."*".$$deliveryDate.""));
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and embellishment_status_id=".$$updateIdDtls."",0);
				$rId_emb_status_ac=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$$updateIdDtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			}
			else
			{
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown) !='')
				{
					//$data_array_size="";
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
						if ($c!=0) $data_array_size .=",";
						$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$id1=$id1+1;
						$add_comma3++;
						//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
					}
				}
				//CONS break down end===============================================================================================
			
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1, embellishment_status_id=".$id_dtls."  where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		}
		//echo $data_array.'=='; die;
		//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);

		$flag=1;
		if($data_array!="")
		{
			//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array;
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		/*echo '=='.$data_array.'==';
		die;*/
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));

			if($rID1) $flag=1; else $flag=0;
		}
		if($data_array_size_up!="")
		{
			if($flag==1)
			{
				$rID2=execute_query(bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr ));
				//echo "10**".bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr );die;
				if($rID2) $flag=1; else $flag=0;
			}
		}
		
		if($data_array_size !=""){
			if($flag==1)
			{
				$rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
				if($rID2) $flag=1;else $flag=0;
			}
		}

		if($txtDeltedIdWa!="" || $txtDeltedIdWa!=0)
		{
			$fields="is_deleted";
			$fields2="status_active*is_deleted";
			$fields_sd="embellishment_status";
			$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","embellishment_status_id",$txtDeltedIdWa,0);
			// echo $delSampleDtls;die;
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdWa,0);
			$size_del=sql_multirow_update("sample_develop_embl_color_size",$fields2,"0*1","dtls_id",$txtDeltedIdWa,0);
			//echo $delSampleDtls." second ".$del;

			//$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag.'='.$delSampleDtls.'='.$del.'='.$size_del; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**4";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*3",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_print")
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

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;	$data_array_size="";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboPrSampleName_".$i;
			$cboReGarmentItem="cboPrGarmentItem_".$i;
			$cboReName="cboPrName_".$i;
			$cboReType="cboPrType_".$i;
			$cboReRemarks="txtPrRemarks_".$i;

			$cboReSupplierName="cboPrSupplierName_".$i;
			$cboReBodyPart="cboPrBodyPart_".$i;
			$deliveryDate="deliveryPrDate_".$i;
			
			$txtReQty="txtPrQty_".$i;
			$txtReRate="txtPrRate_".$i;
			$txtReAmount="txtPrAmount_".$i;
			$txtcolorBreakdown="txtPrcolorBreakdown_".$i;
			//$updateIdDtls="updateidRequiredPrDtls_".$i;
			// fab_status_id,acc_status_id,embellishment_status_id
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,4,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";

			//	CONS break down===============================================================================================
		if(str_replace("'",'',$$txtcolorBreakdown) !=''){
		
			//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  wo_booking_dtls_id =".$$txtbookingid."",0);
			$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
			for($c=0;$c < count($consbreckdown_array);$c++){
				$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
				//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
				if ($c!=0) $data_array_size .=",";
				$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id1=$id1+1;
				$add_comma++;
				//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
			}
		}
		//CONS break down end===============================================================================================
			
			$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			$id_dtls=$id_dtls+1;

		}
		//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
		$flag=1;
		$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		if($rID_1) $flag=1;else $flag=0;
		
		if($data_array_size !=""){
			if($flag==1)
			{
			 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
			 if($rID2) $flag=1;else $flag=0;
			}
		}
		//echo "10**".$rID_1.'='.$rID2.'='.$flag;die;
		

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**4";

			}
		else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);

		$field_array_up="sample_name_re*gmts_item_id_re*name_re*type_re*color_size_breakdown*fin_fab_qnty*rate*amount*remarks_re*updated_by*update_date*body_part_id*supplier_id*delivery_date";
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_size_up= "mst_id*dtls_id*sample_size_dtls_id*item_id*color_id*size_id*qnty*rate*amount*updated_by*update_date*status_active*is_deleted";
		
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1) ;
		//echo "10**kausar".$total_row; die;
		$add_comma=0;$add_comma2=0;$add_comma3=0; $data_array=""; //echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboPrSampleName_".$i;
			$cboReGarmentItem="cboPrGarmentItem_".$i;
			$cboReName="cboPrName_".$i;
			$cboReType="cboPrType_".$i;
			$cboReRemarks="txtPrRemarks_".$i;
			$updateIdDtls="updateidRequiredPrDtls_".$i;
			$cboReSupplierName="cboPrSupplierName_".$i;
			$cboReBodyPart="cboPrBodyPart_".$i;
			$deliveryDate="deliveryPrDate_".$i;
			$txtReQty="txtPrQty_".$i;
			$txtReRate="txtPrRate_".$i;
			$txtReAmount="txtPrAmount_".$i;
			$txtcolorBreakdown="txtPrcolorBreakdown_".$i;
			
			if (str_replace("'",'',$$updateIdDtls)!="")
			{
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown) !='')
				{
					 $rID_de1=execute_query( "delete from sample_develop_embl_color_size where  dtls_id =".$$updateIdDtls."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if(str_replace("'",'',$consbreckdownarr[9]==""))
						{
						$size_mst_update_arr[]=str_replace("'",'',$consbreckdownarr[9]);
						$data_array_size_up[str_replace("'",'',$consbreckdownarr[9])] =explode("*",("".$update_id."*".$$updateIdDtls."*".$consbreckdownarr[7]."*".$consbreckdownarr[0]."*".$consbreckdownarr[1]."*".$consbreckdownarr[2]."*".$consbreckdownarr[3]."*".$consbreckdownarr[4]."*".$consbreckdownarr[5]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
						
						}
						else
						{
							$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
							for($c=0;$c < count($consbreckdown_array);$c++){
								$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
								//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
								if ($c!=0) $data_array_size .=",";
								$data_array_size .="(".$id1.",".$update_id.",".$$updateIdDtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
								$id1=$id1+1;
								$add_comma3++;
								//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
							}
						}
					}
				}
			//CONS break down end===============================================================================================
			
				$id_arr[]=str_replace("'",'',$$updateIdDtls);

				$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboReSampleName."*".$$cboReGarmentItem."*".$$cboReName."*".$$cboReType."*".$$txtcolorBreakdown."*".$$txtReQty."*".$$txtReRate."*".$$txtReAmount."*".$$cboReRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$cboReBodyPart."*".$$cboReSupplierName."*".$$deliveryDate.""));
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and embellishment_status_id=".$$updateIdDtls."",0);
				$rId_emb_status_ac=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$$updateIdDtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			}
			else
			{
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown) !='')
				{
					//$data_array_size="";
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
						if ($c!=0) $data_array_size .=",";
						$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$id1=$id1+1;
						$add_comma3++;
						//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
					}
				}
				//CONS break down end===============================================================================================
			
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,4,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1, embellishment_status_id=".$id_dtls."  where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		}
		//echo $data_array.'=='; die;
		//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);

		$flag=1;
		if($data_array!="")
		{
			//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array;
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		/*echo '=='.$data_array.'==';
		die;*/
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));

			if($rID1) $flag=1; else $flag=0;
		}
		if($data_array_size_up!="")
		{
			if($flag==1)
			{
				$rID2=execute_query(bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr ));
				//echo "10**".bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr );die;
				if($rID2) $flag=1; else $flag=0;
			}
		}
		
		if($data_array_size !=""){
			if($flag==1)
			{
				$rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
				if($rID2) $flag=1;else $flag=0;
			}
		}

		if($txtDeltedIdPr!="" || $txtDeltedIdPr!=0)
		{
			$fields="is_deleted";
			$fields2="status_active*is_deleted";
			$fields_sd="embellishment_status";
			$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","embellishment_status_id",$txtDeltedIdPr,0);
			// echo $delSampleDtls;die;
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdPr,0);
			$size_del=sql_multirow_update("sample_develop_embl_color_size",$fields2,"0*1","dtls_id",$txtDeltedIdPr,0);
			//echo $delSampleDtls." second ".$del;

			//$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag.'='.$delSampleDtls.'='.$del.'='.$size_del; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**4";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*3",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_embellishment")
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

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;	$data_array_size="";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboReSampleName_".$i;
			$cboReGarmentItem="cboReGarmentItem_".$i;
			$cboReName="cboReName_".$i;
			$cboReType="cboReTypeId_".$i;
			$cboReRemarks="txtReRemarks_".$i;

			$cboReSupplierName="cboReSupplierName_".$i;
			$cboReBodyPart="cboReBodyPart_".$i;
			$deliveryDate="deliveryDate_".$i;
			
			$txtReQty="txtReQty_".$i;
			$txtReRate="txtReRate_".$i;
			$txtReAmount="txtReAmount_".$i;
			$txtcolorBreakdown="txtcolorBreakdown_".$i;
			//$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
			// fab_status_id,acc_status_id,embellishment_status_id
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,5,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";

			//	CONS break down===============================================================================================
		if(str_replace("'",'',$$txtcolorBreakdown) !=''){
		
			//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  wo_booking_dtls_id =".$$txtbookingid."",0);
			$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
			for($c=0;$c < count($consbreckdown_array);$c++){
				$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
				//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
				if ($c!=0) $data_array_size .=",";
				$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id1=$id1+1;
				$add_comma++;
				//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
			}
		}
		//CONS break down end===============================================================================================
			
			$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			$id_dtls=$id_dtls+1;

		}
		//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
		$flag=1;
		$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		if($rID_1) $flag=1;else $flag=0;
		
		if($data_array_size !=""){
			if($flag==1)
			{
			 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
			 if($rID2) $flag=1;else $flag=0;
			}
		}
		//echo "10**".$rID_1.'='.$rID2.'='.$flag;die;
		

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**4";

			}
		else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);

		$field_array_up="sample_name_re*gmts_item_id_re*name_re*type_re*color_size_breakdown*fin_fab_qnty*rate*amount*remarks_re*updated_by*update_date*body_part_id*supplier_id*delivery_date";
		$field_array= "id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, color_size_breakdown, fin_fab_qnty, rate, amount, remarks_re, inserted_by, insert_date, status_active, is_deleted, form_type, body_part_id, supplier_id, delivery_date";
		$field_array_size= "id, mst_id, dtls_id, sample_size_dtls_id, item_id, color_id, size_id, qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_size_up= "mst_id*dtls_id*sample_size_dtls_id*item_id*color_id*size_id*qnty*rate*amount*updated_by*update_date*status_active*is_deleted";
		
		$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
		$add_comma=0;$add_comma2=0;$add_comma3=0; $data_array=""; //echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboReSampleName="cboReSampleName_".$i;
			$cboReGarmentItem="cboReGarmentItem_".$i;
			$cboReName="cboReName_".$i;
			$cboReType="cboReTypeId_".$i;
			$cboReRemarks="txtReRemarks_".$i;
			$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
			$cboReSupplierName="cboReSupplierName_".$i;
			$cboReBodyPart="cboReBodyPart_".$i;
			$deliveryDate="deliveryDate_".$i;
			$txtReQty="txtReQty_".$i;
			$txtReRate="txtReRate_".$i;
			$txtReAmount="txtReAmount_".$i;
			$txtcolorBreakdown="txtcolorBreakdown_".$i;
			
			if (str_replace("'",'',$$updateIdDtls)!="")
			{
				//	CONS break down===============================================================================================
			if(str_replace("'",'',$$txtcolorBreakdown) !=''){
			
				$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  dtls_id =".$$updateIdDtls."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'",'',$consbreckdownarr[9]!=""))
					{
					// $size_mst_update_arr[]=str_replace("'",'',$consbreckdownarr[9]);
					// $data_array_size_up[str_replace("'",'',$consbreckdownarr[9])] =explode("*",("".$update_id."*".$$updateIdDtls."*".$consbreckdownarr[7]."*".$consbreckdownarr[0]."*".$consbreckdownarr[1]."*".$consbreckdownarr[2]."*".$consbreckdownarr[3]."*".$consbreckdownarr[4]."*".$consbreckdownarr[5]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
					if ($c!=0) $data_array_size .=",";
					$data_array_size .="(".$id1.",".$update_id.",".$$updateIdDtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id1=$id1+1;
					$add_comma3++;
					
					}
					else
					{
						$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
							if ($c!=0) $data_array_size .=",";
							$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
							$id1=$id1+1;
							$add_comma3++;
							//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
						}
					}
					//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
				}
			}
			//CONS break down end===============================================================================================
			
				$id_arr[]=str_replace("'",'',$$updateIdDtls);

				$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboReSampleName."*".$$cboReGarmentItem."*".$$cboReName."*".$$cboReType."*".$$txtcolorBreakdown."*".$$txtReQty."*".$$txtReRate."*".$$txtReAmount."*".$$cboReRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$cboReBodyPart."*".$$cboReSupplierName."*".$$deliveryDate.""));
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and embellishment_status_id=".$$updateIdDtls."",0);
				$rId_emb_status_ac=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$$updateIdDtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
			}
			else
			{
				//	CONS break down===============================================================================================
			if(str_replace("'",'',$$txtcolorBreakdown) !=''){
				//$data_array_size="";
				$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
					if ($c!=0) $data_array_size .=",";
					$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id1=$id1+1;
					$add_comma3++;
					//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
				}
			}
			//CONS break down end===============================================================================================
			
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,5,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1, embellishment_status_id=".$id_dtls."  where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				$id_dtls=$id_dtls+1;
				$add_comma++;

			}
		}
		//echo $data_array.'=='; die;
		//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);

		$flag=1;
		if($data_array!="")
		{
			//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array;
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		/*echo '=='.$data_array.'==';
		die;*/
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));

			if($rID1) $flag=1; else $flag=0;
		}
		if($data_array_size_up!="")
		{
			if($flag==1)
			{
			$rID2=execute_query(bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr ));
			//echo "10**".bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr );die;
			
			if($rID2) $flag=1; else $flag=0;
			}
		}
		
		if($data_array_size !=""){
			if($flag==1)
			{
			 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
			if($rID2) $flag=1;else $flag=0;
			}
		}

		if($txtDeltedIdRe!="" || $txtDeltedIdRe!=0)
		{
			$fields="is_deleted";
			$fields2="status_active*is_deleted";
			$fields_sd="embellishment_status";
			$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","embellishment_status_id",$txtDeltedIdRe,0);
			// echo $delSampleDtls;die;
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRe,0);
			$size_del=sql_multirow_update("sample_develop_embl_color_size",$fields2,"0*1","dtls_id",$txtDeltedIdRe,0);
			//echo $delSampleDtls." second ".$del;

			//$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
		}

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**4";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**4";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=449 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*3",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if($action=="load_php_dtls_form")
{
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	$readonly="";
	$sample_mst_data=sql_select("SELECT sample_stage_id, quotation_id, buyer_name, company_id from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id=449 and id='$up_id'");
	$sample_stage=0; $companyid=0;
	if(count($sample_mst_data)>0){
		foreach($sample_mst_data as $row){
			$sample_stage=$row[csf('sample_stage_id')];
			$job_id=$row[csf('quotation_id')];
			$buyer_name=$row[csf('buyer_name')];
			$companyid=$row[csf('company_id')];
		}               
	}
	
	$sql_result = sql_select("select variable_list, color_from_library from variable_order_tracking where company_name='$companyid' and variable_list in (23) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$color_from_lib=$sql_result[0][csf("color_from_library")];
	
	if($sample_stage==1){
		$order_gmts_item=sql_select("SELECT gmts_item_id, smv_pcs from wo_po_details_mas_set_details where job_id=$job_id");
		if(count($order_gmts_item)>0){
			foreach($order_gmts_item as $row){
				$order_gmts_arr[$row[csf('gmts_item_id')]]['gmt_item']=$row[csf('gmts_item_id')];
				$order_gmts_arr[$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
				$order_gmts_id_arr[$row[csf('gmts_item_id')]]=$row[csf('gmts_item_id')];
			}
		}
		$readonly="readonly";
	}
	
	
	$order_gmts_str=implode(",",$order_gmts_id_arr);

	if($type!=1)
	{
		$sql_gmts_re=sql_select("select id, gmts_item_id from sample_development_dtls where is_deleted=0 and status_active=1 and entry_form_id=449 and sample_mst_id='$up_id'");
		$gmtsf="";
		foreach ($sql_f as $rowf)
		{
			$gmtsf.=$rowf[csf("gmts_item_id")].",";
		}
		
		$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=449 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
		$samp_array=array();
		$samp_result=sql_select($sql);
		if(count($samp_result)>0)
		{
			foreach($samp_result as $keys=>$vals)
			{
				$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
			}
		}
	}
	if($type==1)
	{
		$buyer_aganist_req=return_library_array("select id, buyer_name from sample_development_mst where is_deleted=0 and status_active=1 order by buyer_name","id", "buyer_name");
		if($color_from_lib==1) $readonly="readonly";
		$sql_sam="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge,measurement_chart, sample_curency, size_data, fabric_status, acc_status, embellishment_status, sent_to_buyer_date, comments, fab_status_id from sample_development_dtls where entry_form_id=449 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
		$value=return_field_value("quotation_id","sample_development_mst","entry_form_id=449 and id='$up_id' and status_active=1 and is_deleted=0");
		$sql_result =sql_select($sql_sam); $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1) $disabletype=1;
						else $disabletype=0;
						//echo 'A';

						$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$buyer_aganist_req[$up_id] and a.business_nature=3 and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ";
						echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "-Select Sample-", $row[csf("sample_name")], "",$disabletype);
						?>
					</td>
					<td>
						<?
						if($value=="" || $value==0)
						{
							echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "-Item-",$row[csf("gmts_item_id")], "",$disabletype,"");
						}
						else
						{
							echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "-Item-",$row[csf("gmts_item_id")], "",$disabletype,$row[csf("gmts_item_id")]);
						}
						?>
					</td>
					<td>
						<input style="width:40px;" type="text" class="text_boxes_numeric" name="txtSmv_<?=$i; ?>" id="txtSmv_<?=$i; ?>" value="<?=$row[csf("smv")]; ?>"/>
						<input type="hidden" id="updateidsampledtl_<?=$i; ?>" name="updateidsampledtl_<?=$i; ?>" style="width:20px" value="<?=$row[csf("id")]; ?>" />
                        <input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
					</td>
					<td><input style="width:50px;" type="text" class="text_boxes"  name="txtArticle_<?=$i; ?>" id="txtArticle_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("article_no")]; ?>" /></td>
					<td><input style="width:70px;" type="text" class="text_boxes"  name="txtColor_<?=$i; ?>" id="txtColor_<?=$i; ?>" placeholder="Write/Browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','<?=$i; ?>');" value="<?=$color_arr[$row[csf("sample_color")]]; ?>" <?=$readonly; ?> />
                    <input type="hidden" id="hiddenColorid_<?=$i; ?>" name="hiddenColorid_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("sample_color")];?>" />
                    </td>
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<?=$i; ?>" readonly id="txtSampleProdQty_<?=$i; ?>" placeholder="Browse"  ondblclick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<?=$i;?>')" value="<?=$row[csf("sample_prod_qty")]; ?>" onFocus="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup_mouseover','Size Search','<? echo $i;?>')"   />

							<?
						}
						else 
						{
							?>
							<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<?=$i; ?>" readonly id="txtSampleProdQty_<?=$i; ?>" placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<?=$i;?>')"  value="<?=$row[csf("sample_prod_qty")]; ?>"/>
							<?
						}
						?>
                        <input type="hidden" class="text_boxes"  name="txtAllData_<?=$i;?>" id="txtAllData_<?=$i;?>" value="<?=$row[csf("size_data")]; ?>"/>
					</td>
					<td><input style="width:60px;" type="text" class="text_boxes_numeric" name="txtSubmissionQty_<?=$i; ?>" readonly id="txtSubmissionQty_<?=$i; ?>" placeholder=""  value="<?=$row[csf("submission_qty")]; ?>" /></td>
					<td><input style="width:55px;" class="datepicker" name="txtDelvStartDate_<?=$i; ?>" id="txtDelvStartDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delv_start_date")]); ?>"/></td>
					<td><input style="width:55px;" class="datepicker" name="txtDelvEndDate_<?=$i; ?>" id="txtDelvEndDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delv_end_date")]); ?>" /></td>
					<td><input style="width:55px;" class="datepicker" name="txtBuyerSubDate_<?=$i; ?>" id="txtBuyerSubDate_<?=$i; ?>" value="<?=change_date_format($row[csf("sent_to_buyer_date")]); ?>" /></td>

					<td><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<?=$i; ?>" id="txtChargeUnit_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("sample_charge")]; ?>"/></td>
					<td><? echo create_drop_down( "cboCurrency_$i", 60, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
					<td><input type="button" class="image_uploader" name="txtFile_<?=$i; ?>" id="txtFile_<?=$i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_<?=$i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
					<td><input style="width:60px;" type="text" class="text_boxes"  name="txtMchart_<?=$i; ?>" id="txtMchart_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("measurement_chart")]; ?>"/></td>

					<td><input style="width:60px;" type="text" class="text_boxes"  name="txtRemarks_<?=$i; ?>" id="txtRemarks_<?=$i; ?>" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1',<?=$i; ?>);"  value="<?=$row[csf("comments")]; ?>"/></td>
					<td>
						<?
						if($row[csf("fabric_status")] ==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input type="button" id="increase_<?=$i; ?>" name="increase_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
							<input type="button" id="decrease_<?=$i; ?>" name="decrease_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="" />
							<?
						}
						else
						{
							?>
							<input type="button" id="increase_<?=$i; ?>" name="increase_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
							<input type="button" id="decrease_<?=$i; ?>" name="decrease_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
							<?
						}
						?>
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){		
				$i=1;
				foreach($order_gmts_arr as $gmtsid=>$value)
				{
					?>
					<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
						<td>
							<?
							echo create_drop_down( "cboSampleName_$i", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$buyer_name and a.business_nature=3 and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "Select Sample", $selected, "" );
						?>
						</td>
						<td>
							<?
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item", $value['gmt_item'], "get_smv_value( $i, this.value)","",$order_gmts_str);
							?>
						</td>
						<td>
							<input style="width:40px;" type="text" class="text_boxes_numeric" name="txtSmv_<?=$i; ?>" id="txtSmv_<?=$i; ?>" value="<?= $value['smv_pcs'] ?>" readonly/>
							<input type="hidden" id="updateidsampledtl_<?=$i; ?>" name="updateidsampledtl_<?=$i; ?>" style="width:20px" value="<?=$row[csf("id")]; ?>" />
							<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
						</td>
						<td><input style="width:50px;" type="text" class="text_boxes"  name="txtArticle_<?=$i; ?>" id="txtArticle_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("article_no")]; ?>" /></td>
						<td>
							<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_<?=$i?>" id="txtColor_<?=$i?>" placeholder="Browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','<?=$i?>');" readonly/>
							<input type="hidden" id="hiddenColorid_<?=$i; ?>" name="hiddenColorid_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("sample_color")];?>" />						
						</td>
						<td>
						<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<?=$i?>" id="txtSampleProdQty_<?=$i?>"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<?=$i?>')" />
						<input type="hidden" class="text_boxes"  name="txtAllData_<?=$i;?>" id="txtAllData_<?=$i;?>" value="<?=$row[csf("size_data")]; ?>"/>
						</td>
						<td><input style="width:60px;" type="text" class="text_boxes_numeric" name="txtSubmissionQty_<?=$i; ?>" readonly id="txtSubmissionQty_<?=$i; ?>" placeholder=""  value="<?=$row[csf("submission_qty")]; ?>" /></td>
						<td><input style="width:55px;" class="datepicker" name="txtDelvStartDate_<?=$i; ?>" id="txtDelvStartDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delv_start_date")]); ?>"/></td>
						<td><input style="width:55px;" class="datepicker" name="txtDelvEndDate_<?=$i; ?>" id="txtDelvEndDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delv_end_date")]); ?>" /></td>
						<td><input style="width:55px;" class="datepicker" name="txtBuyerSubDate_<?=$i; ?>" id="txtBuyerSubDate_<?=$i; ?>" value="<?=change_date_format($row[csf("sent_to_buyer_date")]); ?>" /></td>

						<td><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<?=$i; ?>" id="txtChargeUnit_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("sample_charge")]; ?>"/></td>
						<td><? echo create_drop_down( "cboCurrency_$i", 60, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
						<td><input type="button" class="image_uploader" name="txtFile_<?=$i; ?>" id="txtFile_<?=$i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_<?=$i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
						<td><input style="width:60px;" type="text" class="text_boxes"  name="txtMchart_<?=$i; ?>" id="txtMchart_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("measurement_chart")]; ?>"/></td>

						<td><input style="width:60px;" type="text" class="text_boxes"  name="txtRemarks_<?=$i; ?>" id="txtRemarks_<?=$i; ?>" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1',<?=$i; ?>);"  value="<?=$row[csf("comments")]; ?>"/></td>
						<td>
							<?
							if($row[csf("fabric_status")] ==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
							{
								?>
								<input type="button" id="increase_<?=$i; ?>" name="increase_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
								<input type="button" id="decrease_<?=$i; ?>" name="decrease_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="" />
								<?
							}
							else
							{
								?>
								<input type="button" id="increase_<?=$i; ?>" name="increase_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
								<input type="button" id="decrease_<?=$i; ?>" name="decrease_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
								<?
							}
							?>
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
	}
	else if($type==2)
	{
		$sample_color_sql=sql_select("select sample_mst_id,sample_name,gmts_item_id,sample_color from sample_development_dtls where entry_form_id=449 and sample_mst_id=$up_id  and is_deleted=0  and status_active=1 order by id ASC");

		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		foreach($sample_color_sql as $row)
		{
			$sample_color_arr[$row[csf("sample_name")]][$row[csf("gmts_item_id")]].=$color_library[$row[csf("sample_color")]].'***';
		}

		$sql_fabric="SELECT id, sample_mst_id, sample_name, gmts_item_id, body_part_id,body_part_type_id, fabric_nature_id, fabric_description, gsm,dia, sample_color, color_type_id, width_dia_id, uom_id, required_dzn, required_qty, color_data, determination_id, fabric_source, delivery_date, process_loss_percent, grey_fab_qnty, remarks_ra, weight_type, cuttable_width,rate,amount from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and is_deleted=0 and status_active=1 order by id ASC";
		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				$fab_color=$row[csf("sample_color")];
				$gmts_item_id=$row[csf("gmts_item_id")];

				$sample_colors=rtrim($sample_color_arr[$row[csf("sample_name")]][$row[csf("gmts_item_id")]],'***');
				$sample_color_id=$sample_color_id_arr[$row[csf("sample_name")]];

				$a=$row[csf("color_data")];
				$colors="";
				$c=explode("-----",$a);
				foreach($c as $v)
				{
					$cc=explode("_",$v);
					if($colors=="")
					{
						$colors.=$cc[1];
					}
					else
					{
						$colors.='***'.$cc[1];
					}
				}

				if($sample_colors!=$colors)
				{
					$td_title='Sample Details Color is changed,You should update';
					$color_data='';
				}
				else
				{
					$td_color='';
					$td_title='';
				}
				//echo $sample_colors.'='.$colors.'D';
				$color_data=$row[csf("color_data")];
				$sample_colors=$colors;
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td id="rfSampleId_<?=$i; ?>">
						<?
						$smple=$row[csf("sample_name")];

						echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"sample_wise_item($up_id,this.value,$i,1);");
						?>
					</td>
					<td id="rfItemId_<?=$i; ?>">
						<?=create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf); ?>
					</td>
					<td id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');"); ?></td> 
					<td id="rf_body_part_type_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPartType_$i", 95, $body_part_type,"", 1, "Select Body Part Type", $row[csf("body_part_type_id")], ""); ?></td>
					<td id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","2,3"); ?> </td>
					<td id="rf_fabric_description_<?=$i; ?>">
						<input style="width:58px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="Browse" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>"/>
						<input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
					</td>
					<td id="rf_gsm_<?=$i; ?>">
						<input style="width:38px;" type="text" class="text_boxes_numeric" name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" placeholder="" value="<?=$row[csf("gsm")]; ?>"/>
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
					</td>
                    <td id="weighttype_<?=$i; ?>"><?=create_drop_down( "cboweighttype_$i", 70, $fabric_weight_type,"", 1, "-Select-", $row[csf("weight_type")], "","","" ); ?></td>
					<td id="rf_dia_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes"  name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" /></td>
                    <td id="cuttablewidth_<?=$i; ?>"><input style="width:38px;" type="text" placeholder="Write" class="text_boxes"  name="txtcuttablewidth_<?=$i; ?>" id="txtcuttablewidth_<?=$i; ?>" value="<?=$row[csf("cuttable_width")]; ?>" /></td>
					<td id="rf_color_<?=$i; ?>" title="<?=$td_title;?>" ><input style="width:58px; background-color:<?=$td_color;?>" type="text" class="text_boxes"  name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','<?=$i;?>');" readonly  value="<?=$sample_colors;?>" />
                    <input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$color_data; ?>" class="text_boxes">
					</td>
					<td id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 80, $color_type,"", 1, "-Color Type-", $row[csf("color_type_id")], "","","1,3,4,5,7,20,25,26,28,39"); ?></td>
					<td id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "-Width Type-", $row[csf("width_dia_id")], ""); ?></td>
					<td id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 50, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"","","12,27,1,23" ); ?></td>
					<td id="rf_req_qty_<?=$i; ?>">
                        <input style="width:48px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" placeholder="" value="<?=$row[csf("required_qty")]; ?>" readonly/>
                        <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_<?=$i;?>" id="txtMemoryDataRf_<?=$i; ?>" />
                    </td>
					<td id="rf_reqs_qty_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" placeholder=""  onChange="calculate_requirement('<?=$i; ?>');" value="<?=$row[csf("process_loss_percent")]; ?>" readonly /></td>
					<td id="rf_grey_qnty_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" value="<?=$row[csf("grey_fab_qnty")]; ?>" placeholder="" readonly />
					</td>

					<td id="rf_rate_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" value="<?=$row[csf("rate")]; ?>" placeholder=""  onChange="calculate_amount(<?=$i; ?>)"/>
					</td>
					<td id="rf_amount_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" value="<?=$row[csf("amount")]; ?>" placeholder="" readonly />
					</td>

					<td id="deliveryrfDateid_<?=$i; ?>"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_<?=$i; ?>" id="fabricDelvDate_<?=$i; ?>"  value="<?=change_date_format($row[csf("delivery_date")]); ?>" /></td>
					<td id="rf_fab_<?=$i; ?>"><?=create_drop_down( "cboRfFabricSource_$i", 70, $fabric_source,'', 1, "-Select-",$row[csf("fabric_source")],"","","2,3,4,5" ); ?></td>
					<td id="rf_image_<?=$i; ?>"><input type="button" class="image_uploader" name="txtRfFile_<?=$i; ?>" id="txtRfFile_<?=$i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<?=$i;?>').value,'', 'required_fabric_1', 0 ,1)" value="IMAGE"></td>
					<td id="rf_remarks_<?=$i; ?>">
					<input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_<?=$i; ?>" id="txtRfRemarks_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>"  placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2',<?=$i; ?>);"   />
					</td>
					<td>
						<input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<?=$i; ?>);" />
						<input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<?=$i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}else{
			if($sample_stage==1){
				$sql_fabric="SELECT item_number_id as gmts_item_id,body_part_id,fab_nature_id as fabric_nature_id,fabric_description,lib_yarn_count_deter_id as determination_id,gsm_weight as gsm,color_type_id,width_dia_type as width_dia_id,uom as uom_id from wo_pre_cost_fabric_cost_dtls where job_id='$job_id' and is_deleted=0  and status_active=1 order by id asc";
				//echo $sql_fabric; die;
				$sql_resultf =sql_select($sql_fabric);  $i=1;
				if(count($sql_resultf)>0)
				{
					foreach($sql_resultf as $row)
					{
						?>
						<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
						<td id="rfSampleId_<?=$i; ?>">
							<?
							$smple=$row[csf("sample_name")];

							echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"sample_wise_item($up_id,this.value,$i,1);");
							?>
						</td>
						<td id="rfItemId_<?=$i; ?>">
							<?=create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf); ?>
						</td>
						<td id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');"); ?></td> 
						<td id="rf_body_part_type_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPartType_$i", 95, $body_part_type,"", 1, "Select Body Part Type", $row[csf("body_part_type_id")], ""); ?></td>
						<td id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","2,3"); ?> </td>
						<td id="rf_fabric_description_<?=$i; ?>">
							<input style="width:58px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="Browse" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>"/>
							<input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
						</td>
						<td id="rf_gsm_<?=$i; ?>">
							<input style="width:38px;" type="text" class="text_boxes_numeric" name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" placeholder="" value="<?=$row[csf("gsm")]; ?>"/>
							<input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>"  />
							<input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
						</td>
						<td id="weighttype_<?=$i; ?>"><?=create_drop_down( "cboweighttype_$i", 70, $fabric_weight_type,"", 1, "-Select-", $row[csf("weight_type")], "","","" ); ?></td>
						<td id="rf_dia_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes"  name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" /></td>
						<td id="cuttablewidth_<?=$i; ?>"><input style="width:38px;" type="text" placeholder="Write" class="text_boxes"  name="txtcuttablewidth_<?=$i; ?>" id="txtcuttablewidth_<?=$i; ?>" value="<?=$row[csf("cuttable_width")]; ?>" /></td>
						<td id="rf_color_<?=$i; ?>" title="<?=$td_title;?>" ><input style="width:58px; background-color:<?=$td_color;?>" type="text" class="text_boxes"  name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','<?=$i;?>');" readonly  value="<?=$sample_colors;?>" />
						<input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$color_data; ?>" class="text_boxes">
						</td>
						<td id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 80, $color_type,"", 1, "-Color Type-", $row[csf("color_type_id")], "","","1,3,4,5,7,20,25,26,28,39"); ?></td>
						<td id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "-Width Type-", $row[csf("width_dia_id")], ""); ?></td>
						<td id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 50, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"","","12,27,1,23" ); ?></td>
						<td id="rf_req_qty_<?=$i; ?>">
							<input style="width:48px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" placeholder="" value="<?=$row[csf("required_qty")]; ?>" readonly/>
							<input type="hidden" class="text_boxes"  name="txtMemoryDataRf_<?=$i;?>" id="txtMemoryDataRf_<?=$i; ?>" />
						</td>
						<td id="rf_reqs_qty_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" placeholder=""  onChange="calculate_requirement('<?=$i; ?>');" value="<?=$row[csf("process_loss_percent")]; ?>" readonly /></td>
						<td id="rf_grey_qnty_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" value="<?=$row[csf("grey_fab_qnty")]; ?>" placeholder="" readonly />
						</td>

						<td id="rf_rate_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" value="<?=$row[csf("rate")]; ?>" placeholder=""  onChange="calculate_amount(<?=$i; ?>)"/>
						</td>
						<td id="rf_amount_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" value="<?=$row[csf("amount")]; ?>" placeholder="" readonly />
						</td>

						<td id="deliveryrfDateid_<?=$i; ?>"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_<?=$i; ?>" id="fabricDelvDate_<?=$i; ?>"  value="<?=change_date_format($row[csf("delivery_date")]); ?>" /></td>
						<td id="rf_fab_<?=$i; ?>"><?=create_drop_down( "cboRfFabricSource_$i", 70, $fabric_source,'', 1, "-Select-",$row[csf("fabric_source")],"","","2,3,4,5" ); ?></td>
						<td id="rf_image_<?=$i; ?>"><input type="button" class="image_uploader" name="txtRfFile_<?=$i; ?>" id="txtRfFile_<?=$i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<?=$i;?>').value,'', 'required_fabric_1', 0 ,1)" value="IMAGE"></td>
						<td id="rf_remarks_<?=$i; ?>">
						<input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_<?=$i; ?>" id="txtRfRemarks_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>"  placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2',<?=$i; ?>);"   />
						</td>
						<td>
							<input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<?=$i; ?>);" />
							<input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<?=$i; ?>);" />
						</td>
					</tr>
						<?
						$i++;
					}
				}
			}
	   }
	}
	else if($type==3)
	{
		$supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
		$itemGroupArr=return_library_array("select id, item_name from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
		
		$sql_sam="SELECT id, sample_mst_id, sample_name_ra, gmts_item_id_ra, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, supplier_id, delivery_date, fabric_source, nominated_supp_multi from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				$nominated_supp_str="";
				 $exnominated_supp=explode(",",$row[csf('nominated_supp_multi')]);
				 foreach($exnominated_supp as $supp)
				 {
					if($nominated_supp_str=="") $nominated_supp_str=$supplier_library[$supp]; else $nominated_supp_str.=','.$supplier_library[$supp];
				 }
				?>
				<tr  id="tr_<?=$i; ?>" class="general">
					<td id="raSampleId_<?=$i; ?>">
						<?=create_drop_down( "cboRaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_ra")], "sample_wise_item($up_id,this.value,$i,2);",""); ?>
					</td>
					<td id="raItemId_<?=$i; ?>">
						<?=create_drop_down( "cboRaGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_ra")] , "",0,$gmtsf); ?>
					</td>
					<td id="ra_trims_group_<?=$i; ?>">
                    	<input placeholder="Browse" title="<?=$itemGroupArr[$row[csf("trims_group_ra")]]; ?>" readonly type="text" id="cbogrouptext_<?=$i; ?>" name="cbogrouptext_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$itemGroupArr[$row[csf("trims_group_ra")]]; ?>" onDblClick="openpopup_itemgroup(<?=$i; ?>);"/>
	                    <input type="hidden" id="cboRaTrimsGroup_<?=$i; ?>" name="cboRaTrimsGroup_<?=$i; ?>" class="text_boxes" style="width:50px" value="<?=$row[csf("trims_group_ra")]; ?>"/>
                    
						<? //=create_drop_down( "cboRaTrimsGroup_$i", 100, $itemGroupArr,"", 1, "Select Item", $row[csf("trims_group_ra")] , "load_uom_for_trims('$i',this.value);"); ?>
					</td>
					<td id="ra_description_<?=$i; ?>">
						<input style="width:120px;" type="text" class="text_boxes"  name="txtRaDescription_<? echo $i;?>" id="txtRaDescription_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("description_ra")]; ?>"/>
						<input type="hidden" id="updateidAccessoriesDtl_<? echo $i;?>" name="updateidAccessoriesDtl_<? echo $i;?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<td>
						<input readonly type="text" id="txtnominasupplier_<?=$i; ?>" name="txtnominasupplier_<?=$i; ?>" class="text_boxes" placeholder="Browse" style="width:90px" value="<?=$nominated_supp_str; ?>" onDblClick="fncopenpopup_trimsupplier(<?=$i; ?>);"/>
                        <input type="hidden" id="hidnominasupplierid_<?=$i; ?>" name="hidnominasupplierid_<?=$i; ?>" class="text_boxes" style="width:50px" value="<?=$row[csf("nominated_supp_multi")]; ?>" />
                    </td>
					
					<td id="ra_brand_supp_<?=$i; ?>">
						<input style="width:80px;" type="text" class="text_boxes"  name="txtRaBrandSupp_<?=$i;?>" id="txtRaBrandSupp_<?=$i;?>" placeholder="Write" value="<?=$row[csf("brand_ref_ra")]; ?>"/>
                        <input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
					</td>
					<td id="ra_uom_<?=$i; ?>"><?=create_drop_down( "cboRaUom_$i", 60, $unit_of_measurement,'', '', "",$row[csf("uom_id_ra")],"","","" ); ?></td>
					<td id="ra_req_dzn_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRaReqDzn_<?=$i;?>" id="txtRaReqDzn_<?=$i;?>" placeholder="Write" value="<?=$row[csf("req_dzn_ra")]; ?>" /></td>
					<td id="ra_req_qty_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes_numeric" name="txtRaReqQty_<?=$i;?>" id="txtRaReqQty_<?=$i;?>" placeholder="Write" value="<?=$row[csf("req_qty_ra")]; ?>" />
                        <input type="hidden" class="text_boxes"  name="txtMemoryDataRa_<?=$i;?>" id="txtMemoryDataRa_<?=$i;?>" />
					</td>
					<td id="deliveryraDateid_<?=$i;?>">
						<input style="width:55px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="accDate_<?=$i;?>" id="accDate_<?=$i;?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" />
					</td>
					<td id="ra_fab_<?=$i;?>"><?=create_drop_down( "cboRaFabricSource_$i", 80, $fabric_source,'', '',"",$row[csf("fabric_source")],"","","2,3,4,5" ); ?></td>
					<td id="ra_remarks_<?=$i; ?>">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRaRemarks_<? echo $i;?>" id="txtRaRemarks_<? echo $i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','3',<? echo $i; ?>);"  value="<? echo $row[csf("remarks_ra")]; ?>" />
					</td>
					<td id="ra_image_<?=$i; ?>"><input type="button" class="image_uploader" name="txtRaFile_<?=$i;?>" id="txtRaFile_<?=$i;?>" onClick="file_uploader ( '../../', document.getElementById('updateidAccessoriesDtl_<?=$i;?>').value,'', 'required_accessories_1', 0 ,1)"style="width:70px;" value="ADD IMAGE"></td>
					<td>
						<input type="button" id="increasera_<? echo $i;?>" name="increasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(<?=$i;?>);" />
						<input type="button" id="decreasera_<? echo $i;?>" name="decreasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(<?=$i;?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
	}
	else if($type==4) //Wash
	{
		$supplierNameArr=return_library_array("select a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name", "id", "supplier_name");
		
		$sql_sam="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id, color_size_breakdown, fin_fab_qnty, rate, amount from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and is_deleted=0 and status_active=1 and name_re='3' order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
					<td id="waSampleId_<?=$i;?>">
						<?=create_drop_down( "cboWaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
					</td>
					<td id="waItemIid_<?=$i;?>">
						<?=create_drop_down( "cboWaGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$gmtsf); ?>

						<input type="hidden" id="updateidRequiredWaDtls_<?=$i;?>" name="updateidRequiredWaDtls_<?=$i;?>"   style="width:20px;" value="<?=$row[csf("id")]; ?>" />
						<input type="hidden" id="txtDeltedIdWa" name="txtDeltedIdWa"   style="width:20px;" value="" class="text_boxes"/>
					</td>
					<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboWaName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "cbotype_loder($i);","",3); ?></td>
					<td id="reType_<?=$i ?>"><?=create_drop_down( "cboReType_$i", 120, $emblishment_wash_type,"", 1, "Select Type",$row[csf("type_re")] , ""); ?></td>
					<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboWaBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
					<td><?=create_drop_down( "cboWaSupplierName_$i", 100, $supplierNameArr,"", 1, "-Select Supplier-", $row[csf("supplier_id")], "",0 ); ?></td>
						
                    <td id="re_qty_<?=$i;?>">
						<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaQty_<?=$i;?>" id="txtWaQty_<?=$i;?>" placeholder="click"  readonly="" onClick="open_consumption_popupWash('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<?=$i;?>)" value="<?=$row[csf("fin_fab_qnty")]; ?>"/>
                          <input style="width:60px;" type="hidden" class="text_boxes"  name="txtWacolorBreakdown_<? echo $i;?>" id="txtWacolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
                        </td>
                        <td id="re_rate_<?=$i;?>">
                            <input style="width:50px;" type="text" class="text_boxes_numeric" name="txtWaRate_<? echo $i;?>" id="txtWaRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
                        </td>
                        <td id="re_amount_<?=$i;?>">
                            <input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaAmount_<? echo $i;?>" id="txtWaAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
                        </td>

					<td id="re_remarks_<?=$i;?>">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtWaRemarks_<? echo $i;?>" id="txtWaRemarks_<? echo $i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>
					<td id="deliveryDateid_<?=$i;?>">
					<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryWaDate_<?=$i;?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" id="deliveryWaDate_<?=$i;?>" />
					</td>
					<td id="re_image_<?=$i;?>"><input type="button" class="image_uploader" name="waTxtFile_<?=$i;?>" id="waTxtFile_<?=i;?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredWaDtls_<?=$i;?>').value,'', 'requiredwash_1', 0 ,1);"></td>
					<td>
						<input type="button" id="increasere_<?=$i; ?>" name="increasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_wash_tr(<?=$i; ?>);" />
						<input type="button" id="decreasere_<?=$i; ?>" name="decreasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_wash_deleteRow(<?=$i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){
				$sql_sam="SELECT id,emb_name as name_re, emb_type as type_re,body_part_id, nominated_supp_multi as supplier_id from wo_pre_cost_embe_cost_dtls where job_id='$job_id' and  is_deleted=0  and status_active=1 and  emb_type>0 and emb_name=3 order by id asc";
				$sql_result =sql_select($sql_sam);  $i=1;
				foreach($sql_result as $row)
				{
					?>
					<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
						<td id="waSampleId_<?=$i;?>">
							<?=create_drop_down( "cboWaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
						</td>
						<td id="waItemIid_<?=$i;?>">
							<?=create_drop_down( "cboWaGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$order_gmts_str); ?>

							<input type="hidden" id="updateidRequiredWaDtls_<?=$i;?>" name="updateidRequiredWaDtls_<?=$i;?>"   style="width:20px;" value="" />
							<input type="hidden" id="txtDeltedIdWa" name="txtDeltedIdWa"   style="width:20px;" value="" class="text_boxes"/>
						</td>
						<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboWaName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "cbotype_loder($i);","",3); ?></td>
						<td id="reType_<?=$i ?>"><?=create_drop_down( "cboReType_$i", 120, $emblishment_wash_type,"", 1, "Select Type",$row[csf("type_re")] , ""); ?></td>
						<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboWaBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
						<td><?=create_drop_down( "cboWaSupplierName_$i", 100, $supplierNameArr,"", 1, "-Select Supplier-", $row[csf("supplier_id")], "",0 ); ?></td>
							
						<td id="re_qty_<?=$i;?>">
							<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaQty_<?=$i;?>" id="txtWaQty_<?=$i;?>" placeholder="click"  readonly="" onClick="open_consumption_popupWash('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<?=$i;?>)" value="<?=$row[csf("fin_fab_qnty")]; ?>"/>
							<input style="width:60px;" type="hidden" class="text_boxes"  name="txtWacolorBreakdown_<? echo $i;?>" id="txtWacolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
							</td>
							<td id="re_rate_<?=$i;?>">
								<input style="width:50px;" type="text" class="text_boxes_numeric" name="txtWaRate_<? echo $i;?>" id="txtWaRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
							</td>
							<td id="re_amount_<?=$i;?>">
								<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaAmount_<? echo $i;?>" id="txtWaAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
							</td>

						<td id="re_remarks_<?=$i;?>">
							<input style="width:90px;" type="text" class="text_boxes"  name="txtWaRemarks_<? echo $i;?>" id="txtWaRemarks_<? echo $i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
						</td>
						<td id="deliveryDateid_<?=$i;?>">
						<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryWaDate_<?=$i;?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" id="deliveryWaDate_<?=$i;?>" />
						</td>
						<td id="re_image_<?=$i;?>"><input type="button" class="image_uploader" name="waTxtFile_<?=$i;?>" id="waTxtFile_<?=i;?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredWaDtls_<?=$i;?>').value,'', 'requiredwash_1', 0 ,1);"></td>
						<td>
							<input type="button" id="increasere_<?=$i; ?>" name="increasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_wash_tr(<?=$i; ?>);" />
							<input type="button" id="decreasere_<?=$i; ?>" name="decreasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_wash_deleteRow(<?=$i; ?>);" />
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
	}
	else if($type==5) //Print
	{
		   $sql_sam="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id, color_size_breakdown, fin_fab_qnty, rate, amount from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=4 and is_deleted=0 and status_active=1 and name_re='1' order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
					<td id="prSampleId_<?=$i;?>">
						<?=create_drop_down( "cboPrSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
					</td>
					<td id="prItemIid_<?=$i;?>">
						<?=create_drop_down( "cboPrGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$gmtsf); ?>

						<input type="hidden" id="updateidRequiredPrDtls_<? echo $i;?>" name="updateidRequiredPrDtls_<? echo $i;?>"   style="width:20px;" value="<? echo $row[csf("id")]; ?>" class="text_boxes"/>
						<input type="hidden" id="txtDeltedIdPr" name="txtDeltedIdPr"   style="width:20px;" value="" class="text_boxes"/>
					</td>
					<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboPrName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "","",1);//cbotype_loder($i); ?></td>
					<td id="reType_<?=$i ?>"><?=create_drop_down( "cboPrType_$i", 120, $emblishment_print_type,"", 1, "Select Type",$row[csf("type_re")] , ""); ?>
					</td>
					<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboPrBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
					<td><? echo create_drop_down( "cboPrSupplierName_$i", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $row[csf("supplier_id")], "",0 ); ?> </td>
						
                    <td id="re_qty_<?=$i;?>">
						<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrQty_<? echo $i;?>" id="txtPrQty_<? echo $i;?>" placeholder="click"  readonly="" onClick="open_consumption_popupPrint('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<? echo $i;?>)" value="<? echo $row[csf("fin_fab_qnty")]; ?>"/>
                          <input style="width:60px;" type="hidden" class="text_boxes"  name="txtPrcolorBreakdown_<? echo $i;?>" id="txtPrcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
                        </td>
                        <td id="re_rate_<?=$i;?>">
                            <input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtPrRate_<? echo $i;?>" id="txtPrRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
                        </td>
                        <td id="re_amount_<?=$i;?>">
                            <input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrAmount_<? echo $i;?>" id="txtPrAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
                        </td>

					<td id="re_remarks_<?=$i;?>">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtPrRemarks_<? echo $i;?>" id="txtPrRemarks_<? echo $i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','5',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>
					<td id="deliveryDateid_<?=$i;?>">
					<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryPrDate_<? echo $i;?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" id="deliveryPrDate_<? echo $i;?>" />
					</td>
					<td id="re_image_<?=$i;?>"><input type="button" class="image_uploader" name="prTxtFile_<? echo $i;?>" id="prTxtFile_<? echo $i;?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredPrDtls_<? echo $i;?>').value,'', 'requiredPrint_1', 0 ,1);"></td>
					<td>
						<input type="button" id="increasere_<? echo $i; ?>" name="increasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_print_tr(<?=$i; ?>);" />
						<input type="button" id="decreasere_<? echo $i; ?>" name="decreasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_print_deleteRow(<?=$i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){
				$sql_sam="SELECT id,emb_name as name_re, emb_type as type_re,body_part_id, nominated_supp_multi as supplier_id from wo_pre_cost_embe_cost_dtls where job_id='$job_id' and  is_deleted=0  and status_active=1 and  emb_type>0 and emb_name=1 order by id asc";
				$sql_result =sql_select($sql_sam);  $i=1;
				foreach($sql_result as $row)
				{
					?>
					<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
						<td id="prSampleId_<?=$i;?>">
							<?=create_drop_down( "cboPrSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
						</td>
						<td id="prItemIid_<?=$i;?>">
							<?=create_drop_down( "cboPrGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$order_gmts_str); ?>

							<input type="hidden" id="updateidRequiredPrDtls_<? echo $i;?>" name="updateidRequiredPrDtls_<? echo $i;?>"   style="width:20px;" value="" class="text_boxes"/>
							<input type="hidden" id="txtDeltedIdPr" name="txtDeltedIdPr"   style="width:20px;" value="" class="text_boxes"/>
						</td>
						<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboPrName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "","",1);//cbotype_loder($i); ?></td>
						<td id="reType_<?=$i ?>"><?=create_drop_down( "cboPrType_$i", 120, $emblishment_print_type,"", 1, "Select Type",$row[csf("type_re")] , ""); ?>
						</td>
						<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboPrBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
						<td><? echo create_drop_down( "cboPrSupplierName_$i", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $row[csf("supplier_id")], "",0 ); ?> </td>
							
						<td id="re_qty_<?=$i;?>">
							<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrQty_<? echo $i;?>" id="txtPrQty_<? echo $i;?>" placeholder="click"  readonly="" onClick="open_consumption_popupPrint('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<? echo $i;?>)" value="<? echo $row[csf("fin_fab_qnty")]; ?>"/>
							<input style="width:60px;" type="hidden" class="text_boxes"  name="txtPrcolorBreakdown_<? echo $i;?>" id="txtPrcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
							</td>
							<td id="re_rate_<?=$i;?>">
								<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtPrRate_<? echo $i;?>" id="txtPrRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
							</td>
							<td id="re_amount_<?=$i;?>">
								<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrAmount_<? echo $i;?>" id="txtPrAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
							</td>

						<td id="re_remarks_<?=$i;?>">
							<input style="width:90px;" type="text" class="text_boxes"  name="txtPrRemarks_<? echo $i;?>" id="txtPrRemarks_<? echo $i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','5',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
						</td>
						<td id="deliveryDateid_<?=$i;?>">
						<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryPrDate_<? echo $i;?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" id="deliveryPrDate_<? echo $i;?>" />
						</td>
						<td id="re_image_<?=$i;?>"><input type="button" class="image_uploader" name="prTxtFile_<? echo $i;?>" id="prTxtFile_<? echo $i;?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredPrDtls_<? echo $i;?>').value,'', 'requiredPrint_1', 0 ,1);"></td>
						<td>
							<input type="button" id="increasere_<? echo $i; ?>" name="increasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_print_tr(<?=$i; ?>);" />
							<input type="button" id="decreasere_<? echo $i; ?>" name="decreasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_print_deleteRow(<?=$i; ?>);" />
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
	}
	else if($type==6)
	{
		$supplierNameArr=return_library_array("select a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name", "id", "supplier_name");
		
		$sql_sam="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id, color_size_breakdown, fin_fab_qnty, rate, amount from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=5 and is_deleted=0 and status_active=1 and name_re='2' order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
					<td id="reSampleId_<?=$i;?>">
						<?=create_drop_down( "cboReSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
					</td>
					<td id="reItemIid_<?=$i;?>">
						<?=create_drop_down( "cboReGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$gmtsf); ?>

						
						<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"   style="width:20px;" value="" class="text_boxes"/>
					</td>
					<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboReName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "","",2);//cbotype_loder($i); ?></td>
					<td id="reType_<?=$i; ?>"><?=create_drop_down( "cboReTypeId_$i",120,$emblishment_embroy_type,"",1, "Select Type", $row[csf("type_re")], ""); ?></td>
					<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboReBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
					<td><?=create_drop_down( "cboReSupplierName_$i", 100, $supplierNameArr,"", 1, "-Select Supplier-", $row[csf("supplier_id")], "",0 ); ?></td>
						
                    <td id="re_qty_<?=$i;?>">
						<input style="width:60px;" type="text" class="text_boxes_numeric" name="txtReQty_<?=$i;?>" id="txtReQty_<?=$i;?>" placeholder="Click"  readonly="" onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<?=$i;?>)" value="<?=$row[csf("fin_fab_qnty")]; ?>"/>
                          <input style="width:60px;" type="hidden" class="text_boxes" name="txtcolorBreakdown_<? echo $i;?>" id="txtcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
                        </td>
                        <td id="re_rate_<?=$i;?>">
                            <input style="width:50px;" type="text" class="text_boxes_numeric" name="txtReRate_<? echo $i;?>" id="txtReRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
                        </td>
                        <td id="re_amount_<?=$i;?>">
                            <input style="width:60px;" type="text" class="text_boxes_numeric" name="txtReAmount_<?=$i;?>" id="txtReAmount_<?=$i;?>" value="<?=$row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
                        </td>

					<td id="re_remarks_<?=$i;?>">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<?=$i;?>" id="txtReRemarks_<?=$i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','6',<?=$i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>
					<td id="deliveryDateid_<?=$i; ?>">
					<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryDate_<?=$i;?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" id="deliveryDate_<?=$i;?>" />
					</td>
					<td id="re_image_<?=$i; ?>"><input type="button" class="image_uploader" name="reTxtFile_<?=$i;?>" id="reTxtFile_<?=$i; ?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredEmbellishdtl_<?=$i;?>').value,'', 'required_embellishment_1', 0 ,1);">
					<input type="hidden" id="updateidRequiredEmbellishdtl_<? echo $i;?>" name="updateidRequiredEmbellishdtl_<? echo $i;?>"   style="width:20px;" value="<? echo $row[csf("id")]; ?>" class="text_boxes"/>
				</td>
					<td>
						<input type="button" id="increasere_<?=$i; ?>" name="increasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(<?=$i; ?>);" />
						<input type="button" id="decreasere_<?=$i; ?>" name="decreasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(<?=$i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){
				$sql_sam="SELECT id,emb_name as name_re, emb_type as type_re,body_part_id, nominated_supp_multi as supplier_id from wo_pre_cost_embe_cost_dtls where job_id='$job_id' and  is_deleted=0  and status_active=1 and  emb_type>0 and emb_name=2 order by id asc";
				$sql_result =sql_select($sql_sam);  $i=1;
				if(count($sql_result)>0)
				{
					foreach($sql_result as $row)
					{
						?>
						<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
							<td id="reSampleId_<?=$i;?>">
								<?=create_drop_down( "cboReSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);",""); ?>
							</td>
							<td id="reItemIid_<?=$i;?>">
								<?=create_drop_down( "cboReGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$order_gmts_str); ?>

								
								<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"   style="width:20px;" value="" class="text_boxes"/>
							</td>
							<td id="re_name_<?=$i;?>"><?=create_drop_down( "cboReName_$i", 120, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "","",""); ?></td>
							<td id="reType_<?=$i; ?>"><?
							$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
							echo create_drop_down( "cboReTypeId_$i", 120, $type_array[$row[csf("name_re")]],"", 1, "Select Type",$row[csf("type_re")] , "");
							//create_drop_down( "cboReTypeId_$i",120,$emblishment_embroy_type,"",1, "Select Type", $row[csf("type_re")], ""); ?>
							</td>
							<td id="re_body_part_<?=$i;?>"><?=create_drop_down( "cboReBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], ""); ?></td>
							<td><?=create_drop_down( "cboReSupplierName_$i", 100, $supplierNameArr,"", 1, "-Select Supplier-", $row[csf("supplier_id")], "",0 ); ?></td>
								
							<td id="re_qty_<?=$i;?>">
								<input style="width:60px;" type="text" class="text_boxes_numeric" name="txtReQty_<?=$i;?>" id="txtReQty_<?=$i;?>" placeholder="Click"  readonly="" onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<?=$i;?>)" value="<?=$row[csf("fin_fab_qnty")]; ?>"/>
								<input style="width:60px;" type="hidden" class="text_boxes" name="txtcolorBreakdown_<? echo $i;?>" id="txtcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
								</td>
								<td id="re_rate_<?=$i;?>">
									<input style="width:50px;" type="text" class="text_boxes_numeric" name="txtReRate_<? echo $i;?>" id="txtReRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
								</td>
								<td id="re_amount_<?=$i;?>">
									<input style="width:60px;" type="text" class="text_boxes_numeric" name="txtReAmount_<?=$i;?>" id="txtReAmount_<?=$i;?>" value="<?=$row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
								</td>

							<td id="re_remarks_<?=$i;?>">
								<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<?=$i;?>" id="txtReRemarks_<?=$i;?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','6',<?=$i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
							</td>
							<td id="deliveryDateid_<?=$i; ?>">
							<input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryDate_<?=$i;?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" id="deliveryDate_<?=$i;?>" />
							</td>
							<td id="re_image_<?=$i; ?>"><input type="button" class="image_uploader" name="reTxtFile_<?=$i;?>" id="reTxtFile_<?=$i; ?>" size="20" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredEmbellishdtl_<?=$i;?>').value,'', 'required_embellishment_1', 0 ,1);">
							<input style="width:60px;" type="hidden" class="text_boxes_numeric" name="updateidRequiredEmbellishdtl_<?=$i;?>" id="updateidRequiredEmbellishdtl_<?=$i;?>"  placeholder="Amount"  readonly="" />
						</td>
							<td>
								<input type="button" id="increasere_<?=$i; ?>" name="increasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(<?=$i; ?>);" />
								<input type="button" id="decreasere_<?=$i; ?>" name="decreasere_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(<?=$i; ?>);" />
							</td>
						</tr>
						<?
						$i++;
					}
				}
			}
		}
	}
	exit();
}

if($action=="openpopup_itemgroup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		/*function check_all_data() {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}*/
		var selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
					document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
					if( jQuery.inArray( $('#txttrimgroupdata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txttrimgroupdata_' + i).val());
					}
				}
				var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + '__';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 2 );
                $('#itemdata').val( trimgroupdata );
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txttrimgroupdata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
				var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + '__';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 2 );
                $('#itemdata').val( trimgroupdata );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		//var selected_name = new Array();

		function js_set_value( str ) {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if($("#search"+str).css("display") !='none'){
				//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txttrimgroupdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimgroupdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimgroupdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + '__';
			}
			if(selected_name.length == tbl_row_count){
                document.getElementById("check_all").checked = true;
            }
            else{
                document.getElementById("check_all").checked = false;
            }
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 2 );

			$('#itemdata').val( trimgroupdata );
		}

	/*function js_set_value(id, name)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		parent.emailwindow.hide();
	}*/
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="itemdata" name="itemdata"/>
        <? $sql_tgroup=sql_select( "select id, item_name, order_uom,trim_uom,trim_type from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name"); ?>
        <table width="470" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
            	<th  width="190">Item Group</th>
                <th width="50">Order Uom</th>
                 <th width="50">Cons Uom</th>
                <th width="">Trims Type</th>
            </thead>
        </table>
        <div style="width:470px; overflow-y:scroll; max-height:340px;" >
        <table width="450" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row_tgroup)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row_tgroup[csf('id')].'***'.$row_tgroup[csf('item_name')].'***'.$row_tgroup[csf('trim_uom')];
					?>
					<tr id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td  width="190"><? echo $row_tgroup[csf('item_name')]; ?>
                        	<input type="hidden" name="txttrimgroupdata_<? echo $i; ?>" id="txttrimgroupdata_<? echo $i; ?>" value="<? echo $str; ?>"/>
                        </td>
                        <td width="50"><? echo $unit_of_measurement[$row_tgroup[csf('order_uom')]]; ?></td>
                         <td width="50"><? echo $unit_of_measurement[$row_tgroup[csf('trim_uom')]]; ?></td>
                        <td width=""><p><? echo $trim_type[$row_tgroup[csf('trim_type')]]; ?></p></td>

					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if($action == "trims_cost_template_name_popup")
{
	extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);?>
    <script>
        var selected_name = new Array();
    	function fnc_close(data) {
            var data=data.split('_');
            var check_trims_data = return_global_ajax_value( data[1], 'check_trims_data', '', 'quotation_entry_controller');
            if(check_trims_data == 1)
            {
                 var r=confirm("If you want to replace previous data then press Ok, otherwise press Cancel");
                if(r==false)
                {
                    parent.emailwindow.hide();
                    return;
                }
                else
                {
                    document.getElementById('hidden_template_name').value=data[0];
                    parent.emailwindow.hide();
                }
            }
            else
            {
                document.getElementById('hidden_template_name').value=data[0];
                parent.emailwindow.hide();
            }
        }
        function insert_template_data(data,id)
        {
            var template_data=return_global_ajax_value( data, 'get_template_data', '', 'sample_requisition_with_booking_controller');
            var template_data=trim(template_data) ;
            var tbl_row_count = document.getElementById( 'template_name_tbl' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            for(var i=0; i<tbl_row_count; i++){
                var color = document.getElementById( 'tempname'+i ).style.backgroundColor;
                if(color == "yellow"){
                   if(id%2==0  ){
                    toggle( document.getElementById( 'tempname' + i ), '#FFFFFF');
                    }
                    if(id%2!=0 ){
                    toggle( document.getElementById( 'tempname' + i ), '#E9F3FF');
                    }
                }
            }
            if(template_data)
            {
            	if(id%2==0  ){
                    toggle( document.getElementById( 'tempname' + id ), '#FFFFFF');
                }
                if(id%2!=0 ){
                    toggle( document.getElementById( 'tempname' + id ), '#E9F3FF');
                }
                $("tbody#template_date").html('');
                $("tbody#template_date").append(template_data);
                $('#check_all_tbl').css('display','block');
            }
        }
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'template_data_tbl' ).rows.length-1;
            tbl_row_count = tbl_row_count;

            if(document.getElementById('check_all').checked){
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txttemplatedata_' + i).val(), selected_name ) == -1 ) {
	                    selected_name.push($('#txttemplatedata_' + i).val());
	                }
                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + ',';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );
                $('#select_template_data').val( templatedata );
            }else{
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0  ){
                        document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    }
                    if(i%2!=0 ){
                        document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
                    }
                    for( var j = 0; j < selected_name.length; j++ ) {
                        if( selected_name[j] == $('#txttemplatedata_' + i).val() ) break;
                    }
                    selected_name.splice( j,1 );

                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + ',';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );
                $('#select_template_data').val( templatedata );

            }

        }

        function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;

            }
        }

        function onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function js_set_value( str) {
        	var tbl_row_count = document.getElementById( 'template_data_tbl' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            if($("#search"+str).css("display") !='none'){
                if(str%2==0  ){
                    toggle( document.getElementById( 'search' + str ), '#FFFFFF');
                }
                if(str%2!=0 ){
                    toggle( document.getElementById( 'search' + str ), '#E9F3FF');
                }
                if( jQuery.inArray( $('#txttemplatedata_' + str).val(), selected_name ) == -1 ) {
                    selected_name.push($('#txttemplatedata_' + str).val());
                }
                else{
                    for( var i = 0; i < selected_name.length; i++ ) {
                        if( selected_name[i] == $('#txttemplatedata_' + str).val() ) break;
                    }
                    selected_name.splice( i,1 );
                }
            }
            var templatedata='';
            for( var i = 0; i < selected_name.length; i++ ) {
                templatedata += selected_name[i] + ',';
            }
            if(selected_name.length == tbl_row_count){
				document.getElementById("check_all").checked = true;
			}
			else{
				document.getElementById("check_all").checked = false;
			}
            templatedata = templatedata.substr( 0, templatedata.length - 1 );
            $('#select_template_data').val( templatedata );
        }
    </script>
    <?
    $template_name_sql=sql_select("select a.template_name from wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$buyer_name and a.is_deleted=0 group by  a.template_name");
	//echo "select distinct a.template_name from wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$buyer_name and a.is_deleted=0 group by  a.template_name";
  //  $template_name_sql=sql_select("select distinct template_name from wo_lib_trim_cost_temp where is_deleted=0 and related_buyer in($buyer_name)");
	//echo "select distinct template_name from wo_lib_trim_cost_temp where is_deleted=0 and related_buyer in($buyer_name)";
	//echo $buyer_name.'SASASAS';;

      ?>
    </head>
    <body>
    <div align="center" style="width:100%;">
    	<div style="width:200px; float: left">
	    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table" align="left">
	    		<tr>
	    			<input id="hidden_template_name" type="hidden" name="hidden_template_name">
	    			<th width="100"><h3>Template Name</h3></th>
	    		</tr>
	    	</table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" width="200" id="template_name_tbl">
	    		<?
	    		$i=0;
	    		foreach ($template_name_sql as $row){
	    		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
	    		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="insert_template_data('<? echo $row[csf("template_name")] ?>',<?echo $i ?>)" id="tempname<? echo $i; ?>">
	    			<td width="100" align="center"><span style="font-size: 14px"><? echo $row[csf("template_name")]; ?></span></td>
	    		</tr>
	    		<? $i++; } ?>

	    	</table>
    	</div>
        <table id="trmplate_data_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <input type="hidden" id="select_template_data" name="select_template_data"/>
            <thead>
                <tr>
                	<th width="50">SL</th>
                    <th width="100">User Code</th>
                    <th width="100">Group</th>
                    <th width="100">Description</th>
                    <th width="100">Brand/Sup Ref.</th>
                    <th width="100">Nominated Supp</th>
                    <th width="70">Cons UOM</th>
                    <th width="80">Apvl Req.</th>
                </tr>
            </thead>
        </table>
        <table id="template_data_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <tbody id="template_date">
            </tbody>
        </table>
        <table width="420" id="check_all_tbl" cellspacing="0" cellpadding="0" style="border:none; display: none; margin-top: 10px" align="center">
        <tr>
            <td align="center" height="30" width="200" valign="bottom">
                <div style="width:300px">
                    <div style="width:150px; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:150px; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    </div>
    </body>
    <script type="text/javascript">
        setFilterGrid("template_name_tbl",-1);
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action == "get_template_data")
{
    $trim_variable=1;
    $trim_variable_sql=sql_select("select trim_rate from  variable_order_tracking where company_name='$data[4]' and variable_list=35 order by id");
    foreach($trim_variable_sql as $trim_variable_row)
    {
        $trim_variable= $trim_variable_row[csf('trim_rate')];
    }
    $lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
    $trim_rate_from_library=return_library_array( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.status_active=1 and    a.is_deleted=0 group by a.id", "id", "rate");
    $supplier_library=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
    $data_array=sql_select("SELECT * from wo_lib_trim_cost_temp where template_name='$data' and status_active=1 and is_deleted=0 order by id");
    $i=1;
    foreach( $data_array as $row )
    {
        $rate=$trim_rate_from_library[$row[csf('trims_group')]];
        $amount=$row[csf('cons_dzn_gmts')]*$trim_rate_from_library[$row[csf('trims_group')]];
        if($rate=="" || $rate==0)
        {
            $rate=$row[csf('purchase_rate')];
            $amount=$row[csf('amount')];
        }
        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        $str="";
        $str=$lib_item_group_arr[$row[csf("trims_group")]].'***'.$row[csf('user_code')].'***'.$row[csf('trims_group')].'***'.$row[csf('cons_uom')].'***'.$row[csf('cons_dzn_gmts')].'***'.$row[csf('purchase_rate')].'***'.$row[csf('amount')].'***'.$row[csf('apvl_req')].'***'.$row[csf('supplyer')].'***'.$row[csf('sup_ref')].'***'.$row[csf('item_description')].'***'.$row[csf('ex_per')].'***'.$row[csf('tot_cons')].'***'.$supplier_library[$row[csf("supplyer")]];
     ?>
        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" class="itemdata" onClick="js_set_value(<?=$i; ?>)" >
        	<td width="50"><?=$i; ?></td>
            <td width="100">
                <?=$row[csf("user_code")]; ?>
                <input type="hidden" name="txttemplatedata_<?=$i; ?>" id="txttemplatedata_<?=$i; ?>" value="<?=$str; ?>"/>
                </td>
            <td width="100"><? echo $lib_item_group_arr[$row[csf("trims_group")]]; ?></td>
            <td width="100"><? echo $row[csf("item_description")];?></td>
            <td width="100"><? echo $row[csf("sup_ref")]; ?></td>
            <td width="100"><? echo $supplier_library[$row[csf("supplyer")]]; ?></td>
            <td width="80"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
            <td width="70"><? echo $yes_no[$row[csf("apvl_req")]]; ?></td>
        </tr>

    <?  $i++; } ?>
    <script type="text/javascript">
        setFilterGrid("template_data_tbl",-1);
    </script>
    <?
    exit();
}

if($action=="openpopup_trimsupplier")
{
	echo load_html_head_contents("Nominated Supplier PopUp","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'supp_table' ).rows.length;
			tbl_row_count = tbl_row_count;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
				//js_set_value( i);
				document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
				if( jQuery.inArray( $('#txttrimsuppdata_' + i).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimsuppdata_' + i).val());
				}
				else{
					for( var j = 0; j < selected_name.length; j++ ) {
						if( selected_name[j] == $('#txttrimsuppdata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}

				}
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					if( jQuery.inArray( $('#txttrimsuppdata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txttrimsuppdata_' + i).val());
					}
					else{
						for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txttrimsuppdata_' + i).val() ) break;
						}
						selected_name.splice( j,1 );
					}
				}
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_name = new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txttrimsuppdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimsuppdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimsuppdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + ',';
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#suppdata').val( trimgroupdata );
		}
		
		function close_supp_data()
		{
			var s=$('#suppdata').val();
			//alert(s)
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="suppdata" name="suppdata"/>
        <?
		$supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.is_deleted=0 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
		?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
                <th>Supplier Name</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="supp_table">
            <tbody>
				<?
                $i=1;
                foreach($supplier_library as $sid=>$sname)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $supplier_library2[$sid].', ';
					if($supplier_chk_arr[$sid]>0)
					{
						$str="";
						$str=$sid.'***'.$sname;
						?>
						<tr id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>);" bgcolor="<?=$bgcolor; ?>" style="cursor:pointer">
							<td width="40"><?=$i; ?></td>
							<td><?=$sname; ?>
								<input type="hidden" name="txttrimsuppdata_<?=$i; ?>" id="txttrimsuppdata_<?=$i; ?>" value="<?=$str; ?>"/>
							</td>
						</tr>
						<?
						$i++;
					}
					if($supplier_chk_arr[$sid]<=0)
					{
						$str="";
						$str=$sid.'***'.$sname;
						?>
						<tr id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>);" bgcolor="<?=$bgcolor; ?>" style="cursor:pointer">
							<td width="40"><?=$i; ?></td>
							<td><?=$sname; ?>
								<input type="hidden" name="txttrimsuppdata_<?=$i; ?>" id="txttrimsuppdata_<?=$i; ?>" value="<?=$str; ?>"/>
							</td>
						</tr>
						<?
						$i++;
					}
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="close_supp_data();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('supp_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}


if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];

		function poportionate_qty(qty)
		{
			var txtwoq=document.getElementById('txtwoq').value;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val();
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),6,0);
				//alert(txtwoq_cal); 
				//hiddenreqqty_
				$('#reqqty_'+i).val(txtwoq_cal);
				//calculate_requirement(i)
			}
			set_sum_value( 'qty_sum', 'reqqty_')
			var j=i-1;
			var qty_sum=document.getElementById('qty_sum').value*1;
			if(qty_sum >txtwoq_qty ){
				$('#reqqty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),6,0))
			}
			else if(qty_sum < txtwoq_qty ){
				$('#reqqty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),6,0))
			}
			else{
				$('#reqqty_'+j).val(number_format_common(txtwoq_cal,6,0));
			}
			set_sum_value( 'qty_sum', 'reqqty_');
			calculate_requirement(j)
		}

		function calculate_requirement(i){
			var cons=(document.getElementById('reqqty_'+i).value)*1;
			//var WastageQty='';
			WastageQty=cons;
			WastageQty= number_format_common( WastageQty, 6, 0) ;
			document.getElementById('reqqty_'+i).value= WastageQty;
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:6,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:6,comma:0,currency:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}

		function copy_value(value,field_id,i)
		{
				//alert(value);
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('gmtsColorID_'+i).value;
			
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val();
		

			for(var j=i; j<=rowCount; j++)
			{
				
				if(field_id=='reqqty_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
						set_sum_value( 'qty_sum', 'reqqty_'  );
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'reqqty_'  );
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('gmtsColorID_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'reqqty_'  );
						}
					}
				}
			
				if(field_id=='rate_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_amount(j)
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('gmtsColorID_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
				}
			}
		}

		function calculate_amount(i){
			var rate=(document.getElementById('rate_'+i).value)*1;
			var woqny=(document.getElementById('reqqty_'+i).value)*1;
			var amount=number_format_common((rate*woqny),5,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate()
		}

		function calculate_avg_rate(){
			var woqty_sum=document.getElementById('qty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
			document.getElementById('rate_sum').value=avg_rate;
		}

		function js_set_value(){
			//var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_break_down="";
			for(var i=1; i<=row_num; i++){
				
				//alert(txtdescription.match(reg))
				
				var gmtItemID=$('#cboGarmentItem_'+i).val();
				if(gmtItemID=='') gmtItemID=0;
				
				var gmtcolorid=$('#gmtsColorID_'+i).val();
				if(gmtcolorid=='') gmtcolorid=0;

				var gmtssizesid=$('#gmtssizesid_'+i).val();
				if(gmtssizesid=='') gmtssizesid=0;
				var reqqty=$('#reqqty_'+i).val();
				if(reqqty=='') reqqty=0;

				var rate=$('#rate_'+i).val();
				if(rate=='') rate=0;

				var amount=$('#amount_'+i).val();
				if(amount=='') amount=0;

			
				var dtlsid=$('#dtlsid_'+i).val();
				if(dtlsid=='') dtlsid=0;
				var sizedtlsid=$('#sizedtlsid_'+i).val()
				if(sizedtlsid=='') sizedtlsid=0;

				var updateid=$('#updateid_'+i).val();
				if(updateid=='') updateid=0;
				var mstupdateid=$('#mstupdateid_'+i).val();
				if(mstupdateid=='') mstupdateid=0;
			
				if(cons_break_down==""){
					cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid+'_'+mstupdateid;
				}
				else{
					cons_break_down+="__"+gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid+'_'+mstupdateid;
				}
			}
			//alert(cons_break_down);
			document.getElementById('cons_break_down').value=cons_break_down;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<?
        extract($_REQUEST);
     
        ?>
        <div align="center" style="width:610px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="610" cellspacing="0" class="rpt_table" align="center" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="10" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="10">
                                    <input type="hidden" id="cons_break_down" name="cons_break_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<? //echo $txtReQty;?>"/>
                                    Cons Qty:<input type="hidden" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtReQty; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$updateidRequiredEmbellishdtl) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($updateidRequiredEmbellishdtl) { echo "checked";} ?>>No Copy
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th  width="150">Gmts. Item</th>
                                <th  width="150">Gmts. Color</th>
                                <th  width="70">Gmts. sizes</th>
                                <th width="70">Qty</th>
                                <th width="70">Rate</th>
                                <th width="">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                       $booking_data=sql_select("select id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount  from sample_develop_embl_color_size where dtls_id in($updateidRequiredEmbellishdtl) and status_active=1 and is_deleted=0");
					// echo "select id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount  from sample_develop_embl_color_size where dtls_id in($updateidRequiredEmbellishdtl) and status_active=1 and is_deleted=0";
                        foreach($booking_data as $row){
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['id']=$row[csf('id')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['qnty']=$row[csf('qnty')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['rate']=$row[csf('rate')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['amount']=$row[csf('amount')];
                        }
						//echo $updateidRequiredEmbellishdtl.'DD';
						$sql="select b.id as sam_dtls_id, b.gmts_item_id, b.sample_color, c.size_id, c.id as size_dtls_id, c.total_qty from sample_development_mst a, sample_development_dtls b, sample_development_size c  where a.id=b.sample_mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form_id=449 and a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and  a.id in($update_id) and b.sample_name in($cboReSampleName) and b.gmts_item_id in($cboReGarmentItem) order by b.id"; 
						 
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
									$i++;
									if($req_data_arr[$row[csf('size_dtls_id')]]['qnty']=="") $req_data_arr[$row[csf('size_dtls_id')]]['qnty']=$row[csf('total_qty')];
								?>
									<tr id="break_1" align="center">
                                        <td><?=$i;?></td>
                                        <td><?=create_drop_down( "cboGarmentItem_".$i, 150, $garments_item,"", 1, "Select Item", $row[csf('gmts_item_id')], "",1); ?></td>
                                        <td>
                                            <input type="text" id="gmtsColor_<? echo $i;?>"  name="gmtsColor_<? echo $i;?>" class="text_boxes" style="width:150px" value="<? echo $color_library[$row[csf('sample_color')]]; ?>"  disabled readonly/>
                                            <input type="hidden" id="gmtsColorID_<? echo $i;?>"  name="gmtsColorID_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('sample_color')]; ?>"  disabled readonly/>
                                         
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_id')]]; ?>" disabled readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:40px" value="<? echo $row[csf('size_id')]; ?>" readonly />
                                        </td>
                                       
                                        <td><input type="hidden" id="hiddenreqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? //echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="reqqty_<? echo $i;?>"  onChange="set_sum_value( 'qty_sum', 'reqqty_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'reqqty_',<? echo $i;?>)"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? //echo $txtwoq_cal; ?>" value="<? echo number_format($req_data_arr[$row[csf('size_dtls_id')]]['qnty'],0,'.','');?>"/>
                                        </td>
                                       
                                        <td>
                                        	<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['rate']; ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:70px" value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['amount']; ?>" readonly>
                                             <input type="hidden" id="dtlsid_<? echo $i;?>"  name="dtlsid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('sam_dtls_id')]; ?>" readonly />
                                             <input type="hidden" id="sizedtlsid_<? echo $i;?>"  name="sizedtlsid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('size_dtls_id')]; ?>" readonly />
                                             <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $update_id; ?>" readonly />
                                              <input type="hidden" id="mstupdateid_<? echo $i;?>"  name="mstupdateid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['id']; ?>" readonly />
                                        </td>
                                       
									</tr>
								<?
								//}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="30">&nbsp;</th>
                               <th width="150">&nbsp;</th>
                                <th width="150">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                                <th width="70"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                                <th width=""><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                               
                            </tr>
                        </tfoot>
                    </table>
                    <table width="610" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
		   $(this).select();
		});
		
		set_sum_value( 'qty_sum', 'reqqty_' );
		//set_sum_value( 'woqty_sum', 'woqny_' );
		set_sum_value( 'amount_sum', 'amount_' );
		//set_sum_value( 'pcs_sum', 'pcs_' );
		calculate_avg_rate();
		//var wo_qty=$('#txtwoq_qty').val()*1;

	//	var wo_qty_sum=$('#qty_sum').val()*1;

		/*if(wo_qty!=wo_qty_sum)
		{
			$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		}*/


	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}
if ($action=="copy_requisition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

  	if ($operation==5)  // Insert Here
  	{
  		$con = connect();
  		if($db_type==0)
  		{
  			mysql_query("BEGIN");
  		}




  		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;
  		if($db_type==0)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where  entry_form_id=449 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}
  		if($db_type==2)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=449 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}


  		$field_array="id,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,is_copy,req_ready_to_approved,copy_from,material_delivery_date,season_buyer_wise,season_year,brand_id";
  		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,449,1,'2',".$txt_requisition_id.",".$txt_material_dlvry_date.",".$cbo_season_name.",".$cbo_season_year.",".$cbo_brand_id.")";
  		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
  		$mst_id=return_field_value("max(id) as id","sample_development_mst","status_active=1 and is_deleted=0","id");
  		$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;



  		$field_array_dtls= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,measurement_chart,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
  		$query_dtls=sql_select("SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,measurement_chart,sample_curency,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id from sample_development_dtls where entry_form_id=449 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");

  		$id_size=return_next_id( "id","sample_development_size", 1 ) ;
		$id_fabric=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;

  		$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";
  		$data_array_dtls="";
  		$data_array_size="";
  		for ($i=0;$i<count($query_dtls);$i++)
  		{
  			if ($data_array_dtls) $data_array_dtls .=",";
  			$data_array_dtls .="(".$id_dtls.",".$mst_id.",".$query_dtls[$i][csf("sample_name")].",".$query_dtls[$i][csf("gmts_item_id")].",'".$query_dtls[$i][csf("smv")]."','".$query_dtls[$i][csf("article_no")]."','".$query_dtls[$i][csf("sample_color")]."','".$query_dtls[$i][csf("sample_prod_qty")]."','".$query_dtls[$i][csf("submission_qty")]."','".$query_dtls[$i][csf("delv_start_date")]."','".$query_dtls[$i][csf("delv_end_date")]."','".$query_dtls[$i][csf("sent_to_buyer_date")]."','".$query_dtls[$i][csf("comments")]."','".$query_dtls[$i][csf("sample_charge")]."','".$query_dtls[$i][csf("measurement_chart")]."','".$query_dtls[$i][csf("sample_curency")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,449,'".$query_dtls[$i][csf("size_data")]."','".$query_dtls[$i][csf("fabric_status")]."','".$query_dtls[$i][csf("acc_status")]."','".$query_dtls[$i][csf("embellishment_status")]."','".$id_fabric."','".$query_dtls[$i][csf("acc_status_id")]."','".$query_dtls[$i][csf("embellishment_status_id")]."')";




  			$ex_data=explode("__",$query_dtls[$i][csf("size_data")]);
  			$countsize=count($ex_data);

  			foreach($ex_data as $size_data)
  			{
  				$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;
  				$ex_size_data=explode("_",$size_data);
  				$size_name=$ex_size_data[0];
  				$bhqty=$ex_size_data[1];
  				$plqty=$ex_size_data[2];
  				$dyqty=$ex_size_data[3];
  				$testqty=$ex_size_data[4];
  				$selfqty=$ex_size_data[5];
  				$totalqty=$ex_size_data[6];

  				if($size_name!="")
  				{
  					if (!in_array($size_name,$new_array_size))
  					{
  						$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","440");
  						$new_array_size[$size_id]=str_replace("'","",$size_name);

  					}
  					else $size_id =  array_search($size_name, $new_array_size);
  				}
  				else $size_id=0;
  				

  				if($data_array_size) $data_array_size .=',';
  				$data_array_size.="(".$id_size.",".$mst_id.",".$id_dtls.",'".$size_id."','".$bhqty."','".$plqty."','".$dyqty."','".$testqty."','".$selfqty."','".$totalqty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
  				$id_size=$id_size+1;
  			}
  			$id_dtls=$id_dtls+1;
  		}
  		 //echo "10** insert into sample_development_size ($field_array_size) values $data_array_size";die;
 			//echo "555**"."INSERT INTO sample_development_size(".$field_array_size.")VALUES ".$data_array_size;
  		$rid_dtls=sql_insert("sample_development_dtls",$field_array_dtls,$data_array_dtls,1);
  		$rid_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);


	    // fabric details entry



  		$field_array_fabric= "id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,determination_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_fabric=sql_select("SELECT id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,determination_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,form_type from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");


  		$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
  		for($i=0;$i<count($query_fabric);$i++)
  		{

  			if ($i!=0) $data_array_fabric .=",";

  			$data_array_fabric .="(".$id_fabric.",".$mst_id.",".$query_fabric[$i][csf("sample_name")].",".$query_fabric[$i][csf("gmts_item_id")].",'".$query_fabric[$i][csf("process_loss_percent")]."','".$query_fabric[$i][csf("grey_fab_qnty")]."','".$query_fabric[$i][csf("delivery_date")]."','".$query_fabric[$i][csf("fabric_source")]."','".$query_fabric[$i][csf("remarks_ra")]."','".$query_fabric[$i][csf("fin_fab_qnty")]."','".$query_fabric[$i][csf("determination_id")]."',".$query_fabric[$i][csf("body_part_id")].",".$query_fabric[$i][csf("fabric_nature_id")].",'".$query_fabric[$i][csf("fabric_description")]."','".$query_fabric[$i][csf("gsm")]."','".$query_fabric[$i][csf("dia")]."','".$query_fabric[$i][csf("color_data")]."',".$query_fabric[$i][csf("color_type_id")].",".$query_fabric[$i][csf("width_dia_id")].",".$query_fabric[$i][csf("uom_id")].",'".$query_fabric[$i][csf("required_dzn")]."','".$query_fabric[$i][csf("required_qty")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1)";
  			$ex_data=explode("-----",$query_fabric[$i][csf("color_data")]);
  			foreach($ex_data as $color_data)
  			{
  				$ex_size_data=explode("_",$color_data);
  				$colorName=$ex_size_data[1];
  				$colorId=$ex_size_data[2];
  				$contrast=$ex_size_data[3];
  				$qnty=$ex_size_data[4];
  				$txtProcessLoss=$ex_size_data[5];
  				$txtGrayFabric=$ex_size_data[6];
  				$fab_color_id=$ex_size_data[7];
  				if($data_array_col !="")  $data_array_col.=",";
 					if ($i!=1) $add_comma .=",";
  				$data_array_col.="(".$idColorTbl.",".$mst_id.",".$id_fabric.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
  				$idColorTbl = $idColorTbl + 1;
  			}
  			$id_fabric=$id_fabric+1;

  		}

  		$rid_fabric=sql_insert("sample_development_fabric_acc",$field_array_fabric,$data_array_fabric,1);
  		$rid_color_rf=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);

		//accessories entry
  		$id_acc=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;


  		$field_array_acc= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_acc=sql_select("select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,form_type  from sample_development_fabric_acc where form_type=2 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		for($i=0;$i<count($query_acc);$i++)
  		{
  			if ($i!=0) $data_array_acc .=",";
  			$data_array_acc .="(".$id_acc.",".$mst_id.",".$query_acc[$i][csf("sample_name_ra")].",".$query_acc[$i][csf("gmts_item_id_ra")].",'".$query_acc[$i][csf("supplier_id")]."','".$query_acc[$i][csf("delivery_date")]."','".$query_acc[$i][csf("fabric_source")]."','".$query_acc[$i][csf("trims_group_ra")]."','".$query_acc[$i][csf("description_ra")]."','".$query_acc[$i][csf("brand_ref_ra")]."',".$query_acc[$i][csf("uom_id_ra")].",'".$query_acc[$i][csf("req_dzn_ra")]."','".$query_acc[$i][csf("req_qty_ra")]."','".$query_acc[$i][csf("remarks_ra")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";

  			$id_acc=$id_acc+1;

  		}
  		$acc_id=sql_insert("sample_development_fabric_acc",$field_array_acc,$data_array_acc,1);


	  //print_r($query_emb);
  		$a=count($query_emb);

		// embellishment entry
  		$id_emb=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;

  		$query_emb=sql_select("select id,sample_mst_id,sample_name_re,gmts_item_id_re,body_part_id,supplier_id,delivery_date,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
	  //print_r($query_emb);
  		$a=count($query_emb);
  		$field_array_emb= "id,sample_mst_id,sample_name_re,gmts_item_id_re,body_part_id,supplier_id,delivery_date,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type";
  		for ($i=0;$i<$a;$i++)
  		{

  			if ($i!=0) $data_array_emb .=",";
  			$data_array_emb .="(".$id_emb.",".$mst_id.",'".$query_emb[$i][csf("sample_name_re")]."','".$query_emb[$i][csf("gmts_item_id_re")]."','".$query_emb[$i][csf("body_part_id")]."','".$query_emb[$i][csf("supplier_id")]."','".$query_emb[$i][csf("delivery_date")]."','".$query_emb[$i][csf("name_re")]."','".$query_emb[$i][csf("type_re")]."','".$query_emb[$i][csf("remarks_re")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3)";

  			$id_emb=$id_emb+1;

  		}

  		$emb_id=sql_insert("sample_development_fabric_acc",$field_array_emb,$data_array_emb,1);
  		//echo "10**$rID && $rid_dtls && $rid_size ";
  		if($db_type==0)
  		{
			//&&  $emb_id && $acc_id &&  $rid_fabric && $rid_color_rf
  			if($rID && $rid_dtls && $rid_size )
  			{
  				mysql_query("COMMIT");
  				echo "0**".$new_system_id[0]."**".$id_mst;
  			}
  			else
  			{
  				mysql_query("ROLLBACK");
  				echo "10**".$id_mst;
  			}
  		}
  		//echo "10**$rID  $rid_dtls $rid_size";
  		else if($db_type==2 || $db_type==1 )
  		{
			//&&  $emb_id && $acc_id && $rid_fabric && $rid_color_rf
  			if($rID && $rid_dtls && $rid_size  )
  			{
  				oci_commit($con);
  				echo "0**".$new_system_id[0]."**".$id_mst;
  			}
  			else
  			{
  				oci_rollback($con);
  				echo "10**".$id_mst;
  			}
  		}
  		disconnect($con);
  		die;
  	}
}

if ($action == 'btn_load_acknowledge') {
	$sql = "";
	$data_array = sql_select($sql);
	echo count($data_array);
	exit();
}

if ($action == 'show_acknowledge')
{
	$sql = "select requisition_number_prefix_num, requisition_number, refusing_cause from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id=449  and refusing_cause is not null order by id desc";
	$data_array = sql_select($sql);

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="90">Req No</th>
			<th>Refusing Cause</th>
		</thead>
	</table><!--onClick='set_form_data("<? //echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('sales_booking_no')]; ?>")' -->
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_cause" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($data_array as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="90"><? echo $row[csf('requisition_number')]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('refusing_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if ($action=="cbo_dealing_merchant_book")
{
	echo create_drop_down( "cbo_dealing_merchant_book", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if($action=="save_update_delete_booking")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here  update here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$txt_bodywashcolor)!="")
		{
			if (!in_array(str_replace("'","",$txt_bodywashcolor),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_bodywashcolor), $color_arr, "lib_color", "id,color_name","430");
				$new_array_color[$color_id]=str_replace("'","",$txt_bodywashcolor);
			}
			else $color_id =  array_search(str_replace("'","",$txt_bodywashcolor), $new_array_color);
		}
		else $color_id=0;

		$booking_no=str_replace("'", "", $txt_booking_no);
		
		if(str_replace("'","",$cbo_sample_stage)!=1)//Non Order [Before Order Place and R&D]
		{
			$flag=1;
			if($booking_no)
			{
				$field_array_up="fabric_source*currency_id*source*buyer_req_no*revised_no*style_desc*exchange_rate*pay_mode*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*body_color_id*updated_by*update_date";
				 $data_array_up ="".$cbo_fabric_source."*".$cbo_currency."*".$cbo_sources."*".$txt_buyer_req_no."*".$txt_revise_no."*".$txt_style_desc."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved_book."*".$cbo_team_leader_book."*".$cbo_dealing_merchant_book."*'".$color_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				 $rID=sql_update("wo_non_ord_samp_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
				//echo "10**AAA $rID";
				 if($rID) $flag=1;else $flag=0;
				 $new_booking_no=$booking_no;
				// echo "10**=select id as mst_id from wo_non_ord_samp_booking_mst where booking_no='$new_booking_no'";die;
				 $sql_book=sql_select("select id as mst_id from wo_non_ord_samp_booking_mst where booking_no='$new_booking_no'");
				foreach($sql_book as $row){
					$id=$row[csf('mst_id')];
				}
			}
			else
			{
				if($db_type==0) $mrryearcond="and YEAR(insert_date)"; if($db_type==2) $mrryearcond="and to_char(insert_date,'YYYY')";
				
				$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 $mrryearcond=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				
				$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
				$field_array="id, booking_type, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, fabric_source, currency_id, source, buyer_req_no, revised_no, exchange_rate, pay_mode, booking_date, supplier_id, attention, ready_to_approved, team_leader, dealing_marchant, body_color_id, inserted_by, insert_date, entry_form_id, style_desc";
				$data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",'3',".$cbo_fabric_source.",".$cbo_currency.",".$cbo_sources.",".$txt_buyer_req_no.",".$txt_revise_no.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_booking_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved_book.",".$cbo_team_leader_book.",".$cbo_dealing_merchant_book.",'".$color_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','440',".$txt_style_desc.")";
				//echo "10** insert into wo_non_ord_samp_booking_mst ($field_array) values $data_array";die;
				$rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$new_booking_no[0];
				$update_prev=1;
			}
			if($flag==1)
			{
				if($booking_no)
				{
					$select_prev=sql_select("SELECT booking_no from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0 and entry_form_id=440 and booking_no='$new_booking_no' and style_id=$update_id ");
	
					if(count($select_prev)>0)
					{
						// echo "10**UPDATE wo_non_ord_samp_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=140 and booking_no='$new_booking_no'  and style_id=$update_id  ";die;
						 $update_prev=execute_query("UPDATE wo_non_ord_samp_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=440 and booking_no='$new_booking_no'  and style_id=$update_id  ");
						 if($flag==1)
						 {
							if($update_prev) $flag=1;else $flag=0;
						 }
					}
				}
	
				$id_dtls=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
				$field_array_dtls= "id, booking_no,booking_mst_id, style_id, sample_type, gmts_item_id, body_part, fabric_source, fabric_description, gsm_weight, dia, color_all_data, color_type_id, dia_width, uom, finish_fabric,rate,amount,dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss, grey_fabric, lib_yarn_count_deter_id, remarks, gmts_color, fabric_color, delivery_date";//wo_non_ord_samp_book_dtls_id
	
				$yarn_deter_id="";
				
				for ($i=1;$i<=$total_row;$i++)
				{

					$cboRfSampleName="cboRfSampleName_".$i;
					$cboRfGarmentItem="cboRfGarmentItem_".$i;
					$cboRfBodyPart="cboRfBodyPart_".$i;
					$cboRfFabricSource="cboRfFabricSource_".$i;
					$txtRfFabricDescription="txtRfFabricDescription_".$i;
					$txtRfGsm="txtRfGsm_".$i;
					$txtRfDia="txtRfDia_".$i;
					$txtRfColor="txtRfColor_".$i;
					$cboRfColorType="cboRfColorType_".$i;
					$cboRfWidthDia="cboRfWidthDia_".$i;
					$cboRfUom="cboRfUom_".$i;
					$txtRfReqQty="txtRfReqQty_".$i;
					$txtRate="txtRate_".$i;
					$txtRfColorAllData="txtRfColorAllData_".$i;
					$required_fab_id="updateidRequiredDtl_".$i;
					$txtProcessLoss="txtProcessLoss_".$i;
					$txtGrayFabric="txtGrayFabric_".$i;
					$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
					$txtRfRemarks="txtRfRemarks_".$i;
					$fabricDelvDate="fabricDelvDate_".$i;
					$cboRfFabricNatureId="cboRfFabricNature_".$i;
					$yarn_deter_id.=$$libyarncountdeterminationid.',';
					$rate=str_replace("'",'',$$txtRate);
					if($rate=='') $rate=0;
					$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
					$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNatureId);
					$fab_greyQty_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
					$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);
					
					if ($i!=1) $data_array_dtls .=",";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					// echo "10**".$$txtRfColorAllData;die;
					foreach($ex_data as $color_data)
					{
						$ex_size_data=explode("_",$color_data);
						$colorName=$ex_size_data[1];
						$colorId=$ex_size_data[2];
						$contrast=$ex_size_data[3];
						$qnty2=$ex_size_data[4];
						$txtProcessLoss2=$ex_size_data[5];
						$txtGrayFabric2=$ex_size_data[6];
						$fab_col_id=$ex_size_data[7];
						if($txtGrayFabric2>0)
						{
							$data_array_dtls .="(".$id_dtls.",'".$new_booking_no."',".$id.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricSource.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",'".$qnty2."',".$rate.",".$txtGrayFabric2*$rate.",".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','440','".$txtProcessLoss2."','".$txtGrayFabric2."',".$$libyarncountdeterminationid.",".$$txtRfRemarks.",'".$colorId."','".$fab_col_id."',".$$fabricDelvDate.")";
		
							$id_dtls=$id_dtls+1;
						}
					}
				}
				$rID_1=sql_insert("wo_non_ord_samp_booking_dtls",$field_array_dtls,$data_array_dtls,0);
				// echo "10** insert into wo_non_ord_samp_booking_dtls ($field_array_dtls) values $data_array_dtls";die;
				if($flag==1)
				{
					if($rID_1) $flag=1;else $flag=0;
				}
				 //For yarn dtls table insert
				$updateId=str_replace("'",'',$update_id);
				$rID_up_yarn=execute_query( "update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)",0);
			//	echo "10**update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)";die;
				if($flag==1)
				{
					if($rID_up_yarn) $flag=1;else $flag=0;
				}
			}
		}
		else if(str_replace("'","",$cbo_sample_stage)==1)//With Order [After Order Place]
		{
			$all_po_sql=sql_select("select a.job_no, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$txt_quotation_id group by a.job_no");
			$job_no=$all_po_sql[0][csf("job_no")];
			$all_po=$all_po_sql[0][csf("id")];
			
			$flag=1;
			if($booking_no)
			{
				$is_approved=0;
				$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
				foreach($sql as $row){
					$is_approved=$row[csf('is_approved')];
				}
				if($is_approved==1){
					echo "approved**".str_replace("'","",$txt_booking_no);
					disconnect($con);die;
				}
				
				$sales_order=0;
				$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
				foreach($sqls as $rows){
					$sales_order=$rows[csf('job_no')];
				}
				if($sales_order){
					echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
					disconnect($con);die;
				}
				if(str_replace("'","",$cbo_pay_mode)==2){
					$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
					if($pi_number){
						echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
						disconnect($con);die;
					}
				}
				 
				$field_array="buyer_id*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*updated_by*update_date";
				$data_array ="".$cbo_buyer_name."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_sources."*".$txt_booking_date."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved_book."*".$cbo_team_leader_book."*".$cbo_dealing_merchant_book."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID=sql_update("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);
				//echo "10**AAA $rID";
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$booking_no;
			}
			else
			{
				if($db_type==0) $mrryearcond="and YEAR(insert_date)"; if($db_type==2) $mrryearcond="and to_char(insert_date,'YYYY')";
				
				$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 $mrryearcond=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				
				$id=return_next_id( "id", "wo_booking_mst", 1);
				$field_array="id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, supplier_id, attention, ready_to_approved, team_leader, dealing_marchant, inserted_by, insert_date, entry_form, is_approved"; 
				
				$data_array ="(".$id.",4,'2','".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",'".trim($job_no)."','$all_po','3',".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_sources.",".$txt_booking_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved_book.",".$cbo_team_leader_book.",".$cbo_dealing_merchant_book.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','440','0')";
				//echo "10** insert into wo_booking_mst ($field_array) values $data_array";die;
		 		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$new_booking_no[0];
				$update_prev=1;
			}
			
			if($flag==1)
			{
				if($booking_no)
				{
					$select_prev=sql_select("SELECT booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 and entry_form_id=440 and booking_no='$new_booking_no' and style_id=$update_id");
	
					if(count($select_prev)>0)
					{
						// echo "10**UPDATE wo_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=440 and booking_no='$new_booking_no'  and style_id=$update_id  ";die;
						 $update_prev=execute_query("UPDATE wo_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=440 and booking_no='$new_booking_no'  and style_id=$update_id  ");
						 if($flag==1)
						 {
							if($update_prev) $flag=1;else $flag=0;
						 }
					}
					 $sql_book=sql_select("select id as mst_id from wo_non_ord_samp_booking_mst where booking_no='$new_booking_no'");
					foreach($sql_book as $row){
						$id=$row[csf('mst_id')];
					}
				}
	
				$id_dtls=return_next_id( "id", "wo_booking_dtls", 1);
				//$field_array_dtls= "id, booking_no, style_id, sample_type, gmts_item_id, body_part, fabric_source, fabric_description, gsm_weight, dia, color_all_data, color_type_id, dia_width, uom, finish_fabric, dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss, grey_fabric, lib_yarn_count_deter_id, remarks, gmts_color, fabric_color, delivery_date";//wo_non_ord_samp_book_dtls_id
				
				$field_array_dtls="id, job_no, booking_mst_id,po_break_down_id, pre_cost_fabric_cost_dtls_id, booking_no, booking_type, is_short, style_id, sample_type, gmt_item, body_part, fabric_source, fabric_description, gsm_weight, dia, color_all_data, color_type, dia_width, uom, gmts_color_id, fabric_color_id, gmts_size, item_size, req_dzn, fin_fab_qnty, dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss_percent, grey_fab_qnty, rate, amount"; 
	
				for ($i=1;$i<=$total_row;$i++)
				{
					$cboRfSampleName="cboRfSampleName_".$i;
					$cboRfGarmentItem="cboRfGarmentItem_".$i;
					$cboRfBodyPart="cboRfBodyPart_".$i;
					$cboRfFabricSource="cboRfFabricSource_".$i;
					$txtRfFabricDescription="txtRfFabricDescription_".$i;
					$txtRfGsm="txtRfGsm_".$i;
					$txtRfDia="txtRfDia_".$i;
					$txtRfColor="txtRfColor_".$i;
					$cboRfColorType="cboRfColorType_".$i;
					
					$cboRfWidthDia="cboRfWidthDia_".$i;
					$cboRfUom="cboRfUom_".$i;
					$txtRfReqQty="txtRfReqQty_".$i;
					$txtRfColorAllData="txtRfColorAllData_".$i;
					$required_fab_id="updateidRequiredDtl_".$i;
					$txtProcessLoss="txtProcessLoss_".$i;
					
					$txtGrayFabric="txtGrayFabric_".$i;
					$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
					$txtRfRemarks="txtRfRemarks_".$i;
					$fabricDelvDate="fabricDelvDate_".$i;
					$cboRfFabricNatureId="cboRfFabricNature_".$i;
					$yarn_deter_id.=$$libyarncountdeterminationid.',';
					
					$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
					$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNatureId);
					$fab_greyQty_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
					$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);
					
					if ($i!=1) $data_array_dtls .=",";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					foreach($ex_data as $color_data)
					{
						$ex_size_data=explode("_",$color_data);
						$colorName=$ex_size_data[1];
						$colorId=$ex_size_data[2];
						$contrast=$ex_size_data[3];
						$qnty2=$ex_size_data[4];
						$txtProcessLoss2=$ex_size_data[5];
						$txtGrayFabric2=$ex_size_data[6];
						$fab_col_id=$ex_size_data[7];
						if($txtGrayFabric2>0)
						{
							//$field_array_dtls="id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, booking_no, booking_type, is_short, style_id, sample_type, gmt_item, body_part, fabric_source, fabric_description, gsm_weight, dia, color_all_data, color_type, dia_width, uom, gmts_color_id, fabric_color_id, gmts_size, item_size, req_dzn, fin_fab_qnty, dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss_percent, grey_fab_qnty, rate, amount"; 
							$data_array_dtls .="(".$id_dtls.",'".$job_no."','".$id."',0,0,'".$new_booking_no."','4','2',".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",0,".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",'".$colorId."','".$fab_col_id."',0,0,'".$qnty2."',0,".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','440','".$txtProcessLoss2."','".$txtGrayFabric2."',0,0)";
							$id_dtls=$id_dtls+1;
						}
					}
				}
				//echo "10** insert into wo_booking_dtls ($field_array_dtls) values $data_array_dtls"; die;
				$rID_1=sql_insert("wo_booking_dtls",$field_array_dtls,$data_array_dtls,0);
				if($flag==1)
				{
					if($rID_1) $flag=1;else $flag=0;
				}
				//echo "10**".$rID.'='.$rID_1.'='.$new_booking_no.'='.$flag; die;
				 //For yarn dtls table insert
				$updateId=str_replace("'",'',$update_id);
				$rID_up_yarn=execute_query( "update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)",0);
			//	echo "10**update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)";die;
				if($flag==1)
				{
					if($rID_up_yarn) $flag=1;else $flag=0;
				}
			}
		}
		// echo "10**".$rID."&&".$rID_1."&&".$rID_in2."&&".$update_prev;die;
		if($db_type==0)
		{
			//if($rID && $rID_1 && $rID_in2 && $update_prev)
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$new_booking_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			//if($rID && $rID_1 && $rID_in2 && $update_prev)
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$new_booking_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_booking_no;
			}
		}
		disconnect($con);
		die;
	}
	exit();
}

if ($action=="populate_booking_data_from_search_popup")
{
	$exdata=explode("__",$data);
	$booking_no=$exdata[0];
	$sampleStage=$exdata[1];
	if($sampleStage!=1)
	{
		$sql= "SELECT booking_no, booking_date, company_id, buyer_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, delivery_date, source, booking_year, is_approved, ready_to_approved, team_leader, dealing_marchant, style_desc, source, revised_no, buyer_req_no, body_color_id from wo_non_ord_samp_booking_mst  where booking_no='$booking_no' and entry_form_id='440' and status_active=1 and is_deleted=0 order by booking_no desc ";
		$requisition_id=return_field_value( "style_id", "wo_non_ord_samp_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0 and entry_form_id=440");
	}
	else if($sampleStage==1)
	{
		 $sql= "SELECT id, booking_no, booking_date, company_id, buyer_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, delivery_date, source, is_approved, ready_to_approved, team_leader, dealing_marchant from wo_booking_mst  where booking_no='$booking_no' and entry_form='440' and status_active=1 and is_deleted=0 order by booking_no desc ";
		$requisition_id=return_field_value( "style_id", "wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0 and entry_form_id=440");
	}
	$requisition_no=return_field_value( "requisition_number", "sample_development_mst","id='$requisition_id' and entry_form_id=449 and status_active=1 and is_deleted=0");

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
 		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant_book', 'div_marchant' );\n";
 		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '2';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved_book').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_team_leader_book').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant_book').value = '".$row[csf("dealing_marchant")]."';\n";

		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('txt_buyer_req_no').value = '".$row[csf("buyer_req_no")]."';\n";
		echo "document.getElementById('txt_bodywashcolor').value = '".$color_arr[$row[csf("body_color_id")]]."';\n";
		echo "document.getElementById('cbo_sources').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('txt_revise_no').value = '".$row[csf("revised_no")]."';\n";
	}
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			  }).end().appendTo("#tbl_termcondi_details");
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			  $('#termscondition_'+i).val("");
		}

	}

	function fn_deletebreak_down_tr(rowNo)
	{


			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

	}

	function fnc_fabric_booking_terms_condition( operation )
	{
		    var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
			}
			var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","sample_requisition_with_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
				}
		}
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=440 and booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array2=sql_select("select id, terms from  lib_terms_condition  where is_default=1 and page_id in(449,140) ");// quotation_id='$data'
					foreach( $data_array2 as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
									?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
</div>
<script type="text/javascript">
	var data_array='<? echo count($data_array) ;?>';
	var permissions='<? echo $permission ;?>';
	if(data_array*1>0)
	{
		set_button_status(1, permissions, 'fnc_fabric_booking_terms_condition',1);
 	}

</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0 || $operation==1 )  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.",440)";
			$id=$id+1;
		 }
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where entry_form=440 and  booking_no =".$txt_booking_no."",0);
		if($operation==0)
		{
			$rID_de3=1;
		}

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3 ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3 ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	exit();

}

if($action=="sample_name_change_popup")
{
	echo load_html_head_contents("Sample Change","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
	<script>

	var sample_mst_id='<? echo $sample_mst_id;?>';

	function fnc_sample_name_change( operation )
	{
			var sample_from=$("#sample_from").val();
			var sample_to=$("#sample_to").val();
 			var data="action=save_update_delete_sample_name_change&operation="+operation+'&sample_from='+sample_from+'&sample_to='+sample_to+'&sample_mst_id='+sample_mst_id;
			//freeze_window(operation);
			http.open("POST","sample_requisition_with_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_name_change_reponse;
	}

	function fnc_sample_name_change_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
				}
		}
	}
    </script>

	</head>

	<body>
		<div align="center" style="width:100%;margin-top:20px;" >
			<? echo load_freeze_divs ("../../../",$permission);  ?>
			<fieldset width="400"  style=" margin-top:20px;">
				<form id="sample_change" autocomplete="off">

					<table width="400" cellspacing="0" class="" border="0" id="" rules="">

						<tbody>
						<tr>
							<td class="must_entry_caption"><strong>Sample From</strong></td>
							<td>
								<?
								$sql="select a.id,a.sample_name  from lib_sample a,lib_buyer_tag_sample b,sample_development_dtls c  where a.id=b.tag_sample and a.id=c.sample_name and c.sample_mst_id ='$sample_mst_id' and  b.buyer_id='$cbo_buyer_name' and a.business_nature=3 and b.sequ  is not null group by a.id,a.sample_name  ";

									$sql_to="select a.id,a.sample_name  from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id='$cbo_buyer_name' and a.business_nature=3 and b.sequ  is not null group by a.id,a.sample_name  ";
									echo create_drop_down( "sample_from", 100, $sql,"id,sample_name", 1, "Select Sample", $selected, "");
								?>
							</td>
							<td class="must_entry_caption"><strong>Sample To</strong></td>
							<td>
								<?

									echo create_drop_down( "sample_to", 100, $sql_to,"id,sample_name", 1, "Select Sample", $selected, "");
								?>
							</td>
						</tr>

						</tbody>
					</table>

					<table width="400" cellspacing="0" class="" border="0">
						<tr>
							<td align="center" height="15" width="100%"> </td>
						</tr>
						<tr>
							<td align="center" width="100%" class="button_container">
								<?
								echo load_submit_buttons( $permission, "fnc_sample_name_change", 1,0 ,"reset_form('sample_change','','','','')",1) ;
								?>
							</td>
						</tr>
					</table>

				</form>
			</fieldset>
		</div>

	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_delete_sample_name_change")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1 )  // update only
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}


		$rID1=execute_query( "update sample_development_dtls set sample_name='$sample_to',prev_sample_id='$sample_from' where sample_mst_id='$sample_mst_id'  and sample_name='$sample_from' and entry_form_id=449 ",0);
		$rID2=execute_query( "update sample_development_fabric_acc set sample_name='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name='$sample_from' and form_type=1   ",0);
		$rID3=execute_query( "update sample_development_fabric_acc set sample_name_ra='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name_ra='$sample_from'  and form_type=2 ",0);
		$rID4=execute_query( "update sample_development_fabric_acc set sample_name_re='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name_re='$sample_from' and form_type=3   ",0);

		$rID5=execute_query( "update wo_non_ord_samp_booking_dtls set sample_type='$sample_to' where style_id='$sample_mst_id'  and sample_type='$sample_from'    ",0);

		$rID6=execute_query( "update sample_ex_factory_dtls set sample_name='$sample_to' where sample_development_id='$sample_mst_id'  and sample_name='$sample_from'    ",0);
		$all_production_mst_id_arr=array();
		$prod_sql="SELECT   id,   sample_development_id from sample_sewing_output_mst Where sample_development_id = '$sample_mst_id' ";
		foreach(sql_select($prod_sql) as $v)
		{
			$all_production_mst_id_arr[$v[csf("id")]]=$v[csf("id")];
		}
		$all_production_mst_ids=implode(",", $all_production_mst_id_arr);
		if(!$all_production_mst_ids)$all_production_mst_ids=0;

		$rID7=execute_query( "update sample_sewing_output_dtls set sample_name='$sample_to' where sample_sewing_output_mst_id in($all_production_mst_ids)  and sample_name='$sample_from'    ",0);
		//echo $rID1.  $rID2. $rID3. $rID4. $rID5. $rID6. $rID7;die;

		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 ){
				mysql_query("COMMIT");
				echo "1**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 ){
				oci_commit($con);
				echo "1**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	exit();
}

if($action=="check_save_update"){
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	if($type==1){
		$sql_data=sql_select("SELECT id from sample_development_dtls where entry_form_id=449 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC");	
	}
	else if($type==2)//Fabric
	{
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 order by id ASC");
	}
	else if($type==6){
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=5 and  is_deleted=0  and status_active=1 and name_re='2'  order by id ASC");
	}
	else if($type==4){
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and  is_deleted=0  and status_active=1 and name_re='3'  order by id ASC");
	}
	else if($type==5){
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=4 and  is_deleted=0  and status_active=1 and name_re='1'  order by id ASC");
	}
	if(count($sql_data)>0){
		echo 1;
	}
	else{
		echo 0;
	}
	

}
if($action=="get_smv_value"){
	$ex_data = explode("**",$data);
	$job_id=$ex_data[0];
	$gmts_item_id=$ex_data[1];
	$order_gmts_item=sql_select("SELECT gmts_item_id, smv_pcs from wo_po_details_mas_set_details where job_id=$job_id and gmts_item_id=$gmts_item_id");
	foreach($order_gmts_item as $row){
		$smv_value=$row[csf('smv_pcs')];
	}
	echo $smv_value;
}
?>
