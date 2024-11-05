<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Send on Area
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	16-10-2023
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
echo load_html_head_contents("Send on Area","../../", 1, 1, $unicode,'','');
?>
	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

$('#txt_mnd_no').live('keydown', function(e) {
	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_mnd_no=trim($('#txt_mnd_no').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(2)").text();
			if(txt_barcode_no==barcodeNo){
				
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_mnd_no').val('');
				flag=0;
				return false;
			}
		});
	
		if(flag==1)
		{
			fnc_duplicate_inspno(txt_mnd_no);
		}
	}
});

function fnc_duplicate_inspno(mnd_no)
{
	var challan_duplicate=return_ajax_request_value( mnd_no+'_'+1,"challan_duplicate_check", "requires/bundle_send_on_area_controller");
	var ex_challan_duplicate=challan_duplicate.split("_");
	if(ex_challan_duplicate[0]==2) 
	{
		var alt_str=ex_challan_duplicate[1].split("*");
		var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
		alert(al_msglc);
		$('#txt_mnd_no').val('');
		return;
	}
	else
	{
		if(ex_challan_duplicate[0]==3)
		{
			//alert("Body Part "+ex_challan_duplicate[1]+" Not Already Receive");
			//return;

		} 
		create_row(ex_challan_duplicate[3],'scan');
	}
	$('#txt_mnd_no').val('');
}

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
	var challan_duplicate=return_ajax_request_value( barcode_no+'_'+0,"challan_duplicate_check", "requires/bundle_send_on_area_controller");
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
		/*if(ex_challan_duplicate[0]==3)
		{*/
			//alert("Body Part "+ex_challan_duplicate[1]+" Not Already Receive");
			//return;

		//} 
		create_row(barcode_no,'scan');
	}
	$('#txt_barcode_no').val('');
}

function create_row(bundle_nos,vscan)
{
	//freeze_window(5);
	var tot_row=$('#tbl_details tbody tr').length; 
	//alert(bundle_nos+'_'+vscan)
	var formbarcodeno="";
	$("#tbl_details").find('tbody tr').each(function()
	{
		var bundleNo=$(this).find("td:eq(1)").text();
		var barcodeNo=$(this).find("td:eq(2)").text();
		if(formbarcodeno=="") formbarcodeno=barcodeNo; else formbarcodeno+=','+barcodeNo;
	});
	//alert(formbarcodeno);
	
	if(tot_row==0)
	{
		get_php_form_data( $("#txt_lot_ratio").val()+'_'+bundle_nos, "populate_data_from_barcode", "requires/bundle_send_on_area_controller");
		show_list_view( $("#txt_lot_ratio").val()+'_'+bundle_nos, 'show_dtls_listview_bundle', 'bundle_list_view', 'requires/bundle_send_on_area_controller','');
		$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);	
	}
	else
	{
		var row_num=$("#hidden_row_number").val();
		hidden_cutting_no=$("#txt_lot_ratio").val();
		
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"**"+vscan+"**"+hidden_cutting_no+"**"+formbarcodeno, 'populate_bundle_data', '', 'requires/bundle_send_on_area_controller');
		$('#tbl_details tbody').append(response_data);
		$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);
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
	var page_link='requires/bundle_send_on_area_controller.php?action=bundle_popup&company_id='+document.getElementById('cbo_company_name').value+'&lot_ratio='+document.getElementById('txt_lot_ratio').value+'&bundleNo='+bundleNo;
					
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../');
								
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

// for rescan=================================================================================
function openmypage_bundle_rescan(page_link,title)
{
	var bundleNo='';
	$("#tbl_details").find('tbody tr').each(function()
	{
		bundleNo+=$(this).find("td:eq(1)").text()+',';
		
	});
	
	var title='Bundle Search';
	var page_link='requires/bundle_send_on_area_controller.php?action=bundle_popup_rescan&company_id='+document.getElementById('cbo_company_name').value+'&lot_ratio='+document.getElementById('txt_lot_ratio').value+'&bundleNo='+bundleNo;
	
	emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link+'&bundleNo='+bundleNo, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../');
								
	emailwindow.onclose=function()
	{
		var theform				=this.contentDoc.forms[0];
		var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
		var hidden_lot_ratio	=this.contentDoc.getElementById("hidden_lot_ratio").value;
		$("#hidden_lot_ratio").val(hidden_lot_ratio);

		if (hidden_bundle_nos!="")
		{ 
			create_row_rescan(hidden_bundle_nos,"Browse");
		}
	}
}//end function

function create_row_rescan(bundle_nos,vscan)
{
	//freeze_window(5);
	var tot_row=$('#tbl_details tbody tr').length; 
	if(tot_row==0)
	{
		// alert(bundle_nos)
		get_php_form_data( $("#txt_lot_ratio").val()+'_'+bundle_nos,"populate_data_from_barcode", "requires/bundle_send_on_area_controller");
		show_list_view( $("#txt_lot_ratio").val()+'_'+bundle_nos, 'show_dtls_listview_bundle_rescan','bundle_list_view','requires/bundle_send_on_area_controller','');
		$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);	
	}
	else
	{
		var row_num=$("#hidden_row_number").val();
		hidden_cutting_no=$("#txt_lot_ratio").val();
		
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"**"+vscan+"**"+hidden_cutting_no, 'show_dtls_listview_bundle_rescan2', '', 'requires/bundle_send_on_area_controller');
		$('#tbl_details tbody').append(response_data);
		$("#hidden_row_number").val($("#tbl_details").find('tbody tr').length);
		
		rearrange_serial_no();
		var all_barcode_no=bundle_nos.split(",");
		$("#hidden_row_number").val((all_barcode_no.length*1+row_num*1));
	}
	$('#txt_barcode_rescan').val('');
	release_freezing();
}

$('#txt_barcode_rescan').live('keydown', function(e) {
	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_bundle_no=trim($('#txt_barcode_rescan').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(1)").attr('title');
			if(txt_bundle_no==barcodeNo){
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_barcode_rescan').val('');
				flag=0;
				return false;
			}
		});
		
		if(flag==1)
		{
			fnc_duplicate_bundle_rescan(txt_bundle_no);
		}
	}
});

function fnc_duplicate_bundle_rescan(bundle_no)
{
	var challan_duplicate=return_ajax_request_value( bundle_no+"__"+$('#cbo_company_name').val(),"qty_rescan_check", "requires/bundle_send_on_area_controller");	
		
	var ex_challan_duplicate=challan_duplicate.split("_");
	
	if(ex_challan_duplicate[0]==4)
	{
		alert("Please Scan First.");
		return;
	}
	if(ex_challan_duplicate[0]==3)
	{
		alert("No Data Found.");
		return;
	}
	if(ex_challan_duplicate[0]==2) 
	{
		var alt_str=ex_challan_duplicate[1].split("##");
		var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
		alert(al_msglc);
		$('#txt_bundle_rescan').val('');
		return;
	}
	else
	{
		// if( (ex_challan_duplicate[4]*1)>1 ) $('#cbo_line_no').val( ex_challan_duplicate[4] );
		
		if( (ex_challan_duplicate[3]*1)>1)
		{
			var page_link='requires/bundle_send_on_area_controller.php?action=bundle_popup_line_select&data='+ex_challan_duplicate[2]+"&company_id="+$('#cbo_company_name').val();
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, '', 'width=590px,height=220px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_info").value;//po id
				$('#cbo_line_no').val(hidden_bundle_nos);
				
				var tot_row=$('#tbl_details tbody tr').length; 
				//alert(tot_row)	
				if( (tot_row*1) <2)
					get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_send_date').val(),'load_mst_data','requires/bundle_send_on_area_controller');
				create_row_rescan(bundle_no,hidden_bundle_nos);
			}
		}
		else
		{
			var tot_row=$('#tbl_details tbody tr').length; 
			//alert(tot_row)	
			if( (tot_row*1) <1)
			{
				ex_challan_duplicate[4] = 0;
				get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_send_date').val(),'load_mst_data','requires/bundle_send_on_area_controller');
			}
			create_row_rescan(bundle_no,$('#cbo_line_no').val());
			 //+"__1"
		}
	}
	$('#txt_bundle_rescan').val('');
}

function load_location()
{
	var cbo_company_id = $('#cbo_company_name').val();
	var cbo_source = $('#cbo_source').val();
	var cbo_working_company = $('#cbo_working_company').val();
	if(cbo_source==1)
	{
		load_drop_down( 'requires/bundle_send_on_area_controller',cbo_working_company, 'load_drop_down_location', 'working_location_td' );
	}
	else
	{
		$("#cbo_location_name").val(0).attr("disabled",true);
		$("#cbo_floor").val(0).attr("disabled",true);
	}
}

function fnc_minusRow(id)
{
	 var barcode_no=$('#txt_barcode_'+id).val();
     var is_barcode_receive=return_global_ajax_value(barcode_no,'check_if_barcode_receive','','requires/bundle_send_on_area_controller');
     console.log(is_barcode_receive);
     if(is_barcode_receive=='')
	 {
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

function fnc_send_on_area_entry(operation)
{
	/*if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_delivery_entry_controller');
		return;
	}*/
	
	if ( form_validation('cbo_floor*txt_send_date*cbo_source*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location','Floor*Issue Date*Source*W. Company*WC. Location*LC Company*LC Location')==false )
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
			var color_size_id 	=$(this).find('input[name="txt_colorsize_id[]"]').val();
			var isRescan 		=$(this).find('input[name="isRescan[]"]').val();	
			var qty 			=$(this).find("td:eq(6)").text();
		
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
							'&isRescan_' + j + '=' + isRescan +  		
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
				
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_system_id*txt_lot_ratio*txt_lot_ratio_id*txt_job_no*txt_mnd_no*cbo_company_name*cbo_location*txt_send_date*txt_rec_challan_no*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*txt_remarks*txt_barcode_no*txt_barcode_rescan*hidden_row_number*garments_nature',"../../")+dataString;
		
		freeze_window(operation);
		http.open("POST","requires/bundle_send_on_area_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_send_on_area_entry_Reply_info;
	}
}
  
function fnc_send_on_area_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_send_on_area_entry('+ reponse[1]+')',4000); 
			 
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
			show_msg(trim(reponse[0]));
			
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_system_no').value = reponse[2];
			set_button_status(1, permission, 'fnc_send_on_area_entry',1,1);	
			//$('#txt_operator_id').attr('disabled','true');
			//$('#cbo_location').attr('disabled','true');
			release_freezing();
		}
		if(reponse[0]!=15)
		{
			release_freezing();
		}
 	}
} 

function openmypage_mndSysNo()
{
	var title = 'Mending No Selection Form';	
	var page_link = 'requires/bundle_send_on_area_controller.php?action=mnd_number_popup';
		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mst_id=this.contentDoc.getElementById("hidd_mndmst_id").value;//po id
		
		if(mst_id!="")
		{ 
			freeze_window(5);
			//reset_form('bndlsendonarea_1','list_view_country*breakdown_td_id','','','txt_send_date,<?// echo date("d-m-Y"); ?>','');

			get_php_form_data(mst_id, "populate_data_from_mnd", "requires/bundle_send_on_area_controller"); 
			if (mst_id!="")
			{ 
				var challan_duplicate=return_ajax_request_value( mst_id+'_'+1,"challan_duplicate_check", "requires/bundle_send_on_area_controller");
				var exmstdata=challan_duplicate.split("_");
				$("#tbl_details").find('tbody tr').each(function()
				{
					var bundleNo=$(this).find("td:eq(1)").text();
					var barcodeNo=$(this).find("td:eq(2)").text();
					if(txt_barcode_no==barcodeNo){
						
						alert("Bundle No: "+bundleNo+" already scan, try another one.");
						$('#txt_mnd_no').val('');
						flag=0;
						return false;
					}
				});
				//var exbarcode=exmstdata[1].split("*");
				//alert(exbarcode[0]);
				create_row(exmstdata[3],"Browse");
			}
			//show_list_view($("#txt_mnd_no").val()+'_'+$("#cbo_company_name").val(), 'show_dtls_listview_update', 'bundle_list_view', 'requires/bundle_send_on_area_controller', '');

			//var tot_row=$('#tbl_details tbody tr').length; 
			//$('#hidden_row_number').val(tot_row);
			//set_button_status(0, permission, 'fnc_send_on_area_entry',1,0);
			release_freezing();
		}
	}
	
}//end function

function openmypage_sysNo()
{
	var title = 'Challan Selection Form';	
	var page_link = 'requires/bundle_send_on_area_controller.php?action=system_number_popup';
		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mst_id=this.contentDoc.getElementById("update_mst_id").value;//po id
		if(mst_id!="")
		{ 
			freeze_window(5);
			//reset_form('bndlsendonarea_1','list_view_country*breakdown_td_id','','','txt_send_date,<? echo date("d-m-Y"); ?>','');

			get_php_form_data(mst_id, "populate_data_from_qc", "requires/bundle_send_on_area_controller"); 
			show_list_view($("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val(), 'show_dtls_listview_update', 'bundle_list_view', 'requires/bundle_send_on_area_controller', '');

			var tot_row=$('#tbl_details tbody tr').length; 
			$('#hidden_row_number').val(tot_row);
			set_button_status(1, permission, 'fnc_send_on_area_entry',1,0);
			release_freezing();
		}
	}
	
}//end function

function pageReset(){

}

function generate_report_file(data,action,page)
{
	//window.open("requires/print_embro_delivery_entry_controller.php?data=" + data+'&action='+action, true );
}

</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div style=" float:left" align="left"> 
 		
        <form name="bndlsendonarea_1" id="bndlsendonarea_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>Send on Area</legend>
                <table width="1130">
                    <tr>
                        <td align="right" colspan="5">Send On Area NO :</td>
                        <td colspan="5"> 
                        	<input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Double click to search" />
	                        <input name="txt_system_id" id="txt_system_id" type="hidden" /> 
	                        <input name="txt_lot_ratio" id="txt_lot_ratio" type="hidden" />
	                        <input name="txt_lot_ratio_id" id="txt_lot_ratio_id" type="hidden" />
	                        <input name="txt_job_no" id="txt_job_no" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td width="80" class="must_entry_caption" id="td_caption">MND. No</td>
                        <td width="130">
                        	<input name="txt_mnd_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_mndSysNo();" id="txt_mnd_no" class="text_boxes" style="width:120px" /></td>
                        <td width="80" class="must_entry_caption">LC Company</td>
                        <td width="130">
                        <? 
							$sql_com="select id,company_name from  lib_company comp  where   status_active =1 and  is_deleted=0  $company_cond  order by company_name";
							echo create_drop_down( "cbo_company_name",130, $sql_com,"id,company_name", 1, "-- Select --", $selected,"",1 );
                        ?>
                        </td>
                        <td width="80" class="must_entry_caption">LC Location</td>
                        <td width="130" id="location_td"><?=create_drop_down( "cbo_location",  130, $blank_array, "",  1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td width="80" class="must_entry_caption">Send Date</td>
                        <td width="130"><input  type="text" name="txt_send_date" id="txt_send_date" value="<?=date("d-m-Y"); ?>" class="datepicker" style="width:120px;" /></td> 
                        <td width="80">Rcvd CH. No</td>
                    	<td><input name="txt_rec_challan_no" id="txt_rec_challan_no" class="text_boxes" style="width:120px" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Source</td>
                        <td><?=create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "", 1, '1,3' ); ?></td>
                        <td class="must_entry_caption">W. Company</td>
                        <td id="knitting_com"><?=create_drop_down( "cbo_working_company", 130, $blank_array,"", 1, "-- Select --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td"><?=create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Floor</td>
                        <td  id="floor_td"><?=create_drop_down( "cbo_floor", 130, $blank_array,"", 1,"-- Select Floor --", $selected, "",1 ); ?></td> 
                    </tr>
                    <tr>
	                    <td>Remarks</td>
	                    <td colspan="9"> 
	                    	<input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" style="width:980px" /> 
	                    </td>
                    </tr>
                    <tr>
                    	<td colspan="10">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption" id="td_caption">QR Code Scan</td>
                        <td colspan="2"><input name="txt_barcode_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle();" id="txt_barcode_no" class="text_boxes" style="width:120px" /></td>
                        <td class="must_entry_caption" id="td_caption">Re-Scan QR Code</td>
                        <td colspan="2">
                        	<input name="txt_barcode_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan('requires/bundle_send_on_area_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Search Bundle For Rescan');" id="txt_barcode_rescan" class="text_boxes" style="width:120px" />
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
			</fieldset> <br />
               
            <fieldset style="">
				<legend>Bundle List</legend>
                	
                <div id="bundle_list_view">
                    <table cellpadding="0" width="1160" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="20" rowspan="2">SL</th>
                                <th width="100" rowspan="2">Bundle No</th>
                                <th width="100" rowspan="2"title="Barcode No">QR Code No</th>
                                <th width="120" rowspan="2"> G. Color</th>
                                <th width="50" rowspan="2">Size</th>
                                <th width="70" rowspan="2" >Bundle Qty. (Pcs)</th>
                                <th width="80" colspan="2">GMT No</th>
                                <th width="100" rowspan="2">Knitting Floor</th>
                                <th width="60" rowspan="2">Job No</th>
                                <th width="65" rowspan="2">Buyer</th>
                                <th width="90" rowspan="2">Order No</th>
                                <th width="100" rowspan="2">Gmts. Item</th>
                                <th width="100" rowspan="2">Country</th>
                                <th rowspan="2">-</th>
                            </tr>
                            <tr>
                                <th width="40">From</th>
                                <th width="40">To</th>
                            </tr>
                        </thead>
                    </table>
                    <div  style="width:1160px;max-height:250px;overflow-y:scroll"  align="left">    
                        <table cellpadding="0" width="1140" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                    
               	<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_send_on_area_entry", 0,1 , "reset_form('bndlsendonarea_1','bundle_list_view','', 'txt_send_date,".$date."','pageReset();')",1); 
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