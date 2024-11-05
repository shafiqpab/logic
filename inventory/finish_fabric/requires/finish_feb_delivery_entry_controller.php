<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action=="fabric_sales_order_popup")
{
	echo load_html_head_contents("Fabric Sales Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:1100px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1000px; margin-left:3px">
				<legend>Enter search words</legend> 

				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="960" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th>Style Ref. No</th>                    
						<th>Sales Date Range</th>                    
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
						</th> 
					</thead>
					<tr>
						<td align="center">
							<? echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0); ?>
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_sale_order_no" id="txt_sale_order_no" />	
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"
							style="width:70px" readonly>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_sale_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_fso_search_list_view', 'search_div', 'finish_feb_delivery_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="center"></div> 
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data 				= explode("_",$data);
	$company_id 		= $data[0];
	$fso_no		 		= trim($data[1]);
	$txt_booking_no		= trim($data[2]);
	$txt_style_no		= trim($data[3]);
	$cbo_within_group	= trim($data[4]);	
	$date_from 			= trim($data[5]);
	$date_to 			= trim($data[6]);

	$company_arr 	= return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$supplier_arr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr 		= return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');
	$location_arr 	= return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	
	$search_field_cond="";
	$search_field_cond .= ($txt_booking_no != "")?" and d.sales_booking_no like '%" . $txt_booking_no . "'":"";
	$search_field_cond .= ($fso_no!= "")?" and d.job_no_prefix_num=$fso_no":"";
	$search_field_cond .= ($txt_style_no != "")?" and d.style_ref_no like '%" . $txt_style_no . "%'":"";

	$date_cond = '';
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and d.within_group=$within_group";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";

	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date)";
		$batch_field="group_concat(c.batch_no)";
		$order_id_field="group_concat(b.order_id)";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY')";
		$batch_field="LISTAGG(cast(c.batch_no as varchar(4000)), ',') WITHIN GROUP (ORDER BY c.id)";
		$order_id_field="LISTAGG(b.order_id, ',') WITHIN GROUP (ORDER BY b.order_id)";
	}

	$sql = "select a.company_id, a.knitting_source, a.knitting_company,'' as year,b.buyer_id, sum(b.receive_qnty) as recv_qty, $batch_field as batch_no,b.batch_id, $order_id_field as order_id,d.within_group,d.sales_booking_no,d.style_ref_no,d.job_no_prefix_num,d.job_no,d.booking_date,d.id,d.po_job_no,d.po_company_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,fabric_sales_order_mst d where a.company_id=$company_id and a.entry_form=7 and a.id=b.mst_id and b.batch_id=c.id and c.sales_order_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales=1 $search_field_cond $within_group_cond group by a.company_id,a.knitting_source, a.knitting_company,b.buyer_id, b.batch_id,d.within_group,d.sales_booking_no,d.style_ref_no,d.job_no_prefix_num, d.job_no,d.booking_date,d.id,d.po_job_no,d.po_company_id"; 
	$result = sql_select($sql);	

	?>
	<style type="text/css">
	.rpt_table tr{ text-decoration:none; cursor:pointer; }
	.rpt_table tr td{ text-align: center; }
</style>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
	<thead>
		<th width="40">SL</th>
		<th width="90">Sales Order No</th>
		<th width="60">Year</th>
		<th width="80">Within Group</th>
		<th width="70">Buyer</th>
		<th width="120">Booking No</th>
		<th width="80">Booking date</th>
		<th width="110">Style Ref.</th>
		<th>Location</th>
	</thead>
</table>
<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
	id="tbl_list_search">
	<?
	$i = 1;
	if(!empty($result)){
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$buyer = $buyer_arr[$row[csf('buyer_id')]];

			$booking_data = $row[csf('id')]. "**" . $row[csf('sales_booking_no')]."**".$row[csf('company_id')]."**".$row[csf('within_group')]."**".$row[csf('buyer_id')]."**".$row[csf('job_no')]."**".$row[csf('batch_id')]."**".implode(",",array_unique(explode(",",$row[csf('batch_no')])))."**".$row[csf('po_job_no')]."**".$row[csf('po_company_id')]."**".$company_arr[$row[csf('po_company_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $booking_data; ?>');">
				<td width="40"><? echo $i; ?></td>
				<td width="90"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
				<td width="70"><p><? echo $buyer; ?></p></td>
				<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
				<td width="80"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
				<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
			</tr>
			<?
			$i++;
		}
	}else{
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<th colspan="9">No data found</th>
		</tr>
		<?
	}
	?>
</table>
</div>
<?
exit();
}

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);

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

	$sql="select a.id mst_id,a.recv_number,b.id dtls_id,b.prod_id,b.batch_id,b.order_id,b.body_part_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.dia_width_type,b.is_sales,b.uom,sum(b.receive_qnty) receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.order_id='".$data[0]."' and a.item_category=2 and a.entry_form=7 and a.status_active=1 and b.status_active=1 group by a.id,a.recv_number,b.id,b.prod_id,b.batch_id,b.order_id,b.body_part_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.dia_width_type,b.is_sales,b.uom";
	$data_array=sql_select($sql);
	$batch_id_arr = $color_id_arr = $order_id_arr = $sales_id_arr = array();
	foreach($data_array as $row)
	{
		$batch_id_arr[] = $row[csf("batch_id")];
		$color_id_arr[] = $row[csf("color_id")];
		$order_id_arr[] = $row[csf("order_id")];
	}

	$delivery_arr = array();
	if(!empty($order_id_arr)){
		$delivery_sql = "select a.id,a.sys_number,b.batch_id,b.bodypart_id,b.color_id,b.determination_id,b.gsm,b.dia,b.order_id,b.product_id,b.width_type,sum(b.current_delivery) delivery_qnty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.entry_form=224 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_sales=1 and order_id in(".implode(",",$order_id_arr).") group by a.id,a.sys_number,b.batch_id,b.bodypart_id,b.color_id,b.determination_id,b.gsm,b.dia,b.order_id, b.product_id,b.width_type order by a.id desc";
		$deliveryData = sql_select($delivery_sql);
		foreach ($deliveryData as $row) {
			$delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]] += $row[csf("delivery_qnty")];
		}
	}


	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
	}
	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).")",'id','color_name');
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="380">
		<thead>
			<th width="30">SL</th>
			<th width="70">Batch</th>
			<th>Fabric Description</th>
			<th width="40">UOM</th>
			<th width="70">Dia/ W. Type</th>
			<th width="60">Color</th>
			<th width="50">Qnty</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($data_array as $row)
			{  
				$delivery_qnty = $delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]];
				$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('fabric_description_id')]]."**".$row[csf('gsm')]."**".$row[csf('width')]."**".$row[csf('fabric_description_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('body_part_id')]."**".$row[csf('mst_id')]."**".$row[csf('dia_width_type')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('batch_id')]."**".$row[csf('is_sales')]."**".$row[csf('color_id')]."**".$row[csf('receive_qnty')]."**".$delivery_qnty."**".$row[csf('recv_number')]."**".$row[csf('dtls_id')]."**".$row[csf("prod_id")];
				$fab_desc = $body_part[$row[csf('body_part_id')]].", ".$composition_arr[$row[csf('fabric_description_id')]].", ".$row[csf('gsm')].", ".$row[csf('width')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
					<td align="center"><? echo $i; ?></td>
					<td <? echo $batch_dispaly; ?>><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
					<td><? echo $fab_desc; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td <? echo $dia_w_type_dispaly; ?> align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
					<td align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit;
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	//echo "10**";

	extract(check_magic_quote_gpc( $process ));
	// Data insert block start here
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		if( str_replace("'","",$update_mst_id) == "" ) 
		{
			
			$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);
			//echo $cbo_company_id;
			$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'FDG',224,date("Y",time())));

			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,entry_form,fin_pord_type,delevery_date,company_id,location_id,buyer_id,inserted_by,insert_date,remarks";

			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',224,".$cbo_order_status.",".$txt_delivery_date.",".$cbo_company_id.",".$cbo_location.",".$hdn_buyer_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$txt_remarks.")";
		}

		$field_array_dtls="id,mst_id,entry_form,grey_sys_id,sys_dtls_id,grey_sys_number,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,batch_id,inserted_by,insert_date,is_sales,bodypart_id,color_id,width_type";
		$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
		$data_array_dtls="(".$dtls_id.",".$id.",224,".$hidden_receive_id.",".$hidden_receive_dtls_id.",".$hidden_receive_number.",".$hdn_batch_id.",".$hidden_product_id.",".$txt_po_job.",".$hdn_fso_id.",".$txt_fabric_description_id.",".$txt_gsm.",".$txt_dia.",".$txt_Delivery_qnty.",".$hdn_batch_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,".$cbo_body_part.",".$txt_color_id.",".$txt_dia_width_type.")";

		$rID=$rID2=true;
		if( str_replace("'","",$update_mst_id) == "" )
		{
			$rID=sql_insert("pro_grey_prod_delivery_mst",$field_array,$data_array,1); 
		}
		$rID2=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**insert into pro_grey_prod_delivery_mst (".$field_array.") values ".$data_array;die;
		//echo "10**".$rID."##".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID2 )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 )
			{
				oci_commit($con); 
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
	}
}

?>
