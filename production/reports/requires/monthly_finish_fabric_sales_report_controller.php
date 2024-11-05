<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_lib_arr=return_library_array( "SELECT id, company_short_name from lib_company where status_active=1 and is_deleted=0  ",'id','company_short_name');
$color_arr 		= return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

//====================Location ACTION========

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "load_drop_down( 'requires/monthly_finish_fabric_sales_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor_id", 100, "select id, floor_name from lib_prod_floor where location_id=$data[0] and company_id=$data[1] and production_process=4 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/monthly_finish_fabric_sales_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_machine', 'machine_td');","" );
	exit();
}

if ($action=="load_drop_down_machine")
{
	$data=explode("_",$data);
	echo create_drop_down( "txt_mc_no", 100, "select id, machine_no from lib_machine_name where floor_id=$data[0] and company_id=$data[1] and status_active=1 and is_deleted=0","id,machine_no", 1, "-- select machine --", 0, "","" );
	exit();
}


if ($action=="load_drop_down_po_company")
{
	if($data ==1){
		echo create_drop_down( "cbo_po_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/monthly_finish_fabric_sales_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	}else{
		echo create_drop_down( "cbo_po_company_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "" );
	}

}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_within_no")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==2)
	{
		echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$dataArr[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");

	}else {
		echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" );
	}
	exit();
}


if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							//,4=>"Batch No."
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_sales_order_no_search_list_view', 'search_div', 'monthly_finish_fabric_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_sales_order_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company where id=$company_id",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."%'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."%'";
		else  if($search_by==3)  $search_field_cond=" and a.style_ref_no like '%".$search_string."%'";
		else  if($search_by==4)  $search_field_cond=" and b.batch_no like '%".$search_string."%'";
	}

	if ($db_type == 0)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and YEAR(a.insert_date)=$cbo_year";
		}
	}
	else if ($db_type == 2)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	if($search_by==4)
	{
		$sql = "SELECT a.po_buyer, b.batch_no,b.extention_no, a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a,pro_batch_create_mst b  where a.sales_booking_no=b.booking_no and b.status_active=1 and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
		$style_width="110";

	}
	else
	{
		$sql = "SELECT a.po_buyer,   a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a   where     a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
		$style_width="";
	}

	

	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Sales/ Booking No</th>			 
			<th width="<? echo $style_width;?>">Style Ref.</th>
			<?
			if($search_by==4)
			{
				?>
				<th width="80">Batch No.</th>
				<th>Ext. No.</th>
				<?
			}
			?>

			
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$buyer_arr[$row[csf('po_buyer')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					 
					<td align="center" width="<? echo $style_width;?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<?
				if($search_by==4)
				{
					?>

					<td width="80"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td><p><? echo $row[csf('extention_no')]; ?></p></td>
					<?
				}
					?>
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

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$po_company_id=str_replace("'","",$cbo_po_company_id); 
	$within_group =str_replace("'","",$cbo_within_group);
	$buyer_name=str_replace("'","",$cbo_buyer_id);
	$year=str_replace("'","",$cbo_year);

	$dynamic_search=str_replace("'","",$txt_dynamic_search);
	$dynamic_id = str_replace("'","",$hide_dynamic_id); 
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rpt_type=str_replace("'","",$rpt_type);

	$year_selection = str_replace("'","",$cbo_year_selection);
	$txt_dynamic_search = str_replace("'","",$txt_dynamic_search);

	$condition_string="";
	$condition_string.=($within_group)?" and e.within_group=$within_group": "";
	$condition_string.=($po_company_id)?" and e.po_company_id=$po_company_id": "";
	//$condition_string.=($buyer_name)?" and e.po_buyer=$buyer_name": "";
	$condition_string.=($year)?" and to_char(e.insert_date,'YYYY')=$year": ""; 
	$condition_string.=($dynamic_id)?" and e.id=$dynamic_id": ""; 
	$condition_string.=($txt_dynamic_search)?" and e.job_no_prefix_num=$txt_dynamic_search": ""; 

	if($db_type==0)
	{
		if($txt_date_from!=="" and $txt_date_to!=="")
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			//$date_cond=" and a.transaction_date between '$date_from' and '$date_to'";
			$date_cond   = " and a.transaction_date <= '$date_to'";
		}else{
			if($year_selection>0)
			{
				$production_year_condition=" and YEAR(a.insert_date)=$year_selection";
			}
		}
	}
	else if($db_type==2)
	{
		if($txt_date_from!=="" and $txt_date_to!=="")
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			//$date_cond=" and a.transaction_date between '$date_from' and '$date_to'";
			$date_cond   = " and a.transaction_date <= '$date_to'";
		}else {
			if($year_selection>0)
			{
				$production_year_condition =" and to_char(a.insert_date,'YYYY')=$year_selection";
			}
		}
	}

	if($within_group==1)
	{
		if($buyer_name>0)
		{
			$condition_string .=" and e.po_buyer=$buyer_name" ;
		}
	}
	else if($within_group==2)
	{
		if($buyer_name>0)
		{
			$condition_string .=" and e.buyer_id=$buyer_name" ;
		}
	}
	else
	{
		if($buyer_name>0)
		{
			$condition_string .=" and (e.buyer_id=$buyer_name or e.po_buyer=$buyer_name)" ;
		}
	}

	$sql_dtls="SELECT b.id as dtls_id, b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, d.detarmination_id determination_id,d.gsm,d.dia_width dia,  d.color color_id, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, f.booking_type, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no, f.short_booking_type, 1 as type 
	from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, fabric_sales_order_mst e, wo_booking_mst f
	where a.company_id=$company_id and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id and c.po_breakdown_id=e.id and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 and e.within_group=1 and e.sales_booking_no= f.booking_no $condition_string $date_cond
	group by b.id, b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, d.detarmination_id,d.gsm, d.dia_width, d.color, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, f.booking_type, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no, f.short_booking_type
	union all
	select b.id as dtls_id, b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, d.detarmination_id determination_id,d.gsm,d.dia_width dia,  d.color color_id, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, f.booking_type, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no, 0 as short_booking_type, 2 as type  
	from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, fabric_sales_order_mst e, wo_non_ord_samp_booking_mst f
	where a.company_id=$company_id and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id and c.po_breakdown_id=e.id  and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 and e.within_group=1 and e.sales_booking_no= f.booking_no $condition_string $date_cond
	group by b.id, b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, d.detarmination_id,d.gsm, d.dia_width,d.color, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, f.booking_type, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no
	union all
	select b.id as dtls_id, b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, d.detarmination_id determination_id,d.gsm,d.dia_width dia, d.color color_id, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, 0 as booking_type, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no, 0 as short_booking_type, 3 as type 
	from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, fabric_sales_order_mst e
	where a.company_id=$company_id and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id and c.po_breakdown_id=e.id  and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 and e.within_group=2 $condition_string $date_cond
	group by b.id, b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, d.detarmination_id,d.gsm, d.dia_width,d.color, a.transaction_date, e.po_job_no, e.sales_booking_no, e.within_group, e.booking_entry_form, e.booking_without_order, e.company_id, e.buyer_id, e.po_buyer, e.style_ref_no, e.season_id, e.job_no
	order by within_group, type, sales_booking_no asc, booking_entry_form desc";

	$sql_dtls_result=sql_select($sql_dtls);

	if(!empty($sql_dtls_result))
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_recv_dtls where userid=$user_name");
		$r_id4=execute_query("delete from tmp_poid where userid=$user_name");

		if($r_id3 && $r_id4)
		{
		    oci_commit($con);
		}
	}
	else
	{
		echo "Data not found";
		die;
	}

	$booking_type_arr=array("118"=>"Main","108"=>"Partial","86"=>"Main pre","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	foreach($sql_dtls_result as $row)
	{
		if($row[csf('type')]==1)
		{
			$job_no_arr[$row[csf('po_job_no')]] = $row[csf('po_job_no')];
			$booking_arr[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
		}
		else if($row[csf('type')]==2)
		{
			$samp_non_booking_arr[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
		}

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
		}
		$salesTypeData[$row[csf("po_breakdown_id")]]['booking_type'] = $bookingType;

		if($row[csf('booking_entry_form')]==88)
		{
			$short_booking_arr["'".$row[csf('sales_booking_no')]."'"] = "'".$row[csf('sales_booking_no')]."'";
		}

		//$delivery_dtls_id_arr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
		//$po_breakdown_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		if(!$delivery_dtls_id_arr[$row[csf('dtls_id')]])
		{
		    $delivery_dtls_id_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		    $dtls_id = $row[csf("dtls_id")];
		    $rID=execute_query("insert into tmp_recv_dtls (userid, dtls_id) values ($user_name,$dtls_id)");
		}
		if(!$po_breakdown_id_arr[$row[csf('po_breakdown_id')]])
		{
		   $po_breakdown_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		    $tempPO = $row[csf("po_breakdown_id")];
		    $rID2=execute_query("insert into tmp_poid (userid, poid) values ($user_name,$tempPO)");
		}

		$fabric_description_id .= $row[csf('determination_id')].",";
	
		$fab_desc_str = $row[csf('bodypart_id')]."*".$row[csf('determination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia')]."*".$row[csf('width_type')]."*".$row[csf('color_id')];

		$date_from=date('Y-m-d',strtotime($date_from));
		$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
		
		if($transaction_date >= $date_from)
		{
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['company_id'] = $row[csf('company_id')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['within_group'] = $row[csf('within_group')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['buyer_id'] = $row[csf('buyer_id')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['po_buyer'] = $row[csf('po_buyer')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['style_ref_no'] = $row[csf('style_ref_no')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['season_id'] = $row[csf('season_id')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['po_job_no'] = $row[csf('po_job_no')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
			$data_array[$row[csf('type')]][$row[csf('booking_entry_form')]][$row[csf('sales_booking_no')]][$row[csf('job_no')]][$fab_desc_str]['short_booking_type'] = $row[csf('short_booking_type')];

			$data_array_uom[$row[csf('job_no')]][$fab_desc_str][$row[csf('uom')]]['current_qnty'] += $row[csf('delivery_qnty')];
			$data_array_uom[$row[csf('job_no')]][$fab_desc_str][$row[csf('uom')]]['current_amount'] += $row[csf('amount')];
			
		}
		else
		{
			$data_array_uom[$row[csf('job_no')]][$fab_desc_str][$row[csf('uom')]]['pre_qnty'] += $row[csf('delivery_qnty')];
			$data_array_uom[$row[csf('job_no')]][$fab_desc_str][$row[csf('uom')]]['pre_amount'] += $row[csf('amount')];
		}

		$data_array_fso_uom[$row[csf('job_no')]][$row[csf('uom')]] += $row[csf('delivery_qnty')];
	}

	if($rID && $rID2)
	{
	    oci_commit($con);
	}

	//echo "<pre>";
	//print_r( $data_array_uom);
	
	if(!empty($delivery_dtls_id_arr))
	{
		$delivery_dtls_id_arr = array_filter($delivery_dtls_id_arr);
    	$delivery_dtls_ids = implode(",", $delivery_dtls_id_arr);

	    $all_delivery_dtls_id_cond="";
		$dtlsCond="";
	    if($db_type==2 && count($delivery_dtls_id_arr)>999)
	    {
	    	$delivery_dtls_id_arr_chunk=array_chunk($delivery_dtls_id_arr,999) ;
	    	foreach($delivery_dtls_id_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$dtlsCond.=" delivery_dtls_id in($chunk_arr_value) or ";
	    	}
	    	$all_delivery_dtls_id_cond.=" and (".chop($dtlsCond,'or ').")";
	    }
	    else
	    {
	    	$all_delivery_dtls_id_cond=" and delivery_dtls_id in($delivery_dtls_ids)";
	    }

		//$bill_amount_sql =  sql_select("select order_id, amount, dtls_upcharge, dtls_discount from subcon_inbound_bill_dtls where is_sales=1 and status_active=1 $all_delivery_dtls_id_cond");
		$bill_amount_sql =  sql_select("select a.order_id, a.amount, a.dtls_upcharge, a.dtls_discount from subcon_inbound_bill_dtls a, tmp_recv_dtls b where a.delivery_dtls_id=b.dtls_id and b.userid=$user_name and a.is_sales=1 and a.status_active=1");

		foreach ($bill_amount_sql as $row) 
		{
			$bill_amount_arr[$row[csf('order_id')]]['amount'] += $row[csf('amount')];
			$bill_amount_arr[$row[csf('order_id')]]['dtls_upcharge'] += $row[csf('dtls_upcharge')];
			$bill_amount_arr[$row[csf('order_id')]]['dtls_discount'] += $row[csf('dtls_discount')];
		}
	}

	if(!empty($po_breakdown_id_arr))
	{
		$po_breakdown_id_arr = array_filter($po_breakdown_id_arr);
    	$po_breakdown_ids = implode(",", $po_breakdown_id_arr);

	    $all_fso_id_cond="";
		$fsoCond="";
	    if($db_type==2 && count($po_breakdown_id_arr)>999)
	    {
	    	$po_breakdown_id_arr_chunk=array_chunk($po_breakdown_id_arr,999) ;
	    	foreach($po_breakdown_id_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$fsoCond.=" mst_id in($chunk_arr_value) or ";
	    	}
	    	$all_fso_id_cond.=" and (".chop($fsoCond,'or ').")";
	    }
	    else
	    {
	    	$all_fso_id_cond=" and mst_id in($po_breakdown_ids)";
	    }

		//$fso_grey_sql =  sql_select("select mst_id, grey_qty, grey_qnty_by_uom, amount, cons_uom, body_part_id, determination_id, color_id, color_type_id from fabric_sales_order_dtls where status_active=1 $all_fso_id_cond");
		$fso_grey_sql =  sql_select("select a.mst_id, a.grey_qty, a.grey_qnty_by_uom, a.amount, a.cons_uom, a.body_part_id, a.determination_id, a.color_id, a.color_type_id from fabric_sales_order_dtls a, tmp_poid b where a.mst_id=b.poid and b.userid=$user_name and a.status_active=1");
		//echo "select mst_id, grey_qty from fabric_sales_order_dtls where status_active=1 $all_fso_id_cond";
		foreach ($fso_grey_sql as $row) 
		{
			$fso_grey_qnty_arr[$row[csf('mst_id')]]['grey_qty'] += $row[csf('grey_qty')];
			$fso_grey_qnty_arr[$row[csf('mst_id')]][$row[csf('cons_uom')]]['booking_qnty'] += $row[csf('grey_qnty_by_uom')];
			$fso_grey_qnty_arr[$row[csf('mst_id')]][$row[csf('cons_uom')]]['booking_amount'] += $row[csf('amount')];
			$fso_color_type_arr[$row[csf('mst_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][] = $row[csf('color_type_id')];
		}
	}


	if(!empty($short_booking_arr))
	{
		$short_booking_arr = array_filter($short_booking_arr);
    	$short_booking_nos = implode(",", $short_booking_arr);

	    $all_short_booking_no_cond="";
		$SbookCond="";
	    if($db_type==2 && count($short_booking_arr)>999)
	    {
	    	$short_booking_arr_chunk=array_chunk($short_booking_arr,999);
	    	foreach($short_booking_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$SbookCond.=" a.booking_no in($chunk_arr_value) or ";
	    	}
	    	$all_short_booking_no_cond.=" and (".chop($SbookCond,'or ').")";
	    }
	    else
	    {
	    	$all_short_booking_no_cond=" and a.booking_no in($short_booking_nos)";
	    }

		$short_book_sql =  sql_select("SELECT a.booking_no as short_booking, a.division_id, b.booking_no as main_booking from wo_booking_dtls a, wo_booking_dtls b where a.po_break_down_id=b.po_break_down_id and a.booking_type=1 and a.is_short=1 and b.booking_type=1 and b.is_short=2 and a.status_active=1 and b.status_active=1 $all_short_booking_no_cond group by a.booking_no, a.division_id, b.booking_no");

		foreach ($short_book_sql as $row) 
		{
			$short_booking_ref_arr[$row[csf('short_booking')]]['division'] .= $row[csf('division_id')].",";
			$short_booking_ref_arr[$row[csf('short_booking')]]['main_booking'] .= $row[csf('main_booking')].",";
		}	
	}

    $job_no_arr = array_filter($job_no_arr);
    if(!empty($job_no_arr))
    {
    	$job_nos = "'".implode("','", $job_no_arr)."'";
	    $job_no_arr = explode(",",$job_nos);

	    $all_job_no_cond="";
		$jobCond="";
	    if($db_type==2 && count($job_no_arr)>999)
	    {
	    	$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
	    	foreach($job_no_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$jobCond.=" job_no in($chunk_arr_value) or ";
	    	}

	    	$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
	    }
	    else
	    {
	    	$all_job_no_cond=" and job_no in($job_nos)";
	    }

	    $bom_dtls_arr=array();
		$bom_fab_dtls_sql="select id, body_part_id, width_dia_type, item_number_id, uom, construction, composition from wo_pre_cost_fabric_cost_dtls where 1=1 $all_job_no_cond";
		$bom_fab_dtls_res=sql_select($bom_fab_dtls_sql);
		
		foreach($bom_fab_dtls_res as $row)
		{
			$bom_dtls_arr[$row[csf('id')]]['uom']=$row[csf('uom')];
		}
		unset($bom_fab_dtls_res);
    }
    
	$booking_arr = array_filter($booking_arr);
    if(!empty($booking_arr))
    {
    	$booking_nos = "'".implode("','", $booking_arr)."'";
	    $booking_arr = explode(",",$booking_nos);
	    $all_booking_no_cond="";
		$bookCond="";
	    if($db_type==2 && count($booking_arr)>999)
	    {
	    	$booking_arr_chunk=array_chunk($booking_arr,999) ;
	    	foreach($booking_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$bookCond.=" booking_no in($chunk_arr_value) or ";
	    	}
	    	$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
	    }
	    else
	    {
	    	$all_booking_no_cond=" and booking_no in($booking_nos)";
	    }

	    $sql_booking_history_dtls="select booking_no, approved_no, pre_cost_fabric_cost_dtls_id, construction, copmposition, gsm_weight, dia_width, fabric_color_id, color_type, uom, fin_fab_qnty, grey_fab_qnty, rate, amount from wo_booking_dtls_hstry where status_active=1 and is_deleted=0 $all_booking_no_cond order by booking_no, approved_no, pre_cost_fabric_cost_dtls_id";
		$dtls_arr=array(); $approve_arr=array();
		$sql_booking_history_res=sql_select($sql_booking_history_dtls);
		foreach($sql_booking_history_res as $row)
		{
			$approve_arr[$row[csf('approved_no')]]=$row[csf('approved_no')];
			$uom=0;
			$uom=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['uom'];
	
			if($uom ==12)
			{
				if($row[csf('approved_no')]==1)
				{
					$initial_booking_qnty_kg[1][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_kg[1][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
					$approval_booking_no_kg[$row[csf('booking_no')]] = $row[csf('approved_no')];
				}
				else if($approval_booking_no_kg[$row[csf('booking_no')]] <= $row[csf('approved_no')])
				{
					$approval_booking_no_kg[$row[csf('booking_no')]] = $row[csf('approved_no')];
					$initial_booking_qnty_kg[$row[csf('approved_no')]][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_kg[$row[csf('approved_no')]][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];

				}
			}
			if($uom ==27)
			{
				if($row[csf('approved_no')]==1)
				{
					$initial_booking_qnty_yds[1][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_yds[1][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
					$approval_booking_no_yds[$row[csf('booking_no')]] = $row[csf('approved_no')];
				}
				else if($approval_booking_no_yds[$row[csf('booking_no')]] <= $row[csf('approved_no')])
				{
					$approval_booking_no_yds[$row[csf('booking_no')]] = $row[csf('approved_no')];
					$initial_booking_qnty_yds[$row[csf('approved_no')]][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_yds[$row[csf('approved_no')]][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
				}
			}
		}

		unset($sql_booking_history_res);
    }

    $samp_non_booking_arr = array_filter($samp_non_booking_arr);
    if(!empty($samp_non_booking_arr))
    {
    	$samp_booking_nos = "'".implode("','", $samp_non_booking_arr)."'";
	    $samp_non_booking_arr = explode(",",$samp_booking_nos);
	    $all_samp_booking_no_cond="";
		$sampbookCond="";
	    if($db_type==2 && count($samp_non_booking_arr)>999)
	    {
	    	$samp_non_booking_arr_chunk=array_chunk($samp_non_booking_arr,999) ;
	    	foreach($samp_non_booking_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$sampbookCond.=" a.booking_no in($chunk_arr_value) or ";
	    	}
	    	$all_samp_booking_no_cond.=" and (".chop($sampbookCond,'or ').")";
	    }
	    else
	    {
	    	$all_samp_booking_no_cond=" and a.booking_no in($samp_booking_nos)";
	    }

	    //$sql_booking_history_dtls="select booking_no, approved_no, pre_cost_fabric_cost_dtls_id, construction, copmposition, gsm_weight, dia_width, fabric_color_id, color_type, uom, fin_fab_qnty, grey_fab_qnty, rate, amount from wo_nonor_sambo_dtl_hstry where status_active=1 and is_deleted=0 $all_samp_booking_no_cond order by booking_no, approved_no, pre_cost_fabric_cost_dtls_id";

	    $sql_booking_history_dtls="select a.approved_no, a.booking_no, a.finish_fabric as fin_fab_qnty, a.amount, b.uom from wo_nonor_sambo_dtl_hstry a, wo_non_ord_samp_booking_dtls b where a.booking_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 $all_samp_booking_no_cond order by a.booking_no, a.approved_no";

		$dtls_arr=array(); $approve_arr=array();
		$sql_booking_history_res=sql_select($sql_booking_history_dtls);
		foreach($sql_booking_history_res as $row)
		{
			$approve_arr[$row[csf('approved_no')]]=$row[csf('approved_no')];
			$uom= $row[csf('uom')];
	
			if($uom ==12)
			{
				if($row[csf('approved_no')]==1)
				{
					$initial_booking_qnty_kg[1][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_kg[1][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
					$approval_booking_no_kg[$row[csf('booking_no')]] = $row[csf('approved_no')];
				}
				else if($approval_booking_no_kg[$row[csf('booking_no')]] <= $row[csf('approved_no')])
				{
					$approval_booking_no_kg[$row[csf('booking_no')]] = $row[csf('approved_no')];
					$initial_booking_qnty_kg[$row[csf('approved_no')]][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_kg[$row[csf('approved_no')]][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
				}
			}
			if($uom ==27)
			{
				if($row[csf('approved_no')]==1)
				{
					$initial_booking_qnty_yds[1][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_yds[1][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
					$approval_booking_no_yds[$row[csf('booking_no')]] = $row[csf('approved_no')];
				}
				else if($approval_booking_no_yds[$row[csf('booking_no')]] <= $row[csf('approved_no')])
				{
					$approval_booking_no_yds[$row[csf('booking_no')]] = $row[csf('approved_no')];
					$initial_booking_qnty_yds[$row[csf('approved_no')]][$row[csf('booking_no')]]['finqty'] +=$row[csf('fin_fab_qnty')];
					$initial_booking_qnty_yds[$row[csf('approved_no')]][$row[csf('booking_no')]]['amount'] +=$row[csf('amount')];
				}
			}
		}
		unset($sql_booking_history_res);
    }

    /*echo "<pre>";
    print_r($approval_booking_no_kg);
    die;*/

    $composition_arr = array();
	$constructtion_arr = array();
	$fabric_description_id = implode(",",array_filter(array_unique(explode(",",chop($fabric_description_id,",")))));
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_id)";
	$deter_array = sql_select($sql_deter);
	foreach ($deter_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	unset($deter_array);

	/*echo "<pre>";
	print_r($approval_booking_no_kg);
	die;*/
	$r_id3=execute_query("delete from tmp_recv_dtls where userid=$user_name");
	$r_id4=execute_query("delete from tmp_poid where userid=$user_name");
	
	if($r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break
		{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<div>
		<fieldset style="width:2880px;">
			<table width="2880px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:18px"><? echo $company_lib_arr[$company_id];?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:14px"><? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>
			</table> 
			 
			<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3450" align="left" >
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="70">Company</th>
						<th width="40">Within Group</th>
						<th width="70" class="word_wrap_break">Delivery Party</th>
						<th width="70">PO Buyer</th>
						<th width="70">Style Ref.</th>
						<th width="70">Season</th>
						<th width="70">Booking No</th>
						<th width="70">Booking Type</th>
						<th width="70" class="word_wrap_break">Short Booking Type</th>
						<th width="70">Division</th>
						<th width="70" class="word_wrap_break">Relevent Main Booking No</th>
						<th width="110">FSO No</th>
						<th width="70" class="word_wrap_break">Initial Booking Qty Kg</th>
						<th width="70" class="word_wrap_break">Initial Booking Qty Yds</th>
						<th width="70" class="word_wrap_break">Initial Booking Amount</th>
						<th width="70" class="word_wrap_break">Current Booking Qty Kg</th>
						<th width="70" class="word_wrap_break">Current Booking Qty Yds</th>
						<th width="70" class="word_wrap_break">Current Booking Amount</th>
						<th width="70" class="word_wrap_break">Grey Required Qty Kg [As per FSO]</th>

						<th width="100" class="word_wrap_break">Body Part</th>
						<th width="70" class="word_wrap_break">Construction</th>
						<th width="100" class="word_wrap_break">Composition</th>
						<th width="70">GSM</th>
						<th width="70">F/Dia</th>
						<th width="70">Dia Type</th>
						<th width="70">Color Type</th>
						<th width="70">Fab. Color</th>

						<th width="70" class="word_wrap_break">Previous Delivery Qty Kg</th>
						<th width="70" class="word_wrap_break">Previous Delivery Qty Yds</th>
						<th width="70" class="word_wrap_break">Current Delivery Qty Kg</th>
						<th width="70" class="word_wrap_break">Current Delivery Qty Yds</th>
						<th width="70" class="word_wrap_break">TTL Delivery Qty Kg</th>
						<th width="70" class="word_wrap_break">TTL Delivery Qty Yds</th>

						<th width="70">Avg. Rate (Kg)</th>
						<th width="70">Avg. Rate (Yds)</th>
						<th width="70">Delivery Amount</th>

						<th width="70" class="word_wrap_break">Initial Short / (Excess Qty) Kg</th>
						<th width="70" class="word_wrap_break">Intial Short / (Excess Qty) Yds</th>
						<th width="70" class="word_wrap_break">Current Short / (Excess Qty) Kg</th>
						<th width="70" class="word_wrap_break">Current Short / (Excess Qty) Yds</th>
						<th width="70" class="word_wrap_break">Initial Short / (Excess Qty) Kg %</th>
						<th width="70" class="word_wrap_break">Intial Short / (Excess Qty) Yds %</th>
						<th width="70" class="word_wrap_break">Current Short / (Excess Qty) Kg %</th>
						<th width="70" class="word_wrap_break">Current Short / (Excess Qty) Yds %</th>
						<th width="70" class="word_wrap_break">Bill Amount</th>
						<th width="70" class="word_wrap_break">Upcharge</th>
						<th width="70" class="word_wrap_break">Discount</th>
						<th width="70" class="word_wrap_break">Total Bill Amount</th>
					</tr>
				</thead>
			</table>
			<div style="width:3470px; max-height:540px; overflow-y:scroll"  align="left" id="scroll_body">
				<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3450" id="table_body">
					<tbody>
						<?
						$i=1;
						foreach ($data_array as $type => $type_data)
						{
							krsort($type_data);
							foreach ($type_data as $booking_entry_form => $booking_entry_form_data) 
							{
								foreach ($booking_entry_form_data as $sales_booking_no => $sales_booking_no_data) 
								{
									foreach ($sales_booking_no_data as $fso_no => $fso_no_data) 
									{
										foreach ($fso_no_data as $fabDescStr => $row) 
										{
											$fabDescStrArr = explode("*", $fabDescStr);
											$body_part_id = $fabDescStrArr[0];
											$determination_id = $fabDescStrArr[1];
											$gsm = $fabDescStrArr[2];
											$dia = $fabDescStrArr[3];
											$width_type = $fabDescStrArr[4];
											$color_id = $fabDescStrArr[5];
											
											if($row["within_group"] ==1)
											{
												$delivery_party = $company_lib_arr[$row["buyer_id"]];
											}else{
												$delivery_party = $buyer_arr[$row["buyer_id"]];
											}
											$division_no="";$main_booking="";
											if($booking_entry_form==88)
											{
												$divi_arr = array_unique(explode(",",chop($short_booking_ref_arr[$sales_booking_no]['division'],",")));
												$main_booking = implode(",",array_unique(explode(",",chop($short_booking_ref_arr[$sales_booking_no]['main_booking'],","))));
												foreach ($divi_arr as $val) 
												{
													if($division_no=="")
													{
														$division_no=$short_division_array[$val];
													}
													else{
														$division_no .= ",".$short_division_array[$val];
													}
												}
											}
											
											if($row["within_group"] ==1)
											{
												$initial_kg_qnty = $initial_booking_qnty_kg[1][$sales_booking_no]['finqty'];
												$initial_kg_amnt = $initial_booking_qnty_kg[1][$sales_booking_no]['amount'];
												$last_approval = $approval_booking_no_kg[$sales_booking_no];
												$current_kg_qnty = $initial_booking_qnty_kg[$last_approval][$sales_booking_no]['finqty'];
												$current_kg_amnt = $initial_booking_qnty_kg[$last_approval][$sales_booking_no]['amount'];

												$initial_yds_qnty = $initial_booking_qnty_yds[1][$sales_booking_no]['finqty'];
												$initial_yds_amnt = $initial_booking_qnty_yds[1][$sales_booking_no]['amount'];
												$last_approval = $approval_booking_no_yds[$sales_booking_no];
												$current_yds_qnty = $initial_booking_qnty_yds[$last_approval][$sales_booking_no]['finqty'];
												$current_yds_amnt = $initial_booking_qnty_yds[$last_approval][$sales_booking_no]['amount'];
											}
											else
											{
												$initial_kg_qnty = $current_kg_qnty = $fso_grey_qnty_arr[$row['po_breakdown_id']][12]['booking_qnty'];
												$initial_yds_qnty = $current_yds_qnty = $fso_grey_qnty_arr[$row['po_breakdown_id']][27]['booking_qnty'];
												$initial_kg_amnt = $current_kg_amnt = $fso_grey_qnty_arr[$row['po_breakdown_id']][12]['booking_amount'];
												$initial_yds_amnt = $current_yds_amnt = $fso_grey_qnty_arr[$row['po_breakdown_id']][27]['booking_amount'];
											}


											$initial_total_amnt = $initial_kg_amnt+$initial_yds_amnt;
											$current_total_amnt = $current_kg_amnt+$current_yds_amnt;
											//echo "$fso_no == $fabDescStr<br>";
											$delivery_qnty_kg_pre 		= $data_array_uom[$fso_no][$fabDescStr][12]['pre_qnty'];
											$delivery_amnt_kg_pre 		= $data_array_uom[$fso_no][$fabDescStr][12]['pre_amount'];
											$delivery_qnty_kg_current 	= $data_array_uom[$fso_no][$fabDescStr][12]['current_qnty'];
											$delivery_amnt_kg_current 	= $data_array_uom[$fso_no][$fabDescStr][12]['current_amount'];
											$grand_total_delivery_qnty_kg_current += $delivery_qnty_kg_current;

											$delivery_qnty_kg_total = $delivery_qnty_kg_pre + $delivery_qnty_kg_current;
											$delivery_amnt_kg_total = $delivery_amnt_kg_pre + $delivery_amnt_kg_current;
											$delivery_rate_kg=0;
											if($delivery_qnty_kg_total>0)
											{
												//$delivery_rate_kg = $data_array_uom[$fso_no][$fabDescStr][12]['amount']/$data_array_uom[$fso_no][$fabDescStr][12]['qnty'];
												$delivery_rate_kg = $delivery_amnt_kg_total/$delivery_qnty_kg_total;
											}
											
											$delivery_qnty_yds_pre = $data_array_uom[$fso_no][$fabDescStr][27]['pre_qnty'];
											$delivery_amnt_yds_pre = $data_array_uom[$fso_no][$fabDescStr][27]['pre_amount'];
											$delivery_qnty_yds_current = $data_array_uom[$fso_no][$fabDescStr][27]['current_qnty'];
											$delivery_amnt_yds_current = $data_array_uom[$fso_no][$fabDescStr][27]['current_amount'];
											$grand_total_delivery_qnty_yds_current += $delivery_qnty_yds_current;

											$delivery_qnty_yds_total = $delivery_qnty_yds_pre + $delivery_qnty_yds_current;
											$delivery_amnt_yds_total = $delivery_amnt_yds_pre + $delivery_amnt_yds_current;
											$delivery_rate_yds=0;
											if($delivery_qnty_yds_total>0)
											{
												$delivery_rate_yds = $delivery_amnt_yds_total/$delivery_qnty_yds_total;
											}

											$delivery_amnt_current_total = ($delivery_qnty_kg_current*$delivery_rate_kg);// + ($delivery_qnty_yds_current*$delivery_rate_yds);

											$grand_total_delivery_amnt_current_total  += $delivery_amnt_current_total;

											$fso_kg_delivery = $data_array_fso_uom[$fso_no][12];
											$fso_yds_delivery = $data_array_fso_uom[$fso_no][27];

											//$remarks = implode(",",array_filter(array_unique(explode(",",chop($bill_amount_arr[$row["po_breakdown_id"]]['remarks'],",")))));
											
											$bill_amount = $bill_amount_arr[$row["po_breakdown_id"]]['amount'];
											$dtls_upcharge = $bill_amount_arr[$row["po_breakdown_id"]]['dtls_upcharge'];
											$dtls_discount = $bill_amount_arr[$row["po_breakdown_id"]]['dtls_discount'];

											$total_bill_amount = $bill_amount + $dtls_upcharge - $dtls_discount;
										
											$fso_grey_qnty = $fso_grey_qnty_arr[$row['po_breakdown_id']]['grey_qty'];
											$fso_color_type =array_filter(array_unique($fso_color_type_arr[$row["po_breakdown_id"]][$body_part_id][$determination_id][$color_id]));
											$color_type_name="";
											foreach ($fso_color_type as  $value) 
											{
												$color_type_name = ($color_type_name == "") ? $color_type[$value]: $color_type_name .", ".$color_type[$value];									
											}

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<?echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<?echo $i;?>">
												<td width="30"><? echo $i;?></td>
												<td width="70" class="word_wrap_break"><? echo $company_lib_arr[$row["company_id"]]; ?></td>
												<td width="40"><? echo $yes_no[$row["within_group"]]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $delivery_party; ?></td>
												<td width="70" class="word_wrap_break"><? echo $buyer_arr[$row["po_buyer"]]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $row["style_ref_no"]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $season_arr[$row["season_id"]]; ?></td>
												<?
												if($type ==1)
												{
													?>
													<td width="70" class="word_wrap_break"><a href="##" onclick="generate_booking_history('<? echo $sales_booking_no;?>');"><? echo $sales_booking_no; ?></a></td>
													<?
												}
												else
												{
													?>
													<td width="70" class="word_wrap_break"><? echo $sales_booking_no; ?></td>
													<?
												}
												?>
												<td width="70" class="word_wrap_break"><? echo $salesTypeData[$row["po_breakdown_id"]]['booking_type']; ?></td>
												<td width="70" class="word_wrap_break"><? echo $short_booking_type[$row["short_booking_type"]];?></td>
												<td width="70" class="word_wrap_break"><? echo $division_no;?></td>
												<td width="70" class="word_wrap_break"><? echo $main_booking;?></td>
												<td width="110" class="word_wrap_break"><? echo $fso_no; ?></td>
												<? 
												if($booking_dupli_chk[$fso_no] =="")
												{
													?>
													<td width="70" class="word_wrap_break"><? echo number_format($initial_kg_qnty,"2"); ?></td>
													<td width="70" class="word_wrap_break"><? echo number_format($initial_yds_qnty,"2"); ?></td> 
													<td width="70" class="word_wrap_break"><? echo number_format($initial_total_amnt,"2"); ?></td>
													<td width="70" class="word_wrap_break"><? echo number_format($current_kg_qnty,"2"); ?></td>
													<td width="70" class="word_wrap_break"><? echo number_format($current_yds_qnty,"2"); ?></td> 
													<td width="70" class="word_wrap_break"><? echo number_format($current_total_amnt,"2"); ?></td>
													<td width="70" class="word_wrap_break"><? echo number_format($fso_grey_qnty,"2"); ?></td>
													<?
												}
												else
												{
													?>
													<td width="70"><? //echo number_format($initial_kg_qnty,"2"); ?></td>
													<td width="70"><? //echo number_format($initial_yds_qnty,"2"); ?></td> 
													<td width="70"><? //echo number_format($initial_total_amnt,"2"); ?></td>
													<td width="70"><? //echo number_format($current_kg_qnty,"2"); ?></td>
													<td width="70"><? //echo number_format($current_yds_qnty,"2"); ?></td> 
													<td width="70"><? //echo number_format($current_total_amnt,"2"); ?></td>
													<td width="70"><? //echo number_format($fso_grey_qnty,"2"); ?></td>
													<?
												}
												?>
												<td width="100" class="word_wrap_break"><? echo $body_part[$body_part_id]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $constructtion_arr[$determination_id]; ?></td>
												<td width="100" class="word_wrap_break"><? echo $composition_arr[$determination_id]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $gsm; ?></td>
												<td width="70" class="word_wrap_break"><? echo $dia; ?></td>
												<td width="70" class="word_wrap_break"><? echo $fabric_typee[$width_type]; ?></td>
												<td width="70" class="word_wrap_break"><? echo $color_type_name;?></td>
												<td width="70" class="word_wrap_break"><? echo $color_arr[$color_id]; ?></td>
												<td width="70" class="word_wrap_break"><? echo number_format($delivery_qnty_kg_pre,"2"); ?></td>
												<td width="70" class="word_wrap_break"><? echo number_format($delivery_qnty_yds_pre,"2"); ?></td>
												<td width="70" class="word_wrap_break"><a href="##" onclick="openmypage_delivery_qnty_popup('<? echo $row['po_breakdown_id'];?>','<? echo $fabDescStr; ?>','<? echo $date_from;?>',12);"><? echo number_format($delivery_qnty_kg_current,"2"); ?></a></td>
												<td width="70" class="word_wrap_break"><a href="##" onclick="openmypage_delivery_qnty_popup('<? echo $row['po_breakdown_id'];?>','<? echo $fabDescStr; ?>','<? echo $date_from;?>',27);"><? echo number_format($delivery_qnty_yds_current,"2"); ?></a></td>
												<td width="70" class="word_wrap_break" align="right"><? echo number_format($delivery_qnty_kg_total,"2"); ?></td>
												<td width="70" class="word_wrap_break" align="right"><? echo number_format($delivery_qnty_yds_total,"2"); ?></td>


												<td width="70" class="word_wrap_break" align="right"><? echo number_format($delivery_rate_kg,"2"); ?></td>
												<td width="70" class="word_wrap_break" align="right"><? echo number_format($delivery_rate_yds,"2"); ?></td>
												<td width="70" class="word_wrap_break" align="right"><? echo number_format($delivery_amnt_current_total,"2"); ?></td>
												<?
												if($booking_dupli_chk[$fso_no] =="")
												{
													$initial_short_excess_kg = $fso_kg_delivery-$initial_kg_qnty;
													$initial_short_excess_yds = $fso_yds_delivery-$initial_yds_qnty;
													$current_short_excess_kg = $fso_kg_delivery-$current_kg_qnty;
													$current_short_excess_yds = $fso_yds_delivery-$current_yds_qnty;

													$grand_total_initial_short_excess_kg += $fso_kg_delivery-$initial_kg_qnty;
													$grand_total_initial_short_excess_yds += $fso_yds_delivery-$initial_yds_qnty;
													$grand_total_current_short_excess_kg += $fso_kg_delivery-$current_kg_qnty;
													$grand_total_current_short_excess_yds += $fso_yds_delivery-$current_yds_qnty;
													$grand_total_bill_amount += $bill_amount;
													$grand_total_dtls_upcharge += $dtls_upcharge;
													$grand_total_dtls_discount += $dtls_discount;
													$grand_total_tot_bill_amount += $bill_amount;

													if($initial_kg_qnty){
														$initial_short_excess_kg_perc = ($initial_short_excess_kg/$initial_kg_qnty)*100;
													}
													if($initial_yds_qnty){
														$initial_short_excess_yds_perc = ($initial_short_excess_yds/$initial_yds_qnty)*100;
													}
													if($current_kg_qnty){
														$current_short_excess_kg_perc = ($current_short_excess_kg/$current_kg_qnty)*100;
													}
													if($current_yds_qnty)
													{
														$current_short_excess_yds_perc = ($current_short_excess_yds/$current_yds_qnty)*100;
													}
													?>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($initial_short_excess_kg,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($initial_short_excess_yds,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($current_short_excess_kg,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($current_short_excess_yds,2); ?></td>

													<td width="70" class="word_wrap_break" align="right"><? echo number_format($initial_short_excess_kg_perc,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($initial_short_excess_yds_perc,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($current_short_excess_kg_perc,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($current_short_excess_yds_perc,2); ?></td>

													<td width="70" class="word_wrap_break" align="right"><? echo number_format($bill_amount,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($dtls_upcharge,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($dtls_discount,2); ?></td>
													<td width="70" class="word_wrap_break" align="right"><? echo number_format($total_bill_amount,2); ?></td>
													<?
													$booking_dupli_chk[$fso_no]=$fso_no;
												}
												else
												{
													?>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>

													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>

													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<td width="70">&nbsp;</td>
													<?
												}
												?>												
											</tr>
											<?
											$i++;
										}
									}
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3450" align="left" >
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>

						<th width="100" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="100" class="word_wrap_break">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">Total: </th>
						<th width="70" class="word_wrap_break" id="value_grand_total_delivery_qnty_kg_current"><? echo number_format($grand_total_delivery_qnty_kg_current,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_delivery_qnty_yds_current"><? echo number_format($grand_total_delivery_qnty_yds_current,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70">
							<?
								$avg_rate = number_format($grand_total_delivery_amnt_current_total,2,'.','')/number_format($grand_total_delivery_qnty_kg_current,2,'.','');
								echo number_format($avg_rate,2,'.','');
							?>
						</th>
						<th width="70">&nbsp;</th>
						<th width="70" id="value_grand_total_delivery_amnt_current_total"><? echo number_format($grand_total_delivery_amnt_current_total,2,'.',''); ?></th>

						<th width="70" class="word_wrap_break" id="value_grand_total_initial_short_excess_kg"><? echo number_format($grand_total_initial_short_excess_kg,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_initial_short_excess_yds"><? echo number_format($grand_total_initial_short_excess_yds,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_current_short_excess_kg"><? echo number_format($grand_total_current_short_excess_kg,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_current_short_excess_yds"><? echo number_format($grand_total_current_short_excess_yds,2,'.',''); ?></th>

						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break">&nbsp;</th>
						<th width="70" class="word_wrap_break" id="value_grand_total_bill_amount"><? echo number_format($grand_total_bill_amount,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_upcharge"><? echo number_format($grand_total_dtls_upcharge,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_discount"><? echo number_format($grand_total_dtls_discount,2,'.',''); ?></th>
						<th width="70" class="word_wrap_break" id="value_grand_total_tot_bill_amount"><? echo number_format($grand_total_tot_bill_amount,2,'.',''); ?></th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	echo "Execution Time: " . (microtime(true) - $started) . " S";
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}



if($action=="delivery_quantity_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($poId,$body_part_id,$determination_id,$booking_no,$color_ids)= explode("__", $data);
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0  ", "id", "color_name");
	$comp_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0  " ,"id","company_short_name");

	$fabDescStrArr = explode("*", $fabDescStr);
	$body_part_id = $fabDescStrArr[0];
	$determination_id = $fabDescStrArr[1];
	$gsm = $fabDescStrArr[2];
	$dia = $fabDescStrArr[3];
	$width_type = $fabDescStrArr[4];
	$color_id = $fabDescStrArr[5];
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			$(".flt").css("display","none");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
		}
		var tableFilters =
		{

			col_operation: {
				id: ["value_issue_qty"],
				col: [11],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

	</script>
<fieldset style="width:918px; margin-left:3px">
	<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<div style="width:100%" id="report_container">
		<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_header">
			<caption>
				<b>Finish Fabrics Issue Info</b>
			</caption>
			<thead>
				<th width="20">SL</th>
				<th width="65">Issue Date</th>
				<th width="100">Issue ID</th>
				<th width="50">Batch No</th>
				<th width="50">Ext. No</th>
				<th width="50">Sales Order No</th>
				<th width="60">Booking No</th>
				<th width="60">Batch Date</th>
				<th width="60">Batch Against</th>
				<th width="60">Batch For</th>
				<th width="60">Color</th>
				<th width="60">Issue Qty.</th>
				<th width="60">Remarks</th>
			</thead>
		</table>
		<div style="width:918px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
				<tbody>
					<?
					$sql_data="SELECT a.id issue_id, a.issue_number, a.issue_date, b.batch_id, f.batch_no, f.extention_no, f.batch_date, f.batch_for, f.batch_against, sum(c.quantity) delivery_qnty, d.color color_id, e.job_no, e.sales_booking_no , b.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, fabric_sales_order_mst e, pro_batch_create_mst f where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id =e.id and b.batch_id=f.id and a.entry_form =224 and b.status_active=1 and c.entry_form=224 and c.is_sales =1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id =$companyID and c.po_breakdown_id =$fso_id and d.detarmination_id =$determination_id and d.gsm=$gsm and d.dia_width =$dia and b.body_part_id =$body_part_id and b.width_type =$width_type and d.color =$color_id and b.uom =$uom group by a.id, a.issue_number, a.issue_date, b.batch_id, f.batch_no, f.extention_no, f.batch_date, f.batch_for, f.batch_against, d.color, e.job_no, e.sales_booking_no , b.remarks";

					$data_array=sql_select($sql_data);
					$i=1;
					foreach( $data_array as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$from_date=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('issue_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $from_date)))
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="20"><? echo $i;?></td>
								<td width="65"><? echo change_date_format($row[csf("issue_date")]); ?></td>
								<td width="100"><? echo $row[csf("issue_number")];?></td>
								<td width="50"><p style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("batch_no")];?></p></td>
								<td width="50"><? echo $row[csf("extention_no")];?></td>
								<td width="50"><? echo  $row[csf("job_no")];?></td>
								<td width="60"><? echo  $row[csf("sales_booking_no")]?></td>
								<td width="60"><? echo change_date_format($row[csf("batch_date")]); ?></td>
								<td width="60"><? echo $batch_against[$row[csf("batch_against")]];?></td>
								<td width="60"><? echo $batch_for[$row[csf("batch_for")]];?></td>
								<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo $color_arr[$row[csf("color_id")]];?></p></td>
								<td width="60" align="right"><? echo number_format($row[csf("delivery_qnty")],2);?></td>
								<td width="60">&nbsp;</td>
							</tr>

							<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="20">&nbsp;</th>
				<th width="65">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60" align="right" id="value_issue_qty"></th>
				<th width="60">&nbsp;</th>
			</tfoot>
		</table>
	</div>
</fieldset>
<script>setFilterGrid('table_body',-1,tableFilters);</script>
<?
exit();
}
?>