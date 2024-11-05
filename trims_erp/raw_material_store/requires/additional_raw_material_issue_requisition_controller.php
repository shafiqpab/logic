<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
/*$item_cate_id = $userCredential[0][csf('item_cate_id')];
$origin_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
$category_general_row=implode(",",array_flip($general_item_category)).",97,101,105,106";*/
if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = " and lib_location.id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
/*$item_cat_other=implode(",",array_flip($general_item_category));
$item_cat_other.=",97,101,105,106";
if($item_cate_id !='') {
	$item_cate_other_arr=explode(",",$item_cat_other);
	$item_cate_cre_id_arr=explode(",",$item_cate_id);
	$item_cate_credential_cond="";
	foreach($item_cate_cre_id_arr as $cre_cat_id)
	{
		if(in_array($cre_cat_id,$item_cate_other_arr))
		{
			$item_cate_credential_cond.=$cre_cat_id.",";
		}
	}
    $item_cate_credential_cond = chop($item_cate_credential_cond,",");
}
else
{
     $item_cate_credential_cond=$item_cat_other;
}*/
//echo $item_cate_id."=".$item_cat_other."=".$item_cate_credential_cond;die;

if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}



if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/additional_raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td');load_drop_down( 'requires/additional_raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store','store_td');", '', '', '', '', '',3 );
	exit();	 
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];

	echo create_drop_down('cbo_store_name', 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in (101,22) group by a.id, a.store_name order by store_name ", 'id,store_name', 1, '-- Select --', $selected, "loadStock('$company_id"."_"."$location_id"."_"."'+this.value)");
	 exit();
}

if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",0);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 0);
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 );
	}
	exit();
}

if ($action == "create_batch_search_list_view") 
{
	//print_r ($data);
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	$txt_date_from = $data[4];
	$txt_date_to = $data[5];

	if ($search_common == "") 
	{
		if($db_type==0)
		{ 
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd'); 
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd'); 
		}
		else
		{ 
			$txt_date_from=change_date_format($txt_date_from, "", "",1); 
			$txt_date_to=change_date_format($txt_date_to, "", "",1); 
		}

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.batch_date between '$txt_date_from' and '$txt_date_to'";
	}
	else $date_cond="";

	if ($search_type == 1) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 2) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 3) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$order_arr=array(); $colorid_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('order_quantity')];
		$order_arr[$row[csf('id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
	}
	unset($order_sql);
	
	if($db_type==0) $poid_cond="group_concat(b.po_id)";
	else $poid_cond="listagg(b.po_id,',') within group (order by b.po_id)";
	
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id ,a.operation_type, a.sub_operation, $poid_cond as poid, sum( b.roll_no) as qtypcs from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form in (316)  and a.process_id='1' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_cond
	group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id,a.operation_type, a.sub_operation order by a.id DESC";
	$nameArray = sql_select($sql);
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="40">Ex.</th>
            <th width="90">Color</th>
            <th width="80">Batch Weight</th>
            <th width="80">Batch Qty(Pcs)</th>
            <th width="70">Batch Date</th>
            <th>PO No.</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($nameArray as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					//$order_no = '';
					$order_id = array_unique(explode(",", $row[csf('poid')]));
 					$order_no =""; $subcon_job=''; $party_id=''; $within_group=''; $qty=0; $buyer_po_id=''; $sub_operation='';
					foreach ($order_id as $idpo)
					{
						if($order_no=="") $order_no=$order_arr[$idpo]['po']; else $order_no.= ",".$order_arr[$idpo]['po'];
						if($subcon_job=="") $subcon_job=$order_arr[$idpo]['job']; else $subcon_job.= ",".$order_arr[$idpo]['job'];
						if($party_id=="") $party_id=$order_arr[$idpo]['party_id']; else $party_id.= ",".$order_arr[$idpo]['party_id'];
						if($within_group=="") $within_group=$order_arr[$idpo]['within_group']; else $within_group.= ",".$order_arr[$idpo]['within_group'];
						if($buyer_po_id=="") $buyer_po_id=$order_arr[$idpo]['buyer_po_id']; else $buyer_po_id.= ",".$order_arr[$idpo]['buyer_po_id'];
						
						$qty+=$order_arr[$idpo]['qty'];
					}	
					
					$order_no=implode(", ",array_unique(explode(",",$order_no)));
					$subcon_job=implode(", ",array_unique(explode(",",$subcon_job)));
					$party_id=implode(", ",array_unique(explode(",",$party_id)));
					$within_group=implode(", ",array_unique(explode(",",$within_group)));	
					$buyer_po_id=implode(", ",array_unique(explode(",",$within_group)));
					
					$exbuyer_po_id=	array_unique(explode(", ", $buyer_po_id));	
					$buyer_po=""; $buyer_style="";
					foreach ($exbuyer_po_id as $idbuyerpo)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$idbuyerpo]['po']; else $buyer_po.= ",".$buyer_po_arr[$idbuyerpo]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$idbuyerpo]['style']; else $buyer_style.= ",".$buyer_po_arr[$idbuyerpo]['style'];
					}
					
					$buyer_po=implode(", ",array_unique(explode(",",$buyer_po)));	
					$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
					$suboperation=array_unique(explode(",", $row[csf('sub_operation')]));
					foreach ($suboperation as $sub)
					{
						$sub_operation .=$wash_sub_operation_arr[$sub];
					}
					//$sub_operation = implode(","$wash_sub_operation_arr[array_unique(explode(",", $row[csf('sub_operation')]))]);
					//echo $sub_operation.'=='; 
					$str=$row[csf('id')].'___'.$row[csf('batch_no')].'___'.$subcon_job.'___'.chop($row[csf('poid')],',').'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$qty.'___'.$buyer_po_id.'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('operation_type')].'___'.chop($sub_operation,',');
					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i; ?>" onClick="js_set_value('<?php echo $str; ?>')">
                        <td width="30"><?php echo $i; ?></td>
                        <td width="70"><p><?php echo $row[csf('batch_no')]; ?></p></td>
                        <td width="40"><?php echo $row[csf('extention_no')]; ?>&nbsp;</td>
                        <td width="90"><p><?php echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                        <td width="80" align="right"><p><?php echo $row[csf('batch_weight')]; ?></p></td>
                        <td width="80" align="right"><p>&nbsp;<?php echo $row[csf('qtypcs')]; ?></p></td>
                        <td width="70" align="center"><p><?php echo change_date_format($row[csf('batch_date')]); ?></p></td>
                        <td><p><?php echo $order_no ; ?>&nbsp;</p></td>
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

if($action=="order_details")
{
	$data=explode("***",$data);
	$company_id=$data[0];
	$batch_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		//echo "select id, color_size_id, issue_date, production_hour, qcpass_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$prodData=sql_select("select id, po_id, issue_date, production_hour, qcpass_qty, reje_qty,rewash_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($prodData as $row)
		{
			$data_arr[$row[csf('po_id')]]['issue_date']=$row[csf('issue_date')];
			$data_arr[$row[csf('po_id')]]['production_hour']=$row[csf('production_hour')];
			$data_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$data_arr[$row[csf('po_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$data_arr[$row[csf('po_id')]]['reje_qty']=$row[csf('reje_qty')];
			$data_arr[$row[csf('po_id')]]['rewash_qty']=$row[csf('rewash_qty')];
			$data_arr[$row[csf('po_id')]]['operator_name']=$row[csf('operator_name')];
			$data_arr[$row[csf('po_id')]]['shift_id']=$row[csf('shift_id')];
			$data_arr[$row[csf('po_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($prodData);
	}
	//echo "<pre>";
	//print_r($data_arr);

	/*$sql= "select  a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
	and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
	
	and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$company_id' and d.id='$recipe_id' group by a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by c.id ASC";*/

	/*$sql = "select a.id, a.batch_no,a.process_id , a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty ,c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.embl_type, d.body_part ,e.id as color_size_id from pro_batch_create_mst a, pro_batch_create_dtls b ,subcon_ord_mst c, subcon_ord_dtls d,subcon_ord_breakdown e where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and d.id=e.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";*/
	$sql = "select a.id, a.batch_no, a.process_id, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty, c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.gmts_color_id
	from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and c.id=d.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";

	//echo $sql; die;
	$prod_data_arr=sql_select($sql);

	$i=1; 
	foreach($prod_data_arr as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$process_id = array_unique(explode(",", $row[csf('process_id')]));
		foreach ($process_id as $val)
		{
			if ($process_name == "") $process_name =$wash_type[$val]; else $process_name.= ",".$wash_type[$val];
		}
		$process_name=implode(", ",array_unique(explode(",",$process_name)));
		//echo "<pre>";
		//print_r($prod_data_arr); 
		//echo $prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']; die;
		$upid=$data_arr[$row[csf('po_id')]]['id'];
		$qcpass_qty=$data_arr[$row[csf('po_id')]]['qcpass_qty'];
		$rej_qty=$data_arr[$row[csf('po_id')]]['reje_qty'];
		$rewash_qty=$data_arr[$row[csf('po_id')]]['rewash_qty'];
		$operator_name=$data_arr[$row[csf('po_id')]]['operator_name'];
		$shift_id=$data_arr[$row[csf('po_id')]]['shift_id'];
		$remarks=$data_arr[$row[csf('po_id')]]['remarks'];
		//echo $data_arr[$row[csf('color_size_id')]]['qcpass_qty']."=="; die;
		?>
		<tr bgcolor="<?php echo $bgcolor; ?>" name="tr[]" id="tr_<?php echo $i;?>">
			<td align="center"><?php echo $i; ?></td>
            <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $row[csf("order_no")]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $color_arr[$row[csf('gmts_color_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $process_name; ?>&nbsp;</td>
			<td align="right"><input type="text" name="txtProdQty[]" id="txtProdQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<?php echo $qcpass_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td><input type="text" name="txtRejQty[]" id="txtRejQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:70px" placeholder="Write" value="<?php echo $rej_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td align="right"><input type="text" name="txtReWashQty[]" id="txtReWashQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<?php echo $rewash_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td>
            	<input type="text" name="txtRemarks[]" id="txtRemarks_<?php echo $i;?>" class="text_boxes" style="width:90px" placeholder="Write" value="<?php echo $remarks; ?>" />
                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<?php echo $i;?>" style="width:50px" value="<?php echo $upid; ?>" />
                <input type="hidden" name="txtbuyerPoId[]" id="txtbuyerPoId_<?php echo $i;?>" style="width:50px" value="<?php echo $row[csf('buyer_po_id')]; ?>" />
                <input type="hidden" name="txtPoId[]" id="txtPoId_<?php echo $i;?>" style="width:50px" value="<?php echo $row[csf('po_id')]; ?>" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<?php echo $i;?>" style="width:50px" value="<?php //echo $row[csf('color_size_id')]; ?>" />
            </td>
		</tr>
		<?
		$i++;
	}
	exit();
}

/*/Search Saved data/*/
if($action=="armir_production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_production_data').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Job No');
			else if(val==2) $('#search_by_td').html('Requisition No');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="7"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <!-- <th width="100">Production ID</th> -->
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Job No</th>
                                <th width="130" colspan="2">Issue Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?php echo create_drop_down( "cbo_location_name", 150, "select id, location_name from lib_location where company_id='$cbo_company_name' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td>
									<?php
                                        $search_by_arr=array(1=>'Job No', 2=>'Requisition No');
                                        echo create_drop_down('cbo_type', 100, $search_by_arr, '', 0, '', 1, 'search_by(this.value)', 0);
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<?php echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_production_no_list_view', 'search_div', 'additional_raw_material_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center" valign="middle">
                                    <?php echo load_month_buttons(1);  ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <div id="search_div" ></div>
		</div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_production_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$date_from=$data[2];
	$date_to=$data[3];
	$search_type=$data[4];
	$search_year=$data[7];
	$search_by=str_replace("'","",$data[5]);
	$search_str=trim(str_replace("'","",$data[6]));

	if($company_id==0) { echo 'Select Company first'; die; }
	
	if($db_type==0)
	{
		$start_date= change_date_format($date_from,'yyyy-mm-dd');
		$end_date= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$start_date= change_date_format($date_from, "", "",1) ;
		$end_date= change_date_format($date_to, "", "",1);
	}

	$date_cond = "";
	if ( $start_date != '' && $end_date != '' )
	{
		if ($db_type == 0) 
		{
			$date_cond ="and a.issue_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and a.issue_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} else {
		if ($db_type == 0) 
		{
			$date_cond ="and a.issue_date between '".change_date_format("01-Jan-$search_year", "yyyy-mm-dd", "-")."' and '".change_date_format("31-Dec-$search_year", 'yyyy-mm-dd', '-')."'";
		} 
		else 
		{
			$date_cond="and a.issue_date between '".change_date_format("01-Jan-$search_year", "yyyy-mm-dd", "-", 1)."' and '".change_date_format("31-Dec-$search_year", 'yyyy-mm-dd', '-', 1)."'";
		}
	}

	// echo $date_cond;die;

	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";

	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no='$search_str' ";
			else if($search_by==2) $search_com_cond="and a.requisition_no='$search_str'";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '%$search_str%'";
			else if($search_by==2) $search_com_cond="and a.requisition_no like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '$search_str%'";
			else if($search_by==2) $search_com_cond="and a.requisition_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '%$search_str' ";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
		}
	}
	
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";	
	
	if($location_id !="0") $location_cond= "and b.location_id=$location_id"; else $location_cond= "";
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	?>
	<body>
		<div align="center">
			<fieldset style="width:670px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="150">Requisition No</th>
                            <th width="90">Issue Date</th>
                            <th width="150">Job No</th>
                            <th>Order No</th>
						</thead>
					</table>
					<div style="width:670px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
							<?

							 $sql = "SELECT a.id, a.job_no, a.requisition_no, a.issue_date, b.order_no
									from trims_raw_mat_requisition_mst a, trims_job_card_mst b
									where a.entry_form=501 $date_cond and a.status_active=1 and b.status_active=1 and a.job_no=b.trims_job and a.company_id='$company_id' $location_cond $search_com_cond
									order by id desc";
							// echo $sql; // die;
							$sql_res=sql_select($sql);

							$i=1;  // $sub_operation=''; $batch_no='';  $operation_type='';
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i;?>" onClick="js_set_value('<?php echo $row[csf('id')]; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="150" align="center"><?php echo $row[csf('requisition_no')]; ?></td>
                                    <td width="90" align="center"><?php echo $row[csf('issue_date')]; ?>&nbsp;</td>
                                    <td width="150" align="center"><?php echo $row[csf('job_no')]; ?></td>
                                    <td align="center"><?php echo $row[csf('order_no')]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=='save_update_delete')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)   // Insert Here==============================================================
	{
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 501;
        $mstId = return_next_id('id', 'trims_raw_mat_requisition_mst', 1);
        $dtlsId = return_next_id('id', 'trims_raw_mat_requisition_dtls', 1);
        $year_cond = '';

        // echo "10**$mstId**$dtlsId";die;

        $con = connect();
        if($db_type==0) {
            mysql_query("BEGIN");
        }

        if($db_type==0) $year_cond=" and YEAR(insert_date)";
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

        $new_return_no=explode('*', return_mrr_number( str_replace("'", "", $cbo_company_name), '', 'ARMIR', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from trims_raw_mat_requisition_mst where company_id=$cbo_company_name and entry_form=$entryForm $year_cond=".date('Y',time())." order by id desc", 'prefix_no', 'prefix_no_num' ));

        $field_array_mst = "id, prefix_no, prefix_no_num, requisition_no, company_id, location_id, issue_date, issue_basis, section_id, target_prod_qty, uom_id, store_id, job_no, job_id, order_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array_mst="(".$mstId.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_issue_date.",".$cbo_issue_basis.",".$cbo_section.",".$txt_targeted_prod_qty.",".$cbo_uom.",".$cbo_store_name.",".$txt_job_no.",".$hid_job_id.",".$hid_order_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,$entryForm)";

        $field_array_dtls="id, mst_id, remarks, requisition_qty, issue_date, item_group_id,section_id,product_id, store_id, job_no, job_id,uom, inserted_by, insert_date";
        // $tmpRequQty = '';
        for($i=1; $i<=$total_row; $i++) {
            $itemGroup="hdnItemGroupId_".$i;
			$txtRemarks="txtRemarks_".$i;
			$txtReqQty="txtReqQty_".$i;
			$productId="productId_".$i;
			$sectionId="sectionId_".$i;
			$cboUom="cboUom_".$i;

			// $tmpReqQty .= $$txtReqQty .',';

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

            $data_array_dtls.="(".$dtlsId.",".$mstId.",'".$$txtRemarks."','".$$txtReqQty."',".$txt_issue_date.",'".$$itemGroup."','".$$sectionId."','".$$productId."',".$cbo_store_name.",".$txt_job_no.",".$hid_job_id.",".$$cboUom.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')"; 

            $add_comma = true; // first entry is done. add a comma for next entries
            $dtlsIds .= $dtlsId . ',';
            $dtlsId++; // increment details id by 1
        }

        // echo "10**insert into trims_raw_mat_requisition_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('trims_raw_mat_requisition_mst', $field_array_mst, $data_array_mst, 0);       
      	// echo "10**insert into trims_raw_mat_requisition_dtls(".$field_array_dtls.") values ".$data_array_dtls; die;
        $rID2 = sql_insert('trims_raw_mat_requisition_dtls', $field_array_dtls, $data_array_dtls, 0);
        // echo '10**'.$rID."==".$rID2;die;

        if($db_type==0) {
            if($rID==1 && $rID2==1) {
                mysql_query("COMMIT");              
                echo '0**'.$mstId.'**'.$new_return_no[0].'**'.rtrim($dtlsIds, ',');
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$id;
            }
        }
        else if($db_type==2) {
            if($rID==1 && $rID2==1) {
                oci_commit($con);
                echo '0**'.$mstId.'**'.$new_return_no[0].'**'.rtrim($dtlsIds, ',');
            } else {
                oci_rollback($con);
                echo '10**'.$id;
            }
        }
	}
	else if ($operation==1)   // Update Here============================================================
	{
		$dtlsIds = '';
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="issue_date*store_id*updated_by*update_date";
        $data_array="$txt_issue_date*$cbo_store_name*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 

		$data_array_dtls_update="";
		$field_array_dtls_update="requisition_qty*remarks*store_id*updated_by*update_date";
		$requisition_no=str_replace("'", "", $txt_production_id);
		
		$data_array_dtls="";

		$issave=1; //echo "10**";
		for($i=1;$i<=$total_row;$i++)
		{
			$txtRemarks="txtRemarks_".$i;
			$txtReqQty="txtReqQty_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			//echo "10**".$updateIds;
			$dtlsIds .= $updateIds . ',';

			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;	
				$data_array_dtls_update[$updateIds] = explode("*",("".$$txtReqQty."*'".$$txtRemarks."'*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$issave=1;
				$id_arr_pro[]=$updateIds;
			}
		}
		// echo "10**".bulk_update_sql_statement("trims_raw_mat_requisition_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;

		$rID=sql_update('trims_raw_mat_requisition_mst', $field_array, $data_array, 'id', $update_id, 0);
		if($data_array_dtls_update !=""){
			/*echo "10**".bulk_update_sql_statement( "trims_raw_mat_requisition_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;*/
			$rID2=execute_query(bulk_update_sql_statement('trims_raw_mat_requisition_dtls', 'id', $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ),1);
			
		}
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($rID==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
		}
		else if($db_type==2)
		{
			if($rID==1 && $rID2==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
		}
	}
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// echo "10**".str_replace("'",'',$update_id); die;

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("trims_raw_mat_requisition_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("trims_raw_mat_requisition_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		//echo "10**".$rID."**".$rID1; die;	
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if ($rID==1 && $rID1==1) {
                oci_commit($con);
                echo "2**" .str_replace("'",'',$update_id);
            } else {
                oci_rollback($con);
                echo "10**" .str_replace("'",'',$update_id);
            }
		}
	}

	disconnect($con);
 	die;
}

if($action=="raw_mat_issue_requisition_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location ", 'id', 'store_name');
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");  
	$order_arr=array();
	$order_sql = sql_select("SELECT a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_id
    	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
   		where a.subcon_job = b.job_no_mst and b.id = c.mst_id and a.entry_form = 255 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 ");
	foreach($order_sql as $row)
	{
		
		$order_arr[$row[csf('order_id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('order_id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('order_id')]]['party_id']=$row[csf('party_id')];
	}
	unset($order_sql);
		
	$sql_mst = "SELECT requisition_no as REQUISITION_NO, location_id as LOCATION_ID, job_no as JOB_NO, order_id as ORDER_ID, issue_date as ISSUE_DATE, store_id as STORE_ID,inserted_by as INSERTED_BY from trims_raw_mat_requisition_mst where entry_form=501 and company_id='$data[0]' and id='$data[1]'";
	$dataArray = sql_select($sql_mst); 

	$party_name="";
	if(  $order_arr[$dataArray[0]['ORDER_ID']]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0]['ORDER_ID']]['party_id']];
	else if($order_arr[$dataArray[0]['order_id']]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0]['ORDER_ID']]['party_id']];
	
	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <!-- <img  src='../../<?php echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' /> -->
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><?php echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <!-- <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <?php echo $location_arr[$dataArray[0]['LOCATION_ID']]; ?></strong></td>
                        </tr> -->
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <?php echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><u><?php echo $data[2]; ?></u></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Requisition No: </strong></td>
                <td width="175"><?php echo $dataArray[0]['REQUISITION_NO']; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><?php echo $party_name; ?></td>
                <td width="130"><strong>Req. Date: </strong></td>
                <td width="175"><?php echo change_date_format($dataArray[0]['ISSUE_DATE']); ?></td>
            </tr>
            <tr>
                <td><strong>Location: </strong></td>
                <td><?php echo $location_arr[$dataArray[0]['LOCATION_ID']]; ?></td>
                <td><strong>Job No:</strong></td>
                <td><?php echo $dataArray[0]['JOB_NO']; ?></td>
                <td><strong>Order No:</strong></td>
                <td><?php echo $order_arr[$dataArray[0]['ORDER_ID']]['po']; ?></td>
            </tr>
            <tr>
                <td><strong>Store: </strong></td>
                <td><?php echo $store_arr[$dataArray[0]['STORE_ID']]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th>Item Group</th>
	                <th>Material Description</th>
	                <th>Brand</th>
	                <th>UOM</th>
	                <th>Requ. Qty.</th>
	                <th>Stock</th>
	                <th>Remarks</th>
                </thead>
				<?
				
				$mstId = $data[1];
				$sql = "SELECT b.id as update_id, b.requisition_qty as REQUISITION_QTY, b.remarks as REMARKS, c.id as PRODUCT_ID, c.section_id as SECTION_ID, c.brand_name as BRAND_NAME, c.item_description as SPECIFICATION,c.item_group_id as ITEM_GROUP_ID, c.unit_of_measure as UOM, c.current_stock, d.item_name as ITEM_NAME, sum((case when e.transaction_type in(1,4,5) then e.cons_quantity else 0 end)-(case when e.transaction_type in(2,3,6) then e.cons_quantity else 0 end)) as BALANCE_STOCK
				from trims_raw_mat_requisition_dtls b, product_details_master c, lib_item_group d, inv_transaction e 
				where b.mst_id=$mstId and b.product_id=c.id and c.item_group_id=d.id and b.product_id=e.prod_id and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
				group by b.id , b.requisition_qty , b.remarks, c.id, c.section_id, c.brand_name, c.item_description, c.item_group_id, c.unit_of_measure, c.current_stock, d.item_name 
				order by b.id";
				//echo $sql; // die;
				$data_array=sql_select($sql); 
				
				//echo '<pre>';
				//print_r($data_array);
				// echo '</pre>';

 			$i=1; $reqTotal=0;
 			foreach($data_array as $row) 
			{
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>">
					<td><?php echo $i; ?></td>
					<td>
						<?php echo $row['ITEM_NAME']; ?>
					</td>
					<td>
						<?php echo $row['SPECIFICATION']; ?>
					</td>
					<td style="width: 10%" align="right">
						<?php echo $row['BRAND_NAME']; ?>
					</td>
					<td style="width: 10%">
						<?php echo $unit_of_measurement[$row['UOM']]; ?>
					</td>
					<td style="width: 10%" align="right">
						<?php echo number_format($row['REQUISITION_QTY'], 3);$reqTotal+=$row['REQUISITION_QTY']; ?>
					</td>
					<td style="width: 10%" align="right">
						<?php echo number_format($row['BALANCE_STOCK'], 3); ?>
					</td>
					<td style="width: 10%">
						<?php echo $row['REMARKS']; ?>
					</td>
				</tr>
				<?
				$i++;
			
			}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="5"><strong>Grand Total</strong></td>
                    <td align="right"><?php echo number_format($reqTotal, 3, '.', ''); ?>&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
			<?php echo signature_table(255, $data[0], "930px",'',70,$user_lib_name[$dataArray[0]['INSERTED_BY']]);?>
        </div>
    </div>
	<?
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents('Job Popup Info', '../../../', 1, 1, $unicode, '', '');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'additional_raw_material_issue_requisition_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Requisition No');
			}
			else if(val==2)
			{
				$('#search_by_td').html('Job No');
			}
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead> 
						<tr>
							<th colspan="9"><?php echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
						</tr>
						<tr>
							<th width="140" class="must_entry_caption">Company Name</th>
							<th width="60">Within Group</th>                           
							<th width="140">Party Name</th>
							<th width="80">Search By</th>
							<th width="100" id="search_by_td">Job ID</th>
							<th width="80">Section</th>
							<th width="60">Year</th>
							<th width="170">Date Range</th>                            
							<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
						</tr>           
					</thead>
					<tbody>
						<tr class="general">
							<td><input type="hidden" id="selected_job"><?php $data=explode("_",$data); ?>  <!--  echo $data;-->
								<?php 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
							</td>
							<td>
								<?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", 1, "fnc_load_party_popup(1,this.value);" ); ?>
							</td>
							<td id="buyer_td">
								<?php 
								echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", "", "fnc_load_party_popup(1,this.value);" );   	 	 
								?>
							</td>
							<td>
								<?
									$search_by_arr=array(1=>"Job ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
							</td>
							<td><?php echo create_drop_down( 'cbo_section', 80, $trims_section, '', 1, '-- Select Section --', $data[2], '', 1,'','','','','','',"cboSection[]"); ?></td>
							<td align="center"><?php echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'additional_raw_material_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
							</tr>
							<tr>
								<td colspan="9" align="center" valign="middle">
									<?php echo load_month_buttons();  ?>
									<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
								</td>
							</tr>
							<tr>
								<td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
							</tr>
					</tbody>
				</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$section_id =$data[9];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	/*if($search_str!="")
	{
		$search_com_cond="and a.job_no_prefix_num='$search_str'";
	}*/
	// $search_by_arr=array(1=>"Job ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}
	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
	if($section_id!=0) $section_id_cond=" and a.section_id='$section_id'"; else $section_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}	
	
	$sql= "select a.id, a.trims_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,  a.order_no,a.received_no, a.delivery_date,a.section_id 
	from trims_job_card_mst a, trims_job_card_dtls b
	where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $search_com_cond  $withinGroup $section_id_cond $year_cond
	group by a.id, a.trims_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.order_no ,a.received_no,a.delivery_date,a.section_id 
	order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
        <thead>
            <th width="30">SL</th>
            <th width="120">Job No</th>
            <th width="100">Section</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="120">Receive No</th>
            <th>Delivery Date</th>
        </thead>
        </table>
        <div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
        <tbody>
            <?php 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<?php echo $bgcolor; ?>" onClick='js_set_value("<?php echo $row[csf('id')].'_'.$row[csf('trims_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><?php echo $i; ?></td>
                    <td width="120" style="text-align:center;" ><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="100"><?php echo $trims_section[$row[csf('section_id')]]; ?></td>
                    <td width="60" style="text-align:center;"><?php echo $row[csf('year')]; ?></td>
                    <td width="120"><?php echo $row[csf('order_no')]; ?></td>
                    <td width="120"><?php echo $row[csf('received_no')]; ?></td>
                    <td style="text-align:center;"><?php echo change_date_format($row[csf('delivery_date')]); ?></td>
                </tr>
				<?php 
                $i++; 
            }
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if ($action=="item_popup")
{
	echo load_html_head_contents("Description Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	$store=$data[2];
	?>
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array;
		function js_set_value(str) 
		{  // alert(str);
			var subcon_job = $('#txt_prod_id'+str).val();
			//alert(subcon_job);
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( subcon_job, selected_id ) == -1 ) {
				selected_id.push( subcon_job );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == subcon_job ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id ='';  var id_dtls = ''; var id_break = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			$('#all_ids').val( id );
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>           	 
	                    <th width="120" class="must_entry_caption">Company Name</th>
	                    <th width="70">Item Group</th>  
	                    <th width="100">Section</th>                         
	                    <th width="140">Description</th>
	                    <th width="100">Brand</th>
	                    <th width="70">Product ID</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job">
	                        <? echo create_drop_down( "cbo_company_name", 120, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "",1); ?>
	                    </td>
	                    <td id="item_group_td">
	                        <?
								/*if($data[3]==25){
									$item_category_cond = "and item_category in (22,101)";
								}else{
									$item_category_cond = "and item_category_id in (101,4)";
								}*/
	                            echo create_drop_down( "cbo_item_group", 70, "SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category_id in (4,22,101) order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
	                         ?>
	                    </td>
	                    <td id="section_td">
	                    	<? echo create_drop_down( "cbo_section", 100, $trims_section,"", 1, "-- Select Section --",$data[1],'',1,'','','','','','',"cboSection[]"); ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:127px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_brand_id" id="txt_brand_id" class="text_boxes" style="width:87px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_product_id" id="txt_product_id" class="text_boxes_numeric" style="width:57px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_product_id').value+'_'+document.getElementById('txt_brand_id').value+'_'+document.getElementById('cbo_section').value+'_'+'<?php echo $store; ?>', 'create_description_search_list_view', 'search_div', 'additional_raw_material_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
	                        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                    </td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""> 
	                        	<div style="width:100%; float:left" align="center">
	    							<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
	    							<input type="hidden"  id="all_ids" />
	    						</div>
	    					</td>
	                    </tr>
	                </tbody>
	            </table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_description_search_list_view")
{	
	$data=explode('_',$data);
	$group_id=str_replace("'","",$data[1]);
	$description_str=str_replace("'","",$data[2]);
	$product_id=trim(str_replace("'","",$data[3]));
	$brand_name=trim(str_replace("'","",$data[4]));
	$section_id=str_replace("'","",$data[5]);
	$store_id=str_replace("'","",$data[6]);
	if($store_id!=0 && $store_id!=25)
	{
		$store=" and c.store_id='$store_id'";
	}
	// $actual_section_id=str_replace("'","",$data[6]);
	$item_category_cond = "and a.item_category_id in (22,101 )";
	$entry_form_cond = "and a.entry_form in (220,300,285,0,334)";
	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($group_id!=0) $group=" and a.item_group_id=$group_id"; else { $group=''; }	
	if($description_str!='') $description=" and a.item_description like '%$description_str%'"; else { $description=''; }	
	if($product_id!='') $product=" and a.id='$product_id'"; else { $product=''; }	
	if($brand_name!='') $brand=" and a.brand_name='$brand_name'"; else { $brand=''; }	
	
	/*if($actual_section_id==25){
		$item_category_cond = "and item_category_id in (22,101 )";
		$entry_form_cond = "and entry_form in (220,300,285,0,334)";
	}else{
		$item_category_cond = "and item_category_id in (101)";
		$entry_form_cond = "and entry_form = 334"; 
	}*/
	if($section_id!=0 && $section_id!=25)
	{
		$section=" and a.section_id='$section_id'";
	}

		

	// $sql="select id,company_id, item_code,item_description,item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure ,section_id,item_category_id from product_details_master where status_active=1 and is_deleted=0  $company $group $description $product $brand $section $item_category_cond $entry_form_cond and status_active=1 and is_deleted=0";

	$sql = "SELECT a.id, a.section_id, a.brand_name, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_qnty
	from product_details_master a, lib_item_group b, inv_transaction c 
	where a.item_group_id=b.id and c.prod_id=a.id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company $group $description $product $brand $section $item_category_cond $entry_form_cond $store
	group by a.id, a.section_id, a.brand_name, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name
	having sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end))>0";
	// echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" >
		<thead>
			<th width="30">SL</th>
			<th width="105">Item Group</th>
			<th width="105">Section</th>
			<th width="50">UOM</th>
			<th width="200">Description</th>
			<th width="100">Stock</th>
			<th width="120">Brand</th>
			<th>Product ID</th>
		</thead>
	</table>
	<div style="width:800px; max-height:280px;overflow-y:scroll;" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_po_list">
			<tbody>
				<? 
				$i=1;
				// item_category in (101,5, 6, 7, 23 ) and
				// $itemGroup_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 order by item_name",'id','item_name');
				foreach($data_array as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
						<td width="30"><? echo $i; ?></td>
						<td width="105"><? echo $row[csf('item_name')]; ?></td>
						<td width="105"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
						<td width="50"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
						<td width="200" style="word-break:break-all"  title="<? echo $item_category[$row[csf('item_category_id')]];?>"><? echo $row[csf('item_description')]; ?></td>
						<td width="100"><? echo number_format($row[csf('balance_qnty')],4); ?></td>
						<td width="120" style="word-break:break-all" ><? echo $row[csf('brand_name')]; ?></td>
						<td>
							<? echo $row[csf('id')]; ?>
							<input name="txt_prod_id<? echo $i; ?>" id="txt_prod_id<? echo $i; ?>" type="hidden" value="<? echo $row[csf('id')]; ?>" />
						</td>
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

if( $action=='item_dtls_list_view' ) 
{
	$data=explode('**',$data);
	$searchType=$data[0];
	$allId=$data[1];
	// $jobNo="'".$data[2]."'";
	$trimsJobNo = '';
	$tblRow=0; $buyer_po_arr=array();
	$sql = '';

	if($searchType==1) 
	{
		$sql = "SELECT a.id as PRODUCT_ID, a.section_id as SECTION_ID, a.brand_name as BRAND_NAME, a.item_description as DESCRIPTION,a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UOM, a.current_stock, b.item_name as ITEM_NAME, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as BALANCE_QNTY
		from product_details_master a, lib_item_group b, inv_transaction c 
		where a.id in ($allId) and a.item_group_id=b.id and c.prod_id=a.id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.section_id, a.brand_name, a.item_description, a.item_group_id, a.unit_of_measure, a.current_stock, b.item_name
		having sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end))>0";
	} 
	else
	{
		$sql = "SELECT b.id as update_id, b.requisition_qty as REQUISITION_QTY, b.remarks as REMARKS, c.id as PRODUCT_ID, c.section_id as SECTION_ID, c.brand_name as BRAND_NAME, c.item_description as DESCRIPTION,c.item_group_id as ITEM_GROUP_ID, c.unit_of_measure as UOM, c.current_stock, d.item_name as ITEM_NAME, sum((case when e.transaction_type in(1,4,5) then e.cons_quantity else 0 end)-(case when e.transaction_type in(2,3,6) then e.cons_quantity else 0 end)) as BALANCE_QNTY
		from trims_raw_mat_requisition_dtls b, product_details_master c, lib_item_group d, inv_transaction e 
		where b.mst_id=$allId and b.product_id=c.id and c.item_group_id=d.id and b.product_id=e.prod_id and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
		group by b.id , b.requisition_qty , b.remarks, c.id, c.section_id, c.brand_name, c.item_description, c.item_group_id, c.unit_of_measure, c.current_stock, d.item_name 
		order by b.id";
	}
		
	// echo $sql;
	$data_array=sql_select($sql); 
	// echo '<pre>';
	// print_r($data_array);
	// echo '</pre>';
	$count_data_array=count($data_array);
	if($count_data_array>0){
		foreach($data_array as $row) 
		{		
			$tblRow++;									
			$balance_for_update=$row['BALANCE_QNTY'];
			?>
			<tr bgcolor="<?php echo $bgcolor; ?>" id="row_<?php echo $tblRow; ?>" align="center">
				<td>
					<input id="txtItemGroup_<?php echo $tblRow; ?>" name="txtItemGroup[]" type="text" class="text_boxes" value="<?php echo $row['ITEM_NAME']; ?>" style="width: 90px;" disabled />
				</td>
				<td>
					<input id="txtdescription_<?php echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" value="<?php echo $row['DESCRIPTION']; ?>" style="width: 150px;" disabled />
				</td>
				<td align="right">
					<input type="text" name="txtBrand[]" id="txtBrand_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90px;" value="<?php echo $row['BRAND_NAME']; ?>" disabled />
				</td>
				<td>
					<?php echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --", $row['UOM'],1, 1,'','','','','','',"cboUom[]"); ?>
				</td>
				<td align="right">
					<input type="text" name="txtReqQty[]" id="txtReqQty_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90px;" value="<?php echo number_format($row['REQUISITION_QTY'], 3); ?>" onKeyUp="checkBalance(this.value,'<?php echo $balance_for_update; ?>','<?php echo $tblRow; ?>');" onFocus="checkStoreSelection();" />
				</td>
				<td align="right">
					<input type="text" name="txtStock[]" id="txtStock_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90px;" value="<?php echo $row['BALANCE_QNTY']; ?>" disabled />
				</td>
				<td>
					<input type="text" name="txtRemarks[]" id="txtRemarks_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90px;" placeholder="Write" value="<?php echo $row['REMARKS']; ?>" />
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<?php echo $tblRow; ?>" value="<?php echo $row['UPDATE_ID']; ?>" />
					<input type="hidden" name="hdnItemGroupId[]" id="hdnItemGroupId_<?php echo $tblRow; ?>" value="<?php echo $row['ITEM_GROUP_ID']; ?>"/>
					<input type="hidden" name="hdnBalance[]" id="hdnBalance_<?php echo $tblRow; ?>" value="<?php echo $balance_for_update; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<?php echo $tblRow; ?>" value="<?php echo $row['PRODUCT_ID']; ?>" />
					<input type="hidden" name="sectionId[]" id="sectionId_<?php echo $tblRow; ?>" value="<?php echo $row['SECTION_ID']; ?>" />		
				</td>
			</tr>
				<?
		}
	}else{
		?>
		<tr name="tr[]" id="tr_1">
			<td>
				<?php echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category in (4,101,22) and status_active=1","id,item_name", 1, "Display",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>
			</td>
			<td>
				<input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" placeholder="Browse" style="width: 150px;" onDblClick="openmypage_item();" />
			</td>
			<td align="right">
				<input type="text" name="txtBrand[]" id="txtBrand_1" class="text_boxes" placeholder="Write" style="width: 90;" />
			</td>
			<td>
				<?php echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "Display",2,1, 1,'','','','','','',"cboUom[]"); ?>
			</td>
			<td align="right">
				<input type="text" name="txtReqQty[]" id="txtReqQty_1" class="text_boxes_numeric" placeholder="Write" onBlur="fnc_total_calculate();" style="width: 90px;" />
			</td>
			<td align="right">
				<input type="text" name="txtStock[]" id="txtStock_1" class="text_boxes_numeric" placeholder="Write" style="width: 90px;" />
			</td>
			<td>
				<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" placeholder="Write" style="width: 90px;" />
				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" />
				<input type="hidden" name="hdnBalance[]" id="hdnBalance_1" />
				<input type="hidden" name="productId[]" id="productId_1" />
				<input type="hidden" name="sectionId[]" id="sectionId_1" />
			</td>
		</tr>
		<?
	}
	
	
	exit();
}

if ($action=="load_mst_php_data_to_form")
{
	//echo "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by id DESC"; die;

	$data = explode('**', $data);
    $reqType = $data[0];
    $reqMstId = $data[1];
    $nameArray = array();
    $targetQty = array();

    if ($reqType == 1) {
    	$nameArray=sql_select("SELECT id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form, order_id, order_no, order_qty, received_no, received_id, section_id 
	 	from trims_job_card_mst where entry_form=257 and id=$reqMstId and status_active=1 order by id desc");
		$targetQty = sql_select("select sum(job_quantity) as ord_qty, uom from trims_job_card_dtls where mst_id=$reqMstId and status_active=1 group by job_quantity, uom");
    } else {
    	$nameArray=sql_select("SELECT a.id, a.requisition_no, a.company_id, a.location_id, a.issue_date, a.issue_basis, a.section_id, a.order_id, a.job_id, a.job_no, a.target_prod_qty, a.uom_id, a.store_id, b.order_no
		from trims_raw_mat_requisition_mst a, trims_job_card_mst b
		where a.entry_form=501 and a.id=$reqMstId and a.status_active=1 and b.status_active=1 and a.job_no=b.trims_job");
    }
	
	if ($reqType==1) {
		echo "document.getElementById('hid_job_id').value 				= '".$nameArray[0][csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$nameArray[0][csf("trims_job")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$nameArray[0][csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value 		= '".$nameArray[0][csf("location_id")]."';\n";
		echo "load_drop_down('requires/additional_raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."_".$nameArray[0][csf("location_id")]."', 'load_drop_down_store', 'store_td');";
		echo "document.getElementById('cbo_section').value			= '".$nameArray[0][csf("section_id")]."';\n";
		echo "document.getElementById('hid_order_id').value          	= '".$nameArray[0][csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$nameArray[0][csf("order_no")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_targeted_prod_qty').value         	= '".$targetQty[0][csf("ord_qty")]."';\n";
		echo "document.getElementById('cbo_uom').value         	= '".$targetQty[0][csf("uom")]."';\n";	
	} else {
		echo "document.getElementById('update_id').value 				= '".$nameArray[0][csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$nameArray[0][csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$nameArray[0][csf("company_id")]."';\n";
		echo "load_drop_down('requires/additional_raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."', 'load_drop_down_location', 'location_td');";
		echo "document.getElementById('cbo_location_name').value 		= '".$nameArray[0][csf("location_id")]."';\n";
		echo "load_drop_down('requires/additional_raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."_".$nameArray[0][csf("location_id")]."', 'load_drop_down_store', 'store_td');";
		echo "document.getElementById('cbo_section').value			= '".$nameArray[0][csf("section_id")]."';\n";
		echo "document.getElementById('hid_order_id').value          	= '".$nameArray[0][csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$nameArray[0][csf("order_no")]."';\n";
		echo "document.getElementById('txt_production_id').value         	= '".$nameArray[0][csf('requisition_no')]."';\n";
		echo "document.getElementById('txt_targeted_prod_qty').value         	= '".$nameArray[0][csf('target_prod_qty')]."';\n";
		echo "document.getElementById('cbo_uom').value         	= '".$nameArray[0][csf('uom_id')]."';\n";
		echo "document.getElementById('cbo_store_name').value         	= '".$nameArray[0][csf('store_id')]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
	}

	exit();	
}

?>