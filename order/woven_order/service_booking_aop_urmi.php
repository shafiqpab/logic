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
// Mandary mange from liefld lavel access data-- issue id:ISD-21-10942
$mandatory_fabric=array();
$mandatory_fabric_msg=array();
if(count($_SESSION['logic_erp']['data_arr'][162])>0){
	foreach ($_SESSION['logic_erp']['data_arr'][162] as $company_id => $data) {
		if($data['txt_fab_booking']['is_disable']==2)
			$mandatory_fabric[$company_id] = "txt_fab_booking";
			$mandatory_fabric_msg[$company_id] = "Fabric Booking";
	}
}
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var field_level_data="";
var permission='<? echo $permission; ?>';
<?
	if(isset($_SESSION['logic_erp']['data_arr'][162]))
	{

	  $data_arr=json_encode($_SESSION['logic_erp']['data_arr'][162] );
	  echo "var field_level_data= ". $data_arr . ";\n";
	}
	echo "var mandatory_field = '". implode('*',$mandatory_fabric) . "';\n";
	echo "var mandatory_message = '". implode('*',$mandatory_fabric_msg) . "';\n";
?>
function openmypage_order(page_link,title)
{
	page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year','../../');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var id=this.contentDoc.getElementById("po_number_id");
		var po=this.contentDoc.getElementById("po_number");
		if (id.value!="")
		{
			reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_source*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
			freeze_window(5);
			document.getElementById('txt_order_no_id').value=id.value;
			document.getElementById('txt_order_no').value=po.value;
			get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/service_booking_aop_urmi_controller" );
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
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_aop_urmi_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	if(type=="colorsizesensitive")
	{

	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('cbo_level').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_aop_urmi_controller','$(\'#hide_fabric_description\').val(\'\')');
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
	//var cbo_currency=(document.getElementById('cbo_currency').value)*1;
	var txt_amount=txt_woqnty*txt_rate;

	var updateid=(document.getElementById('updateid_'+i).value);
	
	if(updateid>0){
		var txtreturnqnty=(document.getElementById('txtreturnqnty_'+i).value)*1;
		var txt_prev_woqnty=(document.getElementById('txt_prev_woqnty_'+i).value)*1;
		var txt_blanty=(document.getElementById('txt_blanty_'+i).value)*1;
		var check_bal=txt_prev_woqnty-txtreturnqnty;
		if(txt_blanty==0 && (check_bal > txt_woqnty || txt_prev_woqnty < txt_woqnty  )){
			alert("exced req qnty");
		}
	}
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
		http.open("POST","requires/service_booking_aop_urmi_controller.php",true);
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company_id='+$("#cbo_company_name").val(), title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function() 
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");

		if (theemail.value!="")
		{
			reset_form('servicebooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_urmi_controller" );
			//alert("mmm");
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		    show_list_view(document.getElementById('update_id').value, 'fabric_detls_list_view','data_panel','requires/service_booking_aop_urmi_controller','');

		    get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/service_booking_aop_urmi_controller' );
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

	/*if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}*/

	/*if(operation==2){
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}*/

	var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}

	if(mandatory_field !=''){
		if (form_validation(mandatory_field,mandatory_message)==false){
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
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_tenor*cbo_buyer_name*cbo_level*cbo_is_short*cbo_ready_to_approved*txt_fab_booking*txt_delivery_to*txt_remark',"../../");
	}

	var hide_fabric_description=$('#hide_fabric_description').val();
	var data="action=save_update_delete&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description+'&delete_cause='+delete_cause;

	http.open("POST","requires/service_booking_aop_urmi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}

function fnc_trims_booking_reponse(){
	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 
		 if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Aop Order Entry. Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}

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
			$("#cbo_currency").attr("disabled",true);
		 }
		 if(reponse[0]==2)
		 {
			show_msg(trim(reponse[0]));
			set_button_status(0, permission, 'fnc_trims_booking',1);
			reset_form('','data_panel*booking_list_view','txt_booking_no*cbo_company_name*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor','txt_booking_date,<? echo date("d-m-Y"); ?>');
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
		if(trim(reponse[0])=='recv_no'){
			alert("Next Transaction Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
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
	/*if(operation==2){
		alert('Delete Restricted');
		release_freezing();
		return;
	}*/
/*	if(operation==2){
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
		    return;
		}
	}*/

	var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}

	var data_all="";
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_is_short*cbo_pay_mode',"../../");
	var row_num=$('#tbl_table tbody tr').length;
	//alert(data_all);
	var cbo_is_short=$('#cbo_is_short').val()*1;
		var tot_wo_qty=0; var total_job_req_amount=0; var total_job_booked_amt=0; var total_exc_amount=0;
		 var jobtotal_amount=0; var jobtotamt=0; var jobcuramt=0;var sum_job_total=0;
		 
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('findia_'+i,'Fin Dia')==false)
		{
			release_freezing();
			return;
		}

		if (form_validation('printing_color_'+i,'Printing Color')==false)
		{
			release_freezing();
			return;
		}
		/*if (form_validation('txt_woqnty_'+i,'Wo Qty.')==false)
		{
			release_freezing();
			return;
		}*/
		var txt_woqnty= (document.getElementById('txt_woqnty_'+i).value)*1;
		var txt_bal_qty= (document.getElementById('txt_blanty_'+i).value)*1;
		var update_id= (document.getElementById('updateid_'+i).value)*1;
		var txt_reqqty= (document.getElementById('txtreqnty_'+i).value)*1;
		var txt_prev_woqnty= (document.getElementById('txt_prev_woqnty_'+i).value)*1;
		var tot_wo_qty=txt_woqnty+tot_wo_qty;
		if(cbo_is_short==2)
		{
			if(operation!=2)
			{
				/*if(operation==0)
				{
					var total_curr_wo_woqnty=txt_prev_woqnty+txt_woqnty;
				}
				else
				{
					var total_curr_wo_woqnty=number_format(txt_prev_woqnty+txt_bal_qty,2,'.','' );
				}
				if(txt_woqnty>0)
				{
					//alert(total_curr_wo_woqnty+'=='+txt_woqnty+'=='+txt_prev_woqnty+'=='+txt_reqqty+'=='+txt_bal_qty);
					if(operation==0)
					{
						if(total_curr_wo_woqnty>txt_reqqty)
						{
							var wo_msg="Exceed qty is not allowed.\n Req. Qty : "+txt_reqqty;
							alert(wo_msg);
							release_freezing();
							return;
						}
					}
					else
					{
						if(txt_woqnty>total_curr_wo_woqnty)
						{
							var wo_msg="Exceed qty is not allowed.\n Req. Qty : "+txt_reqqty;
							alert(wo_msg);
							release_freezing();
							return;
						}
					}
				}*/
				//var total_req_amount=$('#txt_amount_'+i).attr('reqamount')*1;
				var total_req_amount=$('#txt_amount_'+i).attr('examt')*1;
				var total_req_amount=number_format((total_req_amount*1),1,'.','');
				var total_amount=number_format(($('#txt_amount_'+i).val()*1),1,'.','');
				var curamt=$('#txt_amount_'+i).attr('curamt')*1;
				var totamt=$('#txt_amount_'+i).attr('totamt')*1;
				//var tot_bom_amount=total_req_amount+total_amount;
				//var total_reqamount=total_req_amount.split(".");
				var total_amount_cal=total_amount.split(".");
 				//var tot_reqamount=(total_reqamount[0]*1);
				var tot_amount_cal=(total_amount_cal[0]*1);
 				var exc_amount=total_req_amount-tot_amount_cal;
 				var booked_amt=number_format((total_amount*1)+((totamt*1)-(curamt*1)),1,'.','');
				var exc_amount=total_req_amount-booked_amt;
				
				/////////////// job wise validation calculation  start 
				total_job_req_amount +=$('#txt_amount_'+i).attr('examt')*1;
 				jobtotal_amount +=$('#txt_amount_'+i).val()*1;//number_format(($('#txt_amount_'+i).val()*1),1,'.','');
				jobtotamt +=$('#txt_amount_'+i).attr('totamt')*1;
				jobcuramt +=$('#txt_amount_'+i).attr('curamt')*1;
  			    sum_job_total=(jobtotal_amount*1)+(jobtotamt*1);
 				total_job_booked_amt=(sum_job_total*1)-(jobcuramt*1);
    			total_exc_amount=total_job_req_amount-total_job_booked_amt;
 				//alert(total_exc_amount);
 				//////////////////////////// job wise validation calculation  end
 				//82.5014.25=82.50=96.75=82.5=93
				//101.25=18.75=82.5=0=93
				//if(tot_amount_cal>tot_reqamount)
				/*if((booked_amt*1)>(total_req_amount*1))
				{
					var wo_msg="Exceed Amount then BOM is not allow.\n Exceed Amount is : "+exc_amount;
					alert(wo_msg);
					release_freezing();
					return;
				}*/
			}
		}
			
		//data_all+=get_submitted_data_string('po_id_'+i,"../../",i);
		data_all+=get_submitted_data_string('job_no_'+i+'*po_id_'+i+'*fabric_description_id_'+i+'*dia_'+i+'*gmts_color_id_'+i+'*color_size_table_id_'+i+'*item_color_'+i+'*findia_'+i+'*artworkno_'+i+'*aop_mc_type_'+i+'*aop_type_'+i+'*startdate_'+i+'*enddate_'+i+'*txt_blanty_'+i+'*txtreqnty_'+i+'*txt_woqnty_'+i+'*uom_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_paln_cut_'+i+'*updateid_'+i+'*printing_color_'+i+'*subprocess_id_'+i,"../../",i);//
	}
		
		//return;
		
		  // alert(total_job_booked_amt);
		   //alert(total_job_req_amount);
		
			if((total_job_booked_amt*1)>(total_job_req_amount*1))
			{
				var wo_msg="Exceed Amount then BOM is not allow.\n Exceed Amount is : "+total_exc_amount;
				alert(wo_msg);
				release_freezing();
				return;
			}
		
		
		//alert(tot_wo_qty)
			if(tot_wo_qty==0 || tot_wo_qty=='') //Dont remove this Issue Id=21214 for Radiance //Emon
			{
					alert('Wo qty empty not allowed, Please fill up at least one');
					release_freezing();
					return;
			}
	//alert(data_all); return;

	var cbo_level=document.getElementById('cbo_level').value;
	var json_data=document.getElementById('json_data').value;
	var update_id=document.getElementById('update_id').value;

	if(cbo_level==1){
		var data="action=save_update_delete_dtls&operation="+operation+data_all+'&row_num='+row_num+'&delete_cause='+delete_cause+'&update_id='+update_id;
	}
	if(cbo_level==2){
		var data="action=save_update_delete_dtls_job_level&operation="+operation+data_all+'&row_num='+row_num+'&json_data='+json_data+'&delete_cause='+delete_cause+'&update_id='+update_id;
	}

	http.open("POST","requires/service_booking_aop_urmi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;
}

function fnc_service_booking_dtls_reponse()
{
	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 
		if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Aop Order Entry. Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='issFinPrcess'){ //Issue ID 18501
				alert("Fabric Issue to Fin. Process :"+trim(reponse[2])+"\n"+trim(reponse[3]));
				release_freezing();
				return;
			}
		
		 if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		 {
			 show_msg(trim(reponse[0]));
		 	$('#booking_list_view').text('');
			show_list_view(document.getElementById('update_id').value, 'fabric_detls_list_view','data_panel','requires/service_booking_aop_urmi_controller','');
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
		if(trim(reponse[0])=='recv_no'){
			alert("Next Transaction Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])==10){
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
		if(action=='show_trim_booking_report4')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate and Amount\nPress  \"OK\"  to Show Rate and Amount");
            if (r==true) show_comment="1"; else show_comment="0";
        }else
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
			if (r==true) show_comment="1";else show_comment="0";
		}
		
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_fab_booking*update_id',"../../")+'&show_comment='+show_comment+'&path=../../';
		http.open("POST","requires/service_booking_aop_urmi_controller.php",true);
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
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_aop_urmi_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);

	}
function fnc_file_upload(i)
	{
			
			var update_id = $("#updateid_"+i).val();
			var gmts_color_id = $("#gmts_color_id_"+i).val();
			var dia_name = $("#dia_"+i).val();
			var po_id = $("#po_id_"+i).val();
			var pre_conv_id = $("#fabric_description_id_"+i).val();
				//alert(issue_id);
				file_uploader ( '../../', update_id,'', 'aop_v2', 0,1);
			
		
	}
	
	function fnc_fab_booking(page_link,title)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		var buyer=$("#cbo_buyer_name").val()*1;
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&buyer='+buyer, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				//reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? //echo date("d-m-Y"); ?>');
			//	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_urmi_controller" );
				//check_month_setting();
				//var is_approved_id=$('#id_approved_id').val();
				//alert(is_approved_id);
			
				//$('#cbo_company_name').attr('disabled','true');
				//set_button_status(1, permission, 'fnc_fabric_booking',1);
				$("#txt_fab_booking").val(theemail.value);
			
				
			}
		}
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
        <!--<h3 style="width:950px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>-->
    <!--<div id="content_search_panel" >-->
        <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            <fieldset style="width:1000px;">
                <legend>Service Booking</legend>
                <table width="1000" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" align="right" class="must_entry_caption"> Booking No </td>              <!-- 11-00030  -->
                        <td colspan="4">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_aop_urmi_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Booking Month</td>
                        <td width="140"><?  echo create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );
                        	echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 );
                        ?></td>
                        <td width="110" class="must_entry_caption">Company Name</td>
                        <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value, 'company_wise_report_button_setting','requires/service_booking_aop_urmi_controller'); check_exchange_rate();","","" ); ?></td>
                        <td width="110" class="must_entry_caption">Buyer Name</td>
                        <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                        <td width="110">Booking Date</td>
                        <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td><?=create_drop_down("cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 );	?></td>
                        <td>Exchange Rate</td>
                        <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                        <td>Delivery Date</td>
                        <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                        <td class="must_entry_caption">Pay Mode</td>
                        <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/service_booking_aop_urmi_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' );","" ); ?></td>
                    </tr>
                    <tr>
                    	<td>Source</td>              <!-- 11-00030  -->
                        <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "3", "","" ); ?></td>
                        <td class="must_entry_caption">Supplier Name</td>
                        <td id="supplier_td"><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=25 and a.status_active=1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_aop_urmi_controller');",0 ); ?></td>
                        <td>Is Short</td>
                        <td><?=create_drop_down( "cbo_is_short", 130, $yes_no,'', 0, '',2,"");?></td>
                        <td>Fabric Booking</td>
						<td><input class="text_boxes" type="text" style="width:120px;"  name="txt_fab_booking" id="txt_fab_booking" onDblClick="fnc_fab_booking('requires/service_booking_aop_urmi_controller.php?action=fabric_booking_popup','fabric Booking Search')" placeholder="Browser" readonly/> </td>
                    </tr>
                    <tr>
                    	<td>Attention</td>
                        <td colspan="3">
                            <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                            <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/service_booking_aop_urmi_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                        </td>
                        <td>Remarks</td>
					 	<td colspan="3"><input style="width:370px;" type="text" class="text_boxes" name="txt_remark" id="txt_remark" /></td>
                    </tr>
                    <tr>
                        <td>Tenor</td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                        <td>Delivery To</td>
                        <td ><input style="width:120px;" type="text" class="text_boxes" name="txt_delivery_to" id="txt_delivery_to" /></td>
                        <td>Level</td>
                        <td>
                        <?
                        $level_arr=array(1=>"PO Level",2=>"Job Level");
                        echo create_drop_down( "cbo_level", 130, $level_arr,"", 0, "", "2", "","","" );
                        ?>
                        </td>
                        <td>Ready To Approved</td>
						<td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" );?> </td>
			        </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking', 0 ,1)"></td>
                        <td>&nbsp;</td>
                        <td align="center">
                            <?
                                include("../../terms_condition/terms_condition.php");
                                terms_condition(162,'txt_booking_no','../../');
                            ?>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
			        </tr>
                    <tr>
                        <td align="center" colspan="8" valign="top" id="booking_list_view1"></td>
                    </tr>
                    <tr>
                    	<td width="100%" colspan="8">
	                    	<table width="100%" border="0">
	                    		<tr>
	                    			<td class="button_container" width="200"><div id="approved" style="float:left; font-size:20px; color:#FF0000;"></div></td>
			                        <td width="400" align="left"valign="middle" class="button_container">
			                        <?
			                        $endis = "disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0 )";
			                        echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','booking_list_view*data_panel*pdf_file_name','','txt_booking_date,".$date."',$endis,'cbo_currency*cbo_booking_year*cbo_booking_month*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name')",1) ;
			                        ?>
									 <input class="text_boxes" type="hidden" style="width:97%;"  name="update_id" id="update_id"/>
			                        </td>
	                    		</tr>
	                    	</table>
                    	</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8">
                            <div id="pdf_file_name"></div>
                            <input type="button" value="Print Booking 1" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:120px; display: none;" name="print_booking1" id="print_booking1" class="formbutton" />
                            <input type="button" value="Print Booking 2" onClick="generate_trim_report('show_trim_booking_report2')"  style="width:120px; display: none;" name="print_booking2" id="print_booking2" class="formbutton" />
                            
                             <input type="button" value="Print Booking 3" onClick="generate_trim_report('show_trim_booking_report3')"  style="width:120px; display: none;" name="print_booking3" id="print_booking3" class="formbutton" />
                             <input type="button" value="Print Booking 4" onClick="generate_trim_report('show_trim_booking_report4')"  style="width:120px;display: none; " name="print_booking4" id="print_booking4" class="formbutton" />
                             <input type="button" value="Print Booking 5" onClick="generate_trim_report('show_trim_booking_report5')"  style="width:120px;display: none; " name="print_booking5" id="print_booking5" class="formbutton" />
							 <input type="button" value="Print Booking 6" onClick="generate_trim_report('show_trim_booking_report6')"  style="width:120px;display: none; " name="print_booking6" id="print_booking6" class="formbutton" />
							 <input type="button" value="Print Booking 7" onClick="generate_trim_report('show_trim_booking_report7')"  style="width:120px;display: none; " name="print_booking7" id="print_booking7" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <!--</div>-->
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
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;

	    var page_link='requires/service_booking_aop_urmi_controller.php?action=fabric_search_popup';
		var title='AOP Booking Search';
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_currency='+cbo_currency+'&cbo_is_short='+cbo_is_short+'&cbo_pay_mode='+cbo_pay_mode;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=400px,center=1,resize=1,scrolling=0','../')
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
				//alert(cbo_currency);
				show_list_view(fabric_desription_id+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_level').value+"**"+theemail.value+"**"+cbo_is_short+"**"+cbo_currency+"**"+cbo_pay_mode, 'generate_aop_booking','booking_list_view','requires/service_booking_aop_urmi_controller','$(\'#hide_fabric_description\').val(\'\')');
			}
		}
	}
}

function set_data(po_id,fabric_cost_id,precost_conver_id,booking_id){
	    document.getElementById('txt_select_item').value=precost_conver_id;
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=fabric_cost_id;
		var cbo_is_short=document.getElementById('cbo_is_short').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		//alert(cbo_is_short);
		show_list_view(fabric_cost_id+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_level').value+"**"+precost_conver_id+"**"+cbo_is_short+"**"+cbo_currency+"**"+cbo_pay_mode, 'show_aop_booking','booking_list_view','requires/service_booking_aop_urmi_controller','$(\'#hide_fabric_description\').val(\'\')');
		set_button_status(1, permission, 'fnc_service_booking_dtls',2);
}

function deletedata(po_id,fabric_cost_id,precost_conver_id,booking_id){
	var operation=2;
	freeze_window(operation);
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		alert('Please  Save Master Part First');
		release_freezing();
		return;
	}


	var delete_cause='';
	if(operation==2){
		delete_cause = prompt("Please enter your delete cause", "");
		if(delete_cause==""){
			alert("You have to enter a delete cause");
			release_freezing();
			return;
		}
		if(delete_cause==null){
			release_freezing();
			return;
		}
		var r=confirm("Press OK to Delete Or Press Cancel");
		if(r==false){
			release_freezing();
			return;
		}
	}

	var row_num=1;
	var i=1;
	var data_all=get_submitted_data_string('txt_booking_no*cbo_is_short*cbo_pay_mode',"../../");
        data_all+="&txtpoid_1="+po_id+"&txtpre_cost_fabric_cost_dtls_id_1="+fabric_cost_id+"&updateid_1="+booking_id+"&fabric_description_id_1="+fabric_cost_id;

	var cbo_level=document.getElementById('cbo_level').value;
	if(cbo_level==1){

	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&row_num='+row_num+'&delete_cause='+delete_cause;
	}
	if(cbo_level==2){
	var data="action=save_update_delete_dtls_job_level&operation="+operation+data_all+'&row_num='+row_num+'&delete_cause='+delete_cause;
	}

	http.open("POST","requires/service_booking_aop_urmi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;

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