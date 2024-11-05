<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) { 
		echo create_drop_down("cbo_working_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company_name", 142, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company_name", 142, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

 //--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/yarn_lot_ratio_planning_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 140, "select id,floor_name from lib_prod_floor where production_process=2 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
else if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

if ($action=="load_drop_down_buyer")
{    
	$data=explode("**",$data);
	$sql="select distinct c.id,c.buyer_name from  wo_po_break_down a, wo_po_details_master b, lib_buyer c where a.job_no_mst=b.job_no and a.is_confirmed=1 and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1 and a.is_confirmed=1";
	$result=sql_select($sql);
	foreach($result as $val)  
	{
		$buyer_value=$val[csf('buyer_name')];
	}
	echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
	exit();
}

if ($action=="load_drop_down_order_garment")
{
	$ex_data = explode("_",$data);
	$gmt_item_arr=return_library_array( "select gmts_item_id from wo_po_details_master where job_no='".$ex_data[0]."' and status_active=1",'id','gmts_item_id');
    $gmt_item_id=implode(",",$gmt_item_arr);
	if(count(explode(",",$gmt_item_id))==1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $garments_item,"", 1, "-- Select Item --", $gmt_item_id, "style_order_data($ex_data[1]+'_'+1);","",$gmt_item_id);
	}
    else if(count(explode(",",$gmt_item_id))>1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $garments_item,"", 1, "-- Select Item --", $selected, "style_order_data($ex_data[1]+'_'+1);","",$gmt_item_id);
	}
	else if(count(explode(",",$gmt_item_id))==0)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
	}
	exit();
}

if ($action=="load_drop_down_color")
{
	$ex_data = explode("_",$data);
	$color_item_arr=return_library_array( "SELECT a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where  a.id=b.color_number_id and c.is_confirmed=1 and c.id =b.po_break_down_id and b.job_no_mst='".$ex_data[0]."' and b.item_number_id='".$ex_data[2]."' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_confirmed=1  group by a.id,a.color_name","id","color_name");
	echo create_drop_down( "cbocolor_$ex_data[1]", 100, $color_item_arr,"", 1, "-Select Color-",'', "reset_fld($ex_data[1]);style_order_data($ex_data[1]+'_'+0);");
	exit();
}

if ($action == "load_drop_down_store") {
	echo create_drop_down("cbo_store_name", 140, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", 0, "", '');
	exit();
}

if ($action=="load_drop_down_color_type")
{
	$ex_data = explode("_",$data);
	$color_type_arr=return_library_array( "SELECT color_type_id,color_type_id from wo_pre_cost_fabric_cost_dtls  where job_no='".$ex_data[0]."'    group by color_type_id","color_type_id","color_type_id");
	$color_type_arr_id=implode(",",$color_type_arr);
	echo create_drop_down( "cboColorType_$ex_data[1]", 100, $color_type,"", 1, "--select--",'', "reset_fld($ex_data[1])","",$color_type_arr_id);

	exit();
}

if ($action == "load_drop_down_store") {
	echo create_drop_down("cbo_store_name", 140, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", 0, "", '');
	exit();
}
// needed
if ($action=="load_drop_down_bodypart")
{
	$ex_data = explode("_",$data);

	 $sql="select a.id, b.body_part_id from sample_development_mst a,sample_development_fabric_acc b where a.id=b.sample_mst_id and a.style_ref_no='".$data."'  and b.knitinggm>0";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		
		$job_bodypart_id[$row[csf('body_part_id')]]=$row[csf('body_part_id')];
	}
	echo "document.getElementById('hidden_body_partstring').value  = '".implode(",",$job_bodypart_id)."';\n"; 
	
	exit();
}

//needed
if ($action=="load_drop_down_order_qty")
{
	$ex_data = explode("_",$data);

	 $sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where job_no_mst='".$ex_data[0]."' and item_number_id=".$ex_data[1]." and color_number_id=".$ex_data[2]." and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '".($row[csf("plan_qty")])."';\n"; 
		$plan_qty=$row[csf("plan_qty")];
	}
	if(($data[4]*1)>0)
	{
		$sql_marker_currnet="select sum(a.marker_qty) as mark_qty from ppl_cut_lay_dtls a,ppl_cut_lay_mst b where b.id=a.mst_id and b.job_no='".$ex_data[0]."' and a.gmt_item_id=".$ex_data[1]." and a.color_id=".$ex_data[2]."  and b.id=".$ex_data[4]." and a.status_active=1 and b.status_active=1";
		$result_current=sql_select($sql_marker_currnet);
		foreach($result_current as $crows)
		{
			echo "document.getElementById('txtmarkerqty_$ex_data[3]').value  = '".$crows[csf("mark_qty")]."';\n"; 
		}
	}

	$sql_marker="select sum(a.marker_qty) as mark_qty from ppl_cut_lay_dtls a,ppl_cut_lay_mst b where b.id=a.mst_id and b.job_no='".$ex_data[0]."' and a.gmt_item_id=".$ex_data[1]." and a.color_id=".$ex_data[2]." and a.status_active=1 and b.status_active=1";
	$result=sql_select($sql_marker);
	foreach($result as $rows)
	{
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".$rows[csf("mark_qty")]."';\n"; 
		$marker_qty=$rows[csf("mark_qty")];
	}
	$lay_balance=$plan_qty-$marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n"; 
	
	$sql_size_data=sql_select("select a.sizeset_no FROM ppl_size_set_mst a,ppl_size_set_dtls b where a.id=b.mst_id and a.job_no='".$ex_data[0]."' and b.item_number_id=".$ex_data[1]." and b.color_id=".$ex_data[2]." and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.sizeset_no ");
	echo "document.getElementById('txt_size_set_no').value  = '';\n";
	if(count($sql_size_data)==1) 
	{
		echo "document.getElementById('txt_size_set_no').value  = '".$sql_size_data[0][csf('sizeset_no')]."';\n";
	}
	else
	{
		echo "open_size_set_popup();\n";
	}

	exit();
}

if ($action=="tna_date_status")
{
	$ex_data = explode("**",$data);
	$cut_start_date=$ex_data[0];
	$cut_end_date=$ex_data[1];
	$order_all=$ex_data[2];
	//echo $cut_start_date;die;
	//**********************************Tna Date*********************************************************************
	for($sl=1; $sl<=$row_num; $sl++)
	{
		$cbo_order_id="cboorderno_".$sl;
		if($tna_order!="") $tna_order.=",".$$cbo_order_id;
		else $tna_order.=$$cbo_order_id;
	}
	$tna_variable=return_field_value("tna_integrated","variable_order_tracking"," company_name=$ex_data[3] AND variable_list=14");
	if($tna_variable==1)
	{
		$min_tna_date=return_field_value(" min(a.task_start_date) as min_start_date","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","min_start_date");
	  	$max_tna_date=return_field_value("max(a.task_finish_date) as max_end_date ","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","max_end_date");
	  
	 	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_all) and is_confirmed=1",'id','po_number');
	  
	//  $min_start_date=date("Y-m_d",strtotime($min_start_date));
	  $max_end_date=date("Y-m_d",strtotime($max_tna_date));
	  $cut_start_date=date("Y-m_d",strtotime($cut_start_date));
	  $cut_end_date=date("Y-m_d",strtotime($cut_end_date));
	  if($cut_end_date>$max_end_date) 
	  {
		 $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b where b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84");
		 if(count($sql_tna_date)>0)
			 {
				 foreach($sql_tna_date as $row)
				 {
					 if($poNumber=="")
					 {
						$poNumber=$order_number_arr[$row[csf('po_number_id')]];
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_end_date=date("d-m-Y",strtotime($po_en_date));
						$po_start_date=date("d-m-Y",strtotime($po_st_date));
					 }
					 else
					 {
						$poNumber=$poNumber."**".$order_number_arr[$row[csf('po_number_id')]]; 
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_start_date=$po_start_date."**".date("d-m-Y",strtotime($po_st_date));
						$po_end_date=$po_end_date."**".date("d-m-Y",strtotime($po_en_date));
					 }
				 }
			  $min_start_date=date("d-m-Y",strtotime($min_tna_date));
			  $max_end_date=date("d-m-Y",strtotime($max_tna_date)); 
			  echo "0##".$poNumber."##".$po_start_date."##".$po_end_date."##".$min_start_date."##".$max_end_date;die;
		 }
		  else echo 1;die;
	  }
	echo 1;die;
	}
	else echo 2;die;
	//***********************************End Tna date*******************************************************************************************
	exit();
}


if($action=="lot_popup")
{
	echo load_html_head_contents("Plies Info Roll Wise","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//echo $rollData;die;	
	?>
	<script>
	
		function openmypage_yarn_lot()
		{
				
			var job_no=$('#hidden_job_no').val();
			var size_set_no=$('#hidden_size_set_no').val();
			
			var title = 'Lot Ratio Pop up';		
			var page_link = 'yarn_lot_ratio_planning_controller.php?job_no='+job_no+'&garments_item='+<? echo $garments_item; ?>+'&color='+<? echo $color; ?>+'&store_id='+<? echo $store_id; ?>+'&update_id='+<? echo $update_id; ?>+'&action=yarn_receive_popup'+'&size_set_no='+size_set_no;	
			//alert(page_link);return;	 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=300px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
				
				if(barcode_nos!="")
				{
					var barcode_upd=barcode_nos.split("*");
					var row_num=$('#txt_tot_row').val();
					$("#tbl_list_search tbody").find("tr:gt(0)").remove();
					$('#txt_tot_row').val(1);
					for(var k=0; k<barcode_upd.length; k++)
					{
						yarn_data=barcode_upd[k].split("_");
						var i=k+1;
						if(k!=0) add_break_down_tr(k);
						else 	$("#alocatedQty_"+i).val("");
						$("#prodId_"+i).val(yarn_data[0]);
						$("#lotNo_"+i).val(yarn_data[3]);
						$("#yarnColorId_"+i).val(yarn_data[1]);
						$("#yarnDescription_"+i).val(yarn_data[4]);
						$("#yarnColor_"+i).val(yarn_data[2]);
						$("#colorPercentage_"+i).val(yarn_data[5]);
						$("#availableQty_"+i).val(yarn_data[6]);
						
					}
				}
			}
		}
		
		
		function add_break_down_tr( i )
		{ 
			//var row_num=$('#tbl_list_search tbody tr').length;
			var row_num=$('#txt_tot_row').val();
			row_num++;

			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return '' }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			$('#alocatedQty_'+row_num).removeAttr("onChange").attr("onChange","calculate_yarn_qty("+row_num+");");
			//$('#rollNo_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
			
			//$('#increase_'+row_num).removeAttr("value").attr("value","+");
		//	$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			//$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
		//	$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			//set_all_onclick();
			$('#txt_tot_row').val(row_num);
		}
		
		
		
		
		function calculate_yarn_qty(i)
		{
			
			var consumption=$("#colorPercentage_"+i).val()*1;
			var available_qty=$("#availableQty_"+i).val()*1;
			var alocated_qty=$("#alocatedQty_"+i).val()*1;
			var job_balance_qty=($("#hidden_job_balance").val()*1)*consumption;
			//alert(job_balance_qty);
			if(available_qty<alocated_qty) $("#alocatedQty_"+i).val(available_qty);
			
			var alocated_qty=$("#alocatedQty_"+i).val()*1;
			var proceedon=3;
			if(job_balance_qty<alocated_qty)
			{
				var yanr_color=$("#yarnColor_"+i).val();
				proceedon=confirm("Required Qty Exeed. Total Needed  " +job_balance_qty+ " For Yarn Color "+yanr_color+" Are You Continue?.");
				if(proceedon==0) {clear_all_row();return;}
			}
			var breakdown=(alocated_qty/consumption).toFixed(2);
			//alert(breakdown);
			var error=0;
			//alert(breakdown.toFixed(2)); 
			var row_num=$('#txt_tot_row').val()
			for(j=1;j<=row_num; j++)
			{
				if(j!=i && error==0)
				{
					
					var alocated_qty=($("#colorPercentage_"+j).val()*1)*breakdown;
					var available_qty=$("#availableQty_"+j).val()*1;
					if(available_qty>=alocated_qty)
					{
						if(proceedon==3)
						{
							var job_balance_qty=($("#hidden_job_balance").val()*1)*($("#colorPercentage_"+j).val()*1);	
							if(job_balance_qty<alocated_qty)
							{
								var yanr_color=$("#yarnColor_"+j).val();
								proceedon=confirm("Required Qty Exeed. Total Needed  " +job_balance_qty+ " For Yarn Color "+yanr_color+" Are You Continue?.");
								if(proceedon==0) {clear_all_row();return};
							}
						}
						$("#alocatedQty_"+j).val(alocated_qty.toFixed(2));
					}
					else
					{
						$("#alocatedQty_"+j).val(available_qty);
						calculate_yarn_qty(j,available_qty);
						error=1;
						return;
					}
				}
			}
		}
		
		function clear_all_row()
		{
			var row_num=$('#txt_tot_row').val()
			for(j=1;j<=row_num; j++)
			{
				$("#alocatedQty_"+j).val('');
			}
			
		}
		
		function fnc_close()
		{
			var total_data=''; var error=0;
			var total_alocated_qty=0;
			$("#tbl_list_search tbody tr").each(function() {
				
				var color_id		=$(this).find('input[name="yarnColorId[]"]').val();
				var color_name		=$(this).find('input[name="yarnColor[]"]').val();
				var consumption_qty	=$(this).find('input[name="colorPercentage[]"]').val();
				var available_qty	=$(this).find('input[name="availableQty[]"]').val();
				var alocated_qty	=$(this).find('input[name="alocatedQty[]"]').val();
				var prod_id			=$(this).find('input[name="prodId[]"]').val();
				var yarn_description=$(this).find('input[name="yarnDescription[]"]').val();
				var lot_no			=$(this).find('input[name="lotNo[]"]').val();
				
				if(alocated_qty*1==0){ error=1;  return;}
				if(total_data!="") total_data=total_data+"**";
				total_alocated_qty+=alocated_qty*1;
				total_data+=lot_no+"="+color_id+"="+color_name+"="+consumption_qty+"="+available_qty+"="+alocated_qty+"="+prod_id+"="+yarn_description;
                
            });
			
			if(error==0){
				$("#hide_data").val(total_data);
				$("#hide_plies").val(total_alocated_qty);
				parent.emailwindow.hide();
			}
		}
	</script>
    
    </head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
		<fieldset style="width:590px">
			<div style="margin-bottom:5px; display:none; float:left" id="batch_div">
	            <strong>Batch No</strong>&nbsp;&nbsp;
	            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" onDblClick="openmypage_batch()" placeholder="Browse/Write/scan"/>
	        </div>
	    	<div style="margin-bottom:5px;" id="barcode_div">
	            <strong>Batch/Lot No</strong>&nbsp;&nbsp;
	            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_yarn_lot()" placeholder="Browse"/>
	            <input type="hidden" id="hidden_job_no" value="<?php echo $job_no; ?>" />
	            <input type="hidden" id="hidden_size_set_no" value="<?php echo $txt_size_set_no; ?>" />
	            <input type="hidden" id="hidden_job_balance" value="<?php echo ($balance_qty+$ratio_qty); ?>" />
	            
	        </div>
	        <table width="790" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
	            <thead>
	                <th>Batch/Lot No.</th>
	                <th>Available Qty.</th>
	                <th>Allocated Qty.</th>
					<th>Yarn Description</th>
	                <th>Yarn Color</th>
	                <th></th>
	            </thead>
	            <tbody>
	            	<?php 
						if($rollData!="")
						{
							$lotData_arr=explode("**",$rollData);
							
							foreach($lotData_arr as $s_lot)
							{
								$s_lot_data=explode("=",$s_lot);
								$update_product_arr[]=$s_lot_data[6];
							}
							   $data_array=sql_select("select p.id,p.available_qnty from product_details_master p where  p.item_category_id=1 and p.id in (".implode(",",$update_product_arr).")   and p.status_active=1 and p.is_deleted=0  ");
							
							//print_r($data_array);
							
							$i=0;
							foreach($lotData_arr as $single_lot)
							{
								$i++;
								$single_lot_data=explode("=",$single_lot);
								?>
	                                <tr id="tr_<?php echo $i; ?>" class="general">
	                                    <td>
	                                        <input type="text" id="lotNo_<?php echo $i; ?>" name="lotNo[]" class="text_boxes" style="width:110px" value="<?php echo $single_lot_data[0]; ?>" disabled />
	                                         <input type="hidden" id="prodId_<?php echo $i; ?>" name="prodId[]" value="<?php echo $single_lot_data[6]; ?>" /><!--onBlur="roll_duplication_check(1);"-->
	                                    </td>
	                                    <td>
	                                        <input type="text" id="availableQty_<?php echo $i; ?>" name="availableQty[]" class="text_boxes_numeric" value="<?php echo $single_lot_data[4]; ?>" style="width:100px" disabled readonly/>
	                                        <input type="hidden" id="colorPercentage_<?php echo $i; ?>" name="colorPercentage[]" class="text_boxes" value="<?php echo $single_lot_data[3]; ?>" style="width:100px"/>
	                                    </td>
	                                    <td>
	                                        <input type="text" id="alocatedQty_<?php echo $i; ?>" name="alocatedQty[]" class="text_boxes_numeric" value="<?php echo $single_lot_data[5]; ?>" style="width:100px" onChange="calculate_yarn_qty(1)"/>
	                                    </td>
	                                    <td>
	                                        <input type="text" id="yarnDescription_<?php echo $i; ?>" name="yarnDescription[]" class="text_boxes" value="<?php echo $single_lot_data[7]; ?>" style="width:280px" disabled/>
	                                    </td>
	                                   
	                                    <td>
	                                        <input type="text" id="yarnColor_<?php echo $i; ?>" name="yarnColor[]" class="text_boxes_numeric" value="<?php echo $single_lot_data[2]; ?>" style="width:100px" disabled/> 
	                                         <input type="hidden" id="yarnColorId_<?php echo $i; ?>" name="yarnColorId[]" class="text_boxes_numeric" value="<?php echo $single_lot_data[1]; ?>" style="width:100px"/>
	                                    </td>
	                                </tr>
								<?php
							}
						}
						else
						{
							$i++;
						?>
	                		<tr id="tr_1" class="general">
	                    <td>
	                        <input type="text" id="lotNo_1" name="lotNo[]" class="text_boxes" style="width:110px" value="" disabled />
	                         <input type="hidden" id="prodId_1" name="prodId[]" value="" /><!--onBlur="roll_duplication_check(1);"-->
	                    </td>
	                    <td>
	                        <input type="text" id="availableQty_1" name="availableQty[]" class="text_boxes_numeric" value="" style="width:100px" disabled readonly/>
	                        <input type="hidden" id="colorPercentage_1" name="colorPercentage[]" class="text_boxes" value="" style="width:100px"/>
	                    </td>
	                    <td>
	                        <input type="text" id="alocatedQty_1" name="alocatedQty[]" class="text_boxes_numeric" value="" style="width:100px" onChange="calculate_yarn_qty(1)"/>
	                    </td>
	                    <td>
	                        <input type="text" id="yarnDescription_1" name="yarnDescription[]" class="text_boxes" value="" style="width:280px" disabled/>
	                    </td>
	                   
	                    <td>
	                        <input type="text" id="yarnColor_1" name="yarnColor[]" class="text_boxes_numeric" value="" style="width:100px" disabled/> 
	                         <input type="hidden" id="yarnColorId_1" name="yarnColorId[]" class="text_boxes_numeric" value="" style="width:100px"/>
	                    </td>
	                    
	                </tr>
	                	<?php
					
						}
						
						?>
	            </tbody>
	        </table>
	        <div align="center" style="margin-top:10px">
	            <input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
	            <input type="hidden" id="hide_plies" />
	            <input type="hidden" id="hide_data" />
	            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<?php echo $i; ?>">
	        </div>
		</fieldset>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="yarn_receive_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	?> 
	<script>
	
		$(document).ready(function(e) {
        	setFilterGrid('tbl_list_search',-1);
        });
		
		var selected_id = new Array();
		var selected_color_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str,color_id) 
		{
			
			
			if( jQuery.inArray( color_id, selected_color_id ) !== -1 ) {
				alert("Same Color Not Allowed.");
				return;
			}
			if( jQuery.inArray( color_id, selected_color_id ) == -1 ) {
				selected_color_id.push( color_id );
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			var total_selected_yarn=$('#total_selected_color').val()*1;
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				total_selected_yarn++;
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				total_selected_yarn--;
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
			//alert(total_selected_yarn);
			$('#total_selected_color').val( total_selected_yarn );
		}
		
		function fnc_close()
		{
			if($('#hidden_job_color').val()!=$('#total_selected_color').val())
			{
				alert("Please Select All Yarn Color.");
				return;
			}
			//alert($('#hidden_barcode_nos').val());
			parent.emailwindow.hide();
		}
		
    </script>

	</head>

	<body>
	<div align="center" style="width:850px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:840px; margin-left:2px">
	            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820">
	                <thead>
	                    <th width="40">SL</th>
	                    <th width="100">Prod ID</th>
	                    <th width="100">Lot No</th>
	                    <th width="100">Style Stock</th>
	                    <th width="100">Yarn Color</th>
	                    <th width="150">Yarn Description</th>
	                    <th width="100">Alocated Qty.</th>
	                    <th width="">Available Qty.</th>
	                </thead>
	            </table>
	            <div style="width:820px; max-height:270px; overflow-y:scroll" id="list_container" align="left"> 
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" id="tbl_list_search">  
					<? 
                    $i=1;
                    $data_array_strip=sql_select("select a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a. production_color_percentage as sample_per,  a.actual_consumption as measurement, a.cons_per_dzn
                    from ppl_size_set_consumption a, ppl_size_set_mst b 
                    where a.mst_id=b.id and b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and a.color_id=$color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
														
														
					if(count($data_array_strip)==0)
					{
						echo ' <div style="color:red; font-size:25px;  ">Size Set Weight Calculation Entry Not Found. </div>';die;
					}
														
					$strip_color_data_arr=array();
					foreach($data_array_strip as $val)
					{
						$strip_color_data_arr[$val[csf('stripe_color')]]['sample_color']	=$val[csf('sample_color')];
						$strip_color_data_arr[$val[csf('stripe_color')]]['sample_per']		=$val[csf('sample_per')];
						$strip_color_data_arr[$val[csf('stripe_color')]]['measurement']		=$val[csf('measurement')];
						$strip_color_arr[$val[csf('stripe_color')]]							=$val[csf('stripe_color')];
					}
							
	                  
						/*$data_array_sql="select
													sum(x.stock_qty_in-stock_qty_out) as stock_qty, 
													x.color,x.lot, 
													x.id, 
													x.product_name_details, 
													x.current_stock, 
													x.available_qnty, 
													x.allocated_qnty  
										from (
												select
													t.cons_quantity as stock_qty_in,
													0 as stock_qty_out,
													p.color,
													p.lot,
													p.id,
													p.product_name_details,
													p.current_stock,
													p.available_qnty,
													p.allocated_qnty
												 from 
													product_details_master p,
													inv_transaction t,
													inv_receive_master r
												 where 
													p.id=t.prod_id and
													t.mst_id=r.id and
													r.entry_form=248 and  
													t.job_no='".$job_no."' and 
													p.color in (".implode(",",$strip_color_arr).") and 
													t.store_id=$store_id and
													p.item_category_id=1 and 
													t.transaction_type=1 and 
													p.status_active=1 and 
													p.is_deleted=0 and 
													t.status_active=1 and 
													t.is_deleted=0 
											union all
												select
													t.cons_quantity as stock_qty_in,
													0 as stock_qty_out,
													p.color,
													p.lot,
													p.id,
													p.product_name_details,
													p.current_stock,
													p.available_qnty,
													p.allocated_qnty
												 from 
													product_details_master p,
													inv_transaction t,
													inv_item_transfer_mst r
												 where 
													p.id=t.prod_id and
													t.mst_id=r.id and
													t.job_no='".$job_no."' and 
													p.color in (".implode(",",$strip_color_arr).") and 
													t.store_id=$store_id and
													p.item_category_id=1 and 
													r.transfer_criteria=4 and
													t.transaction_type=5 and 
													p.status_active=1 and 
													p.is_deleted=0 and 
													t.status_active=1 and 
													t.is_deleted=0
											union all
												select
													0 as stock_qty_in,
													t.cons_quantity  as stock_qty_out,
													p.color,
													p.lot,
													p.id,
													p.product_name_details,
													p.current_stock,
													p.available_qnty,
													p.allocated_qnty
												 from 
													product_details_master p,
													inv_transaction t,
													inv_item_transfer_mst r
												 where 
													p.id=t.prod_id and
													t.mst_id=r.id and
													t.job_no='".$job_no."' and 
													p.color in (".implode(",",$strip_color_arr).") and 
													t.store_id=$store_id and
													p.item_category_id=1 and 
													r.transfer_criteria=4 and
													t.transaction_type=6 and 
													p.status_active=1 and 
													p.is_deleted=0 and 
													t.status_active=1 and 
													t.is_deleted=0) x
										group by
											x.color,
											x.lot, 
											x.id, 
											x.product_name_details, 
											x.current_stock, 
											x.available_qnty, 
											x.allocated_qnty
										order by 
											x.id";
						
						$data_array=sql_select($data_array_sql);
						$data_array_stock=sql_select("select 
														sum(b.alocated_qty) as alocated_qty,
														b.prod_id  
													from 
														ppl_cut_lay_mst a, 
														ppl_cut_lay_prod_dtls b 
													where 
														a.id=b.mst_id and 
														a.job_no='".$job_no."' 
														and b.color_id in (".implode(",",$strip_color_arr).") 
														and a.store_id=".$store_id." and 
														a.status_active=1 and 
														a.is_deleted=0  and 
														b.status_active=1 and 
														b.is_deleted=0 
													group by b.prod_id");
						foreach($data_array_stock as $pre_val)
						{
							$previous_allocated_arr[$pre_val[csf('prod_id')]]=$pre_val[csf('alocated_qty')];
						}
			
						$current_allocated_arr=array();
						$current_alocated=sql_select("select 
														sum(b.alocated_qty) as alocated_qty,
														b.prod_id  
													from 
														ppl_cut_lay_mst a,
														ppl_cut_lay_prod_dtls b 
													where 
														a.id=$update_id and 
														a.id=b.mst_id and 
														a.job_no='".$job_no."' and 
														b.color_id in (".implode(",",$strip_color_arr).") 
														and a.store_id=".$store_id." and 
														a.status_active=1 and 
														a.is_deleted=0  and 
														b.status_active=1 and 
														b.is_deleted=0 
													group by b.prod_id");
						foreach($current_alocated as $c_val)
						{
							$current_allocated_arr[$c_val[csf('prod_id')]]=$c_val[csf('alocated_qty')];
						}*/ 
						//print_r($current_allocated_arr);die;
						
						
					/*$lot_prev_iss_sql="select a.prod_id, sum(a.cons_quantity) as cons_quantity 
					from inv_transaction a, product_details_master b  
					where a.prod_id=b.id and a.job_no='$job_no' and a.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and b.item_category_id=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis=6 and b.color in (".implode(",",$strip_color_arr).")
					 group by a.prod_id";
					echo $lot_prev_iss_sql."<br>";
					$lot_prev_iss=sql_select($lot_prev_iss_sql);
					$lot_prev_issue_arr=array();
					foreach($lot_prev_iss as $row)
					{
						$lot_prev_issue_arr[$row[csf("prod_id")]]=$row[csf("cons_quantity")];
					}
					unset($lot_prev_iss);*/
					
					$lot_ratio_sql="select a.id as lot_id, b.prod_id, b.alocated_qty as alocated_qty  
					from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b
					where a.status_active=1 and b.status_active=1 and a.id=b.mst_id and a.entry_form=253 and a.job_no='$job_no' and a.store_id=$store_id and b.color_id in (".implode(",",$strip_color_arr).")";
					//echo $lot_ratio_sql."<br>";
					$lot_ratio_sql_result=sql_select($lot_ratio_sql);
					$lot_ratio_arr=array();
					foreach($lot_ratio_sql_result as $row)
					{
						if(str_replace("'","",$update_id)==$row[csf("lot_id")])
						{
							$current_allocated_arr[$row[csf("prod_id")]]+=$row[csf("alocated_qty")];
						}
						$lot_ratio_arr[$row[csf("prod_id")]]+=$row[csf("alocated_qty")];
					}
					unset($lot_ratio_sql_result);
					//and t.receive_basis<>6 and t.receive_basis<>6
					$sql_item="select p.id, p.color, p.lot, p.product_name_details, p.current_stock, p.available_qnty, p.allocated_qnty,
					sum(case when t.transaction_type=1 and t.entry_form=248 then t.cons_quantity else 0 end) as rcv_qnty,
					sum(case when t.transaction_type=2 and t.entry_form=277 then t.cons_quantity else 0 end) as issue_qnty,
					sum(case when t.transaction_type=2 and t.entry_form=277 and t.receive_basis=6 then t.cons_quantity else 0 end) as lot_issue_qnty,
					sum(case when t.transaction_type=3 and t.entry_form=381 then t.cons_quantity else 0 end) as rcv_rtn_qnty,
					sum(case when t.transaction_type=4 and t.entry_form=382  then t.cons_quantity else 0 end) as issue_rtn_qnty,
					sum(case when t.transaction_type=5 and t.entry_form=249 then t.cons_quantity else 0 end) as trans_in_qnty,
					sum(case when t.transaction_type=6 and t.entry_form=249 then t.cons_quantity else 0 end) as trans_out_qnty
					from product_details_master p, inv_transaction t 
					where p.id=t.prod_id and p.status_active=1 and p.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and p.item_category_id=1 and t.item_category=1 and t.entry_form in(248,249,277,381,382) and t.store_id=$store_id and t.job_no='$job_no' and p.color in (".implode(",",$strip_color_arr).")
					group by p.id, p.color, p.lot, p.product_name_details, p.current_stock, p.available_qnty, p.allocated_qnty";
					//echo $sql_item;//die;
					$sql_result=sql_select($sql_item);
					$dtls_data=array();
					foreach($sql_result as $row)
					{
						$lot_prev_issue_arr[$row[csf("id")]]+=$row[csf("lot_issue_qnty")];
						$lot_prev_issue_rtn_arr[$row[csf("id")]]+=$row[csf("issue_rtn_qnty")];
						
						$dtls_data[$row[csf("id")]]["id"]=$row[csf("id")];
						$dtls_data[$row[csf("id")]]["color"]=$row[csf("color")];
						$dtls_data[$row[csf("id")]]["lot"]=$row[csf("lot")];
						$dtls_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
						$dtls_data[$row[csf("id")]]["current_stock"]=$row[csf("current_stock")];
						$dtls_data[$row[csf("id")]]["available_qnty"]=$row[csf("available_qnty")];
						$dtls_data[$row[csf("id")]]["allocated_qnty"]=$row[csf("allocated_qnty")];
						$dtls_data[$row[csf("id")]]["stock_qty"]=($row[csf("rcv_qnty")]+$row[csf("issue_rtn_qnty")]+$row[csf("trans_in_qnty")])-($row[csf("issue_qnty")]+$row[csf("rcv_rtn_qnty")]+$row[csf("trans_out_qnty")]);
					}
					
					foreach($dtls_data as $prod_id=>$row)
					{ 

						$alocated_qty=$lot_ratio_arr[$row[('id')]]-$current_allocated_arr[$row[('id')]];
						$cu_lot_qnty=$lot_ratio_arr[$row[('id')]]-$lot_prev_issue_arr[$row[('id')]];
						$available_qty=$row[('stock_qty')]-$cu_lot_qnty;
						$item_abailable_qnty=$row[('available_qnty')];
						$mesurement=(($strip_color_data_arr[$row[('color')]]['measurement']*2.2046226)/12); 
						//echo $alocated_qty;die;
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>,<? echo $row[('color')]; ?>)"> 
							<td width="40">
								<? echo $i; ?>
								 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[('id')]."_".$row[('color')]."_".$color_library[$row[('color')]]."_".$row[('lot')]."_".$row[('product_name_details')]."_".$mesurement."_".$available_qty; ?>"/>
							</td>
							<td width="100"><p><? echo $row[('id')]; ?>&nbsp;</p></td>
							<td width="100" style="word-break:break-all"><p><? echo $row[('lot')]; ?></p></td>
							<td width="100" align="right"><? echo number_format($row[('stock_qty')],2); ?></td>
							<td width="100" style="word-break:break-all"><? echo $color_library[$row[('color')]]; ?></td>
							<td width="150" style="word-break:break-all"><? echo $row[('product_name_details')]; ?></td>
							<td width="100" align="right"><? echo number_format($cu_lot_qnty,2); ?></td>
							<td align="right"><? echo number_format($item_abailable_qnty,2); ?></td>
						</tr>
						<? 
						$i++;
					} 
					?>
				</table>
			</div>
			<table width="820">
				<tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos"> 
						<input type="hidden" name="hidden_job_color" id="hidden_job_color"  value="<?php echo count($strip_color_arr); ?>"> 
						<input type="hidden" name="total_selected_color" id="total_selected_color"  value="0">
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

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];

	if($company_id==0) { echo "Please Select Company First."; die; }

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and d.po_number like '$search_string'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_num')]]=$row[csf('barcode_num')];
	}
	
	$sql="SELECT a.recv_number, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and d.is_confirmed=1 $search_field_cond";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="120">System Id</th>
            <th width="110">Job No</th>
            <th width="110">Order No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:740px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();
}

$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');

// needed
if($action=="size_popup")
{
  	echo load_html_head_contents("Cut and bundle details","../../../", 1, 1, '','1','');
	extract($_REQUEST);
   	$country_is_blank_sql=sql_select("SELECT country_id FROM wo_po_color_size_breakdown where status_active=1 AND is_deleted=0 AND (country_id is null or country_id ='' or country_id=0) AND job_no='".$job_id."'"); 
   	// echo $details_id;
	?>
	<script>
		var permission='<? echo $permission; ?>';
		var without_country='<? echo count($country_is_blank_sql); ?>';
		var gmt_id=<? echo $cbo_gmt_id; ?>;
		var color_id=<? echo $cbo_color_id; ?>;
		var mst_id=<? echo $mst_id; ?>;
		var dtls_id=<? echo $details_id; ?>;
		var cbo_company_id=<? echo $cbo_company_id; ?>;
		var color_type_id=<? echo $cbo_color_type; ?>;
		
		function fnc_cut_lay_size_info( operation )
		{ 
			freeze_window(operation);
			if(operation==2)
		  	{
				alert("Delete Restricted.");
				release_freezing();
				return;
			}
			  
			if(form_validation('txt_bundle_pcs','Pcs Per Bundle')==false)
			{
				release_freezing();
				return;
			}
			var txt_positive_count=$("#txt_positive_count").val()*1;
			
			if((($("#total_sizef_qty").val()*1)!=($("#total_size_qty").text()*1)) && txt_positive_count>0 )
			{
				alert("Total Size Qty. and Total Distributed Qty. Should be same.");
				release_freezing();
				return;
			}
			//var roll_data=$("#roll_data").val();	
			
			var size_row_num=$('#tbl_size tbody tr:first td').length-2;
			
			var data1="&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&cbo_company_id="+cbo_company_id+"&gmt_id="+gmt_id+"&color_type_id="+color_type_id;
			//alert(row_num);return;
			//var data1="action=save_update_delete_size&operation="+operation+"&size_row_num="+size_row_num+"&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&bundle_per_pcs="+bundle_per_pcs+"&to_marker_qty="+to_marker_qty+"&cbo_company_id="+cbo_company_id+"&job_id="+job_id+"&cut_no="+cut_no+"&gmt_id="+gmt_id+"&txt_plies="+txt_plies+"&color_type_id="+color_type_id;
			var data2=''; var size_data=''; var max_seq=0; var size_arr=[]; var roll_data='';
		
			//var size_row_num=$('#tbl_size tbody tr').length;
			var z=1;
			for(var i=1; i<=size_row_num; i++)
			{
				size_data+="&txt_layf_balance_" + z + "='" + $('#txt_layf_balance_'+i).val()+"'"+"&txt_size_ratio_" + z + "='" + $('#txt_size_ratio_'+i).val()+"'"+"&txt_sizef_qty_" + z + "='" + $('#txt_sizef_qty_'+i).val()+"'"+"&hidden_sizef_id_" + z + "='" + $('#hidden_sizef_id_'+i).val()+"'"+"&txt_size_yarn_wgt_" + z + "='" + $('#txt_size_yarn_wgt_'+i).val()+"'";
				
				z++;
				//size_data+=get_submitted_data_string('txt_layf_balance_'+k+'*txt_size_ratio_'+k+'*txt_sizef_qty_'+k+'*hidden_sizef_id_'+k+'*txt_size_yarn_wgt_'+k,"../../../",k);
				var size_id=$("#hidden_sizef_id_"+i).val();	
				size_arr.push(size_id);
			}
			
			var country_row_num=$("#tbl_size_details tbody tr").length;
			//size_data=size_data+"&country_row_num="+country_row_num;	
			var z=1;
			for(var k=1; k<=country_row_num; k++)
			{
				data2+="&cboCountryType_" + z + "='" + $('#cboCountryType_'+k).val()+"'"+"&cboCountry_" + z + "='" + $('#cboCountry_'+k).val()+"'"+"&txt_lay_balance_" + z + "='" + $('#txt_lay_balance_'+k).val()+"'"+"&txt_excess_" + z + "='" + $('#txt_excess_'+k).val()+"'"+"&txt_size_qty_" + z + "='" + $('#txt_size_qty_'+k).val()+"'"+"&hidden_size_id_" + z + "='" + $('#hidden_size_id_'+k).val()+"'"+"&poId_" + z + "='" + $('#poId_'+k).val()+"'";
				z++;
			//data2+=get_submitted_data_string('cboCountryType_'+k+'*cboCountry_'+k+'*txt_lay_balance_'+k+'*txt_excess_'+k+'*txt_size_qty_'+k+'*hidden_size_id_'+k+'*poId_'+k,"../../../",2);
			}
			//alert(data2); release_freezing(); return;
			/*var bundle_per_pcs=$("#txt_bundle_pcs").val();
			var to_marker_qty=$("#total_sizef_qty").val()*1;
			var job_id=$("#hidden_update_job_id").val();
			var cut_no=$("#hidden_update_cut_no").val();
			var txt_plies=$("#totalLotQty").val();*/
			
			var data="action=save_update_delete_size&operation="+operation+"&size_row_num="+size_row_num+"&country_row_num="+country_row_num+get_submitted_data_string('txt_bundle_pcs*total_sizef_qty*hidden_update_job_id*hidden_update_cut_no*totalLotQty',"../../../")+data1+data2+size_data;
			
		//var data=data1+data2+size_data;
			//alert(data); release_freezing(); return;
			
			http.open("POST","yarn_lot_ratio_planning_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cut_lay_size_info_reponse;
		}
		
		function fnc_cut_lay_size_info_reponse()
		{
			if(http.readyState == 4) 
			{
				//release_freezing(); return;
				//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
				if(reponse[0]==0 || reponse[0]==1)
				 {
					 if(reponse[0]==0)
					 {
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 }
					 else if(reponse[0]==1)
					 {
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 }
					
					show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+reponse[7],'show_bundle_list_view','search_div','yarn_lot_ratio_planning_controller','setFilterGrid("list_view",-1)');
					var update_size_id=reponse[3].split('_');
					$("#hidden_plant_qty").val(reponse[4]);
					$("#hidden_total_marker").val(reponse[5]);
					$("#hidden_lay_balance").val(reponse[6]);
					
					if(reponse[7]==1)
					{
						var update_data=reponse[3].split(',');
						var dtlsId_array = new Array();
						for(var k=0; k<update_data.length; k++)
						{
							var datas=update_data[k].split("__");
							var index=datas[1];
							dtlsId_array[index] = datas[0]+"**"+datas[2];
						}
						
						var row_num=$('#tbl_size tbody tr').length;
						for(var i=1;i<=row_num;i++)
						{
							var index=	$("#hidden_size_id_"+i).val();
							var dtls_id=''; var sequence_no='';
							if(dtlsId_array[index])
							{
								var datas=dtlsId_array[index].split("**");
								dtls_id=datas[0];
								sequence_no=datas[1];
							}
							$('#update_size_id_'+i).val(dtls_id);	
							$('#txt_bundle_'+i).val(sequence_no);	
						}
					}
					else
					{
						for(var i=1;i<=update_size_id.length;i++)
						{
							$('#update_size_id_'+i).val(update_size_id[i-1]);	
						}
					}
					set_button_status(1, permission, 'fnc_cut_lay_size_info',1,1);
				}
				
				else if(reponse[0]==15)
				{
					alert("No Data Found");
				}
				else if(reponse[0]==200)
				{
					alert("Update Restricted. This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
				}
				else if(reponse[0]==201)
				{
					alert("Save Restricted. This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
				}
				release_freezing();
			}
		} 
		function fnc_print_bundle()
		{
		   var report_title="Lot Ratio Bundle ";
		   var country=$('#cboCountryBundle').val();
		   print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country, "cut_lay_bundle_print", "yarn_lot_ratio_planning_controller")
			
		}
		
		function fnc_print_bundle_sticker()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			
			data=data+"***"+job_id+'***'+mst_id+'***'+dtls_id+'***'+gmt_id+'***'+color_id;
				data=data;
				//window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();

			http.open( 'POST', 'yarn_lot_ratio_planning_controller.php?action=print_barcode_operation&data='+ data );
			http.onreadystatechange = response_pdf_data;
			http.send(null);
		}
		
		function fnc_print_bundle_sticker_new()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			
			data=data+"***"+job_id+'***'+mst_id+'***'+dtls_id+'***'+gmt_id+'***'+color_id;
				data=data;
				//window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();

			http.open( 'POST', 'yarn_lot_ratio_planning_controller.php?action=print_barcode_operation_bundle_sticker&data='+ data );
			http.onreadystatechange = response_pdf_data;
			http.send(null);
		}
		
		function fnc_print_bundle_sticker_new2()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			
			data=data+"***"+job_id+'***'+mst_id+'***'+dtls_id+'***'+gmt_id+'***'+color_id;
				data=data;
				//window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();

			http.open( 'POST', 'yarn_lot_ratio_planning_controller.php?action=print_barcode_operation_bundle_sticker2&data='+ data );
			http.onreadystatechange = response_pdf_data;
			http.send(null);
		}

		function fnc_print_bundle_sticker_new_()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			
			data=data+"***"+job_id+'***'+mst_id+'***'+dtls_id+'***'+gmt_id+'***'+color_id;
				data=data;
				//window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();

			http.open( 'POST', 'yarn_lot_ratio_planning_controller.php?action=print_barcode_operation_bundle_sticker&data='+ data );
			http.onreadystatechange = response_pdf_data;
			http.send(null);
		}
		
		function check_all_report()
		{
			$("input[name=chk_bundle]").each(function(index, element) { 
					
					if( $('#check_all').prop('checked')==true) 
			 			$(this).attr('checked','true');
					else
						$(this).removeAttr('checked');
			});
		}
		

		function check_same_all_size(size)
		{
			// alert(size);
			$('.'+size).each(function(index, element) 
			{ 					
				if( $('#check_same_size_'+size).prop('checked')==true) 
		 			$(this).attr('checked','true');
				else
					$(this).removeAttr('checked');
			});
		}
		
		function fnc_bundle_report_first()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>;
			
			//var title = 'Search Job No';	
			//var page_link = 'cut_and_lay_ratio_wise_entry_controller_urmi.php?data='+data+'&action=print_report_bundle_barcode_eight';
			//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			//emailwindow.onclose=function()
			// {
				//var theform=this.contentDoc.forms[0]; 
			//	var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data;
				//var url=return_ajax_request_value(data, "print_barcode_first", "yarn_lot_ratio_planning_controller");
				//window.open(url,"##");
				window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_first', true );	
		
			// }	
		}



		function fnc_yarn_lot_ratio_report()
		{
			var report_title="Cut and Lay bundle ";
		   	var data=<?php echo $cbo_company_id; ?>;
		   	var title = 'Bundle List';	
				
			print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+'<? echo $size_set_no; ?>'+'*'+title, "report_yarn_lot_ratio", "yarn_lot_ratio_planning_controller")
		}

		function fnc_send_printer_text()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>;
			var url=return_ajax_request_value(data, "report_bundle_text_file", "yarn_lot_ratio_planning_controller");
		
		    window.open(url+".zip","##");
		}

		function fnc_bundle_report_fivePointeight()
		{
			var data="";
			var error=1;
			$("input[name=chk_bundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			//var order_id='<? echo $order_id; ?>';
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>;
				data=data;
				//window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();

			http.open( 'POST', 'yarn_lot_ratio_planning_controller.php?action=print_barcode_fivePointEight&data='+ data );
			http.onreadystatechange = response_pdf_data;
			http.send(null);	
		}

		function response_pdf_data() 
		{
			if(http.readyState == 4) 
			{
				//alert(response[1]);
				var response = http.responseText.split('###');
				window.open(''+response[1], '', '');
			}
		}
		
		function calculate_perc(i)
		{
			var bl_qty=$('#txt_lay_balance_'+i).val()*1;
			var size_qty=$('#txt_size_qty_'+i).val()*1;
			var excess_qty=size_qty-bl_qty;
			if(excess_qty>0)
			{
				if(bl_qty==0)
				{
					$('#txt_excess_'+i).val(0);
				}
				else
				{
					var excess_perc=(excess_qty/bl_qty)*100;
					$('#txt_excess_'+i).val(excess_perc.toFixed(2));
				}
			}
			else
			{
				$('#txt_excess_'+i).val('');
			}
		}
		
		function check_size_qty(i)
		{
			var curr_size_qty=$("#txt_size_qty_"+i).val()*1;
			var curr_size_id=$("#hidden_size_id_"+i).val();
			var txt_positive_count=$("#txt_positive_count").val()*1;
			var tot_sizeQty='';
			
			var row_num=$("#tbl_size_details tbody tr").length;
			for(var j=1; j<=row_num; j++)
			{
				var size_id=$("#hidden_size_id_"+j).val();
				var size_qty=$("#txt_size_qty_"+j).val();
				if(size_id == curr_size_id)
				{
					tot_sizeQty=tot_sizeQty*1+size_qty*1;
				}
			} 
			
			var row_num=$("#tbl_size tbody tr:first td").length-2;
			var sizef_qty=0;
			for(var j=1; j<=row_num; j++)
			{ 
				var size_id=$("#hidden_sizef_id_"+j).val();
				if(size_id == curr_size_id)
				{
					sizef_qty=$("#txt_sizef_qty_"+j).val();
				}
			}
			if(tot_sizeQty>sizef_qty && txt_positive_count>0)
			{
				alert("Marker Qty Exceeds Distributed Qty.");
				$("#txt_size_qty_"+i).val('');
				$("#txt_excess_"+i).val('');
			}
			total_size_qty();
		}
		
		function total_size_qty()
		{ 
			var row_num=$("#tbl_size_details tbody tr").length;
			var tot_qty=0;
			for(var i=1; i<=row_num; i++)
			{
				tot_qty+=($("#txt_size_qty_"+i).val()!='')?$("#txt_size_qty_"+i).val()*1:0; 
			}
			$('#total_size_qty').text(tot_qty);
		}
		
		function size_popup_close(id,marker,plan,tomarker,lay_balance)
		{
			//var pass_string=id+"**"+marker+"**"+plan+"**"+tomarker+"**"+lay_balance;
			//alert(pass_string);
			//document.getElementById('hidden_marker_no_x').value=pass_string;
		  	parent.emailwindow.hide();
		}
		
		function fnc_updateRow(id_row)
		{
			$("#bundleSizeQty_"+id_row).attr("disabled",false);
			//$("#sizeName_"+id_row).attr("disabled",false);
			//$("#cboCountryB_"+id_row).removeAttr("disabled","disabled");
			$("#hiddenUpdateFlag_"+id_row).val(6);
		}
		
		function bundle_calclution(rowNo)
		{
			var countryId=$("#hiddenCountryB_"+rowNo).val();
			var sizeId=$("#hiddenSizeId_"+rowNo).val();
			var rollId=$("#rollId_"+rowNo).val();
			
			var min_rmg_no=1; 
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
				
				var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				//var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val()); && rollId==rollIdC
				
				if(countryId==countryIdC && sizeId==sizeIdC)
				{
					if(qty*1>0)
					{
						var from=min_rmg_no;
						var to=min_rmg_no*1+qty*1-1;
						min_rmg_no+=qty*1;
						$(this).find('input[name="rmgNoStart[]"]').val(from);
						$(this).find('input[name="rmgNoEnd[]"]').val(to);
					}
					else
					{
						$(this).find('input[name="rmgNoStart[]"]').val('');
						$(this).find('input[name="rmgNoEnd[]"]').val('');
					}
				}
			});
		}
		
		function fnc_addRow(actual_id,i)
		{ 
			var row_num=$('#trBundleListSave tr').length;
			row_num++;
			var clone= $("#trBundleListSave_"+actual_id).clone();
			clone.attr({
				id: "trBundleListSave_"+ row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#trBundleListSave_"+i).after(clone);
			$('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+row_num+");");
			$('#bundleSizeQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","bundle_calclution("+row_num+");");
		
			//=================================================================================================================
			$('#addButton_'+row_num).removeAttr("onclick").attr("onclick","delete_bundle_row("+actual_id+","+row_num+");");
			$("#addButton_"+row_num).val('-');
			//===================================================================================================================
			$("#hiddenExtraTr_"+actual_id).val($("#hiddenExtraTr_"+actual_id).val()+"**"+row_num);
			$("#bundleSizeQty_"+actual_id).attr("disabled",false);
			$("#bundleSizeQty_"+row_num).attr("disabled",false);
			$("#bundleNo_"+row_num).attr("disabled",false);
			$("#bundleSizeQty_"+row_num).val('');
			$("#serialNo_"+row_num).html('');
			$("#bundleNo_"+row_num).val($("#bundleNo_"+actual_id).val()+"-");
			$("#rmgNoStart_"+row_num).val('');
			$("#rmgNoEnd_"+row_num).val('');
			$("#hiddenUpdateValue_"+row_num).val('');
			$("#hiddenUpdateFlag_"+actual_id).val(6);
			$("#hiddenUpdateFlag_"+row_num).val(6);
			serial_rearrange();
		}
		
		function serial_rearrange()
		{
			var k=1; 
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				$(this).find('input[name="sirialNo[]"]').val(k);
				//alert(k)
				k++;
			
			});
		}
		
		function fnc_cut_lay_bundle_info(operation) 
		{
			freeze_window(operation);
			var cbo_color_type=<? echo $cbo_color_type; ?>;
			var gmt_id=<? echo $cbo_gmt_id; ?>;
			if(operation==2)
			{
				show_msg('13');
				release_freezing();	
				return;
			}
			var dataString_bundle="";  
			var j=0; var z=0; var tot_row=0; var sl=0; var error=0;
			var bundle_check_arr=new Array();
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('-');
				var bundle_no_split_length=bundle_break.length;
				if(bundle_no_split_length>3)
				{
					var check_bundle_prifix=bundle_no_split_length-1;
					 if(bundle_break[check_bundle_prifix]=="")
					 {
					  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
					  error=1;
					 }
				}
				
				
				if( jQuery.inArray( $(this).find('input[name="bundleNo[]"]').val(), bundle_check_arr )>-1) 
				{ 
					alert('Duplicate Bundle. Bundle No '+$(this).find('input[name="bundleNo[]"]').val()); 
					error=1;
					release_freezing();	
					return; 
				}
				
				
				bundle_check_arr.push($(this).find('input[name="bundleNo[]"]').val());
				sl++;
			});
			if(error==1) { return;}

			var count_deleted=0;
			
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				var bundle_no=$(this).find('input[name="bundleNo[]"]').val();
				var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
				var bundle_from=$(this).find('input[name="rmgNoStart[]"]').val();
				var bundle_to=$(this).find('input[name="rmgNoEnd[]"]').val();
				var bundle_size_id=$(this).find('select[name="sizeName[]"]').val();
				var hidden_size_id=$(this).find('input[name="hiddenSizeId[]"]').val();
				var hidden_size_qty=$(this).find('input[name="hiddenSizeQty[]"]').val();
				var hidden_update_flag=$(this).find('input[name="hiddenUpdateFlag[]"]').val();
				var hiddenUpdateValue=$(this).find('input[name="hiddenUpdateValue[]"]').val();
				var isExcess=$(this).find('input[name="isExcess[]"]').val();
				
				var hiddenCountryType=$(this).find('input[name="hiddenCountryTypeB[]"]').val();
				var cboCountry=$(this).find('select[name="cboCountryB[]"]').val();
				var po_id=$(this).find('select[name="cboPoId[]"]').val();
				var hiddenCountry=$(this).find('input[name="hiddenCountryB[]"]').val();
				var chk_status=$(this).find('input[name="chk_status[]"]').val();
				if(chk_status*1==0)
				{
					count_deleted++;
				}
				
				j++;
				tot_row++;
				dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue+'&hiddenCountryType_' + j + '=' + hiddenCountryType+'&hiddenCountry_' + j + '=' + hiddenCountry+'&cboCountry_' + j + '=' + cboCountry +'&isExcess_' + j + '=' + isExcess +'&cboPoId_' + j + '=' + po_id +'&status_active_' + j + '=' + chk_status;
			});
			var txt_message="";
			if(count_deleted>0)
			{
				var page_link='yarn_lot_ratio_planning_controller.php?action=deleted_message_popup'; 
				var title="Deleted Message Popup";
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=100px,center=1,resize=0,scrolling=0','../')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0]; 
					txt_message=this.contentDoc.getElementById("txt_message").value; // message 


					var bundle_mst_id=$("#hidden_mst_id").val();

					var bundle_dtls_id=$("#hidden_detls_id").val();
					//alert(bundle_dtls_id);return;
					var hidden_cutting_no=$("#hidden_cutting_no").val();
					var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no+'&color_type_id='+cbo_color_type+"&gmt_id="+gmt_id+"&txt_message="+txt_message;
					//alert(data);return;hidden_cutting_no
					
					http.open("POST","yarn_lot_ratio_planning_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange =fnc_cut_lay_bundle_reply_info;
				}
			}
			else 
			{
				var bundle_mst_id=$("#hidden_mst_id").val();
				var bundle_dtls_id=$("#hidden_detls_id").val();
				//alert(bundle_dtls_id);return;
				var hidden_cutting_no=$("#hidden_cutting_no").val();
				var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no+'&color_type_id='+cbo_color_type+"&gmt_id="+gmt_id+"&txt_message="+txt_message;
				//alert(data);return;hidden_cutting_no
				
				http.open("POST","yarn_lot_ratio_planning_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange =fnc_cut_lay_bundle_reply_info;
			}
			
	
			
		}
		
		function fnc_cut_lay_bundle_reply_info()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(trim(reponse[0]));
				
				if((reponse[0]==0 || reponse[0]==1))
				{
					$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
					{
						$('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					});
					set_button_status(1, permission, 'fnc_cut_lay_bundle_info',2);	
					show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_ratio_wise_entry_controller_urmi','setFilterGrid("list_view",-1)');
				}
				else if(reponse[0]==200)
				{
					alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
				}
				release_freezing();	
			}
		}
		
		function delete_bundle_row(actual_id,rowNo) 
		{ 
			var total_add_id=$("#hiddenExtraTr_"+actual_id).val();
			var countryId=$("#hiddenCountryB_"+rowNo).val();
			var sizeId=$("#hiddenSizeId_"+rowNo).val();
			var rollId=$("#rollId_"+rowNo).val();
			// alert(total_add_id);
			var id_arr=total_add_id.split("**")
			
			id_arr.splice(id_arr.indexOf(rowNo), 1);
			// alert(id_arr.length)
			if( id_arr.length==1)  $('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+actual_id+");");
			var new_id=id_arr.join("**");
			$("#hiddenExtraTr_"+actual_id).val(new_id);
			//alert( $("#hiddenExtraTr_"+actual_id).val())
			$("#trBundleListSave_"+rowNo).remove();
			bundle_calclution_on_dlt(countryId,sizeId,rollId);
			serial_rearrange();
		}
		
		function bundle_calclution_on_dlt(countryId,sizeId,rollId)
		{
			var min_rmg_no=1; 
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
				
				var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val());
				
				if(countryId==countryIdC && sizeId==sizeIdC && rollId==rollIdC)
				{
					if(qty*1>0)
					{
						var from=min_rmg_no;
						var to=min_rmg_no*1+qty*1-1;
						min_rmg_no+=qty*1;
						$(this).find('input[name="rmgNoStart[]"]').val(from);
						$(this).find('input[name="rmgNoEnd[]"]').val(to);
					}
					else
					{
						$(this).find('input[name="rmgNoStart[]"]').val('');
						$(this).find('input[name="rmgNoEnd[]"]').val('');
					}
				}
			});
		}


		function check_all_hold_fnc()
		{
			$("input[name=chk_status]").each(function(index, element) { 
					
					if( $('#check_all_hold').prop('checked')==true) 
					{
						$(this).val(2);
			 			$(this).attr('checked','true');
					}
					else
					{
						$(this).val(1);
						$(this).removeAttr('checked');
					}
			});
		}
		function check_status_value(id)
		{
			
			if( $('#chk_status_'+id).prop('checked')==true) 
			{
				if($('#bundle_next_process_issue_'+id).val())
				{
					alert('Can not hold Issue No: '+ $('#bundle_next_process_issue_'+id).val()+ ' found');
					$('#chk_status_'+id).val(0);
					$('#chk_status_'+id).removeAttr('checked');
				}
				else if($('#bundle_next_process_'+id).val())
				{
					alert('Can not hold CUTTING QC NO: '+ $('#bundle_next_process_'+id).val()+ ' found');
					$('#chk_status_'+id).val(0);
					$('#chk_status_'+id).removeAttr('checked');
				}
				else{
					$('#chk_status_'+id).val(1);
				}
				
			}
			else
			{
				$('#chk_status_'+id).val(0);
			}
		}
	</script>

	</head>
	<body onLoad="set_hotkey()">
	<div id="msg_box_popp" style=" height:15px; width:200px;  position:relative; left:250px "></div>
		<div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">
			<input type="hidden" id="hidden_cutting_no" name="hidden_cutting_no" value="<?=$cutting_no; ?>" />
			<div style="display:none;"><?=load_freeze_divs ("../../../",$permission); ?> </div><?
				$color_name=return_field_value("color_name","lib_color","id='".$cbo_color_id."'"); 
				$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
				$pcs_per_bundle=return_field_value("pcs_per_bundle","ppl_cut_lay_dtls","id=$details_id ","pcs_per_bundle"); 
				$size_set_mstid=return_field_value("id","ppl_size_set_mst","sizeset_no='$size_set_no' ","id"); 
				// echo $size_set_no;
				//$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
				
				$color_size_result=sql_select("select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$cbo_color_id and status_active=1 and is_deleted=0 order by id");
				//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$cbo_color_id and status_active=1 and is_deleted=0 order by id";
				$sizeWiseProdQtyArr=array();
				foreach($color_size_result as $row)
				{
					if($row[csf('gmt_size_id')]!=0)	
					{
						$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
					}
				}
				unset($color_size_result);
				
				$data_array_strip=sql_select("select id, yarn_color_id, production_color_percentage, process_loss from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$cbo_color_id and status_active=1 and is_deleted=0 order by id"); 
				$yarnColorArr=array();
				 foreach ($data_array_strip as $row)
				 {
					 $yarnColorArr[$row[csf('yarn_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
					 $yarnColorArr[$row[csf('yarn_color_id')]]['process_loss']=$row[csf('process_loss')];
				 }
				 unset($data_array_strip);
				 //print_r($sizeWiseProdQtyArr);
				 $sizeSummArr=array();
				 foreach($yarnColorArr as $ycolor=>$ycolorVal)
				 {
					foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
					{
						$colorSizeQty=0;
						$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
						//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
						$sizeSummArr[$gmt_size_id]+=$colorSizeQty;
					}
				 }
			?>
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <fieldset style="width:600px;">
	                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550" id="tbl_bundle_size">
	                	<thead>
	                        <tr>
	                            <th colspan="2" class="must_entry_caption"><strong>Pcs Per Bundle</strong></th>
	                            <th rowspan="<?=count($yarn_ratio_form_msert); ?>"><input name="txt_bundle_pcs" id="txt_bundle_pcs" class="text_boxes_numeric" style="width:120px;" value="<?=$pcs_per_bundle; ?>"/>	
	                            </th>
	                        </tr>
	                    </thead>
	                	<thead>
	                        <tr>
	                            <th><strong>Yarn Color</strong></th>
	                            <th><strong>Batch/Lot</strong></th>
	                            <th><strong>Allocated/ Ratio Qty</strong>
	                                <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id" value="<?=$job_id; ?>"/>
	                                <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no" value="<?=$cutting_no; ?>"/>
	                           </th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?php 
						//echo $rollData;
							$yarn_color_sl=0;
							$yarn_ratio_form_msert=explode("**",$rollData);
							foreach($yarn_ratio_form_msert as $single_yarn_data)
							{
								$yarn_color_sl++;
								$single_yarn_data_arr=explode("=",$single_yarn_data);
								//echo $single_yarn_data;
								$total_lot_qty+=$single_yarn_data_arr[5];
								$total_consumption+=$single_yarn_data_arr[3];
							?>
	                            <tr>
	                                <td width="280"><input type="text" style="width:260px" class="text_boxes"  name="txtColorName[]" id="txtColorName_<?=$yarn_color_sl; ?>" value="<?=$single_yarn_data_arr[2]; ?>" disabled readonly/></td>
	                                <td width="140"><input type="text" style="width:125px" class="text_boxes" name="txtLotName[]" id="txtLotName_<?=$yarn_color_sl; ?>" value="<?=$single_yarn_data_arr[0]; ?>" disabled readonly/></td>
	                                <td width="120">
	                                    <input type="text" style="width:120px" class="text_boxes_numeric" name="txtLotQty[]" id="txtLotQty_<?=$yarn_color_sl; ?>" value="<?=$single_yarn_data_arr[5]; ?>" disabled readonly/>
	                                    <input type="hidden" style="width:110px" class="text_boxes" name="txtLotRatio[]" id="txtLotRatio_<?=$yarn_color_sl; ?>" value="<?=$single_yarn_data_arr[3]; ?>" disabled readonly/>
	                                </td>
	                            </tr>
							<?php	
							}
							$total_consumption_per_pcs=number_format($total_consumption,4);
							?>
	                    </tbody>
	                    <tfoot>
	                        <tr class="form_table_header">
	                            <th  colspan="2" align="right">Total
	                              	<input type="hidden" name="totalLotQty" id="totalLotQty" class="text_boxes_numeric" style="width:120px;" value="<?=$total_lot_qty; ?>"/>
	                            	<input name="consumptionPerPcs" id="consumptionPerPcs" class="text_boxes_numeric" style="width:120px;" type="hidden" value="<?=$total_consumption_per_pcs; ?>"/>
	                            </th>
	                            <th align="right"><?=number_format($total_lot_qty,4); ?></th>
	                        </tr>
	                    </tfoot>
	                </table>
	            </fieldset>
	            
	            <fieldset style="width:935px;">
	            <?
					$po_no_arr=return_library_array("select id, po_number from wo_po_break_down where job_no_mst='".$job_id."' and is_confirmed=1",'id','po_number');
					$po_country_array=array(); $size_order_arr=array(); $poArr=array();
					//echo $sizeid;
					
					if($sizeid!="") 
					{
						$sizeidCond=" and size_number_id in ($sizeid)";
						$cut_lay_SizeCond=" and size_id in ($sizeid)";
					}
					else 
					{
						$sizeidCond=""; $cut_lay_SizeCond="";
					}
					
	                $sql_query=sql_select("select po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order 
										from wo_po_color_size_breakdown 
										where item_number_id=$cbo_gmt_id and job_no_mst='".$job_id."' and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 $sizeidCond order by size_order, country_ship_date, country_type");
		
	                $size_details=array(); $sizeId_arr=array(); $shipDate_arr=array(); $distributed_qty_arr=array(); $sizeSeqArr=array();
	                foreach($sql_query as $row)
	                {
	                    $po_id=$row[csf('po_break_down_id')];
						$country_id=$row[csf('country_id')];
	                    $size_details[$po_id][$row[csf('country_type')]][$country_id][$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
	                    $sizeId_arr[$row[csf('size_number_id')]]						+=$row[csf("plan_cut_qnty")];
	                    $shipDate_arr[$po_id][$row[csf('country_type')]][$country_id]	=$row[csf("country_ship_date")];
						$po_country_array[$country_id]									=$country_arr[$country_id];
						$size_order_arr[$row[csf('size_number_id')]]					=$row[csf("size_order")];
						$sizeSeqArr[$row[csf('size_number_id')]]+=1;
	                }

	                $size_wise_arr=array();
	                $sizeWiseData=sql_select("select size_ratio, size_id, marker_qty, bundle_sequence 
											from ppl_cut_lay_size_dtls 
											where mst_id=".$mst_id." and  dtls_id=".$details_id." and  status_active=1 $cut_lay_SizeCond");
	                foreach($sizeWiseData as $value)
	                {
	                    $size_wise_arr[$value[csf('size_id')]]['ratio']		=$value[csf('size_ratio')];
	                    $size_wise_arr[$value[csf('size_id')]]['marker_qty']=$value[csf('marker_qty')];
						$size_wise_arr[$value[csf('size_id')]]['seq']		=$value[csf('bundle_sequence')];
	                }
	                
	                $sizeDaraArr=array();
	                $sizeData=sql_select("select id, size_ratio, size_id, marker_qty, bundle_sequence, order_id, country_type, country_id, excess_perc
										from ppl_cut_lay_size 
										where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1 $cut_lay_SizeCond");
					
	                if(count($sizeData)>0)
	                {
	                    $is_update=1;
	                    foreach($sizeData as $value)
	                    {
	                        $sizeDaraArr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('size_ratio')]."**".$value[csf('marker_qty')]."**".$value[csf('bundle_sequence')]."**".$value[csf('id')]."**".$value[csf('excess_perc')];
							$distributed_qty_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
	                    }
	                }
	                else
	                {
	                    $is_update=0;
	                }

	                $lay_bl_qty_arr=array();
					$lay_blData=sql_select("select a.order_id, sum(a.marker_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b, ppl_cut_lay_mst m where m.id=b.mst_id and b.id=a.dtls_id and m.job_no='".$job_id."' and b.gmt_item_id=$cbo_gmt_id and a.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and m.status_active=1 and m.is_deleted=0 group by a.order_id, a.country_type,a.country_id, a.size_id");
			
	                foreach($lay_blData as $value)
	                {
	                    $lay_bl_qty_arr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('marker_qty')];
	                    $lay_bl_qty_size_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
	                }
					
					$size_bl_qty_arr=return_library_array("select sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b, ppl_cut_lay_mst m 											where m.id=b.mst_id and m.job_no='".$job_id."' and b.id=a.dtls_id and b.gmt_item_id=$cbo_gmt_id and b.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and a.hold=0 group by a.size_id",'size_id','size_qty');

					$table_one_width=250+(50*(count($sizeId_arr)));
	                ?>
	                <fieldset style="width:<?=$table_one_width+100; ?>px">
	                <legend>Size Wise Ratio Distribution:</legend>
	                <table cellpadding="0" class="rpt_table" cellspacing="0" width="<?=$table_one_width; ?>" id="tbl_size">
	                    <thead>
	                        <th width="150">Particular</th>
	                        <?php 
								$total_plan_cut_qty=0;
								foreach($sizeId_arr as $size_id=>$plan_cut_qty)
								{
									$lay_balance=$plan_cut_qty-$size_bl_qty_arr[$size_id]+$distributed_qty_arr[$size_id];
									$size_wise_lay_balance[$size_id]=$lay_balance;
	                            	$total_layf_balance+=$lay_balance;
									$total_plan_cut_qty+=$plan_cut_qty;
									?>
	                                <th width="50"><?=$size_arr[$size_id];?></th>
	                                <?php
								}
							?>
	                        <th width="100">Total</th>
	                    </thead>
	                    <tbody>
	                        <tr>
	                        	<td width="150"><input type="text" style="width:140px" class="text_boxes"   value="Size wise Balance[Qty]" disabled /> </td>
	                        	<?php
								$i=0;
								$positive=0;
	                        	foreach($sizeId_arr as $size_id=>$plan_cut_qty)
								{
									$i++;
									if($size_wise_lay_balance[$size_id]>0) {
										$positive++;
									}
	                            	?><td align="center"><input type="text" style="width:50px" class="text_boxes_numeric" name="txt_layf_balance_<?=$i; ?>" id="txt_layf_balance_<?=$i; ?>" value="<?=$size_wise_lay_balance[$size_id]; ?>" disabled /></td><?php
	                            }
								?>
	                            <td align="center">
	                            	<input type="text" style="width:80px" class="text_boxes_numeric" name="txt_total_layf_balance" id="txt_total_layf_balance" value="<?=$total_layf_balance; ?>" disabled />
	                            	<input type="hidden" name="txt_positive_count" value="<?=$positive;?>">
	                            </td>
	                        </tr>
                            <tr>
	                        	<td width="150"><input type="text" style="width:140px" class="text_boxes" value="Size Wise Weight Per PCS" disabled /> </td>
	                        	<?php
								$i=0;
	                        	foreach($sizeId_arr as $size_id=>$plan_cut_qty)
								{
									$sizeweightpcs=0;
									$sizeweightpcs=$sizeSummArr[$size_id]/12;
									$i++;
	                            	?><td align="center"><input type="text" style="width:50px" class="text_boxes_numeric" name="txtsizeweightpcs_<?=$i; ?>" id="txtsizeweightpcs_<?=$i; ?>" value="<?=$sizeweightpcs; ?>" disabled /></td><?php
									$totalsizeweightpcs+=$sizeweightpcs;
	                            }
								?>
	                            <td align="center"><input type="text" style="width:80px" class="text_boxes_numeric" name="txttotsizeweightpcs" id="txttotsizeweightpcs" value="<? //=$totalsizeweightpcs; ?>" disabled /></td>
	                        </tr>
                            <tr>
	                        	<td width="150"><input type="text" style="width:140px" class="text_boxes" value="Required Weight" disabled /> </td>
	                        	<?php
								$i=0; $sizeWiseReqWeightArr=array();
	                        	foreach($sizeId_arr as $size_id=>$plan_cut_qty)
								{
									$i++;
									$reqWeight=0;
									$reqWeight=($sizeSummArr[$size_id]/12)*$size_wise_lay_balance[$size_id];
	                            	?><td align="center"><input type="text" style="width:50px" class="text_boxes_numeric" name="txtsizereqweight_<?=$i; ?>" id="txtsizereqweight_<?=$i; ?>" value="<?=$reqWeight; ?>" disabled /></td><?php
									$totReqWeight+=$reqWeight;
									$sizeWiseReqWeightArr[$size_id]=$reqWeight;
	                            }
								?>
	                            <td align="center"><input type="text" style="width:80px" class="text_boxes_numeric" name="txttotsizeweightreq" id="txttotsizeweightreq" value="<?=$totReqWeight; ?>" disabled /></td>
	                        </tr>
	                        <tr>
	                            <td width="150"><input type="text" style="width:140px" class="text_boxes" value="Size Wise Ratio" disabled /> </td>
	                            <?php
	                            $i=0;
	                            foreach($sizeId_arr as $size_id=>$plan_cut_qty)
	                            {
	                                $i++;
									$sizeRatio=0;
									$sizeRatio=$sizeWiseReqWeightArr[$size_id]/$totReqWeight;
									//$siqz_wise_lot_wgt[$size_id]=($plan_cut_qty/$total_plan_cut_qty)*$total_lot_qty;
									//$siqz_wise_lot_qty[$size_id]=number_format(($plan_cut_qty/$total_plan_cut_qty)*($total_lot_qty/$total_consumption_per_pcs),0,".","");
									//$qtyPerSize=number_format($siqz_wise_lot_wgt[$size_id]/($sizeSummArr[$size_id]/12),0,'.','');
	                                ?><td align="center"><input type="text" style="width:50px" class="text_boxes_numeric" name="txt_size_ratio_<?=$i; ?>" id="txt_size_ratio_<?=$i; ?>"  value="<?=number_format($sizeRatio,4); ?>" disabled /></td><?php
	                            }
	                            ?>
	                            <td align="center"><input type="text" style="width:80px" class="text_boxes_numeric" disabled /></td>
	                       </tr>
	                       <tr>
	                            <td width="150"><input type="text" style="width:140px" class="text_boxes" value="Availabe Yarn [Per Size]" disabled /> </td>
	                            <?php
	                            $i=0;
	                            foreach($sizeId_arr as $size_id=>$plan_cut_qty)
	                            {
	                                $i++;
									$availableYarn=0;
									$availableYarn=($sizeWiseReqWeightArr[$size_id]/$totReqWeight)*$total_lot_qty;
	                                ?>
	                                <td align="center"><input type="text" style="width:50px" class="text_boxes_numeric" name="txt_size_yarn_wgt_<?=$i; ?>" id="txt_size_yarn_wgt_<?=$i; ?>" value="<?=number_format($availableYarn,2,".",""); ?>" disabled /></td>
	                                <?php
									$totalAvailableYarn+=$availableYarn;
	                            }
	                            ?>
	                            <td align="center"><input type="text" id="total_size_yarn_wgt" style="width:80px" class="text_boxes_numeric" value="<?=$totalAvailableYarn; ?>" disabled /></td>
	                       </tr>
	                       <tr>
	                            <td width="150"><input type="text" style="width:140px" class="text_boxes" value="Qty. Per Size" disabled /> </td>
	                            <?php
	                            $i=0; $sizeWiseQtyArr=array(); 
	                            foreach($sizeId_arr as $size_id=>$plan_cut_qty)
	                            {
	                                $i++;
									$qtyPerSize=0;
									$qtyPerSize=(($sizeWiseReqWeightArr[$size_id]/$totReqWeight)*$total_lot_qty)/($sizeSummArr[$size_id]/12);
									//number_format($siqz_wise_lot_wgt[$size_id]/($sizeSummArr[$size_id]/12),0,'.','');
									$siqz_wise_lot_qty[$size_id]=number_format($qtyPerSize,0,".","");
									
	                                ?>
	                                <td align="center">				
	                                    <input type="text" style="width:50px" class="text_boxes_numeric" name="txt_sizef_qty_<?=$i; ?>" id="txt_sizef_qty_<?=$i; ?>"  value="<?=number_format($qtyPerSize,0,".",""); ?>" title="<?='(('.$sizeWiseReqWeightArr[$size_id].'/'.$totReqWeight.')*'.$total_lot_qty.')/('.$sizeSummArr[$size_id].'/12)'; ?>" disabled />
	                                    <input type="hidden" style="width:50px" class="text_boxes_numeric" name="hidden_sizef_id_<?=$i; ?>" id="hidden_sizef_id_<?=$i; ?>"  value="<?=$size_id; ?>" disabled />
	                                </td>
	                                <?php
									$total_size_yarn_qty+=number_format($qtyPerSize,0,".","");
	                            }
	                            ?>
	                            <td align="center"><input type="text" id="total_sizef_qty" style="width:80px" class="text_boxes_numeric" value="<?=$total_size_yarn_qty; ?>" disabled /></td>
	                       </tr>
	                    </tbody>
	                </table>
	                </fieldset>
	                <br>
	                <div>
	                
	                    <fieldset style="width:780px">
	                    	<h3 align="left" id="accordion_h1" style="width:810px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> +Country & Size Wise Ratio Balance</h3>
	         				<div id="content_search_panel">  <!-- style="display:none"-->
	                    <table cellpadding="0" cellspacing="0" width="800" class="" rules="all" border="1" id="tbl_size_details">
	                        <thead class="form_table_header">
	                        	<th>Order No.</th>
	                            <th>Country Type</th>
	                            <th>Country Name</th>
	                            <th>Country Ship. Date</th>
	                            <th>Size</th>
	                            <th>Lay Balance</th>
	                            <th>Excess %</th><!--Copy&nbsp;<input type="checkbox" name="checkbox" id="checkbox"><br>&nbsp;-->
	                            <th>Qty.</th>
	                        </thead>
	                        <tbody>
	                        <?
	                        $i=1; $total_lay_balance=0; $total_marker_qty=0; $total_size_ratio=0; $seq=1; $sizeTmpSeq=array();
	                        $disabled="";
	                        if($positive>0)
	                        {
	                        	$disabled="disabled";
	                        }
	                       	foreach($size_details as $po_id=>$po_val)
							{
								foreach($po_val as $country_type_id=>$country_val)
								{
									foreach($country_val as $country_id=>$size_data)
									{
										foreach($size_data as $size_id=>$plan_cut_qnty)
										{
											$data=explode("**",$sizeDaraArr[$po_id][$country_type_id][$country_id][$size_id]);
											$lay_balance=$plan_cut_qnty-$lay_bl_qty_arr[$po_id][$country_type_id][$country_id][$size_id]+$data[1];  
											//echo $data[1];
											//$sizeSeqArr[$size_id]=1;
											/*if($sizeSeqArr[$size_id]==$sizeTmpSeq[$size_id] || $sizeSeqArr[$size_id]==1) { $sizeTmpSeq[$size_id]=0; $qsl=1; }
											else $sizeTmpSeq[$size_id]+=$seq;*/
											$marker_qty=0;
											if($lay_balance>0)
											{
												if($data[1]>0) $marker_qty=$data[1];
												else if($lay_balance>=$siqz_wise_lot_qty[$size_id])
												{
													$marker_qty=$siqz_wise_lot_qty[$size_id];
												}
												else
												{
													//if($qsl==1 && $lay_balance<$siqz_wise_lot_qty[$size_id])
													if($lay_balance<$siqz_wise_lot_qty[$size_id])
													{
														$marker_qty=$siqz_wise_lot_qty[$size_id];
													}
													else $marker_qty=$lay_balance;
												}
												$siqz_wise_lot_qty[$size_id]=$siqz_wise_lot_qty[$size_id]-$marker_qty;
												$total_lay_balance+=$lay_balance;
												$total_marker_qty+=$marker_qty;
											}
											?>
                                            <tr id="gsd_<? echo $i; ?>">
                                                <td align="center">	
                                                     <input type="text" style="width:100px" class="text_boxes" name="poNo_<?=$i; ?>" id="poNo_<?=$i; ?>" value="<?=$po_no_arr[$po_id]; ?>" disabled />	
                                                     <input type="hidden" name="poId_<?=$i; ?>" id="poId_<?=$i; ?>" value="<?=$po_id; ?>"/>	
                                                </td> 
                                                <td align="center"><?=create_drop_down( "cboCountryType_".$i, 100, $country_type,'',0,'',$country_type_id,'',1); ?></td>
                                                <td align="center"><?=create_drop_down( "cboCountry_".$i, 110, $country_arr, '', 1, '',$country_id,'',1); ?></td>
                                                <td align="center"><input type="text" style="width:80px" class="datepicker" name="shipdate_<?=$i; ?>" id="shipdate_<?=$i; ?>" value="<?=change_date_format($shipDate_arr[$po_id][$country_type_id][$country_id]); ?>" disabled readonly /></td> 
                                                <td align="center">	
                                                      <input type="text" style="width:80px" class="text_boxes"  name="txt_size_<?=$i; ?>" id="txt_size_<?=$i; ?>" value="<?=$size_arr[$size_id]; ?>" disabled readonly />
                                                      <input type="hidden" id="hidden_size_id_<?=$i; ?>" name="hidden_size_id_<?=$i; ?>" value="<?=$size_id; ?>">
                                                      <input type="hidden" id="update_size_id_<?=$i; ?>" name="update_size_id_<?=$i; ?>" value="<?=$data[3]; ?>">
                                                </td>                 
                                                <td align="center"><input type="text" style="width:80px" class="text_boxes_numeric" name="txt_lay_balance_<?=$i; ?>" id="txt_lay_balance_<?=$i; ?>" value="<?=$lay_balance; ?>" disabled readonly /></td> 
                                                <td align="center">	<input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_excess_<?=$i; ?>" id="txt_excess_<?=$i; ?>" value="<?=$data[4]; ?>" disabled/><!-- onKeyUp="copy_perc(<?// echo $i; ?>);"--></td>
                                                <td align="center"><input type="text" style="width:80px" class="text_boxes_numeric" name="txt_size_qty_<?=$i; ?>" id="txt_size_qty_<?=$i; ?>" onKeyUp="calculate_perc(<?=$i; ?>);" onBlur="check_size_qty(<?=$i; ?>);"  value="<?=$marker_qty; ?>" <?=$disabled;?> />
                                                    <!--onKeyUp="calculate_perc(<? //echo $i; ?>);" onBlur="check_size_qty(<? //echo $i; ?>);"-->	
                                                </td>
                                            </tr>
                                            <?
                                            $i++;
										}
									}
								}
							}
	                       ?>
	                        </tbody>
	                        <tfoot>
	                            <tr class="form_table_header">
	                                <th colspan="5" align="right">Total</th>
	                                <th align="right"><?=$total_lay_balance; ?>&nbsp;</th>
	                                <th>&nbsp;</th>
	                                <th align="right" id="total_size_qty"><?=$total_marker_qty; ?>&nbsp;</th>
	                            </tr>
	                        </tfoot>
	                    </table>
	                	</div>
	                        <table>
	                        	<tr>
	                                <td align="center" valign="middle" colspan="5" >
	                                    <?=load_submit_buttons($permission, "fnc_cut_lay_size_info", $is_update,0,"clear_size_form()",1); ?>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td align="center" colspan="7" >
	                                    <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<?=$size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
	                                    <input type="button" id="btn_print" name="btn_print" value=" Sticker" class="formbutton" onClick="fnc_print_bundle_sticker()"/>
	                                    <input type="button" id="btn_print" name="btn_print" value=" Bundle Sticker" class="formbutton" onClick="fnc_print_bundle_sticker_new()"/>
	                                    <input type="button" id="btn_print" name="btn_print" value=" Bundle Sticker2" class="formbutton" onClick="fnc_print_bundle_sticker_new2()"/>
	                                    <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle()"/>
	                                    
	                                     <input type="button" id="btn_stiker_urmi" name="btn_stiker_urmi" value=" Sticker " class="formbutton" onClick="fnc_bundle_report_first()" style="display:none" />
	                                     <input type="button" id="btn_stiker_fivePeight" name="btn_stiker_fivePeight" value="  Sticker 5.8X1.5  " class="formbutton" onClick="fnc_bundle_report_fivePointeight()" style="display:none"/>

	                                      <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()" style="display:none"/>
	                                      <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Yarn Lot Ratio" class="formbutton" onClick="fnc_yarn_lot_ratio_report()"/>
	                                    <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  />
	                                    <input type='hidden' id="hidden_total_marker" name="hidden_total_marker"  />
	                                    <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance"  />
	                                    <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  />
	                                </td>
	                            </tr>
	                        </table>
	                    </fieldset>
	                </div>
	                <br>
	            </fieldset>
	     	</form>  
	        <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">    
	            <div id="search_div" style="margin-top:3px">
	                <?
					if($sizeid!="") $sizeidpplCond=" and size_id in ($sizeid)"; else $sizeidpplCond="";
					$sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1 and is_deleted=0 $sizeidpplCond");
					
	                $size_colour_arr=array();
	                foreach($sql_size_name as $asf)
	                {
	                    $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
	                }
	                $i=1;
					if($sizeid!="") $sizeidbndlCond=" and a.size_id in ($sizeid)"; else $sizeidbndlCond="";
	                $bundle_data=sql_select("select a.id, a.bundle_no, a.size_id, a.number_start, a.number_end, a.size_qty, a.update_flag, a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.barcode_no, a.is_excess, a.order_id,a.status_active,a.hold from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." and a.status_active=1 and is_deleted=0 $sizeidbndlCond order by a.id ASC");

	                $bundle_no_arr=array();
	                $bundle_next_process=array();
	                foreach ($bundle_data as $row) {
	                	array_push($bundle_no_arr, $row[csf('bundle_no')]);
	                }

	                $bundle_cond=where_con_using_array($bundle_no_arr,1,"b.bundle_no");
	                $bundle_hold_validation=sql_select("select a.cutting_qc_no,b.bundle_no from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $bundle_cond ");
	                foreach ($bundle_hold_validation as $row) 
	                {
	                	$bundle_next_process[$row[csf('bundle_no')]]=$row[csf('cutting_qc_no')];
	                }

	                $bundle_hold_issue_validation_sql="SELECT 
															       c.bundle_no,
															       d.sys_number
															  FROM ppl_cut_lay_dtls a,
															       ppl_cut_lay_bundle b,
															       pro_garments_production_dtls c,
															       pro_gmts_delivery_mst d
															 WHERE     c.bundle_no = b.bundle_no
															       AND c.barcode_no = b.barcode_no
															       AND a.id = b.dtls_id
															       AND d.id = c.delivery_mst_id
															       AND a.status_active = 1
															       AND a.is_deleted = 0
															       AND b.status_active = 1
															       AND b.is_deleted = 0
															       AND c.status_active = 1
															       AND c.is_deleted = 0
															       AND d.status_active = 1
															       AND d.is_deleted = 0
															       AND d.production_type = 50
															       $bundle_cond ";
		            //echo $bundle_hold_issue_validation_sql;
					$bundle_hold_issue_validation=sql_select($bundle_hold_issue_validation_sql);
	                $bundle_next_process_issue=array();
	                foreach ($bundle_hold_issue_validation as $row) 
	                {
	                	$bundle_next_process_issue[$row[csf('bundle_no')]]=$row[csf('sys_number')];
	                }

	                if(count($bundle_data)>0)
	                {
	                ?>
	                    <fieldset style="width:810px">
	                        <legend>Bundle No and RMG Qty Details<span style="text-align:right;float:right">
	                        	<?
                                // $l=1;
                                $chk_size = array();
                                foreach($sql_size_name as $asf)
                                {
                                	if(!in_array($asf[csf("size_id")], $chk_size))
                                	{
	                                    ?>	
	                                        <input type="checkbox" name="check_same_size"  id="check_same_size_<?=$asf[csf("size_id")]; ?>" onClick="check_same_all_size('<?=$asf[csf("size_id")]; ?>');"><?=$size_arr[$asf[csf("size_id")]]; ?>
	                                    <?
	                                    $chk_size[$asf[csf("size_id")]] = $asf[csf("size_id")];
	                                }
                                }
                                ?>
	                         </span></legend>
	                        <table cellpadding="0" cellspacing="0" width="900" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
	                            <thead class="form_table_header">
                                	<tr>
                                        <th width="30" rowspan="2">SL No</th>
                                        <th width="130" rowspan="2">Order No.</th>
                                        <th width="70" rowspan="2">Country Type</th>
                                        <th width="80" rowspan="2">Country Name</th>
                                        <th width="60" rowspan="2">Size</th>
                                        <th width="120" rowspan="2">Bundle No</th>
                                        <th width="50" rowspan="2">Quantity</th>
                                        <th colspan="2">RMG Number</th>
                                        <th width="100" rowspan="2">
                                            <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<?=$mst_id; ?>" />  
                                            <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<?=$details_id; ?>" />
                                        </th>
                                        <th rowspan="2" width="50">Sticker<br><input type="checkbox" name="check_all" id="check_all" onClick="check_all_report();"></th>
                                        <th rowspan="2">Bundle Hold<br><input type="checkbox" name="check_all_hold" id="check_all_hold" onClick="check_all_hold_fnc();"></th>
                                    </tr>
                                    <tr>
                                    	<th width="50">From</th>
                                        <th width="50">To</th>
                                    </tr>
	                            </thead>
	                            <tbody id="trBundleListSave">
	                            <?
	                            foreach($bundle_data as $row)
	                            { 
	                                $update_f_value=""; 
	                                if(str_replace("'","",$row[csf('update_flag')])==1)
	                                {
	                                    $update_f_value=explode("**",$row[csf('update_value')]);
	                                }
	                                $bundle_next_process_issue_value=$bundle_next_process_issue[$row[csf('bundle_no')]];
	                                $next_process_vaule=$bundle_next_process[$row[csf('bundle_no')]];
	                            ?>
	                                <tr id="trBundleListSave_<?=$i; ?>">
	                                    <td align="center" id="">
	                                        <input type="text" id="sirialNo_<?=$i; ?>" name="sirialNo[]" style="width:20px;" class="text_boxes" value="<?=$i; ?>" disabled/>
	                                        <input type="hidden" id="hiddenExtraTr_<?=$i; ?>" name="hiddenExtraTr[]" value="<?=$i; ?>" />                   
	                                        <input type="hidden" id="hiddenUpdateFlag_<?=$i; ?>" name="hiddenUpdateFlag[]" value="<?=$row[csf('update_flag')];?> " />
	                                        <input type="hidden" id="hiddenUpdateValue_<?=$i; ?>" name="hiddenUpdateValue[]" value="<?=$row[csf('update_value')];?> " />
	                                        <input type="hidden" name="isExcess[]" id="isExcess_<?=$i; ?>" value="<?=$row[csf('is_excess')]; ?>"/>
	                                    </td>
	                                    <td align="center"><?=create_drop_down( "cboPoId_".$i, 130, $po_no_arr,'', 0, '',$row[csf('order_id')],'',1,'','','','','','','cboPoId[]'); ?></td>
	                                    <td align="center">
											<?=create_drop_down( "cboCountryTypeB_".$i, 70, $country_type,'', 0, '',$row[csf('country_type')],'',1); ?>
	                                        <input type="hidden" id="hiddenCountryTypeB_<?=$i; ?>" name="hiddenCountryTypeB[]" value="<?=$row[csf('country_type')];?>"/>
	                                    </td> 
	                                    <td align="center">	
	                                        <?=create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]'); ?> 
	                                        <input type="hidden" id="hiddenCountryB_<?=$i; ?>" name="hiddenCountryB[]" value="<?=$row[csf('country_id')];?> " />
	                                    </td>
	                                    <td align="center" id="update_sizename_<?=$i;  ?>">
	                                        <select name="sizeName[]" id="sizeName_<?=$i; ?>" class="text_boxes" style="width:60px; text-align:center; <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?>" disabled>
	                                        <?
	                                        // $l=1;
	                                        foreach($sql_size_name as $asf)
	                                        {
	                                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
	                                            ?><option value="<?=$asf[csf("size_id")]; ?>" <?=$select_text;  ?>><?=$size_arr[$asf[csf("size_id")]]; ?> </option><?
	                                        }
	                                        ?>          
	                                        </select>
	                                    	<input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<?=$i; ?>" value="<?=$row[csf('size_id')]; ?>" />
	                                    </td>
	                                    <td align="center"><input type="text" name="bundleNo[]" id="bundleNo_<?=$i; ?>" value="<?=$row[csf('bundle_no')]; ?>" class="text_boxes" style="width:110px; text-align:center" disabled title="<?=$row[csf('barcode_no')]; ?>"/></td>
	                                    <td align="center">
	                                    	<input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<?=$i; ?>" onKeyUp="bundle_calclution(<?=$i; ?>);" value="<?=$row[csf('size_qty')]; ?>" style="width:40px; text-align:right;<? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>" class="text_boxes" disabled/>
	                                    	<input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<?=$i; ?>" value="<?=$row[csf('size_qty')]; ?>" disabled/>
	                                    </td>
	                                    <td align="center"><input type="text" name="rmgNoStart[]" id="rmgNoStart_<?=$i; ?>" value="<?=$row[csf('number_start')]; ?>" style="width:40px; text-align:right" class="text_boxes" disabled /></td>
	                                    <td align="center"><input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<?=$i; ?>" value="<?=$row[csf('number_end')]; ?>" style="width:40px; text-align:right" class="text_boxes" disabled/></td>
	                                    <td align="center">
                                        	<input type="button" value="+" name="addButton[]" id="addButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<?=$i; ?>','<?=$i; ?>');"/>&nbsp;
	                                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<?=$i; ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<?=$i; ?>');"/>
	                                    </td>
	                                    <td align="center" width="50">
	                                        <input id="chk_bundle_<?=$i; ?>" type="checkbox" name="chk_bundle" class="<?=$row[csf('size_id')]; ?>">
	                                        <input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<?=$row[csf('id')]; ?>" style="width:15px;" class="text_boxes"/>
	                                    </td>
	                                    <td align="center">
	                                        <input id="chk_status_<?=$i; ?>" type="checkbox" name="chk_status[]"  value="<?=$row[csf('hold')];?>" onClick="check_status_value(<?=$i?>);" <?if($row[csf('hold')]==1){echo 'checked';}?>>

	                                         <input id="bundle_next_process_<?=$i; ?>" type="hidden" name="bundle_next_process[]"  value="<?=$next_process_vaule;?>" >
	                                         <input id="bundle_next_process_issue_<?=$i; ?>" type="hidden" name="bundle_next_process_issue[]"  value="<?=$bundle_next_process_issue_value;?>" >

	                                        
	                                    </td>
	                                </tr>
	                            	<?
	                            	$i++;
	                            } 
	                            ?>
	                            </tbody>
	                        </table>
	                        <table cellpadding="0" cellspacing="0" width="900" border="1" rules="all">
	                            <tr>
	                                <td colspan="13" align="center" class="button_container">
	                                    <?=load_submit_buttons( $permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
	                                </td>
	                            </tr>
	                        </table>
	                    </fieldset>
	                <?
	                }
	                ?>
	            </div>  
	        </form>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom_noselect.js" type="text/javascript"></script>
	<script>
		$('#cboCountryBundle').val(0);
	</script>
	</html>
	<?
	exit();
}

//save size and bundlesave_update_delete
if($action=="save_update_delete_size")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$bundle_per_pcs=str_replace("'",'',$txt_bundle_pcs);
	$cut_no=str_replace("'",'',$hidden_update_cut_no);
	$to_marker_qty=str_replace("'",'',$total_sizef_qty);
	$job_id=str_replace("'",'',$hidden_update_job_id);
	$txt_plies=str_replace("'",'',$totalLotQty);

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
    	$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		if($cutting_qc_no!="") { echo "201**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;}
		
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix	=$cutData[0][csf('cut_num_prefix_no')];
		$job_no			=$cutData[0][csf('job_no')];

		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");

		$sql_sewing_operation=sql_select("SELECT c.id, c.code, c.operation_name, c.bodypart_id from wo_po_details_master j, ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b , lib_sewing_operation_entry c where j.job_no='".$job_no."' and j.style_ref_no=a.style_ref and a.bulletin_type=4 and a.id=b.mst_id and b.lib_sewing_id=c.id and j.status_active=1 and j.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.production_type=2 and c.status_active=1 and c.is_deleted=0 order by c.id");

		$plan_qty= return_field_value("sum(plan_cut_qnty) as plan_qty","wo_po_color_size_breakdown","po_break_down_id in(".$order_id.") and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");
		
		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b","a.id=b.dtls_id and b.order_id in(".$order_id.") and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");
		
		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array(); $seqDatas='';
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,yarn_qty,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_size_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$txt_yarn_wgt="txt_size_yarn_wgt_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if($data_array_size!="") $data_array_size.= ",";
				$data_array_size.="(".$id_size.",".$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$$txt_yarn_wgt.",".$user_id.",'".$pc_date_time."')";
				
				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
			}
			$id_size++;
		}
		
		$bundle_no_array=array();
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,order_id,is_excess,color_type_id,inserted_by,insert_date";
		
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		$feild_array_operation="id, bundle_id,dtls_id,mst_barcode_no, operation_id, operation_name, operation_code, barcode_no,barcode_prifix,barcode_year, status_active, is_deleted";
		
		$rmg_no_creation=return_field_value("smv_source","variable_settings_production "," company_name='$cbo_company_id' and variable_list=39 and status_active=1 and is_deleted=0");
		
		if($rmg_no_creation==1)
		{
			
			/*$sql_bundle=sql_select("SELECT size_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by size_id");
			foreach($sql_bundle as $row)
			{
				$bundle_no_array[$row[csf('size_id')]]['num_prefix']=$row[csf('num_prefix')];
				$bundle_no_array[$row[csf('size_id')]]['last_rmg']=$row[csf('last_rmg')];
			}*/
			
		}
		if($rmg_no_creation==3)
		{
			$bundleData=sql_select("select max(a.number_end) as last_rmg, max(a.bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and b.job_no='".$job_id."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
										
			$last_rmg_no=$bundleData[0][csf('last_rmg')];
			//echo "10**".$last_rmg_no;die;
		}
		else
		{
			//$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
			//$last_rmg_no=$bundleData[0][csf('last_rmg')];
			$last_rmg_no=0;
		}
		$bundle_no_creation=return_field_value("smv_source","variable_settings_production "," company_name='$cbo_company_id' and variable_list=37 and status_active=1 and is_deleted=0");
		
		if($bundle_no_creation==2)
		{
			$last_bundle_no=return_field_value("max(a.bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b","b.id=a.mst_id  and b.job_no='".$job_id."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","last_prefix");
			//echo "10**".$last_bundle_no;die;
		}
		else
		{
			//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id","last_prefix");
			$last_bundle_no=0;
		}
		
		$update_id=''; $tot_marker_qnty_curr=0; $size_country_array=array(); $country_type_array=array(); $sizeRatioBlArr=array();
		
		$id=return_next_id("id", "ppl_cut_lay_size", 1); 
		
		// $year_id=date('Y',time());
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id=$cutNoEx[1];
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		$operation_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle_operation","barcode_year=$year_id","suffix_no");
		$update_id		=''; 
		$bundle_prif_no =$last_bundle_no; 
		$erange			=$last_rmg_no;
		$data_array_bundle_operation='';
		$id_operation=return_next_id("id", "ppl_cut_lay_bundle_operation", 1);
		for($i=1; $i<=$country_row_num; $i++)
		{
			$txt_size_id		="hidden_size_id_".$i;
			$txt_lay_balance	="txt_lay_balance_".$i;
			$cboCountryType		="cboCountryType_".$i;
			$cboCountry			="cboCountry_".$i;
			$excess_perc		="txt_excess_".$i;
			$po_id				="poId_".$i;
			$txt_size_qty		="txt_size_qty_".$i;
			
			$marker_qty			=0;
			$order_id			=str_replace("'",'',$$po_id);
			$size_id			=str_replace("'",'',$$txt_size_id);
			$lay_balance		=str_replace("'",'',$$txt_lay_balance);
			$marker_qty			=str_replace("'",'',$$txt_size_qty);

			
			if($marker_qty>0)
			{
				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$$excess_perc.",".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				
				if($rmg_no_creation==1){$srange=0; $erange=$last_rmg_no;}
				$bl_size_qty=str_replace("'",'',$$txt_size_qty); $data='';
				$bundle_per_size=ceil(str_replace("'","",$$txt_size_qty)/str_replace("'","",$bundle_per_pcs)); $tot_bundle_qty=0;
				
				for($k=1; $k<=$bundle_per_size; $k++)
				{ 
					if($k==$bundle_per_size) 
					{
						$bundle_qty		=$bl_size_qty-$tot_bundle_qty;
					}
					else 
					{
						$bundle_qty		=$bundle_per_pcs;
					}
					
					$company_sort_name	=explode("-",$cut_no);
					$bundle_prif		=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
					$bundle_prif_no		=$bundle_prif_no+1;
					$bundle_no			=$bundle_prif."-".$bundle_prif_no;
					$srange				=$erange+1;
					$erange				=$srange+$bundle_qty-1;
					$tot_bundle_qty		+=$bundle_qty;
					$barcode_suffix_no	=$barcode_suffix_no+1;
					$barcode_no			=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
					
					if($data_array_bundle!="") $data_array_bundle.= ",";
					$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$$txt_size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",".$$cboCountryType.",".$$cboCountry.",'".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";

					if(count($sql_sewing_operation)>0)
					{
						foreach ($sql_sewing_operation as $op_val) {
						
							//$feild_array_operation="id, bundle_id,mst_barcode_no, operation_id, operation_name, operation_code, barcode_no,barcode_prifix,barcode_year,   status_active, is_deleted";
							$operation_suffix_no	=$operation_suffix_no+1;
							$operation_barcode_no			=$year_id."253".str_pad($operation_suffix_no,10,"0",STR_PAD_LEFT);
							if($data_array_bundle_operation!="") $data_array_bundle_operation.= ",";
							$data_array_bundle_operation.="(".$id_operation.",".$bundle_id.",".$dtls_id.",'".$barcode_no."',".$op_val[csf('id')].",'".$op_val[csf('operation_name')]."','".$op_val[csf('code')]."','".$operation_barcode_no."','".$operation_suffix_no."','".$year_id."',1,0)";
							$id_operation++;
						}
					}

					$bundle_id			=$bundle_id+1;
				}
				$id=$id+1;
			}
		}
		
		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		
		$bundleCreate= return_field_value("bundle_no","ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id=$dtls_id and status_active=1 and is_deleted=0");
		
		if($bundleCreate!="") { echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;  disconnect($con); die;}//for duplicate bundle check
	
		$rID3=true;
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle;die;
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=true;
		if(!empty($data_array_size))
		{
			$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0); // change for shohel vai
		}
		
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		if($data_array_bundle_operation!="")
		{
			$rID3=sql_insert("ppl_cut_lay_bundle_operation",$feild_array_operation,$data_array_bundle_operation,0);
		}
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up=$to_marker_qty."*'".$bundle_per_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
		//echo "10**".$rID."**".$rID_size."**".$rID2."**".$rID4."**".$rID3;die;
		
		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);  
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix	=$cutData[0][csf('cut_num_prefix_no')];
		$job_no			=$cutData[0][csf('job_no')];
		if($cutting_qc_no!="") { echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;}
		
		$sql_sewing_operation=sql_select("select c.id, c.code, c.operation_name, c.bodypart_id from wo_po_details_master j, ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b , lib_sewing_operation_entry c where j.job_no='".$job_no."' and j.style_ref_no=a.style_ref and a.bulletin_type=4 and a.id=b.mst_id and b.lib_sewing_id=c.id and j.status_active=1 and j.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.production_type=2 and c.status_active=1 and c.is_deleted=0 order by c.id");
	//echo "10**".count($sql_sewing_operation); die;

		$previous_operation_barcode_data=sql_select("select mst_barcode_no, operation_id barcode_no, barcode_year, barcode_prifix from ppl_cut_lay_bundle_operation where dtls_id=".$dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_operation_barcode_data as $op_val)
		{
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['year']	=$op_val[csf("barcode_year")];
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['prifix']	=$op_val[csf("barcode_prifix")];
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['barcode']	=$op_val[csf("barcode_no")];
		}

		$previous_barcode_data=sql_select("select bundle_no, barcode_no, barcode_year, barcode_prifix from ppl_cut_lay_bundle where mst_id=".$mst_id."  and dtls_id=".$dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']		=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']	=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']	=$b_val[csf("barcode_no")];
		}
		//print_r($previous_barcode_arr);die; 
		
		$plan_qty= return_field_value("sum(plan_cut_qnty) as plan_qty","wo_po_color_size_breakdown","job_no_mst='".$job_no."' and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");
		
		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b,ppl_cut_lay_mst m"," m.id=a.mst_id and a.id=b.dtls_id and m.id=b.mst_id and m.job_no='".$job_no."' and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");
		//echo "10**".$size_row_num.'='.$country_row_num;die;
		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array(); $seqDatas='';
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,yarn_qty,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id		="hidden_sizef_id_".$i;
			$txt_layf_balance		="txt_layf_balance_".$i;
			$txt_sizef_ratio		="txt_size_ratio_".$i;
			$txt_sizef_qty			="txt_sizef_qty_".$i;
			$txt_yarn_wgt			="txt_size_yarn_wgt_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if($data_array_size!="") $data_array_size.= ",";
				$data_array_size.="(".$id_size.",".$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$$txt_yarn_wgt.",".$user_id.",'".$pc_date_time."')";
				
				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
			}
			$id_size++;
		}
		
		
		$bundle_no_array=array();
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,order_id,is_excess,color_type_id,inserted_by,insert_date";
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		$feild_array_operation="id, bundle_id,dtls_id,mst_barcode_no, operation_id, operation_name, operation_code, barcode_no,barcode_prifix,barcode_year, status_active, is_deleted";
		
		
		$rmg_no_creation=return_field_value("smv_source","variable_settings_production "," company_name='$cbo_company_id' and variable_list=39 and status_active=1 and is_deleted=0");
		
		if($rmg_no_creation==1)
		{
			
			/*$sql_bundle=sql_select("SELECT size_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by size_id");
			foreach($sql_bundle as $row)
			{
				$bundle_no_array[$row[csf('size_id')]]['num_prefix']=$row[csf('num_prefix')];
				$bundle_no_array[$row[csf('size_id')]]['last_rmg']=$row[csf('last_rmg')];
			}*/
			
		}
		if($rmg_no_creation==3)
		{
			$bundleData=sql_select("select max(a.number_end) as last_rmg, max(a.bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and  b.job_no='".$job_id."' and a.dtls_id!=".$dtls_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$last_rmg_no=$bundleData[0][csf('last_rmg')];
			//echo "10**".$last_rmg_no;die;
		}
		else
		{
			//$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
			//$last_rmg_no=$bundleData[0][csf('last_rmg')];
			$last_rmg_no=0;
		}
		$bundle_no_creation=return_field_value("smv_source","variable_settings_production "," company_name='$cbo_company_id' and variable_list=37 and status_active=1 and is_deleted=0");
		
		if($bundle_no_creation==2)
		{
			$last_bundle_no=return_field_value("max(a.bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b","b.id=a.mst_id  and b.job_no='".$job_id."' and a.dtls_id!=".$dtls_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","last_prefix");
			//echo "10**".$last_bundle_no;die;
		}
		else
		{
			//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id","last_prefix");
			$last_bundle_no=0;
		}
		
		$update_id=''; $tot_marker_qnty_curr=0;  $size_country_array=array(); $country_type_array=array(); $sizeRatioBlArr=array();
		
		$id=return_next_id("id", "ppl_cut_lay_size", 1); 
		
		// $year_id=date('Y',time());
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id=$cutNoEx[1];
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		$operation_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle_operation","barcode_year=$year_id","suffix_no");
		$update_id=''; $bundle_prif_no=$last_bundle_no; $erange=$last_rmg_no;
		$data_array_bundle_operation='';
		$id_operation=return_next_id("id", "ppl_cut_lay_bundle_operation", 1);// echo "10**";
		for($i=1; $i<=$country_row_num; $i++)
		{
			$txt_size_id		="hidden_size_id_".$i;
			$txt_lay_balance	="txt_lay_balance_".$i;
			$cboCountryType		="cboCountryType_".$i;
			$cboCountry			="cboCountry_".$i;
			$excess_perc		="txt_excess_".$i;
			$po_id				="poId_".$i;
			$txt_size_qty		="txt_size_qty_".$i;
			
			$marker_qty			=0;
			$order_id			=str_replace("'",'',$$po_id);
			$size_id			=str_replace("'",'',$$txt_size_id);
			$lay_balance		=str_replace("'",'',$$txt_lay_balance);
			$marker_qty			=str_replace("'",'',$$txt_size_qty);

			//echo $bundle_per_pcs; die;
			if($marker_qty>0)
			{
				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$$excess_perc.",".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				
				if($rmg_no_creation==1){$srange=0; $erange=$last_rmg_no;}
				$bl_size_qty=str_replace("'",'',$$txt_size_qty); $data='';
				$bundle_per_size=ceil(str_replace("'","",$$txt_size_qty)/str_replace("'","",$bundle_per_pcs)); $tot_bundle_qty=0;
				//echo $bundle_per_size.'-'; 
				for($k=1; $k<=$bundle_per_size; $k++)
				{ 
					if($k==$bundle_per_size) 
					{
						$bundle_qty	=$bl_size_qty-$tot_bundle_qty;
					}
					else 
					{
						$bundle_qty=$bundle_per_pcs;
					}
					//echo $bundle_qty; die;
					$company_sort_name=explode("-",$cut_no);
					$bundle_prif	=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
					$bundle_prif_no	=$bundle_prif_no+1;
					$bundle_no		=$bundle_prif."-".$bundle_prif_no;
					$srange			=$erange+1;
					$erange			=$srange+$bundle_qty-1;
					$tot_bundle_qty+=$bundle_qty;
					
					if(empty($previous_barcode_arr[$bundle_no]))
					{
						$barcode_suffix_no	=$barcode_suffix_no+1;
						$up_barcode_suffix	=$barcode_suffix_no;
						$up_barcode_year	=$year_id;
						$barcode_no			=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
					}
					else
					{
						$up_barcode_suffix	=$previous_barcode_arr[$bundle_no]['prifix'];
						$up_barcode_year	=$previous_barcode_arr[$bundle_no]['year'];
						$barcode_no			=$previous_barcode_arr[$bundle_no]['barcode'];
					}
					
					if($data_array_bundle!="") $data_array_bundle.= ",";
					$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$$txt_size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",".$$cboCountryType.",".$$cboCountry.",'".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
					if(count($sql_sewing_operation)>0)
					{
						foreach ($sql_sewing_operation as $op_val) {
							if(empty($operation_barcode_arr[$barcode_no][$op_val[csf('id')]]))
							{
								$operation_suffix_no	=$operation_suffix_no+1;
								$up_operation_suffix	=$operation_suffix_no;
								$up_operation_year		=$year_id;
								$operation_barcode_no	=$year_id."253".str_pad($up_operation_suffix,10,"0",STR_PAD_LEFT);
							}
							else
							{
								$up_operation_suffix	=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['prifix'];
								$up_operation_year		=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['year'];
								$operation_barcode_no	=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['barcode'];
							}
						
							
							//$operation_barcode_no			=$year_id."253".str_pad($operation_suffix_no,10,"0",STR_PAD_LEFT);
							if($data_array_bundle_operation!="") $data_array_bundle_operation.= ",";
							$data_array_bundle_operation.="(".$id_operation.",".$bundle_id.",".$dtls_id.",'".$barcode_no."',".$op_val[csf('id')].",'".$op_val[csf('operation_name')]."','".$op_val[csf('code')]."','".$operation_barcode_no."','".$up_operation_suffix."','".$up_operation_year."',1,0)";
							$id_operation++;
						}
					}
					$bundle_id=$bundle_id+1;
				}
				$id=$id+1;
			}
		}
		//echo "10**".$data_array_bundle; die;
		
		$delete_bundle_operation=true;
		$delete=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_size=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_bundle=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_bundle_operation=execute_query("delete from ppl_cut_lay_bundle_operation where dtls_id=".$dtls_id."",0);
		
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle."**".$rID2;die;
		$rID3=true;
		if($data_array_bundle_operation!="")
		{
			$rID3=sql_insert("ppl_cut_lay_bundle_operation",$feild_array_operation,$data_array_bundle_operation,0);
		}
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up="".$to_marker_qty."*'".$bundle_per_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
		
		//echo "10**".$rID ."**". $rID_size ."**". $rID2 ."**". $rID4 ."**". $delete ."**". $delete_size."**".$delete_bundle."**".$delete_bundle_operation;die;	
		
		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$lay_balance."**".$total_marker_qty."**".$total_marker_qty_prev."**".$tot_marker_qnty_curr;die;
		
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_bundle_operation)
			{
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_bundle_operation)
			{
				oci_commit($con);  
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID ."&&" .$rID_size ."&&" . $rID2 ."&&" . $rID3 ."&&" . $rID4 ."&&" .  $delete ."&&" . $delete_size ."&&" . $delete_bundle ."&&" . $delete_bundle_operation;
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		exit();	
	}	
}

if($action=="show_bundle_list_view")
{
	$ex_data= explode("**",$data);
	$mst_id=$ex_data[0];
	$dtls_id=$ex_data[1];
	
	$country_arr=return_library_array("select id, country_name from lib_country",'id','country_name'); 
	$po_country_array=array();
	$sql_query=sql_select("select distinct a.country_id as country_id from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b, ppl_cut_lay_size c where a.item_number_id=b.gmt_item_id and a.po_break_down_id=c.order_id and b.id=c.dtls_id and a.color_number_id=b.color_id and b.mst_id=$mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0");
	$size_details=array(); $sizeId_arr=array(); $shipDate_arr=array();
	foreach($sql_query as $row)
	{
		$po_country_array[$row[csf('country_id')]]=$country_arr[$row[csf('country_id')]];
	}
	
	$po_no_arr=return_library_array("select a.id, a.po_number from wo_po_break_down a, ppl_cut_lay_size b where a.id=b.order_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and b.status_active=1 and b.is_deleted=0 and a.is_confirmed=1 group by a.id, a.po_number",'id','po_number');

	?>
    <fieldset style="width:960px">
        <legend>Bundle No and RMG qty details</legend>
        <table cellpadding="0" cellspacing="0" width="950" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
            <thead class="form_table_header">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
               
                
                <th colspan="2">RMG Number</th>
                <th>
                    <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                    <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
                </th>
                <th>Report &nbsp;</th>
                <th>Hold &nbsp;</th>
            </thead>
            <thead class="form_table_header">
                <th>SL No</th>
                <th>Order No.</th>
                <th>Country Type</th>
                <th>Country Name</th>
                <th>Size</th>
                <th>Bundle No</th>
                <th>Quantity</th>
                <th>From</th>
                <th>To</th>
                <th></th>
                <th width="40"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
                <th width="40"><input type="checkbox" name="check_all_hold" id="check_all_hold" onClick="check_all_hold_fnc();"></th>
            </thead>
            <tbody id="trBundleListSave">
            <?
            $sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."");
            $size_colour_arr=array();
            foreach($sql_size_name as $asf)
            {
                $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
            }

            $bundle_data=sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess,a.barcode_no, a.order_id,a.status_active from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." order by a.id ASC");
            $i=1; 
            foreach($bundle_data as $row)
            { 
                $update_f_value=""; 
                if(str_replace("'","",$row[csf('update_flag')])==1)
                {
                    $update_f_value=explode("**",$row[csf('update_value')]);
                }
            ?>
                <tr id="trBundleListSave_<? echo $i;  ?>">
                    <td align="center"  id="">
                        <input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:25px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"/>                   
                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i; ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> "/>
                        <input type="hidden" id="hiddenUpdateValue_<? echo $i; ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " />
                    </td>
                    <td align="center">	
						<?
                            echo create_drop_down( "cboPoId_".$i, 130, $po_no_arr,'', 0, '',$row[csf('order_id')],'',1,'','','','','','','cboPoId[]'); 
                        ?>
                    </td>
                    <td align="center">	
                        <?
                            echo create_drop_down( "cboCountryTypeB_".$i, 70, $country_type,'', 0, '',$row[csf('country_type')],'',1); 
                        ?>                                          
                        <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/> 
                    </td> 
                    <td align="center">	
                        <?
							 echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]');
                        ?> 
                        <input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]"  value="<? echo $row[csf('country_id')];?> " />
                    </td>
                    <td align="center" id="update_sizename_<? echo $i;  ?>">
                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled  >
                        <?
                        // $l=1;
                        foreach($sql_size_name as $asf)
                        {
                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
                            ?>	
                            <option value="<? echo $asf[csf("size_id")]; ?> " <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
                            <?
                            }
                        ?>          
                        </select>
                        <input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
                    </td>
                   
                    <td align="center">
                    	<input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:120px;  text-align:center" disabled  title="<?php echo $row[csf('barcode_no')]; ?>"/>
                    </td>
                    <td align="center">
                        <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:40px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                        <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>" disabled/>
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:40px; text-align:right" class="text_boxes"  disabled />
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:40px; text-align:right" class="text_boxes"  disabled/>
                    </td>
                    <td align="center">
                        <input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
                    </td>
                    <td align="center">
                        <input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle" >
                        <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes"/>

                    </td>
                    <td align="center">
                         <input id="chk_status_<?=$i; ?>" type="checkbox" name="chk_status[]" <?php if($row[csf('status_active')]==2){ echo "checked";}?> value="<?=$row[csf('status_active')];?>" onClick="check_status_value(<?=$i?>);">
                        
                    </td>
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" width="700">
            <tr>
                <td colspan="8" align="center" class="button_container">
                    <? echo load_submit_buttons($permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                </td>
            </tr>
        </table>
    </fieldset>
    <?
	exit();
}

if ($action=="save_update_delete_bundle")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==1)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$hidden_cutting_no."'");
		
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
		
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$hidden_cutting_no."'");
		$cut_on_prifix	=$cutData[0][csf('cut_num_prefix_no')];
		$job_no			=$cutData[0][csf('job_no')];
		$sql_sewing_operation=sql_select("select c.id,
												 b.row_sequence_no,
												c.code,
												c.operation_name,
												c.bodypart_id 
										from wo_po_details_master j,
											ppl_gsd_entry_mst a,
											ppl_gsd_entry_dtls b , 
											lib_sewing_operation_entry c 
										where 
											j.job_no='".$job_no."' and  
											j.style_ref_no=a.style_ref and 
											a.bulletin_type=4 and  
											a.id=b.mst_id and 
											b.lib_sewing_id=c.id and 
											c.gmt_item_id =$gmt_id and 
											j.status_active=1 and 
											j.is_deleted=0  and 
											a.status_active=1 and 
											a.is_deleted=0 and 
											b.status_active=1 and 
											b.is_deleted=0 and 
											c.production_type=2 and
											c.status_active=1 and 
											c.is_deleted=0
											order by b.row_sequence_no,");

		$previous_operation_barcode_data=sql_select("select 
														mst_barcode_no, 
														operation_id
														barcode_no,
														barcode_year,
														barcode_prifix 
													from 
														ppl_cut_lay_bundle_operation 
													where 
														dtls_id=".$bundle_dtls_id." and 
														status_active=1 and 
														is_deleted=0 ");
		foreach($previous_operation_barcode_data as $op_val)
		{
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['year']	=$op_val[csf("barcode_year")];
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['prifix']	=$op_val[csf("barcode_prifix")];
			$operation_barcode_arr[$op_val[csf("mst_barcode_no")]][$op_val[csf("operation_id")]]['barcode']	=$op_val[csf("barcode_no")];
		}

		$previous_barcode_data=sql_select("select 
												bundle_no,
												barcode_no,
												barcode_year,
												barcode_prifix 
											from 
												ppl_cut_lay_bundle 
											where 
												mst_id=".$bundle_mst_id."  and  
												dtls_id=".$bundle_dtls_id." and 
												status_active=1 and 
												is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']		=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']	=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']	=$b_val[csf("barcode_no")];
		}
		
		
		$id=return_next_id("id","ppl_cut_lay_bundle",1);
		

		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,is_excess,order_id,color_type_id,deleted_message,inserted_by,insert_date,status_active,is_deleted,hold";
		$feild_array_operation="id, bundle_id,dtls_id,mst_barcode_no, operation_id, operation_name, operation_code, barcode_no,barcode_prifix,barcode_year, status_active, is_deleted";
		
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		$operation_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle_operation","barcode_year=$year_id","suffix_no");
		$data_array_bundle_operation='';
		$id_operation=return_next_id("id", "ppl_cut_lay_bundle_operation", 1);
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$new_bundle_no		="txtBundleNo_".$j;
			$new_bundle_qty		="txtBundleQty_".$j;
			$hidden_bundle_qty	="hiddenSizeqty_".$j;
			$new_bundle_from	="txtBundleFrom_".$j;
			$new_bundle_to		="txtBundleTo_".$j;
			$new_bundle_size_id	="txtSizeId_".$j;
			$new_update_flag	="hiddenUpdateFlag_".$j;
			$hidden_size_id		="txtHiddenSizeId_".$j;
			$new_update_value	="hiddenUpdateValue_".$j;
			$hiddenCountry		="cboCountry_".$j;
			$hiddenCountryType	="hiddenCountryType_".$j;
		
			$isExcess			="isExcess_".$j;
			$cboPoId			="cboPoId_".$j;
			$check_status			="status_active_".$j;
			$is_deleted=0;
			$status_active=1;
			if(isset($$check_status))
			{
				if($$check_status*1==1)
				{
					$hold=$$check_status;
					
					$deleted_message=str_replace("'", "", $txt_message);
				}
				else 
				{
					$hold=0;
					$deleted_message="";
				}
			}
			else{
				
				$hold=0;
				$deleted_message="";
			}
			$bundle_prif		=explode("-",$$new_bundle_no);
			$new_bundle_prif_no	=explode('-',$bundle_prif[3]);
			$new_bundle_prifix	=$bundle_prif[0]."-".$bundle_prif[1]."-".$bundle_prif[2];
			$update_flag		=0;
			$update_flag_value	="";
			//echo $$new_update_flag."**".$$new_update_value;die;
			if(str_replace("'","",$$new_update_flag)!=1)
			{
				if(str_replace("'","",$$new_update_flag)==6)
				{
					if(trim($$hidden_bundle_qty)!=trim($$new_bundle_qty))	
					{
						$update_flag_value	="".str_replace("'","",$$hidden_bundle_qty)."";
						$update_flag		=1;
					}
					else
					{
						$update_flag_value	="";
					}
					if(trim($$hidden_size_id)!=trim($$new_bundle_size_id))	
					{
						$update_flag_value.="**".str_replace("'","",$$new_bundle_size_id)."";
						$update_flag		=1;
					}
					else
					{
						$update_flag_value.="**";
					}
				}
			}
			else
			{
				$update_flag		=1;
				$update_flag_value	=$$new_update_value;
			}
			
			if(empty($previous_barcode_arr[str_replace("'","",$$new_bundle_no)]))
			{
				$barcode_suffix_no		=$barcode_suffix_no+1;
				$up_barcode_suffix		=$barcode_suffix_no;
				$up_barcode_year		=$year_id;
				$barcode_no				=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
			}
			else
			{
				$up_barcode_suffix		=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['prifix'];
				$up_barcode_year		=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['year'];
				$barcode_no				=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['barcode'];
			}
			
			
				//echo $update_flag_value."***";die;
			if($data_array!="") $data_array.=",";
			 $data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."','".$$new_bundle_from."','".$$new_bundle_to."','".$$new_bundle_qty."',".$update_flag.",'".$update_flag_value."','".str_replace("'","",$$hiddenCountryType)."','".str_replace("'","",$$hiddenCountry)."','".str_replace("'","",$$isExcess)."','".str_replace("'","",$$cboPoId)."',".$color_type_id.",'".$deleted_message."','".$user_id."','".$pc_date_time."',".$status_active.",".$is_deleted.",".$hold.")";
			if(count($sql_sewing_operation)>0)
			{
				foreach ($sql_sewing_operation as $op_val) {
					if(empty($operation_barcode_arr[$barcode_no][$op_val[csf('id')]]))
					{
						$operation_suffix_no	=$operation_suffix_no+1;
						$up_operation_suffix	=$operation_suffix_no;
						$up_operation_year		=$year_id;
						$operation_barcode_no	=$year_id."253".str_pad($up_operation_suffix,10,"0",STR_PAD_LEFT);
					}
					else
					{
						$up_operation_suffix	=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['prifix'];
						$up_operation_year		=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['year'];
						$operation_barcode_no	=$operation_barcode_arr[$barcode_no][$op_val[csf('id')]]['barcode'];
					}
				
					
					//$operation_barcode_no			=$year_id."253".str_pad($operation_suffix_no,10,"0",STR_PAD_LEFT);
					if($data_array_bundle_operation!="") $data_array_bundle_operation.= ",";
					$data_array_bundle_operation.="(".$id_operation.",".$id.",".$bundle_dtls_id.",'".$barcode_no."',".$op_val[csf('id')].",'".$op_val[csf('operation_name')]."','".$op_val[csf('code')]."','".$operation_barcode_no."','".$up_operation_suffix."','".$up_operation_year."',".$status_active.",".$is_deleted.")";
					$id_operation++;
				}
			}
			$id = $id+1;
		}
		//echo "10**".$data_array_bundle_operation;die;
		$rID2=true;
		$rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id." and dtls_id=".$bundle_dtls_id."",0);
		$delete_bundle_operation=execute_query("delete from ppl_cut_lay_bundle_operation where dtls_id=".$bundle_dtls_id."",0);
		$rID1=sql_insert("ppl_cut_lay_bundle",$field_array,$data_array,1);
		if($data_array_bundle_operation!="")
		{
			//echo "10**insert into ppl_cut_lay_bundle_operation($feild_array_operation) values".$data_array_bundle_operation."**".$rID2;die;
			$rID2=sql_insert("ppl_cut_lay_bundle_operation",$feild_array_operation,$data_array_bundle_operation,0);
		}

		//echo "10** $rID && $rID1 && $rID2 && $delete_bundle_operation ";die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $delete_bundle_operation )
			{
				mysql_query("COMMIT");  
				echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $delete_bundle_operation)
			{
				oci_commit($con);   
				echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
			}
		}
			
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here=======================================================================
	{
		
	}		
}
// ---------------------------------bundle for text file-------------------------------------------------------------
if($action=="report_bundle_text_file")
{
	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

	$yarn_color_result=sql_select("select color_name,lot from ppl_cut_lay_prod_dtls where mst_id=$data[2] and 		dtls_id=$detls_id");

	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])	order by b.bundle_sequence,	a.id");
	
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");

	foreach($sql_name as $value)
	{
		$product_dept_name 					=$value[csf('product_dept')];
		$style_name 						=$value[csf('style_ref_no')];
		$buyer_name 						=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 	=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select  entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_name 		=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}

	foreach (glob(""."*.zip") as $filename)
	{			
		@unlink($filename);
    }

    $i=1;
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	}

	$batch_number="";
	if ($batch_no!="") $batch_number="(".$batch_no.")";
	foreach($color_sizeID_arr as $val)
   {
			$file_name="NORSEL-IMPORT_".$i;
			$po_number=$po_number_arr[$val[csf('order_id')]];
			$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
			$txt ="Norsel_imp\r\n1\r\n";
			$txt .=$val[csf("barcode_no")]."\r\n";
			$txt .=$style_name."\r\n";
			$txt .= $po_number."\r\n";
			$txt .= $color_library[$val[csf("color_id")]]."\r\n";
			$txt .= "LRN: ". $cut_prifix."\r\n";
			$txt .= "S:".$size_arr[$val[csf("size_id")]]."\r\n";
			$txt .="B. Qty.: ".$val[csf("size_qty")]." Pcs";
					
			fwrite($myfile, $txt);
			fclose($myfile);
		$i++;
	 }

	 foreach (glob(""."*.txt") as $filenames){			
	   $zip->addFile($file_folder.$filenames);			// Adding files into zip
	}
	$zip->close();
	     
	foreach (glob(""."*.txt") as $filename) {			
			@unlink($filename);
		}
	echo "norsel_bundle";
	exit();
}
//----------------------------------bundle qty update finish----------------------------------------------------------

//bundle_bar_code ****************************************************************************************
if($action=="print_barcode_operation")
{	
	//echo "1000".$data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	//$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
	//	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	//$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

	$yarn_color_result=sql_select("select color_name, lot from ppl_cut_lay_prod_dtls where mst_id=$data[2] and dtls_id=$detls_id");

	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id");
										
	$color_sizeID_operation_sql=sql_select("select a.id, a.mst_barcode_no, a.operation_id, a.operation_name, a.operation_code, b.department_code, a.barcode_no, a.bundle_id, a.status_active, a.is_deleted, a.barcode_prifix, a.barcode_year from ppl_cut_lay_bundle_operation a, lib_sewing_operation_entry b where a.operation_id=b.id and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$detls_id and a.status_active=1 and a.is_deleted=0 and b.production_type=2 order by a.id desc");

	//print_r($color_sizeID_operation_sql);die;
	foreach ($color_sizeID_operation_sql as  $value) {
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_name']=$value[csf('operation_name')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_code']=$value[csf('operation_code')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['dep_code']=$value[csf('department_code')];
		//$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_name']=$value[csf('operation_name')];
	}
	//print_r($color_sizeID_operation_arr);die;
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."' and a.is_confirmed=1");

	foreach($sql_name as $value)
	{
		$product_dept_name 					=$value[csf('product_dept')];
		$style_name 						=$value[csf('style_ref_no')];
		$buyer_name 						=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 	=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_name 		=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					'A4',		// array(65,210),    // format - A4, for example, default ''
					 6,     // font size - default 0
					 '',    // default font family
					 8,    // margin_left
					 3,    // margin right
					 6,     // margin top
					 6,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 		=1;
	$cl=1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$page_break=4;
	//$mpdf->AddPage();
	foreach($color_sizeID_arr as $val)
	{
		//if($i!=1) $mpdf->AddPage();
		$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$po_number=$po_number_arr[$val[csf('order_id')]];
		$bundle_array[$i]=$val[csf("barcode_no")];
		if($i==1 && $cl!=1)
		{
			//AddPage($orientation='',$condition='', $resetpagenum='', $pagenumstyle='', $suppress='',$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0,$pagesel='',$newformat='') 
			$mpdf->AddPage('P',    // mode - default '' L=lndscape / P=portrait
				'A4',		// array(65,210),    // format - A4, for example, default ''
				 '',     // reset page numbering
				 '',    // reset page number styiling e.g. I II, A B
				 '',    // suppress page number from new page onwards
				 '',    // margin_left
				 '',     // margin right
				 '',    // margin top
				 0,     // margin bottom
				 0,     // margin header margin footer
				 '');
	        $html='<br/>';
		}
		$html.='<table cellpadding="0" cellspacing="0" width="985" class="ff" style="border:1pt solid white; margin:1px 0px 4px 1px;" rules="all" id="">			    	
			        <tr >
			            <td width="18%">
							<table  width="" style="font-size:11px; " border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td  width="40%"  >
										<table  width="" border="0">
											<tr>
												<td  width=""  >
												<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60"></div></td>
											</tr>
										</table>
									</td>
									<td  width="60%"  >
										<table  width="">
											<tr>
												<td width="80">'.$val[csf("barcode_no")].'</td>
											
											</tr>
											<tr>
												<td width="70"> S:'.$size_arr[$val[csf("size_id")]].'</td>
											</tr>
										</table>
									</td>
								</tr>
							
							</table>
			            </td>
			            <td colspan="4">
							<table  width="" style="font-size:10px; " border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td >    STYLE : '.$style_name.' &nbsp;&nbsp;&nbsp; JOB NO : '.$data[1].' &nbsp;&nbsp;&nbsp;  GMT. Color :  '.$color_library[$data[5]].' &nbsp;&nbsp;&nbsp;    PO NO : '.$po_number_arr[$val[csf('order_id')]].' &nbsp;&nbsp;&nbsp; LOT RATIO NO : '.$cut_prifix.'</td>
								</tr>
								<tr>
									<td >
										<table  width="" >
											<tr>
												<td width="70">Y. COLOR</td>';
												foreach ($yarn_color_result as  $yarn_value) {
													$html.='<td width="" align="center">&nbsp;'.$yarn_value[csf("color_name")].'&nbsp;</td>';
												}
											$html.='</tr>
											<tr>
												<td width="70">Y. LOT</td>';
												foreach ($yarn_color_result as $yarn_value){
													$html.='<td width="" align="center">&nbsp;'.$yarn_value[csf("lot")].'&nbsp;</td>';
												}
											$html.='</tr>
										</table>
									</td>
								</tr>
								
							</table>
			            </td>

			        </tr>
			    </table>
			    <table cellpadding="0" cellspacing="0" width="985" class=""  rules="all" id="" style="margin-top: 6px;">';


			        for ($x = 0; $x <= 3; $x++)
			        {
			        	$html.='<tr>';
						
						 $j=1;
				         foreach ($color_sizeID_operation_arr[$val[csf("barcode_no")]] as $operation_barcode => $op_bar_val) {
				         	if($j<=4)
				         	{
					         	$filename = $PNG_TEMP_DIR.'test'.md5($operation_barcode).'.png';
		    					QRcode::png($operation_barcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
								$html.='<td width="16%">
									<table  width="100%" style="font-size:8px; margin:8px 0px 4px 1px; border:1pt solid white; border-spacing: 15px;" cellpadding="0" cellspacing="0">
										<tr>
											<td  width="30%"  >
												<table  width="" border="0">
													<tr>
														<td  width=""  >
														<div id="div_'.$j.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60"></div></td>
													</tr>
												</table>
											</td>
											<td  width="70%"  >
												<table  width="100%">
													<tr>
														<td style="overflow:hidden; width: 4.6cm;">TN '.$operation_barcode.' &nbsp; &nbsp; 01/'.$size_arr[$val[csf("size_id")]].'</td>									
													</tr>
													<tr>
														<td width="">JOB '.$data[1].' S:'.$size_arr[$val[csf("size_id")]].'</td>
													</tr>
													<tr>
														<td width=""> '.$machine_category[$op_bar_val["dep_code"]].':'.$op_bar_val["op_code"].'</td>
													</tr>
													<tr>
														<td width="">'.count($color_sizeID_operation_arr[$val[csf("barcode_no")]]).' PCE:'.$val[csf("size_qty")].'</td>
													</tr>

												</table>
											</td>
										</tr>
									
									</table>
					            </td>';
					            unset($color_sizeID_operation_arr[$val[csf("barcode_no")]][$operation_barcode]);
					            $j++;
				        	}
						}
					         
				       	if($j<=4)
			         	{
				
							$html.='<td width="16%">
								<table  width="100%" style="font-size:8px; margin:4px 0px 4px 4px;border:1pt solid white;" cellpadding="0" cellspacing="0">
									<tr>
										<td  width="30%"  >
											<table  width="" border="0">
												<tr>
													<td  width=""  >
													<div id="div_'.$j.'"></div></td>
												</tr>
											</table>
										</td>
										<td  width="70%"  >
											<table  width="100%">
												<tr>
													<td width=""> &nbsp; </td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>
												<tr>
													<td width="">  &nbsp;</td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>

											</table>
										</td>
									</tr>
								
								</table>
				            </td>';
				            $j++;
			        	}
			        	if($j<=4)
			         	{
				
							$html.='<td width="16%">
								<table  width="100%" style="font-size:8px; margin:4px 0px 4px 4px;border:1pt solid white;" cellpadding="0" cellspacing="0">
									<tr>
										<td  width="30%"  >
											<table  width="" border="0">
												<tr>
													<td  width=""  >
													<div id="div_'.$j.'"></div></td>
												</tr>
											</table>
										</td>
										<td  width="70%"  >
											<table  width="100%">
												<tr>
													<td width=""> &nbsp; </td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>
												<tr>
													<td width="">  &nbsp;</td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>

											</table>
										</td>
									</tr>
								
								</table>
				            </td>';
				            $j++;
			        	}
			        	if($j<=4)
			         	{
							//height="200"
							$html.='<td width="16%">
								<table  width="100%"  style="font-size:8px; margin:4px 0px 4px 4px;border:1pt solid white;" cellpadding="0" cellspacing="0">
									<tr>
										<td  width="30%"  >
											<table  width="" border="0">
												<tr>
													<td  width=""  >
													<div id="div_'.$j.'"></div></td>
												</tr>
											</table>
										</td>
										<td  width="70%"  >
											<table  width="100%">
												<tr>
													<td width=""> &nbsp;</td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>
												<tr>
													<td width="">  &nbsp;</td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>

											</table>
										</td>
									</tr>
								
								</table>
				            </td>';
		
				            $j++;
			        	}
			        	if($j<=4)
			         	{
				         
							$html.='<td width="16%">
								<table  width="100%" style="font-size:8px; margin:4px 0px 4px 6px;border:1pt solid white;" cellpadding="0" cellspacing="0">
									<tr>
										<td  width="30%"  >
											<table  width="" border="0">
												<tr>
													<td  width=""  >
													<div id="div_'.$j.'"></div></td>
												</tr>
											</table>
										</td>
										<td  width="70%"  >
											<table  width="100%">
												<tr>
													<td width=""> &nbsp; </td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>
												<tr>
													<td width="">  &nbsp;</td>
												</tr>
												<tr>
													<td width=""> &nbsp;</td>
												</tr>

											</table>
										</td>
									</tr>
								
								</table>
				            </td>';
				       
				            $j++;
			        	}
			        	
						$html.='</tr>';
			        }   

			           

			   
			    $html.='</table>';


	    if($i==$page_break)
	    {
	     	$mpdf->WriteHTML($html);
	        $i=0; 
	        $html=''; 
	           
	    }
		$i++;
		$cl++;
		
	} 
	
	$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}

//bundle_bar_code ****************************************************************************************
if($action=="print_barcode_operation_bundle_sticker")
{	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");

	$yarn_color_result=sql_select("select color_name, lot from ppl_cut_lay_prod_dtls where mst_id=$data[2] and dtls_id=$detls_id");

	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id");
										
	$color_sizeID_operation_sql=sql_select("select a.id, a.mst_barcode_no, a.operation_id, a.operation_name, a.operation_code, b.department_code, a.barcode_no, a.bundle_id, a.status_active, a.is_deleted, a.barcode_prifix, a.barcode_year from ppl_cut_lay_bundle_operation a, lib_sewing_operation_entry b where a.operation_id=b.id and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$detls_id and a.status_active=1 and a.is_deleted=0 and b.production_type=2 order by a.id desc");

	//print_r($color_sizeID_operation_sql);die;
	foreach ($color_sizeID_operation_sql as  $value) {
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_name']=$value[csf('operation_name')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_code']=$value[csf('operation_code')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['dep_code']=$value[csf('department_code')];
		//$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_name']=$value[csf('operation_name')];
	}
	//print_r($color_sizeID_operation_arr);die;
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."' and a.is_confirmed=1");

	foreach($sql_name as $value)
	{
		$product_dept_name 					=$value[csf('product_dept')];
		$style_name 						=$value[csf('style_ref_no')];
		$buyer_name 						=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 	=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_name 		=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('A4',    // mode - default ''
					array(190,65),	// array(65,210),    // format - A4, for example, default ''
					 6,     // font size - default 0
					 '',    // default font family
					 6,    	// margin_left
					 6,    	// margin right
					 6,     // margin top
					 3,    	// margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 	=1;
	$cl =1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$page_break=4;
	$mpdf->autoPageBreak = false;
	$mpdf->AddPage();
	foreach($color_sizeID_arr as $val)
	{
		$counter = 0;
		//if($i!=1) $mpdf->AddPage();
		$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$po_number=$po_number_arr[$val[csf('order_id')]];
		$bundle_array[$i]=$val[csf("barcode_no")];
		
		$html.='
		<table cellpadding="0" cellspacing="0" class="ff"  rules="all" id="">			    	
			        <tr>
			            <td rowspan="5" valign="top" cellpadding="0" cellspacing="0">
							<table width="" style="font-size:11px; " border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table  width="" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" width=""><img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60"></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;"><p>'.$val[csf("barcode_no")].'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;"><p> S:'.$size_arr[$val[csf("size_id")]].'</p></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0"> 
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px; overflow:wrap;"><p>STYLE : '.$style_name.' &nbsp; JOB NO : '.$data[1].' &nbsp;  GMT. Color :  '.$color_library[$data[5]].' &nbsp;    PO NO : '.$po_number_arr[$val[csf('order_id')]].' &nbsp; </p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px; overflow:wrap;"> <p>LOT RATIO NO : '.$cut_prifix.'&nbsp;Y. COLOR:';foreach ($yarn_color_result as  $yarn_value) {$html.= $yarn_value[csf("color_name")];} $html.='</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px; overflow:wrap;"> <p>Y. LOT:';$lot="";foreach ($yarn_color_result as  $yarn_value) {$lot.= ($lot=="") ? $yarn_value[csf("lot")] : $yarn_value[csf("lot")].",";} $html.=$lot.'</p></td>
											</tr>
										</table>
									</td>
								</tr>
							
							</table>
			            </td>
			        </tr><tr>';
			        foreach ($color_sizeID_operation_arr[$val[csf("barcode_no")]] as $operation_barcode => $op_bar_val) 
			        {
			        	$counter++;
			        	$filename = $PNG_TEMP_DIR.'test'.md5($operation_barcode).'.png';
		    			QRcode::png($operation_barcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		    			
				        $html.='<td valign="top" style="margin:0px 0px 7.5590px 3.77952px;">';

				        $html.='<table cellpadding="0" cellspacing="0"> 
				        	<tr>
				        		<td valign="top" colspan="4" align="center"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60"></td>
				        	</tr>
				        	<tr>
								<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.89cm">TN '.$operation_barcode.' &nbsp; &nbsp; 01/'.$size_arr[$val[csf("size_id")]].'</td>									
							
								<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.89cm">JOB '.$data[1].' S:'.$size_arr[$val[csf("size_id")]].'</td>
							
								<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.89cm"> '.$machine_category[$op_bar_val["dep_code"]].':'.$op_bar_val["op_code"].'</td>
							
								<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.89cm">'.count($color_sizeID_operation_arr[$val[csf("barcode_no")]]).' PCE:'.$val[csf("size_qty")].'</td>
							</tr>
				        </table>

				        </td>';
				        if($counter==4)
				        {
				        	$html.='</tr><tr>';
				        	$counter=0;
				    	}
				    	
				    }
			    $html.='</tr></table>
			    <br clear="all">
			    ';
		$i++;
		$cl++;
		// $mpdf->AddPage();
	} 
	
	$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) 
	{			
		@unlink($filename);
	}
	$name = 'bundlStricker_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}

//bundle_bar_code ****************************************************************************************
if($action=="print_barcode_operation_bundle_sticker2") // update date : 19-10-2020
{	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$color_library=return_library_array( "SELECT id, color_name from lib_color where id=$data[5]", "id", "color_name");

	$yarn_color_result=sql_select("SELECT color_name, lot from ppl_cut_lay_prod_dtls where mst_id=$data[2] and dtls_id=$detls_id");

	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) and a.status_active=1 and a.is_deleted=0 order by b.bundle_sequence, a.id");
										
	$color_sizeID_operation_sql=sql_select("SELECT a.id, a.mst_barcode_no, a.operation_id, a.operation_name, a.operation_code, b.department_code, a.barcode_no, a.bundle_id, a.status_active, a.is_deleted, a.barcode_prifix, a.barcode_year from ppl_cut_lay_bundle_operation a, lib_sewing_operation_entry b where a.operation_id=b.id and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$detls_id and a.status_active=1 and a.is_deleted=0 and b.production_type=2 order by a.barcode_no DESC");

	//print_r($color_sizeID_operation_sql);die;
	$operation_array = array();
	foreach ($color_sizeID_operation_sql as  $value) 
	{
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_name']=$value[csf('operation_name')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['op_code']=$value[csf('operation_code')];
		$color_sizeID_operation_arr[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]['dep_code']=$value[csf('department_code')];
		$operation_array[$value[csf('mst_barcode_no')]][$value[csf('barcode_no')]]++;

	}
	//print_r($color_sizeID_operation_arr);die;
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."' and a.is_confirmed=1");

	foreach($sql_name as $value)
	{
		$product_dept_name 					=$value[csf('product_dept')];
		$style_name 						=$value[csf('style_ref_no')];
		$buyer_name 						=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 	=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_name 		=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('A4',    // mode - default ''
					array(189,89),	// array(190,65),    // format - A4, for example, default ''190
					 12,     // font size - default 0
					 '',    // default font family
					 1,    	// margin_left
					 0,    	// margin right
					 0,     // margin top
					 0,    	// margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 	=1;
	$cl =1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$page_break=3;
	$mpdf->autoPageBreak = false;
	// $mpdf->AddPage();
	foreach($color_sizeID_arr as $val)
	{
		$mpdf->AddPage('A4',    // mode - default ''
					array(189,89),	// array(190,65),    // format - A4, for example, default ''190
					 12,     // font size - default 0
					 '',    // default font family
					 1,    	// margin_left
					 0,    	// margin right
					 0,     // margin top
					 0,    	// margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');
		$counter = 0;
		//if($i!=1) $mpdf->AddPage();
		//if($i!=1) $mpdf->AddPage();
		$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$po_number=$po_number_arr[$val[csf('order_id')]];
		$bundle_array[$i]=$val[csf("barcode_no")];
		$operation = array_sum($operation_array[$val[csf("barcode_no")]]);

		if($operation<4)
		{
			$html.='
			<table cellpadding="0" cellspacing="0" class="ff"  rules="all" id="">			    	
			        <tr>
			            <td rowspan="4" valign="top" cellpadding="0" cellspacing="0" height="70">
							<table width="" style="font-size:11px; " border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table  width="" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" width=""><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="68"></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p>'.$val[csf("barcode_no")].'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p> S:'.$size_arr[$val[csf("size_id")]].'</p></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0"> 
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"><p>STYLE : '.$style_name.' &nbsp; JOB NO : '.$data[1].' &nbsp;</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>PO NO : '.$po_number_arr[$val[csf('order_id')]].' &nbsp; LOT RATIO NO : '.$cut_prifix.'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>Y. COLOR:';
												$c=1;
												foreach ($yarn_color_result as  $yarn_value) 
												{
													if($c <= 2)
													{
														$html.= ($c==1) ? $yarn_value[csf("color_name")] : ",".$yarn_value[csf("color_name")];
													}
													$c++;
												} 
												$html.='</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>GMT. Color :  '.$color_library[$data[5]].' &nbsp; Y. LOT:';
												$lot="";
												foreach ($yarn_color_result as  $yarn_value) 
												{
													$lot.= ($lot=="") ? $yarn_value[csf("lot")] : ",".$yarn_value[csf("lot")];
												} 
												$html.=$lot.'</p></td>
											</tr>
										</table>
									</td>
								</tr>
							
							</table>
			            </td>
			            <td>';
			            $item_per_page=12;
			        $qr=1;
			        foreach ($color_sizeID_operation_arr[$val[csf("barcode_no")]] as $operation_barcode => $op_bar_val) 
			        {
			        	if($qr <= $item_per_page)
			        	{
				        	$counter++;
				        	$filename = $PNG_TEMP_DIR.'test'.md5($operation_barcode).'.png';
			    			QRcode::png($operation_barcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			    			
					        $html.='<td height="70" valign="top" style="margin:0px 0px 0px 0px;">';/* 0px 0px 5.5590px 3.77952px */

					        $html.='<table cellpadding="0" cellspacing="0"> 
					        	<tr>
					        		<td valign="top" colspan="4" align="center"><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="68"></td>
					        	</tr>
					        	<tr>
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.6cm">TN '.$operation_barcode.' &nbsp; &nbsp; 01/'.$size_arr[$val[csf("size_id")]].'</td>									
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.6cm">JOB '.$data[1].' S:'.$size_arr[$val[csf("size_id")]].'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.6cm"> '.substr($machine_category[$op_bar_val["dep_code"]],0,3).':'.substr($op_bar_val["op_name"],0,30).'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:10px;height:4.6cm">'.count($color_sizeID_operation_arr[$val[csf("barcode_no")]]).' PCE:'.$val[csf("size_qty")].'</td>
								</tr>
					        </table>

					        </td>';
					        $qr++;
					    }
				        if($counter==3)
				        {
				        	$html.='</tr><tr>';
				        	$counter=0;
				    	}
				    	
				    }			            
			        
			    $html.='</td></tr></table>
			    <br clear="all">
			    ';
				$mpdf->WriteHTML($html);
				$html = '';
				
		}
		else if($operation<5)
		{
			$html.='
			<table cellpadding="0" cellspacing="0" class="ff"  rules="all" id="">			    	
			        <tr>
			            <td rowspan="4" valign="top" cellpadding="0" cellspacing="0" style="min-height:500px;">
							<table width="" style="font-size:11px; " border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table  width="" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" width=""><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="70" width="88"></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p>'.$val[csf("barcode_no")].'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p> S:'.$size_arr[$val[csf("size_id")]].'</p></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0"> 
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; height:300px; overflow:wrap;"><p>STYLE : '.$style_name.' &nbsp; JOB NO : '.$data[1].' &nbsp;</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>PO NO : '.$po_number_arr[$val[csf('order_id')]].' &nbsp; LOT RATIO NO : '.$cut_prifix.'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>Y. COLOR:';
												$c=1;
												foreach ($yarn_color_result as  $yarn_value) 
												{
													if($c <= 2)
													{
														$html.= ($c==1) ? $yarn_value[csf("color_name")] : ",".$yarn_value[csf("color_name")];
													}
													$c++;
												} 
												$html.='</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>GMT. Color :  '.$color_library[$data[5]].' &nbsp; Y. LOT:';$lot="";
												$lot = "";
												foreach ($yarn_color_result as  $yarn_value) 
												{
													$lot.= ($lot=="") ? $yarn_value[csf("lot")] : ",".$yarn_value[csf("lot")];
												} 
												$html.=$lot.'</p></td>
											</tr>
										</table>
									</td>
								</tr>
							
							</table>
			            </td>
			            <td>';
			            $item_per_page=12;
			        $qr=1;
			        foreach ($color_sizeID_operation_arr[$val[csf("barcode_no")]] as $operation_barcode => $op_bar_val) 
			        {
			        	if($qr <= $item_per_page)
			        	{
				        	$counter++;
				        	$filename = $PNG_TEMP_DIR.'test'.md5($operation_barcode).'.png';
			    			QRcode::png($operation_barcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			    			
					        $html.='<td valign="top" style="margin:0px 0px 0px 0px;">';/* 0px 0px 5.5590px 3.77952px */

					        $html.='<table cellpadding="0" cellspacing="0"> 
					        	<tr>
					        		<td valign="top" colspan="4" align="center"><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="70" width="88"></td>
					        	</tr>
					        	<tr>
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">TN '.$operation_barcode.' &nbsp; &nbsp; 01/'.$size_arr[$val[csf("size_id")]].'</td>									
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">JOB '.$data[1].' S:'.$size_arr[$val[csf("size_id")]].'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm"> '.substr($machine_category[$op_bar_val["dep_code"]],0,3).':'.substr($op_bar_val["op_name"],0,30).'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">'.count($color_sizeID_operation_arr[$val[csf("barcode_no")]]).' PCE:'.$val[csf("size_qty")].'</td>
								</tr>
					        </table>

					        </td>';
					        $qr++;
					    }
				        if($counter==4)
				        {
				        	$html.='</tr><tr>';
				        	$counter=0;
				    	}
				    	
				    }			            
			        
			    $html.='</td></tr></table>
			    <br clear="all">
			    ';
				$mpdf->WriteHTML($html);
				$html = '';
		}
		else if($operation >4 )
		{
			$html.='
			<table cellpadding="0" cellspacing="0" class="ff"  rules="all" id="">			    	
			        <tr>
			            <td rowspan="4" valign="top" cellpadding="0" cellspacing="0">
							<table width="" style="font-size:11px; " border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table  width="" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" width=""><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="70" width="88"></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p>'.$val[csf("barcode_no")].'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;"><p> S:'.$size_arr[$val[csf("size_id")]].'</p></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table  width="" cellpadding="0" cellspacing="0"> 
											<tr>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"><p>STYLE : '.$style_name.' &nbsp; JOB NO : '.$data[1].' &nbsp;</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>PO NO : '.$po_number_arr[$val[csf('order_id')]].' &nbsp; LOT RATIO NO : '.$cut_prifix.'</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>Y. COLOR:';
												$c=1;
												foreach ($yarn_color_result as  $yarn_value) 
												{
													if($c <= 2)
													{
														$html.= ($c==1) ? $yarn_value[csf("color_name")] : ",".substr($yarn_value[csf("color_name")],0,20);
													}
													$c++;
												}  
												$html.='</p></td>
												<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px; overflow:wrap;"> <p>GMT. Color :  '.$color_library[$data[5]].' &nbsp; Y. LOT:';$lot="";
												$lot = "";
												foreach ($yarn_color_result as  $yarn_value) 
												{
													$lot.= ($lot=="") ? $yarn_value[csf("lot")] : ",".$yarn_value[csf("lot")];
												} 
												$html.= substr($lot,0,22).'</p></td>
											</tr>
										</table>
									</td>
								</tr>
							
							</table>
			            </td>
			        </tr><tr>';
			        $item_per_page=12;
			        $qr=1;
			        foreach ($color_sizeID_operation_arr[$val[csf("barcode_no")]] as $operation_barcode => $op_bar_val) 
			        {
			        	if($qr <= $item_per_page)
			        	{
				        	$counter++;
				        	$filename = $PNG_TEMP_DIR.'test'.md5($operation_barcode).'.png';
			    			QRcode::png($operation_barcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			    			
					        $html.='<td valign="top" style="margin:0px 0px 0px 0px;">';/* 0px 0px 5.5590px 3.77952px */

					        $html.='<table cellpadding="0" cellspacing="0"> 
					        	<tr>
					        		<td valign="top" colspan="4" align="center"><img style="margin-top:7px;" src="'.$PNG_WEB_DIR.basename($filename).'" height="70" width="88"></td>
					        	</tr>
					        	<tr>
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">TN '.$operation_barcode.' &nbsp; &nbsp; 01/'.$size_arr[$val[csf("size_id")]].'</td>									
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">JOB '.$data[1].' S:'.$size_arr[$val[csf("size_id")]].'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm"> '.substr($machine_category[$op_bar_val["dep_code"]],0,3).':'.substr($op_bar_val["op_name"],0,30).'</td>
								
									<td valign="bottom" style="text-rotate: 90;text-align:center;font-size:12px;height:6.1cm">'.count($color_sizeID_operation_arr[$val[csf("barcode_no")]]).' PCE:'.$val[csf("size_qty")].'</td>
								</tr>
					        </table>

					        </td>';
					        $qr++;
					    }
				        if($counter==4)
				        {
				        	$html.='</tr><tr>';
				        	$counter=0;
				    	}
				    	
				    }
			    $html.='</tr></table>
			    <br clear="all">
			    ';
				$mpdf->WriteHTML($html);
				$html = '';
		}
		$i++;
		$cl++;
		// $mpdf->AddPage();
	} 
	
	// $mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) 
	{			
		@unlink($filename);
	}
	$name = 'bundlStricker_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}

//bundle_bar_code ****************************************************************************************
if($action=="print_barcode_fivePointEight")
{	
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

	$yarn_color_result=sql_select("select 
										color_name,
										lot 
									from 
										ppl_cut_lay_prod_dtls 
									where 
										mst_id=$data[2] and
										dtls_id=$detls_id");

	$color_sizeID_arr=sql_select("select 
										a.id,
										a.size_id,
										a.bundle_no,
										a.barcode_no,
										a.order_id,
										a.number_start,
										a.number_end,
										a.size_qty,
										a.country_id,
										a.roll_no,
										b.bundle_sequence,
										b.color_id 
									from 
										ppl_cut_lay_bundle a, 
										ppl_cut_lay_size_dtls b 
									where 
										a.mst_id=b.mst_id and 
										a.dtls_id=b.dtls_id and 
										a.size_id=b.size_id and 
										a.id in ($data[0]) 
									order by 
										b.bundle_sequence,
										a.id");
	
	$bundle_array=array();
	$sql_name=sql_select("select 
							b.buyer_name,
							b.style_ref_no,
							b.product_dept,
							a.po_number,
							a.id 
						from 
							wo_po_details_master b,
							wo_po_break_down a 
						where 
							a.job_no_mst=b.job_no and 
							a.job_no_mst='".$data[1]."' and a.is_confirmed=1");

	foreach($sql_name as $value)
	{
		$product_dept_name 					=$value[csf('product_dept')];
		$style_name 						=$value[csf('style_ref_no')];
		$buyer_name 						=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 	=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select 
								entry_date,
								table_no,
								cut_num_prefix_no,
								batch_id,
								company_id,
								cutting_no 
							from 
								ppl_cut_lay_mst 
							where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_name 		=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}
	?>
    

    <?
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					 array(18,78),    // format - A4, for example, default ''
					 7,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 		=1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	//$mpdf->AddPage();
	foreach($color_sizeID_arr as $val)
	{
		//if($i!=1) $mpdf->AddPage();
		$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$po_number=$po_number_arr[$val[csf('order_id')]];
		$bundle_array[$i]=$val[csf("barcode_no")];
		$html.='<table  width="220" style="font-size:8px; " border="0" cellpadding="0" cellspacing="0">';
		$html.='<tr><td rowspan="3" width="60"  ><div id="div_'.$i.'">
			<img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60">
		</div></td><td width="70">'.$val[csf("barcode_no")].'</td><td width="70">'.$style_name.'</td></tr>';
		$html.='<tr><td>'.substr($po_number, 0, 20).'</td><td>'.substr($color_library[$val[csf("color_id")]], 0, 25).'</td></tr>';
		$html.='<tr><td>LRN: '.$cut_prifix.', S:'.$size_arr[$val[csf("size_id")]].'</td><td>B. Qty.: '.$val[csf("size_qty")].'Pcs</td></tr>';
		$html.='</table>';
		
		$i++;
		
	} 
	
	$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
	exit();
}

if($action=="print_barcode_first")
{	
	?>
    <style type="text/css" media="print">
       	 p{ page-break-after: always;}
    	</style>
    <?

	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	$yarn_color_result=sql_select("select color_name,lot from ppl_cut_lay_prod_dtls  where mst_id=$data[2] and dtls_id=$detls_id");
	//print_r($color_yarn);die;

	//echo "dfds dsf dsf dsf ";die;
	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence 
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."' and a.is_confirmed=1");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]]=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	
	if($data[7]=="") $data[7]=0;
	$i=1;

	$total_number_of_bundle=count($color_sizeID_arr);
	foreach($color_sizeID_arr as $val)
	{
		if($i%5==1)
		{
			if($total_number_of_bundle>=5) 	{ $table_width=12.5; $total_number_of_bundle=$total_number_of_bundle-5;}
			else							{$table_width=2.5*$total_number_of_bundle;}
			echo '<table style="width:'.$table_width.'in; " border="0" cellpadding="0" cellspacing="0"><tr>';
		}
		$po_number=$po_number_arr[$val[csf('order_id')]];
		echo '<td>';
		$bundle_array[$i]=$val[csf("barcode_no")];//$val[csf("bundle_no")]."_".
		echo '<table style="width: 2.5in;font-size:12px; " border="0" cellpadding="0" cellspacing="0">';
		$bundle="&nbsp;&nbsp;".$val[csf("bundle_no")];
		$title="S:".$size_arr[$val[csf("size_id")]].";Qty:".$val[csf("size_qty")].";B:".$val[csf("bundle_no")];

		echo '<tr><td rowspan="3" width="60"  ><div id="div_'.$i.'"></div></td><td>'.$title.'</td></tr>';
		echo '<tr><td>Style: '.$style_name.'</td></tr>';
		echo '<tr><td>MO No: '.$po_number.'</td></tr>';
		echo '</table>';
		echo '<table style="width: 2.5in;font-size:12px; " border="0" cellpadding="0" cellspacing="0">';
		echo '<tr><td colspan="2">Emp NO.: ,Clr. G.: '.$color_library[$data[5]].'</td></tr>';
		echo '<tr><td  width="1.5in">Color Code</td><td  width="1in">Lot/Batch No</td></tr>';
		$y_sl=1;
		foreach($yarn_color_result as $y_val)
		{
			echo '<tr><td width="1.5in">'.$y_sl.'.&nbsp; '.$y_val[csf("color_name")].'</td><td>'.$y_val[csf("lot")].'</td></tr>';
			$y_sl++;
		}
		
		echo '</table>';
		echo '<td>';
		if($i%5==0)
		{
			echo '</tr></table><br/>';
		}
		$i++;
		
	} 
	if($i%5!=1)
	{
		echo '</tr></table>';
	}  
	?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
	<script>
		var barcode_array =<? echo json_encode($bundle_array); ?>;
		for (var i in barcode_array) 
		{
			//generateBarcode(i,barcode_array[i]);
			 $('#div_'+i).qrcode({width: 50,height: 40, text: barcode_array[i]});
		}
	</script>
    <?
	exit();
}

if($action=="cut_lay_bundle_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );
	
	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$working_comp_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$working_location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$working_floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	 
	
	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];
	
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='".$dataArray[0][csf('job_no')]."' and is_confirmed=1","id","po_number" );
	$batch_no=$dataArray[0][csf('batch_no')];
	?>
	<div style="width:1000px; " align="center" >
	    <table width="990" cellspacing="0" align="center">
	         <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lot Ratio Bundle Information</u></strong></td>
	        </tr>
	         <tr>
	        	<td width="120"><strong>System Number:</strong></td><td width="160"><? echo $cut_no; ?></td>
	            <td width="120"><strong>Job No :</strong></td> <td width="160"><? echo $dataArray[0][csf('job_no')]; ?></td>
				<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
	        </tr>
	        <tr>
				 <td><strong>Gmt Item:</strong></td> <td width="160"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
	             <td><strong>Color :</strong></td><td width="160"><? echo$color_library[$dataArray[0][csf('color_id')]]; ?></td>
	             <td><strong>Manual Ratio No:</strong></td> <td width="160"><? echo $order_cut_no; ?></td>
	        </tr>

	        <tr>
	        
	             <td><strong>Total Ratio:</strong></td> <td width="160"><? echo $dataArray[0][csf('plies')]; ?></td>
	             <td><strong>Ratio Date:</strong></td><td width="160"><? echo $dataArray[0][csf('entry_date')]; ?></td>
	        </tr>
	        <tr>
	       		 <td><strong>Style Ref:</strong></td> <td width="160"><? echo $style_ref; ?></td>
	             <td><strong>Style Desc.:</strong></td> <td width="160"><? echo $style_desc; ?></td>
	             <td align="left" colspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr>
	       		 <td><strong>Working Company:</strong></td> <td width="160"><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	             <td><strong>Working Location:</strong></td> <td width="160"><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>

	             <td><strong>Working Floor:</strong></td> <td width="160"><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	        </tr>
	    </table>
	    <br>
	     <script type="text/javascript" src="../../../js/jquery.js"></script>
	     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	     <script>

				function generateBarcode( valuess ){
					   
						var value = valuess;
						var btype = 'code39';
						var renderer ='bmp';
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
						 value = {code:value, rect: false};
						$("#barcode_img_id").show().barcode(value, btype, settings);
					} 
				   generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		 </script>
		<div style="width:1200px;">
	    	<table align="center" cellspacing="0" width="1180" border="1" rules="all" class="rpt_table" >
	              <thead bgcolor="#dddddd" align="center">
						<th></th>
						<th colspan="6"></th>
						<th>Bundle</th>
						<th colspan="2">RMG Number</th>
						<th colspan="3">QC</th>
						<th></th>
	              </thead>
	              <thead bgcolor="#dddddd" align="center">
	                      <th width="40">SL</th>
	                      <th width="100">System No</th>
	                      <th width="90">Order No</th>
	                      <th>Country Name</th>
	                      <th width="80">Bundle No</th>
	                      <th width="80">Barcode</th>
						  <th width="100">QR Code</th>
	                      <th width="70">Quantity</th>
	                      <th width="70">From</th>
	                      <th width="70">To</th>
	                      <th width="80">Size</th>
	                      <th width="40">REJ</th>
	                      <th width="40">REP</th>
	                      <th width="150">Remarks</th>
	                </thead>
	                <tbody> 
					<?  
						//  For QR CODE Start
							// $color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id 
							// from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id");
								$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
								$PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';
						
								foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
									@unlink($filename);
								}
						
								if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
								
								$filename = $PNG_TEMP_DIR.'test.png';
								$errorCorrectionLevel = 'L';
								$matrixPointSize = 4;
						
							include "../../../ext_resource/phpqrcode/qrlib.php"; 
							require_once("../../../ext_resource/mpdf60/mpdf.php");
						
							$mpdf = new mPDF('',    // mode - default ''
											'A4',		// array(65,210),    // format - A4, for example, default ''
												6,     // font size - default 0
												'',    // default font family
												8,    // margin_left
												3,    // margin right
												6,     // margin top
												6,    // margin bottom
												0,     // margin header
												0,     // margin footer
												'L');
						
						//  QR CODE End
						 $batchNo_arr=return_library_array( "select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=253","id","batch_no" );
						 
						 if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
	                     $size_data=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.id ASC");    
						 $j=1;
	                     foreach($size_data as $size_val)
	                     {
							$total_marker_qty_size=0;
							$bundle_data=sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id,a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.bundle_no ASC");
							//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
	                        foreach($bundle_data as $row)
	                        { 
								//  For QR CODE Start
								$filename = $PNG_TEMP_DIR.'test'.md5($row[csf("barcode_no")]).'.png';
								QRcode::png($row[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
								$po_number=$po_number_arr[$row[csf('order_id')]];
								$bundle_array[$i]=$row[csf("barcode_no")];
								//  For QR CODE End
	               	 			?>
	                           <tr>
	                               <td align="center"><? echo $j;  ?></td>
	                               <td align="center"><? echo $cut_no; ?></td>
	                               <td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
	                               <td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
	                              
	                               <td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  ?></td>
	                               <td align="center"><? echo $row[csf('barcode_no')];?></td>
								   <td align="center"><? echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" height="50" width="60">' ?></td>
	                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
	                               <td align="center"><? echo $row[csf('number_start')];  ?></td>
	                               <td align="center"><? echo $row[csf('number_end')];  ?></td>
	                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
	                               <td align="center"></td>
	                               <td align="center"></td>
	                               <td align="center"></td>
	                          </tr>
	               	 			<?
	                           $j++;
							   $total_marker_qty_size+=$row[csf('size_qty')];
							   $total_marker_qty+=$row[csf('size_qty')];
	                         }
	                       //  $total_marker_qty+=$size_val[csf('marker_qty')];
	                		?>		  
	                        <tr bgcolor="#eeeeee">
	                           <td align="center"></td>
	                           <td  colspan="9" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
	                           <td align="center"><? echo $total_marker_qty_size;  ?></td>
	                           <td align="center"></td>
	                           <td align="center"></td>
	                           <td align="center"></td>
	                        </tr>		  
	                <?	  
	                     }
	                ?>
	               <tr bgcolor="#BBBBBB">
	                   <td align="center"></td>
	                   <td  colspan="9"  align="right"> Total Ratio Qty.</td>
	                   <td align="center"><? echo $total_marker_qty;  ?></td>
	                   <td align="center"></td>
	                   <td align="center"></td>
	                   <td align="center"></td>
	                </tr>
				</tbody>
			</table>
	        <br>
			<? echo signature_table(9, $data[0], "900px"); ?>
			</div>
		</div> 
		<?
		exit(); 
}

if($action=="job_search_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	
	?>
	<script>
		function js_set_order(strCon ) 
		{
		document.getElementById('hidden_job_no').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1020" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	            <thead>
	                <tr>                	 
	                    <th width="150">Company name</th>
	                    <th width="150">Buyer name</th>
	                    <th width="60">Job No</th>
	                    <th width="100">Style Ref.</th>
	                    <th width="100">Order No</th>
	                    <th width="100">File No</th>
	                    <th width="100">Internal Ref. No</th>
	                    <th width="220">Date Range</th>
	                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                <tr class="general">                    
	                    <td><?=create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1); ?></td>
	                    <td>
	                             <?  $sql="select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$cbo_company_id order by a.buyer_name";
								echo create_drop_down( "cbo_buyer_name", 140,$sql,"id,buyer_name", 1, "-- Select --", 0, "", 0,"5,6,7","","","" );
	                            ?>
	                            <input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
	                            <input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
	                            <input type="hidden" id="hidden_prifix" name="hidden_prifix" />
	                            <input type="hidden" id="hidden_job_no" name="hidden_job_no" />
	                    </td>
	                    <td><input style="width:50px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  /></td>
	                    <td><input style="width:90px;" type="text"  class="text_boxes"   name="txt_style_no" id="txt_style_no"  /></td>
	                    <td><input style="width:90px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  /></td>
	                    <td><input style="width:80px;" type="text"  class="text_boxes"   name="txt_file_no" id="txt_file_no"  /></td>
	                    <td><input style="width:80px;" type="text"  class="text_boxes"   name="txt_internal_ref" id="txt_internal_ref"  /></td> 
	                    <td>
	                           <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
	                           <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
	                    </td>
	                    <td>
	                         <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style_no').value, 'create_job_search_list_view', 'search_div', 'yarn_lot_ratio_planning_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
	                    </td>
                    </tr>
                    <tr>                  
                        <td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?></td>
                    </tr>   
                </tbody>
            </table> 
	        <div align="center" valign="top" id="search_div"> </div>  
        </form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$file_no = $ex_data[7];
	$internal_reff = $ex_data[8];
	$style_reff = $ex_data[9];
	$job_cond="";
	
	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	
	if(str_replace("'","",$file_no)!="")  $file_cond="and a.file_no like '%".str_replace("'","",$file_no)."%' "; else $file_cond="";
	
	if(str_replace("'","",$style_reff)!="")  $style_cond="and b.style_ref_no like '%".str_replace("'","",$style_reff)."%' "; else $style_cond="";
	if(str_replace("'","",$internal_reff)!="")  $internal_reff_cond=" and a.grouping like '%".str_replace("'","",$internal_reff)."%' "; else $internal_reff_cond="";
	
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="SELECT b.id, b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where  b.garments_nature=100 and a.is_confirmed=1 and a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.buyer_name,b.job_no,a.po_number order by b.id desc";  
	}
	else if($db_type==2)
	{
		if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
		{
			$sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
	 
		$sql_order="SELECT b.id, b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.is_confirmed=1 $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.id, b.job_no,b.buyer_name, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date,a.file_no,a.grouping order by  b.id desc";  
	}
	//echo $sql_order;
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name,File No,Internal Ref. No, Order No,Shipment Date","60,60,150,150,100,100,150,100","1000","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year,style_ref_no", "", 1, "0,0,0,buyer_name,0,0,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,file_no,grouping,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;	
	exit();
}
//master data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$prev_cut_no_arr=array();
		$dataArrayMst=sql_select("select a.cutting_no, b.color_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=".$cbo_company_name." and a.entry_form=253 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");
		foreach($dataArrayMst as $row)
		{
			$prev_cut_no_arr[$row[csf('color_id')]][$row[csf('order_cut_no')]]=$row[csf('cutting_no')];
		}
		
		$sql_cutNo=sql_select("select max(b.order_cut_no) as manualcut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.entry_form=253 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");

		if($sql_cutNo[0][csf('manualcut_no')]=="") $manualcut_no=0;
		else $manualcut_no=$sql_cutNo[0][csf('manualcut_no')];
		
		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	   
		$job_prifix=return_field_value("job_no_prefix_num","wo_po_details_master","job_no=$txt_job_no");

		$new_sys_number = explode("*", return_next_id_by_sequence("", "ppl_cut_lay_mst",$con,1,$cbo_company_name,'',0,date("Y",time()),0,0,0,0,0 ));
		
		$cut_no_prifix[]=$new_sys_number[2];
		
		$comp_prefix=return_field_value("company_short_name","lib_company", "id=$cbo_company_name");
		$cut_no=str_pad((int) $cut_no_prifix[0],6,"0",STR_PAD_LEFT);
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$new_cutting_number=str_replace("--", "-",$new_sys_number[1]).$cut_no;
		$new_cutting_prifix=str_replace("--", "-",$new_sys_number[1]);
		$id= return_next_id_by_sequence(  "ppl_cut_lay_mst_seq",  "ppl_cut_lay_mst", $con );

		$field_array="id, entry_form, cut_num_prefix, cut_num_prefix_no, cutting_no, job_no, company_id, source, working_company_id, location_id, floor_id, store_id, entry_date, start_time, cad_marker_cons, end_date, end_time, body_part_string, size_set_no, inserted_by, insert_date, status_active, is_deleted";
		$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		$data_array="(".$id.",253,'".$new_cutting_prifix."',".$cut_no_prifix[0].",'".$new_cutting_number."',".$txt_job_no.",".$cbo_company_name.",".$cbo_knitting_source.",".$cbo_working_company_name.",".$cbo_location_name.",".$cbo_floor.",".$cbo_store_name.",".$txt_entry_date.",'".$start_time."',".$txt_marker_cons.",".$txt_end_date.",'".$end_time."',".$hidden_body_partstring.",".$txt_size_set_no.",'".$user_id."','".$pc_date_time."',1,0)";
			
		$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
		$field_array1="id, mst_id, color_type_id, order_cut_no, color_id, gmt_item_id, plies, size_ids, order_qty, roll_data, inserted_by, insert_date, status_active, is_deleted";
		$field_array_alocation_mst="id,entry_form,job_no,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,inserted_by,insert_date";
		$field_array_alocation_dtls = "id,mst_id,job_no,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		
		$field_array_prod="id, mst_id, dtls_id,prod_id, color_id, color_name, produc_name_details, alocated_qty, avaliable_qty, consumsion, lot, inserted_by, insert_date";
		
		$field_array_mst_alo="id,entry_form,job_no,item_category,allocation_date, booking_no, item_id,qnty,qnty_break_down,inserted_by,insert_date";
		$field_array_mst_log="id,entry_form,mst_id,job_no,item_category,allocation_date,booking_no,item_id,qnty,inserted_by,insert_date";

		$field_array_dtls_alo = "id,mst_id,job_no,booking_no,item_category, allocation_date,item_id, qnty, inserted_by, insert_date";
		$field_array_dtls_log = "id,mst_id,job_no,booking_no,item_category,allocation_date,item_id, qnty,inserted_by,insert_date";
		$field_array_hystory = "id,mst_id,dtls_id,job_no,booking_no,item_category,allocation_date, item_id,qnty, inserted_by, insert_date,company_id";
		$field_array_product = "available_qnty*allocated_qnty";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$add_comma=0;
		
		$duplicateMsg=''; $duplicateStatus=true;
		for($i=1; $i<=$row_num; $i++)
		{
			$cbocolor="cbocolor_".$i;
			$cbo_gmt_id="cbogmtsitem_".$i;
			$order_qty="txtorderqty_".$i;
			$txt_plics="txtplics_".$i;
			$hiddsizeid="hiddsizeid_".$i;
			$update_details_id="updateDetails_".$i;
			$order_cut_no="orderCutNo_".$i;
			$rollData="rollData_".$i;
			$cboColorType="cboColorType_".$i;
			$prev_cut_no=$prev_cut_no_arr[str_replace("'",'',$$cbocolor)][str_replace("'",'',$$order_cut_no)];
			
			if((str_replace("'",'',$$order_cut_no)*1)>0)
				$manualcut_no=str_replace("'",'',$$order_cut_no);
			else $manualcut_no=$manualcut_no+1;
			
			/*if(str_replace("'",'',$$order_cut_no)!=""  && $prev_cut_no!="")
			{
				$duplicateStatus=false;
				$duplicateMsg.="Cutting No: ".$prev_cut_no." Found Against Order Cut No-".str_replace("'",'',$$order_cut_no);
			}*/
			$id_mst_log=return_next_id( "id", "inv_mat_allocation_mst_log", 1 ) ;
			$id_dtls_log=return_next_id( "id", "inv_mat_allocation_dtls_log", 1 ) ;
			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				$prod_dtls									=explode("=",$save_string[$x]);
				$lot_no										=$prod_dtls[0];
				$color_name									=$prod_dtls[2];
				$color_id									=$prod_dtls[1];
				$consumsition								=$prod_dtls[3];
				$available_qty								=$prod_dtls[4];
				$alocated_qty								=$prod_dtls[5];
				$prod_id									=$prod_dtls[6];
				$yarn_description							=$prod_dtls[7];
				
				$prod_id_data[$prod_id]['alocated_qty']		=$alocated_qty;
				$prod_id_arr[$prod_id]						=$prod_id;
				
				$id_mst_alo = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$id_dtls_alo = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
				if ($data_array_mst_alo != "") $data_array_mst_alo .= ",";
				$data_array_mst_alo .= "(" . $id_mst_alo . ",253," . $txt_job_no . ",1," . $txt_entry_date . ",'" . $new_cutting_number . "'," . $prod_id . "," . $alocated_qty . ",''," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				if ($data_array_mst_log != "") $data_array_mst_log .= ",";
				$data_array_mst_log .= "(" . $id_mst_log . ",253," . $id_mst_alo . "," . $txt_job_no . ",1," . $txt_entry_date . ",'" . $new_cutting_number . "'," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_dtls_alo != "") $data_array_dtls_alo .= ",";
				$data_array_dtls_alo .= "(" . $id_dtls_alo . "," . $id_mst_alo . "," . $txt_job_no . ",'" . $new_cutting_number . "',1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_dtls_log != "") $data_array_dtls_log .= ",";

				$data_array_dtls_log .= "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_job_no . ",'" . $new_cutting_number . "',1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_hystory != "") $data_array_hystory .= ",";
				$data_array_hystory .= "(" . $id_hystory . ",".$id_mst_alo."," . $id_dtls_log . "," . $txt_job_no . ",'" . $new_cutting_number . "',1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$cbo_company_name.")";
				
				$id_cut_prod = return_next_id_by_sequence("PPL_CUT_LAY_PROD_DTLS_PK_SEQ", "ppl_cut_lay_prod_dtls", $con);
				
				if($data_array_prod!="") $data_array_prod.= ",";
				$data_array_prod.="(".$id_cut_prod.",".$id.",".$detls_id.",".$prod_id.",'".$color_id."','".$color_name."','".$yarn_description."','".$alocated_qty."','".$available_qty."','".$consumsition."','".$lot_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$sql_allocation_product = sql_select("select id,available_qnty,allocated_qnty,current_stock from product_details_master a where id in (".$prod_id.") and a.status_active=1 and a.is_deleted=0");
			
				foreach($sql_allocation_product as $val)
				{
					$item_id_arr[]=$val[csf('id')];
					$update_avaliable_qty=$val[csf('current_stock')]-($val[csf('allocated_qnty')]+$prod_id_data[$val[csf('id')]]['alocated_qty']);
					$update_alocated_qty=$val[csf('allocated_qnty')]+$prod_id_data[$val[csf('id')]]['alocated_qty'];
					$update_product_array[$val[csf('id')]]=explode("*",("".$update_avaliable_qty."*".$update_alocated_qty.""));
				}
				$id_mst_log++;
				$id_dtls_log++;
			}
			

			if ($add_comma!=0) { $data_array1 .=","; $detls_id_array .="_"; }

			$data_array1.="(".$detls_id.",".$id.",".$$cboColorType.",'".$manualcut_no."',".$$cbocolor.",".$$cbo_gmt_id.",".$$txt_plics.",".$$hiddsizeid.",".$$order_qty.",".$$rollData.",'".$user_id."','".$pc_date_time."',1,0)";   
			$detls_id_array.=$detls_id."#".str_replace("'",'',$manualcut_no);
			//$detls_id=$detls_id+1;
			$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
			$add_comma++;
		}
		
		if($duplicateStatus==false)
		{
			echo "13**".$duplicateMsg;
			disconnect($con);
			die;
		}
				
		$rID=true; $rID3=true; $rID4=true;
		//echo "10**insert into inv_mat_allocation_dtls_log($field_array_dtls_log)values".$data_array_dtls_log;die;
		$rID1=sql_insert("ppl_cut_lay_mst",$field_array,$data_array,0);
		
		if($data_array1!="")
		{
			$rID2=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,0); 
		}
		
		if($data_array_prod!="")
		{
			$rID3=sql_insert("ppl_cut_lay_prod_dtls",$field_array_prod,$data_array_prod,0);
		}
		
		$rID_mst = sql_insert("inv_material_allocation_mst", $field_array_mst_alo, $data_array_mst_alo, 0);
		$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
		$rID_dtls = sql_insert("inv_material_allocation_dtls", $field_array_dtls_alo, $data_array_dtls_alo, 0);			
		$rIDdtls_log = sql_insert("inv_mat_allocation_dtls_log", $field_array_dtls_log, $data_array_dtls_log, 0);
		$rID_history = sql_insert("inv_material_allocat_hystory", $field_array_hystory, $data_array_hystory, 0);
		
		$update_product=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_product_array,$item_id_arr),1);
			
		//echo "10**insert into ppl_cut_lay_mst( $field_array) values".$data_array;die;
		//echo "10**".$rID1 ."**". $rID2 ."**". $rID3 ."**". $rID_mst."**". $rID_mst_log ."**". $rID_dtls ."**". $rIDdtls_log."**". $rID_history ."**". $update_product ."**". $rIDdtls_log;die;
		//echo "10**".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);   
				echo "0**".$id."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$prev_cut_no_arr=array();
		$dataArrayMst=sql_select("select a.cutting_no, b.color_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=".$cbo_company_name." and a.entry_form=253 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no and a.id!=$update_id");
		foreach($dataArrayMst as $row)
		{
			$prev_cut_no_arr[$row[csf('color_id')]][$row[csf('order_cut_no')]]=$row[csf('cutting_no')];
		}
		//echo "10**";
		$sql_cutNo=sql_select("select max(b.order_cut_no) as manualcut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.entry_form=253 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");// echo $sql_cutNo; die;

		if($sql_cutNo[0][csf('manualcut_no')]=="") $manualcut_no=0;
		else $manualcut_no=$sql_cutNo[0][csf('manualcut_no')];
		
	/*		$all_prod_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$rollData="rollData_".$i;
			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				if($all_prod_id=='') $all_prod_id=$prod_dtls[6]; else $all_prod_id.=','.$prod_dtls[6];
			}
		}
	*/	
		
		$issue_mrr=0;
        $sqlis=sql_select("select a.issue_number from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.pi_wo_batch_no=$update_id and a.entry_form=277 and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number");

        foreach($sqlis as $rows){
            if($issue_mrr==0) $issue_mrr=$rows[csf('issue_number')]; else $issue_mrr.=','.$rows[csf('issue_number')];
        }
        if($issue_mrr){
            echo "issue**".str_replace("'","",$update_id)."**".$issue_mrr;
            disconnect($con);
            die;
        }
			
		$cutting_product_details=sql_select("select id, mst_id, dtls_id, prod_id, alocated_qty, avaliable_qty from ppl_cut_lay_prod_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		$yarn_prod_dtls=array();
		foreach($cutting_product_details as $cp_val)
		{
			$yarn_prod_dtls[$cp_val[csf('dtls_id')]][$cp_val[csf('prod_id')]]['allocation']=$cp_val[csf('alocated_qty')];
			$yarn_prod_dtls[$cp_val[csf('dtls_id')]][$cp_val[csf('prod_id')]]['id']=$cp_val[csf('id')];
			$previous_inserted_yarn[$cp_val[csf('id')]]=$cp_val[csf('id')];
		}
		
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no=".$txt_cutting_no."");
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
	   
		$rID=true; $rID2=true; $rID3=true; $rID4=true;
	
		//master table update*********************************************************************
		$field_array="source*working_company_id*location_id*floor_id*store_id*entry_date*start_time*cad_marker_cons*end_date*end_time*body_part_string*size_set_no*updated_by*update_date";
		$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		$data_array="".$cbo_knitting_source."*".$cbo_working_company_name."*".$cbo_location_name."*".$cbo_floor."*".$cbo_store_name."*".$txt_entry_date."*'".$start_time."'*".$txt_marker_cons."*".$txt_end_date."*'".$end_time."'*".$hidden_body_partstring."*".$txt_size_set_no."*'".$user_id."'*'".$pc_date_time."'";
		
		
		//$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
		$field_array1="id, mst_id,color_type_id,order_cut_no,color_id,gmt_item_id,plies,size_ids,order_qty,roll_data,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up="color_type_id*order_cut_no*color_id*gmt_item_id*plies*size_ids*order_qty*roll_data*updated_by*update_date";
		//**********************************************************************
		$field_array_alocation_mst="id,entry_form,job_no,item_category,allocation_date,booking_no,item_id,qnty,inserted_by,insert_date";
		$field_array_alocation_dtls = "id,mst_id,job_no,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		
		$field_array_prod="id, mst_id, dtls_id,prod_id, color_id, color_name, produc_name_details, alocated_qty, avaliable_qty, consumsion, lot, inserted_by, insert_date";
		$field_array_prod_update="color_id*color_name*produc_name_details*alocated_qty*avaliable_qty*consumsion*lot* updated_by*update_date";
		
		
		
		$field_array_mst_alo="id,entry_form,mst_id,job_no,item_category,allocation_date, booking_no, item_id,qnty,qnty_break_down,inserted_by,insert_date";
		$field_array_mst_log="id,entry_form,mst_id,job_no,item_category,allocation_date,booking_no,item_id,qnty,inserted_by,insert_date";
		$field_array_dtls_alo = "id,mst_id,job_no,booking_no,item_category, allocation_date,item_id, qnty, inserted_by, insert_date";
		$field_array_dtls_log = "id,mst_id,job_no,booking_no,item_category,allocation_date,item_id, qnty,inserted_by,insert_date";
		$field_array_hystory = "id,mst_id,dtls_id,job_no,booking_no,item_category,allocation_date, item_id,qnty, inserted_by, insert_date,company_id";
		$field_array_product = "available_qnty*allocated_qnty";
	
		$add_comma=0;
		$duplicateMsg=''; $duplicateStatus=true;
		$id_mst_log=return_next_id( "id", "inv_mat_allocation_mst_log", 1 ) ;
		$id_dtls_log=return_next_id( "id", "inv_mat_allocation_dtls_log", 1 ) ;
		for($i=1; $i<=$row_num; $i++)
		{
			$cbocolor="cbocolor_".$i;
			$cbo_gmt_id="cbogmtsitem_".$i;
			$order_qty="txtorderqty_".$i;
			$txt_plics="txtplics_".$i;
			$hiddsizeid="hiddsizeid_".$i;
			$update_details_id="updateDetails_".$i;
			$order_cut_no="orderCutNo_".$i;
			$rollData="rollData_".$i;
			$cboColorType="cboColorType_".$i;
			
			/*$dataArrayYarn=sql_select("select a.* from ppl_cut_lay_prod_dtls a where a.mst_id=$update_id and a.dtls_id=".$$update_details_id." and a.status_active=1 and a.Is_deleted=0");
			foreach($dataArrayYarn as $val)
			{
				$yarn_prod_dtls[$val[csf('prod_id')]]=$val[csf('id')];
			}*/
			
			$prev_cut_no=$prev_cut_no_arr[str_replace("'",'',$$cbocolor)][str_replace("'",'',$$order_cut_no)];
			/*if(str_replace("'",'',$$order_cut_no)!=""  && $prev_cut_no!="")
			{
				$duplicateStatus=false;
				$duplicateMsg.="Cutting No: ".$prev_cut_no." Found Against Order Cut No-".str_replace("'",'',$$order_cut_no);
			}*/
			
			if((str_replace("'",'',$$order_cut_no)*1)>0)
				$manualcut_no=str_replace("'",'',$$order_cut_no);
			else $manualcut_no=$manualcut_no+1;
			
			if(str_replace("'","",$update_id)!="") $msster_id=$update_id; else $msster_id=$id;  
			
			$update_details_id=str_replace("'",'',$$update_details_id);
			if($update_details_id!="") $dtlsId=$update_details_id; else $dtlsId=$detls_id; 
			
			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				$prod_dtls									=explode("=",$save_string[$x]);
				$lot_no										=$prod_dtls[0];
				$color_name									=$prod_dtls[2];
				$color_id									=$prod_dtls[1];
				$consumsition								=$prod_dtls[3];
				$available_qty								=$prod_dtls[4];
				$alocated_qty								=$prod_dtls[5];
				$prod_id									=$prod_dtls[6];
				$yarn_description							=$prod_dtls[7];
				
				$prod_id_data[$prod_id]['alocated_qty']		=$alocated_qty;
				$prod_id_arr[$prod_id]						=$prod_id;
				
				$id_mst_alo = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$id_dtls_alo = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
				if ($data_array_mst_alo != "") $data_array_mst_alo .= ",";
				$data_array_mst_alo .= "(" . $id_mst_alo . ",253,".$msster_id."," . $txt_job_no . ",1," . $txt_entry_date . "," . $txt_cutting_no . "," . $prod_id . "," . $alocated_qty . ",''," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				if ($data_array_mst_log != "") $data_array_mst_log .= ",";
				$data_array_mst_log .= "(" . $id_mst_log . ",253," . $id_mst_alo . "," . $txt_job_no . ",1," . $txt_entry_date . "," . $txt_cutting_no . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_dtls_alo != "") $data_array_dtls_alo .= ",";
				$data_array_dtls_alo .= "(" . $id_dtls_alo . "," . $id_mst_alo . "," . $txt_job_no . "," . $txt_cutting_no . ",1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_dtls_log != "") $data_array_dtls_log .= ",";

				$data_array_dtls_log .= "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_job_no . ",'" . $new_cutting_number . "',1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				
				if ($data_array_hystory != "") $data_array_hystory .= ",";
				$data_array_hystory .= "(" . $id_hystory . ",".$id_mst_alo."," . $id_dtls_log . "," . $txt_job_no . ",'" . $new_cutting_number . "',1," . $txt_entry_date . "," . $prod_id . "," . $alocated_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$cbo_company_name.")";
				
				
				if($yarn_prod_dtls[$dtlsId][$prod_id]['id']!="")
				{
					$data_array_prod_id[]=$yarn_prod_dtls[$dtlsId][$prod_id]['id'];
					$data_array_prod_update[$yarn_prod_dtls[$dtlsId][$prod_id]['id']]=explode("*",("".$color_id."*'".$color_name."'*'".$yarn_description."'*'".$alocated_qty."'*'".$available_qty."'*'".$consumsition."'*'".$lot_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					unset($previous_inserted_yarn[$yarn_prod_dtls[$dtlsId][$prod_id]['id']]);
					
				}
				else
				{
				
					$id_cut_prod = return_next_id_by_sequence("PPL_CUT_LAY_PROD_DTLS_PK_SEQ", "ppl_cut_lay_prod_dtls", $con);
					if($data_array_prod!="") $data_array_prod.= ",";
					$data_array_prod.="(".$id_cut_prod.",".$msster_id.",".$dtlsId.",".$prod_id.",'".$color_id."','".$color_name."','".$yarn_description."','".$alocated_qty."','".$available_qty."','".$consumsition."','".$lot_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

				$sql_allocation_product = sql_select("select id,available_qnty,allocated_qnty,current_stock from product_details_master a where id in (".$prod_id.") and a.status_active=1 and a.is_deleted=0");
			
				foreach($sql_allocation_product as $val)
				{
					
					$item_id_arr[]=$val[csf('id')];
					$update_alocated_qty=$alocated_qty+($val[csf('allocated_qnty')]-$yarn_prod_dtls[$dtlsId][$prod_id]['allocation']);
					//$val[csf('allocated_qnty')]+$prod_id_data[$val[csf('id')]]['alocated_qty']-$yarn_prod_dtls[$dtlsId][$prod_id]['allocation'];
					$update_avaliable_qty=$val[csf('current_stock')]-($update_alocated_qty);
					//echo "10**".$val[csf('allocated_qnty')]."**".$prod_id_data[$val[csf('id')]]['alocated_qty']."**".$yarn_prod_dtls[$dtlsId][$prod_id]['alocation']."**".$update_alocated_qty."**".$update_avaliable_qty."<br/>";
					$update_product_array[$val[csf('id')]]=explode("*",("".$update_avaliable_qty."*".$update_alocated_qty.""));
				}


				$id_mst_log++;
				$id_dtls_log++;
			}
			
			//echo "10**";
		
		//	print_r($previous_inserted_yarn);die;
			$response_data=substr($response_data,0,-2);
			
			if($update_details_id!="")  
			{
				$updateID_array[]=$update_details_id; 
				$data_array_up[$update_details_id]=explode("_",("".$$cboColorType."_'".$manualcut_no."'_".$$cbocolor."_".$$cbo_gmt_id."_".$$txt_plics."_".$$hiddsizeid."_".$$order_qty."_".$$rollData."_'".$user_id."'_'".$pc_date_time."'_1_0"));
				
				if ($add_comma!=0) $detls_id_array .="_";
				$detls_id_array.=$update_details_id."#".str_replace("'",'',$manualcut_no);
				$add_comma++;
			}
			else
			{
				if ($data_array1){ $data_array1 .=","; $detls_id_array .="_"; }
				$data_array1.="(".$detls_id.",".$msster_id.",".$$cbo_order_id.",".$$cboColorType.",'".$manualcut_no."','".$$txt_ship_date."',".$$cbocolor.",".$$cbobatch.",".$$cbo_gmt_id.",".$$txt_plics.",".$$hiddsizeid.",".$$order_qty.",'".$response_data."',".$user_id.",'".$pc_date_time."',1,0)";   
				$detls_id_array.=$detls_id."#".str_replace("'",'',$manualcut_no);
				$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
				$add_comma++;
			}
		 }
		 
		if($duplicateStatus==false)
		{
			echo "13**".$duplicateMsg;
			disconnect($con);
			die;
		}
			//  echo "10**";
		//$detls_id_update.=implode("_",$updateID_array);
		//echo "10**insert into inv_mat_allocation_mst_log( $field_array_mst_log) values".$data_array_mst_log;die;

		//echo "10**";
		//print_r($updateID_array);die;
		$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);
		
		$detls_id_update.=$detls_id_array;
		$rID3=$rID4=$rID5=$rID2=1;
		if(count($updateID_array)>0)
		{
			//echo bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array);die;
			$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
		}
		
		if($data_array1!="")
		{
		   $rID3=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
		}
		
		if(count($data_array_prod_update)>0)
		{
			//echo bulk_update_sql_statement("ppl_cut_lay_prod_dtls","id",$field_array_prod_update,$data_array_prod_update,$data_array_prod_id);
			$rID4=execute_query(bulk_update_sql_statement("ppl_cut_lay_prod_dtls","id",$field_array_prod_update,$data_array_prod_update,$data_array_prod_id),1);
		}
		
		$delete_yarn_dtls=true;
		if(count($previous_inserted_yarn)>0)
		{
			//echo bulk_update_sql_statement("ppl_cut_lay_prod_dtls","id",$field_array_prod_update,$data_array_prod_update,$data_array_prod_id);
			$delete_yarn_dtls=execute_query("update ppl_cut_lay_prod_dtls set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  id in (".implode(",",$previous_inserted_yarn).")",0);
		}

		if($data_array_prod!="")
		{
		   $rID5=sql_insert("ppl_cut_lay_prod_dtls",$field_array_prod,$data_array_prod,1); 
		}
			
		//$delete_roll=execute_query("update  from pro_roll_details where mst_id=$msster_id and entry_form=99",0);	
		$delete_allcation_mst=execute_query("update inv_material_allocation_mst set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  booking_no=".$txt_cutting_no." and entry_form=253",0);
		
		$delete_allcation_log=execute_query("update inv_mat_allocation_mst_log set status_active=0,is_deleted=1  where  booking_no=".$txt_cutting_no." and entry_form=253",0);

		$delete_allcation_dtls=execute_query("update inv_material_allocation_dtls set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  booking_no=".$txt_cutting_no."",0);
		$delete_allcation_log_dtls=execute_query("update inv_mat_allocation_dtls_log set status_active=0,is_deleted=1 where  booking_no=".$txt_cutting_no."",0);
		$rID_mst = sql_insert("inv_material_allocation_mst", $field_array_mst_alo, $data_array_mst_alo, 0);
		$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
		$rID_dtls = sql_insert("inv_material_allocation_dtls", $field_array_dtls_alo, $data_array_dtls_alo, 0);			
		$rIDdtls_log = sql_insert("inv_mat_allocation_dtls_log", $field_array_dtls_log, $data_array_dtls_log, 0);
		$rID_history = sql_insert("inv_material_allocat_hystory", $field_array_hystory, $data_array_hystory, 0);
		
		$update_product=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_product_array,$item_id_arr),1);
		//echo "10**$rID1 && $rID2 && $rID3 && $rID4 && $rID4 && $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $rID_mst && $rID_mst_log  && $rID_dtls && $rIDdtls_log && $rID_history";die;
		// echo "10**insert into ppl_cut_lay_dtls( $field_array1) values".$data_array1;die;	
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $delete_yarn_dtls && $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $rID_mst && $rID_mst_log  && $rID_dtls && $rIDdtls_log && $rID_history)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_cutting_no);
			}
		}
	
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $delete_yarn_dtls && $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $rID_mst && $rID_mst_log  && $rID_dtls && $rIDdtls_log && $rID_history)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_cutting_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		list($prifix,$year,$cut_number_prif)=explode("-",str_replace("'", "", $txt_cutting_no));
		$cut_number_prif=$cut_number_prif*1;
		$sql_issue_number=sql_select("select a.issue_number from inv_issue_master a,inv_transaction b  where a.id=b.mst_id and  a.issue_basis=6 and b.requisition_no=".$cut_number_prif." and a.buyer_job_no=$txt_job_no and b.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		if(!empty($sql_issue_number))
		{
			echo "500**".$sql_issue_number[0][csf("issue_number")];disconnect($con);die;
		}
		$field_array_product = "available_qnty*allocated_qnty";
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cutting_product_details=sql_select("select * from ppl_cut_lay_prod_dtls where mst_id=".$update_id." and status_active=1 and is_deleted=0");

		$yarn_prod_dtls=array();
		foreach($cutting_product_details as $cp_val)
		{
			$yarn_prod_dtls[$cp_val[csf('dtls_id')]][$cp_val[csf('prod_id')]]['alocation']=$cp_val[csf('alocated_qty')];
			$yarn_prod_dtls[$cp_val[csf('dtls_id')]][$cp_val[csf('prod_id')]]['id']=$cp_val[csf('id')];
		}

		for($i=1; $i<=$row_num; $i++)
		{
			$cbocolor="cbocolor_".$i;
			$cbo_gmt_id="cbogmtsitem_".$i;
			$order_qty="txtorderqty_".$i;
			$txt_plics="txtplics_".$i;
			$update_details_id="updateDetails_".$i;
			$order_cut_no="orderCutNo_".$i;
			$rollData="rollData_".$i;
			$cboColorType="cboColorType_".$i;
			
	
			
			$prev_cut_no=$prev_cut_no_arr[str_replace("'",'',$$cbocolor)][str_replace("'",'',$$order_cut_no)];
			if(str_replace("'",'',$$order_cut_no)!=""  && $prev_cut_no!="")
			{
				$duplicateStatus=false;
				$duplicateMsg.="Cutting No: ".$prev_cut_no." Found Against Order Cut No-".str_replace("'",'',$$order_cut_no);
			}
			
			if(str_replace("'","",$update_id)!="")
			{
				$msster_id=$update_id;
			}
			else
			{
				$msster_id=$id;  
			}
			
			$update_details_id=str_replace("'",'',$$update_details_id);
			if($update_details_id!="")  
			{ 
				$dtlsId=$update_details_id;
			}
			else
			{
				$dtlsId=$detls_id; 
			}
			
			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				$prod_dtls									=explode("=",$save_string[$x]);
				$lot_no										=$prod_dtls[0];
				$color_name									=$prod_dtls[2];
				$color_id									=$prod_dtls[1];
				$consumsition								=$prod_dtls[3];
				$available_qty								=$prod_dtls[4];
				$alocated_qty								=$prod_dtls[5];
				$prod_id									=$prod_dtls[6];
				$yarn_description							=$prod_dtls[7];
				
				$prod_id_data[$prod_id]['alocated_qty']		=$alocated_qty;
				$prod_id_arr[$prod_id]						=$prod_id;
				

				
				
				/*if($yarn_prod_dtls[$dtlsId][$prod_id]['id']!="")
				{
					$data_array_prod_id[]=$yarn_prod_dtls[$dtlsId][$prod_id]['id'];
					$data_array_prod_update[$yarn_prod_dtls[$dtlsId][$prod_id]['id']]=explode("*",("".$color_id."*'".$color_name."'*'".$yarn_description."'*'".$alocated_qty."'*'".$available_qty."'*'".$consumsition."'*'".$lot_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
				}*/

				$sql_allocation_product = sql_select("select id,available_qnty,allocated_qnty,current_stock from product_details_master a where id in (".$prod_id.") and a.status_active=1 and a.is_deleted=0");
			
				foreach($sql_allocation_product as $val)
				{
					$item_id_arr[]=$val[csf('id')];
					$update_alocated_qty=$val[csf('allocated_qnty')]-$yarn_prod_dtls[$dtlsId][$prod_id]['alocation'];
					$update_avaliable_qty=$val[csf('current_stock')]-($update_alocated_qty);
					$update_product_array[$val[csf('id')]]=explode("*",("".$update_avaliable_qty."*".$update_alocated_qty.""));
				}

			}
			
		
		 }
		// echo "10**";
		// echo bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_product_array,$item_id_arr);die;
	//print_r($update_product_array);die;
		$update_product=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_product_array,$item_id_arr),1);
		$delete_mst=execute_query("update ppl_cut_lay_mst set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  id=".$update_id." and entry_form=253",0);

		$delete_dtls=execute_query("update ppl_cut_lay_prod_dtls set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  mst_id=".$update_id." ",0);

		$delete_prod_dtls=execute_query("update ppl_cut_lay_dtls set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  mst_id=".$update_id."",0);

		$delete_allcation_mst=execute_query("update inv_material_allocation_mst set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  booking_no=".$txt_cutting_no." and entry_form=253",0);
		
		$delete_allcation_log=execute_query("update inv_mat_allocation_mst_log set status_active=0,is_deleted=1  where  booking_no=".$txt_cutting_no." and entry_form=253",0);

		$delete_allcation_dtls=execute_query("update inv_material_allocation_dtls set status_active=0,is_deleted=1,updated_by=".$user_id.",update_date='".$pc_date_time."'  where  booking_no=".$txt_cutting_no."",0);
		$delete_allcation_log_dtls=execute_query("update inv_mat_allocation_dtls_log set status_active=0,is_deleted=1 where  booking_no=".$txt_cutting_no."",0);

		$delete=execute_query("delete from ppl_cut_lay_size where mst_id=".$update_id." ",0);
		$delete_size=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$update_id." ",0);
		$delete_bundle=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$update_id."",0);
	//echo "10**$update_product ** $delete_mst && $delete_dtls && $delete_prod_dtls &&  $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $delete && $delete_size  && $delete_bundle";die;
		if($db_type==0)
		{
			if($update_product && $delete_mst && $delete_dtls && $delete_prod_dtls &&  $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $delete && $delete_size  && $delete_bundle )
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_cutting_no);
			}
		}
	
		else if($db_type==2 || $db_type==1 )
		{
			if($update_product && $delete_mst && $delete_dtls && $delete_prod_dtls &&  $delete_allcation_mst && $delete_allcation_log && $delete_allcation_dtls && $delete_allcation_log_dtls && $delete && $delete_size  && $delete_bundle )
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_cutting_no);
			}
		}	
		disconnect($con);
		die;
	}		
}

if($action=="bodypart_popup")
{
	echo load_html_head_contents("PO Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//$time_weight_panel;
	//echo $hidden_body_partstring;die;
	?>
		<script>
		
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
			var selected_id = new Array(); var selected_name = new Array();
			
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
				var old=document.getElementById('txt_po_row_id').value; 
				if(old!="")
				{   
					old=old.split(",");
					for(var k=0; k<old.length; k++)
					{   
						js_set_value( old[k] ) 
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
				
				$('#po_id').val(id);
				$('#po_no').val(name);
			}
	    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:400px;margin-left:10px">
	    	<input type="hidden" name="po_id" id="po_id" class="text_boxes" value="">
	        <input type="hidden" name="po_no" id="po_no" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table" >
	                <thead>
	                    <th width="40">SL</th>
	                    <th width="200">Body Part</th>
	                </thead>
	            </table>
	            <div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
						$body_part_id_arr=explode(",",$hidden_body_partstring);
					
	                    foreach($body_part_id_arr as $body_part_id)
	                    {
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="40" align="center"><?php echo "$i"; ?>
								
								</td>	
								<td width="200"><p><? echo $time_weight_panel[$body_part_id]; ?></p></td>
							</tr>
							<?
							$i++;
	                    }
	                ?>
	                    <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $process_row_id; ?>"/>
	                </table>
	            </div>
	             <table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%"> 
	                            <div style="width:100%; float:left" align="center">
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

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >


	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>                	 
	                    <th width="140">Company name</th>
	                    <th width="130">System No</th>
	                    <th width="130">Style Ref.</th>
	                    <th width="130">Job No</th>
	                    <th width="130" style="display:none">Order No</th>
	                    <th width="250">Date Range</th>
	                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                  <tr class="general">                    
	                        <td>
	                              <? 
	                                   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
	                             ?>
	                        </td>
	                      
	                        <td align="center" >
	                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes_numeric"/>
	                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
	                        </td>
	                        <td align="center">
	                               <input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center">
	                               <input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
	                        </td>
	                        <td align="center" style="display:none">
	                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        
	                        <td align="center" width="250">
	                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
	                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
	                        </td>
	                        <td align="center">
	                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_search').value, 'create_cutting_search_list_view', 'search_div', 'yarn_lot_ratio_planning_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                        </td>
	                 </tr>
	        		 <tr>                  
	                    <td align="center"  valign="middle" colspan="6">
	                        <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>   
	            </tbody>
	         </tr>         
	      </table> 
	     <div align="center" valign="top" id="search_div"> </div>  
	  </form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="size_set_number_popup")
{
  	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			
		document.getElementById('hidden_sizeset_no').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
		<input type="hidden" name="hidden_sizeset_no" id="hidden_sizeset_no" class="text_boxes" value="">

		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<?php
				$sql="select a.sizeset_no, a.extention_no FROM ppl_size_set_mst a,ppl_size_set_dtls b where a.id=b.mst_id and a.job_no='".$txt_job_no."' and b.item_number_id=".$cbogmtsitem." and b.color_id=".$cbocolor." and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.sizeset_no,a.extention_no";
				//echo $sql;
				echo create_list_view("list_view", "Size Set No,Extantion No","150,100","300","270",0, $sql , "js_set_cutting_value", "sizeset_no", "", 1, "0,0", $arr, "sizeset_no,extention_no", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
			?>
	  	</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);

	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$style_serch_no= $ex_data[7];

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$style_serch_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no  like '%".$style_serch_no."%' ";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	

	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width,b.style_ref_no,c.color_id, c.marker_qty, c.order_cut_no,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 $conpany_cond $cut_cond $job_cond $sql_cond $style_cond order by id DESC";
	//echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(5=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Style Ref.,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","60,50,60,90,140,200,80,90,80","950","270",0, $sql_order , "js_set_cutting_value", "id", "", 1, "0,0,0,0,0,color_id,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,job_no,style_ref_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
	exit();
}

// need  for this page
if($action=="load_php_mst_form")
{
    $sql_data=sql_select("select a.location_id, a.floor_id, a.source, a.store_id, a.id, a.job_no, a.company_id, a.working_company_id, a.entry_date, end_date, a.cutting_no, a.start_time, a.end_time, a.size_set_no
	from ppl_cut_lay_mst a
	where  a.id=".$data." and a.status_active=1 and a.is_deleted=0 ");

      foreach($sql_data as $val)
	  {
		    $start_time=explode(":",$val[csf("start_time")]);
		    $end_time=explode(":",$val[csf("end_time")]);
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n";
			echo "load_drop_down( 'requires/yarn_lot_ratio_planning_controller', '".($val[csf("company_id")])."', 'load_drop_down_store','store_td');";
			echo "document.getElementById('cbo_store_name').value = '".($val[csf("store_id")])."';\n";
			echo "document.getElementById('cbo_knitting_source').value = '".($val[csf("source")])."';\n"; 
			
			echo "load_drop_down( 'requires/yarn_lot_ratio_planning_controller', ".($val[csf("source")])."+'_'+".($val[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
			echo "document.getElementById('cbo_working_company_name').value = '".($val[csf("working_company_id")])."';\n"; 
			
			echo "load_location();\n";
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
		
			if($val[csf("source")]==1)
			{
				echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
				echo "load_drop_down( 'requires/yarn_lot_ratio_planning_controller', ".$val[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
				
				echo "document.getElementById('cbo_floor').value  = '".($val[csf("floor_id")])."';\n";
			}
			  
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
			echo "document.getElementById('txt_size_set_no').value = '".($val[csf("size_set_no")])."';\n"; 
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n"; 
			  
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n"; 
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";  
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n"; 
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";

			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
			if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}


			$sql=sql_select("select distinct c.id,b.style_ref_no,c.buyer_name,$insert_year from wo_po_details_master b,lib_buyer c where  b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and b.status_active=1");
			
			foreach($sql as $row)
		   	{
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n"; 
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n"; 
				echo "document.getElementById('txt_style_no').value = '".$row[csf("style_ref_no")]."';\n";  
		   	}
	  }
	  exit();
}

if($action=="order_details_list")
{
	// $sql_gmt_arr="select ";
	 $tbl_row=0;
	 $sql_dtls=sql_select("select a.id, a.order_ids, a.ship_date, a.color_id, a.color_type_id, a.batch_id, a.gmt_item_id, a.plies, a.size_ids, a.marker_qty, a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id, a.order_cut_no, a.roll_data from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=".$data." order by a.id");
	
	$gmt_item_arr=return_library_array( "select gmts_item_id from wo_po_details_master where job_no='".$sql_dtls[0][csf('job_no')]."' and status_active=1",'id','gmts_item_id');
	$gmt_item_id=implode(",",$gmt_item_arr);
	$color_item_arr=return_library_array( "select a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where a.id=b.color_number_id and c.id=b.po_break_down_id and b.job_no_mst='".$sql_dtls[0][csf('job_no')]."' and b.item_number_id='".$sql_dtls[0][csf('gmt_item_id')]."' and c.is_confirmed=1 and c.status_active=1 and b.status_active=1 group by a.id,a.color_name","id","color_name");
		
	$color_type_arr=array();
	$sql="SELECT b.color_type_id from wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where  b.id=c.pre_cost_fabric_cost_dtls_id  and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.job_no='".$sql_dtls[0][csf('job_no')]."' and c.cons>0  group by b.color_type_id";
	//echo $sql;die;
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$vals[csf("color_type_id")];
	}
	//print_r($color_type_arr);die;
	
	foreach($sql_dtls as $val)
	{
		$sql="select sum(plan_cut_qnty) as plan_qty from wo_po_color_size_breakdown where job_no_mst='".$val[csf('job_no')]."' and item_number_id=".$val[csf("gmt_item_id")]." and color_number_id=".$val[csf("color_id")]." and status_active=1 group by item_number_id,color_number_id ";
		//echo $sql;
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$plan_qty+=$row[csf("plan_qty")];
		}
		
		$sql_marker="select sum(a.marker_qty) as mark_qty from ppl_cut_lay_dtls a,ppl_cut_lay_mst b where b.id=a.mst_id and b.job_no='".$val[csf('job_no')]."' and a.gmt_item_id=".$val[csf("gmt_item_id")]." and a.color_id=".$val[csf("color_id")]." and a.status_active=1";
		$result=sql_select($sql_marker);
		foreach($result as $rows)
		{
			$total_marker_qty=$rows[csf("mark_qty")];
		}
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo $val[csf("size_ids")];
		$exsizeids=explode(",",$val[csf("size_ids")]);
		//print_r($exsizeids);
		$sizeName="";
		foreach($exsizeids as $sid)
		{
			if($sizeName=="") $sizeName=$size_arr[$sid]; else $sizeName.=','.$size_arr[$sid];
		}

	   $tbl_row++;
		?>
	   <tr class="" id="tr_<?=$tbl_row; ?>" style="height:10px;">
       		<td align="center" id="garment_<?=$tbl_row; ?>"><?=create_drop_down( "cbogmtsitem_".$tbl_row, 120, $garments_item,"", 1, "-- Select Item --", $val[csf('gmt_item_id')], "",0,$gmt_item_id); ?></td>
       		<td align="center" id="color_<?=$tbl_row; ?>"><?=create_drop_down( "cbocolor_".$tbl_row, 100, $color_item_arr,"", 1, "select color", $val[csf('color_id')], "reset_fld(".$tbl_row.")",1); ?></td>                             
			
            <td align="center" id="colorTypeId_<? echo $tbl_row; ?>">
                <?
                $color_type_arr_id=implode(",",$color_type_arr);
				echo create_drop_down( "cboColorType_".$tbl_row, 100, $color_type,"", 1, "--select--",$val[csf('color_type_id')], "",1,$color_type_arr_id);
                ?>
            </td>
			<td align="center" id="cutNo_<?=$tbl_row; ?>">
				<input style="width:60px;" class="text_boxes_numeric" type="text" name="orderCutNo_<?=$tbl_row; ?>" id="orderCutNo_<?=$tbl_row; ?>" placeholder="display" value="<?=$val[csf('order_cut_no')]; ?>" onBlur="cut_no_duplication_check(<?=$tbl_row; ?>);" readonly />
			</td>                            
			<td align="center">
				   <input type="text" name="txtplics_<?=$tbl_row; ?>"  id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $val[csf('plies')];?>" placeholder="Double Click" onDblClick="openmypage_lot(<? echo $tbl_row; ?>)" readonly/>
				  <input type="hidden" name="updateDetails_<? echo $tbl_row; ?>"  id="updateDetails_<? echo $tbl_row; ?>"  value="<? echo $val[csf('id')]; ?>" />
				  <input type="hidden" name="rollData_<? echo $tbl_row; ?>" id="rollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('roll_data')]; ?>" />
			</td>
            <td align="center"><input type="text" name="txtposize_<?=$tbl_row; ?>" id="txtposize_<?=$tbl_row; ?>" class="text_boxes" onDblClick="openmypage_posize();" value="<?=$sizeName; ?>" placeholder="Browse" style="width:90px" /><input type="hidden" name="hiddsizeid_<?=$tbl_row; ?>" id="hiddsizeid_<?=$tbl_row; ?>" class="text_boxes" value="<?=$val[csf("size_ids")]; ?>" readonly /></td>
			<td align="center">
				  <input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)" readonly/>
			</td>
			<td align="center" id="marker_<? echo $tbl_row; ?>">
				  <input type="text" name="txtmarkerqty_<? echo $tbl_row; ?>"  id="txtmarkerqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $val[csf('marker_qty')];?>" disabled />
			</td>
			 <td align="center" id="order_<? echo $tbl_row; ?>" >
				 <input type="text" name="txtorderqty_<? echo $tbl_row; ?>" id="txtorderqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $plan_qty;?>" disabled/>
			</td>
			 <td align="center">
				 <input type="text" name="txttotallay_<? echo $tbl_row; ?>"  id="txttotallay_<? echo $tbl_row; ?>"class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $total_marker_qty;?>" disabled/>
			</td>
			<td align="center">
				 <input type="text" name="txtlaybalanceqty_<? echo $tbl_row; ?>"  id="txtlaybalanceqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $lay_balance;?>" disabled/>
			</td>
		
	   </tr>
	<?	
	 }
	 exit();
}

if($action=="cut_lay_entry_report_print")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
			$table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
		}
		
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}		
		
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]' and is_confirmed=1",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	//print_r($sql_order);
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{ 
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')]; 
		if($order_id!="")
		{
			$order_id.=",".$order_val[csf('order_ids')];
		}
		else 
		{
			$order_id=$order_val[csf('order_ids')];
		}
	}
	$order_ids=array_unique(explode(",",$order_id));
	foreach($order_ids as $poId)
	{
		if($order_number!="")
		{
			$order_number.=",".$order_number_arr[$poId];
		}
		else 
		{
			$order_number=$order_number_arr[$poId];
		}
	}
	
	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
        <table width="500" cellspacing="0" align="center">
            <tr>
                <td  align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr> 
            <tr>
                <td  align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
            </tr>
       </table>
                 
    </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Job No</td><td align="center"> <? echo $data[1]; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item Name</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
         <tr>
              <td>Order No</td><td align="center"><p> <? echo $order_number; ?></p></td>
         </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>
    
    </table>
    </div>
	<div  style="width:550; position:absolute; height:30px; top:70px; left:280px">
    	<table>
        	<tr>
            	<td><b>Working Company: </b></td>
                <td width="260"><? echo $company_library[$data[2]]; ?> </td>
                <td><b>Location: </b></td>
                <td><? echo $location_arr[$data[3]]; ?> </td>
            </tr>
        </table>
		 
	     
	 </div>
    <div  style="width:250; position:absolute; height:30px; top:118px; left:280px">
          <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
              <tr >
              <td width="170"> CAD Fabric Width/Dia</td><td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
             </tr>
          </table>
    </div>
    
 
   <div  style="width:250; position:absolute; height:30px; top:160px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
          <tr >
          <td width="170">CAD GSM</td><td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
         </tr>
      </table>
    </div>
    <div  style="width:300; position:absolute; height:100px; top:280px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
          <tr height="20">
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
          <td width="75" align="center">Batch No </td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
         </tr>
         <tr height="30">
          <td width="80">Cutting No</td>
          <td width="75" align="center"><? echo $comp_name."-".$cut_prifix; ?></td>
          <td width="75" align="center">  <? echo $txt_batch; ?></td>
          <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
         </tr>
      </table>
    </div>
    
     <div  style="width:200; position:absolute; height:400px; top:164px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>
    
    
    <div style=" width:300; position:absolute; top:175px; right:0px; ">
	<table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
         <tr height="30">
              <td width="100"><strong>Line Q.I</strong></td><td width="200" align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Jr. DQ.C</strong></td><td align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Checked By Q.C</strong></td> <td align="center" colspan="2"></td>
        </tr >
         <tr height="30">
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Time</td><td align="center" width="100"></td><td align="center" width="100"></td>
         </tr>
    
    </table>
    </div>
    <div style=" width:270; position:absolute; top:250px;  ">
	<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
    <div style=" float:right;width:190px;">
         <div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
        <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
         <tr height="30">
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr height="30"  >
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>
        
    
    </table>
    </div>
    </div>
 	</div>
 
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){
		   
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
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>
    
    
	 <div style=" width:1100px; position:absolute; top:385px; ">
	   <style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
						font-size:10.5px;
	                    vertical-align:bottom;
	                    display: block;
	                    
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	          
	        </style> 
	   <?
 
	     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from   
	     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
	     and a.is_deleted=0 ");
		 $detali_data_arr=array();
		 $plice_data_arr=array();
		 $size_id_arr=array();
		 $total_gmt_qty=array();
		 $grand_total=0;
		 $size_qty=0;
		 $size_ratio_arr=array();
		 foreach($sql_size_ration as $size_val)
		 {
		  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
		 }
		
		$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id 
		 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a 
		 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
		 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
		 order by a.id,c.id");
		 $detali_data_arr=array();
		 $plice_data_arr=array();
		 $size_id_arr=array();
		 $total_gmt_qty=array();
		 $grand_total=0;
		 $size_qty=0;
		 foreach($sql_main_qry as $main_val)
		 {
		    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
			$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
			$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
			$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
			$grand_total+=$main_val[csf('marker_qty')];
			$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
		 } 
	 //print_r($plice_data_arr);die;
	   $col_span=count($size_id_arr);
	   $td_width=450/$col_span;
	  
	  // echo $td_width;die;
	   
	   ?>
	    <table border="1" cellpadding="1" cellspacing="1"   width="1100"class="rpt_table" rules="all">
	          <tr height="30" >
	          <td width="30">SL</td>
	          <td width="60" align="center">Roll No </td>
	          <td width="60" align="center">Roll Kgs </td>
	          <td width="50" align="center">Color </td>
	          <td width="70"> Plies & Pcs/Bundle</td>
	           <td width="80">Particulars</td>
	          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
	          <td width="50" align="center">Total Gmts</td>
	          <td width="70" align="center">Per Roll Cons</td>
	           <td width="60">Cut Out Faults</td>
	          <td width="60" align="center">End of Roll Length</td>
	          <td width="60" align="center">Total Unused Length </td>
	         </tr>
	        <?
			 $i=1; $tot_gmts=0; $tot_roll_wght=0;
			  foreach($plice_data_arr as $dtls_id=>$dtls_val)
				  {
					foreach($dtls_val as $plice_id=>$plice_val)
					 {
						 $tot_roll_wght+=$plice_val['roll_weight'];
					 ?>
	                 <tr height="20">
	                      <td width="" rowspan="4"><? echo $i;  ?></td>
	                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
	                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
	                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
	                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
	                       <td width="">Size</td>
	                       
	                   <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?>	</td>
							 
						 <?
							 }
						 ?>
	                      <td width="" align="right" valign="bottom" ></td>
	                      <td width="" align="center"></td>
	                      <td width=""></td>
	                      <td width="" align="center"></td>
	                      <td width="" align="center"> </td>
	                   </tr>
	                   <tr height="20">
	                     <td width="">CAD Ratio</td>
	               	 <?
					  foreach($size_id_arr as $size_id=>$size_val)
					    {
							$total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
					 ?>
	                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?>	</td>
	                 <?
					     }
	                 ?>
	                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>
	                      <td width="" align="center"></td>
	                      <td width=""></td>
	                      <td width="" align="center"></td>
	                      <td width="" align="center"> </td>
	                   </tr>
	                     <tr height="20">
	                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
	                       <td width=""> Gmts Qty.
	</td>
	                         <?
							  foreach($size_id_arr as $size_id=>$size_val)
								{
									$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
							 ?>
								   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>
								 
							 <?
								 }
								 $tot_gmts+=$total_gmt_qty_roll;
							 ?> 
	                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td> 
						  <td width="" align="center"></td>
	                      <td width=""></td>
	                      <td width="" align="center"></td>
	                      <td width="" align="center"> </td>
	                   </tr>
	              </tr>
	                     <tr height="20">
	                       <td width="">Bundle Qty.</td>
	                         <?
							  foreach($size_id_arr as $size_id=>$size_val)
							  {
							 ?>
								   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
								   <?  
								   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']); 
								    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
									if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";
								  
								    echo $bdl_qty;
								    ?>	
	                               </td>
							 <?
							 }
							 ?>  
						  <td width="" align="center"></td>
	                      <td width=""></td>
	                      <td width="" align="center"></td>
	                      <td width="" align="center"> </td>
	                   </tr>       
	                
	              <?
					 $i=$i+1;
					 }
				  }
			
			     ?> 
	    
	         
	      </table>
	      <?
	      $table_height=30+($i+1)*20;
		//echo $table_height;die;
		$div_position=$table_height+420;
		
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown 
		where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
			//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
		}

	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
	   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
	   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,125)
	   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
	   $con_per_dzn=array();
	   $po_item_qty_arr=array();
	   $color_size_conjumtion=array();
	   foreach($sql_sewing as $row_sew)
	   {
			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
			
			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
			$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
		
			$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	   }
	   //print_r($color_size_conjumtion);
		$con_qnty=0;
		foreach($color_size_conjumtion as $p_id=>$p_value)
		{
			foreach($p_value as $i_id=>$i_value)
			{
				foreach($i_value as $c_id=>$c_value)
				{
				foreach($c_value as $s_id=>$s_value)
					{
						foreach($s_value as $b_id=>$b_value)
						{
							$order_color_size_qty=$b_value['plan_cut_qty'];
							// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
							$order_qty=$tot_plan_qty;
							$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
							$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
							$con_qnty+=$conjunction_per;
						}
					}
				} 
			}
		}

		$con_qnty=($con_qnty/$costing_per_qty)*12;
		$net_cons=($tot_roll_wght/$tot_gmts)*12;
		$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
		/*$cons_balance=$cad_marker_cons-$net_cons;
		if($cad_marker_cons>$net_cons) 
		{
			$loss_gain='Gain';
			$gain=number_format($cons_balance,4);
		}
		else if($cad_marker_cons<$net_cons) 
		{
			$loss_gain='Loss';
			$loss=number_format(abs($cons_balance),4);
		}*/
		
		$cons_balance=$con_qnty-$net_cons;
		if($con_qnty>$net_cons) 
		{
			$loss_gain='Gain';
			$gain=number_format($cons_balance,4);
		}
		else if($con_qnty<$net_cons) 
		{
			$loss_gain='Loss';
			$loss=number_format(abs($cons_balance),4);
		}
		?>

	       
	       <div style=" width:160px; position:absolute; margin-top:20px;   ">
	          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
	                  <tr  height="30">
	                       <td width="100">Booking<br>Consumption <br>Per Dzn</td>
	                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
	                  </tr>
	            </table>
	       </div>
	       
	        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
	          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
	                  <tr  height="30" >
	                       <td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
	                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
	                  </tr>
	            </table>
	       </div>
	         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
	          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
	                  <tr  height="30">
	                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
	                       <td width="70" align="center" >KGs</td>
	                       <td width="70" align="center" >G.Qty</td>
	                  </tr>
	                   <tr  height="30">
	                       
	                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
	                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
	                  </tr>
	            </table>
	       </div>
	       
	        <div style=" width:230px; position:absolute; right:191px; margin-top:20px;   ">
	          <table border="1" cellpadding="1" cellspacing="1" width="220"class="rpt_table" rules="all">
	                  <!--<tr height="20">
	                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
	                       <td width="70" align="center" >Net</td>
	                       <td width="70" align="center" ></td>
	                  </tr>
	                   <tr height="20">
	                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
	                       <td width="70" align="center" ></td>
	                  </tr>-->
	                  <tr height="20">
	                       <td width="80" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
	                       <td width="70" align="center">Net</td>
	                       <td width="70" align="center">Loss</td>
	                       <td width="70" align="center">Gain</td>
	                  </tr>
	                   <tr height="20">
	                       <td width="70" align="center" ><? echo number_format($net_cons,4); ?></td>
	                       <td width="70" align="center" ><? echo $loss; ?></td>
	                       <td width="70" align="center"><? echo $gain; ?></td>
	                  </tr>
	            </table>
	       </div>
	       <div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
	          <table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
	                  <tr>
	                       <td width="100">Lay<br>Loss/Gain</td>
	                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
	                  </tr>
	            </table>
	       </div>
	       <br><br><br>
	       <? echo signature_table(58, $company_id, "1100px"); ?>
		</div>
	<?
	   exit();
}


if($action=="report_yarn_lot_ratio")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );
	
	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$working_comp_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$working_location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$working_floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	 
	
	$sql="SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no,a.size_set_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("SELECT buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];
	$gmt_item_id=$dataArray[0][csf('gmt_item_id')];
	$color_id=$dataArray[0][csf('color_id')];
	$job_no=$dataArray[0][csf('job_no')];
	
	$po_arr=return_library_array( "SELECT id, po_number from wo_po_break_down where job_no_mst='".$dataArray[0][csf('job_no')]."' and is_confirmed=1","id","po_number" );
	$batch_no=$dataArray[0][csf('batch_no')];
	?>
	<div style="width:1000px; " align="center" >
    <table width="990" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lay and Bundle Information</u></strong></td>
        </tr>
         <tr>
        	<td width="120"><strong>System Number:</strong></td><td width="160"><? echo $cut_no; ?></td>
            <td width="120"><strong>Job No :</strong></td> <td width="160"><? echo $dataArray[0][csf('job_no')]; ?></td>
			<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
        </tr>
        <tr>
			 <td><strong>Gmt Item:</strong></td> <td width="160"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
             <td><strong>Color :</strong></td><td width="160"><? echo$color_library[$dataArray[0][csf('color_id')]]; ?></td>
             <td><strong>Manual Ratio No:</strong></td> <td width="160"><? echo $order_cut_no; ?></td>
        </tr>

        <tr>
        
             <td><strong>Total Ratio:</strong></td> <td width="160"><? echo $dataArray[0][csf('plies')]; ?></td>
             <td><strong>Ratio Date:</strong></td><td width="160"><? echo change_date_format($dataArray[0][csf('entry_date')]); ?></td>
        </tr>
        <tr>
       		 <td><strong>Style Ref:</strong></td> <td width="160"><? echo $style_ref; ?></td>
             <td ><strong>Style Desc.:</strong></td> <td colspan="2" width="260" style="word-break: break-all;"><p><? echo $style_desc; ?></p></td>
             <td align="left"  id="barcode_img_id"></td>
        </tr>
        <tr>
       		 <td><strong>Working Company:</strong></td> <td width="160"><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
             <td><strong>Working Location:</strong></td> <td width="160"><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>

             <td><strong>Working Floor:</strong></td> <td width="160"><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        </tr>
         <tr>
       		 <td><strong>Size Set No:</strong></td> <td width="160"><? echo $dataArray[0][csf('size_set_no')]; ?></td>

        </tr>

    </table>
    <br>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <!-- <script type="text/javascript" src="../../../js/jquerybarcode.js"></script> -->
     <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
     <script>

			function generateBarcode( valuess ){
				   
					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
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
					 value = {code:value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				} 
			  // generateBarcode('<? //echo $dataArray[0][csf('cutting_no')]; ?>');
			  var main_value='<? echo $dataArray[0][csf('cutting_no')]; ?>';
			  $('#barcode_img_id').qrcode(main_value);
			  
	 </script>
	<div style="width:1200px;">
		<?php
			if($data[4]==0) $country_cond=""; else $country_cond=" and b.country_id='".$data[4]."'";
			$size_data=sql_select("SELECT 
										a.id,
										a.size_id,
										count(b.id) as total_bundle,
										sum(b.size_qty) as size_qty, 
										b.country_id, 
										b.order_id
			 						from 
			 							ppl_cut_lay_size_dtls a,
			 							ppl_cut_lay_bundle b 
			 						where 
			 							a.mst_id='$data[1]' and 
			 							a.dtls_id='$data[2]' and 
			 							a.mst_id=b.mst_id and
			 							a.dtls_id=b.dtls_id and 
			 							a.size_id=b.size_id $country_cond 
			 						group by 
			 							a.id,
			 							a.size_id, 
			 							b.country_id, 
			 							b.order_id 
			 						order by a.id ASC");
			$order_wise_bundle=array();
			$order_wise_size_qty=array();
			$ratio_wise_bundle=array();
			$ratio_wise_size_qty=array();
			$total_size_arr=array();
			foreach ($size_data as  $value) {
				$order_wise_bundle[$value[csf('order_id')]][$value[csf('size_id')]]+=$value[csf('total_bundle')];
				$order_wise_size_qty[$value[csf('order_id')]][$value[csf('size_id')]]=$value[csf('size_qty')];
				$total_size_arr[$value[csf('id')]]=$value[csf('size_id')];
				$ratio_wise_bundle[$value[csf('size_id')]]+=$value[csf('total_bundle')];
				$order_wise_size_total[$value[csf('order_id')]]+=$value[csf('size_qty')];
				$order_total_bundle[$value[csf('order_id')]]+=$value[csf('total_bundle')];
				$ratio_wise_size_qty[$value[csf('size_id')]]+=$value[csf('size_qty')];
				$total_bundle+=$value[csf('total_bundle')];
				$total_size_qty+=$value[csf('size_qty')];
			}


			$table_width=400+count($total_size_arr)*60;
		?>
    	<table align="left" cellspacing="0" width="<?php echo $table_width; ?>" border="1" rules="all" class="rpt_table" >          	
            <thead bgcolor="#dddddd" align="center">
            	<tr>
					<th align="left" colspan="<?php echo count($total_size_arr)+3; ?>">Gmts Qty. Details</th>					
                </tr>
              	<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="200" rowspan="2">Order No</th>
					<th width="150" rowspan="2">Total</th>
					<th  colspan="<?php echo count($total_size_arr); ?>">Size</th>
								
                </tr>
                <tr>
                	<?php 
                	foreach ($total_size_arr as $id => $size_id) {
                	?>
                		<th width="60"  ><?php echo $size_arr[$size_id]; ?></th>
                	<?php
                	}
                	?>
										
                </tr>
            </thead>
                <tbody> 
                <? 
				$j=1; 	
                foreach ($order_wise_size_qty as $order_id => $order_data) {
                	 
                    	?>
                           	<tr>
                               <td align="center"><? echo $j;  ?></td>
                               <td style="word-wrap:break-word"><? echo $po_arr[$order_id]; ?></td>
                               <td style="word-wrap:break-word" align="center"><? echo $order_wise_size_total[$order_id]; ?></td>
                           		<?php 
			                	foreach ($total_size_arr as $id => $size_id) {
			                	?>
			                		<td align="center"><?php echo $order_data[$size_id]; ?></td>
			                	<?php
			                	}
			                	?>

                          	</tr>
               	 		<?
						$j++;
                    
                }        
            ?>		   
               <tr bgcolor="#BBBBBB">
                   <td  colspan="2"  align="right"> Total</td>
                   <td align="center"><?php echo $total_size_qty; ?></td>
                   <?php 
	            	foreach ($total_size_arr as $id => $size_id) {
	            	?>
	            		<td align="center"><?php echo $ratio_wise_size_qty[$size_id]; ?></td>
	            	<?php
	            	}
	            	?>
                </tr>
			</tbody>
		</table>
    </div>
	<?
	$sql_query="SELECT po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order 
	from wo_po_color_size_breakdown 
	where item_number_id=$gmt_item_id and job_no_mst='".$job_no."' and color_number_id=$color_id and status_active=1 and is_deleted=0 order by size_order, country_ship_date, country_type";
	// echo $sql_query;
	$res = sql_select($sql_query);
	$sizeId_arr=array();
	foreach($res as $row)
	{
		$sizeId_arr[$row[csf('size_number_id')]] += $row[csf("plan_cut_qnty")];
	}

	$size_bl_qty_arr=return_library_array("SELECT sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b, ppl_cut_lay_mst m where m.id=b.mst_id and m.job_no='".$job_no."' and b.id=a.dtls_id and b.gmt_item_id=$gmt_item_id and b.color_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and a.hold=0 group by a.size_id",'size_id','size_qty');

	$distributed_qty_arr=return_library_array("SELECT size_id, sum(marker_qty) as marker_qty from ppl_cut_lay_size where mst_id=".$data[1]." and dtls_id=".$data[2]." and status_active=1 GROUP BY size_id","size_id","marker_qty");

	$size_set_mstid=return_field_value("id","ppl_size_set_mst","sizeset_no='$data[3]' ","id"); 


	$color_size_result=sql_select("select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$cbo_color_id and status_active=1 and is_deleted=0 order by id";
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);


	$data_array_strip=sql_select("select id, yarn_color_id, production_color_percentage, process_loss,cons_per_dzn from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	// echo "select id, yarn_color_id, production_color_percentage, process_loss,cons_per_dzn from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
	$yarnColorArr=array();
	foreach ($data_array_strip as $row)
	{
		$yarnColorArr[$row[csf('yarn_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		$yarnColorArr[$row[csf('yarn_color_id')]]['process_loss']=$row[csf('process_loss')];
		$cons_per_dzn = $row[csf('cons_per_dzn')];
	}
	unset($data_array_strip);
	$sizeSummArr=array();
	foreach($yarnColorArr as $ycolor=>$ycolorVal)
	{
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			// echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$gmt_size_id]+=$colorSizeQty;
		}
	}

	// $data_array_strip_update=sql_select("SELECT cons_per_dzn from ppl_size_set_consumption where mst_id=$size_set_mstid and item_number_id=$gmt_item_id and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	?>

	<div style="width:1200px; float:left;"> 
        <table style="margin-top:20px;" align="left" cellspacing="0" width="<?php echo $table_width; ?>" border="1" rules="all" class="rpt_table" > 	
            <thead bgcolor="#dddddd" align="center">
            	<tr>
					<th align="center" colspan="<?php echo count($total_size_arr)+3; ?>">Production Weight Details(LBS/DZ)</th>
				</tr>
				
              	<tr>
					<th width="40"  rowspan="3">SL</th>
					<th width="200" rowspan="3">Order No</th>
					<th width="150" rowspan="3">Total Weight(LBS/DZ)</th>
					<th colspan="<?php echo count($total_size_arr); ?>">Total Size Wise Weight [LBS/DZN]</th>
										
                </tr>
				<tr>
					<th colspan="<?php echo count($total_size_arr); ?>">Size</th>
				</tr>
                <tr>
                	<?php 
                	foreach ($sizeId_arr as $id => $size_id) {
                	?>
                		<th width="60"  ><?php echo $size_arr[$id]; ?></th>
                	<?php
                	}
                	?>
										
                </tr>
            </thead>			
			<tbody> 
                <?  
                foreach ($sizeId_arr as $size_id => $plan_cut_qty) 
                {
                    $lay_balance=$plan_cut_qty-$size_bl_qty_arr[$size_id]+$distributed_qty_arr[$size_id];

                    $totReqWeight +=($sizeSummArr[$size_id]/12)*$lay_balance;
                    $sizeWiseReqWeightArr[$size_id] = ($sizeSummArr[$size_id]/12)*$lay_balance;
                }
				$j=1;	
               $totalOrderQtyArr = array();
				foreach($yarnColorArr as $ycolor=>$ycolorVal)
			   {
				foreach($sizeWiseProdQtyArr as $sizeNmae=>$prodQty)
				{
			    	 $colorSizeQty=0;
					 $totalOrderQtyArr[$sizeNmae]['PrQty'] += (($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));

				}
			      
			   }
			   
                foreach ($order_wise_bundle as $order_id => $order_data) 
                {              	
            	    ?>
                   	<tr>
                       <td align="center"><? echo $j;  ?></td>
                       <td style="word-wrap:break-word"><? echo $po_arr[$order_id]; ?></td>
                       <td align="center"><?=number_format(($cons_per_dzn*2.2046226),4); ?></td>
                   		<?php 
	                	foreach ($sizeId_arr as $size_id => $plan_cut_qty) 
						{
							$sizeRatio=$sizeWiseReqWeightArr[$size_id]/$totReqWeight;
							// echo $sizeWiseReqWeightArr[$size_id]."/".$totReqWeight."<br>";
							$dataKey = $totalOrderQtyArr[$size_id]['PrQty'];
	                		?>
	                		<td align="center"><?=number_format($dataKey,4);?></td>
	                		<?php
	                	}
	                	?>

                  	</tr>
       	 		    <?
                   $j++; 
                }        
            ?>
			</tbody>
		</table>
	</div>
	
    <div style="width:1200px;"> 
        <table style="margin-top:20px;" align="left" cellspacing="0" width="<?php echo $table_width; ?>" border="1" rules="all" class="rpt_table" > 	
            <thead bgcolor="#dddddd" align="center">
            	<tr>
					<th align="left" colspan="<?php echo count($total_size_arr)+3; ?>">Bundle Details</th>					
                </tr>
              	<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="200" rowspan="2">Order No</th>
					<th width="150" rowspan="2">Total Bundle</th>
					<th  colspan="<?php echo count($total_size_arr); ?>">Size</th>					
                </tr>
                <tr>
                	<?php 
                	foreach ($total_size_arr as $id => $size_id) {
                	?>
                		<th width="60"  ><?php echo $size_arr[$size_id]; ?></th>
                	<?php
                	}
                	?>
										
                </tr>
            </thead>
                <tbody> 
                <?  
				$j=1;	
                foreach ($order_wise_bundle as $order_id => $order_data) {
                	
            	?>
                   	<tr>
                       <td align="center"><? echo $j;  ?></td>
                       <td style="word-wrap:break-word"><? echo $po_arr[$order_id]; ?></td>
                       <td align="center"><? echo $order_total_bundle[$order_id]; ?></td>
                   		<?php 
	                	foreach ($total_size_arr as $id => $size_id) {
	                	?>
	                		<td align="center"><?php echo $order_data[$size_id]; ?></td>
	                	<?php
	                	}
	                	?>

                  	</tr>
       	 		<?
                   $j++; 
                }        
            ?>		   
               <tr bgcolor="#BBBBBB">
                   <td  colspan="2"  align="right"> Total</td>
                   <td align="center"><?php echo $total_bundle; ?></td>
                   <?php 
	            	foreach ($total_size_arr as $id => $size_id) {
	            	?>
	            		<td align="center"><?php echo $ratio_wise_bundle[$size_id]; ?></td>
	            	<?php
	            	}
	            	?>
                </tr>
			</tbody>
		</table>
        <br>

    </div>
	<div style="width:1200px;"> 
        <?php 

       $lot_data_result=sql_select("SELECT a.prod_id,a.color_name,a.lot, a.alocated_qty ,b.yarn_count_id,b.yarn_comp_type1st,b.product_name_details from ppl_cut_lay_prod_dtls a,product_details_master  b where a.prod_id =b.id and a.mst_id='$data[1]' and 
			 							a.dtls_id='$data[2]' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  ");

        ?>
        <table style="margin-top:20px;" align="left" cellspacing="0" width="850" border="1" rules="all" class="rpt_table" > 	
            <thead bgcolor="#F2F2F2" align="center">
            	<tr>
					<th align="left" colspan="6">Lot Details</th>					
                </tr>
              	<tr>
					<th width="30"  >SL</th>
					<th width="150" >Lot/Batch</th>
					<th width="100" >Count</th>
					<th width="200" >Composition</th>
					<th width="250" >Color</th>
					<th width="" >Qty (Lbs)</th>				
                </tr>
            
            </thead>
                <tbody> 
                <?  	
                $j=1;
                $total_alocated_qty=0;
                $lib_yarn_count=return_library_array( "SELECT yarn_count,id from lib_yarn_count", "id", "yarn_count");
                foreach ($lot_data_result as  $val) {
                	
            	?>
                   	<tr>
                       <td align="center"><? echo $j;  ?></td>
                       <td style="word-wrap:break-word"><? echo $val[csf('lot')]; ?></td>
                       <td align="center"><? echo $lib_yarn_count[$val[csf('yarn_count_id')]]; ?></td>
                       <td align="center"><? echo $composition[$val[csf('yarn_comp_type1st')]]; ?></td>
                       <td align="center"><? echo $val[csf('color_name')]; ?></td>
                       <td align="right"><? echo $val[csf('alocated_qty')]; ?></td>
                   		

                  	</tr>
       	 		<?
                   $j++; 
                   $total_alocated_qty+=$val[csf('alocated_qty')];
                }        
            ?>		   
               <tr bgcolor="#BBBBBB">
                   <td  colspan="5"  align="right"> Total</td>
                   <td align="right"><?php echo $total_alocated_qty; ?></td>
                   
                </tr>
			</tbody>
		</table>
		<? echo signature_table(9, $data[0], "900px"); ?>
		</div>
	</div> 
	<?
	exit(); 
}

if ($action=="load_drop_down_color_type")
{   list($po_id,$row_no,$color,$gmt_id)=explode('_',$data);
	
	$sql_dtls=sql_select("select color_type_id from ppl_cut_lay_dtls where order_ids in(".$po_id.") and color_id=$color and gmt_item_id=$gmt_id");

	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.is_confirmed=1 and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($po_id) and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}
	$status=($sql_dtls[0][csf('color_type_id')])?1:0;
	
	echo create_drop_down( "cboColorType_".$row_no, 100, $color_type_arr,"", 1, "--Select--",$sql_dtls[0][csf('color_type_id')],"",$status,0);
	exit();
}

if($action=="posize_popup")
{
	echo load_html_head_contents("PO Size Info", "../../../", 1, 1,'','','');
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
			var old=document.getElementById('txt_posize_row_id').value; 
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
			
			$('#hidden_size_id').val(id);
			$('#hidden_size_name').val(name);
		}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_size_id" id="hidden_size_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_size_name" id="hidden_size_name" class="text_boxes" value="">
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
                    $i=1; $size_row_id='';
					$sqlSize="select size_number_id from wo_po_color_size_breakdown where job_no_mst='$job_no' and color_number_id='$color' and item_number_id='$garments_item' and is_deleted=0 and status_active=1 group by size_number_id, size_order order by size_order ASC";
					$sqlRes=sql_select($sqlSize);
					$hidden_size_id=explode(",",$hidden_size_id);
                    foreach($sqlRes as $row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 
						if(in_array($row[csf("size_number_id")],$hidden_size_id)) 
						{ 
							if($size_row_id=="") $size_row_id=$i; else $size_row_id.=",".$i;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>)"> 
							<td width="50" align="center"><?=$i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i ?>" value="<?=$row[csf("size_number_id")]; ?>"/>	
								<input type="hidden" name="txt_individual" id="txt_individual<?=$i ?>" value="<?=$size_arr[$row[csf("size_number_id")]]; ?>"/>
							</td>	
							<td style="word-break:break-all"><?=$size_arr[$row[csf("size_number_id")]]; ?></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                    <input type="hidden" name="txt_posize_row_id" id="txt_posize_row_id" value="<?=$sizeid; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();

}



if($action=="deleted_message_popup")
{

	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		

		
		
		function closepopup()
		{
			parent.emailwindow.hide();
		}
		
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset>
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list" style="width: 190px;">
	            	<tr>
	            		<td width="40">Deleted<br>Message</td>
	            		<td>
	            			<input type="text" id="txt_message" name="txt_message" class="combo_boxes " style="width: 150px;"/>
	            			
	            		</td>
	            	</tr>
	            	<tr>
	            		<td colspan="2"><input style="width: 70px;" itype="button" name="close" value="Close" class="formbutton" onClick="closepopup()"></td>
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

?>