<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create knitting work order
Functionality	 :	
JS Functions	 :
Created by		 : Md. Helal Uddin 
Creation date 	 : 14-06-2020
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
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
echo load_html_head_contents("Knitting Work Order No", "../../", 1, 1,$unicode,'','');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function calculate_amount(param1,param2)
{
	var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
	var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;	

}

function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/knitting_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function openmypage_wo_no(page_link,title)
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_supplier_name=$('#cbo_supplier_name').val();
		// var update_id=$('#update_id').val();
	page_link+='&company_id='+cbo_company_name+'&supplier_id='+cbo_supplier_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{	
		var menu_info=this.contentDoc.getElementById("selected_work_order").value; 
	// console.log(menu_info);
        if(menu_info.length){
            var data=menu_info.split('**');
            console.log(data);
            reset_form('servicebookingknitting_1','','','','','');
            reset_table();

            $("#update_id").val(data[0]);
            $("#txt_work_order_no").val(data[1]);
            $("#cbo_company_name").val(data[2]);
            $("#cbo_currency").val(data[3]);
            $("#txt_exchange_rate").val(data[4]);
            $("#cbo_pay_mode").val(data[5]);
            $("#txt_booking_date").val(data[6]);
            $("#txt_delivery_date").val(data[7]);
            $("#cbo_booking_month").val(data[8]);
            $("#cbo_booking_year").val(data[9]);
            $("#cbo_supplier_name").val(data[10]);
			load_drop_down('requires/knitting_work_order_controller', $("#cbo_company_name").val(), 'load_drop_down_supplier_new', 'supplier_td');
            $("#txt_attention").val(data[11]);
            $("#txt_remark").val(data[12]);
            $("#cbo_ready_approval").val(data[15]);
			if (data[16]==1) $("#approved").text('Approved');
			else if (data[16]==3) $("#approved").text("Partial Approved");
			else $("#approved").text("");

            $('#cbo_company_name').attr('disabled', 'disabled');
            $('#cbo_currency').attr('disabled', 'disabled');
            $('#txt_exchange_rate').attr('disabled', 'disabled');
            $('#cbo_pay_mode').attr('disabled', 'disabled');
            $('#txt_booking_date').attr('disabled', 'disabled');
            $('#txt_delivery_date').attr('disabled', 'disabled');
            $('#cbo_booking_month').attr('disabled', 'disabled');
            $('#cbo_booking_year').attr('disabled', 'disabled');
           // $('#cbo_supplier_name').attr('disabled', 'disabled');
            $('#txt_attention').attr('disabled', 'disabled');
            $('#txt_remark').attr('disabled', 'disabled');
            // $('#cbo_ready_approval').attr('disabled', 'disabled');
            // $('#cbo_ready_approval').attr('disabled', 'disabled');

            set_button_status(1, permission, 'fnc_knitting_work_order',1);
            var details_ids = trim(return_global_ajax_value(data[0], 'populate_details_data', '', 'requires/knitting_work_order_controller'));
            //console.log(details_ids);
            //return;
            if(details_ids!=""){
            	rows=details_ids.split("**");
            	for(var i=0;i<rows.length;i++){
            		row=rows[i];
            		//set_button_status(1, permission, 'fnc_knitting_work_order_dtls',0);
            		create_row(row);
            	}
            	const update_button = document.querySelector('#update_details');
				if (update_button.classList.contains("formbutton_disabled")) {
				   update_button.classList.remove("formbutton_disabled");
				}
				const save_button = document.querySelector('#save_details');
				if (!save_button.classList.contains("formbutton_disabled")) {
				   save_button.classList.add("formbutton_disabled");
				}
				const delete_button = document.querySelector('#delete_details');
				if (delete_button.classList.contains("formbutton_disabled")) {
				   delete_button.classList.remove("formbutton_disabled");
				}
            }
        }
	}
}

function fnc_knitting_work_order( operation )
{
	if(operation>2){
		alert('Not Allow');
		return;
	}
	var data_all="";
	if (form_validation('cbo_company_name*cbo_supplier_name*txt_delivery_date*cbo_pay_mode*cbo_currency','Company Name*Supplier*Delivery date*Pay Mode*Currency')==false)
	{
		return;
	}
	else
	{
		data_all=get_submitted_data_string('txt_work_order_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*txt_attention*txt_remark*cbo_ready_approval*update_id',"../../");
		// alert(data_all);
	}

	var data="action=save_update_delete&operation="+operation+data_all;
	freeze_window(operation);
	http.open("POST","requires/knitting_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_knitting_work_order_reponse;
}

function select_item_pop(){
	
	
	if (form_validation('txt_work_order_no*cbo_company_name*update_id*cbo_supplier_name','Knitting Work Order no*Company name*Sytem no*Supplier')==false)
	{
		return;
	}
	else
	{
		console.log('here');
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_supplier_name=$('#cbo_supplier_name').val();
		// var update_id=$('#update_id').val();
		var page_link='requires/knitting_work_order_controller.php?action=select_item_pop&company_id='+cbo_company_name+'&supplier_id='+cbo_supplier_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=1300px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemail=this.contentDoc.getElementById("selected_work_order");	
			//console.log(theemail.value);
				
			if (theemail.value!="")
			{
				//$('#scanning_tbl tbody tr').remove();
				var rows_data=trim(theemail.value).split('***');
				console.log(rows_data);
				for(i=0;i<rows_data.length;i++)
				{
					var row_data=rows_data[i];
					create_row(row_data);        
				}
			}
		}
	}
}

function create_row(datas){
	var msg=0;
	var total_row=Number($("#total_row").val());
	var qty=0;
	var j=Number(total_row);
	var row_datas=datas.split("&&&&");
	var row_data=row_datas[0];
	
	var details_id='';
	var rate='';
	var amount='';
	var remark='';
	var disabled='';
	var ir_no='';
	var fab_pcs_qnty=0;
	if(row_datas.length>1){
		var row_data_update=row_datas[1].split("__");
		details_id=row_data_update[0];
		rate=row_data_update[1];
		amount=row_data_update[2];
		remark=row_data_update[3];
		check=row_data_update[4];
		fab_pcs_qnty=row_data_update[5];
		ir_no=row_data_update[6];
		console.log(row_data_update);
		if(check!="h"){
			disabled="disabled='disabled'";

		}

	}
	else{
		var datax=row_data.split("__");
		fab_pcs_qnty=datax[21];
		ir_no=datax[23];
	}
	$("#scanning_tbl").find('tbody tr').each(function() {

		var checking_data = $(this).find('input[name="checking_data[]"]').val();
		if(trim(row_data) == trim(checking_data)){
			msg++;
			return;
		}
		
	});
	if(msg>0){
		alert("Program Already exists");
	}
	else
	{ 	
		var data=row_data.split("__");
		
		j++;
		qty=data[8];
		var bookingQty=Math.round(data[15] * 100) / 100;
		var bookingRate=Math.round(rate * 100) / 100;
		var bookingAmt=Math.round(amount * 100) / 100;
		var trColor="";
		if (j%2==0) trColor="#E9F3FF"; else trColor="#FFFFFF";
		var html="<tr id='tr_"+j+"' bgcolor="+trColor+" align='center' valign='middle' >";
		html+="<td>"+j+" <input type='hidden' name='sl[]' id='sl_"+j+"' value='"+j+"' /><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'><input type='hidden' value='"+details_id+"' name='detailsId[]' id='detailsId_"+j+"'></td>";
		html+="<td>"+data[1]+" <input type='hidden' name='buyer[]' id='buyer_"+j+"' value='"+data[0]+"' /></td>";
		html+="<td>"+data[3]+" <input type='hidden' name='styleref[]' id='styleref_"+j+"' value='"+data[3]+"' /></td>";
		html+="<td>"+data[2]+" <input type='hidden' name='booking[]' id='booking_"+j+"' value='"+data[2]+"' /></td>";
		html+="<td>"+data[4]+" <input type='hidden' name='jobno[]' id='jobno_"+j+"' value='"+data[4]+"' /></td>";
		html+="<td>"+ir_no+" <input type='hidden' name='irno[]' id='irno_"+j+"' value='"+ir_no+"' /></td>";
		html+="<td>"+data[6]+" <input type='hidden' name='programdate[]' id='programdate_"+j+"' value='"+data[5]+"' /></td>";
		html+="<td>"+data[7]+" <input type='hidden' name='programno[]' id='programno_"+j+"' value='"+data[7]+"' /></td>";
		html+="<td>"+data[9]+" <input type='hidden' name='description[]' id='description_"+j+"' value='"+data[9]+"' /></td>";
		html+="<td>"+data[10]+' x '+data[11]+" <input type='hidden' name='dia[]' id='dia_"+j+"' value='"+data[10]+"' /> <input type='hidden' name='machinegg[]' id='machinegg_"+j+"' value='"+data[11]+"' /></td>";
		html+="<td>"+data[14]+" <input type='hidden' name='stitchlength[]' id='stitchlength_"+j+"' value='"+data[14]+"' /></td>";
		html+="<td>"+data[17]+" <input type='hidden' name='color[]' id='color_"+j+"' value='"+data[17]+"' /></td>";
		html+="<td>"+data[13]+" <input type='hidden' name='colorrange[]' id='colorrange_"+j+"' value='"+data[12]+"' /></td>";
		html+="<td>"+(Math.round(fab_pcs_qnty*100)/100)+" <input type='hidden' name='fabpcsqnty[]' id='fabpcsqnty_"+j+"' value='"+(Math.round(fab_pcs_qnty*100)/100)+"' /></td>";
		html+="<td>"+(Math.round(data[8]*100)/100)+" <input type='hidden' name='programqnty[]' id='programqnty_"+j+"' value='"+(Math.round(data[8]*100)/100)+"' /></td>";
		html+="<td>"+(bookingQty).toFixed(2)+" <input type='hidden' name='bookingqnt[]' id='bookingqnt_"+j+"' value='"+(bookingQty).toFixed(2)+"' /> <input type='hidden' name='withingroup[]' id='withingroup_"+j+"' value='"+data[16]+"' />  </td>";
		html+="<td><input type='text' name='rate[]' id='rate_"+j+"' style='width:50px' placeholder='Rate' class='text_boxes_numeric' onkeyup='calculate();' "+disabled+" value='"+(bookingRate).toFixed(2)+"' /></td>";
		html+="<td><input type='text' name='amount[]' id='amount_"+j+"' style='width:70px' placeholder='Amount' class='text_boxes_numeric' "+disabled+" value='"+(bookingAmt).toFixed(2)+"' /></td>";
		html+="<td><input type='text' name='remark[]' id='remark_"+j+"' style='width:80px' placeholder='Remark' "+disabled+"  class='text_boxes' value='"+remark+"' /></td>";
		html+="<td><input type='button'  onclick='fn_deleteRow("+j+")' class='formbuttonplasminus' value='-' style='width:30px' /></td>";
		html+="</tr>";
		//console.log(html);
		$('#scanning_tbl tbody').append(html);
		calculate_total_qnty();
		$("#total_row").val(j);
	}
}

function calculate_total_qnty(){
	var qty=0;
	$("#scanning_tbl").find('tbody tr').each(function() {
        var programqnty = Number($(this).find('input[name="programqnty[]"]').val());
        qty+=programqnty;
    });
	$("#totalqnty").val(Math.round(Number(qty)*100)/100);
	$("#total_qnty").text(Math.round(Number(qty)*100)/100);
}

function fn_deleteRow(rid)
{
	
	var dtls_id=$("#detailsId_"+rid).val();
	var bill_no = trim(return_global_ajax_value(dtls_id, 'check_delete', '', 'requires/knitting_work_order_controller'));
	if(bill_no!="")
	{
		alert("Remove not allowed . Bill no: "+bill_no);
		return;
	}

	if($("#tr_" + rid).length)
	{
		 $("#tr_" + rid).remove();
	}
	else
	{
		alert('Row not exists');
	}
	var qty=0;
	$("#scanning_tbl").find('tbody tr').each(function() {
        var programqnty = Number($(this).find('input[name="programqnty[]"]').val());
        qty+=programqnty;
    });
	$("#totalqnty").val(Math.round(Number(qty)*100)/100);
	$("#total_qnty").text(Math.round(Number(qty)*100)/100);

              
}
	 
function fnc_knitting_work_order_reponse()
{
	if(http.readyState == 4) 
	{
		 var response=trim(http.responseText).split('**');
		
		if(response[0]==13)
		{
			alert(response[1]);
			release_freezing();
			return;
		}

		if(response[0]==111)
		{
			alert('Update not allow . Bill Already generated. bill : '+response[1])
		}else if(response[0]==112)
		{
			alert('Delete not allow . Bill Already generated ')
		}
		 else if(response[0]==0 || response[0]==1)
		 {
		 	 show_msg(trim(response[0]));
		 	 
			$("#cbo_company_name").attr('disabled', 'disabled');
		 	$("#txt_work_order_no").val(response[1]);
		 	$("#update_id").val(response[2]);
		 	set_button_status(1, permission, 'fnc_knitting_work_order_dtls',1);
		 }
		 else if(response[0]==2)
		 {
		 	show_msg(trim(response[0]));
		 	reset_form('servicebookingknitting_1','','','','','');
            reset_table();
		 }
		 release_freezing();
	}
}


function fnc_knitting_work_order_dtls(operation)
{
	if (form_validation('update_id*cbo_company_name*txt_delivery_date','Work Order* Company Name*Delivery date')==false)
	{
		return;
	}
 	var j = 0;
    var dataString = '';
    $("#scanning_tbl").find('tbody tr').each(function () {
       
        var detailsId = $(this).find('input[name="detailsId[]"]').val();
        var buyer = $(this).find('input[name="buyer[]"]').val();
        var styleref = $(this).find('input[name="styleref[]"]').val();
        var booking = $(this).find('input[name="booking[]"]').val();
        var jobno = $(this).find('input[name="jobno[]"]').val();
		var irno = $(this).find('input[name="irno[]"]').val();
        var programdate = $(this).find('input[name="programdate[]"]').val();
        var programno = $(this).find('input[name="programno[]"]').val();
        var description = $(this).find('input[name="description[]"]').val();
        var dia = $(this).find('input[name="dia[]"]').val();
        var machinegg = $(this).find('input[name="machinegg[]"]').val();
        var stitchlength = $(this).find('input[name="stitchlength[]"]').val();
		var color = $(this).find('input[name="color[]"]').val();
        var colorrange = $(this).find('input[name="colorrange[]"]').val();
        var programqnty = $(this).find('input[name="programqnty[]"]').val();
        var fabpcsqnty = $(this).find('input[name="fabpcsqnty[]"]').val();
        var bookingqnt = $(this).find('input[name="bookingqnt[]"]').val();
        var rate = $(this).find('input[name="rate[]"]').val();
        var amount = $(this).find('input[name="amount[]"]').val();
        var remark = $(this).find('input[name="remark[]"]').val();
        var withingroup = $(this).find('input[name="withingroup[]"]').val();
        if(Number(rate)==0 || Number(amount)==0)
        {
        	alert('Rate and Amount can not be zero');
        	return;
        }
        try {
           
            j++;

            dataString +='&detailsId_' + j + '=' + detailsId +'&buyer_' + j + '=' + buyer + '&styleref_' + j + '=' + styleref + '&booking_' + j + '=' + booking + '&jobno_' + j + '=' + jobno + '&programdate_' + j + '=' + programdate + '&programno_' + j + '=' + programno + '&description_' + j + '=' + description + '&dia_' + j + '=' + dia + '&machinegg_' + j + '=' + machinegg+ '&stitchlength_' + j + '=' + stitchlength + '&color_' + j + '=' + color   + '&colorrange_' + j + '=' + colorrange + '&programqnty_' + j + '=' + programqnty+ '&bookingqnt_' + j + '=' + bookingqnt + '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark+ '&withingroup_' + j + '=' + withingroup+ '&fabpcsqnty_' + j + '=' + fabpcsqnty + '&irno_' + j + '=' + irno ;
        }
        catch (e) {
            //got error no operation
        }
    });

    if (j < 1) {
        alert('No data');
        return;
    }

        
    var data = "action=save_update_delete_details&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('update_id', "../../") + dataString;
    freeze_window(operation);
	http.open("POST","requires/knitting_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_knitting_work_order_details_reponse;
	
}
function fnc_knitting_work_order_details_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		 //console.log(http.responseText);
		 //release_freezing();
		 //return;
		if(response[0]==13)
		{
			alert(response[1]);
			release_freezing();
			return;
		}

		 if(response[0]==111)
		{	 release_freezing();
			alert('Update not allow . Bill Already generated. bill : '+response[1])
		}else if(response[0]==112)
		{
			 release_freezing();
			alert('Delete not allow . Bill Already generated')
		}
		 
		 else if(response[0]==0 || response[0]==1)
		 {
			show_msg(trim(response[0]));
		 	const update_button = document.querySelector('#update_details');
			if (update_button.classList.contains("formbutton_disabled")) {
			   update_button.classList.remove("formbutton_disabled");
			}
			const save_button = document.querySelector('#save_details');
			if (!save_button.classList.contains("formbutton_disabled")) {
			   save_button.classList.add("formbutton_disabled");
			}
			const delete_button = document.querySelector('#delete_details');
			if (delete_button.classList.contains("formbutton_disabled")) {
			   delete_button.classList.remove("formbutton_disabled");
			}
			const print_button = document.querySelector('#print_button');
			if (delete_button.classList.contains("formbutton_disabled")) {
			   delete_button.classList.remove("formbutton_disabled");
			}
			 release_freezing();
		 	
		 }
		 if(response[0]==2)
		 {
			show_msg(trim(response[0]));
			release_freezing();
			reset_form('servicebookingknitting_1','','','','','');
            reset_table();
		 }
		 
	}
}
function print_knitting_work_order(type){
	
	if (form_validation('update_id*cbo_company_name','Work Order* Company Name')==false)
	{
		return;
	}
	var r=confirm("Press \"OK\" to open with Rate & Amount column\nPress \"Cancel\" to open without Rate & Amount column");
	var show_val_column="0";
	if (r==true)
	{
		show_val_column="1";
	}
	else
	{
		show_val_column="0";
	}
	if(type==1){
	print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+show_val_column, "print_knitting_work_order", "requires/knitting_work_order_controller" ) 
		return;
	}else{
		print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+show_val_column, "print_knitting_work_order2", "requires/knitting_work_order_controller" ) 
		return;
	}

}


function calculate()
{
	var rate=[];
	var j=0;
	$("input[name='rate[]']").map( function(key){
       rate[j]=Number($(this).val());
       j=j+1;
    });

    var bookingqnty=[];
    j=0;
    $("input[name='bookingqnt[]']").map( function(key){
       bookingqnty[j]=Number($(this).val());
       j=j+1;
    });
    j=0;
    $("input[name='amount[]']").map( function(key){
       var num=rate[j]*bookingqnty[j];
       $(this).val(Math.round(Number(num)*100)/100);
       j=j+1;
    });

}
function reset_table(){
	$('#scanning_tbl tbody tr').remove();
	$("#totalqnty").val(0);
	$("#total_qnty").text(0);
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}

</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:950px;">
        <legend>Work Order</legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0" style="">
                <tr>
                    <td width="130" align="right" class="must_entry_caption" colspan="3">WO No </td>              <!-- 11-00030  -->
                    <td width="170" colspan="3">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_wo_no('requires/knitting_work_order_controller.php?action=work_order_popup','Work Order Search')" readonly placeholder="Double Click for Work Order" name="txt_work_order_no" id="txt_work_order_no"/>
                    	<input type="hidden" name="update_id" id="update_id">
                    </td>                       
                    <td></td>
                </tr>
                <tr>
                    <td class="">Booking Month</td>   
                    <td> 
                    <? 
                    	echo create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );		
                   		echo create_drop_down( "cbo_booking_year", 82, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
                    </td>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();","","" );
                        ?>	  
                    </td>
                    <td width="130">Booking Date</td>
                    <td width="170">
                    	<input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                    </td>
                </tr>
                <tr>
                     <td class="must_entry_caption">Supplier</td>
                    <td id="supplier_td">
						<?
                            echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/knitting_work_order_controller');",0 );
                        ?> 
                    </td> 
                    <td width="130" class="must_entry_caption">Delivery Date</td>
                    <td width="170">
                    	<input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/>	
                    </td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td>
                    	<? echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" ); ?> 
                    </td>
                   
                   
                </tr>
                <tr>
                    <td class="must_entry_caption">Currency</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 1, "set_conversion_rate(this.value, $('#txt_booking_date').val(), '../../', 'txt_exchange_rate')",0 );		
                        ?>	
                    </td>
                    <td>Exchange Rate</td>
                    <td>
                    	<input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" placeholder="Exchange Rate"  readonly />  
                    </td>
					<td class="must_entry_caption">Ready Approved</td>
					<? $ready_to_approval=array(1=>"Yes",2=>"No");?>
                    <td>
                    	<? echo create_drop_down( "cbo_ready_approval", 172, $ready_to_approval,"", 1, "-- Select Ready Approval --", "", "","" ); ?> 
                    </td>
                   
                </tr>
                <tr>
                    <td>Attention</td>   
                    <td colspan="3">
                    	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage( 'requires/knitting_work_order_controller.php?action=lapdip_no_popup', 'Lapdip No', 'lapdip')">
                    </td>
                    <td colspan="2">
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(412,'txt_work_order_no','../../','txt_work_order_no');
                        ?>   
                    </td>
                </tr>
                <tr>
                	<td>Remark</td>
                	<td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width: 97%;"></td>
                	<td colspan="2"></td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="top" id="booking_list_view1"></td>
                </tr>
                 <tr>
                    <td align="center" colspan="6" valign="top" id="app_status" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
					<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                    	<? echo load_submit_buttons( $permission, "fnc_knitting_work_order", 0,1 ,"reset_form('servicebooking_1','','','','','')",1); ?>
                    </td>
                </tr>
                
            </table>
            <table>
            	<tr>
            		<td>
            			Select Item &nbsp;&nbsp;
            			<input class="text_boxes" type="text" style="width:160px" onDblClick="select_item_pop()" readonly placeholder="Browse Item" name="txt_selected_item_no" id="txt_selected_item_no"/>
            		</td>
            	</tr>
            </table>
        </fieldset>
    </form>
    <br/>
    <form name="servicebookingknitting_1"  autocomplete="off" id="servicebookingknitting_1">   
        <fieldset style="width:1490px;">
        <legend>Details</legend>
            <table width="1470" cellspacing="2" cellpadding="0" border="1" class="rpt_table" id="scanning_tbl" rules="all">
                <thead>
	                <tr>
	                    <th width="30">SL No</th>
	                    <th width="80">Buyer</th>
	                    <th width="100">Style Ref. No</th>
	                    <th width="100">Fab. Booking No</th>
	                    <th width="120">FSO No</th>
						<th width="100">IR/IB</th>
	                    <th width="75">Program Date</th>
	                    <th width="70">Program no</th>
	                    <th width="150">Fabric Description</th>
	                    <th width="80">M/C Dia x Gauge</th>
	                    <th width="50">S.L</th>
						<th width="80">Fabric Color</th>
	                    <th width="80">Color Range</th>
	                    <th width="100">Fabric Qty (Pcs.)</th>
	                    <th width="70">Program Qty.</th>
	                    <th width="70">WO Qty.</th>
	                    <th width="60">Rate</th>
	                    <th width="80">Amount</th>
	                    <th width="90">Remarks</th>
	                    <th>Remove</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            </tbody>
	            <tfoot>
	            	<tr align='center' valign='middle' bgcolor="#CCCCCC">
						<td colspan='14'><input type="hidden" name="total_row" id="total_row" value="0"></td>
						<td align='center' id="total_qnty"><input type='hidden' name='totalqnty' id='totalqnty' value='0' /></td>
                        <td colspan='5' align='left' id="total_amt"><input type='hidden' name='totalAmt' id='totalAmt' value='' /></td>
					</tr>
	            	<tr>
	            		<td colspan="19" align="center">
	            			<p id="details_save">
	            				<input type="button" value="Save" name="save" onClick="fnc_knitting_work_order_dtls(0)" style="width:80px" id="save_details" class="formbutton">
	            				<input type="button" value="Update" name="save" onClick="fnc_knitting_work_order_dtls(1)" style="width:80px" id="update_details" class="formbutton formbutton_disabled">
	            				<input type="button" value="Delete" name="delete" onClick="fnc_knitting_work_order_dtls(2)" style="width:80px" id="delete_details" class="formbutton formbutton_disabled">
	            				<input type="button" value="Refresh" name="refresh" onClick="reset_form('servicebookingknitting_1','','','','','');reset_table()" style="width:80px" id="refresh_details" class="formbutton">

	            			
	            			</p>

	            			 <A style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:100px;height: 60px;margin-top: 5px;" target="_blank" class="formbutton" onClick="print_knitting_work_order(1)" id="print_button">
	            			 	&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;
	            			 </A>
							 <A style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:100px;height: 60px;margin-top: 5px;" target="_blank" class="formbutton" onClick="print_knitting_work_order(2)" id="print_button">
	            			 	&nbsp;&nbsp;&nbsp;Print 2&nbsp;&nbsp;&nbsp;
	            			 </A>
	            		</td>
	            	</tr>
	            </tfoot>
                
            </table>
            <div id="booking_list_view"></div>
             
        </fieldset>
    </form>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>