<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Roll Receive By Store
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	12-03-2015
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
<? 
    $company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
?>
  function generate_report_file(data,action)
	{
		window.open("requires/finish_feb_roll_receive_by_store_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_receive_roll_wise( operation )
	{
		if(operation==2)
		{
		show_msg('13');
		return;
		}
		if(operation==4)
		{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_delivery_print');
		return;
		}
		
	 	if(form_validation('txt_delivery_date*cbo_company_id*cbo_knitting_source*txt_challan_no','Delivery Date*Company*Knitting Source')==false)
		{
		return; 
		}
		
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
			var activeId=1; 
			}
			else
			{
			var activeId=0; 	
			}
			 var updateDetailsId=$(this).find('input[name="updateDetaisId[]"]').val();
			 var transId=$(this).find('input[name="transId[]"]').val();
			 var rollTableId=$(this).find('input[name="rollTableId[]"]').val();
			 var productionId=$(this).find('input[name="productionId[]"]').val();
			 var productionDtlId=$(this).find('input[name="productionDtlsId[]"]').val();
			 var rollId=$(this).find('input[name="rollId[]"]').val();
			 var batchId=$(this).find('input[name="batchID[]"]').val();
			 var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
		   	 var colorId=$(this).find('input[name="colorId[]"]').val();
			 var deterId=$(this).find('input[name="deterId[]"]').val();
			 var productId=$(this).find('input[name="productId[]"]').val();
			 var orderId=$(this).find('input[name="orderId[]"]').val();
			 var buyerId=$(this).find('input[name="buyerId[]"]').val();
			 var rollQty=$(this).find('input[name="rollQty[]"]').val();
			 var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			 var rollNo=$(this).find("td:eq(2)").text();
			 var rolldia=$(this).find("td:eq(9)").text();
			 var rollGsm=$(this).find("td:eq(8)").text();
			 var currentWgt=$(this).find('input[name="currentQty[]"]').val();
			 var rejectQty=$(this).find("td:eq(11)").text();
			 var room=$(this).find('input[name="roomName[]"]').val();
			 var rack=$(this).find('input[name="rackName[]"]').val();
			 var self=$(this).find('input[name="selfName[]"]').val();
			 var job_no=$(this).find('input[name="JobNumber[]"]').val();
			 var wideTypeId=$(this).find('input[name="wideTypeId[]"]').val();
			 var systemId=trim($(this).find("td:eq(21)").text());
			j++;
			dataString+='&rollId_' + j + '=' + rollId  + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '='+colorId  + '&productId_' + j + '='
			+ productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&rollQty_' + j + '=' + rollQty + '&currentWgt_' + j + '=' 
			+ currentWgt+ '&deterId_' + j + '=' + deterId +'&rejectQty_' + j + '=' + rejectQty+ '&job_no_' + j + '=' + job_no+ '&room_' + j + '=' +
			room + '&rolldia_' + j + '=' + rolldia + '&rack_' + j + '=' + rack+ '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' 
			+ activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+ '&systemId_' + j + '=' + systemId+ '&batchId_' + j + '=' 
			+ batchId+ '&self_' + j + '=' + self+ '&productionId_' + j + '=' + productionId+ '&productionDtlId_' + j + '=' + productionDtlId+ '&wideTypeId_' + j + '=' + wideTypeId+ '&buyerId_' + j + '=' + buyerId+ '&rollTableId_' + j + '=' + rollTableId+ '&transId_' + j + '=' + transId;
		});
		
		if(j<1)
		{
			alert('No data found');
			return;
		}
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_knitting_source*knit_company_id*update_id*txt_system_no',"../../")+dataString;
		//alert(data);
		http.open("POST","requires/finish_feb_roll_receive_by_store_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		if((response[0]==0 || response[0]==1))
		{
		document.getElementById('update_id').value = response[1];
		document.getElementById('txt_system_no').value = response[2];
		
		if(trim(response[3])!="")
		{
			var all_id=(response[3]).split(",");
			var k=0;
			for(k=1;k<=all_id.length;k++)
			{
			var tr_id=(all_id[k-1]).split("__");
			$("#updateDetaisId_"+tr_id[0]).val(tr_id[1]);
			$("#transId_"+tr_id[0]).val(tr_id[2]);
			$("#rollTableId_"+tr_id[0]).val(tr_id[3]);
			$("#rollQty_"+tr_id[0]).val(tr_id[4]);
			}
		}
		set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
		$("#btn_fabric_details").removeClass('formbutton_disabled');
		$("#btn_fabric_details").addClass('formbutton');
		
		}
		release_freezing();
		}
	}

	function openmypage_challan()
	{
		var page_link='requires/finish_feb_roll_receive_by_store_controller.php?action=challan_popup'; 
		var title="Search Challan Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=370px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var grey_recv_no=this.contentDoc.getElementById("hidden_challan_no").value.split("_");
			var grey_recv_id=this.contentDoc.getElementById("hidden_challan_id").value;
			$("#txt_challan_no").val(grey_recv_no[0]);
			$("#cbo_company_id").val(grey_recv_no[1]);  
			$("#cbo_knitting_source").val(grey_recv_no[2]); 
			$("#knit_company_id").val(grey_recv_no[3]); 
			$("#txt_knitting_company").val(grey_recv_no[4]); 
			show_list_view(grey_recv_no[0],'finish_item_details','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
			set_button_status(0, permission, 'fnc_grey_receive_roll_wise',1);
			release_freezing();
		}
	}

	$('#txt_challan_no').live('keydown', function(e)
	 {
	 if (e.keyCode === 13)
	 {
	 e.preventDefault();
	 scan_challan_no(this.value); 
	 }
	});	


 	function scan_challan_no(str)
	{
		var response=return_global_ajax_value(str, 'check_challan_no', '', 'requires/finish_feb_roll_receive_by_store_controller');
		if(response==0)
		{
		alert("All Barcode In This Challan Are Saved")	
		$("#txt_challan_no").val('');
		}
		else
		{
		show_list_view(str,'finish_item_details','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
		get_php_form_data(str, "load_php_form", "requires/finish_feb_roll_receive_by_store_controller" );
		}
   }

	function open_mrrpopup()
	{
		var page_link='requires/finish_feb_roll_receive_by_store_controller.php?&action=update_system_popup';
		var title='Finish Fabric Receive By Store';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_no=this.contentDoc.getElementById("hidden_receive_no").value;
			var grey_recv_id=this.contentDoc.getElementById("hidden_update_id").value;
			var grey_challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
			$("#txt_system_no").val(grey_recv_no);
			$("#txt_challan_no").val(grey_challan_no);
			$("#update_id").val(grey_recv_id);
			$("#txt_challan_no").attr('disabled','true');
			if(trim(grey_recv_id)!="")
			{
			show_list_view(grey_challan_no+"_"+grey_recv_id,'finish_item_details_update','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
			get_php_form_data(grey_recv_id, "load_php_form_update", "requires/finish_feb_roll_receive_by_store_controller" );
			set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
			$("#btn_fabric_details").removeClass('formbutton_disabled');
			$("#btn_fabric_details").addClass('formbutton');
			}
		}
		
	}
     function copy_all(str)
	 {
		
		var data=str.split("_");
		var trall=$("#list_view_container tr").length;
		var copy_tr=parseInt(trall);
		if(data[1]==0) data_value=$("#roomName_"+data[0]).val();
		if(data[1]==1) data_value=$("#rackName_"+data[0]).val();
		if(data[1]==2) data_value=$("#selfName_"+data[0]).val();
		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
		if(data[1]==0) 	$("#roomName_"+k).val(data_value);
		if(data[1]==1) 	$("#rackName_"+k).val(data_value);
		if(data[1]==2) 	$("#selfName_"+k).val(data_value);
		}
	 }
	function set_focus()
	{
	$("#txt_challan_no").focus();	
	}
	
	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
	
	
</script>
</head>

<body onLoad="set_hotkey(); set_focus()">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Issue Challan Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
     
                        <td align="right" colspan="3" width="100">Receive No</td>
                        <td colspan="3" align="left">
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="open_mrrpopup()" placeholder="Browse For System No" />
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"  width="100">Challan No</td>
                        <td  align="left">
                        	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_challan()" placeholder="Scan/Browse/Write" /></td>
                        <td align="right" class="must_entry_caption" width="">Receive Date</td>
                        <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;"  /></td>
                        <td align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Display--", 0, "",1 );//$company_cond 
                            ?>
                        </td>
                    </tr>
                    <tr>
                      <td align="right">Prod. Source </td>
                        <td>
							<? 
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); 
							?>
                        </td>
                    <td align="right">Dye/Finish Company</td>
                        <td id="knitting_com"> 
                         <input type="text" name="txt_knitting_company" id="txt_knitting_company" class="text_boxes" style="width:140px;"  disabled readonly placeholder="Display" />
                        	
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>
                      
                    </tr>
                   
                </table>
			</fieldset> 
            <br/>
              <fieldset style="width:1260px;text-align:left">
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
                        <th width="80">Construction</th>
                        <th width="80"> Composition</th>
                        <th width="70">Color</th>
                        <th width="40">GSM</th>
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
                                <td width="80" id="cons_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" id="comps_1" style="word-break:break-all;" align="left"></td>
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
                                <td width="" id="systemId_1" style="word-break:break-all;">
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
                     <table width="1330" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <? 
                               echo load_submit_buttons($permission,"fnc_grey_receive_roll_wise",0,1,"",1);
                            ?>
                            <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
                        </td>
                    </tr>  
                </table>
                </div>
              </fieldset>  
                  <!-- ========================== Child table end ============================ -->   

    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>

		</form>
	</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
