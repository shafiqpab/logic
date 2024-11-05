<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Sample Fabric Booking (Without Order)
Functionality	         :
JS Functions	         :
Created by		         :	Monzu
Creation date 	         : 	27-12-2012
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         : 	zakaria joy
Update date		         : 	29/01/2020
QC Performed BY	         :
QC Date			         :
Comments		         :From this version oracle conversion is start
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Booking Non Order", "../../", 1, 1,$unicode,'','');
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//print_r($_SESSION['logic_erp']['mandatory_field'][90]);
?>
<script>
var mst_mandatory_field="";
var mst_mandatory_message="";
var mandatory_field="";
var mandatory_message="";
<?
	$data_array = sql_select("SELECT  booking_no from wo_booking_mst");
	$operation_booking_no = array();
	foreach($data_array as $row)
	{
		$operation_booking_no[$row[csf("booking_no")]]= $row[csf("booking_no")];
	}
	$operation_booking_no = json_encode($operation_booking_no);
	echo "var operation_booking_no = ".$operation_booking_no.";\n";
	//echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][90]) . "';\n";
	//echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][90]) . "';\n";
	


	if($_SESSION['logic_erp']['mandatory_field'][90][15]!="" && $_SESSION['logic_erp']['mandatory_field'][90][16]!="")
	{
	echo "var mst_mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][15].'*'.$_SESSION['logic_erp']['mandatory_field'][90][16]) . "';\n";
	echo "var mst_mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][15].'*'.$_SESSION['logic_erp']['mandatory_field'][90][16]) . "';\n";
	}
	else if($_SESSION['logic_erp']['mandatory_field'][90][15]!="" && $_SESSION['logic_erp']['mandatory_field'][90][16]=="")
	{
		echo "var mst_mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][15]) . "';\n";
		echo "var mst_mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][15]) . "';\n";
	}
	else if($_SESSION['logic_erp']['mandatory_field'][90][16]!="" && $_SESSION['logic_erp']['mandatory_field'][90][15]=="")
	{
		echo "var mst_mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][16]) . "';\n";
		echo "var mst_mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][16]) . "';\n";
	}	
	
	
	$field='';
	$message='';
	$i=0;
	


foreach($_SESSION['logic_erp']['mandatory_field'][90] as $key=>$value){
	
	if($key==19 || $key==20 || $key==29 || $key==33 || $key==39 || $key==41 || $key==42 || $key==23){
		if($i==0){
				$field.=$value;
				$message.=$value;
				
			}else{
				$field.='*'.$value;
				$message.='*'.$value;
			}
			$i++;
	}

}
echo "var mandatory_field = '". ($field) . "';\n";
echo "var mandatory_message = '". ($message) . "';\n";


	// if( $_SESSION['logic_erp']['mandatory_field'][90][19]!="" && $_SESSION['logic_erp']['mandatory_field'][90][20]!="" && $_SESSION['logic_erp']['mandatory_field'][90][29]!="" && $_SESSION['logic_erp']['mandatory_field'][90][39]!="" && $_SESSION['logic_erp']['mandatory_field'][90][41]!="" && $_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {

	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][19].'*'.$_SESSION['logic_erp']['mandatory_field'][90][20].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][19].'*'.$_SESSION['logic_erp']['mandatory_field'][90][20].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// }elseif($_SESSION['logic_erp']['mandatory_field'][90][20]!="" && $_SESSION['logic_erp']['mandatory_field'][90][29]!="" && $_SESSION['logic_erp']['mandatory_field'][90][39]!="" && $_SESSION['logic_erp']['mandatory_field'][90][41]!="" && $_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][20].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][20].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// }elseif( $_SESSION['logic_erp']['mandatory_field'][90][29]!="" && $_SESSION['logic_erp']['mandatory_field'][90][39]!="" && $_SESSION['logic_erp']['mandatory_field'][90][41]!="" && $_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][29].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// }elseif(  $_SESSION['logic_erp']['mandatory_field'][90][39]!="" && $_SESSION['logic_erp']['mandatory_field'][90][41]!="" && $_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33].'*'.$_SESSION['logic_erp']['mandatory_field'][90][39]) . "';\n";
	// }elseif( $_SESSION['logic_erp']['mandatory_field'][90][41]!="" && $_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][41].'*'.$_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// }elseif($_SESSION['logic_erp']['mandatory_field'][90][42]!="" && $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][42].'*'.$_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// }elseif( $_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	// {
	// 	echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// 	echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	// }
	
	

	/*if($_SESSION['logic_erp']['mandatory_field'][90][20]!="")
	{
		echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][20]) . "';\n";
		echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][20]) . "';\n";
	}
	if($_SESSION['logic_erp']['mandatory_field'][90][33]!="")
	{
		echo "var mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
		echo "var mandatory_message = '". ($_SESSION['logic_erp']['mandatory_field'][90][33]) . "';\n";
	}*/
	
?>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][90] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
function openmypage_booking(page_link,title)
{
	var company=$("#cbo_company_name").val()*1;
	//alert(company);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1030px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>');

			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_booking_non_order_controller" );
			get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_booking_non_order_controller');
			print_button_setting();
			check_kniting_charge();
			reset_form('orderdetailsentry_2','booking_list_view','','','');
			show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/sample_booking_non_order_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			release_freezing();
		}
	}
}

function color_from_library(company_id)
{
	var color_from_library=return_global_ajax_value(company_id, 'color_from_library', '', 'requires/sample_booking_non_order_controller');
	if(color_from_library==1)
	{
		$('#txt_gmt_color').attr('readonly',true);
		$('#txt_gmt_color').attr('placeholder','Click');
		$('#txt_gmt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");

		$('#txt_color').attr('readonly',true);
		$('#txt_color').attr('placeholder','Click');
		$('#txt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");

	}
	else
	{
		$('#txt_gmt_color').attr('readonly',false);
		$('#txt_gmt_color').removeAttr('placeholder','Click');
		$('#txt_gmt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");

		$('#txt_color').attr('readonly',false);
		$('#txt_color').removeAttr('placeholder','Click');
		$('#txt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
	}
}

function color_select_popup(buyer_name,text_box)
{
	//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
	//alert(page_link)
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_booking_non_order_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#'+text_box).val(color_name.value);
		}
	}
}

function calculate_requirement()
{
	var cbo_company_name= document.getElementById('cbo_company_name').value;
	if(cbo_company_name=="")
	{
		alert("Select Company");
		return;
	}

	var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value;
	if(cbo_fabric_natu=="")
	{
		alert("Select Fabric Nature");
		return;
	}
	var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/sample_booking_non_order_controller');
	var txt_finish_qnty=(document.getElementById('txt_finish_qnty').value)*1;
	var processloss=(document.getElementById('txt_process_loss').value)*1;
	var WastageQty='';
	process_loss_method_id= process_loss_method_id.trim();//process_loss_method_id.trim();
	if(process_loss_method_id==1)
	{
		WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
	}
	else if(process_loss_method_id==2)
	{
		var devided_val = 1-(processloss/100);
		var WastageQty=parseFloat(txt_finish_qnty/devided_val);
	}
	else
	{
		WastageQty=0;
	}
	WastageQty= number_format_common( WastageQty, 5, 0) ;
	document.getElementById('txt_grey_qnty').value= WastageQty;
	document.getElementById('process_loss_method').value= process_loss_method_id;
	document.getElementById('txt_amount').value=number_format_common((document.getElementById('txt_rate').value)*1*WastageQty,5,0)
}

function fnc_fabric_booking( operation )
{
	if(operation==1)
	{
		var txt_booking_no=$('#txt_booking_no').val();
		var issue_number=return_global_ajax_value(txt_booking_no, 'booking_no_check', '', 'requires/sample_booking_non_order_controller');


		if(trim(issue_number)!='')
		{
			alert('Source Changing not Allowed .Yarn has already been issued.'+issue_number);
			get_php_form_data( txt_booking_no, "populate_data_from_search_popup", "requires/sample_booking_non_order_controller" );
			return;
		}
	}
	//alert(mst_mandatory_field);
	if(mst_mandatory_field)
		{
			if (form_validation(mst_mandatory_field,mst_mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}

	/*if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}*/
	if(document.getElementById('id_approved_id').value==1)
	{
		alert("This booking is approved")
		return;
	}

	if(document.getElementById('is_found_dtls_part').value==0)
	{
		alert("Without detail part save ,ready to approved request not allowed.")
		return;
	}

	var delivery_date=$('#txt_delivery_date').val();
	if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
	{
		alert("Delivery Date Not Allowed Less than Booking Date");
		return;
	}

 	if (form_validation('cbo_company_name*cbo_buyer_name*cbo_fabric_natu*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name','Company Name*Buyer Name*Fabric Nature*Booking Date*Delivery Date*Pay Mode*Supplier')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_ready_to_approved*cbo_team_leader*cbo_dealing_merchant*cbouom_id*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/sample_booking_non_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_reponse;
	}
}

function fnc_fabric_booking_reponse()
{
	if(http.readyState == 4)
	{
		 var reponse=trim(http.responseText).split('**');
		 if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
			 document.getElementById('txt_booking_no').value=reponse[1];
			  document.getElementById('update_id').value=reponse[2];
			 set_button_status(1, permission, 'fnc_fabric_booking',1);
			 show_msg(trim(reponse[0]));
			 if(parseInt(trim(reponse[0]))==2)
			 {
				 reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>');
				 reset_form('orderdetailsentry_2','','','','');
			 }
		}
		if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='yarnallocation'){
			alert("Yarn Allocation Found.\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}

		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}

		if(trim(reponse[0])=='PPL'){
			alert("Plan Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}

		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
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

		// release_freezing();
		// if(trim(reponse[0])!=2)
		// {
		// enable_disable2(1);
		// }
	}
}


function open_fabric_decription_popup()
{
	var mst_fabric_source=document.getElementById('cbo_fabric_source').value;
	if(mst_fabric_source == 0)
	{
		if (form_validation('cbo_fabric_source_dtls','Fabric Source')==false)
		{
			return;
		}
	}
	var cbofabricnature=document.getElementById('cbo_fabric_natu').value;
	var cbofabricnature=document.getElementById('cbo_fabric_natu').value;
	var dtls_id=document.getElementById('update_id_details').value;
	var yarncountid=document.getElementById('libyarncountdeterminationid').value;
	var oldyarncountid=document.getElementById('oldlibyarncountdeterminationid').value;
	var fab_souce=document.getElementById('cbo_fabric_source_dtls').value;
	var page_link='requires/sample_booking_non_order_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&yarncountid='+yarncountid;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var fab_des_id=this.contentDoc.getElementById("fab_des_id");
		var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
		var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
		var fab_gsm=this.contentDoc.getElementById("fab_gsm");
		var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
		var construction=this.contentDoc.getElementById("construction");
		var composition=this.contentDoc.getElementById("composition");
		document.getElementById('libyarncountdeterminationid').value=fab_des_id.value;
		document.getElementById('txt_fabricdescription').value=fab_desctiption.value;
		document.getElementById('txt_gsm').value=fab_gsm.value;
		document.getElementById('yarnbreackdown').value=yarn_desctiption.value;
		document.getElementById('construction').value=construction.value;
		document.getElementById('composition').value=composition.value;
		if(fab_souce ==1)
		{
			show_list_view(yarn_desctiption.value+'**'+dtls_id+'**'+fab_des_id.value+'**'+oldyarncountid,'yarn_cost_dtls','yarn_cost_dtls','requires/sample_booking_non_order_controller','');
		}
		else
		{
			$('#yarn_cost_dtls').html("");
		}

	}
}

function open_sample_popup()
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	if(cbo_company_name==0)
	{
		alert("Select Company")
		return;
	}
	if(cbo_buyer_name==0)
	{
		alert("Select Buyer")
		return;
	}
	var page_link='requires/sample_booking_non_order_controller.php?action=sample_description_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sample Description', 'width=950px,height=420px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var style_id=this.contentDoc.getElementById("style_id");
		var style_no=this.contentDoc.getElementById("style_no");
		var sample_id=this.contentDoc.getElementById("sample_id");
		var article_no=this.contentDoc.getElementById("article_no");
		var bh_qty=this.contentDoc.getElementById("bh_qty");
		var gmt_color=this.contentDoc.getElementById("gmt_color");
		var size_qty=this.contentDoc.getElementById("size_qty");
		var dtls_id=this.contentDoc.getElementById("hid_dtls_id");
		var sample_name_id=this.contentDoc.getElementById("sample_name_id");


		document.getElementById('txt_style').value=style_id.value;
		document.getElementById('txt_style_no').value=style_no.value;
		document.getElementById('cbo_sample_type').value=sample_id.value;
		document.getElementById('txt_article_no').value=article_no.value;
		document.getElementById('txt_bh_qty').value=bh_qty.value;
		document.getElementById('txt_gmt_color').value=gmt_color.value;
		document.getElementById('txt_rf_qty').value=size_qty.value-bh_qty.value;
		document.getElementById('txt_style_dtls_id').value=dtls_id.value;
		document.getElementById('cbo_sample_type').value=sample_name_id.value;
		$('#txt_article_no').attr('disabled','true');
		$('#txt_bh_qty').attr('disabled','true');
		$('#txt_rf_qty').attr('disabled','true');
		$('#txt_gmts_size').attr('disabled','true');
		$('#cbo_sample_type').attr('disabled','true');
		$('#txt_gmt_color').attr('disabled','true');
		//$('#txt_gsm').attr('disabled','true');
		$('#txt_article_no').attr('disabled','true');
	}
}

	function fnc_fabric_booking_dtls( operation )
	{
		freeze_window(operation);
		var f_sourc=$('#cbo_fabric_source').val();
		var f_sourc_dtls=$('#cbo_fabric_source_dtls').val();
		/*if(f_sourc==0)
		{
			alert('Please , Select Fabric Source from Master part.');
			release_freezing();
			return;
		}*/
	//	alert(mandatory_field+'**'+mandatory_message);
		if(mandatory_field)
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
		//alert(f_sourc);
		if(f_sourc==2)
		{
			if (form_validation('txt_rate','Rate')==false)
			{
				release_freezing();
				return;
			}
		}

		/*if(f_sourc>0 && f_sourc_dtls>0)
		{
			alert('Fabric Source has already Selected.');
			release_freezing();
			return;
		}*/
		if(f_sourc == 0 && f_sourc_dtls==0)
		{
			alert('Please Select Fabric Source.');
			release_freezing();
			return;
		}

		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if (form_validation('cbo_body_part*txt_finish_qnty*cbo_sample_type*txt_fabricdescription*txt_color*cbouom','Body Part*Finish Fabric*Sample type*Fabric Description*Fabric Color*Uom')==false)
		{
			release_freezing();
			return;
		}
		/*if(document.getElementById('libyarncountdeterminationid').value==0 || document.getElementById('libyarncountdeterminationid').value=="")
		{
			alert("You may have copied Fabric Description,Please Browse it");
			release_freezing();
			return;
		}*/

		/*if('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][90]);?>')
		{

		if (form_validation('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][90]);?>','<? //echo implode('*',$_SESSION['logic_erp']['field_message'][90]);?>')==false)
		{
			release_freezing();
			return;
		}

		}*/
		var row_num='';
		var data_all=""; var z=1;
		if(f_sourc_dtls ==1)
		{
			var row_num=$('#tbl_yarn_cost tr').length-1;
			for (var i=1; i<=row_num; i++)
			{
				//data_all=data_all+get_submitted_data_string('yarn_dtls_id_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'+i,"../../");
				data_all+="&yarn_dtls_id_" + z + "='" + $('#yarn_dtls_id_'+i).val()+"'"+"&cbocount_" + z + "='" + $('#cbocount_'+i).val()+"'"+"&cbocompone_" + z + "='" + $('#cbocompone_'+i).val()+"'"+"&percentone_" + z + "='" + $('#percentone_'+i).val()+"'"+"&cbotype_" + z + "='" + $('#cbotype_'+i).val()+"'"+"&consqnty_" + z + "='" + $('#consqnty_'+i).val()+"'";
				z++;
			}
		}

		var data="action=save_update_delete_dtls&operation="+operation+"&row_num="+row_num+get_submitted_data_string('txt_booking_no*update_id*cbo_fabric_natu*cbo_fabric_source*cbo_body_part*cbo_color_type*txt_style*txt_style_des*cbo_sample_type*libyarncountdeterminationid*oldlibyarncountdeterminationid*construction*composition*yarnbreackdown*txt_fabricdescription*txt_gsm*txt_gmt_color*txt_color*txt_gmts_size*txt_size*txt_dia_width*txt_finish_qnty*txt_process_loss*txt_grey_qnty*txt_rate*txt_amount*update_id_details*process_loss_method*txt_article_no*txt_yarn_details*txt_remarks*cbo_body_type*txt_item_qty*txt_knitting_charge*txt_bh_qty*txt_rf_qty*cbo_fabric_source_dtls*txt_delivery_dates*txt_style_dtls_id*cbo_pay_mode*cbouom*cbo_dia_width_type*hidden_collarCuff_data*hidden_collar_cuff_ids',"../../")+data_all;
		//alert(data);
		//release_freezing();
		//return;
		
		http.open("POST","requires/sample_booking_non_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_fabric_booking_dtls_reponse()
	{
		if(http.readyState == 4){
			var reponse=http.responseText.split('**');
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='yarnallocation'){
				alert("Yarn Allocation Found.\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='PPL'){
				alert("Plan Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='sal1'){
				alert("Sales Order found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
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
			 if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
				 show_msg(trim(reponse[0]));
				// if(trim(reponse[0])==2)
				// { 
				// 	reset_form('orderdetailsentry_2','','','','');
				// 	document.getElementById('cbouom').disabled = false;
				// }
				var fab_sourc=$('#cbo_fabric_source').val();
					//alert(fab_sourc);
				 set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
				 if(fab_sourc!=2)
				 {
				 show_list_view(reponse[4]+'**'+reponse[2]+'**'+reponse[3]+'**'+reponse[3],'yarn_cost_dtls','yarn_cost_dtls','requires/sample_booking_non_order_controller','');
				 }
				 show_list_view(reponse[1],'show_fabric_booking','booking_list_view','requires/sample_booking_non_order_controller','setFilterGrid(\'list_view\',-1)');
			 }
			// release_freezing();
			// if(trim(reponse[0])!=2)
			// {
			// 	enable_disable2(2);
			// 	release_freezing();
			// }
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

	function enable_disable(value)
	{
		/*if(value==2){
			document.getElementById('txt_rate').disabled=false;
		}
		else
		{
			document.getElementById('txt_rate').disabled=true;
		}*/
	}
	// function enable_disable2(type)
	// {
	// 	$("#cbo_fabric_source").attr('disabled',true);
	// 	if(type!=1)
	// 	{
	// 	$("#cbo_fabric_source_dtls").attr('disabled',true);
	// 	}
	// }

	function generate_fabric_report(type)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			$report_title=$( "div.form_caption" ).html();
			if(type==2)
			{
				var data="action=show_fabric_booking_report2"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==1)
			{
			var data="action=show_fabric_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==3)
			{
				var data="action=show_fabric_booking_report3"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==4)
			{
				var data="action=show_fabric_booking_report4"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==5)
			{
				var data="action=show_fabric_booking_report5"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==6)
			{
				var data="action=show_fabric_booking_report6"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==7)
			{
				var data="action=show_fabric_booking_report7"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			//freeze_window(5);
			http.open("POST","requires/sample_booking_non_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
	}


	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4)
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
     '<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/sample_booking_non_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);

	}

	function check_kniting_charge()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_kniting_charge', '', 'requires/sample_booking_non_order_controller');

		var response=response.split("_");
		if(response[0]==1)
		{
			$('#txt_knitting_charge').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_knitting_charge').attr('disabled','disabled');
			$('#txt_knitting_charge').val('');

		}
	}
/*	function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		if(company==cbo_supplier_name && cbo_pay_mode==5){
			alert("Same Company Not Allowed");
			document.getElementById('cbo_supplier_name').value=0;
			return;
		}

	}*/
	function print_button_setting()
	{
		$('#button_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sample_booking_non_order_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");

		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==34)
			{
				$('#button_panel').append( '<input type="button" value="Print 1" onClick="generate_fabric_report(1)"  style="width:100px" name="print" id="print" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==35)
			{
				$('#button_panel').append( '<input type="button" value="Print 2" onClick="generate_fabric_report(2)"  style="width:100px" name="print" id="print" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==36)
			{
				$('#button_panel').append( '<input type="button" value="Print 3" onClick="generate_fabric_report(3)"  style="width:100px" name="print3" id="print3" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==37)
			{
				$('#button_panel').append( '<input type="button" value="Print 4" onClick="generate_fabric_report(4)"  style="width:100px" name="print4" id="print4" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==64)
			{
				$('#button_panel').append( '<input type="button" value="Print 5" onClick="generate_fabric_report(5)"  style="width:100px" name="print5" id="print5" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==72)
			{
				$('#button_panel').append( '<input type="button" value="Print 6" onClick="generate_fabric_report(6)"  style="width:100px" name="print6" id="print6" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==174)
			{
				$('#button_panel').append( '<input type="button" value="Print 7" onClick="generate_fabric_report(7)"  style="width:100px" name="print7" id="print7" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
		}
	}

	function open_trims_acc_popup(title)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
			var update_id=document.getElementById('update_id').value;
		if (txt_booking_no=="")
		{
			alert("Save The Booking First")
			return;
		}
		else
		{
			page_link='requires/sample_booking_non_order_controller.php?action=acc_popup'+get_submitted_data_string('txt_booking_no*update_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=470px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				//var theform=this.contentDoc.forms[0];
				//var theemail=this.contentDoc.getElementById("selected_data").value;
				//document.getElementById('trims_acc_hidden_data').value=theemail;
			}
		}
	}

	function fabic_srce_con_fnc()
	{
	   if($('#cbo_fabric_source').val()!=0)
	   {
			 $('#cbo_fabric_source_dtls').attr('disabled',true);
			 $('#cbo_fabric_source_dtls').val(0);
			 $('#cbo_fabric_source_dtls_id').css('color','black');
	   }
	   else
	   {
			$('#cbo_fabric_source_dtls').attr('disabled',false);
			$('#cbo_fabric_source_dtls_id').css('color','blue');

			if (form_validation('cbo_fabric_source_dtls','Fabric Source Dtls')==false)
			{
				return;
			}
	   }
	}

	function fabric_up_con_fnc()
	{
		if($('#update_id_details').val()!=0)
		{
			alert('Please, update Master and Details part.');
			//fabic_srce_con_fnc();
		}
	}


	function openpage_collarCuff() {
        	var collarCuff_data = $('#hidden_collarCuff_data').val();
        	var hidden_bodypartID_data = $('#hidden_bodypartID_data').val();
        	var update_dtls_id = $('#update_id_details').val();

			// alert(update_dtls_id);
        	if (update_dtls_id == "") {
        		alert("Save Data First");
        		return;
        	}
        	var page_link = 'requires/sample_booking_non_order_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&hidden_bodypartID_data='+hidden_bodypartID_data;
        	var title = 'Collar & Cuff Measurement Info';

		
        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;			
				
				var data=hidden_collarCuff_data.split('**');
				 
				
        		$('#hidden_collarCuff_data').val(data[0]);
				$('#hidden_collar_cuff_ids').val(data[1]);
        	}
        }
</script>
</head>
<body onLoad="set_hotkey(); check_exchange_rate();check_kniting_charge();print_button_setting();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:950px;">
        <legend>Sample Fab.Booking [Without Order]</legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="130" align="right"> Booking No </td>              <!-- 11-00030  -->
                    <td width="170"><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/sample_booking_non_order_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                    <td width="130" align="right" class="must_entry_caption">Company Name</td>
                    <td width="170"><? echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_booking_non_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );color_from_library( this.value );check_kniting_charge();print_button_setting();check_exchange_rate();","","" ); // new condtion
                    ?>
                    	<input type="hidden" id="update_id" name="update_id"/>
                        <input type="hidden" id="report_ids" name="report_ids"/>

                    </td>
                    <td width="130" align="right" class="must_entry_caption">Buyer Name</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Fabric Nature</td>
                    <td><? echo create_drop_down( "cbo_fabric_natu", 172, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");	?></td>
                    <td align="right" width="130">Fabric Source</td>
                    
					<td><? echo create_drop_down( "cbo_fabric_source", 172, $fabric_source,"", 1, "-- Select --", $selected,"", "", "2,3");	?></td>
                    <td align="right" class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    <td align="right">Exchange Rate</td>
                    <td><input style="width:160px;" type="text" class="text_boxes" name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                    <td align="right"> Source</td>
                    <td><? echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );	?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption" align="right">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td align="right" class="must_entry_caption">Pay Mode</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "load_drop_down( 'requires/sample_booking_non_order_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?></td>
                    <td class="must_entry_caption" align="right"> Supplier Name </td>              <!-- 11-00030  -->
                    <td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_non_order_controller');",0 ); ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">Attention</td>
                    <td align="left" colspan="3"><input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/sample_booking_non_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    	<input type="hidden" id="id_approved_id">
                    </td>
                    <td align="right">Ready To Approved</td>
                    <td align="center"><? echo create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_booking_non_order_controller');","","" ); ?>
                    	<input type="hidden" name="is_found_dtls_part" id="is_found_dtls_part" value="1"/>
                    </td>
                </tr>

				<tr>
                    <td align="right" class="">Team Leader</td>
                    <td><? echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_booking_non_order_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' ) " ); ?></td>
                    <td align="right">Dealing Merchant</td>
                    <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 172, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                    <td align="right">Copy From</td>
                    <td><input class="text_boxes" type="text" style="width:160px" name="txt_copy_from_booking" id="txt_copy_from_booking" readonly /></td>
                </tr>
				<tr>
                     <td align="right" class="" style="display: none">UOM</td>
                	<td style="display: none"><? echo create_drop_down( "cbouom_id", 172, $unit_of_measurement,'', 1, '-UOM-',"", "",0,"1,12,23,27" ); ?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	<input type="button" class="image_uploader" style="width:130px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'sample_booking_non', 0 ,1)">&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'sample_booking_non', 2 ,1)">&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" id="set_button" class="image_uploader" style="width:130px;" value="Accessories" onClick="open_trims_acc_popup('Accessories Dtls')" /></td>
                    <td>&nbsp;</td>
                    <td>
						<?
							include("../../terms_condition/terms_condition.php");
							terms_condition(90,'txt_booking_no','../../');
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    	<? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" height="10"></td>
                </tr>
            </table>
        </fieldset>
    </form>
    <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
        <fieldset style="width:950px;">
        <legend>Sample Fab. Booking Dtls. [Without Order] </legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="130" align="right" > Style ref </td>              <!-- 11-00030  -->
                    <td width="170" >
                        <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:160px" onDblClick="open_sample_popup()" placeholder="Double Click to Search" readonly/>
                        <input type="hidden" id="txt_style" name="txt_style" class="text_boxes" style="width:100px"/>
                        <input type="hidden" id="txt_style_dtls_id" name="txt_style_dtls_id" style="width:40px"/>
						<input type="hidden" id="hidden_collarCuff_data" name="hidden_collarCuff_data" style="width:40px"/>
						<input type="hidden" id="hidden_bodypartID_data" name="hidden_bodypartID_data" style="width:40px"/>
						<input type="hidden" id="hidden_collar_cuff_ids" name="hidden_collar_cuff_ids" style="width:40px"/>
                    </td>
                    <td align="right">Style Des.</td>
                    <td><input type="text" id="txt_style_des" name="txt_style_des"  class="text_boxes" style="width:160px"/></td>
                    <td align="right" class="must_entry_caption" >Sample type</td>
                    <td id="sampletd"><? echo create_drop_down( "cbo_sample_type", 172, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "","",'' ); ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Body Part</td>
                    <td><? echo create_drop_down( "cbo_body_part", 172, $body_part,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td align="right">Color Type</td>
                    <td><? echo create_drop_down( "cbo_color_type", 172, $color_type,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td align="right" class="must_entry_caption">Fabric Description</td>
                    <td id="fabricdescription_id_td">
                        <input type="hidden" id="libyarncountdeterminationid"  name="libyarncountdeterminationid" class="text_boxes" style="width:10px" />
                        <input type="hidden" id="oldlibyarncountdeterminationid"  name="oldlibyarncountdeterminationid" class="text_boxes" style="width:10px" />
                        <input type="hidden" id="construction" name="construction" class="text_boxes" style="width:10px" />
                        <input type="hidden" id="composition" name="composition" class="text_boxes" style="width:10px"  />
                        <input type="hidden" id="yarnbreackdown" name="yarnbreackdown"  class="text_boxes" style="width:90px"/>
                        <input type="hidden" id="process_loss_method" name="process_loss_method" class="text_boxes" style="width:90px"/>
                        <input type="text" id="txt_fabricdescription" name="txt_fabricdescription" class="text_boxes" style="width:160px" onDblClick="open_fabric_decription_popup()" placeholder="Browse" readonly title="" />
                    </td>
                </tr>
                <tr>
                    <td align="right">GSM</td>
                    <td><input name="txt_gsm" id="txt_gsm" class="text_boxes" type="text" value=""  style="width:160px "/></td>
                    <td align="right">Gmts Color</td>
                    <td><input name="txt_gmt_color" id="txt_gmt_color" class="text_boxes" type="text" value=""  style="width:160px "/></td>
                    <td align="right" class="must_entry_caption">Fabric Color</td>
                    <td id="fabriccolor_id_id_td" ><input name="txt_color" id="txt_color" class="text_boxes" type="text" value=""  style="width:160px "/></td>
                </tr>
                <tr>
                    <td align="right">Gmts size</td>
                    <td><input name="txt_gmts_size" id="txt_gmts_size" class="text_boxes" type="text" value="" style="width:160px "/></td>
                    <td align="right">Item size</td>
                    <td id="itemsize_id_td"><input name="txt_size" id="txt_size" class="text_boxes" type="text" value=""  style="width:160px "/></td>
                    <td align="right">Dia/ Width</td>
                    <!--<td><input name="txt_dia_width" id="txt_dia_width" class="text_boxes" type="text" value=""  style="width:160px "/></td>-->
                    <td>
                        <input name="txt_dia_width" id="txt_dia_width" class="text_boxes" type="text" value=""  style="width:60px "/>
                        <?
                        echo create_drop_down('cbo_dia_width_type', 100, $fabric_typee,'', 1, '-- Select --', '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Finish Fabric</td>
                    <td><input name="txt_finish_qnty" id="txt_finish_qnty" class="text_boxes_numeric" type="text" onChange="calculate_requirement()" value=""  style="width:160px "/></td>
                    <td align="right">Process loss </td>              <!-- 11-00030  -->
                    <td><input name="txt_process_loss" id="txt_process_loss" class="text_boxes_numeric" type="text" value="" onChange="calculate_requirement()"   style="width:160px "/></td>
                    <td align="right">Gray Fabric</td>
                    <td align="left"><input name="txt_grey_qnty" id="txt_grey_qnty" class="text_boxes_numeric" type="text" value=""  style="width:160px " readonly/></td>
                </tr>
                <tr>
                    <td align="right">Article Number</td>
                    <td><input name="txt_article_no" class="text_boxes" ID="txt_article_no" style="width:160px" maxlength="50" title="Maximum 50 Character"></td>
                    <td align="right">Rate</td>
                    <td><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" value=""  style="width:160px " onChange="calculate_requirement()" /></td>
                    <td align="right">Amount</td>
                    <td><input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:160px " readonly/></td>
                </tr>
                <tr>
                    <td align="right">Body Type</td>
                    <td><? echo create_drop_down( "cbo_body_type", 170, $body_type_arr,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td align="right">Item Qty.</td>
                    <td><input name="txt_item_qty" id="txt_item_qty" class="text_boxes_numeric" type="text" value=""  style="width:160px" maxlength="200" title="Maximum 200 Character" /></td>
                    <td align="right">Yarn Details</td>
                    <td><input name="txt_yarn_details" class="text_boxes" ID="txt_yarn_details" style="width:160px" maxlength="200" title="Maximum 200 Character"></td>
                </tr>
                <tr>
                    <td align="right" id="td_knit_caption" >Knitting Charge/KG</td>
                    <td id="td_knit_input"><input name="txt_knitting_charge" id="txt_knitting_charge" class="text_boxes_numeric" type="text" value=""  style="width:160px"  /></td>
                    <td align="right">GMT Qty </td>
                    <td>
                        <input name="txt_bh_qty" id="txt_bh_qty" class="text_boxes_numeric" type="text" style="width:70px " placeholder="BH Qty"/>
                        <input name="txt_rf_qty" id="txt_rf_qty" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="RF Qty"/>
                    </td>
                    <td align="right" class="">Delivery Date</td>
                    <td><input id="txt_delivery_dates" class="datepicker" style="width:160px" name="txt_delivery_dates" type="text"></td>
                </tr>
                <tr>
                    <td align="right">Remarks</td>
                    <td >
                        <input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" value=""  style="width:160px" maxlength="200" title="Maximum 200 Character"  />
                        <input type="hidden" id="update_id_details">
                    </td>
                    <td align="right" class="must_entry_caption">UOM</td>
                	<td><? echo create_drop_down( "cbouom", 172, $unit_of_measurement,"", 1, "-UOM-",$selected, "", "","1,12,23,27" ); ?></td>

                    <td align="right" width="130" id="cbo_fabric_source_dtls_id">Fabric Source</td>
                    <td><? echo create_drop_down( "cbo_fabric_source_dtls", 172, $fabric_source,"", 1, "-- Select --", $selected,"", "", "1,4"); ?></td>
                </tr>
                <tr>
                	<td align="right" colspan="6"></td>
                </tr>
            </table>
            <div id="yarn_cost_dtls"></div>
            <table width="900" cellspacing="2" cellpadding="0" border="0">

                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
						<? echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2) ; ?>
						<input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff" onClick="openpage_collarCuff();" style="width:100px" />
                        <div id="button_panel"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <fieldset style="width:1510px;">
        <legend>Booking Entry</legend>
        <table style="border:none" cellpadding="0" cellspacing="2" border="0">
            <tr align="center">
            	<td id="booking_list_view"></td>
            </tr>
        </table>
    </fieldset>
    </div>
    <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
load_drop_down( 'requires/sample_booking_non_order_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
});
//set_multiselect( 'cbo_booking_gr', '1', '0', '0', '0' );
</script>
</html>