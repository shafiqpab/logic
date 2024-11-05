<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for buyer BTB/Mragin LC amendment
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar
Creation date 	: 	6-05-2013
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
echo load_html_head_contents("BTB/Mragin LC Amendment Form", "../../", 1, 1,'','1','');

$item_category_mix=array(1=>"Yarn",2=>"Knit Finish Fabrics",3=>"Woven Fabrics",4=>"Accessories",5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals",8=>"Spare Parts",9=>"Spare Parts & Machinaries",10=>"Other Capital Items",11=>"Stationaries",12=>"Services - Fabric",13=>'Grey Fabric(Knit)',14=>'Grey Fabric(woven)',15=>'Electical',16=>'Maintenance',17=>'Medical',18=>'ICT',19=>'Print & Publication',20=>'Utilities & Lubricants',21=>'Construction Materials',22=>'Printing Chemicals & Dyes',23=>'Dyes Chemicals & Auxilary Chemicals',24=>'Services - Yarn Dyeing ',25=>'Services - Embellishment',28=>'Cut Panel',30=>'Garments',31=>'Services Lab Test',32=>'Vehicle Components',33=>'Others',110=>'Knit Fabric');

?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';


function fnc_amendment_save(operation) 
{ 
	if(operation==2)
	{
		show_msg('13');
		return;
	}
	var ref_closing_status=$("#hidden_ref_closing_status").val();	
	var txt_gmt_qnty_amnd=$("#txt_gmt_qnty_amnd").val();	
	if(txt_gmt_qnty_amnd!="" && txt_gmt_qnty_amnd>0)
	{
		if ( form_validation('cbo_garments_qnty_change_by','Garments Qnty Change By')==false )
		{
			return;
		}
	}

	if(ref_closing_status==1)
	{
		alert("This reference is closed. No operation is allowed.");
		$("#txt_pi").attr("readonly",true);
		$("#txt_hidden_pi_id").val(' ');
		return;
	}
	if ( form_validation('txt_btb_lc_no*txt_amendment_no*txt_amendment_date','BTB LC No*Amendment No*Amendment Date')==false )
	{
		return;
	}
	else if(parseInt(Number($("#txt_amendment_no").val()))==0)
	{
		alert("Amendment No Should Be Greater Than 0");
		$("#txt_amendment_no").val('');
		$("#txt_amendment_no").focus();
		return;
	}
   /*else if($("#cbo_value_change_by").val()==0 && $("#cbo_lc_basis_id").val()==2)
	{
		alert("Please Select Value Changed By");
		$("#cbo_lc_basis_id").focus();
		return;
	}
	else if($("#txt_pi").val()=="" && $("#cbo_lc_basis_id").val()==1)
	{
		alert("Please Select PI");
		$("#txt_pi").focus();
		return;
	}*/
	else
	{
		if(ref_closing_status!=1)
		{	
			
			var data="action=save_update_delete_amendment&operation="+operation+get_submitted_data_string('txt_amendment_no*txt_amendment_date*txt_amendment_value*cbo_value_change_by*txt_hidden_pi_id*txt_pi_value*txt_last_shipment_date_amnd*txt_expiry_date_amend*cbo_delevery_mode_amnd*cbo_inco_term*txt_inco_term_place*cbo_partial_ship_id*txt_port_of_loading_amnd*txt_port_of_discharge_amnd*cbo_pay_term_amnd*txt_gmt_qnty_amnd*cbo_gmt_uom_id_amnd*txt_application_date_amnd*txt_ud_no_amnd*txt_ud_date_amnd*txt_tenor_amnd*txt_addendum_no*txt_addendum_date*txt_remarks_amnd*txt_system_id*update_id*hide_amendment_value*hide_value_change_by*cbo_garments_qnty_change_by*hdn_gmt_qnty_amnd*hdn_qnty_change_by',"../../");
		
			freeze_window(operation);
		
			http.open("POST","requires/btb_margin_lc_amendment_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_amendment_save_Reply_info;
		}else{
			alert("This reference is closed. No operation is allowed.");
			return;
		}
	}	
}


function fnc_amendment_save_Reply_info()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));	
		
		if((reponse[0]==0 || reponse[0]==1))
		{	
			reset_form('amendmentFrm_1','','','','');
			get_php_form_data( reponse[2], "populate_data_from_btb_lc", "requires/btb_margin_lc_amendment_controller" );
			set_button_status(0, permission, 'fnc_amendment_save',1);
		}
		else if(reponse[0]==14)
		{
			alert("This is not your last amendment. So You can't change it.");
		}
		
		release_freezing();		
	}
}

function open_terms_condition_popup(page_link,title)
{
	var txt_btb_lc_no=document.getElementById('txt_btb_lc_no').value;
	if (txt_btb_lc_no=="")
	{
		alert("Save The Lc No First")
		return;
	}
	else
	{
		page_link=page_link+get_submitted_data_string('txt_btb_lc_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}

function openamendment_popup()
{
	
 	if ( form_validation('txt_btb_lc_no','BTB LC No')==false )
	{
		return;
	}
	
	var page_link='requires/btb_margin_lc_amendment_controller.php?action=amendment_popup&btb_lc_id='+$('#txt_system_id').val();
	var title='Amendment List';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=360px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		
		var theform=this.contentDoc.forms[0];
		var hidden_amendment_no=this.contentDoc.getElementById("hidden_amendment_no").value;
		if(trim(hidden_amendment_no)!="")
		{
			freeze_window(5);
			get_php_form_data( hidden_amendment_no, "get_amendment_data", "requires/btb_margin_lc_amendment_controller" );
			release_freezing();
		}
	}
}


function fn_add_btb_lc()
{ 

	var page_link='requires/btb_margin_lc_amendment_controller.php?action=btb_lc_search';
	var title='BTB L/C Search Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1080px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var btb_id=this.contentDoc.getElementById("hidden_btb_id").value;
		var ref_closing_status=this.contentDoc.getElementById("hidden_ref_closing_status").value;
		
		if(trim(btb_id)!="")
		{
			freeze_window(5);
			get_php_form_data( btb_id, "populate_data_from_btb_lc", "requires/btb_margin_lc_amendment_controller" );
			$("#hidden_ref_closing_status").val(ref_closing_status);
			release_freezing();
		}
	}
	
}

function openmypage()
{
	var pi_entry_form = $('#txt_pi_entry_form').val();
	var item_category = $('#cbo_item_category_id').val(); 
	var btb_id = $('#txt_system_id').val(); 
	var txt_hidden_pi_id = $('#txt_hidden_pi_id').val(); 
	var cbo_importer_id = $('#cbo_importer_id').val();
	var cbo_supplier_id=$("#cbo_supplier_id").val();

	if (form_validation('txt_btb_lc_no','BTB/Margin LC No')==false)
	{
		return;
	}
	else
	{ 	
		var title = 'PI Selection Form';	
		var page_link = 'requires/btb_margin_lc_amendment_controller.php?item_category_id='+item_category+'&txt_hidden_pi_id='+txt_hidden_pi_id+'&btb_id='+btb_id+'&pi_entry_form='+pi_entry_form+'&cbo_importer_id='+cbo_importer_id+'&cbo_supplier_id='+cbo_supplier_id+'&action=pi_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=450px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var pi_id=this.contentDoc.getElementById("txt_selected_id").value; 
			var pi_no=this.contentDoc.getElementById("txt_selected").value;
		 
			if (pi_id!="")
			{ 
				$('#txt_hidden_pi_id').val(pi_id);
				$('#txt_pi').val(pi_no);

				get_php_form_data(pi_id+"**"+document.getElementById("txt_lc_value").value+"**"+document.getElementById("cbo_currency_name").value, "set_value_pi_select", "requires/btb_margin_lc_amendment_controller" );
			} 
			else
			{
				$('#txt_pi').val('');
				$('#txt_hidden_pi_id').val('');	
			}
		}
	}
}


function active_inactive(str)
{
	document.getElementById('txt_amendment_value').value="";
	document.getElementById('txt_pi').value="";
	document.getElementById('txt_hidden_pi_id').value="";
	document.getElementById('txt_pi_value').value="";
	document.getElementById('cbo_value_change_by').value="0";
	
	if(str==1)
	{
		document.getElementById('txt_amendment_value').disabled=true;
		document.getElementById('cbo_value_change_by').disabled=true;
		document.getElementById('txt_pi').disabled=false;
	}
	else
	{
		document.getElementById('txt_amendment_value').disabled=false;
		document.getElementById('cbo_value_change_by').disabled=false;
		document.getElementById('txt_pi').disabled=true;
	}
}

function fnc_print_letter(printType){
	var actionPrint="";
	if(printType ==2) actionPrint = "print_amendment_letter_2";
	else if(printType ==3) actionPrint = "print_amendment_letter_3";
	else if(printType ==4) actionPrint = "print_amendment_letter_4";
	else actionPrint = "print_amendment_letter";

	var data="action="+actionPrint+get_submitted_data_string('cbo_importer_id*txt_amendment_no*txt_amendment_date*txt_amendment_value*hide_amendment_value*txt_hidden_pi_id*txt_pi_value*txt_last_shipment_date_amnd*txt_expiry_date_amend*txt_btb_lc_no*txt_lc_date*txt_system_id*update_id*hide_value_change_by*txt_internal_file_no*cbo_issuing_bank*cbo_currency_name*txt_lc_value*txt_pi*cbo_supplier_id*txt_gmt_qnty_amnd',"../../");
		
		//freeze_window(3);
		
		http.open("POST","requires/btb_margin_lc_amendment_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_print_letter_Reply_info;
}

function fnc_print_letter_Reply_info(){
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=http.responseText.split('**');
		//alert(reponse);
		document.getElementById('report_letter_container').innerHTML = reponse[0];
		//set_button_status(1, permission, 'fnc_btb_mst',1); 
		new_window();
		release_freezing();	
	}
}

function new_window()
{
	//document.getElementById('scroll_body').style.overflow="auto";
	//document.getElementById('scroll_body').style.maxHeight="none";
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_letter_container').innerHTML+'</body</html>');
	d.close();
	release_freezing();	
	//document.getElementById('scroll_body').style.overflow="auto";
	//document.getElementById('scroll_body').style.maxHeight="850px";
}





	function call_print_button_for_mail(mail){
		get_php_form_data(mail+"**"+document.getElementById("txt_system_id").value, "mail_action", "../../auto_mail/btb_margin_lc_amendment_auto_mail" );

	}


	 

</script>

<style>
#currentDataTable input{
	width:135px;
}
#amendmentDataTable input{
	width:135px;
}

</style> 

</head>
 
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?>
     
        <fieldset style="width:1070px; margin-bottom:10px;">
		<form id="amendmentFrm_1" name="amendmentFrm_1" >
            <fieldset style="width:520px; margin-bottom:10px;float:left">
            <legend align="center">Current Record</legend>
            	<table width="100%" class="" id="currentDataTable">
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right" class="must_entry_caption">BTB/Margin LC No</td>
                        <td colspan="2">
                            <input type="hidden" id="txt_system_id" readonly /> 
                            <input type="hidden" id="hidden_ref_closing_status" readonly /> 
                            <input type="text"  name="txt_btb_lc_no"  id="txt_btb_lc_no" class="text_boxes" placeholder="Double Click To Search" onDblClick="fn_add_btb_lc()"  readonly="readonly" >
                        </td>
                    </tr>
                    <tr>
                        <td width="110">Importer</td>
                        <td width="135">
							<?
								echo create_drop_down( "cbo_importer_id", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1 );
						   	?> 
                        </td>
                        <td width="109">Supplier Name</td>
						<td id="supplier_td">
                           <?
								echo create_drop_down( "cbo_supplier_id", 145, "select id, supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "Display", $selected, "",1 );
						   	?>    
						</td>
                    </tr>
                    <tr>
                        <td>Internal File No</td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" value="Display" class="text_boxes_numeric" disabled="disabled" /></td>
                        <td>LC Value</td>
                        <td><input type="text" name="txt_lc_value" id="txt_lc_value" value="Display" class="text_boxes_numeric" disabled="disabled" ></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_currency_name", 145, $currency,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                        <td>Item Category</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_item_category_id", 146, $item_category_mix,"", 1, "Display", 0, "",1 );
							?>
							<input type="hidden" id="txt_pi_entry_form" readonly />
                        </td>
                    </tr>
                    <tr>
                    	<td>Issuing Bank</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_issuing_bank", 145, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and issusing_bank=1 order by bank_name","id,bank_name", 1, "Display", 0, "",1 );
							?>
                        </td>
                        <td>LC Date</td>
                        <td><input type="text" name="txt_lc_date" id="txt_lc_date" class="datepicker" value="Display" disabled="disabled" ></td>
                    </tr> 
                    <tr>
                    	<td>Last Ship. Date</td>
                        <td><input type="text" name="txt_last_shipment_date" id="txt_last_shipment_date" value="Display" class="datepicker" disabled="disabled"></td>
                        <td>LC Expiry Date</td>
                        <td><input type="text" name="txt_expiry_date" id="txt_expiry_date" class="datepicker" value="Display" disabled="disabled"></td>
                    </tr> 
                    <tr>
                        <td>Tolerance %</td>
                        <td><input type="text" name="txt_tolerance" id="txt_tolerance" class="text_boxes_numeric" value="Display" disabled="disabled"></td>
                        <td>Delivery Mode</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_delevery_mode", 146, $shipment_mode,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                    </tr> 
                    <tr>
                        <td>Application Date</td>
                        <td><input type="text" name="txt_application_date" id="txt_application_date" class="text_boxes" value="Display" disabled="disabled"/></td>
                        <td>Port of Loading</td>
                        <td><input type="text" name="txt_port_of_loading" id="txt_port_of_loading" class="text_boxes" value="Display" disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td>Port of Discharge</td>
                        <td><input type="text" name="txt_port_of_discharge" id="txt_port_of_discharge" class="text_boxes" value="Display" disabled="disabled" /></td>
                        <td>Pay Term</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_pay_term", 146, $pay_term,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td>Garments Qnty & UOM</td>
                        <td>
                            <input type="text" name="txt_gmt_qnty" id="txt_gmt_qnty" placeholder="Display" disabled class="text_boxes_numeric" value="" style="width:75px;" />
                            <?php echo create_drop_down( "cbo_gmt_uom_id",55,$unit_of_measurement,'',0,'',1,'',1,1); ?>
                        </td>
                        <td>LC Basis</td>
                        <td> <?php echo create_drop_down( "cbo_lc_basis_id", 146, $lc_basis,'', 1,'Display',0,"",1); ?> </td>
                    </tr>
                    <tr>
                        <td>UD No</td>
                        <td><input type="text" name="txt_ud_no" id="txt_ud_no" class="text_boxes" value="Display" disabled="disabled" /></td>
                        <td>UD Date</td>
                        <td><input type="text" name="txt_ud_date" id="txt_ud_date" class="text_boxes" value="Display" disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td>Tenor</td>
                        <td ><input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" value="Display" disabled="disabled" /></td>
                    </tr>     
                    <tr>
                    	<td>Remarks</td>
               			<td colspan="4">
                        	<textarea name="txt_remarks" id="txt_remarks" style="width:97%" class="text_area" value="Display" disabled="disabled" ></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="4" height="18">&nbsp;</td>
                    </tr>     
                </table>
            </fieldset>
            <fieldset style="width:520px; margin-bottom:10px;margin-left:5px;float:left">
            <legend align="center">Amendment Record</legend>
            	<table width="100%" class="" id="amendmentDataTable">
                	<tr>
                    	<td class="must_entry_caption">Amendment No</td>
                        <td>
                        	<input type="text"  name="txt_amendment_no"  id="txt_amendment_no" class="text_boxes_numeric" placeholder="Double Click To Search" onDblClick="openamendment_popup()">
                            <input type="hidden" id="update_id" readonly />
                        </td>
                        <td class="must_entry_caption">Amendment Date</td>
                        <td><input type="text"  name="txt_amendment_date"  id="txt_amendment_date" class="datepicker" readonly></td>
                    </tr>
                	<tr>
                    	<td>PI No</td>
                        <td>
                            <input type="text" name="txt_pi" id="txt_pi" class="text_boxes" placeholder="Double Click for PI" onDblClick="openmypage()" />
                            <input type="hidden" name="txt_hidden_pi_id" id="txt_hidden_pi_id" class="text_boxes"  />
                        </td>
                       <td>PI Value</td>
                        <td>
                            <input type="text" name="txt_pi_value" id="txt_pi_value" class="text_boxes_numeric" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Amendment Value</td>
                        <td>
                        	<input type="text" name="txt_amendment_value" id="txt_amendment_value" class="text_boxes_numeric">
                            <input type="hidden" name="hide_amendment_value" id="hide_amendment_value" class="text_boxes_numeric">
                        </td>
                        <td>Value Changed By</td>
                        <td>
							<? echo create_drop_down( "cbo_value_change_by", 146, $increase_decrease,"", 1, "--- Select ---", 0, "" ); ?>
                            <input type="hidden" name="hide_value_change_by" id="hide_value_change_by" class="text_boxes_numeric">
                        </td>
                    </tr>
                    <tr>
                    	<td>Last Ship. Date</td>
                        <td><input type="text"  name="txt_last_shipment_date_amnd"  id="txt_last_shipment_date_amnd" class="datepicker" onChange="add_days(this.value,15,1,'txt_expiry_date_amend')"></td>
                        <td>Expiry Date</td>
                        <td><input type="text"  name="txt_expiry_date_amend"  id="txt_expiry_date_amend" class="datepicker"></td>
                    </tr>
                    <tr>
                    	<td>Delivery Mode</td>
                        <td><? echo create_drop_down( "cbo_delevery_mode_amnd", 146, $shipment_mode,"", 1, "--- Select ---", 0, "" ); ?></td>
                        <td>Incoterm</td>
                        <td> 
                        	<?
							   	echo create_drop_down( "cbo_inco_term", 146, $incoterm,"", 1, "--- Select ---", 0, "" );
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Incoterm Place</td>
                        <td><input type="text"  name="txt_inco_term_place"  id="txt_inco_term_place" class="text_boxes"></td>
                        <td>Partial Shipment</td>
                        <td><?php echo create_drop_down( "cbo_partial_ship_id",146,$yes_no,'',0,'',2,0,0); ?></td>
                    </tr>
                    <tr>
                    	<td>Port of Loading</td>
                        <td><input type="text"  name="txt_port_of_loading_amnd"  id="txt_port_of_loading_amnd" class="text_boxes"></td>
                        <td>Port of Discharge</td>
                        <td><input type="text"  name="txt_port_of_discharge_amnd"  id="txt_port_of_discharge_amnd" class="text_boxes"></td>
                    </tr>
                    <tr>
                        <td>Garments Qnty & UOM</td>
                        <td>
                            <input type="text" name="txt_gmt_qnty_amnd" id="txt_gmt_qnty_amnd"  class="text_boxes_numeric" value="" style="width:75px;" />
                            <input type="hidden" name="hdn_gmt_qnty_amnd" id="hdn_gmt_qnty_amnd"/>
                            <?php echo create_drop_down( "cbo_gmt_uom_id_amnd",55,$unit_of_measurement,'',0,'',1,'',1,1); ?>
                        </td>
						<td>Garments Qnty Change By</td>
                        <td>
							<? echo create_drop_down( "cbo_garments_qnty_change_by", 146, $increase_decrease,"",1,"--- Select ---",0,"" ); ?>
							<input type="hidden" name="hdn_qnty_change_by" id="hdn_qnty_change_by" />
                        </td>
                    </tr>
                    <tr>
                        <td>Pay Term</td>
                        <td><? echo create_drop_down( "cbo_pay_term_amnd", 146, $pay_term,"", 1, "--- Select ---", 0,"",0,'' ); ?></td>
                        <td>Tenor</td>
                        <td><input type="text"  name="txt_tenor_amnd"  id="txt_tenor_amnd" class="text_boxes"></td>
                    </tr>
                    <tr>
                        <td>UD No</td>
                        <td><input type="text" name="txt_ud_no_amnd" id="txt_ud_no_amnd" class="text_boxes" /></td>
                        <td>UD Date</td>
                        <td><input type="text" name="txt_ud_date_amnd" id="txt_ud_date_amnd" class="datepicker" /></td>
                    </tr>
                    <tr>
                    	<td >Addendum No</td>
                        <td>
                        	<input type="text"  name="txt_addendum_no"  id="txt_addendum_no" class="text_boxes" >
                        </td>
                        <td >Addendum Date</td>
                        <td><input type="text"  name="txt_addendum_date"  id="txt_addendum_date" class="datepicker" readonly></td>
                    </tr>
					<tr>
						<td>Application Amendment Date</td>
                        <td><input type="text" name="txt_application_date_amnd" id="txt_application_date_amnd" class="datepicker"  /></td>
						
                        <td colspan="4" align="center"><input type="button" id="set_button" class="image_uploader" style="width:150px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/btb_margin_lc_amendment_controller.php?action=terms_condition_popup','Terms Condition')" /></td>                             
                        
					</tr>
                    <tr>
                    	<td>Remarks</td>
                        <td colspan="3"><textarea name="txt_remarks_amnd" id="txt_remarks_amnd" style="width:97%" class="text_area" ></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="20" valign="middle" align="center" class="button_container">
                        <? echo load_submit_buttons( $permission, "fnc_amendment_save", 0,0 ,"reset_form('amendmentFrm_1','','','','')",1) ; ?>
						
                        <input type="button" value="Print" name="print" onClick="fnc_print_letter(1)" style="width:80px" id="print" class="formbutton"/>
                        <input type="button" value="Print2" name="print2" onClick="fnc_print_letter(2)" style="width:80px" id="print2" class="formbutton"/>
						<input type="button" value="Print3" name="print3" onClick="fnc_print_letter(3)" style="width:80px" id="print3" class="formbutton"/>
						<input type="button" value="Print4" name="print4" onClick="fnc_print_letter(4)" style="width:80px" id="print4" class="formbutton"/>
                        
                        <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">

                        </td>
                    </tr>
                </table>
            </fieldset>
			<input type="button" id="image_button" class="image_uploader" style="width:152px; margin-right:300px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('txt_system_id').value, '', 'BTBMargin LC Amendment', 2, 1)" />
        </form>
        </fieldset>
		<div id="report_letter_container" style="visibility:hidden;"> </div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>