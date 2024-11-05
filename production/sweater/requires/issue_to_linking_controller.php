<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";
$company_credential_cond_comp="";

if ($company_id >0) {
    $company_credential_cond = " and id in($company_id)";
    $company_credential_cond_comp = " and comp.id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

 $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========


$bundle_no_creation=1;//1 means Bundle No Limit , 0 means No limitation
$custom_bundle_num_limit=999;
$custom_bundle_range_limit=9999;

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 210, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data'  $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/issue_to_linking_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 210, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=2 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/issue_to_linking_controller', document.getElementById('cbo_cutting_company').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_table', 'table_td' );",0 );
	exit();
}


if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select cutting_update, production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("cutting_update")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}
if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=28 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	exit();
}



if($action=="load_drop_down_cutt_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_cutting_company", 210, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "fnc_workorder_search(this.value);fnc_company_check(this.value);" );
		}
		else
		{
			echo create_drop_down( "cbo_cutting_company", 210, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "fnc_workorder_search(this.value);fnc_company_check(this.value);" );
		}
	}
	else if($data==1)//$selected_company
		echo create_drop_down( "cbo_cutting_company", 210, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/issue_to_linking_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(this.value);",0,0 );
	else
		echo create_drop_down( "cbo_cutting_company", 210, $blank_array,"", 1, "--- Select ---", $selected, "fnc_company_check(this.value);",0 );
	exit();
}


if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) 
		{
            $("#txt_search_common").focus();
			$("#company_search_by").val('<?=$company; ?>');
        });

		function search_populate(str)
		{
			//alert(str);
         	// onkeydown="if (event.keyCode == 13) document.getElementById('btn_show').click()"
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input 	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)" />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input  type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)"  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref. No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				load_drop_down( 'issue_to_linking_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";				
			}
		}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,job_num,company)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_plancut_qnty").val(plan_qnty);

		$("#hidden_country_id").val(country_id);
		$("#hid_job_num").val(job_num);
		$("#hid_company_id").val(company);
   		parent.emailwindow.hide();
 	}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="990" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
	    		<tr>


	        		<td align="center" width="100%">
	            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	                   		 <thead>
								<tr>
									<th colspan="9"><? 
									//$stringsearchtype = array(1 => "Exact", 2 => "Starts with", 3 => "Ends with", 4 => "Contents");
									echo create_drop_down( "cbostringsearchtype", 130, $string_search_type,'',1,"-Select-", $selected, " ",0 ); ?></th>
								</tr>
	                        	<th width="130" class="must_entry_caption">Company Name</th>
	                        	<th width="130">Search By</th>
	                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
	                        	<th width="250">Date Range</th>
	                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	                    	</thead>
	        				<tr class="general">
                                <td width="130">
                                 <? echo create_drop_down( "company_search_by", 210, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, " ",0 ); ?>
                                </td>
	                    		<td width="130">
									<?
	                                    $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref. No");
	                                    echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
	                                ?>
	                    		</td>
	                   			<td width="180" align="center" id="search_by_td">
									<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
	            				</td>
	                    		<td align="center">
	                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px"> To
						  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
						 		</td>
	            		 		<td align="center">
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbostringsearchtype').value, 'create_po_search_list_view', 'search_div', 'issue_to_linking_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
	             		</table>
	          		</td>
	        	</tr>
	        	<tr>
	            	<td  align="center" valign="middle">
					<?=load_month_buttons(1); ?>
	                    <input type="hidden" id="hidden_mst_id">
	                    <input type="hidden" id="hidden_grmtItem_id">
	                    <input type="hidden" id="hidden_po_qnty">
	                    <input type="hidden" id="hidden_plancut_qnty">
	                    <input type="hidden" id="hidden_country_id">
	                    <input type="hidden" id="hid_job_num">
	                    <input type="hidden" id="hid_company_id">
	          		</td>
	            </tr>
	    	</table>
	        <div style="margin-top:10px" id="search_div"></div>
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

 	$ex_data = explode("_",$data);
 	//echo '<pre>';print_r($data);
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
 	$year = $ex_data[6];
	$search_type =$ex_data[7];
	//print_r ($ex_data);
	$sql_cond="";
	if ($search_type==4 || $search_type==0){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no_prefix_num like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
		}
	}
	else if ($search_type==1){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number ='$txt_search_common'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no ='$txt_search_common'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no_prefix_num='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and c.acc_po_no='$txt_search_common'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no='$txt_search_common'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping='$txt_search_common'";
		}
	}
	else if ($search_type==2){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no_prefix_num like '".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and c.acc_po_no like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '".trim($txt_search_common)."%'";
		}
	}
	else if ($search_type==3){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no_prefix_num like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."'";
		}
	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
    if($year !=0)
	{
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(b.shipment_date)=$year";   }
		if($db_type==2) {$sql_shipment_year_cond=" and to_char(b.shipment_date,'YYYY')=$year";}
	}

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";

	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where
			a.id = b.job_id and
			b.id=c.po_break_down_id and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			c.status_active=1 and
			c.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond $sql_shipment_year_cond $projected_po_cond group by b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity,b.plan_cut, b.t_year order by b.shipment_date desc"; // to_char(b.insert_date,'YYYY')='$year' and 
	}
	else
	{
 		$sql = "SELECT b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where
			a.id = b.job_id and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and			 
			a.garments_nature=$garments_nature
			$sql_cond $sql_shipment_year_cond $projected_po_cond order by b.shipment_date desc"; // to_char(b.insert_date,'YYYY')='$year' and
	}
	// echo $sql;die;
	$result = sql_select($sql);
	$po_id_array = array();
	foreach ($result as $val) 
	{
		$po_id_array[$val['ID']] = $val['ID'];
	}
	$po_id_cond = where_con_using_array($po_id_array,0,"po_break_down_id");

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}*/

	$po_country_data_arr=array(); $pocountry_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_id_cond group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
		$pocountry_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].',';
	}

	$total_cut_data_arr=array();
	$total_cut_qty_arr=sql_select("SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=117 $po_id_cond group by po_break_down_id, item_number_id, country_id");

	foreach($total_cut_qty_arr as $row)
	{
		$total_cut_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}

	?>
    <div style="width:1190px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref</th>
                <th width="120">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Plan Qty</th>
                <th width="80">Total Knit Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1190px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1172" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";$job_num="";
				$job_num=$row[csf("job_no")];
				$company_id=$row[csf("company_name")];
				$pocountry_id=chop($pocountry_arr[$row[csf("id")]],',');
				$country=array_unique(explode(",",$pocountry_id));
				//print_r($country);
				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<? echo$job_num;?>','<? echo $company_id;?>');" >
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
                            <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $plan_cut_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                            <td width="80" align="right"><?php echo $total_cut_qty=$total_cut_data_arr[$row[csf('id')]][$grmts_item][$country_id]; ?>&nbsp;</td>
                            <td width="80" align="right">
                                <?php
                                    $balance=$plan_cut_qnty-$total_cut_qty;
                                    echo $balance;
                                ?>&nbsp;
                            </td>
                            <td><?php echo $company_arr[$row[csf("company_name")]];?></td>
                        </tr>
						<?
						$i++;
					}
				}
            }
   		?>
        </table>
    </div>
	<?
	exit();
}



if($action=="price_rate_list_view")
{
	list($sysid,$buyer,$service_company,$from_date,$to_date,$po_break_down_id,$company_id)=explode("_",$data);

	if($sysid=='')$sysid="a.sys_number like('%%')"; else $sysid="a.sys_number like('%".trim($sysid)."%')";
	if($buyer==0)$buyer="b.buyer_id like('%%')"; else $buyer="b.buyer_id ='$buyer'";

	if($from_date!='' && $to_date!=''){
		if($db_type==0){

			$from_date=change_date_format($from_date);
			$to_date=change_date_format($to_date);
		}
		else
		{
			$from_date=change_date_format($from_date,'','',-1);
			$to_date=change_date_format($to_date,'','',-1);
		}
		$date_con="and a.wo_date BETWEEN '$from_date' and '$to_date'";
	}
	else
	{
		$date_con="";
	}

		$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
		$subcon_po_number_arr = return_library_array("select id, order_no from subcon_ord_dtls  where status_active=1 and is_deleted=0","id","order_no");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
		$sql = "select a.id,a.sys_number,a.currence ,a.exchange_rate,  a.service_provider_id, a.wo_date, a.rate_for,b.order_id,b.order_source,sum(b.avg_rate) as rate,b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and $sysid and $buyer and a.rate_for=20 $date_con   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate, a.service_provider_id, a.wo_date, a.rate_for,b.order_id,b.order_source,b.uom order by a.id";
	//echo $sql;
		$result = sql_select($sql);

		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
	        <thead>
	            <th width="50">SL</th>
	            <th width="130">Sys Number</th>
	            <th>Buyer Order</th>
	            <th width="150">Service Provider</th>
	            <th width="100">WO Rate</th>
	            <th width="112">Rate For</th>
	        </thead>
		</table>
		<div style="width:815px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="797" class="rpt_table" id="tbl_list_search">
	        <?


				$i=1;
	            foreach ($result as $row)
	            {
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";


					if($row[csf('order_source')]==1)
					{
						$po_number_arrs=$po_number_arr;
					}
					else
					{
						$po_number_arrs=$subcon_po_number_arr;
					}
					if($row[csf('uom')]==2)
					{
						$rate=$row[csf('rate')]/12;
					}
					else
					{
						$rate=$row[csf('rate')];
					}
				?>
	                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value('<? echo $row[csf('sys_number')]."_".$row[csf('currence')]."_".$row[csf('exchange_rate')]."_".$rate; ?>')" >
	                    <td width="50" align="center"><? echo $i; ?></td>
	                    <td width="130" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                    <td><p><? echo $po_number_arrs[$row[csf('order_id')]]; ?></p></td>
	                    <td width="150"><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td>
	                    <td width="100" align="center"><p><? echo $rate; ?></p></td>
	                    <td width="90" align="center"><? echo $rate_for[$row[csf('rate_for')]]; ?></td>
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


if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];

	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
 		echo "$('#txt_cutting_qty').attr('placeholder','');\n";//initialize quatity input field

		//$set_qty = return_field_value("set_item_ratio","wo_po_details_mas_set_details","job_no='".$result[csf('job_no')]."' and gmts_item_id='$item_id'");
		$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0");

		$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=117 and is_deleted=0");

		echo "$('#txt_cumul_cutting').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_cutting').val('".$total_produced."');\n";
		$yet_to_produced = $plan_cut_qnty - $total_produced;
		echo "$('#txt_yet_cut').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_cut').val('".$yet_to_produced."');\n";
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
		$country_id = $dataArr[4];
		$job_num = $dataArr[5];
		// $variableSettingsRej = $dataArr[6];

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
		foreach(sql_select($sqls_col_size) as $key=>$value)
		{
			$po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
		}


		//#############################################################################################//
		// order wise - color level, color and size level


		//echo "logic123_".$variableSettings;

		if( $variableSettings==2 ) // color level
		{
			
			$dtlsData = ("SELECT c.item_number_id,c.color_number_id,
										(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
										(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
										from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117) and b.po_break_down_id=c.po_break_down_id and c.id=a.color_size_break_down_id");
							//echo $dtlsData;die;
			foreach(sql_select($dtlsData ) as $row)
			{
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
			}
			//echo "<pre>" ;print_r($color_size_qnty_array);die;
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty
				from wo_po_color_size_breakdown a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
				

			
			//echo $sql;
		}
		else if( $variableSettings==3 ) //color and size level
		{


			$dtlsData = ("SELECT a.color_size_break_down_id,c.item_number_id,c.color_number_id,
										(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
										(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
										from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id   and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117)");

							//echo $dtlsData;die;
			foreach(sql_select($dtlsData) as $row)
			{
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
			}
			//  echo "<pre>" ;print_r($color_size_qnty_array);die;

			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order"; //color_number_id, id
			// echo $sql;die;
		


		}
		else // by default color and size level
		{

			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
										(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117)");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
			}

			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
			// echo $sql;die;
		}
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
		
		$colorResult = sql_select($sql);
		//echo "<pre>" ;print_r($colorResult);die;
  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{

				$link_issue = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['link_issue'];
				$dis_qnty = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['dis_qnty'];

				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Qty"'.($dis_qnty - $link_issue ).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeKint" id="txtColSizeKint_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Kint.QTY") '.$disable.'"></td></tr>';
				$totalQnty += ($kint_qnty - $dis_qnty);
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];
					//echo $chkColor;
				}
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$link_issue = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['link_issue'];
				$dis_qnty = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['dis_qnty'];
				// echo $kint_qnty;
				// echo "<pre>" ;print_r($color_size_qnty_array);die;


				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($dis_qnty - $link_issue ).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Kint. Qty"'.$disable.'></td></tr>';
				//echo $colorHTML;
			}

			$i++;
		}
		// echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Knit Qty</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();
}

if($action=="show_dtls_listview")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	//echo $data;die;

	$lib_supplier = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$lib_company = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$lib_location = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$lib_floor = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$lib_table = return_library_array("select id, table_name from lib_table_entry", 'id', 'table_name');
	?>
	<style type="text/css">
		table tr td{ word-wrap: break-word;word-break: break-all; }
	</style>
		<div style="width:100%;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" align="left">
	            <thead>
	                <th width="20">SL</th>
	                <th width="150" align="center">Item Name</th>
	                <th width="100" align="center">Country</th>
	                <th width="70" align="center">Production . Date</th>
	                <th width="60" align="center">Production . Qnty</th>	               
	                <th width="60" align="center">Reporting Hour</th>
	                <th width="120" align="center">Serving Company</th>
	                <th width="100" align="center">Location</th>
	                <th width="100" align="center">Floor</th>
	                
	            </thead>
			</table>
		</div>
		<div style="width:1140px;max-height:250px; overflow-y:auto; overflow-x: auto;" id="sewing_production_list_view" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="tbl_list_search" align="left">
			<?php
				$i=1;
				$total_production_qnty=0;
		 		foreach($sql_color_type as $key=>$value)
		 		{
		 			$color_type_arrs[$value[csf("id")]]=$value[csf("color_type_id")];
		 		}

				$sqlResult =("SELECT a.id,a.po_break_down_id,a.item_number_id,a.production_date,a.production_quantity,a.production_source,TO_CHAR(a.production_hour,'HH24:MI') as production_hour,a.serving_company,a.location,a.floor_id,a.country_id, min(b.id) as color_id from pro_garments_production_mst a,wo_po_color_size_breakdown b where a.po_break_down_id='$po_id' and a.po_break_down_id=b.po_break_down_id and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='117' and a.status_active=1 and a.is_deleted=0 group by a.id,a.po_break_down_id,a.item_number_id, a.production_date, a.production_quantity,a.production_source,a.production_hour,a.serving_company,a.location,a.floor_id,a.country_id order by a.production_date");
					//echo $sqlResult;die;
				

				foreach(sql_select($sqlResult) as $selectResult){

					if ($i%2==0)
	                	$bgcolor="#E9F3FF";
	                else
	               	 	$bgcolor="#FFFFFF";
	 				$total_production_qnty+=$selectResult[csf('production_quantity')];
					$tot_data=$selectResult[csf('id')].'_'.$selectResult[csf('color_id')];
					
					$fnc_var="get_php_form_data('".$tot_data."','populate_cutting_form_data','requires/issue_to_linking_controller');";
			?>

				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  >
					<td width="20" align="center">
						<? //echo $i; ?>
						<input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />	
						<input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" style="width:30px"/>
					</td>
	                <td width="150" onClick="<? echo $fnc_var;?>" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
	                <td width="70" onClick="<? echo $fnc_var;?>" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
	                <td width="60" onClick="<? echo $fnc_var;?>" align="right"><?php  echo $selectResult[csf('production_quantity')]; ?></td>

	                <td width="60" onClick="<? echo $fnc_var;?>" align="center"><?php echo $selectResult[csf('production_hour')]; ?></td>
					<?php
	                       $source= $selectResult[csf('production_source')];
						   if($source==3)
							{
								$lib_supplier[$selectResult[csf('serving_company')]];
							}
							else
							{
								$lib_company[$selectResult[csf('serving_company')]];
							}
	                ?>
	                <td width="120" onClick="<? echo $fnc_var;?>" align="left"><p><?php echo $lib_company[$selectResult[csf('serving_company')]]; ?></p></td>
	               
	                <td width="100" onClick="<? echo $fnc_var;?>" align="left"><? echo $lib_location[$selectResult[csf('location')]]; ?></td>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="left"><? echo $lib_floor[$selectResult[csf('floor_id')]]; ?></td>
                     
				</tr>
				<?php
				$i++;
				}
				?>
	           
			</table>
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
            <th width="60">Shipment Date</th>
            <th width="70">Plan Knit Qty.</th>
            <th>Knitting Qty.</th>
        </thead>
		<?
		$i=1;
		$cutting_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=117 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$cutting_data_arr=array();
		foreach($cutting_qnty_arr as $row)
		{
			$cutting_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
		}
		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cutting_qnty=0;
			$cutting_qnty=$cutting_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
				<td width="20" align="center"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="70"><?  echo $row[csf('plan_cut_qnty')]; ?></td>
                <td align="right"><?  echo $cutting_qnty; ?></td>

			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_cutting_form_data")
{
	//extract($_REQUEST);

	$data=explode('_',$data);
	$size_color_break_id=explode(",",$data[1]);
	$production_hour_cond=($db_type==0)? " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour " : " TO_CHAR(production_hour,'HH24:MI') as production_hour ";

	$sqlResult =("SELECT id,company_id, po_break_down_id, item_number_id, challan_no, production_source, produced_by, production_date, production_quantity, $production_hour_cond , entry_break_down_type, break_down_type_rej, serving_company, location, floor_id, reject_qnty,cut_no, used_qty_kg, reject_qty_kg, reject_qty_kg_break_down, remarks, total_produced, yet_to_produced, country_id,wo_order_id,currency_id,exchange_rate,rate, table_no from pro_garments_production_mst where id='$data[0]' and production_type='117' and status_active=1 and is_deleted=0 order by id");

	$po_id=$sqlResult[0][csf("po_break_down_id")];
	$color_type_val=sql_select("SELECT b.color_type_id  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=117 and b.production_type=117 and a.status_active=1 and b.status_active=1 and  a.id='$data[0]' group by b.color_type_id ");
    $sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
	foreach(sql_select($sqls_col_size) as $key=>$value)
	{
		$po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
	}

  	foreach(sql_select($sqlResult) as $result)
	{
		echo "$('#txt_cutting_date').val('".change_date_format($result[csf('production_date')])."');\n";


		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/issue_to_linking_controller', ".$result[csf('production_source')].", 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
		echo "$('#cbo_cutting_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/issue_to_linking_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/issue_to_linking_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "load_drop_down( 'requires/issue_to_linking_controller', '".$result[csf('serving_company')]."_".$result[csf('location')]."_".$result[csf('floor_id')]."');\n";
		//echo "$('#cbo_table').val('".$result[csf('table_no')]."');\n";

		// echo "load_drop_down( 'requires/issue_to_linking_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		// echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";



		if($result[csf('production_source')]==3)
		{
			echo "load_drop_down( 'requires/issue_to_linking_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."');\n";

			//load_drop_down( 'requires/issue_to_linking_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );

			//echo "$('#cbo_work_order').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#hidden_currency_id').val('".$result[csf('currency_id')]."');\n";
			echo "$('#hidden_exchange_rate').val('".$result[csf('exchange_rate')]."');\n";
			echo "$('#hidden_piece_rate').val('".$result[csf('rate')]."');\n";
				$rate_string=$result[csf('rate')]." ".$currency[$result[csf('currency_id')]];
			if(trim($rate_string)!="")
			{
				$rate_string="Work Order Rate ".$rate_string." /Pcs";
				echo "$('#workorder_rate_td').text('".$rate_string."');\n";
			}
			else
			{
				echo "$('#workorder_rate_td').text('');\n";
			}
		}

	//	echo "$('#cbo_produced_type').val('".$result[csf('produced_by')]."');\n";
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
		//echo "$('#cbo_time').val('".$time."');\n";
		echo "$('#txt_challan_no').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_cutting_qty').attr('placeholder','".$result[csf('production_quantity')]."');\n";
 		echo "$('#txt_cutting_qty').val('".$result[csf('production_quantity')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0");

		$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and production_type=117 and is_deleted=0");

		echo "$('#txt_cumul_cutting').val('".$total_produced."');\n";
		$yet_to_produced = $plan_cut_qnty - $total_produced;
		echo "$('#txt_yet_cut').val('".$yet_to_produced."');\n";
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_cutting_update_entry',1);\n";

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');



		$variableSettings = $result[csf('entry_break_down_type')];
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
		foreach(sql_select($sqls_col_size) as $key=>$value)
		{
			$po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
		}


		
		if( $variableSettings!=1 ) // gross level
				{
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];

					if( $variableSettings==2 ) // color level
					{
						
						$dtlsData = ("SELECT c.item_number_id,c.color_number_id,
														(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
														(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
													from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117) and b.po_break_down_id=c.po_break_down_id and c.id=a.color_size_break_down_id");
										//echo $dtlsData;die;
						foreach(sql_select($dtlsData ) as $row)
						{
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
						}
						//echo "<pre>" ;print_r($color_size_qnty_array);die;
						$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty
							from wo_po_color_size_breakdown a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
							

						
						//echo $sql;
					}
					else if( $variableSettings==3 ) //color and size level
					{


						$dtlsData = ("SELECT a.color_size_break_down_id,c.item_number_id,c.color_number_id,
														(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
														(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
													from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id   and c.id=a.color_size_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117)");

										//echo $dtlsData;die;
						foreach(sql_select($dtlsData) as $row)
						{
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
						}
						//  echo "<pre>" ;print_r($color_size_qnty_array);die;

						$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order"; //color_number_id, id
						// echo $sql;die;
					


					}
					else // by default color and size level
					{

						$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												(CASE WHEN a.production_type=116 then a.production_qnty ELSE 0 END) as dis_qnty,
												(CASE WHEN a.production_type=117 then a.production_qnty ELSE 0 END) as link_issue
													from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,117)");

						foreach($dtlsData as $row)
						{
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['link_issue']+= $row[csf('link_issue')];
							$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['dis_qnty']+= $row[csf('dis_qnty')];
						}

						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
						// echo $sql;die;
					}
				if($variableSettingsRej!=1)
				{
					$disable="";
				}
				else
				{
					$disable="disabled";
				}
				
				$colorResult = sql_select($sql);
				//echo "<pre>" ;print_r($colorResult);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;
				foreach($colorResult as $color)
				{
					if( $variableSettings==2 ) // color level
					{
						$link_issue = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['link_issue'];
						$dis_qnty = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['dis_qnty'];

						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Qty"'.( $dis_qnty - $link_issue).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeKint" id="txtColSizeKint_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Kint.QTY") '.$disable.'"></td></tr>';
						$totalQnty += ( $dis_qnty - $link_issue);
						$colorID .= $color[csf("color_number_id")].",";
					}
					else //color and size level
					{
						if( !in_array( $color[csf("color_number_id")], $chkColor ) )
						{
							if( $i!=0 ) $colorHTML .= "</table></div>";
							$i=0;
							
							$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
							$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
							$chkColor[] = $color[csf("color_number_id")];
							//echo $chkColor;
						}
						//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
						$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
						$link_issue = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['link_issue'];
						$dis_qnty = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['dis_qnty'];
						// echo $kint_qnty;
						// echo "<pre>" ;print_r($color_size_qnty_array);die;


						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.( $dis_qnty - $link_issue).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Kint. Qty"'.$disable.'></td></tr>';
						//echo $colorHTML;
					}

					$i++;
				}
				// echo $colorHTML;die;
				if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Knit Qty</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
				echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
				$colorList = substr($colorID,0,-1);
				echo "$('#hidden_colorSizeID').val('".$colorList."');\n";

		
		//#############################################################################################//
		}
	}
	exit();
}






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
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	$production_date_sql=sql_select("SELECT min(production_date) as PRODUCTION_DATE from pro_garments_production_mst where status_active=1 and is_deleted=0 and po_break_down_id=$hidden_po_break_down_id ");

	$cutting_date=str_replace("'","",$txt_cutting_date);
	if(count($production_date_sql)>0)
	{
		if(strtotime($production_date_sql[0]['PRODUCTION_DATE'])>strtotime($cutting_date))
		{
			echo "120**";die;
		}
	}

	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		

 		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

		

		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		$field_array2="id, garments_nature, company_id, challan_no, po_break_down_id,item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type,entry_break_down_type,production_hour, remarks, floor_id,total_produced,yet_to_produced,currency_id,exchange_rate,rate,amount, inserted_by, insert_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_cutting_qty);}
		else {$amount="";}
		if($db_type==0)
		{
			$data_array2="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.", ".$cbo_item_name.", ".$cbo_country_name.", ".$cbo_source.",".$cbo_cutting_company.",".$cbo_location.",".$txt_cutting_date.",".$txt_cutting_qty.",117,".$sewing_production_variable.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_cutting.",".$txt_yet_cut.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array2="INSERT INTO pro_garments_production_mst (".$field_array2.") VALUES(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.", ".$cbo_item_name.", ".$cbo_country_name.", ".$cbo_source.",".$cbo_cutting_company.",".$cbo_location.",".$txt_cutting_date.",".$txt_cutting_qty.",117,".$sewing_production_variable.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_cutting.",".$txt_yet_cut.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";

		}


		// pro_garments_production_dtls table entry here ----------------------------------------------//
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
 			$rowEx = array_filter(explode("***",$colorIDvalue));
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				//1 means cutting update
		
				if($j==0)$data_array = "(".$dtls_id.",".$id.",117,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1].")";
				else $data_array .= ",(".$dtls_id.",".$id.",117,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1].")";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$rowEx = array_filter(explode("***",$colorIDvalue));
			$colorAndSizeAndValue_arr = array();
			$color_all_id = array();
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
 				$color_all_id[$colorAndSizeAndValue_arr[1]] = $colorAndSizeAndValue_arr[1];
			}
		   $color_all=implode(",",$color_all_id);

			$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and color_number_id in($color_all)  order by color_number_id,size_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			
 			$rowEx = array_filter(explode("***",$colorIDvalue));
	
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
 				//1 means cutting update
 				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

				 if($j==0)$data_array = "(".$dtls_id.",".$id.",117,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				 else $data_array .= ",(".$dtls_id.",".$id.",117,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				 $j++;
			}

		}
		 //echo "10**".$data_array2;die;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{  
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			
		} 
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			
				if($db_type==2)
				{
					$rID=execute_query($data_array2);
					
				}
				else
				{
					$rID=sql_insert("pro_garments_production_mst",$field_array2,$data_array2,1);
					
				}

			
		}
		else
		{
			if($db_type==2)
				{
					$rID=execute_query("pro_garments_production_mst",$data_array2,1);
				}
				else
				{
					$rID=sql_insert("pro_garments_production_mst",$field_array2,$data_array2,1);
				}
				
		}

			//echo"10**". $data_array2;die;
			
		// echo "10** insert into pro_garments_production_mst (".$data_array2.") values ".$data_array2;die;
		// echo "10** insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
		
		// echo "10**".$rID ."&&". $dtlsrID;die;
		/*echo $data_array2;die;*/
		//check_table_status( $_SESSION['menu_id'],0 );

		// echo $rID;die;

	
		if($db_type==1 || $db_type==2 )
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
	
  	else if ($operation==1) // Update Here------------------------------------------------------
	{
		$con = connect();
		

		// var_dump(expression)
		//table lock here
		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}


		// pro_garments_production_mst table data entry here
		$field_array1="company_id*challan_no*production_source*serving_company*location*production_date*production_quantity*production_type*entry_break_down_type*production_hour*remarks*floor_id*total_produced*yet_to_produced*currency_id*exchange_rate*rate*amount*updated_by*update_date";

		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_cutting_qty);}
		else {$amount="";}
		$data_array1="".$cbo_company_name."*".$txt_challan_no."*".$cbo_source."*".$cbo_cutting_company."*".$cbo_location."*".$txt_cutting_date."*".$txt_cutting_qty."*117*".$sewing_production_variable."*".$txt_reporting_hour."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_cutting."*".$txt_yet_cut."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'";

	

 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);

		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			//delete details table data
			$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				// // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 				$rowEx = array_filter(explode("***",$colorIDvalue));
				$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//1 means cutting update
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",117,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",117,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}



			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				// check next process production
				$sql_prod = "SELECT a.production_type,b.color_size_break_down_id as colSizeId, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.production_type in(117) and a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name group by a.production_type,b.color_size_break_down_id";
				// echo "10**$sql_prod";die();
				$prod_qty_array = array();
				foreach (sql_select($sql_prod) as $val) 
				{
					$prod_qty_array[$val[csf('colSizeId')]][$val[csf('production_type')]] = $val[csf('production_qnty')];
				}

				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by color_number_id,size_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			
				$rowEx = array_filter(explode("***",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;	
					$cur_value = $prod_qty_array[$index][1]+$colorSizeValue;
					// CUTTING QTY IS NOT LESS THAN SEWING IN QTY				
					if($cur_value < $prod_qty_array[$index][4])
					{
						echo "168**";disconnect($con);die();
					}
					

					//1 means cutting update
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",117,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",117,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			//echo "10**".$data_array;die;
			// echo $data_array;die;
			//details table data insert here
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);

		}//end cond

		$dtlsrID=true;

		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);

		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
	//echo "10**".$rID."**".$dtlsrID;die;

		

		//check_table_status( $_SESSION['menu_id'],0);

		
		if($db_type==2 || $db_type==1 )
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
					echo "10**".str_replace("'","",$hidden_po_break_down_id).'*'.$rID .'*'. $dtlsrID ;
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
	
		$next_process_sql=sql_select("SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type <> 117 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name");
		$next_process_sql2=sql_select("SELECT po_break_down_id from pro_cut_delivery_order_dtls where status_active=1 and is_deleted=0 and production_type <> 117 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name");
		if(count($next_process_sql)>0 || count($next_process_sql2)>0)
		{
			echo "167**";
			disconnect($con);
			die;

		}


		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$user_id."*'".$pc_date_time."'";
		$field_array_dtls="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls="0*1*".$user_id."*'".$pc_date_time."'";
		$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
 		$rID_dtls=execute_query("UPDATE pro_garments_production_dtls set status_active=0,is_deleted=1 where mst_id=$txt_mst_id");

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtls)
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
?>
<script type="text/javascript">
	function getActionOnEnter(event){
		if (event.keyCode == 13){
			document.getElementById('btn_show').click();
		}
	}
</script>
