<?
/*-- ------------------------------------------ Comments

Purpose			: 	This form will create Finish Fabric Delivery Roll Wise

Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	09-03-2015
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Fabric Delivery Roll Wise","../../", 1, 1, $unicode,'','');
$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	var barcode_dtlsId_array=new Array();
	var barcode_scanned_qnty_array=new Array();

	function openmypage_challan()
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_feb_delivery_roll_entry_controller.php?action=challan_popup&comp_id='+$('#cbo_company_id').val(),'Challan Popup', 'width=835px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_data=this.contentDoc.getElementById("hidden_data").value;	 //challan Id and Number
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos


			if(barcode_nos!="")
			{
				fnc_reset_form();
				var challan_data=hidden_data.split("**");
				$('#update_id').val(challan_data[0]);
				$('#txt_challan_no').val(challan_data[1]);
				$('#txt_delivery_date').val(challan_data[2]);
				$('#txt_attention').val(challan_data[3]);
				$('#txt_remarks').val(challan_data[4]);

				get_php_form_data( challan_data[5] + "__"+challan_data[0], 'populate_delivery_master_n_company_wise_report_button_setting','requires/finish_feb_delivery_roll_entry_controller' );

				//create_row(1,barcode_nos);

				
				var html=return_global_ajax_value(challan_data[0], 'populate_data_update_barcode', '', 'requires/finish_feb_delivery_roll_entry_controller');
				if(trim(html)!="")
				{
					$("#scanning_tbl tbody").html(html);
				}

				set_all_onclick();
				set_button_status(1, permission, 'fnc_grey_delivery_roll_wise',1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#printFso_v2").removeClass('formbutton_disabled');
				$("#printFso_v2").addClass('formbutton');

				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');

				var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;var rollGreyQntyTotal = 0;//prodQty_2 / rejectQty_2 / greytQty_2


				var tot_num_row =$('#scanning_tbl tbody tr').length+1;
				for(var k=1; k<tot_num_row; k++)
				{
					rollProductionQntyTotal+=$("#prodQty_"+k).html()*1
					rollRejectQntyTotal+=$("#rejectQty_"+k).html()*1
					rollGreyQntyTotal+=$("#greyQty_"+k).html()*1
				}
				$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
				$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
				$("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));

				$("#scanning_tbl").find('tbody tr').each(function(){
				   rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
				});
				$("#rollQntyTotal").html(number_format(rollQntyTotal,2));

			}
		}
	}
	function total_crnDelvQty_rejQty_prodQty(){
		var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;rollGreyQntyTotal = 0;//prodQty_2 / rejectQty_2 / greyQty_2
		$("#scanning_tbl").find('tbody tr').each(function(){
		   rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
		   rollProductionQntyTotal+=$(this).find('input[name="prodQnty[]"]').val()*1;
		   rollRejectQntyTotal+=$(this).find('input[name="rejectQnty[]"]').val()*1;
		   rollGreyQntyTotal+=$(this).find('input[name="greyQnty[]"]').val()*1;


		});
		$("#rollQntyTotal").html(number_format(rollQntyTotal,2));
		$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
		$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
		$("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));
	}
	function openmypage_barcode()
	{
		var company_id=$('#cbo_company_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_feb_delivery_roll_entry_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);

				/*var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k]);
				}*/
				var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;var rollGreyQntyTotal = 0;//prodQty_2 / rejectQty_2 / greyQty_2
				var tot_num_row =$('#scanning_tbl tbody tr').length+1;
				for(var k=1; k<tot_num_row; k++)
				{
					rollProductionQntyTotal+=$("#prodQty_"+k).html()*1
					rollRejectQntyTotal+=$("#rejectQty_"+k).html()*1
					rollGreyQntyTotal+=$("#greyQty_"+k).html()*1
				}
				$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
				$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
				$("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));

				$("#scanning_tbl").find('tbody tr').each(function(){
				   rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
				});
				$("#rollQntyTotal").html(number_format(rollQntyTotal,2));
				set_all_onclick();
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/finish_feb_delivery_roll_entry_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_delivery_roll_wise(operation)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}


		/*if(operation==4)
		{
			var vbarcode='';
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if( vbarcode=='') vbarcode=$(this).find('input[name="barcodeNo[]"]').val();
				else vbarcode=vbarcode+","+$(this).find('input[name="barcodeNo[]"]').val();

			});
			//alert(vbarcode);
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+vbarcode,'grey_delivery_print');
			return;
		}*/

	 	if(form_validation('txt_delivery_date*cbo_company_id*cbo_knitting_source*txt_knit_company','Delivery Date*Company*Knitting Source*Knitting Company')==false)
		{
			return;
		}
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_delivery_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var currentDelivery=$(this).find('input[name="currentDelivery[]"]').val()*1;
			var rejectQnty=$(this).find('input[name="rejectQnty[]"]').val()*1;
			var productionId=$(this).find('input[name="productionId[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var productionDtlsId=$(this).find('input[name="productionDtlsId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var gsm=$(this).find("td:eq(14)").text();
			var dia=$(this).find("td:eq(15)").text();
			var jobNo=$(this).find("td:eq(21)").text();
			var systemId=$(this).find("td:eq(25)").text();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var widthTypeId=$(this).find('input[name="widthTypeId[]"]').val();
			var finMstId=$(this).find('input[name="finMstId[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			var rollQty=$(this).find('input[name="rollQty[]"]').val()*1;
			var reProcess=$(this).find('input[name="reProcess[]"]').val();
			var prereProcess=$(this).find('input[name="prereProcess[]"]').val();

			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var bookingNumber=$(this).find('input[name="bookingNumber[]"]').val();

			var greyQntyPcs=$(this).find('input[name="hddGreyQntyPcs[]"]').val();
			var collerCuffSize=$(this).find('input[name="hddCollerCuffSize[]"]').val();

			try
			{
				if(currentDelivery<0.1)
				{
					alert("Please Insert Roll Qty.");
					return;
				}

				j++;

				dataString+='&currentDelivery_' + j + '=' + currentDelivery + '&productionId_' + j + '=' + productionId + '&barcodeNo_' + j + '=' + barcodeNo + '&productionDtlsId_' + j + '=' + productionDtlsId + '&deterId_' + j + '=' + deterId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollNo_' + j + '=' + rollNo + '&dtlsId_' + j + '=' + dtlsId+ '&gsm_' + j + '=' + gsm + '&dia_' + j + '=' + dia + '&jobNo_' + j + '=' +jobNo + '&finMstId_' + j + '=' + finMstId+ '&colorId_' + j + '=' + colorId + '&bodyPartId_' + j + '=' + bodyPartId + '&widthTypeId_' + j + '=' + widthTypeId + '&rejectQnty_' + j + '=' + rejectQnty+ '&systemId_' + j + '=' + systemId+ '&batchId_' + j + '=' + batchId+ '&rollQty_' + j + '=' + rollQty+ '&prereProcess_' + j + '=' + prereProcess+ '&reProcess_' + j + '=' + reProcess+ '&IsSalesId_' + j + '=' + IsSalesId+ '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder+ '&bookingNumber_' + j + '=' + bookingNumber+ '&greyQntyPcs_' + j + '=' + greyQntyPcs+ '&collerCuffSize_' + j + '=' + collerCuffSize;
			}
			catch(e)
			{
				//got error no operation
			}
		});

		if(j<1)
		{
			alert('No data');
			return;
		}
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_knitting_source*txt_knit_company*knit_company_id*knit_location_id*update_id*txt_deleted_id*txt_attention*txt_remarks*txt_deleted_barcode*txt_del_barcode_reprocess',"../../")+dataString;
		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_feb_delivery_roll_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if (response[0] * 1 == 20 * 1)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			show_msg(response[0]);

			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_challan_no').value = response[2];
				$('#txt_deleted_id').val( '' );
				add_dtls_data( response[3]);
				set_button_status(1, permission, 'fnc_grey_delivery_roll_wise',1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#printFso_v2").removeClass('formbutton_disabled');
				$("#printFso_v2").addClass('formbutton');
				
				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');
			}
			release_freezing();
		}
	}

	//var scanned_barcode=new Array();
	/*function create_row(is_update,barcode_no)
	{
		var barcode_upd		 = barcode_no.split(",");
		var bar_code=trim( barcode_no );
		var num_row =$('#scanning_tbl tbody tr').length;

		var msg=0;
		var barcode_da = bar_code.split(",");
		 $("#scanning_tbl").find('tbody tr').each(function() {
            for (var k = 0; k < barcode_da.length; k++) {
                var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
                //alert(trim(barcodeNo));
                //alert(barcode_da[k]);

                if(trim(barcodeNo) == barcode_da[k]){
                	//alert("Yes");
                    msg++;
                    return;
                }
            }
        });
        if(msg>0){
            alert("Barcode already scanned");
            return;
        }

		if( $('#barcode_'+num_row ).text()!='' )
		{
			num_row++;
			var vnum=num_row;
		}
		else
			var vnum=2;

		var num_row_len=0;
		for(var k=0; k<barcode_upd.length; k++)
		{
			num_row_len=vnum+k;

			$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
			{
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ num_row_len },
				  'value': function(_, value) { return value }
				});
			}).end().prependTo("#scanning_tbl");
			$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+num_row_len);
		}

		//if( $('#cbo_company_id').val()==0 )
		if( $('#barcode_'+num_row ).text()=='' )
			var bar_c=bar_code+"__"+num_row+"__1";
		else
			var bar_c=bar_code+"__"+num_row+"__0";


		var update_id=$("#update_id").val();
		if( is_update==1 )
			bar_c=bar_c+"__1"+"__"+num_row_len;
		else
			bar_c=bar_c+"__0"+"__"+num_row_len;

		if( is_update==1 )
			get_php_form_data( update_id+"__"+bar_c, "populate_data_update_barcode", "requires/finish_feb_delivery_roll_entry_controller" );
		else
			get_php_form_data( bar_c, "populate_data_from_barcode", "requires/finish_feb_delivery_roll_entry_controller" );
			total_crnDelvQty_rejQty_prodQty();


		$("#scanning_tbl").find('tbody tr').each(function()
		{
			//N.B 
			//1. As cloning rows are added before data comes so blank rows ( data not comes from controller validation) are ommited. 
			//2. initial blank row (tr_1) is not removed for new data add through cloning.
			
			if( $(this).find('input[name="barcodeNo[]"]').val()=='' && $(this).attr('id')!='tr_1')  $(this).remove();
			
		});

		//var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();

		//N. B. As num_row has incrementend during scan but row may not be added in controller validation so it is re counted.
		num_row = $('#scanning_tbl tbody tr').length;

		$('#txt_tot_row').val(num_row);
		$('#txt_bar_code_num').val('');
		$('#txt_bar_code_num').focus();
	}*/

	function create_row(is_update,barcode_no)
	{
		var barcode_upd		 = barcode_no.split(",");
		var bar_code=trim( barcode_no );
		var num_row =$('#scanning_tbl tbody tr').length;

		var msg=0;
		var barcode_da = bar_code.split(",");
		$("#scanning_tbl").find('tbody tr').each(function() 
		{
            for (var k = 0; k < barcode_da.length; k++) 
            {
                var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
                if(trim(barcodeNo) == barcode_da[k])
                {
                    msg++;
                    return;
                }
            }
        });
        if(msg>0){
            alert("Barcode already scanned");
            $('#txt_bar_code_num').val('');
            return;
        }


		/*if( $('#barcode_'+num_row ).text()!='' )
		{
			num_row++;
			var vnum=num_row;
		}
		else
			var vnum=2;

		var num_row_len=0;*/
		/*for(var k=0; k<barcode_upd.length; k++)
		{
			num_row_len=vnum+k;

			$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
			{
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ num_row_len },
				  'value': function(_, value) { return value }
				});
			}).end().prependTo("#scanning_tbl");
			$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+num_row_len);
		}*/

		//if( $('#cbo_company_id').val()==0 )
		/*if( $('#barcode_'+num_row ).text()=='' )
			var bar_c=bar_code+"__"+num_row+"__1";
		else
			var bar_c=bar_code+"__"+num_row+"__0";


		var update_id=$("#update_id").val();
		if( is_update==1 )
			bar_c=bar_c+"__1"+"__"+num_row_len;
		else
			bar_c=bar_c+"__0"+"__"+num_row_len;*/

		/*if( is_update==1 )
			get_php_form_data( update_id+"__"+bar_c, "populate_data_update_barcode", "requires/finish_feb_delivery_roll_entry_controller" );
		else
			get_php_form_data( bar_c, "populate_data_from_barcode", "requires/finish_feb_delivery_roll_entry_controller" );
			total_crnDelvQty_rejQty_prodQty();*/

		//cbo_company_id cbo_knitting_source knit_company_id

		var barcode_data=trim(return_global_ajax_value(barcode_no, 'populate_data_from_barcode', '', 'requires/finish_feb_delivery_roll_entry_controller'));

		var barcode_res=trim(barcode_data).split('!!');

		if(barcode_res[0]==999)
		{
			alert(barcode_res[1]);
			$('#txt_bar_code_num').val('');
			return;
		}


		var barcode_datas=barcode_data.split("__");

		var id_str= $("#scanning_tbl tbody tr:first").attr('id').split("_");
		var row_num = id_str[1];
		//return;

		for(var k=0; k<barcode_datas.length; k++)
		{
			var data=barcode_datas[k].split("**");
			var company_id=data[0];
			var knitting_source=data[1];
			var knitting_company=data[2];
			var txt_knit_company=data[3];
			var knitting_location_id=data[4];
			var knit_location_name=data[5];
			var barcode_no=data[6];
			var roll_no=data[7];
			var production_qty=data[8];
			var reject_qnty=data[9];
			var roll_qty=data[10];
			var isSales=data[11];
			var booking_without_order=data[12];
			var booking_no=data[13];
			var batch=data[14];
			var body_part_name=data[15];
			var body_part_id=data[16];
			var constructtion=data[17];
			var composition=data[18];
			var fabric_description_id=data[19];
			var color_name=data[20];
			var color_id=data[21];
			var gsm=data[22];
			var width=data[23];
			var dia_width_type=data[24];
			var dia_width_type_name=data[25];
			var batch_id=data[26];
			var knitSource=data[27];
			var receive_date=data[28];
			var year=data[29];
			var job_no=data[30];
			var buyer_name=data[31];
			var order=data[32];
			var prod_id=data[33];
			var recv_number=data[34];
			var id=data[35];
			var dtls_id=data[36];
			var po_breakdown_id=data[37];
			var roll_id=data[38];
			var barcode_process=data[39];
			var greyQty=data[40];
			var grey_qnty_pcs=data[41];
			var coller_cuff_size=data[42];
			

			if( jQuery.inArray( barcode_no, scanned_barcode )>-1)
			{
				alert('Sorry! Barcode Already Scanned.');
				$('#txt_bar_code_num').val('');
				return;
			}

			if($('#cbo_company_id').val() !=0)
			{
				if(company_id != $('#cbo_company_id').val()){
					alert('Multiple company not allowed');
					return
				}
			}

			if($('#cbo_knitting_source').val() !=0)
			{
				if(knitting_source != $('#cbo_knitting_source').val()){
					alert('Multiple production Source not allowed');
					return
				}
			}

			if($('#knit_company_id').val() !=0)
			{
				if(knitting_company != $('#knit_company_id').val()){
					alert('Multiple Dye/Finishing company not allowed');
					return
				}
			}


			if($('#cbo_company_id').val() ==0)
			{
				get_php_form_data( company_id, "load_print_buttons", "requires/finish_feb_delivery_roll_entry_controller" );
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
			}


			$("#cbo_company_id").val(company_id);
			$('#cbo_knitting_source').val(knitting_source);
			$('#txt_knit_company').val(txt_knit_company);
			$('#knit_company_id').val(knitting_company);

			$('#knit_location_id').val(knitting_location_id);

			if (knitting_source == 1) {
				$('#txt_knitting_location').val(knit_location_name);
			} else if (knitting_source == 3) {
				$('#txt_knitting_location').val("");
			}


			/*$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$row[csf("company_id")]."'  and module_id=7 and report_id=167 and is_deleted=0 and status_active=1");
			$print_report_format_arr=explode(",",$print_report_format);
			echo "$('#print1').hide();\n";
			echo "$('#print2').hide();\n";
			echo "$('#print_barcode').hide();\n";
			echo "$('#btn_fabric_details').hide();\n";*/
			/*if($print_report_format != "")
			{
				foreach($print_report_format_arr as $id)
				{
					if($id==86){echo "$('#print1').show();\n";}
					if($id==84){echo "$('#print2').show();\n";}
					if($id==68){echo "$('#print_barcode').show();\n";}
					if($id==69){echo "$('#btn_fabric_details').show();\n";}
				}
			}
			else
			{
				echo "$('#print1').show();\n";
				echo "$('#print2').show();\n";
				echo "$('#print_barcode').show();\n";
				echo "$('#btn_fabric_details').show();\n";
			}*/


			$("#sl_" + row_num).text( row_num );
			$("#barcode_" + row_num).text( barcode_no);
			$("#roll_" + row_num).text(roll_no);
			$("#greyQty_" + row_num).text(greyQty);
			$("#greyQnty_" + row_num).val(greyQty);
			$("#prodQty_" + row_num).text(production_qty);
			$("#prodQnty_" + row_num).val(production_qty);
			$("#rejectQty_" + row_num).text(reject_qnty);
			$("#rejectQnty_" + row_num).val(reject_qnty);
			$("#currentDelivery_" + row_num).val(roll_qty);

			
			$("#rollQty_" + row_num).val(roll_qty);
			$("#IsSalesId_" + row_num).val(isSales);
			$("#bookingWithoutOrder_" + row_num).val(booking_without_order);
			$("#bookingNumber_" + row_num).val(booking_no);
			$("#batch_" + row_num).text(batch);
			$("#bodypart_" + row_num).text(body_part_name);
			$("#cons_" + row_num).text(constructtion);
			$("#comps_" + row_num).text(composition);
			$("#color_" + row_num).text(color_name);

			$("#gsm_" + row_num).text(gsm);
			$("#dia_" + row_num).text(width);
			$("#widthTipe_" + row_num).text(dia_width_type_name);
			$("#widthTypeId_" + row_num).val(dia_width_type);
			$("#batchId_" + row_num).val(batch_id);
			$("#knitSource_" + row_num).text(knitSource);

			$("#finishCompany_" + row_num).text(txt_knit_company);
			$("#prodDate_" + row_num).text(receive_date);
			$("#year_" + row_num).text(year);

			$("#job_" + row_num ).text(job_no);
			$("#buyer_" + row_num).text(buyer_name);
			$("#order_" + row_num).text(order);
			$("#prodId_" + row_num).text(prod_id);
			$("#systemId_" + row_num).text(recv_number);
			$("#barcodeNo_" + row_num).val(barcode_no);
			$("#productionId_" + row_num).val(id);
			$("#productionDtlsId_" + row_num).val(dtls_id);
			$("#deterId_" + row_num).val(fabric_description_id);
			$("#productId_" + row_num).val(prod_id);
			$("#orderId_" + row_num).val(po_breakdown_id);
			$("#rollId_" + row_num).val(roll_id);
			$("#reProcess_" + row_num).val(barcode_process);
			$("#prereProcess_" + row_num).val(barcode_process);
			$("#dtlsId_" + row_num).val("");
			$("#colorId_" + row_num).val(color_id);
			$("#bodyPartId_" + row_num).val(body_part_id);
			$("#finMstId_" + row_num).val(id);

			$("#greyQntyPcs_"+row_num).text(grey_qnty_pcs);
			$("#collerCuffSize_"+row_num).text(coller_cuff_size);
			$("#hddGreyQntyPcs_"+row_num).val(grey_qnty_pcs);
			$("#hddCollerCuffSize_"+row_num).val(coller_cuff_size);

			$("#decrease_" + row_num).removeAttr('onclick').attr('onclick','fn_deleteRow('+row_num+')');
			$("#currentDelivery_" + row_num).removeAttr('onKeyUp').attr('onKeyUp','check_qty('+row_num+')');

			scanned_barcode.push(bar_code);

			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			$('#txt_tot_row').val(row_num);
		}
	}


	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			if(bar_code != ""){
				create_row(0,bar_code);
			}
		}
	});

	function add_dtls_data( data )
	{
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var qty=datas[2];

			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_scanned_qnty_array[barcode_no] = qty;
			//barcode_dtlsId_array.push(bar_code);
		}

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();

			if(dtlsId=="")
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
			}
		});
	}

	function fn_deleteRow( rid )
	{
		var bar_code =$("#barcodeNo_"+rid).val();
		var reProcess =$("#reProcess_"+rid).val();
		//alert(bar_code)
		/*if( jQuery.inArray( bar_code, receive_barcode )>-1)
			{
				alert('Sorry!  Roll Already Received By Store.');
				return;
			}*/
		var selected_id=''; //var selected_barcode ='';
		var num_row =$('#scanning_tbl tbody tr').length;
		//var bar_code =$("#barcodeNo_"+rid).val();
		var dtlsId =$("#dtlsId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var txt_deleted_barcode=$('#txt_deleted_barcode').val();
		var txt_del_barcode_reprocess=$('#txt_del_barcode_reprocess').val();
		selected_id=txt_deleted_id;
		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:nth-last-child(2)):not(:last-child)').each(function(index, element) {
				$(this).html('');
			});

			$('#tr_'+rid).find(":input:not(:button)").val('');
			$('#cbo_company_id').val(0);
			$('#cbo_knitting_source').val(0);
			$('#txt_knit_company').val('');
			$('#knit_company_id').val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}


		if(dtlsId!='')
		{
			if(selected_id=='') selected_id=dtlsId; else selected_id=txt_deleted_id+','+dtlsId;
			$('#txt_deleted_id').val( selected_id );
		}


		if(bar_code != "")
		{
			if(txt_deleted_barcode=='') txt_deleted_barcode=bar_code; else txt_deleted_barcode=txt_deleted_barcode+','+bar_code;
			$('#txt_deleted_barcode').val( txt_deleted_barcode );


			if(txt_del_barcode_reprocess=='') txt_del_barcode_reprocess=bar_code+"="+reProcess; else txt_del_barcode_reprocess=txt_del_barcode_reprocess+','+bar_code+"="+reProcess;
			$('#txt_del_barcode_reprocess').val( txt_del_barcode_reprocess );
		}


		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);

		var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;var rollGreyQntyTotal = 0;//prodQty_2 / rejectQty_2
		/*var tot_num_row =$('#scanning_tbl tbody tr').length+1;
		for(var k=1; k<tot_num_row; k++)
		{
			rollProductionQntyTotal+=$("#prodQty_"+k).html()*1
			rollRejectQntyTotal+=$("#rejectQty_"+k).html()*1
		}
		$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
		$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));*/

        $("#scanning_tbl").find('tbody tr').each(function(){
           rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
           rollProductionQntyTotal+=$(this).find('input[name="prodQnty[]"]').val()*1;
           rollRejectQntyTotal+=$(this).find('input[name="rejectQnty[]"]').val()*1;
           rollGreyQntyTotal+=$(this).find('input[name="greyQnty[]"]').val()*1;
        });
        if(rollQntyTotal > 0){
        $("#rollQntyTotal").html(number_format(rollQntyTotal,2));
        }else{
            $("#rollQntyTotal").html("");
        }
         $("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
         $("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
         $("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));
	}

	function check_qty( rid )
	{
        var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;var rollGreyQntyTotal = 0;//prodQty_2 / rejectQty_2
		var production_qty=$("#prodQty_"+rid).text()*1;
		var roll_delv_qty=$("#currentDelivery_"+rid).val()*1;
		if(roll_delv_qty>production_qty)
		{
                alert("Delivery Quantity Exceeds Production Quantity.");
                $("#currentDelivery_"+rid).val(production_qty.toFixed(2));

				var tot_num_row =$('#scanning_tbl tbody tr').length+1;
				for(var k=1; k<tot_num_row; k++)
				{
					rollProductionQntyTotal+=$("#prodQty_"+k).html()*1
					rollRejectQntyTotal+=$("#rejectQty_"+k).html()*1
					rollGreyQntyTotal+=$("#greyQty_"+k).html()*1
				}
				$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
				$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
				$("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));


                    $("#scanning_tbl").find('tbody tr').each(function(){
                    rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
                });
                $("#rollQntyTotal").html(number_format(rollQntyTotal,2));
			return;
		}
				var tot_num_row =$('#scanning_tbl tbody tr').length+1;
				for(var k=1; k<tot_num_row; k++)
				{
					rollProductionQntyTotal+=$("#prodQty_"+k).html()*1
					rollRejectQntyTotal+=$("#rejectQty_"+k).html()*1
					rollGreyQntyTotal+=$("#greyQty_"+k).html()*1
				}
				$("#rollProductionTotal").html(number_format(rollProductionQntyTotal,2));
				$("#rollRejectQntyTotal").html(number_format(rollRejectQntyTotal,2));
				$("#rollGreyTotal").html(number_format(rollGreyQntyTotal,2));


                $("#scanning_tbl").find('tbody tr').each(function(){
                   rollQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
                });
                $("#rollQntyTotal").html(number_format(rollQntyTotal,2));
	}

	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="40" id="roll_1"></td><td width="70" id="greyQty_1" align="right"></td><td width="70" id="prodQty_1" align="right"></td><td id="rejectQty_1" width="70" align="center"></td><td id="delevQt_1" width="70" align="center"><input type="text" name="currentDelivery[]" id="currentDelivery_1" style="width:55px" class="text_boxes_numeric" onKeyUp="check_qty(1)"/></td><td width="60" id="greyQntyPcs_1"></td><td width="60" id="collerCuffSize_1"></td><td width="80" id="batch_1"></td><td width="100" id="bodypart_1"></td><td width="80" id="cons_1" style="word-break:break-all;" align="left"></td><td width="100" id="comps_1" style="word-break:break-all;" align="left"></td><td width="70" id="color_1"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1" style="word-break:break-all;"></td><td width="60" id="widthTipe_1"></td><td width="75" id="knitSource_1"></td><td width="85" id="finishCompany_1"></td><td width="70" id="prodDate_1"></td><td width="40" id="year_1" align="center"></td><td width="100" id="job_1"></td><td width="55" id="buyer_1"></td><td width="80" id="order_1" style="word-break:break-all;" align="left"></td> <td width="50" id="prodId_1"></td><td id="systemId_1" style="word-break:break-all;"></td><td width="40" id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="productionId[]" id="productionId_1"/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/> <input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/> <input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/><input type="hidden" name="finMstId[]" id="finMstId_1"/><input type="hidden" name="greyQnty[]" id="greyQnty_1"  class="text_boxes_numeric"/><input type="hidden" name="rollQty[]" id="rollQnty_1"/><input type="hidden" name="prodQnty[]" id="prodQnty_1"  class="text_boxes_numeric"/><input type="hidden" name="rejectQnty[]" id="rejectQnty_1"  class="text_boxes_numeric"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="prereProcess[]" id="prereProcess_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/><input type="hidden" name="bookingNumber[]" id="bookingNumber_1"/><input type="hidden" name="hddGreyQntyPcs[]" id="hddGreyQntyPcs_1"/><input type="hidden" name="hddCollerCuffSize[]" id="hddCollerCuffSize_1"/></td></tr>';

		$('#cbo_company_id').val(0);
		$('#cbo_knitting_source').val(0);
		$('#txt_knit_company').val('');
		$('#knit_company_id').val('');
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_challan_no').val('');
		$('#txt_delivery_date').val('');
		$('#txt_deleted_id').val('');
		$("#scanning_tbl tbody").html(html);
        $("#rollQntyTotal").html("");
        $("#rollProductionTotal").html("");
        $("#rollRejectQntyTotal").html("");
        $("#rollGreyTotal").html("");
		/*$("#scanning_tbl tbody tr:last").removeAttr('id').attr('id','tr_'+1);*/
	}
	function set_focus()
	{
		$('#txt_bar_code_num').focus();
	}

	function print_button(type)
	{
		var report_title=$( "div.form_caption" ).html();
		if (type==1) 
		{
			var vbarcode='';
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if( vbarcode=='') vbarcode=$(this).find('input[name="barcodeNo[]"]').val();
				else vbarcode=vbarcode+","+$(this).find('input[name="barcodeNo[]"]').val();

			});
			//alert(vbarcode);
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+vbarcode,'grey_delivery_print');
			return;
		}
		else if(type==2)
		{
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_knitting_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#knit_company_id").val(), 'roll_delivery_no_of_copy_print');
	        return;
		}
		else if (type==3) 
		{
			var vbarcode='';
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if( vbarcode=='') vbarcode=$(this).find('input[name="barcodeNo[]"]').val();
				else vbarcode=vbarcode+","+$(this).find('input[name="barcodeNo[]"]').val();

			});
			//alert(vbarcode);
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+vbarcode,'fso_v2_delivery_print');
			return;
		} 
		else if(type=4)
		{
			var vbarcode='';
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if( vbarcode=='') vbarcode=$(this).find('input[name="barcodeNo[]"]').val();
				else vbarcode=vbarcode+","+$(this).find('input[name="barcodeNo[]"]').val();

			});
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title+'*'+vbarcode + '*' + $('#cbo_knitting_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#knit_company_id").val(), 'roll_delivery_print_4');
	        return;
		}
		// alart(type);
	}

	function barcode_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val(),'issue_challan_print');
	}

	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
</script>
</head>
<body onLoad="set_hotkey();set_focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>

                        <td align="center"  colspan="6">Challan No
                        	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_challan()" placeholder="Browse For Challan No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                     <tr>
                        <td align="center" colspan="6">

                        </td>
                    </tr>
                    <tr>
                        <td align="right" ><strong>Roll Number</strong></td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right" class="must_entry_caption" width="140">Delivery Date</td>
                        <td width=""><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" readonly value="<? echo date("d-m-Y"); ?>" /></td>
                        <td align="right">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)","id,company_name", 1, "--Display--", 0, "",1 );//$company_cond
                            ?>
                        </td>

                    </tr>
                    <tr>

                        <td align="right">Prod. Source </td>
                        <td>
							<?
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1);
							?>

                        </td>
                        <td align="right">Dye/Finishing Company</td>
                        <td id="knitting_com">
                            <input type="text" name="txt_knit_company" id="txt_knit_company" class="text_boxes" style="width:140px;" placeholder="Display" disabled/>
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>
                        <td align="right">Dye/Finishing Location</td>
                        <td>
							<input type="text" name="txt_knitting_location" id="txt_knitting_location" class="text_boxes" style="width:140px;" placeholder="Display" disabled/>
                            <input type="hidden" name="knit_location_id" id="knit_location_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Attention</td>
                        <td>
                        	<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:140px;" placeholder="Write" />
                        </td>
                        <td align="right">Remarks</td>
                        <td>
                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;" placeholder="Write" />
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>

                </table>
			</fieldset>
			<br>
			<fieldset style="width:1390px;text-align:left">
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
				<table cellpadding="0" width="1820" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="40">Roll No</th>
                        <th width="70">Grey Qty.</th>
                        <th width="70">Last Process Prod. Qty.</th>
                        <th width="70">Reject Qty</th>
                        <th width="70">QC Roll qty.</th>
                        <th width="60">Qty in Pcs</th>
                        <th width="60">Item Size</th>
                        <th width="80">Batch No</th>
                        <th width="100">Body Part</th>
                        <th width="80">Construction</th>
                        <th width="100">Composition</th>
                        <th width="70">Color</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="60">Dia/Width Type</th>
                        <th width="75">Prod. Source</th>
                        <th width="85">Dye/Finishing Company</th>
                        <th width="70">Production date</th>
                        <th width="40">Year</th>
                        <th width="100">Job No</th>
                        <th width="55">Buyer</th>
                        <th width="80">Order/FSO No</th>
                        <th width="50">Product Id</th>
                        <th>System Id</th>
                        <th width="40"></th>
                    </thead>
                 </table>
                 <div style="width:1840px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1820" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="30" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="40" id="roll_1"></td>
                                <td width="70" id="greyQty_1" align="right"></td>
                                <td width="70" id="prodQty_1" align="right"></td>
                                <td id="rejectQty_1" width="70" align="center"></td>
                                <td id="delevQt_1" width="70" align="center"><input type="text" name="currentDelivery[]" id="currentDelivery_1" style="width:55px" class="text_boxes_numeric" onKeyUp="check_qty(1)" readonly/></td>

                                <td width="60" id="greyQntyPcs_1"></td>
                                <td width="60" id="collerCuffSize_1"></td>
                                <td width="80" id="batch_1" style="word-break:break-all;"></td>
                                <td width="100" id="bodypart_1"></td>
                                <td width="80" id="cons_1" style="word-break:break-all;" align="left"></td>
                                <td width="100" id="comps_1" style="word-break:break-all;" align="left"></td>
                                <td width="70" id="color_1" style="word-break:break-all;"></td>
                                <td width="40" id="gsm_1"></td>
                                <td width="40" id="dia_1" style="word-break:break-all;"></td>
                                <td width="60" id="widthTipe_1" style="word-break:break-all;"></td>
                                <td width="75" id="knitSource_1" style="word-break:break-all;"></td>
                                <td width="85" id="finishCompany_1" style="word-break:break-all;"></td>
                                <td width="70" id="prodDate_1" style="word-break:break-all;"></td>
                                <td width="40" id="year_1" align="center" style="word-break:break-all;"></td>
                                <td width="100" id="job_1" style="word-break:break-all;"></td>
                                <td width="55" id="buyer_1" style="word-break:break-all;"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="50" id="prodId_1" style="word-break:break-all;"></td>
                                <td style="word-break:break-all;" id="systemId_1"></td>
                                <td width="40" id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="productionId[]" id="productionId_1"/>
                                    <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="finMstId[]" id="finMstId_1"/>
									<input type="hidden" name="greyQnty[]" id="greyQnty_1"/>
                                    <input type="hidden" name="rollQty[]" id="rollQty_1"/>
                                    <input type="hidden" name="prodQnty[]" id="prodQnty_1"/>
                                    <input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/>
                                    <input type="hidden" name="reProcess[]" id="reProcess_1"/>
                                    <input type="hidden" name="prereProcess[]" id="prereProcess_1"/>
                                    <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                                    <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                                    <input type="hidden" name="bookingNumber[]" id="bookingNumber_1"/>
                                    <input type="hidden" name="hddGreyQntyPcs[]" id="hddGreyQntyPcs_1"/>
                                	<input type="hidden" name="hddCollerCuffSize[]" id="hddCollerCuffSize_1"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th id="rollGreyTotal"></th>
                                <th id="rollProductionTotal"></th>
                                <th id="rollRejectQntyTotal"></th>
                                <th id="rollQntyTotal"></th>
                                <th colspan="20"></th>
                            </tr>
                        </tfoot>
                	</table>
                </div>
                <table width="1700" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_barcode" id="txt_deleted_barcode" class="text_boxes" value="">
                            <input type="hidden" name="txt_del_barcode_reprocess" id="txt_del_barcode_reprocess" class="text_boxes" value="">
                            <?
                            	echo load_submit_buttons($permission,"fnc_grey_delivery_roll_wise",0,0,"fnc_reset_form()",1);
                            ?>
                            <input type="button"  class="formbutton_disabled" name="print1" id="print1" style="width:100px;" value="Print" onClick="print_button(1)"/>
                            <input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;"/>
                			<input type="button"  class="formbutton_disabled" name="print2" id="print2" style="width:100px;" value="Print 2" onClick="print_button(2)"/>

                            <input type="button" name="print_barcode" id="print_barcode" class="formbutton_disabled" value="Print Barcode" style=" width:100px" onClick="barcode_print();" >
                            <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
							<input type="button" name="printFso_v2" id="printFso_v2" class="formbutton_disabled" value="FSO(V2)" style=" width:100px" onClick="print_button(3);" >


							<input type="button"  class="formbutton_disabled" name="print3" id="print3" style="width:100px;" value="Print 3" onClick="print_button(4)"/>
                        </td>
                    </tr>
                </table>
			</fieldset>
        </form>
    </div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
