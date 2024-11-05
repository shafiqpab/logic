<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if($db_type==0) $select_field="group";
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/wash_issue_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}
if($action=="print_button_variable_setting") //Print Button
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=21 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("SELECT service_process_id,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("service_process_id")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=28","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";

	echo "$('#wip_valuation_for_accounts').val(0);\n";
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$data and variable_list=76 and status_active=1 and is_deleted=0");
	echo "$('#wip_valuation_for_accounts').val($wip_valuation_for_accounts);\n";
	if($wip_valuation_for_accounts==1)
	{
		echo "$('#wip_valuation_for_accounts_button').show();\n";
	}
	else
	{
		echo "$('#wip_valuation_for_accounts_button').hide();\n";
	}
 	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if($action=="load_drop_down_embro_issue_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "" );
		}
		else
		{
			echo create_drop_down( "cbo_emb_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/wash_issue_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0 );

	exit();
}
if($action=="load_drop_down_embro_issue_source_new")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "" );
		}
		else
		{
			// $sql ="SELECT A.ID,A.SUPPLIER_NAME FROM LIB_SUPPLIER A, LIB_SUPPLIER_PARTY_TYPE B WHERE A.ID=B.SUPPLIER_ID AND B.PARTY_TYPE=23 AND A.STATUS_ACTIVE=1 GROUP BY A.ID,A.SUPPLIER_NAME ORDER BY A.SUPPLIER_NAME";	

			$sql ="SELECT a.id, a.supplier_name	  FROM lib_supplier a, lib_supplier_party_type b WHERE a.id = b.supplier_id AND b.party_type = 23 AND a.status_active = 1  GROUP BY a.id, a.supplier_name  UNION ALL SELECT c.id, c.supplier_name  FROM lib_supplier c, lib_supplier_party_type b, pro_garments_production_mst a	 WHERE     c.id = b.supplier_id	   AND b.party_type = 23   AND c.id = a.serving_company   AND c.status_active IN (1, 3)  GROUP BY c.id, c.supplier_name  ORDER BY supplier_name";
			
			echo create_drop_down( "cbo_emb_company", 170, "$sql","id,supplier_name", 1, "--Select--", 0, "" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/wash_issue_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0 );

	exit();
}
/*if($action=="load_drop_down_embel_name")
{
	//echo $data;
    echo create_drop_down( "cbo_embel_name", 170, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "
	load_drop_down( 'requires/wash_issue_entry_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_embro_issue_type', 'embro_type_td');
	get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'color_and_size_level', 'requires/wash_issue_entry_controller' );
	show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','setFilterGrid(\'tbl_search\',-1)'); ","",$data );
}*/

if($action=="load_drop_down_embro_issue_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$po_id=$data[1];

	if($db_type==0) $embel_name_cond="group_concat(c.emb_type) as emb_type";
	else if($db_type==2) $embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";
	$embl_type=return_field_value("$embel_name_cond","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id and c.emb_name=3","emb_type");

	if($emb_name==1) $conArr=$emblishment_print_type;
	else if($emb_name==2) $conArr=$emblishment_embroy_type;
	else if($emb_name==3) $conArr=$emblishment_wash_type;
	else if($emb_name==4) $conArr=$emblishment_spwork_type;
	else if($emb_name==5) $conArr=$emblishment_gmts_type;
	else $conArr=$blank_array;

	echo create_drop_down( "cbo_embel_type", 170, $conArr,"", 1, "--- Select Wash ---", $selected, "" ,"","$embl_type");

	exit();
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
			$("#company_search_by").val(<?php echo $_REQUEST['company']; ?>);

        });

		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)" type="text" name="txt_search_common" style="width:142px" class="text_boxes" id="txt_search_common" value="" />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text" onkeydown="getActionOnEnter(event)"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="File no";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				load_drop_down( 'wash_issue_entry_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
			}
		}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,country_ship_date)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_company_id").val(document.getElementById('company_search_by').value);
		$("#country_ship_date").val(country_ship_date);
  		parent.emailwindow.hide();
 	}
</script>
</head>
<body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="780" ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                 <thead>
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="130">Search By</th>
                    <th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                    <th width="130" colspan="2">Shipment Date Range</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 ); ?></td>
                    <td>
                        <?
                        $searchby_arr=array(5=>"Job No",0=>"Order No",1=>"Style Ref.",2=>"Buyer Name",3=>"File No",4=>"Internal Ref");
                        echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                        ?>
                    </td>
                    <td id="search_by_td"><input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                        <input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'wash_issue_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="6">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" id="hidden_grmtItem_id">
                        <input type="hidden" id="hidden_po_qnty">
                        <input type="hidden" id="hidden_country_id">
                         <input type="hidden" id="hidden_company_id">
						 <input type="hidden" id="country_ship_date">
                    </td>
                </tr>
            </table>
            <div style="margin-top:10px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	if($ex_data[4]== 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];	
    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');


	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			$sql_cond = " and b.file_no=trim('$txt_search_common')";
		else if(trim($txt_search_by)==4)
			$sql_cond =  " and b.grouping like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond =  " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	$sql = "SELECT b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.id = b.job_id and a.id = c.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status <>3 and c.emb_name=3 and c.cons_dzn_gmts>0 and a.garments_nature=$garments_nature $sql_cond
			group by b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date ASC";
		/*$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond";*/ //old

 	 /* $sql = "SELECT b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,  wo_pre_cost_dtls d where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond and b.shiping_status <>3 and a.job_no=c.job_no and c.job_no=d.job_no and ( d.embel_cost !=0 or d.wash_cost !=0 ) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 "; */
 	// echo $sql;die();


	$result = sql_select($sql);
	$po_id_arr = array();
	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	if($db_type==0) $countryCond="group_concat(distinct(country_id))";
	else if($db_type==2) $countryCond="listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id)";

	$po_country_arr=return_library_array( "SELECT po_break_down_id, $countryCond as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');


	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,color_number_id,country_ship_date,pack_type from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id,color_number_id,country_ship_date,pack_type");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['po_qnty']+=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		$po_color_arr[$row[csf("po_break_down_id")]].=','.$row[csf("color_number_id")];
	}
	// echo "<pre>";
	// print_r($po_country_data_arr);die;
	
	unset($poCountryData);

	// $total_issu_qty_data_arr=array();
	// $total_issu_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 and embel_name=3 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	// foreach($total_issu_qty_arr as $row)
	// {
	// 	$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	// }
	// unset($total_issu_qty_arr);
	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id,production_type, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type in(2,5) and embel_name in(0,3) and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id,production_type");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('production_type')]]+=$row[csf('production_quantity')];
	}






	?>
    <div style="width:1290px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="50">Job No</th>
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="100">Sewing Output</th>
                <th width="80">Total Issue Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1290px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$color=array_unique(explode(",",$po_color_arr[$row[csf("id")]]));
				$color_name = '';
				foreach ($color as $key => $value)
				{
					if ($color_name !='')
					{
						$color_name .=','.$color_arr[$value];
					}
					else
					{
					 	$color_name = $color_arr[$value];
					}
				}

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$numOfCountry = count($country);

				for($k=0; $k<$numOfItem; $k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
					else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

				foreach($country as $country_id)
				{
					foreach ($po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id] as $coun_ship_date=>$coun_ship_date_data) 
					{ 
						//$country_ship_date = $coun_ship_date_data; 
						foreach ($coun_ship_date_data as $pack_type=>$pack_data) 
						{
								$country_ship_date = $coun_ship_date;
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];

						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<?=$row[csf("id")];?>,'<?=$grmts_item;?>','<?=$po_qnty;?>','<?=$plan_cut_qnty;?>','<?=$country_id;?>','<?=$country_ship_date; ?>');" >
							<td width="30" align="center"><?=$i; ?></td>
							<td width="60" align="center"><?=change_date_format($row[csf("shipment_date")]);?></td>
							<td width="50" align="center"><?=$row[csf("job_no_prefix_num")];?></td>
							<td width="100" title="<?=$color_name? $color_name:""?>" style="word-break: break-all;"><?=$row[csf("po_number")]; ?></td>
							<td width="80" style="word-break: break-all;"><?=$buyer_arr[$row[csf("buyer_name")]]; ?></td>
							<td width="100" style="word-break: break-all;"><?=$row[csf("style_ref_no")]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row[csf("file_no")]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row[csf("grouping")]; ?></td>
							<td width="140" style="word-break: break-all;"><?=$garments_item[$grmts_item];?></td>
							<td width="100" style="word-break: break-all;"><?=$country_library[$country_id]; ?>&nbsp;</td>
							<td width="80" align="right"><?=$po_qnty; ?>&nbsp;</td>
							<td width="100" align="right"><?echo $total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][5]; ?>&nbsp;</td>
                            <td width="80" align="right"><?=$total_cut_qty=$total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][2]; ?>&nbsp;</td>
                            <td width="80" align="right"><?php $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp;</td>
							<td style="word-break: break-all;"><?=$company_arr[$row[csf("company_name")]];?> </td>
						</tr>
						<?
						$i++;
					}
				}
			}
		}
     }
   		?>
        </table>
    </div>
	<?
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, c.emb_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where c.cons_dzn_gmts>0 and a.job_id=b.id and b.id=c.job_id and a.id=$po_id and c.emb_name=3 group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, c.emb_name ");
			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			}

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

  		// $dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=5 THEN b.production_qnty END) as totalcutting, SUM(CASE WHEN a.production_type=2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) as totalprinting from pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and b.is_deleted=0");

		  $dataArray=sql_select("SELECT 
		  SUM (
			  CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END)
			  AS totalcutting,
		  SUM (
			  CASE
				  WHEN a.production_type = 2 AND b.embel_name = '3'
				  THEN
					  a.production_qnty
				  ELSE
					  0
			  END)
			  AS totalprinting
				FROM pro_garments_production_dtls a, pro_garments_production_mst b
				WHERE a.status_active = 1
				and b.status_active=1  and a.is_deleted=0 and  b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.production_type in(5,2) " ); 
				
				// $color_size_qnty_arr=array();
				// foreach($dataArray as $row)
				// {
				// 	$color_size_qnty_arr[$row[csf('color_size_break_down_id')]]['totalcutting']+= $row[csf('totalcutting')];
				// 	$color_size_qnty_arr[$row[csf('color_size_break_down_id')]]['totalprinting']+= $row[csf('totalprinting')];
				// }
					
				// echo"<pre>";print_r($color_size_qnty_arr);die;
			 foreach($dataArray as $row)
			 {	
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_issue').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}
		
  	}
 	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$embelName = $dataArr[4];
	$country_id = $dataArr[5];
    $country_ship_date = $dataArr[6];
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and c.country_ship_date='$country_ship_date'";
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond2=" and a.country_ship_date='$country_ship_date'";
  
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//#############################################################################################//
	//order wise - color level, color and size level

	//$variableSettings=2;

	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name=3 and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
		}
		else
		{
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='3' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.status_active=1
					left join pro_garments_production_mst c on c.id=b.mst_id and c.status_active=1
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
		}
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		$dtlsData =sql_select("SELECT a.color_size_break_down_id,
		SUM (
			CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END)
			AS production_qnty,
		SUM (
			CASE
				WHEN a.production_type = 2 AND b.embel_name = '3'
				THEN
					a.production_qnty
				ELSE
					0
			END)
			AS cur_production_qnty
			  FROM pro_garments_production_dtls a, pro_garments_production_mst b
			  WHERE a.status_active = 1
			  and b.status_active=1  and a.is_deleted=0 and  b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) group by a.color_size_break_down_id" );

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array);

		$sql = "SELECT a.color_order,a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $country_ship_date_cond2 and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";
		$colorResult = sql_select($sql);
	}
	//print_r($sql);

	$colorHTML=""; $colorID=''; $i=0; $totalQnty=0; $chkColor = array();
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
			$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				//$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
		}
		$i++;
	}
	//echo $colorHTML;die;
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[3];
	$type = $dataArr[4];
	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
	ob_start();
	?>
    <div style="width:1000px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
            <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="150">Item Name</th>
	                <th width="120">Country</th>
	                <th width="80">Production Date</th>
	                <th width="80">Production Qty</th>
	                <th width="150">Serving Company</th>
	                <th width="120" >Location</th>
	                <th width="80">Color Type</th>
	                <th width="60">Issue ID</th>
	                <th>Challan No</th>
	             </tr>
            </thead>
        </table>
    </div>
	<div style="width:1000px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000px" class="rpt_table" id="tbl_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("SELECT id, po_break_down_id, item_number_id, company_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, location, challan_no, embel_name, embel_type from pro_garments_production_mst where po_break_down_id='$po_id' and production_type='2' and embel_name=3 and status_active=1 and is_deleted=0 order by id");
			//and embel_name like '$embel_name' and item_number_id='$item_id' and country_id='$country_id' change in 29/10/2019 for libas
			$sql_color_type=sql_select("SELECT a.id, b.color_type_id from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.production_type=2 and a.embel_name=3 and a.production_type=2 and a.po_break_down_id='$po_id' group by a.id, b.color_type_id");
	 		foreach($sql_color_type as $key=>$value)
	 		{
	 			$color_type_arrs[$value[csf("id")]]=$value[csf("color_type_id")];
	 		}
			unset($sql_color_type);

			foreach($sqlResult as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$row[csf('production_quantity')];
 				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"> &nbsp;
                    <input type="checkbox" id="tbl_<?=$i; ?>" onClick="fnc_checkbox_check(<?=$i; ?>);"  />&nbsp;
                   <input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$row[csf('id')]; ?>" style="width:30px"/>
                   <input type="hidden" id="emblname_<?=$i; ?>" name="emblname[]" value="<?=$row[csf('embel_name')]; ?>" />
                   <input type="hidden" id="embltype_<?=$i; ?>" name="embltype[]" value="<?=$row[csf('embel_type')]; ?>" />
                    <input type="hidden" id="productionsource_<?=$i; ?>" value="<?=$row[csf('production_source')]; ?>" />

                    <input type="hidden" id="serving_company_<?=$i; ?>" value="<?=$row[csf('serving_company')]; ?>" />
                    <input type="hidden" id="location_<?=$i; ?>" value="<?=$row[csf('location')]; ?>" />

                    </td>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$garments_item[$row[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$country_library[$row[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=change_date_format($row[csf('production_date')]); ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$row[csf('production_quantity')]; ?></p></td>
                    <?php
                            $source= $row[csf('production_source')];
                            if($source==3) $serving_company= $supplier_arr[$row[csf('serving_company')]];
                            else $serving_company= $company_arr[$row[csf('serving_company')]];
                     ?>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$serving_company; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$location_arr[$row[csf('location')]]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$color_type[$color_type_arrs[$row[csf("id")]]]; ?></p></td>
                    <td width="60" align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$row[csf('id')]; ?></p></td>
                    <td align="center" onClick="fnc_load_from_dtls('<?=$row[csf('id')].'**'.$row[csf('embel_name')]; ?>');"><p><?=$row[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
            <?php
                $i++;
			}
			?>
		</table>
        <script>setFilterGrid("tbl_search",-1); </script>
        </div>
	<?
	exit();
}

if($action=="show_country_listview")
{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table">
        <thead>
           <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Country Ship Date</th>
            <th width="70">Order Qty.</th>
            <th>Issue Qty.</th>
        </thead>
		<?
		$i=1;

		$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=2 and embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
		}
		unset($issue_qnty_arr);

		// $sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
			$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cutting_qnty=0;
			$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<?=$row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')];?>,'<? echo $row[csf('country_ship_date')] ?>' );">
				<td width="20" align="center"><?=$i; ?></td>
				<td width="100"><p><?=$garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><?=$country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="70"><?=$row[csf('order_qnty')]; ?></td>
                <td align="right"><?=$issue_qnty; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_issue_form_data")
{
	$data=explode("**",$data);
	//production type=2 come from array
	$sqlResult =sql_select("SELECT country_ship_date,  id, garments_nature, company_id, challan_no, man_cutt_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, sending_location, sending_company from pro_garments_production_mst where id='".$data[0]."' and production_type='2' and status_active=1 and is_deleted=0 order by id");
    
	$color_type_val=sql_select("SELECT b.color_type_id, b.remarks_dtls from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=2 and b.production_type=2 and a.status_active=1 and b.status_active=1 and a.id='$data[0]' group by b.color_type_id, b.remarks_dtls");
	$country_ship_date = $sqlResult[0][csf('country_ship_date')];
	if($country_ship_date=='') $country_ship_date_cond=""; else $country_ship_date_cond="and a.country_ship_date='$country_ship_date'";
    
	//echo $country_ship_date;die;

  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{
		echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/wash_issue_entry_controller', ".$result[csf('production_source')].", 'load_drop_down_embro_issue_source_new', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#txt_manual_cut_no').val('".$result[csf('man_cutt_no')]."');\n";
		echo "load_drop_down( 'requires/wash_issue_entry_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";

		$location_company=0;
		if($result[csf('production_source')]==1) $location_company=$result[csf('serving_company')];
		else $location_company=$result[csf('company_id')];

	    echo "load_drop_down( 'requires/wash_issue_entry_controller', ".$location_company.", 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/wash_issue_entry_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_location').value  = '".($result[csf("location")])."';\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "$('#cbo_sending_location').val('".$result[csf('sending_location')]."*".$result[csf('sending_company')]."');\n";
		echo "load_drop_down( 'requires/wash_issue_entry_controller', '".$result[csf('embel_name')].'**'.$result[csf('po_break_down_id')]."', 'load_drop_down_embro_issue_type', 'embro_type_td' );\n";
		//$result[csf('po_break_down_id')]
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";

  		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
  		echo "$('#txt_remark_dtls').val('".$color_type_val[0][csf("remarks_dtls")]."');\n";

		// $dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting, SUM(CASE WHEN production_type=2 and embel_name=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");

		$dataArray=sql_select("SELECT 
		  SUM (
			  CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END)
			  AS totalcutting,
		  SUM (
			  CASE
				  WHEN a.production_type = 2 AND b.embel_name = '3'
				  THEN
					  a.production_qnty
				  ELSE
					  0
			  END)
			  AS totalprinting
				FROM pro_garments_production_dtls a, pro_garments_production_mst b
				WHERE a.status_active = 1
				and b.status_active=1  and a.is_deleted=0 and  b.is_deleted=0 and a.mst_id=b.id and po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and a.production_type in(5,2) " ); 
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, size_number_id, color_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id='". $data[0] ."' and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}
			//$variableSettings=2;

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name=3 then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
				}
				else
				{
					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name=3 then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
					sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN a.production_type=2 and b.embel_name=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
					from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2)  group by a.color_size_break_down_id ");
					//echo $dtlsData;die;
				
				foreach($dtlsData as $row) 
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				unset($dtlsData);
				//echo "<pre>";print_r($color_size_qnty_array);

				$sql = "select a.color_order, a.id, a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $country_ship_date_cond and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order, a.size_order";

			}
			else // by default color and size level
			{
				$$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2)  group by a.color_size_break_down_id ");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				unset($dtlsData);
				//print_r($color_size_qnty_array);

				$sql = "select a.color_order, a.id, a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id'  $country_ship_date_cond and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.a.color_order, a.size_order";
			}

 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0; $colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					$totalQnty += $amount;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						//$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					// echo $iss_qnty."<br>"."***".$rcv_qnty;

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" onkeyup="" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();
}

if($action=="chk_next_process_qty")
{
	extract($_REQUEST);
	// $col_size_id = explode("*", str_replace("'", "", $hidden_colorSizeID));
	$sql = "SELECT sum(case when a.production_type=2 and embel_name=3 then b.production_qnty else 0 end) as ISSUE_QTY, sum(case when a.production_type=3 and embel_name=3 then b.production_qnty else 0 end) as RECEIVE_QTY,sum(case when a.production_type=3 and embel_name=3 then b.reject_qty else 0 end) as REJECT_QTY from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and c.color_number_id=$colorId and c.size_number_id=$sizeId and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id=$hidden_po_break_down_id and a.production_type in(2,3) and c.id=b.color_size_break_down_id";
	// echo $sql;
	$sql_res = sql_select($sql);
	echo $sql_res[0]['RECEIVE_QTY']+$sql_res[0]['REJECT_QTY']."****".$sql_res[0]['ISSUE_QTY'];
	die();
}
//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{
			echo "786**Projected PO is not allowed to production. Please check variable settings";
			die();
		}
	}

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=28","is_control");



	/* ======================================================================== /
	/							check variable setting							/
	========================================================================= */
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$cbo_company_name and variable_list=76 and status_active=1 and is_deleted=0");
	if($wip_valuation_for_accounts==1)
	{
		/* ================================= get fabric cost =================================== */

		// $sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.trims_cost_per_pcs from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type=5 and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name order by a.production_type asc";

		$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type=5  and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name";
		// echo "10**".$sql;die;
		$res = sql_select($sql);
		$fab_cost_array = array();
		foreach ($res as $v)
		{
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];

		}


		/* $trims_cost = number_format($trims_cost,$dec_place[3],'.','');
		$finish_oh = $finishing_qty*$cpm*$item_smv;
		$sewing_oh = number_format($sewing_oh,$dec_place[3],'.','');
		$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */
		/* ================================== end fabric cost ========================================= */
	}
	// echo "10**";print_r($fab_cost_array);die;

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );

		$field_array1="id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, total_produced, yet_to_produced, inserted_by, insert_date, sending_location, sending_company, man_cutt_no, entry_form, status_active, is_deleted,country_ship_date";
		$data_array1="(".$id.",".$cbo_company_name.",".$garments_nature.",".$txt_challan.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",3,".$cbo_embel_type.",".$txt_issue_date.",".$txt_issue_qty.",2,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_issue_qty.",".$txt_yet_to_issue.",".$user_id.",'".$pc_date_time."','".$sending_location."','".$sending_company."',".$txt_manual_cut_no.",415,1,0,".$country_ship_date.")";

		// echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
		// echo $data_array."---".$rID;die;
  		// pro_garments_production_dtls table entry here ----------------------------------///

		$dtlsData = sql_select("select a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2)
			group by a.color_size_break_down_id");

		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
		unset($dtlsData);

		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, color_type_id, remarks_dtls, entry_form, status_active, is_deleted,cost_of_fab_per_pcs,cut_oh_per_pcs,trims_cost_per_pcs,cost_per_pcs";
  		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Embellishment Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/
				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0) $data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$cbo_color_type.",".$txt_remark_dtls.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$cbo_color_type.",".$txt_remark_dtls.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf('id')];
			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
 			$rowEx = explode("***",$colorIDvalue);
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;

				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Embellishment Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/
				if($colSizeID_arr[$index]!="")
				{
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
					$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$cbo_color_type.",".$txt_remark_dtls.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$cbo_color_type.",".$txt_remark_dtls.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
		}

		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		else if($db_type==1 || $db_type==2 )
		{

			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];

		// pro_garments_production_mst table data entry here
 		$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date*sending_location*sending_company*man_cutt_no";


		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_issue_qty."*2*".$sewing_production_variable."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$user_id."*'".$pc_date_time."'*'".$sending_location."'*'".$sending_company."'*".$txt_manual_cut_no."";
 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $data_array1;die;

		// echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;

		// pro_garments_production_dtls table data entry here
		$embelName=str_replace("'","",$cbo_embel_name);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2) and b.id<>$txt_mst_id
				group by a.color_size_break_down_id");

			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			unset($dtlsData);

 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, remarks_dtls, color_type_id, entry_form, status_active, is_deleted,cost_of_fab_per_pcs,cut_oh_per_pcs,trims_cost_per_pcs,cost_per_pcs";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				unset($color_sizeID_arr);

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue);
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Embellishment Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
						$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
						$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
						// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];

						$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
						$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

						$cost_per_pcs = $amount/$prod_qty;
						$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$txt_remark_dtls.",".$cbo_color_type.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$txt_remark_dtls.",".$cbo_color_type.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				unset($color_sizeID_arr);

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = explode("***",$colorIDvalue);
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;

					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Embellishment Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					if($colSizeID_arr[$index]!="")
					{
						$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
						$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
						$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
						// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];

						$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
						$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

						$cost_per_pcs = $amount/$prod_qty;
						// echo "10**$amount/$prod_qty";die;
						$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$txt_remark_dtls.",".$cbo_color_type.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$txt_remark_dtls.",".$cbo_color_type.",415,1,0,'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
			}
		}//end cond
		//echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET STATUS_ACTIVE=0,IS_DELETED=1 where mst_id=$txt_mst_id",1);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);//echo $rID;die;


		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			//echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		}
		//echo "10**".$rID.'='.$dtlsrID;die;

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}else{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$sql_chk = sql_select("SELECT sum(production_quantity) as QTY,sum(reject_qnty) as REJ_QTY from pro_garments_production_mst where po_break_down_id=$hidden_po_break_down_id and embel_name=3 and production_type=3 and status_active=1 and is_deleted=0");
		if($sql_chk[0]['QTY']>0)
		{
			echo "99**".$sql_chk[0]['QTY']."**".$sql_chk[0]['REJ_QTY']."**".$hidden_po_break_down_id;
			disconnect($con);
			die();
		}

		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}



if($action=="show_cost_details")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');
	// $sqlResult =sql_select("SELECT b.po_number,a.country_id,a.item_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.trims_cost_per_pcs,a.cost_per_pcs from pro_garments_production_mst a,wo_po_break_down b,lib_country c where b.id=a.po_break_down_id and a.country_id=c.id and a.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=2 and a.embel_name=3");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_mst e, pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where  e.id=a.mst_id and a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and c.po_break_down_id=e.po_break_down_id and c.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=2  and e.embel_name=3 and a.entry_form=415 and a.cost_per_pcs is not null");

	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	$data_array = array();
	$po_id_arr = array();
	$itm_id_arr = array();
	$color_id_arr = array();
	foreach ($sqlResult as $v)
	{
		$po_id_arr[$v['PO_ID']] = $v['PO_ID'];
		$itm_id_arr[$v['ITEM_NUMBER_ID']] = $v['ITEM_NUMBER_ID'];
		$color_id_arr[$v['COLOR_NUMBER_ID']] = $v['COLOR_NUMBER_ID'];

		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fab_rate_per_pcs'] = $v['FAB_RATE_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}
	$poIds = where_con_using_array($po_id_arr,0,"c.po_break_down_id");
	$itmIds = where_con_using_array($itm_id_arr,0,"c.item_number_id");
	$colorIds = where_con_using_array($color_id_arr,0,"c.color_number_id");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and a.status_active=1 and a.is_deleted=0 and a.production_type=5 $poIds $itmIds $colorIds");// and a.embel_name=2
	$trims_rate = array();
	foreach ($sqlResult as $v)
	{
		$trims_rate[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']] = $v['TRIMS_COST_PER_PCS'];
	}

	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="100">PO</th>
				<th width="100">Item</th>
				<th width="100">Color</th>
				<th width="90">Sew Output Rate</th>
				<th width="90">Trims Cost</th>
				<th width="90">Sewing OH</th>
				<th width="90">Cost Per Pcs</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach ($data_array as $po_id=>$po_data)
				{
					foreach ($po_data as $itm_id=>$itm_data)
					{
						foreach ($itm_data as $color_id=>$v)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><?=$v['po_number'];?></td>
								<td><?=$garments_item[$itm_id];?></td>
								<td><?=$lib_color[$color_id];?></td>
								<td align="right"><?=$v['cost_of_fab_per_pcs'];?></td>
								<td align="right"><?=$trims_rate[$po_id][$itm_id][$color_id];?></td>
								<td align="right"><?=$v['cut_oh_per_pcs'];?></td>
								<td align="right"><?=$v['cost_per_pcs'];?></td>
							</tr>
							<?
						}
					}
				}
				?>
			</tbody>
		</table>
	<?

	exit();
}

if($action=="emblishment_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="select id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type,production_date, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=2 and id in($data[1]) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]' and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];


?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3" colspan="2">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Issue ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer :</strong></td><td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty:</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Color Type :</strong></td><td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Embel. Name :</strong></td><td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Type:</strong></td><td><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
            <td><strong>Issue Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
         <br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";

			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
//1st Print End

if($action=="emblishment_without_print") //Start here
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	//$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="select id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=2 and id in($data[1]) and status_active=1 and is_deleted=0 ";
	  $sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in($data[1]) and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$type_ids=array_unique(explode(",", $color_type_id));
	$color_tp="";
	foreach($type_ids as $key=>$val)
	{
		if($color_tp=="")
		{
			$color_tp=$color_type[$val];
		}
		else
		{
			$color_tp .=','.$color_type[$val];
		}

	}


	//echo $sql;
	$dataArray=sql_select($sql);




?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
         <?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" width="200" rowspan="3" colspan="2">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">

        	<td colspan="4" align="center" style="font-size:14px;">
				<b style=" ">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
                </b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong><? //echo $data[2];  ?> Embellishment Delivery Challan</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong><br>
            <b>Location: </b> <? echo $location_library[$dataArray[0][csf('location')]];?>;
            </p></td>
            <td width="125"><strong>Sys.Challan No</strong></td> <td width="175px"><strong>: </strong>&nbsp;<? echo $dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer </strong></td><td width="175px"><strong>: </strong>&nbsp;<? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td><strong>: </strong>&nbsp; <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No </strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty</strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. </strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item </strong></td><td><strong>: </strong>&nbsp;<? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Embel. Name </strong></td><td><strong>: </strong>&nbsp;<? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        </tr>

        <tr>
            <td><strong>Emb. Type</strong></td><td><strong>: </strong>&nbsp;<? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]];  ?></td>
            <td><strong>Challan Date</strong></td><td><strong>: </strong>&nbsp;<? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No</strong></td><td><strong>: </strong>&nbsp; <? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
        	<td><strong>Color Type</strong></td>
        	<td><? echo $color_tp; ?></td>
        </tr>
    </table>
         <br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";

			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Qty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
if($action=="emblishment_issue_print2") // Print 2 Start.
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=2 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$issue_date='';
	foreach($dataArray as $row)
	{
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);
	}

	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id='$mst_id' and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];

//echo $issue_dates=implode(", ",array_unique(explode(", ",$issue_date)));
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>

        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td></td>
        </tr>
        <tr class="form_caption">

        	<td colspan="4" align="center" style="font-size:14px">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p>
        		<p><strong>Location : </strong> <? echo $location_library[$dataArray[0][csf('location')]]; ?></p>
        	</td>
            <td width="125"><strong>Issue ID:</strong></td> <td width="175px"><? echo $mst_id;//$dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer :</strong></td><td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty:</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item :</strong></td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Color Type:</strong></td>
        	<td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Embel. Name :</strong></td><td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        <td><strong>Emb. Type:</strong></td><td><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>

            <!--<td><strong>Issue Date:</strong></td><td><? //echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td><? //echo $dataArray[0][csf('challan_no')]; ?></td>-->
        </tr>
    </table>
         <br>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
			//pro_garments_production_mst c, c.id=a.mst_id and

			$sql="SELECT sum(a.production_qnty) as production_qnty,c.production_date as issue_date,c.challan_no, b.color_number_id from pro_garments_production_mst c,pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.id=a.mst_id and a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,c.production_date,c.challan_no ";

			//echo $sql;
			// and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();$issue_data_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$issue_data_array[$row[csf('color_number_id')]]['issue_date'].=",".$row[csf('issue_date')];
				$issue_data_array[$row[csf('color_number_id')]]['chal_no'].=",".$row[csf('challan_no')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="70">Issue Date</th>
            <th width="70">Chal. No</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
					$issue_date=ltrim($issue_data_array[$cid]['issue_date'],',');
					$challn_no=ltrim($issue_data_array[$cid]['chal_no'],',');
					$date_pro=array_unique(explode(",",$issue_date));
					$all_date='';
					foreach($date_pro as $date_val)
					{
						if($all_date=='') $all_date=change_date_format($date_val);else $all_date.=",".change_date_format($date_val);
					}
					//print_r($date_pro);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $all_date;  ?></td>
                        <td><? echo $challn_no;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}


if($action=="emblishment_issue_print3") // Print 3 Start.
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$country_shortname_arr=return_library_array( "select id, short_name from lib_country", "id", "short_name");
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );


	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_quantity, production_type, remarks, floor_id,inserted_by from pro_garments_production_mst where production_type=2 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	//echo $sql;die;
	$sql_color_type=sql_select("SELECT $sel_col from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$type_ids=array_unique(explode(",", $color_type_id));
	$color_tp="";
	foreach($type_ids as $key=>$val)
	{
		if($color_tp=="")
		{
			$color_tp=$color_type[$val];
		}
		else
		{
			$color_tp .=','.$color_type[$val];
		}

	}

	$dataArray=sql_select($sql);
	$inserted_by=$user_library[$dataArray[0][csf('inserted_by')]];
	$issue_date='';
	//$challan_no = "";
	foreach($dataArray as $row)
	{
		$challan_no = $row['challan_no'];
		$country=$row[csf('country_id')];
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);

		$remarks_all .= $row[csf('remarks')].',';
	}
	
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>

        	<?
            $data_array=sql_select("SELECT image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3" colspan="2">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4"  width="300" align="center" style="font-size:24px;padding-right:300px"><strong><? echo $company_library[$data[0]]; ?></strong> <br>
		    <span style="font-size:18px;padding:5px;color:black;font-weight:bold">Gmts. Issue to Wash</span>
		</td>
        </tr>
        <tr class="form_caption">

        	<td colspan="4" align="center" style="font-size:14px;padding-right:312px;">
				<?
 					$nameArray=sql_select( "select country_id from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
 					//$country=$country_arr[$nameArray[0][csf("country_id")]];

                ?>
            </td>
        </tr>
		
        <tr>
            <td colspan="4" width="300" align="center" style="font-size:14px;padding-right:312px"><u><strong>Challan</strong></u></td>
            <!-- <td colspan="4" width="300" align="center" style="font-size:14px;padding-right:312px"><u><strong>Challan/Gate Pass (<? //echo $country_arr[$country]; ?>) </strong></u></td> -->
        </tr>

        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];
                }
            ?>
        	<td width="100" valign="top" colspan="2">
        		<p><strong>To</strong></p>
        	</td>
        	<td width="125">
        		<strong>Buyer :</strong>
        	</td>
        	<td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        	<td>
        		<strong>Item :</strong>
        	</td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>

        <tr>
        	<td colspan="2">
        		<p><strong><? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]]; //.'<br>'.$address;  ?>
        		</strong></p>
        	</td>
        	<td><strong>Style Ref. :</strong></td>
        	<td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        	<td><strong>Emb. Source:</strong></td>
        	<td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
        </tr>

        <tr>
        	<td colspan="2">
        		<?
        			//echo $dataArray[0][csf('production_source')];
        			if($dataArray[0][csf('production_source')]==1)
        				{
        					$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='".$dataArray[0][csf('serving_company')]."' and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
								if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
								if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
								if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
								if ($result[csf('city')]!="") echo $result[csf('city')];
							}
        				}
        			else if($dataArray[0][csf('production_source')]==3)
        				echo $address;
        				/*echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;*/
        		?>
        	</td>
        	<td> <strong>Job No</strong></td>
        	<td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
        	<td><strong>Emb. Type:</strong></td>
	        <td>
	        	<? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]];
	        	?>
	        </td>
        </tr>

        <tr>
         <td><strong>Location :</strong></td>
         <td style="padding-right: 50px">
         	<? echo $location_arr[$dataArray[0][csf('location')]]; ?>
         </td>
         <td><strong>Order No :</strong></td>
         <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
         <td><strong>Emb.Name :</strong></td>
         <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        </tr>

        <tr>
        	<td><strong>Int. Reff :</strong></td>
        	<td>
        		<?
        			$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$dataArray[0][csf('po_break_down_id')],"grouping");
        			echo $internal_ref;
        		?>
        	</td>
            <td><strong>Order Qty:</strong></td>
            <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
            <td><strong>Color Type:</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
        <tr>
        	 <td><strong>Remarks:</strong></td>
            <td><? echo implode(',',array_unique(explode(',',rtrim($remarks_all,',')))); ?></td>
			<td></td>
			<td></td>
			
        </tr> 
		<?
			$sql="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.country_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by c.color_number_id, c.id";

			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>
     

	   
	<div style="width:100%;"> 


	
	    <table align="right" style="margin-top:20px !important ;" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="60">Issue Date</th>
	            <th width="60">Issue ID</th>
				<th width="60">Challan No.</th>
	            <th width="60">Manual Cut No</th>
	            <th width="120">Remarks</th>
	            <th width="60">Country Short Name</th>

	            <th width="80" align="center">Color/Size</th>
					<?
	                foreach ($size_array as $sizid)
	                {
	                    ?>
	                        <th width="50"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
	                    <?
	                }
	                ?>
	            <th width="80" align="center">Total Issue Qty.</th>
	        </thead>
	        <tbody>
	        	<?
	        	 $sql_prod="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, b.remarks_dtls, a.country_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, b.remarks_dtls, a.country_id, c.color_number_id";
	        		$result_prod=sql_select($sql_prod);
					$i=1;
					$tot_specific_size_qnty=array();
					//$grand_tot_color_size_qty=0;
					foreach ($result_prod as $val)
					{
						$tot_color_size_qty=0;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
						<tr>
	                        <td> <? echo $i;  ?> </td>
	                        <td> <? echo change_date_format($val[csf("production_date")]);  ?> </td>
	                        <td> <?	echo $val[csf("id")]; ?> </td>
							<td> <?	echo $val[csf("challan_no")]; ?> </td>
	                        <td> <? echo $val[csf("man_cutt_no")]; ?> </td>
	                        <td> <? echo $val[csf("remarks_dtls")]?> </td>
	                        <td> <? echo $country_shortname_arr[$val[csf("country_id")]]; ?> </td>
	                        <td> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
	                        <?
	                        foreach ($size_array as $sizval)
	                        {
	                        ?>
	                            <td align="right"><? echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; ?></td>
	                        <?
	                           $tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                           $tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                        }
	                        ?>
	                        <td align="right">
	                        	<?
	                        	echo $tot_color_size_qty;
	                        	?>
	                        </td>
	                     </tr>
	            <?
					$i++;
					}
				?>
	        </tbody>
	        <tr>
	            <td colspan="8" align="right"><strong>Grand Total : &nbsp;</strong></td>
	            <?
					foreach ($size_array as $sizval)
					{
						?>
	                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?></td>
	                    <?
					}
				?>
	            <td align="right"><?php echo array_sum($tot_specific_size_qnty); //$grand_tot_color_size_qty; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="8"> <strong>In Word : </strong> <? echo number_to_words( array_sum($tot_specific_size_qnty), 'Pc\'s' ); ?> </td>
	        </tr>
	    </table>
        <br>
		 <?
            echo signature_table(313, $data[0], "900px","",10,$inserted_by);
         ?>
	</div>
	</div>
<?
exit();
}


if ($action=="load_drop_down_color_type")
{

	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where   a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and   a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$data' and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $key=>$vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}


	if(count(sql_select($sql))>1)
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}
	else
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}


	exit();
}

?>
<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>
