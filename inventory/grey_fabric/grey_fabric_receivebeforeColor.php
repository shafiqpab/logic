<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Receive 
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	12/05/2013
Updated by 		:   Kausar (Creating Report)	
Update date		:   12-12-2013 
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
echo load_html_head_contents("Grey Fabric Receive ", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var str_brand = [<? echo substr(return_library_autocomplete( "select distinct(brand_name) from lib_brand", "brand_name"  ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	
	$(document).ready(function(e)
	 {
          $("#txt_brand").autocomplete({
			 source: str_brand
		  });
		  
		   $("#txt_color").autocomplete({
			 source: str_color
		  });
     });
	 

	function set_receive_basis()
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		
		$('#booking_without_order').val('');
		$('#txt_job_no').val('');
		$('#txt_receive_qnty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#txt_deleted_id').val('');
		$('#roll_details_list_view').html('');
		
		$('#txt_receive_qnty').attr('readonly','readonly');
		$('#txt_receive_qnty').attr('onClick','openmypage_po();');	
		$('#txt_receive_qnty').attr('placeholder','Single Click');
		
		if(recieve_basis == 4 || recieve_basis == 6 )
        {
			$('#txt_booking_no').val('');	
			$('#txt_booking_no_id').val('');	
			$('#txt_booking_no').attr('disabled','disabled');
			$('#cbo_buyer_name').removeAttr('disabled','disabled');	
			$('#cbo_body_part').removeAttr('disabled','disabled');	
			$('#fabric_desc_id').val('');
			$('#txt_fabric_description').val('');
			$('#txt_fabric_description').removeAttr('disabled','disabled');	
			set_auto_complete();		
        }
        else
        {
			$('#txt_booking_no').val('');	
			$('#txt_booking_no_id').val('');
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$('#cbo_buyer_name').val(0);
			$('#cbo_buyer_name').attr('disabled','disabled');
			
			if(recieve_basis==1)
			{
				$('#cbo_body_part').removeAttr('disabled','disabled');
				$('#cbo_buyer_name').removeAttr('disabled','disabled');
				set_auto_complete();
			}
			else
			{
				$('#cbo_body_part').val(0);
				$('#cbo_body_part').attr('disabled','disabled');
				$('#cbo_buyer_name').val(0);
				$('#cbo_buyer_name').attr('disabled','disabled');
			}
			
			$('#fabric_desc_id').val('');
			$('#txt_fabric_description').val('');
			$('#txt_fabric_description').attr('disabled','disabled');
        }
		
		$('#list_fabric_desc_container').html('');
	}
	
	function openmypage_wo_pi_production_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var garments_nature = $('#garments_nature').val();
		
		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'WO/PI/Production Selection Form';	
			var page_link = 'requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&garments_nature='+garments_nature+'&action=wo_pi_production_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_production_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_production_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //Access form field with id="emailfield"
				var hidden_buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value; //Access form field with id="emailfield"
				var hidden_production_data=this.contentDoc.getElementById("hidden_production_data").value; //Access form field with id="emailfield"
				
				if(theemail!="")
				{
					freeze_window(5);
					set_receive_basis();

					if(recieve_basis==2)
					{
						get_php_form_data(theemail+"**"+booking_without_order+"**"+recieve_basis, "populate_data_from_booking", "requires/grey_fabric_receive_controller" );
						show_list_view(theename+"**"+booking_without_order+"**"+recieve_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					else
					{
						if(recieve_basis==9)
						{
							$('#cbo_buyer_name').val(hidden_buyer_id);
							if(booking_without_order==1)
							{
								$('#txt_receive_qnty').removeAttr('readonly','readonly');
								$('#txt_receive_qnty').removeAttr('onClick','onClick');
								$('#txt_receive_qnty').removeAttr('placeholder','placeholder');	
							}
							else
							{
								$('#txt_receive_qnty').attr('readonly','readonly');
								$('#txt_receive_qnty').attr('onClick','openmypage_po();');
								$('#txt_receive_qnty').attr('placeholder','Single Click');
							}
							
							var data=hidden_production_data.split("**");
							$('#cbo_knitting_source').val(data[0]);
							$('#txt_receive_chal_no').val(data[2]);
							$('#txt_yarn_issue_challan_no').val(data[3]);
							load_drop_down( 'requires/grey_fabric_receive_controller',data[0]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
							$('#cbo_knitting_company').val(data[1]);
						}
						
						$('#txt_booking_no').val(theename);
						$('#txt_booking_no_id').val(theemail);
						$('#booking_without_order').val(booking_without_order);
						show_list_view(theemail+"**"+booking_without_order+"**"+recieve_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					set_auto_complete();
					release_freezing();
				} 
			}
		}
	}
	
	function set_form_data(data)
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		
		if(recieve_basis==9)
		{
			get_php_form_data(data+"**"+roll_maintained, "populate_data_from_production", "requires/grey_fabric_receive_controller" );
		}
		else
		{
			var data=data.split("**");
			if(recieve_basis!=1)
			{
				$('#cbo_body_part').val(data[0]);
			}
			$('#txt_fabric_description').val(data[1]);
			$('#txt_gsm').val(data[2]);
			$('#txt_width').val(data[3]);
			$('#fabric_desc_id').val(data[4]);
		}
	}
	
	function openmypage_fabricDescription()
	{
		var garments_nature = $('#garments_nature').val();
		var title = 'Fabric Description Info';	
		var page_link = 'requires/grey_fabric_receive_controller.php?action=fabricDescription_popup&garments_nature='+garments_nature;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_desc_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			
			$('#txt_fabric_description').val(theename);
			$('#fabric_desc_id').val(theemail);
			$('#txt_gsm').val(theegsm);
		}
	}
	
	function openmypage_po()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var booking_no=$('#txt_booking_no').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val(); 
		var distribution_method = $('#distribution_method_id').val();
		var fabric_description=$('#txt_fabric_description').val();
		
		var fabric_desc_id=$('#fabric_desc_id').val();
		var cbo_body_part=$('#cbo_body_part').val();
		var txt_gsm=$('#txt_gsm').val();
		var txt_width=$('#txt_width').val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		
		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
			
		if(receive_basis==2 && booking_no=="")
		{
			alert("Please Select Booking No.");
			$('#txt_booking_no').focus();
			return false;
		}
		else if(receive_basis==9 && fabric_description=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_description').focus();
			return false;
		}
		
		if(roll_maintained==1) 
		{
			popup_width='800px';
		}
		else
		{
			popup_width='650px';
		}

		var title = 'PO Info';	
		var page_link = 'requires/grey_fabric_receive_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&barcode_generation='+barcode_generation+'&save_data='+save_data+'&txt_receive_qnty='+txt_receive_qnty+'&prev_distribution_method='+distribution_method+'&cbo_body_part='+cbo_body_part+'&txt_gsm='+txt_gsm+'&txt_width='+txt_width+'&fabric_desc_id='+fabric_desc_id+'&txt_deleted_id='+txt_deleted_id+'&action=po_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=430px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_grey_qnty=this.contentDoc.getElementById("tot_grey_qnty").value; //Access form field with id="emailfield"
			var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			
			$('#save_data').val(save_string);
			$('#txt_receive_qnty').val(tot_grey_qnty);
			if(roll_maintained==1)
			{
				$('#txt_roll_no').val(number_of_roll);
				$('#txt_deleted_id').val(hide_deleted_id);
			}
			else
			{
				$('#txt_deleted_id').val('');
			}
			$('#all_po_id').val(all_po_id);
			$('#distribution_method_id').val(distribution_method);
		}
	}
	
	function fnc_grey_fabric_receive(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_receive_print", "requires/grey_fabric_receive_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_knitting_source*cbo_knitting_company','Company*Receive Basis*Production Date*Store Name*Knitting Source*Knitting Com')==false )
			{
				return;
			}
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Recevied Date Can not Be Greater Than Today");
				return;
			}
			
			if($('#cbo_receive_basis').val()==1 && $('#txt_booking_no').val()=="")
			{
				alert("Please Select Booking No");
				$('#txt_booking_no').focus();
				return;
			}
			
			if($('#txt_yarn_issue_challan_no').val()=="")
			{
				var r=confirm("Press \"OK\" to Insert Yarn Issue Challan No\nPress \"Cancel\" to Insert Yarn Issue Challan No Blank");
				if (r==true)
				{
					$('#txt_yarn_issue_challan_no').focus();
					return;
				}
			}
			
			if( form_validation('cbo_body_part*txt_fabric_description*txt_receive_qnty','Body Part*Fabric Description*Grey Receive Qnty')==false )
			{
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_booking_no_id*txt_booking_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name*txt_yarn_issue_challan_no*cbo_buyer_name*cbo_body_part*txt_fabric_description*fabric_desc_id*txt_gsm*txt_width*cbo_floor_id*cbo_machine_name*txt_roll_no*txt_remarks*txt_receive_qnty*txt_room*txt_reject_fabric_recv_qnty*txt_shift_name*txt_rack*cbo_uom*txt_self*txt_yarn_lot*txt_binbox*cbo_yarn_count*txt_brand*cbo_color_range*txt_color*txt_stitch_length*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*hidden_receive_qnty*roll_maintained*booking_without_order*garments_nature*product_id*txt_deleted_id',"../../");
			freeze_window(operation);
			
			http.open("POST","requires/grey_fabric_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_grey_fabric_receive_Reply_info;
		}
	}
	
	function fnc_grey_fabric_receive_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');	
				
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_recieved_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				show_list_view(reponse[1],'show_grey_prod_listview','list_container_knitting','requires/grey_fabric_receive_controller','');
				
				reset_form('greyreceive_1','roll_details_list_view','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_booking_no*txt_booking_no_id*cbo_buyer_name*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name*txt_yarn_issue_challan_no*txt_job_no*txt_remarks*roll_maintained*barcode_generation*booking_without_order');
				
				set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
			}
			release_freezing();	
		}
	}
	
	function grey_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var garments_nature = $('#garments_nature').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=grey_receive_popup_search';
			var title='Grey Receive Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
				if(trim(grey_recv_id)!="")
				{
					freeze_window(5);
					reset_form('greyreceive_1','list_container_knitting*list_fabric_desc_container','','','','roll_maintained*barcode_generation');
					get_php_form_data(grey_recv_id, "populate_data_from_grey_recv", "requires/grey_fabric_receive_controller" );
					
					var booking_pi_production_no = $('#txt_booking_no').val();
					var booking_pi_production_id = $('#txt_booking_no_id').val();
					var booking_without_order = $('#booking_without_order').val();
					var cbo_receive_basis = $('#cbo_receive_basis').val();
					
					if(cbo_receive_basis==2)
					{
						show_list_view(booking_pi_production_no+"**"+booking_without_order+"**"+cbo_receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					else if(cbo_receive_basis==1 || cbo_receive_basis==9)
					{
						show_list_view(booking_pi_production_id+"**"+booking_without_order+"**"+cbo_receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					
					show_list_view(grey_recv_id,'show_grey_prod_listview','list_container_knitting','requires/grey_fabric_receive_controller','');
					set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
					release_freezing();
				}
							 
			}
		}
	}
	
	function put_data_dtls_part(id,type,page_path)
	{
		//get_php_form_data(id+"**"+$('#roll_maintained').val()+"**"+$('#garments_nature').val(), type, page_path );
		
		var roll_maintained=$('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		get_php_form_data(id+"**"+roll_maintained+"**"+$('#garments_nature').val(), type, page_path );
		if(roll_maintained==1)
		{
			show_list_view("'"+id+"**"+barcode_generation+"'",'show_roll_listview','roll_details_list_view','requires/grey_fabric_receive_controller','');
		}
		else
		{
			$('#roll_details_list_view').html('');
		}
	}
	
	function set_auto_complete()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		if(receive_basis==2)
		{
			var booking_id = $('#txt_booking_no_id').val();
			var booking_without_order = $('#booking_without_order').val();
			get_php_form_data(booking_id+"**"+booking_without_order+"**"+receive_basis, 'load_color', 'requires/grey_fabric_receive_controller');
		}
		else
		{
			$("#txt_color").autocomplete({
				 source: str_color
			});
		}
	}
	
	function issue_challan_no()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_no_popup';
			var title='Issue Challan Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var issue_challan=this.contentDoc.getElementById("issue_challan").value;
				if(trim(issue_challan)!="")
				{
					freeze_window(5);
					$('#txt_yarn_issue_challan_no').val(issue_challan);
					
					release_freezing();
				}
			}
		}
	}
	
	function fnc_check_issue(issue_num)
	{
		if(issue_num!="")
		{
			var issue_result = trim(return_global_ajax_value(issue_num, 'issue_num_check', '', 'requires/grey_fabric_receive_controller'));
			if(issue_result=="")
			{
				alert("Challan Number Not Found");
				$('#txt_yarn_issue_challan_no').val("");
			}
		}
	}
	
	function load_receive_basis()
	{
		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==1)
		{
			$("#cbo_receive_basis option[value='9']").remove();
		}
		else
		{
			if($('#cbo_receive_basis option:last').val()!=9)
			{
				$("#cbo_receive_basis").append('<option value="9">Production</option>');
			}
		}
	}
	
	function check_all_report()
	{
		$("input[name=chkBundle]").each(function(index, element) 
		{ 
			if( $('#check_all').prop('checked')==true) 
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}
	
	function fnc_send_printer_text()
	{
		var dtls_id=$('#update_dtls_id').val();
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
		
		data=data+"***"+dtls_id;
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/grey_fabric_receive_controller");
		window.open("requires/"+trim(url)+".zip","##");
	}
	
	function fnc_barcode_generation()
	{
		var dtls_id=$('#update_dtls_id').val();
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
		
		data=data+"***"+dtls_id;
		window.open("requires/grey_fabric_receive_controller.php?data=" + data+'&action=report_barcode_generation', true );
	}
	
</script>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="greyreceive_1" id="greyreceive_1">
    	<div style="width:950px; float:left;" align="center">        
            <fieldset style="width:950px">
            <legend>Knitting Production Entry</legend>
			<fieldset style="width:930px">
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td align="right" colspan="3"><strong> Received ID </strong></td>
                    <td>
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="grey_receive_popup();" >
                    </td>
                </tr>
                <tr>
                	<td colspan="6" height="10"></td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption"> Company </td>
                    <td width="150">
						<? 
							echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/grey_fabric_receive_controller', document.getElementById('cbo_receive_basis').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value+'_'+document.getElementById('garments_nature').value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_machine', 'machine_td' );get_php_form_data(this.value,'roll_maintained','requires/grey_fabric_receive_controller' ); load_receive_basis();" );
                        ?>
                    </td>
                    <td width="110" class="must_entry_caption"> Receive Basis </td>
                    <td width="150">
                        <? 
							if($_SESSION['fabric_nature']==2) $show_index='1,2,4,6,9'; else $show_index='1,2,4,6';
                        	echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",$show_index);
                        ?>
                    </td>
                    <td width="110" class="must_entry_caption"> Receive Date </td>
                    <td width="150">
                        <input class="datepicker" type="date" style="width:140px" name="txt_receive_date" id="txt_receive_date"/>
                    </td>
                </tr> 
                <tr>
                    <td> Receive Challan No </td>
                    <td>
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:140px" >
                    </td>
                    <? if($_SESSION['fabric_nature']==2) $show_label='WO/PI/Production'; else $show_label='WO/PI'; ?>
                    <td><? echo $show_label; ?></td>
                    <td>
                    	<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_production_popup();" readonly>
                        <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">
                        <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                    </td>
                    <td class="must_entry_caption"> Store Name </td>
                    <td id="store_td">
						<?
                            echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption"> Knitting Source </td>
                    <td>
						<?
							echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/grey_fabric_receive_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
                        ?>
                    </td>
                    <td class="must_entry_caption">Knitting Com</td>
                    <td id="knitting_com">
						<?
                        	echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
                        ?>
                    </td>
                    <td>Location </td>                                              
                    <td id="location_td">
						<? 
							echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", 0, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Yarn Issue Ch. No</td>                                              
                    <td> 
                    	<input type="text" name="txt_yarn_issue_challan_no" id="txt_yarn_issue_challan_no" placeholder="Browse or Write" onDblClick="issue_challan_no();" class="text_boxes" style="width:140px" onBlur="fnc_check_issue(this.value);">
                    </td> 
                    <td>Job No</td>
                    <td>
                    	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" readonly>
                    </td>
                    <td>Buyer</td>
                    <td id="buyer_td_id"> 
                        <?
                           echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Remarks </td>                                              
                    <td colspan="3"> 
                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:465px">
                    </td> 
                </tr>                                      
            </table>
			</fieldset>
            <table cellpadding="0" cellspacing="1" width="935" border="0">
                <tr>
                    <td width="64%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td class="must_entry_caption">Body Part</td>
                                    <td>
                                     <? 
										echo create_drop_down( "cbo_body_part", 130, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
                                     ?>
                                    </td>
                                    <td>UOM</td>
                                    <td>
										<?
                                            echo create_drop_down( "cbo_uom", 132, $unit_of_measurement,"", 0, "", '12', "",1,12 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Fabric Description </td>
                                    <td colspan="3">
                                        <input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:400px" onDblClick="openmypage_fabricDescription()" placeholder="Double Click To Search" disabled="disabled" readonly/>
                                        <input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes">
                                    </td>
                                </tr>
                                <tr>
                                    <td>GSM</td>
                                    <td>
                                        <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:120px;"  />	
                                    </td>
                                    <td>Yarn Count</td>
                                    <td>
                                    <?
                                        echo create_drop_down("cbo_yarn_count",132,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                                    ?>
                                    </td>
                                </tr> 
                                <tr>
                                    <td>Dia / Width</td>
                                    <td>
                                        <input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:120px;text-align:right;" />	
                                    </td>
                                    <td>Brand</td>
                                    <td>
                                        <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:120px" /> 
                                    </td>
                                </tr>
                                <tr>
                                	<td>Stitch Length</td>
                                    <td>
                                        <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:120px;"/>
                                    </td>
                                    <td>Shift Name</td>
                                    <td>
                                        <!--<input type="text" name="txt_shift_name" id="txt_shift_name" class="text_boxes" style="width:120px;"  />-->
                                        <? 
											echo create_drop_down( "txt_shift_name", 132, $shift_name,"", 1, "-- Select Shift --", 0, "",'' );
										?>	
                                    </td>
                                </tr>
                                <tr>
                                	<td>No of Roll</td>
                                    <td>
                                        <input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:120px" />
                                    </td>
                                    <td>Prod. Floor</td>
                                    <td id="floor_td">
                                    	<? echo create_drop_down( "cbo_floor_id", 132, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                	<td class="must_entry_caption">Grey Receive Qnty</td>
                                    <td>
                                        <input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:120px;" onClick="openmypage_po()" placeholder="Single Click" readonly/>	
                                    </td>
                                    <td>Machine No.</td>
                                    <td id="machine_td">
                                    	<? echo create_drop_down( "cbo_machine_name", 132, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                	<td>Fabric Color</td>
                                    <td>
                                        <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:120px;" maxlength="20" title="Maximum 20 Character"/>
                                    </td>
                                    <td>Room</td>
                                    <td>
                                        <input type="text" name="txt_room" id="txt_room" class="text_boxes_numeric" style="width:120px">
                                    </td>
                                </tr> 
                                <tr>
                                	<td>Color Range</td>
                                    <td>
                                        <?
											echo create_drop_down( "cbo_color_range", 132, $color_range,"",1, "-- Select --", 0, "" );
										?>
                                    </td>
                                    <td>Rack</td>
                                    <td>
                                        <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:120px">
                                    </td>
                                </tr>
                                <tr>
                                	<td>Reject Fabric Receive</td>
                                    <td>
                                        <input type="text" name="txt_reject_fabric_recv_qnty" id="txt_reject_fabric_recv_qnty" class="text_boxes_numeric" style="width:120px;" />	
                                    </td>
                                    <td>Shelf</td>
                                    <td>
                                        <input type="text" name="txt_self" id="txt_self" class="text_boxes_numeric" style="width:120px">
                                    </td>
                                </tr>
                                <tr>
                                	<td>Yarn Lot</td>
                                    <td>
                                        <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:120px" /> 
                                    </td>
                                	<td>Bin/Box</td>
                                    <td>
                                        <input type="text" name="txt_binbox" id="txt_binbox" class="text_boxes_numeric" style="width:120px">
                                    </td>
                                </tr>
                             </table>
                        </fieldset>
                    </td>                    
                    <td width="1%" valign="top"></td>
                    <td width="35%" valign="top">
                    	<div id="roll_details_list_view"></div>
                        <!--<fieldset style="display:none">
                        <legend>Display</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                    <td>&nbsp;</td> <td>&nbsp;</td>
                                </tr>
                                <tr>
                                <tr>
                                    <td>Yarn to Knit Comp</td>
                                    <td>
                                        <input type="text"  class="text_boxes_numeric" name="txt_yern_to_knit_com" id="txt_yern_to_knit_com" style="width:135px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Grey Received </td>
                                    <td>
                                        <input type="text"  class="text_boxes_numeric" name="txt_total_grey_recieved" id="txt_total_grey_recieved" style="width:135px" readonly />
                                     </td>
                                </tr>
                                <tr>
                                    <td> Reject Grey Fab. Rceived</td>
                                    <td>
                                        <input  type="text" class="text_boxes_numeric" name="txt_reject_fabric_receive" id="txt_reject_fabric_receive" style="width:135px" readonly/>
                                    </td>                               
                                </tr>
                                <tr>
                                    <td>Yet to Receive</td> 
                                    <td>
                                        <input type="text" class="text_boxes_numeric" name="txt_yet_recieved" id="txt_yet_recieved" style="width:135px" readonly />
                                    </td>  
                                </tr>                                    
                            </table>
                        </fieldset>-->	
                    </td>                       
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="button_container">
						<? 
                            echo load_submit_buttons($permission, "fnc_grey_fabric_receive", 0,1,"reset_form('greyreceive_1','list_container_knitting*list_fabric_desc_container*roll_details_list_view','','cbo_receive_basis,0','disable_enable_fields(\'cbo_company_id\');set_receive_basis();')",1);
                        ?>
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="product_id" id="product_id" readonly><!--For Receive Basis Production-->
                        <input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                        <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
                    </td>	  
                </tr>
            </table>
            <div style="width:930px;" id="list_container_knitting"></div>
		</fieldset>
        </div>  
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_fabric_desc_container" style="max-height:500px; width:340px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>
</body>
<script>
	set_multiselect('cbo_yarn_count','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>