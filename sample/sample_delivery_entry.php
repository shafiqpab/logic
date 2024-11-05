<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sample delivery entry

Functionality	:
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	09-04-2017
Updated by 		: 	zakaria joy
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
echo load_html_head_contents("Delivery Info","../", 1, 1, $unicode,'','');



?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


function fnc_sample_delivery_entry(operation)
{

	if(operation==4)
	{
		  var tbl_row = document.getElementById( 'ex_fac_tbl' ).rows.length;
		  var i,ids="";
		  var req_ids="";
		  for(i=1;i<=tbl_row;i++)
		  {
		  	if($('#isChk_'+i).is(':checked'))
		  	{
		  		if(ids=="")ids=$('#isChk_'+i).val();
		  		else ids+=','+$('#isChk_'+i).val();

		  		if(req_ids=="")req_ids=$('#hiddenReqId_'+i).val();
		  		else req_ids+=','+$('#hiddenReqId_'+i).val();

		  	}
		  }
		  if(ids=="")
		  {
		  	  alert("please select at least one checkbox!");
		  	  return ;
		  }

 		  print_report( $('#mst_update_id').val()+'*'+ids+'*'+$('#cbo_company_name').val()+'*'+$('#cbo_sample_name').val()+'*'+$('#cbo_item_name').val()+'*'+req_ids+'*'+$('#hidden_sample_dtls_tbl_id').val(), "delivery_print", "requires/sample_delivery_entry_controller" )
			 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
 		if (form_validation('cbo_company_name*txt_delivery_date*txt_sample_requisition_id*cbo_sample_name*txt_delivery_qty','Company Name*Delivery Date*Sample Requisition ID*Sewing Date*Sample Name*Delivery Quantity')==false)
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

			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*cbo_location_name*cbo_delivery_to*txt_delivery_date*txt_gp_no*txt_final_destination*txt_received_by*txt_sample_requisition_id*cbo_sample_name*cbo_item_name*txt_delivery_qty*txt_carton_qnty*txt_remark*cbo_shipping_status*hidden_requisition_id*hidden_sample_dtls_tbl_id*hidden_previous_delv_qty',"../");
		   // alert(data);return;
  			http.open("POST","requires/sample_delivery_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_delivery_entry_Reply_info;

		}
	}
}

function fnc_sample_delivery_entry_Reply_info()
{
 	if(http.readyState == 4)
	{

		var response=http.responseText.split('**');

		if(response[0]==0)//insert response;
		{
			show_msg(trim(response[0]));
			show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_controller','');
			//alert(response[1] + " another "+response[3]);
			if(trim(response[1])!="")
			$('#mst_update_id').val(response[1]);
			if( trim(response[3]) !="" )
			$('#txt_challan_no').val(response[3]);
			$('#breakdown_td_id').html('');
  			var val =return_global_ajax_value( response[1]+"__"+response[4]+"__"+response[2]+'__'+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_delivery_entry_controller');
   			var total_cut=$("#txt_total_finished_qty").val();
  			$("#txt_cumul_delivery_qty").val(val);
  			$("#txt_yet_to_delivery").val(total_cut*1 - val*1);
			childFormReset();
			disable_enable_fields( "cbo_company_name*cbo_location_name*cbo_delivery_to", 1 );
		}
		else if(response[0]==1)//update response;
		{
			show_msg(trim(response[0]));
			show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_controller','');
			$('#breakdown_td_id').html('');
		   var val =return_global_ajax_value( response[1]+"__"+response[4]+"__"+response[2]+'__'+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_delivery_entry_controller');
   			var total_cut=$("#txt_total_finished_qty").val();
  			$("#txt_cumul_delivery_qty").val(val);
  			$("#txt_yet_to_delivery").val(total_cut*1 - val*1);
			set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
			childFormReset();
			disable_enable_fields( "cbo_company_name*cbo_location_name*cbo_delivery_to", 1 );
		}
		else if(response[0]==2)//delete response;
		{

			show_msg(trim(response[0]));

			show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_controller','');
			set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
			childFormReset();
			disable_enable_fields( "cbo_company_name*cbo_location_name*cbo_delivery_to", 0 );
		}

		release_freezing();
 	}
}

	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value;//requisition id
			$("#cbo_company_name").removeAttr("disabled");
			if (smp_id!="")
			{
 				$("#txt_sample_requisition_id").val(smp_id);
				var smp_idStr=smp_id+'__0**1';
				get_php_form_data(smp_idStr, "populate_data_from_search_popup", "requires/sample_delivery_entry_controller" );

				show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_delivery_entry_controller','');

				//show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_delivery_entry_controller','');

				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
				$("#cbo_company_name").attr("disabled",true);
				release_freezing();
			}
		}
	}

function put_sample_item_data(sample_dtls_part_tbl_id,smp_id,gmts,color)
{
	var req_id=$("#hidden_requisition_id").val();
	//alert(mst_id+' '+smp_id+' '+gmts+' '+req_id);return;
	//freeze_window(5);
 	get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts+'**'+color, "color_and_size_level", "requires/sample_delivery_entry_controller" );
    set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
	//release_freezing();
}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeQty_"+tableName+index).val();
	var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');

	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )
			void(0);
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
 		}
	}

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
	$("#txt_delivery_qty").val(totalVal);
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


function childFormReset()
{
	reset_form('','','txt_challan_no*txt_delivery_qty*txt_carton_qnty*txt_remark','','');
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

function ex_factory_sys_popup()
{
	/*if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	*/
	var page_link="requires/sample_delivery_entry_controller.php?action=sys_surch_popup&company="+$("#cbo_company_name").val();
	var title="Sample Delivery Info";

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var smp_id=this.contentDoc.getElementById("selected_id").value;
		var smp_id_arr=smp_id.split('*');
		freeze_window(5);
		$("#txt_development_sample_id").val(smp_id_arr[0]);
		//show_list_view(smp_id_arr[0],'show_sample_item_listview','list_view_country','requires/sample_delivery_entry_controller','');
		var data_format=smp_id_arr[0]+"__"+smp_id_arr[1];
		show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_delivery_entry_controller','');
		get_php_form_data(data_format+"**2", "populate_data_from_search_popup", "requires/sample_delivery_entry_controller" );

		set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
		disable_enable_fields( "cbo_company_name*cbo_location_name*cbo_delivery_to", 1 );
		release_freezing();
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
			<form name="sampleDelivery_1" id="sampleDelivery_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                    <tr>
                        <td align="right" colspan="3">Challan No</td>
                        <td colspan="3">
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" value=""  style="width:160px" onDblClick="ex_factory_sys_popup()" placeholder="Browse or Search" />
                         </td>
                    </tr>
                      <tr>
                        <td width="" align="left" class="must_entry_caption">Company Name </td>
                        <td width="">
                            <?
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/sample_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0 ); ?>
                        </td>
                        <td width="" align="left">Location</td>
                        <td width="" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td align="left"> Delivery To</td>
                        <td >
						<?
						$delivery_to = array(1=>'Marketing & Merchandising',2=>'Sample Gift',3=>'Left over',4=>'MnM',50=>'Others');
						//"select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 group by id,team_member_name order by team_member_name"
                        echo create_drop_down( "cbo_delivery_to", 172, $delivery_to,"", 0, "", $selected, "",0 );
                          ?>
                        </td>


                    </tr>
                        <tr>


							<td align="left">GP No</td>
							<td >
							<input type="text" name="txt_gp_no" id="txt_gp_no" class="text_boxes" style="width:160px;" maxlength="50">
							</td>
							<td align="" >Final Destination</td>
	                        <td>
	                        	<input type="text" name="txt_final_destination" id="txt_final_destination" class="text_boxes" style="width:160px;" maxlength="50">
	                        </td>
	                        <td align="left">Received By</td>
                            <td>
                                <input name="txt_received_by" id="txt_received_by" class="text_boxes"  style="width:160px ">
							</td>

                        </tr>
                        <tr>


                          </tr>

                    </table>
                </fieldset>

                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                            <fieldset>
                            <legend>New Entry </legend>
                                <table  cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                	<td align="left"  class="must_entry_caption">Sample Req. No</td>
                            <td>
								<input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id" onDblClick="openmypage('requires/sample_delivery_entry_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID')"  class="text_boxes" style="width:138px " readonly>
								<input type="hidden" id="mst_update_id"  value="" />
								<input type="hidden" id="hidden_requisition_id" />

                            </td>
                                </tr>
                                     <tr>
                                        <td align="left" class="must_entry_caption">Sample Name</td>
                                        <td>
                              			<?
                              			echo create_drop_down( "cbo_sample_name", 150,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );
							  			?>
                              			</td>
                              	<input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                              			 <input type="hidden" name="dtls_update_id" id="dtls_update_id" />
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
										<td align="left" class="must_entry_caption">Delivery Date</td>
										<td>
										<input name="txt_delivery_date" id="txt_delivery_date" class="datepicker"  style="width:138px;" value="<? echo date('d-m-Y');?>" >

										</td>
                                   </tr>


                                   <tr>
                                        <td align="left" class="must_entry_caption">Delivery Quantity</td>
                                        <td width="" valign="top"><input name="txt_delivery_qty" id="txt_delivery_qty" class="text_boxes_numeric"  style="width:138px" readonly >
                                        <input type="hidden" name="hidden_previous_delv_qty" id="hidden_previous_delv_qty">
                                            <input type="hidden" id="hidden_colorSizeID"  value=""/>
                                        </td>
                                   </tr>

                                   <tr>
                                     <td align="left">Total Carton Qty</td>
                                     <td><input type="text" name="txt_carton_qnty" id="txt_carton_qnty" class="text_boxes_numeric" style="width:138px;" /></td>
                                   </tr>

                                   <tr>
                                     <td align="left">Remarks</td>
                                     <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:138px;" /></td>
                                   </tr>

                                   <tr style="display: none;">
                                      <td width="" align="">Shiping Status<span id="completion_perc"></span></td>
                                      <td width="">
                                          <?
                                             echo create_drop_down( "cbo_shipping_status", 150, $shipment_status,"", 0, "-- Select --", 2, "",0,'2,3','','','','' );
                                         ?>
                                      </td>
                                </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                        <td align="left" width="110" id="dynamic_cut_qty">Finished Qty</td>
                                        <td>
                                            <input type="text" name="txt_total_finished_qty" id="txt_total_finished_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" width="110">Cumul. Delivery. Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_delivery_qty" id="txt_cumul_delivery_qty" class="text_boxes_numeric" style="width:80px"  disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td align="left" width="110">Yet to Delivery</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_delivery" id="txt_yet_to_delivery" class="text_boxes_numeric" style="width:80px" / disabled >
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>

                            <fieldset>
                            <legend>Requisition info</legend>
                             <table  cellpadding="0" cellspacing="2" width="100%" >
                              <tr>
								<td width="110">Sample Stage</td>
								<td>
								<?
								echo create_drop_down( "cbo_sample_stage", 93, $sample_stage, "", 1, "--display --", $selected, "", 1, "" );
								?>
								</td>
							  </tr>
							<tr>
								<td width="110">Style Ref</td>
								<td> <input name="txt_style_no" id="txt_style_no" class="text_boxes" type="text" value="" style="width:80px;" disabled="" /> </td>
							</tr>
							<tr>
								<td width="110">Buyer Name</td>
								<td>

								<?
								echo create_drop_down( "cbo_buyer_name", 93, "select id,buyer_name from lib_buyer buy where status_active=1 and is_deleted=0","id,buyer_name", 1, "-- display --", $selected, "",1 );
								?>
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
                            echo load_submit_buttons( $permission, "fnc_sample_delivery_entry", 0,0,"reset_form('sampleDelivery_1','list_view_country','','txt_sewing_date,".$date."*txt_challan,0','childFormReset()')",1);

                            ?>
                            &nbsp; <input type="button" class="formbutton"  value="Print" onClick="fnc_sample_delivery_entry(4);" />

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