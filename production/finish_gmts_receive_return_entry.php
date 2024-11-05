<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Garments Receive Return Entry
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	20-01-2022
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
$u_id=$_SESSION['logic_erp']['user_id'];
$user_level = $_SESSION['logic_erp']['user_level'];


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

$company_credential_cond = "";
if (!empty($company_id)) {
    $company_credential_cond = " and id in($company_id)";
}

//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Garments Receive Return Entry","../", 1, 1, $unicode,1,'');

?>	
<script>
	var permission='<? echo $permission; ?>';
	var user_level='<? echo $user_level; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function fnc_gmt_rcv_rtn_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_order_no').val()+'*'+$('#cbo_buyer_name').val()+'*'+$('#txt_challan_id').val()+'*'+$('#cbo_purpose').val()+'*'+$('#txt_job_no').val()+'*'+$('#txt_style_no').val()+'*'+$('#txt_rcv_rtn_date').val()+'*'+$('#cbo_item_name').val()+'*'+$('#txt_order_qty').val()+'*'+$('#cbo_source').val()+'*'+$('#cbo_finish_company').val()+'*'+$('#cbo_floor').val()+'*'+$('#txt_remark').val()+'*'+report_title,"finish_garment_receive_return_challan_print", "requires/finish_gmts_receive_return_entry_controller" ) 
			return;
		}
		else
		{
			freeze_window(operation);
			var cbo_source=$('#cbo_source').val();
			if(cbo_source==1)
			{
				if ( form_validation('cbo_company_id*cbo_location*cbo_store_name*cbo_purpose*cbo_source*cbo_finish_company*cbo_finish_location*txt_rcv_rtn_date*txt_order_no','Company Name*Location*Store*Return Purpose*Finishing Source*Finishing Company*Finish. Location*Receive Date*Order No')==false )
				{
					release_freezing(); 
					return;
				}		
			}

			let rack_wise_balance_show=$('#rack_wise_balance_show').val();
			if (rack_wise_balance_show==1 )
			{
				if ( form_validation('cbo_floor*cbo_room*txt_rack*txt_shelf','Finish. Floor*Room*Rack*Shelf')==false )
				{
					release_freezing(); 
					return;
				}
			}

			if ( form_validation('cbo_company_id*cbo_location*cbo_store_name*cbo_purpose*cbo_source*cbo_finish_company*txt_rcv_rtn_date*txt_order_no','Company Name*Location*Store*Return Purpose*Finishing Source*Finishing Company*Receive Date*Order No')==false )
			{
				release_freezing(); 
				return;
			}		
			else
			{
				var rcv_qty=$('#txt_finishing_qty').val()*1;
				var carton_qty=$('#txt_carton_qty').val()*1;
				var hdn_rcv_qty=$('#hdn_finishing_qty').val()*1;
				// var balance_qty=$('#txt_yet_to_rcv_rtn').val()*1;
				var balance_carton=$('#hdn_yet_to_carton_rcv_rtn').val()*1;
				if(operation==0 || operation==1)
				{
					if(rcv_qty<1)
					{
						alert("Receive quantity should be filled up.");
						release_freezing(); 
						return;
					}
					if(rcv_qty>hdn_rcv_qty)
					{
						alert("Receive Qnty can not more than Delivery qnty.");
						release_freezing(); 
						return;
					}
					if(carton_qty>balance_carton)
					{
						alert("Total Carton Qty can not more than Delivery Total Carton Qty.");
						release_freezing(); 
						return;
					}
				}
				
					
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				var variableSettingsReject=$('#finish_production_variable_rej').val();

				if(sewing_production_variable=="" || sewing_production_variable==0)
				{
					sewing_production_variable=3;
					variableSettingsReject=3;
				}

				var i=0; var k=0; var colorIDvalue='';
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
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('txt_challan_id*txt_challan_no*garments_nature*cbo_company_id*cbo_purpose*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*cbo_country_name*txt_order_qty*cbo_source*cbo_finish_company*cbo_finish_location*cbo_location*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_finishing_qty*txt_carton_qty*txt_system_no*txt_system_id*txt_rcv_rtn_date*txt_remark*txt_rcv_input_qty*txt_cumul_rcv_rtn_qty*txt_yet_to_rcv_rtn*hidden_break_down_html*txt_mst_id',"../");
				// alert(data);return;
				http.open("POST","requires/finish_gmts_receive_return_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_gmt_rcv_rtn_entry_Reply_info;
			}
		}
	}
	

	function fnc_gmt_rcv_rtn_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==786)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			
			if(reponse[0]==25)
			{
				// $("#txt_finishing_qty").val("");
				show_msg('31');
				release_freezing();
			}
			if(reponse[0]==35)
			{
				$("#txt_finishing_qty").val("");
				show_msg('25');
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==505)
			{
				alert("Full Shipment Order Can't be saved,updated!!");
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			
			if(reponse[0]==0)
			{ 
				document.getElementById('txt_system_no').value = reponse[2];
				document.getElementById('txt_system_id').value = reponse[3];
				var po_id = reponse[1];
				show_msg(reponse[0]);
				var txt_job_no=$('#txt_job_no').val();
				show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/finish_gmts_receive_return_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				release_freezing();
				reset_form('','','txt_finishing_qty*txt_carton_qty*txt_rcv_input_qty*txt_cumul_rcv_rtn_qty*txt_yet_to_rcv_rtn*cbo_room*txt_rack*txt_shelf*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id','','','');		
				disable_enable_fields('txt_challan_no*cbo_company_id*cbo_source*cbo_finish_company*cbo_location*cbo_store_name*cbo_floor',1);	
				set_button_status(0, permission, 'fnc_gmt_rcv_rtn_entry',1,0);
			}
			if(reponse[0]==1)
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				var txt_job_no=$('#txt_job_no').val();
				show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/finish_gmts_receive_return_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				reset_form('','','txt_finishing_qty*txt_carton_qty*txt_remark*txt_rcv_input_qty*txt_cumul_rcv_rtn_qty*txt_yet_to_rcv_rtn*cbo_room*txt_rack*txt_shelf*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id','','','');
				disable_enable_fields('txt_challan_no*cbo_company_id*cbo_source*cbo_finish_company*cbo_location*cbo_store_name*cbo_floor',1);	
				release_freezing();
				set_button_status(0, permission, 'fnc_gmt_rcv_rtn_entry',1,0);
			}
			if(reponse[0]==2)
			{
				if(reponse[4]==2)
				{
					var po_id = reponse[1];
					show_msg(trim(reponse[0]));
					var txt_job_no=$('#txt_job_no').val();
					show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/finish_gmts_receive_return_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
					reset_form('','','txt_finishing_qty*txt_carton_qty*txt_remark*txt_rcv_input_qty*txt_cumul_rcv_rtn_qty*txt_yet_to_rcv_rtn*cbo_room*txt_rack*txt_shelf*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id','','','');
					disable_enable_fields('txt_challan_no*cbo_company_id*cbo_source*cbo_finish_company*cbo_location*cbo_store_name*cbo_floor',1);	
				}
				if(reponse[4]==1)
				{
					release_freezing();
					location.reload();
				}		
				release_freezing();
				set_button_status(0, permission, 'fnc_gmt_rcv_rtn_entry',1,0);
			}

			if(reponse[0]==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			
		}
	}

	function challan_number_popup()
	{
		var page_link = 'requires/finish_gmts_receive_return_entry_controller.php?action=challan_number_popup&company='+$('#cbo_company_id').val()+'&location_name='+$('#cbo_location_name').val()+'&fnisn_company='+$('#cbo_finish_company').val()+'&source='+$('#cbo_source').val();
		
		var title = 'Garments Finishing Delivery Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');

			document.getElementById('txt_challan_id').value=responseDataArr[0];
			document.getElementById('txt_challan_no').value=responseDataArr[1];
			
			get_php_form_data(responseDataArr[0], "populate_challan_form_data", "requires/finish_gmts_receive_return_entry_controller" );
			var variableSettings=$('#sewing_production_variable').val();
			var company_id=$('#cbo_company_id').val();
			show_list_view(company_id+'**'+responseDataArr[0]+'**'+variableSettings,'show_dtls_listview_challan','list_view_challan','requires/finish_gmts_receive_return_entry_controller');		
			release_freezing();
		}
	} 

	function system_number_popup()
	{
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{
			var page_link = 'requires/finish_gmts_receive_return_entry_controller.php?action=system_number_popup&company='+$('#cbo_company_id').val()+'&location_name='+$('#cbo_location_name').val()+'&fnisn_company='+$('#cbo_finish_company').val()+'&source='+$('#cbo_source').val();
		
			var title = 'Finish Garments Receive Search';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=0,scrolling=1','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');

				document.getElementById('txt_system_id').value=responseDataArr[0];
				document.getElementById('txt_system_no').value=responseDataArr[1];

				get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/finish_gmts_receive_return_entry_controller" );			
				show_list_view(responseDataArr[0],'show_dtls_listview','list_view_container','requires/finish_gmts_receive_return_entry_controller');	
				var variableSettings=$('#sewing_production_variable').val();
				var company_id=$('#cbo_company_id').val();
				show_list_view(company_id+'**'+responseDataArr[0]+'**'+variableSettings,'show_dtls_listview_challan','list_view_challan','requires/finish_gmts_receive_return_entry_controller');
				disable_enable_fields('txt_challan_no*cbo_company_id*cbo_source*cbo_finish_company*cbo_location*cbo_store_name*cbo_floor*txt_rcv_rtn_date',1);	
				release_freezing();
			}
		}

	} 

	function childFormReset()
	{
		reset_form('','','txt_finishing_qty*txt_carton_qty*txt_rcv_input_qty*txt_cumul_rcv_rtn_qty*txt_yet_to_rcv_rtn*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id','','');
		disable_enable_fields('txt_challan_no*cbo_company_id*cbo_source*cbo_finish_company*cbo_location*cbo_store_name*cbo_floor',0);	
		$('#txt_cumul_rcv_rtn_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_rcv_rtn').attr('placeholder','');//placeholder value initilize
		// $('#list_view_container').html('');//listview container
		// $("#breakdown_td_id").html('');
	}

	function fn_total(tableName,index) // for color and size level
	{
		var filed_value = $("#colSize_"+tableName+index).val();
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var hidden_variable_cntl=1;
		
		if(filed_value*1 > placeholder_value*1)
		{
			if(hidden_variable_cntl==1 && user_level!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+tableName+index).val('');
				$("#txt_finishing_qty").val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
					void(0);
				else
				{
					$("#colSize_"+tableName+index).val('');
				}
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
		var totalfinishAmount = 0;
		$("input[name=colorSize]").each(function(index, element) {
			var color_id=$(this).attr('id').split("_");
			totalVal += ( $(this).val() )*1;
			
		});
		$("#txt_finishing_qty").val(totalVal);
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var hidden_variable_cntl=1;//$('#hidden_variable_cntl').val()*1;
		if(filed_value*1 > placeholder_value*1)
		{
			if(hidden_variable_cntl==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+index).val('');
				$("#txt_finishing_qty").val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
					void(0);
				else
				{
					$("#colSize_"+index).val('');
				}
			}	
		}
		
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_finishing_qty").val( $("#total_color").val() );
	} 

	$('#txt_challan_no').live('keydown', function(e) 
	{
		if (e.keyCode === 13) {
			e.preventDefault();
			scan_challan_number(trim(this.value));
		}
	});

	function scan_challan_number(str)
	{
		freeze_window(3);
		get_php_form_data( str, "populate_challan_form_data_scan", "requires/finish_gmts_receive_return_entry_controller" );
		release_freezing();
		var variableSettings=$('#sewing_production_variable').val();
		var challan_id=$('#txt_challan_id').val();
		var company_id=$('#cbo_company_id').val();
		show_list_view(company_id+'**'+challan_id+'**'+variableSettings,'show_dtls_listview_challan','list_view_challan','requires/finish_gmts_receive_return_entry_controller');
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
 	<div style="width:900px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Finish Garments Receive Entry</legend>  
            <form name="finishingentry_1" id="finishingentry_1" autocomplete="off" >
 				<fieldset>
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="3"><strong>System ID</strong></td>
                        <td colspan="3"> 
                          <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text"  style="width:160px" onDblClick="system_number_popup()" placeholder="Browse or Search"  readonly/>
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden"  style="width:160px"/>
                        </td>
                    </tr>
                	<tr>
                        <td width="120" class="must_entry_caption" >Receive Challan No</td>
                        <td > 
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:160px" onDblClick="challan_number_popup()" placeholder="Browse/Scan" />
                          <input name="txt_challan_id" id="txt_challan_id" class="text_boxes" type="hidden"  style="width:160px"/>
                        </td>
						<td width="120" class="must_entry_caption">Company</td>
                        <td><? echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_gmts_receive_return_entry_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/finish_gmts_receive_return_entry_controller');" ); ?>	 
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" /> 
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
							<input type="hidden" id="rack_wise_balance_show" value="0"/>
                        </td>
						<td width="120" id="locations" class="must_entry_caption">Location</td>
						<td id="location_td"><? echo create_drop_down( "cbo_location", 170, $blank_array, "", 1, "-- Select Location --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
						<td class="must_entry_caption">Store</td>
						<td id="store_td"> 
							<? echo create_drop_down( "cbo_store_name", 170, $blank_array, "", 1, "-- Select Store --", $selected, "" ); ?>
						</td>
						<td class="must_entry_caption">Return Purpose</td>
						<td >
							<? $return_purpose_arr=array(1=>"Delivery",2=>"Buyer Inspection");
							echo create_drop_down( "cbo_purpose", 170, $return_purpose_arr, "", 1, "-- Select Purpose --", $selected, "" ); ?>
						</td>
						<td class="must_entry_caption">Finishing Source</td>
                        <td id="source_td">
							<? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/finish_gmts_receive_return_entry_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_source', 'finishing_td' );", 0, '1,3' ); ?>
							<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
                        	<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
						</td>
					</tr>
					<tr>
                        <td class="must_entry_caption">Finish. Company</td>
                        <td id="finishing_td"><? echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "-Select finishing Company-", $selected, "" );?></td>
						<td class="must_entry_caption">Finish. Location</td>
						<td id="finish_location_td"><? echo create_drop_down( "cbo_finish_location", 170, $blank_array, "", 1, "-- Select Location --", $selected, "" ); ?></td>
						<td id="floors" class="must_entry_caption">Finish. Floor</td>
						<td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array, "", 1, "-- Select Floor --", $selected, "" ); ?></td>
					</tr>
					<tr>

						<td class="must_entry_caption">Receive Return Date</td>
						<td >
							<input name="txt_rcv_rtn_date" id="txt_rcv_rtn_date" class="datepicker" style="width:160px;" readonly />
						</td>
						<td >Remarks</td>
						<td colspan="3">
							<input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:460px;"  />
						</td>
					</tr>

                </table>
                </fieldset>
                <br />                 
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                           <fieldset>
                            <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
									<td class="must_entry_caption">Room</td>
									<td id="room_td"><? echo create_drop_down( "cbo_room", 152, $blank_array, "", 1, "-- Select Room --", $selected, "" ); ?></td>
                                </tr>
                            	<tr>
									<td class="must_entry_caption">Rack</td>
									<td id="rack_td"><? echo create_drop_down( "txt_rack", 152, $blank_array, "", 1, "-- Select Rack --", $selected, "" ); ?></td>
                                </tr>
                            	<tr>
									<td class="must_entry_caption">Shelf</td>
									<td id="shelf_td"> <? echo create_drop_down( "txt_shelf", 152, $blank_array, "", 1, "-- Select Shelf --", $selected, "" ); ?></td> 
                                </tr>
                            	<tr>
                                    <td width="100" class="must_entry_caption">Order No</td>
									<td width="175">
									<input name="txt_order_no" placeholder="Display" id="txt_order_no" class="text_boxes" style="width:142px " readonly />
									<input type="hidden" id="hidden_po_break_down_id" value="" />
									<input type="hidden" id="garments_nature" value="" />
									</td>
                                </tr>
                                <tr>
                                    <td >Receive Qnty</td>
                                    <td colspan="2"><input name="txt_finishing_qty" id="txt_finishing_qty"  placeholder="Display" class="text_boxes_numeric"  style="width:142px; text-align:right" readonly />
                                        <input type="hidden" id="hdn_finishing_qty"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Carton Qty</td>
                                    <td colspan="2"><input type="text" name="txt_carton_qty" id="txt_carton_qty" class="text_boxes_numeric"   style="width:142px; text-align:right" />
									<input type="hidden" name="txt_rcv_input_carton" id="txt_rcv_input_carton"/>
									<input type="hidden" name="txt_cumul_rcv_rtn_carton" id="txt_cumul_rcv_rtn_carton"/>
									<input type="hidden" name="hdn_yet_to_carton_rcv_rtn" id="hdn_yet_to_carton_rcv_rtn"/>
								</td>
                                </tr>
                            </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                        <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                    <td width="120">Total Receive</td> 
                                    <td><input type="text" name="txt_rcv_input_qty" id="txt_rcv_input_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Total Receive Return</td>
                                    <td><input type="text" name="txt_cumul_rcv_rtn_qty" id="txt_cumul_rcv_rtn_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Balance</td>
                                    <td><input type="text" name="txt_yet_to_rcv_rtn" id="txt_yet_to_rcv_rtn" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Job No</td>
                                    <td><input style="width:100px;" type="text"   class="text_boxes" name="txt_job_no" id="txt_job_no" disabled  /></td>
                                </tr>
                                <tr>
                                    <td width="">Style</td>
                                    <td><input class="text_boxes" name="txt_style_no" id="txt_style_no" type="text" style="width:100px;" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Order Qty.</td>
                                    <td><input class="text_boxes_numeric"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:100px;" disabled/></td>
                                </tr>
                                <tr>
                                    <td width="">Item</td>
                                    <td><?
                                    echo create_drop_down( "cbo_item_name", 112, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                    ?></td>
                                </tr>
                                <tr>
                                    <td width="">Country</td>
                                    <td><?
                                    echo create_drop_down( "cbo_country_name", 112, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                    ?></td>
                                </tr>
                                <tr>
                                    <td width="">Country Qnty.</td>
                                    <td><input type="text" name="txt_country_qty" id="txt_country_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Buyer Name</td>
                                    <td  id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 112, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 );?></td>
                                  </tr>
                                </tr>
                                
                            </table>
                            </fieldset>
                        </td>
                        <td width="43%" valign="top" >
                            <div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
           				<?
							$date=date('d-m-Y');
							echo load_submit_buttons( $permission, "fnc_gmt_rcv_rtn_entry", 0,1 ,"reset_form('finishingentry_1','list_view_country','','txt_delivery_date,".$date."','childFormReset()')",1); 
		   				?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                <div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
	</div>
	<!-- <div id="list_view_country" style="width:400px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:40px"></div>         -->
	<div id="list_view_challan" style="width:400px; overflow:auto; padding-top:5px; position:absolute; left:950px"></div>        
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>