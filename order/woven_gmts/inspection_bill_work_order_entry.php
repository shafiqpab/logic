<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create Inspection Bill Work Order
Functionality	 :
JS Functions	 :
Created by		 : Md Mamun Ahmed Sagor
Creation date 	 : 24-01-2023
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
Report Created BY:
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Inspection Bill Work Order", "../../", 1, 1,$unicode,1,'');


?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';



   var str_item_color= [<? echo substr(return_library_autocomplete( "select color_name from  lib_color  group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_color").autocomplete({
			 source: str_item_color
		  });
     });

function openmypage_wovalue(jobIds,status)
{
	var wo_value = $("#txt_wo_value").val();
    var txt_job_no=$("#txt_job_no").val();
	var txt_workorder_no=$("#txt_workorder_no").val();
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var job_ids="";
	if(status==2){
		 job_ids +=","+jobIds;
	}
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_currency = $("#cbo_currency").val();
	var workorder_date = $("#txt_workorder_date").val();
	var cbo_level = $("#cbo_level").val();
	var title = 'Job Selection Popup';
	var page_link = 'requires/inspection_bill_work_order_entry_controller.php?cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_workorder_no='+txt_workorder_no+'&cbo_level='+cbo_level+'&action=po_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=480px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{		
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") ;
		var selectedJobId=this.contentDoc.getElementById("selected_job_id") ;
		var job_no=theemail.value;
		var job_id=selectedJobId.value;
	
		$("#txt_job_id_breakdown").val(job_id+job_ids);
		$.ajax({
		url: "requires/inspection_bill_work_order_entry_controller.php?action=show_dtls_list_view&job_ids="+job_id+job_ids+'&cbo_currency='+cbo_currency+'&workorder_date='+workorder_date+'&cbo_level='+cbo_level,
			success: function(job_no){
				console.log(job_no);
				document.getElementById('booking_list_view_list').innerHTML=job_no;
			}
		});

	}
}


function openmypage()
{
	var txt_workorder_no=$("#txt_workorder_no").val();
	if (form_validation('cbo_company_name*txt_workorder_no','Company Name*Wo No')==false)
	{
	return;
	}
	var cbo_company_name = $("#cbo_company_name").val();
	var title = 'Job Selection Popup';
	var page_link = 'requires/inspection_bill_work_order_entry_controller.php?cbo_company_name='+cbo_company_name+'&txt_workorder_no='+txt_workorder_no+'&action=order_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=480px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			$("#txt_job_no").val(theemail.value);
			release_freezing();
		}
	}
}



function openmypage_booking()
{
	var cbo_company_name = $("#cbo_company_name").val();
	var page_link = 'requires/inspection_bill_work_order_entry_controller.php?cbo_company_name='+cbo_company_name+'&action=workorder_popup';
	var title='Print Booking Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1270px,height=470px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
		system_data=theemail.value.split('_');
		$("#txt_workorder_no").val(system_data[1]);
		get_php_form_data( theemail.value, "load_php_mst_data", "requires/inspection_bill_work_order_entry_controller" );
		show_list_view(system_data[0],'load_dtls_data_view','data_panel','requires/inspection_bill_work_order_entry_controller','setFilterGrid(\'list_view\',-1)');
		set_button_status(1, permission, 'fnc_fabric_booking',1);
		release_freezing();

		}
	}
}


function fnc_fabric_booking( operation )
{
	if (form_validation('cbo_company_name*cbo_supplier*txt_workorder_date*cbo_currency*cbo_pay_mode','Company Name*Test Company*WO Date*Currency*Pay Mode')==false)
	{
		return;
	}
	else
	{		
		var data_all="";
		var row_num=$('#txtRowId').val();
		var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('cbo_company_name*txt_workorder_no*cbo_supplier*txt_workorder_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_attention*txt_remarks*update_id*txt_job_id_breakdown*cbo_level',"../../");
		if(row_num <1){
			alert("Select Item");
			release_freezing();
			return;
		}
		for (var i=1; i<=row_num; i++){
		
		if (form_validation('cbo_inspection_id_'+i+'*txt_inspection_qnty_'+i+'*txt_inspection_rate_'+i+'*txt_style_desc_'+i,'Inspection For*Inspection Qnty*Inspection Rate*Style Description')==false)
		{
			return;
		}
	/* 	var cbo_level=document.getElementById('cbo_level').value;
		if(cbo_level==1){ */
			data_all+=get_submitted_data_string('txt_job_no_'+i+'*txt_job_id_'+i+'*txt_order_id_'+i+'*txt_order_no_'+i+'*cbo_buyer_id_'+i+'*txt_style_ref_'+i+'*txt_style_desc_'+i+'*cbo_inspection_id_'+i+'*txt_inspection_qnty_'+i+'*txt_inspection_rate_'+i+'*txt_amount_'+i+'*txt_discount_'+i+'*txt_discount_value_'+i+'*txt_insp_val_with_vat_'+i+'*txt_vat_'+i+'*txt_vat_amount_'+i+'*txt_insp_val_without_vat_'+i+'*txt_remarks_'+i+'*update_dtls_id_'+i+'*txtRowId',"../../",i);
		/* }
		if(cbo_level==2){
			data_all+=get_submitted_data_string('txt_job_no_'+i+'*txt_job_id_'+i+'*cbo_buyer_id_'+i+'*txt_style_ref_'+i+'*txt_style_desc_'+i+'*cbo_inspection_id_'+i+'*txt_inspection_qnty_'+i+'*txt_inspection_rate_'+i+'*txt_amount_'+i+'*txt_discount_'+i+'*txt_discount_value_'+i+'*txt_insp_val_with_vat_'+i+'*txt_vat_'+i+'*txt_vat_amount_'+i+'*txt_insp_val_without_vat_'+i+'*txt_remarks_'+i+'*update_dtls_id_'+i+'*txtRowId',"../../",i);
		} */
		
	}
		data +=data_all;
		freeze_window(operation);
		$.ajax({
		url: 'requires/inspection_bill_work_order_entry_controller.php',
		type: 'POST',
		data: data,
		success: function(data){
		var reponse=trim(data).split('**');
		show_msg(reponse[0]);
		if(reponse[0]==14)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_supplier").attr("disabled",true);
			document.getElementById('txt_workorder_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
			set_button_status(1, permission, 'fnc_fabric_booking',1);
	
			show_list_view(reponse[2],'load_dtls_data_view','data_panel','requires/inspection_bill_work_order_entry_controller','setFilterGrid(\'list_view\',-1)');
			//reset_form('printbooking_1', '', '');
			reset_form('printbooking_2','','','');
			
		}
		}
		});
		release_freezing();
 
	}
}

function calculate_wo_value(row,str){
	var rate=0; var qnty=0; var amount=0; var discount=0; var discount_val=0; var txt_insp_val_with_vat=0; var vat_per=0; var vat_val=0; var txt_insp_val_without_vat=0;
	rate=$("#txt_inspection_rate_"+row).val()*1;
	qnty=$("#txt_inspection_qnty_"+row).val()*1;
	qnty=Math.round(qnty);
	rate=(rate).toFixed(4);

	$("#txt_inspection_rate_"+row).val(rate);
	$("#txt_inspection_qnty_"+row).val(qnty);

	amount=(qnty*rate).toFixed(2);;
	$("#txt_amount_"+row).val(amount);

	discount=$("#txt_discount_"+row).val()*1;
	discount=(discount).toFixed(2);
	$("#txt_discount_"+row).val(discount);

	var  disc_val=$("#txt_discount_value_"+row).val()*1;

	discount_val=((amount*discount)/100).toFixed(4);		
	//change the condition for issue id 9225
	if(discount <=0 && str=="dis_amt"){

		discount_val=$("#txt_discount_value_"+row).val()*1;
		discount_val=(discount_val).toFixed(4);	
		discount=((discount_val/amount)*100).toFixed(2);
		$("#txt_discount_"+row).val(discount);
		$("#txt_discount_value_"+row).val(discount_val);
	}
	if(str=="update_qnty"){
		$("#txt_discount_value_"+row).val(discount_val);
	}

	if(discount >0 && str=="discount"){
		$("#txt_discount_value_"+row).val(discount_val);
	}

	if(discount >0 && str=="dis_amt"){
			
		$("#txt_discount_value_"+row).val(disc_val);
			discount=((disc_val/amount)*100).toFixed(2);
			discount_val=(disc_val).toFixed(4);
			$("#txt_discount_"+row).val(discount);
	}

	txt_insp_val_with_vat=(amount-discount_val).toFixed(2);	
	$("#txt_insp_val_with_vat_"+row).val(txt_insp_val_with_vat);		

			
	vat_per=$("#txt_vat_"+row).val()*1;
	vat_per=(vat_per).toFixed(2);
	$("#txt_vat_"+row).val(vat_per);
	vat_val=((txt_insp_val_with_vat*vat_per)/100).toFixed(2);
	var vat_amt=$("#txt_vat_amount_"+row).val()*1;

	if(vat_per <=0 && vat_amt >0 ){
		var vat_val=$("#txt_vat_amount_"+row).val()*1;
		vat_per=((vat_val/txt_insp_val_with_vat)*100).toFixed(2);
		$("#txt_vat_"+row).val(vat_per);
	}else{
		$("#txt_vat_amount_"+row).val(vat_val)*1;
	}

	if(vat_per >0 && vat_amt >0 && str=="vat"){
		$("#txt_vat_amount_"+row).val(vat_val);
	}

	if(vat_per >0 && vat_amt >0 && str=="vat_amt"){
		$("#txt_vat_amount_"+row).val(vat_amt);
		vat_val=(vat_amt).toFixed(2);
		vat_per=((vat_amt/txt_insp_val_with_vat)*100).toFixed(2);
		$("#txt_vat_"+row).val(vat_per);
	}

	var txt_insp_val_without_vat=(txt_insp_val_with_vat-vat_val).toFixed(2);
		
	$("#txt_insp_val_without_vat_"+row).val(txt_insp_val_without_vat);		
}

function fnResetForm()
{
	reset_form('printbooking_1','list_container','','txt_booking_date,<? echo date("d-m-Y"); ?>','disable_enable_fields("txt_job_no*txt_workorder_no*cbo_supplier*txt_workorder_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_attention*txt_remarks*update_id*txt_job_id_breakdown*cbo_level",0)','');
	set_button_status(0, permission, 'fnc_yarn_dyeing',1,0);
}

function new_print_btn_fnc(type) 
{
	if($('#txt_workorder_no').val()=='')
	{
		alert('Wo No Not found.Please,Browse Wo No.');
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	if(type==1){
	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_workorder_date').val()+'*'+report_title, "show_trim_booking_report_new","requires/inspection_bill_work_order_entry_controller" ) ;
	}
	return;
}

function func_currency(id)
{	
	if(id==1){
		$(".currencyName").html("(BDT)");
	}else if(id==2){
		$(".currencyName").html("(USD)");
	}else if(id==3){
		$(".currencyName").html("(EURO)");
	}else if(id==4){
		$(".currencyName").html("(CHF)");
	}else if(id==5){
		$(".currencyName").html("(SGD)");
	}else if(id==6){
		$(".currencyName").html("(POUND)");
	}else if(id==7){
		$(".currencyName").html("(YEN)");
	}

}
function fnc_source_for(val)
	{
		if(val==2){
			$(".order_show").hide();;
		}else{
			$(".order_show").show();;
		}
	}
</script>

</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form name="printbooking_1"  autocomplete="off" id="printbooking_1">
    <fieldset style="width:1000px;">
    <legend>Inspection Bill Work Order</legend>
        <table  width="1000" cellspacing="2" cellpadding="0" border="0" >
            <tr>
				<td width="110" class="must_entry_caption">Wo No</td>
				<td width="130"> <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking()" readonly placeholder="Double Click for Booking" name="txt_workorder_no" id="txt_workorder_no"/> 					 
                	<input type="hidden" id="update_id" name="update_id">
					<input type="hidden" id="txt_job_id_breakdown" name="txt_job_id_breakdown">
				</td>
                <td width="140" class="must_entry_caption">Company Name</td>
                <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", $selected, "","","" ); ?></td>
                <td width="110" class="must_entry_caption">Insp. Company</td>
                <td width="140"><?=create_drop_down( "cbo_supplier", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select Test Company--", 0, "","" ); ?></td>
                <td width="100" class="must_entry_caption">WO Date</td>
                <td width="140"><input class="datepicker" type="text" style="width:100px" name="txt_workorder_date" id="txt_workorder_date" onChange="set_conversion_rate($('#cbo_currency').val(),this.value,'../../','txt_exchange_rate');" value="<?=date("d-m-Y")?>" disabled /></td>              
				<td class="must_entry_caption" >Currency</td>
                <td><?=create_drop_down( "cbo_currency", 110, $currency,"", 1, "-- Select --",'', "set_conversion_rate(this.value, $('#txt_workorder_date').val(),'../../','txt_exchange_rate');func_currency(this.value)",0 ); ?></td>
            </tr>
            <tr>
				<td class="must_entry_caption">Pay Mode</td>
                <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --","", "","" ); ?></td>
               
                <td>Attention</td>
                <td><input   type="text" style="width:120px;"  name="txt_attention" id="txt_attention"  class="text_boxes"/></td>

                <td>Exchange Rate</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate"  id="txt_exchange_rate"  onChange="check_exchange_rate();"  readonly/></td>
				<td>Level</td>
				<td>
				<?
				$level_arr=array(1=>"PO Level",2=>"Job Level");
				echo create_drop_down( "cbo_level", 110, $level_arr,"", 0, "", "2", "fnc_source_for(this.value)","","" );
				?>
				</td>
              
            </tr>
            <tr>
                <td>Remarks</td>
                <td colspan="7"><input class="text_boxes" type="text" style="width:700px;"  name="txt_remarks" id="txt_remarks" /></td>
            </tr>		
        </table>
    </fieldset>
</form>
<br/>
<fieldset style="width:1000px;">
<legend>Details</legend>
    <form id="printbooking_2" name="printbooking_2" autocomplete="off">
       <br>
        <table class="rpt_table" width="1000" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_list_search">
            <thead>
                <tr>
                	<th class="must_entry_caption">Job No</th>
					<th style="display:none;" class="order_show" id="order_show">Order No</th>
                    <th class="must_entry_caption">Buyer</th>
                    <th class="must_entry_caption">Style Ref</th>
                    <th class="must_entry_caption">Style Description</th>
                    <th class="must_entry_caption">Insfection For</th>
                    <th class="must_entry_caption">Insp. Qty</th>
                    <th class="must_entry_caption">Insp. Rate</th>
                    <th>Amount <span class="currencyName">(USD)</span></th>
                    <th>Discount %</th>
                    <th>Discount <span class="currencyName">(USD)</span></th>
                    <th>Total Insp. Value <span class="currencyName">(USD)</span></th>
                    <th>Vat %</th>
					<th>Vat Amount <span class="currencyName">(USD)</span></th>
                    <th>Net Insp. Value <span class="currencyName">(USD)</span></th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody class="" id="booking_list_view_list">


			<tr>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no_1" id="txt_job_no_1"
					 onDblClick="openmypage_wovalue()" placeholder="Double Click" readonly/><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_job_id_1" id="txt_job_id_1"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_no_1" id="txt_order_no_1"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_1" id="txt_order_id_1"   readonly/>
				</td>
				<td style="display:none;" class="order_show" id="order_show">
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no_1" id="txt_order_no_1"
					readonly/><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_1" id="txt_order_id_1"   readonly/>
				</td>
				<td><input style="width:90px;" type="text" class="text_boxes"  name="txt_buyer_name_<?=$i;?>" id="txt_buyer_name_<?=$i;?>"   value="<?=$buyer_arr[$val[csf('buyer_name')]];?>" readonly/>
				<input style="width:90px;" type="hidden" class="text_boxes"  name="cbo_buyer_id_<?=$i;?>" id="cbo_buyer_id_<?=$i;?>"  value="<?=$val[csf('buyer_name')];?>"  readonly/></td>
				<td>
					<input class="text_boxes" type="text" style="width:90px" name="txt_style_ref_1" id="txt_style_ref_1"/>					
				</td>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_1" id="txt_style_desc_1"/> </td>
				<td><? echo create_drop_down( "cbo_inspection_id_1", 90, $inspection_for, 0, 1, "Select Inspection",$selected, "", "", "" );?></td>
				 <td>
					<input class="text_boxes" type="text" style="width:120px" name="txt_inspection_qnty_1" id="txt_inspection_qnty_1" />
					<input type="hidden" id="update_dtls_id_1" name="update_dtls_id_1">
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:80px" name="txt_inspection_rate_1" id="txt_inspection_rate_1" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_amount_1" id="txt_amount_1" onBlur="calculate_wo_value()"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount_1" id="txt_discount_1" onBlur="calculate_wo_value()"/>
				 </td>
				  <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_discount_value_1" id="txt_discount_value_1" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_with_vat_1" id="txt_insp_val_with_vat_1" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_1" id="txt_vat_1" readonly/>
				 </td>
				   <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount_1" id="txt_vat_amount_1" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_without_vat_1" id="txt_insp_val_without_vat_1" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_remarks_1" id="txt_remarks_1" readonly/>
					<input class="text_boxes_numeric" type="hidden" style="width:100px" name="txtRowId" id="txtRowId" readonly/>
				 </td>

			</tr>
				
            </tbody>
			<tfoot>
			<tr>
                	<td align="center" colspan="15" valign="middle" class="button_container">

					<?
				
			    	echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('printbooking_2','','','')",1) ;
				?>
					
              <input class="formbutton" type="button" style="width:100px;"  name="print_booking" id="print_booking" value="Print Booking" onclick="new_print_btn_fnc(1)" />
                </td>
            </tr>
			
			</tfoot>
        </table>
        <br>
		<br>
  <fieldset style="width:1030px;">
           <div id="list_container"></div>
  </fieldset>
        <div  align="left" id="data_panel"></div>
    </form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>