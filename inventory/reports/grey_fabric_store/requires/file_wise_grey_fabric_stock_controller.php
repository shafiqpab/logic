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

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array, selected_name = new Array();

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
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
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit();
}


if ($action == "booking_no_popup")
{
	//echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
								</td>
								<td align="center">
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'file_wise_grey_fabric_stock_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

 	/*$sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";*/
	$sql= "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a where $company $buyer $booking_no $booking_date and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 ";

	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));
	$txt_style_ref = trim(str_replace("'","",$txt_style_ref));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_style_ref!="") $style_cond=" and b.style_ref_no LIKE '%".trim($txt_style_ref)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond $style_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	order by a.id";


	//echo $sql; die;

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')]."_".$row[csf('style_ref_no')];
			$poIds.=$row[csf('id')].",";
			$all_po_id_arr[$row[csf("id")]] = $row[csf("id")];
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$all_po_id_arr = array_filter($all_po_id_arr);
	if(!empty($all_po_id_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($all_po_id_arr as $poId)
		{
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
			
		}
		oci_commit($con);
	}

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
			$otot_po_cond.=" a.to_order_id in($ids) or ";
			$stst_po_cond.=" b.to_order_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
		$otot_po_cond=$poIds_cond_pre.chop($otot_po_cond,'or ').$poIds_cond_suff;
		$stst_po_cond=$poIds_cond_pre.chop($stst_po_cond,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
		$otot_po_cond=" and a.to_order_id in($poIds)";
		$stst_po_cond=" and b.to_order_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );

	
	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, tmp_po_id c where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.po_id and c.user_id=$user_id group by b.po_break_down_id", "po_id", "grey_req_qnty");

	$delv_arr=return_library_array("select a.barcode_num, a.grey_sys_id from pro_grey_prod_delivery_dtls a,tmp_po_id b where a.order_id=b.po_id and b.user_id=$user_id and a.entry_form=56", "barcode_num", "grey_sys_id");


	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);


	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0 $transfer_date  $stst_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0";

	//$poIds_cond_roll
	//echo $query;
	
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		//$ref_barcode_arrxx[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
	}

	if(!empty($ref_barcode_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($ref_barcode_arr as $barcodeNO)
		{
			execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
			
		}
		oci_commit($con);
	}

	if(!empty($ref_barcode_arr))
	{	
		$recvDataArrTrans=array();$recvDataArr=array();
		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		//echo $sqlRecvT;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

			
		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(!empty($yarn_prod_id_arr))
		{
			$con = connect();
			$r_id33=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
			if($r_id33)
			{
				oci_commit($con);
			}

			
			$con = connect();
			foreach($yarn_prod_id_arr as $prodID)
			{
				execute_query("INSERT INTO TMP_PROD_ID(PROD_ID,USERID) VALUES(".$prodID.", ".$user_id.")");
				
			}
			oci_commit($con);
		}

		// N.B.: This ref is AKH Live, mother barcode 21020199824, child barcode 21020222176, after split 21020222176 transfer to another order and issue, after issue split mother barcode 21020222176 child barcode 21020239799. when search by transfer in po or int.ref: 6011 then child barcode 21020239799 qty 4 dot show, so below query is comment
		/*$split_chk_sql = sql_select("SELECT d.barcode_no , d.qnty from tmp_barcode_no e, pro_roll_split c, pro_roll_details d where e.barcode_no=c.barcode_no and e.userid=$user_id and c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}



			$split_barcode_arr = array_filter($split_barcode_arr);
			if(!empty($split_barcode_arr))
			{
				$con = connect();
				$r_id2222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
				if($r_id2222)
				{
					oci_commit($con);
				}
				$con = connect();
				foreach($split_barcode_arr as $barcodeNO)
				{
					execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
					
				}
				oci_commit($con);
			}

			
			$split_ref_sql = sql_select("SELECT a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode 
			from tmp_barcode_no c, pro_roll_details a, pro_roll_details b where a.barcode_no=c.barcode_no and c.userid=$user_id and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
				}
			}
		}*/

		//=========================================================================================
		// N.B.: This ref is AKH Live, mother barcode 21020199824, child barcode 21020222176, after split 21020222176 transfer to another order and issue, after issue split mother barcode 21020222176 child barcode 21020239799. when search by transfer in po or int.ref: 6011 then child barcode 21020239799 qty 4 dot show, so below query is comment
		
		$split_chk_sql = sql_select("SELECT b.barcode_no as mother_barcode, c.qnty from pro_roll_split b, pro_roll_details c, tmp_barcode_no d, tmp_po_id e where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.barcode_no = d.barcode_no and C.PO_BREAKDOWN_ID=e.po_id and b.status_active=1 and c.status_active=1 and d.userid= $user_id and e.user_id= $user_id");
		if(!empty($split_chk_sql))
        {
            $mother_barcode_after_issue_arr=array();
            foreach ($split_chk_sql as $val)
            {
                // $split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
                $mother_barcode_after_issue_arr[$val[csf("mother_barcode")]] += $val[csf("qnty")];
            }
        }
        //=========================================================================================

		//$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			/*$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}*/

			//$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a where a.status_active = 1 $yarn_prod_id_cond","id","yarn_type");
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a,tmp_prod_id b where a.status_active = 1 and a.id=b.prod_id and b.userid=$user_id","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		}
		unset($recvDataT);
	}

	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($ref_febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($ref_febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}

	

	// $iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
	// union all
	// SELECT a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,tmp_po_id d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id=d.po_id and d.user_id=$user_id and c.booking_without_order = 0 
	// union all
	// SELECT b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	// where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.from_order_id=d.po_id and d.user_id=$user_id and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	// group by c.barcode_no, b.from_order_id
	// union all
	// SELECT a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	// where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id=d.po_id and d.user_id=$user_id and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
	// group by c.barcode_no, a.from_order_id ");

	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
	union all
	SELECT a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and c.booking_without_order = 0 
	union all
	SELECT b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id
	union all
	SELECT a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
	group by c.barcode_no, a.from_order_id ");

	//$poIds_cond_trans_roll
	// $ctct_po_cond
	//$otst_po_cond

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();

	foreach($iss_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		/*$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"];
		}*/

		// $iss_qty_arr[$ref_file][$data_prod] +=$row[csf("qnty")];
		$split_issue_qty=$mother_barcode_after_issue_arr[$row[csf("barcode_no")]];
		$iss_qty_arr[$ref_file][$data_prod] +=$row[csf("qnty")]+$split_issue_qty;

		$issue_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";

	}
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll");
	foreach($iss_rtn_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);


	$ref_file="";$data_prod="";
	foreach($data_array as $row)
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];


			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			//$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);

	$con = connect();	
	
	$r_id111=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_id ");
	if($r_id111)
	{
		oci_commit($con);
	}
	$r_id222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	if($r_id222)
	{
		oci_commit($con);
	}
	$r_id333=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
	if($r_id333)
	{
		oci_commit($con);
	}



 	//echo "<br />Execution Time: " . (microtime(true) - $started) . "S"; //die;
	ob_start();
	?>
	<fieldset style="width:2300px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="90">Job No</th>
                <th width="100">Style</th>
                <th width="100">Booking No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="80">Grey Fabric Qty(Kg)</th>
                <th width="110">Construction</th>
                <th width="105">Color</th>
                <th width="80">Color Range</th>
                <th width="85">Y-Count</th>
                <th width="100">Yarn Type</th>
                <th width="140">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="70">MC Dia and Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="70">GSM</th>
                <th width="70">M/C NO.</th>
                <th width="70">Knitting Company</th>
                <th width="90">Receive Qty.</th>
                <th width="90">Issue Rtn. Qty.</th>
                <th width="90">Total Receive Qty.</th>
                <th width="90">Issue Qty.</th>
                <th>Stock Qty.</th>
			</thead>
		</table>
		<div style="width:2300px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<?
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_stock_qnty=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					$bookingNo=$fileRefData[4];
					$StyleRef=$fileRefData[5];

					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					$z=1;
					foreach($recvDtlsDataArr[$fileRefArrData] as $data=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$datas=explode("**",$data);
						$febric_description_id=$datas[0];
						$brand_name=$brand_arr[$datas[2]];
						$yarn_lot=$datas[3];
						$width=$datas[4];
						$stitch_length=$datas[5];
						$gsm=$datas[6];
						$machine_no=$machine_arr[$datas[7]];
						$knitting_company=$datas[8];
						$machine_dia_gg=$datas[9];
						$yarn_product_ids=$datas[10];

						$yarn_count='';
						$yarn_count_id=array_unique(explode(",",$datas[1]));
						foreach($yarn_count_id as $count_id)
						{
							if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
						}
						$yarn_count=chop($yarn_count,",");

						$constuction=$constuction_arr[$febric_description_id];
						$composition=$composition_arr[$febric_description_id];
						$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));

						$recv_qty_only=$value['recv'];
						$issue_return=$iss_rtn_qty_arr[$fileRefArrData][$data];
						$recv_qty=$recv_qty_only + $issue_return;

						//echo "[$fileRefArrData][$data]"."<br>";
						$iss_qty = $iss_qty_arr[$fileRefArrData][$data];
						$recv_qty = number_format($recv_qty,2,".","");
						$iss_qty = number_format($iss_qty,2,".","");
						$stock_qty=$recv_qty-$iss_qty;

						$colorRange='';
						$colorRangeIds=array_unique(explode(",",$value['range']));
						foreach($colorRangeIds as $range_id)
						{
							if($range_id>0) $colorRange.=$color_range[$range_id].',';
						}
						$colorRange=chop($colorRange,",");

						$color='';
						$colorIds=array_unique(explode(",",$value['color']));
						foreach($colorIds as $color_id)
						{
							if($color_id>0) $color.=$color_arr[$color_id].',';
						}
						$color=chop($color,",");

						$barcode_nos=chop($value['barcode_no'],",");
						$type=chop($value['type'],",");

						$yarn_type_id= "";
						foreach(explode(",", $yarn_product_ids) as $YarnProdId)
						{
							$yarn_type_id .= $yarn_type[$yarn_type_id_arr[$YarnProdId]].",";
						}

						$yarn_type_id = implode(",",array_filter(array_unique(explode(",", chop($yarn_type_id)))));

						$rcv_barcode_no_array = explode(",",chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","));
						$issue_barcode_array = explode(",",chop($issue_barcode_arr[$fileRefArrData][$data],","));
						$issue_return_barcode_array = explode(",",chop($issue_return_barcode_arr[$fileRefArrData][$data],","));
						$rem_barcode_array = array_diff($rcv_barcode_no_array, $issue_barcode_array );
						/*if($i == 4){
							echo implode(",",$rcv_barcode_no_array);
							echo "<br>iss=";
							echo implode(",",$issue_barcode_array);
							echo "<br>rem=";
							echo implode(",",$rem_barcode_array);
						}*/
						$stock_barcode_array = array_merge($rem_barcode_array,$issue_return_barcode_array );


						//$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".$barcode_nos."_".$poIds;

						$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],",")."_".$poIds;


						$dataIss=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($issue_barcode_arr[$fileRefArrData][$data],",")."_".$poIds;


						if($z==1)
						{
							$display_font_color="";
							$font_end="";
						}
						else
						{
							$display_font_color="<font style='display:none' color='$bgcolor'>";
							$font_end="</font>";
						}

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="70"><p><? echo $display_font_color.$buyer_arr[$buyer_id].$font_end; ?>&nbsp;</p></td>
                            <td width="90"><p><? echo $display_font_color.$job_no.$font_end; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $StyleRef; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $display_font_color.$bookingNo.$font_end; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $display_font_color.$fileNo.$font_end; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $display_font_color.$refNo.$font_end; ?>&nbsp;</p></td>
                            <td width="80" align="right"><p><? echo $display_font_color; ?><a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $poIds; ?>')"><? echo number_format($grey_qnty,2,'.',''); ?></a><? echo $font_end; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $constuction; ?>&nbsp;</p></td>
							<td width="105"><p><? echo $color; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $colorRange; ?>&nbsp;</p></td>
							<td width="85"><p><? echo $yarn_count; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $yarn_product_ids;?>"><p><? echo $yarn_type_id;//$yarn_type_name; ?>&nbsp;</p></td>
							<td width="140"><p><? echo $composition; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $brand_name; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $width; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $machine_no; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td width="90" align="right" ><? echo number_format($recv_qty_only,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($issue_return,2,'.',''); ?></td>
							<td width="90" align="right" title="<? echo chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","); ?>"><a href="##" onClick="openpage('recv_popup','<? echo $dataP; ?>')"><? echo number_format($recv_qty,2,'.',''); ?></a></td>
							<td width="90" align="right" title="<? echo chop($issue_barcode_arr[$fileRefArrData][$data],","); ?>"><a href="##" onClick="openpage('iss_popup','<? echo $dataIss; ?>')"><? echo number_format($iss_qty,2,'.',''); ?></a></td>

							<td align="right" ><p><a href="##" onClick="openpage('stock_popup','<? echo $dataP; ?>')"><? echo number_format($stock_qty,2,'.',''); ?></a></p></td>
						</tr>
					<?
						$z++;
						$i++;
						$tot_recv_only+=$recv_qty_only;
						$tot_issue_rtn+=$issue_return;
						$tot_recv_qty+=$recv_qty;
						$tot_iss_qty+=$iss_qty;
						$tot_stock_qnty+=$stock_qty;
					}
				}
				?>
			</table>
		</div>
		<table width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="105">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="85">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70" align="right"><b>Total</b></th>
                    <th align="right" width="90" id="value_tot_recv_only"><? echo number_format($tot_recv_only,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_iss_rtn"><? echo number_format($tot_issue_rtn,2,'.',''); ?></th>

                    <th align="right" width="90" id="value_tot_recv"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_iss"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                    <th align="right" style="padding-right:20px" id="value_tot_stock"><? echo number_format($tot_stock_qnty,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	 echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="report_generate_old")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));
	$txt_style_ref = trim(str_replace("'","",$txt_style_ref));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_style_ref!="") $style_cond=" and b.style_ref_no LIKE '%".trim($txt_style_ref)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond $style_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	order by a.id";


	//echo $sql; die;

	$result=sql_select($sql);
	if(!empty($result))
	{
		// $con = connect();	
		// $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
		// if($r_id1)
		// {
		// 	oci_commit($con);
		// }


		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')]."_".$row[csf('style_ref_no')];
			$poIds.=$row[csf('id')].",";
			$all_po_id_arr[$row[csf("id")]] = $row[csf("id")];
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$all_po_id_arr = array_filter($all_po_id_arr);
	if(!empty($all_po_id_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($all_po_id_arr as $poId)
		{
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
			oci_commit($con);
		}
	}

	// $all_po_id_arr = array_filter($all_po_id_arr);
	// $all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	// if(!empty($all_po_id_arr))
	// {	
		
	// 	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_po_id_arr, $empty_arr);//PO ID
	// }

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
			$otot_po_cond.=" a.to_order_id in($ids) or ";
			$stst_po_cond.=" b.to_order_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
		$otot_po_cond=$poIds_cond_pre.chop($otot_po_cond,'or ').$poIds_cond_suff;
		$stst_po_cond=$poIds_cond_pre.chop($stst_po_cond,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
		$otot_po_cond=" and a.to_order_id in($poIds)";
		$stst_po_cond=" and b.to_order_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );

	
	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, tmp_po_id c where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.po_id and c.user_id=$user_id group by b.po_break_down_id", "po_id", "grey_req_qnty");

	$delv_arr=return_library_array("select a.barcode_num, a.grey_sys_id from pro_grey_prod_delivery_dtls a,tmp_po_id b where a.order_id=b.po_id and b.user_id=$user_id and a.entry_form=56", "barcode_num", "grey_sys_id");

	// $grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, GBL_TEMP_ENGINE c where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.ref_val and c.user_id=$user_id and c.entry_form=1990  group by b.po_break_down_id", "po_id", "grey_req_qnty");
	//$poIds_cond

	// $delv_arr=return_library_array("select a.barcode_num, a.grey_sys_id from pro_grey_prod_delivery_dtls a,GBL_TEMP_ENGINE b where a.order_id=b.ref_val and b.user_id=$user_id and b.entry_form=1990 and a.entry_form=56", "barcode_num", "grey_sys_id");
	//$poIds_cond_delv

	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);


	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0 $transfer_date  $stst_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0";


	// $query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	// from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date  and c.booking_without_order = 0
	// union all
	// select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	// from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.ref_val  and c.booking_without_order = 0
	// union all
	// select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	// from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d 
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0 $transfer_date  $stst_po_cond and c.booking_without_order = 0
	// union all
	// select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	// from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d 
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990  and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.ref_val  and c.booking_without_order = 0";
	//$poIds_cond_roll
	//echo $query;
	
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		//$ref_barcode_arrxx[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
	}

	if(!empty($ref_barcode_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($ref_barcode_arr as $barcodeNO)
		{
			execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
			oci_commit($con);
		}
	}

	if(!empty($ref_barcode_arr))
	{	
		$recvDataArrTrans=array();$recvDataArr=array();
		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		//echo $sqlRecvT;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

			
		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(!empty($yarn_prod_id_arr))
		{
			$con = connect();
			$r_id33=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
			if($r_id33)
			{
				oci_commit($con);
			}

			
			$con = connect();
			foreach($yarn_prod_id_arr as $prodID)
			{
				execute_query("INSERT INTO TMP_PROD_ID(PROD_ID,USERID) VALUES(".$prodID.", ".$user_id.")");
				oci_commit($con);
			}
		}


		//echo "select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1"; die;
		$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}



			$split_barcode_arr = array_filter($split_barcode_arr);
			if(!empty($split_barcode_arr))
			{
				$con = connect();
				$r_id2222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
				if($r_id2222)
				{
					oci_commit($con);
				}
				$con = connect();
				foreach($split_barcode_arr as $barcodeNO)
				{
					execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
					oci_commit($con);
				}
			}



			

			//echo "select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b,tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.barcode_no=c.barcode_no and userid=$user_id and a.status_active =1 and b.status_active=1";
			//$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b,tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.barcode_no=c.barcode_no and c.userid=$user_id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
				}
			}



		}
		//$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			/*$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}*/

			//$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a where a.status_active = 1 $yarn_prod_id_cond","id","yarn_type");
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a,tmp_prod_id b where a.status_active = 1 and a.id=b.prod_id and b.userid=$user_id","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		}
		unset($recvDataT);
	}

	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($ref_febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($ref_febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}
	


	// $iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
	// 	union all
	// 	select a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,GBL_TEMP_ENGINE d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and c.booking_without_order = 0 
	// 	union all
	// select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	// where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id   and b.from_order_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	// group by c.barcode_no, b.from_order_id
	// union all
	// select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	// where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990  and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
	// group by c.barcode_no, a.from_order_id ");

	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
		union all
		SELECT a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,tmp_po_id d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id=d.po_id and d.user_id=$user_id and d.entry_form=1990 and c.booking_without_order = 0 
		union all
		SELECT b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.from_order_id=d.po_id and d.user_id=$user_id and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id
	union all
	SELECT a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id=d.po_id and d.user_id=$user_id and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
	group by c.barcode_no, a.from_order_id ");

	//$poIds_cond_trans_roll
	// $ctct_po_cond
	//$otst_po_cond

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();

	foreach($iss_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"];
		}


		$iss_qty_arr[$ref_file][$data_prod] +=$row[csf("qnty")];

		$issue_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";

	}
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll");
	foreach($iss_rtn_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);


	$ref_file="";$data_prod="";
	foreach($data_array as $row)
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];


			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			//$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);

	$con = connect();	
	// $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
	// if($r_id111)
	// {
	// 	oci_commit($con);
	// }
	$r_id111=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_id ");
	if($r_id111)
	{
		oci_commit($con);
	}
	$r_id222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	if($r_id222)
	{
		oci_commit($con);
	}
	$r_id333=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
	if($r_id333)
	{
		oci_commit($con);
	}



 	//echo "<br />Execution Time: " . (microtime(true) - $started) . "S"; //die;
	ob_start();
	?>
	<fieldset style="width:2300px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="90">Job No</th>
                <th width="100">Style</th>
                <th width="100">Booking No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="80">Grey Fabric Qty(Kg)</th>
                <th width="110">Construction</th>
                <th width="105">Color</th>
                <th width="80">Color Range</th>
                <th width="85">Y-Count</th>
                <th width="100">Yarn Type</th>
                <th width="140">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="70">MC Dia and Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="70">GSM</th>
                <th width="70">M/C NO.</th>
                <th width="70">Knitting Company</th>
                <th width="90">Receive Qty.</th>
                <th width="90">Issue Rtn. Qty.</th>
                <th width="90">Total Receive Qty.</th>
                <th width="90">Issue Qty.</th>
                <th>Stock Qty.</th>
			</thead>
		</table>
		<div style="width:2300px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<?
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_stock_qnty=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					$bookingNo=$fileRefData[4];
					$StyleRef=$fileRefData[5];

					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					$z=1;
					foreach($recvDtlsDataArr[$fileRefArrData] as $data=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$datas=explode("**",$data);
						$febric_description_id=$datas[0];
						$brand_name=$brand_arr[$datas[2]];
						$yarn_lot=$datas[3];
						$width=$datas[4];
						$stitch_length=$datas[5];
						$gsm=$datas[6];
						$machine_no=$machine_arr[$datas[7]];
						$knitting_company=$datas[8];
						$machine_dia_gg=$datas[9];
						$yarn_product_ids=$datas[10];

						$yarn_count='';
						$yarn_count_id=array_unique(explode(",",$datas[1]));
						foreach($yarn_count_id as $count_id)
						{
							if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
						}
						$yarn_count=chop($yarn_count,",");

						$constuction=$constuction_arr[$febric_description_id];
						$composition=$composition_arr[$febric_description_id];
						$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));

						$recv_qty_only=$value['recv'];
						$issue_return=$iss_rtn_qty_arr[$fileRefArrData][$data];
						$recv_qty=$recv_qty_only + $issue_return;

						//echo "[$fileRefArrData][$data]"."<br>";
						$iss_qty = $iss_qty_arr[$fileRefArrData][$data];
						$recv_qty = number_format($recv_qty,2,".","");
						$iss_qty = number_format($iss_qty,2,".","");
						$stock_qty=$recv_qty-$iss_qty;

						$colorRange='';
						$colorRangeIds=array_unique(explode(",",$value['range']));
						foreach($colorRangeIds as $range_id)
						{
							if($range_id>0) $colorRange.=$color_range[$range_id].',';
						}
						$colorRange=chop($colorRange,",");

						$color='';
						$colorIds=array_unique(explode(",",$value['color']));
						foreach($colorIds as $color_id)
						{
							if($color_id>0) $color.=$color_arr[$color_id].',';
						}
						$color=chop($color,",");

						$barcode_nos=chop($value['barcode_no'],",");
						$type=chop($value['type'],",");

						$yarn_type_id= "";
						foreach(explode(",", $yarn_product_ids) as $YarnProdId)
						{
							$yarn_type_id .= $yarn_type[$yarn_type_id_arr[$YarnProdId]].",";
						}

						$yarn_type_id = implode(",",array_filter(array_unique(explode(",", chop($yarn_type_id)))));

						$rcv_barcode_no_array = explode(",",chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","));
						$issue_barcode_array = explode(",",chop($issue_barcode_arr[$fileRefArrData][$data],","));
						$issue_return_barcode_array = explode(",",chop($issue_return_barcode_arr[$fileRefArrData][$data],","));
						$rem_barcode_array = array_diff($rcv_barcode_no_array, $issue_barcode_array );
						/*if($i == 4){
							echo implode(",",$rcv_barcode_no_array);
							echo "<br>iss=";
							echo implode(",",$issue_barcode_array);
							echo "<br>rem=";
							echo implode(",",$rem_barcode_array);
						}*/
						$stock_barcode_array = array_merge($rem_barcode_array,$issue_return_barcode_array );


						//$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".$barcode_nos."_".$poIds;

						$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],",")."_".$poIds;


						$dataIss=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($issue_barcode_arr[$fileRefArrData][$data],",")."_".$poIds;


						if($z==1)
						{
							$display_font_color="";
							$font_end="";
						}
						else
						{
							$display_font_color="<font style='display:none' color='$bgcolor'>";
							$font_end="</font>";
						}

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="70"><p><? echo $display_font_color.$buyer_arr[$buyer_id].$font_end; ?>&nbsp;</p></td>
                            <td width="90"><p><? echo $display_font_color.$job_no.$font_end; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $StyleRef; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $display_font_color.$bookingNo.$font_end; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $display_font_color.$fileNo.$font_end; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $display_font_color.$refNo.$font_end; ?>&nbsp;</p></td>
                            <td width="80" align="right"><p><? echo $display_font_color; ?><a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $poIds; ?>')"><? echo number_format($grey_qnty,2,'.',''); ?></a><? echo $font_end; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $constuction; ?>&nbsp;</p></td>
							<td width="105"><p><? echo $color; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $colorRange; ?>&nbsp;</p></td>
							<td width="85"><p><? echo $yarn_count; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $yarn_product_ids;?>"><p><? echo $yarn_type_id;//$yarn_type_name; ?>&nbsp;</p></td>
							<td width="140"><p><? echo $composition; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $brand_name; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $width; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $machine_no; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td width="90" align="right" ><? echo number_format($recv_qty_only,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($issue_return,2,'.',''); ?></td>
							<td width="90" align="right" title="<? echo chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","); ?>"><a href="##" onClick="openpage('recv_popup','<? echo $dataP; ?>')"><? echo number_format($recv_qty,2,'.',''); ?></a></td>
							<td width="90" align="right" title="<? echo chop($issue_barcode_arr[$fileRefArrData][$data],","); ?>"><a href="##" onClick="openpage('iss_popup','<? echo $dataIss; ?>')"><? echo number_format($iss_qty,2,'.',''); ?></a></td>

							<td align="right" ><p><a href="##" onClick="openpage('stock_popup','<? echo $dataP; ?>')"><? echo number_format($stock_qty,2,'.',''); ?></a></p></td>
						</tr>
					<?
						$z++;
						$i++;
						$tot_recv_only+=$recv_qty_only;
						$tot_issue_rtn+=$issue_return;
						$tot_recv_qty+=$recv_qty;
						$tot_iss_qty+=$iss_qty;
						$tot_stock_qnty+=$stock_qty;
					}
				}
				?>
			</table>
		</div>
		<table width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="105">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="85">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70" align="right"><b>Total</b></th>
                    <th align="right" width="90" id="value_tot_recv_only"><? echo number_format($tot_recv_only,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_iss_rtn"><? echo number_format($tot_issue_rtn,2,'.',''); ?></th>

                    <th align="right" width="90" id="value_tot_recv"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_iss"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                    <th align="right" style="padding-right:20px" id="value_tot_stock"><? echo number_format($tot_stock_qnty,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	 echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="report_generate2___")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));
	$txt_style_ref = trim(str_replace("'","",$txt_style_ref));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_style_ref!="") $style_cond=" and b.style_ref_no LIKE '%".trim($txt_style_ref)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond $style_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	order by a.id";


	//echo $sql; //die;

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')]."_".$row[csf('style_ref_no')];
			$poIds.=$row[csf('id')].",";
			$all_po_id_arr[$row[csf("id")]] = $row[csf("id")];
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$all_po_id_arr = array_filter($all_po_id_arr);
	if(!empty($all_po_id_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($all_po_id_arr as $poId)
		{
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
			oci_commit($con);
		}
	}
	//die;

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
			$otot_po_cond.=" a.to_order_id in($ids) or ";
			$stst_po_cond.=" b.to_order_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
		$otot_po_cond=$poIds_cond_pre.chop($otot_po_cond,'or ').$poIds_cond_suff;
		$stst_po_cond=$poIds_cond_pre.chop($stst_po_cond,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
		$otot_po_cond=" and a.to_order_id in($poIds)";
		$stst_po_cond=" and b.to_order_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );

	
	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, tmp_po_id c where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.po_id and c.user_id=$user_id group by b.po_break_down_id", "po_id", "grey_req_qnty");

	$delv_arr=return_library_array("select a.barcode_num, a.grey_sys_id from pro_grey_prod_delivery_dtls a,tmp_po_id b where a.order_id=b.po_id and b.user_id=$user_id and a.entry_form=56", "barcode_num", "grey_sys_id");

	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);


	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0 $transfer_date  $stst_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.po_id  and c.booking_without_order = 0";

	//$poIds_cond_roll
	//echo $query;
	
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		//$ref_barcode_arrxx[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
	}

	if(!empty($ref_barcode_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($ref_barcode_arr as $barcodeNO)
		{
			execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
			oci_commit($con);
		}
	}

	if(!empty($ref_barcode_arr))
	{	
		$recvDataArrTrans=array();$recvDataArr=array();$rcv_qc_pass_qnty_pcs = array(); $rcv_qc_pass_qnty_pcs_arr = array();
		$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.body_part_id, c.coller_cuff_size,c.qc_pass_qnty_pcs FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		//echo $sqlRecvT;die;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

			
		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(!empty($yarn_prod_id_arr))
		{
			$con = connect();
			$r_id33=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
			if($r_id33)
			{
				oci_commit($con);
			}

			
			$con = connect();
			foreach($yarn_prod_id_arr as $prodID)
			{
				execute_query("INSERT INTO TMP_PROD_ID(PROD_ID,USERID) VALUES(".$prodID.", ".$user_id.")");
				oci_commit($con);
			}
		}


		//echo "select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1"; die;
		$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}



			$split_barcode_arr = array_filter($split_barcode_arr);
			if(!empty($split_barcode_arr))
			{
				$con = connect();
				$r_id2222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
				if($r_id2222)
				{
					oci_commit($con);
				}
				$con = connect();
				foreach($split_barcode_arr as $barcodeNO)
				{
					execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
					oci_commit($con);
				}
			}

			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b,tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.barcode_no=c.barcode_no and c.userid=$user_id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
				}
			}



		}
		//$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			/*$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}*/

			//$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a where a.status_active = 1 $yarn_prod_id_cond","id","yarn_type");
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a,tmp_prod_id b where a.status_active = 1 and a.id=b.prod_id and b.userid=$user_id","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"]=$row[csf('coller_cuff_size')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

			$rcv_qc_pass_qnty_pcs_arr[$row[csf('barcode_no')]] +=$row[csf('qc_pass_qnty_pcs')]*1;
		}
		unset($recvDataT);
	}

	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($ref_febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($ref_febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}

	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty, c.qc_pass_qnty_pcs from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
	union all
	SELECT a.po_breakdown_id, c.barcode_no, c.qnty, c.qc_pass_qnty_pcs from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,tmp_po_id d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id=d.po_id and d.user_id=$user_id  and c.booking_without_order = 0 
	union all
	SELECT b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.from_order_id=d.po_id and d.user_id=$user_id and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id
	union all
	SELECT a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,tmp_po_id d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id=d.po_id and d.user_id=$user_id and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
	group by c.barcode_no, a.from_order_id ");
	//$poIds_cond_trans_roll
	// $ctct_po_cond
	//$otst_po_cond

	$ref_file="";$data_prod=""; $issue_barcode_arr = array(); $iss_qc_pass_qnty_pcs_arr = array();

	foreach($iss_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["coller_cuff_size"];
		}


		$iss_qty_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] +=$row[csf("qnty")];

		$issue_barcode_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] .= $row[csf('barcode_no')].",";

		$iss_qc_pass_qnty_pcs_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] +=$row[csf('qc_pass_qnty_pcs')]*1;

	}
	unset($iss_qty_sql);

	/* echo "<pre>";
	print_r($iss_qty_arr); */

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll");
	foreach($iss_rtn_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

		$iss_rtn_qty_arr[$ref_file][$data_prod][$row[csf('barcode_no')]]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);


	$ref_file="";$data_prod="";
	foreach($data_array as $row)
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];


			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			//$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);
	//echo "<pre>";print_r($recvDtlsDataArr);

	$sql_body_part="select id,body_part_full_name,body_part_type from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name";
	$body_part_rslt=sql_select($sql_body_part);
	
	$body_partArr = array();
	foreach($body_part_rslt as $row )
	{
		$body_partArr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_partArr[$row[csf('id')]]['body_part_type']= $row[csf('body_part_type')];
		
	}
	unset($body_part_rslt);



	$con = connect();	
	// }
	$r_id111=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_id ");
	if($r_id111)
	{
		oci_commit($con);
	}
	$r_id222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	if($r_id222)
	{
		oci_commit($con);
	}
	$r_id333=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
	if($r_id333)
	{
		oci_commit($con);
	}



 	//echo "<br />Execution Time: " . (microtime(true) - $started) . "S"; //die;
	ob_start();
	?>
	 <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>
	<fieldset style="width:2040px">
		<table cellpadding="0" cellspacing="0" width="1710">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="90">Job No</th>
                <th width="100">Style</th>
                <th width="100">Booking No</th>
                <th width="80">Grey Required Qty.</th>
                <th width="110">Construction</th>
                <th width="105">Color</th>
                <th width="85">Y-Count</th>
                <th width="100">Yarn Type</th>
                <th width="140">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="70">MC Dia and Gauge</th>
                <th width="70">GSM</th>
                <th width="70">Body Part</th>
                <th width="70">Size</th>
                <th width="90">Receive Qty.(KG)</th>
                <th width="90">Receive Qty.(PCS)</th>
                <th width="90">Issue Qty.(KG)</th>
                <th width="90">Issue Qty.(PCS)</th>
                <th width="90">Stock Qty.(KG)</th>
                <th>Stock Qty.(PCS)</th>
			</thead>
		</table>
		<div style="width:2040px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2020" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<?
				$row_count = array();
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					foreach($recvDtlsDataArr[$fileRefArrData] as $k_rcv=>$v_rcv)
					{
						$datas=explode("**",$k_rcv);
						$body_part_id=$datas[11];
						if($body_partArr[$body_part_id]['body_part_type']==40)
						{
							$row_count[$fileRefArrData]++;
						}
						
					}
				}
				//var_dump($row_count);

				$i=1; 
				$tot_recv_only=0;
				$tot_rcv_qc_pass_qnty_pcs=0;
				$tot_iss_qty=0;
				$tot_iss_qc_pass_qnty_pcs=0;
				$tot_stock_qnty=0;
				$tot_stock_qty_pcs=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					$bookingNo=$fileRefData[4];
					$StyleRef=$fileRefData[5];
					//var_dump($poIds);
					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					
				
					foreach($recvDtlsDataArr[$fileRefArrData] as $k_rcv=>$value)
					{
						
							//var_dump($v_rcv);
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							
							//var_dump($row_span);
							$datas=explode("**",$k_rcv);
							$febric_description_id=$datas[0];
							$brand_name=$brand_arr[$datas[2]];
							$yarn_lot=$datas[3];
							$width=$datas[4];
							$stitch_length=$datas[5];
							$gsm=$datas[6];
							$machine_no=$machine_arr[$datas[7]];
							$knitting_company=$datas[8];
							$machine_dia_gg=$datas[9];
							$yarn_product_ids=$datas[10];
							$body_part_id=$datas[11];
							$coller_cuff_size=$datas[12];

							if($body_partArr[$body_part_id]['body_part_type']==40)
							{

								$row_span = $row_count[$fileRefArrData];

								$yarn_count='';
								$yarn_count_id=array_unique(explode(",",$datas[1]));
								foreach($yarn_count_id as $count_id)
								{
									if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
								}
								$yarn_count=chop($yarn_count,",");

								$constuction=$constuction_arr[$febric_description_id];
								$composition=$composition_arr[$febric_description_id];
								$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));

								
								

								$barcode_nos=array_unique(explode(",",$value['barcode_no']));
								//var_dump($barcode_nos);
								$iss_qty = 0;
								$rcv_qc_pass_qnty_pcs = 0;
								$iss_qc_pass_qnty_pcs = 0;
								foreach($barcode_nos as $barcode_no)
								{
									//if($range_id>0) $colorRange.=$color_range[$range_id].',';
									$iss_qty += $iss_qty_arr[$fileRefArrData][$k_rcv][$barcode_no];
									$rcv_qc_pass_qnty_pcs += $rcv_qc_pass_qnty_pcs_arr[$barcode_no];
									$iss_qc_pass_qnty_pcs += $iss_qc_pass_qnty_pcs_arr[$fileRefArrData][$k_rcv][$barcode_no];
									
								}

								$recv_qty_only=$value['recv'];
								$recv_qty=$recv_qty_only;
								
								$recv_qty = number_format($recv_qty,2,".","");
								$iss_qty = number_format($iss_qty,2,".","");
								$stock_qty=$recv_qty-$iss_qty;

								$recv_qty_pcs = number_format($rcv_qc_pass_qnty_pcs,2,".","");
								$iss_qty_pcs = number_format($iss_qc_pass_qnty_pcs,2,".","");
								$stock_qty_pcs = $recv_qty_pcs-$iss_qty_pcs;

								$colorRange='';
								$colorRangeIds=array_unique(explode(",",$value['range']));
								foreach($colorRangeIds as $range_id)
								{
									if($range_id>0) $colorRange.=$color_range[$range_id].',';
								}
								$colorRange=chop($colorRange,",");

								$color='';
								$colorIds=array_unique(explode(",",$value['color']));
								foreach($colorIds as $color_id)
								{
									if($color_id>0) $color.=$color_arr[$color_id].',';
								}
								$color=chop($color,",");

								$barcode_nos=chop($value['barcode_no'],",");
								$type=chop($value['type'],",");

								$yarn_type_id= "";
								foreach(explode(",", $yarn_product_ids) as $YarnProdId)
								{
									$yarn_type_id .= $yarn_type[$yarn_type_id_arr[$YarnProdId]].",";
								}

								$yarn_type_id = implode(",",array_filter(array_unique(explode(",", chop($yarn_type_id)))));

								// $rcv_barcode_no_array = explode(",",chop($recvDtlsDataArr[$fileRefArrData][$k_rcv]['barcode_no'],","));
								// $issue_barcode_array = explode(",",chop($issue_barcode_arr[$fileRefArrData][$k_rcv][$data],","));
								// $issue_return_barcode_array = explode(",",chop($issue_return_barcode_arr[$fileRefArrData][$k_rcv][$data],","));

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40" ><? echo $i; ?></td>
								<?
								
									if(!in_array($fileRefArrData,$file_chk))
									{
										$file_chk[]=$fileRefArrData;
										?>
										
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
										<td width="90" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $job_no.$font_end; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $StyleRef; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $bookingNo; ?>&nbsp;</p></td>
										<td width="80" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
										<td width="110" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $constuction; ?>&nbsp;</p></td>
										<td width="105" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $color; ?>&nbsp;</p></td>
										<td width="85" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $yarn_count; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>" title="<? echo $yarn_product_ids;?>"><p><? echo $yarn_type_id;//$yarn_type_name; ?>&nbsp;</p></td>
										<td width="140" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $composition; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $brand_name; ?>&nbsp;</p></td>
										<td width="80" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $gsm; ?>&nbsp;</p></td>
										<? 
								
									} ?>
									<td width="70" class="wrd_brk" align="center"><p><? echo $body_partArr[$body_part_id]['body_part_full_name']; ?>&nbsp;</p></td>
									<td width="70" class="wrd_brk" align="center"><p><? echo $coller_cuff_size; ?>&nbsp;</p></td>
									<td width="90" class="wrd_brk" align="right" title="<? echo rtrim($value['barcode_no'],','); ?>"><? echo number_format($recv_qty_only,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right"><? echo number_format($rcv_qc_pass_qnty_pcs,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" ><? echo number_format($iss_qty,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" title=""><? echo number_format($iss_qc_pass_qnty_pcs,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" title="( Receive Qty.(KG) - Issue Qty.(KG))"><p><? echo number_format($stock_qty,2,'.',''); ?></p></td>
									<td align="right" class="wrd_brk" title="( Receive Qty.(PCS) - Issue Qty.(PCS))"><p><? echo number_format($stock_qty_pcs,2,'.',''); ?></p></td>
								</tr>
								<?
								$i++;
								$tot_recv_only+=$recv_qty_only;
								$tot_rcv_qc_pass_qnty_pcs+=$rcv_qc_pass_qnty_pcs;
								$tot_iss_qty+=$iss_qty;
								$tot_iss_qc_pass_qnty_pcs+=$iss_qc_pass_qnty_pcs;
								$tot_stock_qnty+=$stock_qty;
								$tot_stock_qty_pcs+=$stock_qty_pcs;
							}
					}
					
				}
				?>
			</table>
		</div>
		<table width="2040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="105">&nbsp;</th>
                    <th width="85">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70" align="right"><b>Total : </b></th>
                    <th align="right" width="90" ><? echo number_format($tot_recv_only,2,'.',''); ?></th>
                    <th align="right" width="90" ><? echo number_format($tot_rcv_qc_pass_qnty_pcs,2,'.',''); ?></th>

                    <th align="right" width="90" ><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                    <th align="right" width="90" ><? echo number_format($tot_iss_qc_pass_qnty_pcs,2,'.',''); ?></th>
                    <th align="right"  width="90" ><? echo number_format($tot_stock_qnty,2,'.',''); ?></th>
                    <th align="right" style="padding-right:20px" ><? echo number_format($tot_stock_qty_pcs,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	 echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="report_generate2")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));
	$txt_style_ref = trim(str_replace("'","",$txt_style_ref));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_style_ref!="") $style_cond=" and b.style_ref_no LIKE '%".trim($txt_style_ref)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond $style_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, b.style_ref_no
	order by a.id";


	//echo $sql; //die;

	$result=sql_select($sql);
	if(!empty($result))
	{
		$con = connect();	
		$r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
		if($r_id1)
		{
			oci_commit($con);
		}


		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')]."_".$row[csf('style_ref_no')];
			$poIds.=$row[csf('id')].",";
			$all_po_id_arr[$row[csf("id")]] = $row[csf("id")];
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$all_po_id_arr = array_filter($all_po_id_arr);
	//$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{	
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_po_id_arr, $empty_arr);//PO ID
		//die;
	}

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
			$otot_po_cond.=" a.to_order_id in($ids) or ";
			$stst_po_cond.=" b.to_order_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
		$otot_po_cond=$poIds_cond_pre.chop($otot_po_cond,'or ').$poIds_cond_suff;
		$stst_po_cond=$poIds_cond_pre.chop($stst_po_cond,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
		$otot_po_cond=" and a.to_order_id in($poIds)";
		$stst_po_cond=" and b.to_order_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );

	
	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, GBL_TEMP_ENGINE c where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.ref_val and c.user_id=$user_id and c.entry_form=1990  group by b.po_break_down_id", "po_id", "grey_req_qnty");
	//$poIds_cond

	$delv_arr=return_library_array("select a.barcode_num, a.grey_sys_id from pro_grey_prod_delivery_dtls a,GBL_TEMP_ENGINE b where a.order_id=b.ref_val and b.user_id=$user_id and b.entry_form=1990 and a.entry_form=56", "barcode_num", "grey_sys_id");
	//$poIds_cond_delv

	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);


	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.ref_val  and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0 $transfer_date  $stst_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990  and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date and a.to_order_id=d.ref_val  and c.booking_without_order = 0";
	//$poIds_cond_roll
	//echo $query;
	
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		//$ref_barcode_arrxx[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
	}

	if(!empty($ref_barcode_arr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($ref_barcode_arr as $barcodeNO)
		{
			execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
			
		}
		oci_commit($con);
	}

	if(!empty($ref_barcode_arr))
	{	
		$recvDataArrTrans=array();$recvDataArr=array();$rcv_qc_pass_qnty_pcs = array(); $rcv_qc_pass_qnty_pcs_arr = array();
		$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.body_part_id, c.coller_cuff_size,c.qc_pass_qnty_pcs FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c,tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		//echo $sqlRecvT;die;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

			
		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(!empty($yarn_prod_id_arr))
		{
			$con = connect();
			$r_id33=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
			if($r_id33)
			{
				oci_commit($con);
			}

			
			$con = connect();
			foreach($yarn_prod_id_arr as $prodID)
			{
				execute_query("INSERT INTO TMP_PROD_ID(PROD_ID,USERID) VALUES(".$prodID.", ".$user_id.")");
				
			}
			oci_commit($con);
		}


		//echo "select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1"; die;
		$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d,tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and e.barcode_no=c.barcode_no and e.userid=$user_id and c.status_active = 1 and d.status_active = 1");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}



			$split_barcode_arr = array_filter($split_barcode_arr);
			if(!empty($split_barcode_arr))
			{
				$con = connect();
				$r_id2222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
				if($r_id2222)
				{
					oci_commit($con);
				}
				$con = connect();
				foreach($split_barcode_arr as $barcodeNO)
				{
					execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcodeNO.", ".$user_id.")");
					
				}
				oci_commit($con);
			}



			

			//echo "select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b,tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.barcode_no=c.barcode_no and userid=$user_id and a.status_active =1 and b.status_active=1";
			//$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b,tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.barcode_no=c.barcode_no and c.userid=$user_id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
				}
			}



		}
		//$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			/*$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}*/

			//$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a where a.status_active = 1 $yarn_prod_id_cond","id","yarn_type");
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a,tmp_prod_id b where a.status_active = 1 and a.id=b.prod_id and b.userid=$user_id","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"]=$row[csf('coller_cuff_size')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

			$rcv_qc_pass_qnty_pcs_arr[$row[csf('barcode_no')]] +=$row[csf('qc_pass_qnty_pcs')]*1;
		}
		unset($recvDataT);
	}

	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($ref_febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($ref_febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}
	


	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty,c.qc_pass_qnty_pcs from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty,c.qc_pass_qnty_pcs from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,GBL_TEMP_ENGINE d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and c.booking_without_order = 0 
		union all
		select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id   and b.from_order_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990 and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
		group by c.barcode_no, b.from_order_id
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,GBL_TEMP_ENGINE d
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id=d.ref_val and d.user_id=$user_id and d.entry_form=1990  and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
		group by c.barcode_no, a.from_order_id ");
	//$poIds_cond_trans_roll
	// $ctct_po_cond
	//$otst_po_cond

	$ref_file="";$data_prod=""; $issue_barcode_arr = array(); $iss_qc_pass_qnty_pcs_arr = array();

	foreach($iss_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["coller_cuff_size"];
		}


		$iss_qty_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] +=$row[csf("qnty")];

		$issue_barcode_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] .= $row[csf('barcode_no')].",";

		$iss_qc_pass_qnty_pcs_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] +=$row[csf('qc_pass_qnty_pcs')]*1;

	}
	unset($iss_qty_sql);

	/* echo "<pre>";
	print_r($iss_qty_arr); */

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll");
	foreach($iss_rtn_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

		$iss_rtn_qty_arr[$ref_file][$data_prod][$row[csf('barcode_no')]]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod][$row[csf('barcode_no')]] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);


	$ref_file="";$data_prod="";
	foreach($data_array as $row)
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];


			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			//$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["coller_cuff_size"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$data][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);
	//echo "<pre>";print_r($recvDtlsDataArr);

	$sql_body_part="select id,body_part_full_name,body_part_type from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name";
	$body_part_rslt=sql_select($sql_body_part);
	
	$body_partArr = array();
	foreach($body_part_rslt as $row )
	{
		$body_partArr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_partArr[$row[csf('id')]]['body_part_type']= $row[csf('body_part_type')];
		
	}
	unset($body_part_rslt);



	$con = connect();	
	$r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
	if($r_id111)
	{
		oci_commit($con);
	}
	$r_id222=execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	if($r_id222)
	{
		oci_commit($con);
	}
	$r_id333=execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id."");
	if($r_id333)
	{
		oci_commit($con);
	}



 	//echo "<br />Execution Time: " . (microtime(true) - $started) . "S"; //die;
	ob_start();
	?>
	 <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>
	<fieldset style="width:2040px">
		<table cellpadding="0" cellspacing="0" width="1710">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="90">Job No</th>
                <th width="100">Style</th>
                <th width="100">Booking No</th>
                <th width="80">Grey Required Qty.</th>
                <th width="110">Construction</th>
                <th width="105">Color</th>
                <th width="85">Y-Count</th>
                <th width="100">Yarn Type</th>
                <th width="140">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="70">MC Dia and Gauge</th>
                <th width="70">GSM</th>
                <th width="70">Body Part</th>
                <th width="70">Size</th>
                <th width="90">Receive Qty.(KG)</th>
                <th width="90">Receive Qty.(PCS)</th>
                <th width="90">Issue Qty.(KG)</th>
                <th width="90">Issue Qty.(PCS)</th>
                <th width="90">Stock Qty.(KG)</th>
                <th>Stock Qty.(PCS)</th>
			</thead>
		</table>
		<div style="width:2040px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2020" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<?
				$row_count = array();
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					foreach($recvDtlsDataArr[$fileRefArrData] as $k_rcv=>$v_rcv)
					{
						$datas=explode("**",$k_rcv);
						$body_part_id=$datas[11];
						if($body_partArr[$body_part_id]['body_part_type']==40)
						{
							$row_count[$fileRefArrData]++;
						}
						
					}
				}
				//var_dump($row_count);

				$i=1; 
				$tot_recv_only=0;
				$tot_rcv_qc_pass_qnty_pcs=0;
				$tot_iss_qty=0;
				$tot_iss_qc_pass_qnty_pcs=0;
				$tot_stock_qnty=0;
				$tot_stock_qty_pcs=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					$bookingNo=$fileRefData[4];
					$StyleRef=$fileRefData[5];
					//var_dump($poIds);
					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					
				
					foreach($recvDtlsDataArr[$fileRefArrData] as $k_rcv=>$value)
					{
						
							//var_dump($v_rcv);
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							
							//var_dump($row_span);
							$datas=explode("**",$k_rcv);
							$febric_description_id=$datas[0];
							$brand_name=$brand_arr[$datas[2]];
							$yarn_lot=$datas[3];
							$width=$datas[4];
							$stitch_length=$datas[5];
							$gsm=$datas[6];
							$machine_no=$machine_arr[$datas[7]];
							$knitting_company=$datas[8];
							$machine_dia_gg=$datas[9];
							$yarn_product_ids=$datas[10];
							$body_part_id=$datas[11];
							$coller_cuff_size=$datas[12];

							if($body_partArr[$body_part_id]['body_part_type']==40)
							{

								$row_span = $row_count[$fileRefArrData];

								$yarn_count='';
								$yarn_count_id=array_unique(explode(",",$datas[1]));
								foreach($yarn_count_id as $count_id)
								{
									if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
								}
								$yarn_count=chop($yarn_count,",");

								$constuction=$constuction_arr[$febric_description_id];
								$composition=$composition_arr[$febric_description_id];
								$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));

								
								

								$barcode_nos=array_unique(explode(",",$value['barcode_no']));
								//var_dump($barcode_nos);
								$iss_qty = 0;
								$rcv_qc_pass_qnty_pcs = 0;
								$iss_qc_pass_qnty_pcs = 0;
								foreach($barcode_nos as $barcode_no)
								{
									//if($range_id>0) $colorRange.=$color_range[$range_id].',';
									$iss_qty += $iss_qty_arr[$fileRefArrData][$k_rcv][$barcode_no];
									$rcv_qc_pass_qnty_pcs += $rcv_qc_pass_qnty_pcs_arr[$barcode_no];
									$iss_qc_pass_qnty_pcs += $iss_qc_pass_qnty_pcs_arr[$fileRefArrData][$k_rcv][$barcode_no];
									
								}

								$recv_qty_only=$value['recv'];
								$recv_qty=$recv_qty_only;
								
								$recv_qty = number_format($recv_qty,2,".","");
								$iss_qty = number_format($iss_qty,2,".","");
								$stock_qty=$recv_qty-$iss_qty;

								$recv_qty_pcs = number_format($rcv_qc_pass_qnty_pcs,2,".","");
								$iss_qty_pcs = number_format($iss_qc_pass_qnty_pcs,2,".","");
								$stock_qty_pcs = $recv_qty_pcs-$iss_qty_pcs;

								$colorRange='';
								$colorRangeIds=array_unique(explode(",",$value['range']));
								foreach($colorRangeIds as $range_id)
								{
									if($range_id>0) $colorRange.=$color_range[$range_id].',';
								}
								$colorRange=chop($colorRange,",");

								$color='';
								$colorIds=array_unique(explode(",",$value['color']));
								foreach($colorIds as $color_id)
								{
									if($color_id>0) $color.=$color_arr[$color_id].',';
								}
								$color=chop($color,",");

								$barcode_nos=chop($value['barcode_no'],",");
								$type=chop($value['type'],",");

								$yarn_type_id= "";
								foreach(explode(",", $yarn_product_ids) as $YarnProdId)
								{
									$yarn_type_id .= $yarn_type[$yarn_type_id_arr[$YarnProdId]].",";
								}

								$yarn_type_id = implode(",",array_filter(array_unique(explode(",", chop($yarn_type_id)))));

								// $rcv_barcode_no_array = explode(",",chop($recvDtlsDataArr[$fileRefArrData][$k_rcv]['barcode_no'],","));
								// $issue_barcode_array = explode(",",chop($issue_barcode_arr[$fileRefArrData][$k_rcv][$data],","));
								// $issue_return_barcode_array = explode(",",chop($issue_return_barcode_arr[$fileRefArrData][$k_rcv][$data],","));

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40" ><? echo $i; ?></td>
								<?
								
									if(!in_array($fileRefArrData,$file_chk))
									{
										$file_chk[]=$fileRefArrData;
										?>
										
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
										<td width="90" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $job_no.$font_end; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $StyleRef; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $bookingNo; ?>&nbsp;</p></td>
										<td width="80" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
										<td width="110" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $constuction; ?>&nbsp;</p></td>
										<td width="105" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $color; ?>&nbsp;</p></td>
										<td width="85" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $yarn_count; ?>&nbsp;</p></td>
										<td width="100" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>" title="<? echo $yarn_product_ids;?>"><p><? echo $yarn_type_id;//$yarn_type_name; ?>&nbsp;</p></td>
										<td width="140" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $composition; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $brand_name; ?>&nbsp;</p></td>
										<td width="80" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
										<td width="70" valign="middle" class="wrd_brk" align="center" rowspan="<? echo $row_span ;?>"><p><? echo $gsm; ?>&nbsp;</p></td>
										<? 
								
									} ?>
									<td width="70" class="wrd_brk" align="center"><p><? echo $body_partArr[$body_part_id]['body_part_full_name']; ?>&nbsp;</p></td>
									<td width="70" class="wrd_brk" align="center"><p><? echo $coller_cuff_size; ?>&nbsp;</p></td>
									<td width="90" class="wrd_brk" align="right" title="<? echo rtrim($value['barcode_no'],','); ?>"><? echo number_format($recv_qty_only,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right"><? echo number_format($rcv_qc_pass_qnty_pcs,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" ><? echo number_format($iss_qty,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" title=""><? echo number_format($iss_qc_pass_qnty_pcs,2,'.',''); ?></td>
									<td width="90" class="wrd_brk" align="right" title="( Receive Qty.(KG) - Issue Qty.(KG))"><p><? echo number_format($stock_qty,2,'.',''); ?></p></td>
									<td align="right" class="wrd_brk" title="( Receive Qty.(PCS) - Issue Qty.(PCS))"><p><? echo number_format($stock_qty_pcs,2,'.',''); ?></p></td>
								</tr>
								<?
								$i++;
								$tot_recv_only+=$recv_qty_only;
								$tot_rcv_qc_pass_qnty_pcs+=$rcv_qc_pass_qnty_pcs;
								$tot_iss_qty+=$iss_qty;
								$tot_iss_qc_pass_qnty_pcs+=$iss_qc_pass_qnty_pcs;
								$tot_stock_qnty+=$stock_qty;
								$tot_stock_qty_pcs+=$stock_qty_pcs;
							}
					}
					
				}
				?>
			</table>
		</div>
		<table width="2040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="105">&nbsp;</th>
                    <th width="85">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70" align="right"><b>Total : </b></th>
                    <th align="right" width="90" ><? echo number_format($tot_recv_only,2,'.',''); ?></th>
                    <th align="right" width="90" ><? echo number_format($tot_rcv_qc_pass_qnty_pcs,2,'.',''); ?></th>

                    <th align="right" width="90" ><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                    <th align="right" width="90" ><? echo number_format($tot_iss_qc_pass_qnty_pcs,2,'.',''); ?></th>
                    <th align="right"  width="90" ><? echo number_format($tot_stock_qnty,2,'.',''); ?></th>
                    <th align="right" style="padding-right:20px" ><? echo number_format($tot_stock_qty_pcs,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	 echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="fabric_booking_popup")
{
 	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
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
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, c.construction as samp_construction, c.composition as samp_composition, c.gsm_weight as samp_gsm, sum (b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width,c.construction,c.composition,c.gsm_weight order by a.id";
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
                        <td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
						<?if($row[csf('booking_type')]==4){
						?>
						<td width="110"><p><? echo $row[csf('samp_construction')]; ?>&nbsp;</p></td>
                        <td width="150"><p><? echo $row[csf('samp_composition')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('samp_gsm')]; ?>&nbsp;</p></td>
						<?
					}else{
						?>
						<td width="110"><p><? echo $row[csf('construction')]; ?>&nbsp;</p></td>
                        <td width="150"><p><? echo $row[csf('copmposition')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
						<?
					}
						?>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
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
    exit();
}

if($action=="recv_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];
    ?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			var tbl_list_search_1 = document.getElementById("tbl_list_search_1");
			var tbl_list_search_2 = document.getElementById("tbl_list_search_2");
			var tbl_list_search_3 = document.getElementById("tbl_list_search_3");
			if(tbl_list_search_1){
				setFilterGrid('tbl_list_search_1',-1,tableFilters);
			}
			if(tbl_list_search_2){
				setFilterGrid('tbl_list_search_2',-1,tableFilters);
			}
			if(tbl_list_search_3){
				setFilterGrid('tbl_list_search_3',-1,tableFilters);
			}
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($data[15],2); ?>&nbsp;</p></td>
            </tr>
		</table>

        <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            <thead>
            	<tr><th colspan="7" class="wrd_brk">Receive Details</th></tr>
            	<tr>
	                <th width="40" class="wrd_brk">SL</th>
	                <th width="100" class="wrd_brk">Purpose</th>
	                <th width="100" class="wrd_brk">Receive No</th>
	                <th width="120" class="wrd_brk">Store Name</th>
	                <th width="100" class="wrd_brk">Bacode No</th>
	                <th width="80" class="wrd_brk">Roll No</th>
	                <th width="90" class="wrd_brk">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:650px; max-height:250px; overflow-y:auto;">
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
			    <?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;

				$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
				union all
				select c.barcode_no, s.store_name  from inv_item_transfer_mst a, lib_store_location s, inv_item_transfer_dtls b, pro_roll_details c WHERE b.to_store=s.id and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
				order by store_name, barcode_no","barcode_no","store_name");


				$sql="select a.recv_number as system_number, c.barcode_no, c.roll_no, c.qnty, 1 as type from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids)  and c.booking_without_order = 0
				union all
				select a.recv_number as system_number, c.barcode_no, c.roll_no, c.qnty, 2 as type from inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0
				union all
				select a.transfer_system_id as system_number, c.barcode_no, c.roll_no, c.qnty, 3 as type from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and a.transfer_criteria in (1,4) and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0
				and c.barcode_no in($barcode_nos) and b.to_order_id in($po_ids) and c.booking_without_order = 0
				  union all
 				select a.transfer_system_id as system_number, c.barcode_no, c.roll_no, c.qnty, 3 as type
 				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
 				WHERE a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0
 				and c.barcode_no in($barcode_nos) and a.to_order_id in($po_ids) and c.booking_without_order = 0
				order by barcode_no ";


			   	$tot_qnty=0;
                $result= sql_select($sql);

                foreach($result as $row)
                {
                	if($row[csf('type')]==1)
                	{
						$i++;
	                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               		?>
	                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
	                        <td width="100" class="wrd_brk"><? echo "Receive"; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk"><? echo $row[csf("system_number")]; ?></td>
	                        <td width="120" class="wrd_brk"><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk" title="<? echo $row[csf('type')]; ?>"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
	                        <td width="80" class="wrd_brk center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
	                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
	                    </tr>
	                	<?
						$tot_qnty += $row[csf('qnty')];
						$y++;
					}
                }
                ?>
            </table>
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
                <tfoot>
                	<tr>
	                	<th colspan="5" width="460" class="wrd_brk right">Total</th>
	                	<th width="80" class="wrd_brk" style="text-align: center;"><? echo $y; ?></th>
	                	<th width="90" class="wrd_brk"><? echo number_format($tot_qnty,2); ?></th>
	                </tr>
                </tfoot>
            </table>
		</div>
		<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            <thead>
            	<tr><th colspan="7">Issue Return Details</th></tr>
            	<tr>
	                <th width="40" class="wrd_brk">SL</th>
	                <th width="100" class="wrd_brk">Purpose</th>
	                <th width="100" class="wrd_brk">Return No</th>
	                <th width="120" class="wrd_brk">Store Name</th>
	                <th width="100" class="wrd_brk">Bacode No</th>
	                <th width="80" class="wrd_brk">Roll No</th>
	                <th width="90" class="wrd_brk">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:650px; max-height:250px; overflow-y:auto">
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_2">
			    <?
			    $tot_qnty_issue=0;$z=0;
                foreach($result as $row)
                {
                	if($row[csf('type')] ==2)
                	{
						$i++;
	                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               		?>
	                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
	                        <td width="100" class="wrd_brk"><? echo "Issue Return"; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk"><? echo $row[csf("system_number")]; ?></td>
	                        <td width="120" class="wrd_brk"><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk" title="<? echo $row[csf('type')]; ?>"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
	                        <td width="80" class="wrd_brk center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
	                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
	                    </tr>
	                	<?
						$tot_qnty_issue += $row[csf('qnty')];
						$z++;
					}
                }
                ?>
            </table>
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            	<tfoot>
                	<tr>
	                	<th colspan="5" width="460" class="wrd_brk right">Total</th>
	                	<th width="80" class="wrd_brk" style="text-align: center;"><? echo $z; ?></th>
	                	<th width="90" class="wrd_brk"><? echo number_format($tot_qnty_issue,2); ?></th>
	                </tr>
                </tfoot>
            </table>
		</div>
		<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            <thead>
            	<tr><th colspan="7">Transfer In Details</th></tr>
            	<tr>
	                <th width="40" class="wrd_brk">SL</th>
	                <th width="100" class="wrd_brk">Purpose</th>
	                <th width="100" class="wrd_brk">Transfer No</th>
	                <th width="120" class="wrd_brk">Store Name</th>
	                <th width="100" class="wrd_brk">Bacode No</th>
	                <th width="80" class="wrd_brk">Roll No</th>
	                <th width="90" class="wrd_brk">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:650px; max-height:250px; overflow-y:auto">
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_3">
			    <?
			    $k=0;
                foreach($result as $row)
                {
                	if($row[csf("type")] == 3)
                	{
						$i++;
	                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               		?>
	                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
	                        <td width="100" class="wrd_brk"><? echo "Transfer"; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk"><? echo $row[csf("system_number")]; ?></td>
	                        <td width="120" class="wrd_brk"><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</td>
	                        <td width="100" class="wrd_brk" title="<? echo $row[csf('type')]; ?>"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
	                        <td width="80" class="wrd_brk center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
	                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
	                    </tr>
	                	<?
						$total_transfer += $row[csf('qnty')];
						$k++;
					}
                }


                $trans_sql="select b.mst_id, d.transfer_system_id as system_number, c.barcode_no, c.roll_no, c.qnty, 4 as type
				from order_wise_pro_details a,  inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.to_trans_id and b.id=c.dtls_id and b.mst_id=d.id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) group by b.mst_id, d.transfer_system_id, b.id, c.barcode_no, c.roll_no, c.qnty";

				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

               		?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
                        <td width="100" class="wrd_brk"><? echo "Transfer"; ?>&nbsp;</td>
                        <td width="100" class="wrd_brk"><? echo $row[csf("system_number")]; ?></td>
                        <td width="120" class="wrd_brk"><? echo $trans_store_arr[$row[csf("barcode_no")]]; ?>&nbsp;</td>
                        <td width="100" class="wrd_brk" title="<? echo $row[csf('type')]; ?>"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
                        <td width="80" class="wrd_brk center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                	<?
					$total_transfer+=$row[csf('qnty')];
					$k++;
                }
                ?>
            </table>
            <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            	 <tfoot>
                	<tr>
	                	<th colspan="5" class="wrd_brk right" width="460">Total</th>
	                	<th width="80" class="wrd_brk" style="text-align: center;"><? echo $k; ?></th>
	                	<th width="90" class="wrd_brk"><? echo number_format($total_transfer,2); ?></th>
	                </tr>
                </tfoot>
			</table>
		</div>
        <table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
            <tfoot>
	            <tr>
	                <th colspan="5" class="wrd_brk">Roll Total</th>
	                <th width="80" class="wrd_brk" style="text-align: center;"><? echo $y+$z; ?></th>
	                <th width="90" class="wrd_brk"><? echo number_format($tot_qnty+$tot_qnty_issue,2); ?></th>
	            </tr>
	             <tr>
	                <th colspan="5" class="wrd_brk">Total Transfer</th>
	                <th width="80" class="wrd_brk" style="text-align: center;"><? echo $k; ?></th>
	                <th width="90" class="wrd_brk"><? echo number_format($total_transfer,2); ?></th>
	            </tr>
	              <tr>
	                <th colspan="5" class="wrd_brk">Grand Total</th>
	                <th width="80" class="wrd_brk" style="text-align: center;"><? echo $y+$z+$k; ?></th>
	                <th width="90" class="wrd_brk"><? echo number_format($tot_qnty+$tot_qnty_issue+$total_transfer,2); ?></th>
	            </tr>
            </tfoot>
        </table>
    </fieldset>
	<?
	exit();
}



if($action=="iss_popup")
{
 	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];
    ?>
	<script>
	    $(document).ready(function() {
			var tbl_list_search_issue_1 = document.getElementById("tbl_list_search_issue_1");
			var tbl_list_search_issue_2 = document.getElementById("tbl_list_search_issue_2");
			if(tbl_list_search_issue_1){
				setFilterGrid('tbl_list_search_issue_1',-1);
			}
			if(tbl_list_search_issue_2){
				setFilterGrid('tbl_list_search_issue_2',-1);
			}
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
            </tr>
		</table>

        <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1">
            <thead>
            	<tr><th colspan="6" class="wrd_brk">Issue Details</th></tr>
            	<tr>
	                <th width="40" class="wrd_brk">SL</th>
	                <th width="110" class="wrd_brk">Issue Id</th>
	                <th width="120" class="wrd_brk">Issue Purpose </th>
	                <th width="100" class="wrd_brk">Barcode No</th>
	                <th width="80" class="wrd_brk">Total Roll</th>
	                <th width="90" class="wrd_brk">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:560px; max-height:250px; overflow-y:auto">
            <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1" id="tbl_list_search_issue_1">
			    <?
				$i=0; $tot_iss_qnty=0;$x=0;
                $sql="select a.id, a.issue_number_prefix_num, a.issue_purpose,c.barcode_no, count(c.id) as tot_roll, sum(c.qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
				group by a.id, a.issue_number_prefix_num, a.issue_purpose,c.barcode_no
				order by id";
                $result= sql_select($sql);
                $tot_iss_qnty=0;$tot_roll_issue=0;
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
                        <td width="110" class="wrd_brk center"><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
                        <td width="120" class="wrd_brk"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</td>
                        <td width="100" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
                        <td width="80" class="wrd_brk center"><? echo $row[csf('tot_roll')]; ?>&nbsp;</td>
                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                    <?
					$tot_iss_qnty += $row[csf('qnty')];
					$x++;
                }
                ?>
            </table>
            <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1">
            	<tfoot>
                	<tr>
	                	<th colspan="4" width="370" class="wrd_brk right">Total</th>
	                	<th width="80" class="wrd_brk" style="text-align: center;"><? echo $x; ?></th>
	                	<th width="90" class="wrd_brk"><? echo number_format($tot_iss_qnty,2); ?></th>
	                </tr>
                </tfoot>
            </table>
		</div>
		<table cellpadding="0" width="540" class="rpt_table" rules="all" border="1">
            <thead>
            	<tr><th colspan="6" class="wrd_brk">Transfer Details</th></tr>
            	<tr>
	                <th width="40" class="wrd_brk">SL</th>
	                <th width="110" class="wrd_brk">Transfer Id</th>
	                <th width="120" class="wrd_brk">Purpose </th>
	                <th width="100" class="wrd_brk">Barcode No</th>
	                <th width="80" class="wrd_brk">Total Roll</th>
	                <th width="90" class="wrd_brk">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:560px; max-height:250px; overflow-y:auto">
            <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1" id="tbl_list_search_issue_2">
			    <?
				$trans_sql="select d.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll, sum(c.qnty) as qnty
				from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,inv_item_transfer_mst d  where a.trans_id=b.trans_id and b.id=c.dtls_id and b.mst_id = d.id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active =1 and b.status_active = 1 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) group by d.transfer_system_id, b.mst_id ,c.barcode_no
				 union all
				 select a.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll,sum(c.qnty) as qnty
				 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				 where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.barcode_no in($barcode_nos) and b.from_order_id in($po_ids) and a.transfer_criteria in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
				  group by a.transfer_system_id, b.mst_id ,c.barcode_no
				   union all
				 select a.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll,sum(c.qnty) as qnty
				 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				 where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id
				 and c.barcode_no in($barcode_nos)
				 and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
				 group by a.transfer_system_id, b.mst_id ,c.barcode_no

				  order by mst_id";
				$trans_result=sql_select($trans_sql);
				$i=0; $tot_trans_iss_qnty = 0;$total_qnty=0;$y=0;$tot_roll=0;
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               		?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" class="wrd_brk"><? echo $i; ?></td>
                        <td width="110" class="wrd_brk center"><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</td>
                        <td width="120" class="wrd_brk"><? echo "Transfer"; ?>&nbsp;</td>
                        <td width="100" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
                        <td width="80" class="wrd_brk center"><? echo $row[csf('tot_roll')]; ?>&nbsp;</td>
                        <td width="90" class="wrd_brk right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                	<?
					$tot_trans_iss_qnty += $row[csf('qnty')];
					$y++;
                }
                $total_qnty = $tot_iss_qnty +  $tot_trans_iss_qnty;
                $tot_roll = $x +  $y;
                ?>
            </table>
            <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1">
            	<tfoot>
                	<tr>
	                	<th colspan="4" width="370" class="wrd_brk right">Total</th>
	                	<th width="80" class="wrd_brk" style="text-align: center;"><? echo $y; ?></th>
	                	<th width="90" class="wrd_brk"><? echo number_format($tot_trans_iss_qnty,2); ?></th>
	                </tr>
                </tfoot>
            </table>
		</div>
        <table cellpadding="0" width="540" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="4" class="wrd_brk">Grand Total :</th>
                <th width="80" class="wrd_brk" style="text-align: center;"><? echo $tot_roll; ?></th>
                <th width="90" class="wrd_brk" id="value_grey_qty"><? echo number_format($total_qnty,2); ?></th>
            </tfoot>
        </table>
    </fieldset>
	<?
	exit();
}

if($action=="stock_popup")
{
 	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$dataArray=explode("_",$data);
	// echo '<pre>';print_r($dataArray);die;
	$barcode_nos=$dataArray[16];
	$po_ids=$dataArray[17];
	// echo $barcode_nos;die;

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id in($po_ids)
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	order by a.id";
	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')];
			$poIds.=$row[csf('id')].",";
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$recvDtlsDataArr=array();
	$query="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, c.roll_no, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, c.roll_no, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, c.roll_no, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,4) and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, c.roll_no, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0";

	//echo $query;//die;
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}


	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr)) // split and production
	{
		$ref_barcode_nos = implode(",", $ref_barcode_arr);
		$barCond = $ref_barcode_no_cond = "";
		if($db_type==2 && count($ref_barcode_arr)>999)
		{
			$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
			foreach($ref_barcode_arr_chunk as $chunk_arr)
			{
				$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$ref_barcode_no_cond.=" and (".chop($barCond,'or ').")";

		}
		else
		{
			$ref_barcode_no_cond=" and c.barcode_no in($ref_barcode_nos)";
		}

		$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 $ref_barcode_no_cond");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
				if($barcode_nos !=""){
					$barcode_nos .= ",".$val[csf("barcode_no")];
				}
			}


			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
				}
			}
		}




		$recvDataArrTrans=array();$recvDataArr=array();
		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond";
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}

			$yarn_type_id_arr=  return_library_array("select id, yarn_type from product_details_master where status_active = 1 $yarn_prod_id_cond","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		}
		unset($recvDataT);
	}



	$ref_file="";$data_prod="";
	foreach($data_array as $row) // recv data array
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$row[csf('barcode_no')]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];


			//$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['roll_no']=$row[csf("roll_no")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			//$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			$data=$row[csf('barcode_no')]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			//$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['roll_no']=$row[csf("roll_no")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);
	// echo '<pre>';print_r($recvDtlsDataArr);die;

	// echo $barcode_nos;die;
	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty 
	from pro_roll_details c 
	where c.entry_form=61 and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select a.po_breakdown_id, c.barcode_no, c.qnty 
	from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c 
	where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and b.from_order_id in($po_ids)  and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id
	union all
	select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos)
	group by c.barcode_no, a.from_order_id");// and c.is_returned = 0

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();

	foreach($iss_qty_sql as $row)
	{
		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$row[csf('barcode_no')]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$mother_barcode_no."**".$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"];
		}


		$iss_qty_arr[$ref_file][$data_prod] +=$row[csf("qnty")];

		$issue_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";
	}
	unset($iss_qty_sql);
	// echo "<pre>"; print_r($iss_qty_arr);die;



	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids)");
	foreach($iss_rtn_qty_sql as $row)
	{
		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$row[csf('barcode_no')]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);
	// echo "<pre>"; print_r($iss_rtn_qty_sql);die;

	$trans_store_arr=return_library_array("SELECT c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
	union all
	select c.barcode_no, s.store_name  from inv_item_transfer_mst a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
	order by store_name, barcode_no","barcode_no","store_name");


    ?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $dataArray[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $dataArray[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $dataArray[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $dataArray[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $dataArray[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $dataArray[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $dataArray[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $dataArray[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $dataArray[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $dataArray[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $dataArray[15]; ?>&nbsp;</p></td>
            </tr>
		</table>

        <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="120">Store Name</th>
                <th width="100">Bacode No</th>
                <th width="80">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:500px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_stock_qnty=0;
                foreach($recvDtlsDataArr as $fileRefArr=>$fileRef)
                {
                	foreach ($fileRef as $data => $row) 
                	{
                		$datas=explode("**",$data);
						$barcode_no=$datas[0];

						$recv_qty_only=$row['recv'];
                		$issue_return=$iss_rtn_qty_arr[$fileRefArr][$data];
                		$iss_qty = $iss_qty_arr[$fileRefArr][$data].'<br>';
                		$recv_qty=$recv_qty_only + $issue_return;
                		$recv_qty = number_format($recv_qty,2,".","");
                		$iss_qty = number_format($iss_qty,2,".","");
						$stock_qty=$recv_qty-$iss_qty;
						// echo $recv_qty.'-'.$iss_qty.'<br>';
						// echo $stock_qty.'<br>';
						
	                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						if($stock_qty>0)
						{
							$i++;
		               		?>
		                   	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                        <td width="40"><? echo $i; ?></td>
		                        <td width="120"><p><? echo $trans_store_arr[$barcode_no];//$row[csf('store_name')]; ?>&nbsp;</p></td>
		                        <td width="100"><p><? echo $barcode_no; ?></p></td>
		                        <td width="80" align="center"><p><? echo $row['roll_no']; ?>&nbsp;</p></td>
		                        <td align="right"><? echo number_format($stock_qty,2); ?></td>
		                    </tr>
		                	<?
	                		$tot_stock_qnty+=$stock_qty;
	            		}
            		}
                }
            ?>
            </table>
		</div>
        <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="3">Roll Total :</th>
                <th width="80" style="text-align:center"><? echo $i; ?></th>
                <th width="134" id="value_grey_qty"><? echo number_format($tot_stock_qnty,2); ?></th>
            </tfoot>
        </table>
	</fieldset>
	<?
	exit();
}

if($action=="stock_popup__________backup")
{
 	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];

	$ref_barcode_no_cond=" and c.barcode_no in($barcode_nos)";
	$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 $ref_barcode_no_cond");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			if($barcode_nos !=""){
				$barcode_nos .= ",".$val[csf("barcode_no")];
			}
		}
		$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				//$split_barcode_qnty_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
			}
		}
	}

	$iss_sql = sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.is_returned = 0 and c.booking_without_order = 0
	union all
	select a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	union all
	select b.from_order_id as po_breakdown_id,c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and b.from_order_id in($po_ids)  and a.transfer_criteria  in (1,4) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id
	union all
	select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos)
	group by c.barcode_no, a.from_order_id");

	foreach ($iss_sql as $val)
	{
		$iss_qty_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		if($mother_barcode_arr[$val[csf("barcode_no")]] != "")
		{
			$iss_qty_arr[$mother_barcode_arr[$val[csf("barcode_no")]]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		}
	}

	$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
		union all
		select c.barcode_no, s.store_name  from inv_item_transfer_mst a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
				order by store_name, barcode_no","barcode_no","store_name");
    ?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
            </tr>
		</table>

        <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="120">Store Name</th>
                <th width="100">Bacode No</th>
                <th width="80">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:500px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$i=0; $tot_stock_qnty=0;
				$sql=" SELECT s.store_name, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, 1 as type from inv_receive_master a left join lib_store_location s on a.store_id=s.id, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0
					 union all
					 select s.store_name, c.barcode_no, c.roll_no, c.qnty, b.to_order_id as po_breakdown_id, 2 as type
					 from inv_item_transfer_mst a,  inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c
					 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and b.to_order_id in($po_ids) and a.transfer_criteria in (1,4) and c.booking_without_order = 0
					  union all 
					   select s.store_name, c.barcode_no, c.roll_no, c.qnty, b.to_order_id as po_breakdown_id, 2 as type
					  from order_wise_pro_details a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c 
					   where a.trans_id=b.to_trans_id and b.id=c.dtls_id and c.entry_form=83 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) 
					   and a.po_breakdown_id in($po_ids)
					 order by store_name, barcode_no";
               	//echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
					if($stock_qty>0)
					{
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($stock_qty,2); ?></td>
                    </tr>
                <?
                		$tot_stock_qnty+=$stock_qty;
            		}
                }

				/*$trans_sql="select b.mst_id, c.barcode_no, c.roll_no, c.qnty, a.po_breakdown_id
				from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.to_trans_id and b.id=c.dtls_id and c.entry_form=83 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) and c.booking_without_order = 0";

				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
					if($stock_qty>0)
					{
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($stock_qty,2); ?></td>
                    </tr>
                <?
					$tot_stock_qnty+=$stock_qty;
					}
                }*/
            ?>
            </table>
		</div>
        <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="3">Roll Total :</th>
                <th width="80" style="text-align:center"><? echo $i; ?></th>
                <th width="134" id="value_grey_qty"><? echo number_format($tot_stock_qnty,2); ?></th>
            </tfoot>
        </table>
	</fieldset>
	<?
	exit();
}

?>
