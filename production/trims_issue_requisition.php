<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Requistion

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	05-12-2019
Purpose			:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
$working_company_sql= sql_select("SELECT WORKING_UNIT_ID FROM user_passwd WHERE id = '$user_id' AND valid = 1");
if(count($working_company_sql)){$working_cond=" and id in(".$working_company_sql[0]['WORKING_UNIT_ID'].")" ;}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Requistion","../", 1, 1, $unicode,'','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	function fn_order()
	{
		var company = $("#cbo_company_name").val();
		var update_id = $("#update_id").val();
		var cbo_trim_type = $("#cbo_trim_type").val();
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_requisition_controller.php?cbo_company_id='+company+'&update_id='+update_id+'&cbo_trim_type='+cbo_trim_type+'&action=po_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_po_id=this.contentDoc.getElementById("hidden_po_id").value; 
			var hidden_product_id=this.contentDoc.getElementById("hidden_product_id").value;
			var hidden_po_and_prod=this.contentDoc.getElementById("hidden_data").value;
			var dtls_tbl_length=$('#tbl_item_details tbody tr').length*1;
			//alert(dtls_tbl_length);//return;
			var list_view_po =return_global_ajax_value( hidden_po_id+"__"+hidden_product_id+"__"+dtls_tbl_length+"__"+hidden_po_and_prod, 'product_details', '', 'requires/trims_issue_requisition_controller');
			//alert(list_view_po);return;
			$('#requisition_details_container').append(list_view_po);
			disable_enable_fields('cbo_trim_type',1);
		}
	}
	
	function fnc_requisition_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#cbo_location_name').val()+'*'+$('#update_id').val()+'*'+report_title, "garments_exfactory_print", "requires/trims_issue_requisition_controller" )

			 return;
		}
		if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg(13);alert("Not Allow now");return;
			}*/

			if ( form_validation('cbo_company_name*txt_req_date','Company Name*Requisition Date')==false )
			{
				return;
			}
			else
			{
				var row_num=$('#tbl_item_details tbody tr').length;
				var dataString="";
				var j=0; var i=1;
				$("#tbl_item_details").find('tbody tr').each(function()
				{
					var job_no=$('#tdJob_'+i).html();
					var buyer_id=$('#tdBuyer_'+i).attr('title');
					var styleref=$('#tdStyle_'+i).html();
					var order_id=$('#tdOrder_'+i).attr('title');
					var orderQnty=$('#tdOrderQnty_'+i).html();
					var item_group=$('#tdItemGroup_'+i).attr('title');
					var itemdescription=encodeURIComponent($('#tdItemDescrip_'+i).html());
					var product_id=$('#tdItemDescrip_'+i).attr('title');
					var gtms_color_id=$('#tdGmtsColor_'+i).attr('title');
					var itemcolorid=$('#tdColor_'+i).attr('title');
					var itemsizeid=$('#tdSize_'+i).html();
					var cbouom=$('#tdUom_'+i).attr('title');
					var rcv_qnty=$('#tdRcvQnty_'+i).html();
					var stock_qnty=$('#tdInhand_'+i).html();
					var reqQnty=$(this).find('input[name="txtReqQnty[]"]').val();
					var updateId=$(this).find('input[name="hdnUpdateDtlsId[]"]').val();
					if(reqQnty>0)	
					{
						j++;
						dataString+='&job_no' + j + '=' + job_no + '&buyer_id' + j + '=' + buyer_id + '&styleref' + j + '=' + styleref + '&order_id' + j + '=' + order_id + '&orderQnty' + j + '=' + orderQnty+ '&item_group' + j + '=' + item_group + '&itemdescription' + j + '=' + itemdescription+ '&product_id' + j + '=' + product_id + '&gtms_color_id' + j + '=' + gtms_color_id+ '&itemcolorid' + j + '=' + itemcolorid+ '&itemsizeid' + j + '=' + itemsizeid + '&cbouom' + j + '=' + cbouom+ '&rcv_qnty' + j + '=' + rcv_qnty + '&stock_qnty' + j + '=' + stock_qnty + '&reqQnty' + j + '=' + reqQnty + '&updateId' + j + '=' + updateId;
					}
					i++;
				});
				
				var data="action=save_update_delete&operation="+operation+"&row_num="+j+get_submitted_data_string('txt_req_no*update_id*cbo_company_name*txt_req_date*txt_delivery_date*cbo_location_name*cbo_store_name*cbo_working_company*cbo_working_location*cbo_floor_name*cbo_sewing_line*cbo_trim_type*txt_remarks',"../")+dataString;
				//alert(data);return;

	 			freeze_window(operation);
	 			http.open("POST","requires/trims_issue_requisition_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_requisition_entry_Reply_info;
			}
		}
	}

	function fnc_requisition_entry_Reply_info()
	{
	 	if(http.readyState == 4)
		{
			//alert(http.responseText);return;
			var reponse=http.responseText.split('**');

			if(reponse[0]==20)
			{
				alert(reponse[1]);release_freezing(); return;
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$("#update_id").val(trim(reponse[1]));
				$("#txt_req_no").val(trim(reponse[2]));
				show_list_view(trim(reponse[1]),'product_details_update','requisition_details_container','requires/trims_issue_requisition_controller','');
				$('#cbo_company_name').attr('disabled',true);
				release_freezing();
				set_button_status(1, permission, 'fnc_requisition_entry',1,1);
			}
			else if(reponse[0]==2)
			{
				location.reload();
				release_freezing();
				set_button_status(0, permission, 'fnc_requisition_entry',1,1);
			}
			else
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
	 	}
	}


	function return_system_popup() //Return PopUp
	{
		/*if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}*/
		var page_link='requires/trims_issue_requisition_controller.php?action=delivery_system_popup&company='+document.getElementById('cbo_company_name').value;
		var title="System Popup";
		var company = $("#cbo_company_name").val();
		var txt_challan_no=$("#txt_challan_no").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var return_id=this.contentDoc.getElementById("hidden_return_id").value;
			//alert(delivery_id);return;
			if(return_id !="")
			{
				get_php_form_data(return_id, "populate_master_from_data", "requires/trims_issue_requisition_controller" );
				show_list_view(return_id,'product_details_update','requisition_details_container','requires/trims_issue_requisition_controller','');
				set_button_status(1, permission, 'fnc_requisition_entry',1,1);
			}
		}
	}

	
	function return_prod_qty_row(id)
	{
		//alert(id);
		var id=id.split('_');
		var return_qty = $("#txtreturnqty_"+id[1]).val()*1;
		//var delivery_qty = $("#txtexfactoryqty_"+id[1]).val()*1;
		var delivery_qty = $("#txtreturnqty_"+id[1]).attr('placeholder');
		if(return_qty>delivery_qty)
		{
			alert('Return qty. over is not allow than delivery qty.');
			$("#txtreturnqty_"+id[1]).val('');
			$("#txtreturnqty_"+id[1]).focus();
			return;
		}
	}

	function chk_stock(i)
	{
		var quantity=$('#txtReqQnty_'+i).val()*1;
		var inHand_qty = $("#txtReqQnty_"+i).attr('placeholder')*1;
		
		if(quantity>inHand_qty)
		{
			alert ("Req. Qty. Can not Exeed In Hand Qty.");
			$('#txtReqQnty_'+i).val('');
			return;
		}
	}
	function disable_anable()
	{
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_trim_type').attr('disabled',false);
		load_drop_down( 'requires/trims_issue_requisition_controller', 0, 'load_drop_down_location', 'location_td' );
		load_drop_down( 'requires/trims_issue_requisition_controller', 0+'__'+0, 'load_drop_down_store', 'store_id' );
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<?  echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:1450px; float:left" align="center">
        <form name="exFactory_1" id="exFactory_1" autocomplete="off" >
        <fieldset style="width:1400px;">
            <legend>Requisition Master</legend>
                <fieldset>
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="5">Requisition No</td>
                        <td colspan="5">
                          	<input name="txt_req_no" id="txt_req_no" class="text_boxes" type="text"  style="width:160px" onDblClick="return_system_popup()" placeholder="Browse or Search"  readonly="readonly" />
                          	<input name="update_id" id="update_id" class="text_boxes" type="hidden"  style="width:60px"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" align="right" class="must_entry_caption">Company Name </td>
                        <td width="160">
                            <?
                            echo create_drop_down( "cbo_company_name", 152, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/trims_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td' );",0 ); ?>
                        </td>
						<td width="100" align="right">Location</td>
                        <td width="160" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="100" align="right">Store Name</td>
                        <td width="160" id="store_id">
                          <? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="100" align="right" class="must_entry_caption">Requisition Date</td>
                        <td width="160" >
                        	<input name="txt_req_date" id="txt_req_date" class="datepicker"  style="width:160px;" placeholder="Display" readonly />
                        </td>
                        <td width="100" align="right">Delivery Date</td>
                        <td >
                        	<input name="txt_delivery_date" id="txt_delivery_date" class="datepicker"  style="width:160px;" placeholder="Display" readonly />
                        </td>
                    </tr>
					<!-- ------------------- -->
					<tr>
                        <td width="100" align="right" >Working Company </td>
                        <td width="160">
                            <? echo create_drop_down( "cbo_working_company", 152, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $working_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/trims_issue_requisition_controller', this.value, 'load_drop_down_working_location', 'working_location_td');",0 ); ?>
                        </td>
						<td width="100" align="right">Location</td>
                        <td width="160" id="working_location_td">
                           <? echo create_drop_down( "cbo_working_location", 152, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="100" align="right"> Floor</td>
                        <td width="160" id="floor_td">
                          <? echo create_drop_down( "cbo_floor_name", 152, $blank_array,"", 1, "-- Select Floor --", $selected, "" );?>
                        </td>
						<td width="100" align="right"> Sewing Line</td>
                        <td width="160" id="sewing_td">
                          <?echo create_drop_down( "cbo_sewing_line", 110, $blank_array,"", 1, "--- Select ---", $selected, "",1 );?>
                        </td>
                    </tr>
                    <tr>
						<td align="right">Trims Type</td>
						<td>
							<? echo create_drop_down( "cbo_trim_type", 152, $trim_type,"", "1", "---- Select ----", 0, "" ); ?>
						</td>
						<td align="right">Remarks</td>
						<td colspan="3">
							<input type="text" class="text_boxes" style="width:420px;" name="txt_remarks" id="txt_remarks">
						</td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center">
                        Order Number : &nbsp;&nbsp; 
                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:150px;" onDblClick="fn_order()"  placeholder="Browse" readonly />
                        <input type="hidden" id="hdn_order_id" name="hdn_order_id" />
                        </td>
                    </tr>
                </table>
                </fieldset>
                <!-- <br /> -->
                <fieldset style="width:1470px;">
                <legend>Details List View</legend>
                <table cellpadding="0" cellspacing="1" width="1470" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="110">Job No</th>
                            <th width="90">Buyer</th>
                            <th width="100">Style Ref. No.</th>
                            <th width="110">Order No </th>
                            <th width="80">Order Qty</th>
                            <th width="90">Item Group</th>
                            <th width="120">Item Des. </th>
                            <th width="80">Gmts. Color</th>
                            <th width="80">Item Color</th>
                            <th width="80">Size</th>
                            <th width="80">Consumption (Budget)</th>
                            <th width="80">Costing Per</th>
                            <th width="60">Uom</th>
                            <th width="80">Recv. Qty</th>
                            <th width="80">In Hand Qty.</th>
                            <th width="80">Cumulative Issue Qty.</th>
                            <th>Req Qty</th>
                        </tr>
                	</thead>
                 	<tbody id="requisition_details_container">
                    	<!--<tr bgcolor="#FFFFFF" id="tr_1">
                            <td id="slTd_1"></td>
                            <td id="tdJob_1"></td>
                            <td id="tdBuyer_1"></td>
                            <td id="tdStyle_1"></td>
                            <td id="tdOrder_1"></td>
                            <td id="tdOrderQnty_1"></td>
                            <td id="tdItemGroup_1"></td>
                            <td id="tdItemDescrip_1"></td>
                            <td id="tdColor_1"></td>
                            <td id="tdSize_1"></td>
                            <td id="tdUom_1"></td>
                            <td id="tdRcvQnty_1"></td>
                            <td id="tdInhand_1"></td>
                            <td id="tdReqQnty_1">
                            <input type="text" name="txtReqQnty[]" id="txtReqQnty_1" class="text_boxes_numeric"  style="width:80px" />
                            <input type="hidden" id="hdnUpdateDtlsId_1" name="hdnUpdateDtlsId[]" />
                            </td>
                        </tr>-->
                    </tbody>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="930">
                    <tr>
                        <td align="center" colspan="10" valign="middle" class="button_container">
                             <?
                                echo load_submit_buttons( $permission, "fnc_requisition_entry", 0,1,"reset_form('exFactory_1','requisition_details_container','','','disable_anable()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >

                        </td>
                    </tr>
                </table>
           </fieldset>
        </form>
    </div>

</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_name').val(0);
	$('#cbo_working_location').val(0);

</script>
</html>
