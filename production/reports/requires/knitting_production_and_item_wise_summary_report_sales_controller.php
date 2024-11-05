<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	$ex_data=explode('**',$data);
	if($ex_data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else if($ex_data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",1,"" );
	}
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
// common end

if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no)
		{
			document.getElementById('hidden_job_no').value=job_no;
			parent.emailwindow.hide();
		}	
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

								</th> 
							</thead>
							<tr class="general">
								<td align="center">	
									<?
									echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", $cbo_within_group,$dd,0 );
									?>
								</td> 
								<td align="center">	
									<?
									$serach_type_arr=array(1=>'Sales Order No',2=>'Fab. Booking No');
									echo create_drop_down( "cbo_serach_type", 150, $serach_type_arr,"",0, "--Select--","","",0 );
									?>
								</td>           
								<td align="center">				
									<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" placeholder="Write" />	
								</td> 						
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'knitting_production_and_item_wise_summary_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
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

if($action=="create_sales_order_no_search_list")
{
	$data 			= explode('_',$data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location",'id','location_name');

	if($db_type==0)
	{
		if($yearID!=0) $year_cond=" and YEAR(a.insert_date)=$yearID"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($yearID!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$yearID";  else $year_cond="";
	}

	$within_group_cond  = ($within_group == 0)?"":" and a.within_group=$within_group";
	if($serach_type==1)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.job_no like '%$sales_order_no%'";
	}
	else if($serach_type==2)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.sales_booking_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2)? "to_char(a.insert_date,'YYYY') as year":"YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>			
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1){
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				}else{
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="110" align="center"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>					
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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

if ($action=="booking_no_search_popup")
{
	echo load_html_head_contents("Booking No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		var tableFilters = 
		{
			col_11: "select",
			display_all_text:'Show All'
		}
		
		function js_set_value(data)
		{
			$('#hidden_booking_data').val(data);
			parent.emailwindow.hide();
		}

	</script>

	</head>
	<body>
		<div align="center">
			<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
				<fieldset style="width:98%;">
					<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
					<div id="content_search_panel" >
						<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
							<thead>
								<th>Buyer</th>
								<th>Booking Date</th>
								<th>Search By</th>
								<th id="search_by_td_up" width="200">Please Enter Booking No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $companyID; ?>">
									<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes" value="">  
								</th>
							</thead>
							<tr>
								<td align="center">
									<? 
									$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
									$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in ($user_wise_buyer) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
									echo create_drop_down( "cbo_buyer", 150, $buyer_sql,"id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">	
									<?
									$search_by_arr=array(1=>"Booking No",2=>"Job No");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>                 
								<td align="center" id="search_by_td">				
									<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
								</td> 						
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+<? echo $cbo_booking_type; ?>, 'create_booking_search_list_view', 'search_div', 'knitting_production_and_item_wise_summary_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="5" align="center"  valign="middle"><? echo load_month_buttons(1); ?></td>
							</tr>
						</table>
					</div>
					<table width="100%" style="margin-top:5px">
						<tr>
							<td colspan="5">
								<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
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

if($action=="create_booking_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string 	= "%".trim($data[0])."%";
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$date_from 		= trim($data[3]);
	$date_to 		= trim($data[4]);
	$buyer_id 		= $data[5];
	$booking_type 		= $data[6];
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	if($unit_id==0)
	{
		$unit_id_cond="";
	}
	else
	{
		$unit_id_cond=" and a.company_id=$unit_id";
	}
	if($buyer_id==0)
	{
		$buyer_id_cond= $buyer_id_cond;
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.job_no like '$search_string'";
	}
	
	$date_cond='';
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$po_arr=return_library_array( "select id,po_number from wo_po_break_down",'id','po_number');
	$import_booking_id_arr=return_library_array( "select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0",'id','booking_id');
	
	$apporved_date_arr=return_library_array( "select mst_id,max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id",'mst_id','approved_date');
	
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');

	$entry_form_cond = ($booking_type > 0)?" and a.entry_form=$booking_type":"";

	$sql= "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b WHERE a.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $entry_form_cond group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="65">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th width="80">Booking Date</th>     
			<th width="80">App. Date</th>     
			<th width="80">Delivery Date</th> 
			<th width="70">Currency</th>
			<th width="60">Approved</th>   
			<th>PO No.</th>             
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=explode(",",$row[csf('po_break_down_id')]);
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')"> 
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>          
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>               
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>       
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			}
			
			//partial booking start;
			$partial_sql= "SELECT a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date,a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, listagg(d.po_break_down_id, ',') within group (order by d.po_break_down_id) as po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 $entry_form_cond $buyer_id_cond $unit_id_cond $search_field_cond $date_cond group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
			$partial_result = sql_select($partial_sql);
			foreach ($partial_result as $row)
			{ 
				if(!in_array($row[csf('id')],$import_booking_id_arr)) 
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if($row[csf('po_break_down_id')]!="")
					{
						$po_no='';
						$po_ids=array_unique(explode(",",$row[csf('po_break_down_id')]));
						foreach($po_ids as $po_id)
						{
							if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
						}
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')"> 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
						<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
						<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
						<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
						<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
						<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>          
						<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>               
						<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>       
						<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
						<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
						<td style="word-break: break-all;"><? echo $po_no; ?></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<?	
	exit();
}

if($action=="job_no_search_popup")
{
	echo load_html_head_contents("Job No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('hide_job_no').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="120">Please Enter Order No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									if($ordType==1)
									{
										echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
									}
									else if($ordType==2)
									{
										echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
									}
									?>
								</td>                 
								<td align="center">	
									<?
									$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
									$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
									echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>     
								<td align="center" id="search_by_td">				
									<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
								</td> 
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_job_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[5]==1)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$data[1]";
		}


		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1) 
			$search_field="b.po_number"; 
		else if($search_by==2) 
			$search_field="a.style_ref_no"; 	
		else 
			$search_field="a.job_no";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";

		
		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "select $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by a.id DESC";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0,", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else if($data[5]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1) 
			$search_field="b.order_no"; 
		else if($search_by==2) 
			$search_field="b.cust_style_ref"; 	
		else 
			$search_field="a.job_no_prefix_num";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date)";
			$style_cond="group_concat(b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY')";
			$style_cond="listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";//defined Later
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "select $year_field as year, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, $style_cond as cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, a.insert_date order by a.id DESC";

		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref.", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_id,party_id,0,0,0,", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref", "",'','0,0,0,0,0','') ;
	}
	exit(); 
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year); 
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$report_type=str_replace("'","",$report_type);
	$sales_order_no = str_replace("'","",$txt_sales_order);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_within_group = str_replace("'","",$cbo_within_group);
	$sales_order_cond = ($sales_order_no !="")?" and e.job_no like '%$sales_order_no%' " : "";
	//echo $cbo_type.'=='.$report_type;
	// if($report_type==2)
	// echo "string";die;
	if($cbo_type==1 || $cbo_type==0)
	{
		if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
		$booking_no = str_replace("'","",$txt_booking_no);
		if($booking_no !="") $booking_no_cond=" and e.sales_booking_no like '%$booking_no%' "; else $booking_no="";
		if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";
		if (str_replace("'","",$txt_booking_no)=="") $booking_cond=''; else $booking_cond=" and e.sales_booking_no='$booking_no'";

		if($db_type==0)
		{
			$year_field="YEAR(f.insert_date)";
			$year_field_sam="YEAR(a.insert_date)";
			if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(f.insert_date,'YYYY')";
			$year_field_sam="to_char(a.insert_date,'YYYY')";
			if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
		}
		else $year_field="";
		$from_date=$txt_date_from;
		if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

		if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

		$date_con="";
		if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
		$machine_details=array();
		$machine_data=sql_select("select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null");
		//echo "select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
		$machine_in_not=array("CC","GS");
		foreach($machine_data as $row)
		{
			$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
			$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			$machine_details[$row[csf('id')]]['brand']=$row[csf('brand')];
			
			if(!in_array($row[csf('machine_no')],$machine_in_not) && ($row[csf('dia_width')]!="" && $row[csf('gauge')]!="")) 
			{ 
				//if($row[csf('machine_no')]=='GS') echo $row[csf('machine_no')].', ';
				$total_machine[$row[csf('id')]]=$row[csf('id')];
			}
		}
		//print_r($machine_in_not);
		$composition_arr=$construction_arr=array();
		$sql_deter="select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
	
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				$yarn_type_arr[$row[csf('id')]]=$yarn_type[$row[csf('yarn_type')]];
			}
		}
	
		$knit_plan_arr=array();
		$plan_data=sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
		foreach($plan_data as $row)
		{
			$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
			$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')]; 
			$knit_plan_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
			$knit_plan_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')]; 
		}
	}
	// ===============================================================================

	$i=1;
	$con = connect();
    $r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
    oci_commit($con);
	$sql_inhouse="(select b.kniting_charge, a.company_id, a.receive_basis, a.booking_no,nvl(f.booking_type, 1) booking_type, 1 as is_order, f.entry_form, b.febric_description_id, b.gsm,b.width, b.stitch_length, c.po_breakdown_id, e.job_no, e.sales_booking_no, e.booking_id, b.reject_fabric_receive as reject_qty, c.is_sales, e.buyer_id unit_id, e.within_group, a.knitting_source, a.knitting_company, e.buyer_id, c.quantity";
	$within_group_cond = ($cbo_within_group != 0)?" and e.within_group=$cbo_within_group" : "";

	//$entry_form_cond = ($cbo_booking_type > 0)?" and f.entry_form=$cbo_booking_type":"";
	if($cbo_booking_type > 0)
	{
		if($cbo_booking_type == 89){
			$entry_form_cond = " and f.booking_type = 4 ";
		}
		else
		{
			$entry_form_cond = " and f.entry_form=$cbo_booking_type";
		}
	}
	else
	{
		$entry_form_cond = "";
	}

	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond)";

	$sql_inhouse .= " union all  (SELECT b.kniting_charge, a.company_id, a.receive_basis, a.booking_no,nvl(g.booking_type, 1) booking_type, 2 as is_order, g.entry_form_id as entry_form, b.febric_description_id, b.gsm, b.width, b.stitch_length, 
	c.po_breakdown_id, e.job_no, e.sales_booking_no, e.booking_id, b.reject_fabric_receive as reject_qty, c.is_sales, e.buyer_id unit_id, e.within_group, a.knitting_source, a.knitting_company, e.buyer_id, c.quantity";

	$within_group_cond = ($cbo_within_group != 0)?" and e.within_group=$cbo_within_group" : "";

	if($cbo_booking_type > 0)
	{	
		if($cbo_booking_type == 90)
		{
			$entry_form_cond = " and g.booking_type=4";
		}
		else
		{
			$entry_form_cond = " and g.entry_form_id=$cbo_booking_type";
		}
	}
	else
	{
		$entry_form_cond = "";
	}

	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond)";
	
	$sql_inhouse.=" union all (select b.kniting_charge, a.company_id, a.receive_basis, a.booking_no, 999 as booking_type, 1 as is_order, null as entry_form, b.febric_description_id, b.gsm, b.width, b.stitch_length, c.po_breakdown_id, e.job_no, e.sales_booking_no, e.booking_id, b.reject_fabric_receive as reject_qty,c.is_sales, e.buyer_id unit_id, e.within_group, a.knitting_source, a.knitting_company, e.buyer_id, c.quantity";
	if($cbo_booking_type > 0)
	{	
		$entry_form_cond = " and a.id=0";
	}
	else
	{
		$entry_form_cond = "";
	}
	
	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond )";
	// echo $sql_inhouse.'DD';
	$nameArray_inhouse=sql_select( $sql_inhouse);
	
	
	if(str_replace("'","",$cbo_knitting_source)==0 || str_replace("'","",$cbo_knitting_source)==2)
	{
		if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";
		$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
		//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst 
		$sql_inhouse_sub="SELECT 999 as receive_basis,a.insert_date,a.inserted_by, a.product_date as receive_date, null as booking_no, 999 as booking_type, 1 as is_order, null as entry_form, b.cons_comp_id as prod_id,b.gsm, b.dia_width as width, b.stitch_len as stitch_length, b.machine_dia as machine_dia, b.machine_gg as machine_gg, b.order_id as po_breakdown_id, d.job_no_mst as job_no, null as sales_booking_no, b.reject_qnty as reject_qty,0 as is_sales, a.party_id as unit_id,0 as within_group, 2 as knitting_source, a.knitting_company,a.party_id as buyer_id, b.product_qnty, a.company_id
		from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d 
		where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0 and b.shift!=0
		and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $sales_order_cond $booking_no_cond $within_group_cond";

		//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
		if($cbo_booking_type==0)
		{
			$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
		}
	}
	// =========================== end =====================

	$tbl_width=1000;
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
		</tr>
		<tr> 
			<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:12px" ><strong><? if(str_replace("'","",$txt_date_from)!="") echo "From ".str_replace("'","",$txt_date_from); if(str_replace("'","",$txt_date_to)!="") echo " To ".str_replace("'","",$txt_date_to); ?></strong></td>
		</tr>
	</table>
	<?
	if($report_type==2) // Production Summary
	{
		?>
	    <div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width: 725px;"><strong><u><i>In-House + Outbound + Inbound [Knitting Production]</i></u></strong></div>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	        <thead>
	            <tr>
	                <th colspan="10">Knit Production Summary (In-House + Outbound + Inbound)</th>
	            </tr>
	            <tr>
	                <th width="40" rowspan="2">SL</th>
	                <th width="100" rowspan="2">Buyer</th>
	                <th width="90" colspan="2">Inhouse</th>
	                <th width="140" colspan="2">Outbound-Subcon</th>
	                <th width="90" rowspan="2">Inbound-Subcon</th>
	                <th width="90" rowspan="2">Sample With Order</th>
	                <th width="90" rowspan="2">Sample Without Order</th>
	                <th width="60" rowspan="2">Total</th>
	            </tr>
	            <tr>
	                <th width="60">WG Yes</th>
	                <th>WG No</th>
	                <th>WG Yes</th>
	                <th>WG No</th>
	            </tr>
	        </thead>
	        <tbody>
				<?
				
		        $booking_id_check=array();
				foreach ($nameArray_inhouse as $row)
				{
					if( $booking_id_check[$row[csf('booking_id')]] =="" )
		            {
		                $booking_id_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
		                $booking_id = $row[csf('booking_id')];
		                // echo "insert into tmp_booking_id (userid, booking_id) values ($user_id,$booking_id)";
		                $r_id=execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_id,$booking_id)");
		            }
				}
				oci_commit($con);
				//echo $sql_inhouse;

				$get_booking_buyer = sql_select("SELECT a.booking_no, a.buyer_id from wo_booking_mst a, tmp_booking_id b where a.id=b.booking_id and b.userid=$user_id and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 
				union all
				select a.booking_no, a.buyer_id from wo_non_ord_samp_booking_mst a, tmp_booking_id b where  a.id=b.booking_id and b.userid=$user_id and a.booking_type=4 and a.status_active=1 and a.is_deleted=0");
				foreach ($get_booking_buyer as $booking_row)
				{
					$booking_arr[$booking_row[csf("booking_no")]] = $buyer_arr[$booking_row[csf("buyer_id")]];
				}		

				$machine_inhouse_array=$total_running_machine=$buyer_wise_production_arr=array();
				foreach ($nameArray_inhouse as $row)
				{
					$buyer_id = ($row[csf("within_group")]==1)?$booking_arr[$row[csf("sales_booking_no")]]:$buyer_arr[$row[csf("buyer_id")]];
					$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += $row[csf("quantity")];
				}
				
				foreach ($nameArray_inhouse_subcon as $row)
				{
					$buyer_id =$buyer_arr[$row[csf("buyer_id")]];
					$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += $row[csf("product_qnty")];
				}
				
				//echo "<pre>";
				//print_r($buyer_wise_production_arr);die;

				$r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
		        oci_commit($con);
				
				$k=1;
				foreach($buyer_wise_production_arr as $buyer => $rows)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$out_bound_qnty=0;
					$out_bound_qnty_wg_yes=$rows[3][1][1][1] + $rows[3][1][1][2] + $rows[3][999][1][1] +$service_buyer_data[$buyer];
					$out_bound_qnty_wg_no=$rows[3][1][2][1]+ $rows[3][1][2][2] + $rows[3][999][2][1]+ $service_buyer_data[$buyer];						
					$in_bound_qnty=0;
					$in_bound_qnty=$rows[2][999][0][1];
					$sample_with_order=$rows[1][4][1][1]+$rows[3][4][1][1];
					$sample_without_order=$rows[1][4][1][2]+$rows[3][4][1][2];

					$tot_summ=$rows[1][1][1][1]+$rows[1][999][2][1]+$out_bound_qnty_wg_yes+$out_bound_qnty_wg_no+$sample_with_order+$sample_without_order+$in_bound_qnty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center" style="width: 40px;"><? echo $k; ?></td>
						<td align="center" title="PO Buyer-<? echo $buyer;?>"><? echo $buyer; ?></td>
						<td align="right" style="width: 45px;"><? echo number_format($rows[1][1][1][1],2,'.',''); // $rows[1][1][1][1] ?></td>
						<td align="right" style="width: 45px;"><? echo number_format($rows[1][999][2][1],2,'.',''); // $rows[1][1][2][1] ?></td>
						<td align="right"><? echo number_format($out_bound_qnty_wg_yes,2,'.',''); ?></td>
						<td align="right"><? echo number_format($out_bound_qnty_wg_no,2,'.',''); ?></td> 
		                <td align="right"><? echo number_format($in_bound_qnty,2,'.',''); ?></td>
						<td align="right"><? echo  number_format($sample_with_order,2,'.',''); ?></td>
						<td align="right"><? echo number_format($sample_without_order,2,'.','');?></td>
						<td align="right"><? echo  number_format($tot_summ,2,'.',''); ?></td>
					</tr>
					<?
					$tot_qtyinhouse_wg_yes += $rows[1][1][1][1];
					$tot_qtyinhouse_wg_no += $rows[1][999][2][1];
					$tot_qtyoutbound_wg_yes += $out_bound_qnty_wg_yes;
					$tot_qtyoutbound_wg_no += $out_bound_qnty_wg_no;
					$tot_qtyinbound += $in_bound_qnty;
					$tot_sample_with_order += $sample_with_order;
					$tot_sample_without_order += $sample_without_order;
					$total_summ += $tot_summ;
					// unset($subcon_buyer_samary[$buyer]);
					$k++;
				}
				?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <th colspan= "2" align="right"><strong>Total</strong></th>
	                <th align="right"><? echo number_format($tot_qtyinhouse_wg_yes,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($tot_qtyinhouse_wg_no,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($tot_qtyoutbound_wg_yes,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($tot_qtyoutbound_wg_no,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($tot_qtyinbound,2,'.',''); ?></th>
	                
	                <th align="right"><? echo number_format($tot_sample_with_order,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($tot_sample_without_order,2,'.',''); ?></th>
	                <th align="right"><? echo number_format($total_summ,2,'.',''); ?></th>
	            </tr>
	            <tr>
	                <th colspan="2"><strong>In %</strong></th>
	                <th align="right"><? $qtyinhouse_per=($tot_qtyinhouse_wg_yes/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?></th>
	                <th align="right"><? $qtyinhouse_per=($tot_qtyinhouse_wg_no/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?></th>
	                <th align="right"><? $qtyoutbound_per=($tot_qtyoutbound_wg_yes/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?></th>
	                <th align="right"><? $qtyoutbound_per=($tot_qtyoutbound_wg_no/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?></th>
	                <th align="right"><? $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %'; ?></th>
	                
	                <th align="right"><? $qtyoutbound_per=($tot_sample_with_order/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?></th>
	                <th align="right"><? $qtyoutbound_per=($tot_sample_without_order/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?></th>
	                <th align="right"><? echo "100 %"; ?></th>
	            </tr>
	        </tfoot>
	    </table>
	    <?
	}
	else // Item Wise Summary
	{
		$item_wise_production_arr=array();
		foreach ($nameArray_inhouse as $row)
		{
			if($row[csf('receive_basis')]==2)
			{
				$mc_dia_gage=$knit_plan_arr[$row[csf('booking_no')]]['machine_dia']." X ".$knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
			}
			else
			{
				$mc_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
			}
			$str_ref=$mc_dia_gage."*".$row[csf("febric_description_id")]."*".$row[csf("stitch_length")]."*".$row[csf("width")]."*".$row[csf("gsm")];
			$item_wise_production_arr[$row[csf("knitting_source")]][$str_ref]['quantity'] += $row[csf("quantity")];
		}
		// echo "<pre>";print_r($item_wise_production_arr);die;
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	        <thead>
	            <tr>
	                <th colspan="9">Item Wise Summary</th>
	            </tr>
	            <tr>
	                <th width="40" rowspan="2">SL</th>
	                <th width="100" rowspan="2">Knitting Source</th>
	                <th width="90" rowspan="2">M/C Dia & Gauge</th>
	                <th width="140" rowspan="2">Construction</th>
	                <th width="90" rowspan="2">Composition</th>
	                <th width="90" rowspan="2">Stich</th>
	                <th width="90" rowspan="2">Dia</th>
	                <th width="60" rowspan="2">GSM</th>
	                <th width="60" rowspan="2">Production Qty.</th>
	            </tr>
	        </thead>
	        <tbody>
				<?				
				$j=1;
				$grand_total=0;
				foreach($item_wise_production_arr as $knitting_source_val => $knitting_sourceArr)
				{
					$knitting_total=0;
					foreach($knitting_sourceArr as $str_ref => $rows)
					{
						$str_ref_arr = explode("*", $str_ref);
						$mc_dia_gage=$str_ref_arr[0];
	                    $febric_description_id=$str_ref_arr[1];
	                    $stitch_length=$str_ref_arr[2];
	                    $dia=$str_ref_arr[3];
	                    $gsm=$str_ref_arr[4];
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center" style="width: 40px;"><? echo $j; ?></td>
							<td align="center"><? echo $knitting_source[$knitting_source_val]; ?></td>
							<td align="center" style="width: 45px;"><? echo $mc_dia_gage; ?></td>
							<td align="center"><? echo $construction_arr[$febric_description_id]; ?></td>
							<td align="center"><? echo $composition_arr[$febric_description_id]; ?></td> 
			                <td align="center"><? echo $stitch_length; ?></td>
							<td align="center"><? echo $dia; ?></td>
							<td align="center"><? echo $gsm;?></td>
							<td align="right"><? echo  number_format($rows['quantity'],2,'.',''); ?></td>
						</tr>
						<?
						$j++;
						$knitting_total += $rows['quantity'];
						$grand_total += $rows['quantity'];
					}
					?>
                    <!-- Color Total -->
                    <tr class="tbl_bottom">
                        <td align="right" colspan="8"><? echo $knitting_source[$knitting_source_val]; ?> Total</td>
                        <td ><? echo number_format($knitting_total,2,'.',''); ?></td>
                    </tr>
                    <? 
				}
				?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <th colspan="8" align="right"><strong>Grand Total</strong></th>
	                <th align="right"><? echo number_format($grand_total,2,'.',''); ?></th>
	            </tr>
	        </tfoot>
	    </table>
		<?
	}
    ?>
	<?
    foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
    }
    $name=time();
    $filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
    echo "$total_data####$filename";
    exit();
}

if($action=="delivery_challan_print")
{
	echo load_html_head_contents("Delivery Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);	

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$datas=explode('_',$data);
	$program_ids = $datas[0];
	$source_ids = $datas[1];
	$company = $datas[2];
	$from_date = $datas[3];
	$to_date = $datas[4];
	$in_out_data=explode(',',$datas[1]);
	//echo $from_date;
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	//$poNumber_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$po_array=array();
	$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id");
	foreach($po_data as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$knit_plan_arr=array();
	$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
		$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')]; 
	}	

	?>
	<div style="width:1360px;">
		<table width="1350" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="17" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="17" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <? echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></center></td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></center></td>
			</tr>
			<tr >
				<td colspan="17"  style="font-size:14px"><strong><? echo "Date Range :"." ". $from_date." "."To"." ".$to_date; ?></strong></center></td>
			</tr>

		</table>
	</div>
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="60" >Job No</th>
					<th width="90" >Order No</th>
					<th width="60" >Buyer</th>
					<th width="50" >Prod. ID</th>
					<th width="60" >M/C No</th>
					<th width="60" >Req. No</th>
					<th width="90" >Booking No/ Prog. No</th>
					<th width="60" >Yarn Count</th>
					<th width="70" >Yarn Brand</th>
					<th width="70" >Lot No</th>
					<th width="100" >Color</th>
					<th width="" >Fabric Type</th>
					<th width="50" >Stich</th>
					<th width="50" >Fin GSM</th>
					<th width="50" >Fab. Dia</th>
					<th width="50" >M/C Dia</th>
					<th width="50" >Total Roll</th>
					<th width="70" >Total Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:1350px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >

				<?	
				$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no" );
				$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id", "knit_id", "requisition_no"  );

				if($db_type==2) $date_cond="'".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
				if($db_type==0) $date_cond="'".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
				if($in_out_data[0]==1)
				{
					$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id,sum(case when c.entry_form=2 then b.no_of_roll else 0 end)  as roll_no, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  
					from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
					where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id	
					order by a.receive_date";	
				}
				else if ($in_out_data[0]==3)
				{
					$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id 
					order by b.floor_id,a.receive_date";	
				}
				else
				{
					$sql="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id,sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift, sum(case when a.entry_form=2 then b.grey_receive_qnty else 0 end)  as outqntyshift		
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where  a.item_category=13 and a.id=b.mst_id and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=2 
					and a.booking_without_order=1
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id	
					order by a.receive_date";
				}
	//echo $sql;
				$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}

					$reqsn_no=""; $stich_length=""; $color="";
					if($row[csf('receive_basis')]==2)
					{
						$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
						$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
						$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><div style="word-wrap:break-word; width:30px;"><? echo $i; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?></div></td>
						<td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['no']; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
						<td width="60" align="center"><div style="word-wrap:break-word; width:60px;"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $reqsn_no; ?></div></td>
						<td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $row[csf('booking_no')]; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $count; ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $brand_details[$row[csf('brand_id')]]; ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('yarn_lot')]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px;"><? echo $color; ?></div></td>
						<td width=""><div style="word-wrap:break-word; width:210px;"><? echo $composition_arr[$row[csf('febric_description_id')]];; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $stich_length; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('gsm')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('width')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></div></td>
						<td width="50" align="right"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('roll_no')]; $tot_roll+=$row[csf('roll_no')]; ?>&nbsp;</div></td>
						<td width="70" align="right"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('outqntyshift')]; $tot_qty+=$row[csf('outqntyshift')]; ?>&nbsp;</div></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr> 
					<td align="right" colspan="17" ><strong>Total:</strong></td>
					<td align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</td>
					<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="left"><b>Remarks: </b></td>
					<td colspan="17" ><? //echo number_to_words($tot_qty); ?>&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(44, $company, "1340px");
			?>
		</div>
	</div>
	<?
	exit();
}

//getYarnType
function getYarnType($yarn_type_arr, $yarnProdId)
{
	global $yarn_type;
	$yarn_type_name='';
	$expYPId = explode(",",$yarnProdId);
	$yarnTypeIdArr = array();
	foreach($expYPId as $key=>$val)
	{
		$yarnTypeIdArr[$yarn_type_arr[$val]] = $yarn_type_arr[$val];
	}
	
	foreach($yarnTypeIdArr as $key=>$val)
	{
		if($yarn_type_name == '')
			$yarn_type_name=$yarn_type[$val];
		else
			$yarn_type_name.=",".$yarn_type[$val];
	}
	return $yarn_type_name;
}
?>