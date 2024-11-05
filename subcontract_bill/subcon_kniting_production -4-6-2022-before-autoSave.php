<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create subcontract kniting production
Functionality	:	
JS Functions	:
Created by		:	sohel
Creation date 	: 	08-05-2013
Updated by 		: 	Zaman	
Update date		: 	15.02.2020
Oracle Convert 	:	Kausar		
Convert date	: 	22-05-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Grey Production Entry", "../",1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	var str_brand = [<? echo substr(return_library_autocomplete( "select brand_name from lib_brand group by brand_name", "brand_name" ), 0, -1); ?>];

	$(document).ready(function(e){
		//for color
		$("#txt_color").autocomplete({
			source: str_color
		});
		
		//for brand
		$("#txt_brand").autocomplete({
			source: str_brand
		});
     });

	function openmypage_production()
	{
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value;
		emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/subcon_kniting_production_controller.php?action=production_id_popup&data='+data,'Kniting Production Popup', 'width=900px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("product_id");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/subcon_kniting_production_controller" );
				//show_list_view(theemail.value,'kniting_production_list_view','kniting_production_list_view','requires/subcon_kniting_production_controller','setFilterGrid("list_view",-1)');
				show_list_view(theemail.value+'_'+$('#txt_program_no').val(),'kniting_production_list_view','kniting_production_list_view','requires/subcon_kniting_production_controller','setFilterGrid("list_view",-1)');
				
				document.getElementById('cbo_production_basis').disabled=true;
				document.getElementById('cbo_company_id').disabled=true;
				document.getElementById('cbo_party_name').disabled=true;
				
				//$('#cbo_body_part').focus();
				reset_form('','','','','','txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*txt_remarks*update_id*cbo_production_basis*txt_program_no');
				release_freezing();
			}
		}
	}

	function subcon_kniting_production(operation)
	{ 
		if ( form_validation('cbo_company_id*cbo_party_name*txt_production_date*cbo_process*txt_febric_description*txt_gsm*txt_width*txt_order_no*txt_product_qnty*cbo_yarn_count*txt_yarn_lot*cbo_knitting_source*cbo_knitting_company','Company Name*Party Name*production date*process*febric description*GSM*Dia Width*Order No*Product Qnty*Yarn Count*Yarn Lot*Knitting Source*Knitting Company')==false )
		{
			return;
		}
		else
		{
			func_production_qty_check();
			if($('#hdnProductQtyError').val() == 1)
			{
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*cbo_floor_id*txt_remarks*update_id*cbo_process*txt_yarn_lot*txt_febric_description*hidd_comp_id*txt_gsm*cbo_yarn_count*txt_width*cbo_dia_width_type*txt_brand*txt_roll_qnty*cbo_shift_id*txt_order_no*order_no_id*cbo_machine_name*txt_product_qnty*txt_job_no*txt_reject_qnty*text_new_remarks*cbo_uom*txt_stitch_len*cbo_color_range*txt_color*txt_machine_dia*txt_machine_gg*update_id_dtl*txt_deleted_id*save_data*txt_roll_maintained*cbo_knitting_source*cbo_knitting_company*cbo_knit_location_name*cbo_production_basis*txt_program_no*txt_operator_id',"../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/subcon_kniting_production_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = subcon_kniting_production_reponse;
		}
	}

	function subcon_kniting_production_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			
			if(response[0]==17)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];
				show_list_view(response[1]+'_'+$('#cbo_production_basis').val(),'kniting_production_list_view','kniting_production_list_view','requires/subcon_kniting_production_controller','setFilterGrid("list_view",-1)');
				show_msg(response[0]);

				$('#cbo_body_part').focus();
				reset_form('knitingproduction_1','','','','','txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*txt_roll_maintained*barcode_generation*txt_remarks*update_id*txt_order_no*hidd_comp_id*cbo_color_range*txt_stitch_len*cbo_process*txt_yarn_lot*txt_febric_description*show_textcbo_yarn_count*txt_gsm*txt_brand*txt_width*cbo_dia_width_type*txt_color*txt_machine_gg*cbo_floor_id*txt_machine_dia*cbo_uom*txt_job_no*cbo_knitting_source*cbo_knitting_company*cbo_knit_location_name*order_no_id*cbo_yarn_count*cbo_production_basis*txt_program_no');

				var cbo_yarn_count=$("#cbo_yarn_count").val();
				set_multiselect('cbo_yarn_count','0','1',cbo_yarn_count,'0');
				$('#list_fabric_desc_container').html('');
				$('#roll_details_list_view').html('')
			}
			if(response[0]==15) 
			 { 
			 	setTimeout('subcon_kniting_production('+ response[1] +')',8000); 
			 	if(response[2]!="")
			 	{
			 		alert(response[2]);
			 	}
			 	return;
			 }	
			 
			set_button_status(0, permission, 'subcon_kniting_production',1);
			release_freezing();
		}
	}

	function openmypage_order_no()
	{
		if( form_validation('cbo_company_id*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_process').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/subcon_kniting_production_controller.php?action=order_no_popup&data='+data,'Order Selection Form', 'width=800px,height=420px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theemail=this.contentDoc.getElementById("selected_job");
				reset_form('knitingproduction_1','','','','','txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*txt_roll_maintained*barcode_generation*txt_remarks*cbo_knitting_source*cbo_knitting_company*cbo_knit_location_name');
				get_php_form_data( theemail.value, "load_php_data_to_form_dtls_order", "requires/subcon_kniting_production_controller" );
				show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value+"_"+document.getElementById('cbo_production_basis').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');
				release_freezing();		
			}
		}
	}

	function openmypage_remarks() 
	{
		var title = 'Remarks';
		var txt_remarks=$('#text_new_remarks').val();
		var page_link = 'requires/subcon_kniting_production_controller.php?txt_remarks='+txt_remarks+'&action=remarks_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=250px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var save_string=this.contentDoc.getElementById("hidden_txt_remarks").value;
			$('#text_new_remarks').val(save_string);
		}
	}
	
	function openmypage_po()
	{
		//alert('su..re');
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained = $('#txt_roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
	
		var save_data = $('#save_data').val();
		var all_po_id = $('#order_no_id').val();
		var txt_order_no = $('#txt_order_no').val();
		var txt_product_qnty = $('#txt_product_qnty').val(); 
		var txt_reject_qnty = $('#txt_reject_qnty').val(); 
		//var distribution_method = $('#distribution_method_id').val();
		//var booking_without_order = $('#booking_without_order').val();
		
		//var cbo_body_part=$('#cbo_body_part').val();
		var txt_fabric_description=$('#txt_fabric_description').val();
		var txt_gsm=$('#txt_gsm').val();
		var txt_width=$('#txt_width').val();
		var cbo_dia_width_type=$('#cbo_dia_width_type').val();
		var fabric_desc_id=$('#hidd_comp_id').val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		if (form_validation('cbo_company_id*txt_order_no','Company Name*Order No')==false)
		{
			$('#cbo_company_id').focus();
			$('#txt_order_no').focus();
			return;
		}
		
		if(roll_maintained==1) 
		{
			popup_width='1000px';
		}
		else
		{
			popup_width='800px';
		}
		var hdnOrderQty=$('#txtOrderQty').val()*1;
		var hdnTotalProductQty=$('#txtTotalProductQty').val()*1;
		var hdnBalanceQty=$('#txtBalanceQty').val()*1;
		
		//162=1=150=50=
		//alert($('#hidd_comp_id').val()+'='+$('#cbo_dia_width_type').val()+'='+$('#txt_gsm').val()+'='+$('#txt_width').val()+'='+$('#hdnColorId').val());
		var rowNo = $('#hidd_comp_id').val()+$('#cbo_dia_width_type').val()+$('#txt_gsm').val()+$('#txt_width').val()+$('#hdnColorId').val();
		var hdnColorId = $('#hdnColorId').val();
		var txtProductQty = $('#txt_product_qnty').val()*1;
		var hdnProductQty = $('#hdnProductQty').val()*1;
		//var hdnOrderQty = $('#hddnOrderQty_'+rowNo).val()*1;
		//var hdnTotalProductQty = $('#hddnTotalProductQty_'+rowNo).val()*1;
		//var hdnBalanceQty = $('#hddnBalanceQty_'+rowNo).val()*1;
		//alert(txtProductQty+'='+hdnProductQty+'='+hdnOrderQty+'='+hdnTotalProductQty+'='+hdnBalanceQty);
		//0=0=100=100=0=
		//16221

		var title = 'PO Info';	
		var page_link = 'requires/subcon_kniting_production_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&barcode_generation='+barcode_generation+'&save_data='+save_data+'&txt_product_qnty='+txt_product_qnty+'&cbo_process='+cbo_process+'&txt_gsm='+txt_gsm+'&txt_width='+txt_width+'&fabric_desc_id='+fabric_desc_id+'&txt_deleted_id='+txt_deleted_id+'&txt_reject_qnty='+txt_reject_qnty+'&txt_order_no='+txt_order_no+'&hdnProductQty='+hdnProductQty+'&hdnOrderQty='+hdnOrderQty+'&hdnTotalProductQty='+hdnTotalProductQty+'&hdnBalanceQty='+hdnBalanceQty+'&hdnColorId='+hdnColorId+'&action=po_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=430px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_grey_qnty=this.contentDoc.getElementById("tot_qc_qnty").value; //Access form field with id="emailfield"
			var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; 
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value;
			var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			//var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
			//var all_po_id=this.contentDoc.getElementById("po_id").value; //Access form field with id="emailfield"
			//var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			//var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			//alert(save_string);
			
			//alert(save_string+'='+tot_grey_qnty+'='+number_of_roll+'='+tot_reject_qnty+'='+hide_deleted_id);

			
			$('#save_data').val(save_string);
			$('#txt_product_qnty').val(tot_grey_qnty);
			
			$('#txt_reject_qnty').val(tot_reject_qnty);
			if(roll_maintained==1)
			{
				$('#txt_roll_qnty').val(number_of_roll);
				$('#txt_deleted_id').val(hide_deleted_id);
			}
			else
			{
				$('#txt_deleted_id').val('');
			}
			
			//$('#all_po_id').val(all_po_id);
			//$('#distribution_method_id').val(distribution_method);
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#hidd_comp_id').val(data[0]);
		$('#txt_febric_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_color').val(data[3]);
		$('#cbo_dia_width_type').val(data[4]);
		$('#txt_width').val(data[5]);
		$('#hdnColorId').val(data[6]);
		$('#hdnProductQty').val(data[7]*1);
		$('#txt_product_qnty').val('');
		
		$('#txtOrderQty').val(data[8]*1);
		$('#txtTotalProductQty').val(data[7]*1);
		$('#txtBalanceQty').val(data[9]*1);
	}
	
	function put_data_dtls_part(id,type,page_path)
	{
		//get_php_form_data(id+"**"+$('#roll_maintained').val(), type, page_path );
		var roll_maintained=$('#txt_roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		//get_php_form_data(id+"**"+roll_maintained, type, page_path );
		if(roll_maintained==1)
		{
			show_list_view("'"+id+"**"+barcode_generation+"'",'show_roll_listview','roll_details_list_view','requires/subcon_kniting_production_controller','');
		}
		else
		{
			$('#roll_details_list_view').html('');
		}
	}
	
	function check_all_report()
	{
		$("input[name=chkBundle]").each(function(index, element) { 

			if( $('#check_all').prop('checked')==true) 
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}

	function load_location()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',cbo_knitting_company, 'load_drop_down_knit_location', 'knit_location_td');
		}
		else
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',0, 'load_drop_down_knit_location', 'knit_location_td' );
		}
	}

	function load_floor()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_knit_location_name = $('#cbo_knit_location_name').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',cbo_knitting_company+'_'+cbo_knit_location_name, 'load_drop_down_floor', 'floor_td');
		}
		else
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',0+'_'+0, 'load_drop_down_floor', 'floor_td' );
		}
	}

	function load_machine()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',cbo_knitting_company+'_'+cbo_floor_id, 'load_drop_down_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/subcon_kniting_production_controller',0+'_'+0, 'load_drop_down_machine', 'machine_td' );
		}
	}
	
	function fnc_send_printer_text()
	{
		var dtls_id=$('#update_id_dtl').val();
		var mst_id=$('#update_id').val();
		if(dtls_id=="")
		{
			alert("Save First");	
			return;
		}
		var roll_id_data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(roll_id_data=="") roll_id_data=$('#txtRollTableId_'+idd[1] ).val(); else roll_id_data=roll_id_data+","+$('#txtRollTableId_'+idd[1] ).val();
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

		var knitting_source = $('#cbo_knitting_source').val();
		data=roll_id_data+"**"+dtls_id+"**"+mst_id+"**"+knitting_source;
		//alert(data);
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/subcon_kniting_production_controller");
		window.open("requires/"+trim(url)+".zip","##");
	}
	
	// function fnc_barcode_code128()
	// {
	// 	var dtls_id=$('#update_id_dtl').val();
	// 	var mst_id=$('#update_id').val();
	// 	if(dtls_id=="")
	// 	{
	// 		alert("Save First");	
	// 		return;
	// 	}
	// 	var roll_id_data="";
	// 	var error=1;
	// 	$("input[name=chkBundle]").each(function(index, element) {
	// 		if( $(this).prop('checked')==true)
	// 		{
	// 			error=0;
	// 			var idd=$(this).attr('id').split("_");
	// 			var roll_id=$('#txtRollTableId_'+idd[1] ).val();
	// 			if(roll_id!="")
	// 			{
	// 				if(roll_id_data=="") roll_id_data=$('#txtRollTableId_'+idd[1] ).val(); else roll_id_data=roll_id_data+","+$('#txtRollTableId_'+idd[1] ).val();
	// 			}
	// 			else
	// 			{
	// 				$(this).prop('checked',false);
	// 			}
	// 		}
	// 	});

	// 	if( error==1 )
	// 	{
	// 		alert('No data selected');
	// 		return;
	// 	}

	// 	var knitting_source = $('#cbo_knitting_source').val();
	// 	data=roll_id_data+"**"+dtls_id+"**"+mst_id+"**"+knitting_source;
	// 	//alert(data);
	// 	var url=return_ajax_request_value(data, "print_barcode_one_128_v2", "requires/subcon_kniting_production_controller");	
	// 	window.open(url,"##");	
	// }



	





	function fnc_barcode_generation()
	{
		var dtls_id=$('#update_id_dtl').val();
		
		var mst_id=$('#update_id').val();
		if(dtls_id=="")
		{
			alert("Save First");	
			return;
		}
		//alert(mst_id);
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
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
		
		data=data+"***"+dtls_id;
		
		window.open("requires/subcon_kniting_production_controller.php?data=" + data+'&action=report_barcode_generation', true );
	}
	
	function func_browse_plan()
	{
		//alert('su..re');
		//if( form_validation('cbo_company_id*cbo_party_name','Company Name*Party Name')==false )
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_process').value+"_"+document.getElementById('cbo_production_basis').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/subcon_kniting_production_controller.php?action=actn_browse_plan&data='+data,'Order Selection Form', 'width=900px,height=420px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var info = this.contentDoc.getElementById("selected_job").value;
				var infoArr = info.split('*');
				//var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*cbo_floor_id*txt_remarks*update_id*cbo_process*txt_yarn_lot*txt_febric_description*hidd_comp_id*txt_gsm*cbo_yarn_count*txt_width*cbo_dia_width_type*txt_brand*txt_roll_qnty*cbo_shift_id*txt_order_no*order_no_id*cbo_machine_name*txt_product_qnty*txt_job_no*txt_reject_qnty*text_new_remarks*cbo_uom*txt_stitch_len*cbo_color_range*txt_color*txt_machine_dia*txt_machine_gg*update_id_dtl*txt_deleted_id*save_data*txt_roll_maintained*cbo_knitting_source*cbo_knitting_company*cbo_knit_location_name',"../");
				
				
				//					$info = $row[csf('id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('booking_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')];

				$('#txt_program_no').val(infoArr[0]);
				$('#cbo_knitting_source').val(infoArr[1]).attr('disabled', 'disabled');
				//load_drop_down( 'requires/subcon_kniting_production_controller', infoArr[1]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com_plan','knitting_com');
				load_drop_down( 'requires/subcon_kniting_production_controller', infoArr[1]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');

				$('#cbo_knitting_company').val(infoArr[2]).attr('disabled', 'disabled');
				load_drop_down( 'requires/subcon_kniting_production_controller', infoArr[2], 'load_drop_down_floor', 'floor_td' );
				
				load_location();
				load_floor();
				load_machine();
				
				$('#txt_order_no').val(infoArr[3]).attr('disabled', 'disabled');
				$('#cbo_color_range').val(infoArr[4]).attr('disabled', 'disabled');
				$('#cbo_process').val(infoArr[5]).attr('disabled', 'disabled');
				$('#txt_gsm').val(infoArr[6]).attr('disabled', 'disabled');
				$('#txt_width').val(infoArr[7]).attr('disabled', 'disabled');
				$('#cbo_dia_width_type').val(infoArr[8]).attr('disabled', 'disabled');
				$('#txt_color').val(infoArr[9]).attr('disabled', 'disabled');
				$('#txt_machine_gg').val(infoArr[11]).attr('disabled', 'disabled');
				$('#txt_stitch_len').val(infoArr[12]).attr('disabled', 'disabled');
				$('#txt_machine_dia').val(infoArr[13]).attr('disabled', 'disabled');
				$('#cbo_machine_name').val(infoArr[14]).attr('disabled', 'disabled');
				//$('#txt_product_qnty').val(infoArr[15]);
				$('#txt_job_no').val(infoArr[16]);
				
				//$('#order_no_id').val(infoArr[17]);
				//$('#order_no_id').val(infoArr[0]);
				$('#order_no_id').val(infoArr[22]);
				
				$('#cbo_knit_location_name').val(infoArr[18]).attr('disabled', 'disabled');
				$('#hidd_comp_id').val(infoArr[19]);
				$('#txt_febric_description').val(infoArr[20]);
				//main_process_id = infoArr[21];
				$('#cbo_party_name').val(infoArr[23]).attr('disabled', 'disabled');
				//show_list_view(infoArr[0]+'_'+infoArr[21]+'_'+$('#cbo_production_basis').val(),'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');
				show_list_view(infoArr[22]+'_'+infoArr[21]+'_'+$('#cbo_production_basis').val()+'_'+infoArr[0],'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');
				
				/*
				reset_form('knitingproduction_1','','','','','txt_production_id*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_prod_chal_no*txt_yarn_issue_challan_no*txt_roll_maintained*barcode_generation*txt_remarks*cbo_knitting_source*cbo_knitting_company*cbo_knit_location_name');
				get_php_form_data( theemail.value, "load_php_data_to_form_dtls_order", "requires/subcon_kniting_production_controller" );
				show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');
				show_list_view(theemail.value,'actn_plan_dtls_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');
				*/
				release_freezing();		
			}
		}
	}

	function func_onchange_productionBasis(val)
	{
		//alert('su..re');
		if(val == 1)
		{
			$('#txt_program_no').val('').attr('disabled','disabled');
			$('#txt_order_no').removeAttr('disabled');
		}
		else
		{
			$('#txt_program_no').removeAttr('disabled');
			$('#txt_order_no').val('').attr('disabled','disabled');
		}
	}
	
	function func_selected_row(rowNo)
	{
		$('#hdnSelectedRow').val(rowNo);
	}
	
	function func_production_qty_check()
	{
		//alert('su..re');
		var rowNo = $('#hdnSelectedRow').val();
		var txtProductQty = $('#txt_product_qnty').val()*1;
		var hdnProductQty = $('#hdnProductQty').val()*1;
		var hdnOrderQty = 0;
		var hdnTotalProductQty = 0;
		var hdnBalanceQty = 0;
		$('#hdnProductQtyError').val(0);
		
		var hdnOrderQty=$('#txtOrderQty').val()*1;
		var hdnTotalProductQty=$('#txtTotalProductQty').val()*1;
		var hdnBalanceQty=$('#txtBalanceQty').val()*1;
		
		if($('#update_id_dtl').val() == '')
		{
			
		
			//hdnOrderQty = $('#hdnOrderQty_'+rowNo).val()*1;
			//hdnTotalProductQty = $('#hdnTotalProductQty_'+rowNo).val()*1;
			//hdnBalanceQty = $('#hdnBalanceQty_'+rowNo).val()*1;
			//alert(txtProductQty+'='+hdnProductQty+'='+hdnOrderQty+'='+hdnTotalProductQty+'='+hdnBalanceQty+'='+$('#update_id_dtl').val());
			if(hdnOrderQty < (hdnTotalProductQty+txtProductQty))
			{
				alert('Product qty. is larger than order qty.');
				$('#txt_product_qnty').val('');
				$('#hdnProductQtyError').val(1);
				return;
			}
		}
		else
		{
			rowNo = $('#hidd_comp_id').val()+$('#cbo_dia_width_type').val()+$('#txt_gsm').val()+$('#txt_width').val()+$('#hdnColorId').val();
			//hdnOrderQty = $('#hddnOrderQty_'+rowNo).val()*1;
			//hdnTotalProductQty = $('#hddnTotalProductQty_'+rowNo).val()*1;
			//hdnBalanceQty = $('#hddnBalanceQty_'+rowNo).val()*1;
			//alert(txtProductQty+'='+hdnProductQty+'='+hdnOrderQty+'='+hdnTotalProductQty+'='+hdnBalanceQty+'='+rowNo);
			if(txtProductQty < hdnProductQty)
			{
				if($('#hdnisNextProcess').val() == 1)
				{
					alert('Not allow to reduce original qty.');
					$('#txt_product_qnty').val(hdnProductQty);
					$('#hdnProductQtyError').val(0);
					return;
				}
			}
			
			if(txtProductQty != hdnProductQty)
			{
				hdnTotalProductQty = (hdnTotalProductQty-hdnProductQty);
				if(hdnOrderQty < (hdnTotalProductQty+txtProductQty))
				{
					alert('Total product qty. is larger than order qty.');
					$('#txt_product_qnty').val(hdnProductQty);
					$('#hdnProductQtyError').val(0);
					return;
				}
			}
		}
	}
	function fnc_operator_name()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var booking_id = $('#txt_program_no').val();
		var receive_basis = $('#cbo_production_basis').val();
		var cbo_location_name = $('#cbo_knit_location_name').val();
		if (form_validation('cbo_company_id*cbo_knitting_company','Company*Knitting Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/subcon_kniting_production_controller.php?cbo_company_id='+cbo_company_id+'&booking_id='+booking_id+'&receive_basis='+receive_basis+'&cbo_location_name='+cbo_location_name+'&cbo_knitting_company='+cbo_knitting_company+'&action=operator_name_popup';
			var title='Operator Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=280px,height=290px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var operator_hdn=this.contentDoc.getElementById("operator_hdn").value;
				if(trim(operator_hdn)!="")
				{
					operator_data=operator_hdn.split("_");
					operator_id=operator_data[0];
					operator_name=operator_data[1];
					freeze_window(5);
					$('#txt_operator_name').val(operator_name);
					$('#txt_operator_id').val(operator_id);
					release_freezing();
				}
			}
		}
	}



	function fnc_barcode_For_extranal_database() {
		var dtls_id=$('#update_dtls_id').val();
		var mst_id=$('#update_id').val();
		if(dtls_id=="")
		{
			alert("Save First");	
			return;
		}
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
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
		
		data=data+"***"+dtls_id+"*********"+mst_id;
		var response = return_ajax_request_value(data, "save_barcode_for_extranal_database", "requires/subcon_kniting_production_controller");
		if(response==0)
		{
			show_msg('0');
		}
		else
		{
			show_msg('10');
		}
	   // window.open(url + ".zip", "##");
	}

	function fnc_barcode_code128(type)
	{
		var mst_id=$('#update_id').val();
		var dtls_id=$('#update_id_dtl').val();
		
		if(mst_id=="")
		{
			alert("Save First");	
			return;
		}
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
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
		
		data=data+"***"+mst_id+"***"+dtls_id;
		//window.open("requires/grey_production_entry_controller.php?data=" + data+'&action=report_barcode_code128', true );
		if(type==1){
			var url=return_ajax_request_value(data, "print_barcode_one_128", "requires/subcon_kniting_production_controller");
		} else if(type==2){
			var url=return_ajax_request_value(data, "print_barcode_one_128_v2", "requires/subcon_kniting_production_controller");
		}

		window.open(url,"##");
		release_freezing();
	}
</script>
<body onLoad="set_hotkey();">
<div style="width:100%;">   
	<? echo load_freeze_divs ("../",$permission);  ?>
    <form name="knitingproduction_1" id="knitingproduction_1">
    	<div style="width:880px; float:left;">
        <fieldset style="width:850px">
        <legend>Kniting Production</legend>
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td colspan="3">
                        <fieldset>
                        <table cellpadding="0" cellspacing="2" width="100%">
                            <tr>
                                <td align="right" colspan="3"><strong> Production ID </strong></td>
                                <td width="140" align="justify">
                                    <input type="hidden" name="update_id" id="update_id" />
                                    <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openmypage_production();" tabindex="1" readonly>
                                </td>
                            </tr>
                            <tr>
                            	<td width="120" class="must_entry_caption">Production Basis</td>
                                <td width="150"><?php $productionBasisArr = array(1=>'Sub-Contract Order', 2=>'Sub-Contract Plan'); echo create_drop_down( "cbo_production_basis",150,$productionBasisArr,"",0,"",1,"func_onchange_productionBasis(this.value)","","","","","",""); ?></td>
                                
                                <td style="width:120px;">Program No</td>
                                <td><input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:140px;" placeholder="Double Click to Search" onDblClick="func_browse_plan();" disabled="disabled" readonly />	
                                </td>

                                
                            </tr>
                            <tr>
                                <td width="120" class="must_entry_caption">Company Name</td>
                                <td width="150">
									<? 
										echo create_drop_down( "cbo_company_id",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/subcon_kniting_production_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/subcon_kniting_production_controller', this.value, 'load_drop_down_party_name', 'party_td' ); get_php_form_data( this.value,'roll_maintained' ,'requires/subcon_kniting_production_controller');","","","","","",2);
                                    ?>
                                </td>

                                <td width="120">Location </td>                                              
                                <td width="150" id="location_td">
									<? 
										echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                                    ?>
                                </td>
                               
                                <td width="120" class="must_entry_caption">Party Name</td>
                                <td width="140" id="party_td">
									<?
										echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",4);
                                    ?> 
                                </td>
                            </tr> 

                            <tr>
                            	<td width="120" class="must_entry_caption" >Knitting Source</td>
                                <td width="150">
                                	<?
									echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/subcon_kniting_production_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
									?>
                                </td>

                                <td width="120" class="must_entry_caption" >Knitting Company</td>
                                <td width="150" id="knitting_com">
                                	<?
									echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "load_drop_down( 'requires/subcon_kniting_production_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
									?>
                                </td>

                                <td width="120">Knit Company Location </td>                                              
                                <td width="150" id="knit_location_td">
									<? 
										echo create_drop_down( "cbo_knit_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                            	<td width="120" class="must_entry_caption">Production Date</td>
                                <td width="140">
                                    <input class="datepicker" type="text" style="width:140px" name="txt_production_date" id="txt_production_date" tabindex="5" />
                                </td>

                                <td width="120"> Prod. Challan No </td>
                                <td width="140">
                                    <input type="text" name="txt_prod_chal_no" id="txt_prod_chal_no" class="text_boxes" style="width:140px" tabindex="6" placeholder="Write" >
                                </td>
                                <td width="120">Yarn Issue Ch. No</td>                                              
                                <td width="140"> 
                                    <input type="text" name="txt_yarn_issue_challan_no" id="txt_yarn_issue_challan_no" class="text_boxes" style="width:140px" tabindex="7" placeholder="Write" >
                                </td> 
                            </tr>
                            <tr>
                                <td width="120">Remarks </td>                                              
                                <td colspan="3"> 
                                    <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:420px" maxlength="150" title="Maximum 150 Character" tabindex="8">
                                </td> 
                            </tr>                                      
                        </table>
                        </fieldset>
                    </td>
                </tr>
                <tr align="center">
                    <td width="64%" valign="top" style="margin-left:10px;">
                        <fieldset style="width:600px">
                        <legend>New Entry</legend>
                        <table  cellpadding="0" cellspacing="2" width="100%" align="center">
                            <tr>
                                <td style="width:120px ;" class="must_entry_caption">Order No</td>
                                <td>
                                	<input type="hidden" name="update_id_dtl" id="update_id_dtl" />
                                	<input type="hidden" name="order_no_id" id="order_no_id" />
                                    <input type="hidden" name="process_id" id="process_id" /> 
                                    <input type="hidden" name="txt_roll_maintained" id="txt_roll_maintained" />
                                    <input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
                                    <input type="hidden" name="save_data" id="save_data" style="width:50px;" readonly>
                                    <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
                                    <input type="hidden" name="hdnProductQty" id="hdnProductQty" readonly />
                                    <input type="hidden" name="hdnSelectedRow" id="hdnSelectedRow" readonly />
                                    <input type="hidden" name="hdnColorId" id="hdnColorId" readonly />
                                    <input type="hidden" name="hdnProductQtyError" id="hdnProductQtyError" readonly />
                                    <input type="hidden" name="hdnisNextProcess" id="hdnisNextProcess" readonly />
                                    
                                     <input type="hidden" name="txtOrderQty" id="txtOrderQty" readonly />
                                     <input type="hidden" name="txtTotalProductQty" id="txtTotalProductQty" readonly />
                                     <input type="hidden" name="txtBalanceQty" id="txtBalanceQty" readonly />
                                       
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px;" placeholder="Double Click to Search" onDblClick="openmypage_order_no();" readonly tabindex="14" />	
                                </td>
                                <td>Color Range</td>
                                <td>
									<?
										echo create_drop_down( "cbo_color_range", 150, $color_range,"",1,"--Select Range--", 0,"", 0,"","" );
                                    ?>
                                </td>
                                  
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Process</td>
                                <td>
									<? 
										echo create_drop_down("cbo_process", 150, $conversion_cost_head_array,"", 1, "--Select Process--",$selected,"", "","1,3,4","","","",9);
                                    ?>
                                </td>
                                <td style="width:120px;" class="must_entry_caption">Yarn Lot</td>
                                <td style="width:140px;">
                                    <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:140px"  tabindex="17" /> 
                                </td>
                                
                                
                            </tr>
                            <tr>
                            	 <td class="must_entry_caption">Const. Compo.</td>
                                <td><input type="hidden" name="hidd_comp_id" id="hidd_comp_id" />

                                    <input type="text" name="txt_febric_description" id="txt_febric_description" class="text_boxes" style="width:140px" tabindex="10" readonly />
                                </td>
                                
                                <td class="must_entry_caption">Yarn Count</td>
                                <td>
									<?
										echo create_drop_down("cbo_yarn_count",150,"select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",1, "-- Select --", $selected, "","","","","","",18);
                                    ?>
                                </td>   
                            </tr>
                           
                            <tr>
                                <td class="must_entry_caption">GSM</td>
                                <td>
                                    <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:140px;"  tabindex="11"/>	
                                </td>
                                <td>Brand</td>
                                <td>
                                    <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:140px" tabindex="19"/> 
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Dia / Width</td>
                                <td>
                                    <input type="text" name="txt_width" id="txt_width" placeholder="Dia Only" class="text_boxes_numeric" style="width:60px" tabindex="12" /> 
									<?
										echo create_drop_down( "cbo_dia_width_type", 70, $fabric_typee,"",1, "-Width-", 0, "" );
                                	?>	
                                </td>
                                
                                <td>Shift Name</td>
                                <td>
                                    <? 
                                        echo create_drop_down( "cbo_shift_id", 150, $shift_name,"", 1, "-- Select Shift --", 0, "",'' );
                                    ?>	
                                </td>
                            </tr>
                            <tr>
                             <td>Fab. Color</td>
                                <td>
                                    <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px" >
                                </td>
                                 <td>M/C Gauge</td>
                                <td>
                                    <input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:140px;"/>
                                </td>
                            </tr>
                            <tr>
                             	<td>Stitch Length</td>
                                <td>
                                    <input type="text" name="txt_stitch_len" id="txt_stitch_len" class="text_boxes" style="width:140px" >
                                </td>
                                <td>Prod. Floor</td>
                                <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>M/C Dia</td>
                                <td>
                                    <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:140px;"/>
                                </td>
                                 <td class="">Machine No.</td>
                                <td id="machine_td">
                                    <? echo create_drop_down( "cbo_machine_name", 150, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Grey Product Qty</td>
                                <td>
                                <!--<input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" onClick="openmypage_po()" onKeyUp="func_production_qty_check()" style="width:140px;" tabindex="15" placeholder=""/>-->
                                <!--<input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" onKeyUp="func_production_qty_check()" style="width:140px;" tabindex="15" placeholder=""/>	-->
                                <input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" style="width:140px;" tabindex="15" placeholder=""/>	
                                </td>
                                <td>Operator</td>
											<td>
												<input type="text" name="txt_operator_name" id="txt_operator_name" placeholder="Browse" onDblClick="fnc_operator_name();" class="text_boxes" style="width:140px">
												<input type="hidden" name="txt_operator_id" id="txt_operator_id" class="text_boxes" style="width:120px" disabled="disabled">
								</td>
                               
                            </tr>
                            <tr>
                                 <td>UOM</td>
                                <td><? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,"",1,"--Select UOM--", 12,"", 1,"","" ); ?>
                                </td>
                                
                                <td>Reject Fab Receive</td>
                                <td>
                                <input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:140px;" tabindex="16" readonly />	
                                </td>
                                
                            </tr>
                            <tr>
                                <td>Job No</td>
                                <td>
                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" disabled>
                                </td>
                                
                                <td>No of Roll</td>
                                <td>
                                <input type="text" name="txt_roll_qnty" id="txt_roll_qnty" class="text_boxes_numeric" style="width:140px ;text-align:right"  tabindex="13" />
                                </td>
                               
                            </tr>
                            <tr>
                             <td align="left" colspan="2">
                                <input type="text" name="text_new_remarks" id="text_new_remarks" class="text_boxes" title="Maximum 1000 Character" maxlength="1000"  style="width:260px ;text-align:right"  tabindex="14"  placeholder="Click to add Remarks. Maximum 1000 Character." onClick="openmypage_remarks()"; readonly />
                                </td>
                            </tr> 
                        </table>
                        </fieldset>
                    </td>
                    <td width="30%" valign="top" style="margin-left:10px;">
                        <div style="width:98%" id="roll_details_list_view"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="button_container">
						<? 
						echo load_submit_buttons($permission, "subcon_kniting_production", 0,0,"reset_form('knitingproduction_1','kniting_production_list_view','','','disable_enable_fields(\'cbo_company_id*cbo_party_name\',0)')",1);
                        ?> 
                    </td>	  
                </tr>
            </table> 
        </fieldset>
        </div>
    </form>
    <div style="width:800px; margin-top:10px;" id="kniting_production_list_view" align="center"></div>
    <div id="list_fabric_desc_container" style="max-height:400px; width:450px; overflow:auto; float:left; position:relative;"></div>
</div>
</body>
<script>
	set_multiselect('cbo_yarn_count','0','0','','0');
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>