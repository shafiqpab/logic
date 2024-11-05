
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}



if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
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
		<fieldset style="width:820px;margin-left:4px;">
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
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'grey_fabric_receive_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>               
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
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

if ($action == "load_drop_down_floor")
{
	$data = explode("_", $data);
	$company_id = $data[0];

	echo create_drop_down("cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.production_process=2 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

function fnc_tempengine_barcode($table_name, $user_id, $entry_form, $ref_value_arr)
{
	global $con ;
	$numeless=count($ref_value_arr);
	$psql = "BEGIN PRC_TEMP_BARCODE_INSERT(:user_id,:type,:po_arr); END;";
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":user_id",$user_id);
	oci_bind_by_name($stmt,":type",$entry_form);
	oci_bind_array_by_name($stmt, ":po_arr", $ref_value_arr, $numeless, -1, SQLT_INT);
	oci_execute($stmt); 
	oci_commit($con);
	disconnect($con);
}

if($action=="generate_report_receive")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$year_id 			 =str_replace("'","",$cbo_year);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_knitting_source =str_replace("'","",$cbo_knitting_source);
	$cbo_floor_id        =str_replace("'","",$cbo_floor_id);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(e.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(e.insert_date,'YYYY')=$year_id";
		}
	}

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($txt_order_id) $str_cond .=" and e.id =$txt_order_id";
	if($cbo_knitting_source) $str_cond .=" and a.knitting_source=$cbo_knitting_source";

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$date_cond_rcv = " and a.receive_date between '$txt_date_from' and '$txt_date_to' ";
	}

	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_sql = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_sql as  $val) 
	{
		$company_array[$val[csf("id")]] = $val[csf("company_name")];
		$company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id,short_name from lib_supplier where status_active =1",'id','short_name');
	
	$con = connect();
	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = 1 and ENTRY_FORM=868");
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (559)");
    oci_commit($con);

    if ($cbo_floor_id>0) 
    {
    	$production_sql="SELECT b.barcode_no from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c
		where a.id=b.dtls_id and a.mst_id=c.id and c.entry_form=2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1 and a.floor_id=$cbo_floor_id";
		//echo $production_sql;die;
		$production_sql_result=sql_select( $production_sql);
		foreach ($production_sql_result as $key => $row) 
		{
			$productionBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		}
		fnc_tempengine_barcode("TMP_BARCODE_NO", 1, 868, $productionBarcodeArr);
		oci_commit($con);

		// Main query
		$receive_sql = "SELECT a.id,a.receive_date, a.recv_number,a.booking_no as delivery_challan, a.knitting_source, a.knitting_company, a.challan_no, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.po_breakdown_id, a.inserted_by,a.insert_date,d.barcode_no, d.roll_no, d.qnty as rcv_qnty, e.within_group, e.job_no, e.sales_booking_no,e.buyer_id,e.po_job_no, e.po_buyer,e.season_id,e.delivery_date, e.booking_type,e.booking_without_order, e.booking_entry_form,e.booking_id,e.style_ref_no,a.remarks,e.sales_booking_no,e.customer_buyer,d.entry_form 
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e, TMP_BARCODE_NO t 
		WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id  and t.barcode_no=d.barcode_no and t.userid=1 and t.entry_form=868 and d.is_sales=1 and d.entry_form in(58)  and a.entry_form in(58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 $date_cond_rcv $str_cond $year_cond ORDER BY a.receive_date";
		//echo $receive_sql;die;
		$receive_sql_arr=sql_select( $receive_sql);
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = 1 and ENTRY_FORM=868");
		oci_commit($con);
    }
    else
    {
    	// Main query
	    $receive_sql = "SELECT a.id,a.receive_date, a.recv_number,a.booking_no as delivery_challan, a.knitting_source, a.knitting_company, a.challan_no, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.po_breakdown_id, a.inserted_by,a.insert_date,d.barcode_no, d.roll_no, d.qnty as rcv_qnty, e.within_group, e.job_no, e.sales_booking_no,e.buyer_id,e.po_job_no, e.po_buyer,e.season_id,e.delivery_date, e.booking_type,e.booking_without_order, e.booking_entry_form,e.booking_id,e.style_ref_no,a.remarks,e.sales_booking_no,e.customer_buyer,d.entry_form 
	    FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e 
	    WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and d.is_sales=1 and d.entry_form in(58)  and a.entry_form in(58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 $date_cond_rcv $str_cond $year_cond ORDER BY a.receive_date";
	    //echo $receive_sql;
	    $receive_sql_arr=sql_select( $receive_sql);
    }

	foreach ($receive_sql_arr as  $val) 
	{
		$rcvBarcodeArr[$val[csf("barcode_no")]] =$val[csf("barcode_no")];
	}
	// echo "<pre>";print_r($rcvBarcodeArr);
	fnc_tempengine_barcode("TMP_BARCODE_NO", 1, 868, $rcvBarcodeArr);
	oci_commit($con);
	// echo "string";die;
	
	if(!empty($rcvBarcodeArr)) // production
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot,a.brand_id, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id 
		from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, TMP_BARCODE_NO t 
		where a.id=b.dtls_id and a.mst_id=c.id and t.barcode_no=b.barcode_no and t.userid=1 and t.entry_form=868 and c.entry_form=2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
		foreach ($production_sql as $row) 
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
			$prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
			$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
			$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
			$allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
			$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}
		//echo "<pre>";print_r($allDeterArr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 559, 1,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 559, 2,$allColorArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 559, 3,$allYarnProdArr, $empty_arr);
		oci_commit($con);
		//echo "string";die;

		$allDeterArr = array_filter($allDeterArr);
		if(!empty($allDeterArr))
		{
			/*$allDeterIds=implode(",",$allDeterArr);
	        $allDeterCond=""; $deterCond=""; 
	        if($db_type==2 && count($allDeterArr)>999)
	        {
	        	$allDeterArr_chunk=array_chunk($allDeterArr,999) ;
	        	foreach($allDeterArr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);	
	        		$deterCond.="  a.id in($chunk_arr_value) or ";	
	        	}

	        	$allDeterCond.=" and (".chop($deterCond,'or ').")";	
	        }
	        else
	        {
	        	$allDeterCond=" and a.id in($allDeterIds)";	 
	        }*/

			$construction_arr=array(); $composition_arr=array();
			// $sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $allDeterCond";
			$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from GBL_TEMP_ENGINE t, lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=559 and t.REF_FROM=1 and a.id=b.mst_id";
			$deter_array=sql_select($sql_deter);
			foreach( $deter_array as $row )
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];

				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
			unset($deter_array);
		}

		$allColorArr = array_filter($allColorArr);
		if(!empty($allColorArr))
		{
			/*$allColorIds=implode(",",$allColorArr);
	        $allColorCond=""; $colorCond=""; 
	        if($db_type==2 && count($allColorArr)>999)
	        {
	        	$allColorArr_chunk=array_chunk($allColorArr,999) ;
	        	foreach($allColorArr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);	
	        		$colorCond.=" id in($chunk_arr_value) or ";	
	        	}

	        	$allColorCond.=" and (".chop($colorCond,'or ').")";	
	        }
	        else
	        {
	        	$allColorCond=" and id in($allColorIds)";	 
	        }*/
			$color_array=return_library_array( "SELECT a.id, a.color_name from GBL_TEMP_ENGINE t, lib_color a where t.ref_val=a.id and t.user_id=$user_id and t.entry_form=559 and t.ref_from=2 and a.status_active=1", "id", "color_name");// $allColorCond
		}

		$allYarnProdArr = array_filter($allYarnProdArr);
		if(!empty($allYarnProdArr))
		{
			/*$allYarnProdArr=array_unique(explode(",",implode(",",$allYarnProdArr)));
			$allYarnProd_ids=implode(",",$allYarnProdArr);
	        $allYarnProd_Cond=""; $yProdCond=""; 
	        if($db_type==2 && count($allYarnProdArr)>999)
	        {
	        	$allYarnProdArr_chunk=array_chunk($allYarnProdArr,999) ;
	        	foreach($allYarnProdArr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);	
	        		$yProdCond.=" id in($chunk_arr_value) or ";	
	        	}
	        	$allYarnProd_Cond.=" and (".chop($yProdCond,'or ').")";	
	        }
	        else
	        {
	        	$allYarnProd_Cond=" and id in($allYarnProd_ids)";	 
	        }*/
			$yarn_sql=sql_select( "SELECT a.id, a.yarn_type, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.brand from GBL_TEMP_ENGINE t, product_details_master a where t.ref_val=a.id and t.user_id=$user_id and t.entry_form=559 and t.ref_from=3 and a.item_category_id=1");// $allYarnProd_Cond
			foreach ($yarn_sql as  $val) 
			{
				$yarn_data[$val[csf("id")]]["brand"] = $brand_array[$val[csf("brand")]];
				$yarn_data[$val[csf("id")]]["comp"] = $composition[$val[csf("yarn_comp_type1st")]]." ".$val[csf("yarn_comp_percent1st")]."%";
				$yarn_data[$val[csf("id")]]["yarn_type"] = $yarn_type[$val[csf("yarn_type")]];
			}
		}

		$all_book_id_arr =array_filter($all_book_id_arr);
		if(!empty($all_book_id_arr))
		{
			$book_id_cond="";
			if($db_type==2 && count($all_book_id_arr)>999)
			{
				$all_book_id_chunk=array_chunk($all_book_id_arr,999);
				$book_id_cond=" and";
				foreach($all_book_id_chunk as $book_id)
				{
					$book_id_cond.= "( a.id in(".implode(",",$book_id).") or";
				}
				$book_id_cond=chop($book_id_cond,"or");
				$book_id_cond.=")";
			}
			else
			{
				$book_id_cond=" and a.id in(".implode(",",$all_book_id_arr).")";
			}

			$booking_sql=sql_select("select a.id as book_id, a.booking_no, a.short_booking_type, b.division_id 
				from wo_booking_mst a, wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4) and b.booking_type in(1,4) $book_id_cond
				group by a.id, a.booking_no, a.short_booking_type, b.division_id");
			$booking_data=array();
			foreach($booking_sql as $row)
			{
				$booking_data[$row[csf("book_id")]]["short_type"]=$short_booking_type[$row[csf("short_booking_type")]];
				$booking_data[$row[csf("book_id")]]["division_id"].=$short_division_array[$row[csf("division_id")]].",";
			}

			unset($booking_sql);
		}
	}
		
	$data_array = array();
	foreach ($receive_sql_arr as $val) // Main loop array
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			if($val[csf("within_group")] ==2 )
			{
				$buyer_id = $val[csf("buyer_id")];
			}else{
				$buyer_id = $val[csf("po_buyer")];
			}

			$paramStr = $val[csf("knitting_source")]."__".$val[csf("knitting_company")]."__".$buyer_id."__".$val[csf("sales_booking_no")]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_count"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_prod_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["brand_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_lot"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_range_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["width"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["gsm"];

			$data_array[$val[csf("job_no")]][$val[csf("receive_date")]][$paramStr]["quantity"] +=  $val[csf("rcv_qnty")];
		}
	}
	// echo "<pre>";print_r($data_array);

	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = 1 and ENTRY_FORM=868");
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (559)");
    oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:1440px" id="main_body">
		<table width="1440" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="15" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="1410" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Receive Date</th>
					<th width="100">Knitting Company</th>
					<th width="100">Buyer</th>
					<th width="100">Booking No.</th>
					<th width="100">Sales Order No.</th>
					<th width="100">Yarn Count</th>
					<th width="100">Brand</th>
					<th width="100">Yarn Lot</th>					
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="80">Color Range</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="">Receive Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:1430px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="1410" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_receive=""; $total_issue="";
					if(!empty($data_array))
					{
						foreach($data_array as $sales_no => $sales_data)
						{
							foreach($sales_data as $rcv_date => $rcv_data)
							{
								foreach($rcv_data as $refStr => $row)
								{
									$refString = explode("__",$refStr);
									// echo "<pre>";print_r($refString);
									$knittingSource=$refString[0];
									$knitting_company_id=$refString[1];
									$buyer=$refString[2];
									$sales_booking_no=$refString[3];
									$yarn_count=$refString[4];
									$yarn_prod_id=$refString[5];
									$brand_id=$refString[6];
									$yarn_lot=$refString[7];
									$febric_descr=$refString[8];
									$color_id=$refString[9];
									$color_range_id=$refString[10];
									$dia=$refString[11];
									$gsm=$refString[12];
									
									$yarn_brand_name = ""; //$yarn_comp_name = $yarn_type_name="";
									if($yarn_prod_id)
									{
										$yarn_arr = explode(",", $yarn_prod_id);
										foreach ($yarn_arr as $value) 
										{
											$yarn_brand_name .= $yarn_data[$value]["brand"].",";
											//$yarn_comp_name .= $yarn_data[$value]["comp"].",";
											//$yarn_type_name .= $yarn_data[$value]["yarn_type"].",";
										}
										$yarn_brand_name =implode(",",array_unique(explode(",",chop($yarn_brand_name,","))));
									}
									else
									{
										$yarn_brand_name =$brand_array[$brand_id];
									}
									//$yarn_comp_name =implode(",",array_unique(explode(",",chop($yarn_comp_name,","))));
									//$yarn_type_name =implode(",",array_unique(explode(",",chop($yarn_type_name,","))));

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $i;?></td>
										<td width="70" align="center"><? echo change_date_format($rcv_date);?></td>
										<td width="100" align="center"><? 
		                                	if($knittingSource==1)
		                                	{
		                                		echo $company_short_array[$knitting_company_id];
		                                	}
		                                	else
		                                	{
		                                		echo $supplier_arr[$knitting_company_id];
		                                	}
		                                	?></td>
										<td width="100" align="center"><? echo $buyer_arr[$buyer];?></td>
										<td width="100" align="center"><? echo $sales_booking_no;?></td>
										<td width="100" align="center"><? echo $sales_no;?></td>
		                                <td width="100" align="center">
		                                	<? 
		                                	$count_name="";
		                                	foreach (explode(",", $yarn_count) as  $count) 
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?>
		                                </td>
		                                <td width="100"><p><? echo $yarn_brand_name;?></p></td>
		                                <td width="100"><p>&nbsp;<? echo $yarn_lot;?></p></td>
		                                <td width="100"><? echo $construction_arr[$febric_descr];?></td>
		                                <td width="140"><p><? echo $composition_arr[$febric_descr];?></p></td>
		                                <td width="80">
	                                		<p><? 
	                                		$color_names="";
	                                		foreach (explode(",", $color_id) as $key => $color) {
	                                			$color_names .= $color_array[$color].",";
	                                		}
	                                		echo chop($color_names,",");
	                                		?>
	                                		</p>
		                                </td>
		                                <td width="80"><? echo $color_range[$color_range_id];?></td>
		                                <td width="50"><? echo $dia;?></td>
		                                <td width="50"><? echo $gsm;?></td>
		                                <td width="" align="right"><? echo number_format($row["quantity"],2,'.',''); ?></td>
									</tr>
									<?
									$total_roll_weight += $row["quantity"];
									$i++;
								}
							}
						}
					}
					else
					{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="1410" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50">Total:</th>
					<th width="" id="value_total_receive_qnty" align="right"><? echo number_format($total_roll_weight,2,'.','');?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info For Grey Store Wise Receive Issue Summary Sales", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$barcode_nos = $barcode_nos;
	$company_id  = $company_id;
	$store_id    = $store_id;
	$entry_form  = $entry_form;
	
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	?>
	<fieldset style="width:1040; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="12"><b>Barcode Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Knitting Production ID</th>
                        <th width="80">Roll No</th>
                        <th width="80">Roll Weight</th>
                        <th width="80">Barcode No</th>
                        <th width="140">Store</th>
                        <th width="80">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack</th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" id="table_body">
                <?	
				if(!empty($barcode_nos))
				{
					$barcodeData=sql_select("SELECT a.recv_number, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where
					a.id = b.mst_id and b.id=c.dtls_id and a.entry_form = 2 and c.entry_form = 2 and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 
					and c.is_deleted = 0 and c.barcode_no in ($barcode_nos)");

					$barcodeReceive=sql_select("SELECT a.id, c.store_id,c.floor_id, c.room, c.rack, c.self, c.bin_box, d.barcode_no, d.qnty,d.roll_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and d.is_sales=1 and d.entry_form in($entry_form)  and a.entry_form in($entry_form) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 and d.barcode_no in ($barcode_nos)  ORDER BY a.receive_date");

					$roll_details_array=array();
					foreach ($barcodeReceive as $rows) 
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					$barcodeIssue=sql_select("SELECT a.id,b.floor_id, b.room, b.rack, b.self, b.bin_box,b.bin_box,d.qnty,d.roll_no,d.barcode_no
					from inv_issue_master a, inv_grey_fabric_issue_dtls b,  pro_roll_details d, fabric_sales_order_mst e 
					where a.id = b.mst_id  and b.id = d.dtls_id and a.id = d.mst_id and d.po_breakdown_id = e.id and a.entry_form in($entry_form) and d.entry_form in($entry_form) and d.status_active =1 and b.status_active =1 and a.status_active =1 and d.barcode_no in ($barcode_nos) order by a.issue_date");

					foreach ($barcodeIssue as $rows) 
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_name")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					$transfer_sql=sql_select("SELECT a.id, c.store_id,c.floor_id, c.room, c.rack, c.self, c.bin_box, d.barcode_no, d.qnty,d.roll_no 
					FROM inv_item_transfer_mst a,inv_item_transfer_dtls b, inv_transaction c, pro_roll_details d, fabric_sales_order_mst e 
					WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and a.from_order_id = e.id and a.entry_form in($entry_form)  and d.entry_form in($entry_form) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category =13 and d.barcode_no in ($barcode_nos)
					ORDER BY a.transfer_date");

					foreach ($transfer_sql as $rows) 
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					//floorSql
					$floorSql = "
					SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
					FROM lib_floor_room_rack_mst a
					INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
					WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					//echo $roomSql;
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//selfSql
					$selfSql = "
					SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
					FROM lib_floor_room_rack_mst a
					INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
					WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//binSql
					$binSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');

					
				}else{
					 echo "Barcode Not Found";
				}
                if(empty($barcodeData)){
                	echo "Barcode Not Found";
                }
				$i=1;
				foreach ($barcodeData as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120" align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="80" align="center"><p><? echo $roll_details_array[$row[csf("barcode_no")]]['roll_no']; ?></p>&nbsp;</td>
                        <td width="80" align="right"><? echo number_format($roll_details_array[$row[csf("barcode_no")]]['qnty'],2,'.',''); ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="140" align="center"><? echo $store_arr[$store_id]; ?></td>
                        <td width="80" align="center"><? echo $floorDetails[$roll_details_array[$row[csf("barcode_no")]]['floor_id']]; ?></td>
                        <td width="80" align="center"><? echo $roomDetails[$roll_details_array[$row[csf("barcode_no")]]['room']]; ?></td>
                        <td width="80" align="center"><? echo $rackDetails[$roll_details_array[$rows[csf("barcode_no")]]['rack']]; ?></td>
                        <td width="80" align="center"><? echo $selfDetails[$roll_details_array[$rows[csf("barcode_no")]]['self']]; ?></td>
                        <td width="80" align="center"><? echo $binDetails[$roll_details_array[$rows[csf("barcode_no")]]['bin_box']]; ?></td>
                    </tr>
                <?
                $total_qty+=$roll_details_array[$row[csf("barcode_no")]]['qnty'];
                $i++;
                }
                ?>
                <tfoot>
                	<tr>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo number_format($total_qty,2); ?></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                    </tr>
                    
                </tfoot>
            </table>
		</div>
	</fieldset>	
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <?
	exit();
}
?>
