<?
/** -------------------------------------------- Comments
* Purpose			: 	Grey Fabric Bar-code Striker Export Report
* Functionality	:	
* JS Functions	:
* Created by		:	Jahid Hasan
* Creation date 	: 	07-01-2017
* Updated by 		: 	
* Update date		: 	
* QC Performed BY	:		
* QC Date			:	
* Comments		:
*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Daily Batch Creation Report", "../../", 1, 1,'','','',1);
	?>
	<script type="text/javascript">

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
		var permission='<? echo $permission; ?>';



		function generate_report_file(data,action)
		{
			window.open("requires/grey_fabric_barcode_sticker_export_report_controller.php?data=" + data+'&action='+action, true );
		}


		function openmypage_html()
		{
			var cbo_company_id = $('#cmbCompanyName').val();
			var txtBatchNo = $('#txtBatchNo').val();
			var hdnBatchId = $('#hdnBatchId').val();
			var txtBarcodeNo = $('#txtBarcodeNo').val();
			if(hdnBatchId=="")
			{
				if(form_validation('cmbCompanyName*txtBatchNo','Company Name*Batch No')==false)
				{
					return;
				}
			}

			var report_title=$( "div.form_caption" ).html();
			generate_report_file(cbo_company_id+'*'+txtBatchNo+'*'+hdnBatchId+'*'+txtBarcodeNo+'*'+report_title+'*'+'print2','barcode_hmtl_print');
			return;
		}

		function openmypage_batchNo()
		{

			var cbo_company_id = $('#cmbCompanyName').val();	
			if(form_validation('cmbCompanyName','Company Name')==false)
			{
				return;
			}
			var title = 'Grey Fabric Bar-code Striker Export Report';	
			var page_link = 'requires/grey_fabric_barcode_sticker_export_report_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';		  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=470px,center=1,resize=1,scrolling=0','../');		
			emailwindow.onclose=function()
			{
				var batch_number 	= document.getElementById('txtBatchNo').value;
				var barcode_number  = document.getElementById('txtBarcodeNo').value;
				document.getElementById('txtBatchNo').value = this.contentDoc.getElementById("selected_batch_no").value;
				document.getElementById('hdnBatchId').value = this.contentDoc.getElementById("selected_batch_id").value;
				var batch_id = this.contentDoc.getElementById("selected_batch_id").value;
				release_freezing();
				getBarcodeStickerInfo(batch_id, batch_number, barcode_number);
			}
		}

		function fnc_send_printer_text()
		{
			if(form_validation('cmbCompanyName','Company Name')==false)
			{
				return;
			}
			var batch_number='';
			var barcode_number='';
			var batch_id = $("#hdnBatchId").val();
			//alert(batch_id);
			//if(batch_id == ""){
				batch_number 	= $("#txtBatchNo").val();
				barcode_number  = $("#txtBarcodeNo").val();
				if(batch_number == '' && barcode_number == ''){
					alert('Batch No Or Barcode is required.');
					return;
				}
			//}
			var data = $("#hdnRollIds").val() + "***" + batch_number + "***" + barcode_number;
			var url = return_ajax_request_value( data, "report_barcode_text_file", "requires/grey_fabric_barcode_sticker_export_report_controller");
			window.open("requires/"+trim(url)+".zip","##");
		}

		function getBarcodeStickerInfo(batch_id, batch_number, barcode_number) {
			var data="action=get_batch_barcodes&batch_id=" + batch_id + "&barcode_number=" + barcode_number + "&batch_number=" + batch_number;		
			http.open("POST","requires/grey_fabric_barcode_sticker_export_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = grey_fabric_barcode_sticker_export_report_Reply_info;
		}

		function grey_fabric_barcode_sticker_export_report_Reply_info()
		{
			if(http.readyState == 4)
			{
				if(http.responseText == "Not Found"){
					alert("Invalid Barcode");
					return;
				}else{
					document.getElementById('hdnRollIds').value = http.responseText;
				}
				release_freezing();
			}
		}

	</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
			<h3 style="width:550px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel" >      
				<fieldset style="width:550px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption">Company Name</th>
                            <!-- <th>Buyer</th>
                            <th>Job No</th>
                            <th>Order No</th> -->
                            <th>Batch No</th>
                            <th>Barcode No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                        	<tr>
                        		<td> 
                        			<?
                        			echo create_drop_down( "cmbCompanyName", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                        			?>
                        		</td>                               
                                <!-- <td id="cbo_buyer_name_td">
                                	<? //echo create_drop_down( "cmbBuyerName", 170, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?>
                                </td>                               
                                <td>
                                     <input type="text"  name="txtJobNo" id="txtJobNo" class="text_boxes" style="width:85%;" tabindex="1" placeholder="Write/Browse" onDblClick="jobnumber();">
                                     <input type="hidden" name="hdnJobNo" id="hdnJobNo">
                                </td>                                
                                <td>
                                     <input type="text"  name="txtOrderNo" id="txtOrderNo" class="text_boxes" style="width:85%;" tabindex="1" placeholder="Write/Browse" onDblClick="openmypage_order()">
                                     <input type="hidden" name="hdnOrderNo" id="hdnOrderNo">
                                 </td> -->
                                 <td>
                                 	<input type="text"  name="txtBatchNo" id="txtBatchNo" class="text_boxes" style="width:85%;" tabindex="1" placeholder="Browse" onDblClick="openmypage_batchNo();" readonly>
                                 	<input type="hidden" name="hdnBatchId" id="hdnBatchId">
                                 </td>
                                 <td>
                                 	<input type="text"  name="txtBarcodeNo" id="txtBarcodeNo" class="text_boxes" style="width:85%;" tabindex="1" placeholder="Write">
                                 </td>
                                 <td>
                                 	<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fnc_send_printer_text()" />
                                 	<input type="button" name="print2" id="print2" value="HTML Print" class="formbutton" style=" width:100px" onClick="openmypage_html()" >
                                 	<input type="hidden" name="hdnRollIds" id="hdnRollIds">
                                 </td>
                             </tr>

                         </tbody>
                     </table>
                     <br />
                 </fieldset>
             </div>
             <div id="report_container" style="width:1100px; margin:0 auto;"></div>
             <div id="report_container2" style="width:1100px; margin:0 auto; text-align:center;"></div>
         </form>
     </div>

 </body>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>