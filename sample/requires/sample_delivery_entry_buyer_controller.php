<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$item_arrs=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
$color_library=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
$size_library=return_library_array( "select id, size_name from lib_size where status_active=1",'id','size_name');
$req_library=return_library_array( "select id, requisition_number_prefix_num from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)",'id','requisition_number_prefix_num');

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=14 and report_id=170 and is_deleted=0 and status_active=1");
	//echo $print_report_format; disconnect($con); die;
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#print').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print3').hide();\n";
	echo "$('#print4').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==86){echo "$('#print').show();\n";}
			if($id==84){echo "$('#print2').show();\n";}
			if($id==85){echo "$('#print3').show();\n";}
			if($id==881){echo "$('#print4').show();\n";}
		}
	}
	exit();
}

$delivery_sql=sql_select("select sample_dtls_part_tbl_id,sample_name,gmts_item_id,sum(ex_factory_qty) as qc_pass_qty  from  sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0  group by sample_dtls_part_tbl_id,sample_name,gmts_item_id ");
foreach ($delivery_sql as  $result)
{
 	$delivery_arr[$result[csf('sample_dtls_part_tbl_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];
}

$sample_dtls_sql=sql_select("select id,sample_name,gmts_item_id,sample_prod_qty from  sample_development_dtls where entry_form_id in (117,203,449) and status_active=1 and is_deleted=0  group by id,sample_name,gmts_item_id,sample_prod_qty order by id ");
foreach ($sample_dtls_sql as  $result)
{
 	$sample_dtls_arr[$result[csf('id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('sample_prod_qty')];
}


if($db_type==2 || $db_type==1 )
{
	$mrr_date_check=" to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$mrr_date_check=" year(a.insert_date)";
}

$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="populate_data_yet_to_cut")
{
	list($ex_fac_mst,$smp_tbl_id,$req_id,$sample_name,$gmts, $requisition_source)=explode("__", $data);
	if($requisition_source == 3){
		//$val=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_ex_factory_mst_id=$ex_fac_mst and sample_dtls_part_tbl_id=$smp_tbl_id and sample_name=$sample_name and gmts_item_id=$gmts and entry_form_id=132 and sample_development_id=$req_id");
		$val=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$sample_name and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and sample_dtls_part_tbl_id=$smp_tbl_id");
		//echo $val; exit('ww');
	}else{
		$val=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_ex_factory_mst_id=$ex_fac_mst and sample_dtls_part_tbl_id=$smp_tbl_id and sample_name=$sample_name and gmts_item_id=$gmts and entry_form_id=132 and sample_development_id=$req_id");
	}
	echo $val;
	exit();
}

if($action=="booking_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
	?>
	<html>
	<head>
		<script>
			$(document).ready(function(e) {
				$("#txt_search_common").focus();
			});
			function search_populate(str)
			{
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
				 //alert(mst_id);
				var data=mst_id.split("_");
				//alert(data[1]);
				document.getElementById('selected_id').value=data[0];
				document.getElementById('selected_value').value=data[1];
				parent.emailwindow.hide();
			}
		</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
				<table width="1000" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<tr>
						<td align="center" width="100%">
							<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
								<thead>
									<th  colspan="6">
										<?
										echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
										?>
									</th>

								</thead>
								<thead>
									<th width="130">Company Name</th>
									<th width="120">Buyer Name</th>
									<th width="100">Booking No</th>
									<th width="200">Est. Ship Date Range</th>
									<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
								</thead>
								<tr>
									<td>
										<input type="hidden" id="selected_id"/>
										<input type="hidden" id="selected_value"/>
										<?
										echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",0, "-- Select Company --", $company,"load_drop_down( 'sample_delivery_entry_buyer_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
										?>
									</td>
									<td id="buyer_td">
										<?
										echo create_drop_down( "cbo_buyer_name", 120, $blank_array,'', 1, "-- Select Buyer --" );
										?>
									</td>
									<td>
										<input type="text" style="width:100px" class="text_boxes"  name="txt_booking" id="txt_booking"  />
									</td>


									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_search_list_view', 'search_div', 'sample_delivery_entry_buyer_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<?
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
							?>
							<? echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div"></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'sample_delivery_entry_buyer_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);

	if ($data[2]!=0) $company=" and a.company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_id='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.booking_no_prefix_num='$data[1]'"; else $style_id_cond="";
	}
	if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.booking_no_prefix_num like '%$data[1]%' "; else $style_id_cond="";
	}
	if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.booking_no_prefix_num like '$data[1]%' "; else $style_id_cond="";
	}
	if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.booking_no_prefix_num like '%$data[1]' "; else $style_id_cond="";
	}


	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = " and booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = " and booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	if($data[6])
	{
		if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		}
		else
		{
			$year_cond=" and year(a.insert_date)=$data[6]";
		}
	}

	$arr=array (1=>$comp,2=>$buyer_arr);

	 $sql= "SELECT b.id,a.booking_no,a.company_id,a.booking_no_prefix_num, a.buyer_id ,sum(b.bh_qty) as qnty from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company $buyer $style_id_cond $style_cond $estimated_shipdate  $job_cond $year_cond and (a.entry_form_id=0 or a.entry_form_id is null )  group by b.id, a.booking_no,a.company_id,a.booking_no_prefix_num, a.buyer_id order by a.booking_no desc";

	echo  create_list_view("list_view", "Booking No,Company,Buyer Name,Qnty", "140,140,140,50","700","240",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,company_id,buyer_id,0", $arr , "booking_no,company_id,buyer_id,qnty", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
	?>
	<html>
	<head>
		<script>
			$(document).ready(function(e) {
				$("#txt_search_common").focus();
			});
			function search_populate(str)
			{
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
				var data=mst_id.split("_");
				document.getElementById('selected_id').value=data[0];
				document.getElementById('selected_value').value=data[1];
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
				<table width="1000" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<tr>
						<td align="center" width="100%">
							<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
								<thead>
									<th  colspan="6">
										<?
										echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
										?>
									</th>

								</thead>
								<thead>
									<th width="130">Company Name</th>
									<th width="120">Buyer Name</th>
									<th width="100">Order No</th>
									<th width="100">Job No</th>
									<th  width="100" >Style Ref</th>
									<th width="200">Est. Ship Date Range</th>
									<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
								</thead>
								<tr>
									<td>
										<input type="hidden" id="selected_id"/>
										<input type="hidden" id="selected_value"/>
										<?
										echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",0, "-- Select Company --", $company,"load_drop_down( 'sample_delivery_entry_buyer_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
										?>
									</td>
									<td id="buyer_td">
										<?
										echo create_drop_down( "cbo_buyer_name", 120, $blank_array,'', 1, "-- Select Buyer --" );
										?>
									</td>
									<td>
										<input type="text" style="width:100px" class="text_boxes"  name="txt_po" id="txt_po"  />
									</td>

									<td  align="center">
										<input type="text" style="width:100px" class="text_boxes"  name="txt_job" id="txt_job"  />
									</td>

									<td  align="center">
										<input type="text" style="width:100px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />
									</td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_po').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'sample_delivery_entry_buyer_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<?=load_month_buttons(1);  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div"></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'sample_delivery_entry_buyer_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);

	if ($data[2]!=0) $company=" and a.company_name='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.po_number='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no='$data[6]'"; else $style_cond="";
	}
	if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.po_number like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.po_number like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and b.po_number like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]' "; else $style_cond="";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and pub_shipment_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and pub_shipment_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	$job_cond=($data[7])? " and a.job_no_prefix_num='$data[7]'" : "";
	if($data[8])
	{
		if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";
		}
		else
		{
			$year_cond=" and year(a.insert_date)=$data[8]";
		}
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (1=>$comp,2=>$buyer_arr,4=>$product_dept);

	$sql= "SELECT b.id,a.company_name,b.po_number,sum(b.po_quantity) as qnty,a.buyer_name,a.style_ref_no,a.job_no,a.dealing_marchant,a.product_dept from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company $buyer $style_id_cond $style_cond $estimated_shipdate  $job_cond $year_cond group by  b.id,a.company_name,b.po_number, a.buyer_name,a.style_ref_no,a.job_no,a.dealing_marchant,a.product_dept order by a.job_no asc";

	echo  create_list_view("list_view", "Order No,Company,Buyer Name,Style Name,Product Department,Job,Qnty", "60,140,140,100,90,90,90,40","900","240",0, $sql , "js_set_value", "id,po_number", "", 1, "0,company_name,buyer_name,0,product_dept,0,0", $arr , "po_number,company_name,buyer_name,style_ref_no,product_dept,job_no,qnty", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="sample_requisition_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
	?>
	<html>
	<head>
		<script>
			$(document).ready(function(e) {
				$("#txt_search_common").focus();
			});
			function search_populate(str)
			{
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

			function js_set_value( data )
			{
				//var company = $('#cbo_company_mst').val();
				//document.getElementById('selected_id').value=mst_id+'*'+company;
				var data_arr = data.split('_');
				document.getElementById('selected_id').value=data_arr[0];
				document.getElementById('req_no').value=data_arr[1];
				parent.emailwindow.hide();
			}
		</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
				<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<tr>
						<td align="center" width="100%">
							<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
								<thead>
									<th  colspan="6">
										<?
										echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
										?>
									</th>

								</thead>
								<thead>
									<th width="140">Company Name</th>
									<th width="160">Buyer Name</th>
									<th width="130">Requisition No</th>
									<th  width="130" >Style Ref</th>
									<th width="200">Est. Ship Date Range</th>
									<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
								</thead>
								<tr>
									<td width="140">
										<input type="hidden" id="selected_id"/>
										<input type="hidden" id="req_no"/>
										<?
										echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",0, "-- Select Company --", $company,"load_drop_down( 'sample_delivery_entry_buyer_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
										?>
									</td>
									<td id="buyer_td" width="160">
										<?
										echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
										?>
									</td>
									<td width="130">
										<input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
									</td>
									<td width="130" align="center">
										<input type="text" style="width:130px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />
									</td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value, 'create_sample_search_list_view', 'search_div', 'sample_delivery_entry_buyer_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div"></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'sample_delivery_entry_buyer_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if($action=="create_sample_search_list_view")
{
	$data=explode('_',$data);

	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
	if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}


	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
//echo "SELECT qty_source_sample from variable_settings_production where company_name = $data[2] and variable_list=53 and status_active =1 and is_deleted=0";
	$sample_source = sql_select("SELECT qty_source_sample from variable_settings_production where company_name = $data[2] and variable_list=53 and status_active =1 and is_deleted=0");
	//echo count($sample_source).'sddd';
	if(count($sample_source)>0)
	{
		$msg="";
	}
	else
	{
		echo $msg="<b>Please check Delivery Qty Source(Sample) in Variable</b>";die;
	}

 	foreach ($sample_source as $row) {
 		$sample_qty_src = $row[csf('qty_source_sample')];
 	}
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$product_dept,6=>$dealing_marchant);

	/* if($sample_qty_src ==1)
	{
		$sql= "SELECT id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant from sample_development_mst where id in(select sample_development_id from sample_sewing_output_mst where entry_form_id=130  and status_active=1 and is_deleted=0) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id DESC";
	}
 	elseif ($sample_qty_src ==2)
 	{
 		$sql = "SELECT id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant from sample_development_mst where id in(select sample_development_id from sample_ex_factory_dtls where entry_form_id=396 and status_active=1 and is_deleted=0) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id DESC ";
 	} */
	 $sql= "SELECT id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant from sample_development_mst where  entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id DESC";

 	//  echo $sql;
	echo  create_list_view("list_view", "Req No,Year,Company,Buyer Name,Style Name,Product Department,Dealing Merchant", "60,60,140,140,100,90,90,90","960","240",0, $sql , "js_set_value", "id,requisition_number_prefix_num", "", 1, "0,0,company_id,buyer_name,0,product_dept,dealing_marchant", $arr , "requisition_number_prefix_num,year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant", "",'','0,0,0,0,0,0,0') ;

	exit();
}

if($action == 'get_ex_fac_id')
{
	$id = '';
	$mst_sql = sql_select("SELECT sample_ex_factory_mst_id,sample_development_id from sample_ex_factory_dtls where status_active=1 and entry_form_id=132 and sample_development_id = $data group by sample_ex_factory_mst_id,sample_development_id");
	if(count($mst_sql)>0)
	{
		foreach ($mst_sql as $row) {
			$id = $row[csf('sample_ex_factory_mst_id')];
		}
	}
	echo $id.'*'.$id;
}

if($action=="populate_data_from_search_popup")
{
	$data=explode('*',$data);
	$smp_mst_id = sql_select("SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location, delivery_to, ex_factory_date, gp_no, final_destination, received_by, delivery_basis, sent_by, sample_req_source from sample_ex_factory_mst where id=$data[1] and entry_form_id=132 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
	{
		echo "$('#cbo_company_name').val('".$smp_mst_id[0][csf('company_id')]."');\n";
		echo "load_drop_down( 'requires/sample_delivery_entry_buyer_controller', '".$smp_mst_id[0][csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location_name').val('".$smp_mst_id[0][csf('location')]."');\n";
		echo "$('#sample_req_source').val('".$smp_mst_id[0][csf('sample_req_source')]."');\n";
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#txt_challan_no').val('".$smp_mst_id[0][csf('sys_number')]."');\n";
		echo "$('#cbo_delivery_to').val('".$smp_mst_id[0][csf('delivery_to')]."');\n";
		echo "$('#txt_gp_no').val('".$smp_mst_id[0][csf('gp_no')]."');\n";
		echo "$('#txt_final_destination').val('".$smp_mst_id[0][csf('final_destination')]."');\n";
		echo "$('#txt_received_by').val('".$smp_mst_id[0][csf('received_by')]."');\n";
		echo "$('#cbo_delivery_basis').val('".$smp_mst_id[0][csf('delivery_basis')]."');\n";
		echo "$('#txt_sent_by').val('".$smp_mst_id[0][csf('sent_by')]."');\n";
		echo "$('#cbo_company_name').attr('disabled','disabled');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($smp_mst_id[0][csf('ex_factory_date')])."');\n";
		if($smp_mst_id[0][csf('delivery_basis')] == 1)
		{
			echo "$('#shipment_status_id').css('visibility','visible');\n";
		}
		else
		{
			echo "$('#shipment_status_id').css('visibility','hidden');\n";
		}
	}
	exit();
}

if($action=="populate_po_data_from_search_popup")
{
	$res = sql_select("SELECT b.po_number,b.id,a.company_name,a.location_name,a.buyer_name,a.style_ref_no from wo_po_details_master a,wo_po_break_down b  where b.id=$data  and a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	foreach($res as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_name')]."');\n";
		echo "load_drop_down( 'requires/sample_delivery_entry_buyer_controller', '".$result[csf("company_name")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_sample_requisition_id').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_sample_stage').val('1');\n";
	}

	$smp_mst_id = sql_select("SELECT  sent_by,delivery_basis, id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to, ex_factory_date, gp_no, final_destination,received_by from sample_ex_factory_mst where id in(select sample_ex_factory_mst_id from sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0 and sample_development_id=$data) and entry_form_id=132 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
	{
		echo "$('#cbo_company_name').val('".$smp_mst_id[0][csf('company_id')]."');\n";
		echo "load_drop_down( 'requires/sample_delivery_entry_buyer_controller', '".$smp_mst_id[0][csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
 		echo "$('#txt_challan_no').val('".$smp_mst_id[0][csf('sys_number')]."');\n";
		echo "$('#cbo_delivery_to').val('".$smp_mst_id[0][csf('delivery_to')]."');\n";
		echo "$('#txt_gp_no').val('".$smp_mst_id[0][csf('gp_no')]."');\n";
		echo "$('#txt_final_destination').val('".$smp_mst_id[0][csf('final_destination')]."');\n";
		echo "$('#txt_received_by').val('".$smp_mst_id[0][csf('received_by')]."');\n";
		echo "$('#cbo_delivery_basis').val('".$smp_mst_id[0][csf('delivery_basis')]."');\n";
		echo "$('#txt_sent_by').val('".$smp_mst_id[0][csf('sent_by')]."');\n";
	}
	exit();
}

if($action=="populate_booking_data_from_search_popup")
{
	$res = sql_select("SELECT b.booking_no,b.id,a.company_id,a.buyer_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls  b  where b.id=$data  and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	foreach($res as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
 		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_id')]."');\n";
 		echo "$('#txt_sample_requisition_id').val('".$result[csf('booking_no')]."');\n";
		echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_sample_stage').val('0');\n";
	}

	$smp_mst_id = sql_select("SELECT  delivery_basis,sent_by,id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to, ex_factory_date, gp_no, final_destination,received_by from sample_ex_factory_mst where id in(select sample_ex_factory_mst_id from sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0 and sample_development_id=$data) and entry_form_id=132 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
	{
		echo "$('#cbo_company_name').val('".$smp_mst_id[0][csf('company_id')]."');\n";
		echo "load_drop_down( 'requires/sample_delivery_entry_buyer_controller', '".$smp_mst_id[0][csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
 		echo "$('#txt_challan_no').val('".$smp_mst_id[0][csf('sys_number')]."');\n";
		echo "$('#cbo_delivery_to').val('".$smp_mst_id[0][csf('delivery_to')]."');\n";
		echo "$('#txt_gp_no').val('".$smp_mst_id[0][csf('gp_no')]."');\n";
		echo "$('#txt_final_destination').val('".$smp_mst_id[0][csf('final_destination')]."');\n";
		echo "$('#txt_received_by').val('".$smp_mst_id[0][csf('received_by')]."');\n";
		echo "$('#cbo_delivery_basis').val('".$smp_mst_id[0][csf('delivery_basis')]."');\n";
		echo "$('#txt_sent_by').val('".$smp_mst_id[0][csf('sent_by')]."');\n";
	}
	exit();
}

if($action=="show_sample_item_listview")
{
	$data = explode('*', $data);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">Sample Name</th>
			<th width="120">Garments Item</th>
			<th width="75">Color</th>
			<th>Sample Qty</th>
		</thead>
		<?
		$i=1;
		if($data[1] == 2)
		{
			//pro_gmts_delivery_dtls,transfer_criteria
			$sql_sam=sql_select("select a.sys_number from pro_gmts_delivery_dtls b,pro_gmts_delivery_mst a where a.id=b.mst_id  and a.transfer_criteria=2 and  b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.to_po_id in($data[0])");
			foreach($sql_sam as $row)
		   {
			  $sys_number=$row[csf('sys_number')];
		   }
			//echo "SELECT transfer_criteria   from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id = b.sample_ex_factory_mst_id join sample_ex_factory_colorsize c on a.id = c.sample_ex_factory_mst_id and b.id = c.sample_ex_factory_dtls_id where b.sample_development_id = $data[0] and a.entry_form_id =396  and a.status_active=1 and a.is_deleted=0 group by b.id,b.gmts_item_id,b.sample_name,c.color_id order by b.id desc";
			$sqlResult = sql_select("SELECT b.id,b.gmts_item_id,b.sample_name,c.color_id as sample_color,sum(c.size_pass_qty) as size_qty  from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id = b.sample_ex_factory_mst_id join sample_ex_factory_colorsize c on a.id = c.sample_ex_factory_mst_id and b.id = c.sample_ex_factory_dtls_id where b.sample_development_id = $data[0] and a.entry_form_id =396  and a.status_active=1 and a.is_deleted=0 group by b.id,b.gmts_item_id,b.sample_name,c.color_id order by b.id desc");
			if($sys_number!="" && count($sqlResult)<=0) //Issue Id=11602
			{
				$sqlResult = sql_select("SELECT b.id,b.gmts_item_id,b.sample_name,b.sample_color,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");
			}
		}
		else
		{
			$sqlResult = sql_select("SELECT b.id,b.gmts_item_id,b.sample_name,b.sample_color,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");
		}

		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>,<? echo $row[csf('gmts_item_id')]; ?>);">
				<td><? echo $i; ?></td>
				<td><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
				<td><p><? echo $item_arrs[$row[csf('gmts_item_id')]]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('sample_color')]]; ?></p></td>
				<td align="right"><?php echo $row[csf('size_qty')]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}
if($action=="show_po_item_listview")
{
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">Po number</th>
			<th width="120">Garments Item</th>
 			<th>Order Qty</th>
		</thead>
		<?
		$i=1;

		$sqlResult = "SELECT b.po_number,b.id,c.item_number_id,sum(c.order_quantity) as size_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c
			  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,c.item_number_id, b.po_number order by b.id asc" ;

		foreach(sql_select($sqlResult) as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_po_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('id')]; ?>,<? echo $row[csf('item_number_id')]; ?>);">
				<td><? echo $i; ?></td>
				<td><p><? echo $row[csf('po_number')]; ?></p></td>
				<td><p><? echo $item_arrs[$row[csf('item_number_id')]]; ?></p></td>

				<td align="right"><?php echo $row[csf('size_qty')]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="show_booking_item_listview")
{
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">Sample Name</th>
			<th width="120">Booking No</th>
  			<th>BH Qty</th>
		</thead>
		<?
		$i=1;

		$sqlResult = "SELECT sample_type,id,booking_no,sum(bh_qty) as size_qty from wo_non_ord_samp_booking_dtls  where  id=$data   and  status_active=1 and is_deleted=0  group by sample_type,id,booking_no order by  id desc" ;

		foreach(sql_select($sqlResult) as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_booking_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_type')]; ?>,<? echo $row[csf('sample_type')]; ?>);">
				<td><? echo $i; ?></td>

				<td><p><? echo $sample_name_library[$row[csf('sample_type')]]; ?></p></td>
			 	<td><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td align="right"><?php echo $row[csf('size_qty')]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="color_and_size_level")
{
	list($sample_dtls_part_tbl_id,$smp_id,$req_id,$gmts,$req_src,$mst_id)=explode('**',$data);
	if($mst_id != '')
	{
		$ex_factory_mst = sql_select("SELECT b.sample_development_id from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id = b.sample_ex_factory_mst_id  where a.entry_form_id =132 and a.status_active=1 and a.is_deleted=0 and a.id = $mst_id and b.sample_dtls_part_tbl_id= $sample_dtls_part_tbl_id and b.entry_form_id =132 and b.status_active=1 and b.is_deleted=0");
		foreach ($ex_factory_mst as $row) {
			$req_id = $row[csf('sample_development_id')];
		}
	}
	//join sample_development_mst c on b.sample_development_id = c.id
	if($req_src==2)
	{
		$is_exists_wash_dyeing=return_field_value("id","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0 and entry_form_id=132");
		$ex_factory_dtls = sql_select("SELECT * from sample_ex_factory_dtls where entry_form_id =396 and status_active=1 and is_deleted=0 and id = $sample_dtls_part_tbl_id");
	}
	else
	{
		$is_exists_wash_dyeing=return_field_value("id","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and sample_dtls_row_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0 and entry_form_id=131");
	}

	if(count($ex_factory_dtls)>0)
	{
		foreach ($ex_factory_dtls as $row) {
			$sample_development_data = $row[csf('sample_development_id')];
			$sample_dtls_part_tbl_data = $row[csf('sample_dtls_part_tbl_id')];
		}
	}

	$val_req_embel=return_field_value("id","sample_development_fabric_acc","sample_name_re=$smp_id and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3 and name_re<>1 and  name_re<>2 and name_re<>4");
	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$smp_id and gmts_item_id_re=$gmts order by id asc");
 	$name_id=$emb_names[0][csf('name')];
	$name_arr=explode(',', $name_id);
	$last_emb=end($name_arr);
	// echo "$('#txt_sample_requisition_id').val('".$last_emb."');\n"; die;

 	if(trim($is_exists_wash_dyeing)=='' && $val_req_embel=='')
	{
		//echo __LINE__; die;
  		 //echo "$('#txt_sample_requisition_id').val('is_exist_emb');\n"; die;
  		$colorResult_qc_pass = sql_select("SELECT b.sample_name,c.color_id,c.size_id,c.size_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and b.sample_name=$smp_id and b.gmts_item_id=$gmts and b.sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");
	//	echo $req_src.'D=======S';

 		if($req_src==2)
		{
			$colorResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_name=$smp_id  and b.gmts_item_id=$gmts and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=396 and a.entry_form_id=396 and c.entry_form_id=396 and c.color_id is not null and b.id =$sample_dtls_part_tbl_id");
		}
		else
		{
			$colorResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=130 and a.entry_form_id=130 and c.entry_form_id=130 and c.color_id is not null");

		}
		$serial=count($colorResult);
		if($serial==0 || $serial=='')
		{
			if($req_src == 2)
			{
				echo "alert('Sample delivery to MKT data not available for this sample and item');\n";
			}
			else
			{
				// echo "alert('Sewing out put data not available for this sample and item');\n";
			}
		}
		if($req_src==2)
		{
			$total_cut=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396 and id=$sample_dtls_part_tbl_id");
			$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id=$req_id");
			echo "$('#dynamic_cut_qty').html('Delivery Qty');\n";
		}
		else
		{
			$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$sample_dtls_part_tbl_id");
			$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");
			echo "$('#dynamic_cut_qty').html('Total Sewing Qty');\n";
		}
	}
	else
	{
		//echo __LINE__; die;
		$colorResult_qc_pass = sql_select("select
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty
		from
			sample_ex_factory_mst a,
			sample_ex_factory_dtls b,
			sample_ex_factory_colorsize c
		where
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and b.sample_name=$smp_id and b.gmts_item_id=$gmts and b.sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");
		if($req_src == 2)
		{
			$colorResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_name=$smp_id  and b.gmts_item_id=$gmts and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=396 and a.entry_form_id=396 and c.entry_form_id=396 and c.color_id is not null and b.id =$sample_dtls_part_tbl_id");
			$total_cut=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396");
			$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and SAMPLE_DEVELOPMENT_ID=$req_id");

		}
		else
		{
			$colorResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=131 and b.embel_name=$last_emb and c.color_id is not null");
			$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=131 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$last_emb");
			//echo "SELECT sum(ex_factory_qty) as qty from sample_ex_factory_dtls where sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id"; die;
  		 	$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");
		}

 		$serial=count($colorResult);
		if($req_src != 3)
		{
			if($serial==0 || $serial=='')
			{
				echo "alert('as per requisitions(Embellishment) you have to $emblishment_name_array[$last_emb] first then delivery.');\n";
			}
		}
		
  		// echo "$('#txt_sample_requisition_id').val('a $total_cuml');\n"; die;
  		echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$last_emb] Qty');\n";
	}

	foreach($colorResult_qc_pass as $row)
	{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
		$totQcPassQty+=$row[csf("size_pass_qty")];
	}
	foreach($colorResult as $row)
	{
		$colorData[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
	}
	// ======================= getting transfer data ============================
	$sql = "SELECT c.color_id,c.size_id, sum(case when c.trans_type=5 then c.production_qnty else 0 end) as trans_in_qty,sum(case when c.trans_type=6 then c.production_qnty else 0 end) as trans_out_qty from PRO_GMTS_DELIVERY_MST a, PRO_GARMENTS_PRODUCTION_MST b, PRO_GARMENTS_PRODUCTION_DTLS c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transfer_criteria in(2,3) and b.production_type=10 and b.po_break_down_id=$req_id group by c.color_id,c.size_id";
	// echo $sql;die;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		$colorData[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
		$total_cut+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
	}
	if($req_src == 3)
	{
		$colorSizeRes = sql_select("SELECT b.id,b.gmts_item_id,b.sample_name,b.sample_color,c.SIZE_ID,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$req_id and b.gmts_item_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id,c.SIZE_ID order by b.id asc");
		foreach($colorSizeRes as $row)
		{
			$colorData[$row[csf("sample_color")]][$row[csf("SIZE_ID")]]+=$row[csf("size_qty")];
			$oder_qty+=$row[csf("size_qty")];
		}
		
	}
	//echo $colorSizeRes; exit();

	foreach($colorData as $color_id=>$color_value)
	{
		$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
		$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
		$i=1;
		foreach($color_value as $size_id=>$total_qty)
		{
			$colorID .= $color_id."*".$size_id.",";

			$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($total_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'"  onblur="fn_total('.$color_id.','.$i.')"><input type="hidden" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';

			$i++;
		}
		$colorHTML .= "</table></div>";

	}
	echo "$('#txt_cumul_delivery_qty').val('');\n";
	echo "$('#txt_total_finished_qty').val('');\n";
	echo "$('#txt_yet_to_delivery').val('');\n";
	echo "$('#txt_delivery_qty').val('');\n";
	//echo "$('#txt_reject_qnty').val('');\n";
	echo "$('#txt_remark').val('');\n";
	$res = sql_select("select requisition_number_prefix_num,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$req_id  and status_active=1 and is_deleted=0");
	foreach($res as $result)
	{
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#txt_sample_requisition_id').val('".$result[csf('requisition_number_prefix_num')]."');\n";
	}
	if($req_src==2)
	{
		$value=return_field_value("gmts_item_id","sample_ex_factory_dtls","entry_form_id=396 and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");
	}
	else
	{
		$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id in (117,203,449) and sample_mst_id=$req_id and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");
	}

  	$qty=return_field_value("sum(total_qty)","sample_development_size","mst_id=$req_id and dtls_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");


  	//$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name");


	$name_re_val=return_field_value("name_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
	$type_re_val=return_field_value("type_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");

	
	

	echo "$('#cbo_item_name').val(".$value.");\n";
	if(($total_cut-$total_cuml)==0 || ($total_cut-$total_cuml)<0){
		$is_posted_account=return_field_value("is_posted_account as is_posted_account","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and SAMPLE_DEVELOPMENT_ID=$req_id and is_posted_account=1","is_posted_account");
		if($is_posted_account==1) $ac_msg="Already Posted in Accounts.";
		else $ac_msg="";
		echo "$('#is_posted').val(".$is_posted_account.");\n";
	}
	
	echo "$('#posted_account_td').text('".$ac_msg."');\n";
	echo "$('#cbo_item_name').attr('disabled','disabled');\n";
	echo "$('#cbo_sample_name').attr('disabled','disabled');\n";
	echo "$('#txt_sample_qty').val(".$qty.");\n";
	echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
	echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
	echo "var smpqty=$('#txt_sample_qty').val();\n";
	echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
	echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
	echo "$('#txt_yet_to_delivery').val(total_cuts-qcqty);\n";
	if($req_src == 3)
	{
		$yet_to_delivery = $oder_qty-$total_cuml;
		echo "$('#txt_yet_to_delivery').val(".$yet_to_delivery.");\n";
	}
	echo "$('#dtls_update_id').val('');\n";
	echo "$('#cbo_sample_name').val(".$smp_id.");\n";
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	echo "$('#hidden_requisition_id').val('".$req_id."');\n";
	echo "$('#hidden_sample_dtls_tbl_id').val(".$sample_dtls_part_tbl_id.");\n";
	echo "$('#txt_sample_quantity').val(".$qty.");\n";

    exit();
}

if($action=="color_and_size_level_po")
{
	list($po_id,$po_number,$req_id,$gmts)=explode('**',$data);
	$colorResult_qc_pass = sql_select("SELECT
		b.gmts_item_id,c.color_id,c.size_id,c.size_pass_qty
	from
		sample_ex_factory_mst a,
		sample_ex_factory_dtls b,
		sample_ex_factory_colorsize c
	where
		a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132  and b.gmts_item_id='$gmts' ");

	$colorResult = "SELECT c.color_number_id,c.size_number_id,c.order_quantity from wo_po_break_down b, wo_po_color_size_breakdown   c where b.id=c.po_break_down_id   and c.item_number_id='$gmts' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id='$po_id'";
	$colorResult=sql_select($colorResult);


	 $total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","  gmts_item_id='$gmts' and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id='$po_id'");
	 $total_cut=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$gmts'  and status_active=1 and is_deleted=0");

	 foreach($colorResult_qc_pass as $row)
	 {
		$qcPassQtyArr[$row[csf("gmts_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
		$totQcPassQty+=$row[csf("size_pass_qty")];
	 }

	 foreach($colorResult as $row)
	 {
		$colorData[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf("order_quantity")];
	 }

	foreach($colorData as $color_id=>$color_value)
	{
		$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
		$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
		$i=1;
		foreach($color_value as $size_id=>$total_qty)
		{
			$colorID .= $color_id."*".$size_id.",";

			$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px"  onblur="fn_total('.$color_id.','.$i.')"><input type="hidden" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';

			$i++;
		}
		$colorHTML .= "</table></div>";
	}
	echo "$('#txt_cumul_delivery_qty').val('');\n";
	echo "$('#txt_total_finished_qty').val('');\n";
	echo "$('#txt_yet_to_delivery').val('');\n";
	echo "$('#txt_delivery_qty').val('');\n";
	echo "$('#txt_remark').val('');\n";
	$qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$gmts'  and status_active=1 and is_deleted=0");
	echo "$('#cbo_item_name').val(".$gmts.");\n";
	echo "$('#txt_sample_qty').val(".$qty.");\n";
	echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
	echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
	echo "var smpqty=$('#txt_sample_qty').val();\n";
	echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
	echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
	echo "$('#txt_yet_to_delivery').val(total_cuts-qcqty);\n";
	echo "$('#dtls_update_id').val('');\n";
	echo "$('#cbo_sample_name').val(".$smp_id.");\n";
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	echo "$('#hidden_sample_dtls_tbl_id').val(".$sample_dtls_part_tbl_id.");\n";

    exit();
}

if($action=="color_and_size_level_booking")
{
		list($booking_id,$sample,$req_id,$sample)=explode('**',$data);
		$colorResult_qc_pass = sql_select("SELECT
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty
		from
			sample_ex_factory_mst a,
			sample_ex_factory_dtls b,
			sample_ex_factory_colorsize c
		where
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132  and b.sample_name='$sample' ");

	    $colorResult = "SELECT gmts_color,gmts_size,bh_qty from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0  and id='$booking_id'";
		$colorResult=sql_select($colorResult);


  		 $total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","  sample_name='$sample' and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id='$booking_id'");
  		 $total_cut=return_field_value("sum(bh_qty)","wo_non_ord_samp_booking_dtls","id=$booking_id and sample_type='$sample'  and status_active=1 and is_deleted=0");

  		 foreach($colorResult_qc_pass as $row)
  		 {
  		 	$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
  		 	$totQcPassQty+=$row[csf("size_pass_qty")];
  		 }

  		 foreach($colorResult as $row)
  		 {
  		 	$colorData[$row[csf("gmts_color")]][$row[csf("gmts_size")]]+=$row[csf("bh_qty")];
  		 }

		foreach($colorData as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$total_qty)
			{

 				$colorID .= $color_id."*".$size_id.",";

				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px"  onblur="fn_total('.$color_id.','.$i.')"><input type="hidden" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';

				$i++;
			}
			$colorHTML .= "</table></div>";
		}
		echo "$('#txt_cumul_delivery_qty').val('');\n";
		echo "$('#txt_total_finished_qty').val('');\n";
		echo "$('#txt_yet_to_delivery').val('');\n";
 		echo "$('#txt_delivery_qty').val('');\n";
 		echo "$('#txt_remark').val('');\n";
   		$qty=return_field_value("sum(bh_qty)","wo_non_ord_samp_booking_dtls","id=$booking_id and sample_type='$sample'  and status_active=1 and is_deleted=0");
  		echo "$('#cbo_item_name').val(".$gmts.");\n";
 		echo "$('#txt_sample_qty').val(".$qty.");\n";
		echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
		echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
  		echo "var smpqty=$('#txt_sample_qty').val();\n";
  		echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
		echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
		echo "$('#txt_yet_to_delivery').val(total_cuts-qcqty);\n";
 		echo "$('#dtls_update_id').val('');\n";
		echo "$('#cbo_sample_name').val(".$sample.");\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		echo "$('#hidden_sample_dtls_tbl_id').val(".$sample_dtls_part_tbl_id.");\n";

	   exit();
}

if($action=="show_dtls_listview")
{
	list($smp_id,$mst_id)=explode('*',$data);
	if($mst_id) $sql_con="sample_ex_factory_mst_id=$mst_id";
	//else $sql_con="sample_development_id=$smp_id";
	?>


		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table">
            <thead>
				<th width="30">SL</th>
				<th width="50">Req No</th>
				<th width="110">Sample Name</th>
				<th width="110">Garments Item</th>
				<th width="80">Delivery Qnty</th>
				<th width="80">Carton Qnty</th>

				<th>Remarks</th>
            </thead>
		</table>


    <div style="width:550px; max-height:260px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" id="tbl_list_search">
		<?php

	$i = 1;
	$sqlResult = sql_select("SELECT id,sample_ex_factory_mst_id, sample_development_id, sample_name,gmts_item_id, ex_factory_qty, carton_qty,carton_per_qty, remarks, shiping_status,sample_dtls_part_tbl_id from sample_ex_factory_dtls where $sql_con and status_active=1 and is_deleted=0 and entry_form_id=132");
	foreach ($sqlResult as $row) {
	$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";

	?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('sample_development_id')].'**'.$row[csf('sample_ex_factory_mst_id')].'**'.$row[csf('id')].'**'.$row[csf('sample_name')].'**'.$row[csf('gmts_item_id')].'**'.$row[csf('sample_dtls_part_tbl_id')]; ?>','populate_input_form_data','requires/sample_delivery_entry_buyer_controller');" >
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="50"><? echo $req_library[$row[csf('sample_development_id')]]; ?></td>
                <td width="110"><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
                <td width="110"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                <td width="80" align="right"><?php echo $row[csf('ex_factory_qty')]; ?></td>
                <td align="right" width="80"><?php echo $row[csf('carton_qty')]; ?></td>

                <td><p><? echo $row[csf('remarks')]; ?></p></td>
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

if($action=="populate_input_form_data")
{

 	list($req_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);
 	$is_exists_wash_dyeing=return_field_value("id","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and sample_dtls_row_id=$sample_dtls_row_id and status_active=1 and is_deleted=0 and entry_form_id=131");
 	$delivery_basis=return_field_value("delivery_basis","sample_ex_factory_mst","id=$mst_id  and  status_active=1 and is_deleted=0 and entry_form_id=132");
 	if($delivery_basis==2)
 	{

 		if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
 		else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

 		$colorResult = sql_select("SELECT c.sys_number, c.delivery_basis, a.id, a.order_type, a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.delivery_date, a.carton_qty, a.carton_per_qty, a.remarks, a.is_posted_account,a.shiping_status, a.export_invoice_id, b.color_id as sample_color, b.size_id, b.size_pass_qty as size_qty
		from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b
		where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and a.sample_ex_factory_mst_id = $mst_id and b.sample_ex_factory_mst_id = $mst_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132    and a.id='$dtls_id'");


 		foreach($colorResult as $row)
 		{
 			if($row[csf("sample_development_id")]){
 				$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
 				$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];

 				$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
 				$totSizeQcPassQty+=$row[csf("size_qty")];

 				$dtlsArr[$row[csf("id")]]['order_type']=$row[csf('order_type')];
 				$dtlsArr[$row[csf("id")]]['sample_development_id']=$row[csf('sample_development_id')];
 				$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
 				$dtlsArr[$row[csf("id")]]['sys_number']=$row[csf('sys_number')];
 				$dtlsArr[$row[csf("id")]]['delivery_basis']=$row[csf('delivery_basis')];
 				$dtlsArr[$row[csf("id")]]['gmts_item_id']=$row[csf('gmts_item_id')];
 				$dtlsArr[$row[csf("id")]]['ex_factory_qty']=$row[csf('ex_factory_qty')];
 				$dtlsArr[$row[csf("id")]]['delivery_date']=$row[csf('delivery_date')];
 				$dtlsArr[$row[csf("id")]]['carton_qty']=$row[csf('carton_qty')];
 				$dtlsArr[$row[csf("id")]]['invoice_no']=$row[csf('invoice_no')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_id']=$row[csf('lc_sc_id')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_no']=$row[csf('lc_sc_no')];
 				$dtlsArr[$row[csf("id")]]['carton_per_qty']=$row[csf('carton_per_qty')];
 				$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
 				$dtlsArr[$row[csf("id")]]['shiping_status']=$row[csf('shiping_status')];
				$dtlsArr[$row[csf("id")]]['export_invoice_id']=$row[csf('export_invoice_id')];
 				$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_part_tbl_id')];
		if($row[csf("is_posted_account")]==1) $ac_msg="Already Posted in Accounts.";
		else $ac_msg="";
		$dtlsArr[$row[csf("id")]]['posted']=$ac_msg;
		$dtlsArr[$row[csf("id")]]['is_posted']=$row[csf("is_posted_account")];

 			}
 		}
		$ac_msg=$dtlsArr[$dtls_id]['posted'];
		$is_posted=$dtlsArr[$dtls_id]['is_posted'];
 		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
 		echo "$('#txt_challan_no').val('".$dtlsArr[$dtls_id]['sys_number']."');\n";
 		echo "$('#mst_update_id').val('".$mst_id."');\n";
 		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";

		echo "$('#posted_account_td').text('".$ac_msg."');\n";
		echo "$('#is_posted').val('".$is_posted."');\n";

		$invoice_id=$dtlsArr[$dtls_id]['export_invoice_id'];
 		$invoice_number=return_field_value("invoice_no","com_export_invoice_ship_mst","id=$invoice_id");
		echo "$('#txt_invoice_id').val('".$dtlsArr[$dtls_id]['export_invoice_id']."');\n";
		echo "$('#txt_invoice_no').val('".$invoice_number."');\n";

 		$po_id=$dtlsArr[$dtls_id]['sample_development_id'];
 		$po_number=return_field_value("po_number","wo_po_break_down","id=$po_id");
 		echo "$('#txt_sample_requisition_id').val('".$po_number."');\n";

 		echo "$('#hidden_requisition_id').val('".$dtlsArr[$dtls_id]['sample_development_id']."');\n";
 		echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['gmts_item_id']."');\n";
 		echo "$('#txt_delivery_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#hidden_previous_delv_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#txt_delivery_date').val('".change_date_format($dtlsArr[$dtls_id]['delivery_date'])."');\n";

 		echo "$('#txt_carton_qnty').val('".$dtlsArr[$dtls_id]['carton_qty']."');\n";
 		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
 		echo "$('#cbo_shipping_status').val('".$dtlsArr[$dtls_id]['shiping_status']."');\n";

 		$sqlResult = sql_select("SELECT c.color_number_id,c.size_number_id,c.order_quantity from  wo_po_color_size_breakdown c  where c.po_break_down_id=$po_id and    c.item_number_id=".$dtlsArr[$dtls_id]['gmts_item_id']."  and c.status_active=1 and c.is_deleted=0 ");

 		$total_cut=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown"," item_number_id=$gmts and status_active=1 and is_deleted=0  and po_break_down_id=$po_id");
 		$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls"," gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id=$po_id");

 		foreach($sqlResult as $row)
 		{
 			$smp_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf("order_quantity")];
 			$color_arrs[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("color_number_id")];
 		}
		// ======================= getting transfer data ============================
		$sql = "SELECT c.color_id,c.size_id, sum(case when c.trans_type=5 then c.production_qnty else 0 end) as trans_in_qty,sum(case when c.trans_type=6 then c.production_qnty else 0 end) as trans_out_qty from PRO_GMTS_DELIVERY_MST a, PRO_GARMENTS_PRODUCTION_MST b, PRO_GARMENTS_PRODUCTION_DTLS c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transfer_criteria in(2,3) and b.production_type=10 and b.po_break_down_id=$req_id group by c.color_id,c.size_id";
		// echo $sql;die;
		$res = sql_select($sql);
		foreach($res as $row)
		{
			$smp_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
			$total_cut+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
		}

 		$chkColor = array();
 		foreach($color_arrs as $color_id=>$color_value)
 		{
 			//echo "$color_id rrr";
 			if( !in_array( $color_id, $chkColor ) )
 			{
 				$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
 				$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
 				$i=1;
 				foreach($color_value as $size_id=>$size_qty)
 				{
 					$colorID .= $color_id."*".$size_id.",";
 					//$size_qty=$sizeQcPassQty[$color_id][$size_id];
 					$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($sizeQcPassQty[$color_id][$size_id]-$size_qty)).'" onblur="fn_total('.$color_id.','.$i.')"><input type="hidden" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';
 					$i++;
 				}
 				$colorHTML .= "</table></div>";

 				$chkColor[] = $color[csf("color_number_id")];
 			}
 		}
		//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);

 		echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
 		echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
 		echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
 		echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
 		echo "$('#txt_yet_to_delivery').val(total_cuts*1-qcqty*1);\n";

 		echo "set_button_status(1, permission, 'fnc_sample_delivery_entry',1,0);\n";
 		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
 		$colorList = substr($colorID,0,-1);
 		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
 		//echo "$('#hidden_sample_dtls_tbl_id').val('".$sample_dtls_row_id."');\n";
 	}
	else if($delivery_basis==3)
 	{
 		if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
 		else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

 		$colorResult = sql_select("SELECT c.sys_number, c.delivery_basis, a.id, a.order_type, a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.delivery_date, a.carton_qty, a.carton_per_qty, a.remarks, a.is_posted_account,a.shiping_status, a.export_invoice_id, b.color_id as sample_color, b.size_id, b.size_pass_qty as size_qty
		from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b
		where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and a.sample_ex_factory_mst_id = $mst_id and b.sample_ex_factory_mst_id = $mst_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132   ");

 		foreach($colorResult as $row)
 		{
 			if($row[csf("sample_development_id")]){
 				$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
 				$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];

 				$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
 				$totSizeQcPassQty+=$row[csf("size_qty")];

 				$dtlsArr[$row[csf("id")]]['order_type']=$row[csf('order_type')];
 				$dtlsArr[$row[csf("id")]]['sample_development_id']=$row[csf('sample_development_id')];
 				$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
 				$dtlsArr[$row[csf("id")]]['sys_number']=$row[csf('sys_number')];
 				$dtlsArr[$row[csf("id")]]['delivery_basis']=$row[csf('delivery_basis')];
 				$dtlsArr[$row[csf("id")]]['gmts_item_id']=$row[csf('gmts_item_id')];
 				$dtlsArr[$row[csf("id")]]['ex_factory_qty']=$row[csf('ex_factory_qty')];
 				$dtlsArr[$row[csf("id")]]['delivery_date']=$row[csf('delivery_date')];
 				$dtlsArr[$row[csf("id")]]['carton_qty']=$row[csf('carton_qty')];
 				$dtlsArr[$row[csf("id")]]['invoice_no']=$row[csf('invoice_no')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_id']=$row[csf('lc_sc_id')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_no']=$row[csf('lc_sc_no')];
 				$dtlsArr[$row[csf("id")]]['carton_per_qty']=$row[csf('carton_per_qty')];
 				$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
 				$dtlsArr[$row[csf("id")]]['shiping_status']=$row[csf('shiping_status')];
				$dtlsArr[$row[csf("id")]]['export_invoice_id']=$row[csf('export_invoice_id')];
 				$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_part_tbl_id')];
				if($row[csf("is_posted_account")]==1) $ac_msg="Already Posted in Accounts.";
				else $ac_msg="";
				$dtlsArr[$row[csf("id")]]['posted']=$ac_msg;
				$dtlsArr[$row[csf("id")]]['is_posted']=$row[csf("is_posted_account")];
 			}
 		}
		$ac_msg=$dtlsArr[$dtls_id]['posted'];
		$is_posted=$dtlsArr[$dtls_id]['is_posted'];
 		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
 		echo "$('#txt_challan_no').val('".$dtlsArr[$dtls_id]['sys_number']."');\n";
 		echo "$('#mst_update_id').val('".$mst_id."');\n";
 		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
		echo "$('#posted_account_td').text('".$ac_msg."');\n";
		echo "$('#is_posted').val('".$is_posted."');\n";

		$invoice_id=$dtlsArr[$dtls_id]['export_invoice_id'];
 		$invoice_number=return_field_value("invoice_no","com_export_invoice_ship_mst","id=$invoice_id");
		echo "$('#txt_invoice_id').val('".$dtlsArr[$dtls_id]['export_invoice_id']."');\n";
		echo "$('#txt_invoice_no').val('".$invoice_number."');\n";

 		$po_id=$dtlsArr[$dtls_id]['sample_development_id'];
 		$booking_no=return_field_value("booking_no","wo_non_ord_samp_booking_dtls","id=$po_id");
 		echo "$('#txt_sample_requisition_id').val('".$booking_no."');\n";

 		echo "$('#hidden_requisition_id').val('".$dtlsArr[$dtls_id]['sample_development_id']."');\n";
 		echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['gmts_item_id']."');\n";
 		echo "$('#txt_delivery_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#hidden_previous_delv_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#txt_delivery_date').val('".change_date_format($dtlsArr[$dtls_id]['delivery_date'])."');\n";

 		echo "$('#txt_carton_qnty').val('".$dtlsArr[$dtls_id]['carton_qty']."');\n";
 		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
 		echo "$('#cbo_shipping_status').val('".$dtlsArr[$dtls_id]['shiping_status']."');\n";
		//$colorResult = "SELECT gmts_color,gmts_size,bh_qty from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0  and id='$booking_id'";
 		$sqlResult = sql_select("SELECT  gmts_color,gmts_size,bh_qty  from  wo_non_ord_samp_booking_dtls where id=$po_id and    sample_type=".$dtlsArr[$dtls_id]['sample_name']."  and status_active=1 and is_deleted=0 ");

 		$total_cut=return_field_value("sum(bh_qty)","wo_non_ord_samp_booking_dtls"," sample_type='$sample_name' and status_active=1 and is_deleted=0  and id=$po_id");
 		$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls"," gmts_item_id='$gmts' and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id=$po_id");

 		foreach($sqlResult as $row)
 		{
 			$smp_qty_arr[$row[csf("gmts_color")]][$row[csf("gmts_size")]]+=$row[csf("bh_qty")];
 		}
		 // ======================= getting transfer data ============================
		 $sql = "SELECT c.color_id,c.size_id, sum(case when c.trans_type=5 then c.production_qnty else 0 end) as trans_in_qty,sum(case when c.trans_type=6 then c.production_qnty else 0 end) as trans_out_qty from PRO_GMTS_DELIVERY_MST a, PRO_GARMENTS_PRODUCTION_MST b, PRO_GARMENTS_PRODUCTION_DTLS c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transfer_criteria in(2,3) and b.production_type=10 and b.po_break_down_id=$req_id group by c.color_id,c.size_id";
		 // echo $sql;die;
		 $res = sql_select($sql);
		 foreach($res as $row)
		 {
			 $smp_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
			 $total_cut+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
		 }

 		foreach($colorData[$dtls_id] as $color_id=>$color_value)
 		{
 			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
 			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
 			$i=1;
 			foreach($color_value as $size_id=>$size_qty)
 			{
 				$colorID .= $color_id."*".$size_id.",";

 				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'"  onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';
 				$i++;
 			}
 			$colorHTML .= "</table></div>";
 		}
		//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);

 		echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
 		echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
 		echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
 		echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
 		echo "$('#txt_yet_to_delivery').val(total_cuts*1-qcqty*1);\n";

 		echo "set_button_status(1, permission, 'fnc_sample_delivery_entry',1,0);\n";
 		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
 		$colorList = substr($colorID,0,-1);
 		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
 		echo "$('#hidden_sample_dtls_tbl_id').val('".$sample_dtls_row_id."');\n";

 	}
 	else
 	{
 		$is_exists_wash_dyeing=return_field_value("id","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and sample_dtls_row_id=$sample_dtls_row_id and status_active=1 and is_deleted=0 and entry_form_id=131");
		$val_req_embel=return_field_value("id","sample_development_fabric_acc","sample_name_re=$sample_name and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3 and name_re<>1 and  name_re<>2 and name_re<>4");
 		if($db_type==2)
 		{
 			$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");
 		}
 		else
 		{
 			$emb_names=sql_select("select  group_concat(name_re,',')  as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");
 		}
 		$name_id=$emb_names[0][csf('name')];
 		$name_arr=explode(',', $name_id);
 		$last_emb=end($name_arr);

 		if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
 		else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}
 		$colorResult = sql_select("SELECT c.sys_number, c.delivery_basis, a.id, a.order_type, a.sample_development_id, a.sample_name, a.gmts_item_id,  a.ex_factory_qty, a.delivery_date, a.carton_qty, a.carton_per_qty, a.remarks, a.is_posted_account,a.shiping_status, a.export_invoice_id, b.color_id as sample_color, b.size_id, b.size_pass_qty as size_qty, c.sample_req_source
		from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b
		where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and a.sample_ex_factory_mst_id = $mst_id and b.sample_ex_factory_mst_id = $mst_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and b.color_id IS NOT NULL");


 		foreach($colorResult as $row)
 		{
 			if($row[csf("sample_development_id")]){
 				$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
 				$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];

 				$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
 				$totSizeQcPassQty+=$row[csf("size_qty")];

 				$dtlsArr[$row[csf("id")]]['order_type']=$row[csf('order_type')];
 				$dtlsArr[$row[csf("id")]]['sample_development_id']=$row[csf('sample_development_id')];
 				$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
 				$dtlsArr[$row[csf("id")]]['sys_number']=$row[csf('sys_number')];
 				$dtlsArr[$row[csf("id")]]['delivery_basis']=$row[csf('delivery_basis')];
 				$dtlsArr[$row[csf("id")]]['gmts_item_id']=$row[csf('gmts_item_id')];
 				$dtlsArr[$row[csf("id")]]['ex_factory_qty']=$row[csf('ex_factory_qty')];
 				$dtlsArr[$row[csf("id")]]['delivery_date']=$row[csf('delivery_date')];
 				$dtlsArr[$row[csf("id")]]['carton_qty']=$row[csf('carton_qty')];
 				$dtlsArr[$row[csf("id")]]['invoice_no']=$row[csf('invoice_no')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_id']=$row[csf('lc_sc_id')];
 				$dtlsArr[$row[csf("id")]]['lc_sc_no']=$row[csf('lc_sc_no')];
 				$dtlsArr[$row[csf("id")]]['carton_per_qty']=$row[csf('carton_per_qty')];
 				$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
 				$dtlsArr[$row[csf("id")]]['shiping_status']=$row[csf('shiping_status')];
				$dtlsArr[$row[csf("id")]]['export_invoice_id']=$row[csf('export_invoice_id')];
 				$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_part_tbl_id')];
				if($row[csf("is_posted_account")]==1) $ac_msg="Already Posted in Accounts.";
				else $ac_msg="";
				$dtlsArr[$row[csf("id")]]['posted']=$ac_msg;
				$dtlsArr[$row[csf("id")]]['is_posted']=$row[csf("is_posted_account")];

 			}
 			$sample_req_source = $row[csf('sample_req_source')];
 		}
		$ac_msg=$dtlsArr[$dtls_id]['posted'];
		$is_posted=$dtlsArr[$dtls_id]['is_posted'];
 		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
 		echo "$('#txt_challan_no').val('".$dtlsArr[$dtls_id]['sys_number']."');\n";
 		echo "$('#mst_update_id').val('".$mst_id."');\n";
 		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
 		echo "$('#txt_sample_requisition_id').val('".$req_library[$dtlsArr[$dtls_id]['sample_development_id']]."');\n";
 		echo "$('#hidden_requisition_id').val('".$dtlsArr[$dtls_id]['sample_development_id']."');\n";
 		echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['gmts_item_id']."');\n";
 		echo "$('#txt_delivery_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#hidden_previous_delv_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#txt_delivery_date').val('".change_date_format($dtlsArr[$dtls_id]['delivery_date'])."');\n";
 		echo "$('#txt_carton_qnty').val('".$dtlsArr[$dtls_id]['carton_qty']."');\n";
 		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
 		echo "$('#cbo_shipping_status').val('".$dtlsArr[$dtls_id]['shiping_status']."');\n";
		echo "$('#posted_account_td').text('".$ac_msg."');\n";
		echo "$('#is_posted').val('".$is_posted."');\n";

		$invoice_id=$dtlsArr[$dtls_id]['export_invoice_id'];
 		$invoice_number=return_field_value("invoice_no","com_export_invoice_ship_mst","id=$invoice_id");
		echo "$('#txt_invoice_id').val('".$dtlsArr[$dtls_id]['export_invoice_id']."');\n";
		echo "$('#txt_invoice_no').val('".$invoice_number."');\n";

 		//echo "10**". $is_exists_wash_dyeing.'--'.$val_req_embel; die;
 		if(trim($is_exists_wash_dyeing)=='' && $val_req_embel=='')
 		{
 			//echo "10**".$sample_req_source; die;
 			if($sample_req_source == 2)
 			{
 				$sqlResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']."  and b.gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=396 and a.entry_form_id=396 and c.entry_form_id=396 and c.color_id is not null");
				$total_cut=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=".$dtlsArr[$dtls_id]['sample_name']." and gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_development_id =$req_id ");
				$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=".$dtlsArr[$dtls_id]['sample_name']." and gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id=$req_id");
 			}
 			else
 			{
				$sqlResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=$gmts and a.entry_form_id=130 and b.entry_form_id=130 and c.entry_form_id=130 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
 				$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=".$dtlsArr[$dtls_id]['sample_name']." and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$sample_dtls_row_id");
 				$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$sample_name and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_dtls_part_tbl_id=$sample_dtls_row_id ");
 			}
 		}
 		else
 		{
 			if($sample_req_source == 1)
 			{
 				$sqlResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and a.entry_form_id=131 and b.entry_form_id=131 and c.entry_form_id=131 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=$last_emb");
 				$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=131 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$last_emb");
 				$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$sample_name and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_dtls_part_tbl_id=$sample_dtls_row_id");
 			}
 			else
 			{
				$sqlResult = sql_select("SELECT c.color_id,c.size_id,c.size_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']."  and b.gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=396 and a.entry_form_id=396 and c.entry_form_id=396 and c.color_id is not null");
				$total_cut=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=".$dtlsArr[$dtls_id]['sample_name']." and gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_development_id =$req_id ");
				$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=".$dtlsArr[$dtls_id]['sample_name']." and gmts_item_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and status_active=1 and is_deleted=0 and entry_form_id=132 and sample_development_id=$req_id");
 			}
 		}

 		$res = sql_select("select requisition_number_prefix_num,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$req_id  and status_active=1 and is_deleted=0");

	  	foreach($res as $result)
		{
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		    echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		}

 		foreach($sqlResult as $row)
 		{
 			$smp_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
 		}
		// ======================= getting transfer data ============================
		$sql = "SELECT c.color_id,c.size_id, sum(case when c.trans_type=5 then c.production_qnty else 0 end) as trans_in_qty,sum(case when c.trans_type=6 then c.production_qnty else 0 end) as trans_out_qty from PRO_GMTS_DELIVERY_MST a, PRO_GARMENTS_PRODUCTION_MST b, PRO_GARMENTS_PRODUCTION_DTLS c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transfer_criteria in(2,3) and b.production_type=10 and b.po_break_down_id=$req_id group by c.color_id,c.size_id";
		// echo $sql;die;
		$res = sql_select($sql);
		foreach($res as $row)
		{
			$smp_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
			$total_cut+=$row[csf("trans_in_qty")] - $row[csf("trans_out_qty")];
		}

		
		if($sample_req_source == 3)
		{
			$colorSizeRes = sql_select("SELECT b.id,b.gmts_item_id,b.sample_name,b.sample_color,c.SIZE_ID,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$req_id and b.gmts_item_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id,c.SIZE_ID order by b.id asc");
			foreach($colorSizeRes as $row)
			{
				$colorData[$row[csf("sample_color")]][$row[csf("SIZE_ID")]]+=$row[csf("size_qty")];
				$oder_qty+=$row[csf("size_qty")];
			}
			
		}

 		foreach($colorData[$dtls_id] as $color_id=>$color_value)
 		{
 			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
 			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
 			$i=1;
 			foreach($color_value as $size_id=>$size_qty)
 			{
 				$colorID .= $color_id."*".$size_id.",";

 				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($sizeQcPassQty[$color_id][$size_id]-$size_qty)).'" onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';
 				$i++;
 			}
 			$colorHTML .= "</table></div>";

 		}
		//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);
 		echo "$('#cbo_item_name').attr('disabled','disabled');\n";
 		echo "$('#cbo_sample_name').attr('disabled','disabled');\n";
 		echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
 		echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
 		echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
 		echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
 		echo "$('#txt_yet_to_delivery').val(total_cuts*1-qcqty*1);\n";
		 if($sample_req_source == 3)
		 {
			 $yet_to_delivery = $oder_qty-$total_cuml;
			 echo "$('#txt_yet_to_delivery').val(".$yet_to_delivery.");\n";
		 }
 		echo "set_button_status(1, permission, 'fnc_sample_delivery_entry',1,0);\n";
 		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
 		$colorList = substr($colorID,0,-1);
 		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
 		echo "$('#hidden_sample_dtls_tbl_id').val('".$sample_dtls_row_id."');\n";
 	}
 	exit();
}

if($action == 'qty_validation')
{
	$data_arr = explode("_", $data);
	$hidden_requisition_id = $data_arr[0];
	$cbo_sample_name = $data_arr[1];
	$dtls_id = $data_arr[2];
	$color_size = explode("*",$data_arr[3]);
	$color_id = $color_size[0];
	$cbo_item_name = $data_arr[4];
	$sent_to_id = $data_arr[5];
	if($dtls_id == '')
	{
		//echo "SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id, b.size_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.gmts_item_id=$cbo_item_name and a.entry_form_id = 132 and b.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0"; die;
		$previous_sampl_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id, b.size_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.gmts_item_id=$cbo_item_name and a.entry_form_id = 132 and b.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");
	}
	else
	{
		$previous_sampl_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id, b.size_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.gmts_item_id=$cbo_item_name and a.entry_form_id = 132 and b.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.id <> $dtls_id");
	}

	$previous_sample_qty_size = array();
	foreach ($previous_sampl_data as $value) {
		$previous_sample_qty_size[$value[csf('sample_development_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('color_id')]][$value[csf('size_id')]] += $value[csf('ex_factory_qty')];
	}
	$size_wise_qty_sql = sql_select("SELECT b.size_id, b.bh_qty, b.plan_qty, b.dyeing_qty, b.test_qty, b.self_qty from sample_development_dtls a join sample_development_size b on a.id = b.dtls_id where a.sample_mst_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.sample_color = $color_id");
	$color_size_req_data = array();
	//$sent_to_data = array(1=>'bh_qty',2=>'plan_qty',3=>'dyeing_qty',4=>'test_qty',5=>'self_qty');
	foreach ($size_wise_qty_sql as $row) {
		foreach ($sent_to_data as $field_name) {
			$color_size_req_data[$row[csf('size_id')]][$field_name] = $row[csf($field_name)];
		}
	}
	$over_qty =array();
	$color = $color_size[0];
	$size = $color_size[1];
	$qty = $color_size[2];
	$total_req_qty = $color_size_req_data[$size][$sample_sent_to_list[$sent_to_id]];
	$total_delivery_qty_size = $previous_sample_qty_size[$hidden_requisition_id][$cbo_sample_name][$cbo_item_name][$color][$size] + $qty;
	//echo "10**".$field.'--'.$size.'--'.$total_req_qty.'--'.$total_delivery_qty_size.'<br>';
	if($total_delivery_qty_size > $total_req_qty){
		$over_qty[] = $size_library[$size].'('.$total_req_qty.')';
	}
	if(count($over_qty)>0)
	{
		$size_str = implode(",", $over_qty);
		echo "As per requisitions you have to develiver $size_str quantity";
		/*if (confirm('As per requisitions you have to develiver $size_str quantity')) {
		  // Save it!
		  console.log('Thing was saved to the database.');
		} else {
		  // Do nothing!
		  console.log('Thing was not saved to the database.');
		}*/
	}
	else{
		echo '';
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sample_req_source = str_replace("'","",$sample_req_source);
	//echo "10**".__LINE__; die;
	//if(str_replace("'","",$sample_req_source) == 1)
	//{
		//echo "10**SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.gmts_item_id=$cbo_item_name and a.entry_form_id = 396 and b.entry_form_id = 396 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0"; die;
		/*$delivery_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and a.sample_name = $cbo_sample_name and a.gmts_item_id=$cbo_item_name and a.entry_form_id = 396 and b.entry_form_id = 396 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");
		$delivery_qty = array();
		foreach ($delivery_data as $value) {
			$delivery_qty[$value[csf('sample_development_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('color_id')]] += $value[csf('ex_factory_qty')];
		}*/
		if(str_replace("'","",$sample_req_source) == 2)
		{
			$total_cut=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$cbo_sample_name and gmts_item_id=$cbo_item_name and status_active=1 and is_deleted=0 and entry_form_id=396 and id=$hidden_sample_dtls_tbl_id");
		}
		else
		{
			$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$cbo_sample_name and item_number_id=$cbo_item_name and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$hidden_sample_dtls_tbl_id");
		}
	//}

	$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst a, inv_gate_pass_dtls b", "a.id=b.mst_id and b.challan_no in($mst_update_id) and a.basis=28 and  a.status_active=1 and a.is_deleted=0 group by a.sys_number", "sys_number");
	if ($check_in_gate_pass != "")
	{
		echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";disconnect($con);
		die;
	}

	if ($operation!=2)
	{
		$invoice_mendatory=return_field_value("invoice_mendatory","lib_sample","status_active=1 and is_deleted=0 and id=$cbo_sample_name","invoice_mendatory");
		//$invoice_no=str_replace("'","",$txt_invoice_no);
		$invoice_id=str_replace("'","",$txt_invoice_id);
		if($invoice_id==0) $invoice_id='';
		// echo $invoice_id.'=D='.$invoice_mendatory;disconnect($con);die;
		if( $invoice_mendatory==1 && $invoice_id=='')
		{
			echo "18**Invoice No Mendatory";
			disconnect($con);
			die;
		}
	}
	$txt_invoice_id=str_replace("'","",$txt_invoice_id);

	$reqSmpNameArr=array();
	if(str_replace("'","",$hidden_requisition_id)!="")
	{

		$sql_sample_req="select id, sample_stage_id, quotation_id from sample_development_mst where id=$hidden_requisition_id";
		$result_sample_req=sql_select($sql_sample_req); $dbsample_stage_id=$dbquotation_id=0;
		foreach($result_sample_req as $row)
		{
			$dbsample_stage_id=$row[csf('sample_stage_id')];
			$dbquotation_id=$row[csf('quotation_id')]*1;
		}
		unset($result_sample_req);

		if(str_replace("'","",$dbsample_stage_id)==1 && str_replace("'","",$dbquotation_id)!=0)
		{
			$sqlDtls="select sample_mst_id, sample_name from sample_development_dtls where sample_mst_id=$hidden_requisition_id and status_active=1 and is_deleted=0";
			$sqlDtlsData=sql_select($sqlDtls);

			foreach($sqlDtlsData as $drow)
			{
				$reqSmpNameArr[$drow[csf('sample_name')]]=$drow[csf('sample_name')];
			}
			unset($sqlDtlsData);

			$jobNo=return_field_value( "job_no", "wo_po_details_master","id=".$dbquotation_id." and status_active=1 and is_deleted=0");
		}
	}

	if ($operation==0) // Insert part----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$flag=1;
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		if(str_replace("'","",$cbo_delivery_basis) == 1)
		{
			$previous_sampl_data = sql_select("SELECT sum(a.ex_factory_qty) as ex_factory_qty FROM sample_ex_factory_dtls a  where a.sample_development_id = $hidden_requisition_id  and a.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0");
			//echo $previous_sampl_data; exit();
			$previous_sample_qty = 0;
			$previous_sample_qty_size = array();
			foreach ($previous_sampl_data as $value) {
				$previous_sample_qty = $value[csf('ex_factory_qty')];
			}
			$color_id = '';
			$size_id = '';
			$color_data = explode("***",$colorIDvalue);
			
			foreach ($color_data as $value) {
				$color_size = explode("*",$value);
				if($color_id == '' || $size_id == '')
				{
					$color_id = $color_size[0];
					$size_id = $color_size[1];
					
				}else{
					break;
				}
			}
			$total_delivery_qty = $previous_sample_qty + str_replace("'","",$txt_delivery_qty);
			
			if(str_replace("'","",$sample_req_source) == 3)
			{
				$previous_sampl_data = sql_select("SELECT sum(a.ex_factory_qty) as ex_factory_qty FROM sample_ex_factory_dtls a  where a.SAMPLE_DTLS_PART_TBL_ID = $hidden_sample_dtls_tbl_id  and a.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0");
				//echo $previous_sampl_data; exit();
				$previous_sample_qty = 0;
				$previous_sample_qty_size = array();
				foreach ($previous_sampl_data as $value) {
					$previous_sample_qty += $value[csf('ex_factory_qty')];
				}
				$total_delivery_qty = $previous_sample_qty + str_replace("'","",$txt_delivery_qty);
				$colorSizeRes = sql_select("SELECT sum(c.total_qty) as size_qty from  sample_development_size c 
				where c.DTLS_ID=$hidden_sample_dtls_tbl_id and c.status_active=1 and c.is_deleted=0");
				foreach($colorSizeRes as $row)
				{
					$oder_qty+=$row[csf("size_qty")];
				}
				//echo $total_delivery_qty . ">= # " . $oder_qty;
				if($total_delivery_qty >= $oder_qty){
					$cbo_shipping_status = 3;
				}
				else{
					$cbo_shipping_status =$cbo_shipping_status;
				}
				//echo $total_delivery_qty . ">=" . $oder_qty ." status".$cbo_shipping_status;
			}else{
				if($total_delivery_qty >= $total_cut){
					$cbo_shipping_status = 3;
				}
				else{
					$cbo_shipping_status =$cbo_shipping_status; //15974-FFL/Sabbir/Taifur
				}
			}
			

			
			$sent_to_id = str_replace("'","",$cbo_delivery_to);
			//echo "10**".$total_delivery_qty.'--'.$total_cut.'--'.$cbo_shipping_status; die;
		}
		else
		{
			$sample_req_source ='';
		}

		if($mst_update_id=='')
		{
			// master part--------------------------------------------------------------;

			$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GDS', date("Y",time()), 5, "select a.sys_number_prefix,a.sys_number_prefix_num from sample_ex_factory_mst a where a.entry_form_id=132 and a.company_id=$cbo_company_name and $mrr_date_check =".date('Y',time())." order by a.id DESC", "sys_number_prefix", "sys_number_prefix_num" ));

			$mst_id=return_next_id("id", "sample_ex_factory_mst", 1);
			$field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to,  gp_no, final_destination,received_by,sent_by, inserted_by, insert_date, status_active, is_deleted,entry_form_id,ex_factory_date,delivery_basis,sample_req_source,export_invoice_id";

			$data_array_mst="(".$mst_id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_delivery_to.",".$txt_gp_no.",".$txt_final_destination.",".$txt_received_by.",".$txt_sent_by.",".$user_id.",'".$pc_date_time."','1','0','132',".$txt_delivery_date.",".$cbo_delivery_basis.",".$sample_req_source.",'".$txt_invoice_id."')";
 			// Details part--------------------------------------------------------------;
			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id, sample_development_id,sample_dtls_part_tbl_id, sample_name,gmts_item_id,delivery_date, ex_factory_qty, carton_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted,entry_form_id,export_invoice_id";
			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$hidden_requisition_id.",".$hidden_sample_dtls_tbl_id.",".$cbo_sample_name.",".$cbo_item_name.",".$txt_delivery_date.",".$txt_delivery_qty.",".$txt_carton_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0','132','".$txt_invoice_id."')";

			// Color & Size Breakdown part--------------------------------------------------------------;
			$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);

			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;
				if($colorID !='' && $sizeID !='' && $colorSizeValue != ''){
					if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','132')";
					else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','132')";
					$colorsize_brk_id+=1;
					$j++;
				}
			}

			$flag=1; $rID1=1;

			//echo "10**".__LINE__."insert into sample_ex_factory_colorsize ($field_array_brk) values $data_array_brk"; die;
			//insert here----------------------------------------;
			$rID_mst=sql_insert("sample_ex_factory_mst",$field_array_mst,$data_array_mst,0);
			if($rID_mst) $flag=1; else $flag=0;

			//$rID_dtls=execute_query("insert into sample_ex_factory_dtls ($field_array_dtls) values $data_array_dtls");
			//echo "10**=insert into sample_ex_factory_dtls ($field_array_dtls) values $data_array_dtls";die;
			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,1);
			//echo "10**=".$rID_mst.'='.$rID_dtls.'='.$rID_brk.'='.$flag;die;
			if($rID_brk==1 && $flag==1) $flag=1; else $flag=0;

			if($flag==1)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
				$total=$delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)]+ str_replace("'","", $txt_delivery_qty);
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
 					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);
 				}
			}

			if(!empty($reqSmpNameArr) && str_replace("'","",$cbo_delivery_to)==1)
			{
				$rID1=execute_query("update wo_po_sample_approval_info set submitted_to_buyer=".$txt_delivery_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0",1);
				//echo "10**update wo_po_sample_approval_info set send_to_factory_date=".$txt_requisition_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0"; die;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".$hidden_requisition_id."**".$new_mrr_number[0]."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".$hidden_requisition_id."**".$new_mrr_number[0]."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
		}
		else
		{
			$mrr_no=return_field_value("sys_number","sample_ex_factory_mst","status_active=1 and entry_form_id='132' and id='$mst_update_id'","sys_number");

			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id, sample_development_id,sample_dtls_part_tbl_id, sample_name,gmts_item_id,delivery_date, ex_factory_qty, carton_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted,entry_form_id,export_invoice_id";
			$data_array_dtls="(".$dtls_id.",".$mst_update_id.",".$hidden_requisition_id.",".$hidden_sample_dtls_tbl_id.",".$cbo_sample_name.",".$cbo_item_name.",".$txt_delivery_date.",".$txt_delivery_qty.",".$txt_carton_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0','132','".$txt_invoice_id."')";

			// Color & Size Breakdown part--------------------------------------------------------------;
			$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);

			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				if($colorID !='' && $sizeID !='' && $colorSizeValue != ''){
					if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','132')";
					else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','132')";
					$colorsize_brk_id+=1;
					$j++;
				}
			}

			$flag=1; $rID1=1;

			//echo "10**".__LINE__."insert into sample_ex_factory_dtls ($field_array_dtls) values $data_array_dtls"; die;
			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);
 			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);
			if($rID_brk==1 && $flag==1) $flag=1; else $flag=0;

			if($flag==1)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
				$total=$delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)]+ str_replace("'","", $txt_delivery_qty);
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
 					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);
 				}
			}

			if(!empty($reqSmpNameArr) && str_replace("'","",$cbo_delivery_to)==1)
			{
				$rID1=execute_query("update wo_po_sample_approval_info set submitted_to_buyer=".$txt_delivery_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0",1);
				//echo "10**update wo_po_sample_approval_info set send_to_factory_date=".$txt_requisition_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0"; die;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**".str_replace("'","",$mrr_no)."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**".str_replace("'","",$mrr_no)."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
		}

		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update part ------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		if(str_replace("'","",$cbo_delivery_basis) == 1)
		{
			$previous_sampl_data = sql_select("SELECT sum(a.ex_factory_qty) as ex_factory_qty FROM sample_ex_factory_dtls a  where a.sample_development_id = $hidden_requisition_id  and a.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0");
			//echo $previous_sampl_data; exit();
			$previous_sample_qty = 0;
			$previous_sample_qty_size = array();
			foreach ($previous_sampl_data as $value) {
				$previous_sample_qty = $value[csf('ex_factory_qty')];
			}
			$color_id = '';
			$color_data = explode("***",$colorIDvalue);
			foreach ($color_data as $value) {
				$color_size = explode("*",$colorIDvalue);
				if($color_id == '')
				{
					$color_id = $color_size[0];
					break;
				}
			}
			$total_delivery_qty = $previous_sample_qty + str_replace("'","",$txt_delivery_qty);

			if(str_replace("'","",$sample_req_source) == 3)
			{
				$previous_sampl_data = sql_select("SELECT sum(a.ex_factory_qty) as ex_factory_qty FROM sample_ex_factory_dtls a  where a.SAMPLE_DTLS_PART_TBL_ID = $hidden_sample_dtls_tbl_id  and a.entry_form_id = 132 and a.status_active =1 and a.is_deleted=0");
				//echo $previous_sampl_data; exit();
				$previous_sample_qty = 0;
				$previous_sample_qty_size = array();
				foreach ($previous_sampl_data as $value) {
					$previous_sample_qty += $value[csf('ex_factory_qty')];
				}
				$total_delivery_qty = $previous_sample_qty + str_replace("'","",$txt_delivery_qty);

				$colorSizeRes = sql_select("SELECT sum(c.total_qty) as size_qty from  sample_development_size c 
				where c.DTLS_ID=$hidden_sample_dtls_tbl_id and c.status_active=1 and c.is_deleted=0");
				foreach($colorSizeRes as $row)
				{
					$oder_qty+=$row[csf("size_qty")];
				}
				//echo $total_delivery_qty . ">= # " . $oder_qty; 
				if($total_delivery_qty >= $oder_qty){
					$cbo_shipping_status = 3;
				}
				else{
					$cbo_shipping_status =$cbo_shipping_status;
				}
				//echo $total_delivery_qty . ">=" . $oder_qty ." status".$cbo_shipping_status;
			}else{
				if($total_cut>0 && ($total_delivery_qty >= $total_cut))
				{
					$cbo_shipping_status = 3;
				}
				else{
					$cbo_shipping_status = $cbo_shipping_status;//15974-FFL/Sabbir/Taifur
				}
			}

			
		//echo "10**".$total_delivery_qty.'--'.$previous_sample_qty[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color_id].'--'.$total_cut.'--'.$cbo_shipping_status; die;
		}
		else
		{
			$sample_req_source ='';
		}

 		if($mst_update_id!='')
		{
			// master part--------------------------------------------------------------;
		   $field_array_mst="delivery_to*export_invoice_id*gp_no*final_destination*ex_factory_date*received_by*sent_by*updated_by*update_date";
			$data_array_mst="".$cbo_delivery_to."*'".$txt_invoice_id."'*".$txt_gp_no."*".$txt_final_destination."*".$txt_delivery_date."*".$txt_received_by."*".$txt_sent_by."*".$user_id."*'".$pc_date_time."'";



			$field_array_dtls="ex_factory_qty*sample_name*gmts_item_id*delivery_date*carton_qty*remarks*shiping_status*updated_by*update_date*export_invoice_id";
			$data_array_dtls="".$txt_delivery_qty."*".$cbo_sample_name."*".$cbo_item_name."*".$txt_delivery_date."*".$txt_carton_qnty."*".$txt_remark."*".$cbo_shipping_status."*".$user_id."*'".$pc_date_time."'*'".$txt_invoice_id."'";


			// Color & Size Breakdown part--------------------------------------------------------------;


			$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);

			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',132)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',132)";
				$colorsize_brk_id+=1;
				$j++;
			}
			$flag=1; $rID1=1;

			$rID_mst=sql_update("sample_ex_factory_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
			$rID_dtls=sql_update("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;

			if($flag==1)
			{
				$rID_brk_delete = execute_query("DELETE from sample_ex_factory_colorsize WHERE sample_ex_factory_dtls_id=$dtls_update_id");//Delete fast;
				if($rID_brk_delete==1 && $flag==1) $flag=1; else $flag=0;
			}

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);
			if($rID_brk==1 && $flag==1) $flag=1; else $flag=0;

			$old_delv=str_replace("'", "", $hidden_previous_delv_qty);
			$new_delv=str_replace("'", "", $txt_delivery_qty);
			$delv_diff=$new_delv - $old_delv;
  			if($flag==1)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
 				$total=($delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)] )+ ($delv_diff);

				$rIds=sql_multirow_update($db_table, $field_array_update, "''","id",$hidden_sample_dtls_tbl_id,1);
				if($rIds==1 && $flag==1) $flag=1; else $flag=0;
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
  					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);
					if($rID_up==1 && $flag==1) $flag=1; else $flag=0;
 				}
			}

			if(!empty($reqSmpNameArr) && str_replace("'","",$cbo_delivery_to)==1)
			{
				$rID1=execute_query("update wo_po_sample_approval_info set submitted_to_buyer=".$txt_delivery_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0",1);
				//echo "10**update wo_po_sample_approval_info set send_to_factory_date=".$txt_requisition_date." where job_no_mst ='".$jobNo."' and sample_type_id in (".implode(",",$reqSmpNameArr).") and status_active=1 and is_deleted=0"; die;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}

 			//echo $rID_mst.','.$rID_dtls.','.$rID_brk; mysql_query("ROLLBACK");die;
			//-------------------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0"."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mst_update_id."**".$hidden_requisition_id."**0";
				}

			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0"."**".$hidden_sample_dtls_tbl_id."**".$sample_req_source;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mst_update_id."**".$hidden_requisition_id."**0";
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
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);

 		$rID = sql_delete("sample_ex_factory_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id  ',$dtls_update_id,1);
		$dtlsrID = sql_delete("sample_ex_factory_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_ex_factory_dtls_id',$dtls_update_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$mst_update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".$mst_update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_update_id;
			}
		}
		disconnect($con);
		die;
	}
}

function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}
	echo $strQuery;die;
		//return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);

	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
if($action=="sys_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
 	?>
	<script>
	function js_set_value(smp,mst)
	{
 		$("#selected_id").val(smp+'*'+mst);
    	parent.emailwindow.hide();
 	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th width="160">Company</th>
					<th width="150">Buyer Name</th>
					<th width="100">Challan No</th>
					<th width="100">Req No</th>
					<th width="200">Delivery Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
				</thead>
				<tr align="center">
					<td>
					<?
						echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --",$company, "",0 );
					?>
					</td>
					<td>
					<?
						echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
					?>
					</td>
					<td align="center" >
						<input type="text" style="width:100px" class="text_boxes"  name="txt_challan" id="txt_challan" />
					</td>
					<td align="center" >
						<input type="text" style="width:100px" class="text_boxes"  name="txt_req_no" id="txt_req_no" />
					</td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
					<td align="center">
						<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_challan').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_delivery_search_list', 'search_div_delivery', 'sample_delivery_entry_buyer_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
					</td>
				</tr>
				<tr>
					<td align="center" height="40" colspan="6" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="selected_id" >
					</td>
				</tr>
			</table>
			<div id="search_div_delivery" style="margin-top:20px;"></div>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_delivery_search_list")
{
	$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$exfact_qty_arr=return_library_array( "select sample_ex_factory_mst_id, sum(ex_factory_qty) as ex_factory_qty from sample_ex_factory_dtls where status_active=1  group by sample_ex_factory_mst_id",'sample_ex_factory_mst_id','ex_factory_qty');

	$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");

	$smp_id_arr=return_library_array( "select sample_ex_factory_mst_id,sample_development_id from sample_ex_factory_dtls where status_active=1 group by sample_ex_factory_mst_id,sample_development_id",'sample_ex_factory_mst_id','sample_development_id');

	$ex_data = explode("_",$data);
	//echo "<pre>";print_r($ex_data);die;
	$company = $ex_data[0];
	$cbo_delivery_to = $ex_data[1];
	$challan = $ex_data[2];
	$req_no=$ex_data[3];
	$txt_date_from = $ex_data[4];
	$txt_date_to = $ex_data[5];
	 $date_cond="";
 	if($txt_date_from!="" and  $txt_date_to!="")
	{
		if($db_type==0){$date_cond  = " and a.id in(select sample_ex_factory_mst_id from sample_ex_factory_dtls where delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."')";}
		if($db_type==2 || $db_type==1){ $date_cond= " and a.id in(select sample_ex_factory_mst_id from sample_ex_factory_dtls where delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to)) ."')";}

	}
	if(trim($company)!=0) {$com_cond= " and a.company_id= '$company'";} else {$com_cond="";}
	if(trim($cbo_delivery_to)!=0) {$delv_cond= " and a.delivery_to= '$cbo_delivery_to'";} else {$delv_cond="";}

	if(trim($challan)!="") {$challan_cond= " and a.sys_number_prefix_num= '$challan'";} else {$challan_cond="";}

	if(trim($req_no)!="") {
		if(trim($company)!=0) {$com_cond1= " and company_id= '$company'";} else {$com_cond1="";}
		$req_cond= " and b.sample_development_id in (select id from sample_development_mst where requisition_number_prefix_num ='$req_no' and status_active=1 and is_deleted=0 $com_cond1) ";
	} else {$req_cond="";}

	$sql = "SELECT a.id, a.sys_number, a.company_id, a.location, a.delivery_to,  a.gp_no, a.final_destination, a.received_by, a.delivery_basis, b.sample_development_id, b.shiping_status from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id=b.sample_ex_factory_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $com_cond $delv_cond $challan_cond $date_cond $req_cond and a.entry_form_id=132 group by a.id, a.sys_number, a.company_id, a.location, a.delivery_to, a.gp_no, a.final_destination, a.received_by, a.delivery_basis, b.sample_development_id, b.shiping_status order by a.id DESC";
	//echo $sql;die;
	$result = sql_select($sql);
   ?>
     	<table cellspacing="0" width="960" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
					<th width="30" >SL</th>
					<th width="120" >Sys Num</th>
                     <th width="100" >Req. No</th>
					<th width="120" >Buyer Name</th>
					<th width="120" >Final Destination</th>
					<th width="130" >Received By</th>
                     <th width="100" >Sent To</th>
					<th width="80">Ex-fact Qty</th>
					<th>Delivery Status</th>
            </thead>
     	</table>
     <div style="width:960px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="940" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			//$sent_to = array(1=>'BH Qty',2=>'Plan',3=>'Dyeing',4=>'Test',5=>'Self');
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $sample_development_id= $row[csf('sample_development_id')];
				$sys_number=$row[csf("sys_number")];
				$delivery_to=$sample_sent_to_list[$row[csf("delivery_to")]];
				$delivery_basis_id=$row[csf('delivery_basis')];
				if($delivery_basis_id=='') $delivery_basis_id=0;else $delivery_basis_id=$delivery_basis_id;
				//echo $delivery_basis_id.'d';;
                if($delivery_basis_id ==1)
                {
                	$sql_req=sql_select("SELECT requisition_number as req_po_no,buyer_name,id from sample_development_mst where status_active=1 and id =$sample_development_id");
					foreach($sql_req as $row2)
					{
						$buyer_id=$row2[csf('buyer_name')];
						$req_po_no = $row2[csf('req_po_no')];
					}

                }
                elseif ($delivery_basis_id ==2) {
					$sql_req=sql_select("SELECT a.buyer_name,b.po_number  as req_po_no ,b.id as po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where a.status_active=1 and b.id =$sample_development_id");
					$buyer_id=0;
					foreach($sql_req as $row3)
					{
						$buyer_id=$row3[csf('buyer_name')];
						$req_po_no = $row3[csf('req_po_no')];
					}

                }
                elseif ($delivery_basis_id ==3) {
                	$buyer_name ='';$buyer_id=0;
                }
                else
                {
                	$buyer_name ='';$buyer_id=0;
                }
                //echo $smp_id_arr[$row[csf('id')]] in js_set_value
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('sample_development_id')];?>,<? echo $row[csf('id')];?>);" >
                    <td width="30" title="<? echo $row[csf('id')]; ?>" align="center"><? echo $i; ?></td>
                    <td width="120" align="center" title="delivery_basis=<? echo $delivery_basis_id;?>"><p><? echo $sys_number; ?></p></td>
                     <td width="100" align="center"><p><? echo $req_po_no; ?></p></td>
                    <td width="120" align="center"><p><? echo $buyer_name_arr[$buyer_id]; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><? echo $row[csf("final_destination")]; ?>&nbsp;</p></td>
                    <td width="130" align="center"><p><? echo $row[csf("received_by")];?>&nbsp;</p></td>
                     <td width="100" align="center"><p><? echo $delivery_to; ?></p></td>
                    <td align="center" width="80"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td>
                    <td><? echo $shipment_status[$row[csf("shiping_status")]] ?></td>

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

if($action=="delivery_print")
{
	extract($_REQUEST);
	list($mst_id,$dtls_id,$company_name,$sample_name,$gmts,$req_id,$hidden_sample_dtls_tbl_id)=explode('*',$data);
	echo load_html_head_contentss("Garments Delivery Info","../", 1, 1, $unicode,'','');
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$supplier_lib=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );

	$order_arr=return_library_array( "select  id, job_no from wo_po_details_master  where status_active=1 and is_deleted=0", "id","job_no"  );
	$dealing_marchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );
	$mst_data=sql_select("select * from sample_ex_factory_mst where id=$mst_id and status_active=1 and entry_form_id=132");
	$dtls_data=sql_select("select delivery_date from sample_ex_factory_dtls where sample_ex_factory_mst_id=$mst_id and status_active=1 and entry_form_id=132");
	$req_array=array();
	$req_sql=sql_select("select * from sample_development_mst where is_deleted=0 and status_active=1 and entry_form_id in (117,203,449) and id=$req_id");
	foreach($req_sql as $row)
	{
		$req_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$req_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$req_array[$row[csf("id")]]['dealing_marchant']=$row[csf("dealing_marchant")];
		$req_array[$row[csf("id")]]['requisition_number_prefix_num']=$row[csf("requisition_number_prefix_num")];
		$req_array[$row[csf("id")]]['sample_stage_id']=$row[csf("sample_stage_id")];
		$req_array[$row[csf("id")]]['quotation_id']=$row[csf("quotation_id")];
	}


	$sql="SELECT * from sample_ex_factory_dtls where sample_ex_factory_mst_id=$mst_id  and status_active=1 and is_deleted=0 and entry_form_id=132";
	$result=sql_select($sql);
	foreach($result as $row){
		$data_arr[]=array(
			'sample_name'=>$row[csf('sample_name')],
			'sample_development_id'=>$row[csf('sample_development_id')],
			'invoice_no'=>$row[csf('invoice_no')],
			'ex_factory_qty'=>$row[csf('ex_factory_qty')],
			'carton_qty'=>$row[csf('carton_qty')],
			'remarks'=>$row[csf('remarks')]
			);
		$smp_id_arr[]=$row[csf('sample_name')];
		$gmts_id_arr[]=$row[csf('gmts_item_id')];

	}
	$smp_id= implode(',',$smp_id_arr);
	$gmts_id= implode(',',$gmts_id_arr);

	$result_smp=sql_select("select b.sample_name,a.buyer_name,a.style_ref_no,b.gmts_item_id from sample_development_mst a,sample_development_dtls b where a.company_id=$company_name and a.entry_form_id in (117,203,449) and b.entry_form_id in (117,203,449) and b.sample_name in($smp_id) and b.gmts_item_id in($gmts_id) group by a.buyer_name,a.style_ref_no,b.gmts_item_id,b.sample_name");

	foreach($result_smp as $row){
		$buy_data[$row[csf('sample_name')]]=$buyer_lib[$row[csf('buyer_name')]];
		$sty_data[$row[csf('sample_name')]]=$row[csf('style_ref_no')];
		$item_data[$row[csf('sample_name')]]=$garments_item[$row[csf('gmts_item_id')]];
	}
	?>
	<div style="width:1000px; border:1px solid #fff; ">
		<table width="100%" cellspacing="0" align="right" cellpadding="10"  >
			<tr>
				<td colspan="6" align="center" valign="middle">
					<img src="../<? echo $image_location; ?>" height="50" width="60" style="float:left;">
					<strong style=" font-size:xx-large;"><? echo $company_library[$company_name]; ?></strong>
				</td>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px;" >
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
					foreach ($nameArray as $result)
					{
						?>
						<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?>
						<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
						<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?>
						<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
						<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
						<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?>
						<? if($result[csf('province')]!="") echo $result[csf('province')];?>
						<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
						<? if($result[csf('website')]!="") echo $result[csf('website')];
					}
					?>
				</td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="6" align="center"><strong>100% Export Oriented</strong></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="6" style="font-size:20px;" align="center"><strong>Sample Delivery Challan</strong></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="9" height="5">  </td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:16px;">  <? echo $buyer_lib[$req_array[$req_id]['buyer_name']]; ?>  </td>
				<td align="left"><strong>Delivery Date :</strong></td>
				<td  colspan="2" style="font-size:16px;" align="left"> <? echo change_date_format($dtls_data[0][csf('delivery_date')]); ?> </td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td colspan="2" style="max-width: 150px;"><? echo $buyer_add[$req_array[$req_id]['buyer_name']]; ?></td>
				<td align="left" valign="top"><strong>Challan No :</strong></td>
				<td  colspan="2" style="font-size:16px;" align="left" valign="top"> <? echo $mst_data[0][csf('sys_number')]; ?></td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="left" valign="top"><strong>Requisition No :</strong></td>
				<td colspan="2" style="font-size:16px;" align="left" valign="top"> <? echo $req_array[$req_id]['requisition_number_prefix_num']; ?></td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="left" valign="top"><strong>Dealing Merchant :</strong></td>
				<td  colspan="2" style="font-size:16px;" align="left" valign="top"> <? echo $dealing_marchant[$req_array[$req_id]['dealing_marchant']]; ?> </td>
				<td colspan="4"></td>
			</tr>
		</table>
	</div>
	<?
	$sql="
	select a.gmts_item_id,a.sample_development_id,a.ex_factory_qty,a.carton_qty,a.remarks ,a.sample_name,	b.color_id,b.size_id,b.size_pass_qty
	from
	sample_ex_factory_dtls a,sample_ex_factory_colorsize b
	where
	a.sample_ex_factory_mst_id=$mst_id and a.sample_name=$sample_name and a.gmts_item_id=$gmts and a.id=b.sample_ex_factory_dtls_id and a.id=$dtls_id and a.sample_dtls_part_tbl_id=$hidden_sample_dtls_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.entry_form_id=132 and b.entry_form_id=132";

	$sql_result=sql_select($sql);
	foreach ($sql_result as $rows)
	{
		$dtls_data['sample_name']=$rows[csf('sample_name')];
		$dtls_data['ex_factory_qty']=$rows[csf('ex_factory_qty')];
		$dtls_data['carton_qty']=$rows[csf('carton_qty')];
		$dtls_data['remarks']=$rows[csf('remarks')];
		$dtls_data['gmts_item_id']=$rows[csf('gmts_item_id')];

		$size_arr[]=$rows[csf('size_id')];

		$tot_color_good_qty[$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];

		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];

		$good_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]=$rows[csf('size_pass_qty')];
	}
	$tot_size=count($size_arr);
	$width=round((100/$tot_size)+25);
	$width_2=($width*$tot_size)+650;
	?>
	<div style="width:<? echo $width_2;?>px;float: left;margin-top: 20px;">
		<table align="right" cellspacing="0" width="100%"  border="1" class="rpt_table" rules="all">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="30" rowspan="2">SL</th>
					<th width="100" rowspan="2" >Buyer</th>
					<th width="120" rowspan="2">Style Ref.</th>
					<?
					if($req_array[$req_id]['sample_stage_id']==1)
					{
						?>
						<th width="80" rowspan="2">Job No</th>
						<?
					}

					?>
					<th width="120" rowspan="2">Sample</th>
					<th width="120" rowspan="2">Item Name</th>
					<th width="80" rowspan="2">Color</th>
					<th colspan="<? echo $tot_size;?>">Size</th>
					<th width="80" rowspan="2" >Delivery Qty</th>
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>
					<?
					foreach ($size_arr as $size_id)
					{
						?>
						<th align="center" width="<? echo $width;?>"><? echo $size_library[$size_id]; ?></th>
						<?
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
				$i=1;$cols_pan=6;
				foreach($good_qty as $color_id=>$size_val)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="right"><? echo $i; ?></td>
						<td align="center"> <? echo $buyer_lib[$req_array[$req_id]['buyer_name']]; ?> </td>
						<td align="center"> <? echo $req_array[$req_id]['style_ref_no']; ?> </td>
						<?
						if($req_array[$req_id]['sample_stage_id']==1)
						{
							$cols_pan=7;
							?>
							<td align="center"> <? echo $order_arr[$req_array[$req_id]['quotation_id']]; ?> </td>

							<?
						}

						?>
						<td align="center"> <? echo $sample_name_library[$dtls_data['sample_name']]; ?> </td>
						<td align="center"> <? echo $garments_item[$dtls_data['gmts_item_id']]; ?> </td>
						<td align="center"><? echo $color_library[$color_id]; ?></td>
						<?
						foreach ($size_arr as $size_id)
						{
							?>
							<td align="right"><? echo $good_qty[$color_id][$size_id]; ?></td>
							<?
						}
						?>
						<td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
						<td> <? echo $dtls_data['remarks']; ?> </td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
			<tr>
				<td colspan="<? echo $cols_pan;?>" align="right"><b> Grand Total :</b> </td>
				<?
				foreach ($size_arr as $size_id)
				{
					?>
					<td align="right"><?php echo $tot_size_good_qty[$size_id]; ?></td>
					<?
				}
				?>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</div>
	<?
	echo signature_table(127, $company_name, "810px");
	$barcode_no=$mst_data[0][csf('sys_number')];
	?>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $barcode_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}


if($action=="print_delivery_2")
{
	extract($_REQUEST);
	list($txt_challan_no,$mst_update_id,$cbo_company_name,$cbo_delivery_basis)=explode('*',$data);
	$company_name=str_replace("'", "", $cbo_company_name);
	$mst_id=$mst_update_id;
	$challan=$txt_challan_no;
 	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 	$lib_sample=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
 	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");

	$dealing_marchant_lib=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );

	$mst_data=sql_select("SELECT sys_number,ex_factory_date,final_destination,delivery_to,sent_by from sample_ex_factory_mst where id=$mst_id and entry_form_id=132 and status_active=1 and is_deleted=0");

	$data_array=array();
	$size_array=array();

	$data_sql="SELECT b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id,sum(c.size_pass_qty) as qnty,b.remarks  from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=$mst_id group by b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id , b.remarks";
	//echo $data_sql;
	$sample_dep_id=array();
	foreach(sql_select($data_sql) as $keys=>$vals)
	{
		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["basis"]=$vals[csf("delivery_basis")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["qnty"]+=$vals[csf("qnty")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["remarks"]=$vals[csf("remarks")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["sample_development_id"]=$vals[csf("sample_development_id")];

		$data_array_size_wise[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]+=$vals[csf("qnty")];

		$size_array[$vals[csf("size_id")]]=$vals[csf("size_id")];
		$booking_or_po_id[$vals[csf("sample_development_id")]]=$vals[csf("sample_development_id")];
		array_push($sample_dep_id, $vals[csf("sample_development_id")]);
	}
	$id_cond= where_con_using_array($sample_dep_id,0,"id");
	$res_style = sql_select("select requisition_number_prefix_num,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) $id_cond  and status_active=1 and is_deleted=0");
	$style_data=array();

  	foreach($res_style as $result)
	{

	    $style_data[$result[csf('id')]]=$result[csf('style_ref_no')];
	}
	$all_po_or_booking_id=implode(",", $booking_or_po_id);
	if(!$all_po_or_booking_id)
	{
		$all_po_or_booking_id=0;
	}
	$count_size=count($size_array);
	$po_array=array();
	$booking_array=array();
	$cbo_delivery_basis=str_replace("'","", $cbo_delivery_basis);
	if($cbo_delivery_basis==2)
	{
		$po_sql="SELECT b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_or_booking_id) group by b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number ";

		foreach(sql_select($po_sql) as $key=>$rows)
		{
			$po_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_name")];
			$po_array[$rows[csf("id")]]["job"]=$rows[csf("job_no_prefix_num")];
			$po_array[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
			$po_array[$rows[csf("id")]]["po_number"]=$rows[csf("po_number")];
			$po_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];
		}
		$dealing_marchant=$po_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==3)
	{
		$booking_sql="SELECT a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.id in($all_po_or_booking_id) group by a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant";

		foreach(sql_select($booking_sql) as $key=>$rows)
		{
			$booking_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_id")];
			$booking_array[$rows[csf("id")]]["book"]=$rows[csf("booking_no_prefix_num")];
			$booking_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];

		}
		$dealing_marchant=$booking_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==1)
	{
		$req_sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where id in ($all_po_or_booking_id) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 order by id asc";

		foreach (sql_select($req_sql) as $row) {
			$dealing_marchant_arr[$row[csf('id')]] = $dealing_marchant_lib[$row[csf('dealing_marchant')]];
			$buyer_id=$row[csf('buyer_name')];
			$style_ref_no=$row[csf('style_ref_no')];
			$buyer_name_arr[$row[csf('id')]]=$row[csf('buyer_name')];
		}
		$dealing_marchant = implode(",", $dealing_marchant_arr);
	}
	/*echo '<pre>';
	print_r($dealing_marchant); die;*/
	?>
	<div style="width:1000px; border:1px solid #fff;margin:0px auto; ">
		<table width="100%" cellspacing="0" align="right" cellpadding="2"  >
			<tr>
			<td rowspan="5"> <img src="../../<? echo $image_location; ?>" height="70" width="200" style="float:left;"></td>
			<td  colspan="2" align="center"><strong style=" font-size:25;"><? echo $company_library[str_replace("'", "", $company_name)]; ?>
					</strong></td>
				<td   align="left"  style="font-size:xx-large;">
					<div style="float:left;width:34%; text-align:right">
						<div style="float:left; height:5%; width:5%;" id="qrcode"></div>
					</div>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="center"  >
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$company_name'");
					foreach ($nameArray as $result)
					{
						?>
						<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?>
						<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
						<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?>
						<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
						<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
						<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?>
						<? if($result[csf('province')]!="") echo $result[csf('province')];?>
						<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email:<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
						Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
					}
					?>
				</td>
				<td colspan="2">&nbsp;</td>

			</tr>
			<tr>
				<td colspan="2" align="center"><strong>100% Export Oriented Garments</strong></td>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  align="center"><strong>Sample Delivery Challan</strong></td>
				 <td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" height="5">  </td>
			</tr>
			<table width="100%" cellspacing="0" border="1" align="right" rules="all"  class="rpt_table" cellpadding="2">
				<tr>
					<td ><strong>Challan NO:</strong>   </td>
					<td><? echo $mst_data[0][csf("sys_number")]; ?></td>
					<td><strong>Challan Date :</strong></td>
					<td> <? echo change_date_format($mst_data[0][csf('ex_factory_date')]); ?></td>
				</tr>
				<tr>
					<?
						//$seto_to_full = array(1=>'Buying House',2=>'Planning', 3=>'Dyeing', 4=>'Test', 5=>'Self')
					?>
					<td ><strong>Sent To:</strong>   </td>
					<td><? echo $sample_sent_to_list[$mst_data[0][csf("delivery_to")]]; ?></td>
					<td ><strong>Final Destination:</strong></td>
					<td > <? echo $mst_data[0][csf('final_destination')]; ?> </td>
				</tr>
				<tr>
					<td ><strong>Dealing Merchant</strong>   </td>
					<td><? echo $dealing_marchant;?></td>
					<td ><strong>Sent By</strong></td>
					<td ><? echo $mst_data[0][csf('sent_by')]; ?> </td>
				</tr>
			</table>
			<tr>
				<td colspan="4" height="6">&nbsp;</td>
			</tr>
		</table>

		<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
			<tr>
				<th rowspan="2">SL</th>
				<th rowspan="2">Type Of Sample</th>
				<th rowspan="2">Buyer</th>
				<th rowspan="2">Job & Booking No</th>
				<th rowspan="2">Style Ref.</th>
				<th rowspan="2">Order No</th>
				<th rowspan="2">Color Name</th>
				<th rowspan="2">Item Name</th>
				<th colspan="<? echo $count_size;?>">Size</th>
				<th rowspan="2">Delivery Qnty</th>
				<th rowspan="2">Remarks</th>
			</tr>
			<tr>
				<?
				foreach($size_array as $key=>$val)
				{
					?>
					<th><? echo $size_library[$val]; ?></th>
					<?
				}
				?>
			</tr>

			<?
			$i=0;
			$total_delv=0;
			foreach($data_array as $sample_id=>$item_data)
			{
				foreach($item_data as $item_id=>$color_data)
				{
					foreach($color_data as $color_id=>$row)
					{
						$i++;
						$basis=$row["basis"];
						$sample_development_id=$row["sample_development_id"];
						if($basis==2)
						{
							$buyer=$buyer_lib[$po_array[$sample_development_id]["buyer"]];
							$job_or_book=$po_array[$sample_development_id]["job"];
							$style=$po_array[$sample_development_id]["style_ref_no"];
							$order_no=$po_array[$sample_development_id]["po_number"];
						}
						else if($basis==3)
						{
							$buyer=$buyer_lib[$booking_array[$sample_development_id]["buyer"]];
							$job_or_book=$booking_array[$sample_development_id]["book"];
						}
						else if($basis==1)
						{

							$buyer=$buyer_lib[$buyer_name_arr[$sample_development_id]];
							//$buyer=$buyer_lib[$buyer_id["buyer"]];
							//$style=$style_ref_no;
							$style=$style_data[$sample_development_id];
						}
						?>
						<tr>
							<td align="left"><? echo $i; ?></td>
							<td align="left"><? echo $lib_sample[$sample_id]; ?></td>
							<td align="left"><? echo $buyer; ?></td>
							<td align="left"><? echo $job_or_book; ?></td>
							<td align="left"><? echo $style; ?></td>
							<td align="left"><? echo $order_no; ?></td>
							<td align="left"><? echo $color_library[$color_id]; ?></td>
							<td align="left"><? echo $garments_item[$item_id]; ?></td>
							<?
							 foreach($size_array as $key=>$val)
							 {
							 	?>
							 	<td align="right"><? echo $data_array_size_wise[$sample_id][$item_id][$color_id][$val]; ?></td>

							 	<?
							 }
							 $total_delv+=$row["qnty"];
							?>
							<td align="right"><? echo $row["qnty"]; ?></td>
							<td align="left"><? echo $row["remarks"]; ?></td>
						</tr>
						<?
					}
				}
			}
			?>
			<tr bgcolor="#E4E4E4">
				<td  align="center"  colspan="<? echo 8+$count_size; ?>" ><strong>Grand Total :</strong></td>
				<td align="right"><strong><? echo $total_delv; ?></strong></td>
				<td><strong> </strong></td>
			</tr>
			<tr>
				<td colspan="<? echo 10+$count_size; ?>" > In Words : <?  echo number_to_words($total_delv); ?> Pcs</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >

		<tr>
			<? $width='1000px'; echo signature_table(127, $company_name,$width, ''); ?>
		</tr>

		</table>
		<script type="text/javascript" src="../../js/jquery.js"></script>

		<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
		<script>

			var main_value='<? echo $challan;?>';
			$('#qrcode').qrcode(main_value);
		</script>
	</div>
	<?
	exit();
}

if($action=="print_delivery_3")
{
	extract($_REQUEST);
	list($txt_challan_no,$mst_update_id,$cbo_company_name,$cbo_delivery_basis)=explode('*',$data);
	$company_name=str_replace("'", "", $cbo_company_name);
	$mst_id=$mst_update_id;
	$challan=$txt_challan_no;
	$system_no=str_replace("'", "", $txt_challan_no);
 	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 	$lib_sample=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
 	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$department_arr=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$dealing_marchant_lib=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
	$mst_data=sql_select("SELECT sys_number,ex_factory_date,final_destination,delivery_to,sent_by from sample_ex_factory_mst where id=$mst_id and entry_form_id=132 and status_active=1 and is_deleted=0");

	//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where status_active =1 and is_deleted=0 order by id", "id","po_number"  );


	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.SECURITY_LOCK_NO,a.DRIVER_NAME,a.DRIVER_LICENSE_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company_name." AND a.basis = 28 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	//echo $sql_get_pass; die;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');

				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
			}
		}
	}
	echo "<pre>";print_r($gatePassDataArr);
	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	$data_array=array();
	$size_array=array();

	$data_sql="SELECT b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id,sum(c.size_pass_qty) as qnty,b.remarks  from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=$mst_id group by b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id , b.remarks";
	$sample_dep_id=array();
	foreach(sql_select($data_sql) as $keys=>$vals)
	{
		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["basis"]=$vals[csf("delivery_basis")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["qnty"]+=$vals[csf("qnty")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["remarks"]=$vals[csf("remarks")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["sample_development_id"]=$vals[csf("sample_development_id")];

		$data_array_size_wise[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]+=$vals[csf("qnty")];

		$size_array[$vals[csf("size_id")]]=$vals[csf("size_id")];
		$booking_or_po_id[$vals[csf("sample_development_id")]]=$vals[csf("sample_development_id")];
		array_push($sample_dep_id, $vals[csf("sample_development_id")]);
	}
	$id_cond= where_con_using_array($sample_dep_id,0,"id");

	$res_style = sql_select("select requisition_number_prefix_num,requisition_number,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) $id_cond  and status_active=1 and is_deleted=0");
	$style_data=array();
	$req_no_arr=array();
	$booking_no_arr=array();
	$buyer_name_arr=array();
  	foreach($res_style as $result)
	{
	    $style_data[$result[csf('id')]]=$result[csf('style_ref_no')];
	    $req_no_arr[$result[csf('id')]]=$result[csf('requisition_number')];
	    $booking_no_arr[$result[csf('id')]]=$result[csf('id')];
	    $buyer_name_arr[$result[csf('id')]]=$result[csf('buyer_name')];
	}
	$all_po_or_booking_id=implode(",", $booking_or_po_id);
	if(!$all_po_or_booking_id)
	{
		$all_po_or_booking_id=0;
	}
	$count_size=count($size_array);
	$po_array=array();
	$booking_array=array();
	$cbo_delivery_basis=str_replace("'","", $cbo_delivery_basis);
	if($cbo_delivery_basis==2)
	{
		$po_sql="SELECT b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_or_booking_id) group by b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number ";

		foreach(sql_select($po_sql) as $key=>$rows)
		{
			$po_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_name")];
			$po_array[$rows[csf("id")]]["job"]=$rows[csf("job_no_prefix_num")];
			$po_array[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
			$po_array[$rows[csf("id")]]["po_number"]=$rows[csf("po_number")];
			$po_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];
		}
		$dealing_marchant=$po_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==3)
	{
		$booking_sql="SELECT a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.id in($all_po_or_booking_id) group by a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant";

		foreach(sql_select($booking_sql) as $key=>$rows)
		{
			$booking_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_id")];
			$booking_array[$rows[csf("id")]]["book"]=$rows[csf("booking_no_prefix_num")];
			$booking_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];

		}
		$dealing_marchant=$booking_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==1)
	{
		$req_sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where id in ($all_po_or_booking_id) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 order by id asc";
		//echo $req_sql; die;
		foreach (sql_select($req_sql) as $row) {
			$dealing_marchant_arr[$row[csf('id')]] = $dealing_marchant_lib[$row[csf('dealing_marchant')]];
			$buyer_id=$row[csf('buyer_name')];
			$style_ref_no=$row[csf('style_ref_no')];
		}
		$dealing_marchant = implode(",", $dealing_marchant_arr);
	}
	/*echo '<pre>';
	print_r($dealing_marchant); die;*/
		?>
		<div style="width:1000px; border:1px solid #fff;margin:0px auto; ">
			<table width="100%" cellspacing="0" align="right" cellpadding="2"  >
				<tr>
				<td rowspan="5"> <img src="../../<? echo $image_location; ?>" height="70" width="200" style="float:left;"></td>
				<td  colspan="10" align="center"><strong style=" font-size:25;"><? echo $company_library[str_replace("'", "", $company_name)]; ?>
						</strong></td>
					<!-- <td   align="left"  style="font-size:xx-large;">
						<div style="float:left;width:34%; text-align:right">
							<div style="float:left; height:5%; width:5%;" id="qrcode"></div>
						</div>
					</td> -->
					<td width="34%"  align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>


				</tr>
				<tr>
					<td colspan="9" align="center"  width="80%" >
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$company_name'");
						foreach ($nameArray as $result)
						{
							?>
							<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") echo $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center"><strong>100% Export Oriented Garments</strong></td>
					<td colspan="">&nbsp;</td>

				</tr>
				<tr>
					<td colspan="7"  align="center"><strong>Sample Delivery Challan</strong></td>
					 <td colspan="">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6" height="5">  </td>
				</tr>
				<table width="100%" cellspacing="0" border="1" align="right" rules="all"  class="rpt_table" cellpadding="2">
					<tr>
						<td ><strong>Challan NO:</strong>   </td>
						<td><? echo $mst_data[0][csf("sys_number")]; ?></td>
						<td><strong>Challan Date :</strong></td>
						<td> <? echo change_date_format($mst_data[0][csf('ex_factory_date')]); ?></td>
					</tr>
					<tr>
						<?
							//$seto_to_full = array(1=>'Buying House',2=>'Planning', 3=>'Dyeing', 4=>'Test', 5=>'Self')
						?>
						<td ><strong>Sent To:</strong>   </td>
						<td><? echo $sample_sent_to_list[$mst_data[0][csf("delivery_to")]]; ?></td>
						<td ><strong>Final Destination:</strong></td>
						<td > <? echo $mst_data[0][csf('final_destination')]; ?> </td>
					</tr>
					<tr>
						<td ><strong>Dealing Merchant</strong>   </td>
						<td><? echo $dealing_marchant;?></td>
						<td ><strong>Sent By</strong></td>
						<td ><? echo $mst_data[0][csf('sent_by')]; ?> </td>
					</tr>
					<tr>
						<td align="center" colspan="6" id="barcode_img_id_<?=$x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
					</tr>
				</table>
			</table>
			<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
				<tr>
					<th rowspan="2">SL</th>
					<th rowspan="2">Type Of Sample</th>
					<th rowspan="2">Buyer</th>
					<th rowspan="2">Requisition No</th>
					<th rowspan="2">Booking No</th>
					<th rowspan="2">Style Ref.</th>
					<!-- <th rowspan="2">Order No</th> -->
					<th rowspan="2">Color Name</th>
					<th rowspan="2">Item Name</th>
					<th colspan="<? echo $count_size;?>">Size</th>
					<th rowspan="2">Delivery Qnty</th>
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>
					<?
					foreach($size_array as $key=>$val)
					{
						?>
						<th><? echo $size_library[$val]; ?></th>

						<?
					}
					?>
				</tr>
				<?
				$i=0;
				$total_delv=0;
				foreach($data_array as $sample_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$row)
						{
							$i++;
							$basis=$row["basis"];
							$sample_development_id=$row["sample_development_id"];
							if($basis==2)
							{
								$buyer=$buyer_lib[$po_array[$sample_development_id]["buyer"]];
								$job_or_book=$po_array[$sample_development_id]["job"];
								$style=$po_array[$sample_development_id]["style_ref_no"];
								$order_no=$po_array[$sample_development_id]["po_number"];


							}
							else if($basis==3)
							{
								$buyer=$buyer_lib[$booking_array[$sample_development_id]["buyer"]];
								$job_or_book=$booking_array[$sample_development_id]["book"];
							}
							else if($basis==1)
							{
								//$buyer=$buyer_lib[$buyer_id["buyer"]];
								$buyer=$buyer_lib[$buyer_name_arr[$sample_development_id]];
								//$style=$style_ref_no;
								$style=$style_data[$sample_development_id];

								$requisition_no=$req_no_arr[$sample_development_id];
		    					$booking_no=$req_wise_booking[$booking_no_arr[$sample_development_id]];
		    					//$order_no=$po_number_arr[$booking_no_arr[$sample_development_id]];
							}
							?>
							<tr>
								<td align="left"><? echo $i; ?></td>
								<td align="left"><? echo $lib_sample[$sample_id]; ?></td>
								<td align="left"><? echo $buyer; ?></td>
								<td align="left"><? echo $requisition_no; ?></td>
								<td align="left"><? echo $booking_no; //$job_or_book; ?></td>
								<td align="left"><? echo $style; ?></td>
								<!-- <td align="left"><? //echo $order_no; ?></td> -->
								<td align="left"><? echo $color_library[$color_id]; ?></td>
								<td align="left"><? echo $garments_item[$item_id]; ?></td>
								<?
								 foreach($size_array as $key=>$val)
								 {
								 	?>
								 	<td align="right"><? echo $data_array_size_wise[$sample_id][$item_id][$color_id][$val]; ?></td>

								 	<?
								 }
								 $total_delv+=$row["qnty"];
								?>
								<td align="center"><? echo $row["qnty"]; ?></td>
								<td align="center"><? echo $row["remarks"]; ?></td>
							</tr>
							<?
						}
					}
				}
				?>
				<tr bgcolor="#E4E4E4">
					<td  align="right"  colspan="<? echo 8+$count_size; ?>" ><strong>Grand Total :</strong></td>
					<td align="right"><strong><? echo $total_delv; ?></strong></td>
					<td><strong> </strong></td>
				</tr>
				<tr>
					<td colspan="<? echo 10+$count_size; ?>" > In Words : <?  echo number_to_words($total_delv); ?> Pcs</td>
				</tr>
			</table>
			<br>
			<!-- ============= Gate Pass Info Start ========= -->
            <table style="margin-right:-40px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <tr>
                    <td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: left; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                </tr>
                <tr>
                    <td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                    <td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                    <td colspan="2"><strong>To Company:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                    <td colspan="3"><strong>Carried By:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>From Location:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                    <td colspan="2"><strong>To Location:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                    <td colspan="3"><strong>Driver Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass ID:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                    <td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                    <!-- <td align="center"><strong>Kg</strong></td>
                    <td align="center"><strong>Roll</td> -->
                    <td align="center" colspan="3"><strong>PCS</td>
                    <td colspan="3"><strong>Vehicle Number:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                    <!-- <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                    <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td> -->
                    <td align="center" colspan="3"><?php
                    if ($gatePassDataArr[$system_no]['gate_pass_id'] !="")
                    {
                        if ($total_delv>0) {
                            echo $total_delv;
                         }
                    }
                    ?></td>
                    <td colspan="3"><strong>Driver License No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                    <td colspan="2"><strong>Dept. Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                    <td colspan="3"><strong>Mobile No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Time:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                    <td colspan="2"><strong>Attention:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                    <td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Returnable:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                    <td colspan="2"><strong>Purpose:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Est. Return Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                    <td colspan="2"><strong>Remarks:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                </tr>
            </table>
                    <!-- ============= Gate Pass Info End =========== -->
        <table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >
			<tr>
				<? $width='1000px'; echo signature_table(127, $company_name,$width, ''); ?>
			</tr>
        </table>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
        <script>

            var main_value='<? echo $challan;?>';
            $('#qrcode').qrcode(main_value);
        </script>

        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $txt_challan_no; ?>');

            //for gate pass barcode
            function generateBarcodeGatePass(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#gate_pass_barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }

            if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
            {
                generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
            }
        </script>
        <div style="page-break-after:always;"></div>
		<?php
	exit();
}

if($action=="print_delivery_4")//shariar
{
	extract($_REQUEST);
	list($txt_challan_no,$mst_update_id,$cbo_company_name,$cbo_delivery_basis)=explode('*',$data);
	$company_name=str_replace("'", "", $cbo_company_name);
	$mst_id=$mst_update_id;
	$challan=$txt_challan_no;
	$system_no=str_replace("'", "", $txt_challan_no);
 	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 	$lib_sample=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
 	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$department_arr=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$dealing_marchant_lib=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
	$mst_data=sql_select("SELECT sys_number,ex_factory_date,final_destination,delivery_to,sent_by from sample_ex_factory_mst where id=$mst_id and entry_form_id=132 and status_active=1 and is_deleted=0");

	//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where status_active =1 and is_deleted=0 order by id", "id","po_number"  );


	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.SECURITY_LOCK_NO,a.DRIVER_NAME,a.DRIVER_LICENSE_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company_name." AND a.basis = 28 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	//echo $sql_get_pass; die;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');

				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					//$row['LOCATION_ID'] = $row['LOCATION_ID'];
				}

				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_lib[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $location_lib[ $row['LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
			}
		}
	}

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	$data_array=array();
	$size_array=array();

	$data_sql="SELECT b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id,sum(c.size_pass_qty) as qnty,b.remarks  from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=$mst_id group by b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id , b.remarks";
	$sample_dep_id=array();
	foreach(sql_select($data_sql) as $keys=>$vals)
	{
		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["basis"]=$vals[csf("delivery_basis")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["qnty"]+=$vals[csf("qnty")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["remarks"]=$vals[csf("remarks")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["sample_development_id"]=$vals[csf("sample_development_id")];

		$data_array_size_wise[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]+=$vals[csf("qnty")];

		$size_array[$vals[csf("size_id")]]=$vals[csf("size_id")];
		$booking_or_po_id[$vals[csf("sample_development_id")]]=$vals[csf("sample_development_id")];
		array_push($sample_dep_id, $vals[csf("sample_development_id")]);
	}
	$id_cond= where_con_using_array($sample_dep_id,0,"id");

	$res_style = sql_select("select requisition_number_prefix_num,requisition_number,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) $id_cond  and status_active=1 and is_deleted=0");
	$style_data=array();
	$req_no_arr=array();
	$booking_no_arr=array();
	$buyer_name_arr=array();
  	foreach($res_style as $result)
	{
	    $style_data[$result[csf('id')]]=$result[csf('style_ref_no')];
	    $req_no_arr[$result[csf('id')]]=$result[csf('requisition_number')];
	    $booking_no_arr[$result[csf('id')]]=$result[csf('id')];
	    $buyer_name_arr[$result[csf('id')]]=$result[csf('buyer_name')];
	}
	$all_po_or_booking_id=implode(",", $booking_or_po_id);
	if(!$all_po_or_booking_id)
	{
		$all_po_or_booking_id=0;
	}
	$count_size=count($size_array);
	$po_array=array();
	$booking_array=array();
	$cbo_delivery_basis=str_replace("'","", $cbo_delivery_basis);
	if($cbo_delivery_basis==2)
	{
		$po_sql="SELECT b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_or_booking_id) group by b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number ";

		foreach(sql_select($po_sql) as $key=>$rows)
		{
			$po_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_name")];
			$po_array[$rows[csf("id")]]["job"]=$rows[csf("job_no_prefix_num")];
			$po_array[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
			$po_array[$rows[csf("id")]]["po_number"]=$rows[csf("po_number")];
			$po_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];
		}
		$dealing_marchant=$po_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==3)
	{
		$booking_sql="SELECT a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.id in($all_po_or_booking_id) group by a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant";

		foreach(sql_select($booking_sql) as $key=>$rows)
		{
			$booking_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_id")];
			$booking_array[$rows[csf("id")]]["book"]=$rows[csf("booking_no_prefix_num")];
			$booking_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];

		}
		$dealing_marchant=$booking_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==1)
	{
		$req_sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where id in ($all_po_or_booking_id) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 order by id asc";
		//echo $req_sql; die;
		foreach (sql_select($req_sql) as $row) {
			$dealing_marchant_arr[$row[csf('id')]] = $dealing_marchant_lib[$row[csf('dealing_marchant')]];
			$buyer_id=$row[csf('buyer_name')];
			$style_ref_no=$row[csf('style_ref_no')];
		}
		$dealing_marchant = implode(",", $dealing_marchant_arr);
	}
	/*echo '<pre>';
	print_r($dealing_marchant); die;*/
		?>
		<div style="width:1000px; border:1px solid #fff;margin:0px auto; ">
			<table width="100%" cellspacing="0" align="right" cellpadding="2"  >
				<tr>
				<td rowspan="5"> <img src="../../<? echo $image_location; ?>" height="70" width="200" style="float:left;"></td>
				<td  colspan="10" align="center"><strong style=" font-size:25;"><? echo $company_library[str_replace("'", "", $company_name)]; ?>
						</strong></td>
					<!-- <td   align="left"  style="font-size:xx-large;">
						<div style="float:left;width:34%; text-align:right">
							<div style="float:left; height:5%; width:5%;" id="qrcode"></div>
						</div>
					</td> -->
					<td width="34%"  align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>


				</tr>
				<tr>
					<td colspan="9" align="center"  width="80%" >
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$company_name'");
						foreach ($nameArray as $result)
						{
							?>
							<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") echo $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center"><strong>100% Export Oriented Garments</strong></td>
					<td colspan="">&nbsp;</td>

				</tr>
				<tr>
					<td colspan="7"  align="center"><strong>Sample Delivery Challan</strong></td>
					 <td colspan="">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6" height="5">  </td>
				</tr>
				<table width="100%" cellspacing="0" border="1" align="right" rules="all"  class="rpt_table" cellpadding="2">
					<tr>
						<td ><strong>Challan NO:</strong>   </td>
						<td><? echo $mst_data[0][csf("sys_number")]; ?></td>
						<td><strong>Challan Date :</strong></td>
						<td> <? echo change_date_format($mst_data[0][csf('ex_factory_date')]); ?></td>
					</tr>
					<tr>
						<?
							//$seto_to_full = array(1=>'Buying House',2=>'Planning', 3=>'Dyeing', 4=>'Test', 5=>'Self')
						?>
						<td ><strong>Sent To:</strong>   </td>
						<td><? echo $sample_sent_to_list[$mst_data[0][csf("delivery_to")]]; ?></td>
						<td ><strong>Final Destination:</strong></td>
						<td > <? echo $mst_data[0][csf('final_destination')]; ?> </td>
					</tr>
					<tr>
						<td ><strong>Dealing Merchant</strong>   </td>
						<td><? echo $dealing_marchant;?></td>
						<td ><strong>Sent By</strong></td>
						<td ><? echo $mst_data[0][csf('sent_by')]; ?> </td>
					</tr>
					<tr>
						<td align="center" colspan="6" id="barcode_img_id_<?=$x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
					</tr>
				</table>
			</table>
			<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
				<tr>
					<th rowspan="2">SL</th>
					<th rowspan="2">Type Of Sample</th>
					<th rowspan="2">Buyer</th>
					<th rowspan="2">Requisition No</th>
					<th rowspan="2">Booking No</th>
					<th rowspan="2">Style Ref.</th>
					<!-- <th rowspan="2">Order No</th> -->
					<th rowspan="2">Color Name</th>
					<th rowspan="2">Item Name</th>
					<th colspan="<? echo $count_size;?>">Size</th>
					<th rowspan="2">Delivery Qnty</th>
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>
					<?
					foreach($size_array as $key=>$val)
					{
						?>
						<th><? echo $size_library[$val]; ?></th>

						<?
					}
					?>
				</tr>
				<?
				$i=0;
				$total_delv=0;
				foreach($data_array as $sample_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$row)
						{
							$i++;
							$basis=$row["basis"];
							$sample_development_id=$row["sample_development_id"];
							if($basis==2)
							{
								$buyer=$buyer_lib[$po_array[$sample_development_id]["buyer"]];
								$job_or_book=$po_array[$sample_development_id]["job"];
								$style=$po_array[$sample_development_id]["style_ref_no"];
								$order_no=$po_array[$sample_development_id]["po_number"];


							}
							else if($basis==3)
							{
								$buyer=$buyer_lib[$booking_array[$sample_development_id]["buyer"]];
								$job_or_book=$booking_array[$sample_development_id]["book"];
							}
							else if($basis==1)
							{
								//$buyer=$buyer_lib[$buyer_id["buyer"]];
								$buyer=$buyer_lib[$buyer_name_arr[$sample_development_id]];
								//$style=$style_ref_no;
								$style=$style_data[$sample_development_id];

								$requisition_no=$req_no_arr[$sample_development_id];
		    					$booking_no=$req_wise_booking[$booking_no_arr[$sample_development_id]];
		    					//$order_no=$po_number_arr[$booking_no_arr[$sample_development_id]];
							}
							?>
							<tr>
								<td align="left"><? echo $i; ?></td>
								<td align="left"><? echo $lib_sample[$sample_id]; ?></td>
								<td align="left"><? echo $buyer; ?></td>
								<td align="left"><? echo $requisition_no; ?></td>
								<td align="left"><? echo $booking_no; //$job_or_book; ?></td>
								<td align="left"><? echo $style; ?></td>
								<!-- <td align="left"><? //echo $order_no; ?></td> -->
								<td align="left"><? echo $color_library[$color_id]; ?></td>
								<td align="left"><? echo $garments_item[$item_id]; ?></td>
								<?
								 foreach($size_array as $key=>$val)
								 {
								 	?>
								 	<td align="right"><? echo $data_array_size_wise[$sample_id][$item_id][$color_id][$val]; ?></td>

								 	<?
								 }
								 $total_delv+=$row["qnty"];
								?>
								<td align="center"><? echo $row["qnty"]; ?></td>
								<td align="center"><? echo $row["remarks"]; ?></td>
							</tr>
							<?
						}
					}
				}
				?>
				<tr bgcolor="#E4E4E4">
					<td  align="right"  colspan="<? echo 8+$count_size; ?>" ><strong>Grand Total :</strong></td>
					<td align="right"><strong><? echo $total_delv; ?></strong></td>
					<td><strong> </strong></td>
				</tr>
				<tr>
					<td colspan="<? echo 10+$count_size; ?>" > In Words : <?  echo number_to_words($total_delv); ?> Pcs</td>
				</tr>
			</table>
			<br>
			<!-- ============= Gate Pass Info Start ========= -->
            <table style="margin-right:-40px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <tr>
                    <td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: left; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                </tr>
                <tr>
                    <td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                    <td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                    <td colspan="2"><strong>To Company:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                    <td colspan="3"><strong>Carried By:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>From Location:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                    <td colspan="2"><strong>To Location:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                    <td colspan="3"><strong>Driver Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass ID:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                    <td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                    <!-- <td align="center"><strong>Kg</strong></td>
                    <td align="center"><strong>Roll</td> -->
                    <td align="center" colspan="3"><strong>PCS</td>
                    <td colspan="3"><strong>Vehicle Number:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                    <!-- <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                    <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td> -->
                    <td align="center" colspan="3"><?php
                    if ($gatePassDataArr[$system_no]['gate_pass_id'] !="")
                    {
                        if ($total_delv>0) {
                            echo $total_delv;
                         }
                    }
                    ?></td>
                    <td colspan="3"><strong>Driver License No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                    <td colspan="2"><strong>Dept. Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                    <td colspan="3"><strong>Mobile No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Time:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                    <td colspan="2"><strong>Attention:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                    <td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Returnable:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                    <td colspan="2"><strong>Purpose:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Est. Return Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                    <td colspan="2"><strong>Remarks:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                </tr>
            </table>
			<br>
			<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
				<tr>
					<th rowspan="2">SL</th>
					<th rowspan="2">Type Of Sample</th>
					<th rowspan="2">Buyer</th>
					<th rowspan="2">Requisition No</th>
					<th rowspan="2">Booking No</th>
					<th rowspan="2">Style Ref.</th>
					<!-- <th rowspan="2">Order No</th> -->
					<th rowspan="2">Color Name</th>
					<th rowspan="2">Item Name</th>
					<th colspan="<? echo $count_size;?>">Size</th>
					<th rowspan="2">Delivery Qnty</th>
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>
					<?
					foreach($size_array as $key=>$val)
					{
						?>
						<th><? echo $size_library[$val]; ?></th>

						<?
					}
					?>
				</tr>
				<?
				$i=0;
				$total_delv=0;
				foreach($data_array as $sample_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$row)
						{
							$i++;
							$basis=$row["basis"];
							$sample_development_id=$row["sample_development_id"];
							if($basis==2)
							{
								$buyer=$buyer_lib[$po_array[$sample_development_id]["buyer"]];
								$job_or_book=$po_array[$sample_development_id]["job"];
								$style=$po_array[$sample_development_id]["style_ref_no"];
								$order_no=$po_array[$sample_development_id]["po_number"];


							}
							else if($basis==3)
							{
								$buyer=$buyer_lib[$booking_array[$sample_development_id]["buyer"]];
								$job_or_book=$booking_array[$sample_development_id]["book"];
							}
							else if($basis==1)
							{
								//$buyer=$buyer_lib[$buyer_id["buyer"]];
								$buyer=$buyer_lib[$buyer_name_arr[$sample_development_id]];
								//$style=$style_ref_no;
								$style=$style_data[$sample_development_id];

								$requisition_no=$req_no_arr[$sample_development_id];
		    					$booking_no=$req_wise_booking[$booking_no_arr[$sample_development_id]];
		    					//$order_no=$po_number_arr[$booking_no_arr[$sample_development_id]];
							}
							?>
							<tr>
								<td align="left"><? echo $i; ?></td>
								<td align="left"><? echo $lib_sample[$sample_id]; ?></td>
								<td align="left"><? echo $buyer; ?></td>
								<td align="left"><? echo $requisition_no; ?></td>
								<td align="left"><? echo $booking_no; //$job_or_book; ?></td>
								<td align="left"><? echo $style; ?></td>
								<!-- <td align="left"><? //echo $order_no; ?></td> -->
								<td align="left"><? echo $color_library[$color_id]; ?></td>
								<td align="left"><? echo $garments_item[$item_id]; ?></td>
								<?
								 foreach($size_array as $key=>$val)
								 {
								 	?>
								 	<td align="right"><? echo $data_array_size_wise[$sample_id][$item_id][$color_id][$val]; ?></td>

								 	<?
								 }
								 $total_delv+=$row["qnty"];
								?>
								<td align="center"><? echo $row["qnty"]; ?></td>
								<td align="center"><? echo $row["remarks"]; ?></td>
							</tr>
							<?
						}
					}
				}
				?>
				<tr bgcolor="#E4E4E4">
					<td  align="right"  colspan="<? echo 8+$count_size; ?>" ><strong>Grand Total :</strong></td>
					<td align="right"><strong><? echo $total_delv; ?></strong></td>
					<td><strong> </strong></td>
				</tr>
				<tr>
					<td colspan="<? echo 10+$count_size; ?>" > In Words : <?  echo number_to_words($total_delv); ?> Pcs</td>
				</tr>
			</table>
			<br>
                    <!-- ============= Gate Pass Info End =========== -->
        <table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >
			<tr>
				<? $width='1000px'; echo signature_table(302, $company_name,$width, ''); ?>
			</tr>
        </table>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
        <script>

            var main_value='<? echo $challan;?>';
            $('#qrcode').qrcode(main_value);
        </script>

        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $txt_challan_no; ?>');

            //for gate pass barcode
            function generateBarcodeGatePass(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#gate_pass_barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }

            if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
            {
                generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
            }
        </script>
        <div style="page-break-after:always;"></div>
		<?php
	exit();
}

if($action=="print_delivery_5")
{
	extract($_REQUEST);
	list($txt_challan_no,$mst_update_id,$cbo_company_name,$cbo_delivery_basis)=explode('*',$data);
	$company_name=str_replace("'", "", $cbo_company_name);
	$mst_id=$mst_update_id;
	$challan=$txt_challan_no;
	$system_no=str_replace("'", "", $txt_challan_no);
 	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 	$lib_sample=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
 	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$department_arr=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$dealing_marchant_lib=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
	$mst_data=sql_select("SELECT sys_number,ex_factory_date,final_destination,delivery_to,sent_by from sample_ex_factory_mst where id=$mst_id and entry_form_id=132 and status_active=1 and is_deleted=0");
	$sample_library_arr=return_library_array( "select id,sample_name   from   lib_sample ", "id", "sample_name"  );
	//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where status_active =1 and is_deleted=0 order by id", "id","po_number"  );
	//for gate pass
	 $sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.SECURITY_LOCK_NO,a.DRIVER_NAME,a.DRIVER_LICENSE_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company_name." AND a.basis = 28 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	//echo $sql_get_pass; die;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');

				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];

			}
		}
	}
	//echo "<pre>";print_r($gatePassDataArr);
	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	$data_array=array();
	$size_array=array();

	$data_sql="SELECT b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id,sum(c.size_pass_qty) as qnty,b.remarks  from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=$mst_id group by b.sample_development_id,a.delivery_basis,b.sample_name,b.gmts_item_id,c.color_id,c.size_id , b.remarks";
	$sample_dep_id=array();
	foreach(sql_select($data_sql) as $keys=>$vals)
	{
		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["basis"]=$vals[csf("delivery_basis")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["qnty"]+=$vals[csf("qnty")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["remarks"]=$vals[csf("remarks")];

		$data_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]]["sample_development_id"]=$vals[csf("sample_development_id")];

		$data_array_size_wise[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]+=$vals[csf("qnty")];

		$size_array[$vals[csf("size_id")]]=$vals[csf("size_id")];
		$booking_or_po_id[$vals[csf("sample_development_id")]]=$vals[csf("sample_development_id")];
		array_push($sample_dep_id, $vals[csf("sample_development_id")]);
	}
	$id_cond= where_con_using_array($sample_dep_id,0,"id");

	$res_style = sql_select("select requisition_number_prefix_num,requisition_number,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) $id_cond  and status_active=1 and is_deleted=0");
	$style_data=array();
	$req_no_arr=array();
	$booking_no_arr=array();
	$buyer_name_arr=array();
  	foreach($res_style as $result)
	{
	    $style_data[$result[csf('id')]]=$result[csf('style_ref_no')];
	    $req_no_arr[$result[csf('id')]]=$result[csf('requisition_number')];
	    $booking_no_arr[$result[csf('id')]]=$result[csf('id')];
	    $buyer_name_arr[$result[csf('id')]]=$result[csf('buyer_name')];
	}
	$all_po_or_booking_id=implode(",", $booking_or_po_id);
	if(!$all_po_or_booking_id)
	{
		$all_po_or_booking_id=0;
	}
	$count_size=count($size_array);
	$po_array=array();
	$booking_array=array();
	$cbo_delivery_basis=str_replace("'","", $cbo_delivery_basis);
	if($cbo_delivery_basis==2)
	{
		$po_sql="SELECT b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_or_booking_id) group by b.id, a.buyer_name,a.dealing_marchant,a.job_no_prefix_num,a.style_ref_no,b.po_number ";

		foreach(sql_select($po_sql) as $key=>$rows)
		{
			$po_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_name")];
			$po_array[$rows[csf("id")]]["job"]=$rows[csf("job_no_prefix_num")];
			$po_array[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
			$po_array[$rows[csf("id")]]["po_number"]=$rows[csf("po_number")];
			$po_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];
		}
		$dealing_marchant=$po_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==3)
	{
		$booking_sql="SELECT a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.id in($all_po_or_booking_id) group by a.buyer_id,b.id,a.booking_no_prefix_num,a.dealing_marchant";

		foreach(sql_select($booking_sql) as $key=>$rows)
		{
			$booking_array[$rows[csf("id")]]["buyer"]=$rows[csf("buyer_id")];
			$booking_array[$rows[csf("id")]]["book"]=$rows[csf("booking_no_prefix_num")];
			$booking_array[$rows[csf("id")]]["dealing_marchant"]=$dealing_marchant[$rows[csf("dealing_marchant")]];

		}
		$dealing_marchant=$booking_array[$all_po_or_booking_id]["dealing_marchant"];
	}
	else if($cbo_delivery_basis==1)
	{
		$req_sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where id in ($all_po_or_booking_id) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 order by id asc";
		//echo $req_sql; die;
		foreach (sql_select($req_sql) as $row) {
			$dealing_marchant_arr[$row[csf('id')]] = $dealing_marchant_lib[$row[csf('dealing_marchant')]];
			$buyer_id=$row[csf('buyer_name')];
			$style_ref_no=$row[csf('style_ref_no')];
		}
		$dealing_marchant = implode(",", $dealing_marchant_arr);
	}
	/*echo '<pre>';
	print_r($dealing_marchant); die;*/
		?>
		<div style="width:1000px; border:1px solid #fff;margin:0px auto; ">
			<table width="100%" cellspacing="0" align="right" cellpadding="2"  >
				<tr>
				<td rowspan="5"> <img src="../../<? echo $image_location; ?>" height="70" width="200" style="float:left;"></td>
				<td  colspan="10" align="center"><strong style=" font-size:25;"><? echo $company_library[str_replace("'", "", $company_name)]; ?>
						</strong></td>
					<!-- <td   align="left"  style="font-size:xx-large;">
						<div style="float:left;width:34%; text-align:right">
							<div style="float:left; height:5%; width:5%;" id="qrcode"></div>
						</div>
					</td> -->
					<td width="34%"  align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>


				</tr>
				<tr>
					<td colspan="9" align="center"  width="80%" >
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$company_name'");
						foreach ($nameArray as $result)
						{
							?>
							<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") echo $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center"><strong>100% Export Oriented Garments</strong></td>
					<td colspan="">&nbsp;</td>

				</tr>
				<tr>
					<td colspan="7"  align="center"><strong>Sample Delivery Challan</strong></td>
					 <td colspan="">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6" height="5">  </td>
				</tr>
				<table width="100%" cellspacing="0" border="1" align="right" rules="all"  class="rpt_table" cellpadding="2">
					<tr>
						<td ><strong>Challan NO:</strong>   </td>
						<td><? echo $mst_data[0][csf("sys_number")]; ?></td>
						<td><strong>Challan Date :</strong></td>
						<td> <? echo change_date_format($mst_data[0][csf('ex_factory_date')]); ?></td>
					</tr>
					<tr>
						<?
							//$seto_to_full = array(1=>'Buying House',2=>'Planning', 3=>'Dyeing', 4=>'Test', 5=>'Self')
						?>
						<td ><strong>Sent To:</strong>   </td>
						<td><? echo $sample_sent_to_list[$mst_data[0][csf("delivery_to")]]; ?></td>
						<td ><strong>Final Destination:</strong></td>
						<td > <? echo $mst_data[0][csf('final_destination')]; ?> </td>
					</tr>
					<tr>
						<td ><strong>Dealing Merchant</strong>   </td>
						<td><? echo $dealing_marchant;?></td>
						<td ><strong>Sent By</strong></td>
						<td ><? echo $mst_data[0][csf('sent_by')]; ?> </td>
					</tr>
					<tr>
						<td align="center" colspan="6" id="barcode_img_id_<?=$x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
					</tr>
				</table>
			</table>
			<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
				<tr>
					<th rowspan="2">SL</th>
					<th rowspan="2">Type Of Sample</th>
					<th rowspan="2">Buyer</th>
					<th rowspan="2">Requisition No</th>
					<th rowspan="2">Booking No</th>
					<th rowspan="2">Style Ref.</th>
					<!-- <th rowspan="2">Order No</th> -->
					<th rowspan="2">Color Name</th>
					<th rowspan="2">Item Name</th>
					<th colspan="<? echo $count_size;?>">Size</th>
					<th rowspan="2">Delivery Qnty</th>
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>
					<?
					foreach($size_array as $key=>$val)
					{
						?>
						<th><? echo $size_library[$val]; ?></th>

						<?
					}
					?>
				</tr>
				<?
				$i=0;
				$total_delv=0;
				foreach($data_array as $sample_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$row)
						{
							$i++;
							$basis=$row["basis"];
							$sample_development_id=$row["sample_development_id"];
							if($basis==2)
							{
								$buyer=$buyer_lib[$po_array[$sample_development_id]["buyer"]];
								$job_or_book=$po_array[$sample_development_id]["job"];
								$style=$po_array[$sample_development_id]["style_ref_no"];
								$order_no=$po_array[$sample_development_id]["po_number"];


							}
							else if($basis==3)
							{
								$buyer=$buyer_lib[$booking_array[$sample_development_id]["buyer"]];
								$job_or_book=$booking_array[$sample_development_id]["book"];
							}
							else if($basis==1)
							{
								//$buyer=$buyer_lib[$buyer_id["buyer"]];
								$buyer=$buyer_lib[$buyer_name_arr[$sample_development_id]];
								//$style=$style_ref_no;
								$style=$style_data[$sample_development_id];

								$requisition_no=$req_no_arr[$sample_development_id];
		    					$booking_no=$req_wise_booking[$booking_no_arr[$sample_development_id]];
		    					//$order_no=$po_number_arr[$booking_no_arr[$sample_development_id]];
							}
							?>
							<tr>
								<td align="left"><? echo $i; ?></td>
								<td align="left"><? echo $lib_sample[$sample_id]; ?></td>
								<td align="left"><? echo $buyer; ?></td>
								<td align="left"><? echo $requisition_no; ?></td>
								<td align="left"><? echo $booking_no; //$job_or_book; ?></td>
								<td align="left"><? echo $style; ?></td>
								<!-- <td align="left"><? //echo $order_no; ?></td> -->
								<td align="left"><? echo $color_library[$color_id]; ?></td>
								<td align="left"><? echo $garments_item[$item_id]; ?></td>
								<?
								 foreach($size_array as $key=>$val)
								 {
								 	?>
								 	<td align="right"><? echo $data_array_size_wise[$sample_id][$item_id][$color_id][$val]; ?></td>

								 	<?
								 }
								 $total_delv+=$row["qnty"];
								?>
								<td align="center"><? echo $row["qnty"]; ?></td>
								<td align="center"><? echo $row["remarks"]; ?></td>
							</tr>
							<?
						}
					}
				}
				?>
				<tr bgcolor="#E4E4E4">
					<td  align="right"  colspan="<? echo 8+$count_size; ?>" ><strong>Grand Total :</strong></td>
					<td align="right"><strong><? echo $total_delv; ?></strong></td>
					<td><strong> </strong></td>
				</tr>
				<tr>
					<td colspan="<? echo 10+$count_size; ?>" > In Words : <?  echo number_to_words($total_delv); ?> Pcs</td>
				</tr>
			</table>
			<br><br><br>
			<table width="100%" cellspacing="0" align="right"   border="1" rules="all"  class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="100" align="center">Item Category</th>
						<th width="100" align="center">Sample</th>
						<th width="200" align="center">Item Description</th>
						<th width="50" align="center">UOM</th>
						<th width="80" align="center">Quantity</th>
						<th width="80" align="center">Reject Qty</th>
						<th width="50" align="center">Rate</th>
						<th width="80" align="center">Amount </th>
						<th width="100" align="center">Buyer</th>
						<th width="100" align="center">Style</th>
						<th width="160" align="center">Order No</th>
						<th width="100" align="center">Color</th>
						<th width="60" align="center">NO of Bags/Rolls/GMT</th>
						<th width="70" align="center">Carton Qty</th>
						<th align="center">Remarks</th>
					</thead>
				<?
	 				$sql_get_pass_qry = "SELECT  a.basis, a.company_id, a.challan_no, b.quantity, b.no_of_bags,b.item_category_id, b.sample_id, b.item_description,b.uom_qty,b.reject_qty, b.uom, b.rate, b.amount, b.buyer_order_id, b.buyer_order,b. remarks, b.challan_no, b.total_carton_qty from inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id = b.mst_id and a.company_id = ".$company_name." and a.basis = 28 and a.status_active = 1 and a.is_deleted = 0 and a.challan_no like '".$system_no."%'";

				$sql_get_pass_rslt = sql_select($sql_get_pass_qry);
				$uom_check=array();
				$tot_qty=0;$tot_amount=0;$total_bags=$total_carton_qty=0;
				foreach($sql_get_pass_rslt as $row)
				{

					$ref_id=$row[csf("challan_no")];
					if ($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$color_name=rtrim($order_array[$row[csf('buyer_order_id')]]['color'],', ');
					$buyer=$order_array[$row[csf('buyer_order_id')]]['buyer_name'];
					$style_ref_no=$order_array[$row[csf('buyer_order_id')]]['style_ref_no'];
					$uom_check[$row[csf('uom')]]=$row[csf('uom')];
					$desc=trim($row[csf('item_description')]);

					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><?  echo $item_category[$row[csf('item_category_id')]]; ?></td>
							<td><?  echo $sample_library_arr[$row[csf('sample_id')]]; ?></td>
							<td><?  echo $row[csf('item_description')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo $row[csf('quantity')]; ?></td>
							<td align="right"><? echo $row[csf('reject_qty')]; ?></td>
							<td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
							<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
							<td align="center"><? echo $buyer_library[$buyer];?></td>
							<td><?echo $style_ref_no;?></td>
							<td><? echo $row[csf('buyer_order')];?></td>
							<td ><? echo $color_name; ?></td>
							<td align="right"><? echo $row[csf('no_of_bags')];?></td>
							<td align="right"><? echo $row[csf('total_carton_qty')];?></td>
							<td><? echo $row[csf('remarks')]; ?></td>
						</tr>
					<?
					$i++;
					$tot_qty+= $row[csf('quantity')];
					$tot_reject_qty+=$row[csf('reject_qty')];
					$tot_amount+=$row[csf('amount')];
					$total_bags+=$row[csf('no_of_bags')];
					$total_carton_qty+=$row[csf('total_carton_qty')];
					$inWordTxt=$unit_of_measurement[$row[csf('uom')]];
				}

				if(count($uom_check)==1)
				{
					?>
					<tfoot bgcolor="#CCCCCC">
						<tr>
							<td colspan="5" align="right"><b>Total</b></td>
							<td align="right"><b><? echo number_format($tot_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($tot_reject_qty,2);?></b></td>
							<td></td><td align="right"><b> <? echo number_format($tot_amount,2);?></b></td>
							<td colspan="4"></td>
							<td align="right"><b><? echo number_format($total_bags,4);?></b></td>
							<td align="right"><b><? echo number_format($total_carton_qty,4);?></b></td>
							<td></td>
						</tr>
					</tfoot>
					<?
					$uom_id=implode(",",$uom_check);
				}

				?>
			</table>
			<br><br><br>
			<!-- ============= Gate Pass Info Start ========= -->
            <table style="margin-right:-40px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <tr>
                    <td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: left; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                </tr>
                <tr>
                    <td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                    <td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                    <td colspan="2"><strong>To Company:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                    <td colspan="3"><strong>Carried By:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>From Location:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                    <td colspan="2"><strong>To Location:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                    <td colspan="3"><strong>Driver Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass ID:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                    <td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                    <!-- <td align="center"><strong>Kg</strong></td>
                    <td align="center"><strong>Roll</td> -->
                    <td align="center" colspan="3"><strong>PCS</td>
                    <td colspan="3"><strong>Vehicle Number:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                    <!-- <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                    <td align="center"><?php //echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td> -->
                    <td align="center" colspan="3"><?php
                    if ($gatePassDataArr[$system_no]['gate_pass_id'] !="")
                    {
                        if ($total_delv>0) {
                            echo $total_delv;
                         }
                    }
                    ?></td>
                    <td colspan="3"><strong>Driver License No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                    <td colspan="2"><strong>Dept. Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                    <td colspan="3"><strong>Mobile No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Out Time:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                    <td colspan="2"><strong>Attention:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                    <td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Returnable:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                    <td colspan="2"><strong>Purpose:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Est. Return Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                    <td colspan="2"><strong>Remarks:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                </tr>
            </table>
                    <!-- ============= Gate Pass Info End =========== -->
        <table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >
			<tr>
				<? $width='1000px'; echo signature_table(127, $company_name,$width, ''); ?>
			</tr>
        </table>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
        <script>

            var main_value='<? echo $challan;?>';
            $('#qrcode').qrcode(main_value);
        </script>

        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $txt_challan_no; ?>');

            //for gate pass barcode
            function generateBarcodeGatePass(valuess)
            {
                var zs = '<?php echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#gate_pass_barcode_img_id_"+zs).html('11');
                value = {code: value, rect: false};
                $("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
            }

            if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
            {
                generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
            }
        </script>
        <div style="page-break-after:always;"></div>
		<?php
	exit();
}
function load_html_head_contentss($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart, $jqlatest)
{
	$html='
	<!DOCTYPE HTML>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>'.$title.'</title>
		<script src="'.$path.'includes/functions.js" type="text/javascript"></script>';


		if( $jqlatest==1 ) $html .=' <script type="text/javascript" src="'.$path.'js/jquery_latest.js"></script>';
		else  $html .=' <script type="text/javascript" src="'.$path.'js/jquery.js"></script>';

		if ( $filter!="" )
			$html .='
		<link href="'.$path.'css/filtergrid.css" rel="stylesheet" type="text/css" media="screen" />
		<script src="'.$path.'js/tablefilter.js" type="text/javascript"></script>';

		if ( $popup!="" )
			$html .='
		<link href="'.$path.'css/modal_window.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="'.$path.'js/modal_window.js"></script>';
		if ( $unicode!="" )
			$html .='
		<script type="text/javascript" src="'.$path.'js/driver.phonetic.js" ></script>
		<script type="text/javascript" src="'.$path.'js/driver.probhat.js" ></script>
		<script type="text/javascript" src="'.$path.'js/engine.js" ></script>';

		if ( $multi_select!="" )
			$html .='
		<script src="'.$path.'js/multi_select.js" type="text/javascript"></script>';
		if ($am_chart!="")
			$html .='
		<script type="text/javascript" src="'.$path.'ext_resource/amcharts/flash/swfobject.js" ></script>
		<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/amcharts.js" ></script>
		<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/amfallback.js" ></script>
		<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/raphael.js" ></script>
		<script type="text/javascript" src="'.$path.'js/chart/logic_chart.js" ></script>';

		return $html; die;
}

if($action == 'get_qty_source_sample')
{
	$sample_source = sql_select("SELECT qty_source_sample from variable_settings_production where company_name = $data and variable_list=53 and status_active =1 and is_deleted=0");
 	foreach ($sample_source as $row) {
 		$sample_qty_src = trim($row[csf('qty_source_sample')]);
 	}
 	echo $sample_qty_src;
}

if($action=="invoice_popup")
{
	extract($_REQUEST);
	//echo $company_id."=".$requisition_id;die;
	$sql_inv="select a.ID, a.INVOICE_NO from COM_EXPORT_INVOICE_SHIP_MST a, COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id and a.LC_FOR=2 and b.PO_BREAKDOWN_ID=$requisition_id group by a.ID, a.INVOICE_NO";
	//echo $sql_inv;die;
	$sql_inv_result=sql_select($sql_inv);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
	?>
	<html>
	<head>
		<script>

			function js_set_value( data )
			{
				var data_arr = data.split(',');
				//alert(data+"="+data_arr);
				document.getElementById('txt_inv_id').value=data_arr[0];
				document.getElementById('txt_inv_no').value=data_arr[1];
				parent.emailwindow.hide();
			}
		</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" width="500" class="rpt_table" align="center" border="1" rules="all">
                    <thead>
                        <th width="100">SL</th>
                        <th>Invoice No
                        <input type="hidden" id="txt_inv_id" name="txt_inv_id" />
                        <input type="hidden" id="txt_inv_no" name="txt_inv_no" />
                        </th>
                    </thead>
                    <tbody>
                    <?
					$i=1;
					foreach($sql_inv_result as $row)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";

						//echo $smp_id_arr[$row[csf('id')]] in js_set_value
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['ID'];?>,<? echo $row['INVOICE_NO'];?>');" >
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row['INVOICE_NO'];?></p></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                    </tbody>
                </table>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
?>