<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	
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
//echo integration_params(2);die;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bundle Issue to Knitting Floor","../../", 1, 1, $unicode,'','');
?>
	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


$('#txt_barcode_no').live('keydown', function(e) {


	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_barcode_no=trim($('#txt_barcode_no').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(2)").text();
			if(txt_barcode_no==barcodeNo){
				
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_barcode_no').val('');
				flag=0;
				return false;
			}
		});
	
		if(flag==1)
		{
			fnc_duplicate_bundle(txt_barcode_no);
		}
	}
});



function fnc_duplicate_bundle(barcode_no)
{
	var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/bundle_knitting_qc_controller");
	var ex_challan_duplicate=challan_duplicate.split("_");
	if(ex_challan_duplicate[0]==2) 
	{
		var alt_str=ex_challan_duplicate[1].split("*");
		var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
		alert(al_msglc);
		$('#txt_barcode_no').val('');
		return;
	}
	else
	{
		/*if($('#txt_lot_ratio').val())
		{
			if($('#txt_lot_ratio').val()!=ex_challan_duplicate[3]) {
				alert("Lot Retio Mixed Not Allow.This Barcode not Belong to "+$('#txt_lot_ratio').val());
				$('#txt_bundle_no').val('');
				return;
			}
		}*/
		if(ex_challan_duplicate[0]==3)
		{
			//alert("Body Part "+ex_challan_duplicate[1]+" Not Already Receive");
			//return;

		} 
		create_row(barcode_no,'scan');
	}
	$('#txt_barcode_no').val('');
}


function create_row(bundle_nos,vscan)
{
	//freeze_window(5);
	var tot_row=$('#tbl_details tbody tr').length; 
	if(tot_row==0)
	{
		//alert(bundle_nos)
			get_php_form_data(
								$("#txt_lot_ratio").val()+'_'+bundle_nos,
								"populate_data_from_barcode", 
								"requires/bundle_knitting_qc_controller"
							 );
	
			show_list_view(
							$("#txt_lot_ratio").val()+'_'+bundle_nos,
							'show_dtls_listview_bundle',
							'bundle_list_view',
							'requires/bundle_knitting_qc_controller',
							''
						  );

			$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);	
	}
	else
	{

		var row_num=$("#hidden_row_number").val();
		hidden_cutting_no=$("#txt_lot_ratio").val();
		
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"**"+vscan+"**"+hidden_cutting_no, 'populate_bundle_data', '', 'requires/bundle_knitting_qc_controller');
		$('#tbl_details tbody').append(response_data);
		$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);
		calculate_defect_qty();
		rearrange_serial_no();
		var all_barcode_no=bundle_nos.split(",");
		$("#hidden_row_number").val((all_barcode_no.length*1+row_num*1));
	}
	release_freezing();
}



function openmypage_bundle(page_link,title)
{
	
	var bundleNo='';
	$("#tbl_details").find('tbody tr').each(function()
	{
		bundleNo+=$(this).find("td:eq(1)").text()+',';
		
	});
	
	var title='Bundle Search';
	var page_link='	requires/bundle_knitting_qc_controller.php?action=bundle_popup&company_id='+
					document.getElementById('cbo_company_name').value+
					'&lot_ratio='+document.getElementById('txt_lot_ratio').value+
					'&bundleNo='+bundleNo;
					
	
	emailwindow=dhtmlmodal.open('EmailBox', 
								'iframe', 
								page_link+
								'&bundleNo='+bundleNo, 
								title, 
								'width=1050px,height=370px,center=1,resize=0,scrolling=0',
								'../')
								
	emailwindow.onclose=function()
	{
		var theform				=this.contentDoc.forms[0];
		var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
		var hidden_lot_ratio	=this.contentDoc.getElementById("hidden_lot_ratio").value;
		$("#hidden_lot_ratio").val(hidden_lot_ratio);

		if (hidden_bundle_nos!="")
		{ 
			create_row(hidden_bundle_nos,"Browse");
		}
	}
	
}//end function



function load_location()
{
	var cbo_company_id = $('#cbo_company_name').val();
	var cbo_source = $('#cbo_source').val();
	var cbo_working_company = $('#cbo_working_company').val();
	if(cbo_source==1)
	{
		load_drop_down( 'requires/bundle_knitting_qc_controller',cbo_working_company, 'load_drop_down_location', 'working_location_td' );
	}
	else
	{
		$("#cbo_location_name").val(0).attr("disabled",true);
		$("#cbo_floor").val(0).attr("disabled",true);
	}
}




function calculate_defect_qty()
{
	var total_defect_qty 	=0;
	var total_replace_qty 	=0;
	var total_qc_qty 		=0;
	var total_bundle_qty 	=0
	$("#tbl_details").find('tbody tr').each(function()
	{
		var bundle_qty=$(this).find("td:eq(5)").text()*1;
		var defect_qty=$(this).find('input[name="txt_defect_qty[]"]').val()*1;
		var replace_qty=$(this).find('input[name="txt_replace_qty[]"]').val()*1;
		var qcpass_qty=bundle_qty-defect_qty+replace_qty;
		total_qc_qty+=qcpass_qty;
		$(this).find("td:eq(8)").text(qcpass_qty);
		total_defect_qty+=defect_qty;
		total_replace_qty+=replace_qty;
		total_bundle_qty+=bundle_qty;		
	});
	$("#total_defect_qty").text(total_defect_qty);
	$("#total_replace_qty").text(total_replace_qty);
	$("#total_qc_qty").text(total_qc_qty); 
	$("#total_bundle_qty").text(total_bundle_qty);
	
}



function defect_qty_popup(row_id)
{
	var actual_infos=$("#actual_reject_"+row_id).val();
	
	var page_link='requires/bundle_knitting_qc_controller.php?action=reject_qty_popup&actual_infos='+actual_infos+'&lot_ratio='+$("#txt_lot_ratio").val();
	var title='Reject Record Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=400px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]
		var actual_infos=this.contentDoc.getElementById("actual_reject_infos").value; 
		var actual_qty=this.contentDoc.getElementById("actual_reject_qty").value;
		$("#actual_reject_"+row_id).val(actual_infos); 
	
	}
}




function openmypage_operator()
{
	var page_link='requires/bundle_knitting_qc_controller.php?action=operator_popup';
	var title='Bundle Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{				
		var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");//po id
		$("#txt_operator_id").val(employee_data[1]);
		$("#txt_operation_name").val(employee_data[2]);	
	}
	
}//end function

function openmypage_inspec_operator()
{
	var page_link='requires/bundle_knitting_qc_controller.php?action=operator_popup';
	var title='Bundle Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{				
		var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");
		$("#txt_insp_opr_id").val(employee_data[1]);
		$("#txt_insp_opr_name").val(employee_data[2]);	
	}
	
}//end function

function openmypage_inspec_supp()
{
	var page_link='requires/bundle_knitting_qc_controller.php?action=operator_popup';
	var title='Bundle Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{				
		var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");
		$("#hidden_insp_sup_id").val(employee_data[1]);
		$("#txt_insp_sup_name").val(employee_data[2]);	
	}
	
}//end function

function fnc_minusRow(id)
{
	 var barcode_no=$('#txt_barcode_'+id).val();
     var is_barcode_receive=return_global_ajax_value(barcode_no,'check_if_barcode_receive','','requires/bundle_knitting_qc_controller');
     console.log(is_barcode_receive);
     if(is_barcode_receive=='')
             {
                $("#txt_defect_qty_"+id).closest('tr').remove();
				calculate_defect_qty();
				rearrange_serial_no(); 
             }
             else
             {
                alert('Remove not allowed. Barcode Already Issue .');
                return;
             }  
	
}

function rearrange_serial_no()
{
	var i=1;
	$("#tbl_details").find('tbody tr').each(function()
	{
		$(this).find("td:eq(0)").text(i);
		i++;
	});
}



function fnc_issue_print_embroidery_entry(operation)
{
		
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_delivery_entry_controller');
		return;
	}
	
	if ( form_validation('cbo_floor*txt_qc_date*cbo_source*txt_insp_opr_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*txt_insp_sup_name',
		'Floor*QC Date*Source*Insp Opr ID*W. Company*WC. Location*LC Company*LC Location*Insp Sup Name')==false )
	{
		return;
	}
	
	
	if(operation==0 || operation==1 || operation==2)
	{
				
		
			var j 			=0; 
			var dataString 	='';

			$("#tbl_details").find('tbody tr').each(function()
			{
				//var machine_no 		=$(this).find('input[name="txt_machine_no[]"]').val();
				var bundleNo 		=$(this).find("td:eq(1)").text();
				var barcodeNo 		=$(this).find("td:eq(2)").text();
				var colorId 		=$(this).find('input[name="txt_color_id[]"]').val();
				var sizeId 			=$(this).find('input[name="txt_size_id[]"]').val();
				var countryId 		=$(this).find('input[name="txt_country_id[]"]').val();
				var orderId 		=$(this).find('input[name="txt_order_id[]"]').val();
				var gmtsitemId 		=$(this).find('input[name="txt_gmt_item_id[]"]').val();
				var defect_qty 		=$(this).find('input[name="txt_defect_qty[]"]').val();			
				var replace_qty 	=$(this).find('input[name="txt_replace_qty[]"]').val();
				var color_size_id 	=$(this).find('input[name="txt_colorsize_id[]"]').val();
				var actual_reject 	=$(this).find('input[name="actual_reject[]"]').val();	
				var qty 			=$(this).find("td:eq(5)").text();
				var qcpass_qty 		=$(this).find("td:eq(8)").text();		
				
				try 
				{
					j++;
					dataString+='&bundleNo_' + j + '=' + bundleNo + 
								'&gmtsitemId_' + j + '=' + gmtsitemId + 
								'&countryId_' + j + '=' + countryId + 
								'&colorId_' + j + '=' + colorId + 
								'&colorSizeId_' + j + '=' + color_size_id + 
								'&orderId_' + j + '=' + orderId + 
								'&sizeId_' + j + '=' + sizeId  + 
								'&qty_' + j + '=' + qty + 
								'&qcpass_qty_' + j + '=' + qcpass_qty + 
								'&defect_qty_' + j + '=' + defect_qty + 
								'&replace_qty_' + j + '=' + replace_qty + 
								'&actual_reject_' + j + '=' + actual_reject +  		
								'&barcodeNo_' + j + '=' + barcodeNo; 							
				}
				catch(e) 
				{
					alert("There is some problem.");
					return;
				}
			});
			
			if(j<1)
			{
				alert('No data Found.');
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+
							'&tot_row='+j+
							get_submitted_data_string('cbo_floor*txt_qc_date*cbo_source*txt_insp_opr_id*txt_operator_id*hidden_insp_sup_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*txt_system_no*txt_system_id*txt_lot_ratio*txt_job_no*txt_remarks*garments_nature',"../../")+dataString;
		
		freeze_window(operation);
		http.open("POST","requires/bundle_knitting_qc_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_issue_to_knitting_Reply_info;
	}
}
  
function fnc_issue_to_knitting_Reply_info()
{
 	if(http.readyState == 4) 
	{		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',4000); 
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
			show_msg(trim(reponse[0]));
			
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_system_no').value = reponse[2];
			set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);	
			//$('#txt_operator_id').attr('disabled','true');
			//$('#cbo_location').attr('disabled','true');

		}
		if(reponse[0]!=15)
		{
		  release_freezing();
		}
 	}
} 


function openmypage_sysNo()
{
	
	var title = 'Challan Selection Form';	
	var page_link = 'requires/bundle_knitting_qc_controller.php?action=system_number_popup';
		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mst_id=this.contentDoc.getElementById("update_mst_id").value;//po id
		if(mst_id!="")
		{ 
			freeze_window(5);
			//reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','');

			get_php_form_data(mst_id, "populate_data_from_qc", "requires/bundle_knitting_qc_controller"); 
			show_list_view($("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val(), 'show_dtls_listview_update', 'bundle_list_view', 'requires/bundle_knitting_qc_controller', '');

			var tot_row=$('#tbl_details tbody tr').length; 
			$('#hidden_row_number').val(tot_row);
			set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,0);
			release_freezing();
		}
	}
	
}//end function

function pageReset(){

	
}



function generate_report_file(data,action,page)
{
	window.open("requires/print_embro_delivery_entry_controller.php?data=" + data+'&action='+action, true );
}





</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div style=" float:left" align="left"> 
 		
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>Master Part</legend>
                <table width="1130">
                    <tr>
                        <td align="right" colspan="5">System ID</td>
                        <td colspan="5"> 
                        	<input name="txt_system_no"id="txt_system_no"class="text_boxes"type="text"style="width:120px"onDblClick="openmypage_sysNo();"placeholder="Double click to search" />
                        
	                        <input  
	                        	name="txt_system_id"  
	                        	id="txt_system_id"  
	                        	type="hidden" /> 
	                      
	                        <input 
	                        	name="txt_lot_ratio"  
	                        	id="txt_lot_ratio"  
	                        	type="hidden" />
	                        <input  
	                        	name="txt_lot_ratio_id"  
	                        	id="txt_lot_ratio_id"  
	                        	type="hidden" />
	                        <input  
	                        	name="txt_job_no"  
	                        	id="txt_job_no"  
	                        	type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td width="80" class="must_entry_caption" id="td_caption">Barcode Scan</td>
                        <td width="130" >
                        	<input  
                        		name="txt_barcode_no"  
                        		placeholder="Browse/Write/Scan"  
                        		onDblClick="openmypage_bundle()"  	
                        		id="txt_barcode_no"  
                        		class="text_boxes" 
                        		style="width:120px" />
                        </td>
                        <td width="100" class="must_entry_caption" id="td_caption">Re-Scan Barcode</td>
                        <td width="130">
                        	<input  name="txt_barcode_rescan"  
                        		placeholder="Browse/Write/Scan"  
                        		onDblClick="openmypage_bundle_rescan()"   
                        		id="txt_barcode_rescan"  
                        		class="text_boxes"  
                        		style="width:120px" />
                        </td>

                        <td width="80">OP ID</td>
                        <td width="130">
                        	<input name="txt_operator_id" 
                        		id="txt_operator_id"  
                        		class="text_boxes"  
                        		type="text"  
                        		style="width:120px" 
                        		disabled />
                        </td>

                        <td width="90">Operator Name</td>
                        <td width="130">
                        	<input  
                        		name="txt_operator_name"  
                        		id="txt_operator_name" 
                        		class="text_boxes"  
                        		type="text"  
                        		style="width:120px"  
                        		disabled /> 
                        </td>
                        <td class="must_entry_caption">LC Company</td>
                        <td>
                        <? 
							$sql_com="select id,company_name from  lib_company comp  where   status_active =1 and  is_deleted=0  $company_cond  order by company_name";
							echo create_drop_down( "cbo_company_name",130, $sql_com,"id,company_name", 1, "-- Select --", $selected,"",1 );
                        ?>
                        </td>
                        
                    </tr>
                    <tr>
                        
                        <td class="must_entry_caption">LC Location</td>
                        <td id="location_td">
                        <? 
							echo create_drop_down( "cbo_location",  130, $blank_array, "",  1, "-- Select Location --", $selected, "",1 );
                        ?>
                        </td>
                        <td class="must_entry_caption">Source</td>
                        <td>
                        <? 
							echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "", 1, '1,3' );
                        ?>
                        </td>
                        <td class="must_entry_caption">W. Company</td>
                        <td id="knitting_com">
                        <? 
							echo create_drop_down( "cbo_working_company", 130, $blank_array,"", 1, "-- Select --", $selected, "",1 );
                        ?>
                        </td>
                        <td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td">
                        <? 
							echo create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",1 );
                        ?>
                        </td>
                        <td width="80" class="must_entry_caption">Floor</td>
                        <td width="130" id="floor_td">
                        <? 
							echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1,"-- Select Floor --", $selected, "",1 );
                        ?>
                        </td> 
                    </tr>
                    <tr>
                    	                      
                        <td class="must_entry_caption">QC Date</td>
                        <td width="110">
                        	<input  type="text"  
                        		name="txt_qc_date"   
                        		id="txt_qc_date" 
                        		value="<? echo date("d-m-Y")?>"   
                        		class="datepicker"  
                        		style="width:120px;"  />
                        </td> 
                         
                        <td width="80" class="must_entry_caption">Insp Opr ID</td>
                        <td width="110">   
                        	<input  
                        		ondblclick="openmypage_inspec_operator()" 
                                onKeyDown="fnc_employee_id_scanner(event,this.value,'txt_insp_opr_id*txt_insp_opr_name')" 
                        		type="text"   
                        		name="txt_insp_opr_id"   
                        		id="txt_insp_opr_id"   
                        		value=""   
                        		class="text_boxes"   
                        		style="width:120px;"  
                        		placeholder="Browse/Write/Scan" />
                        </td> 
                        <td width="80" class="must_entry_caption">Insp Opr Name</td>
                        <td width="110">   
                        	<input 

                        		type="text"   
                        		name="txt_insp_opr_name"   
                        		id="txt_insp_opr_name"   
                        		value=""   
                        		class="text_boxes"   
                        		style="width:120px;" 
                        		disabled  />  
                        </td> 
                        <td width="80" class="must_entry_caption">Insp Sup Name</td>
                        <td width="110">
                        	<input
                        		ondblclick="openmypage_inspec_supp()"   
                        		type="text"   
                        		name="txt_insp_sup_name"   
                        		id="txt_insp_sup_name"   
                        		value=""   
                        		class="text_boxes"   
                        		style="width:120px;"    
                        		placeholder="Browse"/>

                        	<input name="hidden_insp_sup_id" 
                        		id="hidden_insp_sup_id"  
                        		class="text_boxes"  
                        		type="hidden"
                        		disabled />
                      	</td> 
                    </tr>
                    <tr>
                    <td class="must_entry_caption">Remarks</td>
                    <td colspan="9"> 
                    	<input 
                    		name="txt_remarks" 
                    		id="txt_remarks" 
                    		class="text_boxes" 
                    		type="text" 
                    		style="width:1030px" /> 
                    </td>
                    </tr>
                </table>
			</fieldset> <br />
               
            <fieldset style="">
				<legend>Bundle List</legend>
                	<fieldset style="">
                    	<div id="bundle_list_view">
	                        <table cellpadding="0" width="1210" cellspacing="0" border="1" class="rpt_table" rules="all">
	                            
	                            <thead>
	                                <th width="30">SL</th>
	                                <th width="100">Bundle No</th>
	                                <th width="100">Barcode No</th>
	                                <th width="60">G. Color</th>
	                                <th width="70">Size</th>
	                                <th width="65">Bundle Qty. (Pcs)</th>
	                                <th width="65">Defect Qty (Pcs)</th>
	                                <th width="65">Replace Qty (Pcs)</th>
	                                <th width="65">QC Qty. (Pcs)</th>
	                                <th width="50">Job Year</th>
	                                <th width="60">Job No</th>
	                                <th width="65">Buyer</th>
	                                <th width="65">Style No</th>
	                                <th width="90">Order No</th>
	                                <th width="120">Gmts. Item</th>
	                                <th width="100">Country</th>
	                                <th></th>
	                            </thead>
	                        </table>
	                        <div  style="width:1230px;max-height:250px;overflow-y:scroll"  align="left">    
	                            <table cellpadding="0" width="1210" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
	                                <tbody>
	                                   
	                                </tbody>
	                            </table>
	                        </div>
	                    </div>
                	</fieldset>
                    
               		<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 , "reset_form('printembro_1','bundle_list_view','', 'txt_qc_date,".$date."','pageReset();')",1); 
                            ?>
                          	<input type="hidden"  name="hidden_row_number" id="hidden_row_number"> 
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               	</table>
            </fieldset>
        </form>
    </div>
	<div  id="list_view_country"   style="	width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>