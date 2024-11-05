<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Receive 
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	1/02/2015
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
echo load_html_head_contents("Grey Fabric Receive ", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	
	function fnc_grey_fabric_receive(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_receive_print", "requires/grey_fabric_receive_roll_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
		if( form_validation('cbo_company_id*txt_receive_date*cbo_store_name*cbo_knitting_source*cbo_knitting_company','Company*Receive Basis*Production Date*Store Name*Knitting Source*Knitting Com')==false )
			{
				return;
			}
		var tr_rows=$("#total_row").val();
		if(operation==0)
		{
		var j=1;
		for(var i=1; i<=tr_rows; i++)
		{
		if($('#checkedId_'+i).is(':checked'))
			 {
				 if($('#checkedId_'+i).is(':checked')) { var	check_value=1; } else {var check_value=0;}
				 if(j==1) var check_data=check_value; else check_data=check_data+"*"+check_value;
				 
				  if(j==1) var tran_id = escape(document.getElementById('hiden_transid_' + i).value); else tran_id=tran_id+"*"+escape(document.getElementById('hiden_transid_' + i).value);
				 if(j==1) var gray_dtlsid = escape(document.getElementById('hidden_greyid_' + i).value); else gray_dtlsid=gray_dtlsid+"*"+escape(document.getElementById('hidden_greyid_' + i).value);
				 if(j==1) var roll_id = escape(document.getElementById('hidden_rollid_' + i).value); else roll_id=roll_id+"*"+escape(document.getElementById('hidden_rollid_' + i).value);
				 
				 if(j==1) var sys = escape(document.getElementById('hidesysid_' + i).value); else sys=sys+"*"+escape(document.getElementById('hidesysid_' + i).value);
				 if(j==1) var receive_number = escape(document.getElementById('hidesysnum_' + i).value); else receive_number=receive_number+"*"+escape(document.getElementById('hidesysnum_' + i).value);
				 if(j==1) var program_id = escape(document.getElementById('hideprogrum_' + i).value); else program_id=program_id+"*"+escape(document.getElementById('hideprogrum_' + i).value);
				 if(j==1) var receive_basis = escape(document.getElementById('txtBasis_' + i).value); else receive_basis=receive_basis+"*"+escape(document.getElementById('txtBasis_' + i).value);
				 if(j==1) var barcode_id= escape(document.getElementById('hidenBarcode_' + i).value); else barcode_id=barcode_id+"*"+escape(document.getElementById('hidenBarcode_' + i).value);
			
				 if(j==1) var prod_id= escape(document.getElementById('hideprodid_' + i).value); else prod_id=prod_id+"*"+escape(document.getElementById('hideprodid_' + i).value);
				 if(j==1) var room_no = escape(document.getElementById('txtRoom_' + i).value); else room_no=room_no+"*"+escape(document.getElementById('txtRoom_' + i).value);
				 if(j==1) var rack = escape(document.getElementById('txtRack_' + i).value); else rack=rack+"*"+escape(document.getElementById('txtRack_' + i).value);
				 if(j==1) var self = escape(document.getElementById('txtSelf_' + i).value); else self=self+"*"+escape(document.getElementById('txtSelf_' + i).value);
				 if(j==1) var bin = escape(document.getElementById('txtBin_' + i).value); else bin=bin+"*"+escape(document.getElementById('txtBin_' + i).value);
				 if(j==1) var issue_qty= escape(document.getElementById('txtcurrentdelivery_' + i).value); else issue_qty=issue_qty+"*"+escape(document.getElementById('txtcurrentdelivery_' + i).value);
				  if(j==1) var roll_no = escape(document.getElementById('txtroll_' + i).value); else roll_no=roll_no+"*"+escape(document.getElementById('txtroll_' + i).value);
				 if(j==1) var knitting_source= escape(document.getElementById('knittingsource_' + i).value); else knitting_source=knitting_source+"*"+escape(document.getElementById('knittingsource_' + i).value);
				 if(j==1) var buyer_id = escape(document.getElementById('hiddenBuyer_' + i).value); else buyer_id=buyer_id+"*"+escape(document.getElementById('hiddenBuyer_' + i).innerHTML);
				 if(j==1) var po_id= escape(document.getElementById('hiddenPoId_' + i).value); else po_id=po_id+"*"+escape(document.getElementById('hiddenPoId_' + i).value);
				 if(j==1) var dia= escape(document.getElementById('hidedia_' + i).value); else dia=dia+"*"+escape(document.getElementById('hidedia_' + i).value);
				 if(j==1) var determination_id = escape(document.getElementById('hideconstruction_' + i).value); else determination_id=determination_id+"*"+escape(document.getElementById('hideconstruction_' + i).value);
		
				 if(j==1) var body_part= escape(document.getElementById('hidden_bodypart_' + i).value); else body_part=body_part+"*"+escape(document.getElementById('hidden_bodypart_' + i).value); 
				// alert(ac_code); return;
				 if(j==1) var color_id = escape(document.getElementById('hiddenColor_' + i).value); else color_id=color_id+"*"+escape(document.getElementById('hiddenColor_' + i).value);
				  if(j==1) var color_range= escape(document.getElementById('hiddenColorRange_' + i).value); else color_range=color_range+"*"+escape(document.getElementById('hiddenColorRange_' + i).value);
				  if(i==1) var yean_lot = escape(document.getElementById('hidden_yeanlot_' + i).value); else yean_lot=yean_lot+"*"+escape(document.getElementById('hidden_yeanlot_' + i).value);
			      if(j==1) var gsm= escape(document.getElementById('hidegsm_' + i).value); else gsm=gsm+"*"+escape(document.getElementById('hidegsm_' + i).value);
			
				 if(j==1) var uom = escape(document.getElementById('hiddenUom_' + i).value); else uom=uom+"*"+escape(document.getElementById('hiddenUom_' + i).value);
				 if(j==1) var yean_cont= escape(document.getElementById('hiddenYeanCount_' + i).value); else yean_cont=yean_cont+"*"+escape(document.getElementById('hiddenYeanCount_' + i).value); 
				 if(j==1) var band_id = escape(document.getElementById('hiddenBand_' + i).value); else band_id=band_id+"*"+escape(document.getElementById('hiddenBand_' + i).value);
				 
				 if(i==1) var shift_id= escape(document.getElementById('hiddenShift_' + i).value); else shift_id=shift_id+"*"+escape(document.getElementById('hiddenShift_' + i).value);
				 if(j==1) var floor_id = escape(document.getElementById('hiddenFloorId_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('hiddenFloorId_' + i).value);
				 if(j==1) var machine_name = escape(document.getElementById('hiddenMachine_' + i).value); else machine_name=machine_name+"*"+escape(document.getElementById('hiddenMachine_' + i).value);
				   if(j==1) var hidden_qty = escape(document.getElementById('hidden_delivery_qty_' + i).value); else hidden_qty=hidden_qty+"*"+escape(document.getElementById('hidden_delivery_qty_' + i).value);
				   j++;
			 }
			
		  }
				 
		}
		else
		{
		for(var i=1; i<=tr_rows; i++)
		{
			     if($('#checkedId_'+i).is(':checked')) { var	check_value=1; } else {var check_value=0;}
				 if(i==1) var check_data=check_value; else check_data=check_data+"*"+check_value;
				 if(i==1) var tran_id = escape(document.getElementById('hiden_transid_' + i).value); else tran_id=tran_id+"*"+escape(document.getElementById('hiden_transid_' + i).value);
				 if(i==1) var gray_dtlsid = escape(document.getElementById('hidden_greyid_' + i).value); else gray_dtlsid=gray_dtlsid+"*"+escape(document.getElementById('hidden_greyid_' + i).value);
				 if(i==1) var roll_id = escape(document.getElementById('hidden_rollid_' + i).value); else roll_id=roll_id+"*"+escape(document.getElementById('hidden_rollid_' + i).value);
				 if(i==1) var receive_number = escape(document.getElementById('hidesysnum_' + i).value); else receive_number=receive_number+"*"+escape(document.getElementById('hidesysnum_' + i).value);
				 if(i==1) var program_id = escape(document.getElementById('hideprogrum_' + i).value); else program_id=program_id+"*"+escape(document.getElementById('hideprogrum_' + i).value);
				 if(i==1) var receive_basis = escape(document.getElementById('txtBasis_' + i).value); else receive_basis=receive_basis+"*"+escape(document.getElementById('txtBasis_' + i).value);
				 if(i==1) var barcode_id= escape(document.getElementById('hidenBarcode_' + i).value); else barcode_id=barcode_id+"*"+escape(document.getElementById('hidenBarcode_' + i).value);
				
				 if(i==1) var prod_id= escape(document.getElementById('hideprodid_' + i).value); else prod_id=prod_id+"*"+escape(document.getElementById('hideprodid_' + i).value);
				 if(i==1) var room_no = escape(document.getElementById('txtRoom_' + i).value); else room_no=room_no+"*"+escape(document.getElementById('txtRoom_' + i).value);
				 if(i==1) var rack = escape(document.getElementById('txtRack_' + i).value); else rack=rack+"*"+escape(document.getElementById('txtRack_' + i).value);
				 if(i==1) var self = escape(document.getElementById('txtSelf_' + i).value); else self=self+"*"+escape(document.getElementById('txtSelf_' + i).value);
				 if(i==1) var bin = escape(document.getElementById('txtBin_' + i).value); else bin=bin+"*"+escape(document.getElementById('txtBin_' + i).value);
				 if(i==1) var issue_qty= escape(document.getElementById('txtcurrentdelivery_' + i).value); else issue_qty=issue_qty+"*"+escape(document.getElementById('txtcurrentdelivery_' + i).value);
				  if(i==1) var roll_no = escape(document.getElementById('txtroll_' + i).value); else roll_no=roll_no+"*"+escape(document.getElementById('txtroll_' + i).value);
				 if(i==1) var knitting_source= escape(document.getElementById('knittingsource_' + i).value); else knitting_source=knitting_source+"*"+escape(document.getElementById('knittingsource_' + i).value);
				
				 if(i==1) var buyer_id = escape(document.getElementById('hiddenBuyer_' + i).value); else buyer_id=buyer_id+"*"+escape(document.getElementById('hiddenBuyer_' + i).innerHTML);
				 if(i==1) var po_id= escape(document.getElementById('hiddenPoId_' + i).value); else po_id=po_id+"*"+escape(document.getElementById('hiddenPoId_' + i).value);
				 if(i==1) var dia= escape(document.getElementById('hidedia_' + i).value); else dia=dia+"*"+escape(document.getElementById('hidedia_' + i).value);
				 if(i==1) var determination_id = escape(document.getElementById('hideconstruction_' + i).value); else determination_id=determination_id+"*"+escape(document.getElementById('hideconstruction_' + i).value);
		
				 if(i==1) var body_part= escape(document.getElementById('hidden_bodypart_' + i).value); else body_part=body_part+"*"+escape(document.getElementById('hidden_bodypart_' + i).value); 
				 if(i==1) var color_id = escape(document.getElementById('hiddenColor_' + i).value); else color_id=color_id+"*"+escape(document.getElementById('hiddenColor_' + i).value);
				  if(i==1) var color_range= escape(document.getElementById('hiddenColorRange_' + i).value); else color_range=color_range+"*"+escape(document.getElementById('hiddenColorRange_' + i).value);
				 if(i==1) var yean_lot = escape(document.getElementById('hidden_yeanlot_' + i).value); else yean_lot=yean_lot+"*"+escape(document.getElementById('hidden_yeanlot_' + i).value);
			      if(i==1) var gsm= escape(document.getElementById('hidegsm_' + i).value); else gsm=gsm+"*"+escape(document.getElementById('hidegsm_' + i).value);
			
				 if(i==1) var uom = escape(document.getElementById('hiddenUom_' + i).value); else uom=uom+"*"+escape(document.getElementById('hiddenUom_' + i).value);
				 if(i==1) var yean_cont= escape(document.getElementById('hiddenYeanCount_' + i).value); else yean_cont=yean_cont+"*"+escape(document.getElementById('hiddenYeanCount_' + i).value); 
				 if(i==1) var band_id = escape(document.getElementById('hiddenBand_' + i).value); else band_id=band_id+"*"+escape(document.getElementById('hiddenBand_' + i).value);
				 
				  if(i==1) var shift_id= escape(document.getElementById('hiddenShift_' + i).value); else shift_id=shift_id+"*"+escape(document.getElementById('hiddenShift_' + i).value);
				 if(i==1) var floor_id = escape(document.getElementById('hiddenFloorId_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('hiddenFloorId_' + i).value);
				 if(i==1) var machine_name = escape(document.getElementById('hiddenMachine_' + i).value); else machine_name=machine_name+"*"+escape(document.getElementById('hiddenMachine_' + i).value);
				  if(i==1) var hidden_qty = escape(document.getElementById('hidden_delivery_qty_' + i).value); else hidden_qty=hidden_qty+"*"+escape(document.getElementById('hidden_delivery_qty_' + i).value);
			 }
				 
		}
		
			
			var company_id=$("#cbo_company_id").val();
			var cbo_store_name=$("#cbo_store_name").val();
			var txt_receive_date=$("#txt_receive_date").val();
			var txt_receive_chal_no=$("#txt_receive_chal_no").val();
			var cbo_location_name=$("#cbo_location_name").val();
			var txt_recieved_id=$("#txt_recieved_id").val();
			var cbo_knitting_source=$("#cbo_knitting_source").val();
			var cbo_knitting_company=$("#cbo_knitting_company").val();
			var yarn_issue_challan_no=$("#txt_yarn_issue_challan_no").val();
			var txt_remarks=$("#txt_remarks").val();
			var update_id=$("#update_id").val();
			var hidden_delivery_id=$("#hidden_delivery_id").val();
			var txt_challan_no=$("#txt_challan_no").val();
			var data='action=save_update_delete&operation='+operation+
			
			'&txt_recieved_id='+txt_recieved_id+
			'&cbo_company_id='+company_id+
			'&cbo_store_name='+cbo_store_name+
			'&cbo_location_name='+cbo_location_name+
			'&txt_receive_date='+txt_receive_date+
			'&txt_receive_chal_no='+txt_receive_chal_no+
			'&cbo_knitting_source='+cbo_knitting_source+
			'&cbo_knitting_company='+cbo_knitting_company+
			'&yarn_issue_challan_no='+yarn_issue_challan_no+
			'&txt_remarks='+txt_remarks+
			'&update_id='+update_id+
			'&hidden_delivery_id='+hidden_delivery_id+
			'&txt_challan_no='+txt_challan_no+
			'&check_data='+check_data+ 
			'&receive_number='+receive_number+
			'&receive_basis='+receive_basis+
			'&barcode_id='+barcode_id+
			'&gsm='+gsm+
		
			'&program_id='+program_id+
			'&prod_id='+prod_id+
			'&room_no='+room_no+
			'&rack='+rack+
			'&self='+self+
			'&bin='+bin+
			'&issue_qty='+issue_qty+
			'&roll_no='+roll_no+
			'&knitting_source='+knitting_source+
			//'&receive_date='+receive_date+
			'&buyer_id='+buyer_id+
			'&hidden_qty='+hidden_qty+
			
			'&tran_id='+tran_id+
			'&gray_dtlsid='+gray_dtlsid  +
			'&roll_id='+roll_id+
			
			'&po_id='+po_id+
			'&dia='+dia+
			'&determination_id='+determination_id+  
			'&body_part='+body_part+
			'&color_id='+color_id+
			'&color_range='+color_range+
			'&yean_lot='+yean_lot+
			'&uom='+uom+
			'&yean_cont='+yean_cont+
			'&band_id='+band_id+
			'&shift_id='+shift_id+	
			'&floor_id='+floor_id+
			'&machine_name='+machine_name;
				
			//freeze_window(operation);
			//alert(data);return;
			http.open("POST","requires/grey_fabric_receive_roll_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_grey_fabric_receive_Reply_info;
		}
	}
	
	function fnc_grey_fabric_receive_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_recieved_id').value =reponse[2];
				document.getElementById('hidden_delevery_scan').value =reponse[4];
				$('#cbo_company_id').attr('disabled','disabled');
			}

			set_button_status(1, permission, 'fnc_grey_fabric_receive',1,1);	
			release_freezing();	
		}
	}
	
	


// for receive pop up	
	
	function grey_receive_popup()
	{
			var page_link='requires/grey_fabric_receive_roll_controller.php?action=grey_receive_popup_search';
			var title='Grey Receive Form';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
				var grey_recv_no=(this.contentDoc.getElementById("hidden_data").value).split("_");
				if(trim(grey_recv_id)!="")
				{
					$("#txt_challan_no").val(grey_recv_no[0]);
					$("#cbo_company_id").val(grey_recv_no[1]);
					$("#txt_company_name").val(grey_recv_no[2]);
					$("#cbo_knitting_source").val(grey_recv_no[3]);
					$("#cbo_knitting_company").val(grey_recv_no[4]);
					$("#txt_knitting_company").val(grey_recv_no[5]);
					$("#hidden_delivery_id").val(grey_recv_no[6]);
					$("#update_id").val(grey_recv_id);
					show_list_view(grey_recv_id, 'grey_item_details_update', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', '' );
					get_php_form_data(grey_recv_id, "load_php_update_form", "requires/grey_fabric_receive_roll_controller" );
					set_button_status(1, permission, 'fnc_grey_fabric_receive',1,1);
					//release_freezing();
				}
							 
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
	
   function focace_change()
   {
	$('#txt_challan_scan').focus();  
   }	
	  
	  
	  
	function challan_no_popup()
	{
	    var cbo_company_id = $('#cbo_company_id').val();
		var page_link='requires/grey_fabric_receive_roll_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=challan_popup';
		var title='Grey Challan Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_id=this.contentDoc.getElementById("hidden_receive_id").value;
			var grey_recv_no=(this.contentDoc.getElementById("hidden_data").value).split("_");
			$("#txt_challan_no").val(grey_recv_no[0]);
			$("#cbo_company_id").val(grey_recv_no[1]);
			$("#txt_company_name").val(grey_recv_no[2]);
			$("#cbo_knitting_source").val(grey_recv_no[3]);
			$("#cbo_knitting_company").val(grey_recv_no[4]);
			$("#txt_knitting_company").val(grey_recv_no[5]);
			$("#hidden_delivery_id").val(grey_recv_id);
			
			if(trim(grey_recv_id)!="")
			{
				set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
				show_list_view(grey_recv_no[0], 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', '' );
				load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[1], 'load_drop_down_store', 'store_td');
				load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[1], 'load_drop_down_location', 'location_td');
			}
		}
	}

	$('#txt_challan_scan').live('keydown', function(e) {
	   
		if (e.keyCode === 13) {
			e.preventDefault();
		    scan_challan_no(this.value); 
		}
	});	
	
	function scan_challan_no(str)
	{
		if(str.length<15)
		{
		  alert("Invalid Challan No");
		  $('#txt_challan_scan').val('');
		  return; 
		}
		
	    var previous_challan=$('#txt_challan_no').val();
	    var updatable_challan=$('#hidden_delevery_scan').val();
		if(previous_challan!="")
		{
			if(updatable_challan!="")
			 {
				if(str==updatable_challan)
				{
					alert("Roll of this Challan already Received.");
					$('#txt_challan_scan').val('');
					return;
				}
			 }
			 if(str==previous_challan)
			 {
			 $('#txt_challan_scan').val('')	;
			 alert("Roll of this challan already shown");
			 return;
			 }
			 else
			 {
			 r=confirm("Press OK to Save Previous Challan Or Press Cancel to discard previous challan");
			 if(r==false)
				 {
				 $('#txt_challan_no').val(str)	;	
				 $('#txt_challan_scan').val('');
				 show_list_view(str, 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', '' );
				 get_php_form_data( str, "load_php_mst_form", "requires/grey_fabric_receive_roll_controller" );
				 $('#hidden_delevery_scan').val('');
				 }
			 else
				 {
				  $('#txt_challan_scan').val('');
				  return; 
				 }
			 }
		}
		else
		{
			 $('#txt_challan_no').val(str)	;	
			 $('#txt_challan_scan').val('');
			 show_list_view(str, 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', '' );
			 get_php_form_data( str, "load_php_mst_form", "requires/grey_fabric_receive_roll_controller" );
			 $('#hidden_delevery_scan').val('');
		}
	}

	
</script>
<body onLoad="set_hotkey();focace_change()">
 <div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="greyreceive_1" id="greyreceive_1">
    	   <fieldset style="width:850px">   
          
            <legend>Knitting Production Entry</legend>
			
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr> 
                    <td align="right" colspan="3"><strong> Received ID </strong></td>
                    <td colspan="3">
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="hidden" name="hidden_delevery_scan" id="hidden_delevery_scan" />
                        
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="grey_receive_popup();" >
                    </td>
                </tr>
               <tr> <td  align="left" colspan="6"></td> </tr>
                <tr>
                	<td  align="left" >Scan Challan No</td>              <!-- 11-00030  -->
                      <td  align="left"><input type="text" name="txt_challan_scan" id="txt_challan_scan" class="text_boxes" style="width:130px" placeholder="Browse or Scan/Write" onDblClick="challan_no_popup()"   /></td>
                     <td  align="left" >Receive Challan </td>              <!-- 11-00030  -->
                     <td  align="left"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px" placeholder="" onDblClick=""    disabled/></td>
                     <td width="100" class="must_entry_caption"> Company </td>
                    <td width="">
                    <input type="hidden" name="cbo_company_id" id="cbo_company_id" />
                    <input type="text" name="txt_company_name" id="txt_company_name" class="text_boxes" style="width:140px" placeholder="Display"     disabled/>
					
                        </td>
                </tr>
                <tr>
                    
                     <td width="110" class="must_entry_caption"> Store Name </td>
                    <td width="150" id="store_td">
						<?
                            echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
                        ?>
                    </td>
                    <td width="110" class="must_entry_caption"> Receive Date </td>
                    <td width="150">
                        <input class="datepicker" type="date" style="width:140px" name="txt_receive_date" id="txt_receive_date"/>
                        <input type="hidden" id="hidden_delivery_id" name="hidden_delivery_id">
                    </td>
                     <td>  Challan No </td>
                    <td>
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:140px" >
                    </td>
                </tr> 
                
                <tr>
                   
                    
                    <td>Location </td>                                              
                    <td id="location_td">
						<? 
							echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", 0, "" );
                        ?>
                    </td>
                      <td class="must_entry_caption"> Dyeing Source </td>
                    <td>
						<?
							echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/grey_fabric_receive_roll_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",1,'1,3');
                        ?>
                    </td>
                       <td class="must_entry_caption">Dyeing Company</td>
                    <td id="knitting_com">
                     <input type="hidden" name="cbo_knitting_company" id="cbo_knitting_company" />
                    <input type="text" name="txt_knitting_company" id="txt_knitting_company" class="text_boxes" style="width:140px" placeholder="Display"    disabled/>
						
                    </td>
                </tr>
           
                <tr>
                    
                </tr> 
                                                   
            </table>
            <br>
			</fieldset>
			<br>
			<fieldset style="width:1320px;text-align:left">
              <div id="recipe_items_list_view" style="margin-top:10px"> 
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
             <table cellpadding="0" width="1330" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="60">Batch No</th>
                        <th width="80">Body Part</th>
                        <th width="100">Construction</th>
                        <th width="60"> Production Qty.</th>
                        <th width="70">Color</th>
                        <th width="40">Gsm</th>
                        <th width="60"> Production Qty.</th>
                        <th width="40">Dia</th>
                        <th width="50">Roll Qty.</th>
                        <th width="50">Reject Qty.</th>
                        <th width="50">Room</th>
                        <th width="50">Rack</th>
                        <th width="50">Shelf</th>
                        <th width="60">Dia/  Width Type</th>
                        <th width="45">Year</th>
                        <th width="45">Job No</th>
                        <th width="65">Buyer</th>
                        <th width="80">Order No</th>
                        <th width="60">Product Id</th>
                        <th width="">System Id</th>
                    </thead>
                 </table>
                 <div style="width:1360px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1330" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    <tbody id="list_view_container">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="40" id="sl_1" >1&nbsp;&nbsp;
                               <input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" ></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="45" id="rollNo_1"></td>
                                <td width="60" id="batchNo_1"></td>
                                <td width="80" id="bodyPart_1" style="word-break:break-all;" align="left"></td>
                                <td width="100" id="cons_1" style="word-break:break-all;" align="left"></td>
                                
                                <td width="70" id="color_1"></td>
                                <td width="40" id="gsm_1"></td>
                                <td width="40" id="dia_1"></td>
                                <td width="50" id="rollWgt_1">
                                <input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" /></td>
                                <td width="50" id="rejectQty_1"></td>
                                <td width="50" id="room_1"><input type="text" id="roomName_1" class="text_boxes"  style="width:35px" name="roomName[]"/></td>
                                <td width="50" id="rack_1"><input type="text" id="rackName_1" class="text_boxes"  style="width:35px" name="rackName[]"/></td>
                                <td width="50" id="self_1"><input type="text" id="selfName_1" class="text_boxes"  style="width:35px" name="selfName[]"/></td>
                                <td width="60" id="wideType_1"></td>
                                <td width="45" id="year_1" align="center"></td>
                                <td width="45" id="job_1"></td>
                                <td width="65" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="60" id="prodId_1"></td>
                                <td width="" id="systemId_1">
                                <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>
                                <input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value=""/>
                                <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value=""/>
                                <input type="hidden" name="deterId[]" id="deterId_1" value=""/>
                                <input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value=""/>
                                <input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value=""/>
                                <input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value=""/>
                                <input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="" />
                                <input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="" />
                                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value=""/> 
                                <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value=""/> 
                                <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>"  /> 
                                <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>" /> 
                                <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  /> 
                             </td>  
                            </tr>
                        </tbody>
                	</table>
              
              
              
              </div>
               <table width="1000" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <? 
                               echo load_submit_buttons($permission,"fnc_grey_fabric_receive",0,0,"",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	
     </div>
        
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>