<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

if($db_type==0) $select_field="group";
else if($db_type==2) $select_field="wm";


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/subcon_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' ); load_drop_down( 'requires/subcon_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('cbo_dyeing_floor').value, 'load_drop_down_machine', 'machine_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data = explode('_', $data);
	$loca = $data[1];
	$com = $data[0];
	if($loca>0) $loc_cond="and location_id='$loca' ";else $loc_cond="";

	echo create_drop_down( "cbo_dyeing_floor", 172, "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$com' and production_process=3 $loc_cond  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/subcon_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" );
	exit();
}


if ($action=="load_drop_down_machine")
{
	$data= explode("_", $data);

	if($data[1]>0) $loc_cond="and location_id=$data[1] ";else $loc_cond="";
	if($data[1]==0)
	{
		echo create_drop_down( "txt_machine_no", 172, $blank_array,"", 1, "-- Select Machine --", $selected, "" );
	}
	else
	{
		if($db_type==2)
		{

			echo create_drop_down( "txt_machine_no", 172, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id in(2,4,6) and company_id='$data[0]'  and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 $loc_cond order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
		}
		else if($db_type==0)
		{
			echo create_drop_down( "txt_machine_no", 172, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id in(2,4,6) and company_id='$data[0]'  and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 $loc_cond order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
		}
	}
	exit();
}

if ($action=="load_drop_down_po_id")
{
	$data= explode("_", $data);

	//echo create_drop_down( "poId_1", 90, "select b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id='$data[0]' and b.main_process_id='$data[2]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job like '%$data[1]%' group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 1, "-- Select PO --", $selected, "hidden_data_load(1);" );
	echo create_drop_down( "poId_1", 90,"select b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id='$data[0]' and b.main_process_id='$data[2]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job like '%$data[1]%' group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 1, "-- Select PO --", $selected, "hidden_data_load(1);","","","", "", "", "", "","poId[]" );
}

if ($action == "collar_and_cuff_popup") 
{
	echo load_html_head_contents("Plies Info Roll Wise", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
		<script>
			function add_break_down_tr(i) 
			{ 
				var row_num = $('#txt_tot_row').val();
				row_num++;

				var clone = $("#tr_" + i).clone();
				clone.attr({
					id: "tr_" + row_num,
				});

				clone.find("input,select").each(function() {

					$(this).attr({
						'id': function(_, id) {
							var id = id.split("_");
							return id[0] + "_" + row_num
						},
						'name': function(_, name) {
							return name
						},
						'value': function(_, value) {
							return ''
						}
					});

				}).end();

				$("#tr_" + i).after(clone);


				$('#increase_' + row_num).removeAttr("value").attr("value", "+");
				$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
				$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
				$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");
				set_all_onclick();
				$('#txt_tot_row').val(row_num);
				
			}
			function fn_deleteRow(rowNo) 
			{
				var numRow = $('#tbl_list_search tbody tr').length;
				if (numRow != 1) {
					$("#tr_" + rowNo).remove();
				}
			} 
			function fnc_close() 
			{
				var save_string = ''; 
				var i = 1;
				$("#tbl_list_search tbody").find('tr').each(function() 
				{
					var bodyPart = $(this).find('input[name="bodyPart[]"]').val();
					var greySize = $(this).find('input[name="greySize[]"]').val();
					var finishSize = $(this).find('input[name="finishSize[]"]').val();
					var gmtsSize = $(this).find('input[name="gmtsSize[]"]').val();
					var qtyPices = $(this).find('input[name="qtyPices[]"]').val();
					var needlePerCm = $(this).find('input[name="needlePerCm[]"]').val(); 

					if (qtyPices * 1 > 0) {
						if (save_string == "") {
							save_string = bodyPart + "=" + greySize + "=" + finishSize + "=" + gmtsSize + "=" + qtyPices + "=" + needlePerCm;
						} else {
							save_string += "$$" + bodyPart + "=" + greySize + "=" + finishSize + "=" + gmtsSize + "=" + qtyPices + "=" + needlePerCm;
						}
					} 
					i++;
				});

				$('#hide_data').val(save_string); 
				parent.emailwindow.hide();
		}
		</script>
		</head> 
		<body>
			<div align="center" style="width:100%; overflow-y:hidden;">
				<fieldset style="width:590px"> 
					<table width="690" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
						<thead>
							<th>Body Part</th>
							<th>Grey Size</th>
							<th>Finish Size</th> 
							<th>Gmts Size</th>
							<th>Qty. Pcs</th>
							<th>Needle Per CM</th>
							<th> </th>
						</thead>
						<tbody>
							<? 
								if ($collarAndCuffStr != "") 
								{
									$collarAndCuffArr = explode('$$',$collarAndCuffStr); 
									foreach ($collarAndCuffArr as  $dataStr) 
									{ 
										$collarAndCuffDataArr = explode('=',$dataStr);
										$body_part 		= $collarAndCuffDataArr[0];
										$grey_size 		= $collarAndCuffDataArr[1];
										$finish_size	= $collarAndCuffDataArr[2];
										$gmts_size 		= $collarAndCuffDataArr[3];
										$qnty_pics 		= $collarAndCuffDataArr[4];
										$needle_per_cm 	= $collarAndCuffDataArr[5];
										$i = 1;
										// echo $collarAndCuffDataArr; 
										?>
											<tr id="tr_<?= $i ?>" class="general">
												<td>
													<input type="text" value="<?= $body_part ?>" id="bodyPart_<?= $i ?>" name="bodyPart[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $grey_size ?>" id="greySize_<?= $i ?>" name="greySize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $finish_size ?>" id="finishSize_<?= $i ?>" name="finishSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $gmts_size ?>" id="gmtsSize_<?= $i ?>" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $qnty_pics ?>" id="qtyPices_<?= $i ?>" name="qtyPices[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td>
													<input type="text" value="<?= $needle_per_cm ?>" id="needlePerCm_<?= $i ?>" name="needlePerCm[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td width="70"> 
													<input type="button" id="increase_<?= $i ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>)" /> 
													<input type="button" id="decrease_<?= $i ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<?= $i ?>);" />
												</td>
											</tr>
										<?
									}
								}
								else
								{
									$collarCuff_sql = "select collar_and_cuff_str as collar_cuff_data from subcon_ord_dtls where job_no_mst='$hiddin_job_no' and status_active=1  and is_deleted=0";
        							$collarCuff_data=sql_select($collarCuff_sql);
									foreach($collarCuff_data as $row)
									{
										$collarAndCuffString=$row[csf("collar_cuff_data")];
									}
									$collarAndCuffArray = explode('$$',$collarAndCuffString); 
									foreach ($collarAndCuffArray as  $dataStr) 
									{ 
										$collarAndCuffDataArray = explode('=',$dataStr);
										$body_part 		= $collarAndCuffDataArray[0];
										$grey_size 		= $collarAndCuffDataArray[1];
										$finish_size	= $collarAndCuffDataArray[2];
										$gmts_size 		= $collarAndCuffDataArray[3];
										$qnty_pics 		= $collarAndCuffDataArray[4];
										$needle_per_cm 	= $collarAndCuffDataArray[5];
										$i = 1;
										// echo $collarAndCuffDataArr; 
										?>
											<tr id="tr_<?= $i ?>" class="general">
												<td>
													<input type="text" value="<?= $body_part ?>" id="bodyPart_<?= $i ?>" name="bodyPart[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $grey_size ?>" id="greySize_<?= $i ?>" name="greySize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $finish_size ?>" id="finishSize_<?= $i ?>" name="finishSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $gmts_size ?>" id="gmtsSize_<?= $i ?>" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $qnty_pics ?>" id="qtyPices_<?= $i ?>" name="qtyPices[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td>
													<input type="text" value="<?= $needle_per_cm ?>" id="needlePerCm_<?= $i ?>" name="needlePerCm[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td width="70"> 
													<input type="button" id="increase_<?= $i ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>)" /> 
													<input type="button" id="decrease_<?= $i ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<?= $i ?>);" />
												</td>
											</tr>
										<?
									}
								}
							?> 
						</tbody>
					</table>
					<div align="center" style="margin-top:10px">
						<input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px" />
						<input type="hidden" id="hide_data" /> 
						<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
					</div>
				</fieldset>
			</div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

		</html>
	<?
}


if($action=="po_wise_data_load")
{
	$party_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$data= explode("_", $data);
	$row_no=$data[0];
	$company_id=$data[1];
	$job_no=$data[2];
	$po_id=$data[3];

	$order_con=" and b.id ='$po_id'";
	$job_con=" and a.subcon_job like '%$job_no%'";

	if($db_type==0)
	{
		$sql="select a.subcon_job as job_no, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.party_id, b.id as po_id, b.main_process_id, b.process_id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date, group_concat(distinct(c.item_id)) as item_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_con $job_con group by b.id order by b.id DESC";// die;
	}
	else if($db_type==2)
	{
		$sql="select LISTAGG(CAST(a.subcon_job AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.subcon_job) as job_no, a.party_id, LISTAGG(CAST(a.job_no_prefix_num AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.job_no_prefix_num) as job_no_prefix_num, TO_CHAR(max(a.insert_date),'YYYY') as year, b.id as po_id, b.main_process_id, b.process_id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date,
		LISTAGG(CAST(c.item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.item_id) as item_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_con $job_con group by a.party_id, b.id, b.main_process_id, b.process_id, b.cust_style_ref, b.order_uom, b.order_no, b.order_quantity, b.delivery_date order by b.id DESC";// die;
	}

	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{

		echo "document.getElementById('txtPoNo_".$row_no."').value 		= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('processId_".$row_no."').value 	= '".$row[csf("main_process_id")]."';\n";
		echo "document.getElementById('hide_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hide_party_id').value 			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txtJobParty_".$row_no."').value  = '".$party_arr[$row[csf("party_id")]]."';\n";
	}

exit();
}



if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	$sql_result = sql_select("select batch_no_creation from variable_settings_production where company_name=$data and variable_list=24 and status_active=1 and is_deleted=0");
 	foreach($sql_result as $result)
	{
		echo "$('#variable_check').val(".$result[csf("batch_no_creation")].");\n";
	}
 	exit();
}
if ($action=="load_fabric_source_from_variable_settings")
{
	$sql_result = sql_select("select dyeing_fin_bill from variable_settings_subcon where company_id = $data and variable_list = 4 and is_deleted = 0 and status_active = 1");

	$fabricfrom=array(1=>"Receive",2=>"Production",3=>"Issue");
	if($sql_result)
	{
		$data_ids=explode(",", $sql_result[0][csf('dyeing_fin_bill')]);
		$values=$sql_result[0][csf('dyeing_fin_bill')];

		$selected = (count($data_ids)==1)? $data_ids[0] : "0";
	}
	else
	{
		$values=1;
		$selected =1;
	}

	//echo create_drop_down("cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0,$values,"","","","","","","fabric_source");
	echo create_drop_down("cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0, $values, "", "", "", "", "", "cbofabricfrom[]");

	/*if($sql_result)
	{
	    foreach($sql_result as $result)
		{
	        echo "$('.fabric_source').val(".$result[csf("dyeing_fin_bill")].");\n";
		}
    }
    else
    {
            echo "$('.fabric_source').val(1);\n";
    }*/
 	exit();
}

if($action=="itemdes_popup")
{
  	echo load_html_head_contents("Item Description Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value( prod_id,challan,description,gsm,grey_dia,fin_dia,balance)
		{
			document.getElementById('prod_id').value=prod_id;
			document.getElementById('challan').value=challan;
			document.getElementById('description').value=description;
			document.getElementById('gsm').value=gsm;
			document.getElementById('grey_dia').value=grey_dia;
			document.getElementById('fin_dia').value=fin_dia;
            document.getElementById('balance').value=balance;
			parent.emailwindow.hide();
		}
    </script>
</head>
<?
	$batch_array=array();
	$batch_sql="select po_id, prod_id, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where po_id='$po_id' and status_active=1 and is_deleted=0 group by po_id, prod_id";
	$result_batch_sql=sql_select( $batch_sql );
	foreach($result_batch_sql as $row)
	{
		$batch_array[$row[csf('po_id')]][$row[csf('prod_id')]]=$row[csf('batch_qnty')];
	}

	$material_issue_arr=array();
	$material_return_arr=array();

	if ($db_type==0)
	{
		$sql_issue="select b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
		$sql_return="select b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
	}
	elseif($db_type==2)
	{
		$sql_issue="select b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
		$sql_return="select b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
	}

	$nameArray_issue=sql_select($sql_issue);
	foreach ($nameArray_issue as $row)
	{
		$material_issue_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
	}

	$nameArray_return=sql_select($sql_return);
	foreach($nameArray_return as $row)
	{
		$material_return_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
	}
	//var_dump($material_return_arr);

	if($fabricfrom==1)
	{
		if ($db_type==0)
		{
			$sql="select a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, sum(b.quantity) as quantity, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by a.chalan_no";
		}
		elseif($db_type==2)
		{
			$sql="select  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, sum(b.quantity) as quantity, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by a.chalan_no";
		}
	}
	else if($fabricfrom==2)
	{
		if ($db_type==0)
		{
			$sql="select '' as chalan_no, fabric_description as material_description, id, gsm, dia_width as fin_dia, color_id, sum(product_qnty) as quantity, concat(fabric_description,',', gsm,',', dia_width) as description from subcon_production_dtls where order_id='$po_id' and product_type=2 and status_active=1 group by id, fabric_description, gsm, dia_width, color_id";
		}
		else if ($db_type==2)
		{
			$sql="select '' as chalan_no, fabric_description as material_description, id, gsm, dia_width as fin_dia, color_id, sum(product_qnty) as quantity, fabric_description || ',' || gsm || ',' || dia_width as description from subcon_production_dtls where order_id='$po_id' and product_type=2 and status_active=1 group by id, fabric_description, gsm, dia_width, color_id";
		}
    }
    else
    {

        if($db_type ==0)
            {
                $sql="select  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, b.order_id,
                sum(b.quantity) as quantity, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description
                from sub_material_mst a, sub_material_dtls b
                where a.id=b.mst_id and a.trans_type=2
                and a.company_id = $cbo_company_id and b.order_id in ($po_id)
                and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id ,b.order_id
                order by a.chalan_no";
            }
        else if ($db_type == 2)
            {
                $sql="select  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, b.order_id,
                sum(b.quantity) as quantity, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description
                from sub_material_mst a, sub_material_dtls b
                where a.id=b.mst_id and a.trans_type=2
                and a.company_id = $cbo_company_id and b.order_id in ($po_id)
                and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id ,b.order_id
                order by a.chalan_no";
            }
   	}
//        echo $batch_sql;
//        echo "<pre>";
//        print_r($batch_array);
//	   	  echo $sql;

	?>
        <input type="hidden" name="prod_id" id="prod_id" value="">
        <input type="hidden" name="challan" id="challan" value="">
        <input type="hidden" name="description" id="description" value="">
        <input type="hidden" name="gsm" id="gsm" value="">
        <input type="hidden" name="grey_dia" id="grey_dia" value="">
        <input type="hidden" name="fin_dia" id="fin_dia" value="">
        <input type="hidden" name="balance" id="balance" value="">

    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="60">Challan</th>
                <th width="130">Fab. Desc.</th>
                <th width="40">GSM</th>
                <th width="50">G.Dia</th>
                <th width="50">F.Dia</th>
                <th width="80">Color</th>
                <? if($fabricfrom == 3){?>
                <th width="80">Issue. Qty</th>
                <? }else{
                    ?>
                    <th width="80">Rec. Qty</th>
                        <?
                }?>
                <th>Balance</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$return_qty=$material_return_arr[$selectResult[csf('chalan_no')]][$selectResult[csf('description')]][$selectResult[csf('color_id')]];

                    $balance=$selectResult[csf('quantity')]-$return_qty-$batch_array[$po_id][$selectResult[csf('id')]];
                    if($fabricfrom == 3)
                    {
                        $balance=  $selectResult[csf('quantity')] - $batch_array[$po_id][$selectResult[csf('id')]];
                    }

                    //echo $selectResult[csf('quantity')]."**".$return_qty."**".$batch_array[$po_id][$selectResult[csf('id')]];

					if($balance>0)
					{
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('chalan_no')]; ?>','<? echo $selectResult[csf('material_description')]; ?>','<? echo $selectResult[csf('gsm')]; ?>','<? echo $selectResult[csf('grey_dia')]; ?>','<? echo $selectResult[csf('fin_dia')]; ?>','<? echo number_format($balance,2);?>')">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="60" align="center"><p><? echo $selectResult[csf('chalan_no')]; ?></p></td>
							<td width="130" align="center"><? echo $selectResult[csf('material_description')]; ?></td>
							<td width="40"  align="center"><p><? echo $selectResult[csf('gsm')]; ?></p></td>
							<td width="50"  align="center"><p><? echo $selectResult[csf('grey_dia')]; ?></p></td>
							<td width="50"  align="center"><p><? echo $selectResult[csf('fin_dia')]; ?></p></td>
							<td width="80"  align="center"><p><? echo  $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($selectResult[csf('quantity')],2); ?></p></td>
							<td align="right"><?  echo number_format($balance,2); ?></td>
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	/*	$po_batch_no_arr=array();
		$po_batch_data=sql_select("select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from  pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id group by b.color_id, a.po_id");
		foreach($po_batch_data as $row)
		{
			$po_batch_no_arr[$row[csf('color_id')]][$row[csf('po_id')]]=$row[csf('po_batch_no')];
		}
	*/	

	if (str_replace("'", "", $txt_ext_no) != "") {
		$extention_no_cond = "and extention_no=$txt_ext_no";
		$extention_no_cond2 = "and batch_ext_no=$txt_ext_no";
	} else {
		$extention_no_cond = "";
		$extention_no_cond2 = "";
	}

	$collarAndCuffStr = str_replace("'",'',$collarAndCuffStr);
	if($operation==0)// Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$poArr=array();
		for($i=1;$i<=$total_row;$i++)
		{
				$poid="poId_".$i;
				$poArr[$$poid]=$$poid;
		}

		if(!empty($poArr))
		{
			$sql_prod_knit=sql_select("select b.order_id as po_id, b.id as dtls_id,b.product_qnty as qnty from  subcon_production_mst a, subcon_production_dtls b where b.mst_id=a.id and a.entry_form=159  and b.order_id in ('".implode(",",$poArr)."')");
			foreach($sql_prod_knit as $row)
			{
				$knit_prod_no_arr[$row[csf('po_id')]][$row[csf('dtls_id')]]+=$row[csf('qnty')];
			}

			$issue_prod_no_arr=array();
			$po_prod_data_aa=sql_select("select a.trans_type,b.order_id,b.id as dtls_id, b.quantity from  sub_material_mst a, sub_material_dtls b where b.mst_id=a.id and a.trans_type in(1,2)  and b.order_id in (".implode(",",$poArr).") and b.status_active=1 and a.status_active=1");

			foreach($po_prod_data_aa as $row)
			{
				if($row[csf('trans_type')]==2)
				{
					$issue_prod_no_arr[$row[csf('order_id')]][$row[csf('dtls_id')]]+=$row[csf('quantity')];
					//echo "10**".$row[csf('quantity')].',';
				}
				else if($row[csf('trans_type')]==1)
				{
					$recv_prod_no_arr[$row[csf('order_id')]][$row[csf('dtls_id')]]+=$row[csf('quantity')];
				}
			}
			//print_r($issue_prod_no_arr);
			//echo "10**";die;
			$batch_precv_data=sql_select("select b.po_id,b.prod_id as dtls_id, b.batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where b.mst_id=a.id and a.entry_form=36  and b.po_id in (".implode(",",$poArr).") and a.status_active=1 and b.status_active=1");

			foreach($batch_precv_data as $row)
			{
				$prev_batch_qnty_arr[$row[csf('po_id')]][$row[csf('dtls_id')]]+=$row[csf('batch_qnty')];
			}

		}

		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation);

		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");

		if(str_replace("'","",$txt_batch_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","36");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_batch_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$flag=1;
		if(str_replace("'","",$update_id)=="")
		{
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;

		 	if($batch_no_creation==1)
			{
				//$txt_batch_number="'".$serial_no."'";
				$txt_batch_number="'".$id."'";
			}
			else
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and entry_form=36 and status_active=1 $extention_no_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0";
					disconnect($con);
					die;
				}

				$txt_batch_number=$txt_batch_number;
			}

			$field_array="id, batch_against, batch_no, floor_id, machine_no, location_id, batch_date, entry_form, company_id, extention_no, color_id, batch_weight, total_trims_weight, color_range_id, process_id, dur_req_hr, dur_req_min, exp_load_hr, exp_load_min, batch_delivery_date, remarks,double_dyeing,collar_qty,cuff_qty,collar_and_cuff_str, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_batch_against.",".$txt_batch_number.",".$cbo_dyeing_floor.",".$txt_machine_no.",".$cbo_location_name.",".$txt_batch_date.",36,".$cbo_company_id.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_exp_load_hr.",".$txt_exp_load_min.",".$txt_batch_delivery_date.",".$txt_remarks.",".$cbo_double_dyeing.",".$txt_collar_qty.",".$txt_cuff_qty.",'".$collarAndCuffStr."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);

			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and color_id=$color_id  and id!=$update_id and entry_form=36 $extention_no_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0";
					disconnect($con);
					die;
				}
			}

			$field_array_update="batch_against*batch_no*floor_id*machine_no*location_id*batch_date*company_id*extention_no*color_id*batch_weight*total_trims_weight*color_range_id*process_id*dur_req_hr*dur_req_min*exp_load_hr*exp_load_min*batch_delivery_date*remarks*double_dyeing*collar_qty*cuff_qty*collar_and_cuff_str*updated_by*update_date";

			$data_array_update=$cbo_batch_against."*".$txt_batch_number."*".$cbo_dyeing_floor."*".$txt_machine_no."*".$cbo_location_name."*".$txt_batch_date."*".$cbo_company_id."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_exp_load_hr."*".$txt_exp_load_min."*".$txt_batch_delivery_date."*".$txt_remarks."*".$cbo_double_dyeing."*".$txt_collar_qty."*".$txt_cuff_qty."*'".$collarAndCuffStr."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}

		//$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );

		$field_array_dtls="id, mst_id, fabric_from, po_id, item_description, prod_id,gsm,grey_dia, fin_dia, width_dia_type, roll_no, batch_qnty, rec_challan, inserted_by, insert_date";
		//$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";
		//$roll_table_id='';

		for($i=1;$i<=$total_row;$i++)
		{
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$po_id="poId_".$i;
			$cbofabricfrom="cbofabricfrom_".$i;
			$prod_id="txtItemDescid_".$i;
			$prod_desc="txtItemDesc_".$i;
			$gsm="txtGsm_".$i;
			$dia="txtDia_".$i;
			$findia="txtFinDia_".$i;
			$txtRollNo="txtRollNo_".$i;
			$hideRollNo="hideRollNo_".$i;
			$txtBatchQnty="txtBatchQnty_".$i;
			$cboDiaWidthType="cboDiaWidthType_".$i;
			$txtrecChallan="txtrecChallan_".$i;
			$poid=str_replace("'","",$$po_id);
			$prodid=str_replace("'","",$$prod_id);
			//$itemDesc=str_replace("'","",$$prod_desc).', '.str_replace("'","",$$gsm).', '.str_replace("'","",$$dia);
			//echo $ItemDesc;die;
			//$po_batch_no=$po_batch_no_arr[$color_id][str_replace("'","",$$po_id)]+1;


			$tot_BatchQnty=str_replace("'","",$$txtBatchQnty)+$prev_batch_qnty_arr[$poid][$prodid];
			$fabricfrom_id=str_replace("'","",$$cbofabricfrom);
			$po_id_from=str_replace("'","",$$po_id);

			if($fabricfrom_id==1) //Recv
			{

				$recv_qty=$recv_prod_no_arr[$po_id_from][$prodid];
				if($recv_qty>0)
				{
					if($tot_BatchQnty>$recv_qty)
					{
						echo "recv**".$recv_qty."**".$prev_batch_qnty_arr[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
			else if($fabricfrom_id==3)
			{
				$issue_qty=$issue_prod_no_arr[$po_id_from][$prodid];
				//echo "10**".$issue_qty.'=='.$prev_batch_qnty_arr[$po_id_from][$prodid];die;
				if($issue_qty>0)
				{
					if($tot_BatchQnty>$issue_qty)
					{
						echo "issue**".$issue_qty."**".$prev_batch_qnty_arr[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
			else if($fabricfrom_id==2) //Production
			{
				$prod_qty=$knit_prod_no_arr[$po_id_from][$prodid];
				if($prod_qty>0)
				{
					if($tot_BatchQnty>$prod_qty)
					{
						echo "prod**".$prod_qty."**".$prev_batch_qnty[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
			//echo "10**";die;
			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$cbofabricfrom.",".$$po_id.",'".$$prod_desc."','".$$prod_id."','".$$gsm."','".$$dia."','".$$findia."','".$$cboDiaWidthType."','".$$txtRollNo."',".$$txtBatchQnty.",'".$$txtrecChallan."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//$id_dtls=$id_dtls+1;
		}

		$flag=1;

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}

		// echo "10**insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
		$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2==1) $flag=1; else $flag=0;
		}
		
		//=========================================================================================
		//										Collar AND CUFF 	
		//=========================================================================================	
		//$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
			$collarAndCuffArr = explode('$$',$collarAndCuffStr);
			$kk= 0 ;
			$data_array4 = ''; 
			foreach ($collarAndCuffArr as $row) 
			{
				$id4 = return_next_id_by_sequence(  "subcon_ord_collar_and_cuff_dtls_seq", "subcon_ord_collar_and_cuff_dtls", $con );

				$collarAndCuffDataArr = explode('=',$row);
				$body_part 		= $collarAndCuffDataArr[0];
				$grey_size 		= $collarAndCuffDataArr[1];
				$finish_size	= $collarAndCuffDataArr[2];
				$gmts_size 		= $collarAndCuffDataArr[3];
				$qnty_pics 		= $collarAndCuffDataArr[4];
				$needle_per_cm 	= $collarAndCuffDataArr[5];
				if ($kk==0) 
				{
					$data_array4 .="(".$id4.",".$id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id4.",".$id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}

			 // echo "10** INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4;die;
				$rID4=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
				if($flag==1)
				{
					if($rID4==1) $flag=1; else $flag=0;
				}
		}	
		//echo "10**=".$rID.'='.$rID2.'='.$rID4.'='.$flag;die;

		/*if($data_array_roll!="")
		{
			$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}
		//echo $flag;die;
		if($roll_table_id!="")
		{
			$rID4=sql_multirow_update("pro_roll_details","roll_used",1,"id",$roll_table_id,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}*/

		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if (str_replace("'","",$update_id)!="")
		{
			$fabric_finish_data = sql_select("SELECT a.product_no, b.batch_id from SUBCON_PRODUCTION_MST a, SUBCON_PRODUCTION_DTLS b where a.id=b.mst_id and b.BATCH_ID=$update_id and a.status_active=1 and b.status_active=1");
			$fabric_finish_batch_id=0;
			foreach ($fabric_finish_data as $row)
			{
				$fabric_finish_batch_id= $row[csf('product_no')];
			}

			if($fabric_finish_batch_id)
			{
				$sub_msg="Fabric Finishing Entry Found. Production ID= ".$fabric_finish_batch_id;
				echo "16**$sub_msg";
				disconnect($con);
				die;
			}
		}
		// echo "10**string";die;

		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_no=$txt_batch_number and batch_id=$update_id and entry_form=38 and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0 $extention_no_cond2") == 1)
		{
			disconnect($con);
			echo "14**0";
			die;
		}
		$poArr=array();
		for($i=1;$i<=$total_row;$i++)
		{
				$poid="poId_".$i;
				$poArr[$$poid]=$$poid;
		}

		if(!empty($poArr))
		{

			$sql_prod_knit=sql_select("select b.order_id as po_id, b.product_qnty as qnty,b.id as dtls_id from  subcon_production_mst a, subcon_production_dtls b where b.mst_id=a.id and a.entry_form=159  and b.order_id in (".implode(",",$poArr).")");
			foreach($sql_prod_knit as $row)
			{
				$knit_prod_no_arr[$row[csf('po_id')]][$row[csf('dtls_id')]]+=$row[csf('qnty')];
			}
			$issue_prod_no_arr=array();
			$po_prod_data=sql_select("select a.trans_type,b.order_id,b.id as dtls_id, b.quantity from  sub_material_mst a, sub_material_dtls b where b.mst_id=a.id and a.trans_type in(1,2)  and b.order_id in (".implode(",",$poArr).") and a.status_active=1 and b.status_active=1");
			foreach($po_prod_data as $row)
			{
				if($row[csf('trans_type')]==2)
				{
					$issue_prod_no_arr[$row[csf('order_id')]][$row[csf('dtls_id')]]+=$row[csf('quantity')];
				}
				else if($row[csf('trans_type')]==1)
				{
					$recv_prod_no_arr[$row[csf('order_id')]][$row[csf('dtls_id')]]+=$row[csf('quantity')];
				}
			}

			$batch_precv_data=sql_select("select b.po_id,b.prod_id, b.batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where b.mst_id=a.id and a.entry_form=36  and b.po_id in (".implode(",",$poArr).") and a.id<>$update_id and a.status_active=1 and b.status_active=1");

			foreach($batch_precv_data as $row)
			{
				$prev_batch_qnty_arr[$row[csf('po_id')]][$row[csf('prod_id')]]+=$row[csf('batch_qnty')];
			}

		}


		$prev_batch_data_arr=array();
		$prev_batch_data=sql_select("select a.id as dtls_id, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and b.id=$update_id");
		foreach($prev_batch_data as $row)
		{
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id']=$row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color']=$row[csf('color_id')];
		}

		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_batch_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","36");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_batch_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$flag=1; $batch_no_creation=str_replace("'","",$batch_no_creation);

		if(str_replace("'","",$cbo_batch_against)!=2 && str_replace("'","",$hide_update_id)=="" )
		{

			if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=$update_id and entry_form=38 and load_unload_id in(2) and result=1 and status_active=1 and is_deleted=0") == 1) 			{
				disconnect($con);
				echo "14**0";
				die;
			}

		}
		if(str_replace("'","",$cbo_batch_against)==2 && str_replace("'", "", $unloaded_batch) != "" && str_replace("'", "", $ext_from) == 0)
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and entry_form=36 and status_active=1 and is_deleted=0 $extention_no_cond" )==1)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "11**0";
				disconnect($con);
				die;
			}

			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
			$field_array="id, batch_against, batch_no, floor_id, machine_no,location_id, batch_date, entry_form, company_id, extention_no, re_dyeing_from, color_id, batch_weight, total_trims_weight, color_range_id, process_id, dur_req_hr, dur_req_min,exp_load_hr, exp_load_min, batch_delivery_date,remarks,double_dyeing,collar_qty,cuff_qty, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_batch_against.",".$txt_batch_number.",".$cbo_dyeing_floor.",".$txt_machine_no.",".$cbo_location_name.",".$txt_batch_date.",36,".$cbo_company_id.",".$txt_ext_no.",".$update_id.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_exp_load_hr.",".$txt_exp_load_min.",".$txt_batch_delivery_date.",".$txt_remarks.",".$cbo_double_dyeing.",".$txt_collar_qty.",".$txt_cuff_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			/*	$field_array="id, batch_against, batch_no, batch_date, company_id, extention_no, color_id, batch_weight, total_trims_weight, color_range_id, process_id, re_dyeing_from, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_batch_against.",".$txt_batch_number.",".$txt_batch_date.",".$cbo_company_id.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$cbo_color_range.",".$txt_process_id.",".$update_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			*/
			//echo "insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
			//$rID=$rID2=$rID3=$rID4=true;


			//$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;


			$field_array_dtls="id, mst_id, fabric_from, po_id, item_description, prod_id, gsm, grey_dia ,fin_dia, width_dia_type, roll_no, batch_qnty, rec_challan, inserted_by, insert_date";


			for($i=1;$i<=$total_row;$i++)
			{
				$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$po_id="poId_".$i;
				$cbofabricfrom="cbofabricfrom_".$i;
				$prod_id="txtItemDescid_".$i;
				$prod_desc="txtItemDesc_".$i;
				$gsm="txtGsm_".$i;
				$dia="txtDia_".$i;
				$findia="txtFinDia_".$i;
				$txtRollNo="txtRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$txtrecChallan="txtrecChallan_".$i;
				//$itemDesc=str_replace("'","",$$prod_desc).', '.str_replace("'","",$$gsm).', '.str_replace("'","",$$dia);
				$po_id_from=str_replace("'","",$$po_id);
				$prodid=str_replace("'","",$$prod_id);
				if($data_array_dtls!="") $data_array_dtls.=",";

				$RollNo=str_replace("'","",$$txtRollNo);
				if($RollNo=='') $RollNo=0;

				$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$cbofabricfrom.",".$$po_id.",'".$$prod_desc."',".$$prod_id.",'".$$gsm."','".$$dia."','".$$findia."','".$$cboDiaWidthType."','".$RollNo."',".$$txtBatchQnty.",'".$$txtrecChallan."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$tot_BatchQnty=str_replace("'","",$$txtBatchQnty)+$prev_batch_qnty_arr[$po_id_from][$prodid];
			$fabricfrom_id=str_replace("'","",$$cbofabricfrom);

		if(str_replace("'","",$cbo_batch_against)!=2)
		{
			if($fabricfrom_id==1) //Recv
			{
				$recv_qty=$recv_prod_no_arr[$po_id_from][$prodid];
				if($recv_qty>0)
				{
					if($tot_BatchQnty>$recv_qty)
					{
						echo "recv**".$recv_qty."**".$prev_batch_qnty_arr[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
			else if($fabricfrom_id==3)
			{
				$issue_qty=$issue_prod_no_arr[$po_id_from][$prodid];
				if($issue_qty>0)
				{
					if($tot_BatchQnty>$issue_qty)
					{
						echo "issue**".$issue_qty."**".$prev_batch_qnty_arr[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
			else if($fabricfrom_id==2) //Production
			{
				$prod_qty=$knit_prod_no_arr[$po_id_from][$prodid];
				if($prod_qty>0)
				{
					if($tot_BatchQnty>$prod_qty)
					{
						echo "prod**".$prod_qty."**".$prev_batch_qnty_arr[$po_id_from][$prodid]."**".$i;
						disconnect($con);
						die;
					}
				}
			}
		}


				//$id_dtls=$id_dtls+1;
			}

			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID==1  && $flag==1) $flag=1; else $flag=0;
			//echo "10**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,0);//die;

			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			 //echo "10**".$rID.'='.$rID2.'='.$flag; die;
		}
		else
		{
			//$poBatchNoArr=array();
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);

			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and id!=$update_id and entry_form=36 and status_active=1 and is_deleted=0  $extention_no_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0**100";
					disconnect($con);
					die;
				}
				//echo "11**select batch_no from pro_batch_create_mst where company_id=$cbo_company_id and batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and entry_form=36 and status_active=1 and is_deleted=0";die;
			}

			$field_array_update="batch_against*batch_no*floor_id*machine_no*location_id*batch_date*extention_no*color_id*batch_weight*total_trims_weight*color_range_id*process_id*dur_req_hr*dur_req_min*exp_load_hr*exp_load_min*batch_delivery_date*remarks*double_dyeing*collar_qty*cuff_qty*collar_and_cuff_str*updated_by*update_date";

			$data_array_update=$cbo_batch_against."*".$txt_batch_number."*".$cbo_dyeing_floor."*".$txt_machine_no."*".$cbo_location_name."*".$txt_batch_date."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_exp_load_hr."*".$txt_exp_load_min."*".$txt_batch_delivery_date."*".$txt_remarks."*".$cbo_double_dyeing."*".$txt_collar_qty."*".$txt_cuff_qty."*'".$collarAndCuffStr."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";



			//$id_dtls_batch=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;

			$field_array_dtls="id, mst_id, fabric_from, po_id, item_description, prod_id,gsm,grey_dia, fin_dia, width_dia_type, roll_no, batch_qnty, rec_challan, inserted_by, insert_date";
			$field_array_dtls_update="fabric_from*po_id*item_description*prod_id*gsm*grey_dia*fin_dia*width_dia_type*roll_no*batch_qnty*rec_challan*updated_by*update_date";

			for($i=1;$i<=$total_row;$i++)
			{
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$po_id="poId_".$i;
				$cbofabricfrom="cbofabricfrom_".$i;
				$prod_id="txtItemDescid_".$i;
				$prod_desc="txtItemDesc_".$i;
				$gsm="txtGsm_".$i;
				$dia="txtDia_".$i;
				$findia="txtFinDia_".$i;
				$txtRollNo="txtRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$txtrecChallan="txtrecChallan_".$i;
				$RollNo=str_replace("'","",$$txtRollNo);
				if($RollNo=='') $RollNo=0;
				//$itemDesc=str_replace("'","",$$prod_desc).', '.str_replace("'","",$$gsm).', '.str_replace("'","",$$dia);

				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($$cbofabricfrom."*".$$po_id."*'".$$prod_desc."'*".$$prod_id."*'".$$gsm."'*'".$$dia."'*'".$$findia."'*".$$cboDiaWidthType."*'".$RollNo."'*".$$txtBatchQnty."*'".$$txtrecChallan."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					$id_dtls=str_replace("'",'',$$updateIdDtls);
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls_batch.",".$batch_update_id.",".$$cbofabricfrom.",".$$po_id.",'".$$prod_desc."',".$$prod_id.",'".$$gsm."','".$$dia."','".$$findia."','".$$cboDiaWidthType."','".$RollNo."',".$$txtBatchQnty.",'".$$txtrecChallan."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					//$id_dtls_batch=$id_dtls_batch+1;
				}

			}

			//echo "10**".bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID==1  && $flag==1) $flag=1; else $flag=0;

			if($data_array_dtls_update!="")
			{
				$rID2=execute_query(bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));


				//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}

			//echo "6**0**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			if($data_array_dtls!="")
			{
				$rID3=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;

			}

			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

				$rID4=sql_multirow_update("pro_batch_create_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;

			}
		}

		$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
			$collarAndCuffArr = explode('$$',$collarAndCuffStr);
			$kk= 0 ;
			$data_array4 = ''; 
			foreach ($collarAndCuffArr as $row) 
			{
				$id6 = return_next_id_by_sequence(  "subcon_ord_collar_and_cuff_dtls_seq", "subcon_ord_collar_and_cuff_dtls", $con );

				$collarAndCuffDataArr = explode('=',$row);
				$body_part 		= $collarAndCuffDataArr[0];
				$grey_size 		= $collarAndCuffDataArr[1];
				$finish_size	= $collarAndCuffDataArr[2];
				$gmts_size 		= $collarAndCuffDataArr[3];
				$qnty_pics 		= $collarAndCuffDataArr[4];
				$needle_per_cm 	= $collarAndCuffDataArr[5];
				if ($kk==0) 
				{
					$data_array4 .="(".$id6.",".$update_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id6.",".$update_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}
			 //echo "10**INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4; die;
			$rID6=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID6==1 &&  $flag==1) $flag=1; else $flag=0;
			 if($flag ==1)
			{
				$rID7=execute_query( "delete from subcon_ord_collar_and_cuff_dtls where ord_mst_id=$update_id",0);
				if($rID7){
					$rID8=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
				}
				if($rID8==1 &&  $flag==1) $flag=1; else $flag=0;
			} 

			
		}
		//echo "10**".$rID6."=".$rID7."=".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2) // Not Used Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$sql = "select id from pro_fab_subprocess where batch_id=$update_id and entry_form in(32,35) and status_active=1 and is_deleted=0";
		$data_array = sql_select($sql, 1);
		if (count($data_array) > 0) {
			echo "13**" . str_replace("'", "", $update_id);disconnect($con);die;
		}

		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$changeStatus = sql_update("pro_batch_create_mst", $field_array_status, $data_array_status, "id", $update_id, 1);
		$changeStatus2 = sql_update("pro_batch_create_dtls", $field_array_status, $data_array_status, "mst_id", $update_id, 1);
		$rID2 = 1;
			if($collarAndCuffStr !="" )
			{
				$rID2 = sql_delete("subcon_ord_collar_and_cuff_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'ord_mst_id',$update_id,1);
			}
		//echo $changeStatus."&&".$changeStatus2."&&".$changeStatus3;die;
		if ($db_type == 0) {
			if ($changeStatus && $changeStatus2 ) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "7**" . str_replace("'", "", $update_id);

			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($changeStatus && $changeStatus2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				oci_rollback($con);
				echo "7**" . str_replace("'", "", $update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	function js_set_value( batch_id,batch_no,ext_from,unloaded_batch)
	{
		document.getElementById('hidden_batch_id').value=batch_id;
		document.getElementById('hidden_batch_no').value = batch_no;
		document.getElementById('hidden_unloaded_batch').value = unloaded_batch;
		document.getElementById('hidden_ext_from').value = ext_from;

		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:600px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="500" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="3"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>
                        <th>Batch SL</th>
                        <th>Batch No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
							<input type="hidden" name="hidden_ext_from" id="hidden_ext_from" value="">
							<input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" style="width:140px" class="text_boxes"  name="txt_search_batch_sl" id="txt_search_batch_sl" placeholder="Write Before ( - )" />
                        <?
                           //$search_by_arr=array(1=>"Batch No");
                            //echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_batch_sl').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_string_search_type').value, 'create_batch_search_list_view', 'search_div', 'subcon_batch_creation_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>
        </form>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	//print_r ($data);
	$data=explode('_',$data);
	$search_sl=$data[1];
	$batch_number_search =$data[0];
	$company_id =$data[2];
	$search_type =$data[3];

	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	if($search_sl!='') $search_sl_cond=" and a.id='$search_sl'"; else $search_sl_cond="";

	if($search_type==1)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no='$batch_number_search'"; else $batch_number_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search%'"; else $batch_number_cond="";
	}
	else if($search_type==2)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '$batch_number_search%'"; else $batch_number_cond="";
	}
	else if($search_type==3)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search'"; else $batch_number_cond="";
	}



	if($db_type==0)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min, group_concat(b.po_id) as po_id from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=36 $batch_number_cond $search_sl_cond group by a.id, a.batch_no, a.extention_no order by a.id DESC";
	}
	elseif($db_type==2)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=36 $batch_number_cond $search_sl_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min order by a.id DESC";
	}
	//echo $sql;

	$nameArray=sql_select( $sql );
	$batch_id=array();
	foreach ($nameArray as $row) {
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$batch_id[] .= $row[csf("id")];
	}
	$sql_load_unload="select id, batch_id,load_unload_id,result from pro_fab_subprocess where batch_id in (".implode(",",$batch_id).") and load_unload_id in (1,2) and entry_form=38 and is_deleted=0 and status_active=1";
	$load_unload_data=sql_select($sql_load_unload);
	foreach ($load_unload_data as $row)
	{
		if($row[csf('load_unload_id')]==1)
		{
			$load_unload_arr[$row[csf('batch_id')]] = $row[csf('load_unload_id')];
		}
		else if($row[csf('load_unload_id')]==2 )
		{
			$unloaded_batch[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
	}

	$re_dyeing_from = return_library_array("select  re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0 and entry_form=36","re_dyeing_from","re_dyeing_from");
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="70">Batch No</th>
                <th width="40">Ex.</th>
                <th width="90">Color</th>
                <th width="80">Batch Weight</th>
                <th width="80">Total Trims Weight</th>
                <th width="70">Batch Date</th>
                <th>PO No.</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
            <?
				$i=1;

				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$order_no='';
					$order_id=array_unique(explode(",",$selectResult[csf("po_id")]));
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=", ".$po_arr[$val];
					}
					if($re_dyeing_from[$selectResult[csf('id')]])
					{
						$ext_from = $re_dyeing_from[$selectResult[csf('id')]];
					}else{
						$ext_from = "0";
					}

				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $ext_from;?>','<? echo $unloaded_batch[$selectResult[csf('id')]]; ?>')">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="70" align="center"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                        <td width="40" align="center"><? echo $selectResult[csf('extention_no')]; ?></td>
                        <td width="90"  align="center"><p><? echo  $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                        <td width="80"  align="center"><p><? echo $selectResult[csf('batch_weight')]; ?></p></td>
                        <td width="80"  align="center"><p><? echo $selectResult[csf('total_trims_weight')]; ?></p></td>
                        <td width="70"  align="center"><p><? echo change_date_format($selectResult[csf('batch_date')]); ?></p></td>
                        <td><p><? echo $order_no; ?></p></td>
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
    </div>
    <?
	//echo  create_list_view("list_view", "Batch No,Ext. No,Batch Weight,Total Trims Weight, Batch Date, Color", "100,70,80,80,80,80","600","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,color_id", $arr, "batch_no,extention_no,batch_weight,total_trims_weight,batch_date,color_id", "",'','0,0,2,2,3,0');

	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$batch_id=$data[1];
	$batch_against=$data[0];
	//echo $batch_against.'dd';
	$batch_no = $data[2];
	$ext_from = $data[3];
	$company_id = $data[4];
	$unloaded_batch = $data[5];
	//echo "select id, company_id, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, color_id, color_range_id, process_id, DATE_FORMAT(insert_date,'%y') as year from pro_batch_create_mst where id='$batch_id'";
	if($db_type==0)
	{
		$year_cond=" DATE_FORMAT(insert_date,'%y') as year";
	}
	else if($db_type==2)
	{
		$year_cond=" TO_CHAR(insert_date,'RR') as year";
	}
	$incrementExtentionNo="";
	if($batch_against==2) // Re-dyeing- Extention sequence maintain
	{
		if($unloaded_batch!="" && $ext_from ==0)
		{
			$exists_data_no = sql_select("select a.batch_no,max(a.extention_no) as max_extention_no from pro_batch_create_mst a  where a.batch_no = '".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.entry_form=36 and a.is_deleted = 0 group by batch_no");
			//echo "select a.batch_no,max(a.extention_no) as max_extention_no from pro_batch_create_mst a  where a.batch_no = '".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.entry_form=36 and a.is_deleted = 0 group by batch_no";
			$exists_extention_no = $exists_data_no[0][csf('max_extention_no')];
			if($exists_extention_no>0)
			{
				$incrementExtentionNo = $exists_extention_no+1;
			}else {
				$incrementExtentionNo = 1;
			}
		}
	}

	$dyeing_batch="Select batch_id from pro_fab_subprocess where batch_id='$batch_id' and entry_form=38 and status_active=1 and is_deleted=0";
	$dyeing_batch_result=sql_select($dyeing_batch);
	foreach ($dyeing_batch_result as $row)
	{
		echo "document.getElementById('dyeing_batch_id').value 	= '".$row[csf("batch_id")]."';\n";
	}


	//$main_process_id=return_field_value("c.main_process_id","pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c","a.id=b.mst_id and b.po_id=c.id and a.id='$batch_id' and a.status_active=1 and b.status_active=1 and c.status_active=1");


	$data_array=sql_select("select id, company_id, location_id, floor_id, machine_no, batch_against, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, re_dyeing_from, color_id, color_range_id, process_id,double_dyeing,collar_qty,cuff_qty,dur_req_hr, dur_req_min, exp_load_hr, exp_load_min, batch_delivery_date,remarks, $year_cond from pro_batch_create_mst where id='$batch_id' and entry_form=36");
	foreach ($data_array as $row)
	{
		//if($row[csf("extention_no")]==0) $ext_no=''; else $ext_no=$row[csf("extention_no")];
		if($incrementExtentionNo=="")
		{
			if ($row[csf("extention_no")] == 0) $incrementExtentionNo = ''; else $incrementExtentionNo = $row[csf("extention_no")];
		}

		$serial_no=$row[csf("id")]."-".$row[csf("year")];

		$process_name='';
		$process_id_array=explode(",",$row[csf("process_id")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}

		echo "document.getElementById('txt_batch_sl_no').value 		= '".$serial_no."';\n";
		echo "document.getElementById('cbo_batch_against').value 	= '".$row[csf("batch_against")]."';\n";
		echo "active_inactive();\n";
		echo "document.getElementById('txt_batch_date').value 		= '".change_date_format($row[csf("batch_date")])."';\n";
		echo "document.getElementById('txt_batch_weight').value 	= '".$row[csf("batch_weight")]."';\n";
		echo "document.getElementById('cbo_company_id').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_tot_trims_weight').value = '".$row[csf("total_trims_weight")]."';\n";
		echo "document.getElementById('txt_batch_number').value 	= '".$row[csf("batch_no")]."';\n";
		//echo "document.getElementById('txt_ext_no').value 			= '".$ext_no."';\n";
		echo "document.getElementById('txt_ext_no').value = '" . $incrementExtentionNo . "';\n";
		echo "document.getElementById('txt_batch_color').value 		= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('cbo_color_range').value 		= '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_process_id').value 		= '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('txt_process_name').value 	= '".$process_name."';\n";
		echo "document.getElementById('txt_du_req_hr').value 		= '".$row[csf("dur_req_hr")]."';\n";
		echo "document.getElementById('txt_du_req_min').value 		= '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_exp_load_hr').value 		= '".$row[csf("exp_load_hr")]."';\n";
		echo "document.getElementById('txt_exp_load_min').value 		= '".$row[csf("exp_load_min")]."';\n";
		echo "document.getElementById('txt_batch_delivery_date').value 	= '".$row[csf("batch_delivery_date")]."';\n";
		echo "document.getElementById('cbo_double_dyeing').value 		= '".$row[csf("double_dyeing")]."';\n";
		echo "document.getElementById('txt_collar_qty').value 		= '".$row[csf("collar_qty")]."';\n";
		echo "document.getElementById('txt_cuff_qty').value 		= '".$row[csf("cuff_qty")]."';\n";
		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hide_update_id').value 		= '';\n";
		echo "document.getElementById('ext_from').value = '".$ext_from."';\n";
		echo "document.getElementById('unloaded_batch').value = '".$unloaded_batch."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";

		//echo "document.getElementById('hidden_main_process_id').value 	= '1';\n";

		echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_dyeing_floor').value 	= '".$row[csf("floor_id")]."';\n";

		echo "load_drop_down( 'requires/subcon_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_dyeing_floor').value, 'load_drop_down_machine', 'machine_td' );";

		echo "document.getElementById('txt_machine_no').value 	= '".$row[csf("machine_no")]."';\n";




		if($batch_against==2)
		{
			echo "document.getElementById('cbo_batch_against').value = '".$batch_against."';\n";
			//echo "$('#txt_ext_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_batch_color').attr('disabled','disabled');\n";
			echo "$('#txt_batch_number').attr('readOnly','readOnly');\n";
			echo "$('#cbo_color_range').attr('disabled','disabled');\n";
			echo "$('#txt_process_name').attr('disabled','disabled');\n";
		}

		if($row[csf("batch_against")]==2)
		{
			$prv_batch_against=return_field_value("batch_against","pro_batch_create_mst","id=$row[re_dyeing_from]");
			echo "document.getElementById('hide_batch_against').value = '".$prv_batch_against."';\n";
			echo "document.getElementById('hide_update_id').value = '".$row[csf("id")]."';\n";
		}
		else
		{
			echo "document.getElementById('hide_batch_against').value = '".$row[csf("batch_against")]."';\n";
			echo "document.getElementById('hide_update_id').value = '';\n";
		}

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";

	}
	exit();
}

if( $action == 'batch_details' )
{
	$data=explode('**',$data);
	$batch_id=$data[1];
	$batch_against=$data[0];
	$dyeing_batch_id=$data[2];
	$tblRow=0;

	if($batch_against==2)
	{
		$disbled="disabled='disabled'";
		$disbled_drop_down=1;
	}
	elseif ($batch_against==1)
	{
		if ($dyeing_batch_id=='')
		{
			$disbled="";
			$disbled_drop_down=0;
		}
		else
		{
			$disbled="disabled='disabled'";
			$disbled_drop_down=1;
		}
	}
 	$party_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
 	$job_no_arr = return_library_array( "select id, job_no_mst from subcon_ord_dtls",'id','job_no_mst');
 	$main_process_arr=return_library_array( "select id, main_process_id from subcon_ord_dtls",'id','main_process_id');

	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id',' short_name');
	$order_arr=array();
	$order_sql=sql_select("select a.party_id, b.id, b.main_process_id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
	}

	$data_array=sql_select("select a.company_id,b.id, b.fabric_from, b.po_id, b.item_description, b.prod_id, b.fin_dia, b.width_dia_type, b.roll_no, b.batch_qnty, b.rec_challan,b.grey_dia,b.gsm from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id='$batch_id' and b.status_active=1 and b.is_deleted=0 and a.entry_form=36 order by b.id");
	foreach($data_array as $row)
	{
		$tblRow++;
		?>
		<tr class="general" id="tr_<? echo $tblRow; ?>">
			<?
				$po_no=$row[csf('po_id')];
				$item_id=$row[csf('prod_id')];
				$item_desc=$row[csf('item_description')];
				$ex_item_desc=explode(',',$item_desc);
				//$po_number=return_field_value("order_no","subcon_ord_dtls","id='$po_no' and status_active=1 and is_deleted=0 group by order_no",'order_no');
				?>
                <td>
                    <?
                        $fabricfrom=array(1=>"Receive",2=>"Production",3=>"Issue");
						echo create_drop_down("cbofabricfrom_".$tblRow, 70, $fabricfrom, "", 0, "", $row[csf('fabric_from')], "", 1, "", "", "", "", "", "", "cbofabricfrom[]");
                    ?>
                </td>
				<td align='center' id="field_po_id">

					<?
				//	echo "select b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id='".$row[csf('company_id')]."' and b.main_process_id='$main_process_arr[$po_no]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job like '%$job_no_arr[$po_no]%' group by b.id, b.order_no order by b.order_no ASC";
					echo create_drop_down( "poId_$tblRow", 90, "select b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id='".$row[csf('company_id')]."' and b.main_process_id='$main_process_arr[$po_no]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job like '%$job_no_arr[$po_no]%' group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 1, "-- Select PO --", $po_no, "hidden_data_load($tblRow);","", "", "", "", "", "","","poId[]" );

					?>

				</td>

				<td align='center' id='itemDescTd_<? echo $tblRow; ?>'>

				<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:70px;" value="<? echo $order_arr[$po_no]['po_no']; ?>" <? echo $disbled; ?> readonly />

                <input type="text" name="txtItemDesc[]" id="txtItemDesc_<? echo $tblRow; ?>" value="<? echo $row[csf('item_description')]; ?>" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_itemdes(<? echo $tblRow; ?>)" readonly />
                <input type="hidden" name="txtItemDescid[]" id="txtItemDescid_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes" style="width:60px" />
				</td>

				<td id="gsmTd_<? echo $tblRow; ?>">
					<input type="text" name="txtGsm[]" id="txtGsm_<? echo $tblRow; ?>" value="<? echo $row[csf('gsm')]; ?>" class="text_boxes_numeric" style="width:50px" <? echo $disbled; ?> readonly />
				</td>
				<td id="diaTd_<? echo $tblRow; ?>">
					<input type="text" name="txtDia[]" id="txtDia_<? echo $tblRow; ?>" value="<? echo $row[csf('grey_dia')];//$ex_item_desc[2]; ?>" class="text_boxes_numeric" style="width:50px" <? echo $disbled; ?> readonly />
				</td>
				<td id="finDiaTd_<? echo $tblRow; ?>">
					<input type="text" name="txtFinDia[]" id="txtFinDia_<? echo $tblRow; ?>" value="<? echo $row[csf('fin_dia')]; ?>" class="text_boxes_numeric" style="width:50px" <? echo $disbled; ?> readonly />
				</td>
				<td id='DiaWidthType_<? echo $tblRow; ?>'>
					<?
						echo create_drop_down("cboDiaWidthType_".$tblRow, 100, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", $disbled_drop_down, "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
			<td>
				<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes" style="width:40px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" <? echo $disbled; ?> />

				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" readonly />

				<input type="hidden" name="processId[]" id="processId_<? echo $tblRow;?>" style="width:50px" class="text_boxes" readonly />
			</td>
			<td>
				<input type="text" name="txtBatchQnty[]"  id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:70px" value="<? echo $row[csf('batch_qnty')]; ?>"  />
			</td>
            <td>
                <input type="text" name="txtrecChallan[]" id="txtrecChallan_<? echo $tblRow; ?>" value="<? echo $row[csf('rec_challan')]; ?>" class="text_boxes" style="width:60px" />
            </td>
            <td>
                <input type="text" name="txtJobParty[]" id="txtJobParty_<? echo $tblRow; ?>" value="<? echo $party_arr[$order_arr[$po_no]['party_id']]; ?>" class="text_boxes" style="width:50px" />
            </td>
			<td width="70">
				<input type="button" id="increase_<? echo $tblRow; ?>" name="increase_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
				<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
			</td>
		</tr>
	<?
	}
	exit();
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();

		function check_all_data()
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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
			var old=document.getElementById('txt_process_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] );
				}
			}
		}

		function js_set_value( str )
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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

			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1; $process_row_id=''; $not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124);
					//$process_id_print_array=array(25,31,32,33,34,35,39,60,63,64,65,66,67,68,69,70,71,82,83,84,89,90,91,93,125,129,132,133,136,137,146);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						//if(in_array($id,$process_id_print_array))
						if (!in_array($id, $not_process_id_print_array))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if(in_array($id,$hidden_process_id))
							{
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								</td>
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						}
                    }
                ?>
                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="batch_no_creation")
{
	$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");

	if($batch_no_creation!=1) $batch_no_creation=0;

	echo "document.getElementById('batch_no_creation').value 				= '".$batch_no_creation."';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if($batch_no_creation==1)
	{
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}
	exit();
}

if($action=="roll_maintained")
{

	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	exit();
}

if($action=="batch_card_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];


	$batch_mst_update_id=str_pad($update_id,10,'0',STR_PAD_LEFT);
	//echo $data[3]; die;
	if($db_type==2) $machine_field="machine_no || '-' || brand as machine_no";
	else $machine_field="concat(machine_no,'-',brand) as machine_no";
	$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor", 'id', 'floor_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$machine_library=return_library_array( "select id, $machine_field from lib_machine_name", "id", "machine_no");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$job_buyer=return_library_array( "select subcon_job, party_id from subcon_ord_mst", "subcon_job", "party_id");
	if($db_type==0)
	{
		$year_cond=" DATE_FORMAT(a.insert_date,'%y') as year";
		$sql=" SELECT a.id, a.batch_no, a.batch_date, a.floor_id, a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, group_concat(distinct(c.order_no)) AS po_number, a.total_trims_weight, c.job_no_mst,c.cust_buyer,c.cust_style_ref, c.delivery_date,a.remarks,$year_cond from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.batch_no,a.batch_date, a.extention_no,a.remarks";
	}
	else if ($db_type==2)
	{
		$year_cond=" TO_CHAR(a.insert_date,'RR') as year";
		$sql=" SELECT a.id,a.batch_no, a.batch_date, a.color_id, a.floor_id, a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number, a.total_trims_weight, listagg(cast(c.job_no_mst as varchar2(4000)),',') within group (order by c.job_no_mst) as job_no_mst, listagg(cast(c.delivery_date as varchar2(4000)),',') within group (order by c.delivery_date) as delivery_date,a.remarks ,c.cust_buyer,c.cust_style_ref,$year_cond from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.batch_no, a.batch_date, a.color_id, a.floor_id, a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, a.total_trims_weight, a.remarks, c.cust_buyer, c.cust_style_ref, a.insert_date ";
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	if($data[4]==1)
	{
		$batch_sl_no=$dataArray[0][csf('id')]."-".$dataArray[0][csf('year')];
	}
	else
	{
		$batch_sl_no=$data[2];
	}


    ?>
    <div style="width:930px">
    <div align="right"><strong>Printing Time: &nbsp;</strong> <? echo $date=date("F j, Y, g:i a"); ?> </div>
	<table width="930" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18"><strong><u> Batch Card</u></strong></td>
        </tr>
         <tr>
            <td colspan="6" align="left" style="font-size:16px;"><strong><u>Reference Details</u></strong></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch No</strong></td><td width="250px"> : <? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch Ext.</strong></td><td width="200px"> : <? echo $dataArray[0][csf('extention_no')]; ?></td>
            <td width="110"><strong>Batch Serial</strong></td><td width="150px" colspan="2"> : <? echo $batch_sl_no; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch Color</strong></td><td width="250px"> : <? echo   $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            <td width="110"><strong>Color Range</strong></td><td width="200"> : <? echo $color_range[$dataArray[0][csf('color_range_id')]];?></td>
            <td width="110"><strong>Batch Weight</strong></td><td width="150" colspan="2"> : <? echo $dataArray[0][csf('batch_weight')];?></td>
        </tr>
        <tr>
            <td width="110"><strong>Party</strong></td>
            <td width="250"> : <? $job_no_party=implode(",",array_unique(explode(",",$dataArray[0][csf('job_no_mst')]))); echo $buyer_arr[$job_buyer[$job_no_party]] ; ?>

            </td>
            <td width="110"><strong>Job No</strong></td>
            <td width="200"> : <?
			if($db_type==0)
			{
				$job_no=$dataArray[0][csf('job_no_mst')];
			}
			else if ($db_type==2)
			{
				$job_no=implode(",",array_unique(explode(",",$dataArray[0][csf('job_no_mst')])));
			}

			echo $job_no; ?>

			</td>
            <td width="110"><strong>Delivery Date</strong></td>
            <td colspan="2" width="150"> : <?
			$delivery_date=$dataArray[0][csf('delivery_date')];
			$delivery_date=array_unique(explode(",",$delivery_date));
			$ddate_con='';
			foreach($delivery_date as $ddate)
			{
				if($ddate_con=='') $ddate_con=change_date_format($ddate);else $ddate_con.=','.change_date_format($ddate);
			}
			echo $ddate_con;
			// echo ($delivery_date == '0000-00-00' || $delivery_date == '' ? '' : ($delivery_date));

			 ?>

            </td>
        </tr>
        <tr>
            <td width="110" align="left"><strong>Order No</strong></td>
            <td width="250"> : <?
			if($db_type==0)
			{
				$po_no=$dataArray[0][csf('po_number')];
			}
			else if ($db_type==2)
			{
				$po_no=implode(",",array_unique(explode(",",$dataArray[0][csf('po_number')])));
			}
			echo $po_no; ?>

			</td>

			<td width="110" align="left"><strong>Machine No</strong></td>
			<td width="200"> : <?

			echo $machine_library[$dataArray[0][csf('machine_no')]];
			?>

			</td>

            <td width="110"><strong>Cust Buyer  </strong></td>
            <td width="150" align="left" style="font-size:24px"> :<? echo $dataArray[0][csf('cust_buyer')];
			?>
            </td>
        </tr>
        <tr>
        	<td width="110"><strong>Cust Style Ref </strong></td>
        	<td width="250">:<? echo $dataArray[0][csf('cust_style_ref')]; ?></td>
        	<td width="110"><strong>Bar Code  </strong></td>
            <td  width="200" id="barcode_img_id" align="left" style="font-size:24px"></td>
        	<td width="110">Batch Date</td>
            <td colspan="2" width="150">: <? echo change_date_format($dataArray[0][csf('batch_date')]);?></td>
       </tr>
       <tr>
       		<td width="110"><strong>Dyeing Floor</strong></td>
        	<td>:<? echo $floor_arr[$dataArray[0][csf('floor_id')]]; ?></td>
       		<td width="110">Remarks</td>
            <td colspan="4">: <? echo $dataArray[0][csf('remarks')];?></td>
       </tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="1070"  border="1" rules="all" class="rpt_table" style="font-size:14px">
       <thead bgcolor="#dddddd">
            <th width="30">SL</th>
            <th width="150">Const. & Comp.</th>
            <th width="50">GSM</th>
            <th width="60">Grey Dia/Width</th>
            <th width="60">Fin. Dia/Width</th>
            <th width="80">M/C Dia X Gauge</th>
            <th width="80">D/W Type</th>
            <th width="80">Grey Qty.</th>
            <th width="80">Rec. Challan</th>
            <th width="50">Roll No.</th>
            <th width="70">Yarn Lot</th>
			<th width="70">Brand</th>
            <th width="70">Yarn Count</th>
			<th width="70">Stitch Length</th>
            <th width="">ID Code</th>
        </thead>
        <tbody>
		<?
			$i=1;
			$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
			$yarn_lot_supp=return_library_array( "select lot, supplier_id from  product_details_master",'lot','supplier_id');
			$machine_lib_dia=return_library_array( "select id,dia_width from  lib_machine_name", "id", "dia_width"  );
			$machine_lib_gauge=return_library_array( "select id,gauge from  lib_machine_name", "id", "gauge"  );

			// $yarn_dtls_arr=array();$mc_dia_gauge_arr=array();
			// $yarn_lot_data=sql_select("select order_id, cons_comp_id, yarn_lot, yrn_count_id, machine_id from  subcon_production_dtls where  order_id='$po_id' and product_type=2 and status_active=1 and is_deleted=0   group by order_id, cons_comp_id, yarn_lot, yrn_count_id, machine_id");

			// foreach($yarn_lot_data as $rows)
			// {
			// 	$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_lot']=$rows[csf('yarn_lot')];//implode(",",array_unique($rows[csf('yarn_lot')]));
			// 	$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_count']=$rows[csf('yarn_count')];
			// 	$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['machine_no_id']=$rows[csf('machine_no_id')];
			// }
			//var_dump($yarn_dtls_arr);
			$mc_dia_gauge_data=sql_select("select b.order_id, b.material_description,b. mc_dia, b.mc_gauge,b.stitch_length,a.chalan_no,b.lot_no,b.brand from sub_material_mst a ,sub_material_dtls b where a.id=b.mst_id and b.status_active=2 and b.is_deleted=0");
			foreach($mc_dia_gauge_data as $datas)
			{
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_dia']=$datas[csf('mc_dia')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_gauge']=$datas[csf('mc_gauge')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['stitch_length']=$datas[csf('stitch_length')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['chalan_no']=$datas[csf('chalan_no')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['lot_no']=$datas[csf('lot_no')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['brand']=$datas[csf('brand')];
			}
			// $sql_dtls="select a.id, SUM(a.batch_qnty) AS batch_qnty, a.roll_no, a.item_description, a.fin_dia,a.gsm,a.grey_dia, a.po_id, a.prod_id, a.width_dia_type, a.rec_challan, a.fabric_from,b.order_id, b.cons_comp_id, b.yarn_lot, b.yrn_count_id, b.machine_id  from pro_batch_create_dtls a,subcon_production_dtls b where a.mst_id=$update_id and b.order_id=a.po_id and b.product_type = 2 and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 GROUP BY a.id, a.roll_no, a.item_description, a.fin_dia,a.gsm,a.grey_dia, a.po_id, a.prod_id, a.width_dia_type, a.rec_challan, a.fabric_from,b.order_id, b.cons_comp_id, b.yarn_lot, b.yrn_count_id, b.machine_id";

			$sql_dtls="select a.id, SUM(a.batch_qnty) AS batch_qnty, a.roll_no, a.item_description, a.fin_dia,a.gsm,a.grey_dia, a.po_id, a.prod_id, a.width_dia_type, a.rec_challan, a.fabric_from,b.order_id, b.cons_comp_id, b.yarn_lot, b.yrn_count_id, b.machine_id,b.stitch_len  from pro_batch_create_dtls a left join subcon_production_dtls b on   b.order_id=a.po_id and b.product_type = 2 and b.status_active=1 and b.is_deleted=0  where a.mst_id=$update_id and  a.status_active=1 and a.is_deleted=0   GROUP BY a.id, a.roll_no, a.item_description, a.fin_dia,a.gsm,a.grey_dia, a.po_id, a.prod_id, a.width_dia_type, a.rec_challan, a.fabric_from,b.order_id, b.cons_comp_id, b.yarn_lot, b.yrn_count_id, b.machine_id,b.stitch_len";
			$sql_result=sql_select($sql_dtls);

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$desc=explode(",",$row[csf('item_description')]);
				//$y_count=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_count'];
				//$y_count_id=explode(',',$y_count);
				// $yarn_count_value='';
				// foreach ($y_count_id as $val)
				// {
				// 	if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];

				// }

				$yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
				$count_name="";
				foreach($yarn_count as $val)
				{
					if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
				}
				$yarn_lot_d=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_lot'];
				$exp_lot=explode(',',$yarn_lot_d);

				$machine_dia_up=$machine_lib_dia[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				$machine_gauge_up=$machine_lib_gauge[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				if($mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge']!="")
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'] .' X ' .$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge'];
				}
				else
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'];
				}
				if($row[csf('rec_challan')]==""){
					$rec_challan=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['chalan_no'];
				}else{
					$rec_challan=$row[csf('rec_challan')];
				}
				if($row[csf('yarn_lot')]==""){
					$yarn_lot=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['lot_no'];
				}else{
					$yarn_lot=$row[csf('yarn_lot')];
				}
				if($row[csf('stitch_length')]==""){
					$stitch_length=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['stitch_length'];
				}else{
					$stitch_length=$row[csf('stitch_length')];
				}
				if($row[csf('brand')]==""){
					$brand=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['brand'];
				}else{
					$brand=$row[csf('brand')];
				}
				if($row[csf('mc_dia')]==""){
					$mc_dia=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'];
				}else{
					$mc_dia=$row[csf('mc_dia')];
				}
				if($row[csf('mc_gauge')]==""){
					$mc_gauge=$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge'];
				}else{
					$mc_gauge=$row[csf('mc_gauge')];
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td style="font-size:14px" width="30"><? echo $i; ?></td>
					<td style="font-size:14px" width="150" ><? echo $row[csf('item_description')];//$desc[0]; ?></td>
					<td style="font-size:14px" width="50" align="center"><? echo $row[csf('gsm')];//$desc[1]; ?></td>
					<td style="font-size:14px" width="60" align="center"><? echo $row[csf('grey_dia')];//$desc[2]; ?></td>
					<td style="font-size:14px" width="60"><? echo $row[csf('fin_dia')]; ?></td>
					<td style="font-size:14px" width="80" align="center"><? if($row[csf('fabric_from')]==1 || $row[csf('fabric_from')]==3) { echo $mc_dia."X".$mc_gauge; }else{if($machine_gauge_up!='') echo $machine_dia_up." X ".$machine_gauge_up; else echo $machine_dia_up;} ?></td>
					<td style="font-size:14px" width="80" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td style="font-size:14px" width="80" align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
                    <td style="font-size:14px" width="80" align="center"><? echo $rec_challan; ?></td>
					<td style="font-size:14px" width="50" align="center"><? echo $row[csf('roll_no')];  ?></td>
					<td style="font-size:14px" width="70" align="center"><p><? echo $yarn_lot;  ?></p></td>
					<td style="font-size:14px" width="70" align="center"><p><? echo $brand;  ?></p></td>
					<td style="font-size:14px" width="70" align="center"><? echo $count_name; ?></td>
					<td style="font-size:14px" width="70" align="center"><? echo $stitch_length; ?></td>
					<td style="font-size:14px" width="">&nbsp;</td>
				</tr>
				<?php
			   $b_qty+= $row[csf('batch_qnty')];
				$i++;
			}
			?>
        </tbody>
        <tr>
            <td colspan="7" align="right"><b>Sum:</b></td>
            <td align="right" ><b><? echo number_format($b_qty,2); ?> </b></td>
            <td colspan="7">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" align="right"><b>Trims Weight:</b></td>
            <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
            <td colspan="7">&nbsp;</td>
        </tr>
         <tr>
            <td colspan="7" align="right"><b>Total:</b></td>
            <td align="right"><b><? echo number_format($b_qty+$dataArray[0][csf('total_trims_weight')],2); ?></b></td>
            <td colspan="7">&nbsp;</td>
        </tr>
         <tr>
            <td colspan="15"  align="right">&nbsp; </td>
        </tr>
			<?
                $process=$dataArray[0][csf('process_id')];
                $process_id=explode(',',$process);
                $process_value='';
                $i=1;
                foreach ($process_id as $val)
                {
                    if($process_value=='') $process_value=$i.'. '. $conversion_cost_head_array[$val]; else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
                    $i++;
                }
             ?>
            <tr>
                <th colspan="15" align="left" ><strong>Process Required</strong></th>
            </tr>
            <tr>
                <td colspan="15" title="<? echo $process_value; ?>"> <strong><? echo $process_value; ?> </strong></td>
            </tr>
            <tr>
                <td colspan="5" align="left">Heat Setting Date:</td>
                <td colspan="4" align="left">Loading Date:</td>
                <td colspan="6" align="left">UnLoading Date:</td>
            </tr>
        </table>
     <div style="float:left; margin-left:10px;"><strong> Quality Instruction(<i>Hand Written</i>)</strong> </div>
    <table width="930" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="440">
                <table cellspacing="0" width="430"  align="left" border="1" rules="all" class="rpt_table">
                    <tr>
                    	<th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table width="430"  cellspacing="0"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="440" valign="top">
                <table width="430" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:451px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="428" >
                    <tr>
                        <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <th><b>Length % </b></th><th><b>Width % </b></th><th><b> Twist % </b></th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="930" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="930" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:930px; height:120px" >&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <?
				$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$batch_sl_no)."' and entry_form = 36 order by id");
				if(count($data_array)>0)
				{
					?>
					<tr>
						<td width="930" colspan="3">
							<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060">
								<tr>
									<th>SL</th>
									<th><strong>Terms & Condition/Notes</strong></th>
								</tr>
								<?

								foreach($data_array as $row){
									$k++;
									?>
									<tr>
										<td style="width:30px; text-align:center;"><? echo $k; ?></td>
										<td style=""><? echo $row[csf('terms')] ?></td>
									</tr>
									<?
								}
								?>
							</table>
						</td>
					</tr>

					<?php
				}

				?>
    </table>
    <br>
		<?
            echo signature_table(56, $company, "930px");
        ?>
    </div>

	<?
	if($data[4]==1){?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<? }else{?>
		<script type="text/javascript" src="../js/jquery.js"></script>
    	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<? }?>

    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
    <?
}
if( $action == "batch_color_popup"){
    echo load_html_head_contents("Process Name Info","../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
        <script>
            function js_set_value(color,job,main_process_id){
                document.getElementById('color_name').value=color;
                document.getElementById('job_no').value=job;
                document.getElementById('main_process_name').value=main_process_id;
                parent.emailwindow.hide()
            }
        </script>
    <body>
	<fieldset style="width:720px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="720" class="rpt_table">
                <thead>
                    <tr>
                    	<th>Process</th>
						<th>Party Name</th>
                        <th>Job Search</th>
                        <th>Order Search</th>
                        <th colspan="2">Date ranges</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="po_no" id="po_no" value="">
                            <input type="hidden" name="job_no" id="job_no" value="">
                            <input type="hidden" name="main_process_name" id="main_process_name" value="">
                            <input type="hidden" name="start" id="start" value="">
                            <input type="hidden" name="end" id="end" value="">
                            <input type="hidden" name="color_name" id="color_name" value="">
                        </th>
                    </tr>
                </thead>
                <tr class="general">

                	<td align="center">
                        <? echo create_drop_down( "cbo_process_name", 90, $production_process,"", 1, "--Select Process--",4,"", "","" ); ?>
                    </td>
					 <td id="">
					 	<?
						$sql_buyer="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";

						echo create_drop_down( "cbo_party_name", 100, $sql_buyer,"id,buyer_name", 1, "-- Select Party --", $selected, "" );
				 ?>
					 </td>

                    <td align="center">
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_job" id="txt_job" placeholder="Job" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_order" id="txt_order" placeholder="Order" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:100px" class="text_boxes datepicker "  name="txt_start" id="txt_start" readonly />
                    </td>
                    <td align="center">
                        <input type="text" style="width:100px" class="text_boxes datepicker"  name="txt_end" id="txt_end" readonly />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order').value+'_'+document.getElementById('txt_job').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_start').value+'_'+document.getElementById('txt_end').value+'_'+document.getElementById('cbo_process_name').value+'_'+document.getElementById('cbo_party_name').value, 'create_batch_color_search_list_view', 'search_div', 'subcon_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>
        </form>
    </fieldset>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//set_all();
</script>
</html>
<?
exit();
}
if( $action== "create_batch_color_search_list_view")
{
    $data=explode('_',$data);
	$order_val=$data[0];
	$job_val=$data[1];
	$company_id =$data[2];
	$txt_start =$data[3];
	$txt_end =$data[4];
	$cbo_process_name =$data[5];
	$party_id =$data[6];

	if($party_id!=0) $party_id_cond="and a.party_id=$party_id";else $party_id_cond="";

	if($cbo_process_name==0)
	{
		echo "<p align='center'>Please Select a Process</p>";
		die;
	}
	$batch_color_variable= sql_select("select id, dyeing_fin_bill from variable_settings_subcon where company_id='$company_id' and variable_list=19 order by id");
	$batch_color_from=0;
	if(count($batch_color_variable)>0){
		foreach ($batch_color_variable as $row) {
			$batch_color_from=$row[csf('dyeing_fin_bill')];
		}
	}


	//print_r($data);
        $sql_cond = "";
        if($txt_start && $txt_end){
            if($db_type==0)
            {
                $sql_cond .= " and a.insert_date between '".change_date_format($txt_start,"yyyy-mm-dd")."' and '".change_date_format($txt_end,"yyyy-mm-dd")."' ";
            }
            else
            {
                $sql_cond .= " and a.insert_date between '".change_date_format($txt_start,"", "",1)."' and '".change_date_format($txt_end,"", "",1)."' ";
            }

        }

        if($job_val){
           $sql_cond .= " and a.job_no_prefix_num like '%$job_val%' ";
        }
        if($order_val){
           $sql_cond .= " and b.order_no like '%$order_val%' ";
        }
        if($batch_color_from==1){
        	$sql= "SELECT d.color_id, a.job_no_prefix_num,a.party_id, b.order_no, a.subcon_job, b.process_id, a.insert_date from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_ord_color_breakdown d  where a.subcon_job=b.job_no_mst and b.id=c.order_id and d.subcon_ord_breakdown_id=c.id and a.company_id = $company_id and b.main_process_id=$cbo_process_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond $party_id_cond and d.color_id  is not null order by a.insert_date DESC";
        }
        else{
        	$sql= "SELECT c.color_id, a.job_no_prefix_num,a.party_id, b.order_no, a.subcon_job, b.process_id, a.insert_date from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id = $company_id and b.main_process_id=$cbo_process_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond $party_id_cond and c.color_id  is not null order by a.insert_date DESC";
        }

       //echo $sql;
        ?>
        <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="150">Job No</th>
				<th width="100">Party Name</th>
                <th width="70">Year</th>
                <th width="150">Order No</th>
                <th width="">Color</th>

            </thead>
        </table>
        <div style="width:718px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search" >
            <?
                    $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
                    $i=1;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        /*$processid=explode(",",$selectResult[csf('process_id')]);
                        $dye_fin_array=array(25,26,31);
                        if(array_intersect($dye_fin_array,$processid))
                        {*/

                        ?>

                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $color_arr[$selectResult[csf('color_id')]];?>','<? echo $selectResult[csf('subcon_job')]?>','<? echo $cbo_process_name?>')">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="150" align="center"><p><? echo $selectResult[csf('subcon_job')]; ?></p></td>
							 <td width="100" align="center"><p><? echo $buyer_arr[$selectResult[csf('party_id')]]; ?></p></td>
                            <td width="70" align="center"><? echo date("Y",strtotime($selectResult[csf('insert_date')])); ?></td>
                            <td width="150"><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                            <td width="" title="<? echo $selectResult[csf('color_id')];?>"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                        </tr>

                        <?
                            $i++;
                         //   }
                        }
			?>
            </table>
        </div>
	</div>
        <?
}
if($action == "show_color_listview")
{
    $data = explode("*", $data);
    $company_id = $data[0];
    $job = $data[1];
    $main_process_id = $data[2];

    $sql_cond = "";

    if ($job) {
        $sql_cond .= " and a.subcon_job like '%$job%' ";
    }

    $sql = "select c.color_id, a.job_no_prefix_num, b.order_no, a.subcon_job, b.process_id, a.insert_date
                from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id
                and a.company_id = $company_id and b.main_process_id=$main_process_id
                and a.status_active = 1 and a.is_deleted = 0
                and b.status_active = 1 and b.is_deleted = 0
                $sql_cond
                and c.color_id  is not null";

        $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
        $i = 1;
        $nameArray = sql_select($sql);
?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
                <thead>
                    <th width="25">SL</th>
                    <th width="80">Color</th>
                    <th width="75">Job</th>
                    <th width="">Order</th>
                </thead>
                <tbody>
    <?
        foreach ($nameArray as $selectResult) {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

            $processid = explode(",", $selectResult[csf('process_id')]);
			$process_name="";
            foreach($processid as $proc_id)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$proc_id]; else $process_name.=','.$conversion_cost_head_array[$proc_id];
			}

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="color_set_value('<? echo $color_arr[$selectResult[csf('color_id')]]; ?>','<? echo $selectResult[csf('subcon_job')]; ?>','<? echo $selectResult[csf('process_id')]; ?>','<? echo $process_name; ?>')">
                    <td width="" align="center"><? echo $i; ?></td>
                    <td width="" title="<? echo $selectResult[csf('color_id')]; ?>"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                    <td width="" align="center"><p><? echo $selectResult[csf('subcon_job')]; ?></p></td>
                    <td width=""><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                </tr>

                <?
            $i++;
        	//}
    }
    ?>
                  </tbody>
    </table>
    <?
}
?>