<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Grey Fabric Issue Entry
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	21-03-2015
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
echo load_html_head_contents("Grey Issue Info","../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 
<? 
    $company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
?>
 //alert(issue_details_arr )
    function generate_report_file(data,action)
	{
		window.open("requires/finish_feb_receive_by_cutting_controller.php?data=" + data+'&action='+action, true );
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'roll_receive_print');
			return;
		}
		
	 	if(form_validation('txt_delivery_date','Delivery Date')==false)
		{
			return; 
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_delivery_date').val(), current_date)==false)
		{
			alert("Delivery Date Can not Be Greater Than Current Date");
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
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var rollwgt=$(this).find('input[name="rolWgt[]"]').val();
			var rolldia=$(this).find('input[name="rollDia[]"]').val();
			var rollGsm=$(this).find('input[name="rollGsm[]"]').val();
			var receiveBasis=$(this).find('input[name="receiveBasis[]"]').val();
			var knittingSource=$(this).find('input[name="knittingSource[]"]').val();
			var knittingComp=$(this).find('input[name="knittingComp[]"]').val();
			var job_no=$(this).find('input[name="jobNo[]"]').val();
			var bookingNo=$(this).find('input[name="bookingNo[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodNumber[]"]').val();
			var rollNo=$(this).find('input[name="rollNo[]"]').val();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var widthType=$(this).find('input[name="widthType[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();
			var bwoNo=$(this).find('input[name="bwoNo[]"]').val();
			var booking_without_order_status=$(this).find('input[name="booking_without_order_status[]"]').val();
			
			j++;
			dataString+='&rollId_' + j + '=' + rollId + '&buyerId_' + j + '=' + buyerId + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '=' +
			colorId  + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&knittingSource_' + 
			j + '=' + knittingSource + '&knittingComp_' + j + '=' + knittingComp+ '&deterId_' + j + '=' + deterId+ '&receiveBasis_' + j + '=' + receiveBasis+ '&job_no_' + j + '=' + job_no+ '&rollwgt_' + j + '=' + rollwgt + '&rolldia_' + j + 
			'=' + rolldia + '&bookingNo_' + j + '=' + bookingNo+ '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' + activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+'&batchId_' + j + '=' + batchId+'&rolltableId_' + j + '=' + rolltableId+'&widthType_' + j + '=' + widthType+ '&IsSalesId_' + j + '=' + IsSalesId+ '&bwoNo_' + j + '=' + bwoNo+ '&booking_without_order_status_' + j + '=' + booking_without_order_status;			
		});
		if(j<1)
		{
			alert('No data found');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_knitting_source*cbo_knitting_company*update_id*txt_system_no*cbo_basis*txt_batch_no*txt_reqsn_no',"../")+dataString;
		//alert(data); return;
		http.open("POST","requires/finish_feb_receive_by_cutting_controller.php",true);
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
				details_id=response[3].split(",");
				var j=0;
			    for(j=0;j<details_id.length;j++)
				{
					var id_all=details_id[j].split("#");
					$('#updateDetaisId_'+id_all[2]).val(id_all[0]);
					$('#rolltableId_'+id_all[2]).val(id_all[1]);
				}
				}
				set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			}
			release_freezing();
		}
	}
	
 





	function grey_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var page_link='requires/finish_feb_receive_by_cutting_controller.php?cbo_company_id='+cbo_company_id+'&action=challan_popup';
		var title='Grey Receive Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_no=this.contentDoc.getElementById("hidden_challan_no").value;
			var grey_recv_id=this.contentDoc.getElementById("hidden_challan_id").value;
			var is_sales=this.contentDoc.getElementById("hidden_is_sales").value;

			$("#txt_challan_no").val(grey_recv_no);
			if(trim(grey_recv_id)!="")
			{
				show_list_view(grey_recv_no+'_'+is_sales, 'grey_item_details', 'scanning_tbl','requires/finish_feb_receive_by_cutting_controller', '' );
				get_php_form_data(grey_recv_no, "load_php_form", "requires/finish_feb_receive_by_cutting_controller" );
				//set_button_status(1, permission, 'fnc_grey_fabric_receive',1,1);
				//release_freezing();

				
			}

						 
		}
		
	}

function openmypage_challan()
{
	var company=1;
	var page_link='requires/finish_feb_receive_by_cutting_controller.php?action=mrr_popup&company='+company; 
	var title="Search Challan Popup";
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		//var sysNumber=this.contentDoc.getElementById("hidden_sys_number").value; // system number
		$("#txt_system_no").val(sysNumber);		
		get_php_form_data(sysNumber, "populate_data_from_data", "requires/grey_fabric_issue_controller");	 
		show_list_view($("#hidden_system_id").val(),'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
 		$("#child_tbl").find('input,select').val('');
		$("#display").find('input,select').val('');
		
		var issuePurpose=$("#cbo_issue_purpose").val();
		if(issuePurpose!=8)
		{
			$("#color_td").html('<? echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');


		}

	
		//set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,1);
		//enable_disable();
  	}
}



	$('#txt_challan_no').live('keydown', function(e) {
		   
			if (e.keyCode === 13) {
				e.preventDefault();
			   scan_challan_no(this.value); 
			}
		});	


  function scan_challan_no(str)
		 {
	
			show_list_view(str, 'grey_item_details', 'scanning_tbl','requires/finish_feb_receive_by_cutting_controller', '' );
			get_php_form_data(str, "load_php_form", "requires/finish_feb_receive_by_cutting_controller" );
			
		 }

	function open_mrrpopup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var garments_nature =2;
	
		 	
			var page_link='requires/finish_feb_receive_by_cutting_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=update_system_popup';
			var title='Grey Receive Form';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
				var theform=this.contentDoc.forms[0];
				
				var grey_recv_no=this.contentDoc.getElementById("hidden_receive_no").value;
				var grey_recv_id=this.contentDoc.getElementById("hidden_update_id").value;
				var grey_challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
				$("#txt_system_no").val(grey_recv_no);
				$("#txt_challan_no").val(grey_challan_no);
				
				if(trim(grey_recv_id)!="")
				{
					show_list_view(grey_recv_id, 'grey_item_details_update', 'scanning_tbl','requires/finish_feb_receive_by_cutting_controller', '' );
					get_php_form_data(grey_recv_id, "load_php_form_update", "requires/finish_feb_receive_by_cutting_controller" );
					set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
					$("#btn_fabric_details").removeClass('formbutton_disabled');
					$("#btn_fabric_details").addClass('formbutton');
					//release_freezing();
				}
							 
			}
		
	}
   function change_cursore()
   {
	 $("#txt_challan_no").focus(); 
   }

	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
	
</script>
</head>

<body onLoad="set_hotkey();change_cursore();">
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
                        	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="grey_receive_popup()" placeholder="Scan/Browse/Write" />
                        <td align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Display--", 0, "",1 );//$company_cond 
                            ?>
                        </td>
                        <td align="right">Dyeing Source </td>
                        <td>
							<? 
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); 
							?>
                        </td>
                        
                    </tr>
                    <tr>
                    <td align="right">Dyeing Company</td>
                        <td id="knitting_com">
                        	<?
							 echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
							 
							 ?>
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>
                      <td align="right" class="must_entry_caption" width="100">Receive Date</td>
                        <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;"  /></td>
                        <td width="120" align="right" >Purpose</td>
                        <td width="" id="issue_purpose_td">
                            <? 
							echo create_drop_down( "cbo_basis", 152, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 1, "",1,"11,3,4,8" ); 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" >Batch No&nbsp;&nbsp;</td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" onDblClick="" placeholder="Display"/>
                            <input type="hidden" name="txt_batch_id" id="txt_batch_id"/>
                            
                        </td>
                        <td align="right" >Requsn No&nbsp;&nbsp;</td>
                        <td>
                            <input type="text" name="txt_reqsn_no" id="txt_reqsn_no" class="text_boxes" style="width:140px;" onDblClick="" placeholder="Display"/>
                             <input type="hidden" name="txt_reqsn_id" id="txt_reqsn_id"/>
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
				<table cellpadding="0" width="1300" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="50">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="60">Batch No</th>
                        <th width="90">Body Part</th>
                        <th width="110">Const./ Composition</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="70">Color</th>
                        <th width="70">Dia/  Width Type</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="50">Job No</th>
                        <th width="50">Year</th>
                        <th width="65">Buyer</th>
                        <th width="80">Order No</th>
                        <th width="80">Kniting Com</th>
                        <th width="100">Program/ Booking /Pi No</th>
                        <th width="">Opening / Independent</th>
                    </thead>
                 </table>
                 <div style="width:1330px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1300" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="50" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="50" id="rollNo_1"></td>
                                <td width="60" id="batchNo_1" style="word-break:break-all;"></td>
                                <td width="90" id="bodypart_1" style="word-break:break-all;"></td>
                                <td width="110" id="cons_1"></td>
                                <td width="50" id="gsm_1"></td>
                                 <td width="50" id="dia_1"></td>
                                <td width="70" id="color_1"></td>
                                <td width="70" id="widthType_1"></td>
                               
                                <td width="60" id="rollWgt_1"></td>

                                <td width="50" id="job_1"></td>
                                <td width="50" id="year_1" align="center"></td>
                                <td width="65" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" id="knitcom_1" style="word-break:break-all;" align="left"></td>
                           
                                <td width="100" id="bookProgram_1" style="word-break:break-all;" align="left"></td>
                                <td width="" id="basis_1" style="word-break:break-all;">
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="productionId[]" id="productionId_1"/>
                                    <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                                    <input type="text" name="bwoNo[]" id="bwoNo_1"/>
                                    <input type="text" name="booking_without_order_status[]" id="booking_without_order_status_1"/>
                               </td> 
                            </tr>
                        </tbody>

                         <tfoot>
                            <tr>
                                <th colspan="10">Total</th>
                                <th id="total_rollWgt"></th>
                            </tr>
                        </tfoot> 

                	</table>

                     <table width="1000" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
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

    		</div>
		</form>
	</div>    
</body>  

<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
