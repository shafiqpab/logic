<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Embellishment Issue
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-08-2019
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Embellishment Issue","../", 1, 1, $unicode,'','');
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var smp_id=this.contentDoc.getElementById("selected_id").value;//requisition id
				
		if (smp_id!="")
		{
			$("#txt_sample_requisition_id").val(smp_id);
			get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_embellishment_issue_controller" );
			$("#cbo_company_name").attr('disabled','disabled');
			$('#mst_update_id').val('');
			
			show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_embellishment_issue_controller','');		
			
			show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_embellishment_issue_controller','');
			
			set_button_status(0, permission, 'fnc_sample_embellishment_entry',1,0);
			release_freezing();
		}
	}
} 



function put_sample_item_data(sample_dtls_part_tbl_id,smp_id,gmts,color_id)
{
			
	var req_id=$("#hidden_requisition_id").val();
	var status_id=$("#cbo_status_id").val()*1;
	var embel_name=$("#cbo_embel_name").val();
	var embel_type=$("#cbo_embel_type").val();
	
	if (form_validation('cbo_company_name*txt_sample_requisition_id*cbo_embellishment_company*txt_embellishment_date*cbo_embel_name*cbo_embel_type','Company Name*Sample Requisition ID*Embellishment Company*Embellish Date*Sample Name*Embel. Name*Embel. Type')==false)
	{
		return;
	}	
	//alert(color_id);
	//$('#cbo_embel_name').val('');
	freeze_window(5);
 	get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts+'**'+embel_name+'**'+'single'+'**'+status_id+'**'+status_id+'**'+color_id+'**'+embel_type, "color_and_size_level", "requires/sample_embellishment_issue_controller" ); 
    set_button_status(0, permission, 'fnc_sample_embellishment_entry',1,0);
	release_freezing();
}
 
function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeQty_"+tableName+index).val();
	var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
	var totalRow = $("#table_"+tableName+" tr").length;
	math_operation( "total_"+tableName, "colSizeQty_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	
	var totalVal = 0;
	$("input[name=colSizeQty]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_embellishment_qty").val(totalVal);
	/*if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )	
		{
			$("#txt_embellishment_qty").val('');
			$("#colSizeQty_"+tableName+index).val('');
		}
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
			$("#txt_embellishment_qty").val('');
 		}
	}*/
}
function check_requisition_embellishment_process_Not(data) //Not used
{
	var res=data.split('__');	
	$('#breakdown_td_id').html('');
	var emb_type = return_global_ajax_value( res[0]+'__'+$('#hidden_requisition_id').val()+'__'+$('#cbo_sample_name').val()+'__'+$('#cbo_item_name').val(), 'embellishment_type_as_per_req', '', 'requires/sample_embellishment_issue_controller');
	var emb_id = return_global_ajax_value( $('#hidden_requisition_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#cbo_item_name').val(), 'embellishment_id_as_per_req', '', 'requires/sample_embellishment_issue_controller');
	cbo_status_id=$("#cbo_status_id").val();
	if(trim(emb_id).length==1)
	{
		get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'single'+'**'+cbo_status_id+'**'+cbo_status_id,'color_and_size_level', 'requires/sample_embellishment_issue_controller' );
		if($('#breakdown_td_id').html()=='')
		{
			alert("No cutting info found for this sample and garments item");
		}
	}
	else 
	{
		var r=trim(emb_id).split(",");
		var emb = return_global_ajax_value( $('#hidden_requisition_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#cbo_item_name').val(), 'embellishment_name_as_per_req', '', 'requires/sample_embellishment_issue_controller');
		
		var position=r.indexOf(res[0]); 
		if(position==0)
		{
			get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'single'+'**'+r[0]+'**'+cbo_status_id,'color_and_size_level', 'requires/sample_embellishment_issue_controller' );
			if($('#breakdown_td_id').html()=='')
			{
				alert("No Cutting info found for this sample and garments item");
			}
			// here single means position one and goes on.....
		}
		else if(position==1)
		{
			get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'position_one'+'**'+r[0]+'**'+cbo_status_id,'color_and_size_level', 'requires/sample_embellishment_issue_controller' );
			
			if($('#breakdown_td_id').html()=='')
			{
				//$('#breakdown_td_id').html('please follow requisition embellishment prcoess sequence').css("color","crimson"); 
				alert("follow requisition embellishment prcoess sequence.."+emb.trim());
			}
		}
		else if(position==2)
		{
			get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'position_two'+'**'+r[1]+'**'+cbo_status_id,'color_and_size_level', 'requires/sample_embellishment_issue_controller' );
			
			if($('#breakdown_td_id').html()=='')
			{
				//$('#breakdown_td_id').html('please follow requisition embellishment prcoess sequence').css("color","crimson"); 
				alert("follow requisition embellishment prcoess sequence.."+emb.trim());
			}
		}
	}
	$("#cbo_embel_type").val(emb_type.trim());
	if(emb_id.trim()=='')
	{
		alert("no embellishment found for this sample name and item in requisition");
		//$('#breakdown_td_id').html('');
	}
}

function fnc_sample_embellishment_entry(operation)
{
	if(operation==0 && $('#txt_total_cutting_qty').val()==0)
	{
		alert("Total cutting value zero");
	}
	var embel_name = $('#cbo_embel_name').val();
	if(operation==4)
	{	
	    var check_print_ids="";
		var i=1;
		//var check_id=$("#sample_detail_tbl tr").length();
		$("#tbl_list_search tr").each(function(){
			//alert($("#check_for_print_"+i).is(":checked"));
			if($("#check_for_print_"+i).is(":checked")){
				if(check_print_ids=="") check_print_ids  = $("#check_for_print_"+i).val() ;
				else
				check_print_ids += ','+$("#check_for_print_"+i).val();				
			}
			i++;
		});
		//alert(check_print_ids);
		if(check_print_ids=="")
		{
			alert("Select at least one checkbox"); 
			$("#check_for_print_"+i).focus();
			return;
		}
		else
		{		
		    //var report_title=$( "div.form_caption" ).html();
		    print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val()+'*'+$("#cbo_item_name").val()+'*'+check_print_ids+'*'+$("#dtls_update_id").val()+'*'+$("#hidden_requisition_id").val()+'*'+$("#hidden_sample_dtls_tbl_id").val(), "embellishment_print", "requires/sample_embellishment_issue_controller" ) 
		    return;
		}
	}
	 else if(operation==5)
	{	
	    var check_print_ids="";
		var i=1;
		//var check_id=$("#sample_detail_tbl tr").length();
		$("#tbl_list_search tr").each(function(){
			//alert($("#check_for_print_"+i).is(":checked"));
			if($("#check_for_print_"+i).is(":checked")){
				if(check_print_ids=="") check_print_ids  = $("#check_for_print_"+i).val() ;
				else
				check_print_ids += ','+$("#check_for_print_"+i).val();				
			}
			i++;
		});
		//alert(check_print_ids);
		if(check_print_ids=="")
		{
			alert("Select at least one checkbox"); 
			$("#check_for_print_"+i).focus();
			return;
		}
		else
		{		
		    //var report_title=$( "div.form_caption" ).html();
		    print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val()+'*'+$("#cbo_item_name").val()+'*'+check_print_ids+'*'+$("#dtls_update_id").val()+'*'+$("#hidden_requisition_id").val()+'*'+$("#hidden_sample_dtls_tbl_id").val()+'*'+embel_name, "embellishment_print2", "requires/sample_embellishment_issue_controller" ) 
		    return;
		}
	}

	else if(operation==6)
	{	
	    var check_print_ids="";
		var i=1;
		//var check_id=$("#sample_detail_tbl tr").length();
		$("#tbl_list_search tr").each(function(){
			//alert($("#check_for_print_"+i).is(":checked"));
			if($("#check_for_print_"+i).is(":checked")){
				if(check_print_ids=="") check_print_ids  = $("#check_for_print_"+i).val() ;
				else
				check_print_ids += ','+$("#check_for_print_"+i).val();				
			}
			i++;
		});
		//alert(check_print_ids);
		if(check_print_ids=="")
		{
			alert("Select at least one checkbox"); 
			$("#check_for_print_"+i).focus();
			return;
		}
		else
		{		
		    //var report_title=$( "div.form_caption" ).html();
		    print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val()+'*'+$("#cbo_item_name").val()+'*'+check_print_ids+'*'+$("#dtls_update_id").val()+'*'+$("#hidden_requisition_id").val()+'*'+$("#hidden_sample_dtls_tbl_id").val()+'*'+embel_name, "embellishment_print3", "requires/sample_embellishment_issue_controller" ) 
		    return;
		}
	}
	else if(operation==0 || operation==1 || operation==2)
	{	
 		if (form_validation('cbo_company_name*txt_sample_requisition_id*cbo_embellishment_company*txt_embellishment_date*cbo_sample_name*txt_embellishment_qty*cbo_embel_name*cbo_embel_type','Company Name*Sample Requisition ID*Embellishment Company*Embellish Date*Sample Name*Issue Quantity*Embel. Name*Embel. Type')==false)
		{
			return;
		}		
		else
		{
 			var colorList = ($('#hidden_colorSizeID').val()).split(",");
 			//alert(colorList);return;
 			var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
			
			$("input[name=colSizeQty]").each(function(index, element) {
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
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][338]);?>')
		{
		    if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][338]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][338]);?>')==false)
		    {
		      release_freezing();
		      return;
		    }
		} 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*txt_sample_requisition_id*cbo_buyer_name*txt_style_no*cbo_item_name*txt_sample_qty*cbo_source*cbo_embellishment_company*cbo_location*cbo_floor*cbo_sample_name*txt_embellishment_date*txt_embellishment_qty*txt_remark*hidden_sample_dtls_tbl_id*hidden_requisition_id*cbo_embel_name*cbo_embel_type*txt_total_cutting_qty*cbo_status_id',"../"); 
			 // alert(data);return;
			freeze_window(operation);
  			http.open("POST","requires/sample_embellishment_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_embellishment_entry_Reply_info;
		}
	}
}
  
function fnc_sample_embellishment_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');		 
		
		if(response[0]==0 || response[0]==1)//insert update response;
		{
			show_msg(trim(response[0]));
			show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_embellishment_issue_controller','');
			$('#mst_update_id').val(response[1]);
			$('#txt_mst_issue_id').val(response[1]);
  			$('#breakdown_td_id').html('');
  			var val =return_global_ajax_value( response[5]+"__"+response[1]+"__"+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val()+"__"+$('#cbo_embel_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_embellishment_issue_controller');
  			var prod_qty=$("#txt_sample_qty").val();
  			var total_cut=$("#txt_total_cutting_qty").val();
  			$("#txt_cumul_embel_qty").val(val);
  			$("#txt_yet_to_embel").val(total_cut*1 - val*1);
   			childFormReset();
			set_button_status(0, permission, 'fnc_sample_embellishment_entry',1,0);
   			$("#txt_embellishment_date").datepicker().datepicker("setDate", new Date());
		}
		/* else if(response[0]==2)//delete reponse;
		{
			show_msg(trim(response[0]));
			set_button_status(0, permission, 'fnc_sample_embellishment_entry',1,0);
		} */
		else if(response[0]==2)//delete reponse;
		{
			show_msg(trim(response[0]));
			show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_embellishment_issue_controller','');
			if(response[6]==1)
			{
			childFormReset(1);
			}
			set_button_status(0, permission, 'fnc_sample_embellishment_entry',1,0);
		}
		if(response[0]==13) //Cutting Validation
		{
			alert(response[1]);
			release_freezing();
			return;
		}
		release_freezing();
 	}
} 

function release_print_buuton(element)
{
	set_button_status(1, permission, 'fnc_sample_embellishment_entry',1,0);
}
function fnc_td_change(type)
{
	//alert(type);
	$('#breakdown_td_id').html('');
	if(type==1 || type==0)
	{
		$('#dynamic_cut_qty').html('Total Cutting Qty')
	}
	else
	{
		$('#dynamic_cut_qty').html('Total Sewing Output Qty')
	}
}
function childFormReset()
{
	reset_form('','','txt_embellishment_date*txt_embellishment_qty*txt_remark','','');
}
 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Sample Production</legend>  
			<form name="sampleEmbellishment_1" id="sampleEmbellishment_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="120" class="must_entry_caption">Company</td>
                            <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_embellishment_issue_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
                            <td width="120">Source</td>
                            <td width="160"><? echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sample_embellishment_issue_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );", 0, '1,3' ); ?></td>
                            <td width="120" class="must_entry_caption">Embel. Company</td>
                            <td id="sew_company_td"><? echo create_drop_down( "cbo_embellishment_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "" ); ?></td>
                        </tr>
                        <tr>  
                            <td class="must_entry_caption">Sample Req. No</td>
                            <td>
                                <input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id" onDblClick="openmypage('requires/sample_embellishment_issue_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID')"  class="text_boxes" style="width:140px " readonly>
                                <input type="hidden" id="mst_update_id" />	 
                                <input type="hidden" id="hidden_requisition_id" />	
								<input type="hidden" id="txt_challan_no" /> 
                            </td>
                            <td>Buyer</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>  
                            <td>Style</td>
                            <td><input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:140px " disabled readonly></td>
                        </tr>
                        <tr>  
                            <td>Prod Qty</td>
                            <td><input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes"  style="width:140px " disabled readonly></td>
                            <td>Location</td>
                            <td id="location_td"><? echo create_drop_down( "cbo_location", 150,$blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                            <td>Floor</td>
                            <td id="floor_td"><? $floor_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  and production_process in (5) order by floor_name", "id", "floor_name"  );
                            
                            	echo create_drop_down( "cbo_floor", 150, $floor_library,"", 1, "-- Select Floor --", $selected, "" ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Embel.Name</td>
                            <td id="embel_name_td"><? echo create_drop_down( "cbo_embel_name", 150, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "",'','','','','99' ); ?></td>
                            <td class="must_entry_caption">Embel.Type</td>
                            <td id="emb_type_td"><? echo create_drop_down( "cbo_embel_type", 150, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td>&nbsp;</td>
                            <td id="Req_color_td" style="color:#F00; font-size:20px">&nbsp;</td>
                        </tr>
                    </table>
                </fieldset>
                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td width="35%" valign="top">
                            <fieldset>
                                <legend>New Entry</legend>
                                <table  cellpadding="0" cellspacing="1" width="100%">
                                  <tr>
                                        <td>Status</td>
                                        <td title="Before Sewing means From Cutting And After Sewing Means From Sewing Out"><? echo create_drop_down( "cbo_status_id", 150, $sample_statusArr,"", 1, "-- Select Status --", $selected, "fnc_td_change(this.value)",0,0 );?></td> 
                                </tr>
                                    
                                    <tr>
                                        <td width="130" class="must_entry_caption">Sample Name<input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value=""></td> 
                                        <td><? echo create_drop_down( "cbo_sample_name", 150,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );	?></td> 
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Name</td>
                                        <td><? echo create_drop_down( "cbo_item_name", 150, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	?></td> 
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Issue Date</td>
                                        <td><input name="txt_embellishment_date" id="txt_embellishment_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:140px;" /></td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Issue Quantity</td> 
                                        <td>
                                        	<input name="txt_embellishment_qty" id="txt_embellishment_qty" class="text_boxes_numeric" style="width:70px" readonly >
                                        	<input type="hidden" id="hidden_colorSizeID" value=""/>
                                              Issue Id&nbsp;<input name="txt_mst_issue_id" id="txt_mst_issue_id" class="text_boxes_numeric" style="width:50px" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Remarks</td>
                                        <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:140px;" /></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">&nbsp;</td>
                       	<td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%">
                                    <tr>
                                        <td width="110" id="dynamic_cut_qty">Total Cutting Qty</td>
                                        <td><input type="text" name="txt_total_cutting_qty" id="txt_total_cutting_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td>Cumul. Issue Qty</td>
                                        <td><input type="text" name="txt_cumul_embel_qty" id="txt_cumul_embel_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td>Yet to Issue</td>
                                        <td><input type="text" name="txt_yet_to_embel" id="txt_yet_to_embel" class="text_boxes_numeric" style="width:80px" / readonly disabled ></td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="40%" valign="top" >
                        	<div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>                         
                    </tr>
                    <tr>
                        <td align="center" colspan="3" valign="middle" class="button_container">
							<?
                            $date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_sample_embellishment_entry", 0,1,"reset_form('sampleEmbellishment_1', 'list_view_country', '', 'txt_embellishment_date,".$date."','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="dtls_update_id" id="dtls_update_id" readonly />
							<input type="button" value="Print2" name="print" onClick="fnc_sample_embellishment_entry(5)" style="width:80px" id="Print2" class="formbutton">
							<input type="button" value="Print3" name="print" onClick="fnc_sample_embellishment_entry(6)" style="width:80px" id="Print3" class="formbutton">
                        </td>
                    </tr>
                </table>
            </form>
        	</fieldset>
            <div style="float:left;" id="list_view_container"></div>
        </div>
		<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>