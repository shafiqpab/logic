<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if($action=="load_drop_down_buyer")
	{
		$data=explode("_",$data);
		if($data[1]==1) $party="1,3,21,90"; else $party="80";
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
		exit();
	}


	if($action=="booking_no_popup")
	{
		echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		?>

		<script>

			var selected_id = new Array; var selected_name = new Array;

			function check_all_datas()
			{
				var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
				tbl_row_count = tbl_row_count - 1;

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tr_'+i).trigger('click'); 
				}
			}

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function js_set_value2( str ) {

				if (str!="") str=str.split("_");

				toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

				if( jQuery.inArray( str[1], selected_id ) == -1 ) {
					selected_id.push( str[1] );
					selected_name.push( str[2] );


				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == str[1] ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );

				}
				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
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

		function show_list_details()
		{

			var booking_type = '<? echo $booking_type?>';
			if($('#txt_search_common').val() =="" || (booking_type==2 && $('#cbo_search_by').val()==2))
			{
				if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
				{
					return;
				}
			}
			

			show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $booking_type; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'sample_grey_fabrics_stock_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:740px;">
				<table width="720" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="110">Buyer</th>
						<th width="100">Search By</th>
						<th id="search_by_td_up" width="120">Please Enter Booking No</th>
						<th width="140">Booking Date</th>
						<th colspan="2"><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						
					</thead>
					<tbody>
						<tr>
							<td align="center" width="110">
								<? 
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>   

							<td align="center">	
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>     
							<td align="center" id="search_by_td" >				
								<input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td width="140">
                            	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px;" readonly/>To
                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px;" readonly/>				
                        	</td>

							</td>
							<td align="center" width="100">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_details ();" style="width:100px;" />
							</td>
						</tr>
						<tr>
                    		<td colspan="5" align="center" ><? echo load_month_buttons(1); ?></td>
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
if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$booking_type=$data[5];
	$date_from 	= trim($data[6]);
	$date_to 	= trim($data[7]);
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}
	
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}else{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}




	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	$job_no_cond = "";

	if($search_by==2)
	{
		$job_no_cond =" and a.job_no_prefix_num like ".$search_string;
	}
	else 
	{
		$booking_no_cond = " and b.booking_no like '".$search_string."'";
		$booking_no_cond2 = " and a.booking_no like '".$search_string."'";
	}

	if($db_type==0) 
	{
		$year_field_by="and YEAR(c.insert_date)"; 
		$year_field_by2="and YEAR(a.insert_date)"; 
	}
	else if($db_type==2) 
	{
		$year_field_by=" and to_char(c.insert_date,'YYYY')";
		$year_field_by2=" and to_char(a.insert_date,'YYYY')";
	}
	else {
		$year_field_by="";
		$year_field_by2="";
	}

	if($year_id!=0) 
	{
		$year_cond=" $year_field_by=$year_id";
		$year_cond2=" $year_field_by2=$year_id";
	} 
	else 
	{
		$year_cond="";
	}
	
	if($booking_type==1)
	{
		$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(4) and c.is_short=2 and a.company_name=$company_id $job_no_cond $buyer_id_cond $year_cond $booking_no_cond $date_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num ";
	}
	else
	{
		$sql ="	select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id, a.booking_no, a.booking_no_prefix_num, a.id as booking_id
		from wo_non_ord_samp_booking_mst a where a.item_category in (2,3) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 $booking_no_cond2 $year_cond2 $date_cond2";	
	}
	
	/*$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num ";

	if($search_by != 2) 
	{
		$sql .=" union all
		select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id, a.style_desc as style_ref_no, a.booking_no, a.booking_no_prefix_num, a.id as booking_id
		from wo_non_ord_samp_booking_mst a where a.item_category in (2,13) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 and $search_field2 like '$search_string' ";
	}
	$sql .= " order by booking_no desc";*/

	//echo $sql;//die;	
	$sqlResult=sql_select($sql);
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th> 
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="">Booking No</th>

					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no_prefix_num')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
									<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
									<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
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


		<?

		exit(); 
	}

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	
	$machine_arr=return_library_array( "select id, dia_width from lib_machine_name", "id", "dia_width"  );

	if($action=="report_generate")
	{ 
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));
		$rpt_type=str_replace("'","",$rpt_type);
		$cbo_value_with=str_replace("'","",$cbo_value_with);
		$cbo_booking_type=str_replace("'","",$cbo_booking_type);
		$booking_no=str_replace("'","",$txt_booking_no);
		$year_id=str_replace("'","",$cbo_year);
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			
		


		if(str_replace("'","",$cbo_buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and c.buyer_id=$cbo_buyer_id";
		}

		$date_from=str_replace("'","",$txt_date_from);
		$date_to=str_replace("'","",$txt_date_to);
		/*if( $date_from=="") 
		{
			$receive_date= $issue_date = $issue_return_date= $transfer_date="";
		} 
		else 
		{
			$receive_date= " and b.receive_date <=".$txt_date_from."";
			$issue_date= " and c.issue_date <=".$txt_date_from."";
			$issue_return_date= " and c.receive_date <=".$txt_date_from."";
			$transfer_date= " and a.transfer_date <=".$txt_date_from."";
			
		}*/

		$booking_date_cond="";
		if( $date_from!="" && $date_to!="")
		{
			if($db_type==0)
			{
				$date_from=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
				$date_to=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
			}
			elseif($db_type==2)
			{
				$date_from=change_date_format(str_replace("'","",$txt_date_from),'','',1);
				$date_to=change_date_format(str_replace("'","",$txt_date_to),'','',1);
			}

			$booking_date_cond = " and c.booking_date between '$date_from' and '$date_to' ";
		}


		$booking_year="";
		if ($db_type==0) 
		{
			$select_Ycount = " group_concat(b.yarn_count) as yarn_counts";
			$select_Ylot  = " group_concat(b.yarn_lot) as yarn_lots";
			$select_Yprod = " group_concat(b.yarn_prod_id) as yarn_prod_ids";
			if($year_id!=0) $booking_year = " and year(c.booking_date) = $year_id" ;

			
		}
		else{
			$select_Ycount = " listagg(cast(b.yarn_count  as varchar(4000)),',') within group (order by b.yarn_count) as yarn_counts";
			$select_Ylot = " listagg(cast(b.yarn_lot  as varchar(4000)),',') within group (order by b.yarn_lot) as yarn_lots";
			$select_Yprod =  " listagg(cast(b.yarn_prod_id  as varchar(4000)),',') within group (order by b.yarn_prod_id) as yarn_prod_ids";
			if($year_id!=0) $booking_year=" and TO_CHAR(c.booking_date,'YYYY')=$year_id"; 
		}


		if($booking_no!="") $bookng_no_cond = " and c.booking_no_prefix_num in ($booking_no)";

		$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name = $cbo_company_id and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
		if($roll_maintained==0)
		{
			echo "This Report will be generated in roll level only";die;
		}
		//echo "test";die;
		if($cbo_booking_type == 1)
		{
			$sql_product_rcv = sql_select("SELECT a.id,a.recv_number, a.receive_date, a.booking_no,a.booking_without_order, e.qnty, b.prod_id,b.trans_id, b.color_range_id, b.febric_description_id,b.gsm, b.width, b.machine_dia, b.stitch_length, b.color_id,b.order_id as po_ids, a.entry_form, $select_Ylot,$select_Ycount,$select_Yprod, c.buyer_id, d.job_no, d.style_ref_no, c.is_approved, c.item_category,c.fabric_source,e.barcode_no
			from inv_receive_master a, pro_grey_prod_entry_dtls b, wo_booking_mst c , wo_po_details_master d,pro_roll_details e
			where a.id = b.mst_id  and a.entry_form in (2,22) and a.booking_id = c.id and a.receive_basis=1 and c.job_no = d.job_no and a.id = e.mst_id and b.id = e.dtls_id and e.entry_form in (2,22) and c.booking_type in (4) and  c.is_short = 2 and a.booking_without_order=0 and b.status_active =1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and c.company_id = $cbo_company_id $booking_year $bookng_no_cond $booking_date_cond $buyer_id_cond group by a.id, a.recv_number, a.receive_date, a.booking_no, a.booking_without_order, e.qnty, b.prod_id, b.trans_id, b.color_range_id, b.febric_description_id,b.gsm, b.width, b.machine_dia, b.stitch_length, b.color_id, b.order_id, a.entry_form, c.buyer_id, d.job_no, d.style_ref_no, c.is_approved, c.item_category,c.fabric_source,e.barcode_no");		
		}
		else
		{			
			$sql_product_rcv = sql_select("SELECT a.id,a.receive_date, a.recv_number,c.booking_no,a.booking_without_order, b.prod_id,b.trans_id, b.color_range_id, b.febric_description_id,b.gsm, b.width, b.machine_dia, b.stitch_length, b.color_id, null as po_ids, a.entry_form, $select_Ylot,$select_Ycount,$select_Yprod, c.buyer_id, null as job_no, null as style_ref_no, c.is_approved, c.item_category, c.fabric_source, e.qnty, e.barcode_no
			from inv_receive_master a, pro_grey_prod_entry_dtls b, wo_non_ord_samp_booking_mst c, pro_roll_details e
			where a.id = b.mst_id  and a.entry_form in (2,22) and e.po_breakdown_id = c.id and a.id = e.mst_id and b.id = e.dtls_id and e.entry_form in (2,22) and a.booking_without_order=1 and b.status_active =1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and c.company_id = $cbo_company_id $booking_year $bookng_no_cond $booking_date_cond $buyer_id_cond group by a.id,a.recv_number, a.receive_date, c.booking_no, a.booking_without_order, e.qnty, b.prod_id, b.trans_id, b.color_range_id, b.febric_description_id,b.gsm, b.width, b.machine_dia, b.stitch_length, b.color_id,a.entry_form, c.buyer_id, c.is_approved, c.item_category,c.fabric_source,e.barcode_no");
			// and a.booking_id = c.id				
		}


		//FAL-SMN-18-00041
		foreach ($sql_product_rcv as $val) 
		{
			if($val[csf("trans_id")] != 0)
			{
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["qnty"] += $val[csf("qnty")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["febric_description_id"] = $val[csf("febric_description_id")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["machine_dia"] = $val[csf("machine_dia")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["stitch_length"] = $val[csf("stitch_length")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_range_id"] = $val[csf("color_range_id")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["yarn_counts"] = $val[csf("yarn_counts")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["yarn_lots"] = $val[csf("yarn_lots")];
				$grey_receive_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["yarn_prod_ids"] = $val[csf("yarn_prod_ids")];
				
			}
			else
			{
				$production_id[$val[csf("id")]] = $val[csf("id")];

				$productionBarcodeWithoutRcv[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
			}
			$job_buyer_style[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
			$job_buyer_style[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_buyer_style[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
			$job_buyer_style[$val[csf("booking_no")]]["po_ids"] .= $val[csf("po_ids")].",";
			$all_booking_no[$val[csf("booking_no")]] = $val[csf("booking_no")];

			$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"] = $val[csf("booking_no")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"] = $val[csf("prod_id")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["febric_description_id"] = $val[csf("febric_description_id")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["gsm"] = $val[csf("gsm")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["width"] = $val[csf("width")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["machine_dia"] = $val[csf("machine_dia")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["stitch_length"] = $val[csf("stitch_length")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["color_id"] = $val[csf("color_id")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["color_range_id"] = $val[csf("color_range_id")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_counts"] = $val[csf("yarn_counts")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_lots"] = $val[csf("yarn_lots")];
			$production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_prod_ids"] = $val[csf("yarn_prod_ids")];


			//$product_id_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
			$yarn_prod_id_arr = array_filter(explode(",", $val[csf("yarn_prod_ids")]));
			foreach ($yarn_prod_id_arr as $value) 
			{
				$product_id_arr[$value] = $value;
			}


		}
		//print_r($job_buyer_style);die;

		if($cbo_booking_type==2)
		{
			$transfer_sql_in = sql_select("select a.to_order_id, b.from_prod_id, b.to_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot,a.transfer_criteria, a.entry_form,c.booking_no, c.buyer_id, sum(d.qnty) as transfer_in_qnty, count(d.id) as transfer_in_roll ,d.barcode_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c , pro_roll_details d where a.to_company=$cbo_company_id and a.id=b.mst_id and c.id = a.to_order_id and a.id = d.mst_id and b.id = d.dtls_id and d.entry_form in (110,180) and c.company_id = $cbo_company_id $booking_date_cond $bookng_no_cond $booking_year and a.transfer_criteria in(6,8) and a.entry_form in (110,180) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.to_order_id, b.from_prod_id, b.to_prod_id, b.y_count, b.yarn_lot, b.to_rack, b.to_shelf,a.transfer_criteria, a.entry_form, c.booking_no, c.buyer_id, d.barcode_no");


			foreach ($transfer_sql_in as $val) 
			{
				$all_booking_no[$val[csf("booking_no")]] = $val[csf("booking_no")];
				$grey_trans_in_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["qnty"] +=  $val[csf("transfer_in_qnty")];
				$grey_trans_in_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["transfer_in_roll"] +=  $val[csf("transfer_in_roll")];
				$transInBarcodeArr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

				$transIn_Rcv_BarcodeArr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

				$job_buyer_style[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];

				$trans_id_prod_id_barcode[$val[csf("barcode_no")]] = $val[csf("to_prod_id")];
			}

			$transInBarcodeArr = array_filter(array_unique($transInBarcodeArr));
			if(count($transInBarcodeArr)>0)
			{
				$transInBarcodeNos = implode(",", $transInBarcodeArr);
				$transBar = $trans_barcode_cond = ""; 

				if($db_type==2 && count($transInBarcodeArr)>999)
				{
					$transInBarcodeArr_chunk=array_chunk($transInBarcodeArr,999) ;
					foreach($transInBarcodeArr_chunk as $chunk_arr)
					{
						$transBar.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";	
					}
							
					$trans_barcode_cond.=" and (".chop($transBar,'or ').")";			
					
				}
				else
				{ 	
					$trans_barcode_cond=" and c.barcode_no in($transInBarcodeNos)";  
				}

				$production_barcode = sql_select("select c.barcode_no, a.booking_no , b.prod_id,  b.febric_description_id, b.gsm, b.width,b.machine_dia,b.stitch_length, b.color_id, b.color_range_id, $select_Ylot,$select_Ycount,$select_Yprod from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id = b.mst_id and b.id = c.dtls_id and a.id = c.mst_id and a.entry_form in (2,22) and c.entry_form in (2,22) and c.status_active = 1 and b.status_active = 1 $trans_barcode_cond group by c.barcode_no, a.booking_no , b.prod_id,  b.febric_description_id, b.gsm, b.width,b.machine_dia,b.stitch_length, b.color_id, b.color_range_id");

				foreach ($production_barcode as $val) 
				{
					/*$transIn_barcode_ref_arr[$val[csf("prod_id")]]["book"] = $val[csf("booking_no")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["prod"] = $val[csf("prod_id")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["febric_description_id"] = $val[csf("febric_description_id")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["width"] = $val[csf("width")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["machine_dia"] = $val[csf("machine_dia")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["stitch_length"] = $val[csf("stitch_length")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["color_range_id"] = $val[csf("color_range_id")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["yarn_counts"] = $val[csf("yarn_counts")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["yarn_lots"] = $val[csf("yarn_lots")];
					$transIn_barcode_ref_arr[$val[csf("prod_id")]]["yarn_prod_ids"] = $val[csf("yarn_prod_ids")];*/

					//getting transfer in barcode_no ref
					$trans_id_prod_id = $trans_id_prod_id_barcode[$val[csf("barcode_no")]];
					//$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["book"] = $val[csf("booking_no")];
					//$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["prod"] = $val[csf("prod_id")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["febric_description_id"] = $val[csf("febric_description_id")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["gsm"] = $val[csf("gsm")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["width"] = $val[csf("width")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["machine_dia"] = $val[csf("machine_dia")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["stitch_length"] = $val[csf("stitch_length")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["color_id"] = $val[csf("color_id")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["color_range_id"] = $val[csf("color_range_id")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["yarn_counts"] = $val[csf("yarn_counts")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["yarn_lots"] = $val[csf("yarn_lots")];
					$transIn_barcode_ref_from_production_arr[$trans_id_prod_id]["yarn_prod_ids"] = $val[csf("yarn_prod_ids")];

					$product_id_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
					$determination_id_arr[$val[csf("febric_description_id")]] = $val[csf("febric_description_id")];
					$color_id_arr[$val[csf("color_id")]] = $val[csf("color_id")];

				}
			}
		}

		$all_booking_no = "'".implode("','", array_filter(array_unique($all_booking_no)))."'";
		$bookCond = $all_booking_no_cond = ""; 
		//$bookCond2 = $all_booking_no_cond2 = "";
		$all_booking_no_arr=explode(",", $all_booking_no);

		if(count(array_filter(explode(",", str_replace("'", "", $all_booking_no))))>0)
		{
			//echo "**".$all_booking_no;
			if($db_type==2 && count($all_booking_no_arr)>999)
			{
				$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
				foreach($all_booking_no_arr_chunk as $chunk_arr)
				{
					$bookCond.=" a.booking_no in(".implode(",",$chunk_arr).") or ";	
					//$bookCond2.=" c.booking_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";			
				//$all_booking_no_cond2.=" and (".chop($bookCond2,'or ').")";			
				
			}
			else
			{ 	
				$all_booking_no_cond=" and a.booking_no in($all_booking_no)";  
				//$all_booking_no_cond2=" and c.booking_no in($all_booking_no)";  
			}
		}

		$productionBarcodeWithoutRcv = array_filter(array_unique($productionBarcodeWithoutRcv));
		if(count($productionBarcodeWithoutRcv)>0)
		{
			$production_barcode_nos = implode(",", $productionBarcodeWithoutRcv);
			$rcvBarcode = $rcv_barcode_cond = ""; 
			$production_barcode_nos_arr=explode(",",$production_barcode_nos);

			if($db_type==2 && count($production_barcode_nos_arr)>999)
			{
				$production_barcode_nos_arr_chunk=array_chunk($production_barcode_nos_arr,999) ;
				foreach($production_barcode_nos_arr_chunk as $chunk_arr)
				{
					$rcvBarcode.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$rcv_barcode_cond.=" and (".chop($rcvBarcode,'or ').")";			
				
			}
			else
			{ 	
				$rcv_barcode_cond=" and a.barcode_no in($production_barcode_nos)";  
			}

			//$rcv_barcode_arr = sql_select("SELECT d.barcode_no, d.qnty ,d.receive_basis, d.floor_id, d.room, d.rack, d.self,count(d.barcode_no) as no_of_roll from (select a.barcode_no, a.qnty ,b.booking_no as receive_basis, c.floor_id, c.room, c.rack, c.self  from pro_roll_details a , inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id = b.id and b.id = c.mst_id and a.entry_form in (58) and b.entry_form in (58) $rcv_barcode_cond  and a.status_active = 1 group by a.barcode_no, a.qnty ,b.booking_no, c.floor_id, c.room, c.rack, c.self) d group by d.barcode_no, d.qnty ,d.receive_basis, d.floor_id, d.room, d.rack, d.self");


			$rcv_barcode_arr = sql_select("SELECT a.barcode_no, a.qnty ,b.booking_no as receive_basis, c.floor_id, c.room, c.rack, c.self  
				from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c 
				where a.mst_id = b.id and b.id = c.mst_id and a.entry_form in (58) and b.entry_form in (58) $rcv_barcode_cond  and a.status_active = 1 
				group by a.barcode_no, a.qnty ,b.booking_no, c.floor_id, c.room, c.rack, c.self");


			foreach ($rcv_barcode_arr as $val) 
			{
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["qnty"] += $val[csf("qnty")];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["febric_description_id"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["febric_description_id"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["gsm"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["gsm"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["width"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["width"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["machine_dia"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["machine_dia"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["stitch_length"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["stitch_length"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["color_id"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["color_id"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["color_range_id"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["color_range_id"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["yarn_counts"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_counts"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["yarn_lots"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_lots"];
				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["yarn_prod_ids"] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["yarn_prod_ids"];

				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["no_of_roll"]+=1;

				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["receive_basis"] .= $val[csf("receive_basis")].",";

				$grey_receive_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["book"]][$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]["rack_self"] .= $val[csf("floor_id")]."**".$val[csf("room")]."**".$val[csf("rack")]."**".$val[csf("self")].",";

				$transIn_Rcv_BarcodeArr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];


				$product_id_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"]]=$production_barcode_ref_arr[$val[csf("barcode_no")]]["prod"];

				$determination_id_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["febric_description_id"]] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["febric_description_id"];
				$color_id_arr[$production_barcode_ref_arr[$val[csf("barcode_no")]]["color_id"]] = $production_barcode_ref_arr[$val[csf("barcode_no")]]["color_id"];
			}
		}
		// echo "<pre>";print_r($grey_receive_arr);
		
		$transfer_sql_out = sql_select("select a.from_order_id, b.from_prod_id, b.rack, b.shelf, b.y_count, b.yarn_lot,c.booking_no, sum(d.qnty) as transfer_out_qnty, count(d.barcode_no) as transfer_out_roll from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c, pro_roll_details d where a.company_id=$cbo_company_id and a.id=b.mst_id and a.from_order_id = c.id and a.id=d.mst_id and b.id=d.dtls_id and c.company_id = $cbo_company_id $booking_date_cond $bookng_no_cond $booking_year and a.transfer_criteria in(7,8) and a.entry_form in (183,180) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.rack, b.shelf, c.booking_no");

		foreach ($transfer_sql_out as $val) 
		{
			$grey_trans_out_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]]["qnty"] +=  $val[csf("transfer_out_qnty")];
			$grey_trans_out_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]]["transfer_out_roll"] +=  $val[csf("transfer_out_roll")];
		}

		$transIn_Rcv_BarcodeArr = array_filter($transIn_Rcv_BarcodeArr);
		if(count($transIn_Rcv_BarcodeArr) > 0)
		{
			$transIn_Rcv_Barcode_nos = implode(",", $transIn_Rcv_BarcodeArr);
			$rcvBarcode = $issue_barcode_cond = ""; 
			if($db_type==2 && count($transIn_Rcv_BarcodeArr)>999)
			{
				$transIn_Rcv_BarcodeArr_chunk=array_chunk($transIn_Rcv_BarcodeArr,999) ;
				foreach($transIn_Rcv_BarcodeArr_chunk as $chunk_arr)
				{
					$rcvBarcode.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$issue_barcode_cond.=" and (".chop($rcvBarcode,'or ').")";			
				
			}
			else
			{ 	
				$issue_barcode_cond=" and a.barcode_no in($transIn_Rcv_Barcode_nos)";  
			}
		}
		 
		/*$grey_issue_sql = sql_select("select b.prod_id, sum(a.qnty) as issue_qnty, count(a.id) as issue_roll ,d.booking_no from pro_roll_details a, inv_grey_fabric_issue_dtls b, inv_issue_master c, wo_non_ord_samp_booking_mst d where a.dtls_id=b.id and b.mst_id = c.id and a.mst_id = c.id and a.po_breakdown_id=d.id and c.entry_form = 61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(61) $issue_barcode_cond group by b.prod_id, d.booking_no");*/

		/*

			N.B. Sample with order booking er kaj pending thakbe data source er jonno

		*/

		$grey_issue_sql = sql_select("select b.prod_id, sum(a.qnty) as issue_qnty, count(a.id) as issue_roll ,c.booking_no, a.barcode_no from pro_roll_details a, inv_grey_fabric_issue_dtls b, wo_non_ord_samp_booking_mst c where a.dtls_id=b.id  and a.po_breakdown_id=c.id and  a.entry_form = 61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_year $bookng_no_cond $booking_date_cond $buyer_id_cond group by b.prod_id, c.booking_no ,a.barcode_no");

		foreach ($grey_issue_sql as $val) 
		{
			$gray_issue_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["qnty"] += $val[csf("issue_qnty")]; 
			$gray_issue_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["issue_roll"] += $val[csf("issue_roll")]; 
		}
		
		$transIn_Rcv_BarcodeArr = array_filter($transIn_Rcv_BarcodeArr);
		if(count($transIn_Rcv_BarcodeArr) > 0)
		{
			$transIn_Rcv_Barcode_nos = implode(",", $transIn_Rcv_BarcodeArr);
			$rcvBarcode = $issue_barcode_cond = ""; 
			if($db_type==2 && count($transIn_Rcv_BarcodeArr)>999)
			{
				$transIn_Rcv_BarcodeArr_chunk=array_chunk($transIn_Rcv_BarcodeArr,999) ;
				foreach($transIn_Rcv_BarcodeArr_chunk as $chunk_arr)
				{
					$rcvBarcode.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$issue_barcode_cond.=" and (".chop($rcvBarcode,'or ').")";			
				
			}
			else
			{ 	
				$issue_barcode_cond=" and a.barcode_no in($transIn_Rcv_Barcode_nos)";  
			}
		}

		$grey_issue_ret_sql = sql_select("select b.prod_id,a.barcode_no, c.booking_no, sum(a.qnty ) as issue_qnty, count(a.id) as iss_ret_roll from pro_roll_details a, pro_grey_prod_entry_dtls b, wo_non_ord_samp_booking_mst c where a.dtls_id=b.id and a.po_breakdown_id = c.id and a.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date_cond $bookng_no_cond $booking_year group by b.prod_id,a.barcode_no,c.booking_no");

		foreach ($grey_issue_ret_sql as $val) 
		{
			$gray_issueRet_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["qnty"] += $val[csf("issue_qnty")];
			$gray_issueRet_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["iss_ret_roll"] += $val[csf("iss_ret_roll")]; 
		}

		if($cbo_booking_type==1)
		{
			$req_qnty_sql = sql_select("select a.booking_no,b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qnty 
			from wo_booking_dtls a , wo_pre_cost_fabric_cost_dtls b
			where a.pre_cost_fabric_cost_dtls_id = b.id and a.booking_type = 4 and a.is_deleted = 0  $all_booking_no_cond
			group by a.booking_no ,b.lib_yarn_count_deter_id ");
		}
		else
		{
			$req_qnty_sql = sql_select("select a.booking_no,a.lib_yarn_count_deter_id, sum(a.grey_fabric) as req_qnty 
			from wo_non_ord_samp_booking_dtls a where a.is_deleted = 0  $all_booking_no_cond
			group by a.booking_no,a.lib_yarn_count_deter_id 
			order by booking_no desc");
		}
		

		$req_qnty_arr=array();

		foreach ($req_qnty_sql as $value) 
		{
			$req_qnty_arr[$value[csf("booking_no")]][$value[csf("lib_yarn_count_deter_id")]] +=  $value[csf("req_qnty")];
		}
		unset($req_qnty_sql);


		

		$product_id_arr = array_filter($product_id_arr);
		if(count($product_id_arr) > 0)
		{
			$product_ids = implode(",", $product_id_arr);
			$prod = $all_product_id_cond = ""; 
			$prod2 = $all_product_id_cond2 = ""; 
			if($db_type==2 && count($product_id_arr)>999)
			{
				$product_id_arr_chunk=array_chunk($product_id_arr,999) ;
				foreach($product_id_arr_chunk as $chunk_arr)
				{
					$prod.=" id in(".implode(",",$chunk_arr).") or ";	
					$prod2.=" prod_id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$all_product_id_cond.=" and (".chop($prod,'or ').")";			
				$all_product_id_cond2.=" and (".chop($prod,'or ').")";			
				
			}
			else
			{ 	
				$all_product_id_cond=" and id in($product_ids)";  
				$all_product_id_cond2=" and prod_id in($product_ids)";  
			}
		}



		$determination_id_arr = array_filter(array_unique($determination_id_arr));
		if(count($determination_id_arr) > 0)
		{
			$determination_ids = implode(",", $determination_id_arr);
			$deter = $all_determination_id_cond = ""; 
			if($db_type==2 && count($determination_id_arr)>999)
			{
				$determination_id_arr_chunk=array_chunk($determination_id_arr,999) ;
				foreach($determination_id_arr_chunk as $chunk_arr)
				{
					$deter.=" a.id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$all_determination_id_cond.=" and (".chop($deter,'or ').")";			
				
			}
			else
			{ 	
				$all_determination_id_cond=" and a.id in($determination_ids)";  
			}
		}

		$color_id_arr = array_filter(array_unique($color_id_arr));
		if(count($color_id_arr) > 0)
		{
			$color_ids = implode(",", $color_id_arr);
			$colorId = $all_color_id_cond = ""; 
			if($db_type==2 && count($color_id_arr)>999)
			{
				$color_id_arr_chunk=array_chunk($color_id_arr,999) ;
				foreach($color_id_arr_chunk as $chunk_arr)
				{
					$colorId.=" id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$all_color_id_cond.=" and (".chop($colorId,'or ').")";			
				
			}
			else
			{ 	
				$all_color_id_cond=" and id in($color_ids)";  
			}
		}

		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_id_cond", "id", "color_name"  );


		$product_array=array();	
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id in (13,1) and company_id=$cbo_company_id and status_active=1 and is_deleted=0 $all_product_id_cond";
		$prod_query_sql=sql_select($prod_query);
		if(count($prod_query_sql)>0)
		{
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
				$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
				$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
				$product_array[$row[csf('id')]]['color']=$row[csf('color')];
			}
		}

		$transaction_date_array=array();
		$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 $all_product_id_cond2 group by prod_id";
		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);


		
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_determination_id_cond";
		$data_deter_array=sql_select($sql_deter);
		if(count($data_deter_array)>0)
		{
			foreach( $data_deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
					
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$row[csf('construction')];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
					
				}
			}
		}
		unset($data_deter_array);

		$all_receive_data_for_row = array();
		foreach ($grey_receive_arr as $book => $bookdata) 
		{
			foreach ($bookdata as $prod => $row) 
			{
				$all_receive_data_for_row[$book][$prod]["qnty"] = $row["qnty"];
				$transfer_no_in_prod_array[$prod] = $prod;
			}
		}
		foreach ($grey_trans_in_arr as $tr_book => $tr_bookdata) 
		{
			foreach ($tr_bookdata as $tr_prod => $val) 
			{
				if($transfer_no_in_prod_array[$tr_prod] == "")
				{
					$all_receive_data_for_row[$tr_book][$tr_prod]["tran_in_prod_qnty"] = $val["qnty"];
				}
				
			}
		}

		// echo "<pre>";print_r($all_receive_data_for_row);
		$booking_tr_span_arr = array();

		foreach ($all_receive_data_for_row as $Abook => $Abookdata) 
		{
			$prod_count = 0; 
			foreach ($Abookdata as $Aprod => $row) 
			{
				$rcvQntyForChk =  $row["qnty"] + $gray_issueRet_arr[$Abook][$Aprod]["qnty"]+$grey_trans_in_arr[$Abook][$Aprod]["qnty"];
				$issueQntyForChk = $gray_issue_arr[$Abook][$Aprod]["qnty"] + $grey_trans_out_arr[$Abook][$Aprod]["qnty"];
				$stockQntyChk = $rcvQntyForChk - $issueQntyForChk;


				//echo "$rcvQntyForChk - $issueQntyForChk =$stockQntyChk <br> rcv== ".$row['qnty'] ."+". $gray_issueRet_arr[$Abook][$Aprod]['qnty'] ."+". $grey_trans_in_arr[$Abook][$Aprod]['qnty'] ."<br>"."issue==".$gray_issue_arr[$Abook][$Aprod]["qnty"] ."+". $grey_trans_out_arr[$Abook][$Aprod]["qnty"]."<br>";


				if($cbo_value_with== 1 || ($cbo_value_with== 2 && $stockQntyChk>0))
				{
					$prod_count++;
					//echo " *$prod_count* <br>";
					/*if($row["tran_in_prod_qnty"]){
						$detarmination_id= $transIn_barcode_ref_arr[$Aprod]["febric_description_id"];
					}else{
						$detarmination_id=  $grey_receive_arr[$Abook][$Aprod]["febric_description_id"];
					}*/


					//$total_rcv_qnty_arr[$Abook][$detarmination_id] += $row["qnty"] + $gray_issueRet_arr[$Abook][$Aprod]["qnty"]+$grey_trans_in_arr[$Abook][$Aprod]["qnty"]+$row["tran_in_prod_qnty"];
					//$total_issue_qnty_arr[$Abook][$detarmination_id] +=  $gray_issue_arr[$Abook][$Aprod]["qnty"] + $grey_trans_out_arr[$Abook][$Aprod]["qnty"];
				}

			}
			$booking_tr_span_arr[$Abook] = $prod_count;
		}
		$floor_room_rack_array=return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
		
		ob_start();
		//print_r($booking_tr_span_arr);
		//$cbo_value_with
		?>
		<style type="text/css">
			.wordBreakWrap {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<fieldset>
			<table cellpadding="0" cellspacing="0" width="2770">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2950" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th colspan="12">Fabric Details</th>
						<th colspan="3">Used Yarn Details</th>
						<th width="120" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
						<th width="80" rowspan="2">Req. Qty.</th>
						<th colspan="5">Receive Details</th>
						<th colspan="5">Issue Details</th>
						<th colspan="9">Stock Details</th>
					</tr>
					<tr>
						<th width="120">Booking No.</th>
						<th width="100">Buyer</th>
						<th width="80">Job No.</th>
						<th width="80">Style Ref</th>
						
						<th width="150">Constraction</th>
						<th width="120">Composition</th>
						<th width="60">GSM</th>
						<th width="60">F/Dia</th>
						<th width="60">M/Dia</th>
						<th width="60">Stich Length</th> 
						<th width="80">Dyeing Color</th>
						<th width="80">Color Range</th>

						<th width="60">Y. Count</th>
						<th width="80">Y. Brand</th>
						<th width="80">Y. Lot</th>

						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="60">Recv. Roll</th>

						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="60">Issue Roll</th>

						<th width="80">Stock Qty.</th>
						<th width="50">Roll Qty.</th>
						<th width="50">Floor</th>
						<th width="50">Room</th>
						<th width="50">Rack</th>
						<th width="50">Shelf</th>
						<th width="50">DOH</th>
						
						<th width="70">Recv. Balance</th>
						<th width="70">Issue Balance</th>
					</tr>
				</thead>
			</table>
			<div style="width:2970px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2950" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
					<tbody>
					<?
					//echo "<pre>";
					//print_r($req_qnty_arr);die;
						$i=$m=1;
						foreach ($all_receive_data_for_row as $booking_no => $booking_data) 
						{
							$booking_span = $booking_tr_span_arr[$booking_no]; $k=1;
							foreach ($booking_data as $prod_id => $val) 
							{
								$stockChkRcv = $val["qnty"] + $gray_issueRet_arr[$booking_no][$prod_id]["qnty"]+$grey_trans_in_arr[$booking_no][$prod_id]["qnty"];
								$stockChkIss = $gray_issue_arr[$booking_no][$prod_id]["qnty"]+$grey_trans_out_arr[$booking_no][$prod_id]["qnty"];
								$stockTdChk = $stockChkRcv-$stockChkIss;

								if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($cbo_value_with== 1 || ($cbo_value_with== 2 && $stockTdChk>0))
								{
									
									$transfer_no_in_prod_array[$prod_id] = $prod_id;

									$machine_dia=$stitch_length=$color_id=$color_range_id=$yarn_counts=$yarn_prod_ids=$yarn_lots=$receive_basis=$rack_self_rcv=$no_of_roll="";

									if($val["tran_in_prod_qnty"])
									{
										
										/*$detarmination_id = $product_array[$prod_id]['detarmination_id'];
										$gsm = $product_array[$prod_id]['gsm'];
										$width = $product_array[$prod_id]['dia_width'];*/

										$detarmination_id= $transIn_barcode_ref_from_production_arr[$prod_id]["febric_description_id"];
										$gsm= $transIn_barcode_ref_from_production_arr[$prod_id]["gsm"];
										$width= $transIn_barcode_ref_from_production_arr[$prod_id]["width"];
										$machine_dia= $transIn_barcode_ref_from_production_arr[$prod_id]["machine_dia"];
										$stitch_length= $transIn_barcode_ref_from_production_arr[$prod_id]["stitch_length"];
										$color_id= $transIn_barcode_ref_from_production_arr[$prod_id]["color_id"];
										$color_range_id= $transIn_barcode_ref_from_production_arr[$prod_id]["color_range_id"];
										$yarn_prod_ids= $transIn_barcode_ref_from_production_arr[$prod_id]["yarn_prod_ids"];
										$yarn_lots= $transIn_barcode_ref_from_production_arr[$prod_id]["yarn_lots"];

										/*$detarmination_id= $transIn_barcode_ref_arr[$prod_id]["febric_description_id"];
										$gsm= $transIn_barcode_ref_arr[$prod_id]["gsm"];
										$width= $transIn_barcode_ref_arr[$prod_id]["width"];
										$machine_dia= $transIn_barcode_ref_arr[$prod_id]["machine_dia"];
										$stitch_length= $transIn_barcode_ref_arr[$prod_id]["stitch_length"];
										$color_id= $transIn_barcode_ref_arr[$prod_id]["color_id"];
										$color_range_id= $transIn_barcode_ref_arr[$prod_id]["color_range_id"];
										$yarn_prod_ids= $transIn_barcode_ref_arr[$prod_id]["yarn_prod_ids"];
										$yarn_lots= $transIn_barcode_ref_arr[$prod_id]["yarn_lots"];*/
									}
									else
									{
										$detarmination_id=  $grey_receive_arr[$booking_no][$prod_id]["febric_description_id"];
										$gsm=  $grey_receive_arr[$booking_no][$prod_id]["gsm"];
										$width=  $grey_receive_arr[$booking_no][$prod_id]["width"];
										$machine_dia=  $grey_receive_arr[$booking_no][$prod_id]["machine_dia"];
										$stitch_length=  $grey_receive_arr[$booking_no][$prod_id]["stitch_length"];
										$color_id=  $grey_receive_arr[$booking_no][$prod_id]["color_id"];
										$color_range_id=  $grey_receive_arr[$booking_no][$prod_id]["color_range_id"];
										$yarn_counts=  $grey_receive_arr[$booking_no][$prod_id]["yarn_counts"];
										$yarn_prod_ids=  $grey_receive_arr[$booking_no][$prod_id]["yarn_prod_ids"];
										$yarn_lots=  $grey_receive_arr[$booking_no][$prod_id]["yarn_lots"];
										$receive_basis=  $grey_receive_arr[$booking_no][$prod_id]["receive_basis"];
										$rack_self_rcv=  $grey_receive_arr[$booking_no][$prod_id]["rack_self"];
										$no_of_roll=  $grey_receive_arr[$booking_no][$prod_id]["no_of_roll"];
									}


									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $m;?>','<? echo $bgcolor;?>')" id="tr<? echo $m;?>">
										<? 
										if($k==1)
										{
										?>
											<td width="30" rowspan="<? echo $booking_span;?>" align="center"><? echo $i;?></td>
											<td width="120" rowspan="<? echo $booking_span;?>" align="center">
												<p class="wordBreakWrap">
													<? 
														//$job_buyer_style[$booking_no]["fabric_source"];
														$production_po_ids = $job_buyer_style[$booking_no]["po_ids"];
														$production_po_ids = implode(",",array_filter(array_unique(explode(",",chop($production_po_ids,",")))));
													?>
													<a href="##" onClick="openpage_fabric_booking('<? echo $job_buyer_style[$booking_no]["is_approved"]."_".$job_buyer_style[$booking_no]["item_category"]."_".$booking_no."_".$job_buyer_style[$booking_no]["job_no"]."_".$production_po_ids."_".$job_buyer_style[$booking_no]["fabric_source"];?>');">
														<? echo $booking_no;?>
													</a>
												</p>
											</td>
											<td width="100" rowspan="<? echo $booking_span;?>" align="center"><p class="wordBreakWrap"><? echo $buyer_arr[$job_buyer_style[$booking_no]["buyer_id"]];?></p></td>
											<td width="80" rowspan="<? echo $booking_span;?>" align="center"><p class="wordBreakWrap"><? echo $job_buyer_style[$booking_no]["job_no"];?></p></td>
											<td width="80" rowspan="<? echo $booking_span;?>" align="center"><p class="wordBreakWrap"><? echo $job_buyer_style[$booking_no]["style_ref_no"];?></p></td>
										<? 
											$i++;
											}
										?>
											<td width="150" align="center">
												<p class="wordBreakWrap"><? 
												
												echo $constructionArr[$detarmination_id];
												?></p>
											</td>
											<td width="120" align="center">
												<p class="wordBreakWrap"><? 
													echo $composition_arr[$detarmination_id];
												?></p>
											</td>
											<td width="60" align="center"><? echo $gsm;?></td>
											<td width="60" align="center"><p class="wordBreakWrap"><? echo $width;?></p></td>
											<td width="60" align="center"><p class="wordBreakWrap"><? echo $machine_dia;?></p></td>
											<td width="60" align="center"><p class="wordBreakWrap"><? echo $stitch_length;?></p></td>
											<td width="80" align="center"><p class="wordBreakWrap"><? echo $color_arr[$color_id];?></p></td>
											<td width="80" align="center"><p class="wordBreakWrap"><? echo $color_range[$color_range_id];?></p></td>
											<td width="60" align="center"><p class="wordBreakWrap">
												<? 
													$count_id=explode(',',$yarn_counts); $count_val='';
													foreach ($count_id as $val1)
													{
														if($val>0){ if($count_val=='') $count_val=$count_arr[$val1]; else $count_val.=",".$count_arr[$val1]; }
													}
													echo $count_val;
												?></p>
											</td>
											
											<td width="80" align="center"><p class="wordBreakWrap">
												<? 
													$y_prod_id=explode(',',$yarn_prod_ids); $brand_val='';
													foreach ($y_prod_id as $val2)
													{
														if($val>0){ if($brand_val=='') $brand_val=$product_array[$val2]['brand']; else $brand_val.=",".$product_array[$val2]['brand']; }
													}
													echo implode(",",array_filter(explode(",", $brand_val)));
												?></p>
											</td>

											<td width="80" align="center"><p class="wordBreakWrap"><? echo $yarn_lots;?></p></td>

										<td width="120"><p class="wordBreakWrap"><? echo implode(",",array_unique(explode(",",chop($receive_basis,","))));?></p></td>
										<? 
										//if($k==1)
										//{
											?>
												<td width="80" align="right" ><p class="wordBreakWrap"><? echo number_format($req_qnty_arr[$booking_no][$detarmination_id],2);?></p></td>
											<?
											$grand_tot_req += $req_qnty_arr[$booking_no][$detarmination_id];
										//}
										?>
										<td width="80" align="right"><? echo $val["qnty"];?></td>
										<td width="80" align="right"><? echo $gray_issueRet_arr[$booking_no][$prod_id]["qnty"];?></td>
										<td width="80" align="right">
											<? 
												echo number_format($grey_trans_in_arr[$booking_no][$prod_id]["qnty"],2);
											?>
										</td>
										<td width="80" align="right">
											<? 
											$total_rcv_qnty = $val["qnty"] + $gray_issueRet_arr[$booking_no][$prod_id]["qnty"]+$grey_trans_in_arr[$booking_no][$prod_id]["qnty"];
											echo number_format($total_rcv_qnty,2);
											?>
										</td>
										<td width="60" align="right" title="<? echo $no_of_roll."_".$gray_issueRet_arr[$booking_no][$prod_id]['iss_ret_roll'];?>">
											<? 
											$rcv_roll = $no_of_roll+ $gray_issueRet_arr[$booking_no][$prod_id]["iss_ret_roll"] + $grey_trans_in_arr[$booking_no][$prod_id]["transfer_in_roll"];
												echo $rcv_roll;
											?>
										</td>
										<td width="80" align="right" title="<? echo $booking_no."=".$prod_id;?>">
											<? 
												echo number_format($gray_issue_arr[$booking_no][$prod_id]["qnty"],2);
											?>
										</td>
										<td width="80"><? ?></td>
										<td width="80" align="right"><? echo number_format($grey_trans_out_arr[$booking_no][$prod_id]["qnty"],2);?></td>
										<td width="80" align="right">
											<? 
											$total_issue_qnty = $gray_issue_arr[$booking_no][$prod_id]["qnty"]+$grey_trans_out_arr[$booking_no][$prod_id]["qnty"];
												echo number_format($total_issue_qnty,2);
											?>
										</td>

										<td width="60" align="right">
											<? 
											$issue_roll = $gray_issue_arr[$booking_no][$prod_id]["issue_roll"] + $grey_trans_out_arr[$booking_no][$prod_id]["transfer_out_roll"];
												echo $issue_roll;
											?>
										</td>
										<td width="80" align="right">
											<? 
											$stock_qnty = $total_rcv_qnty - $total_issue_qnty;
												echo number_format($stock_qnty,2);
											?>
										</td>
										<td width="50" align="right">
											<? 
											$stock_roll = $rcv_roll - $issue_roll;
											echo $stock_roll;
											?>
										</td>
										<td width="50" align="center"><p>
											<? 
											$floor_nos = "";$room_nos = "";$rack_nos = "";$self_nos= "";
											$rack_self_arr=array_filter(array_unique(explode(',',chop($rack_self_rcv,","))));
											// echo "<pre>"; print_r($rack_self_arr);
											foreach ($rack_self_arr as $rs) 
											{
												// echo $rs.'<br>';
												$rack_self = explode("**", $rs);
												
												$floor_nos .= $floor_room_rack_array[$rack_self[0]].",";
												$room_nos .= $floor_room_rack_array[$rack_self[1]].",";
												$rack_nos .= $floor_room_rack_array[$rack_self[2]].",";
												$self_nos .= $floor_room_rack_array[$rack_self[3]].",";

												/*if($floor_nos=="") $floor_nos = $floor_room_rack_array[$rack_self[0]].",";else $floor_nos .= $floor_room_rack_array[$rack_self[0]];
												if($room_nos=="") $room_nos = $floor_room_rack_array[$rack_self[1]].","; else $room_nos .= $floor_room_rack_array[$rack_self[1]];
												if($rack_nos=="") $rack_nos = $floor_room_rack_array[$rack_self[2]].","; else $rack_nos .= $floor_room_rack_array[$rack_self[2]];
												if($self_nos=="") $self_nos = $floor_room_rack_array[$rack_self[3]].","; else $self_nos .= $floor_room_rack_array[$rack_self[3]];*/
											}
											echo chop($floor_nos,",");
											?>
										</p></td>
										<td width="50" align="center"><p>
											<? 
											echo chop($room_nos,",");
											?></p>
										</td>
										<td width="50" align="center"><p>
											<? 
											echo chop($rack_nos,",");
											?></p>
										</td>
										<td width="50" align="center"><p>
											<? 
											echo chop($self_nos,",");
											?></p>
										</td>
										<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prod_id]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
										?>
										<td width="50" align="center"><p><? echo $daysOnHand;?></p></td>
										<? 
										//if($k==1)
										//{
											$rcv_balance = $req_qnty_arr[$booking_no][$detarmination_id] - $total_rcv_qnty;//$total_rcv_qnty_arr[$booking_no][$detarmination_id];
											$iss_balance = $req_qnty_arr[$booking_no][$detarmination_id] - $total_issue_qnty;//$total_issue_qnty_arr[$booking_no][$detarmination_id];
											?>
											<td width="70"  align="right"><p><? echo number_format($rcv_balance,2);?></p></td>
											<td width="70"  align="right"><p><? echo number_format($iss_balance,2);?></p></td>
											<? 
											$grand_rcv_balance += $rcv_balance;
											$grand_iss_balance += $iss_balance;
										//}	
										
										?>							
									</tr>
									<?
									$grand_rcv_qnty += $val["qnty"];
									$grand_iss_ret_qnty += $gray_issueRet_arr[$booking_no][$prod_id]["qnty"];
									$grand_trans_in_qnty += $grey_trans_in_arr[$booking_no][$prod_id]["qnty"];
									$grand_total_rcv += $total_rcv_qnty;
									$grand_rcv_roll += $rcv_roll;
									$grand_issue_qnty += $gray_issue_arr[$booking_no][$prod_id]["qnty"];
									$grand_trans_out_qnty +=$grey_trans_out_arr[$booking_no][$prod_id]["qnty"];
									$grand_total_issue_qnty += $total_issue_qnty;
									$grand_issue_roll += $issue_roll;
									$grand_stock_qnty += $stock_qnty;
									$grand_stock_roll += $stock_roll;
									$k++;$m++;
								}
								
							}
							
						}

						?>
					</tbody>
				</table>
			</div>
			<table width="2950" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" > 
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						
						<th width="150">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th> 
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($grand_tot_req,2);?></th>
						<th width="80" align="right"><? echo number_format($grand_rcv_qnty,2);?></th>
						<th width="80" align="right"><? echo number_format($grand_iss_ret_qnty,2);?></th>
						<th width="80" align="right"><? echo number_format($grand_trans_in_qnty,2);?></th>
						<th width="80" align="right"><? echo number_format($grand_total_rcv,2);?></th>
						<th width="60" align="right"><? echo number_format($grand_rcv_roll,2);?></th>

						<th width="80" align="right"><? echo number_format($grand_issue_qnty,2);?></th>
						<th width="80"></th>
						<th width="80" align="right"><? echo number_format($grand_trans_out_qnty,2);?></th>
						<th width="80" align="right"><? echo number_format($grand_total_issue_qnty,2);?></th>
						<th width="60" align="right"><? echo number_format($grand_issue_roll,2);?></th>

						<th width="80" align="right"><? echo number_format($grand_stock_qnty,2);?></th>
						<th width="50" align="right"><? echo number_format($grand_stock_roll,2);?></th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						
						<th width="70" align="right"><? echo number_format($grand_rcv_balance,2);?></th>
						<th width="70" align="right"><p class="wordBreakWrap"><? echo number_format($grand_iss_balance,2);?></p></th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<?



		$html = ob_get_contents();
		ob_clean();

		foreach (glob("*.xls") as $filename) {

			@unlink($filename);
		}

		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html####$filename####$rpt_type"; 
		exit();
	}

	if($action=="fabric_booking_popup")
	{
		echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		?>
		<fieldset style="width:890px">
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
				<thead>
					<th width="40">SL</th>
					<th width="60">Booking No</th>
					<th width="50">Year</th>
					<th width="60">Type</th>
					<th width="80">Booking Date</th>
					<th width="90">Color</th>
					<th width="110">Fabric</th>
					<th width="150">Composition</th>
					<th width="70">GSM</th>
					<th width="70">Dia</th>
					<th>Grey Req. Qty.</th>
				</thead>
			</table>
			<div style="width:100%; max-height:320px; overflow-y:scroll">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		        else $year_field="";//defined Later
		        
		        $i=1; $tot_grey_qnty=0;
		        $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
		       //echo $sql;//die;
		        $result= sql_select($sql);
		        foreach($result as $row)
		        {
		        	if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
		        	
		        	if($row[csf('booking_type')]==4) 
		        	{
		        		$booking_type="Sample";
		        	}
		        	else
		        	{
		        		if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
		        	}
		        	?>
		        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		        		<td width="40"><? echo $i; ?></td>
		        		<td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
		        		<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
		        		<td width="60" align="center"><p><? echo $booking_type; ?></p></td>
		        		<td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
		        		<td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
		        		<td width="110"><p><? echo $row[csf('construction')]; ?></p></td>
		        		<td width="150"><p><? echo $row[csf('copmposition')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
		        		<td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
		        	</tr>
		        	<? 
		        	$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
		        	$i++;
		        } 
		        ?>
		        <tfoot>
		        	<th colspan="10">Total</th>
		        	<th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
		        </tfoot>
		    </table>
		</div> 
	</fieldset>
	<?
	exit();}
	if($action=="grey_recv_popup")
	{
		echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		$companyID= $companyID;
		$orderID=$orderID;
		$programNo=$programNo;
		$prodID=$prodID;
		$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
		$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
		$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
		$buyer_name_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
		$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');

		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<?
							if ($type==1) {$tbl_title='Grey Fabrics Receive Details';}else{$tbl_title='Grey Fabrics Issue Details';}
							?>
							<th colspan="12"><b><? echo $tbl_title; ?></b></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="70"><?if ($type==1) { echo 'Receive ID';}else{echo 'Issue ID';} ?></th>
							<th width="120"><?if ($type==1) { echo 'Receive Date';}else{echo 'Issue Date';} ?></th>
							<th width="200">Fabric Des</th>
							<th width="80">Store</th>
							<th width="80">Room</th>
							<th width="100">Rack </th>
							<th width="100">Shelf</th>
							<th width="60">Bin</th>
							<th width="60">UOM</th>
							<th width="60">Qty</th>
							<th>No of Roll</th>
						</tr>
					</thead>
				</table>
				<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body">
						<?
						if ($type==1) 
						{
							$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id  
								from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d  
								where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
						}
						else
						{
							$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.entry_form in(16,61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
						}				



						$i=1;
						foreach ($programData as $row) {

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
								<td width="120"  align="center"><? echo $row[csf('sys_no_date')];// ?></td>
								<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
								<td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
								<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="60" align="right"><? echo $row[csf('grey_receive_qnty')]; ?>&nbsp;</td>
								<td align="right"><? echo $row[csf("no_of_roll")]; ?>&nbsp;</td> 

							</tr>
							<?
							$total_recv_qty+=$row[csf('grey_receive_qnty')];
							$i++;
						}
						?>
						<tfoot>
							<tr>
								<th colspan="10" align="right">Total</th>
								<th align="right"><? echo number_format($total_recv_qty,2); ?></th>
								<th align="right"></th>
							</tr>

						</tfoot>
					</table>
				</div>
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<?
							if ($type==1) {$tbl_title='Grey Issue Return Details';}else{$tbl_title='Grey Receive Return Details';}
							?>
							<th colspan="12"><b><? echo $tbl_title; ?></b></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="70"><?if ($type==1) { echo 'Issue Return ID';}else{echo 'Receive Return ID';} ?></th>
							<th width="120"><?if ($type==1) { echo 'Issue Date';}else{echo 'Receive Date';} ?></th>
							<th width="200">Fabric Des</th>
							<th width="80">Store</th>
							<th width="80">Room</th>
							<th width="100">Rack </th>
							<th width="100">Shelf</th>
							<th width="60">Bin</th>
							<th width="60">UOM</th>
							<th width="60">Qty</th>
							<th>No of Roll</th>
						</tr>
					</thead>
				</table>
				<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body2">
					<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body2">
						<?	
						if ($type==1) 
						{
							$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no   
								from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d  
								where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(84,51) and c.trans_id <>0 and c.entry_form in (84,51) and c.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
						}
						else
						{			
							$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and a.entry_form in(45) and c.trans_id <>0 and c.entry_form in (45) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=1 and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id ");
						}

						$ii=1;
						foreach ($programData as $row) {
							$store_arr[$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
								<td width="30"><? echo $ii; ?></td>
								<td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
								<td width="120"  align="center"><? echo $row[csf('sys_number_date')];// ?></td>
								<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
								<td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
								<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="60" align="right"><? echo $row[csf('grey_issue_rtn_qnty')]; ?>&nbsp;</td>
								<td align="right"><? echo $row[csf("roll_no")]; ?>&nbsp;</td> 

							</tr>
							<?
							$total_issue_rtn_qty+=$row[csf('grey_issue_rtn_qnty')];
							$ii++;
						}
						?>
						<tfoot>
							<tr>
								<th colspan="10" align="right">Total</th>
								<th align="right"><? echo number_format($total_issue_rtn_qty,2); ?></th>
								<th align="right"></th>
							</tr>

						</tfoot>
					</table>
				</div>
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<?
							if ($type==1) {$tbl_title='Grey Transfer In Details';}else{$tbl_title='Grey Transfer Out Details';}
							?>
							<th colspan="12"><b><? echo $tbl_title; ?></b></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="70">Transfer ID</th>
							<th width="120">Receive Date</th>
							<th width="200">Fabric Des</th>
							<th width="80">Store</th>
							<th width="80">Room</th>
							<th width="100">Rack </th>
							<th width="100">Shelf</th>
							<th width="60">Bin</th>
							<th width="60">UOM</th>
							<th width="60">Qty</th>
							<th>No of Roll</th>
						</tr>
					</thead>
				</table>
				<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
					<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
						<?	
						if ($type==1) 
						{
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.to_trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");  
							$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no ,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=1 and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");    


						}			
						else
						{
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");

							$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d  where a.id=b.mst_id and b.trans_id=c.trans_id and  b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");
						}



						$iii=1;
						foreach ($programData as $row) {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
								<td width="30"><? echo $iii; ?></td>
								<td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="120"  align="center"><? echo $row[csf('transfer_date')];// ?></td>
								<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
								<td width="80"><div style="word-break:break-all"><? if($row[csf('store_id')]>0){echo $store_name_arr[$row[csf('to_store_id')]];}else{echo  $store_name_arr[$store_arr[$row[csf('prod_id')]]['store_id']];} ?></div></td>                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
								<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
								<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</td>
								<td width="60" align="right"><? echo $row[csf('roll_qnty')]; ?>&nbsp;</td>
								<td align="right"><? echo $row[csf("roll_no")]; ?>&nbsp;</td> 

							</tr>
							<?
							$total_trnsf_qty+=$row[csf('roll_qnty')];
							$iii++;
						}
						?>
						<tfoot>
							<tr>
								<th colspan="10" align="right">Total</th>
								<th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
								<th align="right"></th>
							</tr>

						</tfoot>
					</table>
				</div>

			</div>
		</fieldset>	

		<? 
		die;

		foreach( $programData as $row )
		{
			$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
		}

		$i=1; $product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
		$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84,110,183) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
		$result_trans=sql_select( $sql_trans );
		foreach ($result_trans as $row)
		{
			$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
			$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
		}

		foreach($dataArray as $row)
		{ 
						//$issue_id_arr[]=$row[csf('id')];
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";	
			$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
							//$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
			$yarn_count_value = "";
			foreach ($y_count as $val) {
				if ($val > 0) {
					if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
				}
			}

			$po_id=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["po_id"];
			$barcode_no=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["barcode_no"];
			$no_of_roll=$roll_no_data_arr2[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["no_of_roll"];
							//echo $po_id.'dff';
			$brand_value=$brand_name_arr[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["brand_id"]];
			$body_part_name=$body_part[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["body_part_id"]];
			$gsm=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["gsm"] ;
			$width=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["width"] ;



			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
				<td width="30"><? echo $i; ?></td>
				<td width="70"><p><? echo $row[csf('issue_date')]; ?></p></td>
				<td width="120"><? echo $row[csf('issue_number')];// ?></td>
				<td width="150"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
				<td width="80"><div style="word-break:break-all"><? echo $buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
				<td width="80"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["style"]; ?></div></td>
				<td width="100"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["job_no"] ; ?></div></td>
				<td width="100"><div style="word-break:break-all"><? echo $body_part_name; ?></div></td>
				<td width="60"><p><? echo $row[csf('stitch_length')]; ?></p>&nbsp;</td>
				<td width="60"><? echo $gsm; ?>&nbsp;</td>
				<td width="60"><? echo $width; ?>&nbsp;</td>
				<td><? echo $dya_gauge_arr[$row[csf("machine_id")]]["dia_width"] ?>&nbsp;</td> 

			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<tr>
				<th colspan="10" align="right">Total</th>
				<th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
			</tr>

		</tfoot>
	</table>
</div>	
</div>
</fieldset>
<script>
	setFilterGrid("table_body",-1);
</script>
<br>


<?
exit();
}
