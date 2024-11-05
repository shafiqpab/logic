<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$woven_issue_basis = array(1=>'Batch Basis',2=>'Requisition Basis');
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
$field_level_data_arr =  $_SESSION['logic_erp']['data_arr'][19];
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fabric_issue_controller",$data);
}

if($action=="company_wise_report_button_setting"){

	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=126 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print').hide();\n";
	echo "$('#print3').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}
			if($id==85){echo "$('#print3').show();\n";}
		}
	}
	else
	{
		echo "$('#print').hide();\n";
		echo "$('#print3').hide();\n";
	}

	exit();
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		$userCredential = sql_select("SELECT working_unit_id FROM user_passwd where id='$user_id'");
		$working_company_id = $userCredential[0][csf('working_unit_id')];
		//echo $working_company_id;
		if ($working_company_id >0)
		{
	    	$working_company_credential_cond = " and comp.id in($working_company_id)";
	    	if (count(explode(",", $working_company_id))==1)
	    	{
	    		$selected=$working_company_id;
	    	}
		}
		else
		{
			$selected=0;
		}

		echo create_drop_down( "cbo_sewing_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $working_company_credential_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", $selected, "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_sewing_location','sewinglocation_td');","" );
	}
	else if($data[0]==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select id, supplier_name from lib_supplier where find_in_set(22,party_type) and find_in_set($company_id,tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');" );
		}
		else
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');" );
		}
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
	}

	exit();
}
if($action == "load_drop_down_cutting"){
	$sql = "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 and company_id=$data order by floor_name";
	echo create_drop_down( "cbo_cutting_floor", 170, $sql,"id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}
if ($action=="load_drop_down_sewing_location")
{

	$locationArr=return_library_array( "select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1",'id','location_name');
	$PreviligLocationArr=return_library_array( "select id,company_location_id from user_passwd where id='$user_id' and is_deleted=0  and status_active=1",'id','company_location_id');
	if($PreviligLocationArr[$user_id]!=""){$PreviligLocationCond="and id in($PreviligLocationArr[$user_id])";}
	//echo $PreviligLocationCond;

	if(count($locationArr)==1)
	{
		echo create_drop_down( "cbo_sewing_location", 170, "select id,location_name from lib_location where company_id='$data' $PreviligLocationCond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "-- Select Location --", 0, "" );
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_sewing_location", 170, "select id,location_name from lib_location where company_id='$data' $PreviligLocationCond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
		exit();
	}
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, '',1 );
	exit();

}


if($action=="load_drop_down_gmt_item")
{

	if($db_type==0)
	{
		$gmt_item=return_field_value("group_concat(a.gmts_item_id) as gmt_item_id","wo_po_details_master a, wo_po_break_down b","a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.id in($data)","gmt_item_id");
	}
	else
	{
		$gmt_item=return_field_value("listagg(cast(a.gmts_item_id as varchar2(4000)) ,',')  within group(order by b.id) as gmt_item_id","wo_po_details_master a, wo_po_break_down b","a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.id in($data)","gmt_item_id");
	}

	$item_id_arr=explode(",",$gmt_item);
	if(count($item_id_arr)==1) { $dissable=1; $selected_item=$gmt_item;} else { $dissable=0;$selected_item="";}
	//echo $gmt_item;die;
	echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Gmt. Item --", $selected_item, "",0,$gmt_item );
	exit();
}
if($action=="varible_setting_wvn_style_wise")
{
	$sql_variable_inv_wvn_style_wise=sql_select("select id, user_given_code_status  from variable_settings_inventory where company_name=$data and variable_list=34 and status_active=1 and is_deleted=0 and item_category_id = 3");

 	echo $sql_variable_inv_wvn_style_wise[0][csf("user_given_code_status")];
	//if(count($sql_variable_inv_wvn_style_wise)>0){echo 1;}else{echo 0;}
	die;
}

if($action == "requisition_batch_lot_popup")
{
	echo load_html_head_contents("Batch/Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<?
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	//$data_array=sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order, b.batch_id from product_details_master a, inv_transaction b,pro_batch_create_mst c where a.id=b.prod_id and b.batch_id=c.id and c.company_id=$data[1] and b.batch_lot='$data[0]' $batch_id_cond and a.item_category_id=3 and b.item_category=3 and b.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order,b.batch_id");
	$po_data = sql_select("SELECT a.id, b.mst_id as batch_id, a.po_number, a.job_no_mst, c.style_ref_no from wo_po_break_down a, pro_batch_create_dtls b, wo_po_details_master c where a.id=b.po_id and c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.company_name= $cbo_company_id and c.job_no = '$job_no'");
	$all_po_id ="";
	foreach ($po_data as $value)
	{
		if($all_po_id=="") $all_po_id=$value[csf('batch_id')]; else $all_po_id.=",".$value[csf('batch_id')];
		$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
		$batch_ref_arr[$value[csf('batch_id')]]["po_id"] .=  $value[csf("id")].", ";
		$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
		$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
	}
 	$all_po_id = implode(",",array_unique(array_filter(explode(",", $all_po_id))));
	$fabric_desc = preg_replace('/\s+/', '', $cbo_fabric_desc);

	$nameArray = sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order, b.batch_id,b.batch_lot,c.id as batch_id from product_details_master a, inv_transaction b,pro_batch_create_mst c where a.id=b.prod_id and b.batch_id=c.id and c.company_id=$cbo_company_id and a.item_category_id=3 and b.item_category=3 and b.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and replace (a.product_name_details, ' ', '') = '".$fabric_desc."' group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order,b.batch_id,b.batch_lot,c.id");

	//$nameArray = sql_select("SELECT a.batch_lot, b.batch_id, b.order_id, d.color_id from inv_transaction a join pro_finish_fabric_rcv_dtls b on a.id = b.trans_id join product_details_master c on c.id = b.prod_id join pro_batch_create_mst d on d.id=b.batch_id where a.company_id =$cbo_company_id and a.store_id=$cbo_store_name and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and  a.batch_id is not null and replace (c.product_name_details, ' ', '') = '".$fabric_desc."' group by a.batch_lot, b.batch_id, b.order_id, d.color_id");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table" >
        <thead>
        <tr><td colspan="6" align="center"> Batch/Lot For <strong><? echo $cbo_fabric_desc ?></strong></td></tr>
        <tr>
            <th width="40">SL</th>
            <th width="100">Batch/Lot</th>
            <th width="120">Job No</th>
            <th width="100">Style No</th>
            <th width="100">PO No</th>
            <th width="100">Color</th>
            <input type="hidden" id="hidden_data">
        </tr>
        </thead>
    </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table" id="tbl_list_search" >
    <?
		$i=1;

		foreach($nameArray as $row)
		{
			$po_nos = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$row[csf('batch_id')]]["po_number"],", ")))));
			$po_id= implode(", ",array_unique(array_filter(explode(",", chop($batch_ref_arr[$row[csf('batch_id')]]["po_id"],", ")))));

			$style_ref = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$row[csf('batch_id')]]["style_ref_no"],", ")))));
			$job_no = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$row[csf('batch_id')]]["job_no_mst"],", ")))));

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$js_set_data = $row[csf('id')]."**".$row[csf('current_stock')]."**".$row[csf('body_part_id')]."**".$unit_of_measurement[$row[csf('unit_of_measure')]]."**".$row[csf('booking_without_order')]."**".$row[csf('batch_id')]."**".$row[csf('color')]."**".$row[csf('dia_width')]."**".$row[csf('weight')]."**".$row[csf('batch_lot')].'**'.$po_nos.'**'.$po_id;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $js_set_data;?>')">
				<td width="40" align="center"><? echo $i; ?></td>
				<td width="100" align="center"><? echo $row[csf('batch_lot')]; ?></td>
				<td width="120" style="word-break:break-word; word-wrap: break-word;"><p><? echo $job_no; ?></p></td>
				<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $style_ref; ?></p></td>
				<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $po_nos; ?></p></td>
				<td width="100"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
			</tr>
			<?
			$i++;
		}
	?>
    </table>

	<?
}

if ($action=="batch_lot_popup")
{
	echo load_html_head_contents("Batch/Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(batch_id,company_id,store_id,booking_no,batchLot_no,job_no,allPoids,hdn_variable,issue_purpose,colorId)
		{
			$('#hidden_batchLot_no').val(batchLot_no);
			$('#hidden_batch_id').val(batch_id);
			$('#txt_company_id').val(company_id);
			$('#hidden_store_id').val(store_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_job_no').val(job_no);
			$('#hidden_poIds').val(allPoids);
			$('#hidden_colorId').val(colorId);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:850px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:820px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" rules="1" border="1" width="770" class="rpt_table">
                <thead>
					<tr>
						<th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
					<tr>
						<th>Booking Type</th>
						<th>Buyer</th>
						<th>Batch/Lot</th> 
						<th>Job No</th>
						<th>Int. Ref</th>
						<th>Style No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">

							<input type="hidden" name="txt_store_id" id="txt_store_id" class="text_boxes" value="<? echo $cbo_store_name; ?>">

							<input type="hidden" name="hidden_batchLot_no" id="hidden_batchLot_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_store_id" id="hidden_store_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_poIds" id="hidden_poIds" class="text_boxes" value="">
							<input type="hidden" name="hidden_colorId" id="hidden_colorId" class="text_boxes" value="">
						</th>
					</tr>
                </thead>
                <tr class="general">
                	<td align="center" width="120px">
						<?
						if($cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==64){}else if($cbo_issue_purpose==8){$selectedId=3;}else{$selectedId=0;}
						$booking_type_arr=array(0=>"--Select--",1=>"Main Fabric",2=>"Sample With Order",3=>"Sample Without Order");
						echo create_drop_down( "cbo_booking_type", 150, $booking_type_arr,"",0, "--Select--", $selectedId,0,0 );
						?>
					</td>
                    <td id="store_td">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, '',0 );
                        ?>
                    </td>
                   <!--  <td align="center">
						<?
							//$search_by_arr=array(1=>"Batch/Lot No",2=>"Job No.",3=>"Style No.",4=>"PO NO");
							//$search_by_arr=array(1=>"Batch/Lot No",2=>"Job No.",3=>"Style No.");
							//$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							//echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td> -->

                    <td align="center">
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_job" id="txt_search_job" />
                    </td>
					<td align="center">
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_int_ref" id="txt_search_int_ref" />
                    </td>

                    <td align="center">
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_style" id="txt_search_style" />
                    </td>


                    <!-- <td align="center" id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td> -->
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_store_id').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('txt_search_job').value+'_'+<? echo $hdn_variable_setting_status; ?>+'_'+document.getElementById('txt_search_int_ref').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('cbo_booking_type').value+'_'+<? echo $cbo_issue_purpose; ?>+'_'+document.getElementById('cbo_string_search_type').value, '<? if($hdn_variable_setting_status==1){echo 'create_style_search_list_view';}else{echo 'create_batchlot_search_list_view';} ?>', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
           <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
        </fieldset>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batchlot_search_list_view")
{
	$data = explode("_",$data);
	/*$search_string="%".trim($data[0])."%";
	$search_by=$data[1];*/
	$company_id =$data[0];
	$buyer_id =$data[1];
	$store_id =$data[2];
	$batchNO ="%".trim($data[3])."%";
	$jobNO ="%".trim($data[4])."%";
	$hdn_variable_setting_status =$data[5];
	$styleNO ="%".trim($data[6])."%";
	$booking_type =$data[7];
	$cbo_issue_purpose =$data[8];

	if($buyer_id) $buyer_conds = " and c.buyer_name = '$buyer_id'";
	if($buyer_id) $buyer_conds2 = " and d.buyer_id = '$buyer_id'";

	$search_field_cond="";$search_field_cond_2="";
	if($data[3]) $search_field_cond="and batch_lot like '$batchNO'";
	if($data[3]) $search_field_cond_2="and a.batch_no like '$batchNO'";
	if($data[4])$job_cond="and c.job_no like '%$jobNO'";
	if($data[6])$style_cond="and c.style_ref_no like '%$styleNO'";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	if($buyer_conds !="" || $job_cond !="" || $style_cond !="")
	{

		$po_data = sql_select("SELECT a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no ,c.buyer_name
		  from wo_po_break_down a,pro_batch_create_dtls b,wo_po_details_master c
		  where  a.id=b.po_id and c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 $job_cond $style_cond $buyer_conds");
		$all_po_id ="";
		foreach ($po_data as $value)
		{
			$bathId_arr[$value[csf('batch_id')]] = $value[csf('batch_id')];
			if($all_po_id=="") $all_po_id=$value[csf('batch_id')]; else $all_po_id.=",".$value[csf('batch_id')];
			$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
		}

			$bathId_arr=array_unique(explode(',',implode(',',$bathId_arr)));
			if($db_type==2 &&  count($bathId_arr)>999)
			{
				$batchId_chunk=array_chunk($bathId_arr, 999);
				foreach($batchId_chunk as $row)
				{
					$batchId_ids=implode(",", $row);
					if($all_batchId_cond=="")
					{
						$all_batchId_cond.=" and (b.id in ($batchId_ids)";
						$all_batchId_cond2.=" and (b.to_batch_id in ($batchId_ids)";

					}
					else
					{
						$all_batchId_cond.=" or b.id in ($batchId_ids)";
						$all_batchId_cond2.=" or b.to_batch_id in ($batchId_ids)";
					}

				}
				$all_batchId_cond.=")";
				$all_batchId_cond2.=")";

			}
			else
			{

				$all_batchId_cond=" and b.id in(".implode(",",$bathId_arr).")";
				$all_batchId_cond2=" and b.to_batch_id in(".implode(",",$bathId_arr).")";
			}

	}

	if($db_type==0)
	{
		$sql = "select x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id
				from (
				SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id from inv_receive_master d, inv_transaction a, pro_batch_create_mst b, pro_batch_create_dtls c where d.id=a.mst_id and a.batch_id = b.id and b.id = c.mst_id	and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and a.batch_id <>'' $all_batchId_cond $buyer_conds2 $search_field_cond group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id
			union all
			SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id
			from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c
			where a.id=b.to_batch_id and b.mst_id=c.id and c.to_company=$company_id and a.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2
			group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id) x
 		group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id";
	}
	else
	{
		$sql = "SELECT x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id
		from (
		SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id from inv_receive_master d, inv_transaction a, pro_batch_create_mst b, pro_batch_create_dtls c where d.id=a.mst_id and a.batch_id = b.id and b.id = c.mst_id and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id
		union all
		SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id
		from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c
		where a.id=b.to_batch_id and b.mst_id=c.id and c.to_company=$company_id and a.status_active=1
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2
		group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id) x
		group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id";
	}

	$nameArray=sql_select( $sql );

	if($buyer_conds =="" && $job_cond =="" && $style_cond =="")
	{
		foreach($nameArray as $selectResult)
		{
			$result_batch_arr[$selectResult[csf('batch_id')]] = $selectResult[csf('batch_id')];
		}

		$all_result_batch_arr = array_filter($result_batch_arr);
		if(count($all_result_batch_arr)>0)
		{
			$all_result_batch_nos = implode(",", $all_result_batch_arr);
			$all_result_batch_no_cond="";$all_recv_batch_no_cond="";$all_issue_batch_no_cond="";$all_issueRcvRtn_batch_no_cond=""; $batchCond="";$recvBatchCond="";$issueBatchCond="";$issueRtnBatchCond="";
			if($db_type==2 && count($all_result_batch_arr)>999)
			{
				$all_result_batch_arr_chunk=array_chunk($all_result_batch_arr,999) ;
				foreach($all_result_batch_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$batchCond.="  b.mst_id in($chunk_arr_value) or ";
					$recvBatchCond.="  b.batch_id in($chunk_arr_value) or ";
					$issueBatchCond.="  a.batch_id in($chunk_arr_value) or ";
					$issueRtnBatchCond.="  a.batch_id_from_fissuertn in($chunk_arr_value) or ";
				}

				$all_result_batch_no_cond.=" and (".chop($batchCond,'or ').")";
				$all_recv_batch_no_cond.=" and (".chop($recvBatchCond,'or ').")";
				$all_issue_batch_no_cond.=" and (".chop($issueBatchCond,'or ').")";
				$all_issueRcvRtn_batch_no_cond.=" and (".chop($issueRtnBatchCond,'or ').")";
			}
			else
			{
				$all_result_batch_no_cond=" and b.mst_id in($all_result_batch_nos)";
				$all_recv_batch_no_cond=" and b.batch_id in($all_result_batch_nos)";
				$all_issue_batch_no_cond=" and a.batch_id in($all_result_batch_nos)";
				$all_issueRcvRtn_batch_no_cond=" and a.batch_id_from_fissuertn in($all_result_batch_nos)";
			}
			$po_data_2 = sql_select("SELECT a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no ,buyer_name
			from wo_po_break_down a,pro_batch_create_dtls b,wo_po_details_master c
			where  a.id=b.po_id and c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 $all_result_batch_no_cond");
			foreach ($po_data_2 as $value)
			{
				$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
				$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
				$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
			}
		}
	}
	else
	{
		if ($data[3]!="")
		{
			if($batchNO) $all_recv_batch_no_cond = " and c.batch_no='$batchNO'";
			//$all_recv_batch_no_cond= "and c.batch_no='$data[0]'";
		}
	}

	$previousRcecByBatch=sql_select("SELECT b.batch_id,sum(b.receive_qnty ) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id $all_recv_batch_no_cond and a.company_id=$company_id and a.entry_form=17 and a.item_category=3 and a.receive_basis in(1,2,4) group by b.batch_id");// in(1,2)

	foreach ($previousRcecByBatch as $row)
	{
		$recvQntyArrByBatch[$row[csf('batch_id')]]["receive_qnty"]=$row[csf('receive_qnty')];
	}

	$issueRtnByBatch=sql_select("SELECT  a.batch_id_from_fissuertn as batch_id, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_batch_create_mst c
	where a.batch_id_from_fissuertn = c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=3 and a.transaction_type in(4) and a.company_id=$company_id $all_issueRcvRtn_batch_no_cond group by a.batch_id_from_fissuertn");

	foreach ($issueRtnByBatch as $row)
	{
		$issueRtnQntyArrByBatch[$row[csf('batch_id')]]["issrqnty"]=$row[csf('issrqnty')];
	}

	$transInByBatch=sql_select("SELECT b.to_batch_id, sum(b.transfer_qnty) as trans_in_qnty
	from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
	where c.id=d.trans_id and d.dtls_id=b.id and c.company_id=$company_id and c.transaction_type=5 and c.item_category=3 and b.to_batch_id=a.id and c.status_active =1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=5 $all_recv_batch_no_cond group by b.to_batch_id");

	foreach ($transInByBatch as $row)
	{
		$transInQntyArrByBatch[$row[csf('to_batch_id')]]["trans_in_qnty"]=$row[csf('trans_in_qnty')];
	}
	/*echo "<pre>";
	print_r($transInQntyArrByBatch);*/
	//===========
	$previousIssueByBatch=sql_select("SELECT a.batch_id, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty
	from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d
	where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id  $all_issue_batch_no_cond group by a.batch_id");

	foreach ($previousIssueByBatch as $row)
	{
		$issueQntyArrByBatch[$row[csf('batch_id')]]["issue_qnty"]=$row[csf('issue_qnty')];
	}

	$recvRtnByBatch=sql_select("SELECT a.batch_id_from_fissuertn as batch_id,sum(case when a.transaction_type=3 then a.cons_quantity end) as recvrqnty
	from inv_transaction a,inv_mrr_wise_issue_details b,pro_batch_create_mst c where a.id=b.issue_trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(3) and a.batch_id_from_fissuertn = c.id  and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $all_issueRcvRtn_batch_no_cond group by a.batch_id_from_fissuertn");

	foreach ($recvRtnByBatch as $row)
	{
		$recvRtnQntyArrByBatch[$row[csf('batch_id')]]["recvrqnty"]=$row[csf('recvrqnty')];
	}

	$transOutByBatch=sql_select("SELECT b.batch_id, sum(b.transfer_qnty) as trans_out_qnty
	from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
	where  c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id  and c.transaction_type = 6 and c.item_category = 3 and b.batch_id = a.id  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=6  $all_recv_batch_no_cond group by b.batch_id");

	foreach ($transOutByBatch as $row)
	{
		$transOutQntyArrByBatch[$row[csf('batch_id')]]["trans_out_qnty"]=$row[csf('trans_out_qnty')];
	}


	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch/Lot</th>
                <th width="120">Job No</th>
                <th width="100">Style No</th>
                <th width="100">Color</th>
                <th width="100">Receive Qnty</th>
                <th width="100">Cumulative Issue Qnty</th>
                <th>Balance</th>

            </thead>
        </table>
        <div style="width:768px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;

				foreach($nameArray as $selectResult)
				{
					$po_nos = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["po_number"],", ")))));
					$style_ref = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["style_ref_no"],", ")))));
					$job_no = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["job_no_mst"],", ")))));

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$allRecv=$recvQntyArrByBatch[$selectResult[csf('batch_id')]]["receive_qnty"]+$issueRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["issrqnty"]+$transInQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_in_qnty"];

					$allIssue=$issueQntyArrByBatch[$selectResult[csf('batch_id')]]["issue_qnty"]+$recvRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["recvrqnty"]+$transOutQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_out_qnty"];

					//echo $allRecv.'-'.$allIssue.'=BatchID:'.$selectResult[csf('batch_id')].'<br>';
					//echo $recvQntyArrByBatch[$selectResult[csf('batch_id')]]["receive_qnty"]."+".$transInQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_in_qnty"]."-".$recvRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["recvrqnty"];

					//echo $issueQntyArrByBatch[$selectResult[csf('batch_id')]]["issue_qnty"].'+'.$transOutQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_out_qnty"].'-'.$issueRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["issrqnty"];

					$allRecvQnty=($recvQntyArrByBatch[$selectResult[csf('batch_id')]]["receive_qnty"]+$transInQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_in_qnty"])-$recvRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["recvrqnty"];
					$allIssueQnty=($issueQntyArrByBatch[$selectResult[csf('batch_id')]]["issue_qnty"]+$transOutQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_out_qnty"])-$issueRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["issrqnty"];


					$balance=$allRecv-$allIssue;
					if($balance>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('batch_id')];?>','<? echo $selectResult[csf('company_id')]; ?>','<? echo $selectResult[csf('store_id')]; ?>','<? echo $selectResult[csf('booking_no')]; ?>','<? echo $selectResult[csf('batch_lot')]; ?>',)">
							<td width="40" align="center"><? echo $i;  ?></td>
							<td width="100" align="center"><? echo $selectResult[csf('batch_lot')]; ?></td>
							<td width="120" style="word-break:break-word; word-wrap: break-word;"><p><? echo $job_no; ?></p></td>
							<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $style_ref; ?></p></td>
							<td width="100"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="100" align="right"><p><? echo $allRecvQnty; ?></p></td>
							<td width="100" align="right"><p><? echo $allIssueQnty; ?></p></td>
							<td align="right"><p><? echo $allRecvQnty-$allIssueQnty; ?></p></td>

						</tr>
						<?
						$i++;
					}
				}
			?>
            </table>
        </div>
	</div>
	<?
	exit();
}
if($action=="create_style_search_list_view")
{
	$data = explode("_",$data);
	/*$search_string="%".trim($data[0])."%";
	$search_by=$data[1];*/
	// print_r($data);
	$company_id =$data[0];
	$buyer_id =$data[1];
	$store_id =$data[2];
	$batchNO =$data[3];
	$jobNO =trim($data[4]);
	$hdn_variable_setting_status =$data[5];
	$intRefNO=trim($data[6]);
	$styleNO =trim($data[7]);
	$booking_type =$data[8];
	$cbo_issue_purpose =$data[9];
	$search_type=$data[10];

	if($buyer_id) $buyer_conds = " and c.buyer_name = '$buyer_id'";
	if($buyer_id) $buyer_conds2 = " and d.buyer_id = '$buyer_id'";
	if($data[6]) $int_ref_count="and f.grouping like '%$intRefNO%'";
	if($data[6]) $int_ref_count2="and a.grouping like '%$intRefNO%'";

	// var_dump($int_ref_count); 
	// var_dump($booking_type); 




	$search_field_cond="";$search_field_cond_2="";
	if($search_type==1){
		if($data[3]) $search_field_cond="and batch_lot ='$batchNO'";
		if($data[3]) $search_field_cond_2="and a.batch_no ='$batchNO'";
		//if($data[4])$job_cond="and c.job_no '%$jobNO'";
		if($data[4])$job_cond="and c.job_no_prefix_num='$jobNO'";
		// if($data[6])$int_ref_count="and a.grouping like '%$intRefNO%'";
		if($data[7])$style_cond="and c.style_ref_no ='$styleNO'";
    }
	elseif($search_type==2){
		if($data[3]) $search_field_cond="and batch_lot like '$batchNO%'";
		if($data[3]) $search_field_cond_2="and a.batch_no like '$batchNO%'";
		//if($data[4])$job_cond="and c.job_no like '%$jobNO'";
		if($data[4])$job_cond="and c.job_no_prefix_num like '$jobNO%'";
		// if($data[6])$int_ref_count="and a.grouping like '%$intRefNO%'";
		if($data[7])$style_cond="and c.style_ref_no like '$styleNO%'";
     	}
	elseif($search_type==3){
		if($data[3]) $search_field_cond="and batch_lot like '%$batchNO'";
		if($data[3]) $search_field_cond_2="and a.batch_no like '%$batchNO'";
		//if($data[4])$job_cond="and c.job_no like '%$jobNO'";
		if($data[4])$job_cond="and c.job_no_prefix_num like '%$jobNO'";
		// if($data[6])$int_ref_count="and a.grouping like '%$intRefNO%'";
		if($data[7])$style_cond="and c.style_ref_no like '%$styleNO'";
	    }
     elseif($search_type==4 || $search_type==0){
		if($data[3]) $search_field_cond="and batch_lot like '%$batchNO%'";
		if($data[3]) $search_field_cond_2="and a.batch_no like '%$batchNO%'";
		//if($data[4])$job_cond="and c.job_no like '%$jobNO'";
		if($data[4])$job_cond="and c.job_no like '%$jobNO%'";
		// if($data[6])$int_ref_count="and a.grouping like '%$intRefNO%'";
		if($data[7])$style_cond="and c.style_ref_no like '%$styleNO%'";
       }
	// if($data[3]) $search_field_cond="and batch_lot like '$batchNO'";
	// if($data[3]) $search_field_cond_2="and a.batch_no like '$batchNO'";
	// //if($data[4])$job_cond="and c.job_no like '%$jobNO'";
	// if($data[4])$job_cond="and c.job_no_prefix_num=$jobNO";
	// if($data[6])$style_cond="and c.style_ref_no like '%$styleNO%'";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	if($buyer_conds !="" || $job_cond !="" || $style_cond !="" || $$int_ref_count2 !="")
	{

		$po_data = sql_select("SELECT a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no ,c.buyer_name, a.grouping
		  from wo_po_break_down a,pro_batch_create_dtls b,wo_po_details_master c
		  where  a.id=b.po_id and c.job_no=a.job_no_mst and c.company_name=$company_id and a.status_active=1 and a.is_deleted=0 $job_cond $style_cond $buyer_conds $int_ref_count2");
		$all_po_id ="";$job_nos="";
		foreach ($po_data as $value)
		{
			$bathId_arr[$value[csf('batch_id')]] = $value[csf('batch_id')];
			if($all_po_id=="") $all_po_id=$value[csf('batch_id')]; else $all_po_id.=",".$value[csf('batch_id')];
			$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["grouping"] .= $value[csf("grouping")].", ";

			//$job_nos.="'".$value[csf('job_no_mst')]."',";
			$job_arr[$value[csf('job_no_mst')]] = "'".$value[csf('job_no_mst')]."'";
		}
		//$job_nos=chop($job_nos,",");
		$job_arr=array_unique(explode(',',implode(',',$job_arr)));
		$bathId_arr=array_unique(explode(',',implode(',',$bathId_arr)));
		if($db_type==2 &&  count($bathId_arr)>999)
		{
			$batchId_chunk=array_chunk($bathId_arr, 999);
			foreach($batchId_chunk as $row)
			{
				$batchId_ids=implode(",", $row);
				if($all_batchId_cond=="")
				{
					$all_batchId_cond.=" and (b.id in ($batchId_ids)";
					$all_batchId_cond2.=" and (b.to_batch_id in ($batchId_ids)";

				}
				else
				{
					$all_batchId_cond.=" or b.id in ($batchId_ids)";
					$all_batchId_cond2.=" or b.to_batch_id in ($batchId_ids)";
				}

			}
			$all_batchId_cond.=")";
			$all_batchId_cond2.=")";

		}
		else
		{

			$all_batchId_cond=" and b.id in(".implode(",",$bathId_arr).")";
			$all_batchId_cond2=" and b.to_batch_id in(".implode(",",$bathId_arr).")";
		}

		//job no chunk
		if($db_type==2 &&  count($job_arr)>999)
		{
			$job_chunk=array_chunk($job_arr, 999);
			foreach($job_chunk as $row)
			{
				$job_Nos =implode(",", $row);
				if($all_jobNo_cond=="")
				{
					$all_jobNo_cond.=" and (g.job_no in ($job_Nos)";

				}
				else
				{
					$all_jobNo_cond.=" or g.job_no in ($job_Nos)";
				}

			}
			$all_jobNo_cond.=")";
		}
		else
		{
			$all_jobNo_cond=" and g.job_no in(".implode(",",$job_arr).")";
		}

	}

	if($booking_type==1) // main fabric booking
	{
		$sql = "SELECT x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping , x.recv_qnty
		from (
		SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping , sum(e.quantity) as recv_qnty from inv_receive_master d,inv_transaction a,pro_batch_create_mst b,order_wise_pro_details e, wo_po_break_down f,wo_po_details_master g,wo_booking_mst h where d.id=a.mst_id and a.batch_id = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_id=g.id and b.booking_no=h.booking_no and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and e.trans_type=1 and e.entry_form= 17 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_jobNo_cond and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond $int_ref_count $int_ref_count and h.booking_type=1 and h.is_short in(1,2) group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping 
		union all
		SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping , sum(e.quantity) as recv_qnty
		from pro_batch_create_mst a,inv_item_transfer_dtls b,inv_item_transfer_mst c,order_wise_pro_details e,wo_po_break_down f,wo_po_details_master g,wo_booking_mst h
		where a.id=b.to_batch_id and b.mst_id=c.id and b.to_order_id=e.po_breakdown_id and b.id=e.dtls_id and e.po_breakdown_id=f.id and f.job_id=g.id and a.booking_no=h.booking_no and c.to_company=$company_id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and e.trans_type=5 and e.entry_form= 258
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2 $all_jobNo_cond $int_ref_count and h.booking_type=1 and h.is_short in(1,2)
		group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ) x
		group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping , x.recv_qnty";


	}
	else if($booking_type==2) // sample with order
	{
		$sql = "SELECT x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no,x.grouping , x.recv_qnty
		from (
		SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no,f.grouping , sum(e.quantity) as recv_qnty from inv_receive_master d,inv_transaction a,pro_batch_create_mst b,order_wise_pro_details e, wo_po_break_down f,wo_po_details_master g,wo_booking_mst h where d.id=a.mst_id and a.batch_id = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_id=g.id and b.booking_no=h.booking_no and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and e.trans_type=1 and e.entry_form= 17 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_jobNo_cond and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond $int_ref_count and h.booking_type=4 and h.is_short in(2) group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id,e.po_breakdown_id,g.job_no, g.style_ref_no, f.grouping 
		union all
		SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping , sum(e.quantity) as recv_qnty
		from pro_batch_create_mst a,inv_item_transfer_dtls b,inv_item_transfer_mst c,order_wise_pro_details e,wo_po_break_down f,wo_po_details_master g,wo_booking_mst h
		where a.id=b.to_batch_id and b.mst_id=c.id and b.to_order_id=e.po_breakdown_id and b.id=e.dtls_id and e.po_breakdown_id=f.id and f.job_id=g.id and a.booking_no=h.booking_no and c.to_company=$company_id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and e.trans_type=5 and e.entry_form= 258
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2 $all_jobNo_cond $int_ref_count and h.booking_type=4 and h.is_short in(2)
		group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ) x
		group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no,  x.grouping , x.recv_qnty";
	}
	else if($booking_type==3 || $cbo_issue_purpose==8) // samplw with out order
	{
		if($cbo_issue_purpose==8)
		{
			$sql = "SELECT  a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,sum(b.transfer_qnty) as qnty
			from pro_batch_create_mst a,inv_item_transfer_dtls b,inv_item_transfer_mst c,wo_non_ord_samp_booking_mst h, wo_non_ord_samp_booking_dtls j,inv_transaction k
			where a.id=b.to_batch_id and b.mst_id=c.id and k.id=b.to_trans_id and a.booking_no=h.booking_no and c.to_company=$company_id  and c.company_id=$company_id and h.booking_no=j.booking_no and k.status_active=1 and k.is_deleted=0 and a.status_active=1 and a.booking_without_order=1 and k.transaction_type=5 and a.entry_form= 258 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.to_store=$store_id  and h.booking_type=4  group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id";
			//--,e.po_breakdown_id,g.job_no,g.style_ref_no
			//--,e.po_breakdown_id,g.job_no,g.style_ref_no,sum(e.quantity) as recv_qnty
			//--,order_wise_pro_details e,wo_po_break_down f,wo_po_details_master g
			//--and b.to_order_id=e.po_breakdown_id and e.po_breakdown_id=f.id and f.job_id=g.id

		}
		else if($booking_type==3 && $jobNO!="")
		{

			$sql = "SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id
			,null as po_breakdown_id,null as job_no,null as style_ref_no, null as grouping, sum(a.cons_quantity) as recv_qnty 
			from inv_receive_master d,pro_finish_fabric_rcv_dtls e,inv_transaction a,pro_batch_create_mst b  
			where d.id=a.mst_id and a.batch_id = b.id 
			and d.id=e.mst_id and e.trans_id=a.id and e.prod_id=a.prod_id and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and d.entry_form= 17 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_jobNo_cond and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond and b.booking_without_order=1 group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id";
		}
		else
		{
			$sql = "SELECT x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping ,  x.recv_qnty
			from (
			SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ,  sum(e.quantity) as recv_qnty from inv_receive_master d,inv_transaction a,pro_batch_create_mst b,order_wise_pro_details e, wo_po_break_down f,wo_po_details_master g,wo_non_ord_samp_booking_mst h where d.id=a.mst_id and a.batch_id = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_id=g.id and b.booking_no=h.booking_no and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and e.trans_type=1 and e.entry_form= 17 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_jobNo_cond and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond and h.booking_type=4 and h.is_short in(2) group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no , f.grouping 
			union all
			SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping , sum(e.quantity) as recv_qnty
			from pro_batch_create_mst a,inv_item_transfer_dtls b,inv_item_transfer_mst c,order_wise_pro_details e,wo_po_break_down f,wo_po_details_master g,wo_non_ord_samp_booking_mst h
			where a.id=b.to_batch_id and b.mst_id=c.id and b.to_order_id=e.po_breakdown_id and b.id=e.dtls_id and e.po_breakdown_id=f.id and f.job_id=g.id and a.booking_no=h.booking_no and c.to_company=$company_id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and e.trans_type=5 and e.entry_form= 258
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2 $all_jobNo_cond and h.booking_type=4 and h.is_short in(2)
			group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ) x
			group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping ,  x.recv_qnty";
		}			
	}
	else
	{
		if($db_type==0)
		{
			$sql = "select x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping, x.recv_qnty
					from (
					SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ,  sum(e.quantity) as  recv_qnty  from inv_receive_master d, inv_transaction a, pro_batch_create_mst b,order_wise_pro_details e, wo_po_break_down f , wo_po_details_master g  where d.id=a.mst_id and a.batch_id = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_no_mst=g.job_no  and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and a.batch_id <>'' $all_jobNo_cond $all_batchId_cond $buyer_conds2 $search_field_cond $int_ref_count group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id,e.po_breakdown_id
				union all
				SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ,  sum(e.quantity) as  recv_qnty
				from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c,order_wise_pro_details e, wo_po_break_down f , wo_po_details_master g
				where a.id=b.to_batch_id and b.mst_id=c.id and b.to_order_id=e.po_breakdown_id nd b.id=e.dtls_id and e.po_breakdown_id=f.id and f.job_no_mst=g.job_no and c.to_company=$company_id and a.status_active=1
				and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2  $all_jobNo_cond $int_ref_count
				group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id,e.po_breakdown_id) x
	 		group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping ,  x.recv_qnty";
		}
		else
		{
			$sql = "SELECT x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping ,  x.recv_qnty
			from (
			SELECT a.company_id,b.booking_no,a.store_id as store_id,a.batch_lot,b.id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ,  sum(e.quantity) as recv_qnty from inv_receive_master d,inv_transaction a,pro_batch_create_mst b,order_wise_pro_details e, wo_po_break_down f,wo_po_details_master g where d.id=a.mst_id and a.batch_id = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_id=g.id and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and e.trans_type=1 and e.entry_form= 17 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_jobNo_cond and a.batch_id is not null $all_batchId_cond $buyer_conds2 $search_field_cond $int_ref_count and (b.booking_without_order=0  OR b.booking_without_order is null) group by a.company_id,b.booking_no,a.store_id,a.batch_lot ,b.id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no , f.grouping 
			union all
			SELECT a.company_id,a.booking_no,b.to_store as store_id,a.batch_no as batch_lot,b.to_batch_id as batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping , sum(e.quantity) as recv_qnty 
			from pro_batch_create_mst a ,inv_transaction h,inv_item_transfer_dtls b,inv_item_transfer_mst c,order_wise_pro_details e,wo_po_break_down f,wo_po_details_master g  
			where a.id=h.pi_wo_batch_no and h.id=b.to_trans_id 
			and a.id=b.to_batch_id and b.mst_id=c.id and b.to_order_id=e.po_breakdown_id and e.po_breakdown_id=f.id and f.job_id=g.id and c.to_company=$company_id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and e.trans_type=5 and e.entry_form= 258 and h.id=e.trans_id and h.transaction_type=5 and h.item_category=3 and h.status_active=1 and h.is_deleted=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			and b.to_store=$store_id $search_field_cond_2 $all_batchId_cond2 $all_jobNo_cond $int_ref_count
			group by a.company_id,a.booking_no,b.to_store,a.batch_no,b.to_batch_id,b.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping ) x
			group by x.company_id,x.booking_no,x.store_id,x.batch_lot,x.batch_id,x.color_id,x.po_breakdown_id,x.job_no,x.style_ref_no, x.grouping ,  x.recv_qnty";
		}
	}
	// echo $sql;
	$nameArray=sql_select( $sql );
	$allPoids="";$dataArr=array();
	$intRefArr=array();
	foreach($nameArray as $row)
	{
		$batchNoArr[$row[csf('batch_id')]]=$row[csf('batch_lot')];
		$styleRefArr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
		$intRefArr[$row[csf('job_no')]]=$row[csf('grouping')];
		$allPoids[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		$allPoidsByJob[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";
		$dataArr[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['recv_qnty']+=$row[csf('recv_qnty')];
		

	}
	/*echo "<pre>";
	print_r($dataArr);
	echo "</pre>";*/


	if($buyer_conds =="" && $job_cond =="" && $style_cond =="")
	{
		foreach($nameArray as $selectResult)
		{
			$result_batch_arr[$selectResult[csf('batch_id')]] = $selectResult[csf('batch_id')];
		}

		$all_result_batch_arr = array_filter($result_batch_arr);
		if(count($all_result_batch_arr)>0)
		{
			$all_result_batch_nos = implode(",", $all_result_batch_arr);
			$all_result_batch_no_cond="";$all_recv_batch_no_cond="";$all_issue_batch_no_cond="";$all_issueRcvRtn_batch_no_cond=""; $batchCond="";$recvBatchCond="";$issueBatchCond="";$issueRtnBatchCond="";
			if($db_type==2 && count($all_result_batch_arr)>999)
			{
				$all_result_batch_arr_chunk=array_chunk($all_result_batch_arr,999) ;
				foreach($all_result_batch_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$batchCond.="  b.mst_id in($chunk_arr_value) or ";
					$recvBatchCond.="  b.batch_id in($chunk_arr_value) or ";
					$issueBatchCond.="  a.batch_id in($chunk_arr_value) or ";
					$issueRtnBatchCond.="  a.batch_id_from_fissuertn in($chunk_arr_value) or ";
				}

				$all_result_batch_no_cond.=" and (".chop($batchCond,'or ').")";
				$all_recv_batch_no_cond.=" and (".chop($recvBatchCond,'or ').")";
				$all_issue_batch_no_cond.=" and (".chop($issueBatchCond,'or ').")";
				$all_issueRcvRtn_batch_no_cond.=" and (".chop($issueRtnBatchCond,'or ').")";
			}
			else
			{
				$all_result_batch_no_cond=" and b.mst_id in($all_result_batch_nos)";
				$all_recv_batch_no_cond=" and b.batch_id in($all_result_batch_nos)";
				$all_issue_batch_no_cond=" and a.batch_id in($all_result_batch_nos)";
				$all_issueRcvRtn_batch_no_cond=" and a.batch_id_from_fissuertn in($all_result_batch_nos)";
			}
			$po_data_2 = sql_select("SELECT a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no , a.grouping  ,buyer_name
			from wo_po_break_down a,pro_batch_create_dtls b,wo_po_details_master c
			where  a.id=b.po_id and c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 $all_result_batch_no_cond");
			foreach ($po_data_2 as $value)
			{
				$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
				$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
				$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
				$batch_ref_arr[$value[csf('batch_id')]]["grouping"] .= $value[csf("grouping")].", ";

			}
		}
	}
	else
	{
		if ($data[3]!="")
		{
			if($batchNO) $all_recv_batch_no_cond = " and c.batch_no='$batchNO'";
			//$all_recv_batch_no_cond= "and c.batch_no='$data[0]'";
		}
	}
	//echo "SELECT b.batch_id,sum(b.receive_qnty ) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id $all_recv_batch_no_cond and a.company_id=$company_id and a.entry_form=17 and a.item_category=3 and a.receive_basis in(1,2,4) group by b.batch_id";
	/*$previousRcecByBatch=sql_select("SELECT b.batch_id,sum(b.receive_qnty ) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id $all_recv_batch_no_cond and a.company_id=$company_id and a.entry_form=17 and a.item_category=3 and a.receive_basis in(1,2,4) group by b.batch_id");// in(1,2)

	foreach ($previousRcecByBatch as $row)
	{
		$recvQntyArrByBatch[$row[csf('batch_id')]]["receive_qnty"]=$row[csf('receive_qnty')];
	}*/

	$issueRtnByBatch=sql_select("SELECT a.batch_id_from_fissuertn as batch_id,b.booking_no,e.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping, sum(e.quantity) as  issrqnty from inv_transaction a,pro_batch_create_mst b, order_wise_pro_details e, wo_po_break_down f , wo_po_details_master g where a.batch_id_from_fissuertn = b.id and a.id=e.trans_id and e.prod_id=a.prod_id and e.po_breakdown_id=f.id and f.job_no_mst=g.job_no and e.entry_form=209 and e.trans_type=4 and e.status_active=1 and e.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.transaction_type in(4) and a.store_id=$store_id and a.company_id=$company_id $all_issueRcvRtn_batch_no_cond group by a.batch_id_from_fissuertn,b.booking_no,e.color_id,e.po_breakdown_id,g.job_no,g.style_ref_no, f.grouping");

	foreach ($issueRtnByBatch as $row)
	{
		$allPoidsByJob[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";
		$dataArrQnty[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['issrqnty']+=$row[csf('issrqnty')];

	}

	/*$transInByBatch=sql_select("SELECT b.to_batch_id, sum(b.transfer_qnty) as trans_in_qnty
	from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
	where c.id=d.trans_id and d.dtls_id=b.id and c.company_id=$company_id and c.transaction_type=5 and c.item_category=3 and b.to_batch_id=a.id and c.status_active =1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=5 $all_recv_batch_no_cond group by b.to_batch_id");

	foreach ($transInByBatch as $row)
	{
		$transInQntyArrByBatch[$row[csf('to_batch_id')]]["trans_in_qnty"]=$row[csf('trans_in_qnty')];
	}*/
	/*echo "<pre>";
	print_r($transInQntyArrByBatch);*/
	//===========

	$previousIssueByBatch=sql_select("SELECT a.batch_id, sum(f.quantity) as issue_qnty ,d.booking_no,f.color_id,f.po_breakdown_id,h.job_no,h.style_ref_no,  g.grouping  from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d,order_wise_pro_details f, wo_po_break_down g , wo_po_details_master h where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.store_id=$store_id and b.company_id=$company_id  $all_issue_batch_no_cond  and f.trans_type=2 and f.entry_form=19  and a.id=f.trans_id and f.prod_id=a.prod_id and f.po_breakdown_id=g.id and g.job_no_mst=h.job_no  and d.booking_without_order=0 group by  a.batch_id,d.booking_no,f.color_id,f.po_breakdown_id,h.job_no,h.style_ref_no,g.grouping
		union all 
		SELECT a.batch_id, sum(a.cons_quantity) as issue_qnty ,d.booking_no,c.color_id,null as po_breakdown_id,null as job_no,null as style_ref_no , null as grouping
		from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d
		where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 
		and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and b.status_active=1 and b.is_deleted=0 
		and a.store_id=$store_id and b.company_id=$company_id $all_issue_batch_no_cond and b.entry_form=19 and d.booking_without_order=1 
		group by a.batch_id,d.booking_no,c.color_id");

	foreach ($previousIssueByBatch as $row)
	{
		$allPoidsByJob[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";
		$dataArrQnty[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
		
	}

	$recvRtnByBatch=sql_select("SELECT a.batch_id_from_fissuertn as batch_id, sum(f.quantity) as recvrqnty ,d.booking_no,f.color_id,f.po_breakdown_id,h.job_no,h.style_ref_no,h.job_no,h.style_ref_no, g.grouping from inv_transaction a,inv_mrr_wise_issue_details b,pro_batch_create_mst d, order_wise_pro_details f, wo_po_break_down g , wo_po_details_master h where a.id=b.issue_trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(3) and a.batch_id_from_fissuertn = d.id and a.id=f.trans_id and f.prod_id=a.prod_id  and f.po_breakdown_id=g.id and g.job_no_mst=h.job_no and f.trans_type=3 and a.store_id=$store_id and  f.entry_form=202 and f.status_active=1 and f.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $all_issueRcvRtn_batch_no_cond group by  a.batch_id_from_fissuertn,d.booking_no,f.color_id,f.po_breakdown_id,h.job_no,h.style_ref_no,h.job_no,h.style_ref_no, g.grouping");

	foreach ($recvRtnByBatch as $row)
	{
		$allPoidsByJob[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";;
		$dataArrQnty[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['recvrqnty']+=$row[csf('recvrqnty')];

	}

	$transOutByBatch=sql_select("SELECT b.batch_id, sum(d.quantity) as trans_out_qnty , a.booking_no,d.color_id,d.po_breakdown_id,h.job_no,h.style_ref_no,h.job_no,h.style_ref_no , g.grouping from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a, wo_po_break_down g , wo_po_details_master h where c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id and c.transaction_type = 6 and c.item_category = 3 and b.batch_id = a.id and c.id=d.trans_id and d.po_breakdown_id=g.id and g.job_no_mst=h.job_no and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=6 and c.transaction_type=6 and b.from_store=$store_id group by b.batch_id , a.booking_no,d.color_id,d.po_breakdown_id,h.job_no,h.style_ref_no,h.job_no,h.style_ref_no, g.grouping");

	foreach ($transOutByBatch as $row)
	{
		$allPoidsByJob[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";
		$dataArrQnty[$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]]['trans_out_qnty']+=$row[csf('trans_out_qnty')];
	}

	if($booking_type==3 && $cbo_issue_purpose==8)
	{
		?>
	    <div>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" >
	            <thead>
	                <th width="40">SL</th>
	                <th width="100">Batch/Lot</th>
					<th width="100">Int Ref.</th>
	                <th width="120">Booking No</th>
	                <th width="100">Color</th>
	                <th>Qnty</th>
	            </thead>
	        </table>
	        <div style="width:668px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
	            <?
					$i=1;

					foreach($nameArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('batch_id')];?>','<? echo $row[csf('company_id')] ; ?>','<? echo $row[csf('store_id')]; ?>','<? echo $row[csf('booking_no')]; ?>','<? echo $batchNoArr[$row[csf('batch_id')]]; ?>','<? echo $jobNo; ?>','<? echo $allPoIdss; ?>','<? echo $hdn_variable_setting_status; ?>','<? echo $cbo_issue_purpose; ?>','<? echo $row[csf('color_id')]; ?>',)">
							<td width="40" align="center"><? echo $i;  ?></td>
							<td width="100" align="center"><? echo $batchNoArr[$row[csf('batch_id')]]; ?></td> 
							<td width="100" align="center"><? echo $row[csf('grouping')]; ?></td> 
							<td width="120" style="word-break:break-word; word-wrap: break-word;"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
	            </table>
	        </div>
		</div>
		<?
	}
	else
	{
		?>
	    <div>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
	            <thead>
	                <th width="40">SL</th>
	                <th width="100">Batch/Lot</th>
	                <th width="120">Job No</th>
					<th width="100">Int Ref.</th>
	                <th width="100">Style No</th>
	                <th width="100">Color</th>
	                <th width="100">Receive Qnty</th>
	                <th width="100">Cumulative Issue Qnty</th>
	                <th>Balance</th>

	            </thead>
	        </table>
	        <div style="width:868px; overflow-y:scroll; max-height:230px; float: left;" id="buyer_list_view" align="center">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search" style="float: left;">
	            <?
					$i=1;
					foreach ($dataArr as $bookingNo => $bookingData)
					{
						foreach ($bookingData as $jobNo => $jobData)
						{
							foreach ($jobData as $colorId => $colorData)
							{
								foreach ($colorData as $batchId => $row)
								{
									
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$allRecv=$row['recv_qnty']+$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issrqnty'];

									$allIssue=$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issue_qnty']+$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['recvrqnty']+$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['trans_out_qnty'];

									//echo $allRecv.'-'.$allIssue.'=BatchID:'.$selectResult[csf('batch_id')].'<br>';
									//echo $recvQntyArrByBatch[$selectResult[csf('batch_id')]]["receive_qnty"]."+".$transInQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_in_qnty"]."-".$recvRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["recvrqnty"];

									//echo $issueQntyArrByBatch[$selectResult[csf('batch_id')]]["issue_qnty"].'+'.$transOutQntyArrByBatch[$selectResult[csf('batch_id')]]["trans_out_qnty"].'-'.$issueRtnQntyArrByBatch[$selectResult[csf('batch_id')]]["issrqnty"];

									//echo $row['recv_qnty']."<br/>";
									//echo $dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issue_qnty']."+".$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['trans_out_qnty']."-".$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issrqnty']."<br/>";

									$allRecvQnty=$row['recv_qnty']-$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['recvrqnty'];
									$allIssueQnty=($dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issue_qnty']+$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['trans_out_qnty'])-$dataArrQnty[$bookingNo][$jobNo][$colorId][$batchId]['issrqnty'];


									$allPoIdss=$allPoidsByJob[$bookingNo][$jobNo][$colorId][$batchId]['po_breakdown_id'];
									$allPoIdss=chop($allPoIdss,",");

									$balance=$allRecv-$allIssue;
									$balance= number_format($balance,4);

									if($balance>0.0000)
									{
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $batchId;?>','<? echo $company_id ; ?>','<? echo $store_id; ?>','<? echo $bookingNo; ?>','<? echo $batchNoArr[$batchId]; ?>','<? echo $jobNo; ?>','<? echo $allPoIdss; ?>','<? echo $hdn_variable_setting_status; ?>','<? echo $cbo_issue_purpose; ?>','<? echo $colorId; ?>',)">
												<td width="40" align="center"><? echo $i;  ?></td>
												<td width="100" align="center"  title="<? echo $batchId; ?>"><? echo $batchNoArr[$batchId]; ?></td>
												<td width="120" style="word-break:break-word; word-wrap: break-word;"><p><? echo $jobNo; ?></p></td>
												<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $intRefArr["$jobNo"]; ?></p></td> 
												<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $styleRefArr[$jobNo]; ?></p></td>
												<td width="100"><p><? echo $color_arr[$colorId]; ?></p></td>
												<td width="100" align="right"><p><? echo number_format($allRecvQnty,4); ?></p></td>
												<td width="100" align="right"><p><? echo number_format($allIssueQnty,4); ?></p></td>
												<td align="right"><p><? echo  number_format($allRecvQnty-$allIssueQnty,4); ?></p></td>

											</tr>
										<?
										$i++;
									}
								}
							}
						}
					}
				?>
	            </table>
	        </div>
		</div>
		<?
	}
	exit();
}

if($action=='show_fabric_desc_listview')
{
	$data= explode('_', $data);
	$batch_id = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$booking_no = $data[3];
	$batch_no = $data[4];
	$hdn_variable_setting_status = $data[5];
	if ($store_id!=""){$store_cond="and a.store_id in(".$store_id.")";} else{$store_cond="";}

	if($db_type==0)
	{
		$rackCond = " IFNULL(a.rack, 0) rack";
		$rackCond2 = " IFNULL(b.rack, 0) rack_no";
		$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
		$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
		$rackCond5 = " IFNULL(b.to_rack, 0) rack_no";

		if($booking_no)
		{
			$booking_null_cond_a = " and a.booking_no = '$booking_no'";
			$booking_null_cond_c = " and c.booking_no = '$booking_no'";
			$booking_null_cond_d = " and d.booking_no = '$booking_no'";
			$booking_null_cond_e = " and e.booking_no = '$booking_no'";
		}
		else
		{
			$booking_null_cond_a = " and a.booking_no = ''";
			$booking_null_cond_c = " and c.booking_no = ''";
			$booking_null_cond_d = " and d.booking_no = ''";
			$booking_null_cond_e = " and e.booking_no = ''";
		}
	}
	else
	{
		$rackCond = " nvl(a.rack, 0) rack";
		$rackCond2 = " nvl(b.rack, 0) rack_no";
		$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
		$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
		$rackCond5 = " nvl(b.to_rack, 0) rack_no";

		if($booking_no)
		{
			$booking_null_cond_a = " and a.booking_no = '$booking_no'";
			$booking_null_cond_c = " and c.booking_no = '$booking_no'";
			$booking_null_cond_d = " and d.booking_no = '$booking_no'";
			$booking_null_cond_e = " and e.booking_no = '$booking_no'";
		}
		else
		{
			$booking_null_cond_a = " and a.booking_no is null";
			$booking_null_cond_c = " and c.booking_no is null";
			$booking_null_cond_d = " and d.booking_no is null";
			$booking_null_cond_e = " and e.booking_no is null";
		}
	}

	if($data[0]!="") $batch_id_cond="and e.id=$data[0]";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	/*echo "SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id";

	$data_array=sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id");*/


	$issue_qty_array=array();

	$issData = sql_select("SELECT a.prod_id, a.batch_id,a.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty
	from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d
	where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id $booking_null_cond_d and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id $store_cond  and a.batch_lot='$batch_no'
	group by a.prod_id, a.batch_id, a.body_part_id,a.floor_id, a.room, a.rack, a.self,a.bin_box");
	//and a.batch_id in(".$batch_id.")

	foreach($issData as $row)
	{
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
	}

	$recvRt_qty_array=array(); $issRt_qty_array=array();
	$receiveReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, a.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=3 then a.cons_quantity end) as recvrqnty from inv_transaction a,inv_mrr_wise_issue_details b,pro_batch_create_mst c where a.id=b.issue_trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(3) and a.batch_id_from_fissuertn = c.id $booking_null_cond_c and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id =$store_id and a.batch_lot='$batch_no'  group by a.prod_id, a.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	//and  a.batch_id_from_fissuertn in($batch_id)

	foreach($receiveReturnData as $row)
	{
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]=$row[csf('recvrqnty')];
	}


	$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, a.body_part_id,  a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_batch_create_mst c where a.batch_id_from_fissuertn = c.id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(4) $booking_null_cond_c and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id and a.store_id =$store_id  and a.batch_lot='$batch_no' group by a.prod_id, a.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	//and a.batch_id_from_fissuertn in($batch_id)

	/*$issue_rtn_recv_qnty=sql_select("select b.id, a.po_breakdown_id,a.quantity as qnty,y.job_no_mst ,d.style_ref_no,c.rate,c.booking_no,c.job_no from inv_transaction b, product_details_master x,order_wise_pro_details a,wo_po_break_down y,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f  where b.prod_id=x.id and x.id=a.prod_id and b.id=a.trans_id and a.po_breakdown_id=y.id and y.job_no_mst=d.job_no and d.job_no=c.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and y.id=c.po_break_down_id and x.color='".$hidden_color_id."'  $bodyPartCond_2 and x.detarmination_id='".$fabric_desc_id."' and b.company_id=$company_id and b.store_id =$store_id and  b.pi_wo_batch_no=$batch_id $txt_width_cond $txt_weight_cond and b.order_rate='".$txt_rate."' and b.status_active=1 and a.entry_form=209 and b.is_deleted=0 and b.status_active=1 and x.is_deleted=0 and x.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.item_category=3 and b.transaction_type=4  and b.prod_id=$hidden_prod_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond  group by b.id, a.po_breakdown_id,a.quantity,y.job_no_mst,d.style_ref_no,c.rate,c.booking_no,c.job_no");
		foreach($issue_rtn_recv_qnty as $row)
		{
			$cumu_issue_rtn_recv[$row[csf('order_id')]]+=$row[csf('quantity')];
				$cumu_issue_rtn_recv_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('rate')]]['qnty']+=$row[csf('qnty')];
		}*/



	//
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin]=$row[csf('issrqnty')];
	}

	$transInData = sql_select("select b.to_batch_id, b.to_store, (case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as floor,
	c.body_part_id, (case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond5,(case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) as bin_box, sum(b.transfer_qnty) as trans_in_qnty, b.to_prod_id as prod_id
	from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
	where c.id=d.trans_id and d.dtls_id=b.id and c.company_id= $company_id and c.transaction_type=5 and c.item_category=3 and b.to_batch_id=a.id
	and a.batch_no='$batch_no' $booking_null_cond_a and b.to_store =$store_id
	and c.status_active =1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=5
	group by b.to_batch_id, b.to_store, b.to_floor_id, c.body_part_id, b.to_room,b.to_rack,b.to_shelf,b.to_bin_box,b.to_prod_id");
	foreach($transInData as $row)
	{
		$trans_in_qnty_array[$row[csf('prod_id')]][$row[csf('to_batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_in_qnty')];
	}

	$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) as bin_box, sum(b.transfer_qnty) as trans_out_qnty,  b.from_prod_id as prod_id
		from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
		where  c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id and b.from_store =$store_id and c.transaction_type = 6 and c.item_category = 3 and b.batch_id = a.id $booking_null_cond_a and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=6 and a.batch_no= '$batch_no'
		group by b.batch_id, b.from_store, b.floor_id, c.body_part_id, b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");
	//and b.batch_id in ($batch_id)

	foreach($transOutData as $row)
	{
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
	}

	$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width, x.store_id, x.floor, x.body_part_id, x.batch_id,x.batch_no,x.room,x.rack_no, x.shelf_no,x.bin, sum(x.cons_amount) as cons_amount, sum(x.qnty) as qnty, x.prod_id,x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.rate
		from
		(
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id,(case when b.floor is null or b.floor=0 then 0 else b.floor end) as  floor, b.body_part_id, b.batch_id,e.batch_no,(case when d.room is null or d.room=0 then 0 else d.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin,sum(d.cons_quantity) as qnty, d.gmt_item_id, b.prod_id, sum(d.cons_amount) as cons_amount,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,d.order_rate as rate
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id $booking_null_cond_e  and e.batch_no='$batch_no' and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (17) and a.item_category_id=3 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id, b.floor,b.body_part_id, b.batch_id,e.batch_no, d.room, b.rack_no, b.shelf_no,b.bin,d.gmt_item_id,b.prod_id,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,d.order_rate
		union all
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,null as original_gsm,null as original_width,c.store_id,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as  floor, c.body_part_id, b.to_batch_id as batch_id,e.batch_no,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) as bin,sum(c.cons_quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id, sum(c.cons_amount) as cons_amount,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,c.order_rate as rate
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 3  and b.to_batch_id = e.id $booking_null_cond_e and e.batch_no='$batch_no' and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0
		group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id, b.to_floor_id, c.body_part_id,e.batch_no, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf,b.to_bin_box, b.to_prod_id, a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,c.order_rate
		) x
		group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width,x.store_id, x.floor, x.body_part_id, x.batch_id,x.batch_no, x.room,x.rack_no, x.shelf_no,x.bin,x.prod_id, x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.rate";

		//and b.batch_id in($batch_id)
		//and b.to_batch_id in ($batch_id)

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
	where b.status_active=1 and b.is_deleted=0";
	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
		}
	}
	//print_r($lib_bin_arr);

	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company_id and item_category_id=3 and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=1020;
	else $table_width=720;
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
        	<th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="200">Fabric Description</th>
            <th width="50">UOM</th>
            <th width="70">Color</th>
            <?
            if ($varriable_setting_rack_self_maintain==1)
            {
            	?>
            	<th width="60">Floor</th>
            	<th width="60">Room</th>
            	<th width="60">Rack</th>
            	<th width="60">Shelf</th>
            	<th width="60">Bin</th>
            	<?
        	}
            ?>
            <th width="60">Recv. Qty</th>
            <th width="60">Issue Qty</th>
            <th width="40">Rate</th>
            <th width="60">Balance</th>
            <th>Prod. ID</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>" id="fabric_listview">
        <?
        $i=1;
        $data_array=sql_select($data_sql);
        foreach($data_array as $row)
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


			$iss_qnty=$issue_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$recvRt_qnty=$recvRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$issRt_qnty=$issRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];

			$trans_out_qnty=$trans_out_qnty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$trans_in_qnty=$trans_in_qnty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];


			$cum_recv_qty=(($row[csf('qnty')]-$recvRt_qnty)+$trans_in_qnty);
			$cum_recv_qty_title="(Receive - Receive Return + Transfer In)\nReceive=".$row[csf('qnty')]."\nReceive Return=".$recvRt_qnty;

			$cum_iss_qty=$iss_qnty-$issRt_qnty + $trans_out_qnty;
			$cum_iss_qty_title="(Issue - Issue Return + Transfer Out)\nIssue=".$iss_qnty."\nIssue Return=".$issRt_qnty . "\nTransfer Out=" . $trans_out_qnty;

			$balance= number_format($cum_recv_qty,2,".","")-number_format($cum_iss_qty,2,".","");

			$store_id=$row[csf('store_id')];
			$company_id=$row[csf('company_id')];
			$floor_id=$row[csf('floor')];
			$room_id=$row[csf('room')];
			$rack_id=$row[csf('rack_no')];
			$shelf_id=$row[csf('shelf_no')];
			$bin=$row[csf('bin')];

			$floor_name 	= $lib_floor_arr[$row[csf("company_id")]][$row[csf("floor")]];
			$room_name 		= $lib_room_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]];
			$rack_name		= $lib_rack_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]];
			$shelf_name 	= $lib_shelf_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]];
			$bin 			= $lib_bin_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("bin")]];

			$cons_rate = $row[csf('cons_amount')]/$row[csf('qnty')];
			$cons_rate = number_format($cons_rate,4,".","");
			if($balance>0)
			{
				//."**".$row[csf('po_breakdown_id')]."**".$row[csf('rate')]
         		?>
	            <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('current_stock')]."**".$row[csf('body_part_id')]."**".$unit_of_measurement[$row[csf('unit_of_measure')]]."**".$row[csf('booking_without_order')]."**".$row[csf('batch_id')]."**".$row[csf('color')]."**".$row[csf('dia_width')]."**".$row[csf('weight')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$color_arr[$row[csf('color')]]."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name."**".$row[csf('floor')]."**".$row[csf('room')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$cons_rate."**".$row[csf('detarmination_id')]."**".$row[csf('bin')]."**".$bin."**".$row[csf('unit_of_measure')]."**".$row[csf('fabric_ref')]."**".$row[csf('rd_no')]."**".$row[csf('weight_type')]."**".$row[csf('cutable_width')]."**".$row[csf('original_gsm')]."**".$row[csf('original_width')]; ?>")' style="cursor:pointer" >
	                <td width="30"><? echo $i; ?></td>
	                <td width="70"><? echo $row[csf('batch_no')]; ?></td>
	                <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                <td width="50" align="center" ><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
	                <td width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
	                <?
		            if ($varriable_setting_rack_self_maintain==1)
		            {
		            	?>
						<td width="60"><p><? echo $floor_name; ?></p></td>
						<td width="60"><p><? echo $room_name; ?></p></td>
						<td width="60"><p><? echo $rack_name; ?></p></td>
						<td width="60"><p><? echo $shelf_name; ?></p></td>
						<td width="60"><p><? echo $bin; ?></p></td>
						<?
		        	}
		            ?>
					<td width="60" align="right" title="<? echo $cum_recv_qty_title; ?>"><? echo number_format($cum_recv_qty,2,'.',''); ?></td>
					<td width="60" align="right" title="<? echo $cum_iss_qty_title; ?>"><? echo number_format($cum_iss_qty,2,'.',''); ?></td>
					<td width="40" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
					<td width="60" align="right" title="<? echo $ref_title?>"><? echo number_format($balance,2,'.',''); ?></td>
					<td><p><? echo $row[csf('id')]; ?></p></td>
	            </tr>
        		<?
        		$i++;
			}
		}
        ?>
    </table>
	<?
	exit();
}
if($action=='show_fabric_desc_listview_style_wise')
{
	$data= explode('_', $data);
	$batch_id = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$booking_no = $data[3];
	$batch_no = $data[4];
	$job_no = $data[5];
	$poIds = $data[6];
	$hdn_variable_setting_status = $data[7];
	$cbo_issue_purpose = $data[8];
	$colorID = $data[9];

	if ($store_id!=""){$store_cond="and a.store_id in(".$store_id.")";} else{$store_cond="";}
	if ($poIds!=""){$poIds_cond="and f.po_breakdown_id in(".$poIds.")";} else{$poIds_cond="and f.po_breakdown_id is NULL";}
	if ($poIds!=""){$poIds_cond2="and e.po_breakdown_id in(".$poIds.")";} else{$poIds_cond="and f.po_breakdown_id is NULL";}

	if($db_type==0)
	{
		$rackCond = " IFNULL(a.rack, 0) rack";
		$rackCond2 = " IFNULL(b.rack, 0) rack_no";
		$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
		$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
		$rackCond5 = " IFNULL(b.to_rack, 0) rack_no";

		if($booking_no)
		{
			$booking_null_cond_a = " and a.booking_no = '$booking_no'";
			$booking_null_cond_c = " and c.booking_no = '$booking_no'";
			$booking_null_cond_d = " and d.booking_no = '$booking_no'";
			$booking_null_cond_e = " and e.booking_no = '$booking_no'";
		}
		else
		{
			$booking_null_cond_a = " and a.booking_no = ''";
			$booking_null_cond_c = " and c.booking_no = ''";
			$booking_null_cond_d = " and d.booking_no = ''";
			$booking_null_cond_e = " and e.booking_no = ''";
		}
	}
	else
	{
		$rackCond = " nvl(a.rack, 0) rack";
		$rackCond2 = " nvl(b.rack, 0) rack_no";
		$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
		$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
		$rackCond5 = " nvl(b.to_rack, 0) rack_no";

		if($booking_no)
		{
			$booking_null_cond_a = " and a.booking_no = '$booking_no'";
			$booking_null_cond_c = " and c.booking_no = '$booking_no'";
			$booking_null_cond_d = " and d.booking_no = '$booking_no'";
			$booking_null_cond_e = " and e.booking_no = '$booking_no'";
		}
		else
		{
			$booking_null_cond_a = " and a.booking_no is null";
			$booking_null_cond_c = " and c.booking_no is null";
			$booking_null_cond_d = " and d.booking_no is null";
			$booking_null_cond_e = " and e.booking_no is null";
		}
	}

	if($data[0]!="") $batch_id_cond="and e.id=$data[0]";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	/*echo "SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id";

	$data_array=sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id");*/


	$issue_qty_array=array();

	$issData = sql_select("SELECT a.prod_id, a.batch_id,a.body_part_id,a.cutable_width,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id, nvl(a.rack, 0) rack,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when e.trans_type=2 then e.quantity end) as issue_qnty,sum(case when e.trans_type=2 then a.cons_rate*e.quantity  end) as cons_amount  from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d,order_wise_pro_details e  where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and a.id=e.trans_id and e.trans_type=2 and e.status_active=1 and e.is_deleted=0 $poIds_cond2 $booking_null_cond_d and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id $store_cond and a.batch_lot='$batch_no' and d.booking_without_order=0 group by a.prod_id, a.batch_id, a.body_part_id,a.cutable_width,a.floor_id, a.room, a.rack, a.self,a.bin_box
		union all
		SELECT a.prod_id, a.batch_id,a.body_part_id,a.cutable_width,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id, nvl(a.rack, 0) rack,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when a.transaction_type=2 then a.cons_quantity end) as issue_qnty,sum(case when a.transaction_type=2 then a.cons_rate*a.cons_quantity  end) as cons_amount  from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d  where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id  $booking_null_cond_d and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id $store_cond and a.batch_lot='$batch_no' and d.booking_without_order=1 group by a.prod_id, a.batch_id, a.body_part_id,a.cutable_width,a.floor_id, a.room, a.rack, a.self,a.bin_box ");

	//and a.batch_id in(".$batch_id.")

	foreach($issData as $row)
	{
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('cutable_width')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
		$issue_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('cutable_width')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('cons_amount')];
	}

	$recvRt_qty_array=array(); $issRt_qty_array=array();
	$receiveReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, a.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when d.trans_type=3 then d.quantity end) as recvrqnty,sum(case when d.trans_type=3 then a.cons_rate*d.quantity  end) as cons_amount  from inv_transaction a,inv_mrr_wise_issue_details b,pro_batch_create_mst c, order_wise_pro_details d where a.id=b.issue_trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(3) and a.batch_id_from_fissuertn = c.id and a.id=d.trans_id and d.status_active=1 and d.is_deleted=0 and d.trans_type=3 and d.po_breakdown_id in($poIds) $booking_null_cond_c and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id=$store_id and a.batch_lot='$batch_no' group by a.prod_id, a.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	//and  a.batch_id_from_fissuertn in($batch_id)

	foreach($receiveReturnData as $row)
	{
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]=$row[csf('recvrqnty')];
		$recvRt_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]=$row[csf('cons_amount')];
	}

	$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, a.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when b.trans_type=4 then b.quantity end) as issrqnty,sum(case when b.trans_type=4 then a.cons_rate*b.quantity  end) as cons_amount  from inv_transaction a,pro_batch_create_mst c,order_wise_pro_details b where a.batch_id_from_fissuertn = c.id and a.id=b.trans_id and b.trans_type=4 and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(4) $booking_null_cond_c and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($poIds) and a.company_id=$company_id and a.store_id =$store_id and a.batch_lot='$batch_no' group by a.prod_id, a.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");

	//and a.batch_id_from_fissuertn in($batch_id)

	/*$issue_rtn_recv_qnty=sql_select("select b.id, a.po_breakdown_id,a.quantity as qnty,y.job_no_mst ,d.style_ref_no,c.rate,c.booking_no,c.job_no from inv_transaction b, product_details_master x,order_wise_pro_details a,wo_po_break_down y,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f  where b.prod_id=x.id and x.id=a.prod_id and b.id=a.trans_id and a.po_breakdown_id=y.id and y.job_no_mst=d.job_no and d.job_no=c.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and y.id=c.po_break_down_id and x.color='".$hidden_color_id."'  $bodyPartCond_2 and x.detarmination_id='".$fabric_desc_id."' and b.company_id=$company_id and b.store_id =$store_id and  b.pi_wo_batch_no=$batch_id $txt_width_cond $txt_weight_cond and b.order_rate='".$txt_rate."' and b.status_active=1 and a.entry_form=209 and b.is_deleted=0 and b.status_active=1 and x.is_deleted=0 and x.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.item_category=3 and b.transaction_type=4  and b.prod_id=$hidden_prod_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond  group by b.id, a.po_breakdown_id,a.quantity,y.job_no_mst,d.style_ref_no,c.rate,c.booking_no,c.job_no");
		foreach($issue_rtn_recv_qnty as $row)
		{
			$cumu_issue_rtn_recv[$row[csf('order_id')]]+=$row[csf('quantity')];
				$cumu_issue_rtn_recv_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('rate')]]['qnty']+=$row[csf('qnty')];
		}*/



	//
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin]=$row[csf('issrqnty')];
		$issRt_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin]=$row[csf('cons_amount')];
	}

	$transInData = sql_select("select b.to_batch_id, b.to_store, (case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as floor,
	c.body_part_id, (case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond5,(case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) as bin_box, sum(d.quantity) as trans_in_qnty, b.to_prod_id as prod_id,sum(case when d.trans_type=5 then c.cons_rate*d.quantity  end) as cons_amount
	from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
	where c.id=d.trans_id and d.dtls_id=b.id and c.company_id= $company_id and c.transaction_type=5 and c.item_category=3 and b.to_batch_id=a.id
	and a.batch_no='$batch_no' $booking_null_cond_a and b.to_store =$store_id and d.po_breakdown_id in($poIds)
	and c.status_active =1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=5
	group by b.to_batch_id, b.to_store, b.to_floor_id, c.body_part_id, b.to_room,b.to_rack,b.to_shelf,b.to_bin_box,b.to_prod_id");
	foreach($transInData as $row)
	{
		$trans_in_qnty_array[$row[csf('prod_id')]][$row[csf('to_batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_in_qnty')];
		$trans_in_amount_array[$row[csf('prod_id')]][$row[csf('to_batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('cons_amount')];
	}
	$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) as bin_box, sum(d.quantity) as trans_out_qnty, b.from_prod_id as prod_id,sum(case when d.trans_type=6 then c.cons_rate*d.quantity  end) as cons_amount  from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a where c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id and b.from_store =$store_id and c.transaction_type = 6 and c.item_category = 3 and b.batch_id = a.id $booking_null_cond_a  and b.from_order_id=d.po_breakdown_id  and a.batch_no= '$batch_no' and d.trans_type=6 and d.po_breakdown_id in($poIds)  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=6 group by b.batch_id, b.from_store, b.floor_id, c.body_part_id,b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");

	//and b.batch_id in ($batch_id)

	foreach($transOutData as $row)
	{
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
		$trans_out_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('cons_amount')];
	}
	if($cbo_issue_purpose==8)
	{

		$data_sql="select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,a.weight as original_gsm,CAST(a.dia_width as varchar2(4000)) as original_width,c.store_id,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as floor, c.body_part_id, b.to_batch_id as batch_id,e.batch_no,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, cast(b.to_rack as varchar(4000)) as rack_no,(case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) as bin,sum(b.transfer_qnty) as qnty,0 as gmt_item_id, b.to_prod_id as prod_id,sum(c.cons_rate*b.transfer_qnty) as cons_amount,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width, 0 as po_breakdown_id
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 3 and b.to_batch_id = e.id $booking_null_cond_e and e.batch_no='$batch_no' and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0 and c.transaction_type = 5
		group by a.id,a.product_name_details,a.color,a.unit_of_measure,a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id, b.to_floor_id, c.body_part_id,e.batch_no, b.to_batch_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box,b.to_prod_id,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width";

	}
	else
	{
		$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock,x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width, x.store_id, x.floor, x.body_part_id,x.batch_id,x.batch_no,x.room,x.rack_no, x.shelf_no,x.bin, sum(x.cons_amount) as cons_amount, sum(x.qnty) as qnty,x.prod_id,x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.po_breakdown_id from (select a.id,a.product_name_details, a.color, a.unit_of_measure,a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id,(case when b.floor is null or b.floor=0 then 0 else b.floor end) as floor, b.body_part_id, b.batch_id,e.batch_no,(case when d.room is null or d.room=0 then 0 else d.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin,sum(f.quantity) as qnty, d.gmt_item_id, b.prod_id,sum(d.cons_rate*f.quantity) as cons_amount ,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,listagg((CAST(f.po_breakdown_id as varchar2(4000))),',') within group (order by f.po_breakdown_id) as po_breakdown_id from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e,order_wise_pro_details f where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and d.id=f.trans_id $booking_null_cond_e  and e.batch_no='$batch_no' and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (17) and a.item_category_id=3 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and f.is_deleted=0 and f.status_active=1 $poIds_cond  and a.color in ($colorID) and f.entry_form in (17) group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id, b.floor,b.body_part_id, b.batch_id,e.batch_no, d.room, b.rack_no, b.shelf_no,b.bin,d.gmt_item_id,b.prod_id,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width 

		union all 


		select a.id,a.product_name_details, a.color, a.unit_of_measure,a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id,
		(case when b.floor is null or b.floor=0 then 0 else b.floor end) as floor, b.body_part_id, b.batch_id,e.batch_no,(case when d.room is null or d.room=0
		then 0 else d.room end) room, $rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,
		(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin,sum(d.cons_quantity) as qnty, d.gmt_item_id, b.prod_id,sum(d.cons_rate*d.cons_quantity) as cons_amount ,
		a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,null as po_breakdown_id 
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e 
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id  $booking_null_cond_e 
		and e.batch_no='$batch_no' and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (17) and a.item_category_id=3 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1  and a.color in ($colorID) and c.entry_form in (17) and c.booking_without_order=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,b.original_gsm,b.original_width,c.store_id, b.floor,b.body_part_id, b.batch_id,e.batch_no, d.room, b.rack_no, b.shelf_no,b.bin,d.gmt_item_id,b.prod_id,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width
  
		 union all
		 select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,a.weight as original_gsm,CAST(a.dia_width as varchar2(4000)) as original_width,c.store_id,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as floor, c.body_part_id, b.to_batch_id as batch_id,e.batch_no,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4,(case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) as bin,sum(f.quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id,sum(c.cons_rate*f.quantity) as cons_amount ,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,listagg((CAST(f.po_breakdown_id as varchar2(4000))),',') within group (order by f.po_breakdown_id) as po_breakdown_id from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e ,order_wise_pro_details f where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 3 and b.to_batch_id = e.id and c.id=f.trans_id $booking_null_cond_e and e.batch_no='$batch_no' and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0 and f.is_deleted=0 and f.status_active=1 $poIds_cond  and f.trans_type = 5 group by a.id,a.product_name_details,a.color,a.unit_of_measure,a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id, b.to_floor_id, c.body_part_id,e.batch_no, b.to_batch_id, b.to_room, b.to_rack,b.to_shelf,b.to_bin_box,b.to_prod_id,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width) x group by x.id,x.product_name_details,x.color,x.unit_of_measure, x.current_stock, x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width,x.store_id, x.floor,x.body_part_id, x.batch_id,x.batch_no, x.room,x.rack_no,x.shelf_no,x.bin,x.prod_id,x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.po_breakdown_id";
		 //and f.po_breakdown_id in($poIds)


			$ref_info_sql = sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.dia_width, a.fabric_color_id as color_id,c.rd_no,c.fabric_ref,y.style_ref_no,z.id as batch_id from wo_po_break_down x, wo_po_details_master y , wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b ,lib_yarn_count_determina_mst c, pro_batch_create_mst z  where x.job_id=y.id and y.job_no=a.job_no and a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no=z.booking_no  and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0  group by b.body_part_id,a.booking_no, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom,c.rd_no,c.fabric_ref,y.style_ref_no,z.id");
			foreach($ref_info_sql as $row)
			{
				if($style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('batch_id')]]['style_ref_no_chk']!=$row[csf('style_ref_no')])
				{
					$style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('batch_id')]]['style_ref_no_chk']=$row[csf('style_ref_no')];
					$style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('batch_id')]]['style_ref_no'].=$row[csf('style_ref_no')].",";
				}

			}
			unset($ref_info_sql);
	}

	//and b.batch_id in($batch_id)
	//and b.to_batch_id in ($batch_id)
	$main_data_array=array();
	$data_array=sql_select($data_sql);
	foreach($data_array as $row)
	{
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['id']=$row[csf('id')];
		
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['color']=$row[csf('color')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['dia_width']=$row[csf('dia_width')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['weight']=$row[csf('weight')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['body_part_id']=$row[csf('body_part_id')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['batch_id']=$row[csf('batch_id')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['detarmination_id']=$row[csf('detarmination_id')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['cutable_width']=$row[csf('cutable_width')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['store_id']=$row[csf('store_id')];
		

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['floor']=$row[csf('floor')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['room']=$row[csf('room')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['rack_no']=$row[csf('rack_no')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['shelf_no']=$row[csf('shelf_no')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['bin']=$row[csf('bin')];


		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['batch_no']=$row[csf('batch_no')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['unit_of_measure']=$row[csf('unit_of_measure')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['original_gsm']=$row[csf('original_gsm')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['original_width']=$row[csf('original_width')];

		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['booking_without_order']=$row[csf('booking_without_order')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['product_name_details']=$row[csf('product_name_details')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['fabric_ref']=$row[csf('fabric_ref')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['rd_no']=$row[csf('rd_no')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['weight_type']=$row[csf('weight_type')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['company_id']=$row[csf('company_id')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['prod_id']=$row[csf('prod_id')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['po_breakdown_id'].=$row[csf('po_breakdown_id')].",";


		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['cons_amount']+=$row[csf('cons_amount')];
		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['current_stock']+=$row[csf('current_stock')];


		$main_data_array[$row[csf('id')]][$row[csf('color')]][$row[csf('dia_width')]][$row[csf('weight')]][$row[csf('body_part_id')]][$row[csf('batch_id')]][$row[csf('detarmination_id')]][$row[csf('cutable_width')]][$row[csf('store_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]]['qnty']+=$row[csf('qnty')];

	}
	//echo "<pre>";
	//print_r($main_data_array);

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
	where b.status_active=1 and b.is_deleted=0";
	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
		}
	}
	//print_r($lib_bin_arr);

	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company_id and item_category_id=3 and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=980;
	else $table_width=680;
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="200">Fabric Description</th>
            <th width="50">UOM</th>
            <th width="70">Color</th>
            <?
            if ($varriable_setting_rack_self_maintain==1)
            {
            	?>
            	<th width="60">Floor</th>
            	<th width="60">Room</th>
            	<th width="60">Rack</th>
            	<th width="60">Shelf</th>
            	<th width="60">Bin</th>
            	<?
        	}
            ?>
            <th width="60">Recv. Qty</th>
            <th width="60">Issue Qty</th>
            <th width="60">Balance</th>
            <th>Prod. ID</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>" id="fabric_listview">
        <?
        $i=1;
        $data_array=sql_select($data_sql);
		foreach ($main_data_array as $id => $prodData)
		{
			foreach ($prodData as $color => $colorData)
			{
				foreach ($colorData as $dia_width => $dia_widthData)
				{
					foreach ($dia_widthData as $weight => $weightData)
					{
						foreach ($weightData as $body_part_id => $body_part_idData)
						{
							foreach ($body_part_idData as $batch_id => $batchData)
							{
								foreach ($batchData as $detarmination_id => $detarmination_idData)
								{
									foreach ($detarmination_idData as $cutable_width => $cutable_widthData)
									{
										foreach ($cutable_widthData as $store_id => $store_idData)
										{
											foreach ($store_idData as $floor => $floorData)
											{
												foreach ($floorData as $room => $roomData)
												{
													foreach ($roomData as $rack_no => $rackData)
													{
														foreach ($rackData as $shelf_no => $shelfData)
														{
															foreach ($shelfData as $bin => $row)
															{																	
																if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

																$iss_qnty=$issue_qty_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['cutable_width']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$recvRt_qnty=$recvRt_qty_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$issRt_qnty=$issRt_qty_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
													
																$trans_out_qnty=$trans_out_qnty_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$trans_in_qnty=$trans_in_qnty_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
													
																//Amount
																$iss_amount=$issue_amount_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['cutable_width']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$recvRt_amount=$recvRt_amount_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$issRt_amount=$issRt_amount_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
													
																$trans_out_amount=$trans_out_amount_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
																$trans_in_amount=$trans_in_amount_array[$row['id']][$row['batch_id']][$row['body_part_id']][$row['floor']][$row['room']][$row['rack_no']][$row['shelf_no']][$row['bin']];
													
																//echo $row['qnty'].'-'.$recvRt_qnty."<br/>";
																//$cum_recv_qty=(($row['qnty']-$recvRt_qnty)+$trans_in_qnty);
																
																$cum_recv_qty=(($row['qnty']-$recvRt_qnty));
																$cum_recv_amount=(($row['cons_amount']-$recvRt_amount));
																$cum_recv_qty_title="(Receive - Receive Return + Transfer In)\nReceive=".$row['qnty']."\nReceive Return=".$recvRt_qnty;
																
																//echo $row['qnty'].'-'.$recvRt_qnty .'/'.$iss_qnty.'-'.$issRt_qnty .'+'. $trans_out_qnty."<br/>";
																
																$cum_iss_qty=$iss_qnty-$issRt_qnty + $trans_out_qnty;
																$cum_iss_amount=$iss_amount-$issRt_amount + $trans_out_amount;
																$cum_iss_qty_title="(Issue - Issue Return + Transfer Out)\nIssue=".$iss_qnty."\nIssue Return=".$issRt_qnty . "\nTransfer Out=" . $trans_out_qnty;
													
																$balance= number_format($cum_recv_qty,2,".","")-number_format($cum_iss_qty,2,".","");
																$balanceAmount=$cum_recv_amount-$cum_iss_amount;
													
																$store_id=$row['store_id'];
																$company_id=$row['company_id'];
																$floor_id=$row['floor'];
																$room_id=$row['room'];
																$rack_id=$row['rack_no'];
																$shelf_id=$row['shelf_no'];
																$bin=$row['bin'];
													
																$floor_name 	= $lib_floor_arr[$row["company_id"]][$row["floor"]];
																$room_name 		= $lib_room_arr[$row["company_id"]][$row["floor"]][$row["room"]];
																$rack_name		= $lib_rack_arr[$row["company_id"]][$row["floor"]][$row["room"]][$row["rack_no"]];
																$shelf_name 	= $lib_shelf_arr[$row["company_id"]][$row["floor"]][$row["room"]][$row["rack_no"]][$row["shelf_no"]];
																$bin 			= $lib_bin_arr[$row["company_id"]][$row["floor"]][$row["room"]][$row["rack_no"]][$row["shelf_no"]][$row["bin"]];
													
																$cons_rate = $balanceAmount/$balance;
																$cons_rate = number_format($cons_rate,4,".","");
													
																$style_ref_no = $style_ref_no_arr[$row['detarmination_id']][$row['unit_of_measure']][$row['weight']][$row['dia_width']][$row['color']][$row['batch_id']]['style_ref_no'];
																$style_ref_no = chop($style_ref_no,",");
													
																if($balance>0)
																{
																	?>
																	<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row['id']."**".$row['product_name_details']."**".$row['current_stock']."**".$row['body_part_id']."**".$unit_of_measurement[$row['unit_of_measure']]."**".$row['booking_without_order']."**".$row['batch_id']."**".$row['color']."**".$row['dia_width']."**".$row['weight']."**".$row['rack_no']."**".$row['shelf_no']."**".$color_arr[$row['color']]."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name."**".$row['floor']."**".$row['room']."**".$batch_arr[$row['batch_id']]."**".$row['store_id']."**".$cons_rate."**".$row['detarmination_id']."**".$row['bin']."**".$bin."**".$row['unit_of_measure']."**".$row['fabric_ref']."**".$row['rd_no']."**".$row['weight_type']."**".$row['cutable_width']."**".$row['original_gsm']."**".$row['original_width']."**".chop($row['po_breakdown_id'],","); ?>")' style="cursor:pointer" >
																		<td width="30"><? echo $i; ?></td>
																		<td width="70" title="<? echo "[Style=".$style_ref_no."]"; ?>"><? echo $row['batch_no']; ?></td>
																		<td width="200"><p><? echo $row['product_name_details']; ?></p></td>
																		<td width="50" align="center" ><p><? echo $unit_of_measurement[$row['unit_of_measure']]; ?></p></td>
																		<td width="70"><? echo $color_arr[$row['color']]; ?></td>
																		<?
																		if ($varriable_setting_rack_self_maintain==1)
																		{
																			?>
																			<td width="60"><p><? echo $floor_name; ?></p></td>
																			<td width="60"><p><? echo $room_name; ?></p></td>
																			<td width="60"><p><? echo $rack_name; ?></p></td>
																			<td width="60"><p><? echo $shelf_name; ?></p></td>
																			<td width="60"><p><? echo $bin; ?></p></td>
																			<?
																		}
																		?>
																		<td width="60" align="right" title="<? echo $cum_recv_qty_title; ?>"><? echo number_format($cum_recv_qty,2,'.',''); ?></td>
																		<td width="60" align="right" title="<? echo $cum_iss_qty_title; ?>"><? echo number_format($cum_iss_qty,2,'.',''); ?></td>
																		<td width="60" align="right" title="<? echo $ref_title?>"><? echo number_format($balance,2,'.',''); ?></td>
																		<td><p><? echo $row['id']; ?></p></td>
																	</tr>
																	<?
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
							}
						}
					}
				}
			}
		}															
        ?>
    </table>
	<?
	exit();
}
if($action=='show_fabric_desc_listview_requ')
{
	$data= explode('_', $data);
	$jobNo 				= $data[0];
	$company_id 		= $data[1];
	$body_part 			= $data[2];
	$colorId 			= $data[3];
	$determination_id 	= $data[4];
	$gsm 				= $data[5];
	$dia 				= $data[6];
	$po_id 				= $data[7];
	$requ_mst_id 		= $data[8];
	$hdn_variable_setting_status = $data[9];

	$gsm_expl =explode(",", $gsm);
 	$gsmx ="'" . implode("', '", $gsm_expl) ."'";
	// y.gsm='120,8.3' and y.determination_id= and y.body_part=15,14

	if ($store_id!=""){$store_cond="and a.store_id in(".$store_id.")";} else{$store_cond="";}

	if($db_type==0)
	{
		$rackCond = " IFNULL(a.rack, 0) rack";
		$rackCond2 = " IFNULL(b.rack, 0) rack_no";
		$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
		$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
	}else{
		$rackCond = " nvl(a.rack, 0) rack";
		$rackCond2 = " nvl(b.rack, 0) rack_no";
		$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
		$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
	}

	if($data[0]!="") $batch_id_cond="and e.id=$data[0]";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	/*echo "SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id";

	$data_array=sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no, d.batch_id from product_details_master a, inv_transaction d,pro_batch_create_mst e where a.id=d.prod_id and d.batch_id=e.id and e.company_id=$data[1] and d.batch_lot='$data[4]' $batch_id_cond and a.item_category_id=3 and d.item_category=3 and d.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,d.body_part_id,a.color, a.unit_of_measure,e.booking_without_order,e.batch_no,d.batch_id");*/


	//$batch_id_sql = sql_select("select a.id as batch_id,b.po_id from pro_batch_create_mst a, pro_batch_create_dtls b, order_wise_pro_details c where a.company_id = $company_id and  b.po_id in($po_id) and a.entry_form in(17,19,202,209,258) group by a.id,b.po_id");


	$batch_id_sql = sql_select("select a.id as batch_id,a.booking_no,a.booking_no_id,b.po_id
	from pro_batch_create_mst a, pro_batch_create_dtls b, order_wise_pro_details c
	where a.id=b.mst_id and b.po_id=c.po_breakdown_id and a.company_id =$company_id and  b.po_id in($po_id) and a.entry_form in(17,19,202,209,258) and a.booking_no is not NULL
	group by a.id,b.po_id,a.booking_no,a.booking_no_id");


	$batchIds="";$bookingNos="";
	foreach($batch_id_sql as $row)
	{
		$batchIds.=$row[csf('batch_id')].",";
		$bookingNos.="'".$row[csf('booking_no')]."',";
	}
	$bookingNos=implode(",",array_unique(explode(",", $bookingNos)));
	// $bookingNos=
	$batchIds=chop($batchIds,",");
	$bookingNos=chop($bookingNos,",");

	$req_issue_qty_array=array();
	$reqIssData = sql_select("SELECT a.prod_id, a.batch_id,a.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when e.trans_type=2 then e.quantity end) as issue_qnty,b.req_id
	from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d ,order_wise_pro_details e
	where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and a.id=e.trans_id and e.trans_type=2 and e.status_active=1 and e.is_deleted=0 and e.po_breakdown_id in($po_id)  and d.booking_no in($bookingNos) and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and a.batch_id in(".$batchIds.")  and b.issue_basis=2 and b.req_id=$requ_mst_id  group by a.prod_id, a.batch_id, a.body_part_id,a.floor_id, a.room, a.rack,b.req_id, a.self,a.bin_box");
	foreach($reqIssData as $row)
	{
		$req_issue_qty_array[$row[csf('req_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
	}

	$issue_qty_array=array();

	$issData = sql_select("SELECT a.prod_id, a.batch_id,a.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when e.trans_type=2 then e.quantity end) as issue_qnty,sum(case when e.trans_type=2 then a.cons_rate*e.quantity  end) as cons_amount
	from inv_issue_master b,inv_transaction a,inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d ,order_wise_pro_details e
	where b.entry_form=19 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.batch_id = d.id and a.id=e.trans_id and e.trans_type=2 and e.status_active=1 and e.is_deleted=0 and e.po_breakdown_id in($po_id)  and d.booking_no in($bookingNos) and b.req_id=$requ_mst_id  and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and a.batch_id in(".$batchIds.") group by a.prod_id, a.batch_id, a.body_part_id,a.floor_id, a.room, a.rack, a.self,a.bin_box");


	foreach($issData as $row)
	{
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
		$issue_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('cons_amount')];
	}


	$recvRt_qty_array=array(); $issRt_qty_array=array();

	$receiveReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=3 then a.cons_quantity end) as recvrqnty,sum(case when d.trans_type=3 then a.cons_rate*d.quantity  end) as cons_amount from inv_transaction a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c, order_wise_pro_details d  where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(3) and a.batch_id_from_fissuertn = c.id and a.id=d.trans_id and d.status_active=1 and d.is_deleted=0 and d.trans_type=3 and d.po_breakdown_id in($po_id)  and c.booking_no in($bookingNos) and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.batch_id_from_fissuertn in($batchIds) group by a.prod_id, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");

	foreach($receiveReturnData as $row)
	{
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]=$row[csf('recvrqnty')];
		$recvRt_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]=$row[csf('cons_amount')];
	}

	$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.body_part_id,  a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty,sum(case when d.trans_type=4 then a.cons_rate*d.quantity  end) as cons_amount from inv_transaction a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,order_wise_pro_details d where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type in(4) and a.batch_id_from_fissuertn = c.id  and a.id=d.trans_id and d.trans_type=4 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id in($po_id) and c.booking_no in($bookingNos) and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.batch_id_from_fissuertn in($batchIds) group by a.prod_id, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin]=$row[csf('issrqnty')];
		$issRt_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin]=$row[csf('cons_amount')];
	}

	$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) as bin_box, sum(d.quantity) as trans_out_qnty,  b.from_prod_id as prod_id,sum(case when d.trans_type=6 then c.cons_rate*d.quantity  end) as cons_amount
		from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
		where  c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id and c.transaction_type = 6 and c.item_category = 3 and b.batch_id = a.id and a.booking_no in($bookingNos) and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=258 and d.trans_type=6 and d.po_breakdown_id in($po_id) and b.active_dtls_id_in_transfer = 1 and b.batch_id in ($batchIds)
		group by b.batch_id, b.from_store, b.floor_id, c.body_part_id, b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");

	foreach($transOutData as $row)
	{
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
		$trans_out_amount_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('cons_amount')];
	}
	if($hdn_variable_setting_status==1)
	{
		$requition_data = sql_select("SELECT x.id,y.color_id as requ_gmts_color,y.fab_color_id, listagg((CAST(y.po_id as varchar2(4000))),',') within group (order by y.po_id) as po_id, y.job_no, y.item_id, y.body_part, y.determination_id, y.gsm, y.dia, y.size_id, sum(y.reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_mst x, pro_fab_reqn_for_cutting_dtls y where   x.id = y.mst_id and  x.status_active=1 and x.is_deleted=0 and y.mst_id=$requ_mst_id and x.entry_form=507 and y.po_id in($po_id) and y.gsm in($gsmx) and y.determination_id in($determination_id) and y.body_part in($body_part) group by x.id,y.color_id,y.fab_color_id, y.job_no, y.item_id, y.body_part, y.determination_id, y.gsm, y.dia, y.size_id");
	}
	else
	{

		$requition_data = sql_select("SELECT x.id,y.color_id as requ_gmts_color,y.fab_color_id, y.po_id, y.job_no, y.item_id, y.body_part, y.determination_id, y.gsm, y.dia, y.size_id,y.reqn_qty from pro_fab_reqn_for_cutting_mst x, pro_fab_reqn_for_cutting_dtls y where   x.id = y.mst_id and  x.status_active=1 and x.is_deleted=0 and x.entry_form=507 and y.mst_id=$requ_mst_id and y.po_id in($po_id) and y.gsm in($gsmx) and y.determination_id in($determination_id) and y.body_part in($body_part)");
	}
	$requ_gmts_colorID=$requ_fab_colorID=$body_partID=$poID=$determinationID="";
	foreach($requition_data as $row)
	{
		$body_partID.=$row[csf('body_part')].",";
		$determinationID.=$row[csf('determination_id')].",";
		$poID.=$row[csf('po_id')].",";
		$requ_gmts_colorID.=$row[csf('requ_gmts_color')].",";
		$requ_fab_colorID.=$row[csf('fab_color_id')].",";
		if($hdn_variable_setting_status==1)
		{
			$toatalReqQntyArr[$row[csf('id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]]+=$row[csf('reqn_qty')];
		}
		else
		{
			$toatalReqQntyArr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]]+=$row[csf('reqn_qty')];	
		}
	}
	$poID=chop($poID,",");
	$determinationID=chop($determinationID,",");
	$body_partID=chop($body_partID,",");
	$requ_gmts_colorID=chop($requ_gmts_colorID,",");
	$requ_fab_colorID=chop($requ_fab_colorID,",");

	// As per Rakib vai if color_size_sensitive=3 contrasta color same as fabric color. or if color_size_sensitive=1 gmts color same as gmts color

	$fab_color_from_gmts_color_data = sql_select("select b.fabric_color_id,b.gmts_color_id
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c
	where a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 and c.color_size_sensitive=1 and b.po_break_down_id in($poID) and b.gmts_color_id in($requ_gmts_colorID) and c.lib_yarn_count_deter_id in($determinationID) and c.body_part_id in($body_partID)
	group by b.fabric_color_id,b.gmts_color_id
	union all
	select b.fabric_color_id,d.contrast_color_id gmts_color_id
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_color_dtls d
	where a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0
	and b.pre_cost_fabric_cost_dtls_id = c.id and c.job_id = d.job_id and c.status_active=1
	and b.po_break_down_id in($poID) and c.lib_yarn_count_deter_id in($determinationID) and d.contrast_color_id in($requ_fab_colorID) and c.body_part_id in($body_partID) and c.color_size_sensitive=3 and d.status_active=1
	group by b.fabric_color_id,d.contrast_color_id");
	$fabric_colorID="";
	foreach($fab_color_from_gmts_color_data as $row)
	{
		$fabric_colorID.=$row[csf('fabric_color_id')].",";
	}
	$fabric_colorID=chop($fabric_colorID,",");

	$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width, x.store_id, x.floor, x.body_part_id, x.batch_id,x.batch_no,x.room,x.rack_no, x.shelf_no,x.bin, sum(x.cons_amount) as cons_amount, sum(x.qnty) as qnty, x.prod_id,x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.po_breakdown_id,x.id
		from
		(
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id,b.original_gsm,b.original_width,(case when b.floor is null or b.floor=0 then 0 else b.floor end) as  floor, b.body_part_id, b.batch_id,e.batch_no,(case when b.room is null or b.room=0 then 0 else b.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin,sum(f.quantity) as qnty, d.gmt_item_id, b.prod_id,sum(d.cons_rate*f.quantity) as cons_amount,a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,listagg((CAST(f.po_breakdown_id as varchar2(4000))),',') within group (order by f.po_breakdown_id) as po_breakdown_id,c.id as recv_mst_id
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e,order_wise_pro_details f
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and d.id=f.trans_id and e.booking_no in($bookingNos) and b.batch_id in($batchIds) and c.company_id=$company_id and a.color in($fabric_colorID) and b.body_part_id in($body_part) and c.entry_form in (17) and a.item_category_id=3 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and f.status_active=1 and f.po_breakdown_id in($po_id)  and f.entry_form in (17)  group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id,b.original_gsm,b.original_width, b.floor,b.body_part_id, b.batch_id,e.batch_no, b.room, b.rack_no, b.shelf_no,b.bin, d.gmt_item_id, b.prod_id, a.detarmination_id,e.booking_without_order,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,c.id
		union all
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id,null as original_gsm,null as original_width,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as  floor, c.body_part_id, b.to_batch_id as batch_id,e.batch_no,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) as bin,sum(f.quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id,sum(c.cons_rate*f.quantity) as cons_amount ,a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,listagg((CAST(f.po_breakdown_id as varchar2(4000))),',') within group (order by f.po_breakdown_id) as po_breakdown_id,null as recv_mst_id
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e,order_wise_pro_details f
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and a.color in($fabric_colorID) and c.transaction_type = 5 and c.item_category = 3  and b.to_batch_id = e.id and c.id=f.trans_id and e.booking_no in($bookingNos) and c.body_part_id in($body_part) and b.to_batch_id in ($batchIds) and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0 and f.is_deleted=0 and f.status_active=1 and f.po_breakdown_id in($po_id) and f.trans_type = 5
		group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,a.dia_width,a.weight,c.store_id, b.to_floor_id, c.body_part_id,e.batch_no, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf,b.bin_box, b.to_prod_id, a.detarmination_id,e.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width
		) x
		group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.dia_width,x.weight,x.original_gsm,x.original_width,x.store_id, x.floor, x.body_part_id, x.batch_id,x.batch_no, x.room,x.rack_no, x.shelf_no,x.bin,x.prod_id, x.detarmination_id,x.booking_without_order,x.fabric_ref,x.rd_no,x.weight_type,x.cutable_width,x.po_breakdown_id,x.id";

		$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=e.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0";
	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_floor_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
		}
	}


	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company_id and item_category_id=3 and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=1055;
	else $table_width=755;


	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="200">Fabric Description</th>
            <th width="50">UOM</th>
            <th width="70">Color</th>
            <?
            if ($varriable_setting_rack_self_maintain==1)
            {
            	?>
	            <th width="60">Floor</th>
	            <th width="60">Room</th>
	            <th width="60">Rack</th>
	            <th width="60">Shelf</th>
	            <th width="60">Bin</th>
	        	<?
	        }
	        ?>
            <th width="60">Recv. Qty</th>
            <th width="60">Issue Qty</th>
            <th width="60">Balance</th>
            <th>Req. Balance</th>
            <th width="70">Prod. ID</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>" id="fabric_listview">
        <?
        $i=1;
        $data_array=sql_select($data_sql);
        foreach($data_array as $row)
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            if($hdn_variable_setting_status==1)
			{
            	$toatalReqQnty=$toatalReqQntyArr[$requ_mst_id][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight')]][$row[csf('dia_width')]];
            	//echo $requ_mst_id.'='.$row[csf('body_part_id')].'='.$row[csf('detarmination_id')].'='.$row[csf('weight')].'='.$row[csf('dia_width')];
            }
            else
            {
            	$toatalReqQnty=$toatalReqQntyArr[$requ_mst_id][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight')]][$row[csf('dia_width')]];
            	//echo $requ_mst_id.'='.$row[csf('po_breakdown_id')].'='.$row[csf('body_part_id')].'='.$row[csf('detarmination_id')].'='.$row[csf('weight')].'='.$row[csf('dia_width')];
            }

            $requisition_iss_qnty=$req_issue_qty_array[$requ_mst_id][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
            $requitionBalance=$toatalReqQnty-$requisition_iss_qnty;
            //echo $toatalReqQnty."-".$requisition_iss_qnty."<br/>";


			$iss_qnty=$issue_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$recvRt_qnty=$recvRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$issRt_qnty=$issRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];

			$trans_out_qnty=$trans_out_qnty_array[$row[csf('id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];



			//Amount
			$iss_amount=$issue_amount_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$recvRt_amount=$recvRt_amount_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$issRt_amount=$issRt_amount_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];

			$trans_out_amount=$trans_out_amount_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];
			$trans_in_amount=$trans_in_amount_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin')]];

			$cum_recv_qty=($row[csf('qnty')]-$recvRt_qnty);
			$cum_recv_amount=(($row[csf('cons_amount')]-$recvRt_amount));
			$cum_recv_qty_title="(Receive - Receive Return + Transfer In)\nReceive=".$row[csf('qnty')]."\nReceive Return=".$recvRt_qnty;

			$cum_iss_qty=$iss_qnty-$issRt_qnty + $trans_out_qnty;
			$cum_iss_amount=$iss_amount-$issRt_amount + $trans_out_amount;
			$cum_iss_qty_title="(Issue - Issue Return + Transfer Out)\nIssue=".$iss_qnty."\nIssue Return=".$issRt_qnty . "\nTransfer Out=" . $trans_out_qnty;

			$balance= number_format($cum_recv_qty,2,".","")-number_format($cum_iss_qty,2,".","");
			$balanceAmount=$cum_recv_amount-$cum_iss_amount;


			$store_id=$row[csf('store_id')];
			$company_id=$row[csf('company_id')];
			$floor_id=$row[csf('floor')];
			$room_id=$row[csf('room')];
			$rack_id=$row[csf('rack_no')];
			$shelf_id=$row[csf('shelf_no')];
			$bin=$row[csf('bin')];

			$floor_name 	= $lib_floor_arr[$row[csf("company_id")]][$row[csf("floor")]];
			$room_name 		= $lib_room_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]];
			$rack_name		= $lib_rack_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]];
			$shelf_name 	= $lib_shelf_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]];
			$bin 			= $lib_bin_arr[$row[csf("company_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("bin")]];

			//$cons_rate = $row[csf('cons_amount')]/$row[csf('qnty')];
			//$cons_rate = number_format($cons_rate,2,".","");

			$cons_rate = $balanceAmount/$balance;
			$cons_rate = number_format($cons_rate,4,".","");


			if($balance>0)
			{
         		?>

	            <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('current_stock')]."**".$row[csf('body_part_id')]."**".$unit_of_measurement[$row[csf('unit_of_measure')]]."**".$row[csf('booking_without_order')]."**".$row[csf('batch_id')]."**".$row[csf('color')]."**".$row[csf('dia_width')]."**".$row[csf('weight')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$color_arr[$row[csf('color')]]."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name."**".$row[csf('floor')]."**".$row[csf('room')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$cons_rate."**".$row[csf('detarmination_id')]."**".$row[csf('bin')]."**".$bin."**".$row[csf('unit_of_measure')]."**".$row[csf('fabric_ref')]."**".$row[csf('rd_no')]."**".$row[csf('weight_type')]."**".$row[csf('cutable_width')]."**".$row[csf('original_gsm')]."**".$row[csf('original_width')]."**".$row[csf('po_breakdown_id')]."**".$requitionBalance; ?>")' style="cursor:pointer" >


	                <td width="30"><? echo $i; ?></td>
	                <td width="70"><? echo $row[csf('batch_no')]; ?></td>
	                <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                <td width="50" align="center" ><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
	                <td width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
	                <?
		            if ($varriable_setting_rack_self_maintain==1)
		            {
		            	?>
						<td width="60"><p><? echo $floor_name; ?></p></td>
						<td width="60"><p><? echo $room_name; ?></p></td>
						<td width="60"><p><? echo $rack_name; ?></p></td>
						<td width="60"><p><? echo $shelf_name; ?></p></td>
						<td width="60"><p><? echo $bin; ?></p></td>
						<?
					}
					?>
					<td width="60" align="right" title="<? echo $cum_recv_qty_title; ?>"><? echo number_format($cum_recv_qty,2,'.',''); ?></td>
					<td width="60" align="right" title="<? echo $cum_iss_qty_title; ?>"><? echo number_format($cum_iss_qty,2,'.',''); ?></td>
					<td width="60" align="right" title="<? echo $ref_title?>"><? echo number_format($balance,2,'.',''); ?></td>
					<td align="right" title="<? echo $ref_title?>"><? echo number_format($requitionBalance,2,'.',''); ?></td>
					<td width="70"><p><? echo $row[csf('id')]; ?></p></td>
	            </tr>
        		<?
        		$i++;
			}
		}
        ?>
    </table>
<?
exit();
}

if ($action=="po_popup_booking_wise")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	//$data=explode("_",$data);
	if($dtls_tbl_id!="")
	{
		$hide_qty_array=return_library_array( "select po_breakdown_id, quantity from order_wise_pro_details where dtls_id=$dtls_tbl_id and entry_form=19 and status_active=1 and is_deleted=0",'po_breakdown_id','quantity');
	}


	foreach($field_level_data_arr[$cbo_company_id] as $val=>$row)
	{
		$is_disable= $row[is_disable];
		$defalt_value= $row[defalt_value];
	}
	if($is_disable)
	{
		$disable_drop_down=$is_disable;
	}
	if($defalt_value)
	{
		$prev_distribution_method=$defalt_value;
	}
	?>
	<script>

		function distribute_qnty(str)
		{
			var issue_basis = $('#issue_basis').val()*1;
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalIssue=0;
				var tot_placeholder_value=0;

				$("#tbl_list_search").find('tr').each(function()
				{
					var req_qnty=$(this).find('input[name="txtReqQnty[]"]').val()*1;
					if(req_qnty>0)
					{
						var placeholder_value =$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder');
						var issued_qnty =$(this).find('input[name="hideQnty[]"]').val();
						tot_placeholder_value = tot_placeholder_value*1+placeholder_value*1+issued_qnty*1;
					}
				});

				if(txt_prop_issue_qnty>tot_placeholder_value)
				{
					var exceeds_qty=txt_prop_issue_qnty-tot_placeholder_value;
					alert("Total Issue Qty Exceeds Total Balance Qty (By "+exceeds_qty+" Qty).");
					$('#txt_prop_issue_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						var issued_qnty=$(this).find('input[name="hideQnty[]"]').val()*1;
						if(issued_qnty==0) issued_qnty='';
						$(this).find('input[name="txtIssueQnty[]"]').val(issued_qnty);
					});

					return;
				}


				if(txt_prop_issue_qnty>=0)
				{
					if(issue_basis == 2)
					{
						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;
							var req_qty = $('#requistion_qty_'+len).val();
							if(req_qty > 0){
								var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
								var perc=(po_qnty/tot_po_qnty)*100;

								var issue_qnty=(perc*txt_prop_issue_qnty)/100;

								totalIssue = totalIssue*1+issue_qnty*1;
								totalIssue = totalIssue.toFixed(2);
								if(tblRow==len)
								{
									var balance = txt_prop_issue_qnty-totalIssue;
									if(balance!=0) issue_qnty=issue_qnty+(balance);
								}
								if(req_qty >=issue_qnty)
								{
									$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
								}
							}
						});
					}
					if(issue_basis == 1)
					{
						var totalAvailableQnty=0;
						$("#tbl_list_search").find('tr').each(function()
						{
							totalAvailableQnty+=$(this).find('input[name="available_blnc_qty[]"]').val()*1;
						});
						if(totalAvailableQnty<txt_prop_issue_qnty)
						{
							alert("Issue quantity is more than receive quantity. Availabe Quantity = "+totalAvailableQnty);
							return;
						}

						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;

							var required_qnty=$(this).find('input[name="txtReqQnty[]"]').val()*1;
							var available_blnc_qty=$(this).find('input[name="available_blnc_qty[]"]').val()*1;
							var perc=(required_qnty/tot_po_qnty)*100;

							var issue_qnty=(perc*txt_prop_issue_qnty)/100;
							//alert(available_blnc_qty+'<'+issue_qnty);
							if(available_blnc_qty<issue_qnty)
							{
								issue_qnty=available_blnc_qty;
							}

							totalIssue = totalIssue*1+issue_qnty*1;
							totalIssue = totalIssue.toFixed(2);
							if(tblRow==len)
							{
								var balance = txt_prop_issue_qnty-totalIssue;
								if(balance!=0) issue_qnty=issue_qnty+(balance);
							}
							$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
						});
					}

				}
			}
			/*if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalIssue=0;
				var tot_placeholder_value=0;

				$("#tbl_list_search").find('tr').each(function()
				{
					var placeholder_value =$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder');
					var issued_qnty =$(this).find('input[name="hideQnty[]"]').val();
					tot_placeholder_value = tot_placeholder_value*1+placeholder_value*1+issued_qnty*1;
				});

				if(txt_prop_issue_qnty>tot_placeholder_value)
				{
					var exceeds_qty=txt_prop_issue_qnty-tot_placeholder_value;
					alert("Total Issue Qty Exceeds Total Balance Qty (By "+exceeds_qty+" Qty).");
					$('#txt_prop_issue_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						var issued_qnty=$(this).find('input[name="hideQnty[]"]').val()*1;
						if(issued_qnty==0) issued_qnty='';
						$(this).find('input[name="txtIssueQnty[]"]').val(issued_qnty);
					});

					return;
				}


				if(txt_prop_issue_qnty>0)
				{
					if(issue_basis == 2)
					{
						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;
							var req_qty = $('#requistion_qty_'+len).val();
							if(req_qty > 0){
								var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
								var perc=(po_qnty/tot_po_qnty)*100;

								var issue_qnty=(perc*txt_prop_issue_qnty)/100;

								totalIssue = totalIssue*1+issue_qnty*1;
								totalIssue = totalIssue.toFixed(2);
								if(tblRow==len)
								{
									var balance = txt_prop_issue_qnty-totalIssue;
									if(balance!=0) issue_qnty=issue_qnty+(balance);
								}
								if(req_qty >=issue_qnty)
								{
									$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
								}
							}
						});
					}
					if(issue_basis == 1)
					{
						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;

							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var perc=(po_qnty/tot_po_qnty)*100;

							var issue_qnty=(perc*txt_prop_issue_qnty)/100;

							totalIssue = totalIssue*1+issue_qnty*1;
							totalIssue = totalIssue.toFixed(2);
							if(tblRow==len)
							{
								var balance = txt_prop_issue_qnty-totalIssue;
								if(balance!=0) issue_qnty=issue_qnty+(balance);
							}

							$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
						});
					}

				}
			}*/
			else
			{
				$('#txt_prop_issue_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtIssueQnty[]"]').val('');
				});
			}
		}

		var selected_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );
		}
		function qty_check(i,issue_basis)
		{
			if(issue_basis == 2){
				var reqn_qty = $('#requistion_qty_'+i).val()*1;
				var current_qty = $('#txtIssueQnty_'+i).val()*1;
				if(current_qty > reqn_qty){
					alert("Issue Qty Can Not Greater Then Requisition Qty");
					$('#txtIssueQnty_'+i).val('');
					return;
				}
			}
		}
		function check_balance(row_no)
		{
			var issue_basis = $('#issue_basis').val()*1;
			var placeholder_value =$('#txtIssueQnty_'+row_no).attr('placeholder')*1;
			var issued_qnty =$('#hideQnty_'+ row_no).val()*1;
			var qnty =$('#txtIssueQnty_'+row_no).val()*1;
			var available_blnc_qty =$('#available_blnc_qty_'+row_no).val()*1;
			var hdn_recv_qnty =$('#hdn_recv_qnty_'+row_no).val()*1; 

			if(issue_basis==2)
			{
				//if(qnty>(hdn_recv_qnty+issued_qnty))
				if(qnty>available_blnc_qty)
				{
					alert("Issue Qnty Exceeds Recv Qnty =" + hdn_recv_qnty + " and Balance =" + available_blnc_qty);
					if(issued_qnty==0) issued_qnty='';
					$('#txtIssueQnty_'+row_no).val(issued_qnty);
				}
			}
			else
			{
				if(qnty>(placeholder_value+issued_qnty))
				{
					alert("Issue Qnty Exceeds Balance Qnty. Balance = " + (placeholder_value+issued_qnty) );
					if(issued_qnty==0) issued_qnty='';
					$('#txtIssueQnty_'+row_no).val(issued_qnty);
				}
			}
			
		}
		function fnc_close()
		{
			var save_data=''; var tot_issue_qnty=''; var tot_rollNo='';
			var po_id_array = new Array(); var buyer_id =''; var po_no='';
			var chk_status=0;var tot_row=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var row_id=$(this).find('input[name="txtPoId[]"]').attr('id');
				var row_id_split=row_id.split("_");
				var row_id_sl=row_id_split[1]*1;

				var txtIssueQntyx = $('#txtIssueQnty_'+row_id_sl).val();
				if(txtIssueQntyx>0)
				{
					var txtHdnPoRcvRatio= $('#txtHdnPoRcvRatio_'+row_id_sl).val();
					tot_row++;
				}
				var placeholder_value =$('#txtIssueQnty_'+row_id_sl).attr('placeholder')*1;
				var issued_qnty =$('#hideQnty_'+ row_id_sl).val()*1;
				var qnty =$('#txtIssueQnty_'+row_id_sl).val()*1;
				if(qnty>(placeholder_value+issued_qnty))
				{
					alert("Issue Qnty Exceeds Balance Qnty. Balance = " + (placeholder_value+issued_qnty) );
					if(issued_qnty==0) issued_qnty='';
					$('#txtIssueQnty_'+row_id_sl).val(issued_qnty);
					chk_status+=1;
				}


				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtIssueQnty=$(this).find('input[name="txtIssueQnty[]"]').val()*1;
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val()*1;

				tot_rollNo=tot_rollNo*1+txtRollNo*1;

				if(txtIssueQnty*1>0)
				{
					if(txtIssueQntyx>0)
					{
						var txtHdnPoRcvRatio= $('#txtHdnPoRcvRatio_'+row_id_sl).val();
						var txtHdnPoRevRat=txtHdnPoRcvRatio.split(",");
							var totOrdReqQnty=0;var po_req_ref=new Array();
							for(var j=0; j<txtHdnPoRevRat.length; j++)
							{
								var txtHdnPo=txtHdnPoRevRat[j].split("=");
								var txtPoIdx=txtHdnPo[0];
								totOrdReqQnty+=txtHdnPo[1]*1;
								po_req_ref[txtPoIdx]=txtHdnPo[1]*1;
							}

							//----------------For adjust praction after calculation po wise qnty distribution---------------------
							var totQntyAftrPraction=0;var txtPoIdxArr=new Array();
							for(var y=0; y<txtHdnPoRevRat.length; y++)
							{
								var txtHdnPo=txtHdnPoRevRat[y].split("=");
								var txtPoIdx=txtHdnPo[0];
								var txtIssueQntx=(po_req_ref[txtPoIdx]/totOrdReqQnty)*txtIssueQntyx;
								txtIssueQntx=decimal_format(txtIssueQntx, 1)*1;
								txtPoIdxArr[txtPoIdx]= txtIssueQntx*1;
								txtIssueQntx=txtIssueQntx*1;
								totQntyAftrPraction+=txtIssueQntx;
							}
							//txtIssueQntyx=txtIssueQntyx*1;

							var lastPoSll=0;

							for(var y=0; y<txtHdnPoRevRat.length; y++)
							{
								var txtHdnPo=txtHdnPoRevRat[y].split("=");
								var txtPoIdx=txtHdnPo[0];

								lastPoSll=txtHdnPoRevRat.length-y;
								//alert(txtPoIdxArr[txtPoIdx]);
								if(lastPoSll==1)
								{
									if(txtIssueQntyx>totQntyAftrPraction)
									{
										var dueQntyafterMinuz = (txtIssueQntyx-totQntyAftrPraction)*1;
										//dueQntyafterMinuz = decimal_format(dueQntyafterMinuz, 1)*1;
										dueQntyafterMinuz = Math.round(dueQntyafterMinuz*100)/100;

										txtPoIdxArr[txtPoIdx]+= dueQntyafterMinuz*1;
										//txtPoIdxArr[txtPoIdx]=decimal_format(txtPoIdxArr[txtPoIdx], 1)*1;
										txtPoIdxArr[txtPoIdx]=Math.round(txtPoIdxArr[txtPoIdx]*100)/100;

									}
									else
									{
										var dueQntyafterMinuz = (totQntyAftrPraction-txtIssueQntyx);
										//dueQntyafterMinuz = decimal_format(dueQntyafterMinuz, 1)*1;
										//dueQntyafterMinuz = dueQntyafterMinuz*1;
										dueQntyafterMinuz = Math.round(dueQntyafterMinuz*100)/100;


										txtPoIdxArr[txtPoIdx]-=dueQntyafterMinuz*1;
										//txtPoIdxArr[txtPoIdx]=decimal_format(txtPoIdxArr[txtPoIdx], 1)*1;
										//txtPoIdxArr[txtPoIdx]=txtPoIdxArr[txtPoIdx]*1;
										txtPoIdxArr[txtPoIdx]=Math.round(txtPoIdxArr[txtPoIdx]*100)/100;
									}
									//alert(txtPoIdxArr[txtPoIdx]);
									//alert(txtPoIdxArr[txtPoIdx]+'+'+dueQntyafterMinuz);
								}

							}


							//alert(txtIssueQntx+parseFloat(dueQntyafterMinuz).toFixed(14)*1);
							lastPoSl=0;
							//-----------------End For adjust praction after calculation po wise qnty distribution-------------

							for(var k=0; k<txtHdnPoRevRat.length; k++)
							{
								var txtHdnPo=txtHdnPoRevRat[k].split("=");
								var txtPoIdx=txtHdnPo[0];
								//var txtIssueQuantity=(po_req_ref[txtPoIdx]/totOrdReqQnty)*txtIssueQntyx;
								var txtIssueQuantity=txtPoIdxArr[txtPoIdx];
								lastPoSl=txtHdnPoRevRat.length-k; //this line for adjust po wise qnty distribution

								if(save_data=="")
								{
									save_data=txtPoIdx+"_"+txtIssueQuantity+"_"+txtRollNo;
								}
								else
								{
									save_data+=","+txtPoIdx+"_"+txtIssueQuantity+"_"+txtRollNo;
								}
							}



					}


					/*if(save_data=="")
					{
						save_data=txtPoId+"_"+txtIssueQnty;
					}
					else
					{
						save_data+=","+txtPoId+"_"+txtIssueQnty;
					}*/

					if( jQuery.inArray(txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}

					if( buyer_id=="" )
					{
						buyer_id=buyerId;
					}

					tot_issue_qnty=tot_issue_qnty*1+txtIssueQnty*1;
				}
			});
			if(chk_status>0)
			{
				return;
			}
			$('#save_data').val( save_data );
			$('#tot_issue_qnty').val(tot_issue_qnty);
			$('#tot_rollNo').val( tot_rollNo );
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#buyer_id').val( buyer_id );
			$('#distribution_method').val( $('#cbo_distribiution_method').val());

			parent.emailwindow.hide();
		}
    </script>
	</head>

	<body>

		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:820px;margin-left:10px">
	        	<input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
	            <input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="">
	            <input type="hidden" name="tot_rollNo" id="tot_rollNo" class="text_boxes" value="">
	            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
	            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
	            <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
	            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	            <input type="hidden" id="issue_basis" value="<? echo $issue_basis; ?>">
	            <div style="width:800px; margin-top:10px; margin-bottom:10px" align="center">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
	                    <thead>
	                        <th>Total Issue Qnty</th>
	                        <th>Distribution Method</th>
	                    </thead>
	                    <?
	                        $is_disabled =0;
	                    	if($issue_basis ==2){
	                    		$prev_distribution_method =2;
	                    		$is_disabled=1;
	                    	}

	                    ?>
	                    <tr class="general">
	                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" value="<? echo number_format($txt_issue_qnty,2,'.','');?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? if($issue_basis ==2) echo 'disabled';?>></td>
	                        <td>
	                            <?
	                                $distribiution_method=array(0=>"--Select--",1=>"Proportionately",2=>"Manually");
	                                echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"",$prev_distribution_method, "distribute_qnty(this.value);",$is_disabled );
	                            ?>
	                        </td>
	                    </tr>
	                </table>
	            </div>
	            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="750">
	                <thead>
	                	<th width="100">Job No</th>
	                	<th width="100">Style Ref.</th>
	                    <th width="120">Booking No</th>
	                    <th width="100">Req. Qnty</th>
	                    <th width="100"><p>Recv. Qty</p></th>
	                    <th width="100"><p>Cum. Issue Qnty</p></th>
	                    <th width="40">Roll</th>
	                    <th>Issue Qnty</th>
	                </thead>
	            </table>
	            <div style="width:768px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="750" id="tbl_list_search">
	                <?
						if($hidden_dia_width!=""){$dia_width_cond = "and a.dia_width='$hidden_dia_width'";}
						if($hidden_dia_width!=""){$gsm_weight_cond = "and a.gsm_weight=$hidden_gsm_weight";}

						$all_batch_po_sql=sql_select("select o.po_breakdown_id,0 as booking_without_order from pro_finish_fabric_rcv_dtls b, order_wise_pro_details o where b.trans_id=o.trans_id and b.batch_id='$txt_batch_id' and o.entry_form in (17) and o.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0
							union all 
							select null as po_breakdown_id,o.booking_without_order from pro_finish_fabric_rcv_dtls b, inv_receive_master o where b.mst_id=o.id and b.batch_id='$txt_batch_id' and o.booking_without_order=1 and o.entry_form in (17) and b.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0
							union all
							select o.po_breakdown_id,0 as booking_without_order from inv_item_transfer_dtls b, order_wise_pro_details o where b.to_trans_id=o.trans_id and b.to_batch_id='$txt_batch_id' and o.entry_form in (258) and o.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0 ");
						foreach($all_batch_po_sql as $p_val)
						{
							if($p_val[csf('booking_without_order')]==1)
							{
								$non_order_arr[$p_val[csf('booking_without_order')]]=$p_val[csf('booking_without_order')];
							}
							else
							{
								$batch_po_arr[$p_val[csf('po_breakdown_id')]]=$p_val[csf('po_breakdown_id')];
							}
							
						}
						$batch_po_id=implode(',',$batch_po_arr);
						$non_order_status=implode(',',$non_order_arr);
						$cumu_rec_qty=array(); $cumu_iss_qty=array();
						if($cbo_body_part!=""){$bodyPartCond="and a.body_part_id=$cbo_body_part";}
						if($cbo_body_part!=""){$bodyPartCond_2="and b.body_part_id=$cbo_body_part";}

						if($txt_fabric_ref){$txt_fabric_ref_cond="and f.fabric_ref='$txt_fabric_ref'";}
						if($txt_rd_no){$txt_rd_no_cond="and f.rd_no='$txt_rd_no'";}
						if($cbo_weight_type){$cbo_weight_type_cond="and b.weight_type='$cbo_weight_type'";}
						if($txt_cutable_width){$txt_cutable_width_cond="and b.cutable_width='$txt_cutable_width'";}
						if($hidden_weight_original){$txt_weight_cond="and c.gsm_weight='$hidden_weight_original'";}
						if($hidden_width_original){$txt_width_cond="and c.dia_width='$hidden_width_original'";}
						if($hidden_selected_po_ids){$hidden_selected_po_cond="and c.po_breakdown_id in($hidden_selected_po_ids)";}
						if($hidden_selected_po_ids){$hidden_selected_po_cond2="and a.po_breakdown_id in($hidden_selected_po_ids)";}


						if($batch_po_id!='' || $non_order_status!='')
						{
							$store_cond = ($cbo_store_name!="") ? "and b.store_id='$cbo_store_name'":"";
							$floor_cond = ($txt_floor!="" && $txt_floor!=0) ? "and b.floor_id='$txt_floor'":"";
							$room_cond 	= ($txt_room!="" && $txt_room!=0) ? "and b.room='$txt_room'":"";
							$rack_cond 	= ($txt_rack!="" && $txt_rack!=0) ? "and b.rack='$txt_rack'":"";
							$shelf_cond = ($txt_shelf!="" && $txt_shelf!=0) ? "and b.self='$txt_shelf'":"";
							$bin_cond = ($txt_bin!="" && $txt_bin!=0) ? "and b.bin_box='$txt_bin'":"";

							/*$sql_stock1="select a.po_breakdown_id as order_id, b.transaction_type, sum(a.quantity) as quantity from inv_transaction b, order_wise_pro_details a where b.id=a.trans_id and a.entry_form in (19,202,209,258) and b.transaction_type in (2,3,4,6) $store_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  group by a.po_breakdown_id, b.prod_id, b.batch_id, b.pi_wo_batch_no, b.body_part_id, b.transaction_type,b.room,b.rack,b.self,b.floor_id,b.bin_box";*/

							$issue_rtn_recv_qnty=sql_select("select b.id, a.po_breakdown_id,a.quantity as qnty,y.job_no_mst ,d.style_ref_no,c.booking_no,c.job_no from inv_transaction b, product_details_master x,order_wise_pro_details a,wo_po_break_down y,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f  where b.prod_id=x.id and x.id=a.prod_id and b.id=a.trans_id and a.po_breakdown_id=y.id and y.job_no_mst=d.job_no and d.job_no=c.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and y.id=c.po_break_down_id and x.color='".$hidden_color_id."'  $bodyPartCond_2 and x.detarmination_id='".$fabric_desc_id."' and b.pi_wo_batch_no=$txt_batch_id $txt_width_cond $txt_weight_cond $store_cond  and b.status_active=1 and a.entry_form=209 and b.is_deleted=0 and b.status_active=1 and x.is_deleted=0 and x.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.item_category=3 and b.transaction_type=4  and b.prod_id=$hidden_prod_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and a.po_breakdown_id in($hidden_selected_po_ids)  group by b.id, a.po_breakdown_id,a.quantity,y.job_no_mst,d.style_ref_no,c.booking_no,c.job_no");
							foreach($issue_rtn_recv_qnty as $row)
							{
								$cumu_issue_rtn_recv[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_issue_rtn_recv_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
							}

							$recv_rtn_issue_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (202) and b.transaction_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id and a.po_breakdown_id in($hidden_selected_po_ids)");
							foreach($recv_rtn_issue_sql as $row)
							{
								$cumu_recv_rtn_issue[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_recv_rtn_issue_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}

							$prev_issue_sql=sql_select("select x.id,a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=y.mst_id and y.trans_id=b.id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id and e.lib_yarn_count_deter_id=f.id and a.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id $hidden_selected_po_cond2 group by x.id,a.po_breakdown_id, b.transaction_type,a.quantity,c.job_no,c.booking_no ,d.style_ref_no
								union all 
								select x.id,null as order_id, b.transaction_type,b.cons_quantity as quantity,null as job_no,c.booking_no ,null as style_ref_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b,pro_batch_create_mst a,wo_non_ord_samp_booking_dtls c where x.id=y.mst_id and y.trans_id=b.id and b.batch_id=a.id and a.booking_no=c.booking_no and a.booking_without_order=1 and x.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and y.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_weight_cond and c.dia='$hidden_width_original' and c.fabric_color=$hidden_color_id and c.lib_yarn_count_deter_id=$fabric_desc_id group by x.id,b.transaction_type,b.cons_quantity,c.booking_no");
							foreach($prev_issue_sql as $row)
							{
								$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_issue_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}

							$prev_transIn_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (5) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id and a.po_breakdown_id in($hidden_selected_po_ids)");
							foreach($prev_transIn_sql as $row)
							{
								$cumu_transIn_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_transIn_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}

							/*$prev_transOut_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond and e.lib_yarn_count_deter_id=$fabric_desc_id and a.po_breakdown_id in($hidden_selected_po_ids)");
							// group by a.po_breakdown_id, b.transaction_type,a.quantity,c.job_no,c.booking_no ,d.style_ref_no
							foreach($prev_transOut_sql as $row)
							{
								$cumu_transOut_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_transOut_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}*/

							$prev_transOut_sql=sql_select("select  h.id as trsnfer_dtls_id,a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no
							from inv_transaction b,order_wise_pro_details a,inv_item_transfer_dtls h, pro_batch_create_mst g, wo_booking_dtls c ,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f 
							where b.id=a.trans_id and a.dtls_id=h.id and b.transaction_type = 6 and b.item_category = 3 and h.batch_id = g.id  and h.from_order_id=a.po_breakdown_id 
							and g.booking_no=c.booking_no and c.po_break_down_id=a.po_breakdown_id  and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id and e.lib_yarn_count_deter_id=f.id and c.job_no=d.job_no and a.trans_type=6  and b.status_active =1 and b.is_deleted =0 and h.status_active =1 and h.is_deleted =0 and a.status_active = 1 and a.is_deleted =0 and a.entry_form=258 and a.trans_type=6 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond and e.lib_yarn_count_deter_id=$fabric_desc_id and a.po_breakdown_id in($hidden_selected_po_ids) group by h.id,a.po_breakdown_id, b.transaction_type,a.quantity,c.job_no,c.booking_no ,d.style_ref_no");
 
							foreach($prev_transOut_sql as $row)
							{
								$cumu_transOut_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_transOut_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}
							
							//print_r($cumu_transOut_qty_arr);

							//$sql_stock1=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no,b.order_rate as rate ,d.style_ref_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d where b.id=a.trans_id   and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and a.entry_form in (19,202,209,258)  and b.transaction_type in (2,3,4,6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2");
							//	fal batch abcNew

							/*select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no,b.order_rate as rate ,d.style_ref_no
							from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e
							where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id and a.entry_form in (19,202,209,258) and b.transaction_type in (2,3,4,6) and b.status_active=1 and b.is_deleted=0
							and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=1 and b.prod_id=29360 and a.prod_id=29360 and b.batch_id=12079 and b.store_id='168' and b.body_part_id=152
							and e.lib_yarn_count_deter_id= 1075 and b.order_rate=1 and b.weight=23 and a.color_id=6089
							group by a.po_breakdown_id,b.transaction_type, a.quantity,c.job_no,c.booking_no,b.order_rate,d.style_ref_no */

							//$sql_recnt_saved_qnty="select a.po_breakdown_id as order_id, b.transaction_type, sum(a.quantity) as quantity from inv_transaction b, order_wise_pro_details a where b.id=a.trans_id and a.entry_form in (19,202,209,258) and b.transaction_type in (2,3,4,6) $store_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 and  group by a.po_breakdown_id, b.prod_id, b.batch_id, b.pi_wo_batch_no, b.body_part_id, b.transaction_type,b.room,b.rack,b.self,b.floor_id,b.bin_box";

							/*$sql_result_cuml=sql_select($sql_stock1);
							foreach($sql_result_cuml as $row)
							{
								if($row[csf('transaction_type')]==2)
								{
 									//$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 									//$cumu_issue_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('rate')]]['qnty']+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==3)
								{
									$cumu_rcv_ret_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==4)
								{
									$cumu_issue_ret_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==6)
								{
									//$cumu_trans_out_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}

								/*$cumu_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv')];
								$cumu_rec_rtn_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv_rtn')];
								$cumu_iss_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_issue')];
								$cumu_iss_rtn_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_issue_rtn')];
								$cumu_transfer_in_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_in')];
								$cumu_transfer_out_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_out')];
							}*/

						}

	                    $i=1; $tot_po_qnty=0; $finish_qnty_array=array();$poWiseRollNoArr=array();
						$explSaveData = explode(",",$save_data);
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$finish_qnty=$po_wise_data[1];

							$finish_qnty_array[$order_id]=$finish_qnty;
							$poWiseRollNoArr[$order_id]=$po_wise_data[2];
						}
					
				 		$sql = "select  x.id,x.buyer_name,x.job_no, sum(x.quantity) as quantity,x.po_qnty_in_pcs, x.po_number,x.style_ref_no,x.booking_no
						 from (
						 select  d.id,e.buyer_name,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no,a.booking_no
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
						 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and  d.job_id=e.id and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$cbo_company_id and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $bodyPartCond_2 $hidden_selected_po_cond
						 group by d.id,e.buyer_name,e.job_no, d.po_number,e.style_ref_no,a.booking_no
						 union all

						select null as id,x.buyer_id as buyer_name,null as job_no, sum(b.cons_quantity) as quantity,null as po_qnty_in_pcs, null as po_number, null as style_ref_no,
						a.booking_no from pro_batch_create_mst a, inv_transaction b, INV_RECEIVE_MASTER x
						where a.id=b.batch_id and b.mst_id=x.id and b.item_category=3 and b.transaction_type=1 
						and x.entry_form in(17) and x.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 

						and x.status_active=1 and x.is_deleted=0 
						and b.batch_id>0 and b.company_id=$cbo_company_id and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $bodyPartCond_2 $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond
						group by x.buyer_id,a.booking_no 

						 union all
						 select  d.id,e.buyer_name,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no,a.booking_no
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
						 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and  d.job_id=e.id and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond  $hidden_selected_po_cond 
						 group by d.id,b.pi_wo_batch_no, e.buyer_name,e.job_no, d.po_number,e.style_ref_no,a.booking_no 
						 union all 
						  select null as id,x.buyer_id as buyer_name,null as job_no, sum(b.cons_quantity) as quantity,null as po_qnty_in_pcs, null as po_number,null as style_ref_no,a.booking_no from wo_non_ord_samp_booking_mst x,pro_batch_create_mst a, inv_transaction b,inv_item_transfer_mst c where x.booking_no=a.booking_no and a.id=b.pi_wo_batch_no and b.mst_id=c.id and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond group by b.pi_wo_batch_no, x.buyer_id,a.booking_no) x
						 group by x.id,x.buyer_name,x.job_no, x.po_number,x.po_qnty_in_pcs,x.style_ref_no,x.booking_no";
 						$nameArray=sql_select($sql);
 						$poIDS="";$bookingNos="";$rev_req_qnty_total=0;
						foreach($nameArray as $row)
						{
							$rev_req_qnty_total+=$row[csf('quantity')];
							$dataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["po_id"].=$row[csf('id')].",";
							//if ($chk_pi_id[$row[csf('pi_id')]]=="")
							//{
								$dataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["recv_qnty"]+=$row[csf('quantity')];
								$dataArr2[$row[csf('booking_no')]]+=$row[csf('quantity')];
								$prev_recv_qnty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
								$prev_recv_qnty_arr2[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('id')]]['qnty']+=$row[csf('quantity')];
							//}
							$poIDS.=$row[csf('id')].",";
							$bookingNos.="'".$row[csf('booking_no')]."',";
						}
						$poIDS=implode(",",array_unique(explode(",", $poIDS)));
						$bookingNos=implode(",",array_unique(explode(",", $bookingNos)));
						$poIDS=chop($poIDS,",");
						$bookingNos=chop($bookingNos,",");
						/*echo "<pre>";
						print_r($dataArr);
						echo "</pre>";*/
						$reqQnty = "SELECT a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty,a.gsm_weight,a.dia_width,a.fabric_color_id from wo_booking_dtls a where a.status_active=1 and a.is_deleted=0 and fabric_color_id=$hidden_color_id $dia_width_cond $gsm_weight_cond group by a.po_break_down_id,a.gsm_weight,a.dia_width,a.fabric_color_id";

						$reqQnty_res = sql_select($reqQnty);
						$req_qty_array=array();
						foreach($reqQnty_res as $req_val)
						{
							$req_qty_array[$req_val[csf('po_break_down_id')]] = $req_val[csf('fabric_qty')];
						}
							/*echo "SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no and d.id in($poIDS) and b.fabric_color_id in($hidden_color_id) and b.booking_no in($bookingNos) and c.body_part_id=$hidden_bodypart_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.gsm_weight='".$hidden_weight_original."' and b.dia_width='".$hidden_width_original."'  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no";*/
					
						if($poIDS!=""){$poCondtions="and d.id in($poIDS)";}else{}

						$finish_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no $poCondtions and b.fabric_color_id in($hidden_color_id) and b.booking_no in($bookingNos) and c.body_part_id=$hidden_bodypart_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.gsm_weight='".$hidden_weight_original."' and b.dia_width='".$hidden_width_original."' and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number,b.booking_no,e.style_ref_no
							union all 
							SELECT null as po_id,null job_no_mst, null po_number, sum(b.finish_fabric) as fin_fab_qnty,b.booking_no,null as style_ref_no
							from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
							where a.booking_no=b.booking_no 
							and b.fabric_color in($hidden_color_id) and b.booking_no in($bookingNos) and b.body_part=$hidden_bodypart_id and b.lib_yarn_count_deter_id=$fabric_desc_id and b.gsm_weight='".$hidden_weight_original."' 
							and b.dia='".$hidden_width_original."'
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
							group by b.booking_no");
						$finish_req_qnty_total=0;
						foreach ($finish_req_qnty_sql as $row) {

							$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
							$finish_req_qnty_total+=$row[csf('fin_fab_qnty')];
							$finish_req_qnty_arr2[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
							$finish_req_qnty_arr3[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
						}

						if($issue_basis == 2)
						{
							$requisition_data = sql_select("SELECT b.reqn_qty, b.po_id,b.job_no,b.style_ref_no from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id where a.company_id =$cbo_company_id and a.entry_form=507 and b.body_part=$hidden_bodypart_id and b.fab_color_id=$hidden_color_id and b.dia ='$hidden_dia_width' and b.gsm=$hidden_gsm_weight and b.status_active=1 and b.is_deleted=0 and b.mst_id= $requisition_id");
							foreach ($requisition_data as $row)
							{
								//$requ_qty_arr[$row[csf('po_id')]] += $row[csf('reqn_qty')];
								$requ_qty_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]] += $row[csf('reqn_qty')];
							}

							$prev_issue_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=y.mst_id and y.trans_id=b.id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id and a.po_breakdown_id in($hidden_selected_po_ids) and x.req_id=$requisition_id group by a.po_breakdown_id, b.transaction_type,a.quantity,c.job_no,c.booking_no ,d.style_ref_no");
							foreach($prev_issue_sql as $row)
							{
								$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
 								$cumu_requsition_issue_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							}

							foreach($dataArr as $jobNo => $job_data)
							{
								foreach($job_data as $bookingNo => $booking_data)
								{
									foreach($booking_data as $styleRef => $row)
									{
										//foreach($style_data as $rate => $row)
										//{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


											$hideQnty=$hide_qty_array[$row[csf('id')]];

											//$cumnRecvQty =($row[csf('quantity')]-$cumu_rcv_ret_qty[$row[csf('id')]]);//-$cumu_trans_out_qty[$row[csf('id')]];

											//echo $cumu_issue_qty[$row[csf('id')]]."+".$cumu_trans_out_qty[$row[csf('id')]]."-".$cumu_issue_ret_qty[$row[csf('id')]]."<br/>";


											//transfer in and recv rtn,issue rtn working pending. when that page modified then adjust in this page
											$prevRecvRtn_issue_Qnty=$cumu_recv_rtn_issue_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevIssueRtn_recv_Qnty=$cumu_issue_rtn_recv_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevIssuQnty=$cumu_issue_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevTransOutQnty=$cumu_transOut_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];


											$prevRecQnty=($prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecvRtn_issue_Qnty);//-$cumu_trans_out_qty[$row[csf('id')]];

											//echo "(".$prevIssuQnty."+".$prevTransOutQnty.")-".$prevIssueRtn_recv_Qnty;
											$cumuIissQty = ($prevIssuQnty+$prevTransOutQnty)-$prevIssueRtn_recv_Qnty;
											$cumul_balance=$prevRecQnty-$cumuIissQty;
											$requiredQnty_finish2=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];

											$balance_req_qnty=$requiredQnty_finish2-$prevRecQnty;

											$availabeQntyWithOverRcv=(($requiredQnty_finish2*$over_receive_limit)/100)+$requiredQnty_finish2;
											$availabeQntyWithOverRcv=$availabeQntyWithOverRcv-$prevRecQnty;
											$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
											$hdn_po_recv_ratio_ref="";$hdn_po_ratio_ref="";$iss_qty="";$roll_no="";
											foreach ($po_arr_uniq as $poID) {
												$orderRequiredQnty=$finish_req_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]["finish_reqt_qnty"];
												$hdn_po_ratio_ref.=$poID."=".$orderRequiredQnty.",";

												$orderRecvRequiredQnty=$prev_recv_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]['qnty'];
												$hdn_po_recv_ratio_ref.=$poID."=".$orderRecvRequiredQnty.",";

												//$po_ratio=$orderRecvRequiredQnty*1/$rev_req_qnty_total*1;
												$iss_qty+=$finish_qnty_array[$poID];
												$roll_no=$poWiseRollNoArr[$poID];

											}


											$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");
											$hdn_po_recv_ratio_ref=chop($hdn_po_recv_ratio_ref,",");
											$po_ids=implode(",", $po_arr_uniq);

											$tot_po_qnty+=$requiredQnty_finish2;

											if ($issue_basis == 2) {
												//$requ_qty = $requ_qty_arr[$po_ids];
												$requ_qty=0;
												foreach($po_arr_uniq as $po_id){
													$requ_qty += $requ_qty_arr[$po_id];
												}
												$requ_qty = $requ_qty_arr[$jobNo][$styleRef];
												$cumuIissQty=$cumu_requsition_issue_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
												$cumul_balance=$requ_qty-$cumuIissQty;
											}

					                     	?>
					                     	<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					                        	<td width="100" title="<? echo  $requ_qty; ?>">
					                        		<? echo $jobNo; ?>
					                        	</td>
					                        	<td width="100">
					                        		<? echo $styleRef; ?>
					                        	</td>
					                            <td width="120" title="<? echo $po_ids; ?>">
					                                <p><? echo $bookingNo; ?></p>

					                                <input type="hidden" name="txtHdnPoRatio[]" id="txtHdnPoRatio_<? echo $i; ?>" value="<? echo $hdn_po_ratio_ref; ?>">
					                                <input type="hidden" name="txtHdnPoRcvRatio[]" id="txtHdnPoRcvRatio_<? echo $i; ?>" value="<? echo $hdn_po_recv_ratio_ref; ?>">

					                                <input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">

					                                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_ids; ?>">
					                                <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
					                                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
					                                <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
					                            </td>

					                            <td width="100" align="right">
													<? echo number_format($requ_qty,2,'.',''); ?>
													<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $requiredQnty_finish2; ?>">

					                            </td>
					                            <td width="100" align="center">
					                                <? echo number_format($prevRecQnty,2,'.',''); ?>
					                                 <input type="hidden" id="hdn_recv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty?>">
					                            </td>
					                            <td width="100" align="center">
					                                <? echo number_format($cumuIissQty,2,'.','');
					                                /*if($issue_basis == 1){
					                                	$cumul_balance=($cumnRecvQty-$cumuIissQty-$cumuIissRtnQty);
					                                }
					                                if($issue_basis == 2){
					                                	$cumul_balance=($requ_qty-$cumuIissQty);
					                                }*/
					                                if($update_dtls_id!="")
					                                {
					                                	/*if($issue_basis == 1)
					                                	{*/
					                                		//echo '<br/>';
					                                		//echo $prevRecQnty.'-'.$cumuIissQty.'-'.$iss_qty;
					                                		//$cumul_balance=($prevRecQnty-($cumuIissQty-$iss_qty));
					                                		$cumul_balance=($requ_qty-($cumuIissQty-$iss_qty));
					                                	//}
					                                	if($iss_qty>0)
														{
															$balanceIssue=($cumuIissQty-$iss_qty);
															$balanceIssue=($prevRecQnty-$balanceIssue);
														}
					                                }

													else{$balanceIssue=$prevRecQnty-$cumuIissQty;	}

					                                ?>
					                            </td>
					                            <td width="40">
				                					<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:28px" value="<? echo $roll_no; ?>">
				                				</td>
					                            <td align="center">
					                                <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width: 60px" value="<? if($iss_qty>0){echo $iss_qty;} ?>" placeholder="<? echo $cumul_balance; ?>" onChange="qty_check(<? echo $i ?>,<? echo $issue_basis ?>)" onKeyUp="check_balance(<? echo $i; ?>);">
					                                <input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
					                                <input type="hidden" id="requistion_qty_<? echo $i; ?>" value="<? echo $cumul_balance?>">
					                                <input type="hidden" name="available_blnc_qty[]" id="available_blnc_qty_<? echo $i; ?>" value="<? echo $balanceIssue;?>">
					                            </td>
					                        </tr>

					                     	<?
					                     	$i++;
										//}
									}
								}
							}
						}
						else
						{
							foreach($dataArr as $jobNo => $job_data)
							{
								foreach($job_data as $bookingNo => $booking_data)
								{
									foreach($booking_data as $styleRef => $row)
									{
										//foreach($style_data as $rate => $row)
										//{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


											$hideQnty=$hide_qty_array[$row[csf('id')]];

											//$cumnRecvQty =($row[csf('quantity')]-$cumu_rcv_ret_qty[$row[csf('id')]]);//-$cumu_trans_out_qty[$row[csf('id')]];

											//echo $cumu_issue_qty[$row[csf('id')]]."+".$cumu_trans_out_qty[$row[csf('id')]]."-".$cumu_issue_ret_qty[$row[csf('id')]]."<br/>";

											if ($issue_basis == 2) {
												$requ_qty = $requ_qty_arr[$po_ids];
											}

											//transfer in and recv rtn,issue rtn working pending. when that page modified then adjust in this page
											$prevRecvRtn_issue_Qnty=$cumu_recv_rtn_issue_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevIssueRtn_recv_Qnty=$cumu_issue_rtn_recv_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevIssuQnty=$cumu_issue_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
											$prevTransOutQnty=$cumu_transOut_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];


											$prevRecQnty=($prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecvRtn_issue_Qnty);//-$cumu_trans_out_qty[$row[csf('id')]];
											//echo $prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'].'-'.$prevRecvRtn_issue_Qnty;
											//echo "(".$prevIssuQnty."+".$prevTransOutQnty.")-".$prevIssueRtn_recv_Qnty;
											$cumuIissQty = ($prevIssuQnty+$prevTransOutQnty)-$prevIssueRtn_recv_Qnty;
											$cumul_balance=$prevRecQnty-$cumuIissQty;
											$requiredQnty_finish2=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];

											$balance_req_qnty=$requiredQnty_finish2-$prevRecQnty;

											$availabeQntyWithOverRcv=(($requiredQnty_finish2*$over_receive_limit)/100)+$requiredQnty_finish2;
											$availabeQntyWithOverRcv=$availabeQntyWithOverRcv-$prevRecQnty;
											$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
											$hdn_po_recv_ratio_ref="";$hdn_po_ratio_ref="";$iss_qty="";$roll_no="";
											foreach ($po_arr_uniq as $poID) {
												$orderRequiredQnty=$finish_req_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]["finish_reqt_qnty"];
												$hdn_po_ratio_ref.=$poID."=".$orderRequiredQnty.",";

												$orderRecvRequiredQnty=$prev_recv_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]['qnty'];
												$hdn_po_recv_ratio_ref.=$poID."=".$orderRecvRequiredQnty.",";

												//$po_ratio=$orderRecvRequiredQnty*1/$rev_req_qnty_total*1;
												$iss_qty+=$finish_qnty_array[$poID];
												$roll_no=$poWiseRollNoArr[$poID];

											}


											$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");
											$hdn_po_recv_ratio_ref=chop($hdn_po_recv_ratio_ref,",");
											$po_ids=implode(",", $po_arr_uniq);

											$tot_po_qnty+=$requiredQnty_finish2;


					                     	?>
					                     	<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					                        	<td width="100" title="<? echo  $requ_qty; ?>">
					                        		<? echo $jobNo; ?>
					                        	</td>
					                        	<td width="100">
					                        		<? echo $styleRef; ?>
					                        	</td>
					                            <td width="120" title="<? echo $po_ids; ?>">
					                                <p><? echo $bookingNo; ?></p>

					                                <input type="hidden" name="txtHdnPoRatio[]" id="txtHdnPoRatio_<? echo $i; ?>" value="<? echo $hdn_po_ratio_ref; ?>">
					                                <input type="hidden" name="txtHdnPoRcvRatio[]" id="txtHdnPoRcvRatio_<? echo $i; ?>" value="<? echo $hdn_po_recv_ratio_ref; ?>">

					                                <input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">

					                                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_ids; ?>">
					                                <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
					                                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
					                                <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
					                            </td>

					                            <td width="100" align="right">
													<? echo number_format($requiredQnty_finish2,2,'.',''); ?>
													<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $requiredQnty_finish2; ?>">

					                            </td>
					                            <td width="100" align="center">
					                                <? echo number_format($prevRecQnty,2,'.',''); ?>
					                            </td>
					                            <td width="100" align="center">
					                                <? echo number_format($cumuIissQty,2,'.','');
					                                /*if($issue_basis == 1){
					                                	$cumul_balance=($cumnRecvQty-$cumuIissQty-$cumuIissRtnQty);
					                                }
					                                if($issue_basis == 2){
					                                	$cumul_balance=($requ_qty-$cumuIissQty);
					                                }*/
					                                if($update_dtls_id!="")
					                                {
					                                	/*if($issue_basis == 1)
					                                	{*/
					                                		//echo "<br/>";
					                                		//echo $prevRecQnty.'-('.$cumuIissQty.'-'.$iss_qty.')<br/>';
					                                		if($cumuIissQty>0)
					                                		{
					                                			//$cumul_balance=($prevRecQnty-($cumuIissQty-$iss_qty));
					                                			$cumul_balance=($prevRecQnty-($cumuIissQty));
					                                		}
					                                		else
					                                		{
					                                			$cumul_balance=($prevRecQnty);
					                                		}
					                                		
					                                		//echo $cumul_balance;
					                                	//}
					                                	if($iss_qty>0)
														{
															//echo $cumuIissQty.'-'.$iss_qty;
															$balanceIssue=($cumuIissQty-$iss_qty);
															//echo $balanceIssue;
															$balanceIssue=($prevRecQnty-$balanceIssue);
														}
					                                }

													else{$balanceIssue=$prevRecQnty-$cumuIissQty;	}

					                                ?>
					                            </td>
					                            <td width="40">
				                					<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:28px" value="<? echo $roll_no; ?>">
				                				</td>
					                            <td align="center">
					                                <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width: 60px" value="<? if($iss_qty>0){echo $iss_qty;} ?>" placeholder="<? echo $cumul_balance; ?>" onChange="qty_check(<? echo $i ?>,<? echo $issue_basis ?>)" onKeyUp="check_balance(<? echo $i; ?>);">
					                                <input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
					                                <input type="hidden" id="requistion_qty_<? echo $i; ?>" value="<? echo $cumul_balance?>">
					                                <input type="hidden" name="available_blnc_qty[]" id="available_blnc_qty_<? echo $i; ?>" value="<? echo $balanceIssue;?>">
					                            </td>
					                        </tr>

					                     	<?
					                     	$i++;
										//}
									}
								}
							}
						}



	                    ?>
	                    <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
	                </table>
	            </div>
	            <table width="820">
	                 <tr>
	                    <td align="center" >
	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
	                    </td>
	                </tr>
	            </table>
			</fieldset>
		</form>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	//$data=explode("_",$data);
	if($dtls_tbl_id!="")
	{
		$hide_qty_array=return_library_array( "select po_breakdown_id, quantity from order_wise_pro_details where dtls_id=$dtls_tbl_id and entry_form=19 and status_active=1 and is_deleted=0",'po_breakdown_id','quantity');
	}

	//Field lavel Access.................start
	foreach($field_level_data_arr[$cbo_company_id] as $val=>$row){
		$is_disable= $row[is_disable];
		$defalt_value= $row[defalt_value];
	}
	if($is_disable){$disable_drop_down=$is_disable;}
	if($defalt_value){$prev_distribution_method=$defalt_value;}
	?>
	<script>

		function distribute_qnty(str)
		{
			var issue_basis = $('#issue_basis').val()*1;
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalIssue=0;
				var tot_placeholder_value=0;

				$("#tbl_list_search").find('tr').each(function()
				{
					var placeholder_value =$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder');
					var issued_qnty =$(this).find('input[name="hideQnty[]"]').val();
					tot_placeholder_value = tot_placeholder_value*1+placeholder_value*1+issued_qnty*1;
				});

				if(txt_prop_issue_qnty>tot_placeholder_value)
				{
					var exceeds_qty=txt_prop_issue_qnty-tot_placeholder_value;
					alert("Total Issue Qty Exceeds Total Balance Qty (By "+exceeds_qty+" Qty).");
					$('#txt_prop_issue_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						var issued_qnty=$(this).find('input[name="hideQnty[]"]').val()*1;
						if(issued_qnty==0) issued_qnty='';
						$(this).find('input[name="txtIssueQnty[]"]').val(issued_qnty);
					});

					return;
				}


				if(txt_prop_issue_qnty>0)
				{
					if(issue_basis == 2)
					{
						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;
							var req_qty = $('#requistion_qty_'+len).val();
							if(req_qty > 0){
								var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
								var perc=(po_qnty/tot_po_qnty)*100;

								var issue_qnty=(perc*txt_prop_issue_qnty)/100;

								totalIssue = totalIssue*1+issue_qnty*1;
								totalIssue = totalIssue.toFixed(2);
								if(tblRow==len)
								{
									var balance = txt_prop_issue_qnty-totalIssue;
									if(balance!=0) issue_qnty=issue_qnty+(balance);
								}
								if(req_qty >=issue_qnty)
								{
									$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
								}
							}
						});
					}
					if(issue_basis == 1)
					{
						$("#tbl_list_search").find('tr').each(function()
						{
							len=len+1;

							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var perc=(po_qnty/tot_po_qnty)*100;

							var issue_qnty=(perc*txt_prop_issue_qnty)/100;

							totalIssue = totalIssue*1+issue_qnty*1;
							totalIssue = totalIssue.toFixed(2);
							if(tblRow==len)
							{
								var balance = txt_prop_issue_qnty-totalIssue;
								if(balance!=0) issue_qnty=issue_qnty+(balance);
							}

							$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
						});
					}

				}
			}
			else
			{
				$('#txt_prop_issue_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtIssueQnty[]"]').val('');
				});
			}
		}

		var selected_id = new Array();

		 function check_all_data()
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );
		}
		function qty_check(i,issue_basis)
		{
			if(issue_basis == 2){
				var reqn_qty = $('#requistion_qty_'+i).val()*1;
				var current_qty = $('#txtIssueQnty_'+i).val()*1;
				if(current_qty > reqn_qty){
					alert("Issue Qty Can Not Greater Then Requisition Qty");
					$('#txtIssueQnty_'+i).val('');
					return;
				}
			}
		}
		function check_balance(row_no)
		{
			var placeholder_value =$('#txtIssueQnty_'+row_no).attr('placeholder')*1;
			var issued_qnty =$('#hideQnty_'+ row_no).val()*1;
			var qnty =$('#txtIssueQnty_'+row_no).val()*1;

			if(qnty>(placeholder_value+issued_qnty))
			{
				alert("Issue Qnty Exceeds Balance Qnty. Balance = " + (placeholder_value+issued_qnty) );
				if(issued_qnty==0) issued_qnty='';
				$('#txtIssueQnty_'+row_no).val(issued_qnty);
			}
		}
		function fnc_close()
		{
			var save_data=''; var tot_issue_qnty='';
			var po_id_array = new Array(); var buyer_id =''; var po_no='';
			var validation_status=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var row_id=$(this).find('input[name="txtPoId[]"]').attr('id');
				var row_id_split=row_id.split("_");
				var row_id_sl=row_id_split[1]*1;

				// popup close validation
				var placeholder_value =$('#txtIssueQnty_'+row_id_sl).attr('placeholder')*1;
				var issued_qnty =$('#hideQnty_'+ row_id_sl).val()*1;
				var qnty =$('#txtIssueQnty_'+row_id_sl).val()*1;

				if(qnty>(placeholder_value+issued_qnty))
				{
					alert("Issue Qnty Exceeds Balance Qnty. Balance = " + (placeholder_value+issued_qnty) );
					if(issued_qnty==0) issued_qnty='';
					$('#txtIssueQnty_'+row_id_sl).val(issued_qnty);
					validation_status=1;
					return;
				}

				// popup close END validation

				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtIssueQnty=$(this).find('input[name="txtIssueQnty[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();

				if(txtIssueQnty*1>0)
				{
					if(save_data=="")
					{
						save_data=txtPoId+"_"+txtIssueQnty;
					}
					else
					{
						save_data+=","+txtPoId+"_"+txtIssueQnty;
					}

					if( jQuery.inArray(txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}

					if( buyer_id=="" )
					{
						buyer_id=buyerId;
					}

					tot_issue_qnty=tot_issue_qnty*1+txtIssueQnty*1;
				}
			});
			if(validation_status==1)
			{
				return;
			}
			$('#save_data').val( save_data );
			//alert(save_data);
			$('#tot_issue_qnty').val(tot_issue_qnty);
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#buyer_id').val( buyer_id );
			$('#distribution_method').val( $('#cbo_distribiution_method').val());

			parent.emailwindow.hide();
		}
    </script>
	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:820px;margin-left:10px">
	        	<input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
	            <input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="">
	            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
	            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
	            <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
	            <input type="hidden" name="tot_rollNo" id="tot_rollNo" class="text_boxes" value="">
	            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	            <input type="hidden" id="issue_basis" value="<? echo $issue_basis; ?>">
	            <div style="width:800px; margin-top:10px; margin-bottom:10px" align="center">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
	                    <thead>
	                        <th>Total Issue Qnty</th>
	                        <th>Distribution Method</th>
	                    </thead>
	                    <?
	                        $is_disabled =0;
	                    	if($issue_basis ==2){
	                    		$prev_distribution_method =2;
	                    		$is_disabled=1;
	                    	}
	                    ?>
	                    <tr class="general">
	                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" value="<? echo number_format($txt_issue_qnty,2,'.','');?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? if($issue_basis ==2) echo 'disabled';?>></td>
	                        <td>
	                            <?
	                                $distribiution_method=array(0=>"--Select--",1=>"Proportionately",2=>"Manually");
	                                echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"",$prev_distribution_method, "distribute_qnty(this.value);",$is_disabled );
	                            ?>
	                        </td>
	                    </tr>
	                </table>
	            </div>
	            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800">
	                <thead>
	                	<th width="100">Job No</th>
	                	<th width="100">Style Ref.</th>
	                    <th width="120">PO No</th>
	                    <th width="100">PO Qnty</th>
	                    <th width="100">Req. Qnty</th>
	                    <th width="100"><p>Cumn.Recv. Qty</p></th>
	                    <th width="100"><p>Cumn. Issue Qnty</p></th>
	                    <th>Issue Qnty</th>
	                </thead>
	            </table>
	            <div style="width:818px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" id="tbl_list_search">
	                <?

						if($hidden_dia_width!=""){$dia_width_cond = "and a.dia_width='$hidden_dia_width'";}
						if($hidden_dia_width!=""){$gsm_weight_cond = "and a.gsm_weight=$hidden_gsm_weight";}

						$reqQnty = "SELECT a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty,a.gsm_weight,a.dia_width,a.fabric_color_id from wo_booking_dtls a where a.status_active=1 and a.is_deleted=0 and fabric_color_id=$hidden_color_id $dia_width_cond $gsm_weight_cond group by a.po_break_down_id,a.gsm_weight,a.dia_width,a.fabric_color_id";

						$reqQnty_res = sql_select($reqQnty);
						$req_qty_array=array();
						foreach($reqQnty_res as $req_val)
						{
							$req_qty_array[$req_val[csf('po_break_down_id')]] = $req_val[csf('fabric_qty')];
						}
						if($issue_basis == 2){
							$requisition_data = sql_select("SELECT b.reqn_qty, b.po_id from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id where a.company_id =$cbo_company_id and a.entry_form=507 and b.body_part=$hidden_bodypart_id and b.fab_color_id=$hidden_color_id and b.dia =$hidden_dia_width and b.gsm=$hidden_gsm_weight and b.status_active=1 and b.is_deleted=0 and b.mst_id= $requisition_id");// and b.color_id=$hidden_color_id
							foreach ($requisition_data as $row) {
								$requ_qty_arr[$row[csf('po_id')]] += $row[csf('reqn_qty')];
							}
						}

						$all_batch_po_sql=sql_select("select o.po_breakdown_id from pro_finish_fabric_rcv_dtls b, order_wise_pro_details o where b.trans_id=o.trans_id and b.batch_id='$txt_batch_id' and o.entry_form in (17) and o.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0
							union all
							select o.po_breakdown_id from inv_item_transfer_dtls b, order_wise_pro_details o where b.to_trans_id=o.trans_id and b.to_batch_id='$txt_batch_id' and o.entry_form in (258) and o.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0 ");
						foreach($all_batch_po_sql as $p_val)
						{
							$batch_po_arr[$p_val[csf('po_breakdown_id')]]=$p_val[csf('po_breakdown_id')];
						}
						$batch_po_id=implode(',',$batch_po_arr);
						$cumu_rec_qty=array(); $cumu_iss_qty=array();
						if($cbo_body_part!=""){$bodyPartCond="and a.body_part_id=$cbo_body_part";}
						if($cbo_body_part!=""){$bodyPartCond_2="and b.body_part_id=$cbo_body_part";}
						if($batch_po_id!='')
						{
							$store_cond = ($cbo_store_name!="") ? "and b.store_id='$cbo_store_name'":"";
							$floor_cond = ($txt_floor!="" && $txt_floor!=0) ? "and b.floor_id='$txt_floor'":"";
							$room_cond 	= ($txt_room!="" && $txt_room!=0) ? "and b.room='$txt_room'":"";
							$rack_cond 	= ($txt_rack!="" && $txt_rack!=0) ? "and b.rack='$txt_rack'":"";
							$shelf_cond = ($txt_shelf!="" && $txt_shelf!=0) ? "and b.self='$txt_shelf'":"";
							$bin_cond = ($txt_bin!="" && $txt_bin!=0) ? "and b.bin_box='$txt_bin'":"";

							$sql_stock1="select a.po_breakdown_id as order_id, b.transaction_type, sum(a.quantity) as quantity from inv_transaction b, order_wise_pro_details a where b.id=a.trans_id and a.entry_form in (19,202,209,258) and b.transaction_type in (2,3,4,6) $store_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  group by a.po_breakdown_id, b.prod_id, b.batch_id, b.pi_wo_batch_no, b.body_part_id, b.transaction_type,b.room,b.rack,b.self,b.floor_id,b.bin_box";
							$sql_recnt_saved_qnty="select a.po_breakdown_id as order_id, b.transaction_type, sum(a.quantity) as quantity from inv_transaction b, order_wise_pro_details a where b.id=a.trans_id and a.entry_form in (19,202,209,258) and b.transaction_type in (2,3,4,6) $store_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$hidden_prod_id and a.prod_id=$hidden_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2   group by a.po_breakdown_id, b.prod_id, b.batch_id, b.pi_wo_batch_no, b.body_part_id, b.transaction_type,b.room,b.rack,b.self,b.floor_id,b.bin_box";

							$sql_result_cuml=sql_select($sql_stock1);
							foreach($sql_result_cuml as $row)
							{
								if($row[csf('transaction_type')]==2)
								{
 									$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==3)
								{
									$cumu_rcv_ret_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==4)
								{
									$cumu_issue_ret_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}
								if($row[csf('transaction_type')]==6)
								{
									$cumu_trans_out_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
								}

								/*$cumu_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv')];
								$cumu_rec_rtn_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv_rtn')];
								$cumu_iss_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_issue')];
								$cumu_iss_rtn_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_issue_rtn')];
								$cumu_transfer_in_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_in')];
								$cumu_transfer_out_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_out')];*/
							}

						}

	                    $i=1; $tot_po_qnty=0; $finish_qnty_array=array();
						$explSaveData = explode(",",$save_data);
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$finish_qnty=$po_wise_data[1];

							$finish_qnty_array[$order_id]=$finish_qnty;
						}

				 		$sql = "select  x.id,x.buyer_name,x.job_no, sum(x.quantity) as quantity,x.po_qnty_in_pcs, x.po_number,x.style_ref_no
						 from (
						 select  d.id,e.buyer_name,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
						 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2
						 group by d.id,e.buyer_name,e.job_no, d.po_number,e.style_ref_no
						 union all
						 select  d.id,e.buyer_name,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
						 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond
						 group by d.id,b.pi_wo_batch_no, e.buyer_name,e.job_no, d.po_number,e.style_ref_no
						 ) x
						 group by x.id,x.buyer_name,x.job_no, x.po_number,x.po_qnty_in_pcs,x.style_ref_no";


	                    $nameArray=sql_select($sql);
	                    foreach($nameArray as $row)
	                    {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	                        $tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
							$iss_qty=$finish_qnty_array[$row[csf('id')]];
							$hideQnty=$hide_qty_array[$row[csf('id')]];

							$cumnRecvQty =($row[csf('quantity')]-$cumu_rcv_ret_qty[$row[csf('id')]]);//-$cumu_trans_out_qty[$row[csf('id')]];

							$cumuIissQty = ($cumu_issue_qty[$row[csf('id')]]+$cumu_trans_out_qty[$row[csf('id')]])-$cumu_issue_ret_qty[$row[csf('id')]];
							//echo $cumu_issue_qty[$row[csf('id')]]."+".$cumu_trans_out_qty[$row[csf('id')]]."-".$cumu_issue_ret_qty[$row[csf('id')]]."<br/>";
							$cumul_balance=$cumnRecvQty-$cumuIissQty;
							if ($issue_basis == 2) {
								$requ_qty = $requ_qty_arr[$row[csf('id')]];
							}

	                     	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                        	<td width="100" title="<? echo  $requ_qty; ?>">
	                        		<? echo $row[csf('job_no')]; ?>
	                        	</td>
	                        	<td width="100">
	                        		<? echo $row[csf('style_ref_no')]; ?>
	                        	</td>
	                            <td width="120" title="<? echo $row[csf('id')]; ?>">
	                                <p><? echo $row[csf('po_number')]; ?></p>
	                                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
	                                <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
	                                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
	                            </td>
	                            <td width="100" align="right">
	                                <? echo $row[csf('po_qnty_in_pcs')]; ?>
	                                <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
	                            </td>
	                            <td width="100" align="right">
									<? echo number_format($req_qty_array[$row[csf('id')]],2,'.',''); ?>
	                            </td>
	                            <td width="100" align="center">
	                                <? echo number_format($cumnRecvQty,2,'.',''); ?>
	                            </td>
	                            <td width="100" align="center">
	                                <? echo number_format($cumuIissQty,2,'.','');
	                                /*if($issue_basis == 1){
	                                	$cumul_balance=($cumnRecvQty-$cumuIissQty-$cumuIissRtnQty);
	                                }
	                                if($issue_basis == 2){
	                                	$cumul_balance=($requ_qty-$cumuIissQty);
	                                }*/
	                                if($update_dtls_id!="")
	                                {
	                                	if($issue_basis == 1)
	                                	{
	                                		$cumul_balance=($cumul_balance+$cumuIissQty);
	                                	}
	                                }


	                                ?>
	                            </td>
	                            <td align="center">
	                                <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width: 60px" value="<? echo $iss_qty; ?>" placeholder="<? echo $cumul_balance?>" onChange="qty_check(<? echo $i ?>,<? echo $issue_basis ?>)" onKeyUp="check_balance(<? echo $i; ?>);">
	                                <input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
	                                <input type="hidden" id="requistion_qty_<? echo $i; ?>" value="<? echo $cumul_balance?>">
	                            </td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
	                </table>
	            </div>
	            <table width="820">
	                 <tr>
	                    <td align="center" >
	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
	                    </td>
	                </tr>
	            </table>
			</fieldset>
		</form>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="check_same_item_found_inSameMRR")
{
	$data=explode("**",$data);
	$updateID=$data[0];
	$prod_id=$data[1];
	$batch_id=$data[2];
	$body_part_id=$data[3];
	$sql=sql_select("select mst_id from inv_transaction  where mst_id=$updateID and prod_id = $prod_id and transaction_type = 2 and item_category = 3 and body_part_id = '$body_part_id' and batch_id = '$batch_id' and is_deleted=0 and status_active=1");
	$mst_id=$sql[0][csf('mst_id')];
	echo "$('#hidden_chk_saved_item').val('".$mst_id."');\n";

	exit();
}
if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];

	$store_id=$data[2];
	$floor_id=$data[3];
	$room=$data[4];
	$rack=$data[5];
	$self=$data[6];
	$bin_box=$data[7];
	$body_part_id=$data[8];
	$batch_id=$data[9];

	$sql=sql_select("select sum(case when a.entry_form=17 and b.batch_id=$batch_id then a.quantity end) as finish_fabric_recv,sum(case when a.entry_form=202 and b.batch_id=$batch_id then a.quantity end) as finish_fabric_recv_rtn,sum(case when a.entry_form=209 and b.pi_wo_batch_no=$batch_id then a.quantity end) as finish_fabric_issue_rtn, sum(case when a.entry_form=19 and b.batch_id=$batch_id then a.quantity end) as finish_fabric_issue,sum(case when a.entry_form=258 and b.batch_id=$batch_id and a.trans_type=5 then a.quantity end) as finish_fabric_transfer_in ,sum(case when a.entry_form=258 and b.batch_id=$batch_id and a.trans_type=6 then a.quantity end) as finish_fabric_transfer_out from order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.po_breakdown_id in($order_id) and a.prod_id=$prod_id and b.prod_id=$prod_id and b.item_category=3 and b.store_id=$store_id and b.floor_id=$floor_id and b.room=$room and b.rack=$rack and b.self=$self and b.bin_box=$bin_box and b.body_part_id='$body_part_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");

	//$sql=sql_select("select sum(case when entry_form=17 then quantity end) as finish_fabric_recv,sum(case when entry_form=202 then quantity end) as finish_fabric_recv_rtn, sum(case when entry_form=19 then quantity end) as finish_fabric_issue,sum(case when entry_form=258 and trans_type=5 then quantity end) as finish_fabric_transfer_in ,sum(case when entry_form=258 and trans_type=6 then quantity end) as finish_fabric_transfer_out from order_wise_pro_details where po_breakdown_id in($order_id) and prod_id=$prod_id and is_deleted=0 and status_active=1");

	//$finish_fabric_recv=($sql[0][csf('finish_fabric_recv')]+$sql[0][csf('finish_fabric_transfer_in')])-($sql[0][csf('finish_fabric_recv_rtn')]);
	$finish_fabric_recv=($sql[0][csf('finish_fabric_recv')]+$sql[0][csf('finish_fabric_transfer_in')])-($sql[0][csf('finish_fabric_recv_rtn')])-($sql[0][csf('finish_fabric_recv_rtn')]);
	$finish_fabric_issued=$sql[0][csf('finish_fabric_issue')]+$sql[0][csf('finish_fabric_transfer_out')];
	//$yet_issue=$sql[0][csf('finish_fabric_recv')]-$sql[0][csf('finish_fabric_issue')];
	$yet_issue=($finish_fabric_recv-$finish_fabric_issued);

	if($db_type==0)
	{
		$order_nos=return_field_value("group_concat(po_number) as po_number","wo_po_break_down","id in($order_id)","po_number");
	}
	else if($db_type==2)
	{
		$order_nos=return_field_value("listagg((CAST(po_number as varchar2(4000))),',') within group (order by po_number) as po_number","wo_po_break_down","id in($order_id)","po_number");
	}
	$finish_fabric_recv=number_format($finish_fabric_recv,2,'.','');
	$finish_fabric_issued=number_format($finish_fabric_issued,2,'.','');
	$yet_issue=number_format($yet_issue,2,'.','');

	echo "$('#txt_order_numbers').val('".$order_nos."');\n";
	echo "$('#txt_fabric_received').val('".$finish_fabric_recv."');\n";
	echo "$('#txt_cumulative_issued').val('".$finish_fabric_issued."');\n";
	echo "$('#txt_yet_to_issue').val('".$yet_issue."');\n";

	exit();
}

if ($action=="finishFabricIssue_popup")
{
	echo load_html_head_contents("Finish Fabric Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data)
		{
			$('#finish_fabric_issue_id').val(data);
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:805px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:800px;margin-left:3px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
	                <thead>
	                	<th>Store</th>
	                	<th>Issue Date Range</th>
	                    <th>Search By</th>
	                    <th width="140" id="search_by_td_up">Please Enter Issue No</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="finish_fabric_issue_id" id="finish_fabric_issue_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td align="center">

	                		 <?
                        $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
						$store_location_id = $userCredential[0][csf('store_location_id')];
						if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}
 							echo create_drop_down( "cbo_store_name_id", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.category_type in(3) $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "" );
                        ?>
                    	</td>
                    	<td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
	                    <td>
							<?
								$search_by_arr=array(1=>"Issue No",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_store_name_id').value+'_'+<? echo $cbo_company_id; ?>, 'create_issue_search_list_view', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
                		<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                	</tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_issue_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$store_id =$data[4];
	$company_id =$data[5];

	if($search_by==1)
		$search_field="a.issue_number";
	else
		$search_field="a.challan_no";
	if($store_id>0){$storeCond="and b.store_id=$store_id";}

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

 	$sql="select a.id, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose, a.buyer_id, a.sample_type, a.issue_basis  from inv_issue_master a,inv_wvn_finish_fab_iss_dtls b where a.id=b.mst_id and a.item_category=3 and a.company_id=$company_id and $search_field like '$search_string' $storeCond $date_cond and a.entry_form=19 and a.status_active=1 and a.is_deleted=0 group by a.id, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose, a.buyer_id, a.sample_type, a.issue_basis  order by a.id desc";

	$company_short_name_arr = return_library_array("select id, company_short_name from lib_company","id","company_short_name");
	$sample_type_arr = return_library_array("select id, sample_name from lib_sample","id","sample_name");
	$arr=array(2=>$woven_issue_basis,3=>$company_short_name_arr,5=>$yarn_issue_purpose,6=>$buyer_arr,7=>$sample_type_arr);
	echo  create_list_view("tbl_list_search", "Issue No,Challan No,Issue Basis,Company,Issue Date,Issue Purpose,Buyer, Sample Type", "120,90,80,80,80,110,100","795","250",0, $sql, "js_set_value", "id", "", 1, "0,0,issue_basis,company_id,0,issue_purpose,buyer_id,sample_type", $arr, "issue_number,challan_no,issue_basis,company_id,issue_date,issue_purpose,buyer_id,sample_type", '','','0,0,0,0,3,0,0,0');
	exit();
}

if($action=='populate_data_from_issue_master')
{
	$data_array=sql_select("select issue_number, challan_no, company_id, issue_date, issue_purpose, buyer_id, sample_type, knit_dye_source, knit_dye_company, issue_basis, buyer_job_no, req_id, req_no,extra_status,location_sewing,is_posted_account from inv_issue_master where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_extra_status').value 			= '".$row[csf("extra_status")]."';\n";


		echo "active_inactive(".$row[csf("issue_purpose")].",0);\n";

		echo "document.getElementById('cbo_sample_type').value 				= '".$row[csf("sample_type")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";

		echo "load_drop_down( 'requires/woven_finish_fabric_issue_controller', '".$row[csf('knit_dye_source')]."'+'_'+'".$row[csf('company_id')]."', 'load_drop_down_sewing_com','sewingcom_td');\n";
		echo "load_drop_down( 'requires/woven_finish_fabric_issue_controller', '".$row[csf('knit_dye_company')]."', 'load_drop_down_sewing_location','sewinglocation_td');\n";


		echo "document.getElementById('cbo_sewing_location').value 			= '".$row[csf("location_sewing")]."';\n";

		echo "document.getElementById('cbo_sewing_company').value 			= '".$row[csf("knit_dye_company")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_issue_purpose').attr('disabled','disabled');\n";
		echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		if($row[csf('issue_basis')]==1){
			echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		}
		else{
			echo "$('#txt_requisition_no').removeAttr('disabled','disabled');\n";
		}
		echo "document.getElementById('cbo_issue_basis').value = '".$row[csf("issue_basis")]."';\n";
		echo "document.getElementById('txt_requisition_no').value = '".$row[csf("req_no")]."';\n";
		echo "document.getElementById('txt_requisition_id').value = '".$row[csf("req_id")]."';\n";
		echo "document.getElementById('hidden_job').value = '".$row[csf("buyer_job_no")]."';\n";

		echo "document.getElementById('hidden_is_posted_account_id').value = '".$row[csf("is_posted_account")]."';\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "$('#is_posted_account_mst').text('Already Posted In Accounting');\n";
		}
		else
		{
			echo "$('#is_posted_account_mst').text(' ');\n";	
		}
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_issue_entry',1,1);\n";
		exit();
	}
}

if($action=="show_finish_fabric_issue_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$wo_po_arr = return_library_array("select a.id, b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no","id","style_ref_no");
	$sql="select id, batch_lot, prod_id, issue_qnty, store_id, no_of_roll, order_id from inv_wvn_finish_fab_iss_dtls where mst_id='$data' and status_active =1 and is_deleted =0";


	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
              	<th width="100">Batch/lot</th>
                <th width="200">Fabric Description</th>
                <th width="100">Issue Quantity</th>
                <th width="80">No Of Roll</th>
                <th width="110">Store</th>
                <!-- <th>Order Numbers</th> -->
                <th>Style Ref No</th>
            </thead>
        </table>
        <div style="width:820px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$order_nos='';
					$order_id=explode(",",$row[csf('order_id')]);

					$styleRefNo=$wo_po_arr[$order_id[0]];
					/*foreach($order_id as $po_id)
					{
						if($po_id>0) $order_nos.=$po_arr[$po_id].", ";
					}
					$order_nos=chop($order_nos,", ");*/
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>)">
                            <td width="40" align="center"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[csf('batch_lot')]; ?></p></td>
                            <td width="200"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100" align="right"><? echo $row[csf('issue_qnty')]; ?></td>
                            <td width="80" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
                            <td width="110"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                            <td style="word-break:break-word; word-wrap: break-word;"><p><? echo $styleRefNo; ?></p></td>
                        </tr>
                    <?
                    $i++;
				}
			?>
            </table>
        </div>
	</div>
    <?
	exit();
}
if($action=='populate_issue_balanc_list_requi_basis')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$poIDs=$data[2];
	$hdn_variable_setting_status=$data[3];
	$data_array=sql_select("select req_id from inv_wvn_finish_fab_iss_dtls a, inv_transaction b,product_details_master c,inv_issue_master d where d.id=a.mst_id and a.mst_id=b.mst_id and a.trans_id=b.id and b.prod_id=c.id and a.mst_id='$id'");
	$requistionId=$data_array[0][csf('req_id')];
	$cutting_requis_dtls=sql_select("SELECT a.company_id, b.po_id, b.job_no, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id and b.po_id in($poIDs)  where b.status_active=1 and a.entry_form=507 and b.is_deleted=0 and b.mst_id=$requistionId");

	$poIds=$jobNos=$bodyPart=$deterIds=$gsm=$dia=$colorIds="";
	foreach ($cutting_requis_dtls as $row)
	{
		$poIds.=$row[csf('po_id')].",";
		//$jobNos.="'".$row[csf('job_no')]."',";
		$bodyPart.=$row[csf('body_part')].",";
		$deterIds.=$row[csf('determination_id')].",";
		$gsm.=$row[csf('gsm')].",";
		$dia.=$row[csf('dia')].",";
		$colorIds.=$row[csf('color_id')].",";
	}
	$poIds=chop($poIds,",");
	//$jobNos=chop($jobNos,",");
	$bodyPart=chop($bodyPart,",");
	$deterIds=chop($deterIds,",");
	$gsm=chop($gsm,",");
	$dia=chop($dia,",");
	$colorIds=chop($colorIds,",");

	echo "show_list_view('".$jobNos.'_'.$row[csf('company_id')].'_'.$bodyPart.'_'.$colorIds.'_'.$deterIds.'_'.$gsm.'_'.$dia.'_'.$poIds.'_'.$requistionId.'_'.$hdn_variable_setting_status."', 'show_fabric_desc_listview_requ','list_fabric_desc_container_rquisition','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');\n";
}

if($action=='populate_issue_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$hdn_variable_setting_status=$data[2];
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$data_array=sql_select("select a.id, a.mst_id,a.trans_id,a.batch_lot,a.batch_id, a.prod_id, a.issue_qnty,a.original_gsm,a.original_width, a.store_id, a.no_of_roll, a.order_id,a.order_save_string,a.cutting_unit,a.remarks, a.roll_save_string,b.body_part_id,b.gmt_item_id,b.company_id,b.floor_id,b.room,rack,b.self,b.bin_box,b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,c.current_stock,c.product_name_details,c.color,c.dia_width,c.weight,d.issue_basis,d.req_id,a.fabric_description_id,b.cons_rate as rate,b.cons_amount,d.issue_purpose from inv_wvn_finish_fab_iss_dtls a, inv_transaction b,product_details_master c,inv_issue_master d where d.id=a.mst_id and a.mst_id=b.mst_id and a.trans_id=b.id and b.prod_id=c.id and a.id='$id'");

	$store_id=$data_array[0][csf('store_id')];
	$company_id=$data_array[0][csf('company_id')];
	$floor_id=$data_array[0][csf('floor_id')];
	$room_id=$data_array[0][csf('room')];
	$rack_id=$data_array[0][csf('rack')];
	$shelf_no=$data_array[0][csf('self')];
	$binbox=$data_array[0][csf('bin_box')];

	$floor_name=return_field_value("a.floor_room_rack_name","lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","a.floor_room_rack_id=b.floor_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id=$floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_room_rack_name");
	$room_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
	$rack_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.rack_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
	$shelf_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.shelf_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and b.shelf_id=$shelf_no and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
	$bin_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.bin_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and b.shelf_id=$shelf_no and b.bin_id=$binbox and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");

	foreach ($data_array as $row)
	{
		$batchId=$row[csf("batch_id")];
	}

	$checkNonOrder=sql_select("select booking_without_order,booking_no from pro_batch_create_mst where id=$batchId and status_active=1 and is_deleted=0");

	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller*3', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		//echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller*3', 'store','store_td', '".$row[csf('company_id')]."','"."','"."','"."','"."','"."','"."','"."','details_reset();',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		/*echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("self")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";*/

		echo "document.getElementById('txt_floor_name').value 				= '".$floor_name."';\n";
		echo "document.getElementById('txt_room_name').value 				= '".$room_name."';\n";
		echo "document.getElementById('txt_rack_name').value 				= '".$rack_name."';\n";
		echo "document.getElementById('txt_shelf_name').value 				= '".$shelf_name."';\n";
		echo "document.getElementById('txt_bin_name').value 				= '".$bin_name."';\n";

		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("self")]."';\n";
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";


		echo "document.getElementById('txt_batch_lot').value 				= '".$row[csf("batch_lot")]."';\n";
		echo "$('#txt_batch_lot').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_body_part').attr('disabled','disabled');\n";

		//$prodData=sql_select("select c.current_stock, c.product_name_details from product_details_master where id='".$row[csf('prod_id')]."'");

		//$product_details=$prodData[0][csf("product_name_details")];
		//$current_stock=$prodData[0][csf("current_stock")];

		echo "document.getElementById('txt_fabric_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('hidden_detarmination_id').value 		= '".$row[csf("fabric_description_id")]."';\n";
		echo "document.getElementById('txt_hdn_rate').value 				= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_hdn_cons_amount').value 			= '".$row[csf("cons_amount")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('hidden_selected_po_ids').value 		= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('save_string').value 					= '".$row[csf("roll_save_string")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$row[csf("order_save_string")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_global_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf('trans_id')]."';\n";
		echo "document.getElementById('cbo_item_name').value 				= '".$row[csf('gmt_item_id')]."';\n";
		echo "document.getElementById('hidden_bodypart_id').value 			= '".$row[csf('body_part_id')]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf('body_part_id')]."';\n";
		echo "document.getElementById('cbo_cutting_floor').value 			= '".$row[csf('cutting_unit')]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf('remarks')]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_name_arr[$row[csf('color')]]."';\n";
		echo "document.getElementById('hidden_color_id').value 				= '".$row[csf('color')]."';\n";
		echo "document.getElementById('hidden_dia_width').value 			= '".$row[csf('dia_width')]."';\n";
		echo "document.getElementById('hidden_gsm_weight').value 			= '".$row[csf('weight')]."';\n";
		echo "document.getElementById('hidden_weight_original').value 		= '".$row[csf('original_gsm')]."';\n";
		echo "document.getElementById('hidden_width_original').value 		= '".$row[csf('original_width')]."';\n";
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf('batch_id')]."';\n";
		echo "document.getElementById('txt_fabric_ref').value 				= '".$row[csf('fabric_ref')]."';\n";
		echo "document.getElementById('txt_rd_no').value 					= '".$row[csf('rd_no')]."';\n";
		echo "document.getElementById('cbo_weight_type').value 				= '".$row[csf('weight_type')]."';\n";
		echo "document.getElementById('txt_cutable_width').value 			= '".$row[csf('cutable_width')]."';\n";
		echo "document.getElementById('txt_weight').value 					= '".$row[csf('weight')]."';\n";
		echo "document.getElementById('txt_width').value 					= '".$row[csf('dia_width')]."';\n";

		if($row[csf("order_id")]!="")
		{
			echo "get_php_form_data('".$row[csf('order_id')]."'+'**'+'".$row[csf('prod_id')]."'+'**'+'".$row[csf('store_id')]."'+'**'+'".$row[csf('floor_id')]."'+'**'+'".$row[csf('room')]."'+'**'+'".$row[csf('rack')]."'+'**'+'".$row[csf('self')]."'+'**'+'".$row[csf('bin_box')]."'+'**'+'".$row[csf('body_part_id')]."'+'**'+'".$row[csf('batch_id')]."', 'populate_data_about_order', 'requires/woven_finish_fabric_issue_controller' );\n";
			//echo "get_php_form_data('".$row[csf('order_id')]."'+'**'+'".$row[csf('prod_id')]."', 'populate_data_about_order', 'requires/woven_finish_fabric_issue_controller' );\n";
		}

		if($row[csf("issue_basis")]==1)
		{
			$booking_no=$checkNonOrder[0][csf('booking_no')];

			if($hdn_variable_setting_status==1)
			{
				echo "show_list_view('".$row[csf('batch_id')].'_'.$row[csf('company_id')].'_'.$row[csf('store_id')].'_'.$booking_no.'_'.$row[csf('batch_lot')].'_'.''.'_'.$row[csf('order_id')].'_'.$hdn_variable_setting_status.'_'.$row[csf('issue_purpose')].'_'.$row[csf('color')]."', 'show_fabric_desc_listview_style_wise','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');\n";
			}
			else
			{
				echo "show_list_view('".$row[csf('batch_id')].'_'.$row[csf('company_id')].'_'.$row[csf('store_id')].'_'.$booking_no.'_'.$row[csf('batch_lot')]."', 'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');\n";
			}
			echo "document.getElementById('hidden_bookingNo').value = '".$booking_no."';\n";
		}
		else
		{
			$orderID=$row[csf("order_id")];
			echo "show_list_view('".$row[csf('req_id')].'_'.$row[csf("order_id")]."', 'populate_list_view','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','');\n";
			$requistionId=$row[csf('req_id')];
			$cutting_requis_dtls=sql_select("SELECT a.company_id, b.po_id, b.job_no, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id and b.po_id in($orderID)  where b.status_active=1 and b.is_deleted=0 and a.entry_form=507 and b.mst_id=$requistionId");

			$poIds=$jobNos=$bodyPart=$deterIds=$gsm=$dia=$colorIds="";
			foreach ($cutting_requis_dtls as $row)
			{
				$poIds.=$row[csf('po_id')].",";
				//$jobNos.="'".$row[csf('job_no')]."',";
				$bodyPart.=$row[csf('body_part')].",";
				$deterIds.=$row[csf('determination_id')].",";
				$gsm.=$row[csf('gsm')].",";
				$dia.=$row[csf('dia')].",";
				$colorIds.=$row[csf('color_id')].",";
			}
			$poIds=chop($poIds,",");
			//$jobNos=chop($jobNos,",");
			$bodyPart=chop($bodyPart,",");
			$deterIds=chop($deterIds,",");
			$gsm=chop($gsm,",");
			$dia=chop($dia,",");
			$colorIds=chop($colorIds,",");
			echo "show_list_view('".$jobNos.'_'.$row[csf('company_id')].'_'.$bodyPart.'_'.$colorIds.'_'.$deterIds.'_'.$gsm.'_'.$dia.'_'.$poIds.'_'.$requistionId.'_'.$hdn_variable_setting_status."', 'show_fabric_desc_listview_requ','list_fabric_desc_container_rquisition','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');\n";
		}

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_issue_entry',1,1);\n";
                if($row[csf("prod_id")])
		{
			echo "get_php_form_data('".$row[csf('prod_id')]."', 'populate_data_uom', 'requires/woven_finish_fabric_issue_controller' );\n";
		}

	}
	if ($checkNonOrder[0]['booking_without_order']==1)
	{
		echo "$('#txt_issue_qnty').removeAttr('readonly','readonly');\n";
		echo "$('#txt_issue_qnty').removeAttr('onClick','openmypage_po();');\n";
		echo "$('#txt_issue_qnty').removeAttr('placeholder','placeholder');\n";
	}
	else
	{
		echo "$('#txt_issue_qnty').attr('readonly','readonly');\n";
		echo "$('#txt_receive_qty').attr('onClick','openmypage_po();');\n";
		echo "$('#txt_issue_qnty').attr('placeholder','Double Click To Open');\n";
	}
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$max_trans_query = sql_select("SELECT max(case when transaction_type in (1,4,5) then transaction_date else null end) as max_date, max(id) as max_id from inv_transaction where prod_id =$hidden_prod_id and store_id=$cbo_store_name and item_category=3 and status_active=1");
	$max_recv_date = $max_trans_query[0][csf('max_date')];
	$max_trans_id = $max_trans_query[0][csf('max_id')];

	if($max_recv_date != "")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'","",$txt_issue_date)));
		if ($issue_date < $max_recv_date)
		{
			echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
			die;
		}
	}

	/*$txt_rack 		= str_replace("'", "", $txt_rack);
	$txt_shelf 			= str_replace("'", "", $txt_shelf);
	$cbo_room 			= str_replace("'", "", $cbo_room);
	$cbo_floor 			= str_replace("'", "", $cbo_floor);

	if($txt_rack==""){$txt_rack=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	if($cbo_room==""){$cbo_room=0;}
	if($cbo_floor==""){$cbo_floor=0;}

    if($operation==1){

		if($max_trans_id > str_replace("'", "", $update_trans_id))
		{
			echo "20**Next transaction found of this store and product. update not allowed.";
			die;
		}

		$up_issue_cond = " and a.id <> $update_trans_id";
	}
	if($hidden_bodypart_id!=""){$bodyPartCond="and a.body_part_id=$hidden_bodypart_id";}
	if($hidden_bodypart_id!=""){$bodyPartCond_2="and b.body_part_id=$hidden_bodypart_id";}
	$$order_lvl_chk2 = "select  x.id,sum(x.quantity) as quantity,x.floor_id,x.room, x.rack, x.self
			 from (
			 select d.id, sum(c.quantity) as quantity,b.floor_id,b.room, b.rack, b.self
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
			 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id'
			 group by d.id,b.floor_id,b.room, b.rack, b.self
			 union all
			 select  d.id, sum(c.quantity) as quantity,b.floor_id,b.room, b.rack, b.self
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
			 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.prod_id=$hidden_prod_id and a.id='$txt_batch_id'
			 group by d.id,b.floor_id,b.room, b.rack, b.self
			 ) x
			 group by x.id,x.floor_id,x.room, x.rack, x.self ";



	$order_lvl_chk = sql_select("SELECT b.po_breakdown_id, c.po_number, a.floor_id,a.room, a.rack, a.self,
	sum(case when b.entry_form=19 and b.trans_type=2 and a.transaction_type=2  then b.quantity end) as finish_fabric_issue,
	sum(CASE WHEN b.entry_form=202 and b.trans_type=3 and a.transaction_type=3  THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
	sum(CASE WHEN b.entry_form=209 and b.trans_type=4 and a.transaction_type=4  THEN b.quantity ELSE 0 END) AS iss_retn_qnty
	sum(CASE WHEN b.entry_form=258 and b.trans_type=6 and a.transaction_type=6  THEN b.quantity ELSE 0 END) AS iss_trans_out_qnty
	from inv_transaction a, order_wise_pro_details b, wo_po_break_down c
	where a.id=b.trans_id and b.po_breakdown_id=c.id and a.pi_wo_batch_no=$hidden_batch_id  and a.prod_id=$hidden_prod_id and b.prod_id=$hidden_prod_id and a.store_id=$cbo_store_name and a.body_part_id=$cbo_body_part and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $up_issue_cond
	group by b.po_breakdown_id, c.po_number, a.floor_id,a.room, a.rack, a.self");
	$floor_id=$room=$rack=$self=0;
	foreach ($order_lvl_chk as $val)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];

		$order_stock_arr[$val[csf('po_breakdown_id')]][$floor_id][$room][$rack][$self] = ($val[csf('finish_fabric_issue')]+$val[csf('recv_rtn_qnty')]+$val[csf('iss_retn_qnty')]) -
		($val[csf('finish_fabric_issue')]+$val[csf('finish_fabric_trans_issued')]+$val[csf('recv_rtn_qnty')]);
		$order_no_arr[$val[csf('po_breakdown_id')]]=$val[csf('po_number')];
	}*/
	/*echo "10**";
	print_r($order_stock_arr);
	die;*/
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$finish_fabric_issue_num=''; $finish_update_id=''; $product_id=$hidden_prod_id;

		$stock_sql=sql_select("select current_stock, color from product_details_master where id=$product_id");

		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$color_id=$stock_sql[0][csf('color')];

		if(str_replace("'","",$txt_issue_qnty)>$curr_stock_qnty)
		{
			echo "20**Issue Qnty Exceeds Stock Qnty";
			//echo "17**0";
			if($db_type==0)
			{
				mysql_query("ROLLBACK");
			}else{
				oci_rollback($con);
			}
			disconnect($con);die;
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
            $new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'WFFI',19,date("Y",time())));


			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, item_category, company_id, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id, issue_basis, buyer_job_no, req_id, req_no,extra_status,location_sewing, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",19,3,".$cbo_company_id.",".$cbo_sample_type.",".$txt_issue_date.",".$txt_challan_no.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_buyer_name.",".$cbo_issue_basis.",".$hidden_job.",".$txt_requisition_id.",".$txt_requisition_no.",".$cbo_extra_status.",".$cbo_sewing_location.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$finish_fabric_issue_num=$new_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="sample_type*issue_date*challan_no*knit_dye_source*knit_dye_company*buyer_id*extra_status*updated_by*update_date";
			$data_array_update=$cbo_sample_type."*".$txt_issue_date."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_buyer_name."*".$cbo_extra_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$finish_fabric_issue_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}

		$avg_rate=$currentStock=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$product_id");
		foreach($sql as $result)
		{
			//$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_value = $result[csf("stock_value")];
		}
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id,floor_id,room,rack,self,bin_box, batch_lot,batch_id,body_part_id,gmt_item_id,cons_uom,fabric_ref,rd_no,weight_type,cutable_width,order_qnty,inserted_by, insert_date";

		$cons_amnt=str_replace("'","",$txt_hdn_rate)*str_replace("'","",$txt_issue_qnty);
		//$order_amnt=str_replace("'","",$txt_hdn_rate)*str_replace("'","",$txt_issue_qnty);

		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_company_id.",".$product_id.",3,2,".$txt_issue_date.",".$txt_issue_qnty.",".$txt_hdn_rate.",".$cons_amnt.",".$txt_challan_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_batch_lot.",".$txt_batch_id.",".$hidden_bodypart_id.",".$cbo_item_name.",".$hidden_uom.",".$txt_fabric_ref.",".$txt_rd_no.",".$cbo_weight_type.",".$txt_cutable_width.",".$txt_issue_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$id_dtls = return_next_id_by_sequence("INV_WV_FIN_FAB_ISS_DTLS_PK_SEQ", "inv_wvn_finish_fab_iss_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, prod_id, issue_qnty, store_id, batch_lot,batch_id, no_of_roll,cutting_unit,remarks, order_id, roll_save_string, order_save_string,fabric_description_id,original_gsm,original_width,color_id, inserted_by, insert_date";

		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$product_id.",".$txt_issue_qnty.",".$cbo_store_name.",".$txt_batch_lot.",".$txt_batch_id.",".$txt_no_of_roll.",".$cbo_cutting_floor.",".$txt_remarks.",".$all_po_id.",".$save_string.",".$save_data.",".$hidden_detarmination_id.",".$hidden_weight_original.",".$hidden_width_original.",".$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";

		$currentStockValue = $stock_value-$cons_amnt;
		$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
		$avg_rate=$currentStockValue/$curr_stock_qnty;

		if($curr_stock_qnty<=0)
		{
			$currentStockValue=0;
			$avg_rate=0;
		}
		$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*'".$currentStockValue."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		if(str_replace("'","",$roll_maintained)==1 && (str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==8 || str_replace("'","",$cbo_issue_purpose)==9))
		{

			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";

			$save_string=explode(",",str_replace("'","",$save_string));
			for($i=0;$i<count($save_string);$i++)
			{
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($i==0) $add_comma=""; else $add_comma=",";
				$roll_dtls=explode("_",$save_string[$i]);
				$roll_id=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_qnty=$roll_dtls[2];
				$order_id=$roll_dtls[3];

				$data_array_roll.="$add_comma(".$id_roll.",".$finish_update_id.",".$id_dtls.",'".$order_id."',19,'".$roll_qnty."','".$roll_no."','".$roll_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9 || str_replace("'","",$cbo_issue_purpose)==10 || str_replace("'","",$cbo_issue_purpose)==30 || str_replace("'","",$cbo_issue_purpose)==64 || str_replace("'","",$cbo_issue_purpose)==31)
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity,order_rate,order_amount, inserted_by, insert_date";
			$orderIdAll="";
			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if($i==0) $add_comma=""; else $add_comma=",";
				$ord_amnt=str_replace("'","",$txt_hdn_rate)*$order_qnty;
				if($order_id!="")
				{
					$orderIdAll.=$order_id.",";
					$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,19,".$id_dtls.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$txt_hdn_rate.",'".$ord_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
			}
		}
		$orderIdAll=chop($orderIdAll,",");
		$rID=$rID2=$rID3=$prod=$rID4=$rID5=true;
		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**Insert into inv_issue_master ($field_array) values  $data_array"; die;
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
		}
		//echo "10**Insert into inv_transaction ($field_array_trans) values  $data_array_trans"; die;

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		//echo "10**Insert into inv_wvn_finish_fab_iss_dtls ($field_array_dtls) values  $data_array_dtls"; die;
		$rID3=sql_insert("inv_wvn_finish_fab_iss_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0); //echo $prod;die;
		if($flag==1)
		{
			if($prod) $flag=1; else $flag=0;
		}

		if(str_replace("'","",$roll_maintained)==1 && (str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==8 || str_replace("'","",$cbo_issue_purpose)==9))
		{
			if($data_array_roll!="")
			{

				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1)
				{
					if($rID4) $flag=1; else $flag=0;
				}
			}
		}


		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9 || str_replace("'","",$cbo_issue_purpose)==10 || str_replace("'","",$cbo_issue_purpose)==30 || str_replace("'","",$cbo_issue_purpose)==64|| str_replace("'","",$cbo_issue_purpose)==31)
		{
			if($data_array_prop!="")
			{
				//echo "10**Insert into order_wise_pro_details ($field_array_proportionate) values  $data_array_prop"; die;
				$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=0;
				}
			}
		}

		//echo "10** $rID=$rID2=$rID3=$prod=$rID5=".$flag;oci_rollback($con);die();

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**0**".$finish_update_id."**".$finish_fabric_issue_num."**".$orderIdAll;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**0**".$finish_update_id."**".$finish_fabric_issue_num."**".$orderIdAll;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**";
			}
		}
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		if(str_replace("'","",$roll_maintained)==1) $product_id=$hidden_prod_id; else $product_id=$hidden_prod_id;


		//max transaction id VALIDATION its VERY IMPORTANT for Rate, amount calculation// issue id:3510
		$sql_max_trans_id = sql_select("select max(a.id) as max_trans_id from inv_transaction a, product_details_master b where a.prod_id=$hidden_prod_id and a.prod_id=b.id and b.item_category_id =3 and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc");
		$max_trans_id=$sql_max_trans_id[0]['MAX_TRANS_ID'];
		if (str_replace("'", "", trim($update_trans_id))!=$max_trans_id) {
			echo "20**Found next transaction against this product ID";
			disconnect($con);
			die;
		}

		$stock_sql=sql_select("select current_stock,avg_rate_per_unit,stock_value,color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$color_id=$stock_sql[0][csf('color')];
		//$avg_rate=$stock_sql[0][csf('avg_rate_per_unit')];
		//$avg_rate_per_unit=$stock_sql[0][csf('avg_rate_per_unit')];
		$stock_value=$stock_sql[0][csf('stock_value')];
		$cons_amnt=str_replace("'","",$txt_issue_qnty)*str_replace("'","",$txt_hdn_rate);

		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";

		if($product_id==$previous_prod_id)
		{
			//$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty)+str_replace("'", '',$hidden_issue_qnty);
			$curr_stock_qnty=$curr_stock_qnty+str_replace("'", '',$hidden_issue_qnty)-str_replace("'","",$txt_issue_qnty);
			//$curr_stock_value=$stock_value-(str_replace("'","",$txt_issue_qnty)-str_replace("'", '',$hidden_issue_qnty))*$avg_rate;

			$curr_stock_value=$stock_value+str_replace("'", '',$txt_hdn_cons_amount)-(str_replace("'","",$txt_issue_qnty)*str_replace("'","",$txt_hdn_rate));

			//$curr_stock_value=$curr_stock_qnty*$avg_rate;

			$avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
			if($curr_stock_qnty<=0)
			{
				$curr_stock_value=0;
				$avg_rate_per_unit=0;
			}

			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*'".$avg_rate_per_unit."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


			$latest_current_stock=$curr_stock_qnty+str_replace("'", '',$txt_issue_qnty);

		}
		else
		{
			//$stock=return_field_value("current_stock","product_details_master","id=$previous_prod_id");

			$stock_sql_prev=sql_select("select current_stock,avg_rate_per_unit,stock_value from product_details_master where id=$previous_prod_id");
			$stock_prev=$stock_sql_prev[0][csf('current_stock')];
			$avg_rate_per_unit_prev=$stock_sql_prev[0][csf('avg_rate_per_unit')];
			$stock_value_prev=$stock_sql_prev[0][csf('stock_value')];

			$adjust_curr_stock=$stock_prev+str_replace("'", '',$hidden_issue_qnty);
			//$adjust_curr_stock_value=$adjust_curr_stock*$avg_rate_per_unit_prev;
			$adjust_curr_stock_value=$stock_value_prev+ str_replace("'","",$txt_hdn_cons_amount);
			$adjust_curr_stock_avg_rate=$adjust_curr_stock_value/$adjust_curr_stock;

			$latest_current_stock=$curr_stock_qnty;

			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
			$curr_stock_value=$curr_stock_qnty*str_replace("'","",$txt_hdn_rate);

			$avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
			if($curr_stock_qnty<=0)
			{
				$curr_stock_value=0;
				$avg_rate_per_unit=0;
			}
			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$avg_rate_per_unit."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			//echo "17**0";
			echo "20**Issue Quantity is more than Current Stock Quantity";
			//$txt_issue_qnty$latest_current_stock
			disconnect($con);die;
		}


		$field_array_update="sample_type*issue_date*challan_no*knit_dye_source*knit_dye_company*buyer_id*extra_status*location_sewing*updated_by*update_date";

		$data_array_update=$cbo_sample_type."*".$txt_issue_date."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_buyer_name."*".$cbo_extra_status."*".$cbo_sewing_location."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$order_amnt=str_replace("'","",$txt_hdn_rate)*str_replace("'","",$txt_issue_qnty);

		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*bin_box*cons_quantity* cons_rate* cons_amount*issue_challan_no*batch_lot*batch_id*body_part_id*gmt_item_id*cons_uom*fabric_ref*rd_no*weight_type*cutable_width*order_qnty*updated_by*update_date";
		$data_array_trans=$product_id."*".$txt_issue_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_issue_qnty."*".$txt_hdn_rate."*'".$cons_amnt."'*".$txt_challan_no."*".$txt_batch_lot."*".$txt_batch_id."*".$hidden_bodypart_id."*".$cbo_item_name."*".$hidden_uom."*".$txt_fabric_ref."*".$txt_rd_no."*".$cbo_weight_type."*".$txt_cutable_width."*".$txt_issue_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="prod_id*issue_qnty*store_id*batch_lot*batch_id*no_of_roll*cutting_unit*remarks*order_id*roll_save_string*order_save_string*fabric_description_id*original_gsm*original_width*color_id*updated_by*update_date";

		$data_array_dtls=$product_id."*".$txt_issue_qnty."*".$cbo_store_name."*".$txt_batch_lot."*".$txt_batch_id."*".$txt_no_of_roll."*".$cbo_cutting_floor."*".$txt_remarks."*".$all_po_id."*".$save_string."*".$save_data."*".$hidden_detarmination_id."*".$hidden_weight_original."*".$hidden_width_original."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		if(str_replace("'","",$roll_maintained)==1 && (str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==8 || str_replace("'","",$cbo_issue_purpose)==9))
		{

			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";

			$save_string=explode(",",str_replace("'","",$save_string));
			for($i=0;$i<count($save_string);$i++)
			{
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($i==0) $add_comma=""; else $add_comma=",";
				$roll_dtls=explode("_",$save_string[$i]);
				$roll_id=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_qnty=$roll_dtls[2];
				$order_id=$roll_dtls[3];

				$data_array_roll.="$add_comma(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',18,'".$roll_qnty."','".$roll_no."','".$roll_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//$id_roll = $id_roll+1;
			}

			/*if($data_array_roll!="")
			{
				//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1)
				{
					if($rID4) $flag=1; else $flag=0;
				}
			}*/
		}

		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9 || str_replace("'","",$cbo_issue_purpose)==10 || str_replace("'","",$cbo_issue_purpose)==30 || str_replace("'","",$cbo_issue_purpose)==64 || str_replace("'","",$cbo_issue_purpose)==31)
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity,order_rate,order_amount, inserted_by, insert_date";
		//	$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );

			$orderIdAll="";
			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];
				
				$orderIdAll.=$order_id.",";

				if($i==0) $add_comma=""; else $add_comma=",";
				$ord_amnt=str_replace("'","",$txt_hdn_rate)*$order_qnty;
				if($order_id!="")
				{
					$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,19,".$update_dtls_id.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$txt_hdn_rate.",'".$ord_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

				//$id_prop = $id_prop+1;
			}
			$orderIdAll=chop($orderIdAll,",");
			//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			/*if($data_array_prop!="")
			{
				$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=0;
				}
			}*/
		}

		//first


			if(str_replace("'","",$product_id)==str_replace("'","",$previous_prod_id))
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($prod) $flag=1; else $flag=0;
			}
			else
			{
				//echo "10**$adjust_curr_stock.'='.$adjust_curr_stock_value ".$previous_prod_id ."==";
				$adjust_prod=sql_update("product_details_master","current_stock*stock_value*avg_rate_per_unit",$adjust_curr_stock."*".$adjust_curr_stock_value."*".$adjust_curr_stock_avg_rate,"id",$previous_prod_id,0);
				if($adjust_prod) $flag=1; else $flag=0;
				//echo "10**$field_array_prod_update"."=".$data_array_prod_update; die;
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1)
				{
					if($prod) $flag=1; else $flag=0;
				}
			}


			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			}
			//print_r($update_trans_id);die;
			//echo "10**$field_array_trans"."=".$data_array_trans;
			$rID2=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}

			$rID3=sql_update("inv_wvn_finish_fab_iss_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1)
			{
				if($prod) $flag=1; else $flag=0;
			}*/

			$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=19",0);
			if($flag==1)
			{
				if($delete_roll) $flag=1; else $flag=0;
			}

			$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=19",0);
			if($flag==1)
			{
				if($delete_prop) $flag=1; else $flag=0;
			}

			if(str_replace("'","",$roll_maintained)==1 && (str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==8 || str_replace("'","",$cbo_issue_purpose)==9))
			{
				if($data_array_roll!="")
				{
					//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
					$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
					if($flag==1)
					{
						if($rID4) $flag=1; else $flag=0;
					}
				}
			}
			if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9 || str_replace("'","",$cbo_issue_purpose)==10 || str_replace("'","",$cbo_issue_purpose)==30 || str_replace("'","",$cbo_issue_purpose)==64 || str_replace("'","",$cbo_issue_purpose)==31)
			{
				if($data_array_prop!="")
				{
					$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
					if($flag==1)
					{
						if($rID5) $flag=1; else $flag=0;
					}
				}
			}


		//last
		//echo "10**$prod**$rID**$rID2**$rID3**$rID4**$rID5**$flag"; die();


		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$orderIdAll;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$orderIdAll;
			}
			else
			{
				oci_rollback($con);
				echo "6**1";
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------


		//$mst_id = return_field_value("id","inv_receive_master","recv_number like $txt_recv_number");
		$mst_id = return_field_value("id","inv_issue_master","issue_number like $txt_system_id");
		//echo "10**".$mst_id;die;
		//if($mst_id=="" || $mst_id==0){ echo "15**0"; disconnect($con);die;}



		$batchId=str_replace("'","",$txt_batch_id);
		$txt_prod_code=str_replace("'","",$hidden_prod_id);


		//=============== Receive Return Check =======================

		$rcv_rtn_number = sql_select("SELECT A.ISSUE_NUMBER, MAX(B.TRANSACTION_DATE) AS MAX_DATE FROM INV_ISSUE_MASTER A,INV_TRANSACTION B WHERE A.ID = B.MST_ID AND B.ITEM_CATEGORY=3 AND B.PI_WO_BATCH_NO='$batchId' AND B.PROD_ID='$txt_prod_code' AND B.TRANSACTION_TYPE=3 AND A.ENTRY_FORM=202 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ISSUE_NUMBER");

		foreach($rcv_rtn_number as $row)
		{
			if($row['MAX_DATE'] != "")
			{
				$max_transaction_date = date("Y-m-d", strtotime($row['MAX_DATE']));
				$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
				if ($issue_date <= $max_transaction_date)
				{
					echo "20**Delete not possible because Receive Return Number = ".$row['ISSUE_NUMBER']." Found.";
					disconnect($con);
					die;
				}
			}
		}

		//=============== Receive Check =======================

		$recv_number = sql_select("SELECT A.RECV_NUMBER, MAX(B.TRANSACTION_DATE) AS MAX_DATE,max(b.id)  as MAX_RECV_TRANS_ID FROM INV_RECEIVE_MASTER A,INV_TRANSACTION B  WHERE A.ID = B.MST_ID AND B.ITEM_CATEGORY=3 AND B.BATCH_ID='$batchId' AND B.PROD_ID='$txt_prod_code' AND  B.TRANSACTION_TYPE=1 AND A.ENTRY_FORM=17 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.RECV_NUMBER");

		foreach($recv_number as $row)
		{
			if($row['MAX_DATE'] != "" && $row['MAX_RECV_TRANS_ID']>str_replace("'","",$update_trans_id) )
			{
				$max_transaction_date = date("Y-m-d", strtotime($row['MAX_DATE']));
				$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
				if ($issue_date <= $max_transaction_date)
				{
					echo "20**Delete not possible because Receive No = ".$row['RECV_NUMBER']." Found.";
					disconnect($con);
					die;
				}
			}
		}

		//=============== Issue Check =======================

		$issue_number = sql_select("SELECT A.ISSUE_NUMBER, MAX(B.TRANSACTION_DATE) AS MAX_DATE,max(b.id)  as MAX_ISSUE_TRANS_ID FROM INV_ISSUE_MASTER A,INV_TRANSACTION B  WHERE A.ID = B.MST_ID AND B.ITEM_CATEGORY=3 AND B.BATCH_ID='$batchId' AND B.PROD_ID='$txt_prod_code' AND  B.TRANSACTION_TYPE=2 AND A.ENTRY_FORM=19 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ISSUE_NUMBER");

		foreach($issue_number as $row)
		{
			if($row['MAX_DATE'] != "" && $row['MAX_ISSUE_TRANS_ID']>str_replace("'","",$update_trans_id))
			{
				$max_transaction_date = date("Y-m-d", strtotime($row['MAX_DATE']));
				$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
				if ($receive_date <= $max_transaction_date)
				{
					echo "20**Delete not possible because Issue No = ".$row['ISSUE_NUMBER']." Found.";
					disconnect($con);
					die;
				}
			}
		}


		//=============== Issue Return Check =======================

		$issue_rtn_number = sql_select("SELECT A.RECV_NUMBER, MAX(B.TRANSACTION_DATE) AS MAX_DATE FROM INV_RECEIVE_MASTER A,INV_TRANSACTION B WHERE A.ID = B.MST_ID AND B.ITEM_CATEGORY=3 AND B.PI_WO_BATCH_NO='$batchId' AND B.PROD_ID='$txt_prod_code' AND B.TRANSACTION_TYPE=4 AND A.ENTRY_FORM=209 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.RECV_NUMBER");

		foreach($issue_rtn_number as $row)
		{
			if($row['MAX_DATE'] != "")
			{
				$max_transaction_date = date("Y-m-d", strtotime($row['MAX_DATE']));
				$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
				if ($issue_date <= $max_transaction_date)
				{
					echo "20**Delete not possible because Issue Return Number = ".$row['RECV_NUMBER']." Found.";
					disconnect($con);
					die;
				}
			}
		}

		//===============Transfer Check =======================

		$trans_in_out = sql_select("SELECT A.TRANSFER_SYSTEM_ID, MAX(B.TRANSACTION_DATE) AS MAX_DATE FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION B WHERE A.ID = B.MST_ID AND B.ITEM_CATEGORY=3 AND B.PI_WO_BATCH_NO='$batchId' AND B.PROD_ID='$txt_prod_code' AND B.TRANSACTION_TYPE in(5,6) AND A.ENTRY_FORM=258 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.TRANSFER_SYSTEM_ID");

		foreach($trans_in_out as $row)
		{
			if($row['MAX_DATE'] != "")
			{
				$max_transaction_date = date("Y-m-d", strtotime($row['MAX_DATE']));
				$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
				if ($issue_date <= $max_transaction_date)
				{
					echo "20**Delete not possible because Transfer System ID = ".$row['TRANSFER_SYSTEM_ID']." Found.";
					disconnect($con);
					die;
				}
			}
		}

		//===============Product Stock Check =======================

		$hidden_issue_qnty = str_replace("'","",$hidden_issue_qnty);

		$sql = sql_select("select avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_prod_code");
		$presentStock=$presentStockValue=0;$stockValueAfterIssueDelete=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock		= $result[csf("current_stock")];
			$presentStockValue	= $result[csf("stock_value")];
			$stockValueAfterIssueDelete = $result[csf("avg_rate_per_unit")]*$hidden_issue_qnty;

		}

		$currentStock	= $presentStock+$hidden_issue_qnty;
		$StockValue	= $presentStockValue+$stockValueAfterIssueDelete;
		//$avgRate		=number_format($StockValue/$currentStock,$dec_place[3],'.','');

		//if(is_nan($StockValue/$currentStock)){$avgRate=0;}
		//echo "10**".$avgRate.'/'.$currentStock;die;

		$field_array2="current_stock*stock_value*updated_by*update_date";

		$data_array2 = "".$currentStock."*".number_format($StockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=$dtlsrID=$rID3=$rID4=$prodUpdate=true;

		$f_issue_dtls_sql = "select id from INV_WVN_FINISH_FAB_ISS_DTLS where mst_id=$mst_id and status_active=1 and is_deleted=0";
		$f_issue_dtls_rslts = sql_select($f_issue_dtls_sql);

		$rID4=sql_update("INV_WVN_FINISH_FAB_ISS_DTLS",$field_array,$data_array,"id",$update_dtls_id,1);

		if($rID4) $flag=1; else $flag=0;

		$f_issue_dtls_count = count($f_issue_dtls_rslts);

		if($f_issue_dtls_count == 1)
		{
			$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$mst_id,0);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
				$resetLoad=1;
			}
		}
		else
		{
			$resetLoad=2;
		}


		$dtlsrID=sql_update("inv_transaction",$field_array,$data_array,"id",$update_trans_id,1);
		if($flag==1)
		{
			if($dtlsrID) $flag=1; else $flag=0;
		}

		$rID3=sql_update("order_wise_pro_details",$field_array,$data_array,"trans_id",$update_trans_id,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}
		//echo "10**".$data_array2; oci_rollback($con);die;
		$prodUpdate = sql_update("product_details_master",$field_array2,$data_array2,"id",$txt_prod_code,1);
		if($flag==1)
		{
			if($prodUpdate) $flag=1; else $flag=0;
		}
		/*$rID = sql_update("inv_receive_master",'status_active*is_deleted','0*1',"id*item_category","$mst_id*1",1);
		$dtlsrID = sql_update("inv_transaction",'status_active*is_deleted','0*1',"mst_id*item_category","$mst_id*1",1);*/
		//echo "10**".$field_array2."=".$data_array2."=".$txt_prod_code ; die;
		//echo "10**".sql_update("product_details_master","id",$field_array2,$data_array2,$txt_prod_code);die;


		// echo "10**".$resetLoad;die;
		// echo "10**".$flag;die;
		//echo "10**$flag**$rID**$dtlsrID**$rID3**$rID4**$prodUpdate"; die();

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				//echo "2**".str_replace("'","",$txt_system_id)."**".$resetLoad;
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;

			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				//echo "2**".str_replace("'","",$txt_system_id)."**".$resetLoad;
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="woven_finish_fabric_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	 $sql_batch_id=sql_select("select b.batch_id,c.booking_without_order from inv_issue_master a,inv_wvn_finish_fab_iss_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_lot=c.batch_no and a.id='$data[1]' and a.company_id=c.company_id  and a.company_id='$data[0]' and a.entry_form=19 and a.status_active=1 and a.is_deleted=0 group by b.batch_id,c.booking_without_order");

	foreach ($sql_batch_id as $row)
	{
		$booking_without_order=$row[csf("booking_without_order")];
	}

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id,location_sewing from  inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=19";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	if ($booking_without_order==0)
	{
		$sql_dtls="select id, batch_lot, prod_id, issue_qnty, store_id, no_of_roll, order_id,cutting_unit,remarks,batch_id from  inv_wvn_finish_fab_iss_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	}
	else
	{
		$sql_dtls="select a.id, a.batch_lot, c.prod_id, a.issue_qnty, a.store_id, a.no_of_roll, a.order_id,a.cutting_unit,a.remarks,a.batch_id,b.booking_no_id,b.booking_without_order from inv_wvn_finish_fab_iss_dtls a,pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 ";

		$sql_result_batch= sql_select($sql_dtls);
		foreach ($sql_result_batch as $row)
		{
			$batchId=$row[csf("batch_id")];
		}
		$checkNonOrder=sql_select("select id,booking_without_order,booking_no_id from pro_batch_create_mst where id=$batchId and status_active=1 and is_deleted=0");
		foreach ($checkNonOrder as $row)
		{
			$nonOrderbooking_array[]=$row[csf('booking_no_id')];
		}
	}

	$sql_result= sql_select($sql_dtls);
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	$cutting_unit_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 order by floor_name","id","floor_name");

	?>
	<div style="width:1330px;">
    <table width="1300" cellspacing="0" align="left">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large; margin-bottom: 10px;"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>

    <br>

        <tr>
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="130"><strong>Issue Purpose:</strong></td> <td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            <td width="125"><strong>Sample Type:</strong></td><td width="175px"><? echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Sewing Source:</strong></td> <td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Sewing Com:</strong></td><td width="175px"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]]; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];  ?></td>
           <?php /*?> <td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td><?php */?>
		   <td><strong>Sewing Location:</strong></td><td width="175px"><? echo $location_library[$dataArray[0][csf('location_sewing')]]; ?></td>
            <td><strong>Cutting Unit No.</strong></td>
            <td width="175px">
            	<?
            		foreach($sql_result as $cutt_row){
            			$cutting_units .= $cutting_unit_arr[$cutt_row[csf("cutting_unit")]].",";
            		}
            		$cutting_units = chop($cutting_units,",");
            		echo $cutting_units;
            	?>
            </td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">

    	<?
    	if ($booking_without_order==0)
    	{

    		?>
			<style>
					.wrd_brk{word-break: break-all;word-wrap: break-word;}
			</style>

		    <table align="right" cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
		            <th width="100" >Buyer</th>
		            <th width="100" >Style Ref</th>
		            <th width="100" >Job Number</th>
		            <th width="100" >Order Numbers</th>
		            <th width="80" >Batch/Lot</th>
		            <th width="180" >Fabric Description</th>
		            <th width="50" >Color Name</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >Roll Qty</th>
		            <!--<th width="40" >No Of Roll</th>-->
		            <th width="110" >Store</th>
		            <th width="50" >UOM</th>
		            <th width="100" >Remarks</th>
		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{

							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';

						}



						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');

						$sql_uom = sql_select("select id,unit_of_measure  from product_details_master where id in ($prodID)");
						foreach ($sql_uom as $datas) {
							$uom_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];

						}

						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";

							if($row[csf('order_id')]!="")
							{
								if($db_type==0)
								{
									$order_nos=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in(".$row[csf('order_id')].")","po_no");
									$job_nos=return_field_value("group_concat(job_no_mst) as job_no_mst","wo_po_break_down","id in(".$row[csf('order_id')].")","job_no_mst");
									$style_ref_nos=return_field_value("group_concat(b.style_ref_no) as style_ref_no","wo_po_break_down a,wo_po_details_master b ","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","style_ref_no");
									$buyer_names=return_field_value("group_concat(b.buyer_name) as buyer_name","wo_po_break_down a,wo_po_details_master b ","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","buyer_name");
								}
								else
								{
									$order_nos=return_field_value("listagg((CAST(po_number as varchar2(4000))),',') within group (order by po_number) as po_number","wo_po_break_down","id in(".$row[csf('order_id')].")","po_number");
									$job_nos=return_field_value("listagg((CAST(job_no_mst as varchar2(4000))),',') within group (order by job_no_mst) as job_no_mst","wo_po_break_down","id in(".$row[csf('order_id')].")","job_no_mst");
									$style_ref_nos=return_field_value("listagg((CAST(b.style_ref_no as varchar2(4000))),',') within group (order by b.style_ref_no) as style_ref_no","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","style_ref_no");
									$buyer_names=return_field_value("listagg((CAST(b.buyer_name as varchar2(4000))),',') within group (order by b.buyer_name) as buyer_name","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","buyer_name");
								}

								$buyer_name=explode(",",$buyer_names);
								$buyers="";
								foreach(array_unique($buyer_name) as $rows)
								{
									$buyers.= $buyer_library[$rows].',';
								}
								$style_ref_no=explode(",",$style_ref_nos);
								$styleRef="";
								foreach(array_unique($style_ref_no) as $rows)
								{
									$styleRef.= $rows.',';
								}

								$job_no=explode(",",$job_nos);
								$jobNo="";
								foreach(array_unique($job_no) as $rows)
								{
									$jobNo.= $rows.',';
								}
							}
							else
							{

								// if ($row[csf('booking_without_order')]==1)
								// {
								// 	//for without order
								// 	$booking_ids = implode(",",$nonOrderbooking_array);
								// 	if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
								// 	$booking_datas=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
								// 	foreach($booking_datas as $rows){
								// 		$po_number_details[$rows[csf('id')]]['buyer_name'] = $rows[csf('buyer_id')];
								// 		//$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
								// 	}
								// }
								// $order_nos='';
							}

							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center" class="wrd_brk"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo chop($buyers,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? echo chop($styleRef,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? echo chop($jobNo,","); ?></p></td>
					                <td style="word-break:break-all;"><? echo $order_nos; ?></td>
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td><? echo $product_arr[$row[csf("prod_id")]]; ?></td>
					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <?php /*?><td align="center"><? echo $row[csf("no_of_roll")]; ?></td><?php */?>
					                <td><? echo $store_library[$row[csf("store_id")]]; ?></td>
					                <td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="8" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($totalQnty,4); ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="2"><?php // echo $totalAmount; ?></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		else
		{
			?>
			<table align="left" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
		            <th width="100" >Buyer</th>
		            <!-- <th width="100" >Style Ref</th>
		            <th width="100" >Job Number</th>
		            <th width="100" >Order Numbers</th> -->
		            <th width="80" >Batch/Lot</th>
		            <th width="180" >Fabric Description</th>
		            <th width="50" >Color Name</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >Roll Qty</th>
		            <!--<th width="40" >No Of Roll</th>-->
		            <th width="110" >Store</th>
		            <th width="50" >UOM</th>
		            <th width="100" >Remarks</th>
		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{

							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';

						}



						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');

						$sql_uom = sql_select("select id,unit_of_measure  from product_details_master where id in ($prodID)");
						foreach ($sql_uom as $datas) {
							$uom_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];

						}

						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							//for without order
							$booking_ids = implode(",",$nonOrderbooking_array);
							if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
							$booking_datas=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
							foreach($booking_datas as $rows){
								$po_number_details[$rows[csf('id')]]['buyer_name'] = $rows[csf('buyer_id')];
								//$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
							}
							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo $buyer_arr[$po_number_details[$row[csf('booking_no_id')]]['buyer_name']]; ?></p></td>
					                <!-- <td><p style="word-break:break-all;"><? //echo chop($styleRef,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? //echo chop($jobNo,","); ?></p></td>
					                <td><? //echo $order_nos; ?></td> -->
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td><? echo $product_arr[$row[csf("prod_id")]]; ?></td>
					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <?php /*?><td align="center"><? echo $row[csf("no_of_roll")]; ?></td><?php */?>
					                <td><? echo $store_library[$row[csf("store_id")]]; ?></td>
					                <td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="5" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($totalQnty,4); ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="2"><?php // echo $totalAmount; ?></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		?>
        <br>
		 <?
            echo signature_table(22, $data[0], "900px");
         ?>
      </div>
   </div>
	<?
	exit();
}

if ($action=="woven_finish_fabric_issue_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	 $sql_batch_id=sql_select("select b.batch_id,c.booking_without_order from inv_issue_master a,inv_wvn_finish_fab_iss_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_lot=c.batch_no and a.id='$data[1]' and a.company_id=c.company_id  and a.company_id='$data[0]' and a.entry_form=19 and a.status_active=1 and a.is_deleted=0 group by b.batch_id,c.booking_without_order");

	foreach ($sql_batch_id as $row)
	{
		$booking_without_order=$row[csf("booking_without_order")];
	}

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id from  inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=19";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	if ($booking_without_order==0)
	{
		$sql_dtls="select b.id, b.batch_lot, b.prod_id, b.issue_qnty, b.store_id, b.no_of_roll, b.order_id,b.cutting_unit,b.remarks,b.batch_id,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,b.original_gsm,b.original_width from  inv_wvn_finish_fab_iss_dtls b, inv_transaction c where b.trans_id=c.id and b.mst_id='$data[1]' and b.status_active=1 and b.is_deleted=0";
	}
	else
	{
		$sql_dtls="select b.id, b.batch_lot, e.prod_id, b.issue_qnty, b.store_id, b.no_of_roll, b.order_id,b.cutting_unit,b.remarks,b.batch_id,d.booking_no_id,d.booking_without_order,c.fabric_ref,c.rd_no,c.weight_type,c.cutable_width,b.original_gsm,b.original_width from inv_wvn_finish_fab_iss_dtls b,inv_transaction c,pro_batch_create_mst d,pro_batch_create_dtls e where b.trans_id=c.id and c.batch_id=d.id and b.batch_id=d.id and d.id=e.mst_id and b.mst_id='$data[1]' and b.status_active=1 and b.is_deleted=0";

		$sql_result_batch= sql_select($sql_dtls);
		foreach ($sql_result_batch as $row)
		{
			$batchId=$row[csf("batch_id")];
		}
		$checkNonOrder=sql_select("select id,booking_without_order,booking_no_id from pro_batch_create_mst where id=$batchId and status_active=1 and is_deleted=0");
		foreach ($checkNonOrder as $row)
		{
			$nonOrderbooking_array[]=$row[csf('booking_no_id')];
		}
	}





	$sql_result= sql_select($sql_dtls);
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	$cutting_unit_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 order by floor_name","id","floor_name");

	?>
	<div style="width:1330px;">
    	<table width="1300" cellspacing="0" align="left">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							 <? echo $result[csf('plot_no')]; ?>
							 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


						}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large; margin-bottom: 10px;"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
	        </tr>

	    	<br>

	        <tr>
	        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
	            <td width="130"><strong>Issue Purpose:</strong></td> <td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
	            <td width="125"><strong>Sample Type:</strong></td><td width="175px"><? echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
	            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	            <td><strong>Sewing Source:</strong></td> <td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Sewing Com:</strong></td><td width="175px"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]]; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];  ?></td>
	           <?php /*?> <td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td><?php */?>
	            <td><strong>Cutting Unit No.</strong></td>
	            <td width="175px">
	            	<?
	            		foreach($sql_result as $cutt_row){
	            			$cutting_units .= $cutting_unit_arr[$cutt_row[csf("cutting_unit")]].",";
	            		}
	            		$cutting_units = chop($cutting_units,",");
	            		echo $cutting_units;
	            	?>
	            </td>
	        </tr>
    	</table>
        <br>
    	<div style="width:100%;">

    	<?
    	if ($booking_without_order==0)
    	{

    		?>


		    <table align="left" cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
		            <th width="100" >Buyer</th>
		            <th width="100" >Order Numbers</th>
		            <th width="80" >Batch/Lot</th>
		            <th width="180" >Fabric Description</th>

		            <th width="100" >Fabric Ref</th>
		            <th width="100" >RD NO</th>
		            <th width="100" >Weight</th>
		            <th width="100" >Weight Type</th>
		            <th width="100" >Width</th>
		            <th width="100" >Cutable Width</th>
		            <th width="50" >Color</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >Roll Qty</th>
		            <th width="110" >Store</th>
		            <th width="50" >UOM</th>
		            <th width="100" >Remarks</th>
		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{

							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';

						}



						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');

						$sql_uom = sql_select("select id,unit_of_measure  from product_details_master where id in ($prodID)");
						foreach ($sql_uom as $datas) {
							$uom_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];

						}

						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";

							if($row[csf('order_id')]!="")
							{
								if($db_type==0)
								{
									$order_nos=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in(".$row[csf('order_id')].")","po_no");
									$job_nos=return_field_value("group_concat(job_no_mst) as job_no_mst","wo_po_break_down","id in(".$row[csf('order_id')].")","job_no_mst");
									$style_ref_nos=return_field_value("group_concat(b.style_ref_no) as style_ref_no","wo_po_break_down a,wo_po_details_master b ","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","style_ref_no");
									$buyer_names=return_field_value("group_concat(b.buyer_name) as buyer_name","wo_po_break_down a,wo_po_details_master b ","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","buyer_name");
								}
								else
								{
									$order_nos=return_field_value("listagg((CAST(po_number as varchar2(4000))),',') within group (order by po_number) as po_number","wo_po_break_down","id in(".$row[csf('order_id')].")","po_number");
									$job_nos=return_field_value("listagg((CAST(job_no_mst as varchar2(4000))),',') within group (order by job_no_mst) as job_no_mst","wo_po_break_down","id in(".$row[csf('order_id')].")","job_no_mst");
									$style_ref_nos=return_field_value("listagg((CAST(b.style_ref_no as varchar2(4000))),',') within group (order by b.style_ref_no) as style_ref_no","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","style_ref_no");
									$buyer_names=return_field_value("listagg((CAST(b.buyer_name as varchar2(4000))),',') within group (order by b.buyer_name) as buyer_name","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","buyer_name");
								}

								$buyer_name=explode(",",$buyer_names);
								$buyers="";
								foreach(array_unique($buyer_name) as $rows)
								{
									$buyers.= $buyer_library[$rows].',';
								}
								$style_ref_no=explode(",",$style_ref_nos);
								$styleRef="";
								foreach(array_unique($style_ref_no) as $rows)
								{
									$styleRef.= $rows.',';
								}

								$job_no=explode(",",$job_nos);
								$jobNo="";
								foreach(array_unique($job_no) as $rows)
								{
									$jobNo.= $rows.',';
								}
							}
							else
							{

								// if ($row[csf('booking_without_order')]==1)
								// {
								// 	//for without order
								// 	$booking_ids = implode(",",$nonOrderbooking_array);
								// 	if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
								// 	$booking_datas=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
								// 	foreach($booking_datas as $rows){
								// 		$po_number_details[$rows[csf('id')]]['buyer_name'] = $rows[csf('buyer_id')];
								// 		//$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
								// 	}
								// }
								// $order_nos='';
							}

							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo chop($buyers,","); ?></p></td>
					                <td style="word-break:break-all;"><? echo $order_nos; ?></td>
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td><? echo $product_arr[$row[csf("prod_id")]]; ?></td>
					                <td><p style="word-break:break-all;"><? echo $row[csf("fabric_ref")]; ?></p></td>
					                <td><p style="word-break:break-all;"><? echo $row[csf("rd_no")]; ?></p></td>
					                <td><p style="word-break:break-all;"><? echo $row[csf("original_gsm")]; ?></p></td>
					                <td><p style="word-break:break-all;"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("original_width")]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("cutable_width")]; ?></p></td>
					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <?php /*?><td align="center"><? echo $row[csf("no_of_roll")]; ?></td><?php */?>
					                <td><? echo $store_library[$row[csf("store_id")]]; ?></td>
					               	<td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="12" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($totalQnty,4); ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="2"><?php // echo $totalAmount; ?></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		else
		{
			?>
			<table align="left" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
		            <th width="100" >Buyer</th>
		            <!-- <th width="100" >Style Ref</th>
		            <th width="100" >Job Number</th>
		            <th width="100" >Order Numbers</th> -->
		            <th width="80" >Batch/Lot</th>
		            <th width="180" >Fabric Description</th>

		            <th width="100" >Fabric Ref</th>
		            <th width="100" >RD NO</th>
		            <th width="100" >Weight</th>
		            <th width="100" >Weight Type</th>
		            <th width="100" >Width</th>
		            <th width="100" >Cutable Width</th>
		            <th width="50" >Color Name</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >Roll Qty</th>
		            <!--<th width="40" >No Of Roll</th>-->
		            <th width="110" >Store</th>
		            <th width="50" >UOM</th>
		            <th width="100" >Remarks</th>

		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{

							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';

						}



						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');

						$sql_uom = sql_select("select id,unit_of_measure  from product_details_master where id in ($prodID)");
						foreach ($sql_uom as $datas) {
							$uom_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];

						}

						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							//for without order
							$booking_ids = implode(",",$nonOrderbooking_array);
							if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
							$booking_datas=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
							foreach($booking_datas as $rows){
								$po_number_details[$rows[csf('id')]]['buyer_name'] = $rows[csf('buyer_id')];
								//$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
							}
							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo $buyer_arr[$po_number_details[$row[csf('booking_no_id')]]['buyer_name']]; ?></p></td>
					                <!-- <td><p style="word-break:break-all;"><? //echo chop($styleRef,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? //echo chop($jobNo,","); ?></p></td>
					                <td><? //echo $order_nos; ?></td> -->
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td><? echo $product_arr[$row[csf("prod_id")]]; ?></td>

					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("fabric_ref")]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("rd_no")]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("original_gsm")]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("original_width")]; ?></p></td>
					                <td><p style="word-break:break-all; text-align: center;"><? echo $row[csf("cutable_width")]; ?></p></td>

					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <?php /*?><td align="center"><? echo $row[csf("no_of_roll")]; ?></td><?php */?>
					                <td><? echo $store_library[$row[csf("store_id")]]; ?></td>
					                <td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="5" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($totalQnty,4); ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="2"><?php // echo $totalAmount; ?></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		?>
        <br>
		 <?
            echo signature_table(22, $data[0], "900px");
         ?>
      </div>
   </div>
	<?
	exit();
}

if ($action=="woven_finish_fabric_issue_print_3")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$sql_batch_id=sql_select("select b.batch_id,c.booking_without_order from inv_issue_master a,inv_wvn_finish_fab_iss_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_lot=c.batch_no and a.id='$data[1]' and a.company_id=c.company_id  and a.company_id='$data[0]' and a.entry_form=19 and a.status_active=1 and a.is_deleted=0 group by b.batch_id,c.booking_without_order");

	foreach ($sql_batch_id as $row)
	{
		$booking_without_order=$row[csf("booking_without_order")];
	}

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id,location_sewing, req_no from  inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=19";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	if ($booking_without_order==0)
	{
		$sql_dtls="SELECT id, mst_id, batch_lot, prod_id, issue_qnty, store_id, no_of_roll, order_id,cutting_unit,remarks,batch_id from  inv_wvn_finish_fab_iss_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	}
	else
	{
		$sql_dtls="SELECT a.id, a.mst_id, a.batch_lot, c.prod_id, a.issue_qnty, a.store_id, a.no_of_roll, a.order_id,a.cutting_unit,a.remarks,a.batch_id,b.booking_no_id,b.booking_without_order from inv_wvn_finish_fab_iss_dtls a,pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 ";


		$sql_result_batch= sql_select($sql_dtls);
		foreach ($sql_result_batch as $row)
		{
			$batchId=$row[csf("batch_id")];
		}
		$checkNonOrder=sql_select("select id,booking_without_order,booking_no_id from pro_batch_create_mst where id=$batchId and status_active=1 and is_deleted=0");
		foreach ($checkNonOrder as $row)
		{
			$nonOrderbooking_array[]=$row[csf('booking_no_id')];
		}
	}
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	$trans_sql="select id, mst_id, prod_id, body_part_id, floor_id, room, rack, self, bin_box from inv_transaction where mst_id='$data[1]' and company_id='$data[0]' and item_category = 3 and transaction_type = 2 ";
	//echo $trans_sql;
	$trans_sql_result= sql_select($trans_sql);
	$trans_info_array = array();
	foreach ($trans_sql_result as $row)
	{
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['body_part_id']=$row[csf('body_part_id')];
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
		$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
	}

	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	$cutting_unit_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 order by floor_name","id","floor_name");
	$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=".$data[0]."", "floor_room_rack_id", "floor_room_rack_name");

	?>
	<div style="width:1920px;">
    <table width="1300" cellspacing="0" align="left">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large; margin-bottom: 10px;"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>

    <br>
		<tr>
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="130"><strong>Buyer:</strong></td>
			<td width="175px">
				<?
				$buyers="";
				foreach($sql_result as $buyer_row)
				{
					if($buyer_row[csf('order_id')]!="")
					{
						$buyer_names=return_field_value("listagg((CAST(b.buyer_name as varchar2(4000))),',') within group (order by b.buyer_name) as buyer_name","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$buyer_row[csf('order_id')].")","buyer_name");

						$buyer_name=explode(",",$buyer_names);

						foreach(array_unique($buyer_name) as $rows)
						{
							$buyers.= $buyer_library[$rows].',';
						}
					}
				}
				echo chop($buyers,","); ?>
			</td>
            <td width="125"><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
		<tr>
			<td><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			<td><strong>Service Source:</strong></td> <td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            <td width="125"><strong>Cutt. Req. No::</strong></td><td width="175px"><? echo $dataArray[0][csf('req_no')]; ?></td>
        </tr>
		<tr>
			<td><strong>Service Company:</strong></td><td width="175px"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]]; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];  ?></td>
			<td width="130"><strong>Issue Purpose:</strong></td> <td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            <td><strong>Cutting Unit No.</strong></td>
            <td width="175px">
            	<?
            		foreach($sql_result as $cutt_row)
					{
            			$cutting_units .= $cutting_unit_arr[$cutt_row[csf("cutting_unit")]].",";
            		}
            		$cutting_units = chop($cutting_units,",");
            		echo $cutting_units;
            	?>
            </td>
        </tr>

    </table>
        <br>
    <div style="width:100%;">

    	<?
    	if ($booking_without_order==0)
    	{

    		?>
			<style>
					.wrd_brk{word-break: break-all;word-wrap: break-word;}
			</style>

		    <table align="right" cellspacing="0" width="1940"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
		            <th width="100" >Style Ref</th>
		            <th width="100" >Job No</th>
		            <th width="100" >Booking No</th>
		            <th width="100" >Internal Ref.</th>
		            <th width="100" >Body Part</th>
		            <th width="180" >Fabric Description</th>
		            <th width="50" >GSM</th>
		            <th width="50" >Dia</th>
		            <th width="80" >Batch/Lot</th>
		            <th width="50" >Fabric Color</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >No Of Roll</th>
		            <th width="50" >UOM</th>
		            <th width="100" >Floor</th>
		            <th width="100" >Room</th>
		            <th width="100" >Rack</th>
		            <th width="100" >Shelf</th>
		            <th width="100" >Bin/Box</th>
		            <th width="100" >Remarks</th>
		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{

							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';

						}

						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');


						$sql_product_info = sql_select("select id,unit_of_measure, weight, dia_width, product_name_details from product_details_master where id in ($prodID)");
						$product_info_arr = array();
						foreach ($sql_product_info as $datas)
						{
							$product_info_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
							$product_info_arr[$datas[csf('id')]]['gsm']=$datas[csf('weight')];
							$product_info_arr[$datas[csf('id')]]['dia']=$datas[csf('dia_width')];
							$product_info_arr[$datas[csf('id')]]['product_name_details']=$datas[csf('product_name_details')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];

						}

						$i=1;
						foreach($sql_result as $row)
						{

							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";

							if($row[csf('order_id')]!="")
							{
								$int_ref_nos=return_field_value("listagg((CAST(grouping as varchar2(4000))),',') within group (order by grouping) as grouping","wo_po_break_down","id in(".$row[csf('order_id')].")","grouping");
								$job_nos=return_field_value("listagg((CAST(job_no_mst as varchar2(4000))),',') within group (order by job_no_mst) as job_no_mst","wo_po_break_down","id in(".$row[csf('order_id')].")","job_no_mst");
								$style_ref_nos=return_field_value("listagg((CAST(b.style_ref_no as varchar2(4000))),',') within group (order by b.style_ref_no) as style_ref_no","wo_po_break_down a,wo_po_details_master b","a.job_no_mst=b.job_no and a.id in(".$row[csf('order_id')].")","style_ref_no");
								$booking_nos=return_field_value("listagg((CAST(booking_no as varchar2(4000))),',') within group (order by booking_no) as booking_no","wo_booking_dtls","po_break_down_id in(".$row[csf('order_id')].")","booking_no");

								$buyer_name=explode(",",$buyer_names);
								$buyers="";
								foreach(array_unique($buyer_name) as $rows)
								{
									$buyers.= $buyer_library[$rows].',';
								}
								$style_ref_no=explode(",",$style_ref_nos);
								$styleRef="";
								foreach(array_unique($style_ref_no) as $rows)
								{
									$styleRef.= $rows.',';
								}
								$booking_no=explode(",",$booking_nos);
								$booking_num="";
								foreach(array_unique($booking_no) as $rows)
								{
									$booking_num.= $rows.',';
								}

								$job_no=explode(",",$job_nos);
								$jobNo="";
								foreach(array_unique($job_no) as $rows)
								{
									$jobNo.= $rows.',';
								}
							}


							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center" class="wrd_brk"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo chop($styleRef,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? echo chop($jobNo,","); ?></p></td>
					                <td style="word-break:break-all;"><? echo chop($booking_num,","); ?></td>
					                <td style="word-break:break-all;"><? echo $int_ref_nos; ?></td>
					                <td style="word-break:break-all;"><? echo $body_part[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['body_part_id']]; ?></td>
					                <td><? echo $product_info_arr[$row[csf('prod_id')]]['product_name_details']; ?></td>
					                <td><? echo $product_info_arr[$row[csf('prod_id')]]['gsm']; ?></td>
					                <td><? echo $product_info_arr[$row[csf('prod_id')]]['dia']; ?></td>
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <td align="center"><? echo $unit_of_measurement[$product_info_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['floor_id']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['room']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['rack']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['self']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['bin_box']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="11" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo $totalQnty; ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="8"><?php // echo $totalAmount; ?></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		else
		{
			?>
			<table align="left" cellspacing="0" width="1640"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		         	<th width="30">SL</th>
					<th width="100" >Body Part</th>
		            <th width="180" >Fabric Description</th>
					<th width="50" >GSM</th>
		            <th width="50" >Dia</th>
		            <th width="80" >Batch/Lot</th>
		            <th width="50" >Fabric Color</th>
		            <th width="40" >Issue Qty</th>
		            <th width="40" >No Of Roll</th>
		            <th width="50" >UOM</th>
					<th width="100" >Floor</th>
		            <th width="100" >Room</th>
		            <th width="100" >Rack</th>
		            <th width="100" >Shelf</th>
		            <th width="100" >Bin/Box</th>
		            <th width="100" >Remarks</th>
		        </thead>
		        <tbody>
						<?
						$batchIDs="";$prodIDs="";
						foreach($sql_result as $datas)
						{
							$batchIDs.= $datas[csf('batch_id')].',';
							$prodIDs.= $datas[csf('prod_id')].',';
						}

						$batchID=chop($batchIDs,',');
						$prodID=chop($prodIDs,',');

						$sql_product_info = sql_select("select id,unit_of_measure, weight, dia_width, product_name_details from product_details_master where id in ($prodID)");
						$product_info_arr = array();
						foreach ($sql_product_info as $datas)
						{
							$product_info_arr[$datas[csf('id')]]['uom']=$datas[csf('unit_of_measure')];
							$product_info_arr[$datas[csf('id')]]['gsm']=$datas[csf('weight')];
							$product_info_arr[$datas[csf('id')]]['dia']=$datas[csf('dia_width')];
							$product_info_arr[$datas[csf('id')]]['product_name_details']=$datas[csf('product_name_details')];
						}

						$sql_batch_qry=sql_select("select a.id,a.color_id
						from pro_batch_create_mst a
						where a.id in($batchID)  and a.entry_form=17 and a.status_active=1 and a.is_deleted=0");
						$batch_arr=array();
						foreach($sql_batch_qry as $batchData)
						{
							$batch_arr[$batchData[csf('id')]]['color_id']=$batchData[csf('color_id')];
						}

						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							//for without order
							$booking_ids = implode(",",$nonOrderbooking_array);
							if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
							$booking_datas=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
							foreach($booking_datas as $rows){
								$po_number_details[$rows[csf('id')]]['buyer_name'] = $rows[csf('buyer_id')];
								//$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
							}
							$totalQnty +=$row[csf("issue_qnty")];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
					                <td align="center"><? echo $i; ?></td>
									<td style="word-break:break-all;"><? echo $body_part[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['body_part_id']]; ?></td>
					                <td><? echo $product_info_arr[$row[csf('prod_id')]]['product_name_details']; ?></td>
									<td><? echo $product_info_arr[$row[csf('prod_id')]]['gsm']; ?></td>
					                <td><? echo $product_info_arr[$row[csf('prod_id')]]['dia']; ?></td>
					                <td><? echo $row[csf("batch_lot")]; ?></td>
					                <td align="center"><? echo $color_name_arr[$batch_arr[$row[csf('batch_id')]]['color_id']]; ?></td>
					                <td align="right"><? echo $row[csf("issue_qnty")]; ?></td>
					                <td align="right"><? echo $row[csf("no_of_roll")]; $totalRollQty+=$row[csf("no_of_roll")]; ?></td>
					                <td align="center"><? echo $unit_of_measurement[$product_info_arr[$row[csf('prod_id')]]['uom']]; ?></td>
									<td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['floor_id']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['room']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['rack']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['self']]; ?></td>
					                <td><? echo $floorRoomRackShelf_array[$trans_info_array[$row[csf('mst_id')]][$row[csf('prod_id')]]['bin_box']]; ?></td>
					                <td><? echo $row[csf("remarks")]; ?></td>
								</tr>
						<? $i++;
					    } ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="7" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo $totalQnty; ?></td>
		                 <td align="right"><?php echo $totalRollQty; ?></td>
		                <td align="right" colspan="8"></td>
		            </tr>
		        </tfoot>
		    </table>
			<?
		}
		?>
        <br>
		 <?
            echo signature_table(22, $data[0], "900px");
         ?>
      </div>
   </div>
	<?
	exit();
}




if( $action == "populate_data_uom"){
    $sql = sql_select("select unit_of_measure  from product_details_master where id = $data");
    $unit_of_measure= $sql[0][csf('unit_of_measure')];
    echo "$('#txt_uom').val('".$unit_of_measurement[$unit_of_measure]."');\n";
    echo "$('#hidden_uom').val('".$unit_of_measure."');\n";
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
		<script>

			function js_set_value(data)
			{
				var data = data.split('**')
				$('#hidden_reqn_id').val(data[0]);
				$('#hidden_reqn_no').val(data[1]);
				$('#hidden_reqn_po_id').val(data[2]);
				parent.emailwindow.hide();
			}

	    </script>

	</head>

	<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:750px; margin-left:2px">
	            <table cellpadding="0" cellspacing="0" width="740" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Buyer</th>
	                	<th>Job NO</th>
	                    <th>Requisition Date Range</th>
	                    <th id="search_by_td_up">Requisition No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">
	                    	<input type="hidden" name="hidden_reqn_no" id="hidden_reqn_no">
	                    	<input type="hidden" name="hidden_reqn_po_id" id="hidden_reqn_po_id">
	                    	<!-- <input type="hidden" name="hidden_prod_details" id="hidden_prod_details">
	                    	<input type="hidden" name="hidden_job" id="hidden_job"> -->
	                    </th>
	                </thead>
	                <tr class="general">
	                	<th><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></th>
	                	<th><input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" /></th>
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+<? echo $hdn_variable_setting_status; ?>, 'create_reqn_search_list_view', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
	                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$('#cbo_location_id').val(0);
	</script>
	</html>
	<?
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$company_id =$data[3];
	$buyer_id =$data[4];
	$job_no =$data[5];
	$hdn_variable_setting_status =$data[6];

	$lay_plan_arr=return_library_array( "select id, cutting_no from ppl_cut_lay_mst",'id','cutting_no');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and a.reqn_number like '$search_string'";
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";
	$job_cond = "";
	if($job_no != ''){
		$job_cond = "and c.job_no_prefix_num like '%$job_no%'";
	}
	$buyer_cond = "";
	if($buyer_id != 0){
		$buyer_cond = "and b.buyer_id = $buyer_id";
	}

	//echo "SELECT a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id join wo_po_details_master c on c.job_no=b.job_no $job_cond where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $buyer_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no"; die;

	if($hdn_variable_setting_status==1)
	{

		$requisition_data = sql_select("SELECT a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, LISTAGG(b.po_id,',') WITHIN GROUP (ORDER BY b.po_id) as po_id, c.job_no_prefix_num,c.style_ref_no ,sum(b.reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id join wo_po_details_master c on c.job_no=b.job_no $job_cond where a.status_active=1 and a.is_deleted=0 and a.entry_form=507 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $buyer_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, c.job_no_prefix_num,c.style_ref_no order by a.id desc");

	}
	else
	{
		$requisition_data = sql_select("SELECT a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, b.po_id, c.job_no_prefix_num,c.style_ref_no ,sum(b.reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id join wo_po_details_master c on c.job_no=b.job_no $job_cond where a.status_active=1 and a.is_deleted=0 and a.entry_form=507 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $buyer_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, c.job_no_prefix_num,c.style_ref_no,b.po_id order by a.id desc");
	}
	$requisitionIds="";
	foreach($requisition_data as $row)
	{
		$requisitionIds.=$row[csf('id')].',';
	}
	$requisitionIds=chop($requisitionIds,',');
	//$wovn_issueQnty=sql_select("select a.req_id,sum(b.issue_qnty) as issue_qnty from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b where a.id=b.mst_id and a.entry_form=19 and a.item_category=3 and issue_basis=2 and req_id in($requisitionIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.req_id");
	//, sum(b.issue_qnty)
	$wovn_issueQnty=sql_select("select a.req_id,b.issue_qnty as issue_qnty,d.job_no_mst from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b,order_wise_pro_details c ,wo_po_break_down d where a.id=b.mst_id and  b.trans_id=c.trans_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=19 and a.item_category=3 and issue_basis=2 and req_id in($requisitionIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.req_id,d.job_no_mst,b.issue_qnty");
	$issueQntyArr=array();
	foreach($wovn_issueQnty as $rows)
	{
		$issueQntyArr[$rows[csf('req_id')]][$rows[csf('job_no_mst')]]['issue_qnty']+=$rows[csf('issue_qnty')];
	}




	?>
	<div align="center">
		<table  class="rpt_table" width="640" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
			<thead>
				<tr>
					<th width="20">SI</th>
					<th width="50">Req. NO</th>
					<th width="50">Year</th>
					<th width="100">Requisition Date</th>
					<th width="100">Job NO</th>
					<th width="100">Style Ref. No</th>
					<th width="100">PO NO</th>
					<th width="100">Buyer</th>
					<th width="100">Balance</th>
				</tr>
			</thead>
		</table>
		<table id="tbl_list_search" class="rpt_table" width="640" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
	    	<tbody>
	    		<?
					$i=1;
					foreach($requisition_data as $row)
					{

						$balance=$row[csf('reqn_qty')]-$issueQntyArr[$row[csf('id')]][$row[csf('job_no')]]['issue_qnty'];
						if($hdn_variable_setting_status==1)
						{
							$poNos="";$poIds="";
							$poNumber=array_unique(explode(",",$row[csf('po_id')]));
							foreach ($poNumber as $poId) {
								$poNos.=$po_arr[$poId].",";
								$poIds.=$poId.",";
							}
							$poNos=chop($poNos,",");
							$poIds=chop($poIds,",");
						}
						else
						{
							$poNos=$po_arr[$row[csf('po_id')]];
							$poIds=$row[csf('po_id')];
						}
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if ($balance>0)
						{

							?>
							<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')].'**'.$row[csf('reqn_number')].'**'.$poIds; ?>')">
								<td width="20"><? echo $i; ?></td>
								<td width="50" align="left"><? echo $row[csf('reqn_number_prefix_num')]; ?></td>
								<td width="50" align="left"><? echo $row[csf('year')]; ?></td>
								<td width="100" align="left"><? echo $row[csf('reqn_date')]; ?></td>
								<td width="100" align="left"><? echo $row[csf('job_no_prefix_num')]; ?></td>
								<td width="100" align="left"><? echo $row[csf('style_ref_no')]; ?></td>
								<td width="100" align="left"><? echo $poNos; ?></td>
								<td width="100" align="left"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
								<td width="100" align="right"><? echo number_format($balance,2); ?></td>

							</tr>

							<?
							$i++;
						}
					}
				?>
	    	</tbody>
	    </table>
	</div>
	<?
	exit();
}

if( $action == 'populate_list_view' )
{
	$data=explode("_", $data);

 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sizeArr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//$fabric_type_arr=return_library_array( "select id, fabric_type from lib_woben_fabric_type",'id','fabric_type');
	$composition_arr=array(); $constructtion_arr=array();
	//$txt_fabric_type =3;
	$buyer_supplied = '';
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	if($db_type==2)
	{
		 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
		 $null_cond="NVL";
	}
    else if($db_type==0)
	{
		$year=" year(a.insert_date) as year ";
		$null_cond="IFNULL";
	}

	$all_po_id='';
	//$result= array();

	if($data[2]==1)
	{

		$cutting_dtls=sql_select("SELECT a.id as mst_id,a.company_id,a.reqn_number, b.buyer_id,LISTAGG(b.po_id,',') WITHIN GROUP (ORDER BY b.po_id) as po_id, b.job_no, b.item_id, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id, sum(b.reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id  where b.status_active=1 and b.is_deleted=0 and b.mst_id=$data[0] and b.po_id in($data[1]) and a.entry_form=507 group by a.id,a.company_id,a.reqn_number, b.buyer_id, b.job_no, b.item_id, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id");
	}
	else
	{
		$cutting_dtls=sql_select("SELECT a.id as mst_id,a.company_id,a.reqn_number, b.id, b.buyer_id, b.po_id, b.job_no, b.item_id, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id, b.size_id, b.reqn_qty from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id  where b.status_active=1 and b.is_deleted=0 and a.entry_form=507 and b.mst_id=$data[0] ");
		//and b.po_id in($data[1])
	}

	$k =1;
	foreach ($cutting_dtls as $row)
	{
		$key = $row[csf('job_no')].$row[csf('determination_id')].'**'.$row[csf('gsm')].'**'.$row[csf('dia')].'**'.$row[csf('color_id')].'**'.$row[csf('body_part')];
		$txt_fabric_description = $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]];
		//$product_name_details=$fabric_type_arr[$txt_fabric_type].",  ".trim(str_replace("'","",$txt_fabric_description)).", ".$row[csf('gsm')].", ".$row[csf('dia')].", ".$color_arr[$row[csf('color_id')]]. ", ".$buyer_supplied;
		$product_name_details=trim(str_replace("'","",$txt_fabric_description)).", ".$row[csf('gsm')].", ".$row[csf('dia')].", ".$color_arr[$row[csf('color_id')]]. ", ".$buyer_supplied;
		$cutting_dtls_arr[$key]['job_no'] = $row[csf('job_no')];
		$cutting_dtls_arr[$key]['product_fabric'] = $product_name_details;
		$cutting_dtls_arr[$key]['color_id'] = $row[csf('color_id')];
		$cutting_dtls_arr[$key]['reqn_qty'] += $row[csf('reqn_qty')];
		$cutting_dtls_arr[$key]['po_id'][$row[csf('po_id')]] = $row[csf('po_id')];
		$cutting_dtls_arr[$key]['company_id']= $row[csf('company_id')];
		$cutting_dtls_arr[$key]['determination_id']= $row[csf('determination_id')];
		$cutting_dtls_arr[$key]['body_part']= $row[csf('body_part')];
		$cutting_dtls_arr[$key]['gsm']= $row[csf('gsm')];
		$cutting_dtls_arr[$key]['dia']= $row[csf('dia')];
		$cutting_dtls_arr[$key]['po_id']= $row[csf('po_id')];
		$cutting_dtls_arr[$key]['mst_id']= $row[csf('mst_id')];

	}
	/*echo '<pre>';
	print_r($cutting_dtls_arr); die;*/
	/*$all_po_id=substr($all_po_id,0,-1);

	$budget_qty_array=array(); $po_data_array=array();$avg_cons_data=array();$uom_data=array();
	$sql_budget= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty, e.uom, e.avg_cons from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, e.uom, e.avg_cons");
	foreach ($sql_budget as $row)
	{
		$budget_qty_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('budget_qty')];
		$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
		$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];

		$po_data_array[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
		$po_data_array[$row[csf('po_id')]]['prefix']=$row[csf('job_no_prefix_num')];
		$po_data_array[$row[csf('po_id')]]['year']=$row[csf('year')];
	}*/
	$i=1;
	?>

	    <table class="rpt_table" id="requisition_dtls" width="550" cellspacing="0" cellpadding="0" border="0" rules="all">
	    	<thead>
				<tr>
					<th width="40">SL</th>
                    <th width="100">Job No</th>
                    <th width="100">Body Part</th>
                    <th width="150">Const/Composition</th>
                    <th width="80">Gmts. Color</th>
                    <th width="60">Reqn. Qty</th>
				</tr>
			</thead>
	<?
	foreach($cutting_dtls_arr as $row)
	{
		if($data[2]==1)
		{
			$poIDSs= array_unique(explode(",", $row['po_id']));
			$poIDS="";
			foreach ($poIDSs as $poID) {
				$poIDS.=$poID.",";
			}
			$poIDS=chop($poIDS,",");
		}
		else
		{
			$poIDS=$row['po_id'];
		}
		if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
		$js_set_data = $row['job_no'].'**'.$row['product_fabric'].'**'.$row['company_id'].'**'.$row['body_part'].'**'.$row['color_id'].'**'.$row['determination_id'].'**'.$row['gsm'].'**'.$row['dia'].'**'.$poIDS.'**'.$row['mst_id'];
		?>
		<tr style="cursor: pointer;" bgcolor="<? echo $bgcolor; ?>" id="req_tr_<? echo $i; ?>" onClick="requisition_set_data('<? echo $js_set_data ?>');change_color('<? echo $i; ?>','#E9F3FF')">
            <td width="40"><? echo $i; ?></td>
            <td width="100" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
            <td width="100" style="word-break:break-all;"><? echo $body_part[$row['body_part']]; ?></td>
            <td width="150" style="word-break:break-all;"><? echo $row['product_fabric']; ?></td>
            <td width="80" style="word-break:break-all;"><? echo $color_arr[$row['color_id']]; ?></td>
            <td width="60"><? echo number_format($row['reqn_qty'],2,'.',''); ?></td>
        </tr>
		<?
		$i++;
	}
	?>
		</table>
	<?

	exit();
}
?>
