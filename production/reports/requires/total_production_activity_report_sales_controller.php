<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

	require_once('../../../includes/common.php');

	$user_id=$_SESSION['logic_erp']['user_id'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if ($action=="load_drop_down_location")
	{
		echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "",0 );
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
	$floor_details=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
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
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'total_production_activity_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+<? echo $cbo_booking_type; ?>, 'create_booking_search_list_view', 'search_div', 'total_production_activity_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
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

/*$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;*/

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$report_type);
	$sales_order_no = str_replace("'","",$txt_sales_order);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_within_group = str_replace("'","",$cbo_within_group);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 

	$location_cond='';$location_cond_subcontract='';
	if(!empty($cbo_location_id))
	{
		$location_cond=" and a.knitting_location_id=$cbo_location_id ";
	}

	if($cbo_company==0)
		$cbo_company_cond="";
	else
		$cbo_company_cond=" and a.company_id in($cbo_company)";

	if($cbo_working_company==0)
	{
		$company_working_cond="";
	}
	else
	{
		$company_working_cond=" and a.knitting_company=$cbo_working_company";
	}

	$sales_order_cond = ($sales_order_no !="")?" and e.job_no like '%$sales_order_no%' " : "";

	$from_date = $txt_date_from;
	if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
	else $to_date = $txt_date_to;

	$order_date_cond="";
	if ($from_date != "" && $to_date != "") 
	{	
		if($db_type==0)
		{
			$order_date_cond = "and b.po_received_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$order_date_cond = "and b.po_received_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
	// ============== For Conversion rate Start ===========================
	$current_date=date("d-m-Y");
	$p=1;
	$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
	$company_wise_data=array();
	foreach($queryText as $row)
	{
		$company_wise_data[$row["COMPANY_ID"]]++;
	}
	//echo "<pre>";print_r($company_wise_data);die;
	//echo "select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID";die;
	//echo count($queryText);die;
	$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
	foreach($queryText as $val)
	{
		if($company_check_arr[$val["COMPANY_ID"]]=="")
		{
			$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
			$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
			$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
			$sCurrentDate = $sStartDate;
			$sEndDate = $sStartDate;
			$previous_date=$sStartDate;
			$previous_rate=$val["CONVERSION_RATE"];
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			
			$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
			$sEndDate = date("Y-m-d", strtotime($current_date));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			while ($sCurrentDate <= $sEndDate) {
				
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			$q=1;
		}
		else
		{
			$q++;
			$sStartDate = date("Y-m-d", strtotime($previous_date));
			if($company_wise_data[$val["COMPANY_ID"]]==$q)
			{
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				
				$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {
					
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date=$val["CON_DATE"];
				$previous_rate=$val["CONVERSION_RATE"];
			}
			else
			{
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date=$val["CON_DATE"];
				$previous_rate=$val["CONVERSION_RATE"];
			}
		}
		$p++;
	}
	unset($queryText);
	// echo "<pre>";print_r($conversion_data_arr[1]['13-09-2023']);die;
	// ============== For Conversion rate End ===========================
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="1300">
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

	<fieldset style="width:1160px;">
		<!-- Order Received Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1240" class="rpt_table">
			<thead>
				<tr>
			        <th colspan="14" align="center"><b>Order Received (LC Company)</b></th>
			    </tr>
			    <tr>
			        <th rowspan="2" width="100" align="center"><b>Company</b></th>
			        <th rowspan="2" width="100" align="center"><b>Buyer</b></th>
			        <th rowspan="2" width="100" align="center"><b>Job</b></th>
			        <th rowspan="2" width="100" align="center"><b>Internal Ref.</b></th>
			        <th rowspan="2" width="80" align="center"><b>Avg. Lead Time</b></th>
			        <th colspan="3" align="center"><b>Confirm Order</b></th>
			        <th colspan="3" align="center"><b>Projected Order</b></th>
			        <th colspan="3" align="center"><b>Total</b></th>
			    </tr>
			    <tr bgcolor="#EEE">
			        <th width="85" align="center"><b>Qty(Pcs)</b></th>
			        <th width="85" align="center"><b>Value(USD)</b></th>
			        <th width="80" align="center"><b>Avg. Rate</b></th>

			        <th width="85" align="center"><b>Qty.(Pcs)</b></th>
			        <th width="85" align="center"><b>Value(USD)</b></th>
			        <th width="80" align="center"><b>Avg. Rate</b></th>

			        <th width="85" align="center"><b>Qty.(Pcs)</b></th>
			        <th width="85" align="center"><b>Value(USD)</b></th>
			        <th width="" align="center"><b>Avg. Rate</b></th>
			    </tr>
			</thead>
		</table>
		<div style="width:1260px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="1240" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
				    if($is_insert_date_active==0){
				        $str_cond_b=" and b.PUB_SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";
				    }

					$orderSql="SELECT  a.COMPANY_NAME, A.BUYER_NAME,A.JOB_NO,B.GROUPING,B.PUB_SHIPMENT_DATE,B.PO_RECEIVED_DATE,B.ID,b.IS_CONFIRMED,(a.total_set_qnty*b.po_quantity) as PO_QTY, b.po_total_price as PO_VALUE	from wo_po_details_master a, wo_po_break_down b 
					where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.company_name in($cbo_company) and b.status_active=1 and b.shiping_status=1 $order_date_cond order by a.COMPANY_NAME asc";
				    //  echo $orderSql;//die;
				    $orderSqlRs=sql_select($orderSql);
				    $orderDataArr=array(); 
				    foreach($orderSqlRs as $rows)
				    {
				        $daysOnHand = datediff("d",$rows['PO_RECEIVED_DATE'],$rows['PUB_SHIPMENT_DATE']);
				    
				        $key=$rows[BUYER_NAME].'**'.$rows[JOB_NO].'**'.$rows[GROUPING];
				        $orderDataArr[$rows[COMPANY_NAME]][QTY][$key][$rows[IS_CONFIRMED]]+=$rows[PO_QTY];
				        $orderDataArr[$rows[COMPANY_NAME]][VAL][$key][$rows[IS_CONFIRMED]]+=$rows[PO_VALUE];
				        $orderDataArr[$rows[COMPANY_NAME]][LEAD_TIME][$key]+=$daysOnHand;

				  		$job_wise_po_no[$rows[JOB_NO]]+=1;
				    }
				    unset($orderSqlRs);
				    
					// echo "<pre>"; print_r($orderDataArr);
				    $grandTotal=array();	
					$i = 1;
					foreach($orderDataArr as $company=>$companyArr)
					{
						$sub_tot_confirm_qty=0; $sub_tot_confirm_value=0; $sub_tot_projected_qty=0; $sub_tot_projected_value=0; $sub_tot_total_qty=0; $sub_tot_total_value=0; 
						foreach($companyArr[QTY] as $key=>$rowArr)
						{
							list($buyer_id,$job_no,$internal_file)=explode('**',$key);
					        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					        
					        $avgRage[1]=$companyArr[VAL][$key][1]/$companyArr[QTY][$key][1];
					        $avgRage[2]=$companyArr[VAL][$key][2]/$companyArr[QTY][$key][2];
					        
					        $grandTotal[conf_qty]+=$companyArr[QTY][$key][1];
					        $grandTotal[conf_val]+=$companyArr[VAL][$key][1];
					        
					        $grandTotal[proj_qty]+=$companyArr[QTY][$key][2];
					        $grandTotal[proj_val]+=$companyArr[VAL][$key][2];
					        
					        $grandTotal[tot_qty]+=array_sum($companyArr[QTY][$key]);
					        $grandTotal[tot_val]+=array_sum($companyArr[VAL][$key]);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
								<td width="100" align="center"><?=$company_arr[$company]; ?></td>
								<td width="100" align="center"><?=$buyer_library[$buyer_id]; ?></td>
						        <td width="100" align="center"><?=$job_no;?></td>
						        <td width="100" align="center"><?=$internal_file;?></td>
						        <td width="80" align="center"><?=fn_number_format($companyArr[LEAD_TIME][$key]/$job_wise_po_no[$job_no], 2,'.',',');?></td>
						    
						        <td width="85" align="right"><?=fn_number_format($companyArr[QTY][$key][1], 2,'.',',') ;?></td>
						        <td width="85" align="right"><?=fn_number_format($companyArr[VAL][$key][1], 2,'.',',') ;?></td>                        
						        <td width="80" align="right"><?=fn_number_format($avgRage[1], 2,'.',',');?></td>
						    
						        <td width="85" align="right"><?=fn_number_format($companyArr[QTY][$key][2], 2,'.',',') ;?></td>
						        <td width="85" align="right"><?=fn_number_format($companyArr[VAL][$key][2], 2,'.',',') ;?></td>                        
						        <td width="80" align="right"><?=fn_number_format($avgRage[2],2,'.',',');?></td>
						            
						        <td width="85" align="right"><?= fn_number_format(array_sum($companyArr[QTY][$key]), 2,'.',',') ;?></td>
						        <td width="85" align="right"><?= fn_number_format(array_sum($companyArr[VAL][$key]), 2,'.',',') ;?></td>
						        <td width="" align="right" title="<?= array_sum($companyArr[VAL][$key]).'/'.array_sum($companyArr[QTY][$key]);?>"><?=fn_number_format(array_sum($companyArr[VAL][$key])/array_sum($companyArr[QTY][$key]),2,'.',',');?></td>
							</tr>
							<?
							$i++;
							$sub_tot_confirm_qty+=$companyArr[QTY][$key][1];
							$sub_tot_confirm_value+=$companyArr[VAL][$key][1];
							$sub_tot_projected_qty+=$companyArr[QTY][$key][2];
							$sub_tot_projected_value+=$companyArr[VAL][$key][2];
							$sub_tot_total_qty+=array_sum($companyArr[QTY][$key]);
							$sub_tot_total_value+=array_sum($companyArr[VAL][$key]);
						}
						
						?>
						<tr class="tbl_bottom">
							<td width="100"></td>
							<td width="100"></td>
				            <td width="100"></td>
				            <td width="100"></td>
				            <td width="80">Total </td>

				            <td width="85" align="right"><?=number_format($sub_tot_confirm_qty, 2,'.',',') ;?></td>
				            <td width="85" align="right"><?=fn_number_format($sub_tot_confirm_value, 2,'.',',') ;?></td>
				            <td width="80" align="right"></td>

				            <td width="85" align="right"><?=fn_number_format($sub_tot_projected_qty, 2,'.',',') ;?></td>
				            <td width="85" align="right"><?=fn_number_format($sub_tot_projected_value, 2,'.',',') ;?></td>
				            <td width="80" align="right"></td>

				            <td width="85" align="right"><?=fn_number_format($sub_tot_total_qty, 2,'.',',') ;?></td>
				            <td width="85" align="right"><?=fn_number_format($sub_tot_total_value, 2,'.',',') ;?></td>
				            <td width="" align="right"></td>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="1240" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
					<th width="100"></th>
					<th width="100"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="80">Grand Total</th>

		            <th width="85" align="right"><?=number_format($grandTotal[conf_qty], 2,'.',',') ;?></th>
		            <th width="85" align="right"><?=fn_number_format($grandTotal[conf_val], 2,'.',',') ;?></th>
		            <th width="80" align="right"></th>

		            <th width="85" align="right"><?=fn_number_format($grandTotal[proj_qty], 2,'.',',') ;?></th>
		            <th width="85" align="right"><?=fn_number_format($grandTotal[proj_val], 2,'.',',') ;?></th>
		            <th width="80" align="right"><?//=fn_number_format($grandTotal[proj_val]/$grandTotal[proj_qty],2,'.',',');?></th>

		            <th width="85" align="right"><?=fn_number_format($grandTotal[tot_qty], 2,'.',',') ;?></th>
		            <th width="85" align="right"><?=fn_number_format($grandTotal[tot_val], 2,'.',',') ;?></th>
		            <th width="" align="right"><?//=fn_number_format($grandTotal[tot_val]/$grandTotal[tot_qty],2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Order Received End-->	

		<!-- Knitting Production (Working Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="9" height="40" align="center"><strong>Knitting Production (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Working Company</th>
                    <th width="100">Floor</th>
                    <th width="100" align="center">Inhouse</th>
                    <th width="100" align="center">Sample With Order</th>
                    <th width="100" align="center">Sample Without Order</th>
                    <th width="100" align="center">In Bound Subcon</th>
                    <th width="100" align="center">Total</th>
                    <th width="" align="center">Total Delivery to Store Kg</th>
                </tr>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body2">
            <table width="830" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					if($cbo_type==1 || $cbo_type==0)
					{
						if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
						$booking_no = str_replace("'","",$txt_booking_no);
						if($booking_no !="") $booking_no_cond=" and e.sales_booking_no like '%$booking_no%' "; else $booking_no="";
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

						$date_con="";
						if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
					}
					
					// Production > Knitting Production
					$sql_inhouse="
					(SELECT A.ID,A.COMPANY_ID, A.KNITTING_COMPANY, A.RECEIVE_BASIS, NVL(F.BOOKING_TYPE, 1) BOOKING_TYPE, 1 AS IS_ORDER, F.ENTRY_FORM, B.FLOOR_ID AS FLOOR_ID, E.JOB_NO, E.SALES_BOOKING_NO, E.WITHIN_GROUP,A.KNITTING_SOURCE, C.QUANTITY AS QUANTITY";
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

					$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond $location_cond)";

					$sql_inhouse .= " union all  (SELECT A.ID,A.COMPANY_ID, A.KNITTING_COMPANY ,A.RECEIVE_BASIS, NVL(G.BOOKING_TYPE, 1) BOOKING_TYPE, 2 AS IS_ORDER, G.ENTRY_FORM_ID AS ENTRY_FORM, B.FLOOR_ID AS FLOOR_ID, E.JOB_NO,E.SALES_BOOKING_NO, E.WITHIN_GROUP,A.KNITTING_SOURCE, C.QUANTITY AS QUANTITY";
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
					}else
					{
						$entry_form_cond = "";
					}

					$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond $location_cond)";
					
					$sql_inhouse.=" union all (select A.ID,A.COMPANY_ID, A.KNITTING_COMPANY,A.RECEIVE_BASIS,999 AS BOOKING_TYPE, 1 AS IS_ORDER, NULL AS ENTRY_FORM, B.FLOOR_ID AS FLOOR_ID, E.JOB_NO, E.SALES_BOOKING_NO, E.WITHIN_GROUP,A.KNITTING_SOURCE, C.QUANTITY AS QUANTITY ";
					if($cbo_booking_type > 0)
					{	
						$entry_form_cond = " and a.id=0";
					}
					else
					{
						$entry_form_cond = "";
					}
					
					$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $entry_form_cond $location_cond) order by knitting_company  
					";
					//echo $sql_inhouse.'DD';				
					
					// subcon Sql
					if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";
					$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");

					$sql_inhouse_sub="SELECT A.COMPANY_ID, A.KNITTING_COMPANY, B.FLOOR_ID AS FLOOR_ID, 999 AS RECEIVE_BASIS, NULL AS BOOKING_NO, 999 AS BOOKING_TYPE, 1 AS IS_ORDER, NULL AS ENTRY_FORM, D.JOB_NO_MST AS JOB_NO, NULL AS SALES_BOOKING_NO, 0 AS WITHIN_GROUP, 2 AS KNITTING_SOURCE, B.PRODUCT_QNTY AS PRODUCT_QNTY
					from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d 
					where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0 
					and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $sales_order_cond $booking_no_cond $within_group_cond order by a.knitting_company";//and a.company_id=$cbo_company_name
		
					//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
					if($cbo_booking_type==0)
					{
						$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
					}
					
					// echo $sql_inhouse;
					$nameArray_inhouse=sql_select( $sql_inhouse);
					$production_id_arr=array();$wcomp_floor_summary_arr=array();
					$out_bound_qty=0;
					foreach ($nameArray_inhouse as $row)
					{
						if($row["KNITTING_SOURCE"]==1)//in-house
						{
							if($row["IS_ORDER"]==2)//in-house Sample Without Order
							{
								$wcomp_floor_summary_arr[$row['KNITTING_COMPANY']][$row['FLOOR_ID']][4]+=$row['QUANTITY'];//Floor Wise
							}
							elseif($row["IS_ORDER"]==1 && $row["BOOKING_TYPE"]==4) //Sample With Order
							{
								$wcomp_floor_summary_arr[$row['KNITTING_COMPANY']][$row['FLOOR_ID']][3]+=$row['QUANTITY'];//Floor Wise
							}
							else
							{
								$wcomp_floor_summary_arr[$row['KNITTING_COMPANY']][$row['FLOOR_ID']][1]+=$row['QUANTITY'];//Floor Wise
							}
						}
						else // out-bound subcon
						{
							$out_bound_qty+=$row['QUANTITY'];//Floor Wise
						}
						$production_id_arr[$row['ID']]=$row['ID'];
					}
					
					foreach ($nameArray_inhouse_subcon as $row)
					{
						$wcomp_floor_summary_arr[$row['KNITTING_COMPANY']][$row['FLOOR_ID']][5]+=$row['PRODUCT_QNTY'];//Floor Wise
					}
					// echo "<pre>";print_r($wcomp_floor_summary_arr);

					$con = connect();
					execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (999)");					
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$production_id_arr, $empty_arr);
					// Delivery to Store
					$delivery_sql="SELECT a.COMPANY_ID, a.KNITTING_COMPANY, a.KNITTING_SOURCE, e.FLOOR_ID, b.CURRENT_DELIVERY 
					from GBL_TEMP_ENGINE c, PRO_GREY_PROD_DELIVERY_DTLS b, PRO_GREY_PROD_DELIVERY_MST a, PRO_GREY_PROD_ENTRY_DTLS e
					where c.ref_val=b.GREY_SYS_ID and b.mst_id=a.id and b.GREY_SYS_ID=e.mst_id and b.SYS_DTLS_ID=e.id and c.entry_form=999 and c.user_id=$user_id and c.ref_from=1 and a.entry_form=56 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
					// echo $delivery_sql;
					$delivery_sql_data=sql_select( $delivery_sql);
					$delivery_in_qty_arr = array();$delivery_out_qty=0;
					foreach ($delivery_sql_data as $key => $row) 
					{
						if ($row['KNITTING_SOURCE']==1) 
						{
							$delivery_in_qty_arr[$row['KNITTING_COMPANY']][$row['FLOOR_ID']]+=$row['CURRENT_DELIVERY'];
						}
						else
						{
							$delivery_out_qty+=$row['CURRENT_DELIVERY'];
						}
						
						
					}
					// echo "<pre>";print_r($delivery_in_qty_arr);

					execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (999)");
					oci_commit($con);
					disconnect($con);

					$f=1;
					foreach($wcomp_floor_summary_arr as $wcompany=>$floorvalue)
					{
						$sub_tot_inhouse_qnty=0; $sub_tot_samplewith_qnty=0; $sub_tot_samplewithout_qnty=0; $sub_tot_subcon_in_qnty=0; $sub_tot_total_qnty=0;$sub_tot_delivery_in_qnty=0;
						foreach($floorvalue as $key=>$value)
						{
							if ($f % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$out_bound_qnty=$inhouse_qnty=0;

							$inhouse_qnty=$value[1];
							$out_bound_qnty=$value[2];
							$samplewith_qnty=$value[3];
							$samplewithout_qnty=$value[4];
							$subcon_in_qnty=$value[5];

							$tot_flr_summ=$out_bound_qnty+$inhouse_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr2_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $f; ?>" valign="top">
								<td width="50" align="center"><?=$f; ?></td>
						        <td width="100" align="center"><? echo $company_arr[$wcompany]; ?></td>
						        <td width="100" align="center"><? echo $floor_details[$key]; ?></td>
						        <td width="100" align="right"><? echo number_format($inhouse_qnty,2,'.',''); ?></td>
						        <td width="100" align="right"><? echo number_format($samplewith_qnty,2,'.',''); ?></td>					    
						        <td width="100" align="right"><? echo number_format($samplewithout_qnty,2,'.',''); ?></td>
						        <td width="100" align="right"><? echo number_format($subcon_in_qnty,2,'.',''); ?></td>
						        <td width="100" align="right"><? echo number_format($inhouse_qnty+$samplewith_qnty+$samplewithout_qnty+$subcon_in_qnty,2,'.',''); ?></td>
						        <td width="" align="right"><? echo number_format($delivery_in_qty_arr[$wcompany][$key],2,'.',''); ?></td>
							</tr>
							<?
							$f++;
							$sub_tot_inhouse_qnty+=$inhouse_qnty;
							$sub_tot_samplewith_qnty+=$samplewith_qnty;
							$sub_tot_samplewithout_qnty+=$samplewithout_qnty;
							$sub_tot_subcon_in_qnty+=$subcon_in_qnty;
							$sub_tot_total_qnty+=$inhouse_qnty+$samplewith_qnty+$samplewithout_qnty+$subcon_in_qnty;
							$sub_tot_delivery_in_qnty+=$delivery_in_qty_arr[$wcompany][$key];

							$grand_tot_inhouse_qnty+=$inhouse_qnty;
							$grand_tot_samplewith_qnty+=$samplewith_qnty;
							$grand_tot_samplewithout_qnty+=$samplewithout_qnty;
							$grand_tot_subcon_in_qnty+=$subcon_in_qnty;
							$grand_tot_total_qnty+=$inhouse_qnty+$samplewith_qnty+$samplewithout_qnty+$subcon_in_qnty;
							$grand_delivery_in_qnty+=$delivery_in_qty_arr[$wcompany][$key];
						}
						?>
						<tr class="tbl_bottom">
							<td width="50">&nbsp;</td>
		            		<td width="100">&nbsp;</td>
		                    <td width="100">Total</td>
		                    <td width="100" align="right"><?=number_format($sub_tot_inhouse_qnty, 2,'.',',') ;?></td>
		                    <td width="100" align="right"><?=number_format($sub_tot_samplewith_qnty, 2,'.',',') ;?></td>
		                    <td width="100" align="right"><?=number_format($sub_tot_samplewithout_qnty, 2,'.',',') ;?></td>
		                    <td width="100" align="right"><?=number_format($sub_tot_subcon_in_qnty, 2,'.',',') ;?></td>
		                    <td width="100" align="right"><?=number_format($sub_tot_total_qnty, 2,'.',',');?></td>
		                    <td width="" align="right"><?=number_format($sub_tot_delivery_in_qnty, 2,'.',',');?></td>
						</tr>
						<?
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr2_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $f; ?>" valign="top">
				        <td width="100" align="center" colspan="3"><strong>Out-Bound Sub Contract</strong></td>
				        <td width="100" align="center" colspan="5"><strong><? echo number_format($out_bound_qty,2,'.',''); ?></strong></td>
				        <td width="" align="right"><strong><? echo number_format($delivery_out_qty, 2,'.',','); ?></strong></td>
					</tr>
				</tbody>
			</table>
		</div>
		<table width="830" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">&nbsp;</th>
                    <th width="100">Grand Total</th>
                    <th width="100" align="right"><?=number_format($grand_tot_inhouse_qnty, 2,'.',',') ;?></th>
                    <th width="100" align="right"><?=number_format($grand_tot_samplewith_qnty, 2,'.',',') ;?></th>
                    <th width="100" align="right"><?=number_format($grand_tot_samplewithout_qnty, 2,'.',',') ;?></th>
                    <th width="100" align="right"><?=number_format($grand_tot_subcon_in_qnty, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_tot_total_qnty, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_delivery_in_qnty+$delivery_out_qty, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Knitting Production (Working Company) End-->

		<!-- Sewing Input and Output (Working Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="12" height="40" align="center"><strong>Sewing Input and Output (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Working Company</th>
                    <th width="100">Floor</th>
                    <th width="100" align="center">Lay Qty Pcs</th>
                    <th width="100" align="center">Cutting QC Pcs</th>
                    <th width="100" align="center">Print Send Pcs</th>
                    <th width="100" align="center">Print Receive Pcs</th>
                    <th width="100" align="center">Emb Send Pcs</th>
                    <th width="100" align="center">Emb Receive Pcs</th>
                    <th width="100" align="center">Input Qty Pcs</th>
                    <th width="100" align="center">Output Qty Pcs</th>
                    <th width="" align="center">Packing Qty Pcs</th>
                </tr>
			</thead>
		</table>
		<div style="width:1150px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body3">
            <table width="1130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					if($cbo_working_company>0) $company_working_cond=" and a.WORKING_COMPANY_ID=$cbo_working_company";
					if($cbo_working_company>0) $company_working_cond2=" and a.SERVING_COMPANY=$cbo_working_company";

					$from_date = $txt_date_from;
					if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
					else $to_date = $txt_date_to;

					$date_con="";
					if ($from_date != "" && $to_date != "") 
					{	
						$date_con = "and a.ENTRY_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
						$date_con2 = "and a.PRODUCTION_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

						//Cut and Lay Entry Ratio Wise 3
						$lay_qty_sql="SELECT A.COMPANY_ID, A.WORKING_COMPANY_ID, A.FLOOR_ID, A.ENTRY_DATE , B.SIZE_QTY FROM PPL_CUT_LAY_MST a, PPL_CUT_LAY_BUNDLE b
						WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=99 AND a.STATUS_ACTIVE =1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and a.COMPANY_ID in($cbo_company) $company_working_cond $date_con";
						// echo $lay_qty_sql;
						
						$gmt_prod_sql="SELECT a.COMPANY_ID, a.SERVING_COMPANY, a.PRODUCTION_TYPE, a.EMBEL_NAME, a.FLOOR_ID, a.PRODUCTION_DATE, b.PRODUCTION_QNTY
						FROM PRO_GARMENTS_PRODUCTION_MST a, PRO_GARMENTS_PRODUCTION_DTLS b
						WHERE A.ID=B.MST_ID and a.PRODUCTION_TYPE in(1,2,3,4,5,8) and b.PRODUCTION_TYPE in(1,2,3,4,5,8) AND a.STATUS_ACTIVE =1 AND A.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and a.COMPANY_ID in($cbo_company) $date_con2";
						// echo $gmt_prod_sql;
					}
					
					$lay_qty_sql_result=sql_select($lay_qty_sql);
					$lay_qty_data_arr=array();
					foreach ($lay_qty_sql_result as $key => $value)
					{
						$lay_qty_data_arr[$value[WORKING_COMPANY_ID]][$value[FLOOR_ID]]['LAY_QTY']+=$value[SIZE_QTY];
					}

					$gmt_prod_sql_result=sql_select($gmt_prod_sql);
					foreach ($gmt_prod_sql_result as $key => $val)
					{
						if($val['PRODUCTION_TYPE'] ==1)//Cutting
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['CUTTING_QC']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==1 )//Print send
			            {		
			               $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['PRINT_SEND']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==1 )//Print rcv
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['PRINT_RECEIVE']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==2 )//emb send
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['EMB_SEND']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==2 )//emb rcv
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['EMB_RCV']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==4 )//sewing input
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['SWEING_INPUT']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==5 )//sewing_output
			            {		
			                $lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['SWEING_OUTPUT']+=$val['PRODUCTION_QNTY'];
			            }

			            if($val['PRODUCTION_TYPE'] ==8)//Finish Qty
			            {		
			            	$lay_qty_data_arr[$val['SERVING_COMPANY']][$val['FLOOR_ID']]['PACKING_QTY']+=$val['PRODUCTION_QNTY'];
			            }
					}
					
					$i = 1;
					foreach($lay_qty_data_arr as $working_company=>$working_company_val)
					{
						$sub_total_lay_qty=0;
						foreach ($working_company_val as $floor => $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $i; ?>" valign="top">
								<td width="50" align="center"><?=$i; ?></td>
						        <td width="100" align="center"><?=$company_arr[$working_company]; ?></td>
						        <td width="100" align="center"><?=$floor_details[$floor];?></td>
						        <td width="100" align="right"><?=number_format($row['LAY_QTY'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['CUTTING_QC'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['PRINT_SEND'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['PRINT_RECEIVE'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['EMB_SEND'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['EMB_RCV'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['SWEING_INPUT'], 2,'.',',');?></td>
						        <td width="100" align="right"><?=number_format($row['SWEING_OUTPUT'], 2,'.',',');?></td>
						        <td width="" align="right"><?=number_format($row['PACKING_QTY'], 2,'.',',');?></td>
							</tr>
							<?
							$i++;
							$sub_total_lay_qty+=$row['LAY_QTY'];
							$sub_total_cutting_qc+=$row['CUTTING_QC'];
							$sub_total_print_send+=$row['PRINT_SEND'];
							$sub_total_print_receive+=$row['PRINT_RECEIVE'];
							$sub_total_emb_send+=$row['EMB_SEND'];
							$sub_total_emb_rcv+=$row['EMB_RCV'];
							$sub_total_sweing_input+=$row['SWEING_INPUT'];
							$sub_total_sweing_output+=$row['SWEING_OUTPUT'];
							$sub_total_packing_qty+=$row['PACKING_QTY'];

							$grand_total_lay_qty+=$row['LAY_QTY'];
							$grand_total_cutting_qc+=$row['CUTTING_QC'];
							$grand_total_print_send+=$row['PRINT_SEND'];
							$grand_total_print_receive+=$row['PRINT_RECEIVE'];
							$grand_total_emb_send+=$row['EMB_SEND'];
							$grand_total_emb_rcv+=$row['EMB_RCV'];
							$grand_total_sweing_input+=$row['SWEING_INPUT'];
							$grand_total_sweing_output+=$row['SWEING_OUTPUT'];
							$grand_total_packing_qty+=$row['PACKING_QTY'];
						}
						?>
						<tr class="tbl_bottom">
				            <td width="50">&nbsp;</td>
		            		<td width="100">&nbsp;</td>
		                    <td width="100">Total</td>
		                    <td width="100" align="right"><?=number_format($sub_total_lay_qty, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_cutting_qc, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_print_send, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_print_receive, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_emb_send, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_emb_rcv, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_sweing_input, 2,'.',',');?></td>
		                    <td width="100" align="right"><?=number_format($sub_total_sweing_output, 2,'.',',');?></td>
		                    <td width="" align="right"><?=number_format($sub_total_packing_qty, 2,'.',',');?></td>
						</tr>
						<?					
					}					
					?>
				</tbody>
			</table>
		</div>
		<table width="1130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">&nbsp;</th>
                    <th width="100">Grand Total</th>
                    <th width="100" align="right"><?=number_format($grand_total_lay_qty, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_cutting_qc, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_print_send, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_print_receive, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_emb_send, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_emb_rcv, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_sweing_input, 2,'.',',');?></th>
                    <th width="100" align="right"><?=number_format($grand_total_sweing_output, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_total_packing_qty, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Sewing Input and Output (Working Company) End-->

		<!-- Shipment Status (LC Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="540" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="6" height="40" align="center"><strong>Shipment Status (LC Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Working Company</th>
                    <th width="100">Floor</th>
                    <th width="100" align="center">Buyer Name</th>
                    <th width="100" align="center">Shipment Qty Pcs</th>
                    <th width="" align="center">FOB Value USD</th>
                </tr>
			</thead>
		</table>
		<div style="width:560px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body4">
            <table width="540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					if($cbo_working_company>0) $company_working_cond=" and a.DELIVERY_COMPANY_ID=$cbo_working_company";

					$from_date = $txt_date_from;
					if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
					else $to_date = $txt_date_to;

					$date_con="";
					if ($from_date != "" && $to_date != "") 
					{	
						$date_con = "and a.DELIVERY_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

						$sql_delivery="SELECT a.COMPANY_ID, a.DELIVERY_COMPANY_ID, a.DELIVERY_FLOOR_ID, a.DELIVERY_DATE, b.EX_FACTORY_QNTY, c.UNIT_PRICE, d.BUYER_NAME
						FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d
						WHERE a.id = b.delivery_mst_id AND b.po_break_down_id=c.id and c.job_id=d.id and a.COMPANY_ID in($cbo_company) $date_con $company_working_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.is_deleted = 0 AND d.status_active = 1";
				        // echo $sql_delivery;die;
					}

			        $delivery_result=sql_select($sql_delivery);
			        foreach ($delivery_result  as  $row) 
			        {
			            $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['DELIVERY_FLOOR_ID']][$row['BUYER_NAME']]['SHIPMENT_QTY']+=$row['EX_FACTORY_QNTY'];

			            $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['DELIVERY_FLOOR_ID']][$row['BUYER_NAME']]['FOB_VALUE'] += ($row['EX_FACTORY_QNTY'] * $row['UNIT_PRICE']);
			        }
			        //  echo"<pre>";print_r($ex_data_array);die;

					$i = 1;
					foreach($ex_data_array as $delivery_company=>$delivery_company_val)
					{
						$sub_tot_shipment_qty=0; $sub_tot_fob_value=0;
						foreach ($delivery_company_val as $floork => $floorv) 
						{
							foreach ($floorv as $buyerId => $rows) 
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr4_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr4_<? echo $i; ?>" valign="top">
									<td width="50" align="center"><?=$i; ?></td>
							        <td width="100" align="center"><? echo  $company_arr[$delivery_company]; ?></td>
							        <td width="100" align="center"><? echo $floor_details[$floork]; ?></td>
							        <td width="100" align="center"><? echo $buyer_arr[$buyerId]; ?></td>
							        <td width="100" align="right"><?=number_format($rows['SHIPMENT_QTY'], 2,'.',',');?></td>
							        <td width="" align="right"><?=number_format($rows['FOB_VALUE'], 2,'.',',');?></td>
								</tr>
								<?
								$i++;
								$sub_tot_shipment_qty+=$rows['SHIPMENT_QTY'];
								$sub_tot_fob_value+=$rows['FOB_VALUE'];

								$grand_tot_shipment_qty+=$rows['SHIPMENT_QTY'];
								$grand_tot_fob_value+=$rows['FOB_VALUE'];
							}					
						}
						?>
						<tr class="tbl_bottom">
				            <td width="50">&nbsp;</td>
		            		<td width="100">&nbsp;</td>
		                    <td width="100">&nbsp;</td>
		                    <td width="100" align="right">Total</td>
		                    <td width="100" align="right"><?=number_format($sub_tot_shipment_qty, 2,'.',',');?></td>
		                    <td width="" align="right"><?=number_format($sub_tot_fob_value, 2,'.',',');?></td>
						</tr>
						<?					
					}					
					?>
				</tbody>
			</table>
		</div>
		<table width="540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">&nbsp;</th>
                    <th width="100"></th>
                    <th width="100" align="right">Grand Total</th>
                    <th width="100" align="right"><?=number_format($grand_tot_shipment_qty, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_tot_fob_value, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Shipment Status (LC Company) End-->

		<!-- Yarn Received (LC Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="430" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="4" height="40" align="center"><strong>Yarn Received (LC Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Company</th>
                    <th width="150">Received Qty Kg</th>
                    <th width="" align="center">Total Value USD</th>
                </tr>
			</thead>
		</table>
		<div style="width:450px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body5">
            <table width="430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$date_con = "and a.RECEIVE_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

					$yarn_recv_sql="SELECT a.COMPANY_ID, a.RECEIVE_DATE, b.ORDER_QNTY, b.ORDER_RATE
					from INV_RECEIVE_MASTER a, inv_transaction b
					where a.id=b.mst_id and a.ENTRY_FORM=1 and TRANSACTION_TYPE=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.COMPANY_ID in($cbo_company) $date_con";
					// echo $yarn_recv_sql;
					$yarn_recv_sql_rst=sql_select($yarn_recv_sql);
					$yRecv_data_arr=array();
			        foreach ($yarn_recv_sql_rst  as  $row) 
			        {
			            $yRecv_data_arr[$row['COMPANY_ID']]['ORDER_QNTY']+=$row['ORDER_QNTY'];

			            $yRecv_data_arr[$row['COMPANY_ID']]['USD_VALUE'] += ($row['ORDER_QNTY'] * $row['ORDER_RATE']);
			        }
			        //  echo"<pre>";print_r($yRecv_data_arr);die;

					$i = 1;$k=1;
					$sub_tot_order_qnty=0; $sub_tot_usd_value=0;$grand_tot_order_qnty=0; $grand_tot_usd_value=0;
					foreach($yRecv_data_arr as $company=>$row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						if (!in_array($company,$group_by_arr) )
                        {
                            if($k!=1)
                            {
                            	?>  
                                <tr class="tbl_bottom">
                                    <td width="50">&nbsp;</td>
			                        <td width="100" align="right">Total</td>
			                        <td width="150" align="right"><?=number_format($sub_tot_order_qnty, 2,'.',',');?></td>
			                        <td align="right"><?=number_format($sub_tot_usd_value, 2,'.',',');?></td>
                                </tr>                               
                                <?
                                unset($sub_tot_order_qnty);unset($sub_tot_usd_value);
                            }
                            $group_by_arr[]=$company; 
                            $k++; 
                    	}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr5_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr5_<? echo $i; ?>" valign="top">
							<td width="50" align="center"><?=$i; ?></td>
					        <td width="100" align="center"><?=$company_arr[$company];?></td>
					        <td width="150" align="right"><?=number_format($row['ORDER_QNTY'], 2,'.',',');?></td>
					        <td width="" align="right"><?=number_format($row['USD_VALUE'], 2,'.',',');?></td>
						</tr>
						<?
						$i++;
						$sub_tot_order_qnty+=$row['ORDER_QNTY'];
						$sub_tot_usd_value+=$row['USD_VALUE'];

						$grand_tot_order_qnty+=$row['ORDER_QNTY'];
						$grand_tot_usd_value+=$row['USD_VALUE'];
					}
					?> 
                    <tr class="tbl_bottom">
                        <td width="50">&nbsp;</td>
                        <td width="100" align="right">Total</td>
                        <td width="150" align="right"><?=number_format($sub_tot_order_qnty, 2,'.',',');?></td>
                        <td align="right"><?=number_format($sub_tot_usd_value, 2,'.',',');?></td>
                    </tr>
				</tbody>
			</table>
		</div>
		<table width="430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100" align="right">Grand Total</th>
                    <th width="150" align="right"><?=number_format($grand_tot_order_qnty, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_tot_usd_value, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Yarn Received (LC Company) End-->

		<!-- Yarn Issued Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="4" height="40" align="center"><strong>Yarn Issued (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Company</th>
                    <th width="100">Issued Qty Kg</th>
                    <th width="" align="center">Total Value USD</th>
                </tr>
			</thead>
		</table>
		<div style="width:350px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body6">
            <table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$date_con="";
					$date_con = "and a.ISSUE_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
					$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

					/*$yarn_issue_sql="SELECT a.ISSUE_NUMBER, a.COMPANY_ID, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.CONS_QUANTITY, d.ORDER_RATE 
					from inv_issue_master a, inv_transaction b, INV_MRR_WISE_ISSUE_DETAILS c, INV_TRANSACTION d
					where a.id=b.mst_id and b.id=c.ISSUE_TRANS_ID and c.RECV_TRANS_ID=d.id
					and a.ENTRY_FORM=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.COMPANY_ID in($cbo_company) $date_con";*/

					$yarn_issue_sql="SELECT a.ISSUE_NUMBER, a.COMPANY_ID, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.CONS_QUANTITY,
					(case when d.TRANSACTION_TYPE=1 then d.ORDER_RATE else 0 end) as ORDER_RATE
					, b.id as TRANS_ID
					from inv_issue_master a, inv_transaction b, INV_MRR_WISE_ISSUE_DETAILS c, INV_TRANSACTION d 
					where a.id=b.mst_id and b.id=c.ISSUE_TRANS_ID and c.RECV_TRANS_ID=d.id and a.ENTRY_FORM=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 
					and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 
					and a.COMPANY_ID in($cbo_company) $date_con
					group by a.ISSUE_NUMBER, a.COMPANY_ID, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.CONS_QUANTITY, b.id, d.ORDER_RATE, d.TRANSACTION_TYPE";
					// and a.ISSUE_NUMBER='AAL-YIS-23-07688' and b.prod_id=93198
					// echo $yarn_issue_sql;
					$yarn_issue_sql_rst=sql_select($yarn_issue_sql);
					$yRecv_data_arr=array();
			        foreach ($yarn_issue_sql_rst  as  $row) 
			        {
			        	if ($tranCheck[$row['TRANS_ID']]=="") 
			        	{
			        		$tranCheck[$row['TRANS_ID']]=$row['TRANS_ID'];

			        		if ($row['KNIT_DYE_SOURCE']==1) 
			        		{
			        			$knit_dye_company=$company_arr[$row['KNIT_DYE_COMPANY']];
			        		}
			        		else
			        		{
			        			$knit_dye_company=$supplier_name_arr[$row['KNIT_DYE_COMPANY']];
			        		}

			        		$yIssue_data_arr[$knit_dye_company]['CONS_QUANTITY']+=$row['CONS_QUANTITY'];
			            	$yIssue_data_arr[$knit_dye_company]['USD_VALUE'] += ($row['CONS_QUANTITY'] * $row['ORDER_RATE']);
			            	// or rate calculation follow > Show button > inventory\reports\yarn\requires\daily_yarn_issue_report_controller.php > line no > 956 > $exchangeRate=
			        	}
			        }
			        //  echo"<pre>";print_r($yIssue_data_arr);die;
					$i = 1;$k=1;
					$sub_tot_qty=0; $sub_tot_usd_value=0;$grand_tot_qty=0; $grand_tot_usd_value=0;
					foreach($yIssue_data_arr as $dye_company=>$row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if (!in_array($dye_company,$group_dye_company_arr) )
                        {
                            if($k!=1)
                            {
                            	?>  
                                <tr class="tbl_bottom">
                                    <td width="50">&nbsp;</td>
			                        <td width="100" align="right">Total</td>
			                        <td width="100" align="right"><?=number_format($sub_tot_qty, 2,'.',',');?></td>
			                        <td align="right"><?=number_format($sub_tot_usd_value, 2,'.',',');?></td>
                                </tr>                               
                                <?
                                unset($sub_tot_qty);unset($sub_tot_usd_value);
                            }
                            $group_dye_company_arr[]=$dye_company; 
                            $k++; 
                    	}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr6_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr6_<? echo $i; ?>" valign="top">
							<td width="50" align="center"><?=$i; ?></td>
					        <td width="100" align="center" title="<?=$dye_company;?>"><?=$dye_company;?></td>
					        <td width="100" align="right"><?=number_format($row['CONS_QUANTITY'], 2,'.',',');?></td>
					        <td width="" align="right"><?=number_format($row['USD_VALUE'], 2,'.',',');?></td>
						</tr>
						<?
						$i++;
						$sub_tot_qty+=$row['CONS_QUANTITY'];
						$sub_tot_usd_value+=$row['USD_VALUE'];

						$grand_tot_qty+=$row['CONS_QUANTITY'];
						$grand_tot_usd_value+=$row['USD_VALUE'];
					}
					?>
                    <tr class="tbl_bottom">
                        <td width="50">&nbsp;</td>
                        <td width="100" align="right">Total</td>
                        <td width="100" align="right"><?=number_format($sub_tot_qty, 2,'.',',');?></td>
                        <td align="right"><?=number_format($sub_tot_usd_value, 2,'.',',');?></td>
                    </tr>
				</tbody>
			</table>
		</div>
		<table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100" align="right">Grand Total</th>
                    <th width="100" align="right"><?=number_format($grand_tot_qty, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_tot_usd_value, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Yarn Issued End-->

		<!-- Dyes And Chemial Stock (Working Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="4" height="40" align="center"><strong>Dyes And Chemial Stock (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Company</th>
                    <th width="100">Issued Qty Kg</th>
                    <th width="" align="center">Total Value USD</th>
                </tr>
			</thead>
		</table>
		<div style="width:350px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body7">
            <table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					if($cbo_working_company>0) $company_working_cond=" and a.KNIT_DYE_COMPANY=$cbo_working_company";

					$from_date = $txt_date_from;
					if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
					else $to_date = $txt_date_to;
					$date_con='';
					$date_con = "and a.ISSUE_DATE between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

					$chemial_issue_sql="SELECT A.LC_COMPANY, A.COMPANY_ID, A.KNIT_DYE_SOURCE, A.KNIT_DYE_COMPANY, A.ISSUE_NUMBER, b.TRANSACTION_DATE, B.CONS_UOM, B.CONS_QUANTITY, B.CONS_RATE, B.CONS_AMOUNT
					from inv_issue_master a, inv_transaction b
					where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and A.LC_COMPANY in($cbo_company) $company_working_cond $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
					// echo $chemial_issue_sql;
					$chemial_issue_sql_result= sql_select($chemial_issue_sql);
					foreach ($chemial_issue_sql_result as $key => $row) 
					{
						$exchange_rate=$conversion_data_arr[$row["LC_COMPANY"]][change_date_format($row["TRANSACTION_DATE"])];
						// echo $exchange_rate.'=<br>';
						$usd_rate=$row['CONS_QUANTITY']/$exchange_rate;
						$usd_value=$usd_rate*$row['CONS_QUANTITY'];
						$chemial_issue_arr[$row['KNIT_DYE_COMPANY']]['QUANTITY']+=$row['CONS_QUANTITY'];
						$chemial_issue_arr[$row['KNIT_DYE_COMPANY']]['AMOUNT']+=$usd_value;
					}
					$i = 1;$j=1;
					foreach($chemial_issue_arr as $dye_company=>$row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if (!in_array($dye_company,$dye_company_arr) )
                        {
                            if($j!=1)
                            {
                            	?>  
                                <tr class="tbl_bottom">
                                    <td width="50">&nbsp;</td>
			                        <td width="100" align="right">Total</td>
			                        <td width="100" align="right"><?=number_format($sub_tot_issqty, 2,'.',',');?></td>
			                        <td align="right"><?=number_format($sub_tot_usd_issvalue, 2,'.',',');?></td>
                                </tr>                               
                                <?
                                unset($sub_tot_issqty);unset($sub_tot_usd_issvalue);
                            }
                            $dye_company_arr[]=$dye_company; 
                            $j++; 
                    	}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr7_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr7_<? echo $i; ?>" valign="top">
							<td width="50" align="center"><?=$i; ?></td>
					        <td width="100" align="center"><?=$company_arr[$dye_company];?></td>
					        <td width="100" align="right"><?=number_format($row['QUANTITY'], 2,'.',',');?></td>
					        <td width="" align="right"><?=number_format($row['AMOUNT'], 2,'.',',');?></td>
						</tr>
						<?
						$i++;
						$sub_tot_issqty+=$row['QUANTITY'];
						$sub_tot_usd_issvalue+=$row['AMOUNT'];

						$grand_tot_issqty+=$row['QUANTITY'];
						$grand_tot_usd_issvalue+=$row['AMOUNT'];
					}
					?>
					<tr class="tbl_bottom">
                        <td width="50">&nbsp;</td>
                        <td width="100" align="right">Total</td>
                        <td width="100" align="right"><?=number_format($sub_tot_issqty, 2,'.',',');?></td>
                        <td align="right"><?=number_format($sub_tot_usd_issvalue, 2,'.',',');?></td>
                    </tr>
				</tbody>
			</table>
		</div>
		<table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">Grand Total</th>
                    <th width="100"><?=number_format($grand_tot_issqty, 2,'.',',');?></th>
                    <th width="" align="right"><?=number_format($grand_tot_usd_issvalue, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Dyes And Chemial Stock (Working Company) End-->

		<!-- Grey Fabric Store (Working Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="4" height="40" align="center"><strong>Grey Fabric Store (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Company</th>
                    <th width="100">Buyer</th>
                    <th width="" align="center">Stock Qty</th>
                </tr>
			</thead>
		</table>
		<div style="width:350px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body8">
            <table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$con = connect();
					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID=$user_id and entry_form=136");
					execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=136");
					oci_commit($con);

					$from_date=$txt_date_from;
					if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

					$dateCondition = "and f.transaction_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

					$yearCondition = '';
					if ($cbo_year>0) 
					{
						if($db_type==0)
						{
							$yearCondition = " AND YEAR(d.insert_date) = ".$cbo_year."";
						}
						else if($db_type==2)
						{
							$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$cbo_year."";
						}
					}
					
					//jobNoCondition
					$jobNoCondition = '';
					if($sales_order_no != '')
					{
						$jobNoCondition = " AND d.job_no like '%".$sales_order_no."%'";
					}
					
					//bookingNoCondition
					$bookingNoCondition = '';
					if($txt_booking_no != '')
					{
						$bookingNoCondition = " AND d.sales_booking_no in('$txt_booking_no')";
					}
					// echo $bookingNoCondition;die;

					/*
					|--------------------------------------------------------------------------
					| for roll recv qty
					|--------------------------------------------------------------------------
					|
					*/
					$sqlRcvRollQty = "SELECT d.id, d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.company_id, e.po_breakdown_id, e.entry_form, h.qnty AS rcv_qty, h.barcode_no 
					FROM fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, pro_grey_prod_entry_dtls g, pro_roll_details h 
					WHERE d.id = e.po_breakdown_id and e.trans_id = f.id and f.id = g.trans_id and g.id = h.dtls_id and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(2,22,58,84) AND e.trans_type IN(1,4) AND e.trans_id > 0 AND f.status_active = 1 AND f.is_deleted = 0 AND d.company_id IN($cbo_company) $yearCondition $jobNoCondition $bookingNoCondition $dateCondition AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1 "; 
					// echo $sqlRcvRollQty; die;
					$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
					$barcode_no_arr = array();
					foreach($sqlRcvRollRslt as $row)
					{
				        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					// unset($sqlRcvRollRslt);
					// echo "<pre>"; print_r($dataArr); die;

					/*
					|--------------------------------------------------------------------------
					| for issue qty and roll
					| order to order transfer
					|--------------------------------------------------------------------------
					|
					*/
					$sqlNoOfRoll="SELECT d.id, d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.company_id, e.po_breakdown_id, e.trans_type, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, h.barcode_no, i.transfer_criteria 
					FROM fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, inv_item_transfer_dtls g, pro_roll_details h, INV_ITEM_TRANSFER_MST i
					WHERE d.id = e.po_breakdown_id and e.trans_id = f.id and e.dtls_id = g.id and g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id and i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133 AND e.trans_type IN(5,6) AND f.status_active = 1 AND f.is_deleted = 0 AND g.status_active = 1 AND g.is_deleted = 0 AND h.status_active = 1 AND h.is_deleted = 0 and h.is_sales=1 AND d.company_id IN($cbo_company) $yearCondition $jobNoCondition $bookingNoCondition $dateCondition"; 
					// echo "<br>".$sqlNoOfRoll; die;
					$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
					foreach($sqlNoOfRollResult as $row)
					{
				        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					// unset($sqlNoOfRollResult);
					//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
					// echo "<pre>"; print_r($barcode_no_arr);

					/*
					|--------------------------------------------------------------------------
					| for production
					|--------------------------------------------------------------------------
					|
					*/
					if(!empty($barcode_no_arr))
					{
						foreach($barcode_no_arr as $barcode_no)
						{
							if( $barcode_no_check[$barcode_no] =="" )
					        {
					            $barcode_no_check[$barcode_no]=$barcode_no;
					            $barcodeno = $barcode_no;
					            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
					            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,136)");
					        }			
						}
						oci_commit($con);
						$production_sql = sql_select("SELECT b.barcode_no, c.knitting_source, c.knitting_company
						from pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
						where b.mst_id=c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58) and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= 165 and d.entry_form=136");
						$prodBarcodeData=array();
						foreach ($production_sql as $row)
				        {
				            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
				            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
				        }
			    	}

			    	$dataArr = array();
					$poArr = array();
					foreach($sqlRcvRollRslt as $row)
					{
						$compId = $row[csf('company_id')];
						$productId = $row[csf('prod_id')];
						$orderId = $row[csf('po_breakdown_id')];
						$cust_buyer=$row[csf('customer_buyer')];
						$poArr[$orderId] = $orderId;
						$knitting_company=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"];

						if($row[csf('entry_form')]  == 84)
						{
							$dataArr[$knitting_company][$cust_buyer]['issueReturnQty'] += $row[csf('rcv_qty')];
							//$issRtnArr[$knitting_company][$cust_buyer]['orderId'] .= $orderId.',';
						}
						else
						{
							$dataArr[$knitting_company][$cust_buyer]['rcvQty'] += $row[csf('rcv_qty')];
						}
					}
					unset($sqlRcvRollRslt);
					// echo "<pre>";print_r($issRtnArr);die;

					foreach($sqlNoOfRollResult as $row)
					{
						$compId = $row[csf('company_id')];
						$orderId = $row[csf('po_breakdown_id')];
						$cust_buyer=$row[csf('customer_buyer')];
						$knitting_company=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"];
						$str_ref=$productId;
						if($row[csf('trans_type')] == 5)
						{
							$poArr[$orderId] = $orderId;

							if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
							{
								if($row[csf('roll_rcv_qty')])
								{
									$dataArr[$knitting_company][$cust_buyer]['transferInQty'] += $row[csf('roll_rcv_qty')];
								}
								else
								{
									$dataArr[$knitting_company][$cust_buyer]['transferInQty'] += $row[csf('rcv_qty')];
								}
								//$transInArr[$knitting_company][$cust_buyer]['orderId'] .= $orderId.',';
							}
							
						}
						if($row[csf('trans_type')] == 6)
						{
							$transOutArr[$knitting_company][$cust_buyer]['rollIssueQty'] += count($row[csf('issue_roll')]);

							if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
							{
								if($row[csf('roll_rcv_qty')])
								{
									$transOutArr[$knitting_company][$cust_buyer]['transferOutQty'] += $row[csf('roll_rcv_qty')];
								}
								else
								{
									$transOutArr[$knitting_company][$cust_buyer]['transferOutQty'] += $row[csf('rcv_qty')];
								}
							}
						}
					}
					unset($sqlNoOfRollResult);
					// echo "<pre>";print_r($transInArr);die;


					/*
					|--------------------------------------------------------------------------
					| for issue qty and roll
					|--------------------------------------------------------------------------
					|
					*/
					// echo "<pre>";print_r($poArr);die;
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 136, 1,$poArr, $empty_arr);
					if(!empty($poArr))
					{
						//===== For Roll Splitting After Issue start ============
					    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
					    from pro_roll_split C, pro_roll_details D, GBL_TEMP_ENGINE E 
					    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and d.po_breakdown_id=e.ref_val and e.user_id=$user_id and e.entry_form=136 and e.ref_from=1");

					    if(!empty($split_chk_sql))
					    {
					        foreach ($split_chk_sql as $val)
					        {
					            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
					            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
					            {
					                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
					                $split_barcode=$val['BARCODE_NO'];
					                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",136)");
					            }
					        }
					        oci_commit($con);

					        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
					            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
					            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=136 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
					        if(!empty($split_ref_sql))
					        {
					            foreach ($split_ref_sql as $value)
					            {
					                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
					            }
					        }
					    }
					    unset($split_chk_sql);
					    unset($split_ref_sql);
					    // ======== For Roll Splitting After Issue end =========
						
						$sqlNoOfRollIssue="SELECT d.company_id, d.customer_buyer, e.po_breakdown_id, SUM(g.qnty) AS issue_qty, g.barcode_no 
						FROM GBL_TEMP_ENGINE a, fabric_sales_order_mst d, order_wise_pro_details e, pro_roll_details g
						WHERE a.ref_val=d.id and a.user_id=$user_id and a.entry_form=136 and a.ref_from=1 and d.id = e.po_breakdown_id and e.dtls_id = g.dtls_id and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(61) AND e.trans_type = 2 AND g.status_active = 1 AND g.is_deleted = 0 AND g.entry_form IN(61) and g.is_sales=1 AND d.company_id IN($cbo_company)
						GROUP BY d.company_id, d.customer_buyer, e.po_breakdown_id, g.barcode_no ";
						// echo $sqlNoOfRollIssue; die;
						$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
						$noOfRollIssueArr = array();
						foreach($sqlNoOfRollIssueResult as $row)
						{
							$compId = $row[csf('company_id')];
							$orderId = $row[csf('po_breakdown_id')];
							$cust_buyer=$row[csf('customer_buyer')];
							$knitting_company=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"];
							
					        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
					        if($mother_barcode_no != "")
					        {
					        	// echo $mother_barcode_no.'=<br>';
					            $knitting_company=$prodBarcodeData[$mother_barcode_no]["knitting_company"];
					        }
					        $issueQtyArr[$knitting_company][$cust_buyer]['issueQty'] += $row[csf('issue_qty')];
					        //$issueQtyArr[$knitting_company][$cust_buyer]['barcode_no'] .= $row[csf("barcode_no")].',';
						}
						unset($sqlNoOfRollIssueResult);
					}
					// echo "<pre>"; print_r($issueQtyArr);die;

					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID=$user_id and entry_form=136");
					execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=136");
					oci_commit($con);

					$i = 1;
					foreach($dataArr as $KnitCompId=>$KnitComp)
					{
						$sub_tot_stockQty=0;
						foreach ($KnitComp as $buyer => $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							//total receive calculation
							$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
							
							//total issue calculation
							$row['issueQty'] = $issueQtyArr[$KnitCompId][$buyer]['issueQty'];
							$row['rcvReturnQty'] = 0;
							$row['transferOutQty'] = $transOutArr[$KnitCompId][$buyer]['transferOutQty'];
							$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
							
							//stock qty calculation
							$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];
							$title='recv='.$row['rcvQty'].'+issRtn='.$row['issueReturnQty'].'+transIn='.$row['transferInQty'].'-iss='.$row['issueQty'].'+transOut='.$row['transferOutQty'];
							if($row['stockQty'] > 0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr8_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr8_<? echo $i; ?>" valign="top">
									<td width="50" align="center"><?=$i; ?></td>
							        <td width="100" align="center"><?=$company_arr[$KnitCompId]; ?></td>
							        <td width="100" align="center" title="<?=$buyer;?>"><?=$buyer_arr[$buyer];?></td>
							        <td width="" align="right" title="<?=$title;?>"><?=number_format($row['stockQty'], 2,'.',','); ?></td>
								</tr>
								<?
								$i++;
								$sub_tot_stockQty+=$row['stockQty'];
								$grand_tot_stockQty+=$row['stockQty'];
							}
						}
						?>
						<tr class="tbl_bottom">
				            <td width="50">&nbsp;</td>
		                    <td width="100"></td>
		                    <td width="100" align="right">Total</td>
		                    <td width="" align="right"><?=number_format($sub_tot_stockQty, 2,'.',',');?></td>
						</tr>
						<?	
					}					
					?>
				</tbody>
			</table>
		</div>
		<table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">&nbsp;</th>
                    <th width="100">Grand Total</th>
                    <th width="" align="right"><?=number_format($grand_tot_stockQty, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Grey Fabric Store (Working Company) End-->

		<!-- Finish Fabric Store (Working Company) Start-->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
			<thead>
				<tr>
             		<th colspan="4" height="40" align="center"><strong>Finish Fabric Store (Working Company)</strong></th>
             	</tr>
                <tr bgcolor="#EEE">
                	<th width="50" align="center">SL</th>
                    <th width="100">Company</th>
                    <th width="100">Buyer</th>
                    <th width="" align="center">Stock Qty</th>
                </tr>
			</thead>
		</table>
		<div style="width:350px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body9">
            <table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					//$fin_rcv_trans_iss_sql = sql_select("select entry_form,trans_type, color_id, po_breakdown_id, quantity from order_wise_pro_details where entry_form in (225,287,224,230,233) and is_sales =1 and is_deleted =0 and status_active =1 $fin_rcv_trans_iss_fso_Cond ");

					$date_con = "and a.receive_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
					$rcv_rtn_date_con = "and a.issue_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
					$date_con2 = "and b.transaction_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";

					// Finish Fabric Recv Sql
					$fin_rcv_sql = "SELECT a.COMPANY_ID, a.KNITTING_COMPANY, b.PI_WO_BATCH_NO, b.CONS_QUANTITY, c.QUANTITY, d.CUSTOMER_BUYER, E.BATCH_NO
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d, PRO_BATCH_CREATE_MST e
					where a.id=b.mst_id and b.id=c.trans_id and c.PO_BREAKDOWN_ID=d.id and d.id=e.SALES_ORDER_ID and e.IS_SALES=1 and b.PI_WO_BATCH_NO=e.id and a.entry_form in (225) and c.entry_form in (225) and b.item_category=2 and b.TRANSACTION_TYPE=1 and c.is_sales =1 and a.company_id in($cbo_company) $date_con and a.is_deleted =0 and a.status_active =1 and b.is_deleted =0 and b.status_active =1 and c.is_sales =1 and c.is_deleted =0 and c.status_active =1 and d.is_deleted =0 and d.status_active =1";
					// echo $fin_rcv_sql;
					$fin_rcv_sql_result=sql_select($fin_rcv_sql);
					$finish_data_arr=array();
					foreach ($fin_rcv_sql_result as $key => $row) 
					{
						$finish_data_arr[$row['KNITTING_COMPANY']][$row['CUSTOMER_BUYER']]['RECEIVE']+=$row['QUANTITY'];

						$batch_id_arr[$row['PI_WO_BATCH_NO']]=$row['KNITTING_COMPANY'];
						$batch_batch_no_arr[$row['BATCH_NO']]=$row['KNITTING_COMPANY'];
					}

					// Finish Fabric Receive Return
					$finish_recv_rtn_sql="SELECT a.COMPANY_ID, a.KNIT_DYE_COMPANY, b.PI_WO_BATCH_NO, b.CONS_QUANTITY, c.QUANTITY, d.CUSTOMER_BUYER
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where a.id=b.mst_id and b.id=c.trans_id and c.PO_BREAKDOWN_ID=d.id and a.entry_form in (287) and c.entry_form in (287) and a.company_id in($cbo_company) $rcv_rtn_date_con and b.item_category=2 and b.TRANSACTION_TYPE=3 and c.TRANS_TYPE=3 and a.is_deleted =0 and a.status_active =1 and b.is_deleted =0 and b.status_active =1 and c.is_sales =1 and c.is_deleted =0 and c.status_active =1 and d.is_deleted =0 and d.status_active =1";
					// echo $finish_recv_rtn_sql;
					$finish_recv_rtn_result=sql_select($finish_recv_rtn_sql);
					$finish_recv_rtn_arr=array();
					foreach ($finish_recv_rtn_result as $key => $row)
					{
						$finish_recv_rtn_arr[$row['KNIT_DYE_COMPANY']][$row['CUSTOMER_BUYER']]['RECV_RTN']+=$row['QUANTITY'];
					}
					// echo "<pre>";print_r($finish_recv_rtn_arr);

					// Finish Fabric Issue
					$finish_iss_sql="SELECT a.COMPANY_ID, a.KNIT_DYE_COMPANY, b.PI_WO_BATCH_NO, b.CONS_QUANTITY, c.QUANTITY, d.CUSTOMER_BUYER
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and a.entry_form in (224) and c.entry_form in (224) and a.company_id in($cbo_company) $rcv_rtn_date_con and b.item_category=2 and b.transaction_type=2 and c.trans_type=2 and a.is_deleted =0 and a.status_active =1 and b.is_deleted =0 and b.status_active =1 and c.is_sales =1 and c.is_deleted =0 and c.status_active =1 and d.is_deleted =0 and d.status_active =1";
					// echo $finish_iss_sql;
					$finish_iss_result=sql_select($finish_iss_sql);
					$finish_iss_arr=array();
					foreach ($finish_iss_result as $key => $row) 
					{
						$knit_dye_company=$batch_id_arr[$row['PI_WO_BATCH_NO']];
						$finish_iss_arr[$knit_dye_company][$row['CUSTOMER_BUYER']]['ISSUE']+=$row['QUANTITY'];
					}
					// echo "<pre>";print_r($finish_iss_arr);

					// Finish Fabric Issue Return
					$finish_iss_rtn="SELECT a.COMPANY_ID, a.KNITTING_COMPANY, b.PI_WO_BATCH_NO, b.CONS_QUANTITY, c.QUANTITY, d.CUSTOMER_BUYER
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d 
					where a.id=b.mst_id and b.id=c.trans_id and c.PO_BREAKDOWN_ID=d.id and a.entry_form in (233) and c.entry_form in (233) and a.company_id in($cbo_company) $date_con and b.TRANSACTION_TYPE=4 and c.trans_type=4 and a.is_deleted =0 and a.status_active =1 and b.is_deleted =0 and b.status_active =1 and c.is_sales =1 and c.is_deleted =0 and c.status_active =1 and d.is_deleted =0 and d.status_active =1";
					// echo $finish_iss_rtn;
					$finish_iss_rtn_result=sql_select($finish_iss_rtn);
					foreach ($finish_iss_rtn_result as $key => $row) 
					{
						$knit_dye_company=$batch_id_arr[$row['PI_WO_BATCH_NO']];
						$finish_data_arr[$knit_dye_company][$row['CUSTOMER_BUYER']]['ISSUE_RTN']+=$row['QUANTITY'];
					}
					// echo "<pre>";print_r($finish_iss_rtn_arr);

					// Finish Fabric FSO to FSO Transfer
					$finish_transfer="SELECT a.batch_no, b.PI_WO_BATCH_NO, b.CONS_QUANTITY, c.QUANTITY, d.CUSTOMER_BUYER, c.TRANS_TYPE
					from PRO_BATCH_CREATE_MST a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d 
					where a.id=b.PI_WO_BATCH_NO and b.id=c.trans_id and c.PO_BREAKDOWN_ID=d.id and c.entry_form in (230) and b.TRANSACTION_TYPE in(5,6) and c.TRANS_TYPE in(5,6) and b.company_id in($cbo_company) $date_con2 and b.is_deleted =0 and b.status_active =1 and c.is_sales =1 and c.is_deleted =0 and c.status_active =1 and d.is_deleted =0 and d.status_active =1";
					// echo $finish_iss_rtn;
					$finish_transfer_result=sql_select($finish_transfer);
					$finish_transfer_out_arr=array();
					foreach ($finish_transfer_result as $key => $row) 
					{
						// $knit_dye_company=$batch_id_arr[$row['PI_WO_BATCH_NO']];
						$knit_dye_company=$batch_batch_no_arr[$row['BATCH_NO']];

						if ($row['TRANS_TYPE']==5) 
						{
							$finish_data_arr[$knit_dye_company][$row['CUSTOMER_BUYER']]['TRANS_IN']+=$row['QUANTITY'];
						}
						else
						{
							$finish_transfer_out_arr[$knit_dye_company][$row['CUSTOMER_BUYER']]['TRANS_OUT']+=$row['QUANTITY'];
						}
						
					}
					// echo "<pre>";print_r($finish_transfer_arr);

					$i = 1;$grand_tot_stockQty=0;
					foreach($finish_data_arr as $knitting_company=>$knitting_companyv)
					{
						$sub_tot_stockQty=0;
						foreach ($knitting_companyv as $cbuyer => $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							//total receive calculation
							$total_RcvQty = number_format($row['RECEIVE'],2,'.','')+number_format($row['ISSUE_RTN'],2,'.','')+number_format($row['TRANS_IN'],2,'.','');
							
							//total issue calculation
							$issue_qty = $finish_iss_arr[$knitting_company][$cbuyer]['ISSUE'];
							$recv_rtn_qty = $finish_recv_rtn_arr[$knitting_company][$cbuyer]['RECV_RTN'];
							$trans_out_qty = $finish_transfer_out_arr[$knitting_company][$cbuyer]['TRANS_OUT'];

							// echo $issue_qty.'='.$recv_rtn_qty.'='.$trans_out_qty;
							$total_issueQty = number_format($issue_qty,2,'.','')+number_format($recv_rtn_qty,2,'.','')+number_format($trans_out_qty,2,'.','');
							
							//stock qty calculation
							// echo $total_RcvQty .'-'. $total_issueQty;
							$stock_qty = $total_RcvQty - $total_issueQty;
							$title='recv='.$row['RECEIVE'].'+issRtn='.$row['ISSUE_RTN'].'+transIn='.$row['TRANS_IN'].'-iss='.$issue_qty.'+transOut='.$trans_out_qty.'+recv_rtn='.$recv_rtn_qty;

							if($stock_qty > 0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
									<td width="50" align="center"><?=$i; ?></td>
							        <td width="100" align="center"><?=$company_arr[$knitting_company]; ?></td>
								    <td width="100" align="center" title="<?=$cbuyer;?>"><?=$buyer_arr[$cbuyer];?></td>
							        <td width="" align="right" title="<?=$title;?>"><?=number_format($stock_qty, 2,'.',',');?></td>
								</tr>
								<?
								$i++;
								$sub_tot_stockQty+=$stock_qty;
								$grand_tot_stockQty+=$stock_qty;
							}
						}
						?>
						<tr class="tbl_bottom">
				            <td width="50">&nbsp;</td>
		                    <td width="100"></td>
		                    <td width="100" align="right">Total</td>
		                    <td width="" align="right"><?=number_format($sub_tot_stockQty, 2,'.',',');?></td>
						</tr>
						<?
					}
					
					?>
				</tbody>
			</table>
		</div>
		<table width="330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<tfoot>
				<tr class="tbl_bottom">
		            <th width="50">&nbsp;</th>
            		<th width="100">&nbsp;</th>
                    <th width="100">Grand Total</th>
                    <th width="" align="right"><?=number_format($grand_tot_stockQty, 2,'.',',');?></th>
				</tr>
			</tfoot>
		</table>
		<br/>
		<!-- Finish Fabric Store (Working Company) End-->
	</fieldset>
    <br> 
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$report_type";
	exit();      
}
?>