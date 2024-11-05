<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 27-02-2015
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
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';

<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][404] );
	echo "var field_level_data= ". $data_arr . ";\n";
?>
function openmypage_order(page_link,title)
{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			if (id.value!="")
			{
				reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_source*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/service_booking_aop_with_order_controller" );
				set_button_status(0, permission, 'fnc_trims_booking',1);
				release_freezing();
	
			}
		}
}


function set_process(fabric_desription_id,type)
{
	$("#booking_list_view").text('');
	fabric_desription_id=$("#cbo_fabric_description").val();
	
	if(type=='set_process')
	{
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_aop_with_order_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	if(type=="colorsizesensitive")
	{
		
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_aop_with_order_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	$("#hide_fabric_description").val(fabric_desription_id);
}

function fnc_fabric_description_id(color_id, button_status, type)
{
	var hide_color_id='';
	if(type==1)
	{
		hide_color_id=document.getElementById('hide_fabric_description').value;
		//document.getElementById('copy_val').checked=true;
	}
	else
	{
		hide_color_id=parseInt(document.getElementById('hide_fabric_description').value);
		//document.getElementById('copy_val').checked=false;
	}

	if(color_id==hide_color_id)
	{
		document.getElementById('hide_fabric_description').value='';
		set_button_status(0, permission, 'fnc_trims_booking',1);
	}
	else
	{
		document.getElementById('hide_fabric_description').value=color_id;
		set_button_status(button_status, permission, 'fnc_trims_booking',1);	
	}
}
function setmaster_value(process, sensitivity)
{
	document.getElementById('cbo_process').value=process;
	document.getElementById('cbo_colorsizesensitive').value=sensitivity;
}

function calculate_amount(i)
{
	var txt_woqnty=(document.getElementById('txt_woqnty_'+i).value)*1;
	var txt_rate=(document.getElementById('txt_rate_'+i).value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount_'+i).value=txt_amount;	

}
function copy_value(i,type)
{
	 var copy_val=document.getElementById('copy_val').checked;
	 var rowCount=$('#tbl_table tbody tr').length;
	 if(copy_val==true)
	  {
		  for(var j=i; j<=rowCount; j++)
		  {
			  if(type=='txt_rate')
			  {
				  var txt_woqnty=(document.getElementById('txt_woqnty_'+j).value)*1;
	              var txt_rate=(document.getElementById('txt_rate_'+j).value)*1;
                  var txt_amount=txt_woqnty*txt_rate;	
				  document.getElementById('txt_rate_'+j).value=txt_rate;
				  document.getElementById('txt_amount_'+j).value=txt_amount;	
			  }
			  
			  if(type=='txt_woqnty')
			  {
				  var txt_woqnty=(document.getElementById('txt_woqnty_'+j).value)*1;
	              var txt_rate=(document.getElementById('txt_rate_'+j).value)*1;
                  var txt_amount=txt_woqnty*txt_rate;	
				  document.getElementById('txt_woqnty_'+j).value=txt_woqnty;
				  document.getElementById('txt_amount_'+j).value=txt_amount;	
			  }
			  if(type=='uom')
			  {
				  var uom=(document.getElementById('uom_'+ii).value)*1;
				  document.getElementById('uom_'+j).value=uom;
			  }
		  }
	  }
	
}


function fnc_generate_booking()
{
	
	if (form_validation('txt_order_no_id','Order No*Fabric Nature*Fabric Source')==false)
	{
		return;
	}
	else
	{
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_order_no_id',"../../");
		http.open("POST","requires/service_booking_aop_with_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	}
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	}
}




function open_consumption_popup(page_link,title,po_id,i)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var po_id =document.getElementById(po_id).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}
	
	else
	{
		var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&txtwoq='+txtwoq+'&cons_breck_downn='+cons_breck_downn+'&cbocolorsizesensitive='+cbocolorsizesensitive;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("cons_sum");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
		}	
	}
}

function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			reset_form('servicebooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_with_order_controller" );
			//alert("mmm");
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		    show_list_view(document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/service_booking_aop_with_order_controller','');
		}
	}
}





function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}	
	else
	{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}


function fnc_trims_booking( operation ){
	freeze_window(operation);
	var data_all="";
	
	if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}
	
	if(operation==2){
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}
	if (form_validation('cbo_company_name*cbo_pay_mode*cbo_buyer_name*cbo_supplier_name','Company Name*Pay Mode*Buyer Name*Supplier Name')==false)
	{
		release_freezing();
		return;
	}
	else
	{
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_tenor*cbo_buyer_name*cbo_level*cbo_is_short',"../../");
	}
	
	var hide_fabric_description=$('#hide_fabric_description').val();
	var data="action=save_update_delete&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description;
	
	http.open("POST","requires/service_booking_aop_with_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}
	 
function fnc_trims_booking_reponse(){
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		
		 if(reponse[0]==0 || reponse[0]==1)
		 {
			show_msg(trim(reponse[0]));
			document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
		 	set_button_status(1, permission, 'fnc_trims_booking',1);
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_supplier_name").attr("disabled",true);
			$("#cbo_buyer_name").attr("disabled",true);
			$("#cbo_level").attr("disabled",true);
			$("#cbo_is_short").attr("disabled",true);
		 }
		 if(reponse[0]==2)
		 {
			show_msg(trim(reponse[0]));
			set_button_status(0, permission, 'fnc_trims_booking',1);
			reset_form('','data_panel','txt_booking_no*cbo_company_name*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor','txt_booking_date,<? echo date("d-m-Y"); ?>'); 
		 }
		  if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		
		if(trim(reponse[0])=='rec1'){
			alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		
		if(trim(reponse[0])=='iss1'){
			alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		 release_freezing();
	}
}
 



function fnc_service_booking_dtls( operation )
{
	freeze_window(operation);
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		alert('Please  Save Master Part First');
		release_freezing();
		return;
	}
	if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}
	if(operation==2){
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}
	var data_all="";
	var row_num=$('#tbl_table tbody tr').length;
	
	
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('findia_'+i,'Fin Dia')==false){
			release_freezing();
			return;
		}
		data_all+=get_submitted_data_string('job_no_'+i+'*po_id_'+i+'*fabric_description_id_'+i+'*dia_'+i+'*gmts_color_id_'+i+'*color_size_table_id_'+i+'*item_color_'+i+'*findia_'+i+'*artworkno_'+i+'*startdate_'+i+'*enddate_'+i+'*txt_blanty_'+i+'*txtreqnty_'+i+'*txt_woqnty_'+i+'*uom_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_paln_cut_'+i+'*updateid_'+i,"../../",i);	
	}
	
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_is_short*cbo_pay_mode*update_id',"../../");
	var cbo_level=document.getElementById('cbo_level').value;
	var json_data=document.getElementById('json_data').value;
	if(cbo_level==1){
	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&row_num='+row_num;
	}
	if(cbo_level==2){
	var data="action=save_update_delete_dtls_job_level&operation="+operation+data_all+'&row_num='+row_num+'&json_data='+json_data;
	}
	
	http.open("POST","requires/service_booking_aop_with_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;
}
	 
function fnc_service_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		 if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		 {
			 show_msg(trim(reponse[0]));
		 	$('#booking_list_view').text('');
			show_list_view(document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/service_booking_aop_with_order_controller','');
		 	set_button_status(0, permission, 'fnc_service_booking_dtls',2);
		 }
		 if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		
		if(trim(reponse[0])=='rec1'){
			alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		
		if(trim(reponse[0])=='iss1'){
			alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		release_freezing();
	}
}

function generate_trim_report(action)
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true) show_comment="1";
		else show_comment="0";
		
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name',"../../")+'&show_comment='+show_comment+'&path=../../';
		http.open("POST","requires/service_booking_aop_with_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_aop_with_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}


</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style> 
</head>
<body onLoad="set_hotkey();check_exchange_rate();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            <fieldset style="width:1000px;">
                <legend>Sample AOP With Order</legend>
                <table  width="1000" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" align="right" class="must_entry_caption"> Booking No </td>              <!-- 11-00030  -->
                        <td colspan="4">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_aop_with_order_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Booking Month</td>   
                        <td width="140"> 
							<? 
                            echo create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );		
                            echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                            ?>
                        </td>
                        <td width="110" class="must_entry_caption">Company Name</td>
                        <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_aop_with_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_exchange_rate();","","" ); ?></td>
                        <td width="110" class="must_entry_caption">Buyer Name</td>   
                        <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>  
                        <td width="110">Booking Date</td>
                        <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 ); ?></td>
                        <td>Exchange Rate</td>
                        <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                        <td>Delivery Date</td>
                        <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                        <td>Tenor</td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Pay Mode</td>
                        <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "1", "","" ); ?></td>
                        <td>Source</td>              <!-- 11-00030  -->
                        <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "3", "","" ); ?></td>
                        <td class="must_entry_caption">Supplier Name</td>
                        <td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=25 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_aop_with_order_controller');",0 ); ?></td>
                        <td>Is Short</td>
                        <td><?=create_drop_down( "cbo_is_short", 130, $yes_no,'', 0, '',2,"");?></td> 
                    </tr>
                    <tr>  
                        <td>Attention</td>   
                        <td colspan="3">
                            <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                            <input class="text_boxes" type="hidden" style="width:70px;"  name="update_id" id="update_id"/>
                            <input type="hidden" class="image_uploader" style="width:62px" value="Lab DIP No" onClick="openmypage('requires/service_booking_aop_with_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                        </td>
                        <td>Level</td> 
                        <td>
							<?
                            $level_arr=array(1=>"PO Level",2=>"Job Level");
                            echo create_drop_down( "cbo_level", 130, $level_arr,"", 0, "", "2", "","","" );
                            ?>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                        <td valign="middle"><input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking', 0 ,1)"></td>
                        <td>&nbsp;</td>
                        <td align="center"><input type="button" id="set_button" class="image_uploader" style="width:130px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/service_booking_aop_with_order_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="top" id="booking_list_view1">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" class="button_container">
                        <? 
                        $endis = "disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0 )";
                        echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','booking_list_view*data_panel*pdf_file_name','','txt_booking_date,".$date."',$endis,'cbo_currency*cbo_booking_year*cbo_booking_month*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name')",1) ; 
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8">
                        <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking Urmi-1" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:120px" name="print_booking1" id="print_booking1" class="formbutton" />  
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <br/>
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">   
            <fieldset style="width:950px;">
                <legend title="V3">Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;    
                Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data()" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
                <!--<b>Copy</b> :--><input type="checkbox" id="copy_val" name="copy_val" style="display:none" checked/> 
                <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_order_no_id" id="txt_order_no_id"/>
                <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="cbo_fabric_description" id="cbo_fabric_description"/></legend>
                <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
                <? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,0 ,"reset_form('','booking_list_view*data_panel','','','','')",2) ; ?>
            </fieldset>
        </form>
        <div id="booking_list_view_list"></div>
        <div id="data_panel"></div>
        <div id="data_panel2" style="display:none"></div>
    </div>
    <input type="button" id="myBtn" value="OPen" style="display:none"/>
<div id="myModal" class="modal">

  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>
   
  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
</body>
<script>
function fnc_process_data(){
	if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false){

		return;
	}
	else{
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_is_short=document.getElementById('cbo_is_short').value;
		
	    var page_link='requires/service_booking_aop_with_order_controller.php?action=fabric_search_popup';
		var title='Trim Booking Search';
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_currency='+cbo_currency;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1340px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemail2=this.contentDoc.getElementById("txt_pre_cost_dtls_id");
			var theemail3=this.contentDoc.getElementById("txt_selected_po");
			if (theemail.value!=""){
				document.getElementById('txt_select_item').value=theemail.value;
				document.getElementById('txt_order_no_id').value=theemail3.value;
				document.getElementById('cbo_fabric_description').value=theemail2.value;
				fabric_desription_id=theemail2.value;
				show_list_view(fabric_desription_id+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_level').value+"**"+theemail.value+"**"+cbo_is_short, 'generate_aop_booking','booking_list_view','requires/service_booking_aop_with_order_controller','$(\'#hide_fabric_description\').val(\'\')');
				
			}
		}
	}
}

function set_data(po_id,fabric_cost_id,precost_conver_id,booking_id){
	    document.getElementById('txt_select_item').value=precost_conver_id;
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=fabric_cost_id;
		var cbo_is_short=document.getElementById('cbo_is_short').value;
		//alert(cbo_is_short);
		show_list_view(fabric_cost_id+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_level').value+"**"+precost_conver_id+"**"+cbo_is_short, 'show_aop_booking','booking_list_view','requires/service_booking_aop_with_order_controller','$(\'#hide_fabric_description\').val(\'\')');
		set_button_status(1, permission, 'fnc_service_booking_dtls',2);
}

//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata(data){
	
	document.getElementById('ccc').innerHTML=data;
	document.getElementById('myBtn').click();
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>