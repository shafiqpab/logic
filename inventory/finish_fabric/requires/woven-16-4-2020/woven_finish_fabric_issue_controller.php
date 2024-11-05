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

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fabric_issue_controller",$data);
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "0", "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');","" );
	}
	else if($data[0]==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select id, supplier_name from lib_supplier where find_in_set(21,party_type) and find_in_set($company_id,tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');" );
		}
		else
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_drop_down( 'requires/woven_finish_fabric_issue_controller', this.value, 'load_drop_down_cutting','cutting_unit_no');" );
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
		function js_set_value(batchLot_no,batch_id)
		{
			$('#hidden_batchLot_no').val(batchLot_no);
			$('#hidden_batch_id').val(batch_id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:700px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:670px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" rules="1" border="1" width="670" class="rpt_table">
                <thead>
                	<th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Enter Batch/Lot No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">

                        <input type="hidden" name="txt_store_id" id="txt_store_id" class="text_boxes" value="<? echo $cbo_store_name; ?>">

                        <input type="hidden" name="hidden_batchLot_no" id="hidden_batchLot_no" class="text_boxes" value="">
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td id="store_td">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, '',0 );
                        ?>
                    </td>
                    <td align="center">
						<?
							$search_by_arr=array(1=>"Batch/Lot No",2=>"Job No.",3=>"Style No.");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_store_id').value, 'create_batchlot_search_list_view', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$store_id =$data[4];

	if($buyer_id) $buyer_cond = " and c.buyer_name = '$buyer_id'";
	$search_field_cond="";
	if(trim($data[0])!="" && $search_by == 1)
	{
		$search_field_cond="and batch_lot like '$search_string'";
	}
	if(trim($data[0])!="" && $search_by == 2)
	{
		$job_cond="and c.job_no like '%$search_string'";
	}
	if(trim($data[0])!="" && $search_by == 3)
	{
		$style_cond="and c.style_ref_no like '%$search_string'";
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	if($buyer_cond !="" || $job_cond !="" || $style_cond !="")
	{

		$po_data = sql_select("SELECT a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no ,buyer_name
		  from wo_po_break_down a,pro_batch_create_dtls b,wo_po_details_master c
		  where  a.id=b.po_id and c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 $job_cond $style_cond $buyer_cond");
		$all_po_id ="";
		foreach ($po_data as $value)
		{
			if($all_po_id=="") $all_po_id=$value[csf('batch_id')]; else $all_po_id.=",".$value[csf('batch_id')];
			$batch_ref_arr[$value[csf('batch_id')]]["po_number"] .=  $value[csf("po_number")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["job_no_mst"] .= $value[csf("job_no_mst")].", ";
			$batch_ref_arr[$value[csf('batch_id')]]["style_ref_no"] .= $value[csf("style_ref_no")].", ";
		}
	 	$all_po_id = implode(",",array_unique(array_filter(explode(",", $all_po_id))));
		$all_po_id_count=count(explode(",",$all_po_id));  $po_id_cond="";
		if($db_type==2 && $all_po_id_count>400)
		{
			$po_id_cond=" and (";
			$allPoIdArr=array_chunk(explode(",",$all_po_id),399);
			foreach($allPoIdArr as $Po_id)
			{
				$Po_id=implode(",",$Po_id);
				$po_id_cond.=" b.id in($Po_id) or ";
			}
			$po_id_cond=chop($po_id_cond,'or ');
			$po_id_cond.=")";
		}
		else
		{
			$po_id_cond=" and b.id in ($all_po_id)";
		}
	}

	if($db_type==0)
	{
		$sql = " SELECT a.batch_lot,a.batch_id,b.color_id from inv_transaction a, pro_batch_create_mst b, pro_batch_create_dtls c 		where a.batch_id = b.id and b.id = c.mst_id	and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and a.batch_id <>'' $po_id_cond $search_field_cond group by a.batch_lot ,a.batch_id,b.color_id";
	}
	else
	{
		$sql = " SELECT a.batch_lot,a.batch_id,b.color_id from inv_transaction a, pro_batch_create_mst b, pro_batch_create_dtls c 		where a.batch_id = b.id and b.id = c.mst_id and a.company_id=$company_id and a.store_id=$store_id and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0  and a.batch_id is not null $po_id_cond $search_field_cond 		group by a.batch_lot ,a.batch_id,b.color_id";
	}
	//echo $sql;
	$nameArray=sql_select( $sql );

	if($buyer_cond =="" && $job_cond =="" && $style_cond =="")
	{
		foreach($nameArray as $selectResult)
		{
			$result_batch_arr[$selectResult[csf('batch_id')]] = $selectResult[csf('batch_id')];
		}

		$all_result_batch_arr = array_filter($result_batch_arr);
		if(count($all_result_batch_arr)>0)
		{
			$all_result_batch_nos = implode(",", $all_result_batch_arr);
			$all_result_batch_no_cond=""; $batchCond="";
			if($db_type==2 && count($all_result_batch_arr)>999)
			{
				$all_result_batch_arr_chunk=array_chunk($all_result_batch_arr,999) ;
				foreach($all_result_batch_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$batchCond.="  b.mst_id in($chunk_arr_value) or ";
				}

				$all_result_batch_no_cond.=" and (".chop($batchCond,'or ').")";
			}
			else
			{
				$all_result_batch_no_cond=" and b.mst_id in($all_result_batch_nos)";
			}
			$po_data_2 = sql_select("select a.id,b.mst_id as batch_id, a.po_number, a.job_no_mst,c.style_ref_no ,buyer_name
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
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch/Lot</th>
                <th width="120">Job No</th>
                <th width="100">Style No</th>
                <th width="100">PO No</th>
                <th>Color</th>
            </thead>
        </table>
        <div style="width:668px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;

				foreach($nameArray as $selectResult)
				{
					$po_nos = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["po_number"],", ")))));
					$style_ref = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["style_ref_no"],", ")))));
					$job_no = implode(", ",array_unique(array_filter(explode(", ", chop($batch_ref_arr[$selectResult[csf('batch_id')]]["job_no_mst"],", ")))));

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('batch_lot')]; ?>','<? echo $selectResult[csf('batch_id')];?>')">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $selectResult[csf('batch_lot')]; ?></td>
						<td width="120" style="word-break:break-word; word-wrap: break-word;"><p><? echo $job_no; ?></p></td>
						<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $style_ref; ?></p></td>
						<td width="100" style="word-break:break-word; word-wrap: break-word;"><p><? echo $po_nos; ?></p></td>
						<td><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
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

if($action=='show_fabric_desc_listview')
{
	$data= explode('_', $data);
	if($data[2]!="") $batch_id_cond="and c.id=$data[2]";
	$data_array=sql_select("SELECT a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order, b.batch_id from product_details_master a, inv_transaction b,pro_batch_create_mst c where a.id=b.prod_id and b.batch_id=c.id and c.company_id=$data[1] and b.batch_lot='$data[0]' $batch_id_cond and a.item_category_id=3 and b.item_category=3 and b.transaction_type=1 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,a.color,a.dia_width,a.weight,b.body_part_id, a.unit_of_measure,c.booking_without_order,b.batch_id");
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450">
        <thead>
            <th width="30">SL</th>
            <th width="70">Product ID</th>
            <th width="200">Fabric Description</th>
            <th width="85">Stock Qty</th>
            <th>UOM</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450" id="fabric_listview">
            <?
            $i=1;
            foreach($data_array as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('current_stock')]."**".$row[csf('body_part_id')]."**".$unit_of_measurement[$row[csf('unit_of_measure')]]."**".$row[csf('booking_without_order')]."**".$row[csf('batch_id')]."**".$row[csf('color')]."**".$row[csf('dia_width')]."**".$row[csf('weight')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                    <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                    <td align="right" width="85"><? echo number_format($row[csf('current_stock')],2,'.',''); ?></td>
                    <td align="center" ><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
                </tr>
            <?
            $i++;
            }
            ?>
    </table>
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

		function fnc_close()
		{
			var save_data=''; var tot_issue_qnty='';
			var po_id_array = new Array(); var buyer_id =''; var po_no='';

			$("#tbl_list_search").find('tr').each(function()
			{
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

			$('#save_data').val( save_data );
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
			<fieldset style="width:720px;margin-left:10px">
	        	<input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
	            <input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="">
	            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
	            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
	            <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
	            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	            <input type="hidden" id="issue_basis" value="<? echo $issue_basis; ?>">
	            <div style="width:700px; margin-top:10px; margin-bottom:10px" align="center">
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
	                                $distribiution_method=array(1=>"Proportionately",2=>"Manually");
	                                echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"",$prev_distribution_method, "distribute_qnty(this.value);",$is_disabled );
	                            ?>
	                        </td>
	                    </tr>
	                </table>
	            </div>
	            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700">
	                <thead>
	                	<th width="100">Job No</th>
	                    <th width="120">PO No</th>
	                    <th width="100">PO Qnty</th>
	                    <th width="100">Req. Qnty</th>
	                    <th width="100"><p>Cumn.Recv. Qty</p></th>
	                    <th width="100"><p>Cumn. Issue Qnty</p></th>
	                    <th>Issue Qnty</th>
	                </thead>
	            </table>
	            <div style="width:718px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" id="tbl_list_search">
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
							$requisition_data = sql_select("SELECT b.reqn_qty, b.po_id from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id where a.company_id =$cbo_company_id and b.body_part=$hidden_bodypart_id and b.color_id=$hidden_color_id and b.dia =$hidden_dia_width and b.gsm=$hidden_gsm_weight and b.status_active=1 and b.is_deleted=0 and b.mst_id= $requisition_id");
							foreach ($requisition_data as $row) {
								$requ_qty_arr[$row[csf('po_id')]] += $row[csf('reqn_qty')];
							}
						}


						$cumu_rec_qty=array(); $cumu_iss_qty=array();

						$sql_cuml="SELECT b.po_breakdown_id,
									sum(case when b.entry_form in(17) and b.trans_type=1 and a.transaction_type=1 and a.batch_lot='$txt_batch_lot' then b.quantity end) as finish_fabric_recv,
									sum(case when b.entry_form in(202) and b.trans_type=3 and a.transaction_type=3 and a.batch_lot='$txt_batch_lot' then b.quantity end) as finish_fabric_recv_rtn,
									sum(case when b.entry_form=19 and b.trans_type=2 and a.transaction_type=2 and a.batch_lot='$txt_batch_lot' then b.quantity end) as finish_fabric_issue,
									sum(case when b.entry_form=258 and b.trans_type=5 and a.transaction_type=5 and a.prod_id='$hidden_prod_id' then b.quantity end) as finish_fabric_transfer_in,
									sum(case when b.entry_form=258 and b.trans_type=6 and a.transaction_type=6 and a.prod_id='$hidden_prod_id' then b.quantity end) as finish_fabric_transfer_out

									from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.prod_id=$hidden_prod_id and b.prod_id=$hidden_prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id";


						$sql_result_cuml=sql_select($sql_cuml);
						foreach($sql_result_cuml as $row)
						{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv')];
							$cumu_rec_rtn_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_recv_rtn')];
							$cumu_iss_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_issue')];
							$cumu_transfer_in_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_in')];
							$cumu_transfer_out_qty[$row[csf('po_breakdown_id')]]=$row[csf('finish_fabric_transfer_out')];
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

	                    $sql="SELECT b.id,b.po_number, a.buyer_name,a.job_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from inv_transaction i, order_wise_pro_details o, wo_po_break_down b, wo_po_details_master a where i.id=o.trans_id and o.po_breakdown_id=b.id and a.job_no=b.job_no_mst and i.item_category=3 and i.transaction_type=1 and i.status_active=1 and i.is_deleted=0 and o.status_active=1 and o.is_deleted=0 and i.batch_lot='$txt_batch_lot' and i.prod_id=$hidden_prod_id and o.entry_form=17 group by b.id,b.po_number,a.buyer_name,a.job_no, a.total_set_qnty,b.po_quantity order by b.id,b.po_number";

	                    $nameArray=sql_select($sql);
	                    foreach($nameArray as $row)
	                    {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	                        $tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
							$iss_qty=$finish_qnty_array[$row[csf('id')]];
							$hideQnty=$hide_qty_array[$row[csf('id')]];

							$cumnRecvQty = ($cumu_rec_qty[$row[csf('id')]]+$cumu_transfer_in_qty[$row[csf('id')]])-($cumu_transfer_out_qty[$row[csf('id')]]+$cumu_rec_rtn_qty[$row[csf('id')]]);
							$cumuIissQty = $cumu_iss_qty[$row[csf('id')]];
							if ($issue_basis == 2) {
								$requ_qty = $requ_qty_arr[$row[csf('id')]];
							}


	                     	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                        	<td width="100" title="<? echo  $requ_qty; ?>">
	                        		<? echo $row[csf('job_no')]; ?>
	                        	</td>
	                            <td width="120">
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
	                                if($issue_basis == 1){
	                                	$cumul_balance=($cumnRecvQty-$cumuIissQty);
	                                }
	                                if($issue_basis == 2){
	                                	$cumul_balance=($requ_qty-$cumuIissQty);
	                                }

	                                ?>
	                            </td>
	                            <td align="center">
	                                <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width: 60px" value="<? echo $iss_qty; ?>" placeholder="<? echo $cumul_balance?>" onchange="qty_check(<? echo $i ?>,<? echo $issue_basis ?>)">
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
	            <table width="720">
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


if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];

	$sql=sql_select("select sum(case when entry_form=17 then quantity end) as finish_fabric_recv,sum(case when entry_form=202 then quantity end) as finish_fabric_recv_rtn, sum(case when entry_form=19 then quantity end) as finish_fabric_issue,sum(case when entry_form=258 and trans_type=5 then quantity end) as finish_fabric_transfer_in ,sum(case when entry_form=258 and trans_type=6 then quantity end) as finish_fabric_transfer_out from order_wise_pro_details where po_breakdown_id in($order_id) and prod_id=$prod_id and is_deleted=0 and status_active=1");

	$finish_fabric_recv=($sql[0][csf('finish_fabric_recv')]+$sql[0][csf('finish_fabric_transfer_in')])-($sql[0][csf('finish_fabric_transfer_out')]+$sql[0][csf('finish_fabric_recv_rtn')]);
	$finish_fabric_issued=$sql[0][csf('finish_fabric_issue')];
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
            <table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="240" id="search_by_td_up">Please Enter Issue No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="finish_fabric_issue_id" id="finish_fabric_issue_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_issue_search_list_view', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
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
	$company_id =$data[2];

	if($search_by==1)
		$search_field="issue_number";
	else
		$search_field="challan_no";

 	$sql="select id, issue_number, challan_no, company_id, issue_date, issue_purpose, buyer_id, sample_type, issue_basis from inv_issue_master where item_category=3 and company_id=$company_id and $search_field like '$search_string' and entry_form=19 and status_active=1 and is_deleted=0 order by id desc";

	$company_short_name_arr = return_library_array("select id, company_short_name from lib_company","id","company_short_name");
	$sample_type_arr = return_library_array("select id, sample_name from lib_sample","id","sample_name");
	$arr=array(2=>$woven_issue_basis,3=>$company_short_name_arr,5=>$yarn_issue_purpose,6=>$buyer_arr,7=>$sample_type_arr);
	echo  create_list_view("tbl_list_search", "Issue No,Challan No,Issue Basis,Company,Issue Date,Issue Purpose,Buyer, Sample Type", "120,90,80,80,80,110,100","795","250",0, $sql, "js_set_value", "id", "", 1, "0,0,issue_basis,company_id,0,issue_purpose,buyer_id,sample_type", $arr, "issue_number,challan_no,issue_basis,company_id,issue_date,issue_purpose,buyer_id,sample_type", '','','0,0,0,0,3,0,0,0');
	exit();
}

if($action=='populate_data_from_issue_master')
{
	$data_array=sql_select("select issue_number, challan_no, company_id, issue_date, issue_purpose, buyer_id, sample_type, knit_dye_source, knit_dye_company, issue_basis, buyer_job_no, req_id, req_no from inv_issue_master where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";

		echo "active_inactive(".$row[csf("issue_purpose")].",0);\n";

		echo "document.getElementById('cbo_sample_type').value 				= '".$row[csf("sample_type")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";

		echo "load_drop_down( 'requires/woven_finish_fabric_issue_controller', '".$row[csf('knit_dye_source')]."'+'_'+'".$row[csf('company_id')]."', 'load_drop_down_sewing_com','sewingcom_td');\n";

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

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_issue_entry',1,1);\n";
		exit();
	}
}

if($action=="show_finish_fabric_issue_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
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
                <th>Order Numbers</th>
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
					foreach($order_id as $po_id)
					{
						if($po_id>0) $order_nos.=$po_arr[$po_id].", ";
					}
					$order_nos=chop($order_nos,", ");
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>)">
                            <td width="40" align="center"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[csf('batch_lot')]; ?></p></td>
                            <td width="200"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100" align="right"><? echo $row[csf('issue_qnty')]; ?></td>
                            <td width="80" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
                            <td width="110"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                            <td style="word-break:break-word; word-wrap: break-word;"><p><? echo $order_nos; ?></p></td>
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

if($action=='populate_issue_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];

	$data_array=sql_select("select a.id, a.mst_id,a.trans_id,a.batch_lot,a.batch_id, a.prod_id, a.issue_qnty, a.store_id, a.no_of_roll, a.order_id,a.order_save_string,a.cutting_unit,a.remarks, a.roll_save_string,b.body_part_id,b.gmt_item_id,b.company_id,b.floor_id,b.room,rack,b.self,b.bin_box,c.current_stock,c.product_name_details,c.color,c.dia_width,c.weight from inv_wvn_finish_fab_iss_dtls a, inv_transaction b,product_details_master c where a.mst_id=b.mst_id and a.trans_id=b.id and b.prod_id=c.id and a.id='$id'");

	foreach ($data_array as $row)
	{
		$batchId=$row[csf("batch_id")];
	}

	$checkNonOrder=sql_select("select booking_without_order from pro_batch_create_mst where id=$batchId and status_active=1 and is_deleted=0");

	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller*3', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("self")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_issue_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";
		echo "document.getElementById('txt_batch_lot').value 				= '".$row[csf("batch_lot")]."';\n";

		//$prodData=sql_select("select c.current_stock, c.product_name_details from product_details_master where id='".$row[csf('prod_id')]."'");

		//$product_details=$prodData[0][csf("product_name_details")];
		//$current_stock=$prodData[0][csf("current_stock")];

		echo "document.getElementById('txt_fabric_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('save_string').value 					= '".$row[csf("roll_save_string")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$row[csf("order_save_string")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_global_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf('trans_id')]."';\n";
		echo "document.getElementById('cbo_item_name').value 				= '".$row[csf('gmt_item_id')]."';\n";
		echo "document.getElementById('hidden_bodypart_id').value 			= '".$row[csf('body_part_id')]."';\n";
		echo "document.getElementById('cbo_cutting_floor').value 			= '".$row[csf('cutting_unit')]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf('remarks')]."';\n";
		echo "document.getElementById('hidden_color_id').value 				= '".$row[csf('color')]."';\n";
		echo "document.getElementById('hidden_dia_width').value 			= '".$row[csf('dia_width')]."';\n";
		echo "document.getElementById('hidden_gsm_weight').value 			= '".$row[csf('weight')]."';\n";

		if($row[csf("order_id")]!="")
		{
			echo "get_php_form_data('".$row[csf('order_id')]."'+'**'+'".$row[csf('prod_id')]."', 'populate_data_about_order', 'requires/woven_finish_fabric_issue_controller' );\n";
		}
		echo "show_list_view('".$row[csf('batch_lot')].'_'.$row[csf('company_id')].'_'.$row[csf('batch_id')]."', 'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');\n";
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
		echo "$('#txt_issue_qnty').attr('placeholder','Single Click');\n";
	}
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $hidden_prod_id and transaction_type in (1,4,5) and store_id = $cbo_store_name and status_active = 1 ", "max_date");
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
			echo "17**0";
			die;
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
            $new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'WFFI',19,date("Y",time())));


			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, item_category, company_id, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id, issue_basis, buyer_job_no, req_id, req_no, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",19,3,".$cbo_company_id.",".$cbo_sample_type.",".$txt_issue_date.",".$txt_challan_no.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_buyer_name.",".$cbo_issue_basis.",".$hidden_job.",".$txt_requisition_id.",".$txt_requisition_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$finish_fabric_issue_num=$new_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="sample_type*issue_date*challan_no*knit_dye_source*knit_dye_company*buyer_id*updated_by*update_date";
			$data_array_update=$cbo_sample_type."*".$txt_issue_date."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_buyer_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$finish_fabric_issue_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}

		$avg_rate=$currentStock=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$product_id");
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_value = $result[csf("stock_value")];
		}
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id,floor_id,room,rack,self,bin_box, batch_lot,batch_id,body_part_id,gmt_item_id,inserted_by, insert_date";

		$cons_amnt=$avg_rate*str_replace("'","",$txt_issue_qnty);

		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_company_id.",".$product_id.",3,2,".$txt_issue_date.",0,".$txt_issue_qnty.",".$avg_rate.",".$cons_amnt.",".$txt_challan_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_batch_lot.",".$txt_batch_id.",".$hidden_bodypart_id.",".$cbo_item_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$id_dtls = return_next_id_by_sequence("INV_WV_FIN_FAB_ISS_DTLS_PK_SEQ", "inv_wvn_finish_fab_iss_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, prod_id, issue_qnty, store_id, batch_lot,batch_id, no_of_roll,cutting_unit,remarks, order_id, roll_save_string, order_save_string, inserted_by, insert_date";

		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$product_id.",".$txt_issue_qnty.",".$cbo_store_name.",".$txt_batch_lot.",".$txt_batch_id.",".$txt_no_of_roll.",".$cbo_cutting_floor.",".$txt_remarks.",".$all_po_id.",".$save_string.",".$save_data.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$currentStockValue = $stock_value-$cons_amnt;
		$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
		$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*'".$currentStockValue."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


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

		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9)
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,19,".$id_dtls.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

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

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

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


		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9)
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


		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**0**".$finish_update_id."**".$finish_fabric_issue_num;
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
				echo "0**0**".$finish_update_id."**".$finish_fabric_issue_num;
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

		$stock_sql=sql_select("select current_stock,avg_rate_per_unit,stock_value,color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$color_id=$stock_sql[0][csf('color')];
		$avg_rate=$stock_sql[0][csf('avg_rate_per_unit')];
		$stock_value=$stock_sql[0][csf('stock_value')];
		$cons_amnt=str_replace("'","",$txt_issue_qnty)*$avg_rate;
		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*updated_by*update_date";

		if($product_id==$previous_prod_id)
		{
			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty)+str_replace("'", '',$hidden_issue_qnty);
			$curr_stock_value=$stock_value-(str_replace("'","",$txt_issue_qnty)-str_replace("'", '',$hidden_issue_qnty))*$avg_rate;
			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$latest_current_stock=$curr_stock_qnty+str_replace("'", '',$hidden_issue_qnty);

		}
		else
		{
			$stock=return_field_value("current_stock","product_details_master","id=$previous_prod_id");
			$adjust_curr_stock=$stock+str_replace("'", '',$hidden_issue_qnty);

			$latest_current_stock=$curr_stock_qnty;

			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
			$curr_stock_value=$stock_value-str_replace("'","",$txt_issue_qnty)*$avg_rate;

			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		}

		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			echo "17**0";
			die;
		}


		$field_array_update="sample_type*issue_date*challan_no*knit_dye_source*knit_dye_company*buyer_id*updated_by*update_date";

		$data_array_update=$cbo_sample_type."*".$txt_issue_date."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_buyer_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*bin_box*cons_quantity* cons_rate* cons_amount*issue_challan_no*batch_lot*body_part_id*gmt_item_id *updated_by*update_date";
		$data_array_trans=$product_id."*".$txt_issue_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_issue_qnty."*'".$avg_rate."'*'".$cons_amnt."'*".$txt_challan_no."*".$txt_batch_lot."*".$hidden_bodypart_id."*".$cbo_item_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		$field_array_dtls="prod_id*issue_qnty*store_id*batch_lot*no_of_roll*cutting_unit*remarks*order_id*roll_save_string*order_save_string*updated_by*update_date";

		$data_array_dtls=$product_id."*".$txt_issue_qnty."*".$cbo_store_name."*".$txt_batch_lot."*".$txt_no_of_roll."*".$cbo_cutting_floor."*".$txt_remarks."*".$all_po_id."*".$save_string."*".$save_data."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


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

		if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9)
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		//	$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );


			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,19,".$update_dtls_id.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//$id_prop = $id_prop+1;
			}

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
				$adjust_prod=sql_update("product_details_master","current_stock",$adjust_curr_stock,"id",$previous_prod_id,0);
				if($adjust_prod) $flag=1; else $flag=0;

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
			if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==4 || str_replace("'","",$cbo_issue_purpose)==9)
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

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
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
				echo "1**0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
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

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, buyer_id from  inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=19";
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
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province']; ?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
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


		    <table align="right" cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" >
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
					                <td align="center"><? echo $i; ?></td>
					                <td><p style="word-break:break-all;"><? echo chop($buyers,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? echo chop($styleRef,","); ?></p></td>
					                <td><p style="word-break:break-all;"><? echo chop($jobNo,","); ?></p></td>
					                <td><? echo $order_nos; ?></td>
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
		                <td align="right"><?php echo $totalQnty; ?></td>
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
		                <td align="right"><?php echo $totalQnty; ?></td>
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
if( $action == "populate_data_uom"){
    $sql = sql_select("select unit_of_measure  from product_details_master where id = $data");
    $unit_of_measure= $sql[0][csf('unit_of_measure')];
    echo "$('#txt_uom').val('".$unit_of_measurement[$unit_of_measure]."');\n";
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value, 'create_reqn_search_list_view', 'search_div', 'woven_finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

	$requisition_data = sql_select("SELECT a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, b.po_id, c.job_no_prefix_num from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id join wo_po_details_master c on c.job_no=b.job_no $job_cond where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $buyer_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date, b.buyer_id, b.job_no, c.job_no_prefix_num,b.po_id order by a.id desc");

	?>
	<div align="center">
		<table  class="rpt_table" width="440" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
			<thead>
				<tr>
					<th width="20">SI</th>
					<th width="50">Req. NO</th>
					<th width="50">Year</th>
					<th width="100">Requisition Date</th>
					<th width="100">Job NO</th>
					<th width="100">PO NO</th>
					<th width="100">Buyer</th>
				</tr>
			</thead>
		</table>
		<table id="tbl_list_search" class="rpt_table" width="440" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
	    	<tbody>
	    		<?
					$i=1;
					foreach($requisition_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							?>
							<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')].'**'.$row[csf('reqn_number')] ?>')">
								<td width="20"><? echo $i; ?></td>
								<td width="50" align="left"><? echo $row[csf('reqn_number_prefix_num')]; ?></td>
								<td width="50" align="left"><? echo $row[csf('year')]; ?></td>
								<td width="100" align="left"><? echo $row[csf('reqn_date')]; ?></td>
								<td width="100" align="left"><? echo $row[csf('job_no_prefix_num')]; ?></td>
								<td width="100" align="left"><? echo $po_arr[$row[csf('po_id')]]; ?></td>
								<td width="100" align="left"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>

							</tr>

						<?
						$i++;
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
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sizeArr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$fabric_type_arr=return_library_array( "select id, fabric_type from lib_woben_fabric_type",'id','fabric_type');
	$composition_arr=array(); $constructtion_arr=array();
	$txt_fabric_type =3;
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
	$cutting_dtls=sql_select("SELECT a.reqn_number, b.id, b.buyer_id, b.po_id, b.job_no, b.item_id, b.body_part, b.determination_id, b.gsm, b.dia, b.color_id, b.size_id, b.reqn_qty from pro_fab_reqn_for_cutting_mst a join pro_fab_reqn_for_cutting_dtls b on a.id = b.mst_id  where b.status_active=1 and b.is_deleted=0 and b.mst_id=$data");

	$k =1;
	foreach ($cutting_dtls as $row)
	{
		$key = $row[csf('job_no')].$row[csf('determination_id')].'**'.$row[csf('gsm')].'**'.$row[csf('dia')].'**'.$row[csf('color_id')];
		$txt_fabric_description = $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]];
		$product_name_details=$fabric_type_arr[$txt_fabric_type].",  ".trim(str_replace("'","",$txt_fabric_description)).", ".$row[csf('gsm')].", ".$row[csf('dia')].", ".$color_arr[$row[csf('color_id')]]. ", ".$buyer_supplied;
		$cutting_dtls_arr[$key]['job_no'] = $row[csf('job_no')];
		$cutting_dtls_arr[$key]['product_fabric'] = $product_name_details;
		$cutting_dtls_arr[$key]['color_id'] = $row[csf('color_id')];
		$cutting_dtls_arr[$key]['reqn_qty'] += $row[csf('reqn_qty')];
		$cutting_dtls_arr[$key]['po_id'][$row[csf('po_id')]] = $row[csf('po_id')];

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

	    <table class="rpt_table" id="requisition_dtls" width="450" cellspacing="0" cellpadding="0" border="0" rules="all">
	    	<thead>
				<tr>
					<th width="40">SL</th>
                    <th width="100">Job No</th>
                    <th width="150">Const/Composition</th>
                    <th width="80">Gmts. Color</th>
                    <th width="60">Reqn. Qty</th>
				</tr>
			</thead>
	<?
	foreach($cutting_dtls_arr as $row)
	{
		if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
		$js_set_data = $row['job_no'].'**'.$row['product_fabric'];
		?>
		<tr style="cursor: pointer;" bgcolor="<? echo $bgcolor; ?>" id="req_tr_<? echo $i; ?>" onclick="requisition_set_data('<? echo $js_set_data ?>');change_color('<? echo $i; ?>','#E9F3FF')">
            <td width="40"><? echo $i; ?></td>
            <td width="100" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
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
