<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Delivery Roll Wise

Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	28-02-2015
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

/*$sql_compact=sql_select("select a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where   b.roll_id=a.id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33");
	foreach($sql_compact as $c_id)
	{
		$compacting_arr[]=$c_id[csf('barcode_no')];
		$compacting_details_arr[$c_id[csf('barcode_no')]]['prod_qty']=$c_id[csf('production_qty')];
	}
	print_r($compacting_details_arr);*/
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Fabric Production and QC By Roll","../", 1, 1, $unicode,'','');

if($db_type==0)
{
	$machine_array=return_library_array( "select id, concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
}
else
{
	$machine_array=return_library_array( "select id, (machine_no || '-' || brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
}
if(count($machine_array)==0)
{
	$machine_array=array();
}

?>
<script>

    <?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][66] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
<?php
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$jsconstructtion_arr=json_encode($constructtion_arr);
	echo "var constructtion_arr = ". $jsconstructtion_arr . ";\n";

	$jscomposition_arr=json_encode($composition_arr);
	echo "var composition_arr = ". $jscomposition_arr . ";\n";
?>

	function openmypage_mrr()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_fab_prod_roll_wise_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=mrr_popup','Receive Popup', 'width=825px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var recv_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			if(recv_id!="")
			{


				fnc_reset_form();
				get_php_form_data(recv_id, "populate_data_from_data", "requires/finish_fab_prod_roll_wise_entry_controller");

				var barcode_nos=return_global_ajax_value( recv_id, 'barcode_nos', '', 'requires/finish_fab_prod_roll_wise_entry_controller');
				if(trim(barcode_nos)!="")
				{
					create_row(1,trim(barcode_nos));
					/*var barcode_upd=barcode_nos.split(",");
					for(var k=0; k<barcode_upd.length; k++)
					{
						create_row(1,barcode_upd[k]);
					}*/
				}
				set_button_status(1, permission, 'fnc_finish_fab_production_roll_wise',1);
			}
		}
	}

	function openmypage_barcode()
	{
		var company_id=$('#cbo_company_id').val();
		var service_source=$('#cbo_service_source').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_fab_prod_roll_wise_entry_controller.php?company_id='+company_id+'&action=barcode_popup'+'&service_source='+service_source,'Barcode Popup', 'width=1000px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hide_service_source=this.contentDoc.getElementById("service_source").value; //Barcode Nos
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

			if(hide_service_source!=service_source)
			{
				$('#cbo_service_source').val(hide_service_source);
				load_drop_down('requires/finish_fab_prod_roll_wise_entry_controller',hide_service_source+'**'+company_id,'load_drop_down_knitting_com','dyeing_company_td' );
			}

			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);
				// var barcode_no 	= "";
				// var is_sales 	= "";
				// var barcode_upd = barcode_nos.split(",");

				// for(var k=0; k<barcode_upd.length; k++)
				// {
				// 	var barcode_nos = barcode_upd[k].split('_');
				// 	barcode_no 		+= barcode_nos[0] + ",";
				// 	is_sales    	+= barcode_nos[1] + ",";
				// }
				// create_row(0,barcode_no.substring(0, barcode_no.length - 1),is_sales.substring(0, is_sales.length - 1));
              /*.................additional code..............................*/
                 var proQtyTotal =0;
                 var qcQntyTotal = 0;
                 var rejectQntyTotal = 0;
                 $("#scanning_tbl").find('tbody tr').each(function()
                    {
                     proQtyTotal+=$(this).find('input[name="prodQty[]"]').val()*1;
                     qcQntyTotal+=$(this).find('input[name="qcPassQty[]"]').val()*1;
                     rejectQntyTotal+=$(this).find('input[name="reJectQty[]"]').val()*1;
                    });
                   $("#total_prodQnty").html(proQtyTotal.toFixed(2));
                   $("#total_QcPass").html(qcQntyTotal.toFixed(2));
                   $("#total_rejectQnty").html(rejectQntyTotal.toFixed(2));


				set_all_onclick();
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/finish_fab_prod_roll_wise_entry_controller.php?data=" + data+'&action='+action, true );
	}


	function fnc_finish_fab_production_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_delivery_print');
			return;
		}

	 	if(form_validation('cbo_company_id*cbo_service_source*cbo_service_company*txt_recv_date','Company*Service Source*Service Company*Receive Date')==false)
		{
			return;
		}
		var j=0; var dataString=''; var prev_batch=''; var prev_color=''; var breakOut = true; var new_batch_no=''; var new_batch_id='';var shift=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if(breakOut==false)
			{
				return;
			}
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var rollNo=$(this).find("td:eq(3)").text();
			var batchNo=$(this).find('input[name="batchNo[]"]').val();
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var consComp=$(this).find("td:eq(6)").text();
			var gsm=$(this).find('input[name="gsm[]"]').val();
			var dia=$(this).find('input[name="dia[]"]').val();
			var greygsm=$(this).find('input[name="greygsm[]"]').val();
			var greydia=$(this).find('input[name="greydia[]"]').val();

			var color=$(this).find('input[name="color[]"]').val();
			var diaType=$(this).find('select[name="diaType[]"]').val();
			var reJectQty=$(this).find('input[name="reJectQty[]"]').val();
            var qcPassQty=$(this).find('input[name="qcPassQty[]"]').val();

			var cboMachine=$(this).find('select[name="cboMachine[]"]').val();
			var cboShift=$(this).find('select[name="cboShift[]"]').val();
			var cboBatchStatus=$(this).find('select[name="cboBatchStatus[]"]').val();
			var rack=$(this).find('input[name="rack[]"]').val();
			var shelf=$(this).find('input[name="shelf[]"]').val();

			var batchId=$(this).find('input[name="batchId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollWgt=$(this).find('input[name="prodQty[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();

			var booking_without_order=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var booking_no=$(this).find('input[name="bookingNo[]"]').val();
			var greyQntyPcs=$(this).find('input[name="hddGreyQntyPcs[]"]').val();
			var collerCuffSize=$(this).find('input[name="hddCollerCuffSize[]"]').val();

			var chkBundle_id=$(this).find('input[name="chkBundle[]"]').attr("id");

			if($('#'+chkBundle_id).is(':checked'))
			{
				if(cboShift==0)
				{
					shift +=1;
				}
			}

			try
			{
				if(batchNo=="")
				{
					alert("Please Insert Batch No.");
					breakOut = false;
					return false;
				}

				if(color=="")
				{
					alert("Please Insert Color.");
					breakOut = false;
					return false;
				}

				var action_dis=$(this).find('input[name="batchNo[]"]').is(':disabled');
				if(batchId=="" || action_dis==false)
				{
					if(prev_batch=="")
					{
						prev_color=color;
						prev_batch=batchNo;
					}
					else
					{
						if(prev_batch!=batchNo)
						{
							alert("Please Insert Same Batch No.");
							$(this).find('input[name="batchNo[]"]').focus();
							breakOut = false;
							return false;
						}

						if(prev_color!=color)
						{
							alert("Please Insert Same Color.");
							$(this).find('input[name="color[]"]').focus();
							breakOut = false;
							return false;
						}
					}

					//new_batch_no=batchNo;
					/*if(new_batch_no=='') new_batch_no=batchNo;else new_batch_no+= ',' +batchNo;
					if(new_batch_id=='') new_batch_id=batchId;else new_batch_id+= ',' +batchId;
					alert(new_batch_no);*/
				}
				if(new_batch_no=='') new_batch_no=batchNo;else new_batch_no+= ',' +batchNo;
					if(new_batch_id=='') new_batch_id=batchId;else new_batch_id+= ',' +batchId;
					//alert(new_batch_no);
					color=encodeURIComponent(color);

				j++;

				dataString+='&barcodeNo_' + j + '=' + barcodeNo + '&rollNo_' + j + '=' + rollNo + '&batchNo_' + j + '=' + batchNo + '&bodyPartId_' + j + '=' + bodyPartId + '&consComp_' + j + '=' + consComp + '&gsm_' + j + '=' + gsm + '&dia_' + j + '=' + dia + '&color_' + j + '=' + color + '&diaType_' + j + '=' + diaType + '&reJectQty_' + j + '=' + reJectQty + '&qcPassQty_' + j + '=' + qcPassQty + '&cboMachine_' + j + '=' + cboMachine + '&cboShift_' + j + '=' + cboShift + '&cboBatchStatus_' + j + '=' + cboBatchStatus  + '&rack_' + j + '=' + rack + '&shelf_' + j + '=' + shelf + '&batchId_' + j + '=' + batchId + '&deterId_' + j + '=' + deterId + '&orderId_' + j + '=' + orderId + '&rollWgt_' + j + '=' + rollWgt + '&rollId_' + j + '=' + rollId + '&dtlsId_' + j + '=' + dtlsId + '&rolltableId_' + j + '=' + rolltableId+ '&IsSalesId_' + j + '=' + IsSalesId+ '&booking_without_order_' + j + '=' + booking_without_order+ '&booking_no_' + j + '=' + booking_no+ '&greygsm_' + j + '=' + greygsm+ '&greydia_' + j + '=' + greydia+ '&greyQntyPcs_' + j + '=' + greyQntyPcs+ '&collerCuffSize_' + j + '=' + collerCuffSize;
			}
			catch(e)
			{
				//got error no operation
			}
		});

		if(shift>0)
		{
			alert('please select shift name.');
			return;
		}

		if(breakOut==false)
		{
			return;
		}

		if(j<1)
		{
			alert('No data');
			return;
		}


		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+'&new_batch_no='+new_batch_no+'&new_batch_id='+new_batch_id+get_submitted_data_string('txt_recv_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_recv_date*txt_recv_challan*cbo_knit_location*cbo_store_name*cbo_location*fabric_store_auto_update*update_id*txt_deleted_id',"../")+dataString;

		freeze_window(operation);

		http.open("POST","requires/finish_fab_prod_roll_wise_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_finish_fab_production_roll_wise_Reply_info;
	}

	function fnc_finish_fab_production_roll_wise_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if(response[0]*1==20*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			show_msg(response[0]);

			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_recv_no').value = response[2];
				$('#txt_deleted_id').val( '' );
				add_dtls_data( response[3]);
				set_button_status(1, permission, 'fnc_finish_fab_production_roll_wise',1);
			}
			else if(response[0]==11)
			{
				alert("Duplicate Batch No "+ response[1]);
			}
			release_freezing();
		}
	}

	var roll_delivery_arr;
	function create_row(is_update,barcode_no)
	{
		var proQntTotal=0; var QcQntyTotal =0;var rejectQntTotal=0;
		var cbo_service_source = $("#cbo_service_source").val();
		

		if(cbo_service_source ==3)
		{
			var barcode_nos_data=return_global_ajax_value( barcode_no, 'json_barcode_data_outbound', '', 'requires/finish_fab_prod_roll_wise_entry_controller');
		}
		else
		{
			var barcode_nos_data=return_global_ajax_value( barcode_no, 'json_barcode_data', '', 'requires/finish_fab_prod_roll_wise_entry_controller');
		}

		//alert(barcode_nos_data);return;

		var barcode_nos_ref=barcode_nos_data.split("__");
		var scanned_barcode_prev=JSON.parse(barcode_nos_ref[0]);
		var barcode_dtlsId_array=JSON.parse(barcode_nos_ref[1]);
		var barcode_rollTableId_array=JSON.parse(barcode_nos_ref[2]);
		var dtls_data_arr=JSON.parse(barcode_nos_ref[3]);
		var roll_details_array=JSON.parse(barcode_nos_ref[4]);
		var barcode_array=JSON.parse(barcode_nos_ref[5]);
		var batch_dtls_arr=JSON.parse(barcode_nos_ref[6]);
		var batch_barcode_arr=JSON.parse(barcode_nos_ref[7]);
		var grey_iss_barcode_arr=JSON.parse(barcode_nos_ref[8]);
		var compacting_arr=JSON.parse(barcode_nos_ref[9]);
		var compacting_details_arr=JSON.parse(barcode_nos_ref[10]);
		roll_delivery_arr=JSON.parse(barcode_nos_ref[11]);
		var styleArr=JSON.parse(barcode_nos_ref[12]);

  		//alert(batch_dtls_arr[bar_code]['batch_qnty']);return;
		//alert(scanned_barcode+"\n"+barcode_dtlsId_array+"\n"+barcode_rollTableId_array+"\n"+dtls_data_arr+"\n"+roll_details_array+"\n"+barcode_array+"\n"+batch_dtls_arr+"\n"+batch_barcode_arr+"\n"+grey_iss_barcode_arr+"\n"+compacting_arr+"\n"+compacting_details_arr+"\n"+roll_delivery_arr);return;
		//alert(barcode_nos_ref[4]);
		var barcode_ref 	 = barcode_no.split(",");
		//var is_sales_barcode = is_sales.split(",");
		//alert(barcode_ref);return;
		var key;
		var row_num=$('#txt_tot_row').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		if (form_validation('cbo_company_id*cbo_service_source','Company*Service Source')==false)
		{
			return;
		}
		for (key in barcode_ref)
		{
			var bar_code=barcode_ref[key];
			//var num_row =$('#scanning_tbl tbody tr').length;
			var gsm=''; var width=''; var color=''; var receive_qnty=''; var reject_qty=''; var dia_width_type=''; var machine=''; var shift_name=''; var rack=''; var shelf=''; var production_qty=''; var original_gsm=''; var original_width=''; var batch_status=""; var grey_qnty_pcs=""; var coller_cuff_size="";
			if(is_update==0) // Save event
			{
				var check_barcode=batch_barcode_arr[bar_code];
				if(cbo_service_source==3 && !(check_barcode))
				{
					check_barcode=grey_iss_barcode_arr[bar_code];
				}

				if(!check_barcode)
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return;
				}

				if( jQuery.inArray( bar_code, scanned_barcode )>-1 || jQuery.inArray( bar_code, scanned_barcode_prev )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}

				
				gsm=roll_details_array[bar_code]['gsm'];
				width=roll_details_array[bar_code]['width'];
				booking_without_order = roll_details_array[bar_code]['booking_without_order'];
				booking_no = roll_details_array[bar_code]['booking_no'];
				determination_id = roll_details_array[bar_code]['deter_d'];
				if( jQuery.inArray( barcode_array[bar_code], compacting_arr )>-1)
				{
					production_qty=compacting_details_arr[bar_code]['prod_qty'];
					receive_qnty=compacting_details_arr[bar_code]['prod_qty'];
				}
				else
				{
					production_qty=batch_dtls_arr[bar_code]['batch_qnty'];
					receive_qnty=batch_dtls_arr[bar_code]['batch_qnty'];
				}

				original_gsm=roll_details_array[bar_code]['gsm'];
				original_width=roll_details_array[bar_code]['width'];
				
				grey_qnty_pcs=roll_details_array[bar_code]['qnty_pcs'];
				coller_cuff_size=roll_details_array[bar_code]['size'];
			}
			else // Update event
			{
				var dtls_data=dtls_data_arr[bar_code].split("**");
				gsm=dtls_data[0];
				width=dtls_data[1];
				color=dtls_data[2];
				receive_qnty=dtls_data[3];
				reject_qty=dtls_data[4];
				dia_width_type=dtls_data[5];
				machine=dtls_data[6];
				shift_name=dtls_data[7];
				rack=dtls_data[8];
				shelf=dtls_data[9];
				production_qty=dtls_data[10];
				booking_without_order=dtls_data[11];
				//index 12 booking no not used here
				original_gsm=dtls_data[13];
				original_width=dtls_data[14];
				batch_status=dtls_data[15];
				
				grey_qnty_pcs=dtls_data[16];
				coller_cuff_size=dtls_data[17];
			}

			var company_id=roll_details_array[bar_code]['company_id'];
			if(cbo_company_id!=company_id)
			{
				alert("Multiple Company Not Allowed");
				return;
			}

			var bar_code_no=$('#barcodeNo_'+row_num).val();

			if(bar_code_no!="")
			{
				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }
					});
				}).end().prependTo("#scanning_tbl");

				$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
				// $("#scanning_tbl tbody tr:first").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
			}
			//alert($("#booking_without_order_1").val());
			var cons_comp=constructtion_arr[roll_details_array[bar_code]['deter_d']]+", "+composition_arr[roll_details_array[bar_code]['deter_d']];
			//var cons_comp=roll_details_array[bar_code]['deter_d'];
			if(!batch_barcode_arr[bar_code])
			{
				$("#batchNo_"+row_num).val('');
				$("#color_"+row_num).val('');
				$("#batchId_"+row_num).val('');
				$("#diaType_"+row_num).val(0);

				$('#batchNo_'+row_num).removeAttr('disabled','disabled');
				$('#color_'+row_num).removeAttr('disabled','disabled');
			}
			else
			{
				$("#batchNo_"+row_num).val(batch_dtls_arr[bar_code]['batch_no']);
				$("#color_"+row_num).val(batch_dtls_arr[bar_code]['color']);
				$("#batchId_"+row_num).val(batch_dtls_arr[bar_code]['batch_id']);
				$("#diaType_"+row_num).val(batch_dtls_arr[bar_code]['width_dia_type']);

				if(batch_dtls_arr[bar_code]['entry_form']==0)
				{
					$('#batchNo_'+row_num).attr('disabled','disabled');
					$('#color_'+row_num).attr('disabled','disabled');
				}
				else
				{
					$('#batchNo_'+row_num).removeAttr('disabled','disabled');
					$('#color_'+row_num).removeAttr('disabled','disabled');
				}
			}
			
			// inhouse and outbound both data has Batch no and color, so both data will be disabled 
			$('#batchNo_'+row_num).attr('disabled','disabled');
			$('#color_'+row_num).attr('disabled','disabled');

			$("#sl_"+row_num).text(row_num);
			$("#barcode_"+row_num).text(barcode_array[bar_code]);
			$("#roll_"+row_num).text(roll_details_array[bar_code]['roll_no']);
			$("#orderNo_"+row_num).text(roll_details_array[bar_code]['po_number']);


			$("#jobNo_"+row_num).text(roll_details_array[bar_code]['job_number']);

			$("#style_"+row_num).text(roll_details_array[bar_code]['style_ref_no']);
			$("#bodyPart_"+row_num).text(roll_details_array[bar_code]['body_part']);
			$("#cons_"+row_num).text(cons_comp);
			$("#gsm_"+row_num).val(gsm);
			$("#dia_"+row_num).val(width);

			$("#greyGsmTd_"+row_num).text(original_gsm);
			$("#greyDiawidth_"+row_num).text(original_width);
			$("#greygsm_"+row_num).val(original_gsm);
			$("#greydia_"+row_num).val(original_width);
			
			$("#greyQntyPcs_"+row_num).text(grey_qnty_pcs);
			$("#collerCuffSize_"+row_num).text(coller_cuff_size);
			$("#hddGreyQntyPcs_"+row_num).val(grey_qnty_pcs);
			$("#hddCollerCuffSize_"+row_num).val(coller_cuff_size);


			//$("#rollWeight_"+row_num).text(roll_details_array[bar_code]['qnty']);
			$("#reJectQty_"+row_num).val(reject_qty);
			$("#qcPassQty_"+row_num).val(receive_qnty);
			$("#prodQty_"+row_num).val(production_qty);
			fnc_process_loss(row_num);

			$("#cboMachine_"+row_num).val(machine);
			$("#cboShift_"+row_num).val(shift_name);
			$("#rack_"+row_num).val(rack);
			$("#shelf_"+row_num).val(shelf);
			$("#cboBatchStatus_"+row_num).val(batch_status);

			$("#barcodeNo_"+row_num).val(barcode_array[bar_code]);
			$("#deterId_"+row_num).val(roll_details_array[bar_code]['deter_d']);
			$("#bodyPartId_"+row_num).val(roll_details_array[bar_code]['body_part_id']);
			$("#orderId_"+row_num).val(roll_details_array[bar_code]['po_breakdown_id']);
			$("#rollId_"+row_num).val(roll_details_array[bar_code]['roll_id']);
			$("#dtlsId_"+row_num).val(barcode_dtlsId_array[bar_code]);
			$("#rolltableId_"+row_num).val(barcode_rollTableId_array[bar_code]);
			$("#IsSalesId_"+row_num).val(roll_details_array[bar_code]['is_sales']);

			$("#bookingWithoutOrder_"+row_num).val(roll_details_array[bar_code]['booking_without_order']);
			$("#bookingNo_"+row_num).val(roll_details_array[bar_code]['booking_no']);

			if( jQuery.inArray( barcode_array[bar_code], compacting_arr )>-1)
			{
				$("#prodQty_"+row_num).val(production_qty);
				$("#prodQty_"+row_num).attr('disabled','true');
			}
			else
			{
				$("#prodQty_"+row_num).val(production_qty);
				$('#prodQty_'+row_num).removeAttr('disabled','disabled');
			}
			$('#prodQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","balance_qty("+row_num+");");
			$('#qcPassQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","balance_qty("+row_num+");");
			$('#reJectQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","total_reject_qty_fnc();fnc_process_loss("+row_num+")");
			 
			$('#cboMachine_'+row_num).removeAttr("onchange").attr("onchange","copy_value(this.value,'cboMachine_',"+row_num+");");
			$('#cboShift_'+row_num).removeAttr("onchange").attr("onchange","copy_value(this.value,'cboShift_',"+row_num+");");
			$('#cboBatchStatus_'+row_num).removeAttr("onchange").attr("onchange","copy_value(this.value,'cboBatchStatus_',"+row_num+");");
			$('#rack_'+row_num).removeAttr("onBlur").attr("onBlur","copy_value(this.value,'rack_',"+row_num+");");
			$('#shelf_'+row_num).removeAttr("onBlur").attr("onBlur","copy_value(this.value,'shelf_',"+row_num+");");

			//$("#reJectQty_"+row_num).attr('disabled','true');
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");

			if(is_update!=0)
			{
				if( jQuery.inArray( barcode_array[bar_code], roll_delivery_arr)>-1)
				{
					$("#batchNo_"+row_num).attr('disabled','true');
					$("#gsm_"+row_num).attr('disabled','true');
					$("#dia_"+row_num).attr('disabled','true');
					$("#color_"+row_num).attr('disabled','true');
					$("#diaType_"+row_num).attr('disabled','true');
					$("#prodQty_"+row_num).attr('disabled','true');
					//$("#reJectQty_"+row_num).attr('disabled','true');
					$("#cboMachine_"+row_num).attr('disabled','true');
					$("#cboShift_"+row_num).attr('disabled','true');
					$("#rack_"+row_num).attr('disabled','true');
					$("#shelf_"+row_num).attr('disabled','true');
				}
			}

			// ===================QC Result Start
			if (barcode_dtlsId_array[bar_code]!="" && barcode_dtlsId_array[bar_code]!=undefined) 
			{
				$("#qcResult_"+row_num).attr('disabled',false);
				$('#qcResult_'+row_num).removeClass('formbutton_disabled').addClass('formbuttonplasminus');
			}
			else
			{
				$("#qcResult_"+row_num).attr('disabled','disabled');
				$('#qcResult_'+row_num).removeClass('formbuttonplasminus').addClass('formbutton_disabled');
			}
			$('#qcResult_'+row_num).removeAttr("onclick").attr("onclick","fn_knit_defect("+row_num+");");
			// ===================QC Result End

			scanned_barcode.push(bar_code);
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			$("#chkBundle_"+row_num).attr('checked',true);
			/*.........additional code...................*/
			proQntTotal+= production_qty*1;
            QcQntyTotal += receive_qnty*1; //error receive_qnty  qnty reject_qty
            rejectQntTotal+= reject_qty*1;


		}
		$('#cbo_service_source').attr('disabled','disabled');
	    $("#total_prodQnty").html(($("#total_prodQnty").text()*1 + proQntTotal).toFixed(3));;
		$("#total_QcPass").html(($("#total_QcPass").text()*1 + QcQntyTotal).toFixed(3));;
		$("#total_rejectQnty").html(($("#total_rejectQnty").text()*1 + rejectQntTotal).toFixed(2));
		return;
	}

	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			
			if($('#cbo_service_source').val() == 0)
			{
				alert("Please select service source first.");
				$('#cbo_service_source').focus();
				return;
			}

			create_row(0,bar_code);
			set_all_onclick();
		}
	});

	function add_dtls_data( data )
	{
		var barcode_datas=data.split(",");
		var barcode_dtlsId_array=new Array;
		var barcode_rollTableId_array=new Array;
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var roll_table_id=datas[2];

			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_rollTableId_array[barcode_no] = roll_table_id;
		}

		var tot_num_row =$('#scanning_tbl tbody tr').length+1;
		for(var k=1; k<tot_num_row; k++)
		{
			$("#qcResult_"+k).attr('disabled',false);
			$('#qcResult_'+k).removeClass('formbutton_disabled').addClass('formbuttonplasminus');
		}

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();

			if(dtlsId=="")
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);
			}
		});
	}

	function fn_deleteRow( rid )
	{
		var num_row =$('#scanning_tbl tbody tr').length;
		var bar_code =$("#barcodeNo_"+rid).val();
		if( jQuery.inArray( bar_code, roll_delivery_arr)>-1)
		{
			alert('Sorry! Barcode Already Delivery to Store.');
			return;
		}

		var dtlsId =$("#dtlsId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();

		if(num_row==1)
		{
			$("#tr_"+rid).remove();
			var html='<tr id="tr_1" align="center" valign="middle"><td id="button_1" align="center" width="40"><input type="checkbox" id="chkBundle_1" name="chkBundle[]" /></td><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="50" id="roll_1"></td><td style="word-break:break-all;" width="80" id="orderNo_1"></td><td style="word-break:break-all;" width="100" id="jobNo_1"></td><td width="80" id="batch_1"><input type="text" name="batchNo[]" id="batchNo_1" style="width:67px" class="text_boxes" disabled/></td><td style="word-break:break-all;" width="70" id="bodyPart_1"></td><td style="word-break:break-all;" width="70" id="style_1"></td><td style="word-break:break-all;" width="120" id="cons_1" align="left"></td><td width="60" id="gsmTd_1"><input type="text" name="gsm[]" id="gsm_1" style="width:47px" class="text_boxes_numeric"/></td><td width="60" id="diawidth_1"><input type="text" name="dia[]" id="dia_1" style="width:47px" class="text_boxes"/></td><td width="60" id="greyGsmTd_1"></td><td width="60" id="greyDiawidth_1"></td><td width="70" id="colorName_1"><input type="text" name="color[]" id="color_1" style="width:57px" class="text_boxes" disabled onDblClick="openmypage_color(this.id);"/></td><td width="75" id="type_1">'+'<? echo create_drop_down( "diaType_1",73,$fabric_typee,"",1,"- Select -",0,'copy_value(this.value,"cboMachine_",1);',0,'','','','','','','diaType[]'); ?>'+'</td>\n\
                <td width="65" align="right" id="rollWeight_1"><input type="text" name="prodQty[]" id="prodQty_1" style="width:55px" class="text_boxes_numeric" onKeyUp="balance_qty(1)"/></td>\n\
                <td width="58" id="qcPass_1"><input type="text" name="qcPassQty[]" id="qcPassQty_1" style="width:47px" class="text_boxes_numeric" onKeyUp="balance_qty(1)"/></td>\n\
                <td width="60" id="greyQntyPcs_1"></td><td width="60" id="collerCuffSize_1"></td><td width="60" align="right" id="reject_1"><input type="text" id="reJectQty_1" name="reJectQty[]" class="text_boxes_numeric"  style="width:58px" onKeyUp="total_reject_qty_fnc()"></td>\n\
                <td width="60" align="right" id="processLoss_1"><input type="text" id="txtProcessLoss_1" name="txtProcessLoss[]" class="text_boxes_numeric"  style="width:58px"  ></td>\n\
                <td width="75" id="machine_1">'+'<? echo create_drop_down( "cboMachine_1",73,$machine_array,"",1,"- Select -",0,"",0,'','','','','','',"cboMachine[]"); ?>'+'</td><td width="75" id="shift_1">'+'<? echo create_drop_down( "cboShift_1",73,$shift_name,"", 1, "- Select -",0,'',0,'','','','','','','cboShift[]'); ?>'+'</td><td width="75" id="BatchStatus_1">'+'<? echo create_drop_down( "cboBatchStatus_1",73,$batch_status_array,"", 0, "- Select -",0,'',0,'','','','','','','cboBatchStatus[]'); ?>'+'</td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/><input type="hidden" name="bookingNo[]" id="bookingNo_1"/><input type="hidden" name="rack[]" id="rack_1" value="" style="width:50px" class="text_boxes" onBlur="copy_value(this.value,\'shelf_\',1)"/><input type="hidden" name="greygsm[]" id="greygsm_1" style="width:47px" class="text_boxes_numeric"/><input type="hidden" name="greydia[]" id="greydia_1" style="width:47px" class="text_boxes"/><input type="hidden" name="hddGreyQntyPcs[]" id="hddGreyQntyPcs_1"/><input type="hidden" name="collerCuffSize[]" id="collerCuffSize_1"/></td><td id="button_1" align="center"><input type="button" id="qcResult_1" name="qcResult[]" style="width:30px" class="formbutton_disabled" disabled="disabled" value="QC" onClick="fn_knit_defect(1);" /></td></tr>';
			$("#scanning_tbl tbody").html(html);
		}
		else
		{
			$("#tr_"+rid).remove();
		}

		var selected_id=txt_deleted_id;
		if(dtlsId!='')
		{
			if(selected_id=='') selected_id=dtlsId; else selected_id=txt_deleted_id+','+dtlsId;
			$('#txt_deleted_id').val( selected_id );
		}
		//alert($('#txt_deleted_id').val())
		/*.................additional code....................*/
	    var proQtyTotal =0;
        var qcQntyTotal = 0;
        var rejectQtyTotal =0;
        $("#scanning_tbl").find('tbody tr').each(function()
        {
            proQtyTotal+=$(this).find('input[name="prodQty[]"]').val()*1;
            qcQntyTotal+=$(this).find('input[name="qcPassQty[]"]').val()*1;
            rejectQtyTotal+=$(this).find('input[name="reJectQty[]"]').val()*1;
        });
        $("#total_prodQnty").html(proQtyTotal);
        $("#total_QcPass").html(qcQntyTotal);
        $("#reject_prodQnty").html(rejectQtyTotal);


		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
	}

	/*function balance_qty( rid )
	{
		var roll_qty=$("#prodQty_"+rid).val()*1;
		var reject_qty=$("#reJectQty_"+rid).val()*1;
		if(reject_qty>roll_qty)
		{
			alert("Reject Quantity Exceeds Roll Wgt.");
			$("#reJectQty_"+rid).val('');
			reject_qty=0;
		}
		var qcPassQty=roll_qty-reject_qty;
		$("#qcPassQty_"+rid).text(qcPassQty.toFixed(2));
	}
	*/

	function balance_qty( rid )
	{
		var roll_qty=$("#prodQty_"+rid).val()*1;
		var qcPassQty=$("#qcPassQty_"+rid).val()*1;
		var fabric_control_val=$("#fabric_control_val").val()*1;
		var variable_excess=$("#variable_excess").val()*1;
		var variable_excess_qty_kg=$("#variable_excess_qty_kg").val()*1;
		if(qcPassQty>roll_qty)
		{
			//alert(fabric_control_val);
			if(fabric_control_val==1)
			{
				if(variable_excess_qty_kg > 0)
				{
					var perc= variable_excess_qty_kg;
				}
				else
				{
					var perc=(roll_qty*variable_excess)/100;
				}

				perc=parseFloat(perc.toFixed(2));
				//alert(perc+" aa "+roll_qty+" qc"+qcPassQty);
				roll_qty=parseFloat(roll_qty.toFixed(2));
				//alert(roll_qty+perc);
				if( parseFloat(qcPassQty.toFixed(2))>(roll_qty+perc))
				{
					alert("QC Pass Quantity Exceeds Roll Wgt."+  parseFloat(qcPassQty.toFixed(2)) + ' > ' + roll_qty+'+'+perc);
					$("#qcPassQty_"+rid).val('');
				}
			}
			else
			{
				alert("QC Pass Quantity Exceeds Roll Wgt.");
				$("#qcPassQty_"+rid).val('');
				qcPassQty=0;

			}
			
		}
		var reject_qty=roll_qty-qcPassQty;
            $("#reJectQty_"+rid).val(reject_qty)
		//$("#reject_"+rid).text(reject_qty.toFixed(2));

			var rollQcQntyTotal=0; var rollRejectQntyTotal=0;
			var tot_num_row =$('#scanning_tbl tbody tr').length+1;
			for(var k=1; k<tot_num_row; k++)
			{
				rollQcQntyTotal+=$("#qcPassQty_"+k).val()*1; //$(this).find(".qcPassQty_+k").html();*1;
			   	rollRejectQntyTotal+=$("#reJectQty_"+k).val()*1; //$(this).find(".reJectQty_+k").html();*1;
			}
			$("#total_QcPass").html(number_format(rollQcQntyTotal,2));
			$("#total_rejectQnty").html(number_format(rollRejectQntyTotal,2));

	}

	function total_reject_qty_fnc()
	{
		var rollRejectQntyTotal=0;
		var tot_num_row =$('#scanning_tbl tbody tr').length+1;
		for(var k=1; k<tot_num_row; k++)
		{
		   	rollRejectQntyTotal+=$("#reJectQty_"+k).val()*1; //$(this).find(".reJectQty_+k").html();*1;
		}
		$("#total_rejectQnty").html(number_format(rollRejectQntyTotal,2));
	}


	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td id="button_1" align="center" width="40"><input type="checkbox" id="chkBundle_1" name="chkBundle[]" /></td><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="50" id="roll_1"></td><td style="word-break:break-all;" width="80" id="orderNo_1"></td><td style="word-break:break-all;" width="100" id="jobNo_1"></td><td width="80" id="batch_1"><input type="text" name="batchNo[]" id="batchNo_1" style="width:67px" class="text_boxes" disabled/></td><td style="word-break:break-all;" width="70" id="bodyPart_1"></td><td style="word-break:break-all;" width="70" id="style_1"></td><td style="word-break:break-all;" width="120" id="cons_1" align="left"></td><td width="60" id="gsmTd_1"><input type="text" name="gsm[]" id="gsm_1" style="width:47px" class="text_boxes_numeric"/></td><td width="60" id="diawidth_1"><input type="text" name="dia[]" id="dia_1" style="width:47px" class="text_boxes"/></td><td width="60" id="greyGsmTd_1"></td><td width="60" id="greyDiawidth_1"></td><td width="70" id="colorName_1"><input type="text" name="color[]" id="color_1" style="width:57px" class="text_boxes" disabled onDblClick="openmypage_color(this.id);"/></td><td width="75" id="type_1">'+'<? echo create_drop_down( "diaType_1",73,$fabric_typee,"",1,"- Select -",0,'',1,'','','','','','','diaType[]'); ?>'+'</td><td width="65" align="right" id="rollWeight_1"><input type="text" name="prodQty[]" id="prodQty_1" style="width:55px" readonly class="text_boxes_numeric" onKeyUp="balance_qty(1)"/></td>\n\
                <td width="58" id="qcPass_1"><input type="text" name="qcPassQty[]" id="qcPassQty_1" style="width:47px" class="text_boxes_numeric" onKeyUp="balance_qty(1)"/></td>\n\
                <td width="60" id="greyQntyPcs_1"></td><td width="60" id="collerCuffSize_1"></td><td width="60" align="right" id="reject_1"><input type="text" id="reJectQty_1" name="reJectQty[]" class="text_boxes_numeric"  style="width:58px" onKeyUp="total_reject_qty_fnc()"></td>\n\
                <td width="60" align="right" id="processLoss_1"><input type="text" id="txtProcessLoss_1" name="txtProcessLoss[]" class="text_boxes_numeric"  style="width:58px" ></td>\n\
                <td width="75" id="machine_1">'+'<? echo create_drop_down( "cboMachine_1",73,$machine_array,"",1,"- Select -",0,'copy_value(this.value,"cboMachine_",1);',0,'','','','','','','cboMachine[]'); ?>'+'</td><td width="75" id="shift_1">'+'<? echo create_drop_down( "cboShift_1",73,$shift_name,"", 1, "- Select -",0,'copy_value(this.value,"cboShift_",1);',0,'','','','','','','cboShift[]'); ?>'+'</td><td width="75" id="BatchStatus_1">'+'<? echo create_drop_down( "cboBatchStatus_1",73,$batch_status_array,"", 0, "- Select -",0,'copy_value(this.value,"cboBatchStatus_",1);',0,'','','','','','','cboBatchStatus[]'); ?>'+'</td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/><input type="hidden" name="bookingNo[]" id="bookingNo_1"/></td><td id="button_1" align="center"><input type="button" id="qcResult_1" name="qcResult[]" style="width:30px" class="formbutton_disabled" disabled="disabled" value="QC" onClick="fn_knit_defect(1);" /></td>\n\
                 	<input type="hidden" name="rack[]" id="rack_1" value="" style="width:50px" class="text_boxes" onBlur="copy_value(this.value,\'shelf_\',1)"/><input type="hidden" name="shelf[]" id="shelf_1" value="" style="width:50px" class="text_boxes_numeric" onBlur="copy_value(this.value,\'shelf_\',1)"/><input type="hidden" name="greygsm[]" id="greygsm_1" style="width:47px" class="text_boxes_numeric"/><input type="hidden" name="greydia[]" id="greydia_1" style="width:47px" class="text_boxes"/><input type="hidden" name="hddGreyQntyPcs[]" id="hddGreyQntyPcs_1"/><input type="hidden" name="collerCuffSize[]" id="collerCuffSize_1"/></tr>';

		$('#txt_recv_no').val('');
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').removeAttr('disabled','disabled');
		$('#cbo_service_source').val(0);
		$('#cbo_service_source').removeAttr('disabled','disabled');
		$('#cbo_service_company').val('');
		$('#cbo_store_name').val(0);
		$('#txt_recv_challan').val('');
		$('#txt_recv_date').val('');
		$('#txt_deleted_id').val('');
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$("#total_prodQnty").html("");
        $("#total_QcPass").html("");
        $("#reject_prodQnty").html("");
		$("#scanning_tbl tbody").html(html);
		set_button_status(0, permission, 'fnc_finish_fab_production_roll_wise',1);
	}

	function copy_value(value,field_id,i)
	{
		var rowCount=$('#scanning_tbl tbody tr').length;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var currentAnyID = $(this).find('select[name="cboBatchStatus[]"]').attr("id"); //any field to pick current serial number
			var currentAnyIDARR = currentAnyID.split("_");
			// copy only that and below selected data
			if( i >= currentAnyIDARR[1]*1 )
			{
				$("#"+field_id+currentAnyIDARR[1]*1).val(value);
			}
		});
	}

	function fn_knit_defect( rid )
	{
		var barcode_no =$("#barcodeNo_"+rid).val();
		var company_id=$("#cbo_company_id").val();
		var dtlsId=$("#dtlsId_"+rid).val();
		var roll_maintained=1;
		// alert(dtlsId+'='+roll_maintained+'='+company_id+'='+barcode_no);return;
		if(dtlsId=="")
		{
			alert("Sorry !!.");return;
		}
		else
		{
			var title = 'finish Defect Info';
			var page_link='requires/finish_fab_prod_roll_wise_entry_controller.php?update_dtls_id='+dtlsId+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&barcode_no='+barcode_no+'&action=finish_defect_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				
			}
		}
	}

	function check_all___()
	{
		if($('#txt_check_all').attr('checked'))
		{
			$('#scanning_tbl tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').attr('checked','checked');
            });
		}
		else
		{
			$('#scanning_tbl tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').removeAttr('checked');
            });
		}
	}

	function check_all() 
	{
		// alert();
		$('input[name="chkBundle[]"]').each(function (index, element) {

			if ($('#txt_check_all').prop('checked') == true)
				$(this).attr('checked', 'true');
			else
				$(this).removeAttr('checked');
		});
	}

//popu color
function openmypage_color(id)
{
var ord_id = $('#orderId_1').val();
emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_fab_prod_roll_wise_entry_controller.php?ord_id='+ord_id+'&action=color_popup','Color Popup', 'width=280px,height=250px,center=1,resize=1,scrolling=0','')
emailwindow.onclose=function()
  		{
			var colorName=this.contentDoc.getElementById("hidden_color_name").value;	 //Access form field with id="emailfield"
			//alert(colorName);
			$('#'+id).val(colorName);
		}
}
function check_is_inhouse(data)
{
	var service_com=$("#cbo_service_company").val();
	load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller',data+'**'+service_com,'load_drop_down_knit_loc','knit_location_td' );

}
function fnc_process_loss(index)
{
	var prodQty=$("#prodQty_"+index).val()*1;
	var qcPassQty=$("#qcPassQty_"+index).val()*1;
	var reJectQty=$("#reJectQty_"+index).val()*1;
	var processLoss=(prodQty-(qcPassQty+reJectQty));
	//alert(processLoss);
	processLoss=processLoss.toFixed(2);
	//alert(processLoss);
	$("#txtProcessLoss_"+index).val(processLoss)

}

function fnc_barcode_code128(type)
{
	var dtls_id=0;
	var mst_id=$('#update_id').val();
	if(mst_id=="")
	{
		alert("Save First");	
		return;
	}
	var data="";
	var error=1;
	$('input[name="chkBundle[]"]').each(function(index, element) 
	{
		if( $(this).prop('checked')==true)
		{
			error=0;
			var idd=$(this).attr('id').split("_");
			var roll_id=$('#rolltableId_'+idd[1] ).val();
			if(roll_id!="")
			{
				if(data=="") data=$('#rolltableId_'+idd[1] ).val(); else data=data+","+$('#rolltableId_'+idd[1] ).val();
			}
			else
			{
				$(this).prop('checked',false);
			}
		}
	});

	if( error==1 )
	{
		alert('No data selected');
		return;
	}

	freeze_window(3);
	// data=data+"***"+dtls_id;
	// data=data+"***"+dtls_id+"*********"+mst_id;

	if(type==9)
	{
		var url=return_ajax_request_value(data, "print_barcode_b", "requires/finish_fab_prod_roll_wise_entry_controller");
	}
	if(type==2)
	{
		var url=return_ajax_request_value(data, "print_barcode_2", "requires/finish_fab_prod_roll_wise_entry_controller");
	}


	window.open(url,"##");
	release_freezing();
}

function fnc_load_report_format(data)
{
	var report_ids='';
	var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/finish_fab_prod_roll_wise_entry_controller');
	print_report_button_setting(report_ids);
}
function print_report_button_setting(report_ids)
{
	if(trim(report_ids)=="")
	{
		$("#btn_barcode_b").hide();
		$("#btn_barcode_2").hide();
		
	}
	else
	{
		$("#btn_barcode_b").hide();
		$("#btn_barcode_2").hide();
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==298)
			{
				$("#btn_barcode_b").show();
			}
			else if(report_id[k]==299)
			{
				$("#btn_barcode_2").show();
			}
			
		}
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="6" align="center"><b>Receive No&nbsp;</b>
                        	<input type="text" name="txt_recv_no" id="txt_recv_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_mrr()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller', this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller', this.value,'load_drop_down_loc', 'location_td' );get_php_form_data(this.value,'fabric_store_auto_update','requires/finish_fab_prod_roll_wise_entry_controller' );fnc_load_report_format(this.value);",0 );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );check_is_inhouse(this.value);","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Receive Date</td>
                        <td><input type="text" name="txt_recv_date" value="<? echo date("d-m-Y"); ?>" id="txt_recv_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right">Receive Challan</td>
                        <td>
                        	<input type="text" name="txt_recv_challan" id="txt_recv_challan" class="text_boxes_numeric" style="width:140px;"/>
                        </td>


                        <td align="right">Service Location </td>
                        <td id="knit_location_td" >
                            <?
                                echo create_drop_down( "cbo_knit_location", 152, $blank_array,"",1, "--Select --", 1, "" );
                            ?>
                        </td>
                    </tr>

                    <tr>
                    	<td align="right">Location</td>
                            <td id="location_td">
								<?
								echo create_drop_down("cbo_location", 152, $blank_array, "", 1, "-- Select Location --", 0, "");
								?>
                            </td>
							<td align="right" colspan="2"><strong>Roll Number</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right" style="visibility: hidden;"> Store Name </td>
                        <td id="store_td" style="visibility: hidden;">
                            <?
                                echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select --", 1, "" );
                            ?>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1190px;text-align:left">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;

                        height:20px;
                        overflow:auto;
                    }
                </style>
				<table cellpadding="0" width="1805" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">Check All <br><input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)"  checked=""/></th>
                    	<th width="30">SL</th>
                        <th width="70">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="80">Order/FSO No</th>
                        <th width="100">Job No</th>
                        <th width="80" class="must_entry_caption">Batch No</th>
                        <th width="70">Body Part</th>
                        <th width="70">Style</th>
                        <th width="120">Construction/ Composition</th>
                        <th width="60">GSM</th>
                        <th width="60">Dia</th>
                        <th width="60">Grey GSM</th>
                        <th width="60">Grey Dia</th>
                        <th width="70">Color</th>
                        <th width="75">Dia/Width Type</th>
                        <th width="67">Prod. Qty.</th>
                        <th width="60">QC Pass Qty.</th>
                        <th width="60">Qty in Pcs</th>
                        <th width="60">Item Size</th>
                        <th width="70">Reject Qty.</th>
                        <th width="69">Process Loss</th>
                        <th width="75">Machine Name</th>
                        <th width="75" class="must_entry_caption">Shift Name</th>
                        <th width="75">Batch Status</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1825px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1805" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                        		<td id="button_1" align="center" width="40">
                                    <input type="checkbox" id="chkBundle_1" name="chkBundle[]" checked="" />
                                </td>

                                <td width="30" id="sl_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="50" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="orderNo_1"></td>
                                <td style="word-break:break-all;" width="100" id="jobNo_1"></td>
                                <td width="80" id="batch_1"><input type="text" name="batchNo[]" id="batchNo_1" style="width:67px" class="text_boxes" disabled/></td>
                                <td style="word-break:break-all;" width="70" id="bodyPart_1"></td>

                                <td style="word-break:break-all;" width="70" id="style_1"></td>

                                <td style="word-break:break-all;" width="120" id="cons_1" align="left"></td>
                                <td width="60" id="gsmTd_1"><input type="text" name="gsm[]" id="gsm_1" style="width:47px" class="text_boxes_numeric"/></td>
                                <td width="60" id="diawidth_1"><input type="text" name="dia[]" id="dia_1" style="width:47px" class="text_boxes"/></td>
                                <td width="60" id="greyGsmTd_1"></td>
                                <td width="60" id="greyDiawidth_1"></td>
                                <td width="70" id="colorName_1"><input type="text" name="color[]" id="color_1" style="width:57px" class="text_boxes"  onDblClick="openmypage_color(this.id)"  readonly placeholder="Browse Color"/></td>
                                <td width="75" id="type_1"><? echo create_drop_down( "diaType_1",73,$fabric_typee,"",1,"- Select -",0,'',1,'','','','','','','diaType[]'); ?></td>

                                <td width="67" align="right" id="rollWeight_1"><input type="text" name="prodQty[]" id="prodQty_1" style="width:55px" class="text_boxes_numeric"  onKeyUp="balance_qty(1)" disabled readonly/></td>
                                <!--<td width="70" id="reject_1"><input type="text" name="reJectQty[]" id="reJectQty_1" style="width:58px" class="text_boxes_numeric" onKeyUp="balance_qty(1)"/></td>
                                <td width="60" align="right" id="qcPassQty_1"></td>-->

                                <td width="58" id="qcPass_1"><input type="text" name="qcPassQty[]" id="qcPassQty_1" style="width:47px" class="text_boxes_numeric" onKeyUp="balance_qty(1);fnc_process_loss(1);"/></td>
                                <td width="60" id="greyQntyPcs_1"></td>
                                <td width="60" id="collerCuffSize_1"></td>
                                <td width="60" align="right" id="reject_1"><input type="text" id="reJectQty_1" name="reJectQty[]" class="text_boxes_numeric" style="width:58px" onKeyUp="total_reject_qty_fnc();fnc_process_loss(1);"  ></td>

                                <td width="60" align="right" id="processLoss_1"><input type="text" id="txtProcessLoss_1" name="txtProcessLoss[]" class="text_boxes_numeric" style="width:58px"   disabled readonly></td>

                                <td width="75" id="machine_1"><? echo create_drop_down("cboMachine_1",73,$machine_array,"",1,"- Select -",0,"copy_value(this.value,'cboMachine_',1);",0,'','','','','','','cboMachine[]'); ?></td>
                                <td width="75" id="shift_1"><? echo create_drop_down( "cboShift_1",73,$shift_name,"", 1, "- Select -",0,"copy_value(this.value,'cboShift_',1);",0,'','','','','','','cboShift[]'); ?></td>
								<td width="75" id="BatchStatus_1"><? echo create_drop_down( "cboBatchStatus_1",73,$batch_status_array,"", 0, "- Select -",0,"copy_value(this.value,'cboBatchStatus_',1);",0,'','','','','','','cboBatchStatus[]'); ?></td>

                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                                    <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                                    <input type="hidden" name="bookingNo[]" id="bookingNo_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1" value="" style="width:50px" class="text_boxes" onBlur="copy_value(this.value,'shelf_',1)"/>
                                	<input type="hidden" name="shelf[]" id="shelf_1" value="" style="width:50px" class="text_boxes_numeric" onBlur="copy_value(this.value,'shelf_',1)"/>
                                	<input type="hidden" name="greygsm[]" id="greygsm_1" style="width:47px" class="text_boxes_numeric"/>
                                	<input type="hidden" name="greydia[]" id="greydia_1" style="width:47px" class="text_boxes"/>
                                	<input type="hidden" name="hddGreyQntyPcs[]" id="hddGreyQntyPcs_1"/>
                                	<input type="hidden" name="hddCollerCuffSize[]" id="hddCollerCuffSize_1"/>
                                </td>
                                <td id="button_1" align="center">
                                    <input type="button" id="qcResult_1" name="qcResult[]" style="width:30px" class="formbutton_disabled" value="QC" onClick="fn_knit_defect(1);" disabled="disabled" />
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="16">Total</th>
                                <th id="total_prodQnty"></th>
                                <th id="total_QcPass"></th>
                                <th></th>
                                <th></th>
                                <th id="total_rejectQnty"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1600" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="fabric_store_auto_update" id="fabric_store_auto_update" readonly>
                            <input type="hidden" name="fabric_control_val" id="fabric_control_val" readonly>
                            <input type="hidden" name="variable_excess" id="variable_excess" readonly>
                            <input type="hidden" name="variable_excess_qty_kg" id="variable_excess_qty_kg" readonly>
                            <?
                            	echo load_submit_buttons($permission,"fnc_finish_fab_production_roll_wise",0,0,"fnc_reset_form()",1);
                            ?>
                            <input type="button" id="btn_barcode_b" name="btn_barcode_b" value="Barcode Sticker" class="formbutton" onClick="fnc_barcode_code128(9)"/>
							<input type="button" id="btn_barcode_2" name="btn_barcode_2" value="Barcode Sticker 2" class="formbutton" onClick="fnc_barcode_code128(2)"/>
                        </td>
                    </tr>
                </table>
			</fieldset>
        </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
