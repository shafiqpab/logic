<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create print imbro issue
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	24-02-2013
Updated by 		: 	Kausar (Creating Print Report )	
Update date		: 	08-01-2014	   
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
echo load_html_head_contents("Embellishment Delivery Entry","../", 1, 1, $unicode,'','');
?>
	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;	
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
				
			if (po_id!="")
			{ 
				freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id);
				$("#cbo_country_name").val(country_id);
				
				childFormReset();//child form initialize

				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_bundle_receive_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			
				if(variableSettings==1)
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				else
				{
					$('#txt_issue_qty').attr('readonly','readonly');
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/print_embro_bundle_receive_controller" ); 
				}
				
				show_list_view(po_id,'show_country_listview','list_view_country','requires/print_embro_bundle_receive_controller','');	
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				release_freezing();
			}
		}
	}//end else
}//end function

function generate_report_file(data,action,page)
	{
		window.open("requires/print_embro_bundle_receive_controller.php?data=" + data+'&action='+action, true );
	}


function fnc_issue_print_embroidery_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_bundle_receive_controller');
		return;
	}
	
	if(operation==0 || operation==1 || operation==2)
	{
		var delivery_basis=$('#cbo_cut_panel_basis').val();
		
		if(delivery_basis==3)
		{
			if ( form_validation('cbo_company_name*cbo_embel_name*cbo_embel_type*cbo_source*txt_embl_company*txt_issue_date','Company Name*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date')==false )
			{
				return;
			}	
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Print Delivery Date Can not Be Greater Than Current Date");
				return;
			}
			
			var j=0; var dataString='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo=$(this).find("td:eq(1)").text();
				var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var gmtsitemId=$(this).find('input[name="gmtsitemId[]"]').val();
				var countryId=$(this).find('input[name="countryId[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var sizeId=$(this).find('input[name="sizeId[]"]').val();
				var qty=$(this).find('input[name="qty[]"]').val();
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				
				try 
				{
					j++;
					
					dataString+='&bundleNo_' + j + '=' + bundleNo + '&orderId_' + j + '=' + orderId + '&gmtsitemId_' + j + '=' + gmtsitemId + '&countryId_' + j + '=' + countryId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&colorSizeId_' + j + '=' + colorSizeId + '&qty_' + j + '=' + qty + '&dtlsId_' + j + '=' + dtlsId;
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
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*cbo_embel_name*cbo_embel_type*cbo_source*txt_embl_company_id*txt_location_id*txt_floor_id*txt_issue_date*txt_organic*txt_system_id*cbo_cut_panel_basis',"../")+dataString;
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Issue Quantity')==false )
			{
				return;
			}		
			else
			{
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_issue_date').val(), current_date)==false)
				{
					alert("Print Delivery Date Can not Be Greater Than Current Date");
					return;
				}	
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				
				var i=0;var colorIDvalue='';
				if(sewing_production_variable==2)//color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
				}
				else if(sewing_production_variable==3)//color and size level
				{
					$("input[name=colorSize]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
				}
				
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*txt_organic*txt_challan_no*txt_system_id*delivery_basis',"../");
			}
		}
		
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/print_embro_bundle_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
	}
}
  
function fnc_issue_print_embroidery_Reply_info()
{
 	if(http.readyState == 4) 
	{
		//release_freezing();return;
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',8000); 
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			show_msg(trim(reponse[0]));
			
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_challan_no').value = reponse[2];
			var delivery_basis=$('#cbo_cut_panel_basis').val();
			if(delivery_basis==3)
			{
				set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);
			}
			else
			{
				reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_knitting_source*cbo_location*cbo_floor*txt_organic*txt_issue_date*txt_challan_no*txt_system_id');
				show_list_view(reponse[1],'show_dtls_listview','printing_production_list_view','requires/print_embro_bundle_receive_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			}
		}
		release_freezing();
 	}
} 


function childFormReset()
{
	reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
	$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
	$("#breakdown_td_id").html('');

}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+tableName+index).val('');
 		}
	}
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_issue_qty").val(totalVal);
}

function fn_colorlevel_total(index) //for color level
{
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+index).val('');
 		}
	}
	
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_issue_qty").val( $("#total_color").val() );
} 

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	//childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
	
	get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_bundle_receive_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	
	if(variableSettings==1)
	{
		$("#txt_issue_qty").removeAttr("readonly");
	}
	else
	{
		$('#txt_issue_qty').attr('readonly','readonly');
	}
	
	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
	release_freezing();
}

function pageReset()
{
	reset_form('printembro_1','list_view_country*printing_production_list_view','','','txt_issue_date,<? echo date("d-m-Y"); ?>','');
	$('#cbo_company_name').attr('disabled','false');
	$('#tbl_details_order').show();
	$('#printing_production_list_view').show();
	$('#tbl_details_bundle').hide();
	$('#bundle_list_view').hide();
}

function load_html()
{
	var delivery_basis=$('#delivery_basis').val(); 
	$('#printing_production_list_view').val('');
	
	if(delivery_basis==3)
	{
		$('#tbl_details_order').hide();
		$('#printing_production_list_view').hide();
		$('#tbl_details_bundle').show();
		$('#tbl_details tbody tr').remove();
		$('#bundle_list_view').show();
		$('#list_view_country').hide();
	}
	else
	{
		$('#tbl_details_order').show();
		$('#printing_production_list_view').show();
		$('#tbl_details_bundle').hide();
		$('#bundle_list_view').hide();
		$('#list_view_country').show();
		childFormReset();
	}
	
	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
}

function openmypage_bundle(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
			
			if (hidden_bundle_nos!="")
			{ 
				create_row(hidden_bundle_nos);
			}
		}
	}//end else
}//end function

$('#txt_bundle_no').live('keydown', function(e) {
	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_bundle_no=$('#txt_bundle_no').val();
		create_row(txt_bundle_no);
	}
});

function create_row(bundle_nos)
{
	freeze_window(5);
	var row_num=$('#txt_tot_row').val();
	var response_data=return_global_ajax_value(bundle_nos+"**"+row_num, 'populate_bundle_data', '', 'requires/print_embro_bundle_receive_controller');
	$('#tbl_details tbody').prepend(response_data);
	var tot_row=$('#tbl_details tbody tr').length; 
	$('#txt_tot_row').val(tot_row);
	release_freezing();
}

function fn_deleteRow( rid )
{
	$("#tr_"+rid).remove();
}
// 8888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888866666666666666666666666666

function load_caption()
{
	var delivery_basis=$("#cbo_cut_panel_basis").val();
	if(delivery_basis==1)
	{
		document.getElementById('issue_challan_td').innerHTML=" Issue Challan No";
		$('#issue_challan_td').css('color','blue');
		$("#bundle_list_view").css("display","None");
		$("#order_list_view").css("display","Block");
	}
	else
	{
		document.getElementById('issue_challan_td').innerHTML="Bundle No";
		$('#issue_challan_td').css('color','blue');
		$("#bundle_list_view").css("display","Block");
		$("#order_list_view").css("display","None");
	}
}
	
	
function openmypage_issue_challan()
	{
		
		var title = 'Challan Selection Form';
		var cbo_cut_panel_basis=$('#cbo_cut_panel_basis').val();	
		var page_link = 'requires/print_embro_bundle_receive_controller.php?cbo_cut_panel_basis='+cbo_cut_panel_basis+'&action=challan_no_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var delivery_basis=$('#cbo_cut_panel_basis').val();
			if(delivery_basis==3)
			{
				var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
				if (hidden_bundle_nos!="")
				{ 
			
					get_php_form_data(hidden_bundle_nos,'load_mst_data','requires/print_embro_bundle_receive_controller');
					create_row(hidden_bundle_nos);
				}
			}
			else
			{
			var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			if(mst_id!="")
			{ 
					freeze_window(5);
					all_data=mst_id.split("_");
					$("#txt_issue_challan_no").val(all_data[6]);
					$("#cbo_company_name").val(all_data[1]);
					$("#cbo_embel_type").val(all_data[8]);
					$("#cbo_source").val(all_data[2]);
					$("#txt_embl_company").val(all_data[9]);
					$("#txt_embl_company_id").val(all_data[3]);
					$("#txt_location_name").val(all_data[10]);
					$("#txt_location_id").val(all_data[4]);
					$("#txt_floor_name").val(all_data[11]);
					$("#txt_floor_id").val(all_data[5]);
					$("#txt_organic").val(all_data[7]);
					get_php_form_data(all_data[1],'load_variable_settings','requires/print_embro_bundle_receive_controller');
					show_list_view(all_data[0],'show_dtls_listview','order_list_view','requires/print_embro_bundle_receive_controller','');
				}
				release_freezing();
			}
		}//end else
}//end function
	$('#txt_issue_challan_scan').live('keydown', function(e) {
	   
		if (e.keyCode === 13) {
			e.preventDefault();
			
		    scan_challan_no(this.value); 
		}
	});
	
 function scan_challan_no(chanal_no)
 {
	var delivery_basis=$('#cbo_cut_panel_basis').val();
	if(delivery_basis==3)
	{
		
		if (chanal_no!="")
		{ 
			get_php_form_data(chanal_no,'load_mst_data','requires/print_embro_bundle_receive_controller');
			create_row(chanal_no);
			$("#txt_issue_challan_scan").val('');
		}
	}
	else
	{
	var response_data=return_global_ajax_value(chanal_no+"**"+cbo_cut_panel_basis, 'check_bundle_data', '', 'requires/print_embro_bundle_receive_controller'); 
	var all_data=response_data.split("**");
	if(all_data[0]==0) { alert(all_data[1]);}
	$("#txt_issue_challan_no").val(all_data[5]);
	$("#cbo_company_name").val(all_data[4]);
	$("#cbo_embel_type").val(all_data[6]);
	$("#cbo_source").val(all_data[7]);
	$("#txt_embl_company").val(all_data[13]);
	$("#txt_embl_company_id").val(all_data[8]);
	$("#txt_location_name").val(all_data[12]);
	$("#txt_location_id").val(all_data[9]);
	$("#txt_floor_name").val(all_data[11]);
	$("#txt_floor_id").val(all_data[10]);
	$("#txt_organic").val(all_data[14]);
	show_list_view(all_data[3],'show_dtls_listview','order_list_view','requires/print_embro_bundle_receive_controller','');
	}
 }
	
function openmypage_sysNo()
{
	var cbo_cut_panel_basis=$('#cbo_cut_panel_basis').val();
	var title = 'Challan Selection Form';	
	var page_link = 'requires/print_embro_bundle_receive_controller.php?cbo_cut_panel_basis='+cbo_cut_panel_basis+'&action=system_no_popup';
	
	if( form_validation('cbo_cut_panel_basis','Receive Basis')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			if(mst_id!="")
			{ 
				freeze_window(5);
				//reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*cbo_cut_panel_basis');
				get_php_form_data(mst_id, "populate_data_from_challan_popup", "requires/print_embro_bundle_receive_controller" );
				
				var delivery_basis=$('#cbo_cut_panel_basis').val();
				if(delivery_basis==3)
				{
					var bundle_nos=return_global_ajax_value(mst_id, 'bundle_nos', '', 'requires/print_embro_bundle_receive_controller');
					var response_data=return_global_ajax_value(trim(bundle_nos)+"**0**"+mst_id, 'populate_bundle_data_update', '', 'requires/print_embro_bundle_receive_controller');
					$('#tbl_details tbody tr').remove();
					$('#tbl_details tbody').prepend(response_data);
					var tot_row=$('#tbl_details tbody tr').length; 
					$('#txt_tot_row').val(tot_row);
					set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,0);
					var tot_row=$('#tbl_details tbody tr').length; 
					//$('#txt_tot_row').val(tot_row);
					set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,0);
				}
				else
				{
					show_list_view(mst_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_bundle_receive_controller','');
				}
				release_freezing();
			}
		}
	}//end else
}//end function
	
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center"> 
 		<fieldset style="width:930px;">
        <legend>Production Module</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                	<tr>
                    
                        <td align="right" colspan="3">Receive No</td>
                        <td colspan="3"> 
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:167px" onDblClick="openmypage_sysNo()" placeholder="Double click to search" />
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Receive Basis</td>
                        <td>
                            <? 
								$cut_panel_basis=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number");
								echo create_drop_down( "cbo_cut_panel_basis", 180, $cut_panel_basis,'', "", '---- Select ----',"", "load_caption()",'','1,3','','' );
                            ?>
                         
                        </td>
                         <td class="must_entry_caption" id="issue_challan_td">Issue Challan No</td>
                         <td id="embel_name_td">
                            <input type="text" id="txt_issue_challan_scan" name="txt_issue_challan_scan" class="text_boxes" placeholder="Scan/Browse"  style=" width:166px;" onDblClick="openmypage_issue_challan()"/>
                         </td>
                          <td class="must_entry_caption" >Issue Challan No</td>
                         <td >
                            <input type="text" id="txt_issue_challan_no" name="txt_issue_challan_no" class="text_boxes" placeholder="Display"  style=" width:166px;"  readonly/>
                         </td>
                       
                        
                    </tr>
                    
                     <tr>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                            	echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",1 );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="delivery_basis" />
                        </td>
                         <td class="must_entry_caption">Embel. Name</td>
                         <td id="embel_name_td">
                             <? 
							 	echo create_drop_down( "cbo_embel_name", 180, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "",1,1 );
                             ?>
                         </td>
                         <td class="must_entry_caption">Embel. Type</td>
                         <td id="embro_type_td" width="170">
                             <? 
								echo create_drop_down( "cbo_embel_type", 180, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "",1 );  
                             ?>
                         </td>
                    </tr>
                    <tr>
                          <td class="must_entry_caption">Source</td>
                          <td>
                              <? 
                              	echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/print_embro_bundle_receive_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );",1, '1,3' );
                              ?>
                          </td>
                          <td class="must_entry_caption">Embel.Company</td>
                          <td id="emb_company_td">
                              <input name="txt_embl_company" id="txt_embl_company" class="text_boxes" type="text" style="width:167px"  placeholder="Dispay" readonly/>
                              <input name="txt_embl_company_id" id="txt_embl_company_id" class="text_boxes" type="hidden" style="width:167px" />
                          </td>
                          <td>Location</td>
                          <td id="location_td">
                              <input name="txt_location_name" id="txt_location_name" class="text_boxes" type="text" style="width:167px"  placeholder="Dispay" readonly/>
                              <input name="txt_location_id" id="txt_location_id" class="text_boxes" type="hidden" style="width:167px" />
                          </td>
                    </tr>
                    <tr>
                         <td>Floor</td>
                         <td id="floor_td">
                             <input name="txt_floor_name" id="txt_floor_name" class="text_boxes" type="text" style="width:167px"  placeholder="Dispay" readonly/>
                              <input name="txt_floor_id" id="txt_floor_id" class="text_boxes" type="hidden" style="width:167px" />
                         </td>
                         <td>Organic</td>
                         <td>
                            <input name="txt_organic" id="txt_organic" class="text_boxes" type="text" style="width:167px" readonly/>
                         </td>
                         <td class="must_entry_caption">Receive Date</td>
                         <td> 
                         	<input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
                         </td>
                    </tr>
                </table>
               
               
                <div id="order_list_view" style=" margin-top:30px;">
                	
                </div>
               
               
                <div id="bundle_list_view" style="display:none; margin-top:30px;">
                	<table cellpadding="0" width="920" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <th width="30">SL</th>
                            <th width="90">Bundle No</th>
                            <th width="50">Year</th>
                            <th width="60">Job No</th>
                            <th width="65">Buyer</th>
                            <th width="90">Order No</th>
                            <th width="120">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="80">Color</th>
                            <th width="70">Size</th>
                            <th width="80">Qty.</th>
                            <th></th>
                        </thead>
              		</table>
                    <div style="width:920px; max-height:250px; overflow-y:scroll" align="left">    
                    	<table cellpadding="0" width="900" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody id="">
                            </tbody>
                        </table>
                	</div>
                </div>
               	<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','pageReset();')",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="0" readonly >
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               	</table>
               	
        	</form>
        </fieldset>
    </div>

</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>