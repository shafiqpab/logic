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
	echo create_drop_down( "cbo_location", 210, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data'  $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cutting_update_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 210, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=2 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/cutting_update_controller', document.getElementById('cbo_cutting_company').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_table', 'table_td' );",0 );
	exit();
}

if ($action=="load_drop_down_table")
{
	$data=explode('_', $data);
	$company = $data[0];
	$location = $data[1];
	$floor = $data[2];
	echo create_drop_down( 'cbo_table', 210, "select id, table_name from lib_table_entry where status_active=1 and is_deleted=0 and company_name=$company and location_name=$location and floor_name=$floor and table_type=1 order by table_name", "id,table_name", 1, "-- Select Table --", $selected, '', 0 );
	exit();
}

if ($action=="load_variable_settings")
{
	echo "setFieldLevelAccess($data);\n";
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

if ($action=="load_variable_settings_reject")
{
	echo "$('#cutting_production_variable_reject').val(0);\n";
	$sql_result = sql_select("select cutting_update from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#cutting_production_variable_reject').val(".$result[csf("cutting_update")].");\n";
		echo "$('#txt_reject_qty').removeAttr('readonly','readonly');\n";
		if($result[csf("cutting_update")]==3) //Color and Size
		{
				echo "$('#txt_reject_qty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qty').removeAttr('readonly','readonly');\n";
		}
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
		echo create_drop_down( "cbo_cutting_company", 210, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/cutting_update_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(this.value);",0,0 );
	else
		echo create_drop_down( "cbo_cutting_company", 210, $blank_array,"", 1, "--- Select ---", $selected, "fnc_company_check(this.value);",0 );
	exit();
}


if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);

	$sql = "select a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=20 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id";
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 210, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
	exit();
}
if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[2];
	$company_id=$data[0];
	$suppplier=$data[1];
	$sql = sql_select("select a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[3]." and a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=20   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom order by a.id");
	//echo $sql;
	if($sql[0][csf('uom')]==2)
	{
		$rate=$sql[0][csf('rate')]/12;
	}
	else
	{
		$rate=$sql[0][csf('rate')];
	}
	echo "$('#workorder_rate_td').text('');\n";
	echo "$('#hidden_currency_id').val('".$sql[0][csf('currence')]."');\n";
	echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
	echo "$('#hidden_piece_rate').val('".$rate."');\n";
	$rate_string='';
	$rate_string=$rate." ".$currency[$sql[0][csf('currence')]];
	if(trim($rate_string)!="")
	{
		$rate_string="Work Order Rate ".$rate_string." /Pcs";
		echo "$('#workorder_rate_td').text('".$rate_string."');\n";
	}
	//echo "$('#workorder_rate_td').text('".$rate."');\n";
	//echo "$('#txt_style_no').val('".$sql[0][csf('style_ref_no')]."');\n";
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
				load_drop_down( 'cutting_update_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
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
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbostringsearchtype').value, 'create_po_search_list_view', 'search_div', 'cutting_update_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(a.INSERT_DATE)=$year";   }
		if($db_type==2) {$sql_shipment_year_cond=" and to_char(a.INSERT_DATE,'YYYY')=$year";}
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
	$total_cut_qty_arr=sql_select("SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=1 $po_id_cond group by po_break_down_id, item_number_id, country_id");

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

		$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=1 and is_deleted=0");

		echo "$('#txt_cumul_cutting').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_cutting').val('".$total_produced."');\n";
		$yet_to_produced = $plan_cut_qnty - $total_produced;
		echo "$('#txt_yet_cut').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_cut').val('".$yet_to_produced."');\n";
   	}
 	exit();
}

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(val)
		{
			$("#hidden_sys_data").val(val);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
</script>
</head>
<body>
<div style="width:850px;" align="center" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="6">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="2" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="130" colspan="2"> WO Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
				<td><?=create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
				<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $service_company_id, "",0 ); 
				//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.tag_company=$company_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
				
				?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
                
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
                <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_style_ref').value+'_'+'<? echo $txt_job_no; ?>', 'create_wo_search_list_view', 'search_div', 'cutting_update_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_sys_data" value="hidden_sys_data" />
                </td>
            </tr>
        </tbody>
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

if($action=="create_wo_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$search_category=$ex_data[5];
	$booking_prifix=$ex_data[6];
	$job_prifix=$ex_data[7];
	$year_selection=$ex_data[8];
	$chk_job_wo_po=trim($ex_data[9]);
	$style_ref=$ex_data[10];
	$jobno=$ex_data[11];
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";

	if($search_category==0 || $search_category==4)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==1)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num ='$job_prifix' "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num ='$booking_prifix'   "; else $booking_cond="";
	}
	else if($search_category==2)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '$job_prifix%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==3)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_no_id>0 and entry_form=643 and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(d.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.style_ref_no) as style_ref_no from subcon_wo_mst a, subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.pay_mode in (1,2,4) and a.entry_form=643 and b.entry_form=643 and d.job_no='$jobno' $company $supplier $sql_cond $buyer_cond $job_cond $booking_cond $job_ids_cond group by a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, a.insert_date, d.buyer_name order by a.id DESC";
	}
	//echo $sql;
	?>
	<div style="width:850px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="100">WO no</th>
                <th width="50">WO Year</th>
                <th width="70">WO Date</th>
                <th width="140">Service Company</th>
                <th width="140">Buyer Name</th>
				<th width="100">Job No</th>
                <th width="120">Style Ref.</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
				<?
				$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$i=1;
				$nameArray=sql_select( $sql );
				$linkingWoArr=array();
				foreach($nameArray as $row)
				{
					$typeofservice=explode(",",$row[csf("service_sweater")]);
					if (in_array(2, $typeofservice)) {
						$linkingWoArr[$row[csf('id')]]=$row[csf('SUCON_WO_NO')];
					}
				}
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($linkingWoArr[$selectResult[csf('id')]]!="")
					{
						$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
						$style_ref_no=implode(",",array_unique(explode(",",$selectResult[csf("style_ref_no")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						
						$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$selectResult[csf('id')].'_'.$selectResult[csf('SUCON_WO_NO')]; ?>'); ">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('SUCON_WO_NO')]; ?></td>
							<td width="50" align="center"><?=$selectResult[csf('year')]; ?></td>
							<td width="70"><?=change_date_format($selectResult[csf('booking_date')]); ?></td>
							<td width="140" style="word-break:break-all"><?=$supplier; ?></td>
							<td width="140" style="word-break:break-all"><?=$buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
							<td width="100" style="word-break:break-all"><?=$job_no; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td><?=change_date_format($selectResult[csf('CLOSING_DATE')]); ?></td>
						</tr>
							<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
		<?
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
		$variableSettingsRej = $dataArr[6];

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
			
			$dtlsData = sql_select("SELECT c.item_number_id,c.color_number_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1) and b.po_break_down_id=c.po_break_down_id and c.id=a.color_size_break_down_id group by c.item_number_id,c.color_number_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_qnty']= $row[csf('production_qnty')];
			}
			
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty
				from wo_po_color_size_breakdown a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
				

			
			//echo $sql;
		}
		else if( $variableSettings==3 ) //color and size level
		{

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=1 ) as production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1) group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			}

			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order"; //color_number_id, id


		}
		else // by default color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=1 ) as production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1) group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			}

			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
		}
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
		//echo $sql;die;
		$colorResult = sql_select($sql);
  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{
				$production_qnty = $color_size_qnty_array[$color[csf('item_number_id')]][$color[csf('color_number_id')]]['production_qnty'];
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$production_qnty).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
				$totalQnty += $color[csf("plan_cut_qnty")]-$production_qnty;
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
				}
						 $bundle_mst_data="";
						 $bundle_dtls_data="";
					 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];

 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="text" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="'.$po_color_size_qnty_arr[$color[csf("color_number_id")]][$color[csf("size_number_id")]].'" class="text_boxes_numeric" disabled="" readonly=""  style="size:30px;"  /></td></tr>';
			}

			$i++;
		}
		//echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
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
	                <th width="70" align="center">Production Date</th>
	                <th width="60" align="center">Production Qnty</th>
	                <th width="60" align="center">Reject Qnty</th>
	                <th width="60" align="center">Reporting Hour</th>
	                <th width="120" align="center">Serving Company</th>
	                <th width="50" align="center">Color Type</th>
	                <th width="100" align="center">Location</th>
	                <th width="100" align="center">Floor</th>
	                <th width="100" align="center">Table</th>
	                <th width="100" align="center">Knitting No</th>
                    <th width="100"  align="center">Wo No.</th>
	            </thead>
			</table>
		</div>
		<div style="width:1140px;max-height:250px; overflow-y:auto; overflow-x: auto;" id="sewing_production_list_view" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="tbl_list_search" align="left">
			<?php
				$i=1;
				$total_production_qnty=0;
				$sql_color_type=sql_select("SELECT a.id,b.color_type_id from pro_garments_production_mst a , pro_garments_production_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.production_type=1  and a.production_type=1 and a.po_break_down_id='$po_id' group by a.id,b.color_type_id");
		 		foreach($sql_color_type as $key=>$value)
		 		{
		 			$color_type_arrs[$value[csf("id")]]=$value[csf("color_type_id")];
		 		}

				if($db_type==0)
				{
					$sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.production_date,a.production_quantity,a.reject_qnty,a.cut_no,a.production_source,TIME_FORMAT(a.production_hour, '%H:%i' ) as production_hour,a.serving_company,a.location,a.floor_id,a.country_id,a.table_no, b.id as color_id ,a.wo_order_no from pro_garments_production_mst a,wo_po_color_size_breakdown b where a.po_break_down_id='$po_id' and a.po_break_down_id=b.po_break_down_id and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='1' and a.status_active=1 and a.is_deleted=0 group by a.id order by a.production_date");
				}
				else
				{
					$sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.production_date,a.production_quantity,a.reject_qnty,a.cut_no,a.production_source,TO_CHAR(a.production_hour,'HH24:MI') as production_hour,a.serving_company,a.location,a.floor_id,a.country_id,a.table_no, min(b.id) as color_id,a.wo_order_no from pro_garments_production_mst a,wo_po_color_size_breakdown b where a.po_break_down_id='$po_id' and a.po_break_down_id=b.po_break_down_id and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='1' and a.status_active=1 and a.is_deleted=0 group by a.id,a.po_break_down_id,a.item_number_id, a.production_date, a.production_quantity, a.reject_qnty, a.cut_no, a.production_source,a.production_hour,a.serving_company,a.location,a.floor_id,a.country_id,a.table_no,a.wo_order_no order by a.production_date");
				}

				foreach($sqlResult as $selectResult){

					if ($i%2==0)
	                	$bgcolor="#E9F3FF";
	                else
	               	 	$bgcolor="#FFFFFF";
	 				$total_production_qnty+=$selectResult[csf('production_quantity')];
					$tot_data=$selectResult[csf('id')].'_'.$selectResult[csf('color_id')];
					
					$fnc_var="get_php_form_data('".$tot_data."','populate_cutting_form_data','requires/cutting_update_controller');";
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
	                <td width="60" onClick="<? echo $fnc_var;?>" align="right"><?php  echo $selectResult[csf('reject_qnty')]; ?></td>

	                <td width="60" onClick="<? echo $fnc_var;?>" align="center"><?php echo $selectResult[csf('production_hour')]; ?></td>
					<?php
	                       $source= $selectResult[csf('production_source')];
						   if($source==3)
							{
								$serving_company =	$lib_supplier[$selectResult[csf('serving_company')]];
							}
							else
							{
								$serving_company = $lib_company[$selectResult[csf('serving_company')]];
							}
	                ?>
	                <td width="120" onClick="<? echo $fnc_var;?>" align="left"><p><?php echo $serving_company; ?></p></td>
	                <td width="50" onClick="<? echo $fnc_var;?>" align="left"><p><?php echo $color_type[$color_type_arrs[$selectResult[csf("id")]]]; ?></p></td>
	 				<?php
	 					// $location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
	 					// $floorn_name= return_field_value("floor_name","lib_prod_floor","id='".$selectResult[csf('floor_id')]."'");
	 					// $table_name= return_field_value("table_name","lib_table_entry","id='".$selectResult[csf('table_no')]."'");
					?>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="left"><? echo $lib_location[$selectResult[csf('location')]]; ?></td>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="left"><? echo $lib_floor[$selectResult[csf('floor_id')]]; ?></td>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="left"><? echo $lib_table[$selectResult[csf('table_no')]]; ?></td>
	                <td width="100" onClick="<? echo $fnc_var;?>" align="right"><?php  echo $selectResult[csf('cut_no')]; ?></td>
					<td width="100" onClick="<? echo $fnc_var;?>" align="right"><?php  echo $selectResult['WO_ORDER_NO']; ?></td>
                   
				</tr>
				<?php
				$i++;
				}
				?>
	            <!--<tfoot>
	            	<tr>
	                	<th colspan="3"></th>
	                    <th><!? echo $total_production_qnty; ?></th>
	                    <th colspan="3"></th>
	                </tr>
	            </tfoot>-->
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
		$cutting_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
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

	$sqlResult =sql_select("SELECT id,company_id, po_break_down_id, item_number_id, challan_no, production_source, produced_by, production_date, production_quantity, $production_hour_cond , entry_break_down_type, break_down_type_rej, serving_company, location, floor_id, reject_qnty,cut_no, used_qty_kg, reject_qty_kg, reject_qty_kg_break_down, remarks, total_produced, yet_to_produced, country_id, wo_order_id, wo_order_no, currency_id,exchange_rate,rate, table_no from pro_garments_production_mst where id='$data[0]' and production_type='1' and status_active=1 and is_deleted=0 order by id");

	$po_id=$sqlResult[0][csf("po_break_down_id")];
	$color_type_val=sql_select("SELECT b.color_type_id  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and b.status_active=1 and  a.id='$data[0]' group by b.color_type_id ");
    $sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
	foreach(sql_select($sqls_col_size) as $key=>$value)
	{
		$po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
	}

  	foreach($sqlResult as $result)
	{
		echo "$('#txt_cutting_date').val('".change_date_format($result[csf('production_date')])."');\n";


		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/cutting_update_controller', ".$result[csf('production_source')].", 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
		echo "$('#cbo_cutting_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/cutting_update_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/cutting_update_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "load_drop_down( 'requires/cutting_update_controller', '".$result[csf('serving_company')]."_".$result[csf('location')]."_".$result[csf('floor_id')]."', 'load_drop_down_table', 'table_td' );\n";
		echo "$('#cbo_table').val('".$result[csf('table_no')]."');\n";

		echo "load_drop_down( 'requires/cutting_update_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";

		if($result[csf('production_source')]==3)
		{
			echo "load_drop_down( 'requires/cutting_update_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";

			//load_drop_down( 'requires/cutting_update_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );

			echo "$('#txt_wo_id').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#txt_wo_no').val('".$result[csf('wo_order_no')]."');\n";
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

		echo "$('#cbo_produced_type').val('".$result[csf('produced_by')]."');\n";
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
		//echo "$('#cbo_time').val('".$time."');\n";
		echo "$('#txt_challan_no').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_cutting_qty').attr('placeholder','".$result[csf('production_quantity')]."');\n";
 		echo "$('#txt_cutting_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_cutting_no').val('".$result[csf('cut_no')]."');\n";
		echo "$('#txt_used_qty_kg').val('".$result[csf('used_qty_kg')]."');\n";
		echo "$('#txt_reject_qty_kg').val('".$result[csf('reject_qty_kg')]."');\n";
		echo "$('#save_data').val('".$result[csf('reject_qty_kg_break_down')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0");

		$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and production_type=1 and is_deleted=0");

		echo "$('#txt_cumul_cutting').val('".$total_produced."');\n";
		$yet_to_produced = $plan_cut_qnty - $total_produced;
		echo "$('#txt_yet_cut').val('".$yet_to_produced."');\n";

		/*echo "$('#txt_cumul_cutting').val('".$result[csf('total_produced')]."');\n";
		echo "$('#txt_yet_cut').val('".$result[csf('yet_to_produced')]."');\n"; */

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_cutting_update_entry',1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');



		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];

		//echo "shajjad123_".$variableSettings;die;

		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];


			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and b.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
			}

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and  	production_type=1 ) as production_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then reject_qty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=1 ) as reject_qty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id order by color_number_id,id";
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty, sum(b.reject_qty) as reject_qty
				from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=1
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id order by  a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and  	production_type=1 ) as production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0";*/// order by color_number_id,size_number_id

					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=1 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id   and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id <> 0 and a.production_type in(1) group by a.color_size_break_down_id");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";


			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and  	production_type=1 ) as production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0";*/// order by color_number_id,size_number_id

				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=1 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";

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
 			//print_r($sql_dtls);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{

				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];

					$amount 	= $amountArr[$index];
					$rej_qnty 	= $rejectArr[$index];
					//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].': <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";

					}


					 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";

 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
					// $rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];


					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" onkeyup="" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'></td><td><input type="text" name="button" value="'.$po_color_size_qnty_arr[$color[csf("color_number_id")]][$color[csf("size_number_id")]].'" readonly="" disabled="" class="text_boxes_numeric" style="size:30px;"  /></td></tr>';
					//$colorWiseTotal += $amount;
					 $bundle_dtls_data="";
					 $bundle_dtls_data="";
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$result[csf('production_quantity')].'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
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
	$sql = "SELECT sum(case when a.production_type=4 then b.production_qnty else 0 end) as prod_qty,sum(case when a.production_type=1 then b.production_qnty else 0 end) as cut_qty from pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and c.color_number_id=$colorId and c.size_number_id=$sizeId and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id=$hidden_po_break_down_id and a.production_type in(1,4) and c.id=b.color_size_break_down_id";
	// echo $sql;
	$sql_res = sql_select($sql);
	echo $sql_res[0]['CUT_QTY']."****".$sql_res[0]['PROD_QTY'];
	die();
}

if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function fnc_close()
		{
			var save_string='';	
			var txtWestageQnty=$("#txtWestageQnty").val();
			var txtCutPanelQnty=$("#txtCutPanelQnty").val();
			var txtShortQnty=$("#txtShortQnty").val();		
			save_string=txtWestageQnty*1+'__'+txtCutPanelQnty*1+'__'+txtShortQnty*1;	

			// alert (save_string);
			$('#save_string').val( save_string );
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">  
            	<thead>
                	<tr><th colspan="4">Reject Qty (kg) Info</th></tr>
                	<tr><th width="40">Types</th><th width="100">Westage (KG)</th><th width="100"> Reject Cut Panel (KG)</th><th width="100">Short Pcs (KG)</th></tr>
                </thead>             
                    <?					
					$explSaveData = explode("__",$save_data);						
					
                    $i=1;
                    
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                    	<td></td>
                        <td align="center">
                            <input type="text" name="txtWestageQnty" id="txtWestageQnty" class="text_boxes_numeric" style="width:80px" value="<? echo $explSaveData[0]; ?>">
                        </td>
                        <td align="center">
                            <input type="text" name="txtCutPanelQnty" id="txtCutPanelQnty" class="text_boxes_numeric" style="width:80px" value="<? echo $explSaveData[1]; ?>">
                        </td>
                        <td align="center">
                            <input type="text" name="txtShortQnty" id="txtShortQnty" class="text_boxes_numeric" style="width:80px" value="<? echo $explSaveData[2]; ?>">
                        </td>
                    </tr>                        
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="4">
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

	// echo "SELECT min(production_date) as PRODUCTION_DATE from pro_garments_production_mst where status_active=1 and is_deleted=0 and po_break_down_id=$hidden_po_break_down_id "; die();
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

		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		

 		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

		//production_type array

		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		$field_array2="id, garments_nature, company_id, challan_no, po_break_down_id,item_number_id, country_id, production_source, serving_company, location, produced_by, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, production_hour, remarks, floor_id, table_no, reject_qnty, cut_no, used_qty_kg, reject_qty_kg, reject_qty_kg_break_down ,total_produced, yet_to_produced, wo_order_id, wo_order_no, currency_id, exchange_rate, rate, amount, inserted_by, insert_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_cutting_qty);}
		else {$amount="";}
		if($db_type==0)
		{
			$data_array2="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.", ".$cbo_item_name.", ".$cbo_country_name.", ".$cbo_source.",".$cbo_cutting_company.",".$cbo_location.",".$cbo_produced_type.",".$txt_cutting_date.",".$txt_cutting_qty.",1,".$sewing_production_variable.",".$cutting_production_variable_reject.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$cbo_table.",".$txt_reject_qty.",".$txt_cutting_no.",".$txt_used_qty_kg.",".$txt_reject_qty_kg.",".$save_data.",".$txt_cumul_cutting.",".$txt_yet_cut.",".$txt_wo_id.",".$txt_wo_no.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array2="INSERT INTO pro_garments_production_mst (".$field_array2.") VALUES(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.", ".$cbo_item_name.", ".$cbo_country_name.", ".$cbo_source.",".$cbo_cutting_company.",".$cbo_location.",".$cbo_produced_type.",".$txt_cutting_date.",".$txt_cutting_qty.",1,".$sewing_production_variable.",".$cutting_production_variable_reject.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$cbo_table.",".$txt_reject_qty.",".$txt_cutting_no.",".$txt_used_qty_kg.",".$txt_reject_qty_kg.",".$save_data.",".$txt_cumul_cutting.",".$txt_yet_cut.",".$txt_wo_id.",".$txt_wo_no.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";

		}


		// pro_garments_production_dtls table entry here ----------------------------------------------//
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = array_filter(explode("**",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}

			//var_dump($rejQtyArr);die;
 			$rowEx = array_filter(explode("***",$colorIDvalue));
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				//1 means cutting update
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",1,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
				else $data_array .= ",(".$dtls_id.",".$id.",1,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
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
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = array_filter(explode("***",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			//var_dump($rejQtyArr);die;
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
 				//1 means cutting update
 				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
				else $data_array .= ",(".$dtls_id.",".$id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}

		}
		// echo "10**".$data_array2;die;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{  
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		} 
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			if($dtlsrID)
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
		}
		else
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


		//echo $bundle_dtls_data; die;
		/*$bundle_mst_data_row=explode("*****",$bundle_mst_data);
		$bundle_dtls_data_row=explode("*****",$bundle_dtls_data);

		$id_bundle=return_next_id("id", "pro_bundle_mst", 1);
		$field_array2="id, pro_gmts_pro_id,po_break_down_id, color_size_id, pcs_per_bundle,no_of_bundle, batch_no, color_shade, status_active, is_deleted, inserted_by, insert_date";

		$id_dtls=return_next_id( "id", "pro_bundle_dtls", 1 ) ;
		$field_array3="id,bundle_mst_id,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id,bundle_bar_code,bundle_bar_code_prefix";

		$cbo_company_name=str_pad(str_replace("'","",$cbo_company_name),2,"0",STR_PAD_LEFT);
		$hid_job_num=str_replace("'","",explode("-",$hid_job_num));
		$bundle_number=$cbo_company_name."".$hid_job_num[1];
		$bundle_auto_num=return_field_value( "max(bundle_bar_code) as bundle_bar_code", "pro_bundle_dtls", " bundle_bar_code_prefix='$bundle_number'", "bundle_bar_code" );

		if($bundle_auto_num=="") $tmp=1; else $tmp=substr($bundle_auto_num,5,7)+1;

		$id4=return_next_id( "id", " pro_operation_bar_code", 1 ) ;
		$field_array4="id,op_code,bundle_dtls,bundle_mst,prod_mst,style,gsd_mst,gsd_dtls,op_bar_code_prefix";

		for ($mst=0; $mst<count($bundle_mst_data_row); $mst++)
		{
			$bundle_mst_data=explode("__",$bundle_mst_data_row[$mst]);
			$hid_pcs_bundle=$bundle_mst_data[0];
			$hid_tot_bundle=$bundle_mst_data[1];
			$hid_batch_no=$bundle_mst_data[2];
			$hid_color_shade=$bundle_mst_data[3];
			$row_id=$bundle_mst_data[4];

			if($data_array_bnd_mst=="") $data_array_bnd_mst.="(".$id_bundle.",".$id.",".$hidden_po_break_down_id.",".$row_id.",".$hid_pcs_bundle.", ".$hid_tot_bundle.", '".$hid_batch_no."','".$hid_color_shade."',1,0,".$user_id.",'".$pc_date_time."')";
			else $data_array_bnd_mst .=",(".$id_bundle.",".$id.",".$hidden_po_break_down_id.",".$row_id.",".$hid_pcs_bundle.", ".$hid_tot_bundle.", '".$hid_batch_no."','".$hid_color_shade."',1,0,".$user_id.",'".$pc_date_time."')";

			$bundle_dtls_data=explode("***",$bundle_dtls_data_row[$mst]);
			for($dtls=0;$dtls<count($bundle_dtls_data); $dtls++)
			{
				$bundle_dtls_data_data=explode("__",$bundle_dtls_data[$dtls]);
				$pc_range=explode("-",$bundle_dtls_data_data[1]);
				$bundle_number_org=$bundle_number."".str_pad($tmp,7,"0",STR_PAD_LEFT);
				if ($data_array3!="") $data_array3 .=",";
				 $data_array3 .="(".$id_dtls.",".$id_bundle.",'".$bundle_dtls_data_data[0]."','".$pc_range[0]."','".$pc_range[1]."',".$row_id.",'".$bundle_number_org."','".$bundle_number."')";

				$operation_number_org="";

				foreach($operation_array as $opkey)
				{
					$operation_number_org=$bundle_number_org."".str_pad($opkey,3,"0",STR_PAD_LEFT);

					if ($data_array4!="") $data_array4 .=",";
				 	$data_array4.="(".$id4.",'".$operation_number_org."','".$id_dtls."','".$id_bundle."',".$row_id.",1,'".$gsd_mst_array[$opkey]."','".$gsd_dtls_array[$opkey]."','".$bundle_number."')";
					$id4=$id4+1;
				}
				$tmp++;
				$id_dtls++;
			}
			$id_bundle++;
		}*/
		//echo "insert into pro_bundle_mst (".$field_array2.") values ".$data_array_bnd_mst;die;
		/*if($data_array_bnd_mst!="")$rIDb=sql_insert("pro_bundle_mst",$field_array2,$data_array_bnd_mst,1);
		if($data_array3!="")$rID3=sql_insert("pro_bundle_dtls",$field_array3,$data_array3,1);
		if($data_array4!="") $rID4=sql_insert("pro_operation_bar_code",$field_array4,$data_array4,1);*/
		/*echo $rID ."&&". $dtlsrID;
		echo $data_array2;die;*/
		//check_table_status( $_SESSION['menu_id'],0 );

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
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
  	else if ($operation==1) // Update Here------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		// var_dump(expression)
		//table lock here
		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}


		// pro_garments_production_mst table data entry here
		$field_array1="company_id*challan_no*production_source*serving_company*location*produced_by*production_date*production_quantity*production_type* entry_break_down_type*break_down_type_rej*production_hour*remarks*floor_id*table_no*reject_qnty*cut_no*used_qty_kg*reject_qty_kg*reject_qty_kg_break_down*total_produced*yet_to_produced*wo_order_id*wo_order_no*currency_id*exchange_rate*rate*amount*updated_by*update_date";

		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_cutting_qty);}
		else {$amount="";}
		$data_array1="".$cbo_company_name."*".$txt_challan_no."*".$cbo_source."*".$cbo_cutting_company."*".$cbo_location."*".$cbo_produced_type."*".$txt_cutting_date."*".$txt_cutting_qty."*1*".$sewing_production_variable."*".$cutting_production_variable_reject."*".$txt_reporting_hour."*".$txt_remark."*".$cbo_floor."*".$cbo_table."*".$txt_reject_qty."*".$txt_cutting_no."*".$txt_used_qty_kg."*".$txt_reject_qty_kg."*".$save_data."*".$txt_cumul_cutting."*".$txt_yet_cut."*".$txt_wo_id."*".$txt_wo_no."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'";


		//echo "INSERT INTO pro_garments_production_mst (".$field_array.") VALUES ".$data_array;die;


 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);

		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			//delete details table data
			$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				$rowExRej = array_filter(explode("**",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 				$rowEx = array_filter(explode("***",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//1 means cutting update
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",1,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",1,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}



			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				// check next process production
				$sql_prod = "SELECT a.production_type,b.color_size_break_down_id as colSizeId, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.production_type in(1,4) and a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name group by a.production_type,b.color_size_break_down_id";
				// echo "10**$sql_prod";die();
				$prod_qty_array = array();
				foreach ($sql_prod as $val) 
				{
					$prod_qty_array[$val[csf('colSizeId')]][$val[csf('production_type')]] = $val[csf('production_qnty')];
				}

				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by color_number_id,size_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				$rowExRej = array_filter(explode("***",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				//echo "10**"; print_r($rejQtyArr);die;
				 //echo "10**$colorIDvalueRej";die;
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

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
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
		//echo "10**".$rID.$dtlsrID;die;

		/*$bundle_mst_data_row=explode("*****",$bundle_mst_data);
		$bundle_dtls_data_row=explode("*****",$bundle_dtls_data);

		$id_bundle=return_next_id("id", "pro_bundle_mst", 1);
		$field_array2="id, pro_gmts_pro_id,po_break_down_id, color_size_id, pcs_per_bundle,no_of_bundle,batch_no, color_shade, status_active, is_deleted, inserted_by, insert_date";

		$id_dtls=return_next_id( "id", "pro_bundle_dtls", 1 ) ;
		$field_array3="id,bundle_mst_id,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id,bundle_bar_code,bundle_bar_code_prefix";

		$cbo_company_name=str_pad(str_replace("'","",$cbo_company_name),2,"0",STR_PAD_LEFT);
		$hid_job_num=str_replace("'","",explode("-",$hid_job_num));
		$bundle_number=$cbo_company_name."".$hid_job_num[1];
		$bundle_auto_num=return_field_value( "max(bundle_bar_code) as bundle_bar_code", "pro_bundle_dtls", " bundle_bar_code_prefix='$bundle_number'", "bundle_bar_code" );

		if($bundle_auto_num=="") $tmp=1; else $tmp=substr($bundle_auto_num,5,7)+1;

		$id4=return_next_id( "id", " pro_operation_bar_code", 1 ) ;
		$field_array4="id,op_code,bundle_dtls,bundle_mst,prod_mst,style,gsd_mst,gsd_dtls,op_bar_code_prefix";

		for ($mst=0; $mst<count($bundle_mst_data_row); $mst++)
		{
			$bundle_mst_data=explode("__",$bundle_mst_data_row[$mst]);
			$hid_pcs_bundle=$bundle_mst_data[0];
			$hid_tot_bundle=$bundle_mst_data[1];
			$hid_batch_no=$bundle_mst_data[2];
			$hid_color_shade=$bundle_mst_data[3];
			$row_id=$bundle_mst_data[4];
			$prod_mst=$bundle_mst_data[5];

		 	execute_query("delete a.*, b.*, c.* from pro_bundle_mst a, pro_bundle_dtls b, pro_operation_bar_code c  where pro_gmts_pro_id=$txt_mst_id and bundle_mst_id=a.id and bundle_mst=a.id");
			//execute_query("delete from pro_bundle_dtls where bundle_mst_id=$row_id");
			//execute_query("delete from pro_operation_bar_code where bundle_mst=$row_id");

			if($data_array_bnd_mst=="") $data_array_bnd_mst.="(".$id_bundle.",".$txt_mst_id.",".$hidden_po_break_down_id.",".$row_id.",".$hid_pcs_bundle.", ".$hid_tot_bundle.", '".$hid_batch_no."','".$hid_color_shade."',1,0,".$user_id.",'".$pc_date_time."')";
			else $data_array_bnd_mst .=",(".$id_bundle.",".$txt_mst_id.",".$hidden_po_break_down_id.",".$row_id.",".$hid_pcs_bundle.", ".$hid_tot_bundle.", '".$hid_batch_no."','".$hid_color_shade."',1,0,".$user_id.",'".$pc_date_time."')";

			$bundle_dtls_data=explode("***",$bundle_dtls_data_row[$mst]);
			for($dtls=0;$dtls<count($bundle_dtls_data); $dtls++)
			{
				$bundle_dtls_data_data=explode("__",$bundle_dtls_data[$dtls]);
				$pc_range=explode("-",$bundle_dtls_data_data[1]);
				$bundle_number_org=$bundle_number."".str_pad($tmp,7,"0",STR_PAD_LEFT);
				if ($data_array3!="") $data_array3 .=",";
				 $data_array3 .="(".$id_dtls.",".$id_bundle.",'".$bundle_dtls_data_data[0]."','".$pc_range[0]."','".$pc_range[1]."',".$row_id.",'".$bundle_number_org."','".$bundle_number."')";

				$operation_number_org="";

				foreach($operation_array as $opkey)
				{
					$operation_number_org=$bundle_number_org."".str_pad($opkey,3,"0",STR_PAD_LEFT);

					if ($data_array4!="") $data_array4 .=",";
				 	$data_array4.="(".$id4.",'".$operation_number_org."','".$id_dtls."','".$id_bundle."',".$row_id.",1,'".$gsd_mst_array[$opkey]."','".$gsd_dtls_array[$opkey]."','".$bundle_number."')";
					$id4=$id4+1;
				}
				$tmp++;
				$id_dtls++;
			}

			$id_bundle++;
		}*/
	 	// echo "INSERT INTO pro_bundle_mst (".$field_array2.") VALUES ".$data_array_bnd_mst; die;
		//if($data_array_bnd_mst!="") $rIDb=sql_insert("pro_bundle_mst",$field_array2,$data_array_bnd_mst,1);
		//if($data_array3!="")$rID3=sql_insert("pro_bundle_dtls",$field_array3,$data_array3,1);
		//if($data_array4!="") $rID4=sql_insert("pro_operation_bar_code",$field_array4,$data_array4,1);

		 //echo bulk_update_sql_statement( "pro_bundle_mst", "id",$field_array_up,$data_array_up,$id_arr );die;
		//$rIDb=execute_query(bulk_update_sql_statement( "pro_bundle_mst", "id",$field_array_up,$data_array_up,$id_arr ));

		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID  )//&& $rID6 && $rID7 && $rIDb && $rID3
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
		$next_process_sql=sql_select("SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type <> 1 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name");
		$next_process_sql2=sql_select("SELECT po_break_down_id from pro_cut_delivery_order_dtls where status_active=1 and is_deleted=0 and production_type <> 1 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name");
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

		if($db_type==0)
		{
			if($rID && $rID_dtls)
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

if ($action=="save_update_delete_bundle")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$all_pcs_per_bundle=explode(",",$all_pcs_per_bundle);

	$sql=sql_select("select a.id,a.operation_count,b.id as gsd_dtls_id,b.operation_id,b.row_sequence_no from ppl_gsd_entry_dtls b,ppl_gsd_entry_mst a where a.id=b.mst_id and a.po_job_no='$hid_job_num'");
	$operation_array=array();
	foreach($sql as $data)
	{
		$operation_array[]=$data[csf("row_sequence_no")];
		$gsd_mst_array[$data[csf("row_sequence_no")]]=$data[csf("id")];
		$gsd_dtls_array[$data[csf("row_sequence_no")]]=$data[csf("gsd_dtls_id")];
	}

	if($db_type==0)
	{
		$color_size_break_down_ids=return_field_value("group_concat(id) as id","wo_po_color_size_breakdown", "job_no_mst='$hid_job_num'","id");
	}
	else
	{
		$color_size_break_down_ids=return_field_value("listagg(CAST(id as VARCHAR(4000)),',') within group (order by id) as id","wo_po_color_size_breakdown", "job_no_mst='$hid_job_num'","id");
	}

  	if($operation==0) // Insert Here------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}

 		$id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );
		$field_array="id, pro_gmts_pro_id, po_break_down_id, color_size_id, bundle_no_creation, cut_no, pcs_per_bundle, no_of_bundle, batch_no, color_shade, status_active, is_deleted, inserted_by, insert_date";

		//$id_dtls=return_next_id( "id", "pro_bundle_dtls", 1 ) ;
		$id_dtls= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
		$field_array_dtls="id,bundle_mst_id,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id,bundle_bar_code,bundle_bar_code_prefix, cut_no, custom_bundle_no, custom_gmts_range_start, custom_gmts_range_end";

		$company_name=str_pad(str_replace("'","",$company_name),2,"0",STR_PAD_LEFT);
		$hid_job_num=str_replace("'","",explode("-",$hid_job_num));
		$bundle_number=$company_name."".$hid_job_num[1];

		$last_cut_num=return_field_value("max(cut_no) as cut_no", "pro_bundle_dtls", "bundle_bar_code_prefix='$bundle_number'", "cut_no" );
		$curr_cut_no=$last_cut_num+1;
		if(str_replace("'","",$txt_cut_no)>$curr_cut_no)
		{
			echo "20**".$curr_cut_no;
			disconnect($con);
			die;
		}

		$bundle_auto_num=return_field_value( "max(bundle_bar_code) as bundle_bar_code", "pro_bundle_dtls", "bundle_bar_code_prefix='$bundle_number'", "bundle_bar_code" );
		$last_bundle_range=return_field_value("max(pcs_range_end) as pcs_range_end"," pro_bundle_dtls", "color_size_id in (".$color_size_break_down_ids.")","pcs_range_end");

		$erange=$last_bundle_range;
		if($bundle_auto_num=="") $tmp=1; else $tmp=substr($bundle_auto_num,5,7)+1;

		$custom_bundle_data=sql_select("select custom_bundle_no as custom_bundle_no, custom_gmts_range_end as custom_gmts_range_end from pro_bundle_dtls where color_size_id in (".$color_size_break_down_ids.") and rownum=1 order by id desc");

		$custom_bundle_auto_num=$custom_bundle_data[0][csf('custom_bundle_no')];
		$custom_last_bundle_range=$custom_bundle_data[0][csf('custom_gmts_range_end')];

		$custom_erange=$custom_last_bundle_range;
		$custom_bundle_num=$custom_bundle_auto_num+1;

		$id4=return_next_id( "id", "pro_operation_bar_code", 1 ) ;
		$field_array_bar_code="id,op_code,bundle_dtls,bundle_mst,prod_mst,style,gsd_mst,gsd_dtls,op_bar_code_prefix";

		$data_array="(".$id_bundle.",".$txt_mst_id.",".$hidden_po_break_down_id.",".$row_id.",".$bundle_no_creation.",".$txt_cut_no.",".$txt_pcs_per_bendle.",".$txt_no_of_bendle.",".$txt_batch_no.",".$txt_color_shade.",1,0,".$user_id.",'".$pc_date_time."')";

		$bundle_dtls_data=explode("***",str_replace("'","",$txt_details_row));

		for($dtls=0;$dtls<count($bundle_dtls_data); $dtls++)
		{
			$bundle_dtls_data_data=explode("__",$bundle_dtls_data[$dtls]);
			$pcs_per_bundle=$all_pcs_per_bundle[$dtls];

			if($pcs_per_bundle>0)
			{
				$srange=$erange+1;
				$erange=$srange+$pcs_per_bundle-1;

				$bundle_number_org=$bundle_number."".str_pad($tmp,7,"0",STR_PAD_LEFT);

				if($custom_bundle_num>$custom_bundle_num_limit) $custom_bundle_num=1;

				if(($custom_erange+$pcs_per_bundle)>$custom_bundle_range_limit) $custom_erange=0;

				$custom_srange=$custom_erange+1;
				$custom_erange=$custom_srange+$pcs_per_bundle-1;

				if ($data_array_dtls!="") $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id_bundle.",'".$pcs_per_bundle."','".$srange."','".$erange."',".$row_id.",'".$bundle_number_org."','".$bundle_number."',".$txt_cut_no.",".$custom_bundle_num.",".$custom_srange.",".$custom_erange.")";

				$operation_number_org="";

				foreach($operation_array as $opkey)
				{
					$operation_number_org=$bundle_number_org."".str_pad($opkey,3,"0",STR_PAD_LEFT);

					if ($data_array_bar_code!="") $data_array_bar_code .=",";
					$data_array_bar_code.="(".$id4.",'".$operation_number_org."','".$id_dtls."','".$id_bundle."',".$row_id.",1,'".$gsd_mst_array[$opkey]."','".$gsd_dtls_array[$opkey]."','".$bundle_number."')";
					$id4=$id4+1;
				}
				$tmp++;
 				$id_dtls= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
				$custom_bundle_num++;
			}
		}

		$rID=sql_insert("pro_bundle_mst",$field_array,$data_array,1);
		if($rID) $flag=1; else $flag=0;

		//echo "10**INSERT INTO pro_operation_bar_code (".$field_array_bar_code.") VALUES ".$data_array_bar_code;die;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("pro_bundle_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($data_array_bar_code!="")
		{
			$rID3=sql_insert("pro_operation_bar_code",$field_array_bar_code,$data_array_bar_code,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$txt_pcs_per_bendle)."**".$id_bundle;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_pcs_per_bendle);
			}

		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_pcs_per_bendle)."**".$id_bundle;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_pcs_per_bendle);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

 		$id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );
		$field_array="batch_no*color_shade*updated_by*update_date";
		$data_array=$txt_batch_no."*".$txt_color_shade."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$company_name=str_pad(str_replace("'","",$company_name),2,"0",STR_PAD_LEFT);
		$hid_job_num=str_replace("'","",explode("-",$hid_job_num));
		$bundle_number=$company_name."".$hid_job_num[1];

		$last_cut_num=return_field_value("max(cut_no) as cut_no", "pro_bundle_dtls", "bundle_bar_code_prefix='$bundle_number' and bundle_mst_id!=$bundle_mst_id", "cut_no" );

		$curr_cut_no=$last_cut_num+1;
		if(str_replace("'","",$txt_cut_no)>$curr_cut_no)
		{
			echo "20**".$curr_cut_no;
			disconnect($con);
			die;
		}

		$bundle_auto_num=return_field_value( "max(bundle_bar_code) as bundle_bar_code", "pro_bundle_dtls", "bundle_bar_code_prefix='$bundle_number'", "bundle_bar_code" );
		$last_bundle_range=return_field_value("max(pcs_range_end) as pcs_range_end","pro_bundle_dtls", "color_size_id in (".$color_size_break_down_ids.") and bundle_mst_id!=$bundle_mst_id","pcs_range_end");

		$erange=$last_bundle_range;
		if($bundle_auto_num=="") $tmp=1; else $tmp=substr($bundle_auto_num,5,7)+1;

		if($db_type==0)
		{
			$custom_bundle_data=sql_select("select custom_bundle_no as custom_bundle_no, custom_gmts_range_end as custom_gmts_range_end from pro_bundle_dtls where color_size_id in (".$color_size_break_down_ids.") and bundle_mst_id!=$bundle_mst_id order by id desc limit 0,1");
		}
		else
		{
			$custom_bundle_data=sql_select("select custom_bundle_no as custom_bundle_no, custom_gmts_range_end as custom_gmts_range_end from pro_bundle_dtls where color_size_id in (".$color_size_break_down_ids.") and bundle_mst_id!=$bundle_mst_id and rownum=1 order by id desc");
		}

		$custom_bundle_auto_num=$custom_bundle_data[0][csf('custom_bundle_no')];
		$custom_last_bundle_range=$custom_bundle_data[0][csf('custom_gmts_range_end')];

		$custom_erange=$custom_last_bundle_range;
		$custom_bundle_num=$custom_bundle_auto_num+1;

		$id4=return_next_id( "id", "pro_operation_bar_code", 1 ) ;
		$field_array_bar_code="id,op_code,bundle_dtls,bundle_mst,prod_mst,style,gsd_mst,gsd_dtls,op_bar_code_prefix";

		$field_array_dtls="pcs_per_bundle*pcs_range_start*pcs_range_end*cut_no*custom_gmts_range_start*custom_gmts_range_end";

		$bundle_dtls_data=explode("***",str_replace("'","",$txt_details_row));
		for($dtls=0;$dtls<count($bundle_dtls_data); $dtls++)
		{
			$bundle_dtls_data_data=explode("__",$bundle_dtls_data[$dtls]);
			$pcs_per_bundle=$all_pcs_per_bundle[$dtls];
			$id_dtls=$bundle_dtls_data_data[5];

			if($pcs_per_bundle>0)
			{
				$bundle_number_org=$bundle_number."".str_pad($tmp,7,"0",STR_PAD_LEFT);

				$srange=$erange+1;
				$erange=$srange+$pcs_per_bundle-1;

				if(($custom_erange+$pcs_per_bundle)>$custom_bundle_range_limit) $custom_erange=0;

				$custom_srange=$custom_erange+1;
				$custom_erange=$custom_srange+$pcs_per_bundle-1;

				if($id_dtls!="")
				{
					$id_arr[]=$id_dtls;
					$data_array_dtls[$id_dtls] = explode("*",("'".$pcs_per_bundle."'*'".$srange."'*'".$erange."'*".$txt_cut_no."*'".$custom_srange."'*'".$custom_erange."'"));
				}

				$operation_number_org="";
				foreach($operation_array as $opkey)
				{
					$operation_number_org=$bundle_number_org."".str_pad($opkey,3,"0",STR_PAD_LEFT);

					if ($data_array_bar_code!="") $data_array_bar_code .=",";
					$data_array_bar_code.="(".$id4.",'".$operation_number_org."','".$id_dtls."',".$bundle_mst_id.",".$row_id.",1,'".$gsd_mst_array[$opkey]."','".$gsd_dtls_array[$opkey]."','".$bundle_number."')";
					$id4=$id4+1;
				}
				$tmp++;
				$custom_bundle_num++;
			}
		}

		$rID=sql_update("pro_bundle_mst",$field_array,$data_array,"id",$bundle_mst_id,0);
		if($rID) $flag=1; else $flag=0;

		if($data_array_dtls!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_bundle_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr ));
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		//echo "10**INSERT INTO pro_operation_bar_code (".$field_array_bar_code.") VALUES ".$data_array_bar_code;die;
		$delete=execute_query( "delete from pro_operation_bar_code where bundle_mst=$bundle_mst_id", 1 );
		if($flag==1)
		{
			if($delete) $flag=1; else $flag=0;
		}

		if($data_array_bar_code!="")
		{
			$rID3=sql_insert("pro_operation_bar_code",$field_array_bar_code,$data_array_bar_code,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_pcs_per_bendle)."**".str_replace("'","",$bundle_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_pcs_per_bendle);
			}

		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_pcs_per_bendle)."**".str_replace("'","",$bundle_mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_pcs_per_bendle);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="bundle_preparation")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//$bundle_mst=explode("__",$bundle_mst);
	if($bundle_no_creation==1) $tbl_width=850; else $tbl_width=650;
	?>
	<script>
	var cutting_qnty='<? echo $cutting_qnty; ?>';
	var colsize='<? echo $colsize; ?>';
	var permission='<? echo $permission; ?>';
	var bundle_no_creation=<? echo $bundle_no_creation; ?>;

	function calculate_bundle( pcs_per_bund )
	{
		document.getElementById('txt_no_of_bendle').value="";
		if( pcs_per_bund>0 )
		{

			var total_bundle=Math.ceil(cutting_qnty/pcs_per_bund);
			document.getElementById('txt_no_of_bendle').value=total_bundle;
		}

		//var data=document.getElementById('bundle_mst').value+"****"+document.getElementById('bundle_dtls').value+"****"+cutting_qnty+"****"+pcs_per_bund+"****"+colsize+"****"+document.getElementById('prev_pcs_per_bundle').value+"****"+document.getElementById('rpt_data').value;

		var data=cutting_qnty+"**"+pcs_per_bund+"**"+colsize+"**"+document.getElementById('prev_pcs_per_bundle').value+"**"+document.getElementById('rpt_data').value+"**"+document.getElementById('row_id').value+"**"+<? echo $txt_mst_id; ?>+"**"+<? echo $hidden_po_break_down_id; ?>;

		var list_view_bundle = return_global_ajax_value( data, 'load_php_dtls_form', '', 'cutting_update_controller');

 		if(list_view_bundle!='' && list_view_bundle!=0)
		{
			$("#pre_tbl tbody").html('');
			$("#pre_tbl tbody tr").remove();
			$("#pre_tbl tbody").append(list_view_bundle);
		}
		document.getElementById('txt_tot_pcs').value=cutting_qnty;
	}

	function fnc_close()
	{
		parent.emailwindow.hide();
	}

	function fnc_cutting_bundle_entry(operation)
	{
		if(operation==2)
		{
			alert("Delete Not Allowed.");
			return;
		}

		if ( form_validation('txt_cut_no*txt_pcs_per_bendle','Cut No*Pcs Per Bundle')==false )
		{
			return;
		}

		var txt_tot_pcs=$("#txt_tot_pcs").val()*1;
		if(cutting_qnty!=txt_tot_pcs)
		{
			alert("Cutting Qnty & Total Pcs Per Bundle Should Be Same");
			return;
		}

		var tot_row=$("#pre_tbl tbody tr").length; var all_pcs_per_bundle='';
		for(var i=1;i<=tot_row;i++)
		{
			if(all_pcs_per_bundle=='') all_pcs_per_bundle=$("#txt_pcs_per_bundle_"+i).val(); else all_pcs_per_bundle+=","+$("#txt_pcs_per_bundle_"+i).val();
		}

		var data="action=save_update_delete_bundle&operation="+operation+'&txt_mst_id='+<? echo $txt_mst_id; ?>+'&company_name='+<? echo $company_name; ?>+'&hid_job_num='+'<? echo $hid_job_num; ?>'+'&hidden_po_break_down_id='+<? echo $hidden_po_break_down_id; ?>+get_submitted_data_string('txt_cut_no*txt_pcs_per_bendle*txt_no_of_bendle*txt_batch_no*txt_color_shade*row_id*txt_details_row*bundle_mst_id',"../../../")+'&all_pcs_per_bundle='+all_pcs_per_bundle;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","cutting_update_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cutting_bundle_entry_Reply_info;

	}

	function fnc_cutting_bundle_entry_Reply_info()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=http.responseText.split('**');
			if(reponse[0]==15)
			{
				 setTimeout('fnc_cutting_bundle_entry('+reponse[1]+')',8000);
			}
			else if(reponse[0]==20)
			{
				alert("Cut No. Serial Break Not Allowed. Cut No. Should Be "+reponse[1]+".");
				$("#txt_cut_no").val(reponse[1]);
			}
			else if(reponse[0]==0 || reponse[0]==1)//insert
			{
				show_msg(reponse[0]);
				$("#prev_pcs_per_bundle").val(reponse[1]);
				$("#bundle_mst_id").val(reponse[2]);
				$('#check_all').removeAttr('checked');
				$('#txt_pcs_per_bendle').attr('disabled','disabled');
				calculate_bundle(document.getElementById('txt_pcs_per_bendle').value);
				set_button_status(1, permission, 'fnc_cutting_bundle_entry',1);
			}
			release_freezing();
		}
	}

	function fnc_operation_sticker_report()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		data=data+"***"+$('#rpt_data' ).val()+"***"+colsize;
		//alert(data);
		var url=return_ajax_request_value(data, "print_report_operation_barcode", "cutting_update_controller");
		window.open(url,"##");
	}

	function fnc_bundle_report()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		data=data+"***"+$('#rpt_data' ).val()+"***"+colsize;
		var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cutting_update_controller");
		window.open(url,"##");
	}

	function fnc_operation_report()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		data=data+"***"+$('#rpt_data' ).val()+"***"+colsize;
		var url=return_ajax_request_value(data, "print_report_operation", "cutting_update_controller");
		//alert(url);return;
		window.open(url,"##");
	}

	function check_all_report()
	{
		$("input[name=chk_bundle]").each(function(index, element) {

				if( $('#check_all').prop('checked')==true)
		 			$(this).attr('checked','true');
				else
					$(this).removeAttr('checked');
		});
	}

	function calculate_total()
	{
		var tot_row=$("#pre_tbl tbody tr").length;
		//alert(tot_row);
		math_operation( "txt_tot_pcs", "txt_pcs_per_bundle_", "+", tot_row,'' );
	}

	function fn_addRow()
	{
		var bundle_mst_id=$("#bundle_mst_id").val();
		if(bundle_mst_id=="")
		{
			var tot_row = $("#pre_tbl tbody tr").length;
			var txt_gmt_color=$('#txt_gmt_color_'+tot_row).val();
			var txt_gmt_size=$('#txt_gmt_size_'+tot_row).val();
			var counter=tot_row+1;

			if(bundle_no_creation==1)
			{
				$('#pre_tbl tbody').append(
					'<tr align="center">'
						+ '<td><input type="text" name="txt_sl_no_' + counter + '"  id="txt_sl_no_' + counter + '" class="text_boxes_numeric" style="width:40px" value="' + counter + '" readonly></td>'
						+ '<td><input type="text" name="txt_bundle_no_' + counter + '" id="txt_bundle_no_' + counter + '" class="text_boxes_numeric" style="width:80px" readonly ></td>'
						+ '<td><input type="text" name="txt_pcs_per_bundle_' + counter + '"  id="txt_pcs_per_bundle_' + counter + '" class="text_boxes_numeric" onkeyup="calculate_total();" style="width:60px"></td>'
						+ '<td><input type="text" name="txt_pcs_no_range_' + counter + '"  id="txt_pcs_no_range_' + counter + '" class="text_boxes_numeric" style="width:120px" readonly ></td>'
						+ '<td><input type="text" name="txt_custom_bundle_no_' + counter + '"  id="txt_custom_bundle_no_' + counter + '" class="text_boxes_numeric" style="width:80px" readonly ></td>'
						+ '<td><input type="text" name="txt_gmts_pcs_no_range_' + counter + '"  id="txt_gmts_pcs_no_range_' + counter + '" class="text_boxes_numeric" style="width:120px" readonly ></td>'
						+ '<td><input type="text" name="txt_gmt_color_' + counter + '"  id="txt_gmt_color_' + counter + '" class="text_boxes" style="width:100px" value="' + txt_gmt_color + '" readonly ></td>'
						+ '<td><input type="text" name="txt_gmt_size_' + counter + '"  id="txt_gmt_size_' + counter + '" class="text_boxes" style="width:100px" value="' + txt_gmt_size + '" readonly ></td>'
						+ '<td><input type="checkbox" id="chk_bundle_' + counter + '" name="chk_bundle" ></td>'
					+ '</tr>'
				);

			}
			else
			{
				$('#pre_tbl tbody').append(
					'<tr align="center">'
						+ '<td><input type="text" name="txt_sl_no_' + counter + '"  id="txt_sl_no_' + counter + '" class="text_boxes_numeric" style="width:40px" value="' + counter + '" readonly></td>'
						+ '<td><input type="text" name="txt_bundle_no_' + counter + '"  id="txt_bundle_no_' + counter + '" class="text_boxes_numeric" style="width:80px" readonly ></td>'
						+ '<td><input type="text" name="txt_pcs_per_bundle_' + counter + '"  id="txt_pcs_per_bundle_' + counter + '" class="text_boxes_numeric" onkeyup="calculate_total();" style="width:60px"></td>'
						+ '<td><input type="text" name="txt_pcs_no_range_' + counter + '"  id="txt_pcs_no_range_' + counter + '" class="text_boxes_numeric" style="width:120px" readonly ></td>'
						+ '<td><input type="text" name="txt_gmt_color_' + counter + '"  id="txt_gmt_color_' + counter + '" class="text_boxes" style="width:100px" value="' + txt_gmt_color + '" readonly ></td>'
						+ '<td><input type="text" name="txt_gmt_size_' + counter + '"  id="txt_gmt_size_' + counter + '" class="text_boxes" style="width:100px" value="' + txt_gmt_size + '" readonly ></td>'
						+ '<td><input type="checkbox" id="chk_bundle_' + counter + '" name="chk_bundle" ></td>'
					+ '</tr>'
				);
			}
		}
		else
		{
			alert("Not Allowed.");
		}
	}

	function fn_deleteRow()
	{
		var bundle_mst_id=$("#bundle_mst_id").val();
		if(bundle_mst_id=="")
		{
			var numRow = $("#pre_tbl tbody tr").length;

			if(numRow!=1)
			{
				$('#pre_tbl tbody tr:last').remove();
				calculate_total();
			}
		}
		else
		{
			alert("Not Allowed.");
		}
	}

	function reset_pcs_per_bundle()
	{
		var bundle_mst_id=$("#bundle_mst_id").val();

		if(bundle_mst_id=="")
		{
			reset_form('bundlepre_1','','','txt_sl_no_1,1','$(\'#pre_tbl tbody tr:not(:first)\').remove();','txt_tot_pcs');
		}
		else
		{
			alert("Bundle Created. Refresh Not Allowed.");
		}
	}


	</script>
	  </head>
	  <body>
	  <div align="center" style="width:100%;" >
	  <form name="bundlepre_1"  id="bundlepre_1" autocomplete="off">
	  <? echo load_freeze_divs ("../../../",$permission,1); ?>
	   <fieldset style="width:<? echo $tbl_width+10; ?>px;">

	      <table cellspacing="2" cellpadding="0" border="0" class="" align="center" width="600">
	      		<tr><td id="form_caption" align="center" colspan="4"><strong style="font-size:14px">Knitting Qnty: <? echo $cutting_qnty; ?></strong></td></tr>
	            <tr>
	                <td width="120" class="must_entry_caption">Knit No.</td>
	                <td width="120">
	                    <input type="text" name="txt_cut_no" id="txt_cut_no" class="text_boxes_numeric" value="" style="size:80px;" />
	                    <input type="hidden" name="bundle_mst_id" id="bundle_mst_id" class="text_boxes_numeric" value="" style="size:80px;" />
	                </td>
	            </tr>
	            <tr>
	                <td width="120" class="must_entry_caption">Pcs Per Bundle</td>
	                <td width="120">
	                 	<input type="hidden" id="field_id" style="size:80px;" value="<? echo $fld_id; ?>" />
	                    <input type="hidden" id="row_id" style="size:80px;" value="<? echo $row_id; ?>" />
	                    <input type="hidden" id="rpt_data" style="size:80px;" value="<? echo $ext_data; ?>" />
	                    <input type="hidden" id="prev_pcs_per_bundle" style="size:80px;" value="" />

	                    <input type="text" name="txt_pcs_per_bendle" id="txt_pcs_per_bendle" class="text_boxes_numeric" style="size:80px;" value="" onBlur="calculate_bundle(this.value);" />
	                </td>
	                <td width="120">No. of Bundle</td>
	                <td width="120">
	                    <input type="text" name="txt_no_of_bendle" id="txt_no_of_bendle" class="text_boxes_numeric" style="size:80px;" value="" onChange="" readonly />
	                </td>
	            </tr>
	            <tr>
	                <td width="120">Batch / Lot No.</td>
	                <td width="120">
	                    <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" value="" style="size:80px;" />
	                </td>
	                <td width="120">Color Shade</td>
	                <td>
	                    <input type="text" name="txt_color_shade" id="txt_color_shade" class="text_boxes" value="" style="size:80px;" />
	                </td>
	            </tr>
	          </table>
	        </fieldset>
	        <br>
	        <fieldset style="width:<? echo $tbl_width+10; ?>px;">
	     	<legend>Bundle Details</legend>
	          <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="<? echo $tbl_width; ?>" class="rpt_table" id="pre_tbl">
	            <thead>
	                <th width="40" align="center">SL. No.</th>
	                <th width="80" align="center">Bundle No.</th>
	                <th width="60" align="center">Pcs Per Bundle</th>
	                <th width="120" align="center">Pcs Number Range</th>
	                <?
						if($bundle_no_creation==1)
						{
							echo '<th width="80" align="center">Custom Bundle No.</th><th width="120" align="center">Custom Gmts. Number</th>';
						}
					?>
	                <th width="100" align="center">Garment Color</th>
	                <th width="100" align="center">Garment Size</th>
	                <th align="center">Report &nbsp;<input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
	            </thead>
	            <tbody>
	                <tr>
	                    <td align="center">
	                        <input type="text" name="txt_sl_no_1" id="txt_sl_no_1" class="text_boxes_numeric" style="width:40px" value="1" readonly />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_bundle_no_1" id="txt_bundle_no_1" class="text_boxes_numeric" style="width:80px" readonly/>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_pcs_per_bundle_1" id="txt_pcs_per_bundle_1" class="text_boxes_numeric" style="width:60px" readonly />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_pcs_no_range_1" id="txt_pcs_no_range_1" class="text_boxes_numeric" style="width:120px" readonly />
	                    </td>
	                    <?
							if($bundle_no_creation==1)
							{
							?>
	                        	<td align="center">
	                                <input type="text" name="txt_custom_bundle_no_1" id="txt_bundle_no_1" class="text_boxes_numeric" style="width:80px" readonly/>
	                            </td>
	                            <td align="center">
	                            	<input type="text" name="txt_gmts_pcs_no_range_1" id="txt_gmts_pcs_no_range_1" class="text_boxes_numeric" style="width:120px" readonly />
	                            </td>
							<?
							}
						?>
	                    <td align="center">
	                        <input type="text" name="txt_gmt_color_1" id="txt_gmt_color_1" class="text_boxes" style="width:100px" readonly />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_gmt_size_1" id="txt_gmt_size_1" class="text_boxes" style="width:100px" readonly />
	                    </td>
	                    <td align="center"><input type="checkbox" name="chk_bundle"  id="chk_bundle_1"></td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td colspan="2">&nbsp;</td>
	                    <td width="60">
	                        <input type="text" name="txt_tot_pcs" id="txt_tot_pcs" class="text_boxes_numeric" value="" style="width:60px" readonly />
	                    </td>
	                    <td align="center">
	                    	<input type="button" name="add_row" class="formbutton" value="Add Row" id="add_row" onClick="fn_addRow();" style="width:100px" />
	                    </td>
	                    <td align="center">
	                    	<input type="button" name="remove_row" class="formbutton" value="Remove Row" id="remove_row" onClick="fn_deleteRow();" style="width:100px" />
	                    </td>
	                <tr>
	                <tr>
	                <td colspan="9" align="center">
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
							echo load_submit_buttons( $permission, "fnc_cutting_bundle_entry", 0,0,"reset_pcs_per_bundle();",1);
						?>
	                    <input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
	                    <input type="button" name="btn_bundle_report" class="formbutton" value="Bundle Ticket" id="btn_bundle_report" onClick="fnc_bundle_report();" style="width:130px" />&nbsp;&nbsp;
	                     <input type="button" name="btn_operation_sticker_report" class="formbutton" value="Operation Sticker" id="btn_operation_sticker_report" onClick="fnc_operation_sticker_report();" style="width:150px" />&nbsp;
	                     <input type="button" name="btn_operation_report" class="formbutton" value="Operation" id="btn_operation_report" onClick="fnc_operation_report();" style="width:100px"/>
	                </td>
	                </tr>
	            </tfoot>
	         </table>
	         </fieldset>
	         <table cellspacing="2" cellpadding="0" border="0" class="" align="center" width="<? echo $tbl_width; ?>"></table>
	  </form>
	  </div>
	  </body>
	  <script>
	  	get_php_form_data(<? echo $txt_mst_id; ?>+"**"+<? echo $hidden_po_break_down_id; ?>+"**"+document.getElementById('row_id').value, "populate_data_from_bundle", "cutting_update_controller" );
	 	calculate_bundle(document.getElementById('txt_pcs_per_bendle').value);
	  </script>
	  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	  </html>
	<?
	exit();
}

if($action=='populate_data_from_bundle')
{
	$data=explode("**",$data);
	$txt_mst_id=$data[0];
	$hidden_po_break_down_id=$data[1];
	$color_size_id=$data[2];

	$sql="select id, cut_no, pcs_per_bundle, no_of_bundle, batch_no, color_shade from pro_bundle_mst where pro_gmts_pro_id='$txt_mst_id' and po_break_down_id='$hidden_po_break_down_id' and color_size_id='$color_size_id'";

	$data_array=sql_select($sql);

	echo "reset_form('','','bundle_mst_id*txt_pcs_per_bendle*prev_pcs_per_bundle*txt_no_of_bendle*txt_batch_no*txt_color_shade','','');\n";

	foreach($data_array as $row)
	{
		echo "document.getElementById('bundle_mst_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_cut_no').value 					= '".$row[csf("cut_no")]."';\n";
		echo "document.getElementById('txt_pcs_per_bendle').value 			= '".$row[csf("pcs_per_bundle")]."';\n";
		echo "document.getElementById('prev_pcs_per_bundle').value 			= '".$row[csf("pcs_per_bundle")]."';\n";
		echo "document.getElementById('txt_no_of_bendle').value 			= '".$row[csf("no_of_bundle")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_color_shade').value 				= '".$row[csf("color_shade")]."';\n";
		echo "$('#txt_pcs_per_bendle').attr('disabled','disabled');\n";
		echo "set_button_status(1, permission, 'fnc_cutting_bundle_entry',1);\n";
		 
	}
	exit();
}

if ($action=="load_php_dtls_form")
{
	extract($_REQUEST);

	$data = explode("**",$data);

	$cuttiing_qnty=$data[0];
	$pcs_per_bund=$data[1];
	$fraction_bundle=$cuttiing_qnty%$pcs_per_bund;
	$total_bundle=ceil($cuttiing_qnty/$pcs_per_bund);
	$colsize=explode("__",$data[2]);
	$prev_pcs_per_bund=$data[3];
	$style_info=explode("__",$data[4]);
	$color_size_id=$data[5];
	$txt_mst_id=$data[6];
	$hidden_po_break_down_id=$data[7];

	$details_info="";

	$qry="select a.id,a.pcs_per_bundle,a.no_of_bundle,a.batch_no,a.color_shade,a.color_size_id,b.id as dtls_id,bundle_mst_id,b.pcs_per_bundle as pcs_bundle,b.pcs_range_start,b.pcs_range_end,b.color_size_id,b.bundle_bar_code, b.custom_bundle_no, b.custom_gmts_range_start, b.custom_gmts_range_end from pro_bundle_mst a,pro_bundle_dtls b, pro_garments_production_mst c where a.id=b.bundle_mst_id and a.color_size_id=".$color_size_id." and a.pro_gmts_pro_id='$txt_mst_id' and a.po_break_down_id='$hidden_po_break_down_id' and c.id=a.pro_gmts_pro_id order by b.id";
	$result=sql_select($qry);
	$count=count($result);

	/*$color_size_break_down_ids=return_field_value("group_concat(id) as id","wo_po_color_size_breakdown", "job_no_mst='".$style_info[4]."'","id");
	$last_bundle_range=return_field_value("max(pcs_range_end) as pcs_range_end"," pro_bundle_dtls", "color_size_id in (".$color_size_break_down_ids.")","pcs_range_end");

	$erange=$last_bundle_range;*/
	if(($prev_pcs_per_bund!=$pcs_per_bund) || $count==0 ) // New Insert
	{
		for($k=1; $k<=$total_bundle; $k++)
		{
			if($k==$total_bundle)
			{
				if($fraction_bundle>0) $pcs_per_bund=$fraction_bundle;
			}

			//$srange=$erange+1;
			//$erange=$srange+$pcs_per_bund-1;

			if($details_info!="")
				$details_info.="***".$pcs_per_bund."__".$colsize[0]."__".$colsize[1];
			else
				$details_info.=$pcs_per_bund."__".$colsize[0]."__".$colsize[1];
		?>
			<tr>
				<td align="center">
                <?
				if($k==$total_bundle)
				{
				?>
                	<input type="hidden" id="txt_details_row" name="txt_details_row" value="<? echo $details_info; ?>" style="width:150px;" class="text_boxes"/>
                <?
				}
				?>
					<input type="text" name="txt_sl_no_<? echo $k; ?>" id="txt_sl_no_<? echo $k; ?>" value="<? echo $k;?>" class="text_boxes_numeric" style="width:40px" readonly />
				</td>
				<td align="center">
					<input type="text" name="txt_bundle_no_<? echo $k; ?>" id="txt_bundle_no_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px" readonly/>
				</td>
				<td align="center">
					<input type="text" name="txt_pcs_per_bundle_<? echo $k; ?>" id="txt_pcs_per_bundle_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $pcs_per_bund; ?>" style="width:60px" onKeyUp="calculate_total();" />
				</td>
				<td align="center">
					<input type="text" name="txt_pcs_no_range_<? echo $k; ?>" id="txt_pcs_no_range_<? echo $k; ?>" value="<? //echo $srange."-".$erange; ?>" class="text_boxes_numeric" style="width:120px" readonly />
				</td>
                <?
					if($bundle_no_creation==1)
					{
					?>
						<td align="center">
							<input type="text" name="txt_custom_bundle_no_<? echo $k; ?>" id="txt_bundle_no_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px" readonly/>
						</td>
						<td align="center">
							<input type="text" name="txt_gmts_pcs_no_range_<? echo $k; ?>" id="txt_gmts_pcs_no_range_<? echo $k; ?>" class="text_boxes_numeric" style="width:120px" readonly />
						</td>
					<?
					}
				?>
				<td align="center">
					<input type="text" name="txt_gmt_color_<? echo $k; ?>" id="txt_gmt_color_<? echo $k; ?>" class="text_boxes" value="<? echo $colsize[0];?>" style="width:100px" readonly />
				</td>
				<td align="center">
					<input type="text" name="txt_gmt_size_<? echo $k; ?>" id="txt_gmt_size_<? echo $k; ?>" class="text_boxes" value="<? echo $colsize[1];?>" style="width:100px" readonly />
                    <input type="hidden" id="hiddenid_<? echo $r; ?>" name="hiddenid_<? echo $k; ?>" value="" style="width:15px;" class="text_boxes"/>
				</td>
                <td align="center"><input type="checkbox" name="chk_bundle" id="chk_bundle_<? echo $k; ?>"></td>
			</tr>
			<?
		}
	}
	else // From Update
	{
		$details_info=""; $r=1;
		foreach($result as $row)
		{
			if($details_info!="")
				$details_info.="***".$row[csf("pcs_bundle")]."__".$colsize[0]."__".$colsize[1]."__".$row[csf('pcs_range_start')]."__".$row[csf('pcs_range_end')]."__".$row[csf('dtls_id')];
			else
				$details_info.=$row[csf("pcs_bundle")]."__".$colsize[0]."__".$colsize[1]."__".$row[csf('pcs_range_start')]."__".$row[csf('pcs_range_end')]."__".$row[csf('dtls_id')];
			?>
			<tr>
				<td align="center">
				<?
				if($r==$count)
				{
				?>
					<input type="hidden" id="txt_details_row" name="txt_details_row" value="<? echo $details_info; ?>" style="width:15px;" class="text_boxes"/>
				<?
				}
				?>
					<input type="text" name="txt_sl_no_<? echo $r; ?>" id="txt_sl_no_<? echo $r; ?>" value="<? echo $r;?>" class="text_boxes_numeric" style="width:40px" readonly />
				</td>
				<td align="center">
					<input type="text" name="txt_bundle_no_<? echo $r; ?>" id="txt_bundle_no_<? echo $r; ?>" value="<? echo $row[csf("bundle_bar_code")]; ?>" class="text_boxes_numeric" style="width:80px" readonly/>
				</td>
				<td align="center">
					<input type="text" name="txt_pcs_per_bundle_<? echo $r; ?>" id="txt_pcs_per_bundle_<? echo $r; ?>" value="<? echo $row[csf("pcs_bundle")]; ?>" class="text_boxes_numeric"  style="width:60px" onKeyUp="calculate_total();" />
				</td>
				<td align="center">
					<input type="text" name="txt_pcs_no_range_<? echo $r; ?>" id="txt_pcs_no_range_<? echo $r; ?>" value="<? echo $row[csf('pcs_range_start')]."-".$row[csf('pcs_range_end')]; ?>" class="text_boxes_numeric" style="width:120px" readonly />
				</td>
                <?
					if($bundle_no_creation==1)
					{
					?>
						<td align="center">
							<input type="text" name="txt_custom_bundle_no_<? echo $r; ?>" id="txt_bundle_no_<? echo $r; ?>" value="<? echo $row[csf("custom_bundle_no")]; ?>" class="text_boxes_numeric" style="width:80px" readonly/>
						</td>
						<td align="center">
							<input type="text" name="txt_gmts_pcs_no_range_<? echo $r; ?>" id="txt_gmts_pcs_no_range_<? echo $r; ?>" value="<? echo $row[csf('custom_gmts_range_start')]."-".$row[csf('custom_gmts_range_end')]; ?>" class="text_boxes_numeric" style="width:120px" readonly />
						</td>
					<?
					}
				?>
				<td align="center">
					<input type="text" name="txt_gmt_color_<? echo $r; ?>" id="txt_gmt_color_<? echo $r; ?>" class="text_boxes" value="<? echo $colsize[0];?>" style="width:100px" readonly />
				</td>
				<td align="center">
					<input type="text" name="txt_gmt_size_<? echo $r; ?>" id="txt_gmt_size_<? echo $r; ?>" class="text_boxes" value="<? echo $colsize[1];?>" style="width:100px" readonly />
					<input type="hidden" id="hiddenid_<? echo $r; ?>" name="hiddenid_<? echo $r; ?>" value="<? echo $row[csf('dtls_id')]; ?>" style="width:15px;"/>
			</td>
			<td align="center"><input type="checkbox" name="chk_bundle" id="chk_bundle_<? echo $r; ?>"></td>
			</tr>
			<?
			$r++;
		}
	}
	exit();
}

if($action=="print_report_bundle_barcode")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('html_table.php');
	$data=explode("***",$data);
	$ext_data=explode("__",$data[1]);
	$cs_data=explode("__",$data[2]);
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	$pdf=new PDF_Code39();
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select bundle_mst_id,bundle_bar_code,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id from pro_bundle_dtls where id in ( $data[0] ) " );  //where id in ($data)
	$i=5; $j=5; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;
	foreach($color_sizeID_arr as $val){
		if($n==0) $product_dept_name=return_field_value("product_dept","wo_po_details_master a,  wo_po_color_size_breakdown b", "a.id=b.job_id and b.id=".$val[csf('color_size_id')]."","product_dept"); // $field_name, $table_name, $query_cond, $return_fld_name
		$n=1;
		if($br==18) { $pdf->AddPage(); $br=0; $i=5; $j=5; $k=0; }
		if( $k>0 && $k<3 ) { $i=$i+65; }
			$pdf->Code39($i, $j, $val[csf("bundle_bar_code")]);
			$pdf->Code39($i, $j+4,  "Buyer		:".$buyer_library[$ext_data[0]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+8,  "Style Ref	:".$ext_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+12, "Item	:".$garments_item[$ext_data[2]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16, "Color		:".$cs_data[0], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+20, "Size		:".$cs_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+20, $j+20, "Prod. Dept:".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

			$pdf->Code39($i, $j+24, "Cutt Date	:".$ext_data[3], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+35, $j+24, "Table No:", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+28, "Gmts. No:".$val[csf("pcs_range_start")]."-".$val[csf("pcs_range_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+30, $j+28, "Gmts. Qnty:".$val[csf("pcs_per_bundle")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			//$pdf->Code39($i, $j+40, "Fabrication:-".$val[csf("bundle_mst_id")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

		$k++;
		if($k==3){  $k=0; $i=5; $j=$j+48; }
		$br++;

	}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_report_operation_barcode")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');

	$data=explode("***",$data);
	$ext_data=explode("__",$data[1]);
	$cs_data=explode("__",$data[2]);

	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	if($db_type==0)
	{
		$dets_library=return_library_array( "select id,concat(pcs_per_bundle,'_',pcs_range_start,'_',pcs_range_end) as dets from pro_bundle_dtls", "id", "dets"  );
	}
	else
	{
		$dets_library=return_library_array( "select id,pcs_per_bundle || '_' || pcs_range_start || '_' || pcs_range_end as dets from pro_bundle_dtls", "id", "dets"  );
	}

	$pdf=new PDF_Code39();
	$pdf->AddPage();

	$color_sizeID_arr=sql_select( "select op_code,body_part_id,bundle_dtls from  pro_operation_bar_code a, ppl_gsd_entry_dtls b where b.id=a.gsd_dtls and bundle_dtls in ( $data[0] )  order by op_code,row_sequence_no,body_part_id");//  select  op_code from  pro_operation_bar_code  " );

	$i=5; $j=5; $k=0; $mn=0;
	$body_part_array=array();
	$bundle_code_array=array();
	$str=0;
	foreach($color_sizeID_arr as $val){
		 if($j>262) { $pdf->AddPage(); $mn=0;$i=5; $j=5; }
		//BUndle wise page break
		$tmp_code=substr($val[csf("op_code")],0,11);
		$pg++;
		if(!in_array($tmp_code,$bundle_code_array) && $str!=0) // body part wise grouping
		{
			$bundle_code_array[]=$tmp_code; $pdf->AddPage(); $mn=0;$i=5; $j=5;
			$body_part_array[$tmp_code][$val[csf("body_part_id")]]=$val[csf("body_part_id")];
			$pdf->Code39($i, $j, "Body part:".$body_part[$val[csf("body_part_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+43, $j, "Bundle No:".substr($val[csf("op_code")],0,11), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+82, $j, "Buyer:".$buyer_library[$ext_data[0]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+115, $j, "Style Ref:".$ext_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+148, $j, "Gmts. Size:".$cs_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;


			$j=$j+15;
			$i=5;
			$pg=0;
			$k=0;
		}
		if($body_part_array[$tmp_code][$val[csf("body_part_id")]]=="" && $pg!=0)
		{
			if( $k!=0 ) $j=$j+15; //else $j=5;
			$k=0; $i=5; $mn=0;
			$bundle_code_array[]=$tmp_code;
			$body_part_array[$tmp_code][$val[csf("body_part_id")]]=$val[csf("body_part_id")];
			$pdf->Code39($i, $j, "Body part:".$body_part[$val[csf("body_part_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+43, $j, "Bundle No:".substr($val[csf("op_code")],0,11), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+82, $j, "Buyer:".$buyer_library[$ext_data[0]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+115, $j, "Style Ref:".$ext_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+148, $j, "Gmts. Size:".$cs_data[1], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

			$j=$j+15;
			$i=5;
			//$k++;
		}
		if( $k>0 && $k<3 ) { $i=$i+60; }
		$pdf->Code39($i, $j, $val[csf("op_code")]);
		$pcs_ar=explode("_",$dets_library[$val[csf("bundle_dtls")]]);
		$pdf->Code39($i, $j+3, "Pcs:".$pcs_ar[0], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+13, $j+3, "G No:".$pcs_ar[1]."-".$pcs_ar[2], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$k++;

		if($k==3 && $mn!=0){  $k=0; $i=5;$j=$j+22;  }
		$mn++;
		$str++;
	}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	//$pdf->Output();
	$name = 'operation_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_report_operation")
{
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/config/lang/eng.php');
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/tcpdf.php');
	header ('Content-type: text/html; charset=utf-8');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'RA4', true, 'UTF-8', false);	// create new PDF document

	// set document information
	$pdf->SetCreator('Md. Fuad Shahriar');
	$pdf->SetAuthor('Md. Fuad Shahriar');
	$pdf->SetTitle('Logic ERP');
	$pdf->SetSubject('Goods Placement Carton Sticker');
	//$pdf->SetKeywords('Logic, HRM, Payroll, HRM & Payroll, ID Card');

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);	//set default monospaced font
	$pdf->SetMargins(12, 15, 8);								//set margins
	$pdf->SetAutoPageBreak(TRUE, 5);						//set auto page breaks
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);				//set image scale factor
	$pdf->setLanguageArray($l);								//set some language-dependent strings
	$pdf->SetFont('times', '', 10);

	foreach (glob(""."*.pdf") as $filename)
	{
		@unlink($filename);
	}

	$data=explode("***",$data);
	$ext_data=explode("__",$data[1]);
	$cs_data=explode("__",$data[2]);

	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	if($db_type==0)
	{
		//$dets_library=return_library_array( "select id,concat(pcs_per_bundle,'_',pcs_range_start,'_',pcs_range_end) as dets from pro_bundle_dtls", "id", "dets"  );
		$dets_library=return_library_array( "select a.id, concat(a.pcs_per_bundle,'_',a.custom_gmts_range_start,'_',a.custom_gmts_range_end,'_',a.custom_bundle_no,'_',b.cut_no,'_',b.pro_gmts_pro_id,'_',b.batch_no,'_',b.color_shade) as dets from pro_bundle_dtls a, pro_bundle_mst b where a.bundle_mst_id=b.id and a.id in ( $data[0] )", "id", "dets"  );
	}
	else
	{
		//$dets_library=return_library_array( "select id,pcs_per_bundle || '_' || pcs_range_start || '_' || pcs_range_end) as dets from pro_bundle_dtls", "id", "dets"  );
		$dets_library=return_library_array("select a.id, a.pcs_per_bundle || '_' || a.custom_gmts_range_start || '_' || a.custom_gmts_range_end || '_' || a.custom_bundle_no || '_' || b.cut_no || '_' || b.pro_gmts_pro_id || '_' || b.batch_no || '_' || b.color_shade as dets from pro_bundle_dtls a, pro_bundle_mst b where a.bundle_mst_id=b.id and a.id in ( $data[0] )","id","dets");
	}

	$operationArr = return_library_array("select id,operation_name from lib_sewing_operation_entry","id","operation_name");

	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select op_code,body_part_id,bundle_mst,bundle_dtls,total_smv,operation_id from pro_operation_bar_code a, ppl_gsd_entry_dtls b where b.id=a.gsd_dtls and bundle_dtls in ( $data[0] ) order by bundle_dtls, body_part_id,row_sequence_no");//  select  op_code from  pro_operation_bar_code  " );

	$body_part_array=array();
	$bundle_code_array=array();
	$html.='<table border="1" rules="all">';
	$i=1; $pg=1; $z=1;
	foreach($color_sizeID_arr as $val)
	{
		//BUndle wise page break
		$tmp_code=substr($val[csf("op_code")],0,11);

		$pcs_ar=explode("_",$dets_library[$val[csf("bundle_dtls")]]);

		if(!in_array($val[csf("bundle_dtls")],$bundle_code_array))
		{
			$bundle_code_array[]=$val[csf("bundle_dtls")];
			//$body_part_array[$tmp_code][$val[csf("body_part_id")]]=$val[csf("body_part_id")];

			if($z!=1)
			{
				$i=1; $pg=1;
				$html.= "</tr></table>";
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->AddPage();
				$html='<table border="1" rules="all">';
			}
			$z++;
		}

		if($body_part_array[$val[csf("bundle_dtls")]][$val[csf("body_part_id")]]=="") // body part wise grouping
		{
			if($pg!=1)
			{
				$html.='</tr>';
			}
			$body_part_array[$val[csf("bundle_dtls")]][$val[csf("body_part_id")]]=$val[csf("body_part_id")];
			$html.='<tr><td valign="middle" colspan="3" height="50" style="border-left:hidden; border-right:hidden;font-size:45px;">&nbsp;<br><br><b>'."&nbsp;&nbsp;Body part: ".$body_part[$val[csf("body_part_id")]]."; Bundle No: ".$pcs_ar[3]."; Bundle Qty: ".$pcs_ar[0]."; Buyer: ".$buyer_library[$ext_data[0]]."; Style Ref: ".$ext_data[1]."; Lot No: ".$pcs_ar[6]."; Color: ".$pcs_ar[7]."; Gmts. Size: ".$cs_data[1]."; Gmts. No: ".$pcs_ar[1].'-'.$pcs_ar[2]."; Cut No: ".$pcs_ar[4].'</b></td></tr><tr>';
			//$i=1;substr($val[csf("op_code")],0,11)
			$pg++;
		}

		$html.='<td align="center" width="230">&nbsp;&nbsp;&nbsp;&nbsp;'.$val[csf("op_code")]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".date("M-d",strtotime($ext_data[3])).'<br>'.$operationArr[$val[csf("operation_id")]].'; '.$val[csf("total_smv")].'; Qty: '.$pcs_ar[0].'; Gmts No: '.$pcs_ar[1].'-'.$pcs_ar[2].'; B/N: '.$pcs_ar[3].'</td>';//substr($val[csf("op_code")],0,11)

		if($i%3==0) {$html.='</tr><tr>';}
		$i++;
	}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	//$pdf->Output();</tr>
	$html.='</tr></table>';

	$pdf->writeHTML($html, true, false, true, false, '');
	$name = 'operation_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if ($action=="piece_rate_order_cheack")
{
	$ex_data=explode('**',$data);
	if($db_type==0)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1]  and a.rate_for=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	else if($db_type==2)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	//echo $piece_sql;
	$data_array=sql_select($piece_sql,0);
	if(count($data_array)>0)
	{
		$sys_number=""; $wo_qty=0;
		foreach($data_array as $row)
		{

			if ($sys_number=="") $sys_number=$row[csf('sys_number')]; else $sys_number.=','.$row[csf('sys_number')];
			$wo_qty+=$row[csf('wo_qty')];
		}
		echo "1"."_".$sys_number."_".$wo_qty;
	}
	else
	{
		echo "0_";
	}
	exit();

}
if($action == "cutting_reject_challan")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents("Cutting Reject Challan","../../../", 1, 1, $unicode);
    $cbo_source=$data[4];
    $cbo_cutting_company=$data[5];
    $cutting_date=$data[6];
    $cbo_item_name=$data[7];
    $order_no=$data[8];
    $buyer_name=$data[9];
    $job_no=$data[10];
    $style_no=$data[11];
    $remarks=$data[12];
    $challanNo=$data[13];
    $order_qty=$data[14];
    $mstIds =implode(",", explode("_", $data[1]));


    //echo $order_qty; die;
    //$location_library=return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[0]' order by location_name","id","location_name");
    $location=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
    $country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
    $company_library=return_library_array( "select id, company_name from lib_company where id = $data[0]", "id", "company_name"  );
    $buyer_library=return_library_array("select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 and id = $buyer_name","id","buyer_name");
    $size_library=return_library_array("select id,size_name from lib_size","id","size_name");
    $color_library=return_library_array("select id,color_name from lib_color","id","color_name");
    $color_type_sql=sql_select("SELECT max(color_type_id) as color_type_id  from pro_garments_production_dtls     where mst_id in($mstIds) and status_active=1 and production_type=1");
    $color_tp=$color_type[$color_type_sql[0][csf("color_type_id")]];
	
	   
    ?>
    <div style="width:800px; padding: 5px 10px">
    <table width="100%" cellspacing="0" align="center" style="margin-bottom: 5px;">
        <tr>
            <td colspan="4" align="center" style="font-size:20px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="4" align="center" style="font-size:14px;">
                <? foreach ($location as $result){
					?>
						<? echo $result[csf('plot_no')]; ?> &nbsp;
						<? echo $result[csf('level_no')]?>&nbsp;
						<? echo $result[csf('road_no')]; ?> &nbsp;
						<? echo $result[csf('block_no')];?> &nbsp;
						<? echo $result[csf('city')];?> &nbsp;
						<? echo $result[csf('zip_code')]; ?> &nbsp;
						<? echo $result[csf('province')];?> &nbsp;
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')];?> &nbsp;
						<? echo $result[csf('website')];
					}?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:18px;"><strong><? echo $title; ?></strong></td>
        </tr>
        <tr>
            <td width="20%"><strong>Cutting Source</strong></td><td width="30%">: <? echo $knitting_source[$cbo_source]; ?></td>
            <td width="20%"><strong>Company</strong></td><td width="30%">: <? echo $company_library[$cbo_cutting_company]; ?></td>
        </tr>
        <tr>
            <td><strong>Date</strong></td> <td >: <? echo change_date_format($cutting_date); ?></td>
            <td><strong>GMT Item</strong></td><td >: <? echo $garments_item[$cbo_item_name]; ?></td>
        </tr>
        <tr>
            <td><strong>Order No</strong></td> <td>: <? echo $order_no; ?></td>
            <td><strong>Buyer</strong></td><td>: <? echo $buyer_library[$buyer_name]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No</strong></td> <td>: <? echo $job_no; ?></td>
            <td><strong>Style Ref</strong></td><td>: <? echo $style_no; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qnty</strong></td> <td>: <? echo $order_qty; ?></td>
            <td><strong>Color Type</strong></td><td>: <? echo $color_tp; ?></td>
        </tr>
        <tr>
        	<td><strong>Chalan No.</strong></td> <td>: <? echo $challanNo; ?></td>
            <td><strong>Remarks</strong></td><td>: <? echo $remarks; ?></td>            
        </tr>
    </table>
        
        <?
		//$color_type_sql=sql_select("SELECT color_number_id,size_number_id,plan_cut_qnty as plan_cut_qnty  from wo_po_color_size_breakdown     where po_break_down_id in($order_no) and status_active=1 and production_type=1");

        $sql = "SELECT a.mst_id, a.color_size_break_down_id, a.reject_qty,a.production_qnty,b.po_break_down_id as po_id,b.color_number_id,b.plan_cut_qnty, b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b
        where a.color_size_break_down_id = b.id and a.mst_id in($mstIds)";
        // echo $sql;
        $nameArray = sql_select($sql);

        $headerArr = array();
		 
        foreach($nameArray as $row)
        {
            $po_Idarr[$row[csf("po_id")]] = $row[csf("po_id")];
			$headerArr[$row[csf("size_number_id")]] = $row[csf("size_number_id")];
			
            $datacolor[$row[csf("color_number_id")]] = $row[csf("color_number_id")];
            $dataArr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]['rjQnty'] += $row[csf("reject_qty")];
            $dataArr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]['cutQnty'] += $row[csf("production_qnty")];
        }
		
		 $sql_cut = "SELECT b.color_number_id,b.plan_cut_qnty, b.size_number_id
        from  wo_po_color_size_breakdown b
        where  b.po_break_down_id in(".implode(",",$po_Idarr).") and b.status_active=1";
        // echo $sql;
        $nameArrayCut = sql_select($sql_cut);

       
        foreach($nameArrayCut as $row)
        {
           $tot_plan_cut_qntyArr[$row[csf("color_number_id")]]+= $row[csf("plan_cut_qnty")];
		  // $tot_prod_qntyArr[$row[csf("color_number_id")]]+= $row[csf("production_qnty")];
		}
	$sql_cutProd = "SELECT a.mst_id, a.color_size_break_down_id, a.reject_qty,a.production_qnty,b.color_number_id,b.plan_cut_qnty, b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b
        where a.color_size_break_down_id = b.id and b.po_break_down_id in(".implode(",",$po_Idarr).") and a.production_type=1 and b.status_active=1 and a.status_active=1";
        // echo $sql;
        $nameArrayProd = sql_select($sql_cutProd);

       
        foreach($nameArrayProd as $row)
        {
           //$tot_plan_cut_qntyArr[$row[csf("color_number_id")]]+= $row[csf("plan_cut_qnty")];
		   $tot_prod_qntyArr[$row[csf("color_number_id")]]+= $row[csf("production_qnty")];
		}
		
        // echo "<prev>";
        // print_r($dataArr);

        ?>
        <div style="padding: 30px 0">
	        <table  cellspacing="0" cellpadding="0"  border="1" rules="all" class="rpt_table"  align="center">
	            <thead>
	            <tr>
	                <th width="40" align="center">Sl.</th>
	                <th width="120">Color/Size</th>
	                <?
	                foreach($headerArr as $row)
	                {
	                    ?>
	                <th width="90"><? echo $size_library[$row];?></th>
	                    <?
	                }
	                ?>
	                <th width="120">Color Total</th>
                    <th width="100">Order wise <br>Cutting Qnty</th>
                    <th width="100">Cum. Cutting Qnty</th>
                    <th width="100">Total Cutting<br> Production%</th>
                    
	            </tr>
	            </thead>
	            <tbody>
	                <?
	                $i = 1;$tot_tot_plan_cut_qnty=0;
	                foreach($datacolor as $color)
	                {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor;?>">
	                    <td align="center"><? echo $i;?></td>
	                    <td><? echo $color_library[$color];?></td>
	                    <?
						$title="Cum. Cutting Qnty*100/Order wise Cutting Qnty";
	                    $row_total = 0;
	                        foreach($headerArr as $size)
	                        {
		                        ?>
		                        <td align="center"><? echo ($type == 1) ? $dataArr[$color][$size]['rjQnty'] : $dataArr[$color][$size]['cutQnty'];?></td>
		                        <?
		                        $row_total += ($type == 1) ? $dataArr[$color][$size]['rjQnty'] : $dataArr[$color][$size]['cutQnty'];
		                        $grand_total += ($type == 1) ? $dataArr[$color][$size]['rjQnty'] : $dataArr[$color][$size]['cutQnty'];
		                        $dataSizeTotal[$size] += ($type == 1) ? $dataArr[$color][$size]['rjQnty'] : $dataArr[$color][$size]['cutQnty'];
	                        }
							$tot_plan_cut_qnty=$tot_plan_cut_qntyArr[$color];
							 $tot_prod_qnty=$tot_prod_qntyArr[$color];
							$cum_prod_percent=($tot_prod_qnty*100)/$tot_plan_cut_qnty;
	                    ?>
	                        <td align="center"><? echo $row_total;?></td>
                            <td align="center"><? echo $tot_plan_cut_qnty;?></td>
                             <td align="center"><? echo $tot_prod_qnty;?></td>
                            <td align="center" title="<? echo $title;?>"><? echo number_format($cum_prod_percent,2);?></td>
                           
	                </tr>
	                    <?
	                    $i++;
						$tot_tot_plan_cut_qnty+=$tot_plan_cut_qnty;
						$tot_tot_prod_qnty+=$tot_prod_qnty;
	                }
	                    ?>
	                <tr bgcolor="#EEE6E6">
	                    <td colspan="2" align="right"><b>Size Total</b></td>
	                    <?
	                        foreach($headerArr as $row)
	                        {
	                    ?>
	                    <td align="center"><? echo $dataSizeTotal[$row];?></td>
	                    <? } ?>
	                    <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        
	                </tr>
	                <tr bgcolor="#C8C7C7">
	                    <td colspan="2" align="right"><b>Color Grand Total</b></td>
	                    <?
	                        foreach($headerArr as $row)
	                        {
	                    ?>
	                    <td align="center"></td>
	                    <?}?>
	                    <td align="center"><? echo $grand_total;?></td>
                        <td align="center"><? echo $tot_tot_plan_cut_qnty;?></td>
                        <td align="center"><? echo $tot_tot_prod_qnty;?></td>
                        <td align="center"><? //echo $grand_total;?></td>
                        
	                </tr>
	            </tbody>
	        </table>
	        <? echo signature_table(118, $data[0], "700px");?>
    	</div>
    	<br clear="all">
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

	if(!count($color_type_arr))
	{
		$color_type_arr=$blank_array;
	}
	if(count(sql_select($sql))>1)
	{
		echo create_drop_down( "cbo_color_type", 122, $color_type_arr,"", 0, "Select Type", $selected,"");
	}
	else
	{
		echo create_drop_down( "cbo_color_type", 122, $color_type_arr,"", 0, "Select Type", $selected,"");
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