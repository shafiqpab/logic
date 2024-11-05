<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );

if (!function_exists('pre')) 
{
	function pre($arr)
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

 if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_body_part")
{
	$sql="select id,bundle_use_for from ppl_bundle_title where company_id=$data";
	echo create_drop_down( "cbo_body_part", 180, $sql,"id,bundle_use_for", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_company")
{
	$dataEx = explode("_",$data);
	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $dataEx[0], "$load_function");
	exit();
}



// if ($action=="load_drop_down_buyer")
// {
// 	$data=explode("_",$data);

// 	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
// 	else $load_function="";

// 	if($data[1]==1)
// 	{
// 		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
// 	}
// 	else
// 	{
// 		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
// 	}
// 	exit();
// }

//for emblishment type
if ($action=="load_drop_down_embl_type")
{
	$expData = explode("*", $data);

	if($db_type==0)
		$embel_name_cond="group_concat(c.emb_type) as emb_type";
	else if($db_type==2)
		$embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";

	$embl_type=return_field_value("".$embel_name_cond."","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in(".$expData[1].") and c.emb_name = ".$expData[0]."","emb_type");

	if($expData[0]==1)
		echo create_drop_down( "cbo_embel_type", 180, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" ,"", $embl_type );
	elseif($expData[0]==2)
		echo create_drop_down( "cbo_embel_type", 180, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected ,"","", $embl_type );
	elseif($expData[0]==3)
		echo create_drop_down( "cbo_embel_type", 180, $emblishment_wash_type,"", 1, "--- Select wash---", $selected,"","", $embl_type );
	elseif($expData[0]==4)
		echo create_drop_down( "cbo_embel_type", 180, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected,"","", $embl_type );
	elseif($expData[0]==5)
		echo create_drop_down( "cbo_embel_type", 180, $emblishment_gmts_type,"", 1, "--- Select---", $selected,"","", $embl_type );
	else
		echo create_drop_down( "cbo_embel_type", 180, $blank_array,"", 1, "--- Select---", $selected, "" );
	exit();
}


if ($action=="load_drop_down_floor")
{

 	echo create_drop_down( "cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (8) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	echo "$('#work_order_material_auto_receive').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}

	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	if($delivery_basis==3 || $delivery_basis==2) $delivery_basis=3; else $delivery_basis=1;
	echo "$('#delivery_basis').val(".$delivery_basis.");\n";


 	$workorder_material_autoreceive=return_field_value("item_show_in_detail","variable_setting_printing_prod ","company_name=$data and variable_list=7 and status_active=1 and is_deleted=0");

	echo "$('#work_order_material_auto_receive').val(".$workorder_material_autoreceive.");\n";

	echo "$('#wip_valuation_for_accounts').val(0);\n";
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$data and variable_list=76 and status_active=1 and is_deleted=0");
	echo "$('#wip_valuation_for_accounts').val($wip_valuation_for_accounts);\n";
	if($wip_valuation_for_accounts==1)
	{
		echo "$('#wip_valuation_for_accounts_button').show();\n";
	}

 	exit();
}

if ($action=="load_variable_settings_for_working_company")
{
	$sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");

	$working_company="";
 	foreach($sql_result as $row)
	{
		$working_company=$row[csf("working_company_mandatory")];
	}
	echo $working_company;

 	exit();
}
if($action=="show_cost_details")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs from pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and a.delivery_mst_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=2");// and a.embel_name=2
	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	$data_array = array();
	foreach ($sqlResult as $v)
	{
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fab_rate_per_pcs'] = $v['FAB_RATE_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}
	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="150">PO</th>
				<th width="150">Item</th>
				<th width="150">Color</th>
				<th width="100">Cost Per Pcs</th>
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
								<td align="right"><?=$v['cost_per_pcs'];?></td>
							</tr>
							<?
							$i++;
						}
					}
				}
				?>
			</tbody>
		</table>
	<?

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
			echo create_drop_down( "cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
		else
		{
			echo create_drop_down( "cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 180, "select comp.id, comp.company_name from lib_company comp where comp.is_deleted=0 and comp.status_active=1 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );

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
			echo create_drop_down( "cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
		else
		{	
			 $sql="SELECT a.id, a.supplier_name	FROM lib_supplier a, lib_supplier_party_type b	  WHERE a.id = b.supplier_id AND b.party_type = 23 AND a.status_active = 1	GROUP BY a.id, a.supplier_name	UNION ALL SELECT c.id, c.supplier_name	FROM lib_supplier c, lib_supplier_party_type b , pro_gmts_delivery_mst a   WHERE c.id = b.supplier_id AND b.party_type = 23 AND   c.id = a.serving_company AND c.status_active IN (1, 3) GROUP BY c.id, c.supplier_name 	ORDER BY supplier_name  ";
             
			echo create_drop_down( "cbo_emb_company", 180, "$sql","id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 180, "select comp.id, comp.company_name from lib_company comp where comp.is_deleted=0 and comp.status_active=1 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/print_embro_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );

	exit();
}



if ($action=="printing_order_popup")
{
	echo load_html_head_contents("Order Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no);
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,party_name)
	{
		load_drop_down( 'print_embro_delivery_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_company', 'buyer_td' );
		$('#cbo_company_name').attr('disabled',true);
	}


	// function fnc_load_party_order_popup(company,party_name)
	// {
	// 	load_drop_down( 'print_embro_delivery_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
	// 	$('#cbo_party_name').attr('disabled',true);
	// }

	function search_by(val,type)
	{
		if(type==1)
		{
			$('#txt_search_common').val('');
			if(val==1 || val==0) $('#search_td').html('W/O No');
			else if(val==2) $('#search_td').html('Job NO');
			else if(val==3) $('#search_td').html('Style Ref.');
			else if(val==4) $('#search_td').html('Buyer Po');
			else if(val==5) $('#search_td').html('Internal Ref');
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>
                        <th width="150">Company Name-xxxx</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_company_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                        <?
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po" ,5=>"Internal Ref ");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value+'_'+<?= $source;?>+'_'+<?= $emb_company;?>, 'create_printing_booking_search_list_view', 'search_div', 'print_embro_delivery_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" height="30" valign="middle"><?  echo load_month_buttons(); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
    </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_printing_booking_search_list_view")
{
	$data=explode('_',$data); 
	$search_type=$data[7];
	$source = $data[8]; 
	$supplier_id = $data[9]; 
	//echo $data[1]; die;
	//if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $company_cond=" and a.company_name='$data[0]'"; else { $company_cond=""; }

	if($data[1]=="")
	{
		if($data[2]=="" && $data[3]=="")
		{
			?>
			<div style="text-align: center;font-size: 18px;color: red">Please enter date range or search type field value.</div>
			<?
			die();
		}

	}

	// cbo_party_name cbo_company_name

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$data[4]";
		$year_cond2=" and YEAR(c.insert_date)=$data[4]";
	}
	else
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		$year_cond2=" and to_char(c.insert_date,'YYYY')=$data[4]";
	}
	$master_company=$data[0];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no_prefix_num = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
			if ($search_type==5) $internal_cond=" and b.grouping = '$data[1]'";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no_prefix_num like '$data[1]%'";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
 			if ($search_type==5) $internal_cond=" and b.grouping like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
			if ($search_type==5) $internal_cond=" and b.grouping like '%$data[1]' ";
		}
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
			if ($search_type==5) $internal_cond=" and b.grouping like '%$data[1]%' ";
		}
	}
	$outbound_cond = "";
	if ($source == 3) 
	{
		$outbound_cond .= $supplier_id ? " and a.supplier_id=$supplier_id " : "";
		$outbound_cond .= " and a.pay_mode in (1,2) ";
	}
	$po_ids='';

	/*if($data[1]!='')
	{
		if ($search_type==1) $woorderCond=" and c.booking_no_prefix_num = '$data[1]' ";
		if ($search_type==2) $jobCond=" and a.job_no_prefix_num = '$data[1]' ";
		if ($search_type==3) $styleCond=" and a.style_ref_no = '$data[1]' ";
		if ($search_type==4) $poCond=" and b.po_number = '$data[1]' ";
		//embellishment_job
		$attached_po_sql ="SELECT e.embellishment_job from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, subcon_ord_mst e  where a.id=b.job_id and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.id=e.order_id and c.booking_no=e.order_no and c.booking_type=6 and c.company_id='$data[0]' $jobCond $styleCond $poCond $woorderCond $year_cond2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0"; //die;
		// echo $attached_po_sql;die();
		$attached_po_res=sql_select($attached_po_sql);
		if(count($attached_po_res)<>0)
		{
			$printJOBs='';
			foreach ($attached_po_res as $row)
			{
				 $printJOBs .= $row[csf("embellishment_job")].',';
			}
			//echo $printJOBs; die;
			$printJOBs=implode(",",array_unique(explode(",",chop($printJOBs,','))));
			echo "Already has a Job no against this WO. <br> Job No: $printJOBs"; die;
		}

	}*/

	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	$po_id_arr = array();
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4) || ($internal_cond!="" && $search_type==5))
	{
		
		//echo "SELECT b.id from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $job_cond $style_cond $po_cond $company_cond $outbound_cond $internal_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; die;
		$sql = sql_select("SELECT b.id from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $job_cond $style_cond $po_cond $company_cond $outbound_cond $internal_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		
		foreach ($sql as $val)
		{
			$po_id_arr[$val['ID']] = $val['ID'];
		}
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date =""; 
		$sql = sql_select("SELECT b.po_break_down_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no $company $woorder_cond $outbound_cond $booking_date  and a.booking_type=6 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		foreach ($sql as $val)
		{
			$po_id_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		}
	} 
	// print_r($po_id_arr); die;
	if(count($po_id_arr)==0)
	{
		?>
		<div style="text-align: center;font-size: 18px;color: red">Data not found.</div>
		<?
		die();
	}
	
 	
	
	$po_idsCond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
	$po_idsCond2 = where_con_using_array($po_id_arr,0,"b.id");
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $po_idsCond2";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
	}
	unset($po_sql_res);
	

	$pre_cost_trims_arr=array();
	$pre_sql ="SELECT a.id, a.emb_name, a.emb_type, a.body_part_id  from wo_pre_cost_embe_cost_dtls a,wo_po_break_down b where a.job_id=b.job_id and  a.emb_name=1 and a.status_active=1 and a.is_deleted=0 $po_idsCond2";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_id_cond="listagg(b.gmt_item_id,',') within group (order by b.gmt_item_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	}

	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);




	if($db_type==0)
	{
		$sql= "SELECT $wo_year as year, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id, 1 as wo_type from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.status_active=1 and c.emb_name=1 and a.lock_another_process=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id
		UNION ALL
		SELECT $wo_year as year, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, '0' as pre_cost_trims_id, 0 as gmts_item, '0' as po_id, 2 as wo_type  from wo_non_ord_embl_booking_mst a, wo_non_ord_embl_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.entry_form_id=399 and a.status_active=1 and b.status_active=1 $booking_date $company $party_cond $woorder_cond $year_cond group by a.insert_date, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id";
	}
	else
	{
        $outbound_cond = "";
		if ($source !=3) // SOURCE NOT OUTBOUND SUBCONTRACT 
		{
			$outbound_cond = " AND a.lock_another_process=1 ";
		}
		$sql= "SELECT $wo_year as year, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id, 1 as wo_type from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.status_active=1 and c.emb_name=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond $outbound_cond group by a.insert_date, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id
		UNION ALL
		SELECT $wo_year as year, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, '0' as pre_cost_trims_id, $gmts_item_id_cond as gmts_item, '0' as po_id, 2 as wo_type  from wo_non_ord_embl_booking_mst a, wo_non_ord_embl_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.entry_form_id=399 and a.status_active=1 and b.status_active=1 $booking_date $company $party_cond $woorder_cond $year_cond group by a.insert_date, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id";


	}
	// echo $sql;
	
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1240" >
        <thead>
            <th width="30">SL</th>
            <th width="40">Year</th>
            <th width="100">W/O No</th>
            <th width="60">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="120">Buyer Po</th>
            <th width="120">Buyer Style</th>
            <th width="120">Buyer Job</th>
            <th width="120">Internal Ref</th>
            <th width="130">Gmts. Item</th>
            <th width="120">Body Part</th>
            <th width="100">Embl. Type</th>
            <th>Booking Type</th>
        </thead>
    </table>
    <div style="width:1240px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" id="list_view">
        <tbody>
            <?
            $i=1;
            foreach($data_array as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$expo_id=array_unique(explode(",",$row[csf('po_id')]));
				$buyer_name=""; $po_no=""; $buyer_style=""; $buyer_job="";$inter_ref="";
				foreach ($expo_id as $po_id)
				{
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
					if($po_no=="") $po_no=$buyer_po_arr[$po_id]['po']; else $po_no.=','.$buyer_po_arr[$po_id]['po'];
					if($inter_ref=="") $inter_ref=$buyer_po_arr[$po_id]['grouping']; else $inter_ref.=','.$buyer_po_arr[$po_id]['grouping'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}

				$buyer_name=implode(", ",array_unique(explode(",",$buyer_name)));
				$po_no=implode(", ",array_unique(explode(",",$po_no)));
				$inter_ref=implode(", ",array_unique(explode(",",$inter_ref)));
				$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(", ",array_unique(explode(",",$buyer_job)));

				$expre_cost_trims_id=array_unique(explode(",",$row[csf('pre_cost_trims_id')]));
				$body_part_name=""; $embl_name=""; $embl_type="";
				foreach ($expre_cost_trims_id as $pre_cost_id)
				{
					if($body_part_name=="") $body_part_name=$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']]; else $body_part_name.=','.$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']];

					if($embl_name=="") $embl_name=$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']]; else $embl_name.=','.$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']];

					if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==1) $emb_type=$emblishment_print_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==2) $emb_type=$emblishment_embroy_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==3) $emb_type=$emblishment_wash_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==4) $emb_type=$emblishment_spwork_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==5) $emb_type=$emblishment_gmts_type;

					if($embl_type=="") $embl_type=$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; else $embl_type.=','.$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']];
				}

				$body_part_name=implode(", ",array_unique(explode(",",$body_part_name)));
				$embl_name=implode(", ",array_unique(explode(",",$embl_name)));
				$embl_type=implode(", ",array_unique(explode(",",$embl_type)));

				$gmts_item_name="";
				$exgmts_item_id=explode(",",$row[csf('gmts_item')]);
				foreach($exgmts_item_id as $item_id)
				{
					if($gmts_item_name=="") $gmts_item_name=$garments_item[$item_id]; else $gmts_item_name.=','.$garments_item[$item_id];
				}
				$gmts_item_name=implode(", ",array_unique(explode(",",$gmts_item_name)));
				if($row[csf('wo_type')]==1) $booking_type='With Order'; else $booking_type='Without Order';
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('booking_type')].'_'.$row[csf('wo_type')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="40" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                    <td width="60"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_job; ?></td>
					 <td width="120" style="word-break:break-all"><? echo $inter_ref; ?></td>
                    <td width="130" style="word-break:break-all"><? echo $gmts_item_name; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $body_part_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $embl_type; ?></td>
                    <td style="word-break:break-all"><? echo $booking_type; ?></td>
                </tr>
				<?
                $i++;
            }
            ?>
        </tbody>
    </table>
	<?
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
        });

		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value="" />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php
				if($db_type==0)
				{
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
				}
				else
				{
					$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name",'id','buyer_name');
				}
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select name="txt_search_common" style="width:230px" class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}

		function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
                    		<td width="130">
							<?
							$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
  							?>
                    		</td>
                   			<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
            				</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td>
            		 		<td align="center">
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'print_embro_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_country_id">
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
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

 	$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.id = b.job_id and a.id = c.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature and c.emb_name=1
			$sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut order by b.id DESC";
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active =1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active =1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}

	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active =1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}

	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 group by po_break_down_id, item_number_id, country_id");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}

	?>
    <div style="width:1030px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Issue Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1030px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
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
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" >
							<td width="30" align="center"><?php echo $i; ?></td>
							<td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
							<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
							<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
							<td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
							<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
							<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
							<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                            <td width="80" align="right">
							<?php
								echo $total_cut_qty=$total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                             ?> &nbsp;
                           </td>
                           <td width="80" align="right">
							<?php
                             $balance=$po_qnty-$total_cut_qty;
                             echo $balance;
                             ?>&nbsp;
                           </td>
							<td><?php  echo $company_arr[$row[csf("company_name")]];?> </td>
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

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where a.job_id=b.id and b.id=c.job_id and a.id=$po_id and c.emb_name=$embel_name group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

  		$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=2 and embel_name='$embel_name' THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
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

if($action=="bundle_popup_rescan")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}


		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_bundle_nos').val( id );

		}

		function fnc_close()
		{
			document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
			//alert(document.getElementById('hidden_source_cond').value)
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			$('#hidden_source_cond').val( '' );
			selected_id = new Array();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked'), 'create_bundle_rescan_search_list_view', 'search_div', 'print_embro_delivery_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_bundle_rescan_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
	else{ $bundle_no = "%".trim($ex_data[2])."%";}
	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];

	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$curentscanned_bundle_arr[$bn]=$bn;
	}

	if($db_type==2) $not_null_bundle=" and bundle_no is not null";
	else $not_null_bundle=" and bundle_no!=''";

	//if( trim($ex_data[5])=='' ){echo "<h2 style='color:#D00; text-align:center;'>Please Select- Cut No </h2>";exit();}
	if (trim($ex_data[5]) == '')
    {
        echo "<h2 style='color:#D00; text-align:center;'><u>Please Select-Cut No</u></h2>";
        exit();
    }

	//$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$cutting_no= trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	$last_operation=gmt_production_validation_script( 91, 1,'',$cutting_no, $production_squence);





	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


	if ($cut_no != '')
	{
		if($is_exact=='true')
		 {
			$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
			$cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		 }
		else
		{

			 $cutCon = " and c.cut_no like '%".$cut_no."'";
			 $cutCon_a = " and b.cut_no like '%".$cut_no."'";
		}
    }
	if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";


	$sql_scan_bundle=sql_select(" select b.barcode_no,sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type  in (2,4) and a.embel_name in (1,0) and b.status_active=1 and b.is_deleted=0 $not_null_bundle  $cutCon_a  group by b.barcode_no");
	foreach($sql_scan_bundle as $val)
	{
		$scanned_bundle_qty_arr[$val[csf('barcode_no')]]=$val[csf('production_qnty')];
		$scanned_bundle_arr[]=$val[csf('barcode_no')];
	}


	//if( $is_print==1)



	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="50">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
        <?
			$i=1;
			$last_operation_string='';
			foreach($last_operation as  $item_id=>$operation_cond)
			{
				 if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
				else
				{
					$sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and f.id=e.job_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)  and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no'   $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
					foreach($sqld as $arows)
					{
						$reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
						$alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
						$spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
						$replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
					}
				}
				$sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and f.id=e.job_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' $item_id  $operation_conds $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, e.po_number order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
				// echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row)
				{
					//$bundle_qty=$row[csf('qty')]-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];
					 $bundle_qty = ($row[csf('qty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];


					if($bundle_qty>0 && in_array($row[csf('barcode_no')],$scanned_bundle_arr) && !in_array($row[csf('bundle_no')],$curentscanned_bundle_arr))
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="40"><? echo $i; ?>
								 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="50"><? echo $row[csf('cut_no')]; ?></td>
							<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $bundle_qty; ?>&nbsp;&nbsp;</td>
						</tr>
					<?
						$i++;
					}
				}
			}
        	?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}

if($action=="bundle_popup")
{

	extract($_REQUEST);

	//echo "string".$_SESSION["bundleNo"];
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

	if($printing_order_id!=""){$printing_order_id=$printing_order_id;}else{$printing_order_id=0;}
?>
	<script>
	 var bundleNo = localStorage.getItem("bundleNo");


		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}


		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_bundle_nos').val( id );

		}

		function fnc_close()
		{
			document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
			//alert(document.getElementById('hidden_source_cond').value)
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			$('#hidden_source_cond').val( '' );
			selected_id = new Array();
		}
		function show_list_view_new( data, action, div, path, extra_func )
		{
			//alert(data);
			if (!extra_func) var extra_func="";
			if (!data) var data="0";

			if( trim(data).length == 0 ) {
				document.getElementById(div).innerHTML = "";
				return;
			}
			/*var http = createObject();
			http.onreadystatechange = function() {
				if( http.readyState == 4 && http.status == 200 ) {
					//alert(div)
					document.getElementById(div).innerHTML = http.responseText;
					eval(extra_func);
					set_all_onclick();
				}
			}
			http.open( "POST", path+".php?data=" + trim( data ) + "&action=" + action, false );
			http.send();*/
			$.ajax({
				type        : 'POST',
				url         :  'print_embro_delivery_entry_controller.php',
				data 		: {data:data,action:action},
				success 	: function(data){

					document.getElementById(div).innerHTML = data;
				}
			});

		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond">
                        <input type="hidden" value="0" name="all_hidden_bundle" id="all_hidden_bundle">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
            		<td align="center">                                                             <!--removed show_list_view_new for setFilterGrid-->
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+$('#all_hidden_bundle').val()+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+<? echo $printing_order_id; ?>, 'create_bundle_search_list_view', 'search_div', 'print_embro_delivery_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	document.getElementById( 'all_hidden_bundle' ).value=bundleNo;
		//alert(document.getElementById( 'all_hidden_bundle' ).value);
</script>
</html>
<?
exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = trim($ex_data[0]);
	$company = $ex_data[1];
	$bundle_no = trim($ex_data[2]);
	/* if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
	else{ $bundle_no = "%".trim($ex_data[2])."%";} */
	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];

	if( trim($ex_data[5])==''){echo "<h2 style='color:#D00; text-align:center;'>Please Select- Cut No</h2>";exit();}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$cutting_no= trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	$last_operation=gmt_production_validation_script( 91, 1,'',$cutting_no, $production_squence);

 	// print_r($last_operation);
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');



	if ($cut_no != '')
	{
		if($is_exact=='true')
		{
			$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
			$cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		}
		else
		{
			$cutCon = " and c.cut_no='$cutting_no'";
			$cutCon_a = " and b.cut_no='$cutting_no'";
		}
    }

	if ($txt_order_no != '')
	{
		if($is_exact=='true')
		{
			$order_no_con = " and e.po_number = '$txt_order_no'";
		}
		else
		{
			$order_no_con = " and e.po_number like '%$txt_order_no%'";
		}
    }

	if ($bundle_no != '')
	{
		if($is_exact=='true')
		{
			$order_no_con = " and c.bundle_no = '$bundle_no'";
		}
		else
		{
			$order_no_con = " and c.bundle_no like '%$bundle_no%'";
		}
    }

	if($job_no!='')	$jobCon=" and e.job_no_mst like '%$job_no'";else $jobCon="";
	$scanned_bundle_arr=return_library_array( "SELECT b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type  in (2,4) and a.embel_name in (1,0) $cutCon_a  and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;
	}

	//if( $is_print==1)

	if(empty($last_operation))
	{
		if($precost_job!='')
		{
			echo "<h1>No Data Found. Please Check Pre-Costing.<h1/>";die;
		}
		else
		{
			echo "<h1>No Data Found. Please Check Order Entry.<h1/>";die;
		}
	}

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
        <?
			$i=1;
			$last_operation_string='';
			foreach($last_operation as  $item_id=>$operation_cond)
			{
				if($operation_cond!=0) $operation_conds= " and d.id in (".ltrim($operation_cond,",").") ";
				else
				{
					$sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)     $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
					foreach($sqld as $arows)
					{
						$reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
						$alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
						$spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
						$replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
					}
				}


				$sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_pre_cost_embe_cost_dtls f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.job_id and a.company_id=$company  $item_id $jobCon $cutCon  $operation_conds and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.emb_name=1
				group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
				// echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row)
				{
					$row[csf('qty')] = ($row[csf('qty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
					if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $row[csf('qty')]>0)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="40"><? echo $i; ?>
								 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="70"><? echo $row[csf('cut_no')]; ?></td>
							<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
						</tr>
					<?
						$i++;
					}
				}
			}
        	?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}



if($action=="challan_duplicate_check")
{
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg=1;

	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}
	$result=sql_select("SELECT a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=2 and a.status_active=1 and a.embel_name=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by a.sys_number,b.bundle_no");

	$datastr="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{
			$msg=2;
			$datastr=$row[csf('bundle_no')]."##".$row[csf('sys_number')];
		}
	}
	echo rtrim($msg)."_".rtrim($datastr);
	exit();
}


if($action=="populate_bundle_data_update")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=$ex_data[2];
	$bundle_nos="'".implode("','",$bundle)."'";
	//$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=2 and a.embel_name=1 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	$sql="SELECT c.id as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and d.job_id=f.id  and f.company_name='$ex_data[3]' and c.production_type=2 and c.delivery_mst_id=".$mst_id." and c.status_active=1 and c.is_deleted=0 order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc ";
	//echo $sql;die;
	$result = sql_select($sql);
	$count=count($result);
	$barcode_arr = array();
	foreach ($result as $v) 
	{
		$barcode_arr[$v['BARCODE_NO']] = $v['BARCODE_NO'];
	}
	$barcode_cond = where_con_using_array($barcode_arr,1,"b.barcode_no");
	$rcv_bundle_arr = return_library_array( "SELECT b.barcode_no,b.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.status_active=1 and a.is_deleted=0 and a.embel_name=1 and a.production_type=3 $barcode_cond",'barcode_no','barcode_no');
	$i=$count;
	foreach ($result as $row)
	{
		if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" || $mst_id[0]!="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$qty=($bundle_qty_arr[$row[csf('bundle_no')]]+$row[csf('replace_qty')])-($row[csf('raj_qty')]+$row[csf('alt_qty')]+$row[csf('spt_qty')]);
			$qty=$row[csf('production_qnty')];
			$status = ($rcv_bundle_arr[$row[csf('barcode_no')]]=="") ? 0 : 1;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>,<?=$status;?>);" />

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo $row[csf('is_rescan')]; ?>"/>
                </td>
			</tr>

		<?

        	$i--;
		}

	}

	exit();
}


if($action=="populate_bundle_data")
{

 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";
	$vscan=$ex_data[4];
	$source_cond=$ex_data[5];
	$company=$ex_data[3];
	$printing_order_id=$ex_data[6];

	//echo $printing_order_id; die;

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";

		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";

		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}




			 $sql =  sql_select("select item_show_in_detail,id from variable_setting_printing_prod where company_name = $company and variable_list =7 and is_deleted = 0 and status_active = 1");
			$variable_Printing_Material_Auto_Receive="";
			if(count($sql)>0)
			{
				$variable_Printing_Material_Auto_Receive=$sql[0][csf('item_show_in_detail')];
 				if($variable_Printing_Material_Auto_Receive==1)
				{
    					 $search_com_conds="and booking_mst_id=$printing_order_id";
 					     $attached_po_sql ="SELECT po_break_down_id from wo_booking_dtls where   status_active =1 and  is_deleted =0  $search_com_conds group by po_break_down_id";
					     $attached_po_res=sql_select($attached_po_sql);
						if(count($attached_po_res)<>0)
						{
							$po_break_down_ids='';
							foreach ($attached_po_res as $row)
							{
							   $po_break_down_ids .= $row[csf("po_break_down_id")].',';
							}
 						}
 						$po_break_down_ids=chop($po_break_down_ids,",")  ;
 						if($po_break_down_ids!="") $search_com_cond="and d.po_break_down_id in ($po_break_down_ids)";


 				}
			}


	//echo $po_break_down_ids;die;
	//echo $vscan;die;
	//$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=2 and a.embel_name=1 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in ($bundle_nos)",'bundle_no','bundle_no');
	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type in (2,4) and a.embel_name in (1,0) and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond",'bundle_no','bundle_no');

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}

	$last_operation=array();
	$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
	$last_operation=gmt_production_validation_script( 91, 1,'',$cutting_no, $production_squence);

	foreach($last_operation as  $item_id=>$operation_cond)
	{
		if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
		else
		{
			$sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and f.id=e.job_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id='$ex_data[3]' and c.production_type in (3) $bundle_nos_cond $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
			foreach($sqld as $arows)
			{
				$reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
				$alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
				$spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
				$replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
			}
		}

	  $sql="SELECT c.id as prodid, d.id as colorsizeid, e.id as po_id,f.id as job_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and d.job_id=f.id  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and e.status_active =1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond $operation_conds $item_id $search_com_cond group by d.id, e.id,f.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number,c.id order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
		//echo $sql; die;
		$result = sql_select($sql);
		$count=count($result);
		$i=$ex_data[1]+$count;
		foreach ($result as $row)
		{
			$job_id = $row['JOB_ID'];
		}

		$is_emb_in_budget = return_field_value("EMB_NAME","WO_PRE_COST_EMBE_COST_DTLS","JOB_ID=$job_id and emb_name=1 and is_deleted=0 and status_active=1");
		if(empty($is_emb_in_budget))
		{
			die();
		}

		foreach ($result as $row)
		{
			$row[csf('production_qnty')] = ($row[csf('production_qnty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+$spt_qty[$row[csf('bundle_no')]]);

			if( ($scanned_bundle_arr[$row[csf('bundle_no')]]=="" || $mst_id[0]!="" ) && $row[csf('production_qnty')]>0 )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$qty=$row[csf('production_qnty')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
					<td width="30"><? echo $i; ?></td>
					<td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
					<td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
					<td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
					<td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
					<td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
					<td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
					<td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
					<td id="button_1" align="center">
						<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>,0);" />

						<input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
						<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
						<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
						<input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
						<input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
						<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
						<input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
						<input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
						<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prodid')];?>"/>
						<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
					</td>
				</tr>
				<?

				$i--;
			}
		}
	}

	exit();
}

if($action=="populate_bundle_data_rescan")
{

 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";
	$vscan=$ex_data[4];
	$source_cond=$ex_data[5];
	//echo $bundle_nos;die;
	//echo $vscan;die;
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";

		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";

		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}


	$scanned_bundle_arr=return_library_array( "select b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type in (2,4) and a.embel_name in (1,0) and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond group by b.bundle_no",'bundle_no','production_qnty');

	//print_r($scanned_bundle_arr);die;
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}

	$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
	$last_operation=gmt_production_validation_script( 91, 1,'',$cutting_no, $production_squence);

	//print_r($last_operation);die;
	foreach($last_operation as  $item_id=>$operation_cond)
	{
		if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
		else
		{
			$sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and f.id=e.job_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id='$ex_data[3]'  and c.production_type in (3) $bundle_nos_cond $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
			foreach($sqld as $arows)
			{
				$reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
				$alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
				$spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
				$replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
			}
		}

	  $sql="SELECT c.id as prodidd, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id $bundle_nos_cond $operation_conds and c.status_active=1 and c.is_deleted=0  $item_id group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, e.po_number,c.barcode_no,c.id order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

	//echo $sql;

	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		//$qty=$row[csf('production_qnty')]-$scanned_bundle_arr[$row[csf('bundle_no')]];
		$qty = ($row[csf('production_qnty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_arr[$row[csf('bundle_no')]];

		if(($qty*1)>0 && $scanned_bundle_arr[$row[csf('bundle_no')]]!="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
				<td width="30"><? echo $i; ?></td>
				<td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
				<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
				<td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
				<td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
				<td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
				<td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
				<td id="button_1" align="center">
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>,0);" />

					<input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

					<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
					<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
					<input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
					<input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
					<input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
					<input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo  $row[csf('prodidd')]; ?>" />
					<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
				</td>
			</tr>
			<?
			$i--;
			}
		}
	}
	exit();
}

if($action=="bundle_nos")
{
	$bundle_nos=return_library_array( "select b.barcode_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=2 and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bundle_no","barcode_no");
$bundle_nos=implode(",",$bundle_nos);

	echo $bundle_nos;
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

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//#############################################################################################//
	//order wise - color level, color and size level

	//$variableSettings=2;

	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active =1 group by color_number_id";
		}
		else
		{
			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active =1 group by a.item_number_id, a.color_number_id";

		}

		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{

		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/



		$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		//print_r($color_size_qnty_array);

		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active =1 order by color_number_id, id";

		$colorResult = sql_select($sql);
	}
/*	else // by default color and size level
	{
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where  mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
	}
*/
	//$colorResult = sql_select($sql);
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
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
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];



			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
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

?>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="120" align="center">Country</th>
                <th width="80" align="center">Production Date</th>
                <th width="80" align="center">Production Qnty</th>
                <th width="150" align="center">Serving Company</th>
                <th width="120" align="center">Location</th>
                <th align="center">Challan No</th>
            </thead>
        </table>
    </div>
	<div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('production_quantity')];
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_issue_form_data','requires/print_embro_delivery_entry_controller');" >
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                    	<?php
                    		$source= $selectResult[csf('production_source')];
                            if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                            else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                     	?>
                    <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>
        </thead>
		<?
		$i=1;

		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active =1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right"><?php  echo $row[csf('order_qnty')]; ?></td>
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
	//production type=2 come from array
	$sqlResult =sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data' and production_type='2' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{
		//echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
  		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

			$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=2 and embel_name=".$result[csf('embel_name')]." THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}

		echo "get_php_form_data(".$result[csf('po_break_down_id')]."+'**'+".$result[csf("item_number_id")]."+'**'+".$result[csf("embel_name")]."+'**'+".$result[csf("country_id")].", 'populate_data_from_search_popup', 'requires/print_embro_delivery_entry_controller' );\n";

		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";

		echo "show_list_view('".$result[csf('po_break_down_id')]."','show_country_listview','list_view_country','requires/print_embro_delivery_entry_controller','');\n";

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

			$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
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

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active =1 group by color_number_id";
				}
				else
				{
					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active =1 group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active =1 order by color_number_id";

			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active =1 order by color_number_id";
			}

 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;$colorWiseTotal=0;
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
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];


					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';
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

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		//if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
		if(str_replace("'","",$txt_system_id)=="")
		{
			//$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'PER',0,date("Y",time()),0,0,2,$cbo_embel_name,0));

			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type, production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id, inserted_by, insert_date,remarks";
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",2,".$cbo_location.",".$delivery_basis.",".$cbo_embel_name.",".$cbo_embel_type.",".$cbo_source.",".$cbo_emb_company.",".$cbo_floor.",".$txt_organic.",".$txt_issue_date.",".$cbo_body_part.",".$cbo_working_company_name.",".$cbo_working_location.",".$user_id.",'".$pc_date_time."','".str_replace("'","",$txt_remarks)."')";
			$challan_no=(int)$new_sys_number[2];
			$txt_challan_no=$new_sys_number[0];
		}
		else
		{
			$mst_id=str_replace("'","",$txt_system_id);
			//$cbo_working_company_name=str_replace("'","",$cbo_working_company_name);
			$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
			$challan_no=(int) $txt_chal_no[3];

			$field_array_delivery="location_id*delivery_basis*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*body_part*working_company_id*working_location_id*updated_by*update_date*remarks";
			$data_array_delivery="".$cbo_location."*".$delivery_basis."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$cbo_emb_company."*".$cbo_floor."*".$txt_organic."*".$txt_issue_date."*".$cbo_body_part."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'*'".str_replace("'","",$txt_remarks)."'";

		}

		for($j=1;$j<=$tot_row;$j++)
            {
                $bundleCheck="barcodeNo_".$j;
                $is_rescan="isRescan_".$j;
                if($$is_rescan!=1)
                {
                  $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;

            }
            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }
            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=2 and  a.embel_name=1 and c.barcode_no  in ($bundle)  and c.production_type=2 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)";
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {

                $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
            }

			// ================== getting input qty =====================
			$sql="SELECT c.production_qnty,c.bundle_no from pro_garments_production_dtls c where  c.barcode_no  in ($bundle)  and c.production_type=1 and c.status_active=1 and c.is_deleted=0";
			$result = sql_select($sql);
			$prev_qty_arr = array();
			foreach ($result as $row)
			{
				$cutting_qc_qty_arr[trim($row[csf('bundle_no')])] += trim($row[csf('production_qnty')]);
			}

		if(str_replace("'","",$delivery_basis)==3)
		{
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,wo_order_id,wo_order_no,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();
			$poIdArr = array();
            $itemIdArr = array();
            $colorIdArr = array();
            $cutNoArr = array();
            $bundleNoArr = array();
			for($j=1;$j<=$tot_row;$j++)
			{
				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;


				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$checkRescan="isRescan_".$j;
				$qty="qty_".$j;
				if($duplicate_bundle[$$bundleNo]=='')
                {

					$bundleCutArr[$$bundleNo]=$$cutNo;
					$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId."**".$$colorId;
					$dtlsArr[$$bundleNo]=$$qty;
					$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]=$$checkRescan;
					$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
				}
                $poIdArr[$$orderId] = $$orderId;
                $itemIdArr[$$gmtsitemId] = $$gmtsitemId;
                $colorIdArr[$$colorId] = $$colorId;
                $cutNoArr[$$cutNo] = $$cutNo;
                $bundleNoArr[$$bundleNo] = $$bundleNo;
			}

            $poIds = implode(",",$poIdArr);
            $itemIds = implode(",",$itemIdArr);
            $colorIds = implode(",",$colorIdArr);
            $cutNos = "'".implode("','",array_filter($cutNoArr))."'";
            $emb_bundle = "'".implode("','",array_filter($bundleNoArr))."'";


			/* ======================================================================== /
			/							check variable setting							/
			========================================================================= */
			$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$cbo_company_name and variable_list=76 and status_active=1 and is_deleted=0");
			if($wip_valuation_for_accounts==1)
			{
				/* ================================= get fabric cost =================================== */
				$bundle_chk_arr=return_library_array( "SELECT b.bundle_no, b.bundle_no as bundle_nos from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.cut_no in($cutNos) and b.bundle_no in($emb_bundle) and b.production_type=2 and a.embel_name=1", "bundle_no", "bundle_nos" );

				$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3)  and c.po_break_down_id in($poIds) and c.item_number_id in($itemIds) and c.color_number_id in($colorIds) and b.bundle_no in($emb_bundle) order by a.production_type asc";// and b.cut_no in($cutNos)
				$res = sql_select($sql);
				$fab_cost_array = array();
				$x=0;
				foreach ($res as $v)
				{
					if($v['PRODUCTION_TYPE']==1)
					{
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
					}
					else if($v['PRODUCTION_TYPE']==3 && $v['EMBEL_NAME']==2)
					{
						if($x==0)
						{
							$fab_cost_array = array();
							$x++;
						}
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
					}
				}
				/* ================================== end fabric cost ========================================= */
			}

			//echo "10**";print $tot_row;die;


			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						/* $cost_of_fab = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cost_of_fab_per_pcs'];
						$cost_of_fab = number_format($cost_of_fab,$dec_place[3],'.','');

						$cutting_oh = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cut_oh_per_pcs'];
						$cutting_oh = number_format($cutting_oh,$dec_place[3],'.','');

						$cost_per_pcs = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cost_per_pcs'];
						$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */

						$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",2,".$sewing_production_variable.",'".$txt_remark."',".$cbo_floor.",".$hid_order_id.",".$txt_printing_order_no.",'".$cost_of_fab."','".$cutting_oh."','".$cost_per_pcs."',".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						//$id = $id+1;
					}
				}
			}

			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs";
			$error = false;
			foreach($dtlsArr as $bundle_no=>$qty)
			{
				if($cutting_qc_qty_arr[$bundle_no]<=$qty)
				{
					$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
					$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
					$cut_no=$bundleCutArr[$bundle_no];
					if($wip_valuation_for_accounts==1)
					{
						$cost_of_fab = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cost_of_fab_per_pcs'];
						$cost_of_fab = number_format($cost_of_fab,$dec_place[3],'.','');

						$cutting_oh = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cut_oh_per_pcs'];
						$cutting_oh = number_format($cutting_oh,$dec_place[3],'.','');

						// $cost_per_pcs = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cost_per_pcs'];
						// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');

						$prod_qty = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['prod_qty'];
						$amount = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['amount'];
						$cost_per_pcs = ($prod_qty) ? $amount/$prod_qty : 0;
						$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
						$total_cost_of_fabric = $cost_per_pcs*$qty;
						$total_cost_of_fabric = number_format($total_cost_of_fabric,$dec_place[3],'.','');
					}

					if($data_array_dtls!="") $data_array_dtls.=",";
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",2,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."','".$cost_of_fab."','".$cutting_oh."','".$cost_per_pcs."')";
					//$dtls_id = $dtls_id+1;
				}
				else
				{
					$error = true;
				}
			}

			if($error)
			{
				echo "11**Issue qty can not over than cutting qc qty.";die;
			}

  			//echo "10**insert into pro_gmts_delivery_mst (".$field_array_delivery.") values ".$data_array_delivery;die;
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}

			//echo "10**";
			// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;die;
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
			//echo "10**".$challanrID."**".$rID."**".$dtlsrID;die;

			if($db_type==0)
			{
				if($challanrID && $rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
		}
		else
		{
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
			$field_array1="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,wo_order_id,wo_order_no, inserted_by, insert_date";

			$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$txt_issue_qty.",2,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$hid_order_id.",".$txt_printing_order_no.",".$user_id.",'".$pc_date_time."')";
			//echo "10**";
			//echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
			// pro_garments_production_dtls table entry here ----------------------------------///
			$field_array="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty";
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
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
					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
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

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			//echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
			//echo "10**insert into pro_gmts_delivery_mst (".$field_array_delivery.") values ".$data_array_delivery;die;
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}

			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}


			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($rID && $challanrID && $dtlsrID)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;

		//check_table_status( $_SESSION['menu_id'],0);

	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
		$challan_no=(int) $txt_chal_no[3];

		$field_array_delivery="location_id*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*body_part*working_company_id*working_location_id*updated_by*update_date*remarks";
		$data_array_delivery="".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$cbo_emb_company."*".$cbo_floor."*".$txt_organic."*".$txt_issue_date."*".$cbo_body_part."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'*'".str_replace("'","",$txt_remarks)."'";

			for($j=1;$j<=$tot_row;$j++)
            {
                $bundleCheck="bundleNo_".$j;
                $is_rescan="isRescan_".$j;
                if($$is_rescan!=1)
                {
                  $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;

            }
            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=2 and  a.embel_name=1 and c.bundle_no  in ($bundle)  and c.production_type=2 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and (c.is_rescan=0 or c.is_rescan is null)";
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {

                $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
            }


		if(str_replace("'","",$delivery_basis)==3)
		{	//$con = connect();
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);

			$non_delete_arr=production_validation($mst_id,'3_1');
			$issue_data_arr=production_data($mst_id,'2_1');



			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,wo_order_id,wo_order_no,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();
			$poIdArr = array();
            $itemIdArr = array();
            $colorIdArr = array();
            $cutNoArr = array();
            $bundleNoArr = array();
            $coloSizeIDArr = array();
			for($j=1;$j<=$tot_row;$j++)
			{

				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$checkRescan="isRescan_".$j;
				$qty="qty_".$j;

				if($non_delete_arr[$$bundleNo]=="" && $duplicate_bundle[$$bundleNo]==''){
					$bundleCutArr[$$bundleNo]=$$cutNo;
					$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId."**".$$colorId;
					$dtlsArr[$$bundleNo]=$$qty;
					$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]=$$checkRescan;
					$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
				}
                $poIdArr[$$orderId] = $$orderId;
                $itemIdArr[$$gmtsitemId] = $$gmtsitemId;
                $colorIdArr[$$colorId] = $$colorId;
                $cutNoArr[$$cutNo] = $$cutNo;
                $bundleNoArr[$$bundleNo] = $$bundleNo;
                $coloSizeIDArr[$$colorSizeId] = $$colorId;

			}

            $poIds = implode(",",$poIdArr);
            $itemIds = implode(",",$itemIdArr);
            $colorIds = implode(",",$colorIdArr);
            $cutNos = "'".implode("','",array_filter($cutNoArr))."'";
            $emb_bundle = "'".implode("','",array_filter($bundleNoArr))."'";


			/* ======================================================================== /
			/							check variable setting							/
			========================================================================= */
			$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$cbo_company_name and variable_list=76 and status_active=1 and is_deleted=0");
			if($wip_valuation_for_accounts==1)
			{
				/* ================================= get fabric cost =================================== */
				$bundle_chk_arr=return_library_array( "SELECT b.bundle_no, b.bundle_no as bundle_nos from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id  and b.bundle_no in($emb_bundle) and b.production_type=2 and a.embel_name=2", "bundle_no", "bundle_nos" );

				$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3)  and c.po_break_down_id in($poIds) and c.item_number_id in($itemIds) and c.color_number_id in($colorIds) and b.bundle_no in($emb_bundle) order by a.production_type asc";// and b.cut_no in($cutNos)
				// echo "10**".$sql;die;
				// print_r($bundle_chk_arr);
				$res = sql_select($sql);
				$fab_cost_array = array();
				$x=0;
				foreach ($res as $v)
				{
					if($v['PRODUCTION_TYPE']==1)
					{
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
						$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
					}
					else if($v['PRODUCTION_TYPE']==3 && $v['EMBEL_NAME']==2)
					{
						if($x==0)
						{
							$fab_cost_array = array();
							$x++;
						}
						// if($bundle_chk_arr[$v['BUNDLE_NO']]=="")
						// {
							$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
							$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
							$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
							$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
							$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
						// }
					}
				}
				/* ================================== end fabric cost ========================================= */
			}
			// echo "<pre>";print_r($fab_cost_array);die;
			// Not Delete Data...............................start;
			foreach($non_delete_arr as $bi)
			{
				if($issue_data_arr[trim($bi)][csf('po_break_down_id')]!="" && $issue_data_arr[trim($bi)][csf('item_number_id')]!="" &&	 $issue_data_arr[trim($bi)][csf('country_id')]!="")
				{
					$bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
					$cutArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
					$mstArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
					$colorSizeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('po_break_down_id')]."**".$issue_data_arr[trim($bi)][csf('item_number_id')]."**".$issue_data_arr[trim($bi)][csf('country_id')]."**".$coloSizeIDArr[$issue_data_arr[trim($bi)][csf('color_size_break_down_id')]];
					$dtlsArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
					$dtlsArrColorSize[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('color_size_break_down_id')];
					$bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
					$bundleRescanArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('is_rescan')];
				}	

			}
			// Not Delete Data...............................end;


			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						/* $cost_of_fab = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cost_of_fab_per_pcs'];
						$cost_of_fab = number_format($cost_of_fab,$dec_place[3],'.','');

						$cutting_oh = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cut_oh_per_pcs'];
						$cutting_oh = number_format($cutting_oh,$dec_place[3],'.','');

						$cost_per_pcs = $fab_cost_array[$orderId][$gmtsItemId][$countryId]['cost_per_pcs'];
						$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */

						$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",2,".$sewing_production_variable.",'".$txt_remark."',".$cbo_floor.",".$hid_order_id.",".$txt_printing_order_no.",'".$cost_of_fab."','".$cutting_oh."','".$cost_per_pcs."',".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						//$id = $id+1;
					}
				}
			}

			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs";

			foreach($dtlsArr as $bundle_no=>$qty)
			{

				$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				//$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
				if($wip_valuation_for_accounts==1)
				{
					$cost_of_fab = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cost_of_fab_per_pcs'];
					$cost_of_fab = number_format($cost_of_fab,$dec_place[3],'.','');

					$cutting_oh = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cut_oh_per_pcs'];
					$cutting_oh = number_format($cutting_oh,$dec_place[3],'.','');

					// $cost_per_pcs = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['cost_per_pcs'];
					// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');

					$prod_qty = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['prod_qty'];
					$amount = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]]['amount'];
					$cost_per_pcs = ($prod_qty) ? $amount/$prod_qty : 0;
					$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
					$total_cost_of_fabric = $cost_per_pcs*$qty;
					// echo "10**".$amount."/".$prod_qty."=".$colorSizedData[0]."=".$colorSizedData[1]."=".$colorSizedData[3]."<br>";
					$total_cost_of_fabric = number_format($total_cost_of_fabric,$dec_place[3],'.','');
				}

				if($data_array_dtls!="") $data_array_dtls.=",";
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",2,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."','".$cost_of_fab."','".$cutting_oh."','".$cost_per_pcs."')";
				//$dtls_id = $dtls_id+1;
			}



			$delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$mst_id and production_type=2 and embel_name=1");
			$delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$mst_id and production_type=2");

			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);


		  //echo "10**".$challanrID .'&&'. $rID .'&&'. $dtlsrID .'&&'. $delete .'&&'. $delete_dtls;oci_rollback($con);die;
			//echo "10**".$dtlsrID;oci_rollback($con);die;


			if($db_type==0)
			{
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					oci_commit($con);
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);

				}
				else
				{
					oci_rollback($con);
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls."**".$data_array_mst;

				}
			}
		}
		else
		{
			// pro_garments_production_mst table data entry here
			$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*total_produced*yet_to_produced*wo_order_id*wo_order_no*updated_by*update_date";

			$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_issue_qty."*2*".$sewing_production_variable."*'".$challan_no."'*".$txt_remark."*".$cbo_floor."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$hid_order_id."*".$txt_printing_order_no."*".$user_id."*'".$pc_date_time."'";
			// pro_garments_production_dtls table data entry here
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
			{
				$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
				$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
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
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}

				if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
					$rowEx = explode("***",$colorIDvalue);
				//	$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}

				//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}//end cond

			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);//echo $rID;die;
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
			{
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}

			//release lock table
			//check_table_status( $_SESSION['menu_id'],0);

			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					oci_commit($con);
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$challanrID ."**". $rID ."**". $dtlsrID ."**". $delete ."**". $delete_dtls;
				}
			}
		}

		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$mst_id=str_replace("'","",$txt_system_id);
		$print_embro_bundle_rcv_sql = sql_select("SELECT count(*) as total from PRO_GMTS_DELIVERY_MST where ISSUE_CHALLAN_ID=$txt_system_id and status_active=1 and is_deleted=0 and PRODUCTION_TYPE=3 and EMBEL_NAME in(1,2)");
		$total_embl_bndl_rcv = $print_embro_bundle_rcv_sql[0]['TOTAL'];
		if($total_embl_bndl_rcv > 0){
			echo "111**".$mst_id."**".str_replace("'","",$txt_challan_no);
			die;
		}

		$print_material_rcv_sql = sql_select("SELECT count(*) as total from PRO_GMTS_DELIVERY_MST where ISSUE_CHALLAN_ID=$txt_system_id and PRINT_RECEIVE_STATUS=1 and status_active=1 and is_deleted=0 and PRODUCTION_TYPE=3 and EMBEL_NAME in(1,2)");
		$total_print_material_rcv = $print_material_rcv_sql[0]['TOTAL'];
		if($total_print_material_rcv > 0){
			echo "111**".$mst_id."**".str_replace("'","",$txt_challan_no);
			die;
		}

		$print_bundle_rcv_sql = sql_select("SELECT count(*) as total
		from PRO_GARMENTS_PRODUCTION_MST a, PRINTING_BUNDLE_RECEIVE_DTLS b
		where a.id=b.bundle_mst_id and a.delivery_mst_id=$txt_system_id and a.status_active=1 and a.is_deleted=0 and production_type=2 and embel_name in(1)");
		$total_print_bundle_rcv = $print_bundle_rcv_sql[0]['TOTAL'];
		if($total_print_bundle_rcv > 0){
			echo "111**".$mst_id."**".str_replace("'","",$txt_challan_no);
			die;
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$flag=1;
		$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"delivery_mst_id",$txt_system_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$dtlsrID=sql_update("pro_garments_production_dtls",$field_array,$data_array,"delivery_mst_id",$txt_system_id,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		// echo "9909**".$dtlsrID ."**". $rID ."**". $flag; die;
 		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>

	<script>

		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:1020px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:1020px;">
		<legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
                <thead>
				<tr>
               		<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                    <th>Print Type</th>
                    <th>Enter Challan No</th>
                    <th>Enter Cutting No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
                    	<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    	<?
                    	    asort($emblishment_print_type);
							echo create_drop_down( "cbo_embel_type", 160, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );
						?>
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_cutting_no" id="txt_cutting_no" />
                    </td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_challan_no').value+'_'+document.getElementById('cbo_embel_type').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_cutting_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_challan_search_list_view', 'search_div', 'print_embro_delivery_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>

                </tr>
           </table>
           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."";
	if($data[1]==0) $print_type_cond=""; else $print_type_cond=" and a.embel_type=$data[1]";
	$company_id =$data[2];
	// if($data[0]!="") $search_field_cond=" and a.sys_number like '$search_string'";
	// if($data[3]!="") $search_field_cond.=" and b.cut_no like '%".trim($data[3])."%'";
	$search_field_cond=""; $cutting_cond="";
	if($data[4]==1)
	{

		if (trim($data[0])!="") $search_field_cond=" and a.sys_number_prefix_num ='$data[0]'  "; //
		if (trim($data[3])!="") $cutting_cond=" and b.cut_no ='$data[3]'  "; //
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[0])!="") $search_field_cond=" and a.sys_number_prefix_num  like '%$data[0]%'  ";
		if (trim($data[3])!="") $cutting_cond=" and b.cut_no  like '%$data[3]%'  "; //;
	}
	else if($data[4]==2)
	{
		if (trim($data[0])!="") $search_field_cond=" and a.sys_number_prefix_num  like '$data[0]%'  ";
		if (trim($data[3])!="") $cutting_cond=" and b.cut_no  like '$data[3]%'  "; //
	}
	else if($data[4]==3)
	{
		if (trim($data[0])!="") $search_field_cond=" and a.sys_number_prefix_num  like '%$data[0]'  ";
		if (trim($data[3])!="") $cutting_cond=" and b.cut_no  like '%$data[3]'  "; //
	}

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql = "select a.id, a.delivery_date,  $year_field, a.sys_number_prefix_num, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,b.cut_no, sum(b.production_qnty) as total_production_qty from pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.production_type=2 and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=2 and a.company_id=$company_id $search_field_cond $cutting_cond $print_type_cond
 group by a.id, a.delivery_date, a.insert_date, a.sys_number_prefix_num, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,b.cut_no	 order by a.sys_number_prefix_num asc";
	// echo $sql;//die;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	//$cutting_no=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="60">Challan</th>
            <th width="50">Year</th>
            <th width="80">Delivery Date</th>
            <th width="80">Embel. Type</th>
            <th width="100">Source</th>
            <th width="110">Embel. Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th width="80">Cutting No</th>
            <th width="70">Challan Qty</th>
            <th>Organic</th>
        </thead>
	</table>
	<div style="width:1020px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";

                if($row[csf('production_source')]==1)
					$serv_comp=$company_arr[$row[csf('serving_company')]];
				else
					$serv_comp=$supllier_arr[$row[csf('serving_company')]];
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                    <td width="40"><? echo $i; ?></td>
                    <td width="60"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="80"><p><? echo $emblishment_print_type[$row[csf('embel_type')]]; ?></p></td>
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="80"><p><? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="70" align="right"><p><? echo $row[csf('total_production_qty')]; ?></p></td>
                    <td><p><? echo $row[csf('organic')]; ?></p></td>
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

if($action=='populate_data_from_challan_popup')
{

	/*echo "select a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,remarks,a.delivery_date,a.body_part,a.working_company_id,a.working_location_id,b.wo_order_id,b.wo_order_no from pro_gmts_delivery_mst a,pro_garments_production_mst b where  a.id=b.delivery_mst_id  and  a.id='$data'  and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; die;*/
	$data_array=sql_select("SELECT a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,a.remarks,a.delivery_date,a.body_part,a.working_company_id,a.working_location_id,b.wo_order_id,b.wo_order_no from pro_gmts_delivery_mst a,pro_garments_production_mst b where  a.id=b.delivery_mst_id  and  a.id='$data'  and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_source').val('".$row[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', ".$row[csf('production_source')].", 'load_drop_down_embro_issue_source_new', 'emb_company_td' );\n";

		echo "$('#cbo_emb_company').val('".$row[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location').val('".$row[csf('location_id')]."');\n";

		echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('location_id')]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
		echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
		echo "$('#cbo_floor').val('".$row[csf('floor_id')]."');\n";
		echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
		echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
		echo "$('#txt_organic').val('".$row[csf('organic')]."');\n";
		echo "$('#txt_remarks').val('".$row[csf('remarks')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";

		echo "$('#txt_printing_order_no').val('".$row[csf('wo_order_no')]."');\n";
		echo "$('#hid_order_id').val('".$row[csf('wo_order_id')]."');\n";

		echo "$('#txt_issue_date').val('".change_date_format($row[csf('delivery_date')])."');\n";

		echo "$('#cbo_body_part').val('".$row[csf('body_part')]."');\n";
		echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
		echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";

		echo "disable_enable_fields('cbo_company_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_printing_order_no',1);\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_issue_print_embroidery_entry',1,1);\n";
		exit();
	} 
}

if($action=="emblishment_issue_print")
{
	echo 'emblishment_issue_print';
	extract($_REQUEST);
	$data=explode('*',$data);
  
	$cbo_template_id=$data[5];


	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );

	$order_array=array();

	//$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and


	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}

	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,INSERTED_BY,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id, remarks from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";

	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];


 ?>
 <div style="width:930px;">
    <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Emb. Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No</strong></td> <td width="175px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Embel. Name </strong></td><td width="175px">: <? echo "Embellishment Issue for Bundle-".$emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="125"><strong>Emb. Type</strong></td><td width="175px"> :
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td> :
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else										   echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic </strong></td><td>: <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
       	 <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        <tr>
        	<td  colspan="4" id="barcode_img_id"></td>
        	 <td><strong>Remarks</strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
        <?

	/*  if($db_type==2) $group_concat="  listagg(cast(b.cut_no AS VARCHAR2(4000)),',') within group (order by b.cut_no) as cut_no" ;
	else if($db_type==0) $group_concat=" group_concat(b.cut_no) as cut_no" ; */

			$delivery_mst_id =$dataArray[0][csf('id')];
			if($data[2]==3)
			{

				if($db_type==0)
				{
					$sql="SELECT b.cut_no,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
					count(b.id) as 	num_of_bundle
					from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
					where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active =1
					and d.is_deleted=0 and b.bundle_no<>''
					group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id,b.cut_no
					order by a.po_break_down_id,d.color_number_id ";
				}
				else
				{
					$sql="SELECT b.cut_no,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
					count(b.id) as 	num_of_bundle
					from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
					where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active =1
					and d.is_deleted=0  and b.bundle_no is not null
					group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id,b.cut_no
					order by a.po_break_down_id,d.color_number_id ";
				}


			}
			else
			{
				if($db_type==0)
				{
					$sql="SELECT b.cut_no,sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id
					from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
					and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no!=''
					group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.cut_no ";

				}
				else
				{

					$sql="SELECT b.cut_no,sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id
					from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
					and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no is not null
					group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.cut_no ";
				}

			}

			$result=sql_select($sql);  
			$data_array = array();
			foreach ($result as $v) 
			{
				$data_array[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COLOR_NUMBER_ID']]['CUT_NO'][$v['CUT_NO']] 	 = $v['CUT_NO'];
				$data_array[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COLOR_NUMBER_ID']]['PRODUCTION_QNTY'] 		+= $v['PRODUCTION_QNTY'];
				$data_array[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COLOR_NUMBER_ID']]['NUM_OF_BUNDLE'] 		+= $v['NUM_OF_BUNDLE'];
			}
		?>


    <div style="width:100%;">
    <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style=" margin-top:20px; font: 12px tahoma;">
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Cutting Number</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Gmt. Qty</th>
            <? if($data[2]==3)  {  ?>
            <th align="center">No of Bundle</th>
            <? }   ?>
        </thead>
        <tbody>
			<?
            $i=1;
            $tot_qnty=array();
			foreach ($data_array as $po_id => $item_data) 
			{
				foreach ($item_data as $item_id => $country_data) 
				{
					foreach ($country_data as $country_id => $color_data) 
					{
						foreach($color_data as $color_id => $val)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$color_count=count($cid); 
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
								<td><? echo $i;  ?></td>
								<td align="center"><? echo $buyer_arr[$order_array[$po_id]['buyer_name']]; ?></td>
								<td align="center"><? echo $order_array[$po_id]['job_no']; ?></td>
								<td align="center"><? echo $order_array[$po_id]['style_ref']; ?></td>
								<td align="center"><? echo $order_array[$po_id]['style_des']; ?></td>
								<td align="center"><? echo $order_array[$po_id]['po_number']; ?></td>
								<td align="center"><? echo implode(',',$val['CUT_NO']); ?></td>
								<td align="center"><? echo $garments_item[$item_id]; ?></td>
								<td align="center"><? echo $country_library[$country_id]; ?></td>
								<td align="center"><? echo $color_library[$color_id];?></td>
								<td align="right"><?  echo $val['PRODUCTION_QNTY']; ?></td>
								<? if($data[2]==3) 
								{  ?>
								<td  align="center"> <?  echo $val['NUM_OF_BUNDLE']; ?></td>
								<? 
								$color_qty_arr[$color_id] += $val['PRODUCTION_QNTY'];
								$color_wise_bundle_no_arr[$color_id] += $val['NUM_OF_BUNDLE'];
								}   
								?>
								
							</tr>
							<?
							$total_bundle += $val['NUM_OF_BUNDLE'];
							$production_quantity += $val['PRODUCTION_QNTY'];
							$i++;
						}
					}
					
				}  
			} 
            ?>
        </tbody>
        <tr>
        <? if($data[3]==3) $colspan=8 ; else $colspan=7; ?>
            <td colspan="10" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
             <? if($data[2]==3)  {  ?>
            <td  align="center"> <?  echo $total_bundle; ?></td>
            <? }   ?>
        </tr>
    </table>
        <br clear="all">
            <table cellspacing="0" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Color Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Color</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($color_qty_arr as $color_id => $color_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $color_wise_bundle_no_arr[$color_id]; ?></td>
                        <td align="right"><? echo $color_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">Total =</td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
		 <?
		     echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
		    
             // signature_table(243, $data[0], "900px",'','',$user_library[$insert_by]);
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
 <script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();
}


if($action=="emblishment_issue_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	// echo $data[0];
	$cbo_template_id=$data[4];
	// echo $cbo_template_id;
	//  print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_array=array();
	//$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where  c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}

    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, remarks, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];

	?>
	<div style="width:900px;">
    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{

						 echo $result[csf('city')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Emb. Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
       <tr>
       	 <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        <tr>
       	 <td><strong>Remarks </strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>

        </tr>
        <tr>
        	<td  colspan="4" id="barcode_img_id"></td>

        </tr>

    </table><br />
        <?
			$delivery_mst_id =$dataArray[0][csf('id')];
			// base on Embel. Name
			if($data[2]==3)
			{
				if($db_type==0)
				{
					$sql="SELECT  sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
					count(b.id) as 	num_of_bundle,
					(select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
					(select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end
					from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
					where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active =1
					and d.is_deleted=0 and b.bundle_no <> '' and a.production_type=2
					group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
					order by a.po_break_down_id,d.color_number_id ";
				}
				else
				{


					$sql="SELECT  sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,b.cut_no,b.barcode_no,
					count(b.id) as 	num_of_bundle, sum(C.NUMBER_START) number_start, sum(C.NUMBER_END) number_end
					from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d, ppl_cut_lay_bundle  c
					where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.bundle_no = c.bundle_no and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
					and d.is_deleted=0  and b.bundle_no is not null and a.production_type=2
					group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id,b.cut_no,b.barcode_no
					order by b.cut_no,b.barcode_no asc ";
				}
			}
			else
			{
				if($db_type==0)
				{
					$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,b.size_number_id
					from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
					and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no!='' and a.production_type=2
					group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";
				}
				else
				{
					$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ,b.size_number_id
					from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
					and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no is not null and a.production_type=2
					group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";
				}

			}
			$result=sql_select($sql);
		?>


    <div style="width:100%;">
    <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="60" align="center">Bundle No</th>
            <th width="60" align="center">Job</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Size</th>
            <th width="60" align="center">RMG Qty</th>
            <th width="60" align="center">Gmt. Qty</th>
        </thead>
        <tbody>
			<?
            $size_qty_arr=array();
            $i=1;
            $tot_qnty=array();
                foreach($result as $val)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <!--<td align="center"><? //echo $country_library[$val[csf('country_id')]]; ?></td>-->
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]];?></td>
                        <td align="center"><? echo $size_library[$val[csf('size_number_id')]];?></td>
                        <td align="center"><? echo $val[csf('number_start')] . ' - ' . $val[csf('number_end')]; ?></td>
                        <td align="right"><?  echo $val[csf('production_qnty')]; ?></td>

                    </tr>
                    <?
					$production_quantity += $val[csf('production_qnty')];
                    $total_bundle += $val[csf('num_of_bundle')];
                    $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                    $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                    $i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>No. Of Bundle :<?  echo $total_bundle; ?></strong></td>
            <td colspan="8" align="right"><strong>Grand Total </strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
        </tr>
    </table>


 	<br>
            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Size Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Size</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($size_qty_arr as $size_id => $size_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $size_library[$size_id]; ?></td>
                        <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                        <td align="right"><? echo $size_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><strong>Total </strong></td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
		 <?
            // echo signature_table(243, $data[0], "500px");
			echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
	<?
	exit();
}

 
if($action=="emblishment_issue_print_9") //Print 9 .
{
	extract($_REQUEST);
	$data=explode('*',$data);

	// print_r ($data);die;

	$cbo_template_id=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]", "id", "bundle_use_for"  );
	$lib_country=return_library_array( "select id,country_name  from  lib_country", "id", "country_name"  );

	$lib_grouping=return_library_array( "select  job_no_mst,grouping  from  wo_po_break_down", "job_no_mst", "grouping"  );

	$sql_report_info="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by, production_source, serving_company, floor_id, organic, remarks, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	// echo $sql;
	$info=sql_select($sql_report_info);

	$summarySQL = "SELECT a.item_number_id, b.cut_no, c.po_number, d.buyer_name, d.style_ref_no, e.color_number_id, e.size_number_id, a.country_id, b.production_qnty, b.bundle_qty, b.bundle_no, b.id, c.id as order_id, a.wo_order_id

	from
		pro_garments_production_mst   a, 
		pro_garments_production_dtls  b,
		wo_po_color_size_breakdown    e,
		wo_po_break_down              c,
		wo_po_details_master          d,
		lib_size s
	where
		a.delivery_mst_id='$data[1]' and a.id = b.mst_id AND b.color_size_break_down_id = e.id AND e.po_break_down_id = c.id
		AND c.job_id = d.id and a.po_break_down_id=c.id and e.job_id=c.job_id and a.production_type=2 and a.embel_name=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and e.size_number_id = s.id
		order by s.sequence, length(b.bundle_no) asc, b.bundle_no asc
		";
	//echo $summarySQL; die;

	//data=1*13132*3*Bundle Issued to Print*1&action=emblishment_issue_print_9

	$summaryArray = sql_select($summarySQL);
	//echo "<pre>"; print_r($summaryArray);  die;
	$dataArray = array();
	$sizeArray = array();
	$bundleQty = array();
	$sizeBundleArray = array();
	$orderArray = array();
	$itemArray = array();
	$buyerArray = array();
	$woOrderArray = array();
	$cutNo = array();
	foreach($summaryArray as $summery){
		$cutNo[$summery[csf('cut_no')]] = $summery[csf('cut_no')];
	}
	$orderArrayComma = "'" . implode ( "', '", $cutNo ) . "'";
	$rmgSQL = "SELECT b.bundle_no, concat(b.number_start, concat('-', b.number_end)) as rmg_qty, a.batch_id, a.job_no, a.table_no, b.order_id, c.order_cut_no,b.PATTERN_NO
				FROM ppl_cut_lay_mst a, ppl_cut_lay_bundle b, ppl_cut_lay_dtls c
				WHERE a.id = b.mst_id and a.id = c.mst_id  and a.id = c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.cutting_no IN ($orderArrayComma)";
	//echo $rmgSQL; die;
	$rmgQty = sql_select($rmgSQL);
	$bundleRMG = array();
	$batchArray = array();
	$tableArray = array();
	$orderCutArray = array();
	$buyerJobArray = array();
	$intRefArray = array();
	$pattern_array = array();
	foreach($rmgQty as $rmg){
		$orderCutArray[$rmg[csf('order_id')]] = $rmg[csf('order_cut_no')];
		$bundleRMG[$rmg[csf('bundle_no')]] = $rmg[csf('rmg_qty')];
		$batchArray[$rmg[csf('batch_id')]] = $rmg[csf('batch_id')];
		$buyerJobArray[$rmg[csf('job_no')]] = $rmg[csf('job_no')];
		$intRefArray[$lib_grouping[$rmg[csf('job_no')]]] = $lib_grouping[$rmg[csf('job_no')]];
		$tableArray[$table_no_library[$rmg[csf('table_no')]]] = $table_no_library[$rmg[csf('table_no')]];
		$pattern_array[$rmg[csf('bundle_no')]] = $rmg[csf('PATTERN_NO')];
	}

	foreach($summaryArray as $summery){
		// $cutNo[$summery[csf('cut_no')]] = $summery[csf('cut_no')];
		$orderArray[$summery[csf('order_id')]] = $summery[csf('order_id')];
		$woOrderArray[$summery[csf('wo_order_id')]] = $summery[csf('wo_order_id')];
		$sizeArray[$summery[csf('size_number_id')]] = $summery[csf('size_number_id')];
		$itemArray[$garments_item[$summery[csf('item_number_id')]]] = $garments_item[$summery[csf('item_number_id')]];
		$buyerArray[$buyer_arr[$summery[csf('buyer_name')]]] = $buyer_arr[$summery[csf('buyer_name')]];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['cutting_no'] = $summery[csf('cut_no')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['po_number'] = $summery[csf('po_number')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['item_id'] = $summery[csf('item_number_id')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['style_ref'] = $summery[csf('style_ref_no')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['color'] = $summery[csf('color_number_id')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['country'] = $summery[csf('country_id')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['order_cut'] = $summery[csf('order_id')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['size_array'][$summery[csf('size_number_id')]] += $summery[csf('production_qnty')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['bundle_array'][$summery[csf('size_number_id')]] += 1;

		$sizeBundleArray[$summery[csf('cut_no')]][$summery[csf('size_number_id')]][$pattern_array[$summery[csf('bundle_no')]]][$summery[csf('bundle_no')]]['gmts_qty'] =  $summery[csf('production_qnty')];
	}
	// echo "<pre>"; print_r($sizeBundleArray);  die;
	//$orderArrayComma = implode(',', $cutNo);
	
  
	
	//echo "<pre>"; print_r($intRefArray); exit();
	$woOrderArrayComma = implode(',', $woOrderArray);
	$bookingSQL = "SELECT booking_no FROM wo_booking_mst WHERE ID IN ($woOrderArrayComma)";
	$bookingQty = sql_select($bookingSQL);
	$bookingArray = array();
	foreach($bookingQty as $booking){
		$bookingArray[$booking[csf('booking_no')]] = $booking[csf('booking_no')];
	}

	?> 

        <div style="width:1050px;">
		    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
				<tr>
					<td colspan="2" rowspan="3">
						<img src="../../<? echo $image_location; ?>" width="65">
					</td>
					<td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
					<td rowspan="3"><div id="barcode_img_id"></div></td>
				</tr>
				<tr class="form_caption">
					<td colspan="4" align="center" style="font-size:14px">
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								echo $result[csf('city')];
							}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="4" align="center" style="font-size:20px"><u><strong>Bundle Emb. Issue Challan/Gate Pass</strong></u></td>
				</tr>
				<tr>
					<td  width="90"><strong>Challan No</strong></td> <td width="140">: <?= $info[0][csf('sys_number')]; ?></td>
					<td width="110"><strong>Emb Type</strong></td><td width="175"> : <?= $emblishment_print_type[$info[0][csf('embel_type')]]; ?></td>
					<td width="105"><strong>Emb. Company</strong></td><td width="180">: <?=$info[0][csf('production_source')]==1 ?$company_library[$info[0][csf('serving_company')]] : $supplier_library[$info[0][csf('serving_company')]];?> </td>
					<td width="90"><strong>To location</strong></td><td width="90">: <?= $location_library[$info[0][csf('location_id')]]; ?></td>
				</tr>
				<tr>
					<td width="90"><strong>Batch no.</strong></td><td>: <?=implode(',', $batchArray); ?></td>
					<td width="110"><strong>GMT item</strong></td><td>: <?=implode(',', $itemArray); ?></td>
					<td width="105"><strong>Working company</strong></td><td>: <?=$company_library[$info[0][csf('working_company_id')]]; ?></td>
					<td  width="90"><strong>Wo no</strong></td><td width="130">: <?=implode(',', $bookingArray); ?></td>
				</tr>
				<tr>
					<td width="90"><strong>Body part.</strong></td><td>: <? echo $body_part_arr[$data[5]];?></td>
					<td width="110"><strong>Issue date</strong></td><td>: <?= $info[0][csf('delivery_date')]; ?></td>
					<td width="105"><strong>Buyer</strong></td><td>: <?=implode(',', $buyerArray); ?></td>
					<td  width="90"><strong>Table</strong></td><td>: <?=implode(',', $tableArray); ?></td>
					 
				</tr>
				<tr>
					<td><strong>Buyer Job</strong></td><td>: <?=implode(',', $buyerJobArray); ?></td>
					<td><strong>Int.Ref.</strong></td><td>: <?=implode(',', $intRefArray); ?></td>
					<td><strong>W. Location</strong></td><td>: <?=$location_library[$data[6]]; ?></td>
					<td><strong>Remarks</strong></td><td>: <?=$data[7]; ?></td> 	 	
				</tr>
			</table>


			<table class="details-view rpt_table" style="font-size:11px; margin-top:5px;" width="100%" cellspacing="0" cellpadding="0" border="1" align="left">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th class="outer" rowspan="2" width="10">SL</th>
						<th class="inner" rowspan="2" width="50" align="center">Cutting No.</th>
						<th class="inner" rowspan="2" width="70" align="center">PO Number</th>
						<th class="inner" rowspan="2" width="50" align="center">Item</th>
						<th class="inner" rowspan="2" width="70" align="center">Style Ref</th>
						<th class="inner" rowspan="2" width="70" align="center">Color</th>
						<th class="inner" rowspan="2" width="50" align="center">Country</th>
						<th class="inner" rowspan="2" width="30" align="center">Cut</th>
						<th class="inner" colspan="<?=count($sizeArray);?>" width="50" align="center">Size</th>
						<th class="inner"  width="50" align="center"></th>

					</tr>
					<tr>
						<?php
						foreach($sizeArray as $size)
						{

							?>
							<th class="inner" rowspan="2" width="50" align="center"><?=$size_library[$size]; ?></th>
							<?php
						}
							?>
						<th class="inner" rowspan="2" width="50" align="center">Total Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$i = 1;
						foreach($dataArray as $cutting_no => $cuttingWise)
						{
							$i % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
							$cuttingArray = explode("-", $cutting_no);
							$newCutting = ltrim($cuttingArray[2], 0);
							foreach($cuttingWise as $po_number => $poWise)
							{
								foreach($poWise as $country => $countryWise)
								{
									foreach($countryWise as $color => $colorWise)
									{
										//echo "<pre>"; print_r($colorWise); exit();
										?>
										<tr style="font-size:12px" bgcolor="<?=$bgcolor;?>">
											<td class="outer"><?=$i; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $newCutting; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $po_number; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$garments_item[$colorWise['item_id']];?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$colorWise['style_ref'];?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $color_library[$color] ; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $lib_country[$country]; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$orderCutArray[$colorWise['order_cut']]; ?></td>
											<?php
											foreach($colorWise['bundle_array'] as $key => $value)
											{
												$bundleQty[$key] += $value;
											}

											/* $total_qty = 0;
											foreach($colorWise['size_array'] as $key => $value)
											{
												$total_qty += $value;
												?>
													<td class="inner" width="24" align="center"><?=$value; ?></td>
												<?php
											}  */
											$total_qty = 0;
											foreach($sizeArray as $key => $value)
											{
												$total_qty += $colorWise['size_array'][$key];
												?>
													<td class="inner" width="24" align="center"><?=$colorWise['size_array'][$key]; ?></td>
												<?php
											}

												?>
												<td class="inner" align="center"><?=$total_qty;?></td>
										</tr>
										<?php
										$i++;
									}
									
								}
							}
							
						}
					?>
				</tbody>
				<tbody>
					<tr bgcolor="#DDDDDD" height="30">

						<td class="outer" colspan="8" align="right"><strong>Bundle QTY :</strong></td>
						<?php
						/* $total_bundle_qty = 0;
						foreach($bundleQty as $qty){
							$total_bundle_qty += $qty;
						 */
						$total_bundle_qty = 0;
						foreach($sizeArray as $key => $value){
							$total_bundle_qty += $bundleQty[$key];

						?>
						<td class="inner" width="24" align="center"><? //echo $bundleQty[$key]; ?></td>
						<?php } ?>
						<td class="inner" align="center"><?//=$total_bundle_qty; ?></td>
					</tr>
				</tbody>
			</table>
			<?php
				//$item_segment = 53;
				$item_segment = 51;
				$current_row = 1;
				$current_cols = 1;
				$table_data = '';
				$end_table = 0;
				$first = 1;

				$grand_total = 0;
				$serial = 1;
				foreach($sizeBundleArray as $cut_no => $cutWise)
				{
					foreach($cutWise as $size => $sizeWise)
					{
						$size_total = 0;
						foreach($sizeWise as $pattern => $patternData)
						{
							$sl=1;
							$pattern_total=0;
							foreach($patternData as $bundle => $r)
							{
								$serial % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
								$bundleArray = explode('-', $bundle);
								if(!empty($bundleArray[4])){
									$newBundle = $bundleArray[2]."-".$bundleArray[3]."-".$bundleArray[4];
								}else{
									$newBundle = $bundleArray[2]."-".$bundleArray[3];
								}
								
								if($current_row ==1)
								{
									$table_data .= '
									<table class="details-view rpt_table" style="font-size:15px; margin-top:5px; margin-left: 20px;"  cellspacing="0" cellpadding="0" border="1" align="left" width="470">
									<thead bgcolor="#dddddd" align="center">
										<tr>
											<th class="outer" width="30" align="center">SL</th>
											<th class="inner" width="130" align="center">Size</th>
											<th class="inner" width="10" align="center">Ptn</th>
											<th class="inner" width="100" align="center">Bundle No.</th>
											<th class="inner" width="40" align="center"><p class="checkmark">&#10003;</p></th>
											<th class="inner" width="40" align="center">Gmt. Qty </th>
											<th class="inner" width="50" align="center">RMG Qty</th>
											<th class="inner" width="50" align="center">(+/-)</th>

										</tr>
									</thead>
									<tbody>';
									$first++;
								}

								$table_data .= '<tr bgcolor='.$bgcolor.'>
														<td class="outer"  width="30" align="center">'.$sl.'</td>
														<td class="inner"  width="130" align="center">'.$size_library[$size].'</td>
														<td class="inner"  width="10" align="center">'.$pattern.'</td>
														<td class="inner"  width="100" align="center">'.$newBundle.'</td>
														<td class="inner"  width="40" align="center"></td>
														<td class="inner"  width="40" align="center">'.$r['gmts_qty'].'</td>
														<td class="inner"  width="50" align="center">'.$bundleRMG[$bundle].'</td>
														<td class="inner"  width="50" align="center"></td>
													</tr>';
								$serial++;
								$sl++;
								$pattern_total++;
								$size_total += $r['gmts_qty'];
								if ($current_row == $item_segment)
								{
									// echo $current_row .'=='. $item_segment."<br>";
									$current_row = 1;
									$table_data .= '</tbody></table>';

									if($current_cols == 2)
									{
										$end_table = 1;
										$table_data .= '<p style="page-break-after: always;"></p>';
										//$table_data .= '<div class="pagebreak"></div><br clear="all">';
										$current_cols = 1;
										//$item_segment = 67;
										$item_segment = 67;

									}
									else
									{
										$current_cols++;
									}

								}
								else
								{
									$current_row++;
								}

							}
							
							$table_data .= '<tr bgcolor="#DDDDDD">
											<td class="inner" colspan="2" align="right">Bundle Qty</td>
											<td class="inner" align="center">'.$pattern_total.'</td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
										</tr>';
						}//pattern end
						$grand_total += $size_total;
						$table_data .= '<tr bgcolor="#DDDDDD">
											<td class="inner" colspan="5" align="right">Size Total</td>
											<td class="inner"  width="40" align="center">'.$size_total.'</td>
											<td class="inner"  width="60" align="center"></td>
											<td class="inner"  width="40" align="center"></td>
										</tr>';
						
					}
				}
				$table_data .= '<tfoot>
									<tr bgcolor="#DDDDDD">
										<th class="inner" colspan="5" align="right">Grand Total</th>
										<th class="inner"  width="40" align="center">'.$grand_total.'</th>
										<th class="inner"  width="60" align="center"></th>
										<th class="inner"  width="40" align="center"></th>
									</tr>
								</tfoot></table>';
				echo $table_data;
				?>
			<br clear="all">
			<?php 
			//  echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
			 //echo signature_table(243, $data[0], "900px","","10"); 
			?>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode( valuess ){
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output:renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize:5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};

					value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $info[0][csf('sys_number')]; ?>');
		</script>
	<?
	exit();
}



if($action=="emblishment_issue_print_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r ($data);
	$cbo_template_id=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	if($db_type==2) $group_concat="  listagg(cast(b.cut_no AS VARCHAR2(4000)),',') within group (order by b.cut_no) as cut_no" ;
	else if($db_type==0) $group_concat=" group_concat(b.cut_no) as cut_no" ;


	// base on Embel. Name
	if($data[2]==3)
	{
		if($db_type==0)
		{
			$sql="SELECT  sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
			count(b.id) as 	num_of_bundle,
			(select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
			(select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end,
			e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			from wo_po_details_master e, wo_po_break_down f, pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
			where a.delivery_mst_id ='$data[1]' and e.id=f.job_id and f.id=a.po_break_down_id and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active =1
			and d.is_deleted=0 and b.bundle_no <> ''
			group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id, e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			order by a.po_break_down_id,d.color_number_id ";
		}
		else
		{
			$sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
			count(b.id) as 	num_of_bundle, 
			e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			from wo_po_details_master e, wo_po_break_down f, pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
			where a.delivery_mst_id ='$data[1]' and e.id=f.job_id and f.id=a.po_break_down_id and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active =1 and b.production_type=2
			and d.is_deleted=0  and b.bundle_no is not null
			group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id,d.color_order,d.size_order, e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			order by a.po_break_down_id,d.color_number_id,d.color_order,d.size_order ";
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,b.size_number_id,
			 e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			from wo_po_details_master e, wo_po_break_down f, pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and e.id=f.job_id and f.id=c.po_break_down_id
			and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no!=''
			group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity ";
		}
		else
		{
			$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ,b.size_number_id,
			e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity
			from wo_po_details_master e, wo_po_break_down f, pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and e.id=f.job_id and f.id=c.po_break_down_id and c.id=a.mst_id
			and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.bundle_no is not null
			group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, d.color_order,d.size_order, e.job_no, e.buyer_name,e.style_ref_no,e.style_description, f.id, f.po_number, f.po_quantity order by d.color_order,d.size_order ";
		}

	}
	//echo $sql;die;
	$result=sql_select($sql);
	$rows=array();
	$orderIdArray = array();
	$cutNoArray = array();
	foreach($result as $val)
	{
		$rows[$buyer_arr[$val[csf('buyer_name')]]] [$val[csf('job_no')]] [$val[csf('style_ref_no')]] [$val[csf('style_description')]] [$val[csf('po_number')]] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]] [$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['val']+=$val[csf('production_qnty')];

		$rows[$buyer_arr[$val[csf('buyer_name')]]] [$val[csf('job_no')]] [$val[csf('style_ref_no')]] [$val[csf('style_description')]] [$val[csf('po_number')]] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]] [$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['count']++;


		$size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
		$size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;

		$orderIdArray[$val[csf('id')]] = $val[csf('id')];
		$cutNoArray[$val[csf('cut_no')]] = $val[csf('cut_no')];
	}
	/* print_r($cutNoArray);
	die; */
	unset($result);

	$orderIdArrayComma = "'" . implode ( "', '", $cutNoArray ) . "'"; //implode(',', $cutNoArray); //echo $orderIdArrayComma ; exit();
	$tableSQL = "SELECT a.batch_id, a.table_no, c.order_cut_no
				FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls c, ppl_cut_lay_bundle d
				WHERE a.id = c.mst_id and a.id=d.mst_id and a.cutting_no IN ($orderIdArrayComma)";
	//echo $tableSQL; die;
	$tableResults = sql_select($tableSQL);
	$batchArray = array();
	$tableArray = array();
	$orderCutArray = array();
	foreach($tableResults as $val){
		$orderCutArray[$val[csf('order_cut_no')]] = $val[csf('order_cut_no')];
		$batchArray[$val[csf('batch_id')]] = $val[csf('batch_id')];
		$tableArray[$table_no_library[$val[csf('table_no')]]] = $table_no_library[$val[csf('table_no')]];
	}

	//$order_array=array();
	//$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where  c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
	/* $order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	} */

	$sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];

	?>
	<div style="width:900px;">
    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{

						 echo $result[csf('city')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Emb. Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
       <tr>
       	 	<td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
		<tr>
			<td><strong>Batch </strong></td><td> : <?=implode(',', $batchArray);?></td>
        	<td><strong>Order Cut</strong></td><td> : <?=implode(',', $orderCutArray);?></td>
            <td><strong>Table</strong></td><td> : <?=implode(',', $tableArray);?></td>
        </tr>
        <tr>
        	<td  colspan="2" id="barcode_img_id"></td>

        </tr>

    </table><br />
    <div style="width:100%;">
    <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Cutting Number</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Gmt. Qty</th>
            <? if($data[2]==3)  {  ?>
            <th align="center">No of Bundle</th>
            <? }   ?>
        </thead>
        <tbody>
			<?
          //  $size_qty_arr=array();
            $i=1;
            $tot_qnty=array();
			 foreach($rows as $buyer=>$brows)
             {
				 foreach($brows as $job=>$jrows)
				 {
					 foreach($jrows as $styleref=>$srrows)
					 {
						 foreach($srrows as $styledes=>$sdrows)
						 {
							 foreach($sdrows as $order=>$orows)
							 {
								 foreach($orows as $cutn=>$ctrows)
								 {
									foreach($ctrows as $gmtitm=>$girows)
									 {
										foreach($girows as $Country=>$cntrows)
										 {
											 foreach($cntrows as $color=>$cdata)
											 {
											  	if ($i%2==0)
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";
												$color_count=count($cid);
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
													<td><? echo $i;  ?></td>
													<td align="center"><? echo $buyer; ?></td>
													<td align="center"><? echo $job; ?></td>
													<td align="center"><? echo $styleref; ?></td>
													<td align="center"><? echo $styledes; ?></td>
													<td align="center"><? echo $order; ?></td>
													<td align="center"><? echo $cutn; ?></td>
													<td align="center"><? echo $gmtitm; ?></td>
													<td align="center"><? echo $Country; ?></td>
													<td align="center"><? echo $color;?></td>
													<td align="right"><?  echo $cdata['val']; ?></td>
													<? if($data[2]==3)
													 {  ?>
													<td  align="center"> <?  echo $cdata['count']; ?></td>
													<?
													$color_qty_arr[$color] += $cdata['val'];
													$color_wise_bundle_no_arr[$color] += $cdata['count'];
													}
													?>

												</tr>
												<?
												$production_quantity += $cdata['val'];
												$total_bundle += $cdata['count'];
												$i++;
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
        </tbody>
        <tr>
            <td colspan="10" align="right"><strong>Grand Total </strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
            <td align="center"><?  echo $total_bundle; ?></td>
        </tr>
    </table>


 <br>
            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Size Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Size</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($size_qty_arr as $size_id => $size_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $size_library[$size_id]; ?></td>
                        <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                        <td align="right"><? echo $size_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><strong>Total </strong></td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
			<?
            // echo signature_table(243, $data[0], "500px");
			echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();
}

//------------ shamim
if($action=="emblishment_issue_print_4")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	$cbo_template_id=$data[5];
 

	/*	//============================
	$realCutArr=return_library_array( "select c.bundle_no,b.cutting_no from ppl_cut_lay_mst b,ppl_cut_lay_bundle c where  b.id=c.mst_id", "bundle_no", "cutting_no");

	$wrongCutArray=return_library_array( "select d.id,d.bundle_no from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c ,pro_garments_production_dtls d where  b.id=c.mst_id  and c.bundle_no=d.bundle_no and d.cut_no <> b.cutting_no", "id", "bundle_no");


	$con = connect();
	foreach($wrongCutArray as $id=>$bundle_id){
	if($realCutArr[$bundle_id]!=''){
		echo execute_query("update pro_garments_production_dtls set cut_no = '".$realCutArr[$bundle_id]."' where id=".$id."");
		}
	}
	oci_commit($con);
	die;
	//============================== */




	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');

	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_array=array();

	/*$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}


	$cut_lay_arr=array();
	$lay_sql="SELECT a.cutting_no, b.order_id, b.order_cut_no, b.color_id, b.gmt_item_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$lay_sql_data=sql_select($lay_sql);
	foreach($lay_sql_data as $row)
	{
		$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]].=$row[csf('order_cut_no')].',';
	}*/


	$sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];
	if($data[4]=="" or $data[4]==0)
	{
		$data[4]=$data[0];
	}



	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[4]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
						foreach ($nameArray as $result)
						{
							 echo $result[csf('city')];
						}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Print Issue Challan/Gate Pass</strong></u></td>
	        </tr>
	        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
	            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
				<?
					if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
				 ?>
	            </td>
	        </tr>
	        <tr>
	            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	            <td><strong>Emb. Company</strong></td><td>:
					<?
						if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
						else echo $supplier_library[$dataArray[0][csf('serving_company')]];

	                ?>
	            </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	        <td ><strong> Barcode  : </strong></td> <td  id="barcode_img_id"></td>
	            <td><strong>Delivery Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	           <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	        </tr>
	        <tr>
	        	<td  colspan="4" id="barcode_img_id"></td>
				<td><strong>Remarks </strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
	        </tr>

	    </table><br />
	        <?
				$delivery_mst_id =$dataArray[0][csf('id')];

					$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,e.brand_id,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no
					from
						pro_garments_production_mst a,
						pro_garments_production_dtls b,
						wo_po_color_size_breakdown d,
						wo_po_details_master e,
						wo_po_break_down f
					where
						a.delivery_mst_id ='$data[1]'
						and e.id=f.job_id
						and f.id=a.po_break_down_id
						and a.id=b.mst_id
						and b.color_size_break_down_id=d.id
						and b.status_active=1
						and b.is_deleted=0
						and d.status_active =1
						and d.is_deleted=0
					order by e.job_no,d.size_order";
					  //echo $sql;

					$result=sql_select($sql);

					foreach($result as $rows)
					{
						//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

						$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('brand_id')].$rows[csf('job_no')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')];

						$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
						$dataArr[$key]=array(
							country_id=>$rows[csf('country_id')],
							buyer_name=>$rows[csf('buyer_name')],
							brand_id=>$rows[csf('brand_id')],
							po_id=>$rows[csf('po_id')],
							po_number=>$rows[csf('po_number')],
							color_number_id=>$rows[csf('color_number_id')],
							size_number_id=>$rows[csf('size_number_id')],
							style_ref_no=>$rows[csf('style_ref_no')],
							style_description=>$rows[csf('style_description')],
							job_no=>$rows[csf('job_no')],
							cut_no=>$rows[csf('cut_no')],
							order_cut_no=>$rows[csf('order_cut_no')]
						);
						$orderCutArr[$key][$rows[csf('order_cut_no')]]=$rows[csf('order_cut_no')];
						$productionQtyArr[$key]+=$rows[csf('production_qnty')];
						$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
						$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
						$cut_no_arr[$rows[csf('cut_no')]] = $rows[csf('cut_no')];

					}
				unset($result);

				$cut_no_cond = where_con_using_array($cut_no_arr,1,"a.cutting_no");
				$cut_lay_arr=array();
				$lay_sql="SELECT a.cutting_no, b.order_id, b.order_cut_no, b.color_id, c.country_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cut_no_cond";
				// echo $lay_sql;
				$lay_sql_data=sql_select($lay_sql);
				foreach($lay_sql_data as $row)
				{
					$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]][$row[csf('country_id')]]=$row[csf('order_cut_no')];
				}

				// echo "<pre>"; print_r($cut_lay_arr); echo "</pre>";
			?>


	    <div style="width:100%;">
	    <table cellspacing="0" width="980" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">

	    <tr bgcolor="#dddddd" align="center">
	            <th colspan="10"></th>
	            <th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
	            <th align="center" rowspan="2"  >Total Issue Qty </th>
	            <th align="center" rowspan="2">No of Bundle</th>
	            <th width="100" rowspan="2" align="center">Remarks </th>
	        </tr>

	        <tr bgcolor="#dddddd" align="center">
	            <th width="40">SL No</th>
	            <th width="80" align="center">Buyer/ Brand</th>
	            <th width="80" align="center">Job No</th>
	            <th width="80" align="center">Order No</th>
	            <th width="80" align="center">Style Ref</th>
	            <th width="100" align="center">Style Des</th>
	            <th width="80" align="center">Country</th>
	            <th width="80" align="center">Color</th>
	            <th width="80" align="center">Cutting No</th>
	            <th width="80" align="center">Order Cut</th>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
	                <?
	                }
	                ?>
	        </tr>
	        <tbody>
				<?
	            $i=1;
	            $tot_qnty=array();
				foreach($dataArr as $key=>$row)
				{
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
						<td align="center"><? echo $i;  ?></td>
						<td align="center"><p><? echo $buyer_arr[$row[buyer_name]]." / ".$brand_arr[$row[brand_id]]; ?></p></td>
						<td align="center"><p><? echo $row['job_no']; ?></p></td>
						<td align="center"><p><? echo $row['po_number']; ?></p></td>
						<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
						<td align="center"><p><? echo $row['style_description']; ?></p></td>
						<td align="center"><p><? echo $country_library[$row[country_id]]; ?></p></td>
						<td align="center"><p><? echo $color_library[$row[color_number_id]]; ?></p></td>
						<td align="center"><p><? echo $row['cut_no']; ?></td>
						<td align="center"><p><? echo $cut_lay_arr[$row['cut_no']][$row['color_number_id']][$row['country_id']]; ?></p></td>
	                        <?
	                        foreach($bundle_size_arr as $size_id)
							{
								$size_qty=$sizeQtyArr[$key][$size_id];
								?>
								<td align="center" width="50"><? echo $size_qty; ?></td>
								<?
								$grand_total_size_arr[$size_id]+=$size_qty;
							}
							?>
	                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
						<?
						$color_qty_arr[$color] += $cdata['val'];
						$color_wise_bundle_no_arr[$color] += $cdata['count'];
						?>
						<td align="right"> </td>

					</tr>
					<?
						$grand_total_qty+=$productionQtyArr[$key];
						$grand_total_bundle_num+=count($bundleArr[$key]);
						$grand_total_reject_qty+=$val['reject_qty'];
					$i++;
				 }

	                ?>
	        </tbody>
	        <tr>
	            <td colspan="10" align="right"><strong>Grand Total </strong></td>
					<?
	                foreach($bundle_size_arr as $size_id)
	                {
	                    ?>
	                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                    <?
	                }
	                ?>
	            <td align="center">  <? echo $grand_total_qty;  ?></td>
	            <td align="center"><?  echo $grand_total_bundle_num; ?></td>
	        </tr>
	    </table>


	           <br><br>

	            <table cellspacing="0" rules="all" style="font: 12px tahoma; margin-left:110px;">
	            <tr >

	                <td width="90" style="border:1px solid white;"><strong>Transport No</strong></td>    <td width="200px" style="border:1px solid white;">: <?  ?></td>
	                <td width="80" style="border:1px solid white;"><strong>Driver Name</strong></td>    <td width="190px" style="border:1px solid white;"> : <? ?></td>
	                <td width="55" style="border:1px solid white;"><strong>D/L No</strong></td>         <td width="155px" style="border:1px solid white;">: <? ?> </td>
	            </tr>
	            </table>
	            <br><br>

			 <?
	            	echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
	         ?>

	         <br>


	            <br>
		</div>
		</div>
	   	<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess ){
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};

				 value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
		    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
		 </script>
	<?
	exit();
}

if($action=="emblishment_issue_print_5")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[5];
  
	/*	//============================
	$realCutArr=return_library_array( "select c.bundle_no,b.cutting_no from ppl_cut_lay_mst b,ppl_cut_lay_bundle c where  b.id=c.mst_id", "bundle_no", "cutting_no");

	$wrongCutArray=return_library_array( "select d.id,d.bundle_no from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c ,pro_garments_production_dtls d where  b.id=c.mst_id  and c.bundle_no=d.bundle_no and d.cut_no <> b.cutting_no", "id", "bundle_no");


	$con = connect();
	foreach($wrongCutArray as $id=>$bundle_id){
	if($realCutArr[$bundle_id]!=''){
		echo execute_query("update pro_garments_production_dtls set cut_no = '".$realCutArr[$bundle_id]."' where id=".$id."");
		}
	}
	oci_commit($con);
	die;
		//============================== */




	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$buyer_code_arr=return_library_array( "select id, remark from lib_buyer",'id','remark');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_id_arr = return_library_array( "select PO_BREAK_DOWN_ID, PO_BREAK_DOWN_ID from PRO_GARMENTS_PRODUCTION_MST where production_type=2 and delivery_mst_id='$data[1]' and status_active=1 and is_deleted=0", "PO_BREAK_DOWN_ID", "PO_BREAK_DOWN_ID" );
	// print_r($order_id_arr);die;

	$sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	if($data[4]=="" or $data[4]==0)
	{
		$data[4]=$data[0];
	}
	
	$order_array=array();
	$po_id_cond = where_con_using_array($order_id_arr,0,"b.id");
	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$data[0] $po_id_cond";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}


	$po_id_cond = where_con_using_array($order_id_arr,0,"c.order_id");
	$cut_lay_arr=array();
	$lay_sql="SELECT a.cutting_no, b.order_id, b.order_cut_no, b.color_id, b.gmt_item_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 $po_id_cond";
	// echo $lay_sql;die;
	$lay_sql_data=sql_select($lay_sql);
	foreach($lay_sql_data as $row)
	{
		$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]].=$row[csf('order_cut_no')].',';
	}
	// echo "<pre>";print_r($cut_lay_arr);die;
	?>
	<div style="width:900px;">
    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[4]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
				foreach ($nameArray as $result)
				{
					echo $result[csf('city')];
				}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Print Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];
                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
       <tr>
        <td ><strong> Barcode  : </strong></td> <td  id="barcode_img_id"></td>
            <td><strong>Delivery Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
           <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
        </tr>
        <tr>
        	<td  colspan="4" id="barcode_img_id"></td>

        </tr>

    </table><br />
        <?
			$delivery_mst_id =$dataArray[0][csf('id')];

				$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no
				from
					pro_garments_production_mst a,
					pro_garments_production_dtls b,
					wo_po_color_size_breakdown d,
					wo_po_details_master e,
					wo_po_break_down f
				where
					a.delivery_mst_id ='$data[1]'
					and e.job_no=f.job_no_mst
					and f.id=a.po_break_down_id
					and a.id=b.mst_id
					and b.color_size_break_down_id=d.id
					and b.status_active=1
					and b.is_deleted=0
					and d.status_active=1
					and d.is_deleted=0
				order by e.job_no,d.size_order";
				//   echo $sql;die;

				$result=sql_select($sql);

				foreach($result as $rows)
				{
					//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

					$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')];

					$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
					$dataArr[$key]=array(
						country_id=>$rows[csf('country_id')],
						buyer_name=>$rows[csf('buyer_name')],
						po_id=>$rows[csf('po_id')],
						po_number=>$rows[csf('po_number')],
						color_number_id=>$rows[csf('color_number_id')],
						size_number_id=>$rows[csf('size_number_id')],
						style_ref_no=>$rows[csf('style_ref_no')],
						style_description=>$rows[csf('style_description')],
						job_no=>$rows[csf('job_no')],
						cut_no=>$rows[csf('cut_no')]
					);
					$orderCutArr[$key][$rows[csf('order_cut_no')]]=$rows[csf('order_cut_no')];
					$productionQtyArr[$key]+=$rows[csf('production_qnty')];
					$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
					$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

				}


			unset($result);
			// echo "<pre>";print_r($cut_lay_arr);die;
		?>


    <div style="width:100%;">
    <table cellspacing="0" width="980" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">

    <tr bgcolor="#dddddd" align="center">
            <th colspan="10"></th>
            <th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
            <th align="center" rowspan="2"  >Total Issue Qty </th>
            <th align="center" rowspan="2">No of Bundle</th>
            <th width="100" rowspan="2" align="center">Remarks </th>
        </tr>

        <tr bgcolor="#dddddd" align="center">
            <th width="40">SL No</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job No</th>
            <th width="80" align="center">Order No</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Cutting No</th>
            <th width="80" align="center">Order Cut</th>
                <?
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
                ?>
                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
                <?
                }
                ?>
        </tr>
        <tbody>
			<?
            $i=1;
            $tot_qnty=array();
			foreach($dataArr as $key=>$row)
			{
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
					<td align="center"><? echo $i;  ?></td>
					<td align="center" title="<? echo $buyer_arr[$row[buyer_name]]; ?>"><p><? echo $buyer_code_arr[$row[buyer_name]]; ?></p></td>
					<td align="center"><p><? echo $row['job_no']; ?></p></td>
					<td align="center"><p><? echo $row['po_number']; ?></p></td>
					<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
					<td align="center"><p><? echo $row['style_description']; ?></p></td>
					<td align="center"><p><? echo $country_library[$row['country_id']]; ?></p></td>
					<td align="center" title="<?=$row['color_number_id'];?>"><p><? echo $color_library[$row['color_number_id']]; ?></p></td>
					<td align="center"><p><? echo $row['cut_no']; ?></td>
					<td align="center"><p><?= implode(", ",array_unique(array_filter(explode(",",$cut_lay_arr[$row['cut_no']][$row['color_number_id']])))); ?></p></td>
                        <?
                        foreach($bundle_size_arr as $size_id)
						{
							$size_qty=$sizeQtyArr[$key][$size_id];
							?>
							<td align="center" width="50"><? echo $size_qty; ?></td>
							<?
							$grand_total_size_arr[$size_id]+=$size_qty;
						}
						?>
                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
					<?
					$color_qty_arr[$color] += $cdata['val'];
					$color_wise_bundle_no_arr[$color] += $cdata['count'];
					?>
					<td align="right"> </td>

				</tr>
				<?
					$grand_total_qty+=$productionQtyArr[$key];
					$grand_total_bundle_num+=count($bundleArr[$key]);
					$grand_total_reject_qty+=$val['reject_qty'];
				$i++;
			 }

                ?>
        </tbody>
        <tr>
            <td colspan="10" align="right"><strong>Grand Total </strong></td>
				<?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                    <?
                }
                ?>
            <td align="center">  <? echo $grand_total_qty;  ?></td>
            <td align="center"><?  echo $grand_total_bundle_num; ?></td>
        </tr>
    </table>


           <br><br>

           
            <br><br>
            <table cellspacing="0" width="300" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                <thead bgcolor="#dddddd" align="center">
                    <th width="90">Bundle No</th>
                    <th width="110">Bundle ID</th>
                    <th>Total Issue Qty.</th>
                    </thead>
                <tbody>
				<?
				$sql2="SELECT  c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty from pro_garments_production_dtls c where  c.delivery_mst_id=".$data[1]." and c.status_active=1 and c.is_deleted=0 order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc ";
				$result = sql_select($sql2);
				$i=1;
				foreach ($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$qty=$row[csf('production_qnty')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
						<td align="center"><p><? echo $row[csf('bundle_no')];?></p></td>
						<td align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
						<td align="center"><p><? echo $qty ?></p></td>
					</tr>
					<?
					$i++;
				 }
                ?>
        	</tbody>
    		</table>
    		<br><br>

		 <?
		    echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
            // echo signature_table(26, $data[0], "900px");
         ?>

         <br>


            <br>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
 <script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
	<?
	exit();
}
if($action=="emblishment_issue_print_6")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
    $cbo_template_id=$data[5];

	/*	//============================
	$realCutArr=return_library_array( "select c.bundle_no,b.cutting_no from ppl_cut_lay_mst b,ppl_cut_lay_bundle c where  b.id=c.mst_id", "bundle_no", "cutting_no");

	$wrongCutArray=return_library_array( "select d.id,d.bundle_no from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c ,pro_garments_production_dtls d where  b.id=c.mst_id  and c.bundle_no=d.bundle_no and d.cut_no <> b.cutting_no", "id", "bundle_no");


	$con = connect();
	foreach($wrongCutArray as $id=>$bundle_id){
	if($realCutArr[$bundle_id]!=''){
		echo execute_query("update pro_garments_production_dtls set cut_no = '".$realCutArr[$bundle_id]."' where id=".$id."");
		}
	}
	oci_commit($con);
	die;
		//============================== */




	// print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_array=array();

	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, group_id, vat_number from lib_company where status_active=1 and  is_deleted=0");
	$group_com_arr_lib = return_library_array("select id, group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}

	$cut_lay_arr=array();
	$lay_sql="select a.cutting_no, b.order_id, b.order_cut_no, b.color_id, b.gmt_item_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$lay_sql_data=sql_select($lay_sql);
	foreach($lay_sql_data as $row)
	{
		$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]].=$row[csf('order_cut_no')].',';
	}

	$cut_floor_lay_arr=array();
	$cut_floor_sql="SELECT a.cutting_no,a.floor_id,b.delivery_mst_id,b.CUT_NO
    FROM ppl_cut_lay_mst a, pro_garments_production_mst b
    WHERE    a.cutting_no= b.CUT_NO
	AND b.delivery_mst_id='$data[1]'
	AND a.status_active = 1
	AND a.is_deleted = 0";
	$cut_floor_sql_data=sql_select($cut_floor_sql);
	foreach($cut_floor_sql_data as $row)
	{
		$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
	}

	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];
	if($data[4]=="" or $data[4]==0)
	{
		$data[4]=$data[0];
	}



 ?>
 <div style="width:900px;">
    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	    <tr>
            <td colspan="6" align="center" style="font-size:22px">
				<strong><? echo $group_com_arr_lib[$company_array[$data[0]]['group_id']]; ?></strong>
			</td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:14px">
				<strong>Working Company:</strong>
				<strong><? echo $company_library[$data[4]]; ?>
				(Location:
				<? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?>
				)
				</strong>
			</td>
        </tr>
		<tr>
            <td colspan="6" align="center" style="font-size:14px">
				<strong>Working Company Add:</strong>
				<strong>
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								echo $result[csf('city')];
							}
						?>
				</strong>
			</td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:14px">
				<strong>Owner Company:</strong>
				<strong><? echo $company_library[$data[0]]; ?></strong>
			</td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Print Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo " Embellishment Issue for Bundle-".$emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
       <tr>
        <td ><strong> Cutting Floor  </strong></td> <td> : <? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?> </td>
            <td><strong>Delivery Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
           <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
        </tr>

		<tr>
            <td ><strong> Barcode  : </strong></td> <td colspan="3" id="barcode_img_id"></td>
            <td><strong>Remarks</strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
        <tr>
        	<!-- <td  colspan="4" id="barcode_img_id"></td> -->

        </tr>

    </table><br />
        <?
			$delivery_mst_id =$dataArray[0][csf('id')];

				$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id,d.item_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.grouping,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no
				from
					pro_garments_production_mst a,
					pro_garments_production_dtls b,
					wo_po_color_size_breakdown d,
					wo_po_details_master e,
					wo_po_break_down f,
					ppl_cut_lay_mst g,
					ppl_cut_lay_dtls h,
					ppl_cut_lay_bundle i
				where
					a.delivery_mst_id ='$data[1]'
					AND b.production_type = 2
					and e.id=f.job_id
					and d.job_id=e.id
					and f.id=a.po_break_down_id
					and a.id=b.mst_id
					and b.color_size_break_down_id=d.id
					and b.status_active=1
					and b.is_deleted=0
					and d.status_active =1
					and d.is_deleted=0
					and g.id=h.mst_id
					and i.mst_id=g.id
					and i.dtls_id=h.id
					and i.bundle_no=b.bundle_no
					and g.cutting_no=b.cut_no
					and h.color_id=d.color_number_id
					and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
				order by e.job_no,d.size_order,d.item_number_id,b.cut_no asc";
				//  echo $sql;

				$result=sql_select($sql);

				foreach($result as $rows)
				{
					//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

					$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')];

					$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
					$dataArr[$key]=array(
						country_id=>$rows[csf('country_id')],
						buyer_name=>$rows[csf('buyer_name')],
						po_id=>$rows[csf('po_id')],
						po_number=>$rows[csf('po_number')],
						item_number_id=>$rows[csf('item_number_id')],
						color_number_id=>$rows[csf('color_number_id')],
						size_number_id=>$rows[csf('size_number_id')],
						style_ref_no=>$rows[csf('style_ref_no')],
						style_description=>$rows[csf('style_description')],
						job_no=>$rows[csf('job_no')],
						cut_no=>$rows[csf('cut_no')],
						order_cut_no=>$rows[csf('order_cut_no')]
					);
					$orderCutArr[$key][$rows[csf('order_cut_no')]]=$rows[csf('order_cut_no')];
					$productionQtyArr[$key]+=$rows[csf('production_qnty')];
					$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
					$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

				}


			unset($result);
		?>


    <div style="width:100%;">
    <table cellspacing="0" width="1060" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">

    <tr bgcolor="#dddddd" align="center">
            <th colspan="11"></th>
            <th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
            <th align="center" rowspan="2"  >Total Issue Qty </th>
            <th align="center" rowspan="2">No of Bundle</th>
            <th width="100" rowspan="2" align="center">Remarks </th>
        </tr>

        <tr bgcolor="#dddddd" align="center">
            <th width="40">SL No</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job No</th>
            <th width="80" align="center">Order No</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
			<th width="80" align="center">Gmt Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Cutting No</th>
            <th width="80" align="center">Order Cut</th>
                <?
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
                ?>
                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
                <?
                }
                ?>
        </tr>
        <tbody>
			<?
            $i=1;
            $tot_qnty=array();
			foreach($dataArr as $key=>$row)
			{
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
					<td align="center"><? echo $i;  ?></td>
					<td align="center"><p><? echo $buyer_arr[$row[buyer_name]]; ?></p></td>
					<td align="center"><p><? echo $row['job_no']; ?></p></td>
					<td align="center"><p><? echo $row['po_number']; ?></p></td>
					<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
					<td align="center"><p><? echo $row['style_description']; ?></p></td>
					<td align="center"><p><? echo $garments_item[$row['item_number_id']]; ?></p></td>
					<td align="center"><p><? echo $country_library[$row[country_id]]; ?></p></td>
					<td align="center"><p><? echo $color_library[$row[color_number_id]]; ?></p></td>
					<td align="center"><p><? echo $row['cut_no']; ?></td>
					<td align="center"><p><? echo implode(',',$orderCutArr[$key]); ?></p></td>
                        <?
                        foreach($bundle_size_arr as $size_id)
						{
							$size_qty=$sizeQtyArr[$key][$size_id];
							?>
							<td align="center" width="50"><? echo $size_qty; ?></td>
							<?
							$grand_total_size_arr[$size_id]+=$size_qty;
						}
						?>
                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
					<?
					$color_qty_arr[$color] += $cdata['val'];
					$color_wise_bundle_no_arr[$color] += $cdata['count'];
					?>
					<td align="center"> <? //echo $dataArray[0][csf('remarks')]; ?> </td>

				</tr>
				<?
					$grand_total_qty+=$productionQtyArr[$key];
					$grand_total_bundle_num+=count($bundleArr[$key]);
					$grand_total_reject_qty+=$val['reject_qty'];
				$i++;
			 }

                ?>
        </tbody>
        <tr>
            <td colspan="11" align="right"><strong>Grand Total </strong></td>
				<?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                    <?
                }
                ?>
            <td align="center">  <? echo $grand_total_qty;  ?></td>
            <td align="center"><?  echo $grand_total_bundle_num; ?></td>
        </tr>
    </table>


           <br><br>

            <table cellspacing="0" rules="all" style="font: 12px tahoma; margin-left:110px;">
            <tr >

                <td width="90" style="border:1px solid white;"><strong>Transport No</strong></td>    <td width="200px" style="border:1px solid white;">: <?  ?></td>
                <td width="80" style="border:1px solid white;"><strong>Driver Name</strong></td>    <td width="190px" style="border:1px solid white;"> : <? ?></td>
                <td width="55" style="border:1px solid white;"><strong>D/L No</strong></td>         <td width="155px" style="border:1px solid white;">: <? ?> </td>
            </tr>
            </table>
            <br><br>

		 <?
            echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
         ?>

         <br>


            <br>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
	 function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
 <?
 exit();
}

if($action=="emblishment_issue_print_7")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[5];


	/*	//============================
	$realCutArr=return_library_array( "select c.bundle_no,b.cutting_no from ppl_cut_lay_mst b,ppl_cut_lay_bundle c where  b.id=c.mst_id", "bundle_no", "cutting_no");

	$wrongCutArray=return_library_array( "select d.id,d.bundle_no from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c ,pro_garments_production_dtls d where  b.id=c.mst_id  and c.bundle_no=d.bundle_no and d.cut_no <> b.cutting_no", "id", "bundle_no");


	$con = connect();
	foreach($wrongCutArray as $id=>$bundle_id){
	if($realCutArr[$bundle_id]!=''){
		echo execute_query("update pro_garments_production_dtls set cut_no = '".$realCutArr[$bundle_id]."' where id=".$id."");
		}
	}
	oci_commit($con);
	die;
		//============================== */




	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_array=array();

	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}


	$cut_lay_arr=array();
	$lay_sql="select a.cutting_no, b.order_id, b.order_cut_no, b.color_id, b.gmt_item_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$lay_sql_data=sql_select($lay_sql);
	foreach($lay_sql_data as $row)
	{
		$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]].=$row[csf('order_cut_no')].',';
	}

	$cut_floor_lay_arr=array();
	$cut_floor_sql="SELECT a.cutting_no,a.floor_id,b.delivery_mst_id,b.CUT_NO
    FROM ppl_cut_lay_mst a, pro_garments_production_mst b
    WHERE    a.cutting_no= b.CUT_NO
	AND b.delivery_mst_id='$data[1]'
	AND a.status_active = 1
	AND a.is_deleted = 0";
	$cut_floor_sql_data=sql_select($cut_floor_sql);
	foreach($cut_floor_sql_data as $row)
	{
		$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
	}

	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	// echo $sql;
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];
	if($data[4]=="" or $data[4]==0)
	{
		$data[4]=$data[0];
	}



 ?>
 <div style="width:900px;">
    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[4]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						echo $result[csf('city')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Print Issue Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
       <tr>
        <td ><strong> Cutting Floor  </strong></td> <td> : <? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?> </td>
            <td><strong>Delivery Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
           <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
        </tr>

		<tr>
            <td ><strong> Barcode  : </strong></td> <td  id="barcode_img_id"></td>

        </tr>
        <tr>
        	<td  colspan="4" id="barcode_img_id"></td>

        </tr>

    </table><br />
        <?
			$delivery_mst_id =$dataArray[0][csf('id')];

				$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name, e.brand_id, f.po_number,f.grouping,f.file_no,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no,a.delivery_mst_id
				,e.fit_id from
					pro_garments_production_mst a,
					pro_garments_production_dtls b,
					wo_po_color_size_breakdown d,
					wo_po_details_master e,
					wo_po_break_down f,
					ppl_cut_lay_mst g,
					ppl_cut_lay_dtls h,
					ppl_cut_lay_bundle i
				where
					a.delivery_mst_id ='$data[1]'
					AND b.production_type = 2
					and e.id=f.job_id
					and d.job_id=e.id
					and f.id=a.po_break_down_id
					and a.id=b.mst_id
					and b.color_size_break_down_id=d.id
					and b.status_active=1
					and b.is_deleted=0
					and d.status_active =1
					and d.is_deleted=0
					and g.id=h.mst_id
					and i.mst_id=g.id
					and i.dtls_id=h.id
					and i.bundle_no=b.bundle_no
					and g.cutting_no=b.cut_no
					and h.color_id=d.color_number_id
					and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
				order by e.job_no,d.size_order,b.cut_no asc";
				//   echo $sql;

				$result=sql_select($sql);

				foreach($result as $rows)
				{
					//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

					$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('po_number')];

					$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
					$dataArr[$key]=array(
						country_id=>$rows[csf('country_id')],
						buyer_name=>$rows[csf('buyer_name')],
						brand_id=>$rows[csf('brand_id')],
						po_id=>$rows[csf('po_id')],
						po_number=>$rows[csf('po_number')],
						grouping=>$rows[csf('grouping')],
						file_no=>$rows[csf('file_no')],
						color_number_id=>$rows[csf('color_number_id')],
						size_number_id=>$rows[csf('size_number_id')],
						style_ref_no=>$rows[csf('style_ref_no')],
						style_description=>$rows[csf('style_description')],
						job_no=>$rows[csf('job_no')],
						cut_no=>$rows[csf('cut_no')],
						order_cut_no=>$rows[csf('order_cut_no')],
                        delivery_mst_id => $rows[csf('delivery_mst_id')],
						fit_id => $rows[csf('fit_id')]
					);
					$orderCutArr[$key][$rows[csf('order_cut_no')]]=$rows[csf('order_cut_no')];
					$productionQtyArr[$key]+=$rows[csf('production_qnty')];
					$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
					$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

				}


			unset($result);
		?>


    <div style="width:100%;">
    <table cellspacing="0" width="1140" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">

    <tr bgcolor="#dddddd" align="center">
            <th colspan="12"></th>
            <th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
            <th align="center" rowspan="2"  >Total Issue Qty </th>
            <th align="center" rowspan="2">No of Bundle</th>
            <th width="100" rowspan="2" align="center">Remarks </th>
        </tr>

        <tr bgcolor="#dddddd" align="center">
            <th width="40">SL No</th>
            <th width="80" align="center">Buyer/ Brand</th>
            <th width="80" align="center">Job No</th>
            <th width="80" align="center">Order No</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
			<th width="80" align="center">Internal Ref</th>
			<th width="80" align="center">File No</th>
			<th width="80" align="center">Fit</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Cutting No</th>
            <th width="80" align="center">Order Cut</th>
                <?
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
                ?>
                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
                <?
                }
                ?>
        </tr>
        <tbody>
			<?
				$remarksrawspanarray = array();
				foreach($dataArr as $key=>$row)
				{
					$remarksrawspanarray[$row['delivery_mst_id']]++;
				}

            $i=1;
            $tot_qnty=array();
			foreach($dataArr as $key=>$row)
			{
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
					<td align="center"><? echo $i;  ?></td>
					<td align="center"><p><? echo $buyer_arr[$row[buyer_name]].'/ '.$brand_arr[$row[brand_id]]; ?></p></td>
					<td align="center"><p><? echo $row['job_no']; ?></p></td>
					<td align="center"><p><? echo $row['po_number']; ?></p></td>
					<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
					<td align="center"><p><? echo $row['style_description']; ?></p></td>
					<td align="center"><p><? echo $row['grouping']; ?></p></td>
					<td align="center"><p><? echo $row['file_no']; ?></p></td>
					<td align="center"><p><? echo $fit_list_arr[$row['fit_id']]; ?></p></td>
					<td align="center"><p><? echo $country_library[$row[country_id]]; ?></p></td>
					<td align="center"><p><? echo $color_library[$row[color_number_id]]; ?></p></td>
					<td align="center"><p><? echo $row['cut_no']; ?></td>
					<td align="center"><p><? echo implode(',',$orderCutArr[$key]); ?></p></td>
                        <?
                        foreach($bundle_size_arr as $size_id)
						{
							$size_qty=$sizeQtyArr[$key][$size_id];
							?>
							<td align="center" width="50"><? echo $size_qty; ?></td>
							<?
							$grand_total_size_arr[$size_id]+=$size_qty;
						}
						?>
                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
					<?
					$color_qty_arr[$color] += $cdata['val'];
					$color_wise_bundle_no_arr[$color] += $cdata['count'];
					?>
					<?php
					if ($remarksrowspan == 0) {
					?>
					  <td rowspan="<? echo $remarksrawspanarray[$row['delivery_mst_id']]; ?>" align="center"><? echo $dataArray[0][csf('remarks')]; ?></td>
                    <?php
					}
					$remarksrowspan ++;
					?>
				</tr>
				<?
					$grand_total_qty+=$productionQtyArr[$key];
					$grand_total_bundle_num+=count($bundleArr[$key]);
					$grand_total_reject_qty+=$val['reject_qty'];
				$i++;
			}

                ?>
        </tbody>
        <tr>
            <td colspan="12" align="right"><strong>Grand Total </strong></td>
				<?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                    <?
                }
                ?>
            <td align="center">  <? echo $grand_total_qty;  ?></td>
            <td align="center"><?  echo $grand_total_bundle_num; ?></td>
        </tr>
    </table>


           <br><br>

            <table cellspacing="0" rules="all" style="font: 12px tahoma; margin-left:110px;">
            <tr >

                <td width="90" style="border:1px solid white;"><strong>Transport No</strong></td>    <td width="200px" style="border:1px solid white;">: <?  ?></td>
                <td width="80" style="border:1px solid white;"><strong>Driver Name</strong></td>    <td width="190px" style="border:1px solid white;"> : <? ?></td>
                <td width="55" style="border:1px solid white;"><strong>D/L No</strong></td>         <td width="155px" style="border:1px solid white;">: <? ?> </td>
            </tr>
            </table>
            <br><br>

		 <?
            echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
         ?>

         <br>


            <br>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
	 function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
 <?
 exit();
}

if($action=="emblishment_issue_print_8")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[5];



	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "SELECT id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "SELECT id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "SELECT id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "SELECT id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "SELECT id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');

	$brand_arr=return_library_array( "SELECT id, brand_name from lib_buyer_brand",'id','brand_name');

	$body_part_arr=return_library_array( "SELECT id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name" );
	$order_array=array();

	$sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);
	$insert_by=$dataArray[0][csf('inserted_by')];
	if($data[4]=="" || $data[4]==0)
	{
		$data[4]=$data[0];
	}
	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[4]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
						foreach ($nameArray as $result)
						{
							echo $result[csf('city')];
						}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Print Issue Challan/Gate Pass</strong></u></td>
	        </tr>
	        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
	            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
				<?
					if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
				 ?>
	            </td>
	        </tr>
	        <tr>
	            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	            <td><strong>Emb. Company</strong></td><td>:
					<?
						if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
						else echo $supplier_library[$dataArray[0][csf('serving_company')]];
	                ?>
	            </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	        	<td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	        	<td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	        <td ><strong> Barcode  : </strong></td> <td  id="barcode_img_id"></td>
	            <td><strong>Delivery Company </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	           <td><strong>Body Part </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="4" id="barcode_img_id"></td>
				<td><strong>Remarks </strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
	        </tr>
	    </table><br />
		<?
			$delivery_mst_id =$dataArray[0][csf('id')];
 
			$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, d.size_order, e.style_ref_no, e.style_description, e.job_no, f.grouping, e.buyer_name,e.brand_id,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d,
				wo_po_details_master e,
				wo_po_break_down f
			where
				a.delivery_mst_id ='$data[1]'
				and e.id=f.job_id
				and f.id=a.po_break_down_id
				and a.id=b.mst_id
				and b.color_size_break_down_id=d.id
				and b.status_active=1
				and b.is_deleted=0
				and d.status_active =1
				and d.is_deleted=0
			order by d.color_number_id, d.size_order";
			// echo $sql; die;

			$result=sql_select($sql);
			$data_array = array();
			foreach($result as $rows)
			{
				//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['color_number_id']=$rows[csf('color_number_id')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['size_number_id']=$rows[csf('size_number_id')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['buyer_name']=$rows[csf('buyer_name')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['job_no']=$rows[csf('job_no')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['po_id']=$rows[csf('po_id')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['po_number']=$rows[csf('po_number')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['style_description']=$rows[csf('style_description')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['cut_no']=$rows[csf('cut_no')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['order_cut_no']=$rows[csf('order_cut_no')];
				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['brand_id']=$rows[csf('brand_id')];

				$data_array[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]['grouping']=$rows[csf('grouping')];

				$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
				$sizeQtyArr[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
				$productionQtyArr[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]]+=$rows[csf('production_qnty')];
				$bundleArr[$rows[csf('color_number_id')]][$rows[csf('buyer_name')]][$rows[csf('job_no')]][$rows[csf('po_number')]][$rows[csf('style_ref_no')]][$rows[csf('style_description')]][$rows[csf('cut_no')]][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

				$cut_no_arr[$rows[csf('cut_no')]] = $rows[csf('cut_no')];
			}
			// echo "<pre>"; print_r($sizeQtyArr); die;
			unset($result);

			$cut_no_cond = where_con_using_array($cut_no_arr,1,"a.cutting_no");
			$cut_lay_arr=array();
			$lay_sql="SELECT a.cutting_no, b.order_id, b.order_cut_no, b.color_id, c.country_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cut_no_cond";
			// echo $lay_sql;
			$lay_sql_data=sql_select($lay_sql);
			foreach($lay_sql_data as $row)
			{
				$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]]=$row[csf('order_cut_no')];
			}
			// echo "<pre>"; print_r($cut_lay_arr); die;
		?>
	    <div style="width:100%;">
			<table cellspacing="0" width="980" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
				<tr bgcolor="#dddddd" align="center">
					<th colspan="9"></th>
					<th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
					<th align="center" rowspan="2"  >Total Issue Qty </th>
					<th align="center" rowspan="2">No of Bundle</th>
					<th width="100" rowspan="2" align="center">Remarks </th>
				</tr>
				<tr bgcolor="#dddddd" align="center">
					<th width="40">SL No</th>
					<th width="80" align="center">Buyer/ Brand</th>
					<th width="80" align="center">Job No</th>
					<th width="80" align="center">Order No</th>
					<th width="80" align="center">Style Ref</th>
					<th width="100" align="center">Style Des</th>
					<th width="80" align="center">IR No</th>
					<!-- <th width="80" align="center">Country</th> -->
					<th width="80" align="center">Color</th>
					<th width="80" align="center">Cutting No</th>
					<th width="80" align="center">Order Cut</th>
					<?
					$i=0;
					foreach($bundle_size_arr as $inf)
					{
					?>
					<th align="center" width="50"><? echo $size_library[$inf]; ?></th>
					<?
					}
					?>
				</tr>
				<tbody>
					<?
					$color_arr=return_library_array( "SELECT id, color_name from lib_color", "id", "color_name");
					$tot_qnty=array();
					// echo "<pre>"; print_r($data_array); die;
					$grand_total_qty=0;
					$grand_total_bundle_num = 0;
					$grand_total_size_arr=array();
					foreach($data_array as $color_id=> $color_name)
					{
						foreach($color_name as $buyer_id => $buyer_name)
						{
							$sub_total_size_arr=array();
							$sub_total_qty=0;
							$sub_total_bundle_num = 0;
							foreach($buyer_name as $job_id => $job_number)
							{
								foreach($job_number as $po_id => $po_name)
								{
									foreach($po_name as $style_ref_id => $style_ref_name)
									{
										foreach($style_ref_name as $style_desc_id => $style_desc_name)
										{
											foreach($style_desc_name as $cut_no_id => $row)
											{
												$i=1;
												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
													<td align="center"><? echo $i;  ?></td>
													<td align="center"><p><? echo $buyer_arr[$row[buyer_name]]." / ".$brand_arr[$row[brand_id]]; ?></p></td>
													<td align="center"><p><? echo $row['job_no']; ?></p></td>
													<td align="center"><p><? echo $row['po_number']; ?></p></td>
													<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
													<td align="center"><p><? echo $row['style_description']; ?></p></td>
													<td align="center"><p><? echo $row['grouping']; ?></p></td>
													<!-- <td align="center"><p><? //echo $country_arr[$row['country_id']]; ?></p></td> -->
													<td align="center"><p><? echo $color_arr[$row['color_number_id']]; ?></p></td>
													<td align="center"><p><? echo $row['cut_no']; ?></p></td>
													<td align="center"><p><? echo $cut_lay_arr[$row['cut_no']][$row['color_number_id']]; ?></p></td>
													<?
													foreach($bundle_size_arr as $size_id)
													{
														// $size_qty=$sizeQtyArr[1][65]['UG-23-00160']['160A102']['Cutting to Input Report']['Cutting to Input QC']['UG-23-000050'][$size_id];
														$size_qty=$sizeQtyArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id][$size_id];
														?>
														<td align="center" width="50"><? echo $size_qty; ?></td>
														<?
														$grand_total_size_arr[$size_id]+=$size_qty;
														$sub_total_size_arr[$size_id]+=$size_qty;
													}
													?>
													<td align="center"><? echo $productionQtyArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id]; ?></td>

													<td align="center"><? echo count($bundleArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id]); ?></td>
													<?
													$color_qty_arr[$color] += $cdata['val'];
													$color_wise_bundle_no_arr[$color] += $cdata['count'];
													?>
													<td align="right"> </td>
												</tr>
												<?
												$grand_total_qty+=$productionQtyArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id];
												$sub_total_qty+=$productionQtyArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id];
												$grand_total_bundle_num+=count($bundleArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id]);
												$sub_total_bundle_num+=count($bundleArr[$color_id][$buyer_id][$job_id][$po_id][$style_ref_id][$style_desc_id][$cut_no_id]);
												$grand_total_reject_qty+=$val['reject_qty'];
												$i++;
											}
										}
									}
								}
								?>
								<tr bgcolor="#E9F3FF">
									<td colspan="9" align="right"><strong>Sub Total </strong></td>
									<?
									foreach($bundle_size_arr as $size_id)
									{
										?>
										<td align="center" width="50"><? echo $sub_total_size_arr[$size_id]; ?></td>
										<?
									}
									?>
									<td align="center"><? echo $sub_total_qty;?></td>
									<td align="center"><? echo $sub_total_bundle_num;?></td>
									<td align="right"> </td>
								</tr>
								<?
							}
						}
					}
					?>
				</tbody>
				<tr>
					<td colspan="9" align="right"><strong>Grand Total </strong></td>
					<?
					foreach($bundle_size_arr as $size_id)
					{
						?>
						<td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
						<?
					}
					?>
					<td align="center"><? echo $grand_total_qty;?></td>
					<td align="center"><? echo $grand_total_bundle_num;?></td>
					<td align="right"> </td>
				</tr>
			</table><br><br>
			<table cellspacing="0" rules="all" style="font: 12px tahoma; margin-left:110px;">
			<tr>
				<td width="90" style="border:1px solid white;"><strong>Transport No</strong></td>
				<td width="200px" style="border:1px solid white;">: <?  ?></td>
				<td width="80" style="border:1px solid white;"><strong>Driver Name</strong></td>
				<td width="190px" style="border:1px solid white;"> : <? ?></td>
				<td width="55" style="border:1px solid white;"><strong>D/L No</strong></td>
				<td width="155px" style="border:1px solid white;">: <? ?> </td>
			</tr>
			</table><br><br>
			<?
			    echo signature_table(243, $data[0], "900px",$cbo_template_id,20,$user_library[$insert_by]);
			?>
			<br><br>
		</div>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	</script>
	<?
	exit();

}

?>
