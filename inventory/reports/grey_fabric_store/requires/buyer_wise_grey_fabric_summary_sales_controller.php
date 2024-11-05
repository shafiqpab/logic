<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php");
	die;
}

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);
$storeNameArr=return_library_array( "SELECT id,store_name from lib_store_location ", "id", "store_name" );
//manual precision settings here
ini_set('precision',8);
/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if($action=="load_drop_down_buyer")
{
	if($type==1)
		$party="1,3,21,90";
	else
		$party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$choosenCompany.") ".$buyer_cond." AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

/*
|--------------------------------------------------------------------------
| fso_no_popup
|--------------------------------------------------------------------------
|
*/

if($action=="fso_no_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(job_no, job_id)
		{
			document.getElementById('hide_job_no').value=job_no;
			document.getElementById('hide_job_id').value=job_id;
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
						<th>Year</th>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" value="<? echo $cbo_year_id?>" disabled readonly />
						</td>
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year_id; ?>, 'create_fso_no_search_list_view', 'search_div', 'buyer_wise_grey_fabric_summary_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| create_fso_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_fso_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and a.style_ref_no like '".$search_string."%'";
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
	else $year_field="";//defined Later

	$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Cust. Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				/*if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];*/
				$buyer=$buyer_arr[$row[csf('customer_buyer')]];

				//$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="70"><p><? echo $buyer; ?></p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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


/*
|--------------------------------------------------------------------------
| booking_no_popup
|--------------------------------------------------------------------------
|
*/
if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		/*function js_set_value2( str )
		{
			if (str!="") str=str.split("_");
			$('#hide_job_id').val( str[0] );
			$('#hide_job_no').val( str[1] );
			parent.emailwindow.hide();
		}*/

		function js_set_value2( str )
		{
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );

			}
			
			var id = ''; 
			var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';

			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
		}
		
		function func_onchange_booking_search_by(data)
		{
			//alert('su..re');
			//1 = booking no
			//2 = job no
			//3 = Style Ref.
			var jobNo = '<?php echo $txt_job_no; ?>';
			if(data == 2 && jobNo != '')
			{
				$('#txt_search_common').val('<? echo $txt_job_no; ?>').attr('disabled', 'disabled');
			}
			else
			{
				$('#txt_search_common').removeAttr('disabled');
			}
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:680px;">
				<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Cust. Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Booking No</th>
						<th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</th>
						<!--<input type="hidden" name="hide_recv_id" id="hide_recv_id" value="" />-->
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								//is_disabled
								$is_disabled = ($buyer_name != 0 ? '1' : '0');

								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$is_disabled );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"FSO No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../');func_onchange_booking_search_by(this.value) ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_booking_no_search_list_view', 'search_div', 'buyer_wise_grey_fabric_summary_sales_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| create_booking_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond = " AND a.customer_buyer IN (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond = " AND a.customer_buyer=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 3)
	{
		$search_field = "a.style_ref_no";
	}
	else if($search_by == 2)
	{
		$search_field = "a.job_no";
	}
	else
		$search_field = "a.sales_booking_no";

	if($db_type == 0)
		$year_field_by = " AND YEAR(a.insert_date)";
	else if($db_type == 2)
		$year_field_by = " AND TO_CHAR(a.insert_date,'YYYY')";
	else
		$year_field_by = "";
	
	
	if($db_type == 0)
		$month_field_by = " AND month(a.insert_date)";
	else if($db_type == 2)
		$month_field_by = " AND to_char(a.insert_date,'MM')";
	else
		$month_field_by = "";
	
	if($db_type == 0)
		$year_field = " YEAR(a.insert_date) AS year";
	else if($db_type == 2)
		$year_field = " TO_CHAR(a.insert_date,'YYYY') AS year";
	else
		$year_field = "";

	if($year_id != 0)
		$year_cond = " ".$year_field_by." = ".$year_id."";
	else
		$year_cond = "";
	
	if($month_id != 0)
		$month_cond = " ".$month_field_by." = ".$month_id."";
	else
		$month_cond = "";


	$sql = "SELECT a.company_id, a.buyer_id, a.job_no as fso_no, a.style_ref_no, a.sales_booking_no, a.id as fso_id, a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 AND a.company_id IN(".$company_id.") and ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_cond." ".$month_cond;

	//echo $sql;
	$sqlResult=sql_select($sql);
	if(empty($sqlResult))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$buyerIdArr = array();
	foreach($sqlResult as $row)
	{
		$buyerIdArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
	}
	
	// $buyer_arr=return_library_array( "SELECT id, buyer_name FROM lib_buyer WHERE id IN (".implode(',', $buyerIdArr).")",'id','buyer_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$company_id.")",'id','company_name');
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="110">Cust. Buyer</th>
						<th width="110">Sales Order No</th>
						<th width="120">Style Ref.</th>
						<th width="">Booking No</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							// $data = $row[csf('fso_id')].'_'.$row[csf('sales_booking_no')];
							$data = $i.'_'.$row[csf('fso_id')].'_'.$row[csf('sales_booking_no')];
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
								<td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
								<td width="110"><p><? echo $buyer_arr[$row[csf('customer_buyer')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('fso_no')]; ?></p></td>
								<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td width=""><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%">
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
									Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
	<?php
	exit();
}

/*
|--------------------------------------------------------------------------
| report_generate_summary
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = trim(str_replace("'", "", $txt_job_no));
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$txt_interal_ref = str_replace("'", "", $txt_interal_ref);

	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);

	if($buyerId != 0)
	{
		$search_condition .= " AND a.customer_buyer in  (".$buyerId.")";
	}

	if ($year>0) 
	{
		if($db_type==0)
		{
			$search_condition .= " AND YEAR(a.insert_date) = ".$year."";
		}
		else if($db_type==2)
		{
			$search_condition .= " AND TO_CHAR(a.insert_date,'YYYY') = ".$year."";
		}
	}

	if($jobNo != '')
	{
		if($jobId != '')
		{
			$search_condition .= " AND a.id =".$jobId."";
		}else{
			$search_condition .= " AND a.job_no like '%".$jobNo."%'";
		}
	}
	
	if($bookingNo != '')
	{
		$str_arry = explode(",",$bookingNo);
	    $all_bookingNo="";
	    foreach ($str_arry as $key => $booking) 
	    {
	        if ($all_bookingNo=="") 
	        {
	            $all_bookingNo.= $booking;
	        }
	        else 
	        {
	            $all_bookingNo.= "','".$booking;
	        }
	    }
	    // echo $all_bookingNo;die;
		$search_condition .= " AND a.sales_booking_no in('$all_bookingNo')";
	}
	
	if($toDate != '')
	{
		if($db_type == 0)
		{
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$endDate = change_date_format($toDate,"","",1);
		}

		$search_condition .= " AND c.transaction_date <= '".$endDate."'";
	}


	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (55)");
	oci_commit($con);
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$ref_fso_arr=array();
	if($txt_interal_ref!="")
	{
		$int_fso_sql = sql_select("SELECT c.id
		from wo_booking_dtls a, wo_po_break_down b, fabric_sales_order_mst c
		where a.po_break_down_id=b.id and c.within_group=1 and c.book_without_order !=1 and a.booking_no=c.sales_booking_no
		and b.grouping='$txt_interal_ref' 
		group by c.id");
	}
	foreach($int_fso_sql as $row)
	{
		$search_fso_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$int_ref_cond="";
	if(!empty($search_fso_arr))
	{
		$int_ref_cond = " and a.id in (".implode(',',$search_fso_arr).")";
	}

	$data_sql = sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.sales_booking_no, a.customer_buyer, a.buyer_id, a.company_id, a.booking_without_order, a.within_group, b.po_breakdown_id, b.trans_type, b.quantity 
	FROM fabric_sales_order_mst a, order_wise_pro_details b, inv_transaction c
	WHERE a.id = b.po_breakdown_id and b.trans_id=c.id and c.item_category=13 and b.entry_form in (2,22,58,61,84,133) and b.is_sales=1 and b.status_active=1 and b.is_deleted=0 and b.trans_id>0 AND a.company_id=$companyId $search_condition $int_ref_cond");

	$data_array=array();
	$fso_arr=array();
	foreach($data_sql as $row)
	{
		if($row[csf('trans_type')] ==1 || $row[csf('trans_type')] ==5 )
		{
			if($row[csf('trans_type')] ==1 )
			{
				$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['rcv_qnty'] +=$row[csf('quantity')];
			}
			else
			{
				$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['trans_qnty'] +=$row[csf('quantity')];
			}
		}
		else if($row[csf('trans_type')] ==2 )
		{
			$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['issue'] +=$row[csf('quantity')];
		}
		else if($row[csf('trans_type')] ==4 )
		{
			$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['issue_ret'] +=$row[csf('quantity')];
		}
		else if($row[csf('trans_type')] ==6 )
		{
			$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['tr_out'] +=$row[csf('quantity')];
		}

		$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['company_id']=$row[csf('company_id')];
		$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['booking']=$row[csf('sales_booking_no')];
		$data_array[$buyer_array[$row[csf('customer_buyer')]]][$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];

		$fso_arr[$row[csf('id')]]=$row[csf('id')];
       
	}


	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 55, 1,$fso_arr, $empty_arr); // Fso Id temp entry

	$fso_ref_sql=sql_select("SELECT a.job_no, c.grouping, d.id as fso_dtls, d.grey_qty, d.finish_qty from fabric_sales_order_mst a left join wo_booking_dtls b on a.within_group=1 and a.book_without_order !=1 and a.sales_booking_no=b.booking_no
left join wo_po_break_down c on  b.po_break_down_id=c.id, fabric_sales_order_dtls d, GBL_TEMP_ENGINE g
where a.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and a.id=g.ref_val and g.entry_form=55 and g.ref_from=1 and g.user_id=$user_id group by a.job_no, c.grouping, d.id, d.grey_qty, d.finish_qty");
 
$dtls_fso_chk=array();
	foreach($fso_ref_sql as $row)
	{
		if($dtls_fso_chk[$row[csf("fso_dtls")]]=="")
		{
			$sales_array[$row[csf('job_no')]]['grey_qnty'] +=$row[csf('grey_qty')];
			$sales_array[$row[csf('job_no')]]['fin_qnty'] +=$row[csf('finish_qty')];
		}
		$dtls_fso_chk[$row[csf("fso_dtls")]]=$row[csf("fso_dtls")];
		$sales_array[$row[csf('job_no')]]['int_ref'][$row[csf('grouping')]] =$row[csf('grouping')];
       
	}
	

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (55)");
	oci_commit($con);
	disconnect($con);

	$width = 1280;
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
			word-wrap: break-word;
		}
		.font_size {
			font-size: 11px;
			color:black;
		}
	</style>
	<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
		<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <tr class="form_caption" style="border:none;">
                	<th align="center" colspan="13" style="font-size:16px"><strong>Buyer Wise Grey Fabrics Stock Summary Sales Report</strong></th>
                </tr>
                <tr>
					<th width="30">SL</th>
					<th width="100" class="word_break">Company</th>
					<th width="100" class="word_break">Cust. Buyer Name</th>
					<th width="100" class="word_break">IR/IB</th>
					<th width="100" class="word_break">Fab Booking</th>
					<th width="80" class="word_break">FSO</th>
					<th width="100" class="word_break">Required Qty (Finish)</th>
					<th width="100" class="word_break">Required Qty (Grey)</th>
					<th width="100" class="word_break">Total Recv.<br><p class="font_size">(recv + trans. in - trans. out)</p></th>
					<th width="100" class="word_break">Receive<br>Balance</th>
					<th width="100" class="word_break">Total Issue<br><p class="font_size">(issue - issue ret)</p></th>
					<th width="100" class="word_break">Issue<br>Balance</th>
					<th width="100" class="word_break">Stock Qty.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<tbody>
				<?php
				$sl = 1;
				$grandTotal = array();
				ksort($data_array);
				foreach($data_array as $custBuyer=>$custBuyerData)
				{
					$subTotal_fin_req_qnty=$subTotal_grey_req_qnty=$subTotal_totalRcvQty=$subTotal_receive_balance=$subTotal_issueQty=$subTotal_issue_balance=$subTotal_stockQty=0;
					ksort($custBuyerData);
					foreach($custBuyerData as $fsoNo=>$row)
					{
						$totalRcvQty = number_format($row["rcv_qnty"],2,'.','')+number_format($row['trans_qnty'],2,'.','')-number_format($row['tr_out'],2,'.','');
						$issueQty = number_format($row["issue"],2,'.','')- number_format($row["issue_ret"],2,'.','');

						$grey_req_qnty = $sales_array[$fsoNo]['grey_qnty'];
						$fin_req_qnty = $sales_array[$fsoNo]['fin_qnty'];

						$receive_balance=$grey_req_qnty-$totalRcvQty;
						$issue_balance=$grey_req_qnty-$issueQty;
						$stockQty=$totalRcvQty-$issueQty;

						$int_ref = implode(",",array_unique($sales_array[$fsoNo]['int_ref']));

						$total_receive_title = 'receive= '.number_format($row["rcv_qnty"],2,'.','').', + transfer in='.number_format($row['trans_qnty'],2,'.',''). ', - transfer out= ' .number_format($row['tr_out'],2,'.','');
						$total_issue_title = 'issue= '.number_format($row["issue"],2,'.','') .', - issue return= '. number_format($row["issue_ret"],2,'.','');

						if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
							<td width="30" align="center"><?php echo $sl; ?></td>
							<td width="100" class="word_break"><?php echo $company_arr[$row['company_id']]; ?></td>
							<td width="100" class="word_break"><? echo $custBuyer; ?></td>
							<td width="100" class="word_break"><? echo $int_ref; ?></td>
							<td width="100" class="word_break"><? echo $row['booking']; ?></td>
							<td width="80" class="word_break" align="center"><? echo $row['job_no_prefix_num']; ?></td>
							<td width="100" class="word_break" align="right"><?php echo number_format($fin_req_qnty,2); ?></td>
							<td width="100" class="word_break" align="right"><?php echo number_format($grey_req_qnty,2); ?></td>
							<td width="100" class="word_break" align="right" title="<? echo $total_receive_title;?>"><?php echo number_format($totalRcvQty,2); ?></td>
							<td width="100" class="word_break" align="right"><?php echo number_format($receive_balance,2); ?></td>
							<td width="100" class="word_break" align="right" title="<? echo $total_issue_title;?>"><?php echo number_format($issueQty,2); ?></td>
							<td width="100" class="word_break" align="right"><?php echo number_format($issue_balance,2); ?></td>

							<td width="100" class="word_break" align="right"><?php echo number_format($stockQty,2); ?></td>
						</tr>
							<?php
							//$grandTotal
							$grandTotal_fin_req_qnty += number_format($fin_req_qnty,2,'.','');
							$grandTotal_grey_req_qnty += number_format($grey_req_qnty,2,'.','');
							$grandTotal_totalRcvQty += number_format($totalRcvQty,2,'.','');
							$grandTotal_receive_balance += number_format($receive_balance,2,'.','');
							$grandTotal_issueQty += number_format($issueQty,2,'.','');
							$grandTotal_issue_balance += number_format($issue_balance,2,'.','');
							$grandTotal_stockQty += number_format($stockQty,2,'.','');

							$subTotal_fin_req_qnty += number_format($fin_req_qnty,2,'.','');
							$subTotal_grey_req_qnty += number_format($grey_req_qnty,2,'.','');
							$subTotal_totalRcvQty += number_format($totalRcvQty,2,'.','');
							$subTotal_receive_balance += number_format($receive_balance,2,'.','');
							$subTotal_issueQty += number_format($issueQty,2,'.','');
							$subTotal_issue_balance += number_format($issue_balance,2,'.','');
							$subTotal_stockQty += number_format($stockQty,2,'.','');
									
							$customer_buyer = $custBuyer;
						$sl++;
					}
					?>
					<tr valign="middle" bgcolor="<? echo $bgcolor; ?>"  style="font-weight:bold;">
						<td width="610" align="right" colspan="7"><? echo $customer_buyer; ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_grey_req_qnty,2); ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_totalRcvQty,2); ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_receive_balance,2); ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_issueQty,2); ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_issue_balance,2); ?></td>
						<td width="100" class="word_break" align="right"><?php echo number_format($subTotal_stockQty,2); ?></td>
					</tr>
					<?
				}
                ?>
                </tbody>
            </table>
        </div>
        <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
            <tfoot>
                <tr >
                    <th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">Grand Total:</th>
                    <th width="100" align="right" ><?php echo number_format($grandTotal_fin_req_qnty,2); ?></th>
                    <th width="100" align="right" ><?php echo number_format($grandTotal_grey_req_qnty,2); ?></th>
					<th width="100" align="right" ><?php echo number_format($grandTotal_totalRcvQty,2); ?></th>
					<th width="100" align="right" ><?php echo number_format($grandTotal_receive_balance,2); ?></th>
					<th width="100" align="right" ><?php echo number_format($subTotal_issueQty,2); ?></th>
					<th width="100" align="right" ><?php echo number_format($grandTotal_issue_balance,2); ?></th>
                    <th width="100" align="right" ><?php echo number_format($grandTotal_stockQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <?php

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	echo $html."####".$filename."####".$reportType;
	disconnect($con);
	die;
}
?>