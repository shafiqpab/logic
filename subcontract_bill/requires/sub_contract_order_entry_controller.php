<?
include('../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );
	exit();
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
													<input type="text" value="<?= $body_part ?>" id="bodyPart_<?= $i ?>" name="bodyPart[]" class="text_boxes" style="width:80px" value="" /> 
												</td>
												<td>
													<input type="text" value="<?= $grey_size ?>" id="greySize_<?= $i ?>" name="greySize[]" class="text_boxes" style="width:80px" value="" /> 
												</td>
												<td>
													<input type="text" value="<?= $finish_size ?>" id="finishSize_<?= $i ?>" name="finishSize[]" class="text_boxes" style="width:80px" value="" /> 
												</td> 
												<td>
													<input type="text" value="<?= $gmts_size ?>" id="gmtsSize_<?= $i ?>" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" /> 
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
									?>
										<tr id="tr_1" class="general">
											<td>
												<input type="text" id="bodyPart_1" name="bodyPart[]" class="text_boxes" style="width:80px" value="" /> 
											</td>
											<td>
												<input type="text" id="greySize_1" name="greySize[]" class="text_boxes" style="width:80px" value="" /> 
											</td>
											<td>
												<input type="text" id="finishSize_1" name="finishSize[]" class="text_boxes" style="width:80px" value="" /> 
											</td> 
											<td>
												<input type="text" id="gmtsSize_1" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" /> 
											</td> 
											<td>
												<input type="text" id="qtyPices_1" name="qtyPices[]" class="text_boxes_numeric" style="width:80px" value="" /> 
											</td>  
											<td>
												<input type="text" id="needlePerCm_1" name="needlePerCm[]" class="text_boxes_numeric" style="width:80px" value="" /> 
											</td>  
											<td width="70">
												
												<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" /> 
												<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
											</td>
										</tr>
									<?
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
                	if($cbo_process_name==7){ $new_subprocess_array= $emblishment_wash_type;}
                	if($cbo_process_name==12){ $new_subprocess_array= $emblishment_gmts_type;}
                	if($cbo_process_name==8){ $new_subprocess_array= $emblishment_print_type;}
                	if($cbo_process_name==9){ $new_subprocess_array= $emblishment_embroy_type;}

                    $i=1; $process_row_id=''; $not_process_id_print_array=array();
					$not_process_id_print_array=array(2,30,40,72,74,75,76,100,101);
					//$not_process_id_print_array=array(1,2,3,4,101,120,121,122,123,124);
					//$process_id_print_array=array(25,31,32,33,34,39,60,63,64,65,66,68,69,70,71,82,83,84,89,90,91,93,125,129,132,133);
					$hidden_process_id=explode(",",$txt_process_id);

					if($cbo_process_name!=7 && $cbo_process_name !=12 && $cbo_process_name !=8 && $cbo_process_name !=9)
					{
	                    foreach($conversion_cost_head_array as $id=>$name)
	                    {
							if(!in_array($id,$not_process_id_print_array))
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
	                }
	                else
	                {
	                	foreach($new_subprocess_array as $id=>$name)
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	// print_r ($process);die;
	extract(check_magic_quote_gpc( $process ));
    $collarAndCuffStr = str_replace("'",'',$collarAndCuffStr);
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		if(str_replace("'",'',$update_id)=="")
		{
			if($db_type==0)
			{
				$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=238 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
			}
			else if($db_type==2)
			{
				$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=238 and company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
			}

			$id=return_next_id("id","subcon_ord_mst",1);
			$field_array="id,subcon_job,job_no_prefix,job_no_prefix_num,company_id,entry_form,location_id,team_leader,party_id,currency_id,ready_to_approved,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",238,".$cbo_location_name.",".$cbo_team_leader.",".$cbo_party_name.",".$cbo_currency.",".$cbo_approve_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;

			$txt_job_no=$new_job_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*team_leader*party_id*currency_id*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$cbo_team_leader."*".$cbo_party_name."*".$cbo_currency."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$txt_job_no=str_replace("'",'',$txt_job_no);
		}

		$field_array2="id, job_no_mst, mst_id, order_no, order_quantity, order_uom, rate, amount, order_rcv_date, delivery_date, cust_buyer, cust_style_ref, efficiency_per, material_recv_date, main_process_id, process_id, grey_req, smv, status_active, remarks, delay_for, collar_and_cuff_str, inserted_by, insert_date, is_deleted";
		$data_array2="(".$id1.",'".$txt_job_no."','".$id."',".$txt_order_no.",".$txt_order_quantity.",".$cbo_uom.",".$txt_rate.",".$txt_amount.",".$txt_order_receive_date.",".$txt_order_delivery_date.",".$txt_cust_buyer.",".$txt_style_ref.",".$txt_efficiency_per.",".$txt_material_recv_date.",".$cbo_process_name.",".$txt_process_id.",".$cbo_grey_req.",".$txt_smv.",".$cbo_status.",".$txt_details_remark.",".$cbo_delay_cause.",'".$collarAndCuffStr."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
		// echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2;oci_rollback($con); die;

		 //echo $rID2;die;
		//===========================================================================================================================================================
		$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );

		$id3=return_next_id( "id", "subcon_ord_breakdown", 1);
		$field_array3="id, mst_id, order_id, job_no_mst, item_mst_id, color_mst_id, size_mst_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, grey_dia, finish_dia, embellishment_type, description, dia_width_type,dyeing_part,color_range,dyeing_upto,lab_no,add_rate, status_active, is_deleted";
		
		$item_mst=return_library_array( "select item_mst_id, item_id from subcon_ord_breakdown where order_id='$id1' and status_active=1 and is_deleted=0 and item_mst_id!=0", "item_id", "item_mst_id");
		
		$color_mst=return_library_array( "select color_mst_id, color_id from subcon_ord_breakdown where order_id='$id1' and status_active=1 and is_deleted=0 and color_mst_id!=0", "color_id", "color_mst_id");

		$size_mst=return_library_array( "select size_mst_id, size_id from subcon_ord_breakdown where order_id='$id1' and status_active=1 and is_deleted=0 and size_mst_id!=0", "size_id", "size_mst_id");

		$hidden_embelishment_type=explode("*",str_replace("'",'',$hidden_embelishment_type));
		$hidden_description=explode("*",str_replace("'",'',$hidden_description));
		$hidden_item=explode("*",str_replace("'",'',$hidden_item));				
		$hidden_amount=explode("*",str_replace("'",'',$hidden_amount));
		$hidden_color=explode("*",str_replace("'",'',$hidden_color));           
		$hidden_gsm=explode("*",str_replace("'",'',$hidden_gsm));
		$hidden_size=explode("*",str_replace("'",'',$hidden_size));				
		$hidden_excess_cut=explode("*",str_replace("'",'',$hidden_excess_cut));
		$hidden_qnty=explode("*",str_replace("'",'',$hidden_qnty));				
		$hidden_plan_cut=explode("*",str_replace("'",'',$hidden_plan_cut));
		$hidden_rate=explode("*",str_replace("'",'',$hidden_rate));				
		$hidden_loss=explode("*",str_replace("'",'',$hidden_loss));
		$hidden_grey_dia=explode("*",str_replace("'",'',$hidden_grey_dia));     
		$hidden_finish_dia=explode("*",str_replace("'",'',$hidden_finish_dia));
		$hidden_diawidth_type=explode("*",str_replace("'",'',$hidden_diawidth_type));
		$hidden_cbo_dyeing_part=explode("*",str_replace("'",'',$hidden_cbo_dyeing_part));				
		$hidden_cbo_color_range=explode("*",str_replace("'",'',$hidden_cbo_color_range));
		$hidden_cbo_dyeing_upto=explode("*",str_replace("'",'',$hidden_cbo_dyeing_upto));     
		$hidden_txtlab=explode("*",str_replace("'",'',$hidden_txtlab));
		$hidden_txtaddrate=explode("*",str_replace("'",'',$hidden_txtaddrate));

		for ($i=0;$i<count($hidden_item);$i++)
		{
			//$color_tbl_id = return_id($hidden_color[$i], $color_library_arr, "lib_color", "id,color_name");
			//$size_tbl_id = return_id($hidden_size[$i], $size_library_arr, "lib_size", "id,size_name");

			if(str_replace("'","",$hidden_color[$i])!="")
			{
				if (!in_array(str_replace("'","",$hidden_color[$i]),$new_array_color))
				{
					$color_tbl_id = return_id( str_replace("'","",$hidden_color[$i]), $color_library_arr, "lib_color", "id,color_name","238");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_tbl_id]=str_replace("'","",$hidden_color[$i]);

				}
				else $color_tbl_id =  array_search(str_replace("'","",$hidden_color[$i]), $new_array_color);
			}
			else $color_tbl_id=0;

			if(str_replace("'","",$hidden_size[$i])!="")
			{
				if (!in_array(str_replace("'","",$hidden_size[$i]),$new_array_size))
				{
					$size_tbl_id = return_id( str_replace("'","",$hidden_size[$i]), $size_library_arr, "lib_size", "id,size_name","238");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_size[$size_tbl_id]=str_replace("'","",$hidden_size[$i]);
				}
				else $size_tbl_id =  array_search(str_replace("'","",$hidden_size[$i]), $new_array_size);
			}
			else $size_tbl_id=0;
			
			if (array_key_exists(str_replace("'","",$hidden_item[$i]),$item_mst))
			{
				$item_mst_id=$item_mst[str_replace("'","",$hidden_item[$i])];
			}
			else
			{
				$item_mst[str_replace("'","",$hidden_item[$i])]=$id3;
				$item_mst_id=$id3;
			}
			
			if(array_key_exists($color_tbl_id,$color_mst))
			{
				$color_mst_id=$color_mst[$color_tbl_id];
			}
			else
			{
				$color_mst[$color_tbl_id]=$id3;
				$color_mst_id=$id3;
			}
			
			if(array_key_exists($size_tbl_id,$size_mst))
			{
				$size_mst_id=$size_mst[$size_tbl_id];
			}
			else
			{
				$size_mst[$size_tbl_id]=$id3;
				$size_mst_id=$id3;
			}

			if ($i!=0) $data_array3 .=",";
			$data_array3.="(".$id3.",".$id.",".$id1.",'".$txt_job_no."','".$item_mst_id."','".$color_mst_id."','".$size_mst_id."','".$hidden_item[$i]."','".$color_tbl_id."','".$size_tbl_id."','".$hidden_qnty[$i]."','".$hidden_rate[$i]."','".$hidden_amount[$i]."','".$hidden_excess_cut[$i]."','".ceil($hidden_plan_cut[$i])."','".$hidden_loss[$i]."','".$hidden_gsm[$i]."','".$hidden_grey_dia[$i]."','".$hidden_finish_dia[$i]."','".$hidden_embelishment_type[$i]."','".$hidden_description[$i]."','".$hidden_diawidth_type[$i]."','".$hidden_cbo_dyeing_part[$i]."','".$hidden_cbo_color_range[$i]."','".$hidden_cbo_dyeing_upto[$i]."','".$hidden_txtlab[$i]."','".$hidden_txtaddrate[$i]."',1,0)";
			$id3=$id3+1;
		}

		//=========================================================================================
		//										Collar AND CUFF 	
		//=========================================================================================	
		$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,ord_dtls_id,ord_breakdown_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
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
					$data_array4 .="(".$id4.",'".$id."','".$id1."','".$id3."','".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id4.",'".$id."','".$id1."','".$id3."','".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}

			// echo "10** INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4;die;
			$rID4=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID4==1 &&  $flag==1) $flag=1; else $flag=0;
		}	
		
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,0);
			if($rID==1 &&  $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 &&  $flag==1) $flag=1; else $flag=0;
		}

		$rID2=sql_insert("subcon_ord_dtls",$field_array2,$data_array2,0);
		//echo $rID2;die;
		if($rID2==1 &&  $flag==1) $flag=1; else $flag=0;
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2;die;
		$rID3=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
		if($rID3==1 &&  $flag==1) $flag=1; else $flag=0;
		//echo $rID3;die;
		//echo "10**".$rID3."=".$rID2."=".$rID.'='.$flag;die;

		//==========================================================================================================================================================
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_sql = "select a.approved from subcon_ord_mst a where a.id=$update_id and a.status_active=1 and a.approved!=0 and a.is_deleted=0";
        $approved_arr=sql_select($approved_sql);
        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Approved Found";die;                
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found"; die;               
            }
        }

		$field_array="location_id*party_id*currency_id*ready_to_approved*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_party_name."*".$cbo_currency."*".$cbo_approve_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array2="order_no*order_quantity*order_uom*rate*amount*order_rcv_date*delivery_date*cust_buyer*cust_style_ref*efficiency_per*material_recv_date*main_process_id*process_id*grey_req*smv*status_active*remarks*delay_for*collar_and_cuff_str*updated_by*update_date";
		$data_array2="".$txt_order_no."*".$txt_order_quantity."*".$cbo_uom."*".$txt_rate."*".$txt_amount."*".$txt_order_receive_date."*".$txt_order_delivery_date."*".$txt_cust_buyer."*".$txt_style_ref."*".$txt_efficiency_per."*".$txt_material_recv_date."*".$cbo_process_name."*".$txt_process_id."*".$cbo_grey_req."*".$txt_smv."*".$cbo_status."*".$txt_details_remark."*".$cbo_delay_cause."*'".$collarAndCuffStr."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$field_array3="id, mst_id, order_id, job_no_mst, item_mst_id, color_mst_id, size_mst_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, grey_dia, finish_dia, embellishment_type, description, dia_width_type,dyeing_part,color_range,dyeing_upto,lab_no,add_rate, status_active, is_deleted";
		$field_array_up="item_mst_id*color_mst_id*size_mst_id*item_id*color_id*size_id*qnty*rate*amount*excess_cut*plan_cut*process_loss*gsm*grey_dia*finish_dia*embellishment_type*description*dia_width_type*dyeing_part*color_range*dyeing_upto*lab_no*add_rate*updated_by*update_date";
		
		$item_mst=return_library_array( "select item_mst_id, item_id from subcon_ord_breakdown where order_id=$update_id2 and status_active=1 and is_deleted=0 and item_mst_id!=0", "item_id", "item_mst_id");
		
		$color_mst=return_library_array( "select color_mst_id, color_id from subcon_ord_breakdown where order_id=$update_id2 and status_active=1 and is_deleted=0 and color_mst_id!=0", "color_id", "color_mst_id");

		$size_mst=return_library_array( "select size_mst_id, size_id from subcon_ord_breakdown where order_id=$update_id2 and status_active=1 and is_deleted=0 and size_mst_id!=0", "size_id", "size_mst_id");

		$hidden_embelishment_type=explode("*",str_replace("'",'',$hidden_embelishment_type));
		$hidden_description=explode("*",str_replace("'",'',$hidden_description));

		$hidden_item=explode("*",str_replace("'",'',$hidden_item));				
		$hidden_amount=explode("*",str_replace("'",'',$hidden_amount));
		$hidden_color=explode("*",str_replace("'",'',$hidden_color));           
		$hidden_gsm=explode("*",str_replace("'",'',$hidden_gsm));
		$hidden_size=explode("*",str_replace("'",'',$hidden_size));				
		$hidden_excess_cut=explode("*",str_replace("'",'',$hidden_excess_cut));
		$hidden_qnty=explode("*",str_replace("'",'',$hidden_qnty));				
		$hidden_plan_cut=explode("*",str_replace("'",'',$hidden_plan_cut));
		$hidden_rate=explode("*",str_replace("'",'',$hidden_rate));				
		$hidden_loss=explode("*",str_replace("'",'',$hidden_loss));
		$hidden_grey_dia=explode("*",str_replace("'",'',$hidden_grey_dia));																	
		$hidden_tbl_id_break=explode("*",str_replace("'",'',$hidden_tbl_id_break));
    	$update_id_dtls=implode(",",$hidden_tbl_id_break);
		$hidden_diawidth_type=explode("*",str_replace("'",'',$hidden_diawidth_type));
		$hidden_finishdia=explode("*",str_replace("'",'',$hidden_finish_dia));
		$hidden_cbo_dyeing_part=explode("*",str_replace("'",'',$hidden_cbo_dyeing_part));				
		$hidden_cbo_color_range=explode("*",str_replace("'",'',$hidden_cbo_color_range));
		$hidden_cbo_dyeing_upto=explode("*",str_replace("'",'',$hidden_cbo_dyeing_upto));     
		$hidden_txtlab=explode("*",str_replace("'",'',$hidden_txtlab));
		$hidden_txtaddrate=explode("*",str_replace("'",'',$hidden_txtaddrate));


		$id3=return_next_id( "id", "subcon_ord_breakdown", 1 ) ;
		$add_comma=0;$flag=1;$brkdown_idArr=array();

		for ($i=0;$i<count($hidden_item);$i++)
		{
			if(str_replace("'","",$hidden_color[$i])!="")
			{
				if (!in_array(str_replace("'","",$hidden_color[$i]),$new_array_color))
				{
					$color_tbl_id = return_id( str_replace("'","",$hidden_color[$i]), $color_library_arr, "lib_color", "id,color_name","238");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_tbl_id]=str_replace("'","",$hidden_color[$i]);

				}
				else $color_tbl_id =  array_search(str_replace("'","",$hidden_color[$i]), $new_array_color);
			}
			else $color_tbl_id=0;

			if(str_replace("'","",$hidden_size[$i])!="")
			{
				if (!in_array(str_replace("'","",$hidden_size[$i]),$new_array_size))
				{
					$size_tbl_id = return_id( str_replace("'","",$hidden_size[$i]), $size_library_arr, "lib_size", "id,size_name","238");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_size[$size_tbl_id]=str_replace("'","",$hidden_size[$i]);
				}
				else $size_tbl_id =  array_search(str_replace("'","",$hidden_size[$i]), $new_array_size);
			}
			else $size_tbl_id=0;
			
			$item_color_size_mst_id=0;
			
			if($hidden_tbl_id_break[$i]!="")
			{
				$item_color_size_mst_id=$hidden_tbl_id_break[$i];
			}
			else
			{
				$item_color_size_mst_id=$id3;
			}
			
			if (array_key_exists(str_replace("'","",$hidden_item[$i]),$item_mst))
			{
				$item_mst_id=$item_mst[str_replace("'","",$hidden_item[$i])];
			}
			else
			{
				$item_mst[str_replace("'","",$hidden_item[$i])]=$item_color_size_mst_id;
				$item_mst_id=$item_color_size_mst_id;
			}
			
			if(array_key_exists($color_tbl_id,$color_mst))
			{
				$color_mst_id=$color_mst[$color_tbl_id];
			}
			else
			{
				$color_mst[$color_tbl_id]=$item_color_size_mst_id;
				$color_mst_id=$item_color_size_mst_id;
			}
			
			if(array_key_exists($size_tbl_id,$size_mst))
			{
				$size_mst_id=$size_mst[$size_tbl_id];
			}
			else
			{
				$size_mst[$size_tbl_id]=$item_color_size_mst_id;
				$size_mst_id=$item_color_size_mst_id;
			}

			if($hidden_tbl_id_break[$i]!="")
			{

				if($hidden_gsm[$i]=='') $hidden_gsm[$i]='';
				if($hidden_loss[$i]=='') $hidden_loss[$i]=0;
				if($hidden_finishdia[$i]=='') $hidden_finishdia[$i]='';
				if($hidden_excess_cut[$i]=='') $hidden_excess_cut[$i]=0;
				if($hidden_embelishment_type[$i]=='') $hidden_embelishment_type[$i]='';
				if($hidden_diawidth_type[$i]=='') $hidden_diawidth_type[$i]='';
				if($hidden_cbo_dyeing_part[$i]=='') $hidden_cbo_dyeing_part[$i]='';
				if($hidden_cbo_color_range[$i]=='') $hidden_cbo_color_range[$i]='';
				if($hidden_cbo_dyeing_upto[$i]=='') $hidden_cbo_dyeing_upto[$i]='';
				if($hidden_txtlab[$i]=='') $hidden_txtlab[$i]='';
				if($hidden_txtaddrate[$i]=='') $hidden_txtaddrate[$i]='';
				$brkdown_id=str_replace("'",'',$hidden_tbl_id_break[$i]);
				$brkdown_idArr[$brkdown_id]['item_mst_id']=$item_mst_id;
				$brkdown_idArr[$brkdown_id]['color_mst_id']=$color_mst_id;
				$brkdown_idArr[$brkdown_id]['size_mst_id']=$size_mst_id;
				
				$brkdown_idArr[$brkdown_id]['hidden_item']=$hidden_item[$i];
				$brkdown_idArr[$brkdown_id]['color_tbl_id']=$color_tbl_id;
				$brkdown_idArr[$brkdown_id]['size_tbl_id']=$size_tbl_id;
				$brkdown_idArr[$brkdown_id]['hidden_qnty']=$hidden_qnty[$i];
				$brkdown_idArr[$brkdown_id]['hidden_rate']=$hidden_rate[$i];
				$brkdown_idArr[$brkdown_id]['hidden_amount']=$hidden_amount[$i];
				$brkdown_idArr[$brkdown_id]['hidden_excess_cut']=$hidden_excess_cut[$i];
				$brkdown_idArr[$brkdown_id]['hidden_plan_cut']=ceil($hidden_plan_cut[$i]);
				$brkdown_idArr[$brkdown_id]['hidden_loss']=$hidden_loss[$i];
				$brkdown_idArr[$brkdown_id]['hidden_gsm']=$hidden_gsm[$i];
				$brkdown_idArr[$brkdown_id]['hidden_grey_dia']=$hidden_grey_dia[$i];
				$brkdown_idArr[$brkdown_id]['hidden_finishdia']=$hidden_finishdia[$i];
				$brkdown_idArr[$brkdown_id]['hidden_embelishment_type']=$hidden_embelishment_type[$i];
				$brkdown_idArr[$brkdown_id]['hidden_description']=$hidden_description[$i];
				$brkdown_idArr[$brkdown_id]['hidden_diawidth_type']=$hidden_diawidth_type[$i];
				$brkdown_idArr[$brkdown_id]['hidden_cbo_dyeing_part']=$hidden_cbo_dyeing_part[$i];
				$brkdown_idArr[$brkdown_id]['hidden_cbo_color_range']=$hidden_cbo_color_range[$i];
				$brkdown_idArr[$brkdown_id]['hidden_cbo_dyeing_upto']=$hidden_cbo_dyeing_upto[$i];
				$brkdown_idArr[$brkdown_id]['hidden_txtlab']=$hidden_txtlab[$i];
				$brkdown_idArr[$brkdown_id]['hidden_txtaddrate']=$hidden_txtaddrate[$i];
			}
			else
			{
				if ($add_comma!=0) $data_array3 .=",";
				$data_array3 .="(".$id3.",".$update_id.",".$update_id2.",".$txt_job_no.",'".$item_mst_id."','".$color_mst_id."','".$size_mst_id."','".$hidden_item[$i]."','".$color_tbl_id."','".$size_tbl_id."','".$hidden_qnty[$i]."','".$hidden_rate[$i]."','".$hidden_amount[$i]."','".$hidden_excess_cut[$i]."','".ceil($hidden_plan_cut[$i])."','".$hidden_loss[$i]."','".$hidden_gsm[$i]."','".$hidden_grey_dia[$i]."','".$hidden_finishdia[$i]."','".$hidden_embelishment_type[$i]."','".$hidden_description[$i]."','".$hidden_diawidth_type[$i]."','".$hidden_cbo_dyeing_part[$i]."','".$hidden_cbo_color_range[$i]."','".$hidden_cbo_dyeing_upto[$i]."','".$hidden_txtlab[$i]."','".$hidden_txtaddrate[$i]."',1,0)";
				$id3=$id3+1;
				$add_comma++;
			}
		}

		$color_qty_dtls=explode("__",str_replace("'",'',$hidden_color_qty_breakdown));		
		$bid=return_next_id( "id", "subcon_ord_color_breakdown", 1 ) ;
		$color_field_array="id, subcon_ord_breakdown_id, order_id, job_no_mst, qnty, color_id, status_active, inserted_by, insert_date";
	
		foreach ($color_qty_dtls as $colordtlsdata) {
			$color_dtls_arr=explode("###",$colordtlsdata);	
			foreach ($color_dtls_arr as $key=> $val ){
				$color_val=explode("***", $val);		
				$delete_id=$color_val[3];					
				$rId6=execute_query( "delete from subcon_ord_color_breakdown where subcon_ord_breakdown_id=$color_val[2]",0);
				if(str_replace("'","",$color_val[0])!="")
				{
					if (!in_array(str_replace("'","",$color_val[0]),$new_array_color))
					{
						$color_order_id = return_id( str_replace("'","",$color_val[0]), $color_library_arr, "lib_color", "id,color_name","238");
						$new_array_color[$color_order_id]=str_replace("'","",$color_val[0]);
					}
					else $color_order_id =  array_search(str_replace("'","",$color_val[0]), $new_array_color);
				}
				else
				{
					$color_order_id=0;
				}
				if($color_val[2]==""){continue;}
				if ($comma!=0) $color_data_array .=",";

				$color_data_array .="(".$bid.",".$color_val[2].",".$update_id2.",".$txt_job_no.",".$color_val[1].",'".$color_order_id."','1','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				$comma++;$bid++;
			}
		}
	
		$breakID=sql_insert("subcon_ord_color_breakdown",$color_field_array,$color_data_array,1);

		//   echo "10**INSERT INTO subcon_ord_color_breakdown (".$color_field_array.") VALUES ".$color_data_array; die;
		//	die;

		//=========================================================================================
		//										Collar AND CUFF 	
		//=========================================================================================	
		$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,ord_dtls_id,ord_breakdown_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
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
					$data_array4 .="(".$id6.",".$update_id.",".$update_id2.",'".$id3."','".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id6.",".$update_id.",".$update_id2.",'".$id3."','".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}
			// echo "10**INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4; die;
			$rID6=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID6==1 &&  $flag==1) $flag=1; else $flag=0;
			/* if($flag ==1)
			{
				$rID7=execute_query( "delete from subcon_ord_collar_and_cuff_dtls where ord_dtls_id=$update_id2",0);
				 
				if($rID7==1 &&  $flag==1) $flag=1; else $flag=0;
			} */
			$rID6=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID6==1 &&  $flag==1) $flag=1; else $flag=0;
			 if($flag ==1)
			{
				$rID7=execute_query( "delete from subcon_ord_collar_and_cuff_dtls where ord_dtls_id=$update_id2",0);
				if($rID7){
					$rID8=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
				}
				if($rID8==1 &&  $flag==1) $flag=1; else $flag=0;
			} 

			
		}	
		
		//echo $data_array2;die;
		//echo bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array_up,$data_array_up,$id_arr); die;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID==1 &&  $flag==1) $flag=1; else $flag=0;

		$rID2=sql_update("subcon_ord_dtls",$field_array2,$data_array2,"id",$update_id2,0);
		if($rID2==1 &&  $flag==1) $flag=1; else $flag=0;

		$id_break=implode(',',$hidden_tbl_id_break);
		//print_r ($hidden_tbl_id_break);die;
		foreach($brkdown_idArr as $colorsizeMstId=>$val)
		{
			//$brk_data_array_up="";
			$brk_data_array_up="'".$val['item_mst_id']."'*'".$val['color_mst_id']."'*'".$val['size_mst_id']."'*'".$val['hidden_item']."'*'".$val['color_tbl_id']."'*'".$val['size_tbl_id']."'*'".$val['hidden_qnty']."'*'".$val['hidden_rate']."'*'".$val['hidden_amount']."'*'".$val['hidden_excess_cut']."'*'".$val['hidden_plan_cut']."'*'".$val['hidden_loss']."'*'".$val['hidden_gsm']."'*'".$val['hidden_grey_dia']."'*'".$val['hidden_finishdia']."'*'".$val['hidden_embelishment_type']."'*'".$val['hidden_description']."'*'".$val['hidden_diawidth_type']."'*'".$val['hidden_cbo_dyeing_part']."'*'".$val['hidden_cbo_color_range']."'*'".$val['hidden_cbo_dyeing_upto']."'*'".$val['hidden_txtlab']."'*'".$val['hidden_txtaddrate']."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//echo "10**=A=".$colorsizeMstId.'<br>'; 
			//echo $field_array_up."=".$brk_data_array_up."=".$colorsizeMstId;
			$rID3=sql_update("subcon_ord_breakdown",$field_array_up,$brk_data_array_up,"id","".$colorsizeMstId."",1); 
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}
		if($data_array_up!="")
		{
			//$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array_up,$data_array_up,$id_arr),1);
			//if($rID3==1 &&  $flag==1) $flag=1; else $flag=0;
		}
		if($data_array3!="")
		{
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
			$rID4=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID4==1 &&  $flag==1) $flag=1; else $flag=0;
		}
		$deleted_id=str_replace("'",'',$txt_deleted_id);
		if ($deleted_id!="")
		{
			$rID5=execute_query( "delete from subcon_ord_breakdown where id in ($deleted_id)",0);
			if($rID5==1 &&  $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID7."=".$rID6."=".$rID5."=".$rID4."=".$rID3."=".$rID2."=".$rID.'=='.$flag;oci_rollback($con);die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2)
		{

			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			 else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here=======================================================================
	{
		$con = connect();

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$progr_sql = "select b.id from subcon_planning_dtls b,subcon_planning_plan_dtls c where c.dtls_id=b.id and c.po_id=$update_id2 and b.status_active=1   and b.is_deleted=0";
		//echo "10**=".$progr_sql;die;
        $result_prog=sql_select($progr_sql);
		foreach($result_prog as $row)
		{
			$prog_no=$row[csf("id")];
		}

		if($prog_no!="")
		{
			echo "13**Next Process found,Delete not allowed, Program- ".$prog_no;disconnect($con);die;

		}


		$approved_sql = "select a.approved from subcon_ord_mst a where a.id=$update_id and a.status_active=1 and a.approved!=0 and a.is_deleted=0";
        $approved_arr=sql_select($approved_sql);
        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Approved Found";disconnect($con);die;                
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found"; disconnect($con);die;               
            }
        }

		$update_id2=str_replace("'","",$update_id2);
		$subCon_m_rec=sql_select("select a.sys_no,sum(b.quantity) as quantity from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and b.status_active in (2) and b.is_deleted=0 and a.status_active=1 and is_deleted=0 and b.order_id in($update_id2) group by a.sys_no");
		$sub_order_qty=0;
		foreach($subCon_m_rec as $row)
		{
			if($row[csf("quantity")]>0)
			{
				$sub_order_qty=$row[csf("quantity")];
				$sys_no=$row[csf("sys_no")];
			}
		}
		if($sub_order_qty>0)
		{
			echo "14**".$sub_order_qty."**".$sys_no;disconnect($con);die;
		}
		//if ( $delete_master_info==1 )
		//{
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);
			//$rID=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$update_id,1);
			$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
			$rID2 = 1;
			if($collarAndCuffStr !="" )
			{
				$rID2 = sql_delete("subcon_ord_collar_and_cuff_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'ord_dtls_id',$update_id2,1);
			}
		//}
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);;
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
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Job No</th>
                            <th width="100">Order No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                        <td> <input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                            <?
                               echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                        </td>
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                        </td>
                        <td>
                    		<input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" />
                        </td>
                        <td>
                    		<input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'sub_contract_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_job_search_list_view")
{
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];

	if($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if($search_type==1)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num='$search_job'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no='$search_order'"; else $search_order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==2)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==3)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order'"; else $search_order_cond="";
	}

	if($party_id!=0) $party_id_cond=" and party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (3=>$production_process,4=>$party_arr,5=>$service_type);

	if($db_type==0)
	{
		$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.job_no_mst, b.order_no, b.order_rcv_date, b.delivery_date, b.main_process_id, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active in (1,2) $order_rcv_date $company $search_job_cond $search_order_cond $party_id_cond order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.job_no_mst, b.order_no, b.order_rcv_date, b.delivery_date, b.main_process_id, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active in (1,2) $order_rcv_date $company $search_job_cond $search_order_cond $party_id_cond order by a.id DESC";
	}
		 //echo $sql;die;
	 echo  create_list_view("list_view", "Job No,Year,Order No,Process,Party Name,Order Date,Delivery Date","70,80,100,100,100,70,70","740","250",0,$sql, "js_set_value","subcon_job","",1,"0,0,0,main_process_id,party_id,0,0",$arr,"job_no_prefix_num,year,order_no,main_process_id,party_id,order_rcv_date,delivery_date", "",'','0,0,0,0,0,3,3') ;
	exit();
}
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,subcon_job,company_id,team_leader,location_id,party_id,currency_id,ready_to_approved,approved from subcon_ord_mst where subcon_job='$data'" );
	// $sql="select id,subcon_job,company_id,location_id,party_id,currency_id,ready_to_approved from subcon_ord_mst where subcon_job='$data'";
	// echo $sql;die();
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/sub_contract_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/sub_contract_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_team_leader').value		= '".$row[csf("team_leader")]."';\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('txt_process_id').value		= '".$row[csf("service_type")]."';\n";
		echo "document.getElementById('cbo_currency').value			= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_approve_status').value			= '".$row[csf("ready_to_approved")]."';\n";
	    echo "document.getElementById('update_id').value          	= '".$row[csf("id")]."';\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";
		if($row[csf("approved")]==1)  echo "$('#approved').text('Approved');\n";
        else if($row[csf("approved")]==3) echo "$('#approved').text('Partial Approved');\n";
		else echo "$('#approved').text('');\n";
	}
	exit();
}

if ($action=="load_php_data_to_form_dtls")
{
	$nameArray=sql_select( "select id, order_no, order_quantity, order_uom, rate, amount, order_rcv_date, delivery_date, cust_buyer, cust_style_ref, main_process_id, process_id, grey_req,efficiency_per,material_recv_date, smv, status_active, remarks, delay_for,collar_and_cuff_str from subcon_ord_dtls where id='$data'" );

	$subCon_m_rec=sql_select("select order_id from sub_material_dtls b where order_id='$data' and status_active in (2) and is_deleted=0");
	$sub_order_id=0;
	foreach($subCon_m_rec as $row)
	{
		if($row[csf("order_id")]>0)
		{
			$sub_order_id=$row[csf("order_id")];
		}
	}
	//echo $sub_order_id.'SSSSSSS';
	foreach ($nameArray as $row)  //number_format($number, 2, '.', '');
	{
		if($row[csf("main_process_id")]==7){ $new_subprocess_array= $emblishment_wash_type;}
		if($row[csf("main_process_id")]==12){ $new_subprocess_array= $emblishment_gmts_type;}
		if($row[csf("main_process_id")]==8){ $new_subprocess_array= $emblishment_print_type;}
		if($row[csf("main_process_id")]==9){ $new_subprocess_array= $emblishment_embroy_type;}

			$sub_po_arr[$row[csf("order_id")]];

		if($row[csf("main_process_id")]!=7 && $row[csf("main_process_id")] !=12 && $row[csf("main_process_id")] !=8 && $row[csf("main_process_id")] !=9)
		{
			$process_name='';
			$process_id_array=explode(",",$row[csf("process_id")]);
			foreach($process_id_array as $val)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}
		}
		else
		{
			$process_name='';
			$process_id_array=explode(",",$row[csf("process_id")]);
			foreach($process_id_array as $val)
			{
				if($process_name=="") $process_name=$new_subprocess_array[$val]; else $process_name.=",".$new_subprocess_array[$val];
			}
		}

		echo "document.getElementById('txt_order_no').value 	 				= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_order_quantity').value				= '".$row[csf("order_quantity")]."';\n";
		echo "document.getElementById('cbo_uom').value			 				= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_rate').value		 					= '".number_format($row[csf("rate")],2,'.','')."';\n";
		echo "document.getElementById('txt_amount').value		 				= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_order_receive_date').value			= '".change_date_format($row[csf("order_rcv_date")])."';\n";
		echo "document.getElementById('txt_order_delivery_date').value			= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('txt_cust_buyer').value		 			= '".$row[csf("cust_buyer")]."';\n";
		echo "document.getElementById('txt_style_ref').value 		 			= '".$row[csf("cust_style_ref")]."';\n";
		echo "document.getElementById('txt_efficiency_per').value 		 			= '".$row[csf("efficiency_per")]."';\n";
		echo "document.getElementById('txt_material_recv_date').value 		 			= '".change_date_format($row[csf("material_recv_date")])."';\n";

		echo "document.getElementById('cbo_process_name').value				 	= '".$row[csf("main_process_id")]."';\n";
		//echo "set_multiselect('cbo_process','0','1','".$row[csf("process_id")]."','__populate_sub_group_info__requires/sub_contract_order_entry_controller');\n";
		echo "document.getElementById('txt_process_name').value 				= '".$process_name."';\n";
		echo "document.getElementById('txt_process_id').value			 		= '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('cbo_grey_req').value			 			= '".$row[csf("grey_req")]."';\n";
		echo "document.getElementById('txt_smv').value			 				= '".$row[csf("smv")]."';\n";
		echo "document.getElementById('cbo_status').value			 			= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txt_details_remark').value				= '".$row[csf("remarks")]."';\n";
		echo "set_multiselect('cbo_delay_cause','0','1','".$row[csf("delay_for")]."','0');\n";
		echo "document.getElementById('update_id2').value             			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('collarAndCuffStr').value             	= '".$row["COLLAR_AND_CUFF_STR"]."';\n";
		if($sub_order_id>0)
		{
			echo "active_inactive(1);\n";
		}
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";

                if($row[csf("main_process_id")] == 3 || $row[csf("main_process_id")] == 4){
                   echo "$('#grey_req_caption').css('color', 'blue');\n";

                }else{
                   echo "$('#grey_req_caption').css('color', '#444');\n";
                }
	}

	$collor_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');

	$qry_result=sql_select( "select id,mst_id,order_id,item_id,color_id,size_id,qnty,rate,amount,excess_cut,plan_cut,process_loss,grey_dia,finish_dia,gsm,dia_width_type,dyeing_part,color_range,dyeing_upto,lab_no,add_rate from subcon_ord_breakdown where order_id='$data'");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		if($item_id=="") $item_id=$row[csf("item_id")]; else $item_id.="*".$row[csf("item_id")];
        if($grey_dia_id=="") $grey_dia_id=$row[csf("grey_dia")]; else $grey_dia_id.="*".$row[csf("grey_dia")];
        if($finish_dia_id=="") $finish_dia_id=$row[csf("finish_dia")]; else $finish_dia_id.="*".$row[csf("finish_dia")];
		if($color_fild_id=="") $color_fild_id=$collor_arr[$row[csf("color_id")]]; else $color_fild_id.="*".$collor_arr[$row[csf("color_id")]];
		if($size_number_id=="") $size_number_id=$size_arr[$row[csf("size_id")]]; else $size_number_id.="*".$size_arr[$row[csf("size_id")]];
		if($qnty_val=="") $qnty_val=$row[csf("qnty")]; else $qnty_val.="*".$row[csf("qnty")];
		if($rate_val=="") $rate_val=$row[csf("rate")]; else $rate_val.="*".$row[csf("rate")];
		if($amount_val=="") $amount_val=$row[csf("amount")]; else $amount_val.="*".$row[csf("amount")];
		if($excesscut_val=="") $excesscut_val=$row[csf("excess_cut")]; else $excesscut_val.="*".$row[csf("excess_cut")];
		if($plancut_val=="") $plancut_val=$row[csf("plan_cut")]; else $plancut_val.="*".$row[csf("plan_cut")];
		if($process_loss=="") $process_loss=$row[csf("process_loss")]; else $process_loss.="*".$row[csf("process_loss")];
		if($gsm=="") $gsm=$row[csf("gsm")]; else $gsm.="*".$row[csf("gsm")];
		if($dia_width_type=="") $dia_width_type=$row[csf("dia_width_type")]; else $dia_width_type.="*".$row[csf("dia_width_type")];
		if($cbo_dyeing_part=="") $cbo_dyeing_part=$row[csf("dyeing_part")]; else $cbo_dyeing_part.="*".$row[csf("dyeing_part")];
		if($cbo_color_range=="") $cbo_color_range=$row[csf("color_range")]; else $cbo_color_range.="*".$row[csf("color_range")];
		if($cbo_dyeing_upto=="") $cbo_dyeing_upto=$row[csf("dyeing_upto")]; else $cbo_dyeing_upto.="*".$row[csf("dyeing_upto")];
		if($txtlab=="") $txtlab=$row[csf("lab_no")]; else $txtlab.="*".$row[csf("lab_no")];
		if($txtaddrate=="") $txtaddrate=$row[csf("add_rate")]; else $txtaddrate.="*".$row[csf("add_rate")];
	}
	echo "document.getElementById('hidden_item').value 	 					= '".$item_id."';\n";
	echo "document.getElementById('hidden_color').value 	 				= '".$color_fild_id."';\n";
	echo "document.getElementById('hidden_grey_dia').value 	 				= '".$grey_dia_id."';\n";
	echo "document.getElementById('hidden_finish_dia').value 	 				= '".$finish_dia_id."';\n";
	echo "document.getElementById('hidden_size').value 	 					= '".$size_number_id."';\n";
	echo "document.getElementById('hidden_qnty').value 	 					= '".$qnty_val."';\n";
	echo "document.getElementById('hidden_rate').value 	 					= '".$rate_val."';\n";
	echo "document.getElementById('hidden_amount').value 	 					= '".$amount_val."';\n";
	echo "document.getElementById('hidden_excess_cut').value 	 				= '".$excesscut_val."';\n";
	echo "document.getElementById('hidden_plan_cut').value 	 					= '".$plancut_val."';\n";
	echo "document.getElementById('hidden_loss').value 	 						= '".$process_loss."';\n";
	echo "document.getElementById('hidden_tbl_id_break').value 	 				= '".$id."';\n";
	echo "document.getElementById('hidden_gsm').value 	 				= '".$gsm."';\n";
	echo "document.getElementById('hidden_diawidth_type').value 	 				= '".$dia_width_type."';\n";
	echo "document.getElementById('hidden_cbo_dyeing_part').value 	 				= '".$cbo_dyeing_part."';\n";
	echo "document.getElementById('hidden_cbo_color_range').value 	 				= '".$cbo_color_range."';\n";
	echo "document.getElementById('hidden_cbo_dyeing_upto').value 	 				= '".$cbo_dyeing_upto."';\n";
	echo "document.getElementById('hidden_txtlab').value 	 				= '".$txtlab."';\n";
	echo "document.getElementById('hidden_txtaddrate').value 	 				= '".$txtaddrate."';\n";
	exit();
}
if($action=="subcontract_dtls_list_view")
{
	$sql = "select id,job_no_mst,order_no,order_quantity,order_uom,rate,amount,order_rcv_date,delivery_date,cust_buyer,cust_style_ref,main_process_id,status_active from subcon_ord_dtls where status_active<>3 and job_no_mst='$data' and is_deleted=0";

	$arr=array(2=>$unit_of_measurement,9=>$production_process,10=>$row_status);
	echo  create_list_view("list_view", "Order No,Order Qty,Order UOM,Rate/Unit,Amount,Ord Receive Date,Delivery Date,Cust Buyer,Cust Style Ref.,process,Status", "100,80,50,70,80,120,110,80,80,140,80","1070","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form_dtls'", 1, "0,0,order_uom,0,0,0,0,0,0,main_process_id,status_active", $arr , "order_no,order_quantity,order_uom,rate,amount,order_rcv_date,delivery_date,cust_buyer,cust_style_ref,main_process_id,status_active", "requires/sub_contract_order_entry_controller","","0,2,0,2,2,3,3,0,0,0,0") ;

	exit();
}

if($action=="order_qnty_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$order_no=$data[0];
	$process_id=$data[1];
	$order_id=$data[2];
	$company_id=str_replace("'","",$data[6]);
	$party_id=str_replace("'","",$data[7]);
	$break_id=$data[8];
	$mst_id=$data[9];
	$currency_id=$data[10];
	

	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

	$subCon_m_rec=sql_select("select order_id from sub_material_dtls b where order_id='$order_id' and status_active in (2) and is_deleted=0");

	$sub_order_id=0;
	foreach($subCon_m_rec as $row)
	{
		if($row[csf("order_id")]>0)
		{
			$sub_order_id=$row[csf("order_id")];
		}
	}

	$recipie_chk=sql_select("select b.po_id from pro_batch_create_dtls b,pro_recipe_entry_mst c  where b.mst_id=c.batch_id and b.po_id='$order_id' and b.status_active in (1) and b. is_deleted=0 and c.status_active in (1) and c. is_deleted=0");

	$sub_order_id=0;
	foreach($recipie_chk as $row)
	{
		if($row[csf("po_id")]>0)
		{
			$recipie_id=$row[csf("po_id")];
		}
	}
	if($process_id==3 || $process_id==4){
		if($recipie_id>0) {
			$disabled_cond="disabled";
			$disabled_type=1;
			}
			else {
			$disabled_cond="";$disabled_type=0;
			}
	}else{
		if($sub_order_id>0) {
			$disabled_cond="disabled";
			$disabled_type=1;
			}
			else {
			$disabled_cond="";$disabled_type=0;
			}
	}
	
	$check_recive_qty=sql_select("select order_id,material_description,gsm,color_id,fin_dia,grey_dia, sum(quantity) as quantity from sub_material_dtls where order_id='$order_id' and is_deleted=0 and status_active in (2) group by order_id,material_description,gsm,color_id,fin_dia,grey_dia");
	$check_recive_qty_arr=array();
	foreach ($check_recive_qty as $row) {
		$key=$row[csf("material_description")]."_".$row[csf("gsm")]."_".$row[csf("grey_dia")]."_".$row[csf("fin_dia")]."_".$color_arr[$row[csf("color_id")]];
		//$key=hello;
		$check_recive_qty_arr[$key]=$row[csf("quantity")]; ?>
		<!--<script> var check_recive_qty_arr=<?// echo json_encode($check_recive_qty_arr);?></script>-->

        <?
	}
	//var_dump($check_recive_qty_arr);


	?>
	<script>
		var check_recive_qty_arr='<? echo json_encode($check_recive_qty_arr); ?>';
		var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 group by color_name ", "color_name" ), 0, -1); ?> ];
		var str_lab = [<? echo substr(return_library_autocomplete( "select sys_no from lab_color_ingredients_mst where status_active=1 and is_deleted=0 group by sys_no ", "sys_no" ), 0, -1); ?> ];
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active=1 and is_deleted=0 group by size_name ", "size_name" ), 0, -1); ?> ];
		var mst_id='<?=$mst_id;?>';
		function check_receive_qnty2(i)
		{
			var txtorderquantity = $('#txtorderquantity_'+i).val();
			var prev = $('#hiddenOrderQuantity_'+i).val();
			var key = $('#txtitem_'+i).val()+"_"+$('#txtgsm_'+i).val()+"_"+$('#txtgreydia_'+i).val()+"_"+$('#txtfinishdia_'+i).val()+"_"+$('#txtcolor_'+i).val();
			if(parseInt(txtorderquantity) < parseInt(check_recive_qty_arr[key])){
				alert("Order Quantity is not less than Receive Quantity");
				$('#txtorderquantity_'+i).val(prev);

			}
			//var response = return_global_ajax_value( data, "check_order_rcv_qnty", "", "sub_contract_order_entry_controller");
		}

		function set_auto_complete(type)
		{
			if(type=='color_return')
			{
				$(".txt_color").autocomplete({
				source: str_color
				});
			}
		}

		function set_auto_complete_size(type)
		{
			if(type=='size_return')
			{
				$(".txt_size").autocomplete({
				source: str_size
				});
			}
		}

		function set_auto_complete_lab(type)
		{
			if(type=='lab_return')
			{
				$(".txt_lab").autocomplete({
				source: str_lab
				});
			}
		}

		function add_share_row( i )
		{
			var row_num=$('#tbl_share_details_entry tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			var process_id=document.getElementById('hidden_process_id').value;
			if(process_id==2 || process_id==3 || process_id==4)
			{
				if (form_validation('txtdiawidth_'+i,'Width/Dia Type')==false )
				{
					return;
				}
			}

			i++;
			$("#tbl_share_details_entry tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_share_details_entry");
			
			var val="'0_"+i+"'";
		
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","check_receive_qnty2("+i+");");
			
			if(mst_id > 0){
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("ondblclick","openmypage_qnty("+val+");");
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onkeyup","sum_total_qnty("+val+");");
			}
			$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtaddrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtitem_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item("+i+");");
			$('#excess_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtorderquantity_'+i).removeAttr("disabled");
			$('#decreaseset_'+i).removeAttr("disabled");
			$('#txtitem_'+i).removeAttr("disabled");
			$('#txtdiawidth_'+i).removeAttr("disabled");
			$('#cbo_dyeing_part_'+i).removeAttr("disabled");
			$('#cbo_color_range_'+i).removeAttr("disabled");
			$('#cbo_dyeing_upto_'+i).removeAttr("disabled");
			$('#txtlab_'+i).removeAttr("disabled");
			$('#txtaddrate_'+i).removeAttr("disabled");
			$('#txtgsm_'+i).removeAttr("disabled");
			$('#txtgreydia_'+i).removeAttr("disabled");
			$('#txtfinishdia_'+i).removeAttr("disabled");
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtorderrate_'+i).removeAttr("disabled");
			$('#txtsize_'+i).removeAttr("disabled");
			$('#excess_'+i).removeAttr("disabled");
			$('#plan_'+i).removeAttr("disabled");
			$('#loss_'+i).removeAttr("disabled");

			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtsize_'+i).val('');
			$('#txtitem_'+i).val('');
			$('#txtdiawidth_'+i).val('');
			$('#cbo_dyeing_part_'+i).val('');
			$('#cbo_color_range_'+i).val('');
			$('#cbo_dyeing_upto_'+i).val('');
			$('#txtlab_'+i).val('');
			$('#txtaddrate_'+i).val('');
			$('#txtgsm_'+i).val('');
			$('#txtgreydia_'+i).val('');
			$('#txtfinishdia_'+i).val('');
			$('#txtcolor_'+i).val('');
			$('#txtorderrate_'+i).val('');
			$('#txtorderquantity_'+i).val('');
			$('#loss_'+i).val('');

			$('#hiddenid_'+i).val('');
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
				$("#txtsize_"+i).autocomplete({
				source: str_size
			});
			sum_total_qnty(i);
		}

		function fn_deletebreak_down_tr(rowNo)
		{
			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}

				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}

			sum_total_qnty(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;

			var descriptions=""; 
			var emblishment_type="";
			var txt_item="";
			var txt_color=""; 
			var txt_size=""; 
			var txt_order_quantity=""; 
			var txt_order_rate=""; 
			var txt_order_amount=""; 
			var excess_cut=""; 
			var plan_cut="";
			var process_loss="";
			var hidden_id=""; 
			var txt_gsm = "";
			var total_qnty="";
			var total_rate="";
			var total_amount="";
			var txt_greydia =""; 
			var txt_finishdia = "";
			var txtdiawidth = ""; 
			var cbo_dyeing_part = "";
			var cbo_color_range = "";
			var cbo_dyeing_upto = "";
			var txtlab = "";
			var txtaddrate = "";
			var colorbreakdown = ""; 
			var process_id=document.getElementById('hidden_process_id').value;

			for(var i=1; i<=tot_row; i++)
			{
				if(process_id==2 || process_id==3 || process_id==4)
				{
					if (form_validation('txtitem_'+i+'*txtdiawidth_'+i,'Item Description*Width/Dia Type')==false )
					{ 
						return;
					}
				}
				else if(process_id==7 || process_id==8 || process_id==9 || process_id==12)
				{
					if (form_validation('txtitem_'+i+'*cboembtype_'+i,'Item Description*Embellishment Type')==false )
					{
						return;
					}
				}
				else if(process_id==11)
				{
					if (form_validation('txtitem_'+i,'Item Description')==false )
					{
						return;
					}
				}
 
				if(i>1)
				{
					emblishment_type +="*";
					descriptions +="*";

					txt_item +="*";
                    txt_gsm +="*";
                    txt_greydia +="*";
                    txt_finishdia +="*";
					txt_color +="*";
					txt_size +="*";
					txt_order_quantity +="*";
					txt_order_rate +="*";
					txt_order_amount +="*";
					excess_cut +="*";
					plan_cut +="*";
					process_loss +="*";
					hidden_id +="*";
					txtdiawidth +="*";
					cbo_dyeing_part +="*";
					cbo_color_range +="*";
					cbo_dyeing_upto +="*";
					txtlab +="*";
					txtaddrate +="*";
					colorbreakdown +="__";
				}

				emblishment_type += $("#cboembtype_"+i).val();
				descriptions += $("#txtdescription_"+i).val();

				txt_item += $("#hidditemid_"+i).val();
                txt_gsm += $("#txtgsm_"+i).val();
                txt_greydia += $("#txtgreydia_"+i).val();
                txt_finishdia += $("#txtfinishdia_"+i).val();
				txt_color += $("#txtcolor_"+i).val();
				txt_size += $("#txtsize_"+i).val();
				txt_order_quantity += $("#txtorderquantity_"+i).val();
				txt_order_rate += $("#txtorderrate_"+i).val();
				txt_order_amount += $("#txtorderamount_"+i).val();
				excess_cut += $("#excess_"+i).val();
				plan_cut += $("#plan_"+i).val();
				process_loss += $("#loss_"+i).val();
				hidden_id += $("#hiddenid_"+i).val();
				txtdiawidth += $("#txtdiawidth_"+i).val();
				if(process_id==2 || process_id==3 || process_id==4){
					cbo_dyeing_part += $("#cbo_dyeing_part_"+i).val();
					cbo_color_range += $("#cbo_color_range_"+i).val();
					cbo_dyeing_upto += $("#cbo_dyeing_upto_"+i).val();
				}
				else{
					cbo_dyeing_part += 0;
					cbo_color_range += 0;
					cbo_dyeing_upto += 0;
				}
				

				txtlab += $("#txtlab_"+i).val();
				txtaddrate += $("#txtaddrate_"+i).val();
				colorbreakdown += $("#hiddencolorqtybreakdown_"+i).val();
			}

			document.getElementById('hidden_embelishment_type').value=emblishment_type;
			document.getElementById('hidden_description').value=descriptions;
			document.getElementById('hidden_itemid').value=txt_item;
            document.getElementById('hidden_gsm').value=txt_gsm;
            document.getElementById('hidden_grey_dia').value=txt_greydia;
            document.getElementById('hidden_finish_dia').value=txt_finishdia;
			document.getElementById('hidden_color').value=txt_color;
			document.getElementById('hidden_size').value=txt_size;
			document.getElementById('hidden_order_quantity').value=txt_order_quantity;
			document.getElementById('hidden_order_rate').value=txt_order_rate;
			document.getElementById('hidden_order_amount').value=txt_order_amount;
			document.getElementById('hidden_excess').value=excess_cut;
			document.getElementById('hidden_plan').value=plan_cut;
			document.getElementById('hidden_loss').value=process_loss;
			document.getElementById('hidden_tbl_id').value=hidden_id;
			document.getElementById('txt_deleted_id').value;
			document.getElementById('hidden_txtdiawidth').value=txtdiawidth;
			document.getElementById('hidden_cbo_dyeing_part').value=cbo_dyeing_part;
			document.getElementById('hidden_cbo_color_range').value=cbo_color_range;
			document.getElementById('hidden_cbo_dyeing_upto').value=cbo_dyeing_upto;
			document.getElementById('hidden_txtlab').value=txtlab;
			document.getElementById('hidden_txtaddrate').value=txtaddrate;
			document.getElementById('hidden_color_qty_breakdown').value=colorbreakdown;


			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			var currecny_id='<?=$currency_id;?>'
			var ddd={ dec_type:2, comma:0, currency:currecny_id}
			math_operation( "hidden_tot_rate", "txtorderrate_", "+", tot_row );

			/* $("#txtorderamount_"+id).val(($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1)); */
			$("#txtorderamount_"+id).val((($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1))+(($("#txtorderquantity_"+id).val()*1)*($("#txtaddrate_"+id).val()*1)));
			//$("#txt_average_rate").val(($("#hidden_tot_rate").val()*1)/tot_row*1);

			if($("#hidden_process_id").val()==1 || $("#hidden_process_id").val()==5 || $("#hidden_process_id").val()==8 || $("#hidden_process_id").val()==9 || $("#hidden_process_id").val()==10 || $("#hidden_process_id").val()==11)
			{
				$("#plan_"+id).val((($("#txtorderquantity_"+id).val()*1)+(($("#txtorderquantity_"+id).val()*1)*(($("#excess_"+id).val()*1)/100))));
			}

			math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row );
			math_operation( "txt_total_order_amount", "txtorderamount_", "+", tot_row,ddd );

			$("#txt_average_rate").val((($("#txt_total_order_amount").val()*1)/($("#txt_total_order_qnty").val()*1)).toFixed(2));
		}

		<? $color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");  ?>

		function openmypage_item(id)
		{
			
			var data=document.getElementById('hiddencompany').value+'_'+document.getElementById('hiddenparty').value+'_'+document.getElementById('hidden_process_id').value;
			page_link='sub_contract_order_entry_controller.php?action=order_qnty_item_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Item Popup', 'width=600px, height=300px, center=1, resize=0, scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidd_item_id");
				//alert (theemail.value);//return;
				var item_val=theemail.value.split("**");
				//alert(item_val[5])
				if (theemail.value!="")
				{
					document.getElementById('hidditemid_'+id).value=item_val[0];
					document.getElementById('txtitem_'+id).value=item_val[1];
					document.getElementById('txtorderrate_'+id).value=item_val[2];
					sum_total_qnty(id);
					document.getElementById('txtcolor_'+id).value=item_val[3];
					document.getElementById('txtdiawidth_'+id).value=item_val[4];
                    document.getElementById('txtgsm_'+id).value=item_val[5];

                    //alert(item_val[4]);
					//get_php_form_data(id+'*'+item_val[3], "populate_rate", "sub_contract_order_entry_controller" );
				}
			}
		}

		function openmypage_rate(id)
		{
			
			var data=document.getElementById('hiddencompany').value+'_'+document.getElementById('hiddenparty').value+'_'+document.getElementById('hiddencurrency').value;
			page_link='sub_contract_order_entry_controller.php?action=order_qnty_rate_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Item Popup', 'width=600px, height=300px, center=1, resize=0, scrolling=0','../');
			emailwindow.onclose=function()
			{
				var rate_add_new=this.contentDoc.getElementById("tot_rate"); 
				document.getElementById('hiddrateval_'+id).value=rate_add_new.value;
				document.getElementById('txtaddrate_'+id).value=rate_add_new.value;
				sum_total_qnty(id);
				/* var theemail=this.contentDoc.getElementById("tot_rate");
				var rate_val=theemail.value;
				 if (theemail.value!="")
				{
					document.getElementById('hiddrateval_'+id).value=rate_val;
					document.getElementById('txtaddrate_'+id).value=rate_val;
				} 
				parent.emailwindow.hide(); */
			}
		}
		function openmypage_qnty(id){
			var dataarr = id.split('_');
			var qty=document.getElementById('txt_total_order_qnty').value;
			page_link='sub_contract_order_entry_controller.php?action=qnty_popup&data='+dataarr[0]+'&qty='+qty	
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Qnty Dtls Popup', 'width=350px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var hidden_qtytbl_id=this.contentDoc.getElementById("hidden_qtytbl_id"); 	
				var color_qty_breakdown=this.contentDoc.getElementById("hidden_color_qty_breakdown"); 
				var hiddenorderqty=this.contentDoc.getElementById("hidden_order_amount");
				var txtorderrate=this.contentDoc.getElementById("hidden_tot_rate"); 
				var hiddenqty=this.contentDoc.getElementById("hiddenqty"); 	
				sum_total_qnty(id);				
				document.getElementById('hiddencolorqtybreakdown_'+dataarr[1]).value=color_qty_breakdown.value;
				document.getElementById('hidden_qtytbl_id').value=hidden_qtytbl_id.value;
				document.getElementById('txtorderquantity_'+dataarr[1]).value=hiddenqty.value;
				document.getElementById('hidden_tot_rate'+dataarr[1]).value=txtorderrate.value;
				document.getElementById('hidden_color_qty_breakdown'+dataarr[1]).value=hiddenorderqty.value;
		    }
		}
		function check_exchange_rate(i)
		{
			var cbo_currercy=$('#hiddencurrency').val();
			var cbo_party_name = $('#hiddenparty').val();
			var chkdyeing_part=$('#cbo_dyeing_part_'+i).val()*1;
			var chkdiawidth=$('#txtdiawidth_'+i).val()*1;
			var chkcolor_range=$('#cbo_color_range_'+i).val()*1;
			var chkdyeing_upto=$('#cbo_dyeing_upto_'+i).val()*1;
			var company_no=$('#hiddencompany').val();
			var response=return_global_ajax_value( cbo_currercy+"**"+cbo_party_name+"**"+chkdyeing_part+"**"+chkdiawidth+"**"+chkcolor_range+"**"+chkdyeing_upto+"**"+company_no, 'check_popup_rate', '', 'sub_contract_order_entry_controller');
			var response=response.split("_");
			if(response[0]==1)
			{
				$('#txtorderrate_'+i).val(response[1]);
			}
		}
	function auto_completelab(i)
	{
		var company_no=$('#hiddencompany').val();
		var supplier = return_global_ajax_value( company_no, 'lab_company_action', '', 'sub_contract_order_entry_controller');	
		supplierInfo = eval(supplier);
		$('#txtlab_'+i).autocomplete({
		 source: supplierInfo,
		 search: function( event, ui ) {
			$("#hidden_lab_id").val("");
			$("#hidden_lab_name").val("");
		},
		select: function (e, ui) {
				$(this).val(ui.item.label);
				$("#hidden_lab_name").val(ui.item.label);
				$("#hidden_lab_id").val(ui.item.id);
			}
		});

		$(".txt_labs").live("blur",function(){
			  if($(this).siblings(".hdn_lab_name").val() == ""){
				  $(this).val("");
			 }
		});
	}
	function lab_id_check()
	{
		var hidden_lab_id=$('#hidden_lab_id').val()*1;		
		get_php_form_data( hidden_lab_id, 'load_drop_down_color', 'sub_contract_order_entry_controller');
		check_exchange_rate(1);
	}
		$(document).ready(function(){
	        var process_id = "<? echo $process_id;?>";
	        if(process_id == 2 || process_id == 3 || process_id == 4){
	            $(".gmtsSize_show").hide();
	            $(".excessCut_show").hide();
	            $(".emb_type_show").hide();
	            $(".descriptions").hide();
	        }
	        else if(process_id == 7 || process_id == 8 || process_id == 9 || process_id == 12)
	        {
	        	$(".widthDiaType_show").hide();
	            $(".gsm_show").hide();
	            $(".greyDia_show").hide();
	            $(".finishDia_show").hide();
	            $(".psLoss_show").hide();
	            $(".excessCut_show").hide();
	            $(".plan_cut_show").hide();
	            $(".item_word_hide").hide();
				$(".dyeingPart_show").hide();
	            $(".colorRange_show").hide();
	            $(".dyeingUpto_show").hide();
	            $(".labId_show").hide();
	            $(".addRate_show").hide();
	        }
	        else
	        {
	            $(".widthDiaType_show").hide();
	            $(".gsm_show").hide();
	            $(".greyDia_show").hide();
	            $(".finishDia_show").hide();
	            $(".psLoss_show").hide();
	            $(".emb_type_show").hide();
	            $(".descriptions").hide();
				$(".dyeingPart_show").hide();
	            $(".colorRange_show").hide();
	            $(".dyeingUpto_show").hide();
	            $(".labId_show").hide();
	            $(".addRate_show").hide();
	        }
    	});
        </script>
		</head>
		<body onLoad="set_auto_complete('color_return');set_auto_complete_size('size_return');set_auto_complete_lab('lab_return');">
		<div align="center" style="width:100%;" >
            <form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
                <table class="rpt_table" width="1080px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
                    <thead>
                    	<th class="emb_type_show must_entry_caption">Embellishment Type</th>
                        <th class="descriptions">Embelishment Description</th>
                        <th class="must_entry_caption">Item Description</th>
						<th class="widthDiaType_show">Width/Dia Type</th>
						<th class="labId_show">Lab Id</th>
						<th class="dyeingPart_show">Dyeing Part</th>
						<th class="colorRange_show">Color Range</th>
						<th class="dyeingUpto_show">Dyeing Upto</th>
                        <th class="gsm_show">GSM</th>
                        <th class="greyDia_show"> Grey or M/C Dia X Gauge</th>
                        <th class="finishDia_show">Finish Dia</th>
                        <th>Color</th>
                        <th class="gmtsSize_show">GMTS Size</th>
                        <th class="must_entry_caption">Order Qty</th>
                        <th>Rate</th>
						<th class="addRate_show">Add Rate</th>
                        <th>Amount</th>
                        <th class="excessCut_show">Excess Cut%</th>
                        <th class="plan_cut_show">Plan Cut Qty</th>
                        <th class="psLoss_show">Ps.Loss%</th>
                        <th>&nbsp;</th>
                    </thead>
                <tbody>
                	<input type="hidden" name="hiddencheckrcvqty" id="hiddencheckrcvqty" value="<? echo $check_recive_qty;?>">
                    <input type="hidden" name="hiddencompany" id="hiddencompany" value="<? echo $company_id;?>">
                    <input type="hidden" name="hiddenparty" id="hiddenparty" value="<? echo $party_id;?>">
					<input type="hidden" name="hiddencurrency" id="hiddencurrency" value="<? echo $currency_id;?>">
                    <input type="hidden" name="hidden_process_id" id="hidden_process_id" value="<? echo $process_id; ?>">
                    <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly /><?

					if($order_id=="")
					{
						$data_break		=explode('_',$data_break);
						$break_item		=explode('*',$data_break[0]);
						$break_color	=explode('*',$data_break[1]);
						$break_size		=explode('*',$data_break[2]);
						$break_qunty	=explode('*',$data_break[3]);
						$break_rate		=explode('*',$data_break[4]);
						$break_amount	=explode('*',$data_break[5]);
						$break_excess	=explode('*',$data_break[6]);
						$break_plan		=explode('*',$data_break[7]);
						$break_loss		=explode('*',$data_break[8]);
                        $break_gsm		=explode('*',$data_break[9]);
                        $break_greydia	=explode('*',$data_break[10]);
                        $break_finishdia=explode('*',$data_break[11]);
                        $break_embellishment_type=explode('*',$data_break[12]);
						$break_description=explode('*',$data_break[13]);
                        $break_dyeingPart=explode('*',$data_break[14]);
						$break_colorRange=explode('*',$data_break[15]);
						$break_dyeingUpto=explode('*',$data_break[16]);
						$break_labId=explode('*',$data_break[17]);
						$break_addRate=explode('*',$data_break[18]);

						$sql_variavle="select dyeing_fin_bill  from variable_settings_subcon where company_id=$company_id and variable_list=21 and status_active=1 and is_deleted=0";
						$result_variavle=sql_select($sql_variavle);
						if(count($result_variavle)>0){
							foreach($result_variavle as $row)
							{
								$dyeing_fin_bill=$row[csf('dyeing_fin_bill')];
							}		
						}
						if($dyeing_fin_bill==1 && ($process_id==3 || $process_id==4))
						{
							$disabled=1; $txt_disabled="disabled";
						}
						else
						{
							$disabled=0; $txt_disabled="";
						}
						
						
						if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6) /*|| $process_id==7*/
						{
							$garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
							$dia_width_type=return_library_array( "select id, width_dia_id from lib_subcon_charge",'id','width_dia_id');
						}
						else
						{
							$garments_item;
							$dia_width_type;
						}
						$k=0;
						for($i=0; $i<count($break_item); $i++)
						{
							
							$k++;
							?>
							<tr>
								<td class="emb_type_show">
									<?
										$type_array=array(0=>$blank_array,8=>$emblishment_print_type,9=>$emblishment_embroy_type,7=>$emblishment_wash_type,12=>$emblishment_gmts_type);

										if($type_array[$process_id]=="")
										{
											$dropdown_type_array=$blank_array;
										}
										else
										{
											$dropdown_type_array=$type_array[$process_id];
										}

										echo create_drop_down( "cboembtype_".$k, 170, $dropdown_type_array,"", 1, "-- Select --", $break_embellishment_type[$i], "","","" );
									?>
								</td>

								<td class="descriptions">
									<input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes descriptions" style="width:140px" value="<? echo $break_description[$i]; ?>" />
								</td>

								<td>
                                    <input type="hidden" name="hidditemid_<? echo $k;?>" id="hidditemid_<? echo $k;?>" style="width:40px" value="<? echo $break_item[$i];?>">
                                    <input type="text" id="txtitem_<? echo $k;?>" name="txtitem_<? echo $k;?>" class="text_boxes itemdescription" style="width:140px"  onDblClick="openmypage_item(<? echo $k;?>);" placeholder="Double Click" value="<? echo $garments_item[$break_item[$i]];?>" readonly />
								</td>
                                <td  class="widthDiaType_show">
		                        	<?
		                        	echo create_drop_down( "txtdiawidth_".$k, 100, $fabric_typee,"", 1, "-- Select --",$dia_width_type[$break_item[$i]],"check_exchange_rate($k)", "","" );
		                        	?>
                                </td>
								<td  class="labId_show"><input type="text" id="txtlab_<? echo $k;?>" name="txtlab_<? echo $k;?>" class="text_boxes txt_labs" style="width:130px" value="<? echo $break_labId[$i]; ?>" onFocus="auto_completelab(<? echo $k;?>);" onBlur="lab_id_check();" onchange="check_exchange_rate($k)"/>
								<td  class="dyeingPart_show">
		                        	<?
		                        	echo create_drop_down( "cbo_dyeing_part_".$k, 100, $fabric_dyeing_part_arr,"", 1, "-- Select --",$break_dyeingPart[$i],"check_exchange_rate($k)", "","" );
		                        	?>
                                </td>
								<td  class="colorRange_show">
		                        	<?
		                        	echo create_drop_down( "cbo_color_range_".$k, 100, $color_range,"", 1, "-- Select --",$break_colorRange[$i],"check_exchange_rate($k)", "","" );
		                        	?>
                                </td>
								<td  class="dyeingUpto_show">
		                        	<?
		                        	echo create_drop_down( "cbo_dyeing_upto_".$k, 100, $dyeing_sub_process,"", 1, "-- Select --",$break_dyeingUpto[$i],"check_exchange_rate($k)", "","92,117" );
		                        	?>
                                </td>

                                <td  class="gsm_show"><input type="text" id="txtgsm_<? echo $k;?>" value="<? echo $break_gsm[$i]; ?>" class="text_boxes_numeric" style="width:50px;"></td>
                                <td  class="greyDia_show"><input type="text" id="txtgreydia_<? echo $k;?>" value="<? echo $break_greydia[$i]; ?>" class="text_boxes" style="width:130px;"></td>
                                <td  class="finishDia_show"><input type="text" id="txtfinishdia_<? echo $k;?>" value="<? echo $break_finishdia[$i]; ?>" class="text_boxes" style="width:50px;"></td>
								
								<input type="hidden" class="hdn_lab_name" id="hidden_lab_name" name="hidden_lab_name" />
                        		<input type="hidden" id="hidden_lab_id" name="hidden_lab_id" style="width:60px;" class="text_boxes"  ></td>

								<td>
								<input type="hidden" id="txthiddencolor" name="txthiddencolor" class="text_boxes" style="width:80px" value="<? echo $break_color[$i]; ?>" />	
								<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:80px" value="<? echo $break_color[$i]; ?>" /></td>
								
								<td  class="gmtsSize_show"><input type="text" id="txtsize_<? echo $k;?>" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:70px" value="<? echo $break_size[$i]; ?>"/></td>

								<td>
								<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);" onBlur="check_receive_qnty2(<? echo $k;?>);" value="<? echo $break_qunty[$i]; ?>" />
								<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $break_qunty[$i]; ?>" />
								</td>

								<td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo $break_rate[$i]; ?>" <?=$txt_disabled; ?>/>
								</td>

								<td class="addRate_show">
								<input type="hidden" name="hiddrateval_<? echo $k;?>" id="hiddrateval_<? echo $k;?>" style="width:40px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo $break_addRate[$i];?>">
								<input type="text" id="txtaddrate_<? echo $k;?>" name="txtaddrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" onDblClick="openmypage_rate(<? echo $k;?>);" placeholder="Double Click" value="<? echo $break_addRate[$i]; ?>" readonly/>
								</td>

								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $break_amount[$i]; ?>" disabled/></td>
								<?
									if($process_id==1 || $process_id==5 || $process_id==8 || $process_id==9 || $process_id==10 || $process_id==11)
									{
										$is_disable="";
									}
									else $is_disable="disabled";
								?>
                                <td  class="excessCut_show"><input type="text" id="excess_<? echo $k;?>" name="excess_<? echo $k;?>" class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo $break_excess[$i]; ?>" <? echo $is_disable; ?>/></td>
                                <td class="plan_cut_show"><input type="text" id="plan_<? echo $k;?>" name="plan_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $break_plan[$i]; ?>" disabled /></td>
								<td  class="psLoss_show"><input type="text" id="loss_<? echo $k;?>" name="loss_<? echo $k;?>" class="text_boxes_numeric" style="width:45px" value="<? echo $break_loss[$i]; ?>"/></td>
								 <td>
                                 	<input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes"/>
                                    <input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" />
                                    <input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );"/>
								</td>
							</tr>
							 <?
						}
					}
					else
					{
						$check_orderID_recv=sql_select("select order_id,color_id,gsm,fin_dia,grey_dia from sub_material_dtls where order_id=$order_id and is_deleted=0 and status_active=2");
						foreach($check_orderID_recv as $recRow)
						{
							$recRow_arr[$recRow[csf("order_id")]][$recRow[csf("color_id")]][$recRow[csf("gsm")]][$recRow[csf("fin_dia")]][$recRow[csf("grey_dia")]]['order_id']=$recRow[csf("order_id")];
						}

						$qry_result=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss,gsm,grey_dia,finish_dia,dia_width_type,dyeing_part,color_range,dyeing_upto,lab_no,add_rate,embellishment_type,description,dyeing_part,color_range,dyeing_upto,lab_no,add_rate from subcon_ord_breakdown where order_id='$order_id' and mst_id='$mst_id' order by id");
						//echo "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss,gsm,grey_dia,finish_dia,dia_width_type,embellishment_type,description from subcon_ord_breakdown where order_id='$order_id' and mst_id='$mst_id' order by id";
						
						$k=0;
						foreach ($qry_result as $row)
						{
							$k++;
							$id=$row[csf("id")];
							$item_id=$row[csf("item_id")];
							//echo $color_arr[$row[csf("color_id")]];
							$color_fild_id=$color_arr[$row[csf("color_id")]];
							$size_number_id=$size_arr[$row[csf("size_id")]];
							$qnty_val=$row[csf("qnty")];
							$rate_val=$row[csf("rate")];
							$amount_val=$row[csf("amount")];
							$excesscut_val=$row[csf("excess_cut")];
							$plancut_val=$row[csf("plan_cut")];
							$process_loss=$row[csf("process_loss")];
							$dia_width_type=$row[csf("dia_width_type")];
                            $gsm=$row[csf("gsm")];
                            $grey_dia=$row[csf("grey_dia")];
                            $finish_dia=$row[csf("finish_dia")];
                            $embellishment_type=$row[csf("embellishment_type")];
                            $description=$row[csf("description")];
							$cbo_dyeing_part=$row[csf("dyeing_part")];
							$cbo_color_range=$row[csf("color_range")]; 
							$cbo_dyeing_upto=$row[csf("dyeing_upto")];
							$txtlab=$row[csf("lab_no")]; 
							$txtaddrate=$row[csf("add_rate")];
							// echo $description.'T=T';

							$orderID_recv=$recRow_arr[$row[csf("order_id")]][$row[csf("color_id")]][$gsm][$finish_dia][$grey_dia]['order_id'];
							if($orderID_recv !="" || $orderID_recv>0)
							{
								$disable="disabled";
							}
							else{$disable= ""; }

							if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6) //|| $process_id==7
							{
								$garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
							}
							else
							{
								$garments_item;
							}
							?>
							<tr class="general" >
								<td class="emb_type_show">
									<?
										$type_array=array(0=>$blank_array,8=>$emblishment_print_type,9=>$emblishment_embroy_type,7=>$emblishment_wash_type,12=>$emblishment_gmts_type);

										if($type_array[$process_id]=="")
										{
											$dropdown_type_array=$blank_array;
										}
										else
										{
											$dropdown_type_array=$type_array[$process_id];
										}

										echo create_drop_down( "cboembtype_".$k, 170, $dropdown_type_array,"", 1, "-- Select --", $embellishment_type, "",$disabled_type,"" );
									?>
								</td>

								<td class="descriptions">
									<input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes descriptions" style="width:140px" value="<? echo $description; ?>"  <? echo $disabled_cond;?> />
								</td>

								<td>
                                	<input type="hidden" name="hidditemid_<? echo $k; ?>" id="hidditemid_<? echo $k; ?>"  value="<? echo $item_id; ?>"style="width:40px">
                                    <input type="text" id="txtitem_<? echo $k; ?>" name="txtitem_<? echo $k; ?>" value="<? echo $garments_item[$item_id]; ?>" class="text_boxes itemdescription" style="width:140px"  onDblClick="openmypage_item(<? echo $k; ?>);" placeholder="Double Click" <? echo $disabled_cond;?> readonly />
								</td>
                                <td  class="widthDiaType_show">

                                <?
		                        	echo create_drop_down( "txtdiawidth_".$k, 100, $fabric_typee,"", 1, "-- Select --",$dia_width_type,"check_exchange_rate($k)", $disabled_type,"" );
		                        ?>

                                </td>
								<td  class="labId_show"><input type="text" id="txtlab_<? echo $k;?>" name="txtlab_<? echo $k;?>" class="text_boxes txt_labs" style="width:130px" value="<? echo $txtlab; ?>" onFocus="auto_completelab(<? echo $k;?>);" onBlur="lab_id_check();" onchange="check_exchange_rate($k)"/></td>
								<td  class="dyeingPart_show">
									<?
									echo create_drop_down( "cbo_dyeing_part_".$k, 100, $fabric_dyeing_part_arr,"", 1, "-- Select --","","check_exchange_rate($k)", $disabled_type,"" );
									?>
									</td>
								<td  class="colorRange_show">

		                        	<?
		                        	echo create_drop_down( "cbo_color_range_".$k, 100, $color_range,"", 1, "-- Select --",$cbo_color_range,"check_exchange_rate($k)", $disabled_type,"" );
		                        	?>
                                </td>
								<td  class="dyeingUpto_show">

		                        	<?
		                        	echo create_drop_down( "cbo_dyeing_upto_".$k, 100, $dyeing_sub_process,"", 1, "-- Select --",$cbo_dyeing_upto,"check_exchange_rate($k)", $disabled_type,"92,117" );
		                        	?>
                                </td>
                                <td  class="gsm_show"><input type="text" id="txtgsm_<? echo $k; ?>" name="txtgsm_<? echo $k; ?>"  value="<? echo $gsm; ?>" class="text_boxes_numeric" style="width:50px"  <? echo $disabled_cond;?>/></td>

                                <td  class="greyDia_show"><input type="text" id="txtgreydia_<? echo $k; ?>" name="txtgreydia_<? echo $k; ?>"  value="<? echo $grey_dia; ?>" class="text_boxes" style="width:130px"  <? echo $disabled_cond;?>/></td>

                                <td  class="finishDia_show"><input type="text" id="txtfinishdia_<? echo $k; ?>" name="txtfinishdia_<? echo $k; ?>"  value="<? echo $finish_dia; ?>" class="text_boxes" style="width:50px"  <? echo $disabled_cond;?>/></td>

								<input type="hidden" class="hdn_lab_name" id="hidden_lab_name" name="hidden_lab_name" />
                        		<input type="hidden" id="hidden_lab_id" name="hidden_lab_id" style="width:60px;" class="text_boxes"  ></td>


								<td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" value="<? echo $color_fild_id; ?>" class="text_boxes txt_color" style="width:80px" <? //echo $disable; ?> <? echo $disabled_cond;?>/></td>
								
								<td  class="gmtsSize_show"><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>"  value="<? echo $size_number_id; ?>" class="text_boxes txt_size" style="width:70px" <? echo $disabled_cond;?>/></td>

								<td>
								<input type="text" id="txtorderquantity_<? echo $k; ?>" onDblClick="openmypage_qnty('<?= $id.'_'.$k;?>');" name="txtorderquantity_<? echo $k; ?>" value="<? echo $qnty_val; ?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k; ?>);" onBlur="check_receive_qnty2(<? echo $k; ?>);"/>
								<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" value="<? echo $qnty_val; ?>" />
								<input type="hidden" id="hiddencolorqtybreakdown_<? echo $k;?>" value="" />
								</td>

								<td><input type="text" id="txtorderrate_<? echo $k; ?>" name="txtorderrate_<? echo $k; ?>" value="<? echo $rate_val; ?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k; ?>)" <? echo $disabled_cond;?> <?=$txt_disabled; ?>/></td>

								
								<td class="addRate_show">
								<input type="hidden" name="hiddrateval_<? echo $k;?>" id="hiddrateval_<? echo $k;?>" style="width:40px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo $txtaddrate;?>">
								<input type="text" id="txtaddrate_<? echo $k;?>" name="txtaddrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" onDblClick="openmypage_rate(<? echo $k;?>);" placeholder="Double Click" value="<? echo $txtaddrate; ?>" readonly/>
								</td>

								<!-- <td class="addRate_show"><input type="text" id="txtaddrate_<? //echo $k;?>" name="txtaddrate_<? //echo $k;?>"  value="<? //echo $txtaddrate; ?>" class="text_boxes_numeric" style="width:60px"  onDblClick="openmypage_rate(<? //echo $k;?>);" onKeyUp="sum_total_qnty(<? //echo $k;?>)" <? //echo $disabled_cond;?>/>
								</td> -->

								<td><input type="text" id="txtorderamount_<? echo $k; ?>" name="txtorderamount_<? echo $k; ?>" value="<? echo $amount_val; ?>" class="text_boxes_numeric" style="width:70px" disabled/></td>
								<?
									if($process_id==1 || $process_id==5 || $process_id==8 || $process_id==9 || $process_id==10 || $process_id==11)
									{
										$is_disable="";
									}
									else $is_disable="disabled";
								?>
                                 <td  class="excessCut_show"><input type="text" id="excess_<? echo $k; ?>" name="excess_<? echo $k; ?>" value="<? echo $excesscut_val; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k; ?>)" <? echo $is_disable; ?> <? echo $disabled_cond;?>/></td>
                                 <td class="plan_cut_show"><input type="text" id="plan_<? echo $k; ?>" name="plan_<? echo $k; ?>"  value="<? echo $plancut_val; ?>" class="text_boxes_numeric" style="width:70px" disabled/></td>
								<td  class="psLoss_show"><input type="text" id="loss_<? echo $k; ?>" name="loss_<? echo $k; ?>" value="<? echo $process_loss; ?>" class="text_boxes_numeric" style="width:45px" <? echo $disabled_cond;?>/> <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>" value="<? echo $id; ?>" style="width:15px;" class="text_boxes"/>
								</td>
								<td>
                                    <input type="button" id="increaseset_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k; ?>)" />
                                    <input type="button" id="decreaseset_<? echo $k; ?>" style="width:30px"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> ,'tbl_share_details_entry' );" <? echo $disable; ?>/>
								</td>
						   </tr>
							<?
						}
					}
				?>
				</tbody>
            </table>
       		<table width="1050px" border="0" cellspacing="0" cellpadding="0" class="" rules="all">
                <tfoot>
                   	<?
                   		if($process_id == 2 || $process_id == 3 || $process_id == 4)
                   		{
                   			$width_qntity="657px";
                   			$width_amnt="168px";
                   			$width_avg ="633px";
                   		}
						else if($process_id == 7 || $process_id == 8 || $process_id == 9 || $process_id == 12)
						{
							$width_qntity="805px";
                   			$width_amnt="171px";
                   			$width_avg="781px";
						}
						else
						{
							$width_qntity="535px";
							$width_amnt="206px";
							$width_avg="530px";
						}
                   	?>

                    <td align="right" width="<?echo $width_qntity;?>">
                        <input type="text" id="txt_total_order_qnty"  name="txt_total_order_qnty"  class="text_boxes_numeric" value="<? echo $data[3];?>" style="width:75px;" placeholder="Total qnty" disabled />
                    </td>

                    <td align="center">
                    	<input type="hidden" name="hidden_tot_rate" id="hidden_tot_rate" >
                    </td>

                    	<input type="hidden" name="hidden_tot_order_qty" id="hidden_tot_order_qty" >

                    <td align="right" width="<?echo $width_amnt;?>" style="float:left;">
                        <input type="text" id="txt_total_order_amount" name="txt_total_order_amount" class="text_boxes_numeric" value="<? echo $data[5];?>" style="width:75px;" placeholder="Total amount" disabled />
                    </td>

                </tfoot>
            </table>
            <br>
            <table width="1050px" border="0" cellspacing="0" cellpadding="0" class="" rules="all">
                    <td width="<?echo $width_avg;?>" align="right" style="float:left;">Average Rate</td>
                    <td width="160px" align="left" style="float:left;"> &nbsp;&nbsp;&nbsp;
                    	<input type="text" id="txt_average_rate"  name="txt_average_rate" class="text_boxes_numeric" value="<? echo $data[4]; ?>" style="width:98px;" placeholder="Average rate" disabled />
                    </td>
            </table>
            <br>
            <table>
                <tr>
                    <td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
                </tr>
            </table>
				<input type="hidden" name="hidden_itemid" id="hidden_itemid">
				<input type="hidden" name="hidden_gsm" id="hidden_gsm">
				<input type="hidden" name="hidden_grey_dia" id="hidden_grey_dia">
				<input type="hidden" name="hidden_finish_dia" id="hidden_finish_dia">
				<input type="hidden" name="hidden_color" id="hidden_color">
				<input type="hidden" name="hidden_size" id="hidden_size">
				<input type="hidden" name="hidden_order_quantity" id="hidden_order_quantity">
				<input type="hidden" name="hidden_order_rate" id="hidden_order_rate">
				<input type="hidden" name="hidden_order_amount" id="hidden_order_amount">
				<input type="hidden" name="hidden_excess" id="hidden_excess">
				<input type="hidden" name="hidden_plan" id="hidden_plan">
				<input type="hidden" id="hidden_loss" name="hidden_loss" />
				<input type="hidden" name="hidden_tbl_id" id="hidden_tbl_id">
				<input type="hidden" name="hidden_txtdiawidth" id="hidden_txtdiawidth">
				<input type="hidden" name="hidden_cbo_dyeing_part" id="hidden_cbo_dyeing_part">
				<input type="hidden" name="hidden_cbo_color_range" id="hidden_cbo_color_range">
				<input type="hidden" name="hidden_cbo_dyeing_upto" id="hidden_cbo_dyeing_upto">
				<input type="hidden" name="hidden_txtlab" id="hidden_txtlab">
				<input type="hidden" name="hidden_txtaddrate" id="hidden_txtaddrate">
				<input type="hidden" name="hidden_embelishment_type" id="hidden_embelishment_type">
				<input type="hidden" name="hidden_description" id="hidden_description">
				<input type="hidden" name="hidden_color_qty_breakdown" id="hidden_color_qty_breakdown">
				<input type="hidden" name="hidden_qtytbl_id" id="hidden_qtytbl_id">
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action == "lab_company_action")
{
	$data=explode("_",$data);
	$company=$data[0];
		$sql = "select MAX(id) as id, sys_no as label from lab_color_ingredients_mst where status_active=1 and is_deleted=0 group by sys_no order by sys_no";

	$result = sql_select($sql);
	$supplierArr = array();
	foreach($result as $key => $val){
		$supplierArr[$key]["id"]=$val[csf("id")];
		$supplierArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($supplierArr);
    exit();
}
if($action=="load_drop_down_color")
{
	$data=explode('_',$data);
	$supp_id=$data[0];
	$color_att=return_field_value("color_desc","lab_color_ingredients_mst","id =".$supp_id." and is_deleted=0 and status_active=1");
	$dyeing_part_att=return_field_value("dyeing_part","lab_color_ingredients_mst","id =".$supp_id." and is_deleted=0 and status_active=1");
	$color_range_att=return_field_value("color_range","lab_color_ingredients_mst","id =".$supp_id." and is_deleted=0 and status_active=1");
	$dyeing_upto_att=return_field_value("dyeing_upto","lab_color_ingredients_mst","id =".$supp_id." and is_deleted=0 and status_active=1");

	echo "document.getElementById('txtcolor_1').value = '".$color_att."';\n";
	echo "document.getElementById('cbo_dyeing_part_1').value = '".$dyeing_part_att."';\n";
	echo "document.getElementById('cbo_color_range_1').value = '".$color_range_att."';\n";
	echo "document.getElementById('cbo_dyeing_upto_1').value = '".$dyeing_upto_att."';\n";

	exit();
}

if($action=="check_popup_rate")
{
	$data=explode("**",$data);
	$sql_variavle="select dyeing_fin_bill  from variable_settings_subcon where company_id=$data[6] and variable_list=21 and status_active=1 and is_deleted=0";
	$result_variavle=sql_select($sql_variavle);
	if(count($result_variavle)>0){
		foreach($result_variavle as $row)
		{
			$dyeing_fin_bill=$row[csf('dyeing_fin_bill')];
		}		
	}
	if($dyeing_fin_bill==1){
		if($data[0]==1){
			$sql_rate="select c.bdt as currency_rate from lib_prcs_finfab_rt_chrt_mst a,lib_prcs_finfab_rt_chrt_dtls b,lib_prcs_finfab_rt_chrt_rate c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.service_company=$data[1] and b.dyeing_part=$data[2] and b.dia_width_type=$data[3] and b.dyeing_color_range=$data[4] and b.dyeing_upto=$data[5] and a.company_id=$data[6] and a.status_active=1 and b.status_active=1 and b.status=1 and c.status_active=1";
		}
		else if($data[0]==2){
			$sql_rate="select c.usd as currency_rate from lib_prcs_finfab_rt_chrt_mst a,lib_prcs_finfab_rt_chrt_dtls b,lib_prcs_finfab_rt_chrt_rate c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.service_company=$data[1] and b.dyeing_part=$data[2] and b.dia_width_type=$data[3] and b.dyeing_color_range=$data[4] and b.dyeing_upto=$data[5] and a.company_id=$data[6] and a.status_active=1 and b.status_active=1 and b.status=1 and c.status_active=1";
		}
		$result_rate=sql_select($sql_rate);
		if(count($result_rate)>0){
			foreach($result_rate as $row)
			{
				$currency_rate=$row[csf('currency_rate')];
			}
		}
	}
	if(count($result_rate)>0) echo "1_".$currency_rate;
	else echo "0_";
	exit();
}

if($action=="check_color_ingidient")
{
	$data=explode("**",$data);
	$company=$data[0];
	$sys_no=$data[1];

		echo $sql_color="select MAX(id) as sysIid,color_desc from lab_color_ingredients_mst where COMPANY_ID=$company and SYS_NO=$sys_no and a.status_active=1 group by color_desc";

		$result_color=sql_select($sql_color);
		if(count($result_color)>0){
			foreach($result_color as $row)
			{
				$color_desc=$row[csf('color_desc')];
			}
		}
	if(count($result_color)>0) echo "1_".$color_desc;
	else echo "0_";
	exit();
}


if($action=="qnty_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	?>
	<script>
		var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 group by color_name ", "color_name" ), 0, -1); ?> ];
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active=1 and is_deleted=0 group by size_name ", "size_name" ), 0, -1); ?> ];

		function set_auto_complete(type)
		{
			if(type=='color_return')
			{
				$(".txt_color").autocomplete({
				source: str_color
				});
			}
		}	

		function add_share_row( i )
		{
			
			var row_num=$('#tbl_share_details_entry tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_share_details_entry");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");	
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");		
			$('#txtsubcolor_'+i).val('');
			$('#txtsubqty_'+i).val('');
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
				$("#txtsize_"+i).autocomplete({
				source: str_size
			});
			sum_total_qnty(i);
		}
		function fn_deletebreak_down_tr(rowNo)
		{
			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}

				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}

			sum_total_qnty(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;

			var txt_color=""; var txt_order_quantity=""; 
			
			var color_break="";var hidden_id=""; 
		
			var total_qty="";

			for(var i=1; i<=tot_row; i++)
			{		
		
				txt_color = $("#txtsubcolor_"+i).val();			
				txt_order_quantity = $("#txtsubqty_"+i).val();
				total_qty=+txt_order_quantity;
				if(i==1){
					color_break =txt_color+"***"+txt_order_quantity+"***"+<?=$data;?>;
				}else{
					color_break +="###"+txt_color+"***"+txt_order_quantity+"***"+<?=$data;?>;
				}
			}		
			if(<?=$qty;?>< total_qty){
				alert("Color Qnty more than Order qty");return;
			}
	
			document.getElementById('hidden_color_qty_breakdown').value=color_break;	
			document.getElementById('hidden_qtytbl_id').value=tot_row;	
			document.getElementById('hiddenqty').value=total_qty;	
			
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;

			math_operation( "hidden_tot_rate", "txtorderrate_", "+", tot_row );

			//$("#txtorderamount_"+id).val(($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1));

			 $("#txtorderamount_"+id).val((($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1))+(($("#txtorderquantity_"+id).val()*1)*($("#txtaddrate_"+id).val()*1)));
			//$("#txt_average_rate").val(($("#hidden_tot_rate").val()*1)/tot_row*1);

			if($("#hidden_process_id").val()==1 || $("#hidden_process_id").val()==5 || $("#hidden_process_id").val()==8 || $("#hidden_process_id").val()==9 || $("#hidden_process_id").val()==10 || $("#hidden_process_id").val()==11)
			{
				$("#plan_"+id).val((($("#txtorderquantity_"+id).val()*1)+(($("#txtorderquantity_"+id).val()*1)*(($("#excess_"+id).val()*1)/100))));
			}

			math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row );
			math_operation( "txt_total_order_amount", "txtorderamount_", "+", tot_row );

			$("#txt_average_rate").val((($("#txt_total_order_amount").val()*1)/($("#txt_total_order_qnty").val()*1)).toFixed(2));
		}

		<? $color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");  ?>

		

               

        </script>
		</head>
		
		<body >
		<div align="center" style="width:100%;" >
            <form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
                <table class="rpt_table" width="350px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
                    <thead>
                    
                        <th>Color</th>                      
                        <th class="must_entry_caption">Order Qty</th>                      
                        <th>&nbsp;</th>
                    </thead>
                <tbody>
                	<input type="hidden" name="hiddencheckrcvqty" id="hiddencheckrcvqty" value="<? echo $check_recive_qty;?>">
                  
                   
                    <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
				<?

						if($data > 0){
						$sql="select id,color_id,qnty from subcon_ord_color_breakdown where SUBCON_ORD_BREAKDOWN_ID=$data";
						}
						$sql_data=sql_select($sql);

							if(count($sql_data)>0){
														
								
								$k=0;

								for($i=0; $i<count($sql_data); $i++)
								{
									$k++;
								// foreach($sql_data as $val){
								// 	$k++;?>
								
							<tr>								
								<td><input type="text" onClick="set_auto_complete('color_return');" id="txtsubcolor_<? echo $k;?>" name="txtsubcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:80px" value="<? echo $color_library_arr[$sql_data[$i][csf('color_id')]]; ?>" /></td>								
								<td>
									<input type="text" id="txtsubqty_<? echo $k;?>" name="txtsubqty_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);"  value="<? echo $sql_data[$i][csf('qnty')]; ?>" />
									<input type="hidden" id="hidden_color_qty_<? echo $k;?>" name="hidden_color_qty_<? echo $k;?>" class="text_boxes_numeric" style="width:70px"  value="<? echo $sql_data[$i][csf('id')]; ?>" />
								</td>
								
								 <td>
                                 	<input type="hidden" id="hiddenqtyid" name="hiddenqtyid"  value="<?=$data;?>" style="width:15px;" class="text_boxes"/>
									 <input type="hidden" id="hiddenqty" name="hiddenqty"  value="" style="width:15px;" class="text_boxes"/>

                                    <input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" />
                                    <input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );"/>
								</td>
							</tr>
							 <?
							  }
							}else{
								$k=0;						
								$k++;
								?>
							<tr>								
								<td><input type="text" onClick="set_auto_complete('color_return');" id="txtsubcolor_<? echo $k;?>" name="txtsubcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:80px" value="<? echo $break_color[$i]; ?>" /></td>								
								<td>
									<input type="text" id="txtsubqty_<? echo $k;?>" name="txtsubqty_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);"  value="<? echo $break_qunty[$i]; ?>" />
									
								</td>
								 <td>
                                 	<input type="hidden" id="hiddenqtyid" name="hiddenqtyid"  value="<?=$data;?>" style="width:15px;" class="text_boxes"/>
									 <input type="hidden" id="hiddenqty" name="hiddenqty"  value="" style="width:15px;" class="text_boxes"/>

                                    <input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" />
                                    <input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );"/>
								</td>
							</tr>


									

							<?}
					
				?>
				</tbody>
            </table>
       		
            
            <table>
                <tr>
                    <td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					<input type="hidden" name="hidden_color_qty_breakdown" id="hidden_color_qty_breakdown">
                </tr>
            </table>
             
             <input type="hidden" name="hidden_qtytbl_id" id="hidden_qtytbl_id">
      
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="order_qnty_rate_popup")
{
	 
	echo load_html_head_contents("Material Description Form","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company=$ex_data[0];
	$party=$ex_data[1];
	$process=$ex_data[2];
		?>
		<script>
			
		// 	function js_set_value(id)
		// 	{
		// 		document.getElementById('hidd_add_rate').value=id;
		// 		parent.emailwindow.hide();
		// 	}

		// 	$(document).ready(function(e) {
        //     setFilterGrid('list_view_rate',-1);
        // });

		
		var selected_id = new Array; var selected_name = new Array;var selected_num = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;

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

		function js_set_value_rate( str ) {
			if (str!="") str=str.split("**");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_num.push( str[3] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_num.splice( i, 1 );
			}
			var id = ''; var name = '';var num = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_num[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			num = num.substr( 0, num.length - 1 );
			$('#txt_selected_id').val( id );
			$('#hidden_process_id').val( name );
			$('#hidden_new_rate').val( num );
		}
		function fnc_window_close(type)
		{
			var rate=$('#hidden_new_rate').val();
			var rate_len=rate.split(",");
			var rate_tot_cal=0;
			for(k=0;k<rate_len.length;k++)
			{
				var rate_tot_cal=(rate_len[k]*1)+rate_tot_cal;
			}
			$('#tot_rate').val(rate_tot_cal);
			parent.emailwindow.hide();
		}
		</script>
		</head>
		<body>
            <div align="center" style="width:100%;" >
                <form name="searchpofrm"  id="searchpofrm">
                    <fieldset style="width:320px">
					<input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_new_rate" id="hidden_new_rate" class="text_boxes" value="">
					<input type="hidden" name="tot_rate" id="tot_rate" class="text_boxes" value="">
                    <input type="hidden" id="hidd_add_rate" />
					<?
						if($process==1){
							$sql_rate="select a.id,b.additional_process,c.bdt as currency_rate from lib_prcs_finfab_rt_chrt_mst a,lib_prcs_finfab_rt_chrt_dtls b,lib_prcs_finfab_rt_chrt_rate c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.service_company=$party and a.company_id=$company and a.status_active=1 and b.status_active=1 and b.status=1 and c.status_active=1 and b.additional_process>0";
						}
						else if($process==2){
							$sql_rate="select a.id,b.additional_process,c.usd as currency_rate from lib_prcs_finfab_rt_chrt_mst a,lib_prcs_finfab_rt_chrt_dtls b,lib_prcs_finfab_rt_chrt_rate c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.service_company=$party and a.company_id=$company and a.status_active=1 and b.status_active=1 and b.status=1 and c.status_active=1 and b.additional_process>0";
						}
                        $qry_result=sql_select($sql_rate);
                  ?>
                  <table class="rpt_table scroll" width="320" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                     <tr>
                        <th width="20">SL</th>
                        <th width="200">Process No</th>
                        <th width="80">Rate</th>
                       </tr>
                    </thead>
                  </table>
				<div style="width:340px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="320" cellpadding="0" cellspacing="0" border="1" rules="all" id="list_view_rate">
                    <tbody>
                        <?
                        $i=1;
                        foreach($qry_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value_rate("<? echo $i."**".$row[csf('id')]."**".$row[csf('additional_process')]."**".$row[csf('currency_rate')]; ?>")' style="cursor:pointer" id="tr_<? echo $i; ?>"  >
                                <td  width="20"><? echo $i; ?>
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" class="text_boxes"/>
							</td>
                                <td  width="200"><? echo $additional_part_arr[$row[csf('additional_process')]]; ?>					
							</td>
                                <td  width="80"  name="txt_currency_rate_<? echo $i; ?>" id="txt_currency_rate_<? echo $i; ?>" align="right"><? echo number_format($row[csf('currency_rate')],2); ?>
								<input type='hidden' id='txtrate_<? echo $i; ?>' value="<? echo number_format($row[csf('currency_rate')],2,'.',''); ?>" />
							</td>
                            </tr>
                        <?
                        $i++;
                        }
                        ?>
                    </tbody>
					<tfoot>
					<tr style="border:none">
					<td colspan="3" align="center"><input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="fnc_window_close(3)" /></td>
					</tr>
					</tfoot>
                </table>
                </div>
                    </fieldset>
                </form>
            </div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
	<?
	exit();
}

if($action=="order_qnty_item_popup")
{
	echo load_html_head_contents("Material Description Form", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company=$ex_data[0];
	$party=$ex_data[1];
	$process=$ex_data[2];
	//echo $process.'=A';
	//if($process==2 || $process==3 || $process==4 || $process==6 || $process==7)
	if($process==2 || $process==3 || $process==4 || $process==6 || $process==26)
	{
		?>
		<script>
			function js_set_value(id)
			{
				document.getElementById('hidd_item_id').value=id;
				parent.emailwindow.hide();
			}

			$(document).ready(function(e) {
            setFilterGrid('list_view_knitting',-1);
        });

		</script>
		</head>
		<body>
            <div align="center" style="width:100%;" >
                <form name="searchpofrm"  id="searchpofrm">
                    <fieldset style="width:500px">
                    <input type="hidden" id="hidd_item_id" />
					<?
                        $color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

						if($party!=0) $party_cond="and buyer_id='$party'"; else $party_cond="";
                          $sql ="select id, const_comp, width_dia_id, uom_id, customer_rate, color_id ,gsm from lib_subcon_charge where rate_type_id='$process'  and status_active=1 $party_cond
						union all
						select id, const_comp, width_dia_id, uom_id, customer_rate, color_id, gsm from lib_subcon_charge where rate_type_id='$process'  and status_active=1 and   buyer_id=0";
						 //echo $sql;
                        //comapny_id='$company' and comapny_id='$company' and comapny_id='$company' and
                        $qry_result=sql_select($sql);
                  ?>
                  <table class="rpt_table scroll" width="585" cellpadding="0" cellspacing="0" border="1" rules="all">
               <!--  <table class="rpt_table scroll" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" id="list_view_knitting">-->
                    <thead>
                     <tr>
                        <th width="20">SL</th>
                        <th width="200">Const/Compo</th>
                       
                        <th width="70">Width/Dia Type</th>
                        <th width="60">UOM</th>
                        <th width="40">GSM</th>
                        <th width="85">Color</th>
                        <th width="80">Rate</th>
                       </tr>
                    </thead>
                  </table>
				<div style="width:590px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all" id="list_view_knitting">
                    <tbody>
                        <?
                        $i=1;
                        foreach($qry_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $const_comp="";
                            $const_comp=explode(',',$row[csf('const_comp')]);
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')]."**".$row[csf('const_comp')]."**".$row[csf('customer_rate')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('width_dia_id')]."**".$row[csf('gsm')]; ?>")' style="cursor:pointer" >
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="200" title="Id=<? echo $row[csf('id')];?>"><? echo $row[csf('const_comp')]; //$const_comp[0]; ?></td>
                               
                                <td  width="70"><? echo $fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                                <td  width="60"><? echo $unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                                <td width="40"><? echo $row[csf('gsm')]?></td>
                                <td  width="80"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                                <td  width="80" align="right"><? echo number_format($row[csf('customer_rate')],2); ?></td>
                            </tr>
                        <?
                        $i++;
                        }
                        ?>
                    </tbody>
                </table>
                </div>
                    </fieldset>
                </form>
            </div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
	<?
	}
	else if($process==1 || $process==5 || $process==8 || $process==9 || $process==10 || $process==11 || $process==12 || $process==7)
	{
		echo load_html_head_contents("Material Description Form", "../../", 1, 1,'',1,'');
		extract($_REQUEST);
		?>
		<script>
			function js_set_value(id)
			{
				document.getElementById('hidd_item_id').value=id;

				parent.emailwindow.hide();
			}

			$(document).ready(function(e) {
            setFilterGrid('gmts_tbl',-1);
        });

		</script>
		</head>
		<body>
            <div align="center" style="width:100%;" >
                <form name="searchpofrm"  id="searchpofrm">
                <input type="hidden" id="hidd_item_id" />
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500px" class="rpt_table" >
                        <thead>
                            <th width="50" >SL</th>
                            <th>Garments Item</th>
                        </thead>
                    </table>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500px" class="rpt_table" id="gmts_tbl">
						<?
							$i=1;
							$garments_item;
							foreach( $garments_item as $key=>$val )
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $key."**".$val."**".'0'."**".'0'."**".'0'."**".''; ?>');" >
                                    <td width="50" align="center"><?php echo $i; ?></td>
                                    <td><?php echo $val; ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </table>
                </form>
            </div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
	}
	exit();
}

/*if($action=="populate_rate")
{
	$to_data=explode("*",$data);
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$color_name=$color_library_arr[$to_data[1]];
	echo "document.getElementById('txtcolor_'+$to_data[0]).value='$color_name';";
	exit();
}*/
if ($action == "sub_order_print") 
{
		// var_dump($_REQUEST);
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_id = $data[0];
		$update_id = $data[1];
		$job_no = $data[2];
		
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	//	$machine_no_arr = return_library_array("select id,machine_no from lib_machine_name", 'id', 'machine_no');
		$size_arr = return_library_array("select id,size_name from lib_size where is_deleted=0 and status_active=1 order by size_name","id","size_name");

		

		 $sql = "select a.subcon_job as job_no,a.location_id,a.party_id,a.approved,a.currency_id, b.id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.cust_buyer,b.order_quantity as po_qnty_in_pcs,b.order_rcv_date,b.main_process_id,b.process_id, b.delivery_date as delivery_date,b.remarks,b.smv from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id=$company_id and a.subcon_job='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		$dataArray = sql_select($sql);
		$po_id = array_filter(array_unique(explode(",", $dataArray[0][csf('po_id')])));

		$po_number = "";
		$job_number = "";
		$job_style = "";
		$buyer_id = "";
		$ship_date = "";
		$internal_ref = "";
		$file_nos = "";

		$order_rcv_date = $dataArray[0][csf('order_rcv_date')];
		$approved = $dataArray[0][csf('approved')];
		$party_id = $dataArray[0][csf('party_id')];
		$location_id = $dataArray[0][csf('location_id')];
		//$po_number = $dataArray[0][csf('po_number')];
		$job_no = $dataArray[0][csf('job_no')];
		$cust_buyer = $dataArray[0][csf('cust_buyer')];
		$delivery_date = $dataArray[0][csf('delivery_date')];
		$currency_id = $dataArray[0][csf('currency_id')];
		$main_process_id = $dataArray[0][csf('main_process_id')];
		$process_id = $dataArray[0][csf('process_id')];
		$remarks = $dataArray[0][csf('remarks')];
		$smv = $dataArray[0][csf('smv')];
		foreach ($dataArray as $val)
		{
				if ($po_number == "") $po_number = $val[csf('po_number')]; else $po_number .= ',' . $val[csf('po_number')];
			
		}

		//$job_no = implode(",", array_unique(explode(",", $job_number)));
		//$jobstyle = implode(",", array_unique(explode(",", $job_style)));
		////$buyer = implode(",", array_unique(explode(",", $buyer_id)));
		//$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
		//$file_nos = implode(",", array_unique(explode(",", $file_nos)));

		?>
		
   <div style="width:930px;">
         <table width="930" cellspacing="0" align="right" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?
                        $nameArray=sql_select( "select a.plot_no,b.address,a.level_no, a.road_no, a.block_no, a.country_id, a.province, a.city, a.zip_code, a.email, a.website, a.vat_number from lib_company a,lib_location b where a.id=b.company_id and  a.id=$company_id and b.id=$location_id and a.status_active=1 and a.is_deleted=0"); 
						//echo "select a.plot_no, a.level_no, a.road_no, a.block_no, a.country_id, a.province, a.city, a.zip_code, a.email, a.website, a.vat_number from lib_company a,lib_location b where a.id=b.company_id and  a.id=$company_id and b.id=$location_id and a.status_active=1 and a.is_deleted=0";
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            <? //echo $result[csf('plot_no')]; ?>&nbsp;
                            <? //echo $result[csf('level_no')]; ?>&nbsp;
                            <? //echo $result[csf('road_no')]; ?>&nbsp;
                            <? //echo $result[csf('block_no')];?> &nbsp;
                            <? //echo $result[csf('city')]; ?>&nbsp;
                            <? //echo $result[csf('zip_code')]; ?>&nbsp;
                            <? echo $result[csf('address')]; ?>&nbsp;
                            <? echo $result[csf('email')];?><br>
                            <?
                        }
                    ?> 
                </td>
            </tr>           
        	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
            </tr>
          
             <tr>
             
                <td><strong>Party Name  </strong></td><td> :<? echo $buyer_arr[$party_id]; ?></td>
                <td><strong>Order Receive Date  </strong></td><td>: <? echo change_date_format($order_rcv_date); ?></td>
            </tr>
             <tr>
                <td><strong>Order Number </strong></td><td>: <? echo $po_number; ?></td>
                <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($delivery_date); ?></td>
            </tr>
             <tr>
                <td><strong>Job No </strong></td><td>: <? echo $job_no; ?></td>
                <td><strong>Customer Buyer </strong></td><td>: <? echo $cust_buyer; ?></td>
            </tr>
             <tr>
                <td><strong>Location Name </strong></td><td>: <? echo $location_arr[$location_id]; ?></td>
                <td><strong>Currency	 </strong></td><td>: <? echo $currency[$currency_id]; ?></td>
            </tr>
            <tr>
                <td><strong>Main Process </strong></td><td>: <? echo $production_process[$main_process_id]; ?></td>
                <td><strong>SMV	 </strong></td><td>: <? echo $smv; ?></td>
            </tr>
             <tr>
                <td><strong>Sub Process </strong></td><td colspan="4">: <? 
				$process_id=array_unique(explode(",",$process_id));
				$process_name='';
				foreach($process_id as $pid)
				{
					if($process_name=='') $process_name=$conversion_cost_head_array[$pid];else $process_name.=",".$conversion_cost_head_array[$pid];
				}
				echo $process_name; ?></td>
                
            </tr> 
            <tr>
                <td><strong>Remarks </strong></td><td colspan="3">: <? echo $remarks; ?></td>
				<td style="font-size: 22px; color:red;"><strong><? if ($approved==1) echo "Approved"; else if($approved==3) echo "Partial Approved"; ?></strong></td>
            </tr>
        </table>
         <br>
         <?
         $mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select order_id,job_no_mst,item_id,color_id,size_id,qnty,rate,amount,excess_cut,plan_cut,process_loss,gsm,grey_dia,finish_dia,embellishment_type,description,dia_width_type,dyeing_part,color_range,dyeing_upto,lab_no,add_rate from subcon_ord_breakdown  where job_no_mst='$job_no'  and status_active=1 and is_deleted=0");
		 ?>
	
         
        <div style="width:100%;">
        <?
        if($main_process_id==1) //Cutting
		{
		?>
        <table align="left" cellspacing="0" style=" margin:5px;" width="780"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="180" align="center">Item Description	</th>
                <th width="110" align="center">Color</th>
                <th width="100" align="center">GMTS Size</th> 
                <th width="80" align="center">Order Qty</th>
                <th width="50" align="center">Rate</th>
                <th width="80" align="center">Amount</th> 
                <th width="50" align="center">Excess Cut%</th>
                <th width="" align="center">Plan Cut Qty</th>
                
               
            </thead>
		 <?
     		$i=1;
			
			$tot_size_order_qty=$total_size_amount=$tot_size_plan_cut_qty=0;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($main_process_id==2 || $main_process_id==3 || $main_process_id==4 || $main_process_id==6) //|| $process_id==7
				{
					$garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				}
				else
				{
					$garments_item;
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                     <td><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
                     <td align="right"><p><? echo $row[csf('qnty')]; $tot_size_order_qty+=$row[csf('qnty')]; ?>&nbsp;</p></td>
                  
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_size_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                     <td align="right"><p><? echo $row[csf('excess_cut')]; ?></p></td>
                     <td align="right"><p><? echo $row[csf('plan_cut')]; ?></p></td>
                    <? 
					//$carrency_id=$row[csf('currency_id')];
					if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
					$tot_size_plan_cut_qty+=$row[csf('plan_cut')];
				   ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="4"><strong>Total</strong></td>
                <td align="right"><? echo $tot_size_order_qty; ?></td>
                <td align="right"><? //echo number_format($tot_delivery_qty,2,'.',''); ?></td>
                <td align="right"><? echo $format_total_amount=number_format($total_size_amount,2,'.',''); ?></td>
                 <td align="right"><? //echo number_format($tot_delivery_qty,2,'.',''); ?></td>
                <td align="right"><? echo $tot_size_plan_cut_qty; ?></td>
              
                
			</tr>
           <tr>
               <td colspan="9" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
         
        </table>
          <?
		}
		else //Finish
		{
			?>
			<table align="left" cellspacing="0" style=" margin:5px;" width="920"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="250" align="center">Item Description	</th>
                <th width="110" align="center">Width/Dia Type</th>
                <th width="50" align="center">GSM</th> 
                <th width="60" align="center"> gray or M/C Dia X Gauge</th> 
                <th width="60" align="center">Finish Dia</th>
                <th width="100" align="center">Color</th> 
                <th width="70" align="center">Order Qty</th>
                <th width="50" align="center">Rate</th>
                <th width="" align="center">Amount</th> 
               
                
               
            </thead>
		 <?
     		$i=1;
			
			$tot_size_order_qty=$total_size_amount=$tot_size_plan_cut_qty=0;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($main_process_id==2 || $main_process_id==3 || $main_process_id==4 || $main_process_id==6) //|| $process_id==7
				{
					$garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				}
				else
				{
					$garments_item;
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:100px"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></div></td>
                    <td><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:50px"><? echo $row[csf('grey_dia')]; ?></div></td>
                    
                    <td><p><? echo $row[csf('finish_dia')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    
                     <td align="right"><p><? echo $row[csf('qnty')]; $tot_size_order_qty+=$row[csf('qnty')]; ?>&nbsp;</p></td>
                  
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_size_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                      
                    
                    <? 
					//$carrency_id=$row[csf('currency_id')];
					if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
					$tot_size_plan_cut_qty+=$row[csf('plan_cut')];
				   ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="7"><strong>Total</strong></td>
                <td align="right"><? echo $tot_size_order_qty; ?></td>
                <td align="right"><? //echo number_format($tot_delivery_qty,2,'.',''); ?></td>
                <td align="right"><? echo $format_total_amount=number_format($total_size_amount,2,'.',''); ?></td>
                 
              
                
			</tr>
           <tr>
               <td colspan="10" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
         
        </table>
	<? }
		   ?>
        <br>
		 <?
            echo signature_table(200, $data[0], "930px");
         ?>
   </div>
  
   </div>
    <?
    exit();
}
?>