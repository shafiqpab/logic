<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Print Receive Entry
				
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	25-05-2015
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
echo load_html_head_contents("Print Receive Receive Info","../../", 1, 1, $unicode);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

// order popup function here 
function openmypage(page_link,title)
{ 
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','../')
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
				$('#cbo_embel_name').val(0);
				$('#cbo_embel_type').val(0);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_receive_entry_page_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var variableSettingsReject=$('#embro_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				/*if(variableSettings!=1){ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+variableSettingsReject, "color_and_size_level", "requires/print_receive_entry_page_controller" ); 
				}
				else
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}*/	
				
				if(variableSettings==1)
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}
				else
				{
					$('#txt_receive_qty').attr('readonly','readonly');
				}
				
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qty").removeAttr("readonly");
				}
				//$('#cbo_embel_name').val(1);
				//$('#cbo_embel_name').trigger('change');
				
				show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_receive_entry_page_controller','');	
				setFilterGrid("tbl_search",-1);
				show_list_view(po_id,'show_country_listview','list_view_country','requires/print_receive_entry_page_controller','');		
				set_button_status(0, permission, 'fnc_print_receive_entry',1,0);
				release_freezing();
 			}
 			$("#cbo_company_name").attr("disabled","disabled"); 
		}
	}//end else
}//end function

//embrodery receive save here 
function fnc_print_receive_entry(operation)
{
	if(operation==4)
	{ //embro_production_variable
	
	var master_ids = ""; var total_tr=$('#tbl_search tr').length;
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				master_id = $('#mstidall_'+i).val();
				if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	//alert(master_ids);
	if(master_ids=="")
	{
		alert("Please Select At Least One Item");
		return;
	}
			
		// alert($('#txt_mst_id_all').val());
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$("#sewing_production_variable").val(), "emblishment_receive_print", "requires/print_receive_entry_page_controller" ) 
		 return;
		 
	}
	
	
	else if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_receive_date*txt_receive_qty*txt_challan','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Receive Date*Receive Quantity*Challan No')==false )
		{
			return;
		}		
		else
		{ 
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Embel Receive Date Can not Be Greater Than Current Date");
				return;
			}	
			
			freeze_window(operation);			
			var sewing_production_variable = $("#sewing_production_variable").val();
			var variableSettingsReject=$('#embro_production_variable').val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			
			var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
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
			
			if(variableSettingsReject==2)//color level
			{
				$("input[name=txtColSizeRej]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						if(k==0)
						{
							colorIDvalueRej = colorList[k]+"*"+$(this).val();
						}
						else
						{
							colorIDvalueRej += "**"+colorList[k]+"*"+$(this).val();
						}
					}
					k++;
				});
				//alert (colorIDvalueRej);return;
			}
			else if(variableSettingsReject==3)//color and size level
			{
				$("input[name=colorSizeRej]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						if(k==0)
						{
							colorIDvalueRej = colorList[k]+"*"+$(this).val();
						}
						else
						{
							colorIDvalueRej += "***"+colorList[k]+"*"+$(this).val();
						}
					}
 					k++;
				});
			}
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*embro_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_receive_date*txt_receive_qty*txt_challan*txt_remark*txt_issue_qty*txt_reject_qty*txt_cumul_receive_qty*txt_yet_to_receive*hidden_break_down_html*txt_mst_id',"../../");
  			http.open("POST","requires/print_receive_entry_page_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_receive_print_embroidery_Reply_info;
		}
	}
}
  
function fnc_receive_print_embroidery_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var variableSettingsReject=$('#embro_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000); 
		}
		if(reponse[0]==0)//insert
		{
			//alert(reponse[1]);
			
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_receive_entry_page_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','txt_receive_date,<? echo date("d-m-Y"); ?>','');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_receive_entry_page_controller" );
			 
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/print_receive_entry_page_controller" ); 
			}
			else
			{
				$("#txt_receive_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}
			
			release_freezing();
		}
		if(reponse[0]==1)//update
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_receive_entry_page_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','txt_receive_date,<? echo date("d-m-Y"); ?>','');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_receive_entry_page_controller" );
			 
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+variableSettingsReject, "color_and_size_level", "requires/print_receive_entry_page_controller" ); 
			}
			else
			{
				$("#txt_receive_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_print_receive_entry',1,0);
			release_freezing();
		}
		if(reponse[0]==2)//delete
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_receive_entry_page_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_receive_qty*txt_reject_qty*txt_challan*hidden_break_down_html*txt_remark*txt_mst_id','txt_receive_date,<? echo date("d-m-Y"); ?>','childFormReset()');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_receive_entry_page_controller" );
			 
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+variableSettingsReject, "color_and_size_level", "requires/print_receive_entry_page_controller" ); 
			}
			else
			{
				$("#txt_receive_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_print_receive_entry',1,0);
			release_freezing();
		}
 	}
} 

function childFormReset()
{
	reset_form('','','txt_receive_qty*txt_reject_qty*txt_challan*hidden_break_down_html*txt_remark*txt_receive_qty*txt_cumul_receive_qty*txt_yet_to_receive*txt_mst_id','',''); 
	$('#txt_receive_qty').attr('placeholder','')//placeholder value initilize
	$('#txt_cumul_receive_qty').attr('placeholder','')//placeholder value initilize
	$('#txt_yet_to_receive').attr('placeholder','')//placeholder value initilize
	$('#printing_production_list_view').html('')//listview container
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
	$("#txt_receive_qty").val(totalVal);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
	
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
	
	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reject_qty").val(totalValRej);
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
	$("#txt_receive_qty").val( $("#total_color").val() );
} 

function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qty").val( $("#total_color_rej").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
	
	$('#cbo_embel_name').val(0);
	$('#cbo_embel_type').val(0);
 				
	get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_receive_entry_page_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var variableSettingsReject=$('#embro_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	
	if(variableSettings==1)
	{
		$("#txt_receive_qty").removeAttr("readonly");
	}
	else
	{
		$('#txt_receive_qty').attr('readonly','readonly');
	}
	
	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qty").removeAttr("readonly");
	}
	
	show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_receive_entry_page_controller','');
	set_button_status(0, permission, 'fnc_print_receive_entry',1,0);
	release_freezing();
}

/*var selected_id = new Array; var selected_name = new Array;//txt_mst_id_all
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

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
		
		function js_set_value( str ) {
			//alert(str);
			if (str!="") str=str.split("_");
			//toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				//selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				//selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				//name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			//name = name.substr( 0, name.length - 1 );
			
			$('#txt_mst_id_all').val( id );
			//$('#hide_order_no').val( name );
		}*/
		
		// function will loop through all input tags and create
// url string from checked checkboxes

function fnc_checkbox_check22(rowNo)
{}

function fnc_checkbox_check(rowNo)
{
	var isChecked=$('#tbl_'+rowNo).is(":checked");
	var emblname=$('#emblname_'+rowNo).val();
	var mst_source= $('#productionsource_'+rowNo).val();

	if(isChecked==true)
	{
		var tot_row=$('#tbl_search tr').length-1;
		for(var i=1; i<=tot_row; i++)
		{ 
			if(i!=rowNo)
			{
				try 
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						var emblnameCurrent=$('#emblname_'+i).val();
						var productionsourceCurrent=$('#productionsource_'+i).val();
						if((emblname!=emblnameCurrent) || (mst_source!=productionsourceCurrent) )
						{
							alert("Please Select Same Emblname Or Source ");
							$('#tbl_'+rowNo).attr('checked',false);
							return;
						}
					}
				}
				catch(e) 
				{
					//got error no operation
				}
			}
		}
	}
}



function fnc_checkbox_check3(k) {
	
	
	var row_num=$('#tbl_search  tr').length-1;
		var mst_embel_name= $('#cbo_embel_name').val()*1;
		var mst_source= $('#cbo_source').val()*1;
		var all_id="";
		
		var isChecked=$('#checkedId_'+k).is(":checked");
		//if(isChecked==true)
		//{
		for (var i=1; i<=row_num; i++)
			{
				
				
				var embel_name= $('#emblname_'+i).val()*1;
				var source= $('#productionsource_'+i).val()*1;
				
					if(mst_source!=source)
					{
					alert('Same Embel Name and Source are Alowed');
					
					//$('checkedId_'+i).prop('checked', false);
					$('#checkedId_'+i).attr('checked',false);
					
					return;	
					}
				
				if (document.getElementById('checkedId_'+i).checked==true)
					{
					 document.getElementById('checkedId_'+i).value=1;
					var mst_all_id= $('#mstidall_'+i).val()*1;
					
					
					
						if(all_id=="") 
						{
							var all_id=mst_all_id;
						}
						else
						{
							 var all_id =all_id+"_"+mst_all_id;
						}
						//alert(all_id );
						$('#txt_mst_id_all').val(all_id)
					
					
					}
					else
					{
						document.getElementById('checkedId_'+i).value=0;
						//document.getElementById('checkedId_'+i).value=1;
					var mst_all_id= $('#mstidall_'+i).val()*1;
					
					
					
						if(all_id=="") 
						{
							var all_id=mst_all_id;
						}
						else
						{
							 var all_id =all_id+"_"+mst_all_id;
						}
						
						
						
						//alert(document.getElementById('checkedId_'+i).value);
					}
						
					//$('#mst_id_all_'+i).val(all_id)//$('#id_all').val(all_id)
			} //alert(all_id);
		
		
		
		//}
		
			
}


</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
    	<? echo load_freeze_divs ("../../",$permission);  ?>
        <div style="width:930px; float:left" align="center">
            <fieldset style="width:930px;">
                <legend>Production Module</legend>
                <form name="printreceiveentry_1" id="printreceiveentry_1" method="" autocomplete="off" >
                    <fieldset>
                        <table width="100%">
                             <tr>
                                 <td colspan="3" height="40" align="right">Challan No</td> 
                                 <td colspan="3">
                                   <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:188px" />
                                 </td>
                            </tr>
                            <tr> 
                                <td width="100" class="must_entry_caption" align="right">Company</td>
                                <td width="200">
                                    <? 
                                    	echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/print_receive_entry_page_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/print_receive_entry_page_controller'); get_php_form_data(this.value,'load_variable_settings_reject','requires/print_receive_entry_page_controller');" );
                                    ?>
                                    <input type="hidden" id="sewing_production_variable" />	 
                                    <input type="hidden" id="styleOrOrderWisw" />
                                    <input type="hidden" id="embro_production_variable" />	
                                </td>
                                <td width="100" align="right">Location</td>
                                <td width="200" id="location_td">
									<? 
                                    echo create_drop_down( "cbo_location", 200, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                                    ?>
                                </td>
                                <td width="100" class="must_entry_caption" align="right">Buyer</td>
                                <td width="200">
                                    <? 
                                    echo create_drop_down( "cbo_buyer_name", 200, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,0 );
                                    ?>	
                                </td>
                           </tr>
                           <tr>    
                                 <td class="must_entry_caption" align="right">Embel.Type</td>
                                 <td id="emb_type_td">
									<? 
                                    echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );  
                                    ?>
                                 </td>
                                  <td class="must_entry_caption" align="right">Source</td>
                                  <td>
                                      <? 
                                       echo create_drop_down( "cbo_source", 200, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/print_receive_entry_page_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_emb_receive', 'emb_company_td' );", 0, '1,3' );
                                      ?>
                                  </td>
                                  <td class="must_entry_caption" align="right">Embel.Company</td>
                                  <td id="emb_company_td">
                                      <? 
                                       echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "-- Select Embel.Company --", $selected, "" );
                                      ?>
                                  </td>
                           </tr>
                           <tr>    
                                 <td align="right">Floor</td>
                                 <td id="floor_td">
                                    <? 
                                    echo create_drop_down( "cbo_floor", 200, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                                    ?>
                                  </td>
                                 <td class="must_entry_caption" align="right">Embel.Name</td>
                                 <td colspan="3">
                                <? echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "",1,1 );?>
                                 </td>
                            </tr>
                            
                    </table>
                </fieldset> 
                
                <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                  <td width="35%" valign="top">
                        <fieldset>
                           <legend>New Entry</legend>
                           <table  cellpadding="0" cellspacing="2" width="350px">
                                <tr> 
                                    <td width="100" class="must_entry_caption" align="right">Order No</td>
                                    <td width="250">
                                    <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/print_receive_entry_page_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:100px " readonly />
                                    <input type="hidden" id="hidden_po_break_down_id" value="" />
                                    </td>
                                </tr>
    
                                  <tr> 
                                      <td class="must_entry_caption" align="right">Receive Date</td>
                                       <td> 
                                       <input name="txt_receive_date" id="txt_receive_date" class="datepicker"  type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  />
                                       </td>
                                  </tr>
                                  <tr>
                                       <td class="must_entry_caption" align="right">Receive Qnty</td> 
                                       <td> 
                                       <input type="text" name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                       <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                       <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                       </td>
                                   </tr>
                                   <tr>
                                        <td align="right">Reject Qnty</td>
                                        <td><input type="text" name="txt_reject_qty" id="txt_reject_qty"  class="text_boxes_numeric"  style="width:100px" readonly ></td>
                                  </tr>
                                   <tr>
                                        <td align="right">Order Qnty</td>
                                        <td><input type="text" name="txt_order_qty" id="txt_order_qty"  class="text_boxes_numeric"  style="width:100px" readonly ></td>
                                  </tr>
                                  <tr>
                                       <td align="right">Style</td> 
                                       <td>
                                       <input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px " disabled  readonly>
                                       </td>
                                 </tr>
                                  <tr>
                                       <td align="right">Item</td> 
                                       <td>
                                       <?
                                    	echo create_drop_down( "cbo_item_name", 112, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                    ?>
                                       </td>
                                 </tr>
                                 
                                 
                                  <tr>
                                    <td align="right">Country</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_country_name", 112, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                        ?> 
                                    </td>
                                 </tr>
                                  <tr>
                                       <td align="right">Remarks</td> 
                                       <td> 
                                       <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:220px" />
                                       </td>
                                 </tr>
                            </table>
                        </fieldset>
                  </td>
                  <td width="1%" valign="top"></td>
                  <td width="25%" valign="top">
                        <fieldset>
                            <legend>Display</legend>
                                 <table  cellpadding="0" cellspacing="2" width="250px" >
                                    <tr>
                                        <td width="100" align="right">Issue Qnty</td>
                                        <td width="90"> 
                                        <input type="text" name="txt_issue_qty" id="txt_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Cumul.Receive Qnty</td>
                                        <td> 
                                        <input type="text" name="txt_cumul_receive_qty" id="txt_cumul_receive_qty" class="text_boxes_numeric" style="width:80px" disabled readonly />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Yet to Receive </td>
                                        <td> 
                                        <input type="text" name="txt_yet_to_receive" id="txt_yet_to_receive" class="text_boxes_numeric" style="width:80px" disabled readonly />
                                        </td>
                                    </tr>
                                </table>
                        </fieldset>	
                    </td>
                    <td width="33%" valign="top" >
                        <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        <br />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="9" valign="middle" class="button_container">
                        <?
						$date=date('d-m-Y');
                        echo load_submit_buttons( $permission, "fnc_print_receive_entry", 0, 1,"reset_form('printreceiveentry_1','list_view_country','','txt_receive_date,".$date."','childFormReset()')",1); 
                        ?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                         
                    </td>
                	<td>&nbsp;</td>					
                </tr>
            </table>
            <div style="width:900px; margin-top:5px;"  id="printing_production_list_view" align="center"></div>
            </form>
            </fieldset>
        </div>
		<div id="list_view_country" style="width:380px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px; max-height:500px;"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//$('#cbo_embel_name').val(1);
//$('#cbo_embel_name').trigger('change');
</script>
</html>