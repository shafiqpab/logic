<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Wash/Dyeing Entry

Functionality	:
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	08-04-2017
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
echo load_html_head_contents("Wash/Dyeing Info","../", 1, 1, $unicode,'','');

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


function openmypage(page_link,title)
{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value.split('_');//requisition id

			if (smp_id[0]!="")
			{
 				$("#txt_sample_requisition_id").val(smp_id[1]);
 				$("#hidden_requisition_id").val(smp_id[0]);
				get_php_form_data(smp_id[0], "populate_data_from_search_popup", "requires/sample_wash_or_dyeing_entry_controller" );

				show_list_view(smp_id[0],'show_sample_item_listview','list_view_country','requires/sample_wash_or_dyeing_entry_controller','');

				show_list_view(smp_id[0],'show_dtls_listview','list_view_container','requires/sample_wash_or_dyeing_entry_controller','');

				set_button_status(0, permission, 'fnc_sample_wash_or_dyeing_entry',1,0);
				$("#cbo_company_name").attr("disabled",true);
				release_freezing();
			}
		}

}
function check_requisition_embellishment_process(data)
{
	var res=data.split('__');
	$('#breakdown_td_id').html('');
	var emb_type = return_global_ajax_value( res[0]+'__'+$('#hidden_requisition_id').val()+'__'+$('#cbo_sample_name').val()+'__'+$('#cbo_item_name').val(), 'embellishment_type_as_per_req', '', 'requires/sample_wash_or_dyeing_entry_controller');
	var emb_id = return_global_ajax_value( $('#hidden_requisition_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#cbo_item_name').val(), 'embellishment_id_as_per_req', '', 'requires/sample_wash_or_dyeing_entry_controller');
	//alert(emb_type+" abc  "+emb_id);return;

   if(trim(emb_id).length==1)
   {
		get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'single','color_and_size_level', 'requires/sample_wash_or_dyeing_entry_controller' );
		if($('#breakdown_td_id').html()=='')
   	  	{
    	  		 alert("No sewing info found for this sample and garments item");
   	  	}
		//$("#dynamic_cut_qty").html('Total Cutting Qty');

    }
   else
   {
   	  var r=trim(emb_id).split(",");
   	  var emb = return_global_ajax_value( $('#hidden_requisition_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#cbo_item_name').val(), 'embellishment_name_as_per_req', '', 'requires/sample_wash_or_dyeing_entry_controller');

      var position=r.indexOf(res[0]);
   	  if(position==0)
   	  {
   	  	get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'single'+'**'+r[0],'color_and_size_level', 'requires/sample_wash_or_dyeing_entry_controller' );
   	  	if($('#breakdown_td_id').html()=='')
   	  	{
    	  		 alert("No sewing info found for this sample and garments item");
   	  	}
   	  	// here single means position one and goes on.....
   	  }
   	  else
   	  {
   	  	get_php_form_data($('#hidden_sample_dtls_tbl_id').val()+'**'+$('#cbo_sample_name').val()+'**'+$('#hidden_requisition_id').val()+'**'+$('#cbo_item_name').val()+'**'+res[0]+'**'+'position_one'+'**'+r[0],'color_and_size_level', 'requires/sample_wash_or_dyeing_entry_controller' );

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
   	alert("no Wash/Dyeing found for this sample name and item in requisition");
   	//$('#breakdown_td_id').html('');
   }

}

function change_td_value_dynamically(data)
{
	//$("#dynamic_company").html('');
	if(data==3)
	{
		$("#dynamic_company").html('Wash Company');
		$("#dynamic_date").html('Wash Date');
		$("#dynamic_qty").html('Wash Qty');
		$("#dynamic_yet_to").html('Yet to Wash');
		$("#dynamic_cumul").html('Cumul. Wash Qty');
	}
	else
	{
		$("#dynamic_company").html('Dyeing Company');
		$("#dynamic_date").html('Dyeing Date');
		$("#dynamic_qty").html('Dyeing Qty');
		$("#dynamic_yet_to").html('Yet to Dyeing');
		$("#dynamic_cumul").html('Cumul. Dyeing Qty');

	}

}
function put_sample_item_data(sample_dtls_part_tbl_id,smp_id,gmts)
{
	var req_id=$("#hidden_requisition_id").val();
	var embel_name=$("#cbo_embel_name").val();
	 //$('#cbo_embel_name').val('');
	//$('#cbo_embel_type').val('');
	//alert(mst_id+' '+smp_id+' '+gmts+' '+req_id);return;
	freeze_window(5);
 	get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts+'**'+embel_name+'**'+'single', "color_and_size_level", "requires/sample_wash_or_dyeing_entry_controller" );
    set_button_status(0, permission, 'fnc_sample_wash_or_dyeing_entry',1,0);
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
	$("#txt_wash_qty").val(totalVal);

	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )
		{
			$("#txt_wash_qty").val('');
			$("#colSizeQty_"+tableName+index).val('');
		}
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
			$("#txt_wash_qty").val('');
 		}
	}
}
function fn_total_rej(tableName,index) // for color and size level
{
	var filed_value = $("#colSizeRej_"+tableName+index).val();
	var totalRow = $("#table_"+tableName+" tr").length;
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
	    totalValRej += ( $(this).val() )*1;
	});
	$("#txt_reject_qnty").val(totalValRej);
}



function fnc_sample_wash_or_dyeing_entry(operation)
{
	if(operation==0 && $('#txt_total_cutting_qty').val()==0)
	{
		alert("Total cutting value zero");
	}

	if(operation==4)
	{
		var check_print_ids="";
		var i=1;
		//var check_id=$("#sample_detail_tbl tr").length();
		$("#sample_detail_tbl tr").each(function(){
			//alert($("#check_for_print_"+i).is(":checked"));
			if($("#check_for_print_"+i).is(":checked")){
				if(check_print_ids=="") check_print_ids  = $("#check_for_print_"+i).val() ;
				else
				check_print_ids += ','+$("#check_for_print_"+i).val();
			}
			i++;
		});
		//alert(check_print_ids);
		if(check_print_ids==""){
			alert("Select at least one checkbox");
			$("#check_for_print_"+i).focus();
			return;
		}else{
		  print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val()+'*'+$("#cbo_item_name").val()+'*'+check_print_ids+'*'+$("#hidden_requisition_id").val()+'*'+$("#hidden_sample_dtls_tbl_id").val(), "wash_print", "requires/sample_wash_or_dyeing_entry_controller" )
		 return;
		}
	}
	else if(operation==0 || operation==1 || operation==2)
	{
 		if (form_validation('cbo_company_name*txt_sample_requisition_id*cbo_wash_company*txt_wash_Ordyeing_date*cbo_sample_name*txt_wash_qty*cbo_embel_name*cbo_embel_type','Company Name*Sample Requisition ID*Embellishment Company*Embellish Date*Sample Name*Embellish Quantity*Embel. Name*Embel. Type')==false)
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

			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*txt_sample_requisition_id*cbo_buyer_name*txt_style_no*cbo_item_name*txt_sample_qty*cbo_source*cbo_wash_company*cbo_location*cbo_floor*cbo_sample_name*txt_wash_Ordyeing_date*txt_reporting_hour*txt_wash_qty*txt_reject_qnty*txt_remark*hidden_sample_dtls_tbl_id*hidden_requisition_id*cbo_embel_name*cbo_embel_type*txt_total_cutting_qty',"../");
			 // alert(data);return;
			freeze_window(operation);
  			http.open("POST","requires/sample_wash_or_dyeing_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_wash_or_dyeing_entry_Reply_info;

		}
	}
}

function fnc_sample_wash_or_dyeing_entry_Reply_info()
{
 	if(http.readyState == 4)
	{

		var response=http.responseText.split('**');

		if(response[0]==0)//insert response;
		{
			show_msg(trim(response[0]));
			show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_wash_or_dyeing_entry_controller','');
			$('#mst_update_id').val(response[1]);
  			$('#breakdown_td_id').html('');
  			var val =return_global_ajax_value( response[5]+"__"+response[1]+"__"+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val()+"__"+$('#cbo_embel_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_wash_or_dyeing_entry_controller');
  			var prod_qty=$("#txt_sample_qty").val();
  			var total_cut=$("#txt_total_cutting_qty").val();
  			$("#txt_cumul_wash_qty").val(val);
  			$("#txt_yet_to_wash").val(total_cut*1 - val*1);
   			childFormReset();
   			$("#txt_wash_Ordyeing_date").datepicker().datepicker("setDate", new Date());

		}
		else if(response[0]==1)//update response;
		{
			show_msg(trim(response[0]));
 			show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_wash_or_dyeing_entry_controller','');
			var val =return_global_ajax_value( response[5]+"__"+response[1]+"__"+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val()+"__"+$('#cbo_embel_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_wash_or_dyeing_entry_controller');
  			var prod_qty=$("#txt_sample_qty").val();
  			var total_cut=$("#txt_total_cutting_qty").val();
   			$("#txt_cumul_wash_qty").val(val);
  			$("#txt_yet_to_wash").val(total_cut*1 - val*1);
   			childFormReset();
			set_button_status(0, permission, 'fnc_sample_wash_or_dyeing_entry',1,0);
		    $("#txt_wash_Ordyeing_date").datepicker().datepicker("setDate", new Date());

		}
		else if(response[0]==2)//delete reponse;
		{
			show_msg(trim(response[0]));
			set_button_status(0, permission, 'fnc_sample_wash_or_dyeing_entry',1,0);
		}
		release_freezing();
 	}
}


function childFormReset()
{
	reset_form('','','txt_wash_Ordyeing_date*txt_reporting_hour*txt_wash_qty*txt_reject_qnty*txt_remark','','');
}

function fnc_valid_time(val,field_id)
{
	var val_length=val.length;
	if(val_length==2)
	{
		document.getElementById(field_id).value=val+":";
	}

	var colon_contains=val.contains(":");
	if(colon_contains==false)
	{
		if(val>23)
		{
			document.getElementById(field_id).value='23:';
		}
	}
	else
	{
		var data=val.split(":");
		var minutes=data[1];
		var str_length=minutes.length;
		var hour=data[0]*1;

		if(hour>23)
		{
			hour=23;
		}

		if(str_length>=2)
		{
			minutes= minutes.substr(0, 2);
			if(minutes*1>59)
			{
				minutes=59;
			}
		}

		var valid_time=hour+":"+minutes;
		document.getElementById(field_id).value=valid_time;
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Sample Production</legend>
			<form name="sampleWashOrDyeing_1" id="sampleWashOrDyeing_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td align="left" width="102" class="must_entry_caption">Company</td>
                            <td width="170">
								<?
                                 echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_wash_or_dyeing_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                ?>
							</td>
							 <td align="left"> &nbsp; Source</td>
                             <td>
								 <?
                                 echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sample_wash_or_dyeing_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );", 0, '1,3' );
                                 ?>
                             </td>
                         	 <td align="left" class="must_entry_caption" id="dynamic_company" width="120"> &nbsp; Wash Company</td>
                             <td id="sew_company_td" >
								 <?
                                 echo create_drop_down( "cbo_wash_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "" );
                                 ?>
						     </td>


                        </tr>
                        <tr>
                        <td align="left" width="102" class="must_entry_caption">Sample Req. No</td>
                            <td width="170">
								<input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id" onDblClick="openmypage('requires/sample_wash_or_dyeing_entry_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID')"  class="text_boxes" style="width:155px " readonly>
								<input type="hidden" id="mst_update_id" />
								<input type="hidden" id="hidden_requisition_id" />

                            </td>
                            <td align="left">  &nbsp; Buyer</td>
                            <td>
								<?
                                echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
							</td>
                            <td align="left">  &nbsp; Style</td>
                            <td>
                                <input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly>
							</td>

                        </tr>
                        <tr>



                            <td align="left"> Prod Qty</td>
                            <td>
                                <input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes"  style="width:158px " disabled readonly>
							</td>
							<td align="left">  &nbsp; Location</td>
							<td id="location_td">
							<?
								echo create_drop_down( "cbo_location", 168,$blank_array,"", 1, "-- Select Location --", $selected, "" );
							?>
							</td>
							<td align="left"> &nbsp;Floor</td>
                            <td id="floor_td">
								 <?
								 $floor_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  and production_process in (5,7) order by floor_name", "id", "floor_name"  );

								 echo create_drop_down( "cbo_floor", 170, $floor_library,"", 1, "-- Select Floor --", $selected, "" );
                                 ?>
                            </td>


                        </tr>
                        <tr>

                        <td width="" class="must_entry_caption" align="left">Embel.Name</td>
                                 <td width="" id="embel_name_td">
                                     <?
                                     echo create_drop_down( "cbo_embel_name", 170, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/sample_wash_or_dyeing_entry_controller', this.value+'**'+$('#hidden_requisition_id').val(), 'load_drop_down_embro_issue_type', 'emb_type_td');",'','','','','1,2,4,99' );
                                     ?>
                                 </td>
                                 <td width="" class="must_entry_caption" align="left"> &nbsp; Embel.Type</td>
                                 <td id="emb_type_td" width="">
                                     <?
                                     echo create_drop_down( "cbo_embel_type", 170, $blank_array,"", 1, "-- Select --", $selected, "" );
                                     ?>
                                 </td>

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
                                        <td align="left" class="must_entry_caption">Sample Name</td>
                                        <td>
                              			<?
                              			echo create_drop_down( "cbo_sample_name", 150,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );
							  			?>
                              			</td>
                              			<input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                                   </tr>
                                   <tr>
	                                   	<td align="left" class="must_entry_caption"> Item Name</td>
			                             <td>
											 <?
											 $item_arrs="select id,item_name from lib_garment_item where status_active=1 and is_deleted=0";
			                                 echo create_drop_down( "cbo_item_name", 150, $item_arrs,"id,item_name", 1, "-- Select Item --", $selected, "",1,0 );
			                                 ?>
										 </td>
                                   </tr>
                                    <tr>
                                    	<td align="left" class="must_entry_caption" id="dynamic_date" width="100">Wash Date</td>
                                         <td width="">
                                         <input name="txt_wash_Ordyeing_date" id="txt_wash_Ordyeing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:138px;"  onChange="load_drop_down( 'requires/sample_wash_or_dyeing_entry_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" />
                                        </td>
                                     </tr>

                                     <tr>
                                       <td align="left" class="">Reporting Hour</td>
                                       <td ><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:138px" placeholder="24 Hour Format" maxlength="8" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" />

                                       </td>
                                     </tr>

                                   <tr>
                                        <td align="left" class="must_entry_caption" id="dynamic_qty">Wash Quantity</td>
                                        <td width="" valign="top"><input name="txt_wash_qty" id="txt_wash_qty" class="text_boxes_numeric"  style="width:138px" readonly >
                                            <input type="hidden" id="hidden_colorSizeID"  value=""/>
                                        </td>
                                   </tr>

                                   <tr>
                                     <td align="left">Reject Qty</td>
                                     <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:138px;" readonly /></td>
                                   </tr>

                                   <tr>
                                     <td align="left">Remarks</td>
                                     <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:138px;" /></td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                        <td align="left" width="110" id="dynamic_cut_qty">Total Sewing Qty</td>
                                        <td>
                                            <input type="text" name="txt_total_cutting_qty" id="txt_total_cutting_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" width="110" id="dynamic_cumul">Cumul. Wash Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_wash_qty" id="txt_cumul_wash_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td align="left" width="110" id="dynamic_yet_to">Yet to Wash</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_wash" id="txt_yet_to_wash" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                     </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_sample_wash_or_dyeing_entry", 0,0,"reset_form('sampleWashOrDyeing_1','list_view_country','','txt_wash_Ordyeing_date,".$date."*txt_challan,0','childFormReset()')",1);
                            ?>
							<input type="button" class="formbutton" name="btn_ids" id="btn_ids" value="Print" onclick="fnc_sample_wash_or_dyeing_entry(4)"  style="width:80px"/>
                            <input type="hidden" name="dtls_update_id" id="dtls_update_id" readonly />
           				</td>
           				<td>&nbsp;</td>
		  			</tr>
                </table>
            </form>
        	</fieldset>
            <div style="float:left;"id="list_view_container"></div>

        </div>

		<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>