<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "load_drop_down('requires/finish_fabric_issue_controller', this.value, 'load_drop_down_location', 'sewingcomlocation_td' );load_cutting_unit();","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,21,24,22) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 1, "load_cutting_unit();" );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 150, $blank_array,"",1, "--Select Service Company--", 1, "" );
	}
	exit();
}

if($action=="load_drop_down_cutting_unit")
{
	echo create_drop_down( "cbo_cutting_floor", 170, "select id,floor_name from lib_prod_floor where company_id=$data and production_process=1 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0);
	exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, '',1 );
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

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sewing_company_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Service Location --", 0, "" );
	exit();
}

// if ($action=="load_room_rack_self_bin")
// {
// 	load_room_rack_self_bin("requires/finish_fabric_issue_controller",$data);
// }

if ($action=="load_drop_down_store")
{
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$store_location_id = $userCredential[0][csf('store_location_id')];
	if ($store_location_id !='') { $store_location_credential_cond = "and a.id in($store_location_id)"; } else{ $store_location_credential_cond=""; }
	echo create_drop_down("cbo_store_name",170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' $location_cond $store_location_credential_cond and b.category_type=2  and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select store --", 0,"details_reset();", 0);
	exit();
}

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;

	echo "document.getElementById('roll_maintained').value 	= '".$roll_maintained."';\n";

	echo "$('#txt_batch_no').val('');\n";
	echo "$('#hidden_batch_id').val('');\n";
	echo "$('#cbo_buyer_name').val('0');\n";
	echo "$('#txt_issue_qnty').val('');\n";
	echo "$('#hidden_issue_qnty').val('');\n";
	echo "$('#txt_issue_req_qnty').val('');\n";
	echo "$('#hidden_prod_id').val('');\n";
	echo "$('#all_po_id').val('');\n";
	echo "$('#save_data').val('');\n";
	echo "$('#save_string').val('');\n";
	echo "$('#txt_order_numbers').val('');\n";
	echo "$('#txt_fabric_received').val('');\n";
	echo "$('#txt_cumulative_issued').val('');\n";
	echo "$('#txt_yet_to_issue').val('');\n";
	echo "$('#previous_prod_id').val('');\n";
	echo "$('#txt_fabric_desc').val('');\n";
	echo "$('#txt_rack').val('');\n";
	echo "$('#txt_shelf').val('');\n";
	echo "$('#cbo_body_part').val('0');\n";
	echo "$('#list_fabric_desc_container').html('');\n";

	if($roll_maintained==1 || $data==0)
	{
		echo "$('#txt_no_of_roll').val('');\n";
		echo "$('#txt_no_of_roll').attr('disabled','disabled');\n";
		echo "$('#txt_no_of_roll').attr('placeholder','Display');\n";

		echo "$('#txt_fabric_desc').removeAttr('disabled','disabled');\n";
		echo "$('#txt_fabric_desc').attr('readonly','readonly');\n";
		echo "$('#txt_fabric_desc').attr('placeholder','Double Click To Search');\n";
		echo "$('#txt_fabric_desc').attr('onDblClick','openmypage_fabricDescription();');\n";
		//echo "$('#fabricDesc_td').html('".'<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:300px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_fabricDescription();" />'."');\n";
	}
	else
	{
		echo "$('#txt_no_of_roll').removeAttr('disabled','disabled');\n";
		echo "$('#txt_no_of_roll').removeAttr('placeholder');\n";

		echo "$('#txt_fabric_desc').attr('disabled','disabled');\n";
		echo "$('#txt_fabric_desc').attr('placeholder','Display');\n";
		echo "$('#txt_fabric_desc').removeAttr('onDblClick');\n";
		//echo "$('#fabricDesc_td').html('".create_drop_down( "txt_fabric_desc", 310, $blank_array,'', 1, '-- Select Fabric Description --','0', '','','' )."');\n";
	}

	exit();
}

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$field_level_data = $_SESSION['logic_erp']['data_arr'][18];
	?>
	<script>
		var selected_id = new Array();
		var selected_attach_id = new Array();
		var booking_id_arr_chk = new Array;

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(batch_id,batch_no,without_order,buyer_id,booking_no,tr_id,booking_id)
		{
			var any_selected = $('#hidden_batch_id').val();

			if(any_selected=="")
			{
				booking_id_arr_chk = [];
			}

			if(booking_id_arr_chk.length==0)
			{
				booking_id_arr_chk.push( booking_no );
			}
			else if( jQuery.inArray( booking_no, booking_id_arr_chk )==-1 &&  booking_id_arr_chk.length>0)
			{
				alert("Booking Mixed is Not Allowed");
				return;
			}

			if($("#tr_"+tr_id).css("display") !='none')
			{
	           // var str = strs[1];
	           if (jQuery.inArray(batch_id, selected_attach_id) != -1 || selected_attach_id.length < 1)
	           {
	           	toggle(document.getElementById('tr_' + tr_id), '#FFFFCC');

	           	if (jQuery.inArray(batch_id, selected_id) == -1) {
	           		selected_id.push(batch_id);

	           	} else
	           	{
	           		for (var i = 0; i < selected_id.length; i++)
	           		{
	           			if (selected_id[i] == batch_id)
	           				break;
	           		}
	           		selected_id.splice(i, 1);
	           	}
	           	var id = '';
	           	for (var i = 0; i < selected_id.length; i++) {
	           		id += selected_id[i] + ',';
	           	}
	           	id = id.substr(0, id.length - 1);
					//alert(id);
					$('#hidden_batch_id').val(id);
					$("#hidden_booking_no").val(booking_no);
					$('#hidden_without_order').val(without_order);
					$('#hidden_buyer_id').val(buyer_id);
					$('#hidden_booking_id').val(booking_id);
				}
			}
		}
	</script>
</head>
<?  ?>
<body>
	<div align="center" style="width:800px;">
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
			<fieldset style="width:890px;">
				<legend>Enter search words</legend>
				<input type="hidden" name="txt_selected_id" id="txt_selected_id"/>
				<input type="hidden" name="txt_attach_id" id="txt_attach_id"/>
				<table cellpadding="0" cellspacing="0" rules="1" border="1" width="870" class="rpt_table" align="center">
					<thead>
						<th width="120"  style="display:none">Sales Order</th>
						<th width="230">Batch Date Range</th>
						<th width="170">Search By</th>
						<th id="search_by_td_up" width="180">Enter Batch No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_without_order" id="hidden_without_order" class="text_boxes" value="">
							<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr>
						<td align="center"  style="display:none">
							<?
						/*	$is_disable = $field_level_data[$cbo_company_id]["cbo_sales_order"]["is_disable"];
							$defalt_value = $field_level_data[$cbo_company_id]["cbo_sales_order"]["defalt_value"];
							if($defalt_value ==0 || $defalt_value =="")
							{
								$defalt_value=2;
							}*/
							$defalt_value=2;
							$is_disable=1;
							echo create_drop_down("cbo_sales_order", 100, $yes_no, "", 0, "-- Select --", $defalt_value, "", $is_disable, ''); ?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
						</td>
						<td align="center" width="160px">
							<?
							//$search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color",3=>"File No",4=>"Ref no",5=>"Style Ref.",6=>"Job No.");
							$search_by_arr=array(7=>"Batch No",1=>"Fabric Booking no.",2=>"Color",3=>"File No",4=>"Ref no",5=>"Style Ref.",6=>"Job No.",8=>"Order No.");
							$dd="change_search_event(this.value, '0*0*0*0*0*0*0*0', '0*0*0*0*0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td" width="140px">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+'<? echo $issue_purpose; ?>'+'_'+'<? echo $store_id; ?>'+'_'+'<? echo $hidden_booking_no; ?>'+'_'+document.getElementById('cbo_sales_order').value, 'create_batch_search_list_view_all', 'search_div', 'finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
if($action=="create_batch_search_list_view_all")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_string_for_sales_list=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$issue_purpose =$data[5];
	$store_id =$data[6];
	$booking_no =$data[7];
	$is_sales =$data[8];

	if($is_sales == 1)
	{
		echo create_batch_search_list_view_sales($search_string_for_sales_list,$search_by,$start_date,$end_date,$company_id,$issue_purpose,$store_id,$is_sales,$db_type,$booking_no);
	}
	else
	{
		if($search_by==3 && $search_string_for_sales_list!='') $file_cond=" and a.file_no like '%$search_string_for_sales_list%'";else $file_cond="";
		if($search_by==4 && $search_string_for_sales_list!='') $ref_cond=" and a.grouping like '%$search_string_for_sales_list%'";else $ref_cond="";
		if($search_by==5 && $search_string_for_sales_list!='') $style_ref_cond=" and c.style_ref_no like '%$search_string_for_sales_list%'";else $style_ref_cond="";
		if($search_by==6 && $search_string_for_sales_list!='') $job_no_cond=" and c.job_no like '%$search_string_for_sales_list%'";else $job_no_cond="";
		if($search_by==8 && $search_string_for_sales_list!='') $search_po_no_cond=" and a.po_number like '%$search_string_for_sales_list%'";else $search_po_no_cond="";
		if($booking_no!="") $booking_no_cond=" and a.booking_no like '%".$booking_no."%'";else $booking_no_cond="";


		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		if(trim($data[0])!="")
		{
			if($search_by==7)
				$search_field_cond="and a.batch_no like '%$search_string%'";
			else if($search_by==1)
				$search_field_cond="and a.booking_no like '%$search_string%'";
			else if($search_by==2)
				$search_field_cond="and a.color_id in(select id from lib_color where color_name like '%$search_string%')";
		}
		else
		{
			$search_field_cond="";
		}

		if(($search_by==3 || $search_by==4 || $search_by==5 || $search_by==6 || $search_by==8) && $search_string_for_sales_list!="")
		{
			$po_data= sql_select("select a.id, a.po_number,a.file_no,a.grouping as ref, a.job_no_mst, b.booking_no, c.style_ref_no from wo_po_break_down a,wo_booking_dtls b , wo_po_details_master c where a.id =b.po_break_down_id and a.job_no_mst = c.job_no and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $file_cond $ref_cond $style_ref_cond $job_no_cond $search_po_no_cond");
			foreach ($po_data as $row)
			{
				$po_id_arr[$row[csf("id")]] = $row[csf("id")];
			}


			$po_id_arr = array_filter($po_id_arr);
			$all_po_ids = implode(",", $po_id_arr);
			$poCond=$poCond2=""; $all_po_id_cond=$all_po_id_cond2="";
			if(count($po_id_arr)>0)
			{
				if($db_type==2 && count($po_id_arr)>999)
				{
					$po_id_arr_chunk=array_chunk($po_id_arr,999) ;
					foreach($po_id_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$poCond.="  o.po_breakdown_id in($chunk_arr_value) or ";
						$poCond2.="  c.to_order_id in($chunk_arr_value) or ";
					}

					$all_po_id_cond.=" and (".chop($poCond,'or ').")";
					$all_po_id_cond2.=" and (".chop($poCond2,'or ').")";
				}
				else
				{
					$all_po_id_cond=" and o.po_breakdown_id in($all_po_ids)";
					$all_po_id_cond2=" and c.to_order_id in($all_po_ids)";
				}
			}
			else{
				echo "Data Not Found";
				die;
			}
		}


		if($db_type==0)
		{
			$null_cond="IFNULL";
			$order_list=" , group_concat(po_breakdown_id) as po_id";
			$order_list_trans=" , group_concat(c.to_order_id) as po_id";
		}
		else
		{
			$null_cond="NVL";
			$order_list=" , LISTAGG(cast(po_breakdown_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_breakdown_id) as po_id";
			$order_list_trans=" , LISTAGG(cast(c.to_order_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.to_order_id) as po_id";
		}

		$sample_booking_cond='';
		if($issue_purpose==8)
		{
			$sample_booking_cond="and a.booking_without_order=1";
		}
		else if($issue_purpose==4 || $issue_purpose==9)
		{
			$sample_booking_cond="and $null_cond(a.booking_without_order,0)!=1";
		}



		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

		if ($issue_purpose==8)
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id as booking_id from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c, wo_non_ord_samp_booking_mst d where a.id=b.batch_id and b.trans_id>0 and b.mst_id=c.id and a.company_id=$company_id and a.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $po_cond $booking_no_cond and c.store_id=$store_id and b.receive_qnty>0 group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id
			union all
			select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id as booking_id
			from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c, wo_non_ord_samp_booking_mst d
			where a.id=b.to_batch_id and b.mst_id=c.id and c.to_company=$company_id  and a.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $booking_no_cond and b.to_store=$store_id and b.transfer_qnty>0 and b.to_trans_id>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from,d.buyer_id,d.id

			union all
			select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id as booking_id from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c, wo_non_ord_knitdye_booking_mst d where a.id=b.batch_id and b.trans_id>0 and b.mst_id=c.id and a.company_id=$company_id and a.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $po_cond $booking_no_cond and c.store_id=$store_id and b.receive_qnty>0 group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id
			union all
			select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id, d.id as booking_id
			from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c, wo_non_ord_knitdye_booking_mst d
			where a.id=b.to_batch_id and b.mst_id=c.id and c.to_company=$company_id  and a.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $booking_no_cond and b.to_store=$store_id and b.transfer_qnty>0 and b.to_trans_id>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from,d.buyer_id, d.id ";
			//echo $sql;die;
		}
		else
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, b.buyer_id, a.booking_no_id as booking_id, 1 as type  $order_list
			from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details o, inv_receive_master c
			where a.id=b.batch_id and b.trans_id>0 and b.id=o.dtls_id and o.entry_form in (7,37) and b.mst_id=c.id and c.entry_form in (7,37) and c.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $all_po_id_cond and c.store_id=$store_id and b.receive_qnty>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, b.buyer_id, a.booking_no_id
			union all
			select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, '' as buyer_id, a.booking_no_id as booking_id, 2 as type $order_list_trans
			from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c
			where a.id=b.batch_id and b.mst_id=c.id and c.to_company=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $all_po_id_cond2 and b.to_store=$store_id and b.transfer_qnty>0 and b.to_trans_id>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, a.booking_no_id";
			//echo $sql;die;
		}
		//echo $sql;

		$nameArray=sql_select( $sql );

		if(empty($nameArray)){
			echo "Data Not Found";
			die;
		}

		foreach($nameArray as $selectResult)
		{
			if($selectResult[csf('booking_without_order')] != 1)
			{
				$order_arr=array_filter(array_unique(explode(",",$selectResult[csf('po_id')])));
				foreach($order_arr as $value)
				{
					$batch_po_arr[$value] = $value;
				}
			}
		}

		$batch_po_arr = array_filter($batch_po_arr);
		$all_batch_po_ids = implode(",", $batch_po_arr);
		$BpoCond=""; $all_batch_po_id_cond="";
		if(count($batch_po_arr)>0)
		{
			if($db_type==2 && count($batch_po_arr)>999)
			{
				$batch_po_arr_chunk=array_chunk($batch_po_arr,999) ;
				foreach($batch_po_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$BpoCond.="  a.id in($chunk_arr_value) or ";
				}

				$all_batch_po_id_cond.=" and (".chop($BpoCond,'or ').")";
			}
			else
			{
				$all_batch_po_id_cond=" and a.id in($all_batch_po_ids)";
			}

			$po_arr=array();$batch_arr=array();
			$po_data=sql_select("select a.id, a.po_number,a.file_no,a.grouping as ref, a.job_no_mst,c.style_ref_no, c.buyer_name
				from wo_po_details_master c,wo_po_break_down a
				where c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $file_cond $ref_cond $style_ref_cond $job_no_cond $search_po_no_cond $all_batch_po_id_cond");

			$all_po_id='';
			foreach($po_data as $row)
			{
				$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
				$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
			}
		}

		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="95">Batch No</th>
					<th width="75">Extention No</th>
					<th width="70">Batch Date</th>
					<th width="70">Batch Qnty</th>
					<th width="100">Booking No</th>
					<th width="100">Job No.</th>
					<th width="100">Color</th>
					<th width="120">Style Ref.</th>
					<th width="150">PO No</th>
					<th width="80">File No</th>
					<th>Ref. No</th>
				</thead>
			</table>
			<div style="width:1100px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search" >
					<?
					$i=1;

					$b_sl=0;
					foreach($nameArray as $selectResult)
					{
						$po_no='';$po_file='';$po_ref='';$style_ref='';$job_no='';

						if(!in_array($selectResult[csf('booking_no')],$batch_check_arr))
						{
							$b_sl++;
							if ($b_sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$batch_check_arr[]=$selectResult[csf('booking_no')];
						}

						$order_id=array_unique(explode(",",$selectResult[csf('po_id')]));

						foreach($order_id as $value)
						{
							if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
							if($po_file=='') $po_file=$po_arr[$value]['file']; else $po_file.=",".$po_arr[$value]['file'];
							if($po_ref=='') $po_ref=$po_arr[$value]['ref']; else $po_ref.=",".$po_arr[$value]['ref'];
							if($style_ref=='') $style_ref=$po_arr[$value]['style']; else $style_ref.=",".$po_arr[$value]['style'];
							if($job_no=='') $job_no=$po_arr[$value]['job_no']; else $job_no.=",".$po_arr[$value]['job_no'];
						}
						$style_refs=implode(",",array_unique(explode(",", $style_ref)));
						$job_no=implode(",",array_unique(explode(",", $job_no)));
						if($po_no=='') $without_order=1; else $without_order=0;
						if($selectResult[csf('type')]==1) $buy_id=$selectResult[csf('buyer_id')]; else $buy_id=$selectResult[csf('buyer_id')];
						?>
						<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $without_order; ?>','<? echo $buy_id; ?>','<? echo $selectResult[csf('booking_no')]; ?>','<? echo $i; ?>',<? echo $selectResult[csf('booking_id')]?>)">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="95"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
							<td width="75"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
							<td width="70"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="70" align="right"><? echo number_format($selectResult[csf('batch_weight')],0); ?>&nbsp;</td>
							<td width="100"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $job_no; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $style_refs; ?>&nbsp;</div></td>
							<td width="150"><p><? echo $po_no; ?>&nbsp;</p></td>
							<td width="80"><p><? echo implode(",",array_unique(explode(",",$po_file))); ?>&nbsp;</p></td>
							<td><p><? echo implode(",",array_unique(explode(",",$po_ref))); ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>

				</table>
			</div>
			<div>
				<table width="1050">
					<tr>
						<td  colspan="12" align="center">
							<div style="width:100%;" align="center">
								<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}
	exit();
}

if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_string_for_sales_list=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$issue_purpose =$data[5];
	$store_id =$data[6];
	$booking_no =$data[7];
	$is_sales =$data[8];

	if($is_sales == 1)
	{
		echo create_batch_search_list_view_sales($search_string_for_sales_list,$search_by,$start_date,$end_date,$company_id,$issue_purpose,$store_id,$is_sales,$db_type,$booking_no);
	}
	else
	{
		if($search_by==3 && $data[0]!='') $file_cond="and a.file_no=$data[0]";else $file_cond="";
		if($search_by==4 && $data[0]!='') $ref_cond="and a.grouping='$data[0]'";else $ref_cond="";
		if($search_by==5 && $data[0]!='') $style_ref_cond="and c.style_ref_no='$data[0]'";else $style_ref_cond="";
		if($search_by==6 && $data[0]!='') $job_no_cond="and c.job_no like '%$data[0]'";else $job_no_cond="";
		if($booking_no!="") $booking_no_cond="and a.booking_no='".$booking_no."'";else $booking_no_cond="";
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		if(trim($data[0])!="")
		{
			if($search_by==0)
				$search_field_cond="and a.batch_no like '$search_string'";
			else if($search_by==1)
				$search_field_cond="and a.booking_no like '$search_string'";
			else if($search_by==2)
				$search_field_cond="and a.color_id in(select id from lib_color where color_name like '$search_string')";
		}
		else
		{
			$search_field_cond="";
		}

	/*	if($db_type==0)
		{
			$order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 and po_id<>0 group by mst_id",'mst_id','po_id');
			$null_cond="IFNULL";
		}
		else
		{
			$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 and po_id<>0 group by mst_id",'mst_id','po_id');
			$null_cond="NVL";
		}*/

		$sample_booking_cond='';
		if($issue_purpose==8)
		{
			$sample_booking_cond="and a.booking_without_order=1";
		}
		else if($issue_purpose==4 || $issue_purpose==9)
		{
			$sample_booking_cond="and $null_cond(a.booking_without_order,0)!=1";
		}

		$po_arr=array();$batch_arr=array();
		$po_data=sql_select("select a.id,b.mst_id as batch_id, a.po_number,a.file_no,a.grouping as ref, a.job_no_mst,c.style_ref_no, c.buyer_name
			from wo_po_details_master c,wo_po_break_down a, pro_batch_create_dtls b,pro_finish_fabric_rcv_dtls d
			where c.job_no=a.job_no_mst and a.id=b.po_id and b.mst_id=d.batch_id and d.trans_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $file_cond $ref_cond $style_ref_cond $job_no_cond");



		$all_po_id='';
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
			$batch_buyer_data[$row[csf('batch_id')]]['buyer_name']=$row[csf('buyer_name')];
			if(($search_by==3 || $search_by==4 || $search_by==5 || $search_by==6) && $data[0]!='')
			{
				if($all_po_id=="") $all_po_id=$row[csf('batch_id')]; else $all_po_id.=",".$row[csf('batch_id')]; //echo $all_po_id;
			}
		}
		if($all_po_id!='') $po_cond="and a.id in($all_po_id)";else  $po_cond="";

		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

		if ($issue_purpose==8)
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c, wo_non_ord_samp_booking_mst d where a.id=b.batch_id and b.trans_id>0 and b.mst_id=c.id and a.company_id=$company_id and a.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $po_cond $booking_no_cond and c.store_id=$store_id and b.receive_qnty>0 group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, d.buyer_id order by a.booking_no";
			//echo $sql;die;
		}
		else
		{

			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, b.buyer_id, 1 as type
			from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c
			where a.id=b.batch_id and b.trans_id>0 and b.mst_id=c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $po_cond and c.store_id=$store_id and b.receive_qnty>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, b.buyer_id
			union all
			select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, '' as buyer_id, 2 as type
			from pro_batch_create_mst a, inv_item_transfer_dtls b, inv_item_transfer_mst c
			where a.id=b.batch_id and b.mst_id=c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $sample_booking_cond $po_cond and b.to_store=$store_id and b.transfer_qnty>0 and b.to_trans_id>0
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from";
			//echo $sql;
		}
		//echo $sql;
		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="95">Batch No</th>
					<th width="75">Extention No</th>
					<th width="80">Batch Date</th>
					<th width="90">Batch Qnty</th>
					<th width="110">Booking No</th>
					<th width="120">Job No.</th>
					<th width="100">Color</th>
					<th width="120">Style Ref.</th>
					<th width="170">PO No</th>
					<th width="80">File No</th>
					<th width="90">Ref. No</th>
				</thead>
			</table>
			<div style="width:1070px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search" >
					<?
					$i=1;
					$nameArray=sql_select( $sql );
					$b_sl=0;
					foreach($nameArray as $selectResult)
					{
						$po_no='';$po_file='';$po_ref='';$style_ref='';$job_no='';

						if(!in_array($selectResult[csf('booking_no')],$batch_check_arr))
						{
							$b_sl++;
							if ($b_sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$batch_check_arr[]=$selectResult[csf('booking_no')];
						}

						$order_id=array_unique(explode(",",$order_id_arr[$selectResult[csf('id')]]));
						foreach($order_id as $value)
						{
							if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
							if($po_file=='') $po_file=$po_arr[$value]['file']; else $po_file.=",".$po_arr[$value]['file'];
							if($po_ref=='') $po_ref=$po_arr[$value]['ref']; else $po_ref.=",".$po_arr[$value]['ref'];
							if($style_ref=='') $style_ref=$po_arr[$value]['style']; else $style_ref.=",".$po_arr[$value]['style'];
							if($job_no=='') $job_no=$po_arr[$value]['job_no']; else $job_no.=",".$po_arr[$value]['job_no'];
						}
						$style_refs=implode(",",array_unique(explode(",", $style_ref)));
						$job_no=implode(",",array_unique(explode(",", $job_no)));
						if($po_no=='') $without_order=1; else $without_order=0;
						if($selectResult[csf('type')]==1) $buy_id=$selectResult[csf('buyer_id')]; else $buy_id=$batch_buyer_data[$selectResult[csf('id')]]['buyer_name'];
						?>
						<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $without_order; ?>','<? echo $buy_id; ?>','<? echo $selectResult[csf('booking_no')]; ?>','<? echo $i; ?>')">
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="95"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
							<td width="75"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo number_format($selectResult[csf('batch_weight')],0); ?></td>
							<td width="110"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
							<td width="110"><p><? echo $job_no; ?></p></td>
							<td width="100"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $style_refs; ?></div></td>
							<td width="170"><p><? echo $po_no; ?></p></td>
							<td width="80"><p><? echo implode(",",array_unique(explode(",",$po_file))); ?></p></td>
							<td width="90"><p><? echo implode(",",array_unique(explode(",",$po_ref))); ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
					<tr>
						<td  colspan="12" align="center">
							<div style="width:100%;" align="center">
								<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}
	exit();
}

	/*
	function create_batch_search_list_view_sales($search_string_for_sales_list,$search_by,$start_date,$end_date,$company_id,$issue_purpose,$store_id,$is_sales,$db_type,$booking_no)
	{
		if($issue_purpose != 9)
		{
			echo "<p style='font-weight:bold; width:100%;text-align:center;'>Sewing Purpose Issue Only</p>";
			die;
		}
		if($search_by==3 && $search_string_for_sales_list!='') $file_cond="and a.file_no=$search_string_for_sales_list";else $file_cond="";
		if($search_by==4 && $search_string_for_sales_list!='') $ref_cond="and a.grouping='$search_string_for_sales_list'";else $ref_cond="";
		if($search_by==5 && $search_string_for_sales_list!='') $style_ref_cond="and c.style_ref_no='$search_string_for_sales_list'";else $style_ref_cond="";
		if($search_by==6 && $search_string_for_sales_list!='') $job_no_cond="and c.job_no like '%$search_string_for_sales_list%'";else $job_no_cond="";
		if($booking_no!="") $booking_no_cond="and a.booking_no='".$booking_no."'";else $booking_no_cond="";

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		if(trim($search_string_for_sales_list)!="")
		{
			if($search_by==0)
				$search_field_cond="and a.batch_no like '%$search_string_for_sales_list%'";
			else if($search_by==1)
				$search_field_cond="and a.booking_no like '%$search_string_for_sales_list%'";
			else if($search_by==2)
				$search_field_cond="and a.color_id in(select id from lib_color where color_name like '%$search_string_for_sales_list%' and status_active=1)";
		}
		else
		{
			$search_field_cond="";
		}


		$sample_booking_cond='';
		if($issue_purpose==8)
		{
			$sample_booking_cond="and a.booking_without_order=1";
		}
		else if($issue_purpose==4 || $issue_purpose==9)
		{
			$sample_booking_cond="and $null_cond(a.booking_without_order,0)!=1";
		}

		$po_arr=array();$batch_arr=array();
		if(($search_by==3 || $search_by==4 || $search_by==5 || $search_by==6) && $search_string_for_sales_list!="")
		{
			$po_data= sql_select(" select a.id, a.po_number,a.file_no,a.grouping as ref, a.job_no_mst, b.booking_no, c.style_ref_no from wo_po_break_down a,wo_booking_dtls b , wo_po_details_master c where a.id =b.po_break_down_id and a.job_no_mst = c.job_no and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $file_cond $ref_cond $style_ref_cond $job_no_cond");

			foreach ($po_data as $row)
			{
				$booking_array[$row[csf("booking_no")]] = $row[csf("booking_no")];
			}


			$booking_array = array_filter($booking_array);
			$all_booking_nos = "'".implode("','", $booking_array)."'";
			$bookCond=""; $all_booking_no_cond="";
			if(count($booking_array)>0)
			{
				if($db_type==2 && count($booking_array)>999)
				{
					$all_booking_no_arr = explode(",", $all_booking_nos);
					$booking_array_chunk=array_chunk($all_booking_no_arr,999) ;
					foreach($booking_array_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$bookCond.="  a.booking_no in($chunk_arr_value) or ";
					}

					$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
				}
				else
				{
					$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
				}

				$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, d.booking_without_order, a.re_dyeing_from, a.sales_order_no $select_order, d.po_job_no
				from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c , fabric_sales_order_mst d
				where a.id=b.batch_id and b.mst_id=c.id and a.sales_order_id = d.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id=$store_id $all_booking_no_cond $search_field_cond $booking_no_cond and b.receive_qnty>0 and c.entry_form = 225 and b.is_sales =1
				group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, d.booking_without_order, a.re_dyeing_from , a.sales_order_no, d.po_job_no order by  a.booking_no";

			}
		}
		else
		{
			$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a.booking_no, a.color_id, a.batch_against, d.booking_without_order, a.re_dyeing_from, a.sales_order_no $select_order, d.po_job_no
			from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c , fabric_sales_order_mst d
			where a.id=b.batch_id and b.mst_id=c.id and a.sales_order_id = d.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id=$store_id $search_field_cond $booking_no_cond and b.receive_qnty>0 and c.entry_form = 225 and b.is_sales =1
			group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight,a. booking_no, a.color_id, a.batch_against, d.booking_without_order, a.re_dyeing_from , a.sales_order_no, d.po_job_no order by  a.booking_no";
		}

		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$nameArray=sql_select( $sql );
		if(empty($nameArray))
		{
			echo "<div style='color:red;font-weight:bold'>Data Not Found. </div>";
			die;
		}

		foreach($nameArray as $selectResult)
		{
			$job_no_arr[$selectResult[csf('po_job_no')]] = $selectResult[csf('po_job_no')];
		}

		$job_no_arr = array_filter(array_unique($job_no_arr));
		$all_job_no = "'".implode("','", $job_no_arr)."'";
		$job_no_arr = explode(",",$all_job_no);

		$jobCond=""; $all_job_no_cond="";
		if(count($job_no_arr)>0)
		{
			if($db_type==2 && count($job_no_arr)>999)
			{
				$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
				foreach($job_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$jobCond.="  a.job_no_mst in($chunk_arr_value) or ";
				}

				$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
			}
			else
			{
				$all_job_no_cond=" and a.job_no_mst in(".$all_job_no.")";
			}

			$job_ref = sql_select("select a.id, a.po_number,a.file_no,a.grouping as ref, a.job_no_mst, b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst = b.job_no $all_job_no_cond and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0");

			foreach ($job_ref as $row)
			{
				$job_ref[$row[csf('job_no_mst')]]['file'] .=$row[csf('file_no')].",";
				$job_ref[$row[csf('job_no_mst')]]['ref']=$row[csf('ref')].",";
				$job_ref[$row[csf('job_no_mst')]]['po_no'] .=$row[csf('po_number')].",";
				$job_ref[$row[csf('job_no_mst')]]['style']=$row[csf('style_ref_no')];
				$job_ref[$row[csf('job_no_mst')]]['job_no']=$row[csf('job_no_mst')];

			}
		}

		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="95">Batch No</th>
					<th width="75">Extention No</th>
					<th width="80">Batch Date</th>
					<th width="90">Batch Qnty</th>
					<th width="110">Booking No</th>
					<th width="120">Job No.</th>
					<th width="100">Color</th>
					<th width="120">Style Ref.</th>
					<th width="170">PO No</th>
					<th width="80">File No</th>
					<th width="90">Ref. No</th>
				</thead>
			</table>
			<div style="width:1070px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search" >
					<?
					$i=1;$b_sl=0;

					foreach($nameArray as $selectResult)
					{
						$po_no='';$po_file='';$po_ref='';$style_ref='';$job_no='';

						if(!in_array($selectResult[csf('booking_no')],$batch_check_arr))
						{
							$b_sl++;
							if ($b_sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$batch_check_arr[]=$selectResult[csf('booking_no')];
						}



					$style_ref_nos =$po_numbers=$file_nos=$ref_nos= "";
					$job_arr = explode(",", $selectResult[csf('po_job_no')]);
					foreach ($job_arr as $value)
					{
						$style_ref_nos .= $job_ref[$value]['style'].",";

						$po_numbers .= $job_ref[$value]['po_no'].",";
						$file_nos .= $job_ref[$value]['file'].",";
						$ref_nos .= $job_ref[$value]['ref'].",";
					}
					$style_ref_nos = chop($style_ref_nos,",");
					$po_numbers = chop($po_numbers,",");
					$file_nos = chop($file_nos,",");
					$ref_nos = chop($ref_nos,",");


					//$style_refs=implode(",",array_unique(explode(",", $style_ref)));
					//$job_no=implode(",",array_unique(explode(",", $job_no)));

					?>
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $selectResult[csf('booking_without_order')]; ?>','','<? echo $selectResult[csf('booking_no')]; ?>','<? echo $i; ?>')">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="95"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
						<td width="75"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
						<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
						<td width="90" align="right"><? echo number_format($selectResult[csf('batch_weight')],0); ?>&nbsp;</td>
						<td width="110"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $selectResult[csf('po_job_no')];//$job_no; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
						<td width="120"><div style="word-break:break-all"><? echo $style_ref_nos;//$style_refs; ?>&nbsp;</div></td>
						<td width="170"><p><? echo $po_numbers; //$po_no; ?>&nbsp;</p></td>
						<td width="80"><p><? echo implode(",",array_unique(explode(",",$file_nos))); ?>&nbsp;</p></td>
						<td width="90"><p><? echo implode(",",array_unique(explode(",",$ref_nos))); ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr>
					<td  colspan="12" align="center">
						<div style="width:100%;" align="center">
							<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?

	exit();

	}
	*/

	if($action=="load_drop_down_fabric_desc")
	{
		$data=explode("**",$data);
		$batch_id=$data[0];
		$selected_id=$data[1];
		$fab_description=array();

		$sql="select a.id, a.product_name_details from product_details_master a, pro_finish_fabric_rcv_dtls b where a.id=b.prod_id and b.batch_id='$batch_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";

		echo create_drop_down( "txt_fabric_desc", 310, $sql,'id,product_name_details', 1, "-- Select Fabric Description --",$selected_id,'','');
		exit();

	}

	/*if($action=='show_fabric_desc_listview')
	{
		$issue_qty_array=array();
		$issData = sql_select("select a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade, a.floor_id,(case when a.rack is null or a.rack=0 then 0 else a.rack end) rack,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no in(".$data.") and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade,a.floor_id,a.room,a.rack, a.self");

		foreach($issData as $row)
		{
			$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('issue_qnty')];
		}

		$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name" );

		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in ($data) and status_active=1 and is_deleted=0 ","id","batch_no");
		$non_booking_array=array();
		$sql_batch="select a.booking_no,a.id,a.booking_without_order,b.body_part,b.sample_type from pro_batch_create_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($data)";
		$batchData=sql_select($sql_batch);
		foreach($batchData as $row)
		{
			$batch_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_array[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$non_booking_array[$row[csf('id')]][$row[csf('booking_no')]][$row[csf('body_part')]]['sample_type']=$row[csf('sample_type')];
		}
		$with_booking_array=array();
		$sql_batch_book="select a.booking_no,a.id,a.booking_without_order,c.body_part_id,b.sample_type from pro_batch_create_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_type in(1,4,3) and a.status_active=1 and a.is_deleted=0 and a.id  in($data) group by a.booking_no,a.id,a.booking_without_order,c.body_part_id,b.sample_type";
		$batchData_result=sql_select($sql_batch_book);
		foreach($batchData_result as $row)
		{
			$batch_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_array[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$with_booking_array[$row[csf('id')]][$row[csf('booking_no')]][$row[csf('body_part_id')]]['sample_type']=$row[csf('sample_type')];
		}

		$recvRt_qty_array=array(); $issRt_qty_array=array();
		$receiveReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id,b.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum(case when a.transaction_type=3 then a.cons_quantity end) as recvrqnty from inv_transaction a,inv_finish_fabric_issue_dtls b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(3,4) and a.batch_id_from_fissuertn in($data) and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.fabric_shade,a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self");

		foreach($receiveReturnData as $row)
		{
			$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('recvrqnty')];
		}

		$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_finish_fabric_rcv_dtls b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(4) and a.batch_id_from_fissuertn in($data)  and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.fabric_shade,a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self");
		foreach($issueReturnData as $row)
		{
			$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
			$room = ($row[csf('room')]=="")?0:$row[csf('room')];
			$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
			$self = ($row[csf('self')]=="")?0:$row[csf('self')];
			$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self]=$row[csf('issrqnty')];
		}

		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


		$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock,x.company_id,x.store_id,x.floor,x.fabric_shade,x.body_part_id, x.batch_id,x.room,x.rack_no,x.shelf_no,x.cons_rate,x.gmt_item_id,x.qnty,x.prod_id from (select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,b.floor,b.fabric_shade,b.body_part_id, b.batch_id,(case when b.room is null or b.room=0 then 0 else b.room end) room,(case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,sum(b.receive_qnty) as qnty,d.gmt_item_id,b.prod_id,(case when d.cons_rate is null or d.cons_rate=0 then 0 else d.cons_rate end) cons_rate from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id in($data) and c.entry_form in (7,37) and a.item_category_id=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,b.floor,b.fabric_shade,b.body_part_id, b.batch_id,b.room,b.rack_no,b.shelf_no,d.cons_rate,d.gmt_item_id,b.prod_id) x group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock,x.company_id,x.store_id,x.floor,x.fabric_shade,x.body_part_id, x.batch_id,x.room,x.rack_no,x.shelf_no,x.cons_rate,x.gmt_item_id,x.qnty,x.prod_id";
		$data_array=sql_select($data_sql);
		?>
		<fieldset>
			<legend>Frabric Description List</legend>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820" style="overflow-x:auto;">
				<thead>
					<th width="20">SL</th>
					<th width="35">Prod. ID</th>
					<th width="80">Batch No</th>
					<th width="80">Fabric Color</th>
					<th width="50">Shade</th>
					<th width="120">Fabric Description</th>
					<th width="70">Sample</th>
					<th width="50">UOM</th>
					<th width="80">Floor</th>
					<th width="60">Room</th>
					<th width="45">Rack</th>
					<th width="45">Shelf</th>
					<th width="60">Recv. Qty</th>
					<th width="60">Issue Qty</th>
					<th>Balance</th>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($data_array as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$iss_qnty=$issue_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];
						$recvRt_qnty=$recvRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];
						$issRt_qnty=$issRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];
						$cum_recv_qty=($row[csf('qnty')]-$recvRt_qnty);
						$cum_iss_qty=$iss_qnty-$issRt_qnty;

						$balance=$cum_recv_qty-$cum_iss_qty;
						$booking_no=$batch_array[$row[csf('batch_id')]]['booking_no'];
						$booking_without_order=$batch_array[$row[csf('batch_id')]]['booking_without_order'];

						if($booking_without_order==0)
						{
							$sample_type=$with_booking_array[$row[csf('batch_id')]][$booking_no][$row[csf('body_part_id')]]['sample_type'];
						}
						else
						{
							$sample_type=$non_booking_array[$row[csf('batch_id')]][$booking_no][$row[csf('body_part_id')]]['sample_type'];
						}

						$store_id=$row[csf('store_id')];
						$company_id=$row[csf('company_id')];
						$floor_id=$row[csf('floor')];
						$room_id=$row[csf('room')];
						$rack_id=$row[csf('rack_no')];
						$shelf_id=$row[csf('shelf_no')];

						$floor_name=return_field_value("a.floor_room_rack_name","lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","a.floor_room_rack_id=b.floor_id and b.store_id=$store_id and b.floor_id=$floor_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_room_rack_name");
						$room_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id=$room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
						$rack_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.rack_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id=$rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
						$shelf_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.shelf_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and b.shelf_id=$shelf_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('current_stock')]."**".$color_arr[$row[csf('color')]]."**".$row[csf('unit_of_measure')]."**".$row[csf('fabric_shade')]."**".$sample_type."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name."**".$row[csf('floor')]."**".$row[csf('room')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$row[csf('body_part_id')]."**".$row[csf('cons_rate')]; ?>")' style="cursor:pointer" >
							<td><? echo $i; ?></td>
							<td title="<? echo $booking_without_order.'='.$booking_no.'='.$sample_type; ?>"><p><? echo $row[csf('id')]; ?></p></td>
							<td><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
							<td><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
							<td><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td><p><? echo $sample_library[$sample_type]; ?></p></td>
							<td align="center"><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?>&nbsp;</p></td>
							<td><p><? echo $floor_name; ?></p></td>
							<td><p><? echo $room_name; ?></p></td>
							<td><p><? echo $rack_name; ?></p></td>
							<td><p><? echo $shelf_name; ?></p></td>
							<td align="right"><? echo number_format($cum_recv_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($cum_iss_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($balance,2,'.',''); ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?
		exit();
	}*/

	if($action=='show_fabric_desc_listview')
	{
		$data =explode("**", $data);
		$batch_id = $data[0];
		$company_id = $data[1];
		$store_id = $data[2];
		$booking_no = $data[3];
		if ($store_id!=""){$store_cond="and a.store_id in(".$store_id.")";} else{$store_cond="";}
		$issue_qty_array=array();

		$issData = sql_select("select a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade,c.body_part_id,(case when a.floor_id is null or a.rack=0 then 0 else a.floor_id end) floor_id,(case when a.rack is null or a.rack=0 then 0 else a.rack end) rack,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c, pro_batch_create_mst d where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no = d.id and d.booking_no = '$booking_no' and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id $store_cond  group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade, c.body_part_id,a.floor_id, a.room, a.rack, a.self");
		//and a.pi_wo_batch_no in(".$batch_id.")

		foreach($issData as $row)
		{
			$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('issue_qnty')];
		}

		$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name" );

		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where booking_no = '$booking_no' and status_active=1 and is_deleted=0 ","id","batch_no");
		//id in ($batch_id)

		$non_booking_array=array();
		$sql_batch="select a.booking_no,a.id,a.booking_without_order,b.body_part,b.sample_type from pro_batch_create_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no'"; //a.id in($batch_id)
		$batchData=sql_select($sql_batch);
		foreach($batchData as $row)
		{
			$batch_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_array[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$non_booking_array[$row[csf('id')]][$row[csf('booking_no')]][$row[csf('body_part')]]['sample_type']=$row[csf('sample_type')];
		}
		$with_booking_array=array();
		$sql_batch_book="select a.booking_no,a.id,a.booking_without_order,c.body_part_id,b.sample_type from pro_batch_create_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_type in(1,4,3) and a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.id,a.booking_without_order,c.body_part_id,b.sample_type";
		// a.id  in($batch_id)
		$batchData_result=sql_select($sql_batch_book);
		foreach($batchData_result as $row)
		{
			$batch_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_array[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$with_booking_array[$row[csf('id')]][$row[csf('booking_no')]][$row[csf('body_part_id')]]['sample_type']=$row[csf('sample_type')];
		}

		$recvRt_qty_array=array(); $issRt_qty_array=array();
		$receiveReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id,b.fabric_shade, b.body_part_id, a.floor_id,a.room, a.rack, a.self, sum(case when a.transaction_type=3 then a.cons_quantity end) as recvrqnty from inv_transaction a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(3) and a.batch_id_from_fissuertn = c.id and c.booking_no = '$booking_no' and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id =$store_id group by a.prod_id, b.fabric_shade, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self");
		//and a.batch_id_from_fissuertn in($batch_id)

		foreach($receiveReturnData as $row)
		{
			$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('recvrqnty')];
		}

		$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.fabric_shade, b.body_part_id,  a.floor_id,a.room, a.rack, a.self, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(4) and a.batch_id_from_fissuertn = c.id and c.booking_no = '$booking_no' and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id =$store_id group by a.prod_id, b.fabric_shade, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self");
		//and a.batch_id_from_fissuertn in($batch_id)
		foreach($issueReturnData as $row)
		{
			$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
			$room = ($row[csf('room')]=="")?0:$row[csf('room')];
			$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
			$self = ($row[csf('self')]=="")?0:$row[csf('self')];
			$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self]=$row[csf('issrqnty')];
		}


		$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor, b.fabric_shade,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, (case when b.rack is null or b.rack=0 then 0 else b.rack end) rack_no,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no, sum(b.transfer_qnty) as trans_out_qnty,  b.from_prod_id as prod_id
			from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
			where  c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $company_id and b.from_store =$store_id and c.transaction_type = 6 and c.item_category = 2 and b.batch_id = a.id and a.booking_no = '$booking_no' and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form=14 and d.trans_type=6 and b.active_dtls_id_in_transfer = 1
			group by b.batch_id, b.from_store, b.floor_id, b.fabric_shade, c.body_part_id, b.room,b.rack,b.shelf,b.from_prod_id");
		//and b.batch_id in ($batch_id)

		foreach($transOutData as $row)
		{
			$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]]=$row[csf('trans_out_qnty')];
		}

		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

		$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id, x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id,x.room,x.rack_no, x.shelf_no,sum(x.cons_amount) as cons_amount,x.gmt_item_id,sum(x.qnty) as qnty, x.prod_id,x.detarmination_id
		from
		(
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,b.floor,b.fabric_shade, b.body_part_id, b.batch_id,(case when b.room is null or b.room=0 then 0 else b.room end) room,(case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,sum(d.cons_quantity) as qnty,d.gmt_item_id,b.prod_id, sum(d.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and e.booking_no = '$booking_no' and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (7,37) and a.item_category_id=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.floor, b.fabric_shade,b.body_part_id, b.batch_id, b.room, b.rack_no, b.shelf_no, d.gmt_item_id, b.prod_id, a.detarmination_id
		union all
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.to_floor_id as floor, b.fabric_shade, c.body_part_id, b.to_batch_id as batch_id,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no, sum(c.cons_quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id, sum(c.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 2  and b.to_batch_id = e.id and e.booking_no = '$booking_no' and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0
		group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.to_floor_id, b.fabric_shade, c.body_part_id, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf, b.to_prod_id, a.detarmination_id
		) x
		group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id, x.room,x.rack_no, x.shelf_no, x.gmt_item_id, x.prod_id, x.detarmination_id";

		//and b.batch_id in($batch_id)
		//and b.to_batch_id in ($batch_id)

		$data_array=sql_select($data_sql);
		?>
		<fieldset>
			<legend>Frabric Description List</legend>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840">
				<thead>
					<th width="20">SL</th>
					<th width="35">Prod. ID</th>
					<th width="80">Batch No</th>
					<th width="80">Fabric Color</th>
					<th width="50">Shade</th>
					<th width="120">Fabric Description</th>
					<th width="70">Sample</th>
					<th width="50">UOM</th>
					<th width="80">Floor</th>
					<th width="60">Room</th>
					<th width="45">Rack</th>
					<th width="45">Shelf</th>
					<th width="60">Recv. Qty</th>
					<th width="60">Issue Qty</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:860px; max-height:250px; overflow-y:scroll;overflow-x:auto;"  >	
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" id="table_body">
				<tbody>
					<?
					$i=1;
					foreach($data_array as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$iss_qnty=$issue_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];
						$recvRt_qnty=$recvRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];
						$issRt_qnty=$issRt_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];

						$trans_out_qnty=$trans_out_qnty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]];


						$cum_recv_qty=($row[csf('qnty')]-$recvRt_qnty);
						$cum_recv_qty_title="(Receive - Receive Return + Transfer In)\nReceive=".$row[csf('qnty')]."\nReceive Return=".$recvRt_qnty;

						$cum_iss_qty=$iss_qnty-$issRt_qnty + $trans_out_qnty;
						$cum_iss_qty_title="(Issue - Issue Return + Transfer Out)\nIssue=".$iss_qnty."\nIssue Return=".$issRt_qnty . "\nTransfer Out=" . $trans_out_qnty;

						$balance= number_format($cum_recv_qty,2,".","")-number_format($cum_iss_qty,2,".","");
						$booking_no=$batch_array[$row[csf('batch_id')]]['booking_no'];
						$booking_without_order=$batch_array[$row[csf('batch_id')]]['booking_without_order'];

						if($booking_without_order==0)
						{
							$sample_type=$with_booking_array[$row[csf('batch_id')]][$booking_no][$row[csf('body_part_id')]]['sample_type'];
						}
						else
						{
							$sample_type=$non_booking_array[$row[csf('batch_id')]][$booking_no][$row[csf('body_part_id')]]['sample_type'];
						}

						$store_id=$row[csf('store_id')];
						$company_id=$row[csf('company_id')];
						$floor_id=$row[csf('floor')];
						$room_id=$row[csf('room')];
						$rack_id=$row[csf('rack_no')];
						$shelf_id=$row[csf('shelf_no')];

						$floor_name=return_field_value("a.floor_room_rack_name","lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","a.floor_room_rack_id=b.floor_id and b.store_id=$store_id and b.floor_id=$floor_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_room_rack_name");
						$room_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id=$room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
						$rack_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.rack_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id=$rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
						$shelf_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.shelf_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and b.shelf_id=$shelf_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");

						$cons_rate = $row[csf('cons_amount')]/$row[csf('qnty')];
						$cons_rate = number_format($cons_rate,2,".","");
						if($balance>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('current_stock')]."**".$color_arr[$row[csf('color')]]."**".$row[csf('unit_of_measure')]."**".$row[csf('fabric_shade')]."**".$sample_type."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name."**".$row[csf('floor')]."**".$row[csf('room')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$row[csf('body_part_id')]."**".$row[csf('cons_rate')]."**".$row[csf('detarmination_id')]; ?>")' style="cursor:pointer" >
								<td width="20"><? echo $i; ?></td>
								<td width="35" title="<? echo $booking_without_order.'='.$booking_no.'='.$sample_type; ?>"><p><? echo $row[csf('id')]; ?></p></td>
								<td width="80"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
								<td width="80"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
								<td width="50"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
								<td width="120"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="70"><p><? echo $sample_library[$sample_type]; ?></p></td>
								<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $floor_name; ?></p></td>
								<td width="60"><p><? echo $room_name; ?></p></td>
								<td width="45"><p><? echo $rack_name; ?></p></td>
								<td width="45"><p><? echo $shelf_name; ?></p></td>
								<td width="60" align="right" title="<? echo $cum_recv_qty_title; ?>"><? echo number_format($cum_recv_qty,2,'.',''); ?></td>
								<td width="60" align="right" title="<? echo $cum_iss_qty_title; ?>"><? echo number_format($cum_iss_qty,2,'.',''); ?></td>
								<td align="right"><? echo number_format($balance,2,'.',''); ?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		</fieldset>
		<?
		exit();
	}

	if ($action=="fabricDescription_popup")
	{
		echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		?>

		<script>

			var product_id='<? echo $hidden_prod_id; ?>'; product_details='<? echo $txt_fabric_desc; ?>';

			function check_product_duplication(tr_id)
			{
				var prod_id=$('#txt_prod_id'+tr_id).val();
				var prod_details=$('#txt_prod_details'+tr_id).val();

				var tot_row=$("#tbl_list_search tr").length;

				for(var i=1; i<=tot_row; i++)
				{
					var issueQnty=$('#txt_issue_qnty_'+i).val();

					if(issueQnty*1>0)
					{
						if($("#txt_prod_id"+i).val()!=prod_id)
						{
							alert("Product Mix Not Allow.");
							$('#txt_issue_qnty_'+tr_id).val('');
							return;
						}
					}
				}

				product_id=prod_id;
				product_details=prod_details;

				var issue_qnty=$('#txt_issue_qnty_'+tr_id).val()*1;
				if(issue_qnty>0)
				{
					$('#search' + tr_id).css('background-color','yellow');
				}
				else
				{
					$('#search' + tr_id).css('background-color','#FFFFCC');
				}
			}

			function fnc_close()
			{
				var save_string='';	 var hidden_roll_issue_qnty=''; var no_of_roll='';
				var tot_row=$("#tbl_list_search tr").length;

				for(var i=1; i<=tot_row; i++)
				{
					var RollId=$('#txt_individual_id'+i).val();
					var RollNo=$('#txt_individual'+i).val();
					var issueQnty=$('#txt_issue_qnty_'+i).val();
					var txt_po_id=$('#txt_po_id_'+i).val();

					if(issueQnty*1>0)
					{
						if(save_string=="")
						{
							save_string=RollId+"_"+RollNo+"_"+issueQnty+"_"+txt_po_id;
						}
						else
						{
							save_string+=","+RollId+"_"+RollNo+"_"+issueQnty+"_"+txt_po_id;
						}

						if(RollNo*1>0)
						{
							no_of_roll=no_of_roll*1+1;
						}

						hidden_roll_issue_qnty=hidden_roll_issue_qnty*1+issueQnty*1;
					}
				}

				$('#save_string').val( save_string );
				$('#hidden_roll_issue_qnty').val( hidden_roll_issue_qnty );
				$('#number_of_roll').val( no_of_roll );
				$('#product_id').val(product_id);
				$('#product_details').val(product_details);

				parent.emailwindow.hide();
			}

		</script>

	</head>

	<body>
		<div align="center" style="width:780px;">

			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:770px;margin-left:10px">
					<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
					<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
					<input type="hidden" name="hidden_roll_issue_qnty" id="hidden_roll_issue_qnty" class="text_boxes" value="">
					<input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
					<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
					<input type="hidden" name="product_details" id="product_details" class="text_boxes" value="">

					<div style="margin-top:10px" id="search_div">
						<?
						$search_field="d.batch_id";
						$search_string=$hidden_batch_id;
						$sql="select b.id as po_id, b.po_number, c.id, c.roll_no, c.qnty, d.prod_id, e.product_name_details from wo_po_details_master a, wo_po_break_down b, pro_roll_details c, pro_finish_fabric_rcv_dtls d, product_details_master e where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form=4 and e.item_category_id=2 and c.dtls_id=d.id and d.prod_id=e.id and c.roll_no!=0 and e.company_id=$cbo_company_id and $search_field like '$search_string'";
						$result = sql_select($sql);
						?>
						<div>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" >
								<thead>
									<th width="40">SL</th>
									<th width="120">Order No</th>
									<th width="280">Fabric Description</th>
									<th width="80">Roll No</th>
									<th width="110">Roll Qnty</th>
									<th>Issue Qnty</th>
								</thead>
							</table>
							<div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
									<?
									$issue_qnty_array=array();
									$save_string=explode(",",$save_string);

									for($i=0;$i<count($save_string);$i++)
									{
										$roll_wise_data=explode("_",$save_string[$i]);
										$roll_id=$roll_wise_data[0];
										$roll_issue_qnty=$roll_wise_data[2];
										$issue_qnty_array[$roll_id]=$roll_issue_qnty;
									}

									$i=1;
									foreach($result as $row)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";

										$roll_issue_qnty=$issue_qnty_array[$row[csf('id')]];

										if($roll_issue_qnty>0) $bgcolor="yellow"; else $bgcolor=$bgcolor;

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>">
											<td width="40" align="center"><? echo $i; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
											<input type="hidden" id="txt_prod_id<?php echo $i ?>" value="<? echo $row[csf('prod_id')]; ?>" />
											<input type="hidden" id="txt_prod_details<?php echo $i ?>" value="<? echo $row[csf('product_name_details')]; ?>" />
											<input type="hidden" id="txt_po_id_<?php echo $i ?>" value="<? echo $row[csf('po_id')]; ?>" />
										</td>
										<td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
										<td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
										<td width="80" align="right"><p><? echo $row[csf('roll_no')]; ?></p></td>
										<td width="110" align="right"><p><? echo $row[csf('qnty')]; ?></p></td>
										<td align="center">
											<input type="text" name="txt_issue_qnty[]" id="txt_issue_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $roll_issue_qnty; ?>" onKeyUp="check_product_duplication(<? echo $i; ?>);"/>
										</td>
									</tr>
									<?
									$i++;
								}
								?>
							</table>
						</div>
					</div>
				</div>
				<table width="750">
					<tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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

if($action=='create_product_search_list_view')
{
	$data = explode("**",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	if($data[2]==0) $buyer_name="%%"; else $buyer_name=$data[2];
	$company_id=$data[3];
	$hidden_prod_id=$data[4];
	$save_string=$data[5];

	if($search_by==1)
		$search_field="b.po_number";
	else if($search_by==1)
		$search_field="a.job_no";
	else
		$search_field="a.style_ref_no";

	$sql="select b.id as po_id, b.po_number, c.id, c.roll_no, c.qnty, d.prod_id, e.product_name_details from wo_po_details_master a, wo_po_break_down b, pro_roll_details c,  pro_finish_fabric_rcv_dtls d, product_details_master e where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form=4 and c.dtls_id=d.id and d.prod_id=e.id and c.roll_no!=0 and e.company_id=$company_id and $search_field like '$search_string'";
	$result = sql_select($sql);
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="120">Order No</th>
				<th width="280">Fabric Description</th>
				<th width="80">Roll No</th>
				<th width="110">Roll Qnty</th>
				<th>Issue Qnty</th>
			</thead>
		</table>
		<div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
				<?
				$issue_qnty_array=array();
				$save_string=explode(",",$save_string);

				for($i=0;$i<count($save_string);$i++)
				{
					$roll_wise_data=explode("_",$save_string[$i]);
					$roll_id=$roll_wise_data[0];
					$roll_issue_qnty=$roll_wise_data[2];
					$issue_qnty_array[$roll_id]=$roll_issue_qnty;
				}

				$i=1;
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$roll_issue_qnty=$issue_qnty_array[$row[csf('id')]];

					if($roll_issue_qnty>0) $bgcolor="yellow"; else $bgcolor=$bgcolor;

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>">
						<td width="40" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
						<input type="hidden" id="txt_prod_id<?php echo $i ?>" value="<? echo $row[csf('prod_id')]; ?>" />
						<input type="hidden" id="txt_prod_details<?php echo $i ?>" value="<? echo $row[csf('product_name_details')]; ?>" />
						<input type="hidden" id="txt_po_id_<?php echo $i ?>" value="<? echo $row[csf('po_id')]; ?>" />
					</td>
					<td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
					<td width="80" align="right"><p><? echo $row[csf('roll_no')]; ?></p></td>
					<td width="110" align="right"><p><? echo $row[csf('qnty')]; ?></p></td>
					<td align="center">
						<input type="text" name="txt_issue_qnty[]" id="txt_issue_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $roll_issue_qnty; ?>" onKeyUp="check_product_duplication(<? echo $i; ?>);"/>
					</td>
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

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	if($dtls_tbl_id!="")
	{
		$hide_qty_array=return_library_array( "select po_breakdown_id, quantity from order_wise_pro_details where dtls_id=$dtls_tbl_id and entry_form=18 and status_active=1 and is_deleted=0",'po_breakdown_id','quantity');
	}
	?>
	<script>

		var roll_maintained='<? echo $roll_maintained; ?>';
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var balance =txt_prop_issue_qnty;
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

				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;

					var placeholder_value =$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder')*1+$(this).find('input[name="hideQnty[]"]').val()*1;

					if(balance>0)
					{
						if(placeholder_value<0) placeholder_value=0;
						if(balance>placeholder_value)
						{
							var issue_qnty=placeholder_value;
							balance=balance-placeholder_value;
						}
						else
						{
							var issue_qnty=balance;
							balance=0;
						}

						if(tblRow==len)
						{
							var issue_qnty=txt_prop_issue_qnty-totalIssue;
						}

						totalIssue = totalIssue*1+issue_qnty*1;

						$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
					}
					else
					{
						$(this).find('input[name="txtIssueQnty[]"]').val('');
					}
				});
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

		function check_balance(row_no)
		{
			var placeholder_value =$('#txtIssueQnty_'+row_no).attr('placeholder')*1;
			var issued_qnty =$('#hideQnty_'+ row_no).val()*1;
			var qnty =$('#txtIssueQnty_'+row_no).val()*1;

			if(qnty>(placeholder_value+issued_qnty))
			{
				alert("Issue Qty Exceeds Balance Qty.");
				if(issued_qnty==0) issued_qnty='';
				$('#txtIssueQnty_'+row_no).val(issued_qnty);
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
		<fieldset style="width:850px;margin-left:10px">
			<input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
			<input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="">
			<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
			<input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
			<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
			<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
			<div style="width:820px; margin-top:10px" align="center">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
					<thead>
						<th>Total Issue Qnty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" value="<? echo $txt_issue_req_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
						<td>
							<?
							$distribiution_method=array(1=>"Distribute Based On Lowest Shipment Date",2=>"Manually");
							echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0,"",$prev_distribution_method, "distribute_qnty(this.value);",0 );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-left:10px; margin-top:10px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800">
					<thead>
						<th width="150">PO No</th>
						<th width="70">File No</th>
						<th width="70">Ref No</th>
						<th width="80">Shipment Date</th>
						<th width="80">PO Qty</th>
						<th width="80">Req. Qty</th>
						<th width="80">Recv. Qty</th>
						<th width="80">Cumu. Issue Qty</th>
						<th>Issue Qnty</th>
					</thead>
				</table>
				<div style="width:820px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" id="tbl_list_search">
						<?
						$batch_booking_sql=sql_select("select booking_no from pro_batch_create_mst where id='$batch_id' and status_active=1 and is_deleted=0 group by booking_no");
						$batch_booking=$batch_booking_sql[0][csf('booking_no')];
						$req_qty_array=array();
						if($batch_booking!='')
						{
							$reqQnty = "select a.po_break_down_id,sum(fin_fab_qnty) as fabric_qty FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.booking_no ='$batch_booking' and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=b.id and a.is_deleted=0 and b.uom=$cbouom group by po_break_down_id";


							$reqQnty_res = sql_select($reqQnty);
							foreach($reqQnty_res as $req_val)
							{
								$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
							}
						}
						else{
								// N.B: if fabrication changed in booking then Batch quantity is considered as fabric quantity
								$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$batch_id and c.detarmination_id=$hidden_detarmination_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 group by b.po_id");

								foreach($batch_qnty as $req_val)
								{
									$req_qty_array[$req_val[csf("po_id")]]=$req_val[csf("batch_qnty")];
								}
							}

						$all_batch_po_sql=sql_select("select o.po_breakdown_id from pro_finish_fabric_rcv_dtls b,order_wise_pro_details o where b.id=o.dtls_id and b.batch_id='$batch_id' and o.entry_form in (7,37) and o.trans_id>0 and b.status_active=1 and b.is_deleted=0 and o.status_active=1 and o.is_deleted=0  ");
						foreach($all_batch_po_sql as $p_val)
						{
							$batch_po_arr[$p_val[csf('po_breakdown_id')]]=$p_val[csf('po_breakdown_id')];
						}

						$batch_po_id=implode(',',$batch_po_arr);
						$cumu_rec_qty=array(); $cumu_iss_qty=array();
						if($batch_po_id!='')
						{
							$store_cond = ($cbo_store_name!="") ? "and a.store_id='$cbo_store_name'":"";
							$floor_cond = ($txt_floor!="" && $txt_floor!=0) ? "and a.floor_id='$txt_floor'":"";
							$room_cond 	= ($txt_room!="" && $txt_room!=0) ? "and a.room='$txt_room'":"";
							$rack_cond 	= ($txt_rack!="" && $txt_rack!=0) ? "and a.rack='$txt_rack'":"";
							$shelf_cond = ($txt_shelf!="" && $txt_shelf!=0) ? "and a.self='$txt_shelf'":"";
							$shade_cond = ($fabric_shade!="" && $fabric_shade!=0) ? "and a.fabric_shade='$fabric_shade'":"and a.fabric_shade=0";

							$sql_cuml="select b.po_breakdown_id,a.fabric_shade,
							sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_recv,
							sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_issue,
							sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
							sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS iss_retn_qnty,
							sum(case when b.entry_form in(14,15) and b.trans_type=5 and a.transaction_type=5 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_recv,
							sum(case when b.entry_form in(14,15) and b.trans_type=6 and a.transaction_type=6 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_issued
							from inv_transaction a, order_wise_pro_details b
							where a.id=b.trans_id and b.po_breakdown_id in($batch_po_id) and a.prod_id=$prod_id and b.prod_id=$prod_id $floor_cond $room_cond $rack_cond $shelf_cond $shade_cond $store_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id,a.fabric_shade";

							$sql_result_cuml=sql_select($sql_cuml);
							foreach($sql_result_cuml as $row)
							{
								$cumu_rec_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_recv')]+$row[csf('finish_fabric_trans_recv')])-$row[csf('recv_rtn_qnty')];
								$cumu_iss_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_issue')]+$row[csf('finish_fabric_trans_issued')])-$row[csf('iss_retn_qnty')];
							}
						}

						$i=1; $tot_po_qnty=0; $finish_qnty_array=array(); $po_array=array();
						$explSaveData = explode(",",$save_data);

						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_id 	  = $po_wise_data[0];
							$finish_qnty  = $po_wise_data[1];
							$finish_qnty_array[$order_id] = $finish_qnty;
						}

						$po_sql="select b.id,b.file_no,b.grouping as ref, a.buyer_name, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity from wo_po_details_master a, wo_po_break_down b,pro_finish_fabric_rcv_dtls c,order_wise_pro_details o  where a.job_no=b.job_no_mst and b.id=o.po_breakdown_id  and o.dtls_id=c.id and o.entry_form in (7,37) and c.batch_id='$batch_id' and o.trans_id <> 0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.file_no,b.grouping,a.buyer_name, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity";
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{
							if($cumu_rec_qty[$row[csf('id')]] > 0){
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_qnty_in_pcs = $row[csf('po_quantity')]*$row[csf('total_set_qnty')];
								$tot_po_qnty 	+= $po_qnty_in_pcs;
								$issue_qnty 	= $finish_qnty_array[$row[csf('id')]];
								$hideQnty 		= $hide_qty_array[$row[csf('id')]];

								$po_array[]=$row[csf('id')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="150" title="<? echo $row[csf('id')]; ?>">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
									</td>
									<td width="70"><? echo $row[csf('file_no')]; ?> </td>
									<td width="70"><? echo $row[csf('ref')]; ?> </td>
									<td align="center" width="80"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right">
										<? echo $po_qnty_in_pcs; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
									</td>
									<td width="80" align="right">
										<? echo number_format($req_qty_array[$row[csf('id')]],2,'.',''); ?>
									</td>
									<td width="80" align="right" title="Cumulative Receive = ((Receive+Transfer In)-Receive Return)">
										<? echo number_format($cumu_rec_qty[$row[csf('id')]],2,'.',''); ?>
									</td>
									<td width="80" align="right" title="Cumulative Issue = ((Issue+Transfer Out)- Issue Return)">
										<?
										echo number_format($cumu_iss_qty[$row[csf('id')]],2,'.','');
										$cumul_balance=$cumu_rec_qty[$row[csf('id')]]-$cumu_iss_qty[$row[csf('id')]];
										?>
									</td>
									<td align="center">
										<input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" placeholder="<? echo $cumul_balance; ?>" style="width:80px" value="<? echo $issue_qnty; ?>" onKeyUp="check_balance(<? echo $i; ?>);">
										<input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
									</td>
								</tr>
								<?
								$i++;
							}
						}

						if($db_type==0)
						{
							$trans_po_id=return_field_value("group_concat(distinct(b.to_order_id)) as po_id","inv_item_transfer_mst a, inv_item_transfer_dtls b","a.id=b.mst_id  and b.to_batch_id='$batch_id' and b.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						else
						{
							$trans_po_id=return_field_value("LISTAGG(b.to_order_id, ',') WITHIN GROUP (ORDER BY a.to_order_id) as po_id","inv_item_transfer_mst a, inv_item_transfer_dtls b","a.id=b.mst_id  and b.to_batch_id='$batch_id' and b.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						$trans_po_ids=$trans_po_id;
						$trans_po_id=array_unique(explode(",",$trans_po_id));
						if($trans_po_ids!='')
						{
							$reqQnty = "select a.po_break_down_id,sum(fin_fab_qnty) as fabric_qty FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.po_break_down_id in(".implode(",",$trans_po_id).") and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=b.id and a.is_deleted=0 group by po_break_down_id";


							$reqQnty_res = sql_select($reqQnty);
							foreach($reqQnty_res as $req_val)
							{
								$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
							}
						}
						else{
								// N.B: if fabrication changed in booking then Batch quantity is considered as fabric quantity
								$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$batch_id and c.detarmination_id=$hidden_detarmination_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 group by b.po_id");

								foreach($batch_qnty as $req_val)
								{
									$req_qty_array[$req_val[csf("po_id")]]=$req_val[csf("batch_qnty")];
								}
							}

						$result=implode(",",array_diff($trans_po_id, $po_array));
						if($result!="")
						{

							$sql_cuml="select b.po_breakdown_id,a.fabric_shade,
							sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_recv,
							sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_issue,
							sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
							sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS iss_retn_qnty,
							sum(case when b.entry_form=14 and b.trans_type=5 and a.transaction_type=5 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_recv,
							sum(case when b.entry_form=14 and b.trans_type=6 and a.transaction_type=6 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_issued
							from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id in($result) and a.prod_id=$prod_id and b.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id,a.fabric_shade";
							$sql_result_cuml=sql_select($sql_cuml);
							foreach($sql_result_cuml as $row)
							{
								$cumu_rec_qty[$row[csf('po_breakdown_id')]] +=($row[csf('finish_fabric_recv')]+$row[csf('finish_fabric_trans_recv')])-$row[csf('recv_rtn_qnty')];
								$cumu_iss_qty[$row[csf('po_breakdown_id')]] +=($row[csf('finish_fabric_issue')]+$row[csf('finish_fabric_trans_issued')])-$row[csf('iss_retn_qnty')];
							}

							$po_sql="select b.id, a.buyer_name, b.file_no,b.grouping as ref,b.po_number, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result) order by b.pub_shipment_date, b.id";
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								if($cumu_rec_qty[$row[csf('id')]] > 0){
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
									$issue_qnty=$finish_qnty_array[$row[csf('id')]];
									$hideQnty=$hide_qty_array[$row[csf('id')]];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="150">
											<p><? echo $row[csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
											<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
											<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
										</td>
										<td width="70"><? echo $row[csf('file_no')]; ?></td>
										<td width="70"><? echo $row[csf('ref')]; ?></td>
										<td align="center" width="80"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
										<td width="80" align="right">
											<? echo $row[csf('po_qnty_in_pcs')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
										</td>
										<td width="80" align="right">
											<? echo number_format($req_qty_array[$row[csf('id')]],2,'.',''); ?>
										</td>
										<td width="80" align="right">
											<? echo number_format($cumu_rec_qty[$row[csf('id')]],2,'.',''); ?>
										</td>
										<td width="80" align="right">
											<?
											echo number_format($cumu_iss_qty[$row[csf('id')]],2,'.','');
											$cumul_balance=$cumu_rec_qty[$row[csf('id')]]-$cumu_iss_qty[$row[csf('id')]];
											?>
										</td>
										<td align="center">
											<input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" placeholder="<? echo $cumul_balance; ?>" style="width:80px" value="<? echo $issue_qnty; ?>" onKeyUp="check_balance(<? echo $i; ?>);">
											<input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
										</td>
									</tr>
									<?
									$i++;
								}
							}
						}
						?>
						<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
					</table>
				</div>
				<table width="840">
					<tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
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
	$batch_id=$data[2];
	$body_part_id=$data[3];
	$store_id=$data[4];

	$sql=sql_select("select
		sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_recv,
		sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_issue,
		sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
		sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS iss_retn_qnty,
		sum(case when b.entry_form=14 and b.trans_type=5 and a.transaction_type=5 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_recv,
		sum(case when b.entry_form=14 and b.trans_type=6 and a.transaction_type=6 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_issued
		from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id in($order_id) and a.prod_id=$prod_id and b.prod_id=$prod_id and a.store_id=$store_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");

	$finish_fabric_recv=$sql[0][csf('finish_fabric_recv')]+$sql[0][csf('finish_fabric_trans_recv')]+$sql[0][csf('iss_retn_qnty')];
	$finish_fabric_issued=$sql[0][csf('finish_fabric_issue')]+$sql[0][csf('finish_fabric_trans_issued')]+$sql[0][csf('recv_rtn_qnty')];
	$yet_issue=$finish_fabric_recv-$finish_fabric_issued;

	if($db_type==0)
	{
		$order_nos=return_field_value("group_concat(po_number)","wo_po_break_down","id in($order_id)");
	}
	else
	{
		$order_nos=return_field_value("LISTAGG(cast(po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down","id in($order_id)","po_number");
	}

	echo "$('#txt_order_numbers').val('".$order_nos."');\n";
	echo "$('#txt_fabric_received').val('".number_format($finish_fabric_recv,2)."');\n";
	echo "$('#txt_cumulative_issued').val('".number_format($finish_fabric_issued,2)."');\n";
	echo "$('#txt_yet_to_issue').val('".number_format($yet_issue,2)."');\n";

	$sql_finish_receive=sql_select("select sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and a.trans_id=b.trans_id and b.po_breakdown_id in($order_id) and a.prod_id in($prod_id) and a.body_part_id in($body_part_id) and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	echo "$('#hidden_fabric_rate').val('".number_format( $sql_finish_receive[0][csf('ave_rate')],4)."');\n";
	echo "$('#txt_rate').val('".number_format( $sql_finish_receive[0][csf('ave_rate')],2)."').attr('disabled','disabled');;\n";

	exit();
}

if($action=="populate_data_about_sample")
{
	$data=explode("**",$data);
	$batch_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
	if ($store_id>0) {$store_id_cond="and store_id=$store_id";}else{$store_id_cond="";}

	$sql=sql_select("select
		sum(case when transaction_type=1 and pi_wo_batch_no=$batch_id then cons_quantity end) as finish_fabric_recv,
		sum(case when transaction_type=3 and batch_id_from_fissuertn=$batch_id then cons_quantity end) as finish_fabric_recv_return,
		sum(case when transaction_type=2 and pi_wo_batch_no=$batch_id then cons_quantity end) as finish_fabric_issue,
		sum(case when transaction_type=4 and batch_id_from_fissuertn=$batch_id then cons_quantity end) as finish_fabric_iss_return,
		sum(case when transaction_type=5 and pi_wo_batch_no=$batch_id then cons_quantity end) as trans_in,
		sum(case when transaction_type=6 and pi_wo_batch_no=$batch_id then cons_quantity end) as trans_out,
		store_id
		from inv_transaction where prod_id=$prod_id and item_category=2 $store_id_cond  and is_deleted=0 and status_active=1 group by store_id ");

	$finish_fabric_recv=$sql[0][csf('finish_fabric_recv')]+$sql[0][csf('finish_fabric_iss_return')] + $sql[0][csf('trans_in')];
	$finish_fabric_issued=$sql[0][csf('finish_fabric_issue')]+$sql[0][csf('finish_fabric_recv_return')] + $sql[0][csf('trans_out')];
	$yet_issue=$finish_fabric_recv-$finish_fabric_issued;

	echo "$('#txt_order_numbers').val('');\n";
	echo "$('#txt_fabric_received').val('".number_format($finish_fabric_recv,2)."');\n";
	echo "$('#txt_cumulative_issued').val('".number_format($finish_fabric_issued,2)."');\n";
	echo "$('#txt_yet_to_issue').val('".number_format($yet_issue,2)."');\n";
	echo "$('#cbo_store_name').val('".$sql[0][csf('store_id')]."');\n";

	$stock_qty=return_field_value("current_stock","product_details_master","id='$prod_id'");
	echo "$('#txt_global_stock').val('".$stock_qty."');\n";
	echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";



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
	<div align="center" style="width:905px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:900px;margin-left:3px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="780" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter Issue No</th>
						<th width="220">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="finish_fabric_issue_id" id="finish_fabric_issue_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							$search_by_arr=array(1=>"Issue No",2=>"Challan No",3=>"Batch No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../');";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="text" style="width:80px;" class="datepicker"  name="txt_date_from" id="txt_date_from" readonly />&nbsp;TO&nbsp;
							<input type="text" style="width:80px;" class="datepicker"  name="txt_date_to" id="txt_date_to" readonly />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_issue_search_list_view', 'search_div', 'finish_fabric_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="4">
							<? echo load_month_buttons(1);  ?>
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
	$txt_date_form =$data[3];
	$txt_date_to =$data[4];

	if($txt_date_form!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_form=change_date_format($txt_date_form,"yyyy-mm-dd");
			$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
		}
		else
		{
			$txt_date_form=change_date_format($txt_date_form,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}

		$date_con=" and a.issue_date between '$txt_date_form' and '$txt_date_to'";
	}
	else
	{
		$date_con="";
	}
	if($search_by==1 && $data[0]!="")
		$search_field="a.issue_number_prefix_num=$data[0]";
	else if($search_by==2 && $data[0]!="")
		$search_field="a.challan_no like '%".trim($data[0])."%'";
	else
		$search_field="c.batch_no like '%".trim($data[0])."%'";

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date)";
		$batch_field="group_concat(c.batch_no)";
		$batch_field_id="group_concat(c.id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
		$batch_field="LISTAGG(c.batch_no, ', ') WITHIN GROUP (ORDER BY c.id)";
		$batch_field_id="LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id)";
	}
	else
	{
		$year_field="null";
		$batch_field="null";
	}

	$sql="select a.id, issue_number_prefix_num, $year_field as year, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose, a.buyer_id, a.is_posted_account, b.sample_type, sum(b.issue_qnty) as issue_qnty, $batch_field as batch_no,$batch_field_id as batch_id,b.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.item_category=2 and a.company_id=$company_id and $search_field and a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con group by a.id, issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose, a.buyer_id, a.is_posted_account, b.sample_type, a.insert_date,b.store_id order by a.id";

	$company_short_name_arr = return_library_array("select id, company_short_name from lib_company","id","company_short_name");
	$sample_type_arr = return_library_array("select id, sample_name from lib_sample","id","sample_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$arr=array(3=>$company_short_name_arr,6=>$yarn_issue_purpose,7=>$buyer_arr,9=>$sample_type_arr);

	echo  create_list_view("tbl_list_search", "Issue No,Year,Challan No,Company,Issue Date,Issue Qty,Issue Purpose,Buyer,Batch No,Sample Type", "60,50,70,70,75,75,110,70,120","885","240",0, $sql, "js_set_value", "id,batch_id,company_id,store_id,is_posted_account", "", 1, "0,0,0,company_id,0,0,issue_purpose,buyer_id,0,sample_type", $arr, "issue_number_prefix_num,year,challan_no,company_id,issue_date,issue_qnty,issue_purpose,buyer_id,batch_no,sample_type", '','','0,0,0,0,3,1,0,0,0,0');

	exit();
}

if($action=='populate_data_from_issue_master')
{
	$data_array=sql_select("select issue_number, challan_no, company_id, issue_date, issue_purpose, buyer_id, sample_type, knit_dye_source, knit_dye_company, cutt_req_no, location_id, is_posted_account, booking_no, booking_id from inv_issue_master where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";

		echo "active_inactive(".$row[csf("issue_purpose")].",0);\n";


		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";
		echo "document.getElementById('is_posted_account').value 			= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_booking_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('hidden_booking_id').value 			= '".$row[csf("booking_id")]."';\n";

		echo "load_drop_down( 'requires/finish_fabric_issue_controller', ".$row[csf("knit_dye_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_sewing_com','sewingcom_td');\n";
		echo "load_drop_down( 'requires/finish_fabric_issue_controller', ".$row[csf("knit_dye_company")].", 'load_drop_down_location','sewingcomlocation_td');\n";


		echo "document.getElementById('cbo_sewing_company').value 			= '".$row[csf("knit_dye_company")]."';\n";
		echo "document.getElementById('cbo_sewing_company_location').value 	= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_cut_req').value 					= '".$row[csf("cutt_req_no")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_issue_purpose').attr('disabled','disabled');\n";
		echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_issue_entry',1,1);\n";

		exit();
	}
}

if($action=="show_finish_fabric_issue_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

	//$sql="select id,batch_id,prod_id,issue_qnty,store_id,no_of_roll,order_id from inv_finish_fabric_issue_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$sql="select a.id,a.batch_id,a.prod_id,a.issue_qnty,a.store_id,a.no_of_roll,a.order_id,b.detarmination_id from inv_finish_fabric_issue_dtls a,product_details_master b where a.mst_id='$data' and a.prod_id=b.id and a.status_active = '1' and a.is_deleted = '0' ";

	?>
	<div>
		<fieldset>
			<legend>Fabric Details List</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">Batch No</th>
					<th width="170">Fabric Description</th>
					<th width="90">Issue Quantity</th>
					<th width="60">No Of Roll</th>
					<th width="100">Store</th>
					<th>Order Numbers</th>
				</thead>
			</table>
			<div style="width:730px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="712" class="rpt_table" id="tbl_list_search" >
					<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if($row[csf('order_id')]!="")
						{
							$po_ids=explode(",",$row[csf('order_id')]);
							$order_nos="";
							foreach($po_ids as $po_id)
							{
								$order_nos.=$po_arr[$po_id].",";
							}

							$order_nos=chop($order_nos,",");
						}
						else
							$order_nos='';

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,<? echo $row[csf('detarmination_id')]; ?>)">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="170"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="90" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
							<td width="60" align="right"><? echo $row[csf('no_of_roll')]; ?>&nbsp;</td>
							<td width="100"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
							<td><p><? echo $order_nos; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>

			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=='populate_issue_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];

	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$data_array=sql_select("select id, mst_id, trans_id, batch_id, prod_id,sample_type, issue_qnty,fabric_shade, store_id, no_of_roll, remarks, rack_no, shelf_no,floor,room, cutting_unit, order_id, order_save_string, roll_save_string, body_part_id,gmt_item_id,uom from inv_finish_fabric_issue_dtls where id='$id'");

	$store_id=$data_array[0][csf('store_id')];
	$company_id=$data[2];
	$booking_no=$data[3];
	$floor_id=$data_array[0][csf('floor')];
	$room_id=$data_array[0][csf('room')];
	$rack_id=$data_array[0][csf('rack_no')];
	$shelf_no=$data_array[0][csf('shelf_no')];

	$floor_name=return_field_value("a.floor_room_rack_name","lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","a.floor_room_rack_id=b.floor_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id=$floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_room_rack_name");
	$room_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
	$rack_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.rack_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
	$shelf_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.shelf_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id='$floor_id' and b.room_id='$room_id' and b.rack_id='$rack_id' and b.shelf_id=$shelf_no and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");

	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		echo "document.getElementById('hidden_batch_id').value 				= '".$row[csf("batch_id")]."';\n";

		if($roll_maintained!=1)
		{
			echo "show_list_view('".$row[csf('batch_id')]."**".$company_id."**".$store_id."**".$booking_no."','show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');\n";
		}

		echo "load_drop_down('requires/finish_fabric_issue_controller',".$row[csf("prod_id")]."+'**'+".$row[csf("batch_id")].",'load_drop_down_body_part','body_part_td');\n";
		$order_id=rtrim($row[csf("order_id")],',');
		$product_data=sql_select("select current_stock, product_name_details, color from product_details_master where id=".$row[csf("prod_id")]);
		$product_details=$product_data[0][csf('product_name_details')];
		$stock_qty=$product_data[0][csf('current_stock')];
		$color=$color_arr[$product_data[0][csf('color')]];

		echo "document.getElementById('txt_fabric_desc').value 			= '".$product_details."';\n";
		echo "document.getElementById('cbo_sample_type').value 				= '".$row[csf("sample_type")]."';\n";
		echo "$('#txt_global_stock').val('".$stock_qty."');\n";
		echo "$('#txt_color').val('".$color."');\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('txt_issue_req_qnty').value 			= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$order_id."';\n";
		echo "document.getElementById('save_string').value 					= '".$row[csf("roll_save_string")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$row[csf("order_save_string")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('cbo_fabric_type').value 				= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('txt_floor').value 					= '".$row[csf("floor")]."';\n";
		echo "document.getElementById('txt_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";

		echo "document.getElementById('txt_floor_name').value 					= '".$floor_name."';\n";
		echo "document.getElementById('txt_room_name').value 					= '".$room_name."';\n";
		echo "document.getElementById('txt_rack_name').value 					= '".$rack_name."';\n";
		echo "document.getElementById('txt_shelf_name').value 					= '".$shelf_name."';\n";

		echo "document.getElementById('cbo_cutting_floor').value 			= '".$row[csf("cutting_unit")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('cbouom').value 						= '".$row[csf("uom")]."';\n";

		if($row[csf("order_id")]!="")
		{
			echo "load_drop_down( 'requires/finish_fabric_issue_controller', '".$order_id."', 'load_drop_down_gmt_item','gmt_item_td');\n";
			echo "po_fld_reset(0);\n";
			echo "get_php_form_data('".$order_id."**".$row[csf("prod_id")]."**".$row[csf("batch_id")]."**".$row[csf("body_part_id")]."**".$row[csf("store_id")]."', 'populate_data_about_order', 'requires/finish_fabric_issue_controller' );\n";
		}
		else
		{
			echo "po_fld_reset(1);\n";
			echo "get_php_form_data('".$row[csf("batch_id")]."**".$row[csf("prod_id")]."**".$row[csf("store_id")]."', 'populate_data_about_sample', 'requires/finish_fabric_issue_controller' );\n";

		}
		echo "document.getElementById('cbo_item_name').value 				= '".$row[csf("gmt_item_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_issue_entry',1,1);\n";

		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $hidden_prod_id and store_id = $cbo_store_name and transaction_type in (1,4,5) and status_active=1", "max_date");
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

		$finish_fabric_issue_num=''; $finish_update_id=''; $product_id=$hidden_prod_id;
		$stock_sql=sql_select("select current_stock, color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$color_id=$stock_sql[0][csf('color')];

		if(str_replace("'","",$txt_issue_qnty)>$curr_stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			die;
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'KFFI',18,date("Y",time())));

			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, item_category, company_id, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, location_id, buyer_id, booking_no, booking_id, cutt_req_no, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",18,2,".$cbo_company_id.",".$cbo_sample_type.",".$txt_issue_date.",".$txt_challan_no.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_sewing_company_location.",".$cbo_buyer_name.",".$hidden_booking_no.",".$hidden_booking_id.",".$txt_cut_req.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$finish_fabric_issue_num=$new_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="sample_type*issue_date*challan_no*knit_dye_source*knit_dye_company*location_id*buyer_id*cutt_req_no*updated_by*update_date";

			$data_array_update=$cbo_sample_type."*".$txt_issue_date."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sewing_company_location."*".$cbo_buyer_name."*".$txt_cut_req."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$finish_fabric_issue_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}

		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$hidden_fabric_rate=str_replace(",","",$hidden_fabric_rate);
		$cons_amount=$hidden_fabric_rate*str_replace("'","",$txt_issue_qnty);
		$field_array_trans="id, mst_id,company_id,pi_wo_batch_no,prod_id,item_category,transaction_type,transaction_date,cons_uom,cons_quantity, cons_rate,cons_amount,issue_challan_no,store_id,rack,self,floor_id,room,inserted_by,insert_date,fabric_shade,body_part_id";

		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_company_id.",".$hidden_batch_id.",".$product_id.",2,2,".$txt_issue_date.",".$cbouom.",".$txt_issue_qnty.",'".$hidden_fabric_rate."','".$cons_amount."',".$txt_challan_no.",".$cbo_store_name.",".$txt_rack.",".$txt_shelf.",".$txt_floor.",".$txt_room.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_fabric_type.",".$cbo_body_part.")";

		$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
		$field_array_dtls="id, mst_id, trans_id, batch_id, prod_id, uom, issue_qnty,fabric_shade,store_id, no_of_roll, body_part_id,sample_type,gmt_item_id, remarks, rack_no, shelf_no,floor,room, cutting_unit, order_id, roll_save_string, order_save_string, inserted_by, insert_date";

		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$hidden_batch_id.",".$product_id.",".$cbouom.",".$txt_issue_qnty.",".$cbo_fabric_type.",".$cbo_store_name.",".$txt_no_of_roll.",".$cbo_body_part.",".$cbo_sample_type.",".$cbo_item_name.",".$txt_remarks.",".$txt_rack.",".$txt_shelf.",".$txt_floor.",".$txt_room.",".$cbo_cutting_floor.",".$all_po_id.",".$save_string.",".$save_data.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


		//========================================================

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_mrr_wise = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array_trans_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array_mrr_wise = "";
		$updateID_array_trans_mrr = array();
		$update_data_trans_mrr = array();
		$issueQnty = str_replace("'", "", $txt_issue_qnty);
		// check variable settings issue method(LIFO/FIFO)
		$isLIFOfifo = '';
		$sql_variable = sql_select("select store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17) and item_category_id=2 and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row)
		{
			$isLIFOfifo = $row[csf('store_method')];
		}

		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";

		// Trans type: 1=>"Receive",4=>"Issue Return",5=>"Item Transfer Receive"
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$product_id and store_id=$cbo_store_name and pi_wo_batch_no = $hidden_batch_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=2  and floor_id = $txt_floor and room = $txt_room and rack = $txt_rack and self = $txt_shelf and fabric_shade = $cbo_fabric_type and status_active=1 order by transaction_date,id $cond_lifofifo");

		//$txt_rack.",".$txt_shelf.",".$txt_floor.",".
		//,floor_id,room,rack,self

		//echo "10**select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$product_id and store_id=$cbo_store_name and pi_wo_batch_no = $hidden_batch_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=2 and status_active=1 order by transaction_date,id $cond_lifofifo";die;

		foreach ($sql as $result)
		{
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")]*1;
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0)
			{
				$amount = $issueQnty * $cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array_mrr_wise != "") $data_array_mrr_wise .= ",";
				$data_array_mrr_wise .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",18," . $product_id . "," . $issueQnty . ",'" . $cons_rate . "','" . $amount . "','" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array_trans_mrr[] = $recv_trans_id;
				$update_data_trans_mrr[$recv_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			}
			else if ($issueQntyBalance < 0)
			{
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array_mrr_wise != "") $data_array_mrr_wise .= ",";
				$data_array_mrr_wise .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",18," . $product_id . "," . $balance_qnty . ",'" . $cons_rate . "','" . $amount . "','" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array_trans_mrr[] = $recv_trans_id;
				$update_data_trans_mrr[$recv_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}//end foreach
		// LIFO/FIFO then END-----------------------------------------------//

		//echo "10**insert into inv_mrr_wise_issue_details (".$field_array_mrr_wise.") values ".$data_array_mrr_wise;die;
		//echo "10**".bulk_update_sql_statement("inv_transaction", "id", $update_array_trans_mrr, $update_data_trans_mrr, $updateID_array_trans_mrr);die;


		//=============================================================

		//echo "insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$field_array_prod_update="last_issued_qnty*current_stock*updated_by*update_date";

		$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
		$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$data_array_roll='';
		if(str_replace("'","",$roll_maintained)==1 && str_replace("'","",$save_string)!="")
		{
			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";

			$save_string=explode(",",str_replace("'","",$save_string));
			for($i=0;$i<count($save_string);$i++)
			{
				$roll_dtls=explode("_",$save_string[$i]);
				$roll_id=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_qnty=$roll_dtls[2];
				$order_id=$roll_dtls[3];

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_roll.="$add_comma(".$id_roll.",".$finish_update_id.",".$id_dtls.",'".$order_id."',18,'".$roll_qnty."','".$roll_no."','".$roll_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		$data_array_prop='';
		if(str_replace("'","",$save_data)!="")
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,18,".$id_dtls.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		// Query Execution Start
		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**insert into inv_issue_master($field_array)values".$data_array;die;
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
		//echo "10**insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
		if($flag==1)
		{
			if($prod) $flag=1; else $flag=0;
		}
		if($data_array_roll!="")
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}
		if($data_array_prop!="")
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		//if ($data_array_mrr_wise != "")
		//{
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array_mrr_wise, $data_array_mrr_wise, 0);
			if($flag==1)
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0;
			}
		//}

		if (count($updateID_array_trans_mrr) > 0)
		{
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans_mrr, $update_data_trans_mrr, $updateID_array_trans_mrr));
			if($flag==1)
			{
				if($upTrID) $flag=1; else $flag=0;
			}
		}

		//echo "10**".$rID."##".$rID2."##".$rID3."##".$prod."##".$rID4."##".$rID5."##".$mrrWiseIssueID."##".$upTrID;die;

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
		else if($db_type==2 || $db_type==1 )
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

		$product_id=$hidden_prod_id; $color_id=''; $curr_stock_qnty=''; $latest_current_stock='';

		$field_array_update="sample_type*challan_no*knit_dye_source*knit_dye_company*location_id*buyer_id*cutt_req_no*updated_by*update_date";
		$data_array_update=$cbo_sample_type."*".$txt_challan_no."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sewing_company_location."*".$cbo_buyer_name."*".$txt_cut_req."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$stock_sql=sql_select("select current_stock, color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$color_id=$stock_sql[0][csf('color')];
		$field_array_prod_update="last_issued_qnty*current_stock*updated_by*update_date";

		if($product_id==$previous_prod_id)
		{
			$latest_current_stock=$curr_stock_qnty+str_replace("'", '',$hidden_issue_qnty);

			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty)+str_replace("'", '',$hidden_issue_qnty);
			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{
			$stock=return_field_value("current_stock","product_details_master","id=$previous_prod_id");
			$adjust_curr_stock=$stock+str_replace("'", '',$hidden_issue_qnty);

			$latest_current_stock=$curr_stock_qnty;

			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_issue_qnty);
			$data_array_prod_update=$txt_issue_qnty."*".$curr_stock_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			die;
		}

		$recv_trans_id = return_field_value("recv_trans_id", "inv_mrr_wise_issue_details", "issue_trans_id=$update_trans_id and status_active=1 and	is_deleted=0", "recv_trans_id");
		$mrr_receive_check = return_field_value("sum(cons_quantity) recv_qnty", "inv_transaction", "id=$recv_trans_id and status_active=1 and is_deleted=0", "recv_qnty");
		if (str_replace("'", "", $txt_issue_qnty) > $mrr_receive_check) {
			echo "20**Issue quantity can not be greater than Receive quantity.\nReceive quantity = $mrr_receive_check";
			disconnect($con);
			die;
		}

		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*store_id*cons_uom*cons_quantity*cons_rate*cons_amount*issue_challan_no*rack*self*floor_id*room*updated_by*update_date*fabric_shade*body_part_id";
		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$cons_amount=$hidden_fabric_rate*str_replace("'","",$txt_issue_qnty);
		$data_array_trans=$product_id."*".$hidden_batch_id."*".$txt_issue_date."*".$cbo_store_name."*".$cbouom."*".$txt_issue_qnty."*'".$hidden_fabric_rate."'*'".$cons_amount."'*".$txt_challan_no."*".$txt_rack."*".$txt_shelf."*".$txt_floor."*".$txt_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_fabric_type."*".$cbo_body_part;

		$field_array_dtls="batch_id*prod_id*uom*issue_qnty*fabric_shade*store_id*no_of_roll*body_part_id*sample_type*gmt_item_id*remarks*rack_no*shelf_no*floor*room*cutting_unit*
		order_id*roll_save_string*order_save_string*updated_by*update_date";
		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$cons_amount=$hidden_fabric_rate*str_replace("'","",$txt_issue_qnty);

		$data_array_dtls=$hidden_batch_id."*".$product_id."*".$cbouom."*".$txt_issue_qnty."*".$cbo_fabric_type."*".$cbo_store_name."*".$txt_no_of_roll."*".$cbo_body_part."*".$cbo_sample_type."*".$cbo_item_name."*".$txt_remarks."*".$txt_rack."*".$txt_shelf."*".$txt_floor."*".$txt_room."*".$cbo_cutting_floor."*".$all_po_id."*".$save_string."*".$save_data."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		$data_array_roll='';
		if(str_replace("'","",$roll_maintained)==1 && str_replace("'","",$save_string)!="")
		{
			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";
			$save_string=explode(",",str_replace("'","",$save_string));
			for($i=0;$i<count($save_string);$i++)
			{
				$roll_dtls=explode("_",$save_string[$i]);
				$roll_id=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_qnty=$roll_dtls[2];
				$order_id=$roll_dtls[3];

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_roll.="$add_comma(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',18,'".$roll_qnty."','".$roll_no."','".$roll_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		$data_array_prop='';
		if(str_replace("'","",$save_data)!="")
		{
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

			$save_data=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_data);$i++)
			{
				$order_dtls=explode("_",$save_data[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,18,".$update_dtls_id.",'".$order_id."',".$product_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		//=================================================================================
		//previous mrr transaction balance returns
		$trans_data_array = array(); $recv_trans_ids = "";
		$field_array_trans_pre_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount, b.recv_trans_id from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_id and b.entry_form=18 and a.item_category=2");
		$updateID_array_pre_trans = array();
		$update_data_trans_pre_mrr = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$updateID_array_pre_trans[] = $result[csf("id")];
			$update_data_trans_pre_mrr[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
			if($recv_trans_ids =="") {
				$recv_trans_ids = $result[csf("recv_trans_id")];
			}
			else {
				$recv_trans_ids .= ", ".$result[csf("recv_trans_id")];
			}
		}

		//mrr wise insert and again previous transaction balance update

		$field_array_mrr_wise = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$field_array_trans_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array_mrr_wise = "";
		$updateID_array_trans_mrr = array();
		$update_data_trans_mrr = array();
		$issueQnty = str_replace("'", "", $txt_issue_qnty);

		$isLIFOfifo = '';
		$check_allocation = '';
		$sql_variable = sql_select("select store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17) and item_category_id=2  and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row)
		{
			$isLIFOfifo = $row[csf('store_method')];
		}

		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";
		if ($previous_prod_id == $product_id) $balance_cond = " and( balance_qnty>0 or id in($recv_trans_ids))";
		else $balance_cond = " and balance_qnty>0";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$product_id and store_id=$cbo_store_name $balance_cond and transaction_type in (1,4,5) and item_category=2 and floor_id = $txt_floor and room = $txt_room and rack = $txt_rack and self = $txt_shelf and fabric_shade = $cbo_fabric_type order by transaction_date, id $cond_lifofifo");

		foreach ($sql as $result)
		{
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if ($trans_data_array[$issue_trans_id]['qnty'] == "") {
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			} else {
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}

			$cons_rate = $result[csf("cons_rate")]*1;
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $cons_rate;

				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array_mrr_wise != "") $data_array_mrr_wise .= ",";
				$data_array_mrr_wise .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_trans_id . ",18," . $product_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";

				$updateID_array_trans_mrr[] = $issue_trans_id;
				$update_data_trans_mrr[$issue_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array_mrr_wise != "") $data_array_mrr_wise .= ",";
				$data_array_mrr_wise .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_trans_id . ",18," . $product_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";

				$updateID_array_trans_mrr[] = $issue_trans_id;
				$update_data_trans_mrr[$issue_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}
		//echo "10**".bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_mrr, $update_data_trans_mrr, $updateID_array_trans_mrr);die;

		//==================================================================================== field_array_trans_mrr


		//Query Execution Start
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		if($product_id==$previous_prod_id)
		{
			$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1)
			{
				if($prod) $flag=1; else $flag=0;
			}
		}
		else
		{
			$adjust_prod=sql_update("product_details_master","current_stock",$adjust_curr_stock,"id",$previous_prod_id,0);
			if($flag==1)
			{
				if($adjust_prod) $flag=1; else $flag=0;
			}

			$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1)
			{
				if($prod) $flag=1; else $flag=0;
			}
		}
		$rID2=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$rID3=sql_update("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=18",0);
		if($flag==1)
		{
			if($delete_roll) $flag=1; else $flag=0;
		}

		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=18",0);
		if($flag==1)
		{
			if($delete_prop) $flag=1; else $flag=0;
		}
		if($data_array_roll!="")
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}

		if($data_array_prop!="")
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		//echo "10**".bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_pre_mrr, $update_data_trans_pre_mrr, $updateID_array_pre_trans)."<br> insert into inv_mrr_wise_issue_details ($field_array_mrr_wise) values $data_array_mrr_wise <br>".bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_mrr, $update_data_trans_mrr, $updateID_array_trans_mrr)."<br>DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_id and entry_form=18";die;

		if (count($updateID_array_pre_trans) > 0) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_pre_mrr, $update_data_trans_pre_mrr, $updateID_array_pre_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_id and entry_form=18");
			if($flag==1)
			{
				if($query2 && $query3) $flag=1; else $flag=0;
			}
		}

		if ($data_array_mrr_wise != "") {
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array_mrr_wise, $data_array_mrr_wise, 0);
			if($flag==1)
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0;
			}
		}
		if (count($updateID_array_trans_mrr) > 0) {
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_mrr, $update_data_trans_mrr, $updateID_array_trans_mrr));
			if($flag==1)
			{
				if($upTrID) $flag=1; else $flag=0;
			}
		}

		//echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$prod##$adjust_prod##$delete_roll##$delete_prop##$query2 && $query3 && $mrrWiseIssueID &&$upTrID";
		//oci_rollback($con);die;

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

if($action=="check_batch_no")
{
	$data=explode("**",$data);
	$sample_booking_cond='';

	if($db_type==0) {$null_cond="IFNULL";}
	else {$null_cond="NVL";}
	if($data[2]==8)
	{
		$sample_booking_cond="and booking_without_order=1";
	}
	else if($data[2]==4 || $data[2]==9)
	{
		$sample_booking_cond="and $null_cond(booking_without_order,0)!=1";
	}

	$sql="select id, batch_no, booking_no, booking_no_id from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form in (0,7,37) $sample_booking_cond order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		$order_ids='';
		if($db_type==0)
		{
			$order_ids=return_field_value("group_concat(po_id) as po_id","pro_batch_create_dtls","status_active=1 and is_deleted=0 and po_id<>0 and mst_id=".$data_array[0][csf('id')],'po_id');
		}
		else
		{
			$order_ids=return_field_value( "LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id","pro_batch_create_dtls","status_active=1 and is_deleted=0 and po_id<>0 and mst_id=".$data_array[0][csf('id')],'po_id');
		}
		if($order_ids=="") $without_order=1; else $without_order=0;
		echo $data_array[0][csf('id')]."**".$without_order."**".$data_array[0][csf('booking_no')]."**".$data_array[0][csf('booking_no_id')];
	}
	else
	{
		echo "0**";
	}

	exit();
}

if($action=="load_drop_down_body_part")
{
	$data = explode("**",$data);
	$prod_id=$data[0];
	$batch_id=$data[1];

	if($db_type==0)
	{
		$show_body_part_id=return_field_value("group_concat(body_part_id) as body_part_id","pro_finish_fabric_rcv_dtls","prod_id=$prod_id and batch_id=$batch_id and status_active=1 and is_deleted=0",'body_part_id');
	}
	else
	{
		$show_body_part_id=return_field_value("LISTAGG(body_part_id, ',') WITHIN GROUP (ORDER BY body_part_id) as body_part_id","pro_finish_fabric_rcv_dtls","prod_id=$prod_id and batch_id=$batch_id and status_active=1 and is_deleted=0",'body_part_id');
	}

	$show_body_part_id=implode(",",array_unique(explode(",",$show_body_part_id)));
	echo create_drop_down( "cbo_body_part", 170, $body_part,"", 1, "-- Select Body Part --", 0, "",0, $show_body_part_id );
	exit();
}

if ($action=="finish_fabric_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	//print_r ($data);

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company,location_id, buyer_id, cutt_req_no from inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=18";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_location=return_library_array( "select id,location_name from lib_location where company_id='".$dataArray[0][csf('knit_dye_company')]."' and  status_active =1 and is_deleted=0", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	//$buyer_short_name = return_library_array("select id, short_name from lib_buyer","id","short_name");

	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$floor_name_cutting=return_field_value("cutting_unit","inv_finish_fabric_issue_dtls","mst_id='$data[1]'");



	$order_ids=''; $batch_ids=''; $prodIds='';
	$sql_dtls="select id, batch_id, prod_id,rack_no,shelf_no, issue_qnty, no_of_roll, cutting_unit, remarks, order_id, fabric_shade,store_id, body_part_id,uom,floor,room from inv_finish_fabric_issue_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_result= sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		$order_ids.=$row[csf("order_id")].',';
		$batch_ids.=$row[csf("batch_id")].',';
		$prodIds.=$row[csf("prod_id")].',';

		$store_id=$row[csf('store_id')];
		$company_id=$data[0];
		$floor_id=$row[csf('floor')];
		$room_id=$row[csf('room')];
		$rack_id=$row[csf('rack_no')];
		if ($rack_id!="") {$rack_id_cond="and b.rack_id='$rack_id'";}else{$rack_id_cond="";}
		$floor_name=return_field_value("a.floor_room_rack_name","lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","a.floor_room_rack_id=b.floor_id and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_room_rack_name");
		$room_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id=$floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
		$rack_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.rack_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id=$floor_id and b.room_id=$room_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");
		$shelf_name=return_field_value("floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b","  a.floor_room_rack_id=b.shelf_id and b.store_id=$store_id and a.company_id=$company_id and b.floor_id=$floor_id and b.room_id=$room_id $rack_id_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_room_rack_name");

	}

	$order_ids=chop($order_ids,',');
	$batch_ids=chop($batch_ids,',');
	$prodIds=chop($prodIds,',');

	$batch_arr=array();
	$batchDataArr = sql_select("select id, batch_no, color_id from pro_batch_create_mst where id in($batch_ids)");
	foreach($batchDataArr as $row)
	{
		$batch_arr[$row[csf('id')]]['no']=$row[csf('batch_no')];
		$batch_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
	}


	if ($order_ids=="") {
		$batch_buyer_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.buyer_id as buyer_name from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b, wo_po_details_master c where a.booking_no_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id");
	}
	/*else
	{
		$batch_buyer_sql = sql_select("select a.mst_id as batch_id, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($batch_ids)");
	}*/
	$batch_buyer_arr=array();
	foreach($batch_buyer_sql as $row)
	{
		$batch_buyer_arr[$row[csf('batch_id')]]=$row[csf('buyer_name')];
	}

	$product_array=array();
	$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and id in($prodIds)");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}

	$booking_array=array(); $job_no=''; $style_ref_no=''; $po_array=array();
	if($order_ids!="")
	{
		$booking_sql = sql_select("select b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($order_ids) group by b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id");
		foreach($booking_sql as $row)
		{
			$booking_array[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]]['gsm_weight']=$row[csf("gsm_weight")];
			$booking_array[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]]['dia_width']=$row[csf("dia_width")];
		}

		$sql_job="select a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($order_ids)";
		$result_sql_job=sql_select($sql_job);
		$style_arr=array();
		foreach($result_sql_job as $row)
		{
			if($job_no=='') $job_no=$row[csf("job_no_mst")]; else $job_no.=','.$row[csf("job_no_mst")];
			if($style_ref_no=='') $style_ref_no=$row[csf("style_ref_no")]; else $style_ref_no.=','.$row[csf("style_ref_no")];
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("grouping")];
			$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
			$style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
		}
	}

	$job_no=implode(",",array_unique(explode(",",$job_no)));
	$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));


	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$sql_res=sql_select("select a.id,a.address_1 from lib_supplier a,  lib_supplier_tag_company b where a.id=b.supplier_id and a.id=".$dataArray[0][csf('knit_dye_company')]." and a.status_active=1   group by a.id,a.address_1 order by a.id ");
		$sewing_com_address=$sql_res[0][csf('address_1')];
	}
	else if ($dataArray[0][csf('knit_dye_source')]==1)
	{
		$sql_res=sql_select("select a.id,a.city,a.plot_no from lib_company a where a.id=".$dataArray[0][csf('knit_dye_source')]."  and a.status_active=1  order by a.id");
		$sewing_com_address=$sql_res[0][csf('city')].', '.$sql_res[0][csf('plot_no')];
	}
	?>
	<div style="width:1250px;">
		<table width="1250" cellspacing="0" >
			<tr>
				<td rowspan="3"  valign="middle">
					<?
					$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					foreach($data_array2 as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='170' align="middle" />
						<?
					}
					?>
				</td>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo   $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="5" align="center">
					<?
                //echo show_company($data[0],'','');//Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						echo $result[csf('plot_no')];
						if($result[csf('plot_no')]!="") echo ", ";
						echo $result[csf('level_no')];
						if($result[csf('level_no')]!="") echo ", ";
						echo $result[csf('road_no')];
						if($result[csf('road_no')]!="") echo ", ";
						echo $result[csf('block_no')];
						if($result[csf('block_no')]!="") echo ", ";
						echo $result[csf('city')];
						if($result[csf('city')]!="") echo ", ";
						echo $result[csf('zip_code')];
						if($result[csf('zip_code')]!="") echo ", ";
						echo $result[csf('country_id')];
						if($result[csf('country_id')]!="") echo ", ";
						echo "<br> ";
						if($result[csf('email')]!="") echo "Email Address: ".$result[csf('email')];
						if($result[csf('website')]!="") echo "Website No: ".$result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="130"><strong>Issue ID :</strong></td><td width="250"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="130"><strong>Issue Purpose:</strong></td> <td width="250"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td width="130"><strong>Sample Type:</strong></td><td><? echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Issue Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>Challan No:</strong></td><td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Sewing Source:</strong></td> <td ><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>
			<tr>

				<td><strong>Style Ref. No: </strong></td><td><b><? echo $style_ref_no; ?></b></td>
				<td><strong>Job No</strong></td> <td><p><? echo $job_no; ?></p></td>
				<td><strong>Cutt. Req. No:</strong></td><td ><? echo $dataArray[0][csf('cutt_req_no')]; ?></td>

			</tr>
			<tr style=" height:25px">
				<td><strong>Cutting Unit:</strong></td><td ><? echo $cutting_floor_library[$floor_name_cutting]; ?></td>
				<td><strong>Sewing Com:</strong></td><td style="word-break:break-all"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address;

				?></td>
				<td><strong>Sewing Location:</strong></td><td ><? echo $company_location[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr style=" height:25px">
				<td colspan="4"></td>
				<td colspan="2" id="barcode_img_id" align="right"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd">
				<th width="20">SL</th>
				<th width="80">PO Buyer</th>
				<th width="80">Batch No</th>
				<th width="110" >Color</th>
				<th width="110">Order No</th>
				<th width="60">File No</th>
				<th width="60">Ref. No</th>
				<th width="130">Fabric Des.</th>
				<th width="50" >Fab. Shade</th>
				<th width="50" >Roll</th>

				<th width="50" >Rack</th>
				<th width="50" >Shelf</th>
				<th width="60" >Issue Qty</th>
				<th width="50" >UOM</th>
				<th width="50" >Req. Dia</th>
				<th width="50" >Actual Dia</th>
				<th width="50" >Req. GSM</th>
				<th width="50" >Actual GSM</th>
				<th width="100" >Store</th>
				<th>Remarks</th>
			</thead>
			<tbody>
				<?

				$i=1;
				foreach($sql_result as $row)
				{

					if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

					$po_no=array_filter(array_unique(explode(",",$row[csf("order_id")])));
					$order_nos="";$style_ref_nos="";$req_dia_arr="";$req_gsm_arr="";$po_file="";$po_ref="";$po_buyer="";
					foreach($po_no as $val)
					{
						if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
						if ($po_file=="") $po_file=$po_array[$val]['file']; else $po_file.=", ".$po_array[$val]['file'];
						if ($po_ref=="") $po_ref=$po_array[$val]['ref']; else $po_ref.=", ".$po_array[$val]['ref'];
						if ($style_ref_nos=="") $style_ref_nos=$style_arr[$val]; else $style_ref_nos.=",".$style_arr[$val];
						if ($req_dia_arr=="") $req_dia_arr=$booking_array[$val][$row[csf("body_part_id")]]['dia_width']; else $req_dia_arr.=",".$booking_array[$val][$row[csf("body_part_id")]]['dia_width'];

						if ($req_gsm_arr=="") $req_gsm_arr=$booking_array[$val][$row[csf("body_part_id")]]['gsm_weight']; else $req_gsm_arr.=",".$booking_array[$val][$row[csf("body_part_id")]]['gsm_weight'];

						if($po_buyer=="") $po_buyer = $buyer_library[$po_array[$val]['buyer']];else $po_buyer .=", ".$buyer_library[$po_array[$val]['buyer']];
					}
					if(count($po_no)>0)
					{
						$row_buyer_name = $po_buyer;
					}else{
						$row_buyer_name = $buyer_library[$batch_buyer_arr[$row[csf('batch_id')]]];
					}
					$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_nos)));
					$req_dia_arr=implode(",",array_unique(explode(",",$req_dia_arr)));
					$req_gsm_arr=implode(",",array_unique(explode(",",$req_gsm_arr)));

					$totalQnty +=$row[csf("issue_qnty")];
					$totalRoll +=$row[csf("no_of_roll")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $row_buyer_name;//$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></td>
						<td style="word-break:break-all;"><? echo $batch_arr[$row[csf("batch_id")]]['no']; ?></td>
						<td style="word-break:break-all;"><? echo $color_arr[$batch_arr[$row[csf("batch_id")]]['color_id']]; ?></td>
						<td style="word-break:break-all;"><? echo $order_nos; ?></td>
						<td style="word-break:break-all;"><? echo $po_file; ?></td>
						<td style="word-break:break-all;"><? echo $po_ref; ?></td>
						<td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
						<td align="right"><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></td>
						<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>

						<td align="right"><?  echo $rack_name; ?></td>
						<td align="right"><? echo $shelf_name; ?></td>
						<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						<td align="center"><? echo $req_dia_arr;//$booking_array[$row[csf("order_id")]]['dia_width']; ?></td>
						<td align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
						<td align="center"><? echo $req_gsm_arr;//$booking_array[$row[csf("order_id")]]['gsm_weight']; ?></td>
						<td align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
						<td style="word-break:break-all;"><? echo $store_library[$row[csf("store_id")]]; ?></td>
						<td style="word-break:break-all;"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<? $i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" align="right"><strong>Total :</strong></td>
					<td align="right"><?php echo $totalRoll; ?></td>
					<td> </td> <td> </td>
					<td align="right"><?php echo number_format($totalQnty, 2); ?></td>
					<td align="right" colspan="7"><?php // echo $totalAmount; ?></td>
				</tr>
			</tfoot>
		</table>
		<br>
		<?
		echo signature_table(21, $data[0], "1250px");
		?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_issue_print2") // For EuroTex Knit Wear Ltd
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company, location_id, buyer_id, cutt_req_no from inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=18";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_location=return_library_array( "select id,location_name from lib_location where company_id='".$dataArray[0][csf('knit_dye_company')]."' and  status_active =1 and is_deleted=0", "id","location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$floor_name_cutting=return_field_value("cutting_unit","inv_finish_fabric_issue_dtls","mst_id='$data[1]'");

	$order_ids=''; $batch_ids=''; $prodIds='';
	$sql_dtls="select id, batch_id, prod_id, issue_qnty, no_of_roll, cutting_unit, remarks, order_id, fabric_shade,store_id, body_part_id,uom from inv_finish_fabric_issue_dtls where mst_id='$data[1]' and status_active=1 and is_deleted= 0";
	$sql_result= sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		$order_ids.=$row[csf("order_id")].',';
		$batch_ids.=$row[csf("batch_id")].',';
		$prodIds.=$row[csf("prod_id")].',';
	}

	$order_ids=chop($order_ids,',');
	$batch_ids=chop($batch_ids,',');
	$prodIds=chop($prodIds,',');

	$batch_arr=array();
	$batchDataArr = sql_select("select id, batch_no, color_id from pro_batch_create_mst where id in($batch_ids)");
	foreach($batchDataArr as $row)
	{
		$batch_arr[$row[csf('id')]]['no']=$row[csf('batch_no')];
		$batch_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
	}

	$product_array=array();
	$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and id in($prodIds)");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}


	//For Buyer
	if ($order_ids=="") {
		$batch_buyer_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.buyer_id as buyer_name from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b, wo_po_details_master c where a.booking_no_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id");
	}
	/*else
	{
		$batch_buyer_sql = sql_select("select a.mst_id as batch_id, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($batch_ids)");
	}*/
	$batch_buyer_arr=array();
	foreach($batch_buyer_sql as $row)
	{
		$batch_buyer_arr[$row[csf('batch_id')]]=$row[csf('buyer_name')];
	}

	$booking_array=array();
	//$job_no='';
	$style_ref_no=''; $po_array=array();
	if($order_ids!="")
	{
		$booking_sql = sql_select("select b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($order_ids) group by b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id");
		foreach($booking_sql as $row)
		{
			$booking_array[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]]['gsm_weight']=$row[csf("gsm_weight")];
			$booking_array[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]]['dia_width']=$row[csf("dia_width")];
		}

		$sql_job="select a.id, a.po_number,	a.job_no_mst,a.file_no,a.grouping, b.style_ref_no,b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($order_ids)";
		$result_sql_job=sql_select($sql_job);
		$style_arr=array();$po_array=array();
		foreach($result_sql_job as $row)
		{
			//if($job_no=='') $job_no=$row[csf("job_no_mst")]; else $job_no.=','.$row[csf("job_no_mst")];
			if($style_ref_no=='') $style_ref_no=$row[csf("style_ref_no")]; else $style_ref_no.=','.$row[csf("style_ref_no")];

			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("grouping")];
			$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
			$style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
			$jobs_arr[$row[csf("id")]]=$row[csf("job_no_mst")];
		}
	}

	//$job_no=implode(",",array_unique(explode(",",$job_no)));
	$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));

	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$sql_res=sql_select("select a.id,a.address_1 from lib_supplier a,  lib_supplier_tag_company b where a.id=b.supplier_id and a.id=".$dataArray[0][csf('knit_dye_company')]." and a.status_active=1   group by a.id,a.address_1 order by a.id ");
		$sewing_com_address=$sql_res[0][csf('address_1')];
	}
	else if ($dataArray[0][csf('knit_dye_source')]==1)
	{
		$sql_res=sql_select("select a.id,a.city,a.plot_no from lib_company a where a.id=".$dataArray[0][csf('knit_dye_source')]."  and a.status_active=1  order by a.id");
		$sewing_com_address=$sql_res[0][csf('city')].','.$sql_res[0][csf('plot_no')];
	}

	?>
	<div style="width:100%;" align="center">
		<table width="1160" cellspacing="0" >
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<?
				$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array2 as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='180' align="middle" />
						<?
					}
					?>
				</td>
				<td colspan="4" align="center" style="font-size:14px">
					<?
					echo show_company($data[0],'','');//Aziz

					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Issue ID :</strong></td><td style="text-align: left; padding-right: 200"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td><strong>Issue Date:</strong></td><td style="text-align: left; padding-right: 200"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Issue Purpose:</strong></td> <td style="text-align: left; padding-right: 200"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td width="125"><strong>Sample Type:</strong></td><td style="text-align: left; padding-right: 200"><? //echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td><td style="text-align: left; padding-right: 200"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Sewing Com:</strong></td><td style="text-align: left; padding-right: 200"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].'<br>'. $sewing_com_address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]].'<br>'. $sewing_com_address;
				?></td>

			</tr>
			<tr style="height:25px">
				<td><strong>Sewing Source:</strong></td> <td style="text-align: left; padding-right: 200"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Sewing Location:</strong></td><td ><? echo $company_location[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>

		</table>
		<div style="width:1160px; margin-top:20px;" align="center">
			<table cellspacing="0" width="1160"  border="1" rules="all" class="rpt_table" >

				<thead bgcolor="#dddddd" align="center">
					<th width="20">SL</th>
					<th width="90">PO Buyer</th>
					<th width="120">Job No.</th>
					<th width="120">Style Ref. No.</th>
					<th width="70">Batch No</th>
					<th width="70">Color</th>
					<th width="200">Fabric Des.</th>
					<th width="70">Roll</th>
					<th width="50">Issue Qty</th>
					<th width="50">Req. Dia</th>
					<th width="100">Remarks</th>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($sql_result as $row)
					{
		if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";//body_part_id
		$po_no= array_filter(array_unique(explode(",",$row[csf("order_id")])));
		$order_nos="";$style_ref_nos="";$req_dia_arr="";$po_file='';$po_ref='';$po_buyer=''; $job_ref_nos="";
		$check_buyer_arr = array();
		foreach($po_no as $val)
		{
			if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
			if ($po_file=="") $po_file=$po_array[$val]['file']; else $po_file.=", ".$po_array[$val]['file'];
			if ($po_ref=="") $po_ref=$po_array[$val]['ref']; else $po_ref.=", ".$po_array[$val]['ref'];
			if ($style_ref_nos=="") $style_ref_nos=$style_arr[$val]; else $style_ref_nos.=",".$style_arr[$val];
			if ($job_ref_nos=="") $job_ref_nos=$jobs_arr[$val]; else $job_ref_nos.=",".$jobs_arr[$val];

			if ($req_dia_arr=="") $req_dia_arr=$booking_array[$val][$row[csf("body_part_id")]]['dia_width']; else $req_dia_arr.=",".$booking_array[$val][$row[csf("body_part_id")]]['dia_width'];
			if($check_buyer_arr[$po_array[$val]['buyer']] != $po_array[$val]['buyer'])
			{
				if($po_buyer=="") $po_buyer = $buyer_library[$po_array[$val]['buyer']]; else $po_buyer .=", ".$buyer_library[$po_array[$val]['buyer']];
				$check_buyer_arr[$po_array[$val]['buyer']] = $po_array[$val]['buyer'];
			}
		}
			//$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_nos)));
		$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_nos)));
		$req_dia_arr=implode(",",array_unique(explode(",",$req_dia_arr)));
		$job_nos_ref=implode(",",array_unique(explode(",",$job_ref_nos)));
		$totalQnty +=$row[csf("issue_qnty")];
		$totalRoll +=$row[csf("no_of_roll")];

		if(count($po_no)>0)
		{
			$row_buyer_name = $po_buyer;
		}
		else
		{
			$row_buyer_name = $buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]];
		}
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px;">
			<td width="20"  align="center"><? echo $i; ?></td>
			<td width="90"><div style="width:70px; word-wrap:break-word"><? echo $row_buyer_name;//$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></div></td>
			<td width="120"><div style="width:120px; word-wrap:break-word">
				<?
				echo $job_nos_ref;
                		//echo $job_no;
				?>

			</div></td>
			<td width="120"><div style="width:120px; word-wrap:break-word"><? echo $style_ref_nos; ?></div></td>
			<td width="70"><div style="width:70px; word-wrap:break-word"><? echo $batch_arr[$row[csf("batch_id")]]['no']; ?></div></td>
			<td width="70"><div style="width:70px; word-wrap:break-word"><? echo $color_arr[$batch_arr[$row[csf("batch_id")]]['color_id']]; ?></div></td>
			<td width="200"><div style="width:200px; word-wrap:break-word"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></div></td>
			<td width="70"><div style="width:70px; text-align: right; word-wrap:break-word"><? echo $row[csf("no_of_roll")]; ?></div></td>
			<td width="50" align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
			<td width="50" align="right"><? echo $req_dia_arr; ?></td>
			<td width="100" align="left"><? echo $row[csf("remarks")]; ?></td>

		</tr>
		<? $i++;
	}
	?>
</tbody>
<tfoot>
	<tr>
		<td colspan="7" align="right"><strong>Total :</strong></td>
		<td align="right"><?php echo $totalRoll; ?></td>
		<td align="right"><?php echo number_format($totalQnty, 2); ?></td>
		<td align="right">&nbsp;</td>
		<td align="right">&nbsp;</td>
		<!-- <td align="right" colspan="6"><?php // echo $totalAmount; ?></td> -->
	</tr>
</tfoot>
</table>
<br>
<?
echo signature_table(21, $data[0], "1040px");
?>
</div>
</div>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}



if ($action=="finish_fabric_issue_print_4")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	//print_r ($data);

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company,location_id, buyer_id, cutt_req_no from inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=18";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_location=return_library_array( "select id,location_name from lib_location where company_id='".$dataArray[0][csf('knit_dye_company')]."' and  status_active =1 and is_deleted=0", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	//$buyer_short_name = return_library_array("select id, short_name from lib_buyer","id","short_name");

	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$floor_name_cutting=return_field_value("cutting_unit","inv_finish_fabric_issue_dtls","mst_id='$data[1]'");
	$body_part_library=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );



	$order_ids=''; $batch_ids=''; $prodIds='';
	//$sql_dtls="select id, batch_id, prod_id,rack_no,shelf_no, issue_qnty, no_of_roll, cutting_unit, remarks, order_id, fabric_shade,store_id, body_part_id,uom,floor,room from inv_finish_fabric_issue_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_dtls="select a.id, a.batch_id, a.prod_id,a.rack_no,a.shelf_no, a.issue_qnty, a.no_of_roll, a.cutting_unit, a.remarks, a.order_id, a.fabric_shade,a.store_id, a.body_part_id, a.uom,a.floor,a.room,b.detarmination_id,b.product_name_details ,b.gsm,b.dia_width,b.color from inv_finish_fabric_issue_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.body_part_id";
	//echo $sql_dtls; die;
	$sql_result= sql_select($sql_dtls);
	$comma_var = 1;
	foreach($sql_result as $row)
	{
		$comma = ($comma_var > 1 ? "," : "");
		$order_ids.=$comma.$row[csf("order_id")];
		$batch_ids.=$comma.$row[csf("batch_id")];
		$prodIds.=$comma.$row[csf("prod_id")];
		$comma_var++;

	}
	$order_ids=chop($order_ids,',');
	/*
	$order_ids=chop($order_ids,',');
	$batch_ids=chop($batch_ids,',');
	$prodIds=chop($prodIds,',');*/
	$batch_arr=array();$batch_qnty_with_dia_arr=array();
	$booking_without_order="";
	if($batch_ids != ''){
		$batchDataArr = sql_select("select a.id, a.batch_no, a.color_id,a.booking_no,a.booking_without_order,b.batch_qnty ,c.dia_width,c.gsm,b.body_part_id,c.detarmination_id from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c  where a.id in($batch_ids) and a.id=b.mst_id and a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.body_part_id");
		foreach($batchDataArr as $row)
		{
			$batch_arr[$row[csf('id')]]['no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batch_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			//$batch_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			//$batch_qnty_with_dia_arr[$row[csf('id')]][$row[csf('dia_width')]][$row[csf('gsm')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_qnty_with_dia_arr[$row[csf('id')]][$row[csf('dia_width')]][$row[csf('gsm')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$booking_without_order=$row[csf('booking_without_order')];
		}
	}


	if ($order_ids=="") {
		$batch_buyer_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.booking_no,c.body_part,c.color_type_id,b.buyer_id as buyer_name,a.color_id from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.booking_no_id=b.id
			and b.booking_no=c.booking_no and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id,b.booking_no,c.body_part,c.color_type_id,a.color_id");

		/*if ($booking_without_order==1) {

			$booking_without_order_sql = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
			echo "select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$data[0]";
			foreach ($booking_without_order_sql as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
			}

		}*/
	}
	else
	{
		if ($booking_without_order==1) {
			$batch_buyer_sql = sql_select("select b.mst_id as batch_id, c.buyer_id as buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_no_id=c.id and b.mst_id in($batch_ids) group by  b.mst_id, c.buyer_id");
		}
		else
		{
			$batch_buyer_sql = sql_select("select a.mst_id as batch_id, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($batch_ids) group by  a.mst_id, c.buyer_name");
		}
	}
	$batch_buyer_arr=array();

	foreach($batch_buyer_sql as $row)
	{
		$batch_buyer_arr[$row[csf('batch_id')]]=$row[csf('buyer_name')];
		$color_type_id_arr[$buyer_library[$row[csf("buyer_name")]]][$row[csf("booking_no")]][$row[csf("body_part")]][$row[csf("color_id")]]['color_type_id']=$row[csf("color_type_id")];

	}

	$product_array=array();
	$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and id in($prodIds)");
	foreach($product_sql as $row)
	{
		$fab_type=explode(",",$row[csf("product_name_details")]);
		$product_array[$row[csf("id")]]['product_name_details']=$fab_type[0];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}
	/*echo '<pre>';
	print_r($product_array);die;*/

	$booking_array=array();$color_type_id_arr=array(); $job_no=''; $style_ref_no=''; $po_array=array();
	if($order_ids!="")
	{

		/*$booking_sql = sql_select("select b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id,c.color_type_id,d.id as batch_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,pro_batch_create_mst d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.id=d.booking_no_id and a.booking_no=d.booking_no  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($order_ids) and d.id in($batch_ids) group by b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id, c.color_type_id,d.id");*/

		$booking_sql = sql_select("select a.id,a.booking_no_id,a.color_id,b.po_id,b.body_part_id,b.item_description,c.color_type,c.dia_width,c.gsm_weight from pro_batch_create_mst a,pro_batch_create_dtls b,wo_booking_dtls c where a.id=b.mst_id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id in($order_ids) and a.id in($batch_ids) and a.booking_without_order=0 group by a.id,a.booking_no_id,b.body_part_id,a.color_id,b.po_id,b.item_description,c.color_type,c.dia_width,c.gsm_weight order by b.body_part_id");


		foreach($booking_sql as $row)
		{
			/*$booking_array[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]]['gsm_weight']=$row[csf("gsm_weight")];
			$booking_array[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]]['dia_width'].=$row[csf("dia_width")].',';
			$color_type_id_arr[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]['color_type_id']=$row[csf("color_type_id")];*/

			$itemDescripts=explode(',',$row[csf("item_description")]);
			$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
			$gsm_weight=trim($itemDescripts[2]);
			$dia_width=trim($itemDescripts[3]);

			$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription]['gsm_weight']=$gsm_weight;
			$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription]['dia_width']=$dia_width;
			$color_type_id_arr[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription][$row[csf("gsm_weight")]][$row[csf("dia_width")]]['color_type_id']=$row[csf("color_type")];
		}


		/*echo '<pre>';
		print_r($booking_array);
		//die;*/

		$sql_job="select a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($order_ids)";
		//echo $sql_job; die;
		$result_sql_job=sql_select($sql_job);
		$style_arr=array();
		foreach($result_sql_job as $row)
		{
			if($job_no=='') $job_no=$row[csf("job_no_mst")]; else $job_no.=','.$row[csf("job_no_mst")];
			if($style_ref_no=='') $style_ref_no=$row[csf("style_ref_no")]; else $style_ref_no.=','.$row[csf("style_ref_no")];
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("grouping")];
			$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
			$po_array[$row[csf("id")]]['job_no']=$row[csf("job_no_mst")];
			$style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
			$poNumber_arr[$row[csf("po_number")]]['po_id']=$row[csf("id")];
		}
	}
	else
	{
		$booking_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.booking_no,d.body_part_id,c.color_type_id,b.buyer_id as buyer_name,a.color_id,c.lib_yarn_count_deter_id,c.gsm_weight,c.dia_width,sum(d.batch_qnty) as batch_qnty,d.item_description  from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c,pro_batch_create_dtls d where a.booking_no_id=b.id
			and b.booking_no=c.booking_no and a.id=d.mst_id and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id,b.booking_no,d.body_part_id,c.color_type_id,a.color_id,c.lib_yarn_count_deter_id,c.gsm_weight,c.dia_width,d.item_description");


		foreach($booking_sql as $row)
		{

			$itemDescripts=explode(',',$row[csf("item_description")]);
			$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
			$gsm_weight=trim($itemDescripts[2]);
			$dia_width=trim($itemDescripts[3]);

			$booking_array[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]]['gsm_weight']=$gsm_weight;
			$booking_array[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]]['dia_width']=$dia_width;
			//$batch_qnty_with_dia_arr[$row[csf('booking_no')]][$row[csf('batch_id')]][$dia_width][$gsm_weight][$row[csf('body_part_id')]][$row[csf('color_id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$color_type_id_arr[$row[csf('booking_no')]][$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]][$gsm_weight][$dia_width]['color_type_id']=$row[csf("color_type_id")];

		}
	}
	/*echo '<pre>';
		print_r($color_type_id_arr);
		//die;*/

		$job_no=implode(",",array_unique(explode(",",$job_no)));
		$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));


		if($dataArray[0][csf('knit_dye_source')]==3)
		{
			$sql_res=sql_select("select a.id,a.address_1 from lib_supplier a,  lib_supplier_tag_company b where a.id=b.supplier_id and a.id=".$dataArray[0][csf('knit_dye_company')]." and a.status_active=1   group by a.id,a.address_1 order by a.id ");
			$sewing_com_address=$sql_res[0][csf('address_1')];
		}
		else if ($dataArray[0][csf('knit_dye_source')]==1)
		{
			$sql_res=sql_select("select a.id,a.city,a.plot_no from lib_company a where a.id=".$dataArray[0][csf('knit_dye_source')]."  and a.status_active=1  order by a.id");
			$sewing_com_address=$sql_res[0][csf('city')].', '.$sql_res[0][csf('plot_no')];
		}
		?>
		<div style="width:1250px;">
			<table width="1250" cellspacing="0" >
				<tr>
					<td rowspan="3"  valign="middle">
						<?
						$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						foreach($data_array2 as $img_row)
						{
							?>
							<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='170' align="middle" />
							<?
						}
						?>
					</td>
					<td colspan="5" align="center" style="font-size:22px"><strong><? echo   $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="5" align="center">
						<?
                //echo show_company($data[0],'','');
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
							echo $result[csf('plot_no')];
							if($result[csf('plot_no')]!="") echo ", ";
							echo $result[csf('level_no')];
							if($result[csf('level_no')]!="") echo ", ";
							echo $result[csf('road_no')];
							if($result[csf('road_no')]!="") echo ", ";
							echo $result[csf('block_no')];
							if($result[csf('block_no')]!="") echo ", ";
							echo $result[csf('city')];
							if($result[csf('city')]!="") echo ", ";
							echo $result[csf('zip_code')];
							if($result[csf('zip_code')]!="") echo ", ";
							echo $result[csf('country_id')];
							if($result[csf('country_id')]!="") echo ", ";
							echo "<br> ";
							if($result[csf('email')]!="") echo "Email Address: ".$result[csf('email')];
							if($result[csf('website')]!="") echo "Website No: ".$result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
				</tr>
				<tr>
					<td width="130"><strong>Issue ID :</strong></td><td width="250"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="130"><strong>Issue Purpose:</strong></td> <td width="150"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
					<td width="130"><strong>Cutting Unit:</strong></td><td  width="150"><? echo $cutting_floor_library[$floor_name_cutting]; ?></td>
					<td width="130"><strong>Sample Type:</strong></td><td><? echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Issue Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
					<td><strong>Challan No:</strong></td><td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Cutt. Req. No:</strong></td><td ><? echo $dataArray[0][csf('cutt_req_no')]; ?></td>
					<td><strong>Sewing Source:</strong></td> <td ><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Sewing Com:</strong></td><td style="word-break:break-all"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address;
					?></td>
					<!-- <td><strong>Job No</strong></td> <td><p><? //echo $job_no; ?></p></td> -->
					<td></td><td></td>
					<td><strong>Sewing Location:</strong></td><td ><? echo $company_location[$dataArray[0][csf('location_id')]]; ?></td>
					<!--  <td><strong>Style Ref. No: </strong></td><td><b><? //echo $style_ref_no; ?></b></td> -->
				</tr>
				<tr style=" height:25px">
					<td></td><td></td>
				</tr>
        <!-- <tr style=" height:25px">
            <td colspan="4"></td>
             <td colspan="2" id="barcode_img_id" align="right"></td>
         </tr> -->
     </table>
     <br>
     <table cellspacing="0" width="1560"  border="1" rules="all" class="rpt_table" >
     	<thead bgcolor="#dddddd">
     		<th width="20">SL</th>
     		<th width="80">Buyer</th>
     		<th width="120">Job No/Booking</th>
     		<th width="100">Style No</th>
     		<th width="110">Order No</th>
     		<th width="80">Batch No</th>
     		<th width="110" >Color</th>
     		<th width="110" >Color Type</th>

           <!--  <th width="60">File No</th>
           	<th width="60">Ref. No</th> -->
           	<th width="130">Const. &#38; Comp.</th>
           	<th width="80">Dia Wise Batch Qty(Kg)</th>
           	<th width="50" >Roll</th>
            <!-- <th width="50" >Rack</th>
            	<th width="50" >Shelf</th> -->
            	<th width="60" >Issue Qty</th>
            	<!-- <th width="50" >UOM</th> -->
            	<th width="100" >Req. Dia</th>
            	<th width="50" >Actual Dia</th>
            	<th width="50" >Req. GSM</th>
            	<th width="50" >Actual GSM</th>
            	<!-- <th width="100" >Store</th> -->
            	<th width="50" >Fab. Shade</th>
            	<th>Remarks</th>
            </thead>
            <tbody>
            	<?

            	$i=1;
            	//$req_dia_arr="";
            	foreach($sql_result as $row)
            	{

            		if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

            		$po_no=array_filter(array_unique(explode(",",$row[csf("order_id")])));
            		$order_nos="";$style_ref_nos="";$req_dia_arr="";$req_gsm_arr="";$po_file="";$po_ref="";$po_buyer="";$job_no="";$color_type_id="";


            		foreach($po_no as $val)
            		{
            			if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
            			if ($po_file=="") $po_file=$po_array[$val]['file']; else $po_file.=", ".$po_array[$val]['file'];
            			if ($po_ref=="") $po_ref=$po_array[$val]['ref']; else $po_ref.=", ".$po_array[$val]['ref'];
            			if ($job_no=="") $job_no=$po_array[$val]['job_no']; else $job_no.=", ".$po_array[$val]['job_no'];
            			if ($style_ref_nos=="") $style_ref_nos=$style_arr[$val]; else $style_ref_nos.=",".$style_arr[$val];
            			/*if ($req_dia_arr=="") $req_dia_arr=$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['dia_width']; else $req_dia_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['dia_width'];
            			if ($req_gsm_arr=="") $req_gsm_arr=$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['gsm_weight']; else $req_gsm_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['gsm_weight'];*/

            			/*$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]]['gsm_weight']=$row[csf("gsm_weight")];*/

            			$itemDescripts=explode(',',$row[csf("product_name_details")]);
            			$itemDescription=$itemDescripts[0].','.$itemDescripts[1];

            			if ($req_dia_arr=="") $req_dia_arr=$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['dia_width']; else $req_dia_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['dia_width'];
            			if ($req_gsm_arr=="") $req_gsm_arr=$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['gsm_weight']; else $req_gsm_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['gsm_weight'];



            			//echo '#'.$row[csf("batch_id")].'='.$val.'='.$batch_arr[$row[csf("batch_id")]]['color_id'].'='.$row[csf("body_part_id")].'='.$itemDescription.'===';

            			//if ($color_type_id=="") $color_type_id=$booking_array[$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['color_type_id'];
            			//else $color_type_id.=",".$booking_array[$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['color_type_id'];


            			/*if ($color_type_id=="") $color_type_id=$color_type_id_arr[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("gsm")]][$row[csf("dia_width")]]['color_type_id'];
            			else $color_type_id.=",".$color_type_id=$color_type_id_arr[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("gsm")]][$row[csf("dia_width")]]['color_type_id'];
	*/



            			if($po_buyer=="") $po_buyer = $buyer_library[$po_array[$val]['buyer']];else $po_buyer .=",".$buyer_library[$po_array[$val]['buyer']];
            		}

            		if(count($po_no)>0)
            		{
            			$row_buyer_name = $po_buyer;
            		}else{
            			$row_buyer_name = $buyer_library[$batch_buyer_arr[$row[csf('batch_id')]]];
            		}
            		$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_nos)));
            		$req_dia_arr=implode(",",array_unique(explode(",",$req_dia_arr)));
            		$req_gsm_arr=implode(",",array_unique(explode(",",$req_gsm_arr)));
            		//$color_type_id=implode(",",array_unique(explode(",",$color_type_id)));

            		$totalQnty +=$row[csf("issue_qnty")];
            		$totalRoll +=$row[csf("no_of_roll")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["issue_qnty"]+=$row[csf("issue_qnty")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["act_dia_width"]=$req_dia_arr;

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["dia_width"]=$product_array[$row[csf("prod_id")]]['dia_width'];



            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["gsm"]=$product_array[$row[csf("prod_id")]]['gsm'];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["store_id"]=$store_library[$row[csf("store_id")]];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["uom"]=$unit_of_measurement[$row[csf("uom")]];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["remarks"]=$row[csf("remarks")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["no_of_roll"]=$row[csf("no_of_roll")];
            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["job_no"]=$job_no;
            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
            	}

            	/*echo '<pre>';
            	echo $color_type_id;
            	//print_r($color_type_id);
            	die;*/
            	$booking_tot_btchQnty_grnd=0;
            	$booking_batch_wise_span=array();
            	foreach($main_array as $booking_id=>$batch_data)
            	{
            		foreach($batch_data as $batch_id=>$buyer_data)
            		{
            			$span=0;
            			foreach($buyer_data as $buyer_id=>$style_data)
            			{
            				foreach($style_data as $style_id=>$order_data)
            				{
            					foreach($order_data as $order_id=>$color_data)
            					{
            						foreach($color_data as $color_id=>$prod_dtls_data)
            						{
            							foreach($prod_dtls_data as $prod_dtls_id=>$shade_data)
            							{
            								foreach($shade_data as $shade_id=>$roll_data)
            								{
            									foreach($roll_data as $roll_id=>$bodyPart)
            									{
            										foreach($bodyPart as $bodyPartId=>$detarminationData)
            										{
            											foreach($detarminationData as $detarminationId=>$id_data)
            											{
            												foreach($id_data as $id=>$row)
            												{
            													$span++;
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
            			$booking_batch_wise_span[$booking_id][$batch_id]=$span;
            		}

            	}

            	$p=1;
            	foreach($main_array as $booking_id=>$batch_data)
            	{
            		$booking_tot_btchQnty="";
            		$booking_tot_roll="";
            		$booking_tot_issue="";
            		foreach($batch_data as $batch_id=>$buyer_data)
            		{
            			$kk=0;
            			$batch_tot_Qnty="";
            			$batch_tot_roll="";
            			$batch_tot_issue="";
            			$batch_tot_qty = "";
            			$batch_tot_issue="";

            			foreach($buyer_data as $buyer_id=>$style_data)
            			{
            				foreach($style_data as $style_id=>$order_data)
            				{
            					foreach($order_data as $order_id=>$color_data)
            					{
            						foreach($color_data as $color_id=>$prod_dtls_data)
            						{
            							foreach($prod_dtls_data as $prod_dtls_id=>$shade_data)
            							{
            								foreach($shade_data as $shade_id=>$roll_data)
            								{
            									foreach($roll_data as $roll_id=>$bodyPart)
            									{
            										foreach($bodyPart as $bodyPartId=>$detarminationData)
            										{
            											foreach($detarminationData as $detarminationId=>$id_data)
            											{
            												foreach($id_data as $dtls_id=>$row)
            												{
            													if ($order_id!="") {
            														$itemDescripts=explode(',',$row['product_name_details']);
            														$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
            														$req_gsm_arr=$booking_array[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription]['gsm_weight']*1;

            														$req_dia_arr=$booking_array[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription]['dia_width']*1;

            														$colorType_id=$color_type_id_arr[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription][$req_gsm_arr][$req_dia_arr]['color_type_id'];
            													}
            													else{
            														$req_dia= $booking_array[$batch_id][$bodyPartId][$detarminationId][$color_id]['dia_width'];
            														$req_gsm=  $booking_array[$batch_id][$bodyPartId][$detarminationId][$color_id]['gsm_weight'];
            														$colorTypeID=$color_type_id_arr[$booking_id][$batch_id][$bodyPartId][$detarminationId][$color_id][$req_gsm][$req_dia]['color_type_id'];
            													}



            													?>
            													<tr bgcolor="<? echo $bgcolor; ?>">
            														<?
            														if($kk==0)
            														{
            															?>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>" align="center"><? echo $p; ?></td>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><?
            															$buyer_id= implode(",",array_unique(explode(",",$buyer_id)));
            															echo $buyer_id;
            															$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></td>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><? echo
            															'<strong>J :</strong> '.$row['job_no'].'<br><strong>B:</strong> '.$booking_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><? echo $style_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $order_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $batch_arr[$batch_id]['no']; ?></td>
            															<td align="center"  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $color_arr[$batch_arr[$batch_id]['color_id']]; ?></td>

            															<?
            															$kk++;
            															$p++;
            														}

            														?>
				 												<td align="center"   style="word-break:break-all;"><? //echo $color_type[$color_type_id];

				 												if ($order_id!="") {
				 													echo $color_type[$colorType_id];

				 													//echo $color_type[$color_type_id_arr[$batch_id][$poNumber_arr[$order_id]['po_id']][$bodyPartId][$detarminationId][$color_id][$row["gsm"]][chop($row['act_dia_width'],',')]['color_type_id']];
				 												}
				 												else
				 												{
				 													//echo $color_type[$color_type_id_arr[$buyer_id][$booking_id][$bodyPartId][$color_id]['color_type_id']];
				 													echo $color_type[$colorTypeID];
				 												}
				 												?></td>
				 												<td title="<? echo $body_part_library[$bodyPartId]; ?>" style="word-break:break-all;"><? echo $row['product_name_details']; //$prod_dtls_id; ?></td>
				 												<td  title="Required Dia,GSM wise Batch Quantity" align="right" style="word-break:break-all;"><?
				 												//echo $batch_id.'='.$row['dia_width'].'='.$row['gsm'].'='.$bodyPartId.'='.$color_id;
				 												if ($order_id!="") {
				 													echo $batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row["gsm"]][$bodyPartId][$color_id]['batch_qnty'];
				 													$batch_tot_Qnty+=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row["gsm"]][$bodyPartId][$color_id]['batch_qnty'];
				 												}
				 												else
				 												{
				 													echo $batchQntyTot= $batch_qnty_with_dia_arr[$batch_id][$req_dia][$req_gsm][$bodyPartId][$color_id]['batch_qnty'];
				 													$batch_tot_Qnty+=$batchQntyTot;
				 												}
				 												?></td>
				 												<td align="right"><? echo $roll_id; ?></td>
				 												<td align="right"><? echo number_format($row["issue_qnty"],2); ?></td>
				 												<!-- <td align="center"><? //echo $row["uom"]; ?></td> -->
				 												<td style="word-wrap: break-word; word-break: break-all;" width="100" align="center"><p>
				 													<?
				 													if ($order_id!="") {
				 														echo $req_dia_arr;
				 														//echo chop($row['act_dia_width'],",");
				 													}
				 													else
				 													{
				 														echo $req_dia;
				 													}
				 													?>
				 												</p></td>
				 												<td align="center"><? echo $row['dia_width']; //$product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
				 												<td align="center">
				 													<?
				 													if ($order_id!="") {
				 														echo $req_gsm_arr;
				 													}
				 													else
				 													{
				 														echo $req_gsm;
				 													}
				 													?>
				 												</td>
				 												<td align="center"><? echo $row["gsm"]; ?></td>
				 												<!-- <td style="word-break:break-all;"><? //echo $row["store_id"]; ?></td> -->
				 												<td align="right"><? echo $shade_id; ?></td>
				 												<td style="word-break:break-all;"><? echo $row["remarks"]; ?></td>
				 											</tr>
				 											<?
				 											$batch_tot_roll+=$roll_id;
				 											$batch_tot_issue+=$row["issue_qnty"];
				 											//$batch_tot_qty +=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row['gsm']]['batch_qnty'];
				 											//$batch_tot_qty +=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row['gsm']][$bodyPartId][$color_id]['batch_qnty'];
						 									//}

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
				 	$booking_tot_btchQnty+=$batch_tot_Qnty;
				 	$booking_tot_roll+=$batch_tot_roll;
				 	$booking_tot_issue+=$batch_tot_issue;
				 	$booking_tot_btchQnty_grnd+=$batch_tot_Qnty;
				 	?>
				 	<tr>
				 		<td colspan="9" align="right"><strong>Dia Wise Batch Total :</strong></td>
				 		<td align="right" style="font-weight: bold;"><?php echo $batch_tot_Qnty; ?></td>
				 		<td align="right" style="font-weight: bold;"><?php echo $batch_tot_roll; ?></td>
				 		<!-- <td> </td> <td> </td> -->
				 		<td align="right" style="font-weight: bold;"><?php echo number_format($batch_tot_issue, 2); ?></td>
				 		<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
				 	</tr>
				 	<?

				 }
				 ?>
				 <tr>
				 	<td colspan="9" align="right"><strong>Booking Total :</strong></td>
				 	<td align="right" style="font-weight: bold;"><?php echo $booking_tot_btchQnty; ?></td>
				 	<td align="right" style="font-weight: bold;"><?php echo $booking_tot_roll; ?></td>
				 	<!-- <td> </td> <td> </td> -->
				 	<td align="right" style="font-weight: bold;"><?php echo number_format($booking_tot_issue, 2); ?></td>
				 	<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
				 </tr>
				 <?
				}



		    /*foreach($data_array as $booking_nos => $booking_nos_data)
		    {
		        foreach($booking_nos_data as $batch_ids => $batch_ids_data)
		        {
		            $y = 1;
		            foreach($batch_ids_data as $rollNos => $row)
		            {
		            	echo "<pre>";
		            	echo print_r($row);
		            	echo "</pre>";
		            	?>
						<tr bgcolor="<? echo $bgcolor; ?>">
						    <td align="center"><? echo $i; ?></td>
						    <td style="word-break:break-all"><? echo $row_buyer_name;//$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></td>
						    <td style="word-break:break-all"><? echo $batch_arr[$row[csf("batch_id")]]['booking_no']; ?></td>
						    <td style="word-break:break-all"><? echo $style_ref_nos; ?></td>

						    <td style="word-break:break-all;"><? echo $order_nos; ?></td>
						    <td style="word-break:break-all;"><? echo $batch_arr[$row[csf("batch_id")]]['no']; ?></td>
						    <td style="word-break:break-all;"><? echo $batch_arr[$row[csf("batch_id")]]['batch_qnty']; ?></td>
						    <td style="word-break:break-all;"><? echo $color_arr[$batch_arr[$row[csf("batch_id")]]['color_id']]; ?></td>

						    <!-- <td style="word-break:break-all;"><? //echo $po_file; ?></td>
						    <td style="word-break:break-all;"><? //echo $po_ref; ?></td> -->
						    <td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
						    <td align="right"><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></td>
						    <td align="right"><? echo $row[csf("no_of_roll")]; ?></td>

						    <!-- <td align="right"><?  //echo $rack_name; ?></td>
						    <td align="right"><? //echo $shelf_name; ?></td> -->
						    <td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
						    <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						    <td align="center"><? echo $req_dia_arr;//$booking_array[$row[csf("order_id")]]['dia_width']; ?></td>
						    <td align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
						    <td align="center"><? echo $req_gsm_arr;//$booking_array[$row[csf("order_id")]]['gsm_weight']; ?></td>
						    <td align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
						    <td style="word-break:break-all;"><? echo $store_library[$row[csf("store_id")]]; ?></td>
						    <td style="word-break:break-all;"><? echo $row[csf("remarks")]; ?></td>
						</tr>
				<? //$i++;
					}
				}
			}*/
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9" align="right"><strong>Grand Total :</strong></td>
				<td align="right" style="font-weight: bold;"><?php echo $booking_tot_btchQnty_grnd; ?></td>
				<td align="right" style="font-weight: bold;"><?php echo $totalRoll; ?></td>
				<!-- <td> </td> <td> </td> -->
				<td align="right" style="font-weight: bold;"><?php echo number_format($totalQnty, 2); ?></td>
				<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
			</tr>
		</tfoot>
	</table>
	<br>
	<?
	echo signature_table(21, $data[0], "1250px");
	?>
</div>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}


if ($action=="finish_fabric_issue_print_5")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	//print_r ($data);

	$sql="select id, issue_number, issue_purpose, sample_type, issue_date, challan_no, knit_dye_source, knit_dye_company,location_id, buyer_id, cutt_req_no from inv_issue_master where id='$data[1]' and company_id='$data[0]' and entry_form=18";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_location=return_library_array( "select id,location_name from lib_location where company_id='".$dataArray[0][csf('knit_dye_company')]."' and  status_active =1 and is_deleted=0", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	//$buyer_short_name = return_library_array("select id, short_name from lib_buyer","id","short_name");

	$sample_arr=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$floor_name_cutting=return_field_value("cutting_unit","inv_finish_fabric_issue_dtls","mst_id='$data[1]'");
	$body_part_library=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );



	$order_ids=''; $batch_ids=''; $prodIds='';
	//$sql_dtls="select id, batch_id, prod_id,rack_no,shelf_no, issue_qnty, no_of_roll, cutting_unit, remarks, order_id, fabric_shade,store_id, body_part_id,uom,floor,room from inv_finish_fabric_issue_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_dtls="select a.id, a.batch_id, a.prod_id,a.rack_no,a.shelf_no, a.issue_qnty, a.no_of_roll, a.cutting_unit, a.remarks, a.order_id, a.fabric_shade,a.store_id, a.body_part_id, a.uom,a.floor,a.room,b.detarmination_id,b.product_name_details ,b.gsm,b.dia_width,b.color from inv_finish_fabric_issue_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.body_part_id";
	//echo $sql_dtls; die;
	$sql_result= sql_select($sql_dtls);
	$comma_var = 1;
	foreach($sql_result as $row)
	{
		$comma = ($comma_var > 1 ? "," : "");
		$order_ids.=$comma.$row[csf("order_id")];
		$batch_ids.=$comma.$row[csf("batch_id")];
		$prodIds.=$comma.$row[csf("prod_id")];
		$comma_var++;

	}
	$order_ids=chop($order_ids,',');
	/*
	$order_ids=chop($order_ids,',');
	$batch_ids=chop($batch_ids,',');
	$prodIds=chop($prodIds,',');*/
	$batch_arr=array();$batch_qnty_with_dia_arr=array();
	$booking_without_order="";
	if($batch_ids != ''){
		$batchDataArr = sql_select("select a.id, a.batch_no, a.color_id,a.booking_no,a.booking_without_order,b.batch_qnty ,c.dia_width,c.gsm,b.body_part_id,c.detarmination_id from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c  where a.id in($batch_ids) and a.id=b.mst_id and a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.body_part_id");
		foreach($batchDataArr as $row)
		{
			$batch_arr[$row[csf('id')]]['no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batch_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			//$batch_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			//$batch_qnty_with_dia_arr[$row[csf('id')]][$row[csf('dia_width')]][$row[csf('gsm')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_qnty_with_dia_arr[$row[csf('id')]][$row[csf('dia_width')]][$row[csf('gsm')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$booking_without_order=$row[csf('booking_without_order')];
		}
	}


	if ($order_ids=="") {
		$batch_buyer_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.booking_no,c.body_part,c.color_type_id,b.buyer_id as buyer_name,a.color_id from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.booking_no_id=b.id
			and b.booking_no=c.booking_no and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id,b.booking_no,c.body_part,c.color_type_id,a.color_id");

		/*if ($booking_without_order==1) {

			$booking_without_order_sql = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
			echo "select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$data[0]";
			foreach ($booking_without_order_sql as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
			}

		}*/
	}
	else
	{
		if ($booking_without_order==1) {
			$batch_buyer_sql = sql_select("select b.mst_id as batch_id, c.buyer_id as buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_no_id=c.id and b.mst_id in($batch_ids) group by  b.mst_id, c.buyer_id");
		}
		else
		{
			$batch_buyer_sql = sql_select("select a.mst_id as batch_id, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($batch_ids) group by  a.mst_id, c.buyer_name");
		}
	}
	$batch_buyer_arr=array();

	foreach($batch_buyer_sql as $row)
	{
		$batch_buyer_arr[$row[csf('batch_id')]]=$row[csf('buyer_name')];
		$color_type_id_arr[$buyer_library[$row[csf("buyer_name")]]][$row[csf("booking_no")]][$row[csf("body_part")]][$row[csf("color_id")]]['color_type_id']=$row[csf("color_type_id")];

	}

	$product_array=array();
	$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and id in($prodIds)");
	foreach($product_sql as $row)
	{
		$fab_type=explode(",",$row[csf("product_name_details")]);
		$product_array[$row[csf("id")]]['product_name_details']=$fab_type[0];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}
	/*echo '<pre>';
	print_r($product_array);die;*/

	$booking_array=array();$color_type_id_arr=array(); $job_no=''; $style_ref_no=''; $po_array=array();
	if($order_ids!="")
	{

		/*$booking_sql = sql_select("select b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id,c.color_type_id,d.id as batch_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,pro_batch_create_mst d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.id=d.booking_no_id and a.booking_no=d.booking_no  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($order_ids) and d.id in($batch_ids) group by b.po_break_down_id, b.gsm_weight, b.dia_width, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id, c.color_type_id,d.id");*/

		$booking_sql = sql_select("select a.id,a.booking_no_id,a.color_id,b.po_id,b.body_part_id,b.item_description,c.color_type,c.dia_width,c.gsm_weight from pro_batch_create_mst a,pro_batch_create_dtls b,wo_booking_dtls c where a.id=b.mst_id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id in($order_ids) and a.id in($batch_ids) and a.booking_without_order=0 group by a.id,a.booking_no_id,b.body_part_id,a.color_id,b.po_id,b.item_description,c.color_type,c.dia_width,c.gsm_weight order by b.body_part_id");


		foreach($booking_sql as $row)
		{
			/*$booking_array[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]]['gsm_weight']=$row[csf("gsm_weight")];
			$booking_array[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]]['dia_width'].=$row[csf("dia_width")].',';
			$color_type_id_arr[$row[csf("batch_id")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]['color_type_id']=$row[csf("color_type_id")];*/

			$itemDescripts=explode(',',$row[csf("item_description")]);
			$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
			$gsm_weight=trim($itemDescripts[2]);
			$dia_width=trim($itemDescripts[3]);

			$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription]['gsm_weight']=$gsm_weight;
			$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription]['dia_width']=$dia_width;
			$color_type_id_arr[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$itemDescription][$row[csf("gsm_weight")]][$row[csf("dia_width")]]['color_type_id']=$row[csf("color_type")];
		}


		/*echo '<pre>';
		print_r($booking_array);
		//die;*/

		$sql_job="select a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($order_ids)";
		//echo $sql_job; die;
		$result_sql_job=sql_select($sql_job);
		$style_arr=array();
		foreach($result_sql_job as $row)
		{
			if($job_no=='') $job_no=$row[csf("job_no_mst")]; else $job_no.=','.$row[csf("job_no_mst")];
			if($style_ref_no=='') $style_ref_no=$row[csf("style_ref_no")]; else $style_ref_no.=','.$row[csf("style_ref_no")];
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("grouping")];
			$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
			$po_array[$row[csf("id")]]['job_no']=$row[csf("job_no_mst")];
			$style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
			$poNumber_arr[$row[csf("po_number")]]['po_id']=$row[csf("id")];
		}
	}
	else
	{
		$booking_sql = sql_select("select a.id as batch_id, a.booking_no_id,b.booking_no,d.body_part_id,c.color_type_id,b.buyer_id as buyer_name,a.color_id,c.lib_yarn_count_deter_id,c.gsm_weight,c.dia_width,sum(d.batch_qnty) as batch_qnty,d.item_description  from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c,pro_batch_create_dtls d where a.booking_no_id=b.id
			and b.booking_no=c.booking_no and a.id=d.mst_id and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id in($batch_ids) group by a.id , a.booking_no_id,b.buyer_id,b.booking_no,d.body_part_id,c.color_type_id,a.color_id,c.lib_yarn_count_deter_id,c.gsm_weight,c.dia_width,d.item_description");


		foreach($booking_sql as $row)
		{

			$itemDescripts=explode(',',$row[csf("item_description")]);
			$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
			$gsm_weight=trim($itemDescripts[2]);
			$dia_width=trim($itemDescripts[3]);

			$booking_array[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]]['gsm_weight']=$gsm_weight;
			$booking_array[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]]['dia_width']=$dia_width;
			//$batch_qnty_with_dia_arr[$row[csf('booking_no')]][$row[csf('batch_id')]][$dia_width][$gsm_weight][$row[csf('body_part_id')]][$row[csf('color_id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$color_type_id_arr[$row[csf('booking_no')]][$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("color_id")]][$gsm_weight][$dia_width]['color_type_id']=$row[csf("color_type_id")];

		}
	}
	/*echo '<pre>';
		print_r($color_type_id_arr);
		//die;*/

		$job_no=implode(",",array_unique(explode(",",$job_no)));
		$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));


		if($dataArray[0][csf('knit_dye_source')]==3)
		{
			$sql_res=sql_select("select a.id,a.address_1 from lib_supplier a,  lib_supplier_tag_company b where a.id=b.supplier_id and a.id=".$dataArray[0][csf('knit_dye_company')]." and a.status_active=1   group by a.id,a.address_1 order by a.id ");
			$sewing_com_address=$sql_res[0][csf('address_1')];
		}
		else if ($dataArray[0][csf('knit_dye_source')]==1)
		{
			$sql_res=sql_select("select a.id,a.city,a.plot_no from lib_company a where a.id=".$dataArray[0][csf('knit_dye_source')]."  and a.status_active=1  order by a.id");
			$sewing_com_address=$sql_res[0][csf('city')].', '.$sql_res[0][csf('plot_no')];
		}
		?>
		<div style="width:1250px;">
			<table width="1250" cellspacing="0" >
				<tr>
					<td rowspan="3"  valign="middle">
						<?
						$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						foreach($data_array2 as $img_row)
						{
							?>
							<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='170' align="middle" />
							<?
						}
						?>
					</td>
					<td colspan="5" align="center" style="font-size:22px"><strong><? echo   $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="5" align="center">
						<?
                //echo show_company($data[0],'','');
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
							echo $result[csf('plot_no')];
							if($result[csf('plot_no')]!="") echo ", ";
							echo $result[csf('level_no')];
							if($result[csf('level_no')]!="") echo ", ";
							echo $result[csf('road_no')];
							if($result[csf('road_no')]!="") echo ", ";
							echo $result[csf('block_no')];
							if($result[csf('block_no')]!="") echo ", ";
							echo $result[csf('city')];
							if($result[csf('city')]!="") echo ", ";
							echo $result[csf('zip_code')];
							if($result[csf('zip_code')]!="") echo ", ";
							echo $result[csf('country_id')];
							if($result[csf('country_id')]!="") echo ", ";
							echo "<br> ";
							if($result[csf('email')]!="") echo "Email Address: ".$result[csf('email')];
							if($result[csf('website')]!="") echo "Website No: ".$result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
				</tr>
				<tr>
					<td width="130"><strong>Issue ID :</strong></td><td width="250"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="130"><strong>Issue Purpose:</strong></td> <td width="150"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
					<td width="130"><strong>Cutting Unit:</strong></td><td  width="150"><? echo $cutting_floor_library[$floor_name_cutting]; ?></td>
					<td width="130"><strong>Sample Type:</strong></td><td><? echo $sample_arr[$dataArray[0][csf('sample_type')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Issue Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
					<td><strong>Challan No:</strong></td><td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Cutt. Req. No:</strong></td><td ><? echo $dataArray[0][csf('cutt_req_no')]; ?></td>
					<td><strong>Sewing Source:</strong></td> <td ><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Sewing Com:</strong></td><td style="word-break:break-all"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]].', '. $sewing_com_address;
					?></td>
					<!-- <td><strong>Job No</strong></td> <td><p><? //echo $job_no; ?></p></td> -->
					<td></td><td></td>
					<td><strong>Sewing Location:</strong></td><td ><? echo $company_location[$dataArray[0][csf('location_id')]]; ?></td>
					<!--  <td><strong>Style Ref. No: </strong></td><td><b><? //echo $style_ref_no; ?></b></td> -->
				</tr>
				<tr style=" height:25px">
					<td></td><td></td>
				</tr>
        <!-- <tr style=" height:25px">
            <td colspan="4"></td>
             <td colspan="2" id="barcode_img_id" align="right"></td>
         </tr> -->
     </table>
     <br>
     <table cellspacing="0" width="1560"  border="1" rules="all" class="rpt_table" >
     	<thead bgcolor="#dddddd">
     		<th width="20">SL</th>
     		<th width="80">Buyer</th>
     		<th width="120">Job No/Booking</th>
     		<th width="100">Style No</th>
     		<th width="110">Order No</th>
     		<th width="80">Batch No</th>
     		<th width="110" >Color</th>
     		<th width="110" >Color Type</th>

           <!--  <th width="60">File No</th>
           	<th width="60">Ref. No</th> -->
           	<th width="130">Const. &#38; Comp.</th>
           	<th width="80">Dia Wise Batch Qty(Kg)</th>
           	<th width="50" >Roll</th>
            <!-- <th width="50" >Rack</th>
            	<th width="50" >Shelf</th> -->
            	<th width="60" >Issue Qty</th>
            	<!-- <th width="50" >UOM</th> -->
            	<th width="100" >Req. Dia</th>
            	<th width="50" >Actual Dia</th>
            	<th width="50" >Req. GSM</th>
            	<th width="50" >Actual GSM</th>
            	<!-- <th width="100" >Store</th> -->
            	<th width="50" >Fab. Shade</th>
            	<th>Remarks</th>
            </thead>
            <tbody>
            	<?

            	$i=1;
            	//$req_dia_arr="";
            	foreach($sql_result as $row)
            	{

            		if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

            		$po_no=array_filter(array_unique(explode(",",$row[csf("order_id")])));
            		$order_nos="";$style_ref_nos="";$req_dia_arr="";$req_gsm_arr="";$po_file="";$po_ref="";$po_buyer="";$job_no="";$color_type_id="";


            		foreach($po_no as $val)
            		{
            			if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
            			if ($po_file=="") $po_file=$po_array[$val]['file']; else $po_file.=", ".$po_array[$val]['file'];
            			if ($po_ref=="") $po_ref=$po_array[$val]['ref']; else $po_ref.=", ".$po_array[$val]['ref'];
            			if ($job_no=="") $job_no=$po_array[$val]['job_no']; else $job_no.=", ".$po_array[$val]['job_no'];
            			if ($style_ref_nos=="") $style_ref_nos=$style_arr[$val]; else $style_ref_nos.=",".$style_arr[$val];
            			/*if ($req_dia_arr=="") $req_dia_arr=$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['dia_width']; else $req_dia_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['dia_width'];
            			if ($req_gsm_arr=="") $req_gsm_arr=$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['gsm_weight']; else $req_gsm_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['gsm_weight'];*/

            			/*$booking_array[$row[csf("id")]][$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("body_part_id")]]['gsm_weight']=$row[csf("gsm_weight")];*/

            			$itemDescripts=explode(',',$row[csf("product_name_details")]);
            			$itemDescription=$itemDescripts[0].','.$itemDescripts[1];

            			if ($req_dia_arr=="") $req_dia_arr=$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['dia_width']; else $req_dia_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['dia_width'];
            			if ($req_gsm_arr=="") $req_gsm_arr=$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['gsm_weight']; else $req_gsm_arr.=",".$booking_array[$row[csf("batch_id")]][$val][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("body_part_id")]][$itemDescription]['gsm_weight'];



            			//echo '#'.$row[csf("batch_id")].'='.$val.'='.$batch_arr[$row[csf("batch_id")]]['color_id'].'='.$row[csf("body_part_id")].'='.$itemDescription.'===';

            			//if ($color_type_id=="") $color_type_id=$booking_array[$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['color_type_id'];
            			//else $color_type_id.=",".$booking_array[$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']]['color_type_id'];


            			/*if ($color_type_id=="") $color_type_id=$color_type_id_arr[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("gsm")]][$row[csf("dia_width")]]['color_type_id'];
            			else $color_type_id.=",".$color_type_id=$color_type_id_arr[$row[csf("batch_id")]][$val][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$batch_arr[$row[csf("batch_id")]]['color_id']][$row[csf("gsm")]][$row[csf("dia_width")]]['color_type_id'];
	*/



            			if($po_buyer=="") $po_buyer = $buyer_library[$po_array[$val]['buyer']];else $po_buyer .=",".$buyer_library[$po_array[$val]['buyer']];
            		}

            		if(count($po_no)>0)
            		{
            			$row_buyer_name = $po_buyer;
            		}else{
            			$row_buyer_name = $buyer_library[$batch_buyer_arr[$row[csf('batch_id')]]];
            		}
            		$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_nos)));
            		$req_dia_arr=implode(",",array_unique(explode(",",$req_dia_arr)));
            		$req_gsm_arr=implode(",",array_unique(explode(",",$req_gsm_arr)));
            		//$color_type_id=implode(",",array_unique(explode(",",$color_type_id)));

            		$totalQnty +=$row[csf("issue_qnty")];
            		$totalRoll +=$row[csf("no_of_roll")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["issue_qnty"]+=$row[csf("issue_qnty")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["act_dia_width"]=$req_dia_arr;

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["dia_width"]=$product_array[$row[csf("prod_id")]]['dia_width'];



            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["gsm"]=$product_array[$row[csf("prod_id")]]['gsm'];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["store_id"]=$store_library[$row[csf("store_id")]];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["uom"]=$unit_of_measurement[$row[csf("uom")]];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["remarks"]=$row[csf("remarks")];

            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["no_of_roll"]=$row[csf("no_of_roll")];
            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["job_no"]=$job_no;
            		$main_array[$batch_arr[$row[csf("batch_id")]]['booking_no']][$row[csf("batch_id")]][$row_buyer_name][$style_ref_nos][$order_nos][$batch_arr[$row[csf("batch_id")]]['color_id']][$product_array[$row[csf("prod_id")]]['product_name_details']][$fabric_shade[$row[csf("fabric_shade")]]][$row[csf("no_of_roll")]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
            	}

            	/*echo '<pre>';
            	echo $color_type_id;
            	//print_r($color_type_id);
            	die;*/
            	$booking_tot_btchQnty_grnd=0;
            	$booking_batch_wise_span=array();
            	foreach($main_array as $booking_id=>$batch_data)
            	{
            		foreach($batch_data as $batch_id=>$buyer_data)
            		{
            			$span=0;
            			foreach($buyer_data as $buyer_id=>$style_data)
            			{
            				foreach($style_data as $style_id=>$order_data)
            				{
            					foreach($order_data as $order_id=>$color_data)
            					{
            						foreach($color_data as $color_id=>$prod_dtls_data)
            						{
            							foreach($prod_dtls_data as $prod_dtls_id=>$shade_data)
            							{
            								foreach($shade_data as $shade_id=>$roll_data)
            								{
            									foreach($roll_data as $roll_id=>$bodyPart)
            									{
            										foreach($bodyPart as $bodyPartId=>$detarminationData)
            										{
            											foreach($detarminationData as $detarminationId=>$id_data)
            											{
            												foreach($id_data as $id=>$row)
            												{
            													$span++;
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
            			$booking_batch_wise_span[$booking_id][$batch_id]=$span;
            		}

            	}

            	$p=1;
            	foreach($main_array as $booking_id=>$batch_data)
            	{
            		$booking_tot_btchQnty="";
            		$booking_tot_roll="";
            		$booking_tot_issue="";
            		foreach($batch_data as $batch_id=>$buyer_data)
            		{
            			$kk=0;
            			$batch_tot_Qnty="";
            			$batch_tot_roll="";
            			$batch_tot_issue="";
            			$batch_tot_qty = "";
            			$batch_tot_issue="";

            			foreach($buyer_data as $buyer_id=>$style_data)
            			{
            				foreach($style_data as $style_id=>$order_data)
            				{
            					foreach($order_data as $order_id=>$color_data)
            					{
            						foreach($color_data as $color_id=>$prod_dtls_data)
            						{
            							foreach($prod_dtls_data as $prod_dtls_id=>$shade_data)
            							{
            								foreach($shade_data as $shade_id=>$roll_data)
            								{
            									foreach($roll_data as $roll_id=>$bodyPart)
            									{
            										foreach($bodyPart as $bodyPartId=>$detarminationData)
            										{
            											foreach($detarminationData as $detarminationId=>$id_data)
            											{
            												foreach($id_data as $dtls_id=>$row)
            												{
            													if ($order_id!="") {
            														$itemDescripts=explode(',',$row['product_name_details']);
            														$itemDescription=trim($itemDescripts[0]).','.trim($itemDescripts[1]);
            														$req_gsm_arr=$booking_array[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription]['gsm_weight']*1;

            														$req_dia_arr=$booking_array[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription]['dia_width']*1;

            														$colorType_id=$color_type_id_arr[$batch_id][$poNumber_arr[$order_id]['po_id']][$color_id][$bodyPartId][$itemDescription][$req_gsm_arr][$req_dia_arr]['color_type_id'];
            													}
            													else{
            														$req_dia= $booking_array[$batch_id][$bodyPartId][$detarminationId][$color_id]['dia_width'];
            														$req_gsm=  $booking_array[$batch_id][$bodyPartId][$detarminationId][$color_id]['gsm_weight'];
            														$colorTypeID=$color_type_id_arr[$booking_id][$batch_id][$bodyPartId][$detarminationId][$color_id][$req_gsm][$req_dia]['color_type_id'];
            													}



            													?>
            													<tr bgcolor="<? echo $bgcolor; ?>">
            														<?
            														if($kk==0)
            														{
            															?>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>" align="center"><? echo $p; ?></td>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><?
            															$buyer_id= implode(",",array_unique(explode(",",$buyer_id)));
            															echo $buyer_id;
            															$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></td>
            															<td rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><? echo
            															'<strong>J :</strong> '.$row['job_no'].'<br><strong>B:</strong> '.$booking_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all"><? echo $style_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $order_id; ?></td>
            															<td  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $batch_arr[$batch_id]['no']; ?></td>
            															<td align="center"  rowspan="<? echo $booking_batch_wise_span[$booking_id][$batch_id]; ?>"  style="word-break:break-all;"><? echo $color_arr[$batch_arr[$batch_id]['color_id']]; ?></td>

            															<?
            															$kk++;
            															$p++;
            														}

            														?>
				 												<td align="center"   style="word-break:break-all;"><? //echo $color_type[$color_type_id];

				 												if ($order_id!="") {
				 													echo $color_type[$colorType_id];

				 													//echo $color_type[$color_type_id_arr[$batch_id][$poNumber_arr[$order_id]['po_id']][$bodyPartId][$detarminationId][$color_id][$row["gsm"]][chop($row['act_dia_width'],',')]['color_type_id']];
				 												}
				 												else
				 												{
				 													//echo $color_type[$color_type_id_arr[$buyer_id][$booking_id][$bodyPartId][$color_id]['color_type_id']];
				 													echo $color_type[$colorTypeID];
				 												}
				 												?></td>
				 												<td title="<? echo $body_part_library[$bodyPartId]; ?>" style="word-break:break-all;"><? echo $row['product_name_details']; //$prod_dtls_id; ?></td>
				 												<td  title="Required Dia,GSM wise Batch Quantity" align="right" style="word-break:break-all;"><?
				 												//echo $batch_id.'='.$row['dia_width'].'='.$row['gsm'].'='.$bodyPartId.'='.$color_id;
				 												if ($order_id!="") {
				 													echo $batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row["gsm"]][$bodyPartId][$color_id]['batch_qnty'];
				 													$batch_tot_Qnty+=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row["gsm"]][$bodyPartId][$color_id]['batch_qnty'];
				 												}
				 												else
				 												{
				 													echo $batchQntyTot= $batch_qnty_with_dia_arr[$batch_id][$req_dia][$req_gsm][$bodyPartId][$color_id]['batch_qnty'];
				 													$batch_tot_Qnty+=$batchQntyTot;
				 												}
				 												?></td>
				 												<td align="right"><? echo $roll_id; ?></td>
				 												<td align="right"><? echo number_format($row["issue_qnty"],2); ?></td>
				 												<!-- <td align="center"><? //echo $row["uom"]; ?></td> -->
				 												<td style="word-wrap: break-word; word-break: break-all;" width="100" align="center"><p>
				 													<?
				 													if ($order_id!="") {
				 														echo $req_dia_arr;
				 														//echo chop($row['act_dia_width'],",");
				 													}
				 													else
				 													{
				 														echo $req_dia;
				 													}
				 													?>
				 												</p></td>
				 												<td align="center"><? echo $row['dia_width']; //$product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
				 												<td align="center">
				 													<?
				 													if ($order_id!="") {
				 														echo $req_gsm_arr;
				 													}
				 													else
				 													{
				 														echo $req_gsm;
				 													}
				 													?>
				 												</td>
				 												<td align="center"><? echo $row["gsm"]; ?></td>
				 												<!-- <td style="word-break:break-all;"><? //echo $row["store_id"]; ?></td> -->
				 												<td align="right"><? echo $shade_id; ?></td>
				 												<td style="word-break:break-all;"><? echo $row["remarks"]; ?></td>
				 											</tr>
				 											<?
				 											$batch_tot_roll+=$roll_id;
				 											$batch_tot_issue+=$row["issue_qnty"];
				 											//$batch_tot_qty +=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row['gsm']]['batch_qnty'];
				 											//$batch_tot_qty +=$batch_qnty_with_dia_arr[$batch_id][$row['dia_width']][$row['gsm']][$bodyPartId][$color_id]['batch_qnty'];
						 									//}

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
				 	$booking_tot_btchQnty+=$batch_tot_Qnty;
				 	$booking_tot_roll+=$batch_tot_roll;
				 	$booking_tot_issue+=$batch_tot_issue;
				 	$booking_tot_btchQnty_grnd+=$batch_tot_Qnty;
				 	?>
				 	<tr>
				 		<td colspan="9" align="right"><strong>Dia Wise Batch Total :</strong></td>
				 		<td align="right" style="font-weight: bold;"><?php echo $batch_tot_Qnty; ?></td>
				 		<td align="right" style="font-weight: bold;"><?php echo $batch_tot_roll; ?></td>
				 		<!-- <td> </td> <td> </td> -->
				 		<td align="right" style="font-weight: bold;"><?php echo number_format($batch_tot_issue, 2); ?></td>
				 		<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
				 	</tr>
				 	<?

				 }
				 ?>
				 <tr>
				 	<td colspan="9" align="right"><strong>Booking Total :</strong></td>
				 	<td align="right" style="font-weight: bold;"><?php echo $booking_tot_btchQnty; ?></td>
				 	<td align="right" style="font-weight: bold;"><?php echo $booking_tot_roll; ?></td>
				 	<!-- <td> </td> <td> </td> -->
				 	<td align="right" style="font-weight: bold;"><?php echo number_format($booking_tot_issue, 2); ?></td>
				 	<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
				 </tr>
				 <?
				}



		    /*foreach($data_array as $booking_nos => $booking_nos_data)
		    {
		        foreach($booking_nos_data as $batch_ids => $batch_ids_data)
		        {
		            $y = 1;
		            foreach($batch_ids_data as $rollNos => $row)
		            {
		            	echo "<pre>";
		            	echo print_r($row);
		            	echo "</pre>";
		            	?>
						<tr bgcolor="<? echo $bgcolor; ?>">
						    <td align="center"><? echo $i; ?></td>
						    <td style="word-break:break-all"><? echo $row_buyer_name;//$buyer_library[$batch_buyer_arr[$row[csf("batch_id")]]]; ?></td>
						    <td style="word-break:break-all"><? echo $batch_arr[$row[csf("batch_id")]]['booking_no']; ?></td>
						    <td style="word-break:break-all"><? echo $style_ref_nos; ?></td>

						    <td style="word-break:break-all;"><? echo $order_nos; ?></td>
						    <td style="word-break:break-all;"><? echo $batch_arr[$row[csf("batch_id")]]['no']; ?></td>
						    <td style="word-break:break-all;"><? echo $batch_arr[$row[csf("batch_id")]]['batch_qnty']; ?></td>
						    <td style="word-break:break-all;"><? echo $color_arr[$batch_arr[$row[csf("batch_id")]]['color_id']]; ?></td>

						    <!-- <td style="word-break:break-all;"><? //echo $po_file; ?></td>
						    <td style="word-break:break-all;"><? //echo $po_ref; ?></td> -->
						    <td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
						    <td align="right"><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></td>
						    <td align="right"><? echo $row[csf("no_of_roll")]; ?></td>

						    <!-- <td align="right"><?  //echo $rack_name; ?></td>
						    <td align="right"><? //echo $shelf_name; ?></td> -->
						    <td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
						    <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
						    <td align="center"><? echo $req_dia_arr;//$booking_array[$row[csf("order_id")]]['dia_width']; ?></td>
						    <td align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
						    <td align="center"><? echo $req_gsm_arr;//$booking_array[$row[csf("order_id")]]['gsm_weight']; ?></td>
						    <td align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
						    <td style="word-break:break-all;"><? echo $store_library[$row[csf("store_id")]]; ?></td>
						    <td style="word-break:break-all;"><? echo $row[csf("remarks")]; ?></td>
						</tr>
				<? //$i++;
					}
				}
			}*/
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9" align="right"><strong>Grand Total :</strong></td>
				<td align="right" style="font-weight: bold;"><?php echo $booking_tot_btchQnty_grnd; ?></td>
				<td align="right" style="font-weight: bold;"><?php echo $totalRoll; ?></td>
				<!-- <td> </td> <td> </td> -->
				<td align="right" style="font-weight: bold;"><?php echo number_format($totalQnty, 2); ?></td>
				<td align="right" colspan="6"><?php // echo $totalAmount; ?></td>
			</tr>
		</tfoot>
	</table>
	<br>
	<?
	echo signature_table(21, $data[0], "1250px");
	?>
</div>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

?>
